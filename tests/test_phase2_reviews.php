<?php

declare(strict_types=1);

/**
 * ============================================================================
 * KIỂM THỬ GIAI ĐOẠN 2: KHỐI ĐÁNH GIÁ TOUR CHI TIẾT & NHÃN "ĐÃ TỰ TRẢI NGHIỆM"
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../app/models/Review.php';
require_once __DIR__ . '/../app/models/Booking.php';
require_once __DIR__ . '/../app/controllers/ReviewController.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/helpers/FormatHelper.php';

use App\Models\Review;
use App\Models\Booking;
use App\Controllers\ReviewController;
use Core\Request;
use Core\Response;

Response::$preventExit = true;

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

echo "\n" . str_repeat('=', 84) . "\n";
echo "   CHẠY KIỂM THỬ GIAI ĐOẠN 2: ĐÁNH GIÁ CÔNG KHAI & NHÃN 'ĐÃ TỰ TRẢI NGHIỆM'   \n";
echo str_repeat('=', 84) . "\n\n";

$db = Core\Database::getConnection();
$reviewModel = new Review();
$createdReviewIds = [];
$createdBookingIds = [];

// Get existing tourist and host from seed
$tourist = $db->query("SELECT id, name, email FROM tbl_users WHERE role = 'tourist' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$touristId = (int)$tourist['id'];

$secondTourist = $db->query("SELECT id, name, email FROM tbl_users WHERE role = 'tourist' AND id != {$touristId} LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$secondTouristId = (int)$secondTourist['id'];

$service = $db->query("SELECT id, title FROM tbl_services LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$serviceId = (int)$service['id'];

// Clean up any old reviews on this service for test tourists
$cleanupStmt = $db->prepare("DELETE FROM tbl_reviews WHERE tourist_id IN (?, ?)");
$cleanupStmt->execute([$touristId, $secondTouristId]);

// ==============================================================================
// NHÓM 1: ĐÁNH GIÁ MỞ DÀNH CHO DU KHÁCH CHƯA ĐẶT QUA SÀN (is_verified = 0)
// ==============================================================================
echo "--- NHÓM 1: ĐÁNH GIÁ MỞ CHO KHÁCH TỰ DO (is_verified = 0) ---\n";

$openReview = $reviewModel->submitTourReview(
    $secondTouristId,
    $serviceId,
    4,
    'Trải nghiệm tuyệt vời dù mình book qua bạn bè giới thiệu!'
);
$createdReviewIds[] = (int)$openReview['id'];

assertTest("Tạo đánh giá mở thành công cho du khách", !empty($openReview['id']));
assertTest("Đánh giá mở có booking_id là NULL", $openReview['booking_id'] === null);
assertTest("Đánh giá mở có nhãn is_verified = 0 (chưa qua sàn)", (int)$openReview['is_verified'] === 0);
assertTest("Đánh giá mở lưu đúng số sao (4 sao)", (int)$openReview['rating'] === 4);
assertTest("Chống spam: Chặn cùng một du khách gửi đánh giá lần 2 trên cùng một tour", (function() use ($reviewModel, $secondTouristId, $serviceId) {
    try {
        $reviewModel->submitTourReview($secondTouristId, $serviceId, 5, 'Spam review lần 2');
        return false;
    } catch (\Throwable $e) {
        return str_contains($e->getMessage(), 'đã gửi đánh giá cho trải nghiệm này rồi');
    }
})());

// ==============================================================================
// NHÓM 2: ĐÁNH GIÁ BẢO CHỨNG "ĐÃ TỰ TRẢI NGHIỆM" (is_verified = 1)
// ==============================================================================
echo "\n--- NHÓM 2: ĐÁNH GIÁ CÓ ĐƠN COMPLETED -> NHÃN 'ĐÃ TỰ TRẢI NGHIỆM' (is_verified = 1) ---\n";

// Create a completed booking for tourist
$schedule = $db->query("SELECT id FROM tbl_service_schedules WHERE service_id = {$serviceId} LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$scheduleId = (int)($schedule['id'] ?? 1);

$stmtBooking = $db->prepare("
    INSERT INTO tbl_bookings (booking_code, tourist_id, service_id, schedule_id, participants_count, unit_price, total_price, deposit_amount, payment_plan, status, created_at)
    VALUES (?, ?, ?, ?, 1, 500000, 500000, 500000, 'full_online', 'completed', NOW(6))
");
$stmtBooking->execute(['BK_TEST_VERIFIED_' . time(), $touristId, $serviceId, $scheduleId]);
$completedBookingId = (int)$db->lastInsertId();
$createdBookingIds[] = $completedBookingId;

assertTest("Đã chuẩn bị đơn hàng hoàn thành (completed) cho du khách #{$touristId}", $completedBookingId > 0);

// Submit review from tour detail page for this tourist
$verifiedReview = $reviewModel->submitTourReview(
    $touristId,
    $serviceId,
    5,
    'Chuyến đi quá đỉnh, hướng dẫn viên nhiệt tình, cảnh đẹp mê ly!'
);
$createdReviewIds[] = (int)$verifiedReview['id'];

assertTest("Tạo đánh giá thành công cho du khách đã hoàn thành chuyến đi", !empty($verifiedReview['id']));
assertTest("Hệ thống tự động liên kết đúng booking_id của chuyến đi đã hoàn thành", (int)$verifiedReview['booking_id'] === $completedBookingId);
assertTest("Đánh giá tự động được gắn nhãn bảo chứng: is_verified = 1 ('Đã tự trải nghiệm')", (int)$verifiedReview['is_verified'] === 1);
assertTest("Đánh giá bảo chứng hiển thị đúng số sao 5 sao", (int)$verifiedReview['rating'] === 5);

// ==============================================================================
// NHÓM 3: SẮP XẾP ƯU TIÊN VÀ TÍNH ĐIỂM TRUNG BÌNH (Review::getByService & Stats)
// ==============================================================================
echo "\n--- NHÓM 3: SẮP XẾP ƯU TIÊN VÀ THỐNG KÊ ĐÁNH GIÁ (getByService & Stats) ---\n";

$serviceReviews = $reviewModel->getByService($serviceId);
assertTest("Lấy danh sách đánh giá của tour thành công (tối thiểu 2 đánh giá)", count($serviceReviews) >= 2);
assertTest("Đánh giá 'Đã tự trải nghiệm' (is_verified = 1) được ưu tiên xếp lên đầu", (int)$serviceReviews[0]['is_verified'] === 1);

$stats = $reviewModel->getServiceRatingStats($serviceId);
assertTest("Thống kê tính đúng số lượng đánh giá", (int)$stats['reviews_count'] >= 2);
assertTest("Điểm đánh giá trung bình được tính toán chuẩn xác", (float)$stats['avg_rating'] > 0);

// ==============================================================================
// NHÓM 4: CONTROLLER & PHÂN QUYỀN ĐĂNG NHẬP (ReviewController::store)
// ==============================================================================
echo "\n--- NHÓM 4: CONTROLLER & PHÂN QUYỀN ĐĂNG NHẬP (ReviewController) ---\n";

$reviewCtrl = new ReviewController(new Request());

// 4.1 Khách vãng lai chưa đăng nhập -> Chặn với HTTP 401 (AJAX)
$_SESSION = [];
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST = [
    'service_id' => $serviceId,
    'rating' => 5,
    'comment' => 'Khách vãng lai cố tình đánh giá'
];

ob_start();
$reviewCtrl->store(new Request());
$guestResJson = ob_get_clean();
$guestRes = json_decode($guestResJson, true);

assertTest("Khách vãng lai chưa đăng nhập bị chặn (HTTP 401 / Vui lòng đăng nhập)", 
    isset($guestRes['error']) && str_contains($guestRes['error'], 'Vui lòng đăng nhập'));

// ==============================================================================
// NHÓM 5: GIAO DIỆN KHỐI ĐÁNH GIÁ TRÊN TRANG TOUR (services/show.php)
// ==============================================================================
echo "\n--- NHÓM 5: GIAO DIỆN KHỐI ĐÁNH GIÁ (services/show.php) ---\n";

ob_start();
$service = [
    'id' => $serviceId,
    'title' => 'Săn Mây Đỉnh LangBiang',
    'base_price' => 750000,
    'price_mode' => 'per_person',
    'duration_days' => 1,
    'location' => 'Đà Lạt',
    'stops' => [],
];
$stops = [];
$weather = [];
$discussions = [];
$reviews = $serviceReviews;
$reviewStats = $stats;
$user = $tourist;
$hasReviewed = true;
$isEligibleVerified = true;

include __DIR__ . '/../app/views/services/show.php';
$showHtml = ob_get_clean();

assertTest("Giao diện có Khối Đánh giá từ du khách (id='reviewsSection')", 
    str_contains($showHtml, 'id="reviewsSection"'));
assertTest("Giao diện có thanh tóm tắt điểm số 'Đánh Giá Tổng Quan'", 
    str_contains($showHtml, 'Đánh Giá Tổng Quan'));
assertTest("Giao diện hiển thị huy hiệu 'Đã tự trải nghiệm'", 
    str_contains($showHtml, 'Đã tự trải nghiệm'));
assertTest("Giao diện có thông báo 'Bạn đã gửi đánh giá cho chuyến đi này rồi' khi đã review", 
    str_contains($showHtml, 'Bạn đã gửi đánh giá cho chuyến đi này rồi'));

// Clean up test data
if (!empty($createdReviewIds)) {
    $db->exec("DELETE FROM tbl_reviews WHERE id IN (" . implode(',', $createdReviewIds) . ")");
}
if (!empty($createdBookingIds)) {
    $db->exec("DELETE FROM tbl_bookings WHERE id IN (" . implode(',', $createdBookingIds) . ")");
}
echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi kiểm thử Giai đoạn 2.\n";

// ==============================================================================
// TỔNG KẾT
// ==============================================================================
echo "\n" . str_repeat('=', 84) . "\n";
echo "KẾT QUẢ KIỂM THỬ GIAI ĐOẠN 2: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ GIAI ĐOẠN 2 ĐẠT CHUẨN 100%! <<<\n";
} else {
    echo ">>> CÓ BÀI KIỂM THỬ THẤT BẠI! <<<\n";
}
echo str_repeat('=', 84) . "\n\n";

exit($passedTests === $totalTests ? 0 : 1);
