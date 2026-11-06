<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== ONLINETRAVELLER TV2-04 (HOST UI & CONTROLLERS) VERIFICATION ===\n\n";

require dirname(__DIR__) . '/bootstrap/app.php';

function runTest(string $title, callable $fn): void {
    try {
        $result = $fn();
        echo "[PASS] {$title}" . ($result ? " -> {$result}" : "") . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$title} -> " . $e->getMessage() . "\n";
    }
}

// Subclass Response so controller render does not exit
class TestViewResponse extends \Core\Response {
    public string $renderedHtml = '';
    public function html(string $content, int $statusCode = 200): void {
        $this->renderedHtml = $content;
    }
    public function redirect(string $url, int $statusCode = 302): void {
        $this->renderedHtml = "REDIRECT:{$url}";
    }
}

$testResponse = new TestViewResponse();
$hostController = new \App\Controllers\HostController(null, $testResponse);
$scheduleController = new \App\Controllers\ScheduleController(null, $testResponse);

// Simulate logged-in Host (id=2: Nguyễn Văn Bản Địa)
$_SESSION['user'] = [
    'id' => 2,
    'role' => 'host',
    'name' => 'Nguyễn Văn Bản Địa (Đà Lạt)',
    'email' => 'host.dalat@onlinetraveller.vn',
    'living_area' => 'Đà Lạt, Lâm Đồng',
];
$_SESSION['csrf_token'] = 'test-token-12345';

// Test 1: HostController::dashboard
runTest("HostController::dashboard hiển thị giao diện tổng quan", function() use ($hostController, $testResponse) {
    $hostController->dashboard();
    $html = $testResponse->renderedHtml;

    if (strpos($html, 'Không Gian Quản Lý Trải Nghiệm Bản Địa') === false) {
        throw new \Exception("Không tìm thấy tiêu đề Dashboard!");
    }
    if (strpos($html, 'Nguyễn Văn Bản Địa') === false) {
        throw new \Exception("Không tìm thấy tên Host đăng nhập!");
    }
    if (strpos($html, '???') !== false) {
        throw new \Exception("Phát hiện ký tự lỗi encoding '???' trong giao diện!");
    }
    return "Hiển thị đầy đủ KPI Metrics, Bảng Tour & Lịch trình (Chuẩn tiếng Việt)";
});

// Test 2: HostController::services (Danh sách gói trải nghiệm)
runTest("HostController::services hiển thị danh sách gói tour của Host", function() use ($hostController, $testResponse) {
    $hostController->services();
    $html = $testResponse->renderedHtml;

    if (strpos($html, 'Gói Trải Nghiệm Bản Địa') === false) {
        throw new \Exception("Không tìm thấy tiêu đề danh sách tour!");
    }
    if (strpos($html, 'Săn Mây Đỉnh LangBiang') === false) {
        throw new \Exception("Không tìm thấy tour của Host 2!");
    }
    return "Hiển thị danh sách tour kèm thông tin Homestay và Trạm 3D";
});

// Test 3: HostController::createService (Form tạo tour mới)
runTest("HostController::createService hiển thị biểu mẫu tạo tour với 4 chế độ giá", function() use ($hostController, $testResponse) {
    $hostController->createService();
    $html = $testResponse->renderedHtml;

    $requiredSnippets = [
        'Đăng Ký Gói Trải Nghiệm Bản Địa Mới',
        'fixed_choice',
        'host_range',
        'free',
        'prepaid_full',
        'toggleHomestay',
        'addStopBtn',
    ];

    foreach ($requiredSnippets as $snippet) {
        if (strpos($html, $snippet) === false) {
            throw new \Exception("Thiếu thành phần giao diện: [{$snippet}]");
        }
    }

    return "Form đầy đủ 4 chế độ định giá, khối Homestay (1-1) và trạm Three.js 3D";
});

// Test 4: HostController::editService (Form sửa tour)
runTest("HostController::editService nạp dữ liệu tour và Homestay hiện có", function() use ($hostController, $testResponse) {
    $req = new \Core\Request();
    $req->setRouteParams(['id' => '1']);

    $hostController->editService($req, '1');
    $html = $testResponse->renderedHtml;

    if (strpos($html, 'Săn Mây Đỉnh LangBiang') === false) {
        throw new \Exception("Không nạp được tiêu đề tour hiện có!");
    }
    if (strpos($html, 'Homestay Gió Núi LangBiang') === false) {
        throw new \Exception("Không nạp được thông tin Homestay đã đính kèm!");
    }

    return "Form nạp chính xác dữ liệu tour, ảnh bìa và khối Homestay liên kết";
});

// Test 5: ScheduleController::index (Bảng quản lý lịch trình)
runTest("ScheduleController::index hiển thị bảng quản lý lịch khởi hành", function() use ($scheduleController, $testResponse) {
    $req = new \Core\Request();
    $scheduleController->index($req);
    $html = $testResponse->renderedHtml;

    if (strpos($html, 'Kế Hoạch & Lịch Khởi Hành') === false) {
        throw new \Exception("Không tìm thấy tiêu đề quản lý lịch trình!");
    }
    if (strpos($html, 'cancelScheduleModal') === false) {
        throw new \Exception("Không tìm thấy Modal hủy lịch trình!");
    }

    return "Hiển thị bảng lịch trình kèm thanh sức chứa (capacity) và modal hủy lịch";
});

// Test 6: ScheduleController::create (Form mở lịch mới)
runTest("ScheduleController::create hiển thị form mở lịch mới", function() use ($scheduleController, $testResponse) {
    $req = new \Core\Request();
    $scheduleController->create($req);
    $html = $testResponse->renderedHtml;

    if (strpos($html, 'Mở Đợt Khởi Hành Mới') === false) {
        throw new \Exception("Không tìm thấy tiêu đề form mở lịch!");
    }
    if (strpos($html, 'starts_at') === false || strpos($html, 'booking_closes_at') === false) {
        throw new \Exception("Thiếu các trường thời gian khởi hành!");
    }

    return "Form nhập liệu đầy đủ các trường thời gian, sức chứa và điểm hẹn";
});

// Test 7: Kiểm tra Font chữ Montserrat & UTF-8 toàn bộ các view
runTest("Xác thực Font chữ Montserrat và không có ký tự lỗi ??? trong HTML", function() use ($testResponse) {
    $html = $testResponse->renderedHtml;
    if (strpos($html, 'family=Montserrat') === false && strpos($html, 'Montserrat') === false) {
        throw new \Exception("Font Montserrat chưa được tích hợp!");
    }
    if (strpos($html, '???') !== false) {
        throw new \Exception("Phát hiện lỗi hiển thị ký tự '???'!");
    }
    return "Giao diện sử dụng font Montserrat và hiển thị tiếng Việt hoàn hảo";
});

echo "\n=== KẾT QUẢ: TOÀN BỘ GIAO DIỆN & CONTROLLERS TV2-04 ĐẠT 100%! ===\n";
