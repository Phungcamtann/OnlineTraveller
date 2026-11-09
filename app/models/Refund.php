<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * Refund Model: Cancellation settlements and MoMo Sandbox refunds (tbl_refunds).
 */
class Refund extends BaseModel
{
    /**
     * Create refund record (e.g. upon cancellation before or after T24).
     */
    public function createRefund(
        int $bookingId,
        int $paymentId,
        float $amount,
        string $reason,
        string $status = 'requested',
        ?string $adminNote = null
    ): array {
        $code = sprintf('RF%s%04d', date('ymd'), rand(1000, 9999));

        $sql = "
            INSERT INTO tbl_refunds (
                refund_code, payment_id, booking_id, amount,
                reason, status, admin_note, processed_at, created_at
            ) VALUES (
                ?, ?, ?, ?,
                ?, ?, ?, " . ($status === 'approved' ? "NOW(6)" : "NULL") . ", NOW(6)
            )
        ";

        $this->execute($sql, [
            $code,
            $paymentId,
            $bookingId,
            $amount,
            $reason,
            $status,
            $adminNote,
        ]);

        $id = (int)$this->db->lastInsertId();
        return $this->getById($id) ?? [];
    }

    /**
     * Approve and execute refund via Stored Procedure ndtt_sp_refunds_create_and_approve.
     */
    public function approveRefundByAdmin(
        int $adminId,
        int $paymentId,
        float $amount,
        string $reason,
        string $adminNote = ''
    ): array {
        $rows = $this->callProcedure('ndtt_sp_refunds_create_and_approve', [
            $adminId,
            $paymentId,
            $amount,
            $reason,
            $adminNote,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Find refund by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM tbl_refunds WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Find refund by code.
     */
    public function getByCode(string $code): ?array
    {
        $sql = "SELECT * FROM tbl_refunds WHERE UPPER(refund_code) = UPPER(?) LIMIT 1";
        return $this->fetchOne($sql, [trim($code)]);
    }

    /**
     * Find refund record by booking ID.
     */
    public function getByBookingId(int $bookingId): ?array
    {
        $sql = "SELECT * FROM tbl_refunds WHERE booking_id = ? ORDER BY id DESC LIMIT 1";
        return $this->fetchOne($sql, [$bookingId]);
    }

    /**
     * Get refund with full details for user or admin inspection.
     */
    public function getFullRefund(int|string $idOrCode): ?array
    {
        $sql = "
            SELECT 
                rf.*,
                b.booking_code, b.tourist_id, b.service_id, b.status AS booking_status,
                b.total_price, b.deposit_amount,
                s.title AS service_title, s.host_id,
                u.name AS tourist_name, u.email AS tourist_email, u.phone AS tourist_phone,
                h.name AS host_name, h.email AS host_email,
                p.order_id, p.momo_trans_id, p.amount AS payment_amount
            FROM tbl_refunds rf
            JOIN tbl_bookings b ON rf.booking_id = b.id
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_users u ON b.tourist_id = u.id
            JOIN tbl_users h ON s.host_id = h.id
            JOIN tbl_payments p ON rf.payment_id = p.id
            WHERE rf.refund_code = ? OR rf.id = ?
            LIMIT 1
        ";

        return $this->fetchOne($sql, [(string)$idOrCode, is_numeric($idOrCode) ? (int)$idOrCode : 0]);
    }

    /**
     * List all refunds with optional status filter for Admin.
     */
    public function getAllRefunds(?string $status = null, int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT 
                rf.*,
                b.booking_code,
                u.name AS tourist_name,
                s.title AS service_title
            FROM tbl_refunds rf
            JOIN tbl_bookings b ON rf.booking_id = b.id
            JOIN tbl_users u ON b.tourist_id = u.id
            JOIN tbl_services s ON b.service_id = s.id
        ";
        $params = [];
        if (!empty($status)) {
            $sql .= " WHERE rf.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY rf.id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->fetchAll($sql, $params);
    }

    /**
     * Get refund stats for Admin dashboard.
     */
    public function getStats(): array
    {
        $sql = "
            SELECT 
                COUNT(*) AS total_count,
                SUM(CASE WHEN status = 'requested' THEN 1 ELSE 0 END) AS requested_count,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN status = 'succeeded' THEN 1 ELSE 0 END) AS succeeded_count,
                COALESCE(SUM(amount), 0) AS total_amount
            FROM tbl_refunds
        ";
        return $this->fetchOne($sql) ?? [
            'total_count' => 0,
            'requested_count' => 0,
            'approved_count' => 0,
            'succeeded_count' => 0,
            'total_amount' => 0,
        ];
    }

    /**
     * Admin updates status of an existing refund request (approve / reject).
     */
    public function updateStatus(int $id, string $status, ?string $adminNote = null): bool
    {
        $sql = "
            UPDATE tbl_refunds 
            SET status = ?, admin_note = COALESCE(?, admin_note), processed_at = NOW(6)
            WHERE id = ?
        ";
        return $this->execute($sql, [$status, $adminNote, $id]) > 0;
    }
}

if (!class_exists('Refund', false)) {
    class_alias(Refund::class, 'Refund');
}
