<?php

declare(strict_types=1);

/**
 * Test Suite: Sprint 2 - Task TV4-05
 * Nội dung: Hỏi đáp 1 cấp giữa Du khách & Host qua AJAX (pct_sp_discussions_create)
 * 
 * Kiểm thử:
 * 1. Discussion Model: createDiscussion root & 1-level reply.
 * 2. Ngăn chặn trả lời chéo tour (MISMATCH_DISCUSSION_PARENT).
 * 3. Chống trả lời lồng nhau nhiều cấp (chỉ cho phép 1 cấp).
 * 4. Chống trả lời chủ đề đã bị ẩn (Mục 3.8: Server-side guard).
 * 5. getDiscussionsByService gom nhóm chính xác câu hỏi và câu trả lời.
 * 6. Phân quyền: Khách vãng lai bị chặn (HTTP 401), Tài khoản bị khóa bị chặn (HTTP 403).
 * 7. Controller endpoints: DiscussionController::index & DiscussionController::store (AJAX JSON).
 * 8. ServiceController::show nạp discussions ra view.
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
use App\Models\Discussion;
use App\Controllers\DiscussionController;
use App\Controllers\ServiceController;

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
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV4-05: HỎI ĐÁP 1 CẤP AJAX            \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$discussionModel = new Discussion();

// 1. Fetch test users
$hostUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'host' LIMIT 1");
$touristUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'tourist' LIMIT 1");

if (!$hostUser || !$touristUser) {
    die("Không tìm thấy user host hoặc tourist!\n");
}

$hostId = (int)$hostUser['id'];
$touristId = (int)$touristUser['id'];

// Get or create test service 1
$service1 = $db->fetchOne("SELECT id FROM tbl_services WHERE host_id = ? LIMIT 1", [$hostId]);
if (!$service1) {
    $catId = (int)$db->fetchOne("SELECT id FROM tbl_categories LIMIT 1")['id'];
    $service1 = (new Service())->create(
        $hostId, $catId, 'Tour TV4-05 Test 1', 'tour-tv4-05-test-1', 'Test',
        'prepaid_full', 500000, 0, 0, 'Đà Lạt', 11.9, 108.4, 1, '/assets/test.jpg'
    );
}
$service1Id = (int)$service1['id'];

// Get or create test service 2 (for cross-service check)
$catId = (int)$db->fetchOne("SELECT id FROM tbl_categories LIMIT 1")['id'];
$service2 = (new Service())->create(
    $hostId, $catId, 'Tour TV4-05 Test 2 ' . time(), 'tour-tv4-05-test-2-' . time(), 'Test',
    'prepaid_full', 400000, 0, 0, 'Sa Pa', 22.3, 103.8, 1, '/assets/test.jpg'
);
$service2Id = (int)$service2['id'];

// Track created discussion IDs for clean up
$createdDiscussionIds = [];

// ==============================================================================
// TEST GROUP 1: DISCUSSION CREATION & STORED PROCEDURE (pct_sp_discussions_create)
// ==============================================================================
echo "--- NHÓM 1: TẠO CHỦ ĐỀ & TRẢ LỜI 1 CẤP (pct_sp_discussions_create) ---\n";

// 1.1 Tourist creates root discussion topic
$rootMsg = "Tour này xuất phát lúc mấy giờ vậy Host?";
$rootDiscussion = $discussionModel->createDiscussion($touristId, $service1Id, $rootMsg, null);
$rootId = (int)$rootDiscussion['id'];
$createdDiscussionIds[] = $rootId;

assertTest("Tạo chủ đề câu hỏi gốc thành công via pct_sp_discussions_create", 
    $rootId > 0 && (int)$rootDiscussion['service_id'] === $service1Id);
assertTest("Chủ đề gốc có parent_id là NULL và is_visible là 1", 
    empty($rootDiscussion['parent_id']) && (int)$rootDiscussion['is_visible'] === 1);
assertTest("Kết quả trả về kèm tên và avatar của người hỏi", 
    !empty($rootDiscussion['user_name']));

// 1.2 Host creates 1-level reply
$replyMsg = "Chào bạn! Tour xuất phát lúc 5h30 sáng tại quảng trường nhé.";
$replyDiscussion = $discussionModel->createDiscussion($hostId, $service1Id, $replyMsg, $rootId);
$replyId = (int)$replyDiscussion['id'];
$createdDiscussionIds[] = $replyId;

assertTest("Host trả lời câu hỏi gốc thành công", 
    $replyId > 0 && (int)$replyDiscussion['parent_id'] === $rootId);
assertTest("Nội dung phản hồi được lưu trữ chính xác", 
    $replyDiscussion['message'] === $replyMsg);

// ==============================================================================
// TEST GROUP 2: RESTRAINTS & BUSINESS RULES (Mục 3.8)
// ==============================================================================
echo "\n--- NHÓM 2: RÀNG BUỘC NGHIỆP VỤ & BẢO VỆ CHẶT CHẼ (Mục 3.8) ---\n";

// 2.1 Prevent cross-service reply (parent is on service 1, reply sent on service 2)
$crossServiceBlocked = false;
try {
    $discussionModel->createDiscussion($hostId, $service2Id, "Trả lời chéo tour", $rootId);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'MISMATCH_DISCUSSION_PARENT')) {
        $crossServiceBlocked = true;
    }
}
assertTest("Chặn trả lời chéo tour: Ném ngoại lệ MISMATCH_DISCUSSION_PARENT", $crossServiceBlocked);

// 2.2 Prevent multi-level nested replies (replying to a reply)
$nestedBlocked = false;
try {
    $discussionModel->createDiscussion($touristId, $service1Id, "Cảm ơn Host nhiều nhé!", $replyId);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'chỉ hỗ trợ thảo luận 1 cấp')) {
        $nestedBlocked = true;
    }
}
assertTest("Chặn trả lời lồng nhau nhiều cấp (chỉ cho phép 1 cấp theo quy chuẩn)", $nestedBlocked);

// 2.3 Prevent replies to hidden discussions (Section 3.8: server-side guard)
$hiddenTopic = $discussionModel->createDiscussion($touristId, $service1Id, "Câu hỏi bị ẩn", null);
$hiddenTopicId = (int)$hiddenTopic['id'];
$createdDiscussionIds[] = $hiddenTopicId;

// Hide the topic
$discussionModel->hideDiscussion($hiddenTopicId, $touristId);

$replyToHiddenBlocked = false;
try {
    $discussionModel->createDiscussion($hostId, $service1Id, "Trả lời chủ đề đã ẩn", $hiddenTopicId);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'Chủ đề đã bị ẩn')) {
        $replyToHiddenBlocked = true;
    }
}
assertTest("Chủ đề đã bị ẩn không nhận thêm trả lời (Mục 3.8 - kiểm tra tại server)", $replyToHiddenBlocked);

// ==============================================================================
// TEST GROUP 3: THREAD QUERY & HIERARCHICAL GROUPING
// ==============================================================================
echo "\n--- NHÓM 3: TRUY VẤN VÀ GOM NHÓM PHẢN HỒI (getDiscussionsByService) ---\n";

$service1Discussions = $discussionModel->getDiscussionsByService($service1Id);
assertTest("getDiscussionsByService trả về danh sách câu hỏi dạng mảng", is_array($service1Discussions));

// Find our $rootId in the list
$foundRoot = null;
foreach ($service1Discussions as $item) {
    if ((int)$item['id'] === $rootId) {
        $foundRoot = $item;
        break;
    }
}
assertTest("Tìm thấy câu hỏi gốc trong danh sách thảo luận của tour", $foundRoot !== null);
assertTest("Mỗi câu hỏi gốc chứa mảng 'replies' lồng các câu trả lời 1 cấp", 
    isset($foundRoot['replies']) && is_array($foundRoot['replies']));
assertTest("Câu trả lời của Host nằm bên trong mảng replies của câu hỏi gốc", 
    count($foundRoot['replies']) >= 1 && (int)$foundRoot['replies'][0]['id'] === $replyId);

// ==============================================================================
// TEST GROUP 4: CONTROLLER & AJAX API ENDPOINTS
// ==============================================================================
echo "\n--- NHÓM 4: CONTROLLER & GIAO TIẾP AJAX (DiscussionController) ---\n";

$discussionController = new DiscussionController(new Request());

// 4.1 Unauthenticated guest blocked from posting (HTTP 401)
$_SESSION['user'] = null;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST = [
    'service_id' => $service1Id,
    'message' => 'Khách vãng lai gửi câu hỏi',
];

ob_start();
$discussionController->store(new Request());
$guestPostOutput = ob_get_clean();
$guestPostRes = json_decode($guestPostOutput, true);
assertTest("Khách vãng lai gửi câu hỏi bị từ chối với HTTP 401", 
    isset($guestPostRes['error']) && str_contains($guestPostRes['error'], 'đăng nhập'));

// 4.2 Locked user blocked from posting (HTTP 403)
$_SESSION['user'] = [
    'id' => 77777,
    'name' => 'Locked User',
    'email' => 'locked@example.com',
    'role' => 'tourist',
    'status' => 'locked',
];

ob_start();
$discussionController->store(new Request());
$lockedPostOutput = ob_get_clean();
$lockedPostRes = json_decode($lockedPostOutput, true);
assertTest("Tài khoản bị khóa bị từ chối gửi câu hỏi với HTTP 403", 
    isset($lockedPostRes['error']) && str_contains($lockedPostRes['error'], 'khóa'));

// 4.3 Logged in tourist submits new question via AJAX (POST /discussions)
$_SESSION['user'] = $touristUser;
$_POST = [
    'service_id' => $service1Id,
    'message' => 'Tour này có kèm bữa trưa không ạ?',
];

ob_start();
$discussionController->store(new Request());
$ajaxQuestionOutput = ob_get_clean();
$ajaxQuestionRes = json_decode($ajaxQuestionOutput, true);

assertTest("Gửi câu hỏi mới qua AJAX thành công: Trả về JSON {success: true}", 
    isset($ajaxQuestionRes['success']) && $ajaxQuestionRes['success'] === true);
assertTest("Dữ liệu trả về chứa thông tin discussion vừa tạo", 
    isset($ajaxQuestionRes['discussion']['id']) && !empty($ajaxQuestionRes['discussion']['user_name']));
$createdDiscussionIds[] = (int)$ajaxQuestionRes['discussion']['id'];

// 4.4 Host replies to question via AJAX
$_SESSION['user'] = $hostUser;
$_POST = [
    'service_id' => $service1Id,
    'parent_id' => $ajaxQuestionRes['discussion']['id'],
    'message' => 'Có bạn nhé, tour bao gồm ăn trưa đặc sản bản địa.',
];

ob_start();
$discussionController->store(new Request());
$ajaxReplyOutput = ob_get_clean();
$ajaxReplyRes = json_decode($ajaxReplyOutput, true);

assertTest("Gửi câu trả lời qua AJAX thành công: Trả về is_reply=true", 
    isset($ajaxReplyRes['success']) && $ajaxReplyRes['is_reply'] === true);
$createdDiscussionIds[] = (int)$ajaxReplyRes['discussion']['id'];

// 4.5 API list endpoint (GET /services/{id}/discussions)
ob_start();
$discussionController->index(new Request(), (string)$service1Id);
$indexOutput = ob_get_clean();
$indexRes = json_decode($indexOutput, true);

assertTest("Endpoint GET /services/{id}/discussions trả về JSON danh sách thảo luận", 
    isset($indexRes['success']) && is_array($indexRes['discussions']));

// ==============================================================================
// TEST GROUP 5: INTEGRATION WITH SERVICE DETAIL VIEW
// ==============================================================================
echo "\n--- NHÓM 5: TÍCH HỢP GIAO DIỆN CHI TIẾT TOUR (ServiceController::show) ---\n";

$serviceController = new ServiceController();
ob_start();
$serviceController->show(new Request(), (string)$service1Id);
$renderedServiceShow = ob_get_clean();

assertTest("ServiceController::show render giao diện trang chi tiết thành công", 
    strlen($renderedServiceShow) > 1000);
assertTest("Giao diện chứa khối 'Kênh Hỏi Đánh Giá Trực Tiếp Từ Xa'", 
    str_contains($renderedServiceShow, 'Kênh Hỏi Đánh Giá Trực Tiếp Từ Xa'));
assertTest("Giao diện chứa form gửi câu hỏi id='discussion-main-form'", 
    str_contains($renderedServiceShow, 'id="discussion-main-form"'));
assertTest("Giao diện chứa vùng hiển thị thảo luận id='discussions-thread-container'", 
    str_contains($renderedServiceShow, 'id="discussions-thread-container"'));

// Clean up test records
if (!empty($createdDiscussionIds)) {
    $placeholders = implode(',', array_fill(0, count($createdDiscussionIds), '?'));
    $db->execute("DELETE FROM tbl_discussions WHERE id IN ({$placeholders})", $createdDiscussionIds);
}
$db->execute("DELETE FROM tbl_services WHERE id = ?", [$service2Id]);

echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi kiểm thử TV4-05.\n";

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV4-05 ĐẠT CHUẨN 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
