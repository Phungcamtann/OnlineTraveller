<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\BaseController;
use Core\Request;
use App\Models\Receipt;

/**
 * Authorized downloads of private confirmation XML and receipts (TV3-06).
 */
class DocumentController extends BaseController
{
    private Receipt $receiptModel;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);
        $this->receiptModel = new Receipt();
    }

    /**
     * Download or view private receipt document (GET /documents/receipts/{code}).
     */
    public function receipt(Request $request, string $code): void
    {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
            return;
        }

        $receipt = $this->receiptModel->getFullReceipt($code);
        if (!$receipt) {
            $this->flash('danger', 'Không tìm thấy chứng từ yêu cầu.');
            $this->redirect('/my-bookings');
            return;
        }

        $userId = (int)$user['id'];
        $userRole = (string)($user['role'] ?? 'tourist');
        $isTourist = ((int)$receipt['tourist_id'] === $userId);
        $isHost = ((int)$receipt['host_id'] === $userId);
        $isAdmin = ($userRole === 'admin');

        if (!$isTourist && !$isHost && !$isAdmin) {
            $this->response->setStatusCode(403);
            $this->view('errors/403', ['title' => 'Truy cập bị từ chối']);
            return;
        }

        $this->view('receipts/show', [
            'title' => 'Chứng Từ Điện Tử #' . $receipt['receipt_code'],
            'receipt' => $receipt,
            'user' => $user,
        ]);
    }
}

if (!class_exists('DocumentController', false)) {
    class_alias(DocumentController::class, 'DocumentController');
}
