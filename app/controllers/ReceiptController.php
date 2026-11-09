<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Receipt;
use App\Models\Booking;

/**
 * ReceiptController: Electronic payment receipts viewing and printing (TV3-06).
 */
class ReceiptController extends BaseController
{
    private Receipt $receiptModel;
    private Booking $bookingModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->receiptModel = new Receipt();
        $this->bookingModel = new Booking();
    }

    /**
     * Show receipt by receipt ID or receipt code (GET /receipts/{id}).
     */
    public function show(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $receipt = $this->receiptModel->getFullReceipt($id);
        if (!$receipt) {
            $this->flash('danger', 'Không tìm thấy biên nhận điện tử yêu cầu.');
            $this->redirect('/my-bookings');
            return;
        }

        // Authorization check: Tourist owner, Tour host, or Admin
        $userId = (int)$user['id'];
        $userRole = (string)($user['role'] ?? 'tourist');
        $isTourist = ((int)$receipt['tourist_id'] === $userId);
        $isHost = ((int)$receipt['host_id'] === $userId);
        $isAdmin = ($userRole === 'admin');

        if (!$isTourist && !$isHost && !$isAdmin) {
            $this->response->setStatusCode(403);
            if ($request->isAjax()) {
                $this->json(['error' => 'Bạn không có quyền xem biên nhận điện tử này.'], 403);
            } else {
                $this->view('errors/403', ['title' => 'Truy cập bị từ chối']);
            }
            return;
        }

        if ($request->isAjax() || $request->query('format') === 'json') {
            $this->json(['success' => true, 'receipt' => $receipt]);
            return;
        }

        $this->view('receipts/show', [
            'title' => 'Biên Nhận Điện Tử #' . $receipt['receipt_code'],
            'receipt' => $receipt,
            'user' => $user,
        ]);
    }

    /**
     * Show receipt by booking ID (GET /bookings/{id}/receipt).
     */
    public function showByBooking(Request $request, string $id): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $bookingId = (int)$id;
        $receipt = $this->receiptModel->getByBookingId($bookingId);
        if (!$receipt) {
            $this->flash('warning', 'Đơn đặt chỗ này chưa phát hành biên nhận điện tử hoặc chưa thanh toán.');
            $this->redirect('/bookings/' . $bookingId);
            return;
        }

        $this->show($request, (string)$receipt['receipt_code']);
    }
}

if (!class_exists('ReceiptController', false)) {
    class_alias(ReceiptController::class, 'ReceiptController');
}
