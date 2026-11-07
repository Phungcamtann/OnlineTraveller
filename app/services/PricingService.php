<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Promotion;
use App\Models\Service;
use App\Models\ServiceSchedule;
use Core\Database;
use InvalidArgumentException;

/**
 * PricingService: Handles tour price calculation, promotion discount selection,
 * Half-up rounding logic to individual dong, and 50% deposit / 100% full payment splitting.
 */
class PricingService
{
    protected Promotion $promotionModel;
    protected Service $serviceModel;
    protected ServiceSchedule $scheduleModel;

    public function __construct(
        ?Promotion $promotionModel = null,
        ?Service $serviceModel = null,
        ?ServiceSchedule $scheduleModel = null
    ) {
        $this->promotionModel = $promotionModel ?? new Promotion();
        $this->serviceModel = $serviceModel ?? new Service();
        $this->scheduleModel = $scheduleModel ?? new ServiceSchedule();
    }

    /**
     * Pure calculation engine: Đơn giá × số người, trừ khuyến mãi %,
     * làm tròn Half-up đến từng đồng, chia 50% cọc / 100% toàn phần.
     *
     * @param float $unitPrice Đơn giá mỗi khách (VND)
     * @param int $count Số lượng khách (>= 1)
     * @param float $discountPercent Phần trăm chiết khấu (0 - 90%)
     * @param string $paymentPlan Phương thức thanh toán: 'deposit_50', 'full_online', 'host_direct'
     * @return array Kết quả chi tiết báo giá đã làm tròn Half-up
     */
    public function calculatePrice(
        float $unitPrice,
        int $count,
        float $discountPercent = 0.0,
        string $paymentPlan = 'deposit_50'
    ): array {
        $count = max(1, $count);
        $unitPrice = max(0.0, $unitPrice);
        $discountPercent = max(0.0, min(90.0, $discountPercent));

        // 1. Tổng tiền gốc (Gross total)
        $grossTotal = (int) round($unitPrice * $count, 0, PHP_ROUND_HALF_UP);

        // 2. Tiền giảm giá theo % làm tròn Half-up đến từng đồng
        $discountAmount = 0;
        if ($discountPercent > 0.0 && $grossTotal > 0) {
            $discountAmount = (int) round($grossTotal * ($discountPercent / 100.0), 0, PHP_ROUND_HALF_UP);
        }

        // 3. Tổng tiền thực trả sau giảm giá (Net total)
        $totalPrice = max(0, $grossTotal - $discountAmount);

        // 4. Chia khoản thanh toán theo Payment Plan
        $depositAmount = 0;
        $remainingAmount = 0;

        if ($paymentPlan === 'deposit_50') {
            // Cọc 50%: làm tròn Half-up cho tiền cọc, số dư còn lại = total - deposit
            // Bảo đảm deposit + remaining luôn chính xác 100% bằng total_price dù số lẻ
            $depositAmount = (int) round($totalPrice / 2.0, 0, PHP_ROUND_HALF_UP);
            $remainingAmount = $totalPrice - $depositAmount;
        } elseif ($paymentPlan === 'full_online') {
            $depositAmount = $totalPrice;
            $remainingAmount = 0;
        } else {
            // 'host_direct' hoặc tour miễn phí
            $depositAmount = 0;
            $remainingAmount = $totalPrice;
        }

        return [
            'unit_price' => (int) round($unitPrice, 0, PHP_ROUND_HALF_UP),
            'participants_count' => $count,
            'gross_total' => $grossTotal,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
            'payment_plan' => $paymentPlan,
            'deposit_amount' => $depositAmount,
            'remaining_amount' => $remainingAmount,
            'formatted' => [
                'unit_price' => self::formatVnd((int) round($unitPrice, 0, PHP_ROUND_HALF_UP)),
                'gross_total' => self::formatVnd($grossTotal),
                'discount_amount' => self::formatVnd($discountAmount),
                'total_price' => self::formatVnd($totalPrice),
                'deposit_amount' => self::formatVnd($depositAmount),
                'remaining_amount' => self::formatVnd($remainingAmount),
            ],
        ];
    }

