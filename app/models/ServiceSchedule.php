<?php

declare(strict_types=1);

namespace App\Models;

use Core\BaseModel;

/**
 * ServiceSchedule Model: manages departure dates, capacity, bookings count,
 * and cancellation of tour schedules.
 */
class ServiceSchedule extends BaseModel
{
    /**
     * Create a new departure schedule via Stored Procedure ptl_sp_schedules_create.
     */
    public function create(
        int $hostId,
        int $serviceId,
        string $startsAt,
        string $endsAt,
        string $bookingClosesAt,
        int $capacity,
        string $meetingPoint
    ): array {
        $rows = $this->callProcedure('ptl_sp_schedules_create', [
            $hostId,
            $serviceId,
            $startsAt,
            $endsAt,
            $bookingClosesAt,
            $capacity,
            $meetingPoint,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Cancel an existing departure schedule via Stored Procedure ptl_sp_schedules_cancel.
     */
    public function cancel(int $hostId, int $scheduleId, ?string $cancelReason = null): array
    {
        $rows = $this->callProcedure('ptl_sp_schedules_cancel', [
            $hostId,
            $scheduleId,
            $cancelReason,
        ]);

        return $rows[0] ?? [];
    }

    /**
     * Get a single departure schedule by ID with associated tour and availability details.
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT 
                sch.*,
                s.title AS service_title, s.slug AS service_slug, s.host_id, s.duration_days,
                s.price_mode, s.base_price, s.min_price, s.max_price, s.location AS service_location,
                (sch.capacity - sch.booked_seats) AS available_seats
            FROM tbl_service_schedules sch
            JOIN tbl_services s ON sch.service_id = s.id
            WHERE sch.id = ?
            LIMIT 1
        ";

        return $this->fetchOne($sql, [$id]);
    }

    /**
     * Get list of departure schedules for a specific tour.
     *
     * @param int $serviceId Tour ID
     * @param bool $onlyAvailable If true, returns only upcoming open schedules with available seats
     */
    public function getByServiceId(int $serviceId, bool $onlyAvailable = false): array
    {
        $sql = "
            SELECT 
                sch.*,
                (sch.capacity - sch.booked_seats) AS available_seats
            FROM tbl_service_schedules sch
            WHERE sch.service_id = ?
        ";

        if ($onlyAvailable) {
            $sql .= " AND sch.status = 'open' AND sch.booking_closes_at > NOW() AND (sch.capacity - sch.booked_seats) > 0";
        }

        $sql .= " ORDER BY sch.starts_at ASC";

        return $this->fetchAll($sql, [$serviceId]);
    }

    /**
     * Get list of departure schedules managed by a specific Host.
     */
    public function getByHost(int $hostId, ?string $status = null): array
    {
        $sql = "
            SELECT 
                sch.*,
                s.title AS service_title, s.slug AS service_slug, s.location AS service_location,
                s.base_price, s.price_mode,
                (sch.capacity - sch.booked_seats) AS available_seats,
                (SELECT COUNT(*) FROM tbl_bookings b WHERE b.schedule_id = sch.id) AS bookings_count
            FROM tbl_service_schedules sch
            JOIN tbl_services s ON sch.service_id = s.id
            WHERE s.host_id = ?
        ";

        $params = [$hostId];

        if ($status !== null && $status !== '') {
            $sql .= " AND sch.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY sch.starts_at DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Check if a schedule has sufficient seat capacity for booking.
     */
    public function checkCapacity(int $scheduleId, int $seatsNeeded = 1): bool
    {
        $sch = $this->getById($scheduleId);
        if (!$sch) {
            return false;
        }

        if ($sch['status'] !== 'open') {
            return false;
        }

        if (strtotime($sch['booking_closes_at']) <= time()) {
            return false;
        }

        return ((int)$sch['available_seats']) >= $seatsNeeded;
    }

    /**
     * Update departure schedule status (open, closed, cancelled, completed).
     */
    public function updateStatus(int $scheduleId, string $status): bool
    {
        $sql = "UPDATE tbl_service_schedules SET status = ? WHERE id = ?";
        $stmt = $this->db->query($sql, [$status, $scheduleId]);
        return $stmt->rowCount() > 0;
    }
}

if (!class_exists('ServiceSchedule', false)) {
    class_alias(ServiceSchedule::class, 'ServiceSchedule');
}
