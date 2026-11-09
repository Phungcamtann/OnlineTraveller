<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;
use RuntimeException;

/**
 * Booking Model: Manages tourist reservations, pricing quotes, host approval,
 * seat reservations and cancellation flow via Stored Procedures and relational queries.
 */
class Booking extends BaseModel
{
    /**
     * Create a new booking request in 'pending' status via Stored Procedure ndtt_sp_bookings_create.
     *
     * @param int $touristId User ID of tourist
     * @param int $scheduleId ID of departure schedule
     * @param int $participantsCount Number of guests
     * @param string $paymentPlan 'deposit_50', 'full_online', 'host_direct', 'none'
     * @return array Created booking record
     */
    public function create(
        int $touristId,
        int $scheduleId,
        int $participantsCount,
        string $paymentPlan = 'deposit_50'
    ): array {
        $rows = $this->callProcedure('ndtt_sp_bookings_create', [
            $touristId,
            $scheduleId,
            $participantsCount,
            $paymentPlan,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Host approves booking and reserves seats via Stored Procedure ndtt_sp_bookings_approve.
     * Checks capacity concurrency, transitions to 'awaiting_payment' with 15-min countdown,
     * or 'confirmed' for direct payment tours.
     *
     * @param int $hostId ID of host owning the service
     * @param int $bookingId ID of booking to approve
     * @return array Updated booking record
     */
    public function approve(int $hostId, int $bookingId): array
    {
        $rows = $this->callProcedure('ndtt_sp_bookings_approve', [
            $hostId,
            $bookingId,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Cancel a booking and release reserved seats via Stored Procedure ndtt_sp_bookings_cancel.
     *
     * @param int $actorId User ID performing cancellation (tourist, host, or admin)
     * @param int $bookingId ID of booking to cancel
     * @param string $reason Reason for cancellation
     * @return array Updated booking record
     */
    public function cancel(int $actorId, int $bookingId, string $reason): array
    {
        $rows = $this->callProcedure('ndtt_sp_bookings_cancel', [
            $actorId,
            $bookingId,
            $reason,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Host rejects a pending booking with explanation.
     */
    public function reject(int $hostId, int $bookingId, string $reason): array
    {
        $booking = $this->getById($bookingId);
        if (!$booking) {
            throw new RuntimeException('BOOKING_NOT_FOUND');
        }

        if ((int)$booking['host_id'] !== $hostId) {
            throw new RuntimeException('FORBIDDEN_NOT_HOST');
        }

        if ($booking['status'] !== 'pending') {
            throw new RuntimeException('BOOKING_NOT_PENDING');
        }

        $sql = "
            UPDATE tbl_bookings
            SET status = 'rejected',
                rejection_reason = ?,
                updated_at = NOW(6)
            WHERE id = ?
        ";
        $this->execute($sql, [$reason, $bookingId]);

        return $this->getById($bookingId) ?? [];
    }

    /**
     * Get single booking by ID with full service, schedule, host, tourist, and payment relations.
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT 
                b.*,
                s.title AS service_title, s.slug AS service_slug, s.banner_image AS service_banner,
                s.location AS service_location, s.price_mode, s.duration_days,
                s.host_id, h.name AS host_name, h.email AS host_email, h.phone AS host_phone, h.avatar AS host_avatar,
                sch.starts_at, sch.ends_at, sch.meeting_point, sch.capacity AS schedule_capacity,
                sch.booked_seats AS schedule_booked_seats,
                u.name AS tourist_name, u.email AS tourist_email, u.phone AS tourist_phone, u.avatar AS tourist_avatar,
                p.id AS payment_id, p.order_id, p.momo_trans_id, p.status AS payment_status, p.paid_at,
                r.id AS receipt_id, r.receipt_code, r.issued_at AS receipt_issued_at,
                rf.id AS refund_id, rf.refund_code, rf.amount AS refund_amount, rf.status AS refund_status,
                rf.reason AS refund_reason, rf.admin_note AS refund_admin_note, rf.processed_at AS refund_processed_at
            FROM tbl_bookings b
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_service_schedules sch ON b.schedule_id = sch.id
            JOIN tbl_users u ON b.tourist_id = u.id
            JOIN tbl_users h ON s.host_id = h.id
            LEFT JOIN tbl_payments p ON b.id = p.booking_id
            LEFT JOIN tbl_receipts r ON p.id = r.payment_id
            LEFT JOIN tbl_refunds rf ON b.id = rf.booking_id
            WHERE b.id = ?
            LIMIT 1
        ";

        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get single booking by unique booking code (e.g. BK261107...).
     */
    public function getByCode(string $code): ?array
    {
        $sql = "
            SELECT 
                b.*,
                s.title AS service_title, s.slug AS service_slug, s.banner_image AS service_banner,
                s.location AS service_location, s.price_mode, s.duration_days,
                s.host_id, h.name AS host_name, h.email AS host_email, h.phone AS host_phone, h.avatar AS host_avatar,
                sch.starts_at, sch.ends_at, sch.meeting_point, sch.capacity AS schedule_capacity,
                sch.booked_seats AS schedule_booked_seats,
                u.name AS tourist_name, u.email AS tourist_email, u.phone AS tourist_phone, u.avatar AS tourist_avatar,
                p.id AS payment_id, p.order_id, p.momo_trans_id, p.status AS payment_status, p.paid_at,
                r.id AS receipt_id, r.receipt_code, r.issued_at AS receipt_issued_at,
                rf.id AS refund_id, rf.refund_code, rf.amount AS refund_amount, rf.status AS refund_status,
                rf.reason AS refund_reason, rf.admin_note AS refund_admin_note, rf.processed_at AS refund_processed_at
            FROM tbl_bookings b
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_service_schedules sch ON b.schedule_id = sch.id
            JOIN tbl_users u ON b.tourist_id = u.id
            JOIN tbl_users h ON s.host_id = h.id
            LEFT JOIN tbl_payments p ON b.id = p.booking_id
            LEFT JOIN tbl_receipts r ON p.id = r.payment_id
            LEFT JOIN tbl_refunds rf ON b.id = rf.booking_id
            WHERE UPPER(b.booking_code) = UPPER(?)
            LIMIT 1
        ";

        return $this->fetchOne($sql, [trim($code)]);
    }

    /**
     * Get all bookings made by a tourist.
     */
    public function getByTourist(int $touristId, ?string $status = null): array
    {
        $sql = "
            SELECT 
                b.*,
                s.title AS service_title, s.slug AS service_slug, s.banner_image AS service_banner,
                s.location AS service_location,
                h.name AS host_name, h.phone AS host_phone,
                sch.starts_at, sch.ends_at, sch.meeting_point
            FROM tbl_bookings b
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_service_schedules sch ON b.schedule_id = sch.id
            JOIN tbl_users h ON s.host_id = h.id
            WHERE b.tourist_id = ?
        ";

        $params = [$touristId];
        if ($status !== null && $status !== '') {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.created_at DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Get all bookings received by a Host across all their tours.
     */
    public function getByHost(int $hostId, ?string $status = null): array
    {
        $sql = "
            SELECT 
                b.*,
                s.title AS service_title, s.slug AS service_slug,
                u.name AS tourist_name, u.phone AS tourist_phone, u.email AS tourist_email, u.avatar AS tourist_avatar,
                sch.starts_at, sch.ends_at, sch.meeting_point
            FROM tbl_bookings b
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_service_schedules sch ON b.schedule_id = sch.id
            JOIN tbl_users u ON b.tourist_id = u.id
            WHERE s.host_id = ?
        ";

        $params = [$hostId];
        if ($status !== null && $status !== '') {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.created_at DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Get all bookings for a specific departure schedule.
     */
    public function getBySchedule(int $scheduleId): array
    {
        $sql = "
            SELECT 
                b.*,
                u.name AS tourist_name, u.phone AS tourist_phone, u.email AS tourist_email, u.avatar AS tourist_avatar
            FROM tbl_bookings b
            JOIN tbl_users u ON b.tourist_id = u.id
            WHERE b.schedule_id = ?
            ORDER BY b.created_at ASC
        ";

        return $this->fetchAll($sql, [$scheduleId]);
    }

    /**
     * Update booking status directly.
     */
    public function updateStatus(int $bookingId, string $status, ?string $reason = null): bool
    {
        $sql = "UPDATE tbl_bookings SET status = ?, cancel_reason = COALESCE(?, cancel_reason), updated_at = NOW(6) WHERE id = ?";
        return $this->execute($sql, [$status, $reason, $bookingId]) > 0;
    }

    /**
     * Release expired 15-minute seat reservations:
     * Any booking in 'awaiting_payment' where payment_expires_at < NOW()
     * will be set to 'expired', and seats released back to the schedule.
     */
    public function releaseExpiredReservations(): int
    {
        // 1. Release seats back to schedules
        $sqlRelease = "
            UPDATE tbl_service_schedules sch
            JOIN tbl_bookings b ON sch.id = b.schedule_id
            SET sch.booked_seats = GREATEST(0, sch.booked_seats - b.participants_count)
            WHERE b.status = 'awaiting_payment'
              AND b.payment_expires_at IS NOT NULL
              AND b.payment_expires_at < NOW(6)
        ";
        $this->execute($sqlRelease);

        // 2. Mark bookings as expired
        $sqlExpire = "
            UPDATE tbl_bookings
            SET status = 'expired',
                cancel_reason = 'Hết hạn giữ chỗ 15 phút thanh toán',
                updated_at = NOW(6)
            WHERE status = 'awaiting_payment'
              AND payment_expires_at IS NOT NULL
              AND payment_expires_at < NOW(6)
        ";
        return $this->execute($sqlExpire);
    }

    /**
     * Aggregate booking statistics for a Local Host.
     */
    public function getHostBookingStats(int $hostId): array
    {
        $sql = "
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN b.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN b.status = 'awaiting_payment' THEN 1 ELSE 0 END) AS awaiting_payment,
                SUM(CASE WHEN b.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
                SUM(CASE WHEN b.status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN b.status IN ('cancelled', 'rejected', 'expired') THEN 1 ELSE 0 END) AS cancelled,
                SUM(CASE WHEN b.status IN ('confirmed', 'completed') THEN b.total_price ELSE 0 END) AS revenue
            FROM tbl_bookings b
            JOIN tbl_services s ON b.service_id = s.id
            WHERE s.host_id = ?
        ";
        $row = $this->fetchOne($sql, [$hostId]);
        return [
            'total' => (int)($row['total'] ?? 0),
            'pending' => (int)($row['pending'] ?? 0),
            'awaiting_payment' => (int)($row['awaiting_payment'] ?? 0),
            'confirmed' => (int)($row['confirmed'] ?? 0),
            'completed' => (int)($row['completed'] ?? 0),
            'cancelled' => (int)($row['cancelled'] ?? 0),
            'revenue' => (float)($row['revenue'] ?? 0),
        ];
    }

    /**
     * Get bookings received by a Host with filtering and auto seat expiration check.
     */
    public function getHostBookingsFiltered(int $hostId, ?string $status = null, ?int $serviceId = null, ?string $search = null): array
    {
        $this->releaseExpiredReservations();

        $sql = "
            SELECT 
                b.*,
                s.title AS service_title, s.slug AS service_slug, s.banner_image AS service_banner,
                s.location AS service_location,
                u.name AS tourist_name, u.phone AS tourist_phone, u.email AS tourist_email, u.avatar AS tourist_avatar,
                sch.starts_at, sch.ends_at, sch.meeting_point, sch.capacity AS schedule_capacity, sch.booked_seats AS schedule_booked_seats
            FROM tbl_bookings b
            JOIN tbl_services s ON b.service_id = s.id
            JOIN tbl_service_schedules sch ON b.schedule_id = sch.id
            JOIN tbl_users u ON b.tourist_id = u.id
            WHERE s.host_id = ?
        ";
        $params = [$hostId];

        if (!empty($status) && in_array($status, ['pending', 'awaiting_payment', 'confirmed', 'completed', 'cancelled', 'rejected', 'expired'], true)) {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        if (!empty($serviceId)) {
            $sql .= " AND b.service_id = ?";
            $params[] = $serviceId;
        }

        if (!empty($search)) {
            $sql .= " AND (b.booking_code LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $wildcard = '%' . trim($search) . '%';
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
            $params[] = $wildcard;
        }

        $sql .= " ORDER BY b.created_at DESC";

        return $this->fetchAll($sql, $params);
    }
}

if (!class_exists('Booking', false)) {
    class_alias(Booking::class, 'Booking');
}
