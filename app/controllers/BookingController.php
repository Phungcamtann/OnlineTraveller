<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use Core\Response;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Models\ServiceSchedule;
use Exception;

/**
 * BookingController: Handles tourist tour booking flow, real-time price quotation,
 * pending reservation creation, booking status lookup, and cancellation.
 */
class BookingController extends BaseController
{
    protected BookingService $bookingService;
    protected PricingService $pricingService;
    protected ServiceSchedule $scheduleModel;

    public function __construct(
        ?Request $request = null,
        ?Response $response = null,
        ?BookingService $bookingService = null,
        ?PricingService $pricingService = null,
        ?ServiceSchedule $scheduleModel = null
    ) {
        parent::__construct($request, $response);
        $this->bookingService = $bookingService ?? new BookingService();
        $this->pricingService = $pricingService ?? new PricingService();
        $this->scheduleModel = $scheduleModel ?? new ServiceSchedule();
    }

    /**
     * API / AJAX: Calculate real-time price quote with promotions.
     * Route: POST /api/bookings/quote or GET /bookings/quote
     */
    public function quote(Request $request): void
    {
        $scheduleId = (int)($request->input('schedule_id') ?? 0);
        $count = (int)($request->input('participants_count') ?? $request->input('count') ?? 1);
        $plan = (string)($request->input('payment_plan') ?? $request->input('plan') ?? 'deposit_50');
        $promoCode = $request->input('promo_code');

        if ($scheduleId <= 0) {
            $this->json(['success' => false, 'message' => 'Vui lòng chọn lịch khởi hành hợp lệ.'], 400);
            return;
        }

        try {
            $quote = $this->pricingService->quoteSchedule($scheduleId, $count, $plan, $promoCode ? (string)$promoCode : null);
            $this->json([
                'success' => true,
                'data' => $quote,
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display booking creation step (GET /bookings/create).
     */
    public function create(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login?return=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
            return;
        }

        $scheduleId = (int)($request->query('schedule_id') ?? 0);
        $count = (int)($request->query('count') ?? 1);
        $plan = (string)($request->query('plan') ?? 'deposit_50');

        if ($scheduleId <= 0) {
            $this->flash('danger', 'Vui lòng chọn lịch khởi hành của tour trước khi đặt chỗ.');
            $this->redirect('/services');
            return;
        }

        try {
            $quote = $this->pricingService->quoteSchedule($scheduleId, $count, $plan);

            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json(['success' => true, 'data' => $quote]);
                return;
            }

            // Render view nếu file view tồn tại (TV3-04)
            $viewPath = dirname(__DIR__) . '/views/bookings/create.php';
            if (is_file($viewPath)) {
                $this->view('bookings/create', [
                    'title' => 'Xác Nhận Đặt Chỗ — ' . $quote['service_title'],
                    'quote' => $quote,
                    'user' => $user,
                ]);
            } else {
                // Fallback nếu đang ở bước TV3-03 trước khi dựng view TV3-04
                $this->json(['success' => true, 'data' => $quote]);
            }
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/services');
        }
    }

    /**
     * Store new booking request in 'pending' status (POST /bookings).
     */
    public function store(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập để đặt chỗ.'], 401);
            } else {
                $this->redirect('/login');
            }
            return;
        }

        $touristId = (int)$user['id'];
        $scheduleId = (int)$request->post('schedule_id', 0);
        $participantsCount = (int)$request->post('participants_count', 1);
        $paymentPlan = (string)$request->post('payment_plan', 'deposit_50');
        $promoCode = $request->post('promo_code');

        if ($scheduleId <= 0) {
            $message = 'Lịch khởi hành không hợp lệ.';
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => $message], 400);
            } else {
                $this->flash('danger', $message);
                $this->redirect('/services');
            }
            return;
        }

        try {
            $booking = $this->bookingService->createBooking(
                $touristId,
                $scheduleId,
                $participantsCount,
                $paymentPlan,
                $promoCode ? (string)$promoCode : null
            );

            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json([
                    'success' => true,
                    'message' => 'Gửi yêu cầu đặt chỗ thành công! Đang chờ Host bản địa duyệt chỗ.',
                    'booking' => $booking,
                    'redirect' => '/bookings/' . $booking['id'],
                ]);
                return;
            }

            $this->flash('success', 'Yêu cầu giữ chỗ của bạn đã được gửi tới Host! Vui lòng chờ Host duyệt trong giây lát.');
            $this->redirect('/bookings/' . $booking['id']);
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            } else {
                $this->flash('danger', $e->getMessage());
                $this->redirect('/bookings/create?schedule_id=' . $scheduleId . '&count=' . $participantsCount);
            }
        }
    }

    /**
     * Show booking details (GET /bookings/{id}).
     */
    public function show(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login?return=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
            return;
        }

        $bookingId = (int)$id;

        try {
            $booking = $this->bookingService->getBooking($bookingId, (int)$user['id'], (string)($user['role'] ?? 'tourist'));

            if ($request->isAjax() || $request->query('format') === 'json') {
                $this->json(['success' => true, 'booking' => $booking]);
                return;
            }

            $viewPath = dirname(__DIR__) . '/views/bookings/detail.php';
            if (is_file($viewPath)) {
                $this->view('bookings/detail', [
                    'title' => 'Chi Tiết Đơn Đặt #' . $booking['booking_code'],
                    'booking' => $booking,
                    'user' => $user,
                ]);
            } else {
                $this->json(['success' => true, 'booking' => $booking]);
            }
        } catch (Exception $e) {
            $this->flash('danger', $e->getMessage());
            $this->redirect('/my-bookings');
        }
    }

    /**
     * List all bookings for current tourist (GET /my-bookings).
     */
    public function myBookings(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $status = $request->query('status');
        $bookings = $this->bookingService->getTouristBookings((int)$user['id'], $status);

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->json(['success' => true, 'bookings' => $bookings]);
            return;
        }

        $viewPath = dirname(__DIR__) . '/views/bookings/my_bookings.php';
        if (is_file($viewPath)) {
            $this->view('bookings/my_bookings', [
                'title' => 'Chuyến Đi Của Tôi — OnlineTraveller',
                'bookings' => $bookings,
                'status' => $status,
                'user' => $user,
            ]);
        } else {
            $this->json(['success' => true, 'bookings' => $bookings]);
        }
    }

    /**
     * Cancel a booking (POST /bookings/{id}/cancel).
     */
    public function cancel(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
            } else {
                $this->redirect('/login');
            }
            return;
        }

        $bookingId = (int)$id;
        $reason = (string)$request->post('reason', 'Khách hàng yêu cầu hủy đơn');

        try {
            $updated = $this->bookingService->cancelBooking((int)$user['id'], $bookingId, $reason);

            if ($request->isAjax()) {
                $this->json([
                    'success' => true,
                    'message' => 'Hủy đặt chỗ thành công.',
                    'booking' => $updated,
                ]);
                return;
            }

            $this->flash('success', 'Đã hủy đơn đặt chỗ thành công.');
            $this->redirect('/bookings/' . $bookingId);
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            } else {
                $this->flash('danger', $e->getMessage());
                $this->redirect('/bookings/' . $bookingId);
            }
        }
    }
}

if (!class_exists('BookingController', false)) {
    class_alias(BookingController::class, 'BookingController');
}
