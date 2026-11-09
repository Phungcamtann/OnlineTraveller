<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * Receipt Model: Electronic payment receipts (tbl_receipts).
 * Manages receipts issued automatically upon verified MoMo payments.
 */
class Receipt extends BaseModel
{
    /**
     * Find receipt by ID.
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM tbl_receipts WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Find receipt by unique code (e.g. RC261109...).
     */
    public function getByCode(string $code): ?array
    {
        $sql = "SELECT * FROM tbl_receipts WHERE UPPER(receipt_code) = UPPER(?) LIMIT 1";
        return $this->fetchOne($sql, [trim($code)]);
    }

    /**
     * Find receipt by booking ID.
     */
    public function getByBookingId(int $bookingId): ?array
    {
        $sql = "SELECT * FROM tbl_receipts WHERE booking_id = ? ORDER BY id DESC LIMIT 1";
        return $this->fetchOne($sql, [$bookingId]);
    }

    /**
     * Find receipt by payment ID.
     */
    public function getByPaymentId(int $paymentId): ?array
    {
        $sql = "SELECT * FROM tbl_receipts WHERE payment_id = ? LIMIT 1";
        return $this->fetchOne($sql, [$paymentId]);
    }

    /**
     * Get comprehensive receipt details with all joined relations:
     * Payment, Booking, Service, Host, Tourist, Schedule, Accommodation.
     */
    public function getFullReceipt(string|int $idOrCode): ?array
    {
        $sql = "
            SELECT 
                r.id AS receipt_id, r.receipt_code, r.total_amount AS receipt_amount,
                r.issued_at, r.note AS receipt_note,
                p.id AS payment_id, p.order_id, p.request_id, p.payment_method,
                p.momo_trans_id, p.status AS payment_status, p.paid_at,
                b.id AS booking_id, b.booking_code, b.participants_count, b.unit_price,
                b.total_price, b.deposit_amount, b.remaining_amount, b.discount_amount,
                b.payment_plan, b.status AS booking_status, b.created_at AS booking_created_at,
                b.tourist_id, b.service_id, b.schedule_id,
                s.title AS service_title, s.slug AS service_slug, s.location AS service_location,
                s.duration_days, s.banner_image, s.host_id,
                sch.starts_at, sch.ends_at, sch.meeting_point,
                u.name AS tourist_name, u.email AS tourist_email, u.phone AS tourist_phone,
                h.name AS host_name, h.email AS host_email, h.phone AS host_phone, h.living_area AS host_living_area,
                acc.homestay_name, acc.room_type, acc.nights AS nights_count
            FROM tbl_receipts r
            JOIN tbl_payments p ON r.payment_id = p.id
            JOIN tbl_bookings b ON r.booking_id = b.id
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_service_schedules sch ON b.schedule_id = sch.id
            JOIN tbl_users u ON b.tourist_id = u.id
            JOIN tbl_users h ON s.host_id = h.id
            LEFT JOIN tbl_service_accommodations acc ON s.id = acc.service_id
            WHERE r.receipt_code = ? OR r.id = ?
            LIMIT 1
        ";

        return $this->fetchOne($sql, [(string)$idOrCode, is_numeric($idOrCode) ? (int)$idOrCode : 0]);
    }

    /**
     * Get receipts list with pagination for admin/reports.
     */
    public function getAllReceipts(int $limit = 20, int $offset = 0): array
    {
        $sql = "
            SELECT 
                r.*, b.booking_code, u.name AS tourist_name, s.title AS service_title
            FROM tbl_receipts r
            JOIN tbl_bookings b ON r.booking_id = b.id
            JOIN tbl_users u ON b.tourist_id = u.id
            JOIN tbl_services s ON b.service_id = s.id
            ORDER BY r.id DESC
            LIMIT ? OFFSET ?
        ";
        return $this->fetchAll($sql, [$limit, $offset]);
    }
}

if (!class_exists('Receipt', false)) {
    class_alias(Receipt::class, 'Receipt');
}
