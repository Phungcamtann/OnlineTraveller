<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * Payment Model: Coordinates MoMo transactions, electronic receipts,
 * and database operations via Stored Procedures.
 */
class Payment extends BaseModel
{
    /**
     * Create or update a payment attempt in tbl_payments.
     */
    public function createPayment(
        int $bookingId,
        string $orderId,
        string $requestId,
        float $amount,
        string $paymentMethod = 'momo_sandbox',
        ?string $payUrl = null
    ): array {
        $sql = "
            INSERT INTO tbl_payments (
                booking_id, order_id, request_id, amount,
                payment_method, status, pay_url, created_at
            ) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW(6))
            ON DUPLICATE KEY UPDATE
                request_id = VALUES(request_id),
                amount = VALUES(amount),
                pay_url = VALUES(pay_url),
                status = 'pending'
        ";

        $this->execute($sql, [$bookingId, $orderId, $requestId, $amount, $paymentMethod, $payUrl]);

        return $this->getByOrderId($orderId) ?? [];
    }

    /**
     * Find payment record by MoMo unique orderId.
     */
    public function getByOrderId(string $orderId): ?array
    {
        $sql = "
            SELECT 
                p.*,
                b.booking_code, b.tourist_id, b.status AS booking_status,
                b.payment_plan, b.total_price, b.deposit_amount, b.remaining_amount,
                r.receipt_code, r.issued_at AS receipt_issued_at
            FROM tbl_payments p
            JOIN tbl_bookings b ON p.booking_id = b.id
            LEFT JOIN tbl_receipts r ON p.id = r.payment_id
            WHERE p.order_id = ?
            LIMIT 1
        ";

        return $this->fetchOne($sql, [trim($orderId)]);
    }

    /**
     * Find latest payment by booking ID.
     */
    public function getByBookingId(int $bookingId): ?array
    {
        $sql = "
            SELECT 
                p.*,
                r.receipt_code, r.issued_at AS receipt_issued_at
            FROM tbl_payments p
            LEFT JOIN tbl_receipts r ON p.id = r.payment_id
            WHERE p.booking_id = ?
            ORDER BY p.id DESC
            LIMIT 1
        ";

        return $this->fetchOne($sql, [$bookingId]);
    }

    /**
     * Apply verified MoMo IPN transaction via Stored Procedure ndtt_sp_payments_apply_verified.
     * Transitions booking to confirmed, sets payment to succeeded, and issues electronic receipt.
     */
    public function applyVerifiedPayment(
        int $bookingId,
        string $orderId,
        string $requestId,
        float $amount,
        string $momoTransId
    ): ?array {
        return $this->callProcedureOne('ndtt_sp_payments_apply_verified', [
            $bookingId,
            $orderId,
            $requestId,
            $amount,
            $momoTransId,
        ]);
    }

    /**
     * Mark payment as failed or cancelled.
     */
    public function markFailed(string $orderId, ?string $reason = null): bool
    {
        $sql = "UPDATE tbl_payments SET status = 'failed' WHERE order_id = ?";
        return $this->execute($sql, [$orderId]) > 0;
    }

    /**
     * Get electronic receipt by booking ID.
     */
    public function getReceiptByBookingId(int $bookingId): ?array
    {
        $sql = "SELECT * FROM tbl_receipts WHERE booking_id = ? LIMIT 1";
        return $this->fetchOne($sql, [$bookingId]);
    }
}

if (!class_exists('Payment', false)) {
    class_alias(Payment::class, 'Payment');
}
