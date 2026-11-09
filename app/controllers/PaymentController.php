<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Response;
use App\Services\PaymentService;
use App\Models\Booking;
use Exception;

/**
 * Payment Controller: Coordinates payment initiation, MoMo Sandbox redirection,
 * asynchronous IPN webhook handling, and return result view.
 */
class PaymentController extends BaseController
{
    private PaymentService $paymentService;
    private Booking $bookingModel;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?PaymentService $paymentService = null,
        ?Booking $bookingModel = null
    ) {
        parent::__construct($request, $response);
        $this->paymentService = $paymentService ?? new PaymentService();
        $this->bookingModel = $bookingModel ?? new Booking();
    }

    /**
     * Tourist initiates MoMo Sandbox checkout for an approved booking.
     * Route: GET /payments/checkout?booking_id={id} or POST /payments/checkout
     */
    public function checkout(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->flash('warning', 'Vui lòng đăng nhập để thực hiện thanh toán.');
            $this->redirect('/login');
            return;
        }

        $bookingId = (int)($request->input('booking_id') ?? $request->query('booking_id') ?? 0);
        if ($bookingId <= 0) {
            $this->flash('danger', 'Mã đơn đặt chỗ không hợp lệ.');
            $this->redirect('/my-bookings');
            return;
        }

        try {
            $result = $this->paymentService->initiateMomoPayment($bookingId, (int)$user['id']);

            $orderInfo = 'Thanh toán cọc tour #' . ($result['booking_code'] ?? $bookingId);
            $sandboxQrUrl = '/payments/sandbox-qr?orderId=' . urlencode($result['order_id']) .
                '&requestId=' . urlencode($result['request_id']) .
                '&amount=' . urlencode((string)$result['amount']) .
                '&orderInfo=' . urlencode($orderInfo);

            if (!empty($result['pay_url']) && str_starts_with($result['pay_url'], 'http')) {
                $sandboxQrUrl .= '&gatewayUrl=' . urlencode($result['pay_url']);
            }

            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'pay_url' => $sandboxQrUrl,
                    'gateway_url' => $result['pay_url'],
                    'order_id' => $result['order_id'],
                    'amount' => $result['amount'],
                ]);
                return;
            }

            // Redirect tourist to MoMo Sandbox simulation screen
            $this->redirect($sandboxQrUrl);
            return;
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->response->json(['error' => $e->getMessage()], 400);
                return;
            }

            $this->flash('danger', $e->getMessage());
            $this->redirect('/bookings/' . $bookingId);
            return;
        }
    }

    /**
     * MoMo Return URL: Handles browser redirect back from MoMo.
     * Note: Browser redirect displays result; confirmation is verified via IPN or database.
     * Route: GET /payments/callback
     */
    public function callback(Request $request): void
    {
        $orderId = (string)($request->query('orderId') ?? '');
        $resultCode = (string)($request->query('resultCode') ?? '-1');
        $amount = (float)($request->query('amount') ?? 0);
        $transId = (string)($request->query('transId') ?? '');
        $message = (string)($request->query('message') ?? '');

        $payment = $this->paymentService->getPaymentByOrderId($orderId);
        $receipt = null;

        if ($payment) {
            $receipt = $this->paymentService->getReceiptByBookingId((int)$payment['booking_id']);
        }

        $this->view('payments/callback', [
            'title' => 'Kết Quả Thanh Toán MoMo — OnlineTraveller',
            'orderId' => $orderId,
            'resultCode' => $resultCode,
            'amount' => $amount,
            'transId' => $transId,
            'message' => $message,
            'payment' => $payment,
            'receipt' => $receipt,
            'user' => $this->user(),
        ]);
    }

    /**
     * MoMo IPN (Server-to-Server Webhook).
     * Validates HMAC-SHA256 signature and applies verified transaction via Stored Procedure.
     * Route: POST /payments/ipn
     */
    public function ipn(Request $request): void
    {
        // MoMo transmits payload as JSON body
        $payload = $request->json() ?? [];
        if (empty($payload)) {
            $payload = $_POST;
        }

        try {
            $result = $this->paymentService->handleIpn($payload);
            $this->response->json($result, 200);
            return;
        } catch (Exception $e) {
            $this->response->json([
                'resultCode' => 1,
                'message' => 'IPN verification failed: ' . $e->getMessage(),
            ], 400);
            return;
        }
    }

    /**
     * MoMo Sandbox Simulation QR Checkout screen.
     * Enables full test checkout loop in local/offline environments.
     * Route: GET /payments/sandbox-qr
     */
    public function sandboxQr(Request $request): void
    {
        $orderId = (string)$request->query('orderId');
        $requestId = (string)$request->query('requestId');
        $amount = (float)($request->query('amount') ?? 0);
        $orderInfo = (string)$request->query('orderInfo');
        $gatewayUrl = (string)($request->query('gatewayUrl') ?? '');

        $payment = $this->paymentService->getPaymentByOrderId($orderId);

        $this->view('payments/sandbox_qr', [
            'title' => 'Cổng Thanh Toán MoMo Sandbox — OnlineTraveller',
            'orderId' => $orderId,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderInfo' => $orderInfo,
            'gatewayUrl' => $gatewayUrl,
            'payment' => $payment,
            'user' => $this->user(),
        ]);
    }
}

if (!class_exists('PaymentController', false)) {
    class_alias(PaymentController::class, 'PaymentController');
}
