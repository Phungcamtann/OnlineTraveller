<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\ServiceSchedule;
use App\Models\Service;
use App\Services\PricingService;
use InvalidArgumentException;
use RuntimeException;

/**
 * BookingService: Orchestrates tourist reservation submission, quotation calculation,
 * host approval, cancellation, and seat management.
 */
class BookingService
{
    protected Booking $bookingModel;
    protected PricingService $pricingService;
    protected ServiceSchedule $scheduleModel;
    protected Service $serviceModel;

    public function __construct(
        ?Booking $bookingModel = null,
        ?PricingService $pricingService = null,
        ?ServiceSchedule $scheduleModel = null,
        ?Service $serviceModel = null
    ) {
        $this->bookingModel = $bookingModel ?? new Booking();
        $this->pricingService = $pricingService ?? new PricingService();
        $this->scheduleModel = $scheduleModel ?? new ServiceSchedule();
        $this->serviceModel = $serviceModel ?? new Service();
    }

    /**
     * Kích hoạt Lazy Expiration: Tự động quét và giải phóng các đơn đặt chỗ quá hạn 15 phút
     * chưa thanh toán (status = 'awaiting_payment' và payment_expires_at < NOW())
     * mà không cần SSH/Cron CLI chạy nền trên shared/free hosting.
     *
     * @return int Số lượng đơn đã được tự động hủy và hoàn trả ghế về lịch khởi hành
     */
    public function releaseExpiredReservations(): int
    {
        return $this->bookingModel->releaseExpiredReservations();
    }

    /**
     * Tourist submits a new booking request for a departure schedule.
     * System quotes price with promotions and creates reservation in 'pending' status.
     *
     * @param int $touristId ID của du khách đặt tour
     * @param int $scheduleId ID lịch khởi hành
     * @param int $participantsCount Số khách đăng ký
     * @param string $paymentPlan 'deposit_50', 'full_online', 'host_direct', 'none'
     * @param string|null $promoCode Mã ưu đãi nếu du khách nhập
     * @return array Bản ghi booking vừa được tạo kèm thông tin báo giá
     * @throws InvalidArgumentException khi dữ liệu không hợp lệ hoặc lịch đã đóng/hết chỗ
     */
    public function createBooking(
        int $touristId,
        int $scheduleId,
        int $participantsCount,
        string $paymentPlan = 'deposit_50',
        ?string $promoCode = null
    ): array {
        if ($participantsCount < 1) {
            throw new InvalidArgumentException('Số lượng khách tham gia phải từ 1 người trở lên.');
        }

        if (!in_array($paymentPlan, ['deposit_50', 'full_online', 'host_direct', 'none'], true)) {
            $paymentPlan = 'deposit_50';
        }

        // 0. Kích hoạt Lazy Expiration: Quét và giải phóng các đơn quá hạn 15 phút để nhả ghế
        $this->releaseExpiredReservations();

        // 1. Tính toán báo giá và kiểm tra điều kiện lịch khởi hành
        $quote = $this->pricingService->quoteSchedule($scheduleId, $participantsCount, $paymentPlan, $promoCode);

        // 2. Tạo bản ghi đặt tour thông qua Stored Procedure ndtt_sp_bookings_create
        $created = $this->bookingModel->create($touristId, $scheduleId, $participantsCount, $paymentPlan);

        if (empty($created) || empty($created['id'])) {
            throw new RuntimeException('Không thể khởi tạo đơn đặt chỗ. Vui lòng thử lại sau.');
        }

        // 3. Lấy thông tin chi tiết đầy đủ của đơn đặt vừa tạo
        $fullBooking = $this->bookingModel->getById((int)$created['id']);

        return array_merge($fullBooking ?? $created, [
            'quote_summary' => $quote,
        ]);
    }

