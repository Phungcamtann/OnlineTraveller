<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Review;
use App\Models\Booking;
use Exception;

/**
 * ReviewController: Reviews and 1..5 star ratings for completed bookings (TV4-06).
 */
class ReviewController extends BaseController
{
    private Review $reviewModel;
    private Booking $bookingModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->reviewModel = new Review();
        $this->bookingModel = new Booking();
    }

    /**
     * Submit a review for a completed booking (POST /reviews).
     */
    public function store(Request $request): void
    {
        $user = $this->user();
        if (!$user) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Vui lòng đăng nhập để gửi đánh giá.'], 401);
                return;
            }
            $this->flash('warning', 'Vui lòng đăng nhập để đánh giá.');
            $this->redirect('/login');
            return;
        }

        $touristId = (int)($user['id'] ?? ($user['user_id'] ?? 0));
        $bookingId = (int)$request->input('booking_id', 0);
        $serviceId = (int)$request->input('service_id', 0);
        $rating = (int)$request->input('rating', 5);
        $comment = (string)$request->input('comment', '');

        if ($bookingId <= 0 && $serviceId <= 0) {
            if ($request->isAjax()) {
                $this->response->json(['error' => 'Vui lòng chọn tour hoặc đơn đặt để gửi đánh giá.'], 400);
                return;
            }
            $this->flash('danger', 'Thông tin tour đánh giá không hợp lệ.');
            $this->redirect('/');
            return;
        }

        try {
            if ($serviceId > 0) {
                // Submitted from tour detail page (services/show.php)
                $created = $this->reviewModel->submitTourReview($touristId, $serviceId, $rating, $comment, $bookingId > 0 ? $bookingId : null);
                $redirectUrl = '/services/' . $serviceId;
            } else {
                // Submitted from booking detail page (bookings/detail.php)
                $created = $this->reviewModel->createReview($touristId, $bookingId, $rating, $comment);
                $redirectUrl = '/bookings/' . $bookingId;
            }

            $message = 'Cảm ơn bạn đã gửi đánh giá trải nghiệm chuyến đi!';
            if ($request->isAjax()) {
                $this->response->json([
                    'success' => true,
                    'message' => $message,
                    'review' => $created,
                ]);
                return;
            }

            $this->flash('success', $message);
            $this->redirect($redirectUrl);
            return;
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            if ($request->isAjax()) {
                $this->response->json(['error' => $errorMsg], 400);
                return;
            }
            $this->flash('danger', $errorMsg);
            $redirectUrl = ($serviceId > 0) ? ('/services/' . $serviceId) : ('/bookings/' . $bookingId);
            $this->redirect($redirectUrl);
            return;
        }
    }

    /**
     * Get reviews list for a service (GET /services/{id}/reviews).
     */
    public function index(Request $request, string $id): void
    {
        $serviceId = (int)$id;
        $reviews = $this->reviewModel->getByService($serviceId);
        $stats = $this->reviewModel->getServiceRatingStats($serviceId);

        $this->response->json([
            'success' => true,
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }
}

if (!class_exists('ReviewController', false)) {
    class_alias(ReviewController::class, 'ReviewController');
}
