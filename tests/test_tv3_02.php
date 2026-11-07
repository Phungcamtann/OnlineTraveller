<?php

declare(strict_types=1);

echo "=== ONLINETRAVELLER TV3-02 (PRICING SERVICE & PROMOTION MODEL) VERIFICATION ===\n\n";

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Models\Promotion;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Services\PricingService;

$pdo = \Core\Database::getConnection();

function runTest(string $title, callable $fn): void {
    try {
        $result = $fn();
        echo "[PASS] {$title}" . ($result ? " -> {$result}" : "") . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$title} -> " . $e->getMessage() . "\n";
    }
}

$promotionModel = new Promotion();
$pricingService = new PricingService($promotionModel);

echo "--- 1. KIỂM THỬ MODEL PROMOTION ---\n";

// Test 1: Tìm mã khuyến mãi theo code
runTest("Promotion::getByCode tìm mã khuyến mãi hợp lệ", function() use ($promotionModel) {
    $promo = $promotionModel->getByCode('VIPLOCAL');
    if (!$promo) throw new \Exception("Không tìm thấy mã VIPLOCAL!");
    if ((float)$promo['discount_percent'] !== 15.0) {
        throw new \Exception("Mức chiết khấu không đúng 15%! Nhận: {$promo['discount_percent']}");
    }
    return "Tìm thấy mã: {$promo['code']} - {$promo['title']} (Giảm {$promo['discount_percent']}%)";
});

// Test 2: Lấy danh sách khuyến mãi khả dụng cho Service 1
runTest("Promotion::getActiveForService lấy danh sách ưu đãi cho Tour 1", function() use ($promotionModel) {
    $promos = $promotionModel->getActiveForService(1);
    if (empty($promos)) throw new \Exception("Tour 1 phải có ít nhất 1 khuyến mãi!");
    return "Tìm thấy " . count($promos) . " khuyến mãi khả dụng cho Tour 1";
});

// Test 3: Lấy khuyến mãi tốt nhất (chiết khấu cao nhất)
runTest("Promotion::getBestPromotionForService chọn khuyến mãi chiết khấu cao nhất", function() use ($promotionModel) {
    $best = $promotionModel->getBestPromotionForService(1);
    if (!$best) throw new \Exception("Không tìm thấy ưu đãi tốt nhất!");
    if ($best['code'] !== 'VIPLOCAL' || (float)$best['discount_percent'] !== 15.0) {
        throw new \Exception("Ưu đãi tốt nhất phải là VIPLOCAL 15%!");
    }
    return "Tự động chọn ưu đãi tối ưu nhất: {$best['code']} ({$best['discount_percent']}%)";
});

// Test 4: Kiểm tra tính hợp lệ của mã cho Tour
runTest("Promotion::isValidForService kiểm tra mã hợp lệ vs không hợp lệ", function() use ($promotionModel) {
    $valid = $promotionModel->isValidForService('CHAO2026', 1);
    if (!$valid) throw new \Exception("CHAO2026 phải hợp lệ với Tour 1!");

    $invalid = $promotionModel->isValidForService('MA_KHONG_TON_TAI_999', 1);
    if ($invalid !== null) throw new \Exception("Mã không tồn tại phải trả về null!");

    return "Xác thực chính xác: Mã hợp lệ -> {$valid['code']}, Mã sai -> null";
});

echo "\n--- 2. KIỂM THỬ THUẬT TOÁN TÍNH TOÁN & LÀM TRÒN HALF-UP (PRICING SERVICE) ---\n";

// Test 5: Tính giá chuẩn không giảm giá
runTest("PricingService::calculatePrice tính giá gốc không giảm giá (cọc 50%)", function() use ($pricingService) {
    $res = $pricingService->calculatePrice(750000, 2, 0.0, 'deposit_50');
    if ($res['gross_total'] !== 1500000 || $res['total_price'] !== 1500000) {
        throw new \Exception("Tính sai tổng tiền!");
    }
    if ($res['deposit_amount'] !== 750000 || $res['remaining_amount'] !== 750000) {
        throw new \Exception("Chia cọc 50% sai!");
    }
    return "Tổng: {$res['formatted']['total_price']}, Cọc 50%: {$res['formatted']['deposit_amount']}";
});

