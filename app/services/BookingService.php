<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\ServiceSchedule;
use App\Models\Service;
use App\Services\PricingService;
use InvalidArgumentException;
use RuntimeException;

/**
 * BookingService: Orchestrates tourist reservation submission, quotation calculation,
 * host approval, cancellation, and seat management.
 */
class BookingService
{
    protected Booking $bookingModel;
    protected PricingService $pricingService;
    protected ServiceSchedule $scheduleModel;
    protected Service $serviceModel;

    public function __construct(
        ?Booking $bookingModel = null,
        ?PricingService $pricingService = null,
        ?ServiceSchedule $scheduleModel = null,
        ?Service $serviceModel = null
    ) {
        $this->bookingModel = $bookingModel ?? new Booking();
        $this->pricingService = $pricingService ?? new PricingService();
        $this->scheduleModel = $scheduleModel ?? new ServiceSchedule();
        $this->serviceModel = $serviceModel ?? new Service();
    }

    /**
     * Tourist submits a new booking request for a departure schedule.
     * System quotes price with promotions and creates reservation in 'pending' status.
     *
     * @param int $touristId ID của du khách đặt tour
     * @param int $scheduleId ID lịch khởi hành
     * @param int $participantsCount Số khách đăng ký
     * @param string $paymentPlan 'deposit_50', 'full_online', 'host_direct', 'none'
     * @param string|null $promoCode Mã ưu đãi nếu du khách nhập
     * @return array Bản ghi booking vừa được tạo kèm thông tin báo giá
     * @throws InvalidArgumentException khi dữ liệu không hợp lệ hoặc lịch đã đóng/hết chỗ
     */
    public function createBooking(
        int $touristId,
        int $scheduleId,
        int $participantsCount,
        string $paymentPlan = 'deposit_50',
        ?string $promoCode = null
    ): array {
        if ($participantsCount < 1) {
            throw new InvalidArgumentException('Số lượng khách tham gia phải từ 1 người trở lên.');
        }

        if (!in_array($paymentPlan, ['deposit_50', 'full_online', 'host_direct', 'none'], true)) {
            $paymentPlan = 'deposit_50';
        }

        // 1. Tính toán báo giá và kiểm tra điều kiện lịch khởi hành
        $quote = $this->pricingService->quoteSchedule($scheduleId, $participantsCount, $paymentPlan, $promoCode);

        // 2. Tạo bản ghi đặt tour thông qua Stored Procedure ndtt_sp_bookings_create
        $created = $this->bookingModel->create($touristId, $scheduleId, $participantsCount, $paymentPlan);

        if (empty($created) || empty($created['id'])) {
            throw new RuntimeException('Không thể khởi tạo đơn đặt chỗ. Vui lòng thử lại sau.');
        }

        // 3. Lấy thông tin chi tiết đầy đủ của đơn đặt vừa tạo
        $fullBooking = $this->bookingModel->getById((int)$created['id']);

        return array_merge($fullBooking ?? $created, [
            'quote_summary' => $quote,
        ]);
    }

    /**
     * Get booking details by ID with optional authorization check.
     *
     * @param int $bookingId ID đơn đặt
     * @param int|null $userId ID người dùng yêu cầu xem (nếu cần kiểm tra quyền)
     * @param string|null $userRole Quyền người dùng ('tourist', 'host', 'admin')
     * @return array Chi tiết đơn đặt
     * @throws RuntimeException khi không tìm thấy hoặc không có quyền truy cập
     */
    public function getBooking(int $bookingId, ?int $userId = null, ?string $userRole = null): array
    {
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            throw new RuntimeException('Không tìm thấy đơn đặt chỗ yêu cầu.');
        }

        if ($userId !== null && $userRole !== 'admin') {
            $isTouristOwner = ((int)$booking['tourist_id'] === $userId);
            $isHostOwner = ((int)$booking['host_id'] === $userId);

            if (!$isTouristOwner && !$isHostOwner) {
                throw new RuntimeException('Bạn không có quyền truy cập thông tin đơn đặt chỗ này.');
            }
        }

        return $booking;
    }

    /**
     * Get booking details by booking code.
     */
    public function getBookingByCode(string $code, ?int $userId = null, ?string $userRole = null): array
    {
        $booking = $this->bookingModel->getByCode($code);
        if (!$booking) {
            throw new RuntimeException("Không tìm thấy đơn hàng mang mã [{$code}].");
        }

        if ($userId !== null && $userRole !== 'admin') {
            $isTouristOwner = ((int)$booking['tourist_id'] === $userId);
            $isHostOwner = ((int)$booking['host_id'] === $userId);

            if (!$isTouristOwner && !$isHostOwner) {
                throw new RuntimeException('Bạn không có quyền truy cập thông tin đơn đặt chỗ này.');
            }
        }

        return $booking;
    }

    /**
     * Get all bookings submitted by a specific tourist.
     */
    public function getTouristBookings(int $touristId, ?string $status = null): array
    {
        return $this->bookingModel->getByTourist($touristId, $status);
    }

    /**
     * Get all bookings received by a specific host across all tours.
     */
    public function getHostBookings(int $hostId, ?string $status = null): array
    {
        return $this->bookingModel->getByHost($hostId, $status);
    }

    /**
     * Host reviews and approves a pending booking (reserves seats).
     *
     * @param int $hostId ID của Host quản lý tour
     * @param int $bookingId ID đơn đặt chỗ
     * @return array Kết quả phê duyệt
     */
    public function approveBooking(int $hostId, int $bookingId): array
    {
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            throw new RuntimeException('Không tìm thấy đơn đặt chỗ.');
        }

        if ((int)$booking['host_id'] !== $hostId) {
            throw new RuntimeException('Bạn không phải người quản lý gói trải nghiệm này.');
        }

        if ($booking['status'] !== 'pending') {
            throw new RuntimeException("Chỉ có thể phê duyệt đơn ở trạng thái chờ duyệt (Hiện tại: {$booking['status']}).");
        }

        return $this->bookingModel->approve($hostId, $bookingId);
    }

    /**
     * Cancel an existing booking and release reserved seats.
     *
     * @param int $actorId ID người thực hiện hủy
     * @param int $bookingId ID đơn đặt
     * @param string $reason Lý do hủy
     * @return array Kết quả hủy
     */
    public function cancelBooking(int $actorId, int $bookingId, string $reason): array
    {
        if (trim($reason) === '') {
            $reason = 'Người dùng chủ động hủy yêu cầu đặt chỗ.';
        }

        return $this->bookingModel->cancel($actorId, $bookingId, trim($reason));
    }

    /**
     * Host rejects a pending booking with explanation.
     */
    public function rejectBooking(int $hostId, int $bookingId, string $reason): array
    {
        if (trim($reason) === '') {
            $reason = 'Host không thể sắp xếp tiếp đón đợt này.';
        }

        return $this->bookingModel->reject($hostId, $bookingId, trim($reason));
    }
}

if (!class_exists('BookingService', false)) {
    class_alias(BookingService::class, 'BookingService');
}
