<?php

declare(strict_types=1);

namespace App\Services;

use App\Integrations\MomoClient;
use App\Models\Booking;
use App\Models\Payment;
use RuntimeException;
use InvalidArgumentException;

/**
 * Payment Service: Coordinates MoMo checkout attempts, HMAC-SHA256 IPN verification,
 * deduplication, and booking confirmation with electronic receipts.
 */
class PaymentService
{
    private MomoClient $momoClient;
    private Payment $paymentModel;
    private Booking $bookingModel;

    public function __construct(
        ?MomoClient $momoClient = null,
        ?Payment $paymentModel = null,
        ?Booking $bookingModel = null
    ) {
        $this->momoClient = $momoClient ?? new MomoClient();
        $this->paymentModel = $paymentModel ?? new Payment();
        $this->bookingModel = $bookingModel ?? new Booking();
    }

    /**
     * Tourist initiates MoMo Sandbox checkout for an approved booking.
     *
     * @param int $bookingId ID của đơn đặt tour
     * @param int $userId ID du khách thực hiện thanh toán
     * @return array Dữ liệu thanh toán gồm pay_url, order_id, amount
     * @throws RuntimeException khi đơn không hợp lệ hoặc đã hết hạn giữ chỗ
     */
    public function initiateMomoPayment(int $bookingId, int $userId): array
    {
        // 1. Kiểm tra đơn đặt
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            throw new RuntimeException('Không tìm thấy thông tin đơn đặt chỗ.');
        }

        if ((int)$booking['tourist_id'] !== $userId) {
            throw new RuntimeException('Bạn không phải là người tạo đơn đặt tour này.');
        }

        if ($booking['status'] === 'confirmed') {
            throw new RuntimeException('Đơn đặt chỗ này đã được thanh toán và xác nhận thành công trước đó.');
        }

        if ($booking['status'] === 'pending') {
            throw new RuntimeException('Đơn đặt chỗ đang chờ Dân bản địa (Host) duyệt giữ chỗ trước khi thanh toán.');
        }

        if ($booking['status'] !== 'awaiting_payment') {
            throw new RuntimeException("Đơn đặt chỗ hiện ở trạng thái [{$booking['status']}], không thể thanh toán.");
        }

        // 2. Kiểm tra thời hạn 15 phút giữ chỗ
        if (!empty($booking['payment_expires_at']) && strtotime($booking['payment_expires_at']) < time()) {
            $this->bookingModel->releaseExpiredReservations();
            throw new RuntimeException('Thời hạn 15 phút giữ chỗ thanh toán đã hết. Vui lòng gửi lại yêu cầu đặt mới.');
        }

        // 3. Xác định số tiền cần thanh toán theo gói cọc 50% hoặc 100%
        $amount = ($booking['payment_plan'] === 'deposit_50')
            ? (float)$booking['deposit_amount']
            : (float)$booking['total_price'];

        if ($amount <= 0) {
            throw new RuntimeException('Số tiền thanh toán tour không hợp lệ.');
        }

        // 4. Sinh mã đơn hàng và mã yêu cầu duy nhất
        $orderId = sprintf('MOMO_BK%06d_%d', $bookingId, time());
        $requestId = sprintf('REQ_%d_%04d', time(), rand(1000, 9999));
        $orderInfo = 'Thanh toán cọc tour #' . $booking['booking_code'];
        $extraData = base64_encode((string)json_encode(['booking_id' => $bookingId, 'tourist_id' => $userId]));

        // 5. Gọi MoMo gateway tạo liên kết thanh toán QR
        $momoRes = $this->momoClient->createPayment($orderId, $requestId, $amount, $orderInfo, $extraData);
        $payUrl = (string)($momoRes['payUrl'] ?? '');

        // 6. Ghi nhận giao dịch thanh toán vào CSDL
        $paymentRecord = $this->paymentModel->createPayment(
            $bookingId,
            $orderId,
            $requestId,
            $amount,
            'momo_sandbox',
            $payUrl
        );

        return [
            'booking_id' => $bookingId,
            'booking_code' => $booking['booking_code'],
            'order_id' => $orderId,
            'request_id' => $requestId,
            'amount' => $amount,
            'pay_url' => $payUrl,
            'expires_at' => $booking['payment_expires_at'],
            'payment_record' => $paymentRecord,
        ];
    }

    /**
     * Process asynchronous MoMo IPN (Server-to-Server Webhook).
     * Validates HMAC-SHA256 signature, deduplicates provider events,
     * and calls Stored Procedure ndtt_sp_payments_apply_verified.
     *
     * @param array $payload Dữ liệu webhook gửi từ máy chủ MoMo
     * @return array Kết quả xử lý trả về cho MoMo
     * @throws RuntimeException khi sai chữ ký hoặc dữ liệu không hợp lệ
     */
    public function handleIpn(array $payload): array
    {
        // 1. Kiểm tra chữ ký HMAC-SHA256
        if (!$this->momoClient->verifySignature($payload)) {
            throw new RuntimeException('INVALID_SIGNATURE');
        }

        $orderId = (string)($payload['orderId'] ?? '');
        if ($orderId === '') {
            throw new RuntimeException('MISSING_ORDER_ID');
        }

        // 2. Truy vấn bản ghi giao dịch trong hệ thống
        $payment = $this->paymentModel->getByOrderId($orderId);
        if (!$payment) {
            throw new RuntimeException('PAYMENT_NOT_FOUND');
        }

        // 3. Đối soát số tiền thanh toán (Chống giả mạo số tiền)
        $expectedAmount = (int)round((float)$payment['amount']);
        $receivedAmount = (int)round((float)($payload['amount'] ?? 0));
        if ($expectedAmount !== $receivedAmount) {
            throw new RuntimeException('AMOUNT_MISMATCH');
        }

        // 4. Khử trùng lặp sự kiện (Idempotency & Deduplication)
        if ($payment['status'] === 'succeeded') {
            return [
                'resultCode' => 0,
                'message' => 'IPN already processed and verified successfully.',
            ];
        }

        $resultCode = (int)($payload['resultCode'] ?? -1);
        $momoTransId = (string)($payload['transId'] ?? ('MM_' . time()));
        $requestId = (string)($payload['requestId'] ?? $payment['request_id']);

        // 5. Kết quả giao dịch từ MoMo (0 = Thành công)
        if ($resultCode === 0) {
            $applied = $this->paymentModel->applyVerifiedPayment(
                (int)$payment['booking_id'],
                $orderId,
                $requestId,
                (float)$expectedAmount,
                $momoTransId
            );

            return [
                'resultCode' => 0,
                'message' => 'IPN applied and booking confirmed successfully.',
                'data' => $applied,
            ];
        }

        // Giao dịch không thành công từ phía MoMo
        $this->paymentModel->markFailed($orderId, (string)($payload['message'] ?? 'MoMo payment failed'));

        return [
            'resultCode' => 0,
            'message' => 'Payment failure recorded.',
        ];
    }

    /**
     * Get payment details by MoMo order ID.
     */
    public function getPaymentByOrderId(string $orderId): ?array
    {
        return $this->paymentModel->getByOrderId($orderId);
    }

    /**
     * Get electronic receipt for a booking.
     */
    public function getReceiptByBookingId(int $bookingId): ?array
    {
        return $this->paymentModel->getReceiptByBookingId($bookingId);
    }

    public function getMomoClient(): MomoClient
    {
        return $this->momoClient;
    }
}

if (!class_exists('PaymentService', false)) {
    class_alias(PaymentService::class, 'PaymentService');
}