    /**
     * Báo giá hoàn chỉnh cho một lịch trình tour cụ thể (Schedule Quote).
     * Tự động áp dụng khuyến mãi cao nhất hoặc mã giảm giá được cung cấp.
     *
     * @param int $scheduleId ID lịch khởi hành
     * @param int $participantsCount Số khách đăng ký
     * @param string $paymentPlan Kế hoạch trả tiền ('deposit_50', 'full_online', 'host_direct')
     * @param string|null $promoCode Mã giảm giá du khách nhập (nếu có)
     * @return array Báo giá chi tiết kèm thông tin tour và chương trình khuyến mãi
     * @throws InvalidArgumentException khi dữ liệu không hợp lệ hoặc lịch đã đóng
     */
    public function quoteSchedule(
        int $scheduleId,
        int $participantsCount,
        string $paymentPlan = 'deposit_50',
        ?string $promoCode = null
    ): array {
        $participantsCount = max(1, $participantsCount);

        // 1. Lấy thông tin lịch khởi hành và tour
        $schedule = $this->scheduleModel->getById($scheduleId);
        if (!$schedule) {
            throw new InvalidArgumentException('Không tìm thấy lịch khởi hành yêu cầu.');
        }

        if ($schedule['status'] !== 'open') {
            throw new InvalidArgumentException('Lịch khởi hành này không còn mở đăng ký (Trạng thái: ' . $schedule['status'] . ').');
        }

        if (strtotime($schedule['booking_closes_at']) <= time()) {
            throw new InvalidArgumentException('Đã quá thời hạn nhận đăng ký cho chuyến đi này.');
        }

        // Kiểm tra sức chứa
        $availableSeats = (int)$schedule['available_seats'];
        if ($participantsCount > $availableSeats) {
            throw new InvalidArgumentException("Số khách đăng ký ({$participantsCount}) vượt quá số chỗ trống còn lại ({$availableSeats} chỗ).");
        }

        $serviceId = (int)$schedule['service_id'];
        $priceMode = (string)($schedule['price_mode'] ?? 'fixed_choice');
        $basePrice = (float)($schedule['base_price'] ?? 0);

        // 2. Xử lý chế độ giá (Price Mode)
        $effectiveUnitPrice = $basePrice;
        if ($priceMode === 'free') {
            $effectiveUnitPrice = 0.0;
            $paymentPlan = 'host_direct';
        } elseif ($priceMode === 'prepaid_full') {
            // Tour bắt buộc thanh toán 100% online trước
            $paymentPlan = 'full_online';
        }

        // 3. Xử lý khuyến mãi (Promotion)
        $appliedPromotion = null;
        $discountPercent = 0.0;

        if ($promoCode !== null && trim($promoCode) !== '') {
            $code = trim($promoCode);
            $validPromo = $this->promotionModel->isValidForService($code, $serviceId);
            if (!$validPromo) {
                throw new InvalidArgumentException("Mã ưu đãi '{$code}' không tồn tại, đã hết hạn hoặc không áp dụng cho tour này.");
            }
            $appliedPromotion = $validPromo;
            $discountPercent = (float)$validPromo['discount_percent'];
        } elseif ($priceMode !== 'free') {
            // Tự động tìm khuyến mãi cao nhất còn hiệu lực cho tour này
            $bestPromo = $this->promotionModel->getBestPromotionForService($serviceId);
            if ($bestPromo) {
                $appliedPromotion = $bestPromo;
                $discountPercent = (float)$bestPromo['discount_percent'];
            }
        }

        // 4. Tính toán số tiền chi tiết theo công thức Half-up
        $calc = $this->calculatePrice($effectiveUnitPrice, $participantsCount, $discountPercent, $paymentPlan);

        return array_merge([
            'schedule_id' => $scheduleId,
            'service_id' => $serviceId,
            'service_title' => $schedule['service_title'],
            'service_slug' => $schedule['service_slug'],
            'starts_at' => $schedule['starts_at'],
            'ends_at' => $schedule['ends_at'],
            'meeting_point' => $schedule['meeting_point'],
            'price_mode' => $priceMode,
            'applied_promotion' => $appliedPromotion ? [
                'id' => (int)$appliedPromotion['id'],
                'code' => $appliedPromotion['code'],
                'title' => $appliedPromotion['title'],
                'discount_percent' => (float)$appliedPromotion['discount_percent'],
            ] : null,
        ], $calc);
    }

    /**
     * Báo giá trực tiếp thông qua Stored Procedure CSDL `ndtt_sp_bookings_quote`.
     */
    public function quoteViaProcedure(int $scheduleId, int $participantsCount, string $paymentPlan = 'deposit_50'): ?array
    {
        $db = Database::getInstance();
        $rows = $db->callProcedure('ndtt_sp_bookings_quote', [$scheduleId, $participantsCount, $paymentPlan]);
        return $rows[0] ?? null;
    }

    /**
     * Định dạng số tiền VNĐ chuẩn Việt Nam (VD: 1.250.000 đ).
     */
    public static function formatVnd(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

if (!class_exists('PricingService', false)) {
    class_alias(PricingService::class, 'PricingService');
}
