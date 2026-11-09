<?php

declare(strict_types=1);

/**
 * Sprint 2: TV1-05 Test Suite
 * Automated tests for RBAC, RoleMiddleware, HostMiddleware, AdminMiddleware, AuthMiddleware, and CsrfMiddleware.
 */

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require necessary core and application files
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/BaseModel.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/middleware/RoleMiddleware.php';
require_once __DIR__ . '/../app/middleware/HostMiddleware.php';
require_once __DIR__ . '/../app/middleware/AdminMiddleware.php';
require_once __DIR__ . '/../app/middleware/CsrfMiddleware.php';

use Core\Request;
use Core\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\HostMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;

// Enable non-terminating exit in test environment
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
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV1-05: RBAC & CSRF MIDDLEWARE         \n";
echo "========================================================================\n\n";

// Helper dummy next closure
$dummyNext = function (Request $req) {
    return 'NEXT_CALLED';
};

// ==============================================================================
// TEST GROUP 1: GUEST ACCESS TO PROTECTED ROUTES (WEB & AJAX)
// ==============================================================================
echo "--- NHÓM 1: KHÁCH VÃNG LAI (GUEST) TRUY CẬP ROUTE BẢO VỆ ---\n";

// 1.1 Web Guest -> AuthMiddleware -> Redirect to /login
$_SESSION = [];
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/bookings',
    'SCRIPT_NAME' => '/index.php',
];
$req = new Request();
$authMw = new AuthMiddleware();

ob_start();
$res = $authMw->handle($req, $dummyNext);
$output = ob_get_clean();

assertTest("Guest Web accessing /bookings triggers redirect to /login (302)", 
    Response::$lastResponse !== null && Response::$lastResponse->getStatusCode() === 302);
assertTest("Guest Web: Flash warning 'Vui lòng đăng nhập để tiếp tục.' được thiết lập", 
    !empty($_SESSION['flash']['warning']) && in_array('Vui lòng đăng nhập để tiếp tục.', $_SESSION['flash']['warning']));

// 1.2 AJAX Guest -> AuthMiddleware -> 401 JSON
$_SESSION = [];
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/bookings',
    'SCRIPT_NAME' => '/index.php',
    'HTTP_X_REQUESTED_WITH' => 'xmlhttprequest',
];
$req = new Request();
ob_start();
$authMw->handle($req, $dummyNext);
$jsonOutput = ob_get_clean();
$decoded = json_decode($jsonOutput, true);
assertTest("Guest AJAX: AuthMiddleware trả về JSON mã lỗi 401", 
    (Response::$lastResponse !== null && Response::$lastResponse->getStatusCode() === 401)
    && isset($decoded['error']) && str_contains($decoded['error'], 'đăng nhập'));

