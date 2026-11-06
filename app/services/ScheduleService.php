<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ServiceSchedule;
use App\Models\Service;
use RuntimeException;

/**
 * Schedule Service: handles departure schedule planning, timeline validations,
 * capacity checks, and schedule cancellations.
 */
class ScheduleService
{
    private ServiceSchedule $scheduleModel;
    private Service $serviceModel;

    public function __construct(
        ?ServiceSchedule $scheduleModel = null,
        ?Service $serviceModel = null
    ) {
        $this->scheduleModel = $scheduleModel ?? new ServiceSchedule();
        $this->serviceModel = $serviceModel ?? new Service();
    }

    /**
     * Host creates a new departure schedule for a tour.
     *
     * @param int $hostId ID of the local host
     * @param int $serviceId ID of the tour service
     * @param array $data Input form data (starts_at, ends_at, booking_closes_at, capacity, meeting_point)
     * @return array Created schedule details
     * @throws RuntimeException If validation fails or unauthorized
     */
    public function createSchedule(int $hostId, int $serviceId, array $data): array
    {
        $service = $this->serviceModel->getById($serviceId);
        if (!$service) {
            throw new RuntimeException('Gói trải nghiệm không tồn tại trên hệ thống.');
        }

        if ((int)$service['host_id'] !== $hostId) {
            throw new RuntimeException('Bạn không phải người quản lý gói trải nghiệm này.');
        }

        $validated = $this->validateScheduleTimings($data);

        $capacity = (int)($data['capacity'] ?? 0);
        if ($capacity < 1 || $capacity > 500) {
            throw new RuntimeException('Sức chứa đoàn phải từ 1 đến 500 người.');
        }

        $meetingPoint = trim((string)($data['meeting_point'] ?? ''));
        if (mb_strlen($meetingPoint, 'UTF-8') < 3) {
            throw new RuntimeException('Vui lòng cung cấp điểm hẹn đón khách cụ thể (tối thiểu 3 ký tự).');
        }

        $created = $this->scheduleModel->create(
            $hostId,
            $serviceId,
            $validated['starts_at'],
            $validated['ends_at'],
            $validated['booking_closes_at'],
            $capacity,
            $meetingPoint
        );

        if (empty($created['id'])) {
            throw new RuntimeException('Khởi tạo lịch khởi hành thất bại. Vui lòng kiểm tra lại thông tin.');
        }

        return $this->scheduleModel->getById((int)$created['id']) ?? $created;
    }

    /**
     * Host cancels a departure schedule due to weather or force majeure.
     *
     * @param int $hostId Host ID
     * @param int $scheduleId Schedule ID
     * @param string $cancelReason Explanation for cancellation
     * @return array Updated schedule details
     * @throws RuntimeException If unauthorized or invalid
     */
    public function cancelSchedule(int $hostId, int $scheduleId, string $cancelReason): array
    {
        $cleanReason = trim($cancelReason);
        if (mb_strlen($cleanReason, 'UTF-8') < 5) {
            throw new RuntimeException('Vui lòng nêu rõ lý do hủy lịch trình (tối thiểu 5 ký tự).');
        }

        $schedule = $this->scheduleModel->getById($scheduleId);
        if (!$schedule) {
            throw new RuntimeException('Lịch khởi hành không tồn tại.');
        }

        if ((int)$schedule['host_id'] !== $hostId) {
            throw new RuntimeException('Bạn không có quyền thao tác trên lịch khởi hành này.');
        }

        if ($schedule['status'] === 'cancelled') {
            throw new RuntimeException('Lịch khởi hành này đã bị hủy trước đó.');
        }

        $this->scheduleModel->cancel($hostId, $scheduleId, $cleanReason);

        return $this->scheduleModel->getById($scheduleId) ?? $schedule;
    }

    /**
     * Get single departure schedule by ID.
     */
    public function getSchedule(int $scheduleId): ?array
    {
        return $this->scheduleModel->getById($scheduleId);
    }

    /**
     * Get upcoming open schedules for a tour (for tourist booking form).
     */
    public function getAvailableSchedulesForService(int $serviceId): array
    {
        return $this->scheduleModel->getByServiceId($serviceId, true);
    }

    /**
     * Get all schedules for a tour (both past and future).
     */
    public function getAllSchedulesForService(int $serviceId): array
    {
        return $this->scheduleModel->getByServiceId($serviceId, false);
    }

    /**
     * Get all schedules managed by a host.
     */
    public function getHostSchedules(int $hostId, ?string $status = null): array
    {
        return $this->scheduleModel->getByHost($hostId, $status);
    }

    /**
     * Verify if schedule has enough remaining seats.
     */
    public function checkCapacity(int $scheduleId, int $seats): bool
    {
        return $this->scheduleModel->checkCapacity($scheduleId, $seats);
    }

    /**
     * Validate timeline consistency between starts_at, ends_at, and booking_closes_at.
     */
    private function validateScheduleTimings(array $data): array
    {
        $rawStarts = (string)($data['starts_at'] ?? '');
        $rawEnds = (string)($data['ends_at'] ?? '');
        $rawCloses = (string)($data['booking_closes_at'] ?? '');

        $startTimestamp = strtotime($rawStarts);
        $endTimestamp = strtotime($rawEnds);
        $closeTimestamp = strtotime($rawCloses);

        if ($startTimestamp === false || $endTimestamp === false || $closeTimestamp === false) {
            throw new RuntimeException('Định dạng thời gian không hợp lệ. Vui lòng chọn ngày giờ cụ thể.');
        }

        if ($startTimestamp <= time()) {
            throw new RuntimeException('Thời gian khởi hành phải ở một thời điểm trong tương lai.');
        }

        if ($endTimestamp <= $startTimestamp) {
            throw new RuntimeException('Thời gian kết thúc tour phải diễn ra sau thời gian khởi hành.');
        }

        if ($closeTimestamp > $startTimestamp) {
            throw new RuntimeException('Hạn đóng đặt chỗ phải diễn ra trước hoặc cùng thời điểm khởi hành.');
        }

        if ($closeTimestamp <= time()) {
            throw new RuntimeException('Hạn đóng đặt chỗ không được ở quá khứ.');
        }

        return [
            'starts_at' => date('Y-m-d H:i:s', $startTimestamp),
            'ends_at' => date('Y-m-d H:i:s', $endTimestamp),
            'booking_closes_at' => date('Y-m-d H:i:s', $closeTimestamp),
        ];
    }
}

if (!class_exists('ScheduleService', false)) {
    class_alias(ScheduleService::class, 'ScheduleService');
}