    /**
     * Get booking details by ID with optional authorization check.
     * Tự động kích hoạt Lazy Expiration nếu đơn đang xem đã vượt quá mốc 15 phút thanh toán.
     *
     * @param int $bookingId ID đơn đặt
     * @param int|null $userId ID người dùng yêu cầu xem (nếu cần kiểm tra quyền)
     * @param string|null $userRole Quyền người dùng ('tourist', 'host', 'admin')
     * @return array Chi tiết đơn đặt
     * @throws RuntimeException khi không tìm thấy hoặc không có quyền truy cập
     */
    public function getBooking(int $bookingId, ?int $userId = null, ?string $userRole = null): array
    {
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            throw new RuntimeException('Không tìm thấy đơn đặt chỗ yêu cầu.');
        }

        // Kích hoạt Lazy Expiration tức thì nếu đơn đang chờ thanh toán đã quá 15 phút
        if ($booking['status'] === 'awaiting_payment' && !empty($booking['payment_expires_at']) && strtotime($booking['payment_expires_at']) < time()) {
            $this->releaseExpiredReservations();
            $booking = $this->bookingModel->getById($bookingId) ?? $booking;
        }

        if ($userId !== null && $userRole !== 'admin') {
            $isTouristOwner = ((int)$booking['tourist_id'] === $userId);
            $isHostOwner = ((int)$booking['host_id'] === $userId);

            if (!$isTouristOwner && !$isHostOwner) {
                throw new RuntimeException('Bạn không có quyền truy cập thông tin đơn đặt chỗ này.');
            }
        }