// 1.3 Web Guest -> RoleMiddleware('admin') -> Redirect
$_SESSION = [];
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/admin',
    'SCRIPT_NAME' => '/index.php',
];
$adminMw = new AdminMiddleware();
ob_start();
try {
    $adminMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
ob_get_clean();
assertTest("Guest Web: AdminMiddleware chặn và thiết lập thông báo yêu cầu đăng nhập", 
    !empty($_SESSION['flash']['warning']));

// ==============================================================================
// TEST GROUP 2: ROLE-BASED ACCESS CONTROL (TOURIST, HOST, ADMIN)
// ==============================================================================
echo "\n--- NHÓM 2: PHÂN QUYỀN TRUY CẬP (TOURIST, HOST, ADMIN) ---\n";

// 2.1 Tourist accessing /host (Web) -> HTTP 403 with custom error view
$_SESSION['user'] = [
    'id' => 9991,
    'name' => 'Test Tourist',
    'email' => 'tourist_test@example.com',
    'role' => 'tourist',
    'status' => 'active',
];
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/host',
    'SCRIPT_NAME' => '/index.php',
];
$hostMw = new HostMiddleware();
ob_start();
try {
    $hostMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$output403 = ob_get_clean();

assertTest("Tourist Web vào /host: Trả về HTTP 403", http_response_code() === 403);
assertTest("Tourist Web vào /host: Hiển thị giao diện app/views/errors/403.php chuẩn Dark-Alpine", 
    str_contains($output403, '403 FORBIDDEN') && str_contains($output403, 'Khu Vực Giới Hạn Quyền Hạn'));

// 2.2 Tourist accessing /host (AJAX) -> HTTP 403 JSON
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
ob_start();
try {
    $hostMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$ajax403 = ob_get_clean();
$decodedAjax = json_decode($ajax403, true);
assertTest("Tourist AJAX vào /host: Trả về HTTP 403 JSON thông báo không có quyền", 
    http_response_code() === 403 && isset($decodedAjax['error']) && str_contains($decodedAjax['error'], 'quyền'));

// 2.3 Tourist accessing /admin (Web) -> HTTP 403
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
ob_start();
try {
    $adminMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$outputAdmin403 = ob_get_clean();
assertTest("Tourist Web vào /admin: Trả về HTTP 403", http_response_code() === 403);
assertTest("Tourist Web vào /admin: Hiển thị trang 403.php an toàn không lộ chi tiết máy chủ", 
    str_contains($outputAdmin403, 'Khu Vực Giới Hạn Quyền Hạn'));

// 2.4 Host accessing /admin (Web) -> HTTP 403
$_SESSION['user'] = [
    'id' => 9992,
    'name' => 'Test Host',
    'email' => 'host_test@example.com',
    'role' => 'host',
    'status' => 'active',
];
ob_start();
try {
    $adminMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$hostToAdminOutput = ob_get_clean();
assertTest("Host Web vào /admin: Bị chặn với HTTP 403", http_response_code() === 403);

// 2.5 Host accessing /host -> Cho phép đi tiếp (NEXT_CALLED)
$resultHost = $hostMw->handle(new Request(), $dummyNext);
assertTest("Host vào /host: Được phép truy cập (HostMiddleware next passed)", $resultHost === 'NEXT_CALLED');

// 2.6 Admin accessing /admin -> Cho phép đi tiếp
$_SESSION['user'] = [
    'id' => 9993,
    'name' => 'Test Admin',
    'email' => 'admin_test@example.com',
    'role' => 'admin',
    'status' => 'active',
];
$resultAdmin = $adminMw->handle(new Request(), $dummyNext);
assertTest("Admin vào /admin: Được phép truy cập (AdminMiddleware next passed)", $resultAdmin === 'NEXT_CALLED');

// 2.7 Admin accessing /host -> Giám sát host được phép đi tiếp
$resultAdminToHost = $hostMw->handle(new Request(), $dummyNext);
assertTest("Admin vào /host (giám sát): Được phép truy cập", $resultAdminToHost === 'NEXT_CALLED');

// ==============================================================================
// TEST GROUP 3: VERIFY ACCOUNT STATUS (LOCKED & INACTIVE)
// ==============================================================================
echo "\n--- NHÓM 3: XÁC THỰC TRẠNG THÁI TÀI KHOẢN (LOCKED / INACTIVE) ---\n";

// 3.1 Locked account accessing protected route (Web)
$_SESSION['user'] = [
    'id' => 9994,
    'name' => 'Locked User',
    'email' => 'locked@example.com',
    'role' => 'tourist',
    'status' => 'locked',
];
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/profile',
    'SCRIPT_NAME' => '/index.php',
];
ob_start();
try {
    $authMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
ob_get_clean();

assertTest("Tài khoản bị khóa (locked): Phiên làm việc bị thu hồi (unset user session)", empty($_SESSION['user']));
assertTest("Tài khoản bị khóa (locked): Thiết lập flash thông báo 'Tài khoản của bạn đã bị khóa bởi Quản trị viên.'", 
    !empty($_SESSION['flash']['danger']) && in_array('Tài khoản của bạn đã bị khóa bởi Quản trị viên.', $_SESSION['flash']['danger']));

// 3.2 Locked account accessing protected route (AJAX)
$_SESSION['user'] = [
    'id' => 9995,
    'name' => 'Locked User AJAX',
    'email' => 'locked_ajax@example.com',
    'role' => 'tourist',
    'status' => 'locked',
];
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
ob_start();
try {
    $authMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$lockedAjax = ob_get_clean();
$decodedLocked = json_decode($lockedAjax, true);

assertTest("Tài khoản bị khóa qua AJAX: Trả về HTTP 403 JSON thông báo tài khoản bị khóa", 
    http_response_code() === 403 && isset($decodedLocked['error']) && str_contains($decodedLocked['error'], 'khóa'));

// 3.3 RoleMiddleware also enforces account status
$_SESSION['user'] = [
    'id' => 9996,
    'name' => 'Locked Host',
    'email' => 'locked_host@example.com',
    'role' => 'host',
    'status' => 'locked',
];
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
ob_start();
try {
    $hostMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
ob_get_clean();
assertTest("Host bị khóa vào /host: RoleMiddleware lập tức thu hồi session và báo lỗi", empty($_SESSION['user']));

// ==============================================================================
// TEST GROUP 4: CSRF PROTECTION MIDDLEWARE
// ==============================================================================
echo "\n--- NHÓM 4: BẢO MẬT BIỂU MẪU CSRF (POST / PUT / DELETE) ---\n";

// Generate a valid token
$validToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $validToken;

$csrfMw = new CsrfMiddleware();

// 4.1 Missing CSRF token on POST request (Web) -> HTTP 419 + custom 419 view
$_POST = ['name' => 'Nguyen Van A'];
$_SERVER = [
    'REQUEST_METHOD' => 'POST',
    'REQUEST_URI' => '/profile',
    'SCRIPT_NAME' => '/index.php',
];
ob_start();
try {
    $csrfMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$output419 = ob_get_clean();

assertTest("POST thiếu CSRF Token (Web): Trả về HTTP 419", http_response_code() === 419);
assertTest("POST thiếu CSRF Token (Web): Hiển thị view app/views/errors/419.php", 
    str_contains($output419, '419 PAGE EXPIRED') && str_contains($output419, 'Phiên Bảo Mật Đã Hết Hạn'));

// 4.2 Missing CSRF token on POST request (AJAX) -> HTTP 419 JSON
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
ob_start();
try {
    $csrfMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$ajax419 = ob_get_clean();
$decoded419 = json_decode($ajax419, true);

assertTest("POST thiếu CSRF Token (AJAX): Trả về HTTP 419 JSON", 
    http_response_code() === 419 && isset($decoded419['error']) && str_contains($decoded419['error'], 'CSRF'));

// 4.3 Invalid CSRF token
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
$_POST['_csrf_token'] = 'invalid_tampered_token_xyz';
ob_start();
try {
    $csrfMw->handle(new Request(), $dummyNext);
} catch (\Throwable $e) {}
$tampered419 = ob_get_clean();
assertTest("POST với CSRF Token giả mạo: Bị từ chối với HTTP 419", http_response_code() === 419);

// 4.4 Valid CSRF token via '_csrf_token' field
$_POST['_csrf_token'] = $validToken;
$resCsrf1 = $csrfMw->handle(new Request(), $dummyNext);
assertTest("POST với trường '_csrf_token' hợp lệ: Cho phép đi tiếp", $resCsrf1 === 'NEXT_CALLED');

// 4.5 Valid CSRF token via legacy 'csrf_token' field
unset($_POST['_csrf_token']);
$_POST['csrf_token'] = $validToken;
$resCsrf2 = $csrfMw->handle(new Request(), $dummyNext);
assertTest("POST với trường 'csrf_token' hợp lệ: Cho phép đi tiếp", $resCsrf2 === 'NEXT_CALLED');

// 4.6 Valid CSRF token via HTTP Header 'X-CSRF-TOKEN'
$_POST = [];
$_SERVER['HTTP_X_CSRF_TOKEN'] = $validToken;
$resCsrf3 = $csrfMw->handle(new Request(), $dummyNext);
assertTest("POST với Header 'X-CSRF-TOKEN' hợp lệ: Cho phép đi tiếp", $resCsrf3 === 'NEXT_CALLED');

// 4.7 GET requests are exempt from CSRF checks
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_SERVER['HTTP_X_CSRF_TOKEN']);
$resCsrfGet = $csrfMw->handle(new Request(), $dummyNext);
assertTest("GET request tự động bỏ qua kiểm tra CSRF", $resCsrfGet === 'NEXT_CALLED');

// ==============================================================================
// TEST GROUP 5: ERROR VIEWS VALIDATION (NO CRASHES, SAFE CONTENT)
// ==============================================================================
echo "\n--- NHÓM 5: KIỂM TRA TẤT CẢ GIAO DIỆN LỖI (ERRORS VIEW) ---\n";

foreach ([403, 404, 419, 500] as $code) {
    $filePath = dirname(__DIR__) . "/app/views/errors/{$code}.php";
    assertTest("File app/views/errors/{$code}.php tồn tại", is_file($filePath));
    
    ob_start();
    include $filePath;
    $rendered = ob_get_clean();
    
    assertTest("View {$code}.php chứa mã lỗi HTTP {$code} và thông báo song ngữ/tiếng Việt", 
        str_contains($rendered, (string)$code) && str_contains($rendered, 'OnlineTraveller'));
    assertTest("View {$code}.php KHÔNG làm lộ chi tiết máy chủ hay lỗi PHP nhạy cảm", 
        !str_contains($rendered, 'stack trace') && !str_contains($rendered, 'Fatal error') && !str_contains($rendered, 'Notice'));
}

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV1-05 THÀNH CÔNG RỰC RỠ 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
