<?php

declare(strict_types=1);

/**
 * Sprint 2: TV1-06 Test Suite
 * Automated tests for Admin User Management and Lock/Unlock Account via dhs_sp_users_set_status.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/bootstrap/app.php';

use Core\Request;
use Core\Response;
use Core\Database;
use App\Models\User;
use App\Controllers\AdminController;

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

echo "========================================================================\n";
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV1-06: ADMIN USER MANAGEMENT & LOCK    \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$userModel = new User();

// Ensure all admins are active before tests
$db->execute("UPDATE tbl_users SET status = 'active' WHERE role = 'admin'");

// Setup mock admin and target test user in database
$adminUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'admin' LIMIT 1");
if (!$adminUser) {
    die("Không tìm thấy user admin trong CSDL để chạy kiểm thử!\n");
}
$adminId = (int)$adminUser['id'];

// Create a dedicated test tourist user for lock/unlock testing if not present
$testEmail = 'tv1_06_test_target@example.com';
$db->execute("DELETE FROM tbl_users WHERE email = ?", [$testEmail]);
$created = $userModel->register($testEmail, 'Target Test User', password_hash('password123', PASSWORD_BCRYPT), '0901234567', 'tourist');
$testUserId = (int)$created['id'];

echo "--- NHÓM 1: MÔ HÌNH DỮ LIỆU USER & THỐNG KÊ (User.php) ---\n";

// 1.1 getAllUsers returns list
$users = $userModel->getAllUsers(null, null, null, 10, 0);
assertTest("getAllUsers() lấy danh sách người dùng thành công", is_array($users) && count($users) > 0);
assertTest("Mỗi bản ghi người dùng chứa đầy đủ các trường id, role, name, email, status", 
    isset($users[0]['id'], $users[0]['role'], $users[0]['name'], $users[0]['email'], $users[0]['status']));

// 1.2 Filter by role
$hostsOnly = $userModel->getAllUsers('host', null, null, 10, 0);
$allHosts = array_reduce($hostsOnly, fn($c, $u) => $c && $u['role'] === 'host', true);
assertTest("Lọc theo role='host' chỉ trả về người dùng vai trò host", count($hostsOnly) > 0 && $allHosts);

$touristsOnly = $userModel->getAllUsers('tourist', null, null, 10, 0);
$allTourists = array_reduce($touristsOnly, fn($c, $u) => $c && $u['role'] === 'tourist', true);
assertTest("Lọc theo role='tourist' chỉ trả về người dùng vai trò tourist", count($touristsOnly) > 0 && $allTourists);

// 1.3 Filter by search
$searchResults = $userModel->getAllUsers(null, null, 'Target Test', 10, 0);
assertTest("Tìm kiếm từ khóa 'Target Test' tìm thấy đúng người dùng mục tiêu", 
    count($searchResults) === 1 && (int)$searchResults[0]['id'] === $testUserId);

// 1.4 getUserStats aggregate
$stats = $userModel->getUserStats();
assertTest("getUserStats() trả về đầy đủ tổng số, host, tourist, admin, locked, active", 
    isset($stats['total'], $stats['hosts'], $stats['tourists'], $stats['admins'], $stats['locked'], $stats['active']) 
    && $stats['total'] >= 3);

echo "\n--- NHÓM 2: ADMIN CONTROLLER - DANH SÁCH & GIAO DIỆN (AdminController::users) ---\n";

// Set Admin session
$_SESSION['user'] = $adminUser;

// 2.1 View admin/users/index rendering
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/admin/users',
    'SCRIPT_NAME' => '/index.php',
];
$adminController = new AdminController(new Request());
ob_start();
$adminController->users(new Request());
$renderedView = ob_get_clean();

assertTest("AdminController::users render giao diện thành công không lỗi", strlen($renderedView) > 500);
assertTest("Giao diện chứa tiêu đề 'Quản Lý Người Dùng & Phân Quyền'", 
    str_contains($renderedView, 'Quản Lý Người Dùng & Phân Quyền'));
assertTest("Giao diện chứa bảng danh sách tài khoản, 4 thẻ thống kê và bộ lọc", 
    str_contains($renderedView, 'TỔNG NGƯỜI DÙNG') 
    && str_contains($renderedView, 'DÂN BẢN ĐỊA') 
    && str_contains($renderedView, 'dhs_sp_users_set_status'));

echo "\n--- NHÓM 3: KHÓA VÀ MỞ KHÓA TÀI KHOẢN (dhs_sp_users_set_status) ---\n";

// 3.1 Lock test user via POST /admin/users/{id}/status
$_POST = [
    '_csrf_token' => SecurityHelper::csrfToken(),
    'status' => 'locked',
];
$_SERVER['REQUEST_METHOD'] = 'POST';
$postReq = new Request();
$postReq->setRouteParams(['id' => (string)$testUserId]);

ob_start();
$adminController->setUserStatus($postReq, (string)$testUserId);
ob_get_clean();

// Verify in DB
$checkLocked = $userModel->getById($testUserId);
assertTest("Admin khóa tài khoản: Trạng thái trong DB chuyển thành 'locked'", 
    $checkLocked !== null && $checkLocked['status'] === 'locked');
assertTest("Flash thông báo thành công được lưu vào session", 
    !empty($_SESSION['flash']['success']));

// 3.2 Unlock test user back to active
$_POST['status'] = 'active';
$postReqUnlock = new Request();
$postReqUnlock->setRouteParams(['id' => (string)$testUserId]);

ob_start();
$adminController->setUserStatus($postReqUnlock, (string)$testUserId);
ob_get_clean();

$checkActive = $userModel->getById($testUserId);
assertTest("Admin mở khóa tài khoản: Trạng thái trong DB chuyển thành 'active'", 
    $checkActive !== null && $checkActive['status'] === 'active');

// 3.3 AJAX lock request
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
$_POST['status'] = 'locked';
$ajaxReq = new Request();
$ajaxReq->setRouteParams(['id' => (string)$testUserId]);

ob_start();
$adminController->setUserStatus($ajaxReq, (string)$testUserId);
$ajaxOutput = ob_get_clean();
$decodedAjax = json_decode($ajaxOutput, true);

assertTest("AJAX cập nhật trạng thái: Trả về HTTP 200 và JSON {success: true, status: 'locked'}", 
    isset($decodedAjax['success'], $decodedAjax['status']) && $decodedAjax['status'] === 'locked');

echo "\n--- NHÓM 4: BẢO VỆ AN TOÀN & XỬ LÝ LỖI ---\n";

// 4.1 Prevent Admin from locking their own account
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
$_POST['status'] = 'locked';
$selfLockReq = new Request();
$selfLockReq->setRouteParams(['id' => (string)$adminId]);

ob_start();
$adminController->setUserStatus($selfLockReq, (string)$adminId);
ob_get_clean();

$checkAdmin = $userModel->getById($adminId);
assertTest("Chặn Admin tự khóa tài khoản của chính mình: Trạng thái admin vẫn là active", 
    $checkAdmin['status'] === 'active');
assertTest("Flash cảnh báo 'Bạn không thể tự khóa tài khoản quản trị của chính mình!' xuất hiện", 
    !empty($_SESSION['flash']['danger']) && str_contains($_SESSION['flash']['danger'][0], 'tự khóa'));

// 4.2 Invalid status rejected
$_SESSION['flash'] = [];
$_POST['status'] = 'hacked_status';
$invalidReq = new Request();
$invalidReq->setRouteParams(['id' => (string)$testUserId]);
ob_start();
$adminController->setUserStatus($invalidReq, (string)$testUserId);
ob_get_clean();
assertTest("Trạng thái không hợp lệ bị từ chối", 
    !empty($_SESSION['flash']['danger']) && in_array('Trạng thái không hợp lệ.', $_SESSION['flash']['danger']));

// 4.3 Non-existent user ID
unset($_SERVER['HTTP_X_REQUESTED_WITH'], $_SERVER['HTTP_ACCEPT']);
$_SESSION['flash'] = [];
$_POST['status'] = 'locked';
$fakeReq = new Request();
$fakeReq->setRouteParams(['id' => '99999999']);
ob_start();
$adminController->setUserStatus($fakeReq, '99999999');
ob_get_clean();
assertTest("Cập nhật user không tồn tại bị từ chối", 
    !empty($_SESSION['flash']['danger']) && in_array('Người dùng không tồn tại.', $_SESSION['flash']['danger']));

// Clean up test user
$db->execute("DELETE FROM tbl_users WHERE id = ?", [$testUserId]);
echo "  [INFO] Đã dọn dẹp tài khoản test #{$testUserId} khỏi CSDL.\n";

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV1-06 ĐẠT CHUẨN 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
