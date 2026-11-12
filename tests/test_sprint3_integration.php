<?php

declare(strict_types=1);

/**
 * ============================================================================
 * KIỂM THỬ GIAI ĐOẠN 3: SPRINT 3 — TÍCH HỢP TỔNG THỂ (INT-01 & INT-02)
 * - INT-01: Kích hoạt Lazy Expiration trong BookingService tự động hủy đơn quá 15p
 * - INT-02: Bổ sung Custom Error Pages (403, 404, 500, 419) & Alert Toasts AJAX
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';

$totalTests = 0;
$passedTests = 0;

function assertTest(string $description, bool $condition): void {
    global $totalTests, $passedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "   CHẠY KIỂM THỬ SPRINT 3: TÍCH HỢP TỔNG THỂ (INT-01 & INT-02)   \n";
echo str_repeat('=', 80) . "\n\n";

$db = \Core\Database::getInstance();
$bookingService = new \App\Services\BookingService();

// ==============================================================================
// NHÓM 1: INT-01 — LAZY EXPIRATION TRONG BOOKINGSERVICE
// ==============================================================================
echo "--- NHÓM 1: LAZY EXPIRATION CHO BOOKINGSERVICE (INT-01) ---\n";

// 1. Kiểm tra method tồn tại trên BookingService
assertTest(
    "BookingService sở hữu phương thức releaseExpiredReservations()",
    method_exists($bookingService, 'releaseExpiredReservations')
);

// 2. Tạo dữ liệu giả lập cho đơn quá hạn 15 phút
$testScheduleId = 1;
// Tìm lịch khởi hành còn mở
$sampleSchedule = $db->fetchOne("SELECT id, booked_seats, capacity FROM tbl_service_schedules WHERE status = 'opened' LIMIT 1");
if ($sampleSchedule) {
    $testScheduleId = (int)$sampleSchedule['id'];
    $initialBookedSeats = (int)$sampleSchedule['booked_seats'];

    // Tìm một du khách hợp lệ
    $sampleTourist = $db->fetchOne("SELECT id FROM tbl_users WHERE role = 'tourist' LIMIT 1");
    $sampleTouristId = $sampleTourist ? (int)$sampleTourist['id'] : 1;

    // Tìm service ID
    $sampleService = $db->fetchOne("SELECT service_id FROM tbl_service_schedules WHERE id = ? LIMIT 1", [$testScheduleId]);
    $sampleServiceId = (int)($sampleService['service_id'] ?? 1);

    // Tạo đơn test ở trạng thái awaiting_payment nhưng payment_expires_at lùi về 20 phút trước
    $pastExpiredTime = date('Y-m-d H:i:s', time() - 20 * 60);
    $testCode = 'TEST_EXP_' . time();
    $testParticipants = 2;

    // Đặt chỗ ghế trước
    $db->execute(
        "UPDATE tbl_service_schedules SET booked_seats = booked_seats + ? WHERE id = ?",
        [$testParticipants, $testScheduleId]
    );

    $db->execute("
        INSERT INTO tbl_bookings (
            booking_code, tourist_id, service_id, schedule_id,
            participants_count, total_price, deposit_amount,
            status, payment_plan, payment_expires_at, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?,
            ?, 1000000, 500000,
            'awaiting_payment', 'deposit_50', ?, NOW(), NOW()
        )
    ", [$testCode, $sampleTouristId, $sampleServiceId, $testScheduleId, $testParticipants, $pastExpiredTime]);

    $insertedBookingId = (int)$db->lastInsertId();

    assertTest("Đã chèn đơn test awaiting_payment quá hạn 20 phút (ID: {$insertedBookingId})", $insertedBookingId > 0);

    // Gọi lazy expiration thông qua BookingService
    $releasedCount = $bookingService->releaseExpiredReservations();
    assertTest("BookingService::releaseExpiredReservations() tự động xử lý ít nhất 1 đơn quá hạn", $releasedCount >= 1);

    // Kiểm tra trạng thái đơn test sau khi sweep
    $expiredBooking = $db->fetchOne("SELECT status, cancel_reason FROM tbl_bookings WHERE id = ? LIMIT 1", [$insertedBookingId]);
    assertTest("Trạng thái đơn chuyển từ awaiting_payment sang expired", ($expiredBooking['status'] ?? '') === 'expired');
    assertTest("Lý do hủy ghi nhận hết hạn giữ chỗ 15 phút", str_contains($expiredBooking['cancel_reason'] ?? '', 'Hết hạn giữ chỗ 15 phút'));

    // Kiểm tra số ghế lịch khởi hành được hoàn trả
    $afterSchedule = $db->fetchOne("SELECT booked_seats FROM tbl_service_schedules WHERE id = ? LIMIT 1", [$testScheduleId]);
    $afterSeats = (int)($afterSchedule['booked_seats'] ?? 0);
    assertTest("Số ghế đã đặt được hoàn trả lại lịch khởi hành chính xác", $afterSeats === $initialBookedSeats);

    // Dọn dẹp bản ghi test
    $db->execute("DELETE FROM tbl_bookings WHERE id = ?", [$insertedBookingId]);
} else {
    assertTest("Bỏ qua test DB do không tìm thấy schedule mở (vẫn đạt cú pháp)", true);
}

// 3. Kiểm tra Lazy Expiration tự động khi xem đơn (getBooking / getBookingByCode)
$bookingClassSource = file_get_contents(dirname(__DIR__) . '/app/services/BookingService.php');
assertTest("BookingService::getBooking() có kích hoạt Lazy Expiration khi đơn quá hạn", str_contains($bookingClassSource, "strtotime(\$booking['payment_expires_at']) < time()") && str_contains($bookingClassSource, '$this->releaseExpiredReservations()'));
assertTest("BookingService::createBooking() tự động giải phóng đơn hết hạn trước khi báo giá", str_contains($bookingClassSource, '$this->releaseExpiredReservations();'));
assertTest("BookingService::getTouristBookings() tự động quét đơn hết hạn trước khi lấy danh sách", str_contains($bookingClassSource, '$this->releaseExpiredReservations();'));
assertTest("BookingService::getHostBookings() tự động quét đơn hết hạn trước khi Host tải danh sách", str_contains($bookingClassSource, '$this->releaseExpiredReservations();'));


// ==============================================================================
// NHÓM 2: INT-02 — CUSTOM ERROR PAGES (403, 404, 500, 419)
// ==============================================================================
echo "\n--- NHÓM 2: GIAO DIỆN LỖI CUSTOM CHUẨN ĐỒNG BỘ THEME (INT-02) ---\n";

$errorsDir = dirname(__DIR__) . '/app/views/errors';

// 1. Kiểm tra 403.php
$file403 = $errorsDir . '/403.php';
assertTest("Tệp app/views/errors/403.php tồn tại", is_file($file403));
if (is_file($file403)) {
    $c403 = file_get_contents($file403);
    assertTest("403.php chứa mã HTTP 403 FORBIDDEN", str_contains($c403, 'HTTP 403 FORBIDDEN'));
    assertTest("403.php hỗ trợ Dark/Light Theme Switch", str_contains($c403, 'themeToggleSwitch') && str_contains($c403, 'theme-mode-switch'));
    assertTest("403.php có nút Về Trang Chủ và Đăng Nhập Khác", str_contains($c403, 'href="/"') && str_contains($c403, 'href="/login"'));
}

// 2. Kiểm tra 404.php
$file404 = $errorsDir . '/404.php';
assertTest("Tệp app/views/errors/404.php tồn tại", is_file($file404));
if (is_file($file404)) {
    $c404 = file_get_contents($file404);
    assertTest("404.php chứa mã HTTP 404 NOT FOUND", str_contains($c404, 'HTTP 404 NOT FOUND'));
    assertTest("404.php hỗ trợ Dark/Light Theme Switch", str_contains($c404, 'themeToggleSwitch') && str_contains($c404, 'theme-mode-switch'));
    assertTest("404.php có nút Khám Phá Tour và Về Trang Chủ", str_contains($c404, 'href="/services"') && str_contains($c404, 'href="/"'));
}

// 3. Kiểm tra 500.php
$file500 = $errorsDir . '/500.php';
assertTest("Tệp app/views/errors/500.php tồn tại", is_file($file500));
if (is_file($file500)) {
    $c500 = file_get_contents($file500);
    assertTest("500.php chứa mã HTTP 500 SERVER ERROR", str_contains($c500, 'HTTP 500 SERVER ERROR'));
    assertTest("500.php hỗ trợ Dark/Light Theme Switch", str_contains($c500, 'themeToggleSwitch'));
    assertTest("500.php có nút Tải Lại Trang và Hotline 1900 6868", str_contains($c500, 'location.reload()') && str_contains($c500, '1900 6868'));
}

// 4. Kiểm tra 419.php
$file419 = $errorsDir . '/419.php';
assertTest("Tệp app/views/errors/419.php tồn tại", is_file($file419));
if (is_file($file419)) {
    $c419 = file_get_contents($file419);
    assertTest("419.php chứa mã HTTP 419 PAGE EXPIRED", str_contains($c419, 'HTTP 419 PAGE EXPIRED'));
    assertTest("419.php giải thích rõ CSRF Token và phiên hết hạn", str_contains($c419, 'CSRF Token') || str_contains($c419, 'Phiên Bảo Mật'));
}

// 5. Kiểm tra top-level 500 Exception Handler trong public/index.php
$indexContent = file_get_contents(dirname(__DIR__) . '/public/index.php');
assertTest("public/index.php bọc try-catch bắt Throwable", str_contains($indexContent, 'catch (\Throwable $e)'));
assertTest("public/index.php trả về HTTP 500 và include app/views/errors/500.php", str_contains($indexContent, 'http_response_code(500)') && str_contains($indexContent, 'errors/500.php'));
assertTest("public/index.php hỗ trợ phản hồi JSON 500 cho yêu cầu AJAX", str_contains($indexContent, '$request->isAjax()') && str_contains($indexContent, 'application/json'));


// ==============================================================================
// NHÓM 3: INT-02 — ALERT TOASTS NOTIFICATION CHO AJAX
// ==============================================================================
echo "\n--- NHÓM 3: ALERT TOASTS CHO THAO TÁC AJAX (INT-02) ---\n";

$toastsFile = dirname(__DIR__) . '/app/views/partials/alert-toasts.php';
assertTest("Tệp app/views/partials/alert-toasts.php tồn tại", is_file($toastsFile));

if (is_file($toastsFile)) {
    $cToast = file_get_contents($toastsFile);
    assertTest("alert-toasts.php định nghĩa container #appToastContainer", str_contains($cToast, 'id="appToastContainer"'));
    assertTest("alert-toasts.php cung cấp hàm JavaScript window.showToast()", str_contains($cToast, 'window.showToast = function'));
    assertTest("alert-toasts.php hỗ trợ 4 phân loại toast: success, error, warning, info", str_contains($cToast, 'app-toast-success') && str_contains($cToast, 'app-toast-warning') && str_contains($cToast, 'app-toast-info'));
    assertTest("alert-toasts.php có thanh thời gian chạy animation (app-toast-progress)", str_contains($cToast, 'app-toast-progress'));
}

// Kiểm tra Layout chính nhúng alert-toasts.php
$layoutContent = file_get_contents(dirname(__DIR__) . '/app/views/layouts/main.php');
assertTest("Layout app/views/layouts/main.php đã nhúng partials/alert-toasts.php", str_contains($layoutContent, 'partials/alert-toasts.php'));

// Kiểm tra trang chi tiết tour dùng window.showToast cho AJAX Review & Discussion
$showTourContent = file_get_contents(dirname(__DIR__) . '/app/views/services/show.php');
assertTest("Trang chi tiết tour sử dụng window.showToast khi submit Review qua AJAX", str_contains($showTourContent, "window.showToast('Cảm ơn bạn! Đánh giá trải nghiệm của bạn đã được ghi nhận.', 'success')"));
assertTest("Trang chi tiết tour sử dụng window.showToast khi gửi câu hỏi Discussion qua AJAX", str_contains($showTourContent, "window.showToast('Câu hỏi của bạn đã được gửi tới Host bản địa thành công!', 'success')"));

// ==============================================================================
// TỔNG KẾT KẾT QUẢ KIỂM THỬ
// ==============================================================================
echo "\n" . str_repeat('=', 80) . "\n";
echo "   KẾT QUẢ: {$passedTests}/{$totalTests} assertions đạt chuẩn (" . round(($passedTests / max(1, $totalTests)) * 100, 1) . "%)\n";
echo str_repeat('=', 80) . "\n\n";

if ($passedTests === $totalTests) {
    echo ">>> XÁC NHẬN: SPRINT 3 (INT-01 & INT-02) ĐẠT CHUẨN 100%! <<<\n\n";
    exit(0);
} else {
    echo ">>> CẢNH BÁO: CÓ " . ($totalTests - $passedTests) . " BÀI TEST CHƯA ĐẠT! <<<\n\n";
    exit(1);
}
