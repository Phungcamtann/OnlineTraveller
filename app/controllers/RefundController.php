<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Refund;
use App\Models\Booking;

/**
 * RefundController: Cancellation settlements and refund tracking ticket (TV3-06).
 */
class RefundController extends BaseController
{
    private Refund $refundModel;
    private Booking $bookingModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->refundModel = new Refund();
        $this->bookingModel = new Booking();
    }

    /**
     * Show refund details ticket (GET /refunds/{id}).
     */
    public function show(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $refund = $this->refundModel->getFullRefund($id);
        if (!$refund) {
            $this->flash('danger', 'Không tìm thấy phiếu theo dõi hoàn tiền yêu cầu.');
            $this->redirect('/my-bookings');
            return;
        }

        $userId = (int)$user['id'];
        $userRole = (string)($user['role'] ?? 'tourist');
        $isTourist = ((int)$refund['tourist_id'] === $userId);
        $isHost = ((int)$refund['host_id'] === $userId);
        $isAdmin = ($userRole === 'admin');

        if (!$isTourist && !$isHost && !$isAdmin) {
            $this->response->setStatusCode(403);
            if ($request->isAjax()) {
                $this->json(['error' => 'Bạn không có quyền xem thông tin hoàn tiền này.'], 403);
            } else {
                $this->view('errors/403', ['title' => 'Truy cập bị từ chối']);
            }
            return;
        }

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->json(['success' => true, 'refund' => $refund]);
            return;
        }

        $this->view('refunds/show', [
            'title' => 'Phiếu Theo Dõi Hoàn Tiền #' . $refund['refund_code'],
            'refund' => $refund,
            'user' => $user,
        ]);
    }
}

if (!class_exists('RefundController', false)) {
    class_alias(RefundController::class, 'RefundController');
}
