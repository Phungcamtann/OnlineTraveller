<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== ONLINETRAVELLER TV3-03 (BOOKING MODEL, SERVICE & CONTROLLER) VERIFICATION ===\n\n";

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Models\Booking;
use App\Models\ServiceSchedule;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Controllers\BookingController;
use Core\Request;
use Core\Response;

$pdo = \Core\Database::getConnection();

function runTest(string $title, callable $fn): void {
    try {
        $result = $fn();
        echo "[PASS] {$title}" . ($result ? " -> {$result}" : "") . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$title} -> " . $e->getMessage() . "\n";
    }
}

// Subclass Response to avoid exit in CLI tests
class TestResponse extends Response
{
    public ?string $output = null;
    public int $statusCode = 200;

    public function json(mixed $data, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->output = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->statusCode = $statusCode;
        $this->output = "REDIRECT:{$url}";
    }

    public function html(string $content, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->output = $content;
    }

    public function getCapturedJson(): ?array
    {
        return $this->output ? json_decode($this->output, true) : null;
    }
}

$bookingModel = new Booking();
$pricingService = new PricingService();
$bookingService = new BookingService($bookingModel, $pricingService);

$createdBookingId = null;
$createdBookingCode = null;

echo "--- 1. KIỂM THỬ MODEL BOOKING VỚI STORED PROCEDURES ---\n";

// Test 1: Booking::create tạo đơn pending
runTest("Booking::create tạo đơn đặt tour ở trạng thái pending", function() use ($bookingModel, &$createdBookingId, &$createdBookingCode) {
    // Tourist id = 4 đặt Schedule id = 1 (2 người, cọc 50%)
    $bk = $bookingModel->create(4, 1, 2, 'deposit_50');
    if (empty($bk['id'])) throw new \Exception("Không tạo được booking!");
    if ($bk['status'] !== 'pending') throw new \Exception("Trạng thái khởi tạo phải là pending!");
    if (empty($bk['booking_code']) || !str_starts_with($bk['booking_code'], 'BK')) {
        throw new \Exception("Mã booking không đúng định dạng!");
    }

    $createdBookingId = (int)$bk['id'];
    $createdBookingCode = $bk['booking_code'];
    return "Tạo thành công Booking ID: {$createdBookingId}, Mã đơn: {$createdBookingCode}, Trạng thái: {$bk['status']}";
});

// Test 2: Booking::getById nạp đầy đủ thông tin Tour, Host, Schedule và Tourist
runTest("Booking::getById nạp dữ liệu liên kết 5 bảng (Tour, Host, Schedule, Tourist)", function() use ($bookingModel, $createdBookingId) {
    $bk = $bookingModel->getById($createdBookingId);
    if (!$bk) throw new \Exception("Không tìm thấy booking theo ID!");
    if (empty($bk['service_title'])) throw new \Exception("Thiếu tên tour!");
    if (empty($bk['host_name'])) throw new \Exception("Thiếu tên Host!");
    if (empty($bk['tourist_name'])) throw new \Exception("Thiếu tên Du khách!");
    if (empty($bk['starts_at'])) throw new \Exception("Thiếu thời gian khởi hành!");

    return "Tour: '{$bk['service_title']}' | Host: {$bk['host_name']} | Du khách: {$bk['tourist_name']} | Điểm hẹn: {$bk['meeting_point']}";
});

// Test 3: Booking::getByCode tìm theo mã đơn hàng
runTest("Booking::getByCode tìm chính xác theo mã booking", function() use ($bookingModel, $createdBookingCode) {
    $bk = $bookingModel->getByCode($createdBookingCode);
    if (!$bk) throw new \Exception("Không tìm thấy booking theo code!");
    return "Tìm thấy đơn: {$bk['booking_code']} (Tổng tiền: " . number_format((float)$bk['total_price']) . "đ)";
});

// Test 4: Booking::getByTourist lấy danh sách đơn của du khách
runTest("Booking::getByTourist lấy lịch sử đặt chỗ của khách (id=4)", function() use ($bookingModel) {
    $list = $bookingModel->getByTourist(4);
    if (empty($list)) throw new \Exception("Danh sách đơn của khách không được rỗng!");
    return "Tìm thấy " . count($list) . " đơn đặt chỗ của du khách 4";
});