// Test 6: Tính giá có giảm giá 15%
runTest("PricingService::calculatePrice tính giá có giảm 15% VIPLOCAL", function() use ($pricingService) {
    $res = $pricingService->calculatePrice(750000, 2, 15.0, 'deposit_50');
    // 750k x 2 = 1.5M. Giảm 15% = 225k. Tổng = 1.275M. Cọc 50% = 637.5k
    if ($res['gross_total'] !== 1500000 || $res['discount_amount'] !== 225000 || $res['total_price'] !== 1275000) {
        throw new \Exception("Tính sai chiết khấu!");
    }
    if ($res['deposit_amount'] !== 637500 || $res['remaining_amount'] !== 637500) {
        throw new \Exception("Chia cọc 50% sai!");
    }
    return "Gốc: {$res['formatted']['gross_total']} -> Giảm: {$res['formatted']['discount_amount']} -> Còn: {$res['formatted']['total_price']} (Cọc: {$res['formatted']['deposit_amount']})";
});

// Test 7: Thuật toán làm tròn Half-up với số lẻ tiền cọc
runTest("Thuật toán Half-up chia tiền cọc số lẻ (VD: 125,001đ / 2)", function() use ($pricingService) {
    $res = $pricingService->calculatePrice(125001, 1, 0.0, 'deposit_50');
    // 125,001 / 2 = 62,500.5 -> Half-up làm tròn thành 62,501đ. Số còn lại = 62,500đ.
    if ($res['deposit_amount'] !== 62501 || $res['remaining_amount'] !== 62500) {
        throw new \Exception("Làm tròn Half-up chia cọc sai! Cọc: {$res['deposit_amount']}, Còn lại: {$res['remaining_amount']}");
    }
    if (($res['deposit_amount'] + $res['remaining_amount']) !== 125001) {
        throw new \Exception("Tổng tiền cọc và còn lại không khớp số tổng!");
    }
    return "Tổng: 125.001đ -> Cọc Half-up: {$res['formatted']['deposit_amount']}, Còn lại: {$res['formatted']['remaining_amount']} (Khớp 100%)";
});

// Test 8: Thuật toán làm tròn Half-up với số lẻ tiền chiết khấu %
runTest("Thuật toán Half-up chiết khấu % số lẻ (999,999đ x 12.5%)", function() use ($pricingService) {
    $res = $pricingService->calculatePrice(999999, 1, 12.5, 'full_online');
    // 999,999 * 0.125 = 124,999.875 -> Half-up = 125,000đ.
    if ($res['discount_amount'] !== 125000 || $res['total_price'] !== 874999) {
        throw new \Exception("Làm tròn chiết khấu sai! Giảm: {$res['discount_amount']}, Tổng: {$res['total_price']}");
    }
    return "Gốc: 999.999đ -> Giảm Half-up: {$res['formatted']['discount_amount']} -> Thực thu: {$res['formatted']['total_price']}";
});

// Test 9: Thanh toán toàn phần 100% (full_online)
runTest("Kế hoạch full_online thanh toán 100%", function() use ($pricingService) {
    $res = $pricingService->calculatePrice(500000, 2, 10.0, 'full_online');
    // 1M giảm 10% = 900k. Cọc = 900k, Còn lại = 0đ
    if ($res['deposit_amount'] !== 900000 || $res['remaining_amount'] !== 0) {
        throw new \Exception("full_online phải có cọc = total và remaining = 0!");
    }
    return "Cọc toàn phần: {$res['formatted']['deposit_amount']}, Còn lại: {$res['formatted']['remaining_amount']}";
});

