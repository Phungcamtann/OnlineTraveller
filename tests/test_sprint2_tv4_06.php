<?php

declare(strict_types=1);

/**
 * Test Suite: Sprint 2 - Task TV4-06
 * Nội dung: Đánh giá sao sau chuyến đi & Quản trị Blog cẩm nang du lịch
 * 
 * Kiểm thử:
 * 1. Review Model & pct_sp_reviews_create: Đánh giá 1..5 sao cho đơn completed.
 * 2. Chặn đánh giá khi đơn chưa hoàn thành hoặc không phải chủ đơn.
 * 3. Chặn đánh giá số sao không hợp lệ (< 1 hoặc > 5).
 * 4. Chặn đánh giá trùng lặp cho cùng một đơn (unique booking_id).
 * 5. ReviewController::store qua AJAX và tính toán thống kê rating trung bình.
 * 6. Post Model & pct_sp_posts_save / pct_sp_posts_delete: Thêm, sửa, xóa bài viết.
 * 7. Chặn người dùng không phải Admin tạo hoặc xóa bài viết (FORBIDDEN).
 * 8. PostController: Giao diện công khai (GET /posts, /posts/{slug}) & Giao diện quản trị (GET /admin/posts).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/bootstrap/app.php';

use Core\Database;
use Core\Request;
use Core\Response;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Post;
use App\Services\BookingService;
use App\Controllers\ReviewController;
use App\Controllers\PostController;

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

Response::$preventExit = true;

echo "========================================================================\n";
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV4-06: ĐÁNH GIÁ SAO & QUẢN TRỊ BLOG   \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$bookingModel = new Booking();
$reviewModel = new Review();
$postModel = new Post();
$scheduleModel = new ServiceSchedule();
$bookingService = new BookingService($bookingModel);

// 1. Fetch test users
$adminUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'admin' LIMIT 1");
$hostUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'host' LIMIT 1");
$touristUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'tourist' LIMIT 1");

if (!$adminUser || !$hostUser || !$touristUser) {
    die("Không tìm thấy đủ người dùng admin, host, tourist!\n");
}

$adminId = (int)$adminUser['id'];
$hostId = (int)$hostUser['id'];
$touristId = (int)$touristUser['id'];

// Get or create test service
$service = $db->fetchOne("SELECT id FROM tbl_services WHERE host_id = ? LIMIT 1", [$hostId]);
if (!$service) {
    $catId = (int)$db->fetchOne("SELECT id FROM tbl_categories LIMIT 1")['id'];
    $service = (new Service())->create(
        $hostId, $catId, 'Tour TV4-06 Test', 'tour-tv4-06-test', 'Test',
        'prepaid_full', 500000, 0, 0, 'Đà Lạt', 11.9, 108.4, 1, '/assets/test.jpg'
    );
}
$serviceId = (int)$service['id'];

// Create schedule for testing
$schedule = $scheduleModel->create(
    $hostId,
    $serviceId,
    date('Y-m-d H:i:s', strtotime('+2 days')),
    date('Y-m-d H:i:s', strtotime('+2 days 4 hours')),
    date('Y-m-d H:i:s', strtotime('+1 day')),
    10,
    'Hồ Tuyền Lâm'
);
$scheduleId = (int)$schedule['id'];

// Track created IDs for clean up
$createdBookingIds = [];
$createdPostIds = [];
$createdReviewIds = [];

// ==============================================================================
// TEST GROUP 1: STAR REVIEWS & STORED PROCEDURE (pct_sp_reviews_create)
// ==============================================================================
echo "--- NHÓM 1: ĐÁNH GIÁ SAO SAU CHUYẾN ĐI (pct_sp_reviews_create) ---\n";

// 1.1 Create completed booking
$bkCompleted = $bookingService->createBooking($touristId, $scheduleId, 1, 'deposit_50');
$bkCompletedId = (int)$bkCompleted['id'];
$createdBookingIds[] = $bkCompletedId;

// Transition booking to completed
$db->execute("UPDATE tbl_bookings SET status = 'completed' WHERE id = ?", [$bkCompletedId]);

// Tourist submits 5-star review
$reviewRes = $reviewModel->createReview(
    $touristId,
    $bkCompletedId,
    5,
    'Trải nghiệm săn mây và uống cà phê sáng của Host rất tuyệt vời! Dịch vụ 10/10.'
);
$createdReviewIds[] = (int)$reviewRes['id'];

assertTest("Tạo đánh giá sao thành công qua pct_sp_reviews_create", 
    (int)$reviewRes['id'] > 0 && (int)$reviewRes['rating'] === 5);
assertTest("Bản ghi đánh giá liên kết đúng booking_id, tourist_id, service_id", 
    (int)$reviewRes['booking_id'] === $bkCompletedId && (int)$reviewRes['service_id'] === $serviceId);
assertTest("Kết quả trả về kèm tên và avatar của du khách đánh giá", 
    !empty($reviewRes['tourist_name']));

// 1.2 Verify duplicate review for same booking is rejected
$duplicateBlocked = false;
try {
    $reviewModel->createReview($touristId, $bkCompletedId, 4, 'Đánh giá lần 2');
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'đã gửi đánh giá')) {
        $duplicateBlocked = true;
    }
}
assertTest("Chống gian lận: Chặn đánh giá lần thứ 2 cho cùng một chuyến đi", $duplicateBlocked);

// 1.3 Verify rating out of bounds (< 1 or > 5) is rejected
$bkCompleted2 = $bookingService->createBooking($touristId, $scheduleId, 1, 'deposit_50');
$bkCompleted2Id = (int)$bkCompleted2['id'];
$createdBookingIds[] = $bkCompleted2Id;
$db->execute("UPDATE tbl_bookings SET status = 'completed' WHERE id = ?", [$bkCompleted2Id]);

$outOfBoundsBlocked = false;
try {
    $reviewModel->createReview($touristId, $bkCompleted2Id, 6, 'Cho 6 sao');
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'từ 1 đến 5 sao')) {
        $outOfBoundsBlocked = true;
    }
}
assertTest("Chặn số sao không hợp lệ (lớn hơn 5 sao)", $outOfBoundsBlocked);

// 1.4 Non-owner tourist cannot review another tourist's booking
$strangerReviewBlocked = false;
try {
    $reviewModel->createReview(999999, $bkCompleted2Id, 5, 'Người lạ review');
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'FORBIDDEN_NOT_BOOKING_OWNER')) {
        $strangerReviewBlocked = true;
    }
}
assertTest("Chặn người dùng lạ gửi đánh giá thay cho chủ đơn (FORBIDDEN_NOT_BOOKING_OWNER)", $strangerReviewBlocked);

// 1.5 Cannot review a booking that is not completed (e.g. pending or awaiting_payment)
$bkPending = $bookingService->createBooking($touristId, $scheduleId, 1, 'deposit_50');
$bkPendingId = (int)$bkPending['id'];
$createdBookingIds[] = $bkPendingId;

$uncompletedBlocked = false;
try {
    $reviewModel->createReview($touristId, $bkPendingId, 5, 'Review khi chưa đi');
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'chuyến đi đã hoàn thành')) {
        $uncompletedBlocked = true;
    }
}
assertTest("Chặn đánh giá chuyến đi khi trạng thái chưa phải là 'completed'", $uncompletedBlocked);

// ==============================================================================
// TEST GROUP 2: REVIEW CONTROLLER & RATING STATISTICS
// ==============================================================================
echo "\n--- NHÓM 2: CONTROLLER & THỐNG KÊ ĐÁNH GIÁ (ReviewController) ---\n";

$reviewController = new ReviewController(new Request());

// 2.1 Tourist submits review via AJAX (POST /reviews)
$_SESSION['user'] = $touristUser;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST = [
    'booking_id' => $bkCompleted2Id,
    'rating' => 4,
    'comment' => 'Dịch vụ rất tốt, phong cảnh đẹp, đồ ăn ngon.',
];

ob_start();
$reviewController->store(new Request());
$ajaxReviewOutput = ob_get_clean();
$ajaxReviewRes = json_decode($ajaxReviewOutput, true);

assertTest("ReviewController::store qua AJAX trả về JSON {success: true}", 
    isset($ajaxReviewRes['success']) && $ajaxReviewRes['success'] === true);
$createdReviewIds[] = (int)$ajaxReviewRes['review']['id'];

// 2.2 Rating statistics for the service
$stats = $reviewModel->getServiceRatingStats($serviceId);
assertTest("getServiceRatingStats tính toán số lượt đánh giá chính xác", 
    (int)$stats['reviews_count'] >= 2);
assertTest("Điểm đánh giá trung bình nằm trong khoảng 4.0 đến 5.0", 
    (float)$stats['avg_rating'] >= 4.0 && (float)$stats['avg_rating'] <= 5.0);

// 2.3 Public API endpoint GET /services/{id}/reviews
ob_start();
$reviewController->index(new Request(), (string)$serviceId);
$apiReviewOutput = ob_get_clean();
$apiReviewRes = json_decode($apiReviewOutput, true);

assertTest("Endpoint GET /services/{id}/reviews trả về danh sách đánh giá công khai", 
    isset($apiReviewRes['success']) && is_array($apiReviewRes['reviews']));

// ==============================================================================
// TEST GROUP 3: BLOG POST MODEL & PROCEDURES (pct_sp_posts_save / delete)
// ==============================================================================
echo "\n--- NHÓM 3: MÔ HÌNH BÀI VIẾT CẨM NANG (pct_sp_posts_save & delete) ---\n";

// 3.1 Admin saves new post
$postTitle = 'Kinh Nghiệm Săn Mây Đà Lạt Từ 4h Sáng ' . time();
$postSlug = 'kinh-nghiem-san-may-' . time();
$postContent = 'Đà Lạt mùa săn mây từ tháng 10 đến tháng 4 hàng năm là thời điểm lý tưởng nhất...';

$savedPost = $postModel->savePost(
    $adminId,
    null,
    $postTitle,
    $postSlug,
    $postContent,
    '/assets/images/blog/san-may.jpg',
    1
);
$postId = (int)$savedPost['id'];
$createdPostIds[] = $postId;

assertTest("Admin tạo mới bài viết cẩm nang thành công via pct_sp_posts_save", 
    $postId > 0 && $savedPost['title'] === $postTitle);
assertTest("Đường dẫn tĩnh slug được lưu chính xác", $savedPost['slug'] === $postSlug);
assertTest("Bài viết được gán author_id là adminId", (int)$savedPost['author_id'] === $adminId);

// 3.2 Admin updates existing post
$updatedTitle = $postTitle . ' (Cập Nhật 2026)';
$updatedPost = $postModel->savePost(
    $adminId,
    $postId,
    $updatedTitle,
    $postSlug,
    $postContent . "\nLưu ý nhớ mang áo ấm dày.",
    '/assets/images/blog/san-may-v2.jpg',
    1
);
assertTest("Admin cập nhật bài viết thành công (giữ nguyên ID #{$postId})", 
    (int)$updatedPost['id'] === $postId && $updatedPost['title'] === $updatedTitle);

// 3.3 Non-admin blocked from saving post
$nonAdminBlocked = false;
try {
    $postModel->savePost($touristId, null, 'Tourist viết blog', 'tourist-blog', 'Nội dung', null, 1);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'FORBIDDEN')) {
        $nonAdminBlocked = true;
    }
}
assertTest("Chặn Tourist/Host tự ý đăng bài viết cẩm nang (FORBIDDEN)", $nonAdminBlocked);

// 3.4 Increment views count
$viewsBefore = (int)$postModel->getById($postId)['views_count'];
$postModel->incrementViews($postId);
$viewsAfter = (int)$postModel->getById($postId)['views_count'];
assertTest("Lượt xem bài viết tự động tăng lên sau khi đọc", $viewsAfter === $viewsBefore + 1);

// 3.5 Admin deletes post via pct_sp_posts_delete
$deleted = $postModel->deletePost($adminId, $postId);
assertTest("Admin xóa bài viết thành công via pct_sp_posts_delete", $deleted === true);
assertTest("Bài viết đã xóa không còn tồn tại trong CSDL", $postModel->getById($postId) === null);

// ==============================================================================
// TEST GROUP 4: POST CONTROLLER (PUBLIC & ADMIN ENDPOINTS)
// ==============================================================================
echo "\n--- NHÓM 4: CONTROLLER GIAO DIỆN BLOG (PostController) ---\n";

// Create a permanent test post for controller tests
$ctrlPost = $postModel->savePost(
    $adminId,
    null,
    'Top 5 Điểm Đến Mùa Thu Tây Bắc ' . time(),
    'top-5-tay-bac-' . time(),
    'Hà Giang, Sa Pa, Mù Cang Chải, Y Tý, Tà Xùa...',
    '/assets/taybac.jpg',
    1
);
$ctrlPostId = (int)$ctrlPost['id'];
$createdPostIds[] = $ctrlPostId;

$postController = new PostController(new Request());

// 4.1 Public blog index (GET /posts)
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
$_GET = [];
$_POST = [];

ob_start();
$postController->index(new Request());
$renderedBlogIndex = ob_get_clean();

assertTest("PostController::index render giao diện cẩm nang công khai thành công", 
    strlen($renderedBlogIndex) > 500 && str_contains($renderedBlogIndex, 'Góc Nhìn & Kinh Nghiệm Đi Tour'));

// 4.2 Public article detail (GET /posts/{slug})
ob_start();
$postController->show(new Request(), $ctrlPost['slug']);
$renderedPostDetail = ob_get_clean();

assertTest("PostController::show render chi tiết bài viết thành công", 
    str_contains($renderedPostDetail, $ctrlPost['title']));

// 4.3 Admin articles management (GET /admin/posts)
$_SESSION['user'] = $adminUser;
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_SERVER['HTTP_X_REQUESTED_WITH']);

ob_start();
$postController->adminIndex(new Request());
$renderedAdminIndex = ob_get_clean();

assertTest("PostController::adminIndex render trang quản trị bài viết thành công", 
    str_contains($renderedAdminIndex, 'Quản Trị Bài Viết & Cẩm Nang Du Lịch'));
assertTest("Giao diện quản trị hiển thị đúng tiêu đề bài viết vừa tạo", 
    str_contains($renderedAdminIndex, $ctrlPost['title']));

// 4.4 Non-admin blocked from admin action (HTTP 403)
$_SESSION['user'] = $touristUser;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
ob_start();
$postController->destroy(new Request(), (string)$ctrlPostId);
$nonAdminDestroyOutput = ob_get_clean();
$nonAdminDestroyRes = json_decode($nonAdminDestroyOutput, true);

assertTest("Du khách bị chặn khi cố tình thực hiện xóa bài viết (HTTP 403)", 
    isset($nonAdminDestroyRes['error']) && str_contains($nonAdminDestroyRes['error'], 'quyền'));

// Clean up all test records
if (!empty($createdReviewIds)) {
    $placeholders = implode(',', array_fill(0, count($createdReviewIds), '?'));
    $db->execute("DELETE FROM tbl_reviews WHERE id IN ({$placeholders})", $createdReviewIds);
}
if (!empty($createdBookingIds)) {
    $placeholders = implode(',', array_fill(0, count($createdBookingIds), '?'));
    $db->execute("DELETE FROM tbl_bookings WHERE id IN ({$placeholders})", $createdBookingIds);
}
if (!empty($createdPostIds)) {
    $placeholders = implode(',', array_fill(0, count($createdPostIds), '?'));
    $db->execute("DELETE FROM tbl_posts WHERE id IN ({$placeholders})", $createdPostIds);
}
if (!empty($scheduleId)) {
    $db->execute("DELETE FROM tbl_reviews WHERE booking_id IN (SELECT id FROM tbl_bookings WHERE schedule_id = ?)", [$scheduleId]);
    $db->execute("DELETE FROM tbl_payments WHERE booking_id IN (SELECT id FROM tbl_bookings WHERE schedule_id = ?)", [$scheduleId]);
    $db->execute("DELETE FROM tbl_bookings WHERE schedule_id = ?", [$scheduleId]);
    $db->execute("DELETE FROM tbl_service_schedules WHERE id = ?", [$scheduleId]);
}

echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi kiểm thử TV4-06.\n";

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV4-06 ĐẠT CHUẨN 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