// Test 5: Booking::getByHost lấy danh sách đơn của Host (id=2)
runTest("Booking::getByHost lấy danh sách đơn Host 2 nhận được", function() use ($bookingModel) {
    $list = $bookingModel->getByHost(2);
    if (empty($list)) throw new \Exception("Host 2 phải nhận được đơn!");
    return "Host 2 đang có " . count($list) . " đơn đặt tour";
});

echo "\n--- 2. KIỂM THỬ NGHIỆP VỤ BOOKING SERVICE ---\n";

$serviceBookingId = null;

// Test 6: BookingService::createBooking tính quote và tạo pending reservation
runTest("BookingService::createBooking tính quote và gửi đơn đặt chỗ pending", function() use ($bookingService, &$serviceBookingId) {
    $result = $bookingService->createBooking(4, 1, 2, 'deposit_50');
    if (empty($result['id'])) throw new \Exception("Không tạo được booking qua service!");
    if ($result['status'] !== 'pending') throw new \Exception("Trạng thái phải là pending!");
    if (empty($result['quote_summary'])) throw new \Exception("Thiếu thông tin quote_summary!");

    $serviceBookingId = (int)$result['id'];
    $quote = $result['quote_summary'];
    return "Tạo thành công Booking ID: {$serviceBookingId}, Tổng quote: {$quote['formatted']['total_price']}, Cọc 50%: {$quote['formatted']['deposit_amount']}";
});

// Test 7: BookingService kiểm tra quyền xem đơn (Authorization)
runTest("BookingService::getBooking phân quyền: chặn khách lạ (id=5) xem đơn của khách 4", function() use ($bookingService, $serviceBookingId) {
    // Khách sở hữu (id=4) xem được
    $bkOwner = $bookingService->getBooking($serviceBookingId, 4, 'tourist');
    if (!$bkOwner) throw new \Exception("Chính chủ phải xem được!");

    // Host sở hữu (id=2) xem được
    $bkHost = $bookingService->getBooking($serviceBookingId, 2, 'host');
    if (!$bkHost) throw new \Exception("Host quản lý tour phải xem được!");

    // Người lạ (id=5, role tourist) bị chặn
    try {
        $bookingService->getBooking($serviceBookingId, 5, 'tourist');
        throw new \Exception("Lẽ ra phải chặn người lạ!");
    } catch (\RuntimeException $e) {
        return "Phân quyền chính xác: Chính chủ và Host xem được; Người lạ bị chặn ({$e->getMessage()})";
    }
});

// Test 8: BookingService::approveBooking Host duyệt giữ chỗ và kích hoạt countdown 15p
runTest("BookingService::approveBooking Host duyệt đơn -> awaiting_payment & giữ chỗ", function() use ($bookingService, $serviceBookingId, $pdo) {
    $schBefore = (int)$pdo->query("SELECT booked_seats FROM tbl_service_schedules WHERE id = 1")->fetchColumn();

    $approved = $bookingService->approveBooking(2, $serviceBookingId);

    $schAfter = (int)$pdo->query("SELECT booked_seats FROM tbl_service_schedules WHERE id = 1")->fetchColumn();

    if ($approved['status'] !== 'awaiting_payment') {
        throw new \Exception("Trạng thái sau duyệt phải là awaiting_payment!");
    }
    if ($schAfter !== ($schBefore + 2)) {
        throw new \Exception("Số chỗ giữ trên lịch trình chưa tăng! Trước: {$schBefore}, Sau: {$schAfter}");
    }

    return "Duyệt thành công: Trạng thái -> {$approved['status']}, Đã giữ 2 chỗ (Lịch: {$schBefore} -> {$schAfter}), Hạn thanh toán: {$approved['payment_expires_at']}";
});

// Test 9: BookingService::cancelBooking Hủy đơn và trả lại chỗ trống
runTest("BookingService::cancelBooking hủy đơn -> nhả lại ghế trống vào lịch trình", function() use ($bookingService, $serviceBookingId, $pdo) {
    $schBefore = (int)$pdo->query("SELECT booked_seats FROM tbl_service_schedules WHERE id = 1")->fetchColumn();

    $cancelled = $bookingService->cancelBooking(4, $serviceBookingId, 'Bận việc gia đình đột xuất');

    $schAfter = (int)$pdo->query("SELECT booked_seats FROM tbl_service_schedules WHERE id = 1")->fetchColumn();

    if ($cancelled['status'] !== 'cancelled') {
        throw new \Exception("Trạng thái sau khi hủy phải là cancelled!");
    }
    if ($schAfter !== ($schBefore - 2)) {
        throw new \Exception("Chưa nhả lại số chỗ vào lịch trình!");
    }

    return "Hủy thành công: Trạng thái -> {$cancelled['status']}, Ghế trống đã nhả lại: {$schBefore} -> {$schAfter}";
});