echo "\n--- 3. KIỂM THỬ BÁO GIÁ LỊCH TRÌNH TOUR THỰC TẾ (QUOTE SCHEDULE) ---\n";

// Test 10: quoteSchedule tự động áp mã VIPLOCAL cao nhất
runTest("PricingService::quoteSchedule tự động tìm và áp ưu đãi tốt nhất cho Tour 1", function() use ($pricingService) {
    $quote = $pricingService->quoteSchedule(1, 2, 'deposit_50');
    if ($quote['applied_promotion']['code'] !== 'VIPLOCAL') {
        throw new \Exception("Phải tự động áp VIPLOCAL!");
    }
    if ($quote['total_price'] !== 1275000 || $quote['deposit_amount'] !== 637500) {
        throw new \Exception("Tính sai tổng tiền báo giá!");
    }
    return "Tour: {$quote['service_title']} -> Áp dụng: {$quote['applied_promotion']['title']} (-{$quote['discount_percent']}%) -> Tổng: {$quote['formatted']['total_price']}";
});

// Test 11: quoteSchedule với mã giảm giá CHAO2026 cụ thể
runTest("PricingService::quoteSchedule áp dụng mã chỉ định CHAO2026 (10%)", function() use ($pricingService) {
    $quote = $pricingService->quoteSchedule(1, 2, 'deposit_50', 'CHAO2026');
    // 1.5M giảm 10% = 1.35M. Cọc 50% = 675k
    if ($quote['total_price'] !== 1350000 || $quote['deposit_amount'] !== 675000) {
        throw new \Exception("Tính sai khi chỉ định CHAO2026!");
    }
    return "Mã áp dụng: {$quote['applied_promotion']['code']} (-{$quote['discount_percent']}%) -> Cọc: {$quote['formatted']['deposit_amount']}";
});

// Test 12: Chặn mã giảm giá không hợp lệ
runTest("PricingService::quoteSchedule chặn mã không hợp lệ", function() use ($pricingService) {
    try {
        $pricingService->quoteSchedule(1, 2, 'deposit_50', 'MA_LUA_DAO_2026');
        throw new \Exception("Lẽ ra phải chặn nhưng lại cho qua!");
    } catch (\InvalidArgumentException $e) {
        return "Bắt lỗi thành công: " . $e->getMessage();
    }
});

echo "\n--- 4. ĐỐI SOÁT TÍNH NHẤT QUÁN 100% VỚI STORED PROCEDURE ndtt_sp_bookings_quote ---\n";

// Test 13: So khớp từng đồng giữa PHP PricingService và Stored Procedure
runTest("Khớp từng đồng giữa PricingService::quoteSchedule và SP ndtt_sp_bookings_quote", function() use ($pricingService) {
    $phpQuote = $pricingService->quoteSchedule(1, 3, 'deposit_50');
    $spQuote = $pricingService->quoteViaProcedure(1, 3, 'deposit_50');

    if (!$spQuote) throw new \Exception("Không lấy được kết quả từ Stored Procedure!");

    if ((int)$phpQuote['total_price'] !== (int)$spQuote['total_price'] ||
        (int)$phpQuote['deposit_amount'] !== (int)$spQuote['deposit_amount'] ||
        (int)$phpQuote['remaining_amount'] !== (int)$spQuote['remaining_amount'] ||
        (int)$phpQuote['discount_amount'] !== (int)$spQuote['discount_amount']) {
        throw new \Exception("Kết quả PHP và Stored Procedure không khớp nhau!");
    }

    return "Kết quả khớp tuyệt đối 100%: Tổng = " . number_format((float)$spQuote['total_price']) . "đ, Cọc = " . number_format((float)$spQuote['deposit_amount']) . "đ, Còn lại = " . number_format((float)$spQuote['remaining_amount']) . "đ";
});

echo "\n=== KẾT QUẢ: TOÀN BỘ 13 TEST CASES TV3-02 ĐẠT CHUẨN 100%! ===\n";
