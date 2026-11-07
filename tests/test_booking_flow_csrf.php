<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Middleware\CsrfMiddleware;
use App\Models\Booking;
use App\Models\ServiceSchedule;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Controllers\BookingController;
use Core\Request;
use Core\Response;

echo "=== KIỂM THỬ LUỒNG ĐẶT TOUR ĐẦY ĐỦ VỚI CSRF TOKEN ===\n\n";

$pdo = \Core\Database::getConnection();

// Lấy du khách id=4 hoặc tạo phiên giả lập
$user = $pdo->query("SELECT id, email, role, name FROM tbl_users WHERE role='tourist' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die("Không tìm thấy user tourist!\n");
}
echo "1. Đăng nhập giả lập: {$user['name']} ({$user['email']}) - Role: {$user['role']}\n";
$_SESSION['user'] = $user;

// Xóa csrf token cũ để test xem SecurityHelper có tự sinh mới không
unset($_SESSION['csrf_token']);

// BƯỚC 1: Truy cập trang đặt tour GET /bookings/create?schedule_id=1
echo "2. Du khách vào trang đặt tour GET /bookings/create?schedule_id=1\n";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = ['schedule_id' => '1', 'count' => '2', 'plan' => 'deposit_50'];
$_POST = [];
unset($_SERVER['HTTP_X_REQUESTED_WITH']);

$request = new Request();
$scheduleModel = new ServiceSchedule();
$bookingModel = new Booking();
$pricingService = new PricingService();
$bookingService = new BookingService($bookingModel, $pricingService);

$quote = $pricingService->quoteSchedule(1, 2, 'deposit_50');
$availableSchedules = $scheduleModel->getByServiceId((int)$quote['service_id'], true);

// Render view create
ob_start();
require dirname(__DIR__) . '/app/views/bookings/create.php';
$html = ob_get_clean();

// Kiểm tra token đã sinh trong session và trong input form
if (empty($_SESSION['csrf_token'])) {
    die("[FAIL] Không sinh được CSRF token trong session!\n");
}
$token = $_SESSION['csrf_token'];
echo "   -> Sinh CSRF Token thành công: {$token}\n";

if (strpos($html, "name=\"_csrf_token\" value=\"{$token}\"") === false) {
    die("[FAIL] Input _csrf_token không khớp với token trong session!\n");
}
echo "   -> Input form chứa chính xác _csrf_token\n";

// BƯỚC 2: Submit form POST /bookings với _csrf_token
echo "\n3. Du khách bấm 'Gửi yêu cầu đặt chỗ ngay' (POST /bookings)\n";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    '_csrf_token' => $token,
    'schedule_id' => '1',
    'participants_count' => '2',
    'payment_plan' => 'deposit_50',
    'promo_code' => 'VIPLOCAL'
];
$_GET = [];

$requestPost = new Request();

// Cho chạy qua CsrfMiddleware
$csrfMiddleware = new CsrfMiddleware();
$middlewarePassed = false;

$createdBookingId = null;

$csrfMiddleware->handle($requestPost, function ($req) use (&$middlewarePassed, &$createdBookingId, $bookingService, $pricingService) {
    $middlewarePassed = true;
    echo "   -> [PASS] CsrfMiddleware xác thực token hợp lệ, cho phép đi tiếp!\n";

    // Chạy controller store
    $response = new class extends Response {
        public string $redirectUrl = '';
        public function redirect(string $url, int $statusCode = 302): void {
            $this->redirectUrl = $url;
        }
    };

    $controller = new BookingController($req, $response, $bookingService, $pricingService);
    $controller->store($req);

    echo "   -> BookingController::store tạo đơn thành công! Redirect tới: {$response->redirectUrl}\n";

    if (empty($response->redirectUrl) || strpos($response->redirectUrl, '/bookings/') === false) {
        throw new \Exception("Chuyển hướng thất bại sau khi đặt chỗ!");
    }

    if (preg_match('#/bookings/(\d+)#', $response->redirectUrl, $m)) {
        $createdBookingId = (int)$m[1];
    }

    return $response;
});

if (!$middlewarePassed) {
    die("[FAIL] CsrfMiddleware chặn request đặt tour!\n");
}

// BƯỚC 3: Test nếu dùng name="csrf_token" (tương thích ngược)
echo "\n4. Kiểm tra tương thích ngược khi form gửi trường 'csrf_token' thay vì '_csrf_token'\n";
$_POST = [
    'csrf_token' => $token,
    'schedule_id' => '1',
    'participants_count' => '1',
    'payment_plan' => 'deposit_50'
];
$requestCompat = new Request();
$compatPassed = false;
$csrfMiddleware->handle($requestCompat, function ($req) use (&$compatCompat, &$compatPassed) {
    $compatPassed = true;
    echo "   -> [PASS] Request::csrfToken() nhận diện thành công trường 'csrf_token'!\n";
    return true;
});

if (!$compatPassed) {
    die("[FAIL] Không nhận diện được 'csrf_token'!\n");
}

// Dọn dẹp dữ liệu test
if ($createdBookingId) {
    $pdo->exec("DELETE FROM tbl_bookings WHERE id = {$createdBookingId}");
    echo "   -> Đã dọn dẹp đơn hàng test #{$createdBookingId}\n";
}

echo "\n=== LUỒNG ĐẶT TOUR & XÁC THỰC BẢO MẬT CSRF HOẠT ĐỘNG HOÀN HẢO 100%! ===\n";