        return $booking;
    }

    /**
     * Get booking details by booking code.
     * Tự động kích hoạt Lazy Expiration nếu đơn hàng đã quá hạn 15 phút.
     */
    public function getBookingByCode(string $code, ?int $userId = null, ?string $userRole = null): array
    {
        $booking = $this->bookingModel->getByCode($code);
        if (!$booking) {
            throw new RuntimeException("Không tìm thấy đơn hàng mang mã [{$code}].");
        }

        // Kích hoạt Lazy Expiration tức thì nếu đơn đang chờ thanh toán đã quá 15 phút
        if ($booking['status'] === 'awaiting_payment' && !empty($booking['payment_expires_at']) && strtotime($booking['payment_expires_at']) < time()) {
            $this->releaseExpiredReservations();
            $booking = $this->bookingModel->getByCode($code) ?? $booking;
        }

        if ($userId !== null && $userRole !== 'admin') {
            $isTouristOwner = ((int)$booking['tourist_id'] === $userId);
            $isHostOwner = ((int)$booking['host_id'] === $userId);

            if (!$isTouristOwner && !$isHostOwner) {
                throw new RuntimeException('Bạn không có quyền truy cập thông tin đơn đặt chỗ này.');
            }
        }

        return $booking;
    }

    /**
     * Get all bookings submitted by a specific tourist.
     * Kích hoạt Lazy Expiration tự động trước khi tải danh sách chuyến đi.
     */
    public function getTouristBookings(int $touristId, ?string $status = null): array
    {
        $this->releaseExpiredReservations();
        return $this->bookingModel->getByTourist($touristId, $status);
    }

    /**
     * Get all bookings received by a specific host across all tours.
     * Kích hoạt Lazy Expiration tự động trước khi tải danh sách đơn của Host.
     */
    public function getHostBookings(int $hostId, ?string $status = null): array
    {
        $this->releaseExpiredReservations();
        return $this->bookingModel->getByHost($hostId, $status);
    }

    /**
     * Host reviews and approves a pending booking (reserves seats).
     *
     * @param int $hostId ID của Host quản lý tour
     * @param int $bookingId ID đơn đặt chỗ
     * @return array Kết quả phê duyệt
     */
    public function approveBooking(int $hostId, int $bookingId): array
    {
        // Kích hoạt Lazy Expiration để giải phóng các đơn hết hạn trước khi duyệt đơn mới
        $this->releaseExpiredReservations();

        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            throw new RuntimeException('Không tìm thấy đơn đặt chỗ.');
        }

        if ((int)$booking['host_id'] !== $hostId) {
            throw new RuntimeException('Bạn không phải người quản lý gói trải nghiệm này.');
        }

        if ($booking['status'] !== 'pending') {
            throw new RuntimeException("Chỉ có thể phê duyệt đơn ở trạng thái chờ duyệt (Hiện tại: {$booking['status']}).");
        }

        return $this->bookingModel->approve($hostId, $bookingId);
    }

    /**
     * Cancel an existing booking and release reserved seats.
     *
     * @param int $actorId ID người thực hiện hủy
     * @param int $bookingId ID đơn đặt
     * @param string $reason Lý do hủy
     * @return array Kết quả hủy
     */
    public function cancelBooking(int $actorId, int $bookingId, string $reason): array
    {
        if (trim($reason) === '') {
            $reason = 'Người dùng chủ động hủy yêu cầu đặt chỗ.';
        }

        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            throw new \RuntimeException('BOOKING_NOT_FOUND');
        }

        // 1. Thực thi hủy đặt chỗ và nhả lại ghế qua ndtt_sp_bookings_cancel
        $cancelled = $this->bookingModel->cancel($actorId, $bookingId, trim($reason));

        // 2. Xử lý nghĩa vụ hoàn tiền nếu đơn đã thanh toán online thành công (Mục 3.6)
        $payment = (new \App\Models\Payment())->getByBookingId($bookingId);
        if ($payment && $payment['status'] === 'succeeded' && (float)$payment['amount'] > 0) {
            $refundModel = new \App\Models\Refund();
            $paidAmount = (float)$payment['amount'];
            $startsAt = strtotime($booking['starts_at']);
            $t24 = $startsAt - (24 * 3600);
            $now = time();

            // Xác định vai trò của người thực hiện hủy
            $actor = (new \App\Models\User())->getById($actorId);
            $actorRole = $actor['role'] ?? 'tourist';

            if ($actorRole === 'admin') {
                // Admin hủy đơn: duyệt hoàn tiền trực tiếp qua ndtt_sp_refunds_create_and_approve
                $refund = $refundModel->approveRefundByAdmin(
                    $actorId,
                    (int)$payment['id'],
                    $paidAmount,
                    $reason,
                    'Quản trị viên hủy đơn và duyệt hoàn tiền MoMo Sandbox.'
                );
            } elseif ($now < $t24) {
                // Hủy trước T24: Hủy hợp lệ và hoàn toàn bộ khoản online đã thu (Mục 3.6)
                $refund = $refundModel->createRefund(
                    $bookingId,
                    (int)$payment['id'],
                    $paidAmount,
                    'Hủy hợp lệ trước T24: Hoàn toàn bộ khoản online đã thu (' . number_format($paidAmount) . ' đ). Lý do: ' . $reason,
                    'approved',
                    'Hệ thống tự động duyệt hoàn tiền do đơn hủy trước mốc T24.'
                );
            } else {
                // Hủy sau T24: Tiếp nhận xem xét ngoại lệ bởi Quản trị viên (Mục 3.6)
                $refund = $refundModel->createRefund(
                    $bookingId,
                    (int)$payment['id'],
                    $paidAmount,
                    'Hủy sau T24: Đơn yêu cầu xem xét hoàn tiền ngoại lệ. Lý do: ' . $reason,
                    'requested',
                    'Chờ Quản trị viên xem xét căn cứ hủy sau mốc T24.'
                );
            }

            $cancelled['refund'] = $refund;
        }

        return $cancelled;
    }

    /**
     * Host rejects a pending booking with explanation.
     */
    public function rejectBooking(int $hostId, int $bookingId, string $reason): array
    {
        if (trim($reason) === '') {
            $reason = 'Host không thể sắp xếp tiếp đón đợt này.';
        }

        return $this->bookingModel->reject($hostId, $bookingId, trim($reason));
    }
}

if (!class_exists('BookingService', false)) {
    class_alias(BookingService::class, 'BookingService');
}