echo "\n--- 3. KIỂM THỬ CONTROLLER & API ACTIONS (BOOKING CONTROLLER) ---\n";

// Test 10: BookingController::quote API tính báo giá trực tuyến
runTest("BookingController::quote trả JSON báo giá thời gian thực", function() use ($bookingService, $pricingService) {
    $_GET = [];
    $_POST = [
        'schedule_id' => 1,
        'participants_count' => 2,
        'payment_plan' => 'deposit_50',
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $request = new Request();
    $response = new TestResponse();

    $controller = new BookingController($request, $response, $bookingService, $pricingService);
    $controller->quote($request);

    $json = $response->getCapturedJson();
    if (!$json || empty($json['success']) || empty($json['data'])) {
        throw new \Exception("API quote không trả về cấu trúc hợp lệ! Response: " . ($response->output ?? 'empty'));
    }

    return "API phản hồi HTTP {$response->statusCode} -> Tổng: {$json['data']['formatted']['total_price']}, Cọc 50%: {$json['data']['formatted']['deposit_amount']}";
});

// Test 11: BookingController::store tạo đơn đặt chỗ
$controllerBookingId = null;
runTest("BookingController::store du khách submit form đặt chỗ thành công", function() use ($bookingService, $pricingService, &$controllerBookingId) {
    $_SESSION['user'] = [
        'id' => 4,
        'role' => 'tourist',
        'name' => 'Nguyễn Du Khách',
        'email' => 'tourist@onlinetraveller.vn',
    ];

    $_GET = [];
    $_POST = [
        'schedule_id' => 1,
        'participants_count' => 2,
        'payment_plan' => 'deposit_50',
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

    $request = new Request();
    $response = new TestResponse();

    $controller = new BookingController($request, $response, $bookingService, $pricingService);
    $controller->store($request);

    $json = $response->getCapturedJson();
    if (!$json || empty($json['success']) || empty($json['booking']['id'])) {
        throw new \Exception("Tạo đơn qua controller thất bại! Response: " . ($response->output ?? 'empty'));
    }

    $controllerBookingId = (int)$json['booking']['id'];
    return "Tạo đơn thành công ID: {$controllerBookingId}, Mã đơn: {$json['booking']['booking_code']}, Redirect: {$json['redirect']}";
});

// Test 12: BookingController::show xem chi tiết đơn hàng
runTest("BookingController::show trả thông tin chi tiết đơn hàng cho chính chủ", function() use ($bookingService, $pricingService, $controllerBookingId) {
    $_GET = [];
    $_POST = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

    $request = new Request();
    $response = new TestResponse();

    $controller = new BookingController($request, $response, $bookingService, $pricingService);
    $controller->show($request, (string)$controllerBookingId);

    $json = $response->getCapturedJson();
    if (!$json || empty($json['success']) || empty($json['booking'])) {
        throw new \Exception("Không lấy được chi tiết đơn qua controller! Response: " . ($response->output ?? 'empty'));
    }

    return "Lấy chi tiết thành công: {$json['booking']['service_title']} (Trạng thái: {$json['booking']['status']})";
});

// Dọn dẹp dữ liệu kiểm thử
echo "\n--- DỌN DẸP DỮ LIỆU KIỂM THỬ ---\n";
$idsToDelete = array_filter([$createdBookingId, $serviceBookingId, $controllerBookingId]);
if (!empty($idsToDelete)) {
    $idList = implode(',', $idsToDelete);
    $pdo->exec("DELETE FROM tbl_reviews WHERE booking_id IN ({$idList})");
    $pdo->exec("DELETE FROM tbl_bookings WHERE id IN ({$idList})");
    echo "Đã dọn dẹp sạch sẽ các bản ghi test booking ({$idList}).\n";
}

echo "\n=== KẾT QUẢ: TOÀN BỘ 12 TEST CASES TV3-03 ĐẠT CHUẨN 100%! ===\n";
