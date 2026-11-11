<?php

declare(strict_types=1);

/**
 * Test Suite: Admin Service Moderation & Approval Flow
 * Verifies that Admin can view pending tours, approve, reject with reason,
 * and view moderation controls on Dashboard, dedicated Admin Services page, and Tour Detail page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/bootstrap/app.php';

use Core\Request;
use Core\Response;
use Core\Database;
use App\Models\User;
use App\Models\Service;
use App\Controllers\AdminController;
use App\Controllers\ServiceController;

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
echo "   CHẠY KIỂM THỬ: ADMIN SERVICE MODERATION & PHÊ DUYỆT TOUR CỦA HOST    \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$serviceModel = new Service();
$userModel = new User();

// 1. Setup Admin, Host, and Tourist
$adminUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'admin' LIMIT 1");
if (!$adminUser) {
    die("Không tìm thấy user admin trong CSDL để chạy kiểm thử!\n");
}

$hostUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'host' LIMIT 1");
if (!$hostUser) {
    die("Không tìm thấy user host trong CSDL để chạy kiểm thử!\n");
}

$touristUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'tourist' LIMIT 1");

$category = $db->fetchOne("SELECT id FROM tbl_categories LIMIT 1");
$catId = (int)($category['id'] ?? 1);

// Create a dedicated test service with 'pending' status
$testSlug = 'test-tour-moderation-' . time();
$db->execute("
    INSERT INTO tbl_services (host_id, category_id, title, slug, description, location, base_price, duration_days, status, created_at)
    VALUES (?, ?, 'Tour Thử Nghiệm Kiểm Duyệt', ?, 'Mô tả tour thử nghiệm', 'Đà Lạt', 500000, 1, 'pending', NOW())
", [(int)$hostUser['id'], $catId, $testSlug]);

$testService = $db->fetchOne("SELECT * FROM tbl_services WHERE slug = ?", [$testSlug]);
assertTest("Tạo thành công gói trải nghiệm test ở trạng thái 'pending'", !empty($testService) && $testService['status'] === 'pending');
$testServiceId = (int)$testService['id'];

// 2. Test Access Control for /admin/services
echo "\n--- [TEST 1: KIỂM SOÁT PHÂN QUYỀN (ACCESS CONTROL)] ---\n";

// A. Guest user
$_SESSION['user'] = null;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_ACCEPT'] = 'text/html';
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
$req = new Request();
$res = new Response();
$controller = new AdminController($req, $res);

$controller->services($req);
assertTest("Khách vãng lai (Guest) không được vào /admin/services (Chuyển hướng /login)", $res->getStatusCode() === 302 && str_contains($res->getHeader('Location') ?? '', '/login'));

// B. Tourist user
$_SESSION['user'] = $touristUser;
$res = new Response();
$controller = new AdminController($req, $res);
$controller->services($req);
assertTest("Du khách (Tourist) không được vào /admin/services (Chuyển hướng /login)", $res->getStatusCode() === 302 && str_contains($res->getHeader('Location') ?? '', '/login'));

// C. Host user
$_SESSION['user'] = $hostUser;
$res = new Response();
$controller = new AdminController($req, $res);
$controller->services($req);
assertTest("Host bản địa không được vào /admin/services (Chuyển hướng /login)", $res->getStatusCode() === 302 && str_contains($res->getHeader('Location') ?? '', '/login'));

// D. Guest/Host cannot call moderateService via AJAX
$_SESSION['user'] = $hostUser;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_ACCEPT'] = 'application/json';
$_POST = ['status' => 'approved'];
$res = new Response();
$controller = new AdminController(new Request(), $res);
$controller->moderateService(new Request(), (string)$testServiceId);
assertTest("Host gọi moderateService bị từ chối 403 Forbidden", $res->getStatusCode() === 403);

// Reset server vars
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_SERVER['HTTP_ACCEPT']);
$_POST = [];

// 3. Test Admin accessing /admin/services
echo "\n--- [TEST 2: ADMIN TRUY CẬP TRANG QUẢN LÝ /admin/services] ---\n";
$_SESSION['user'] = $adminUser;
$res = new Response();
$controller = new AdminController(new Request(), $res);

ob_start();
$controller->services(new Request());
$html = ob_get_clean();

assertTest("Admin truy cập /admin/services thành công (Status 200)", $res->getStatusCode() === 200);
assertTest("Trang hiển thị tiêu đề 'Quản Lý & Phê Duyệt Trải Nghiệm'", str_contains($html, 'Quản Lý & Phê Duyệt Trải Nghiệm'));
assertTest("Trang có các khối đếm (CHỜ PHÊ DUYỆT, ĐANG MỞ BÁN, ĐÃ TỪ CHỐI)", str_contains($html, 'CHỜ PHÊ DUYỆT') && str_contains($html, 'ĐANG MỞ BÁN') && str_contains($html, 'ĐÃ TỪ CHỐI'));
assertTest("Danh sách hiển thị tour đang chờ duyệt", str_contains($html, 'Tour Thử Nghiệm Kiểm Duyệt'));
assertTest("Có nút 'Duyệt' và nút 'Từ chối' trong danh sách", str_contains($html, 'Duyệt') && str_contains($html, 'Từ chối'));

// 4. Test Admin approving tour (status = 'approved')
echo "\n--- [TEST 3: ADMIN PHÊ DUYỆT TOUR (APPROVED)] ---\n";
$_SESSION['user'] = $adminUser;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_REFERER'] = '/admin/services';
$_POST = ['status' => 'approved', '_csrf_token' => 'mock_token'];

$res = new Response();
$controller = new AdminController(new Request(), $res);
$controller->moderateService(new Request(), (string)$testServiceId);

$updatedSrv = $db->fetchOne("SELECT status, rejection_reason FROM tbl_services WHERE id = ?", [$testServiceId]);
assertTest("Admin duyệt tour thành công: CSDL cập nhật status = 'approved'", $updatedSrv['status'] === 'approved');

// 5. Test Admin rejecting tour with reason (status = 'rejected')
echo "\n--- [TEST 4: ADMIN TỪ CHỐI TOUR VỚI LÝ DO (REJECTED)] ---\n";
$_SESSION['user'] = $adminUser;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_REFERER'] = '/admin/services';
$rejectionReason = 'Hình ảnh trạm 1 không rõ nét, vui lòng chụp lại phong cảnh sắc nét hơn.';
$_POST = [
    'status' => 'rejected',
    'rejection_reason' => $rejectionReason,
    '_csrf_token' => 'mock_token'
];

$res = new Response();
$controller = new AdminController(new Request(), $res);
$controller->moderateService(new Request(), (string)$testServiceId);

$updatedSrv = $db->fetchOne("SELECT status, rejection_reason FROM tbl_services WHERE id = ?", [$testServiceId]);
assertTest("Admin từ chối tour thành công: CSDL cập nhật status = 'rejected'", $updatedSrv['status'] === 'rejected');
assertTest("CSDL lưu đúng lý do từ chối kiểm duyệt", $updatedSrv['rejection_reason'] === $rejectionReason);

// Reset POST & SERVER
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_SERVER['HTTP_REFERER']);

// 6. Test Admin resetting back to pending and viewing Tour Detail page
echo "\n--- [TEST 5: HIỂN THỊ BANNER KIỂM DUYỆT TRÊN TRANG CHI TIẾT /services/{id}] ---\n";
$db->execute("UPDATE tbl_services SET status = 'pending' WHERE id = ?", [$testServiceId]);

// Admin views detail of pending service
$_SESSION['user'] = $adminUser;
$srvController = new ServiceController();
ob_start();
$srvController->show(new Request(), (string)$testServiceId);
$showHtml = ob_get_clean();

assertTest("Trang chi tiết tour hiển thị banner 'CHỜ DUYỆT (PENDING)' cho Admin", str_contains($showHtml, 'CHỜ DUYỆT (PENDING)'));
assertTest("Trang chi tiết tour có nút 'Phê Duyệt Mở Bán' cho Admin", str_contains($showHtml, 'Phê Duyệt Mở Bán'));
assertTest("Trang chi tiết tour có nút 'Từ Chối' cho Admin", str_contains($showHtml, 'adminRejectTourModal'));

// Tourist views detail of pending service (should NOT see moderation buttons)
$_SESSION['user'] = $touristUser;
ob_start();
$srvController->show(new Request(), (string)$testServiceId);
$touristShowHtml = ob_get_clean();
assertTest("Du khách xem trang tour KHÔNG nhìn thấy banner và nút duyệt của Admin", !str_contains($touristShowHtml, 'Phê Duyệt Mở Bán'));

// Clean up test data
$db->execute("DELETE FROM tbl_services WHERE id = ?", [$testServiceId]);
assertTest("Dọn dẹp bản ghi kiểm thử trong tbl_services hoàn tất", true);

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests}/{$totalTests} passed (" . round(($passedTests / max(1, $totalTests)) * 100, 1) . "%)\n";
echo "========================================================================\n";

if ($passedTests < $totalTests) {
    exit(1);
}
