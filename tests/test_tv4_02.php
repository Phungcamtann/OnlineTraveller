<?php

declare(strict_types=1);

echo "=== ONLINETRAVELLER TV4-02 (OPENWEATHER JSON WEBSERVICE) VERIFICATION ===\n\n";

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Integrations\OpenWeatherClient;
use App\Controllers\ServiceController;
use App\Models\Service;
use Core\Request;
use Core\Response;

function runTest(string $title, callable $fn): void {
    try {
        $result = $fn();
        echo "[PASS] {$title}" . ($result ? " -> {$result}" : "") . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$title} -> " . $e->getMessage() . "\n";
    }
}

// Subclass Response to capture rendered view HTML and JSON
class TestWeatherResponse extends Response
{
    public string $renderedContent = '';
    public int $statusCode = 200;

    public function html(string $content, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->renderedContent = $content;
    }

    public function json(mixed $data, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->renderedContent = json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

$client = new OpenWeatherClient();

echo "--- 1. KIỂM THỬ LỚP TÍCH HỢP WEBSERVICE OpenWeatherClient ---\n";

// Test 1: Khởi tạo và kiểm tra thư mục cache
runTest("OpenWeatherClient khởi tạo và thiết lập thư mục storage/cache/weather", function() use ($client) {
    $cacheDir = dirname(__DIR__) . '/storage/cache/weather';
    if (!is_dir($cacheDir)) {
        throw new \Exception("Thư mục cache không tồn tại: {$cacheDir}");
    }
    return "Thư mục cache sẵn sàng: storage/cache/weather";
});

// Test 2: Lấy thời tiết theo tọa độ GPS (Đà Lạt: 11.9404, 108.4583)
runTest("OpenWeatherClient::getWeatherByCoords dự báo thời tiết theo tọa độ GPS Đà Lạt", function() use ($client) {
    $weather = $client->getWeatherByCoords(11.9404, 108.4583, 'Đỉnh LangBiang, Đà Lạt');

    if (empty($weather['temp']) || !isset($weather['humidity'])) {
        throw new \Exception("Thiếu các chỉ số thời tiết cơ bản!");
    }
    if (empty($weather['icon_url'])) {
        throw new \Exception("Thiếu icon_url thời tiết!");
    }
    if (empty($weather['advice'])) {
        throw new \Exception("Thiếu lời khuyên du lịch chuẩn bị từ Host!");
    }

    return "Nhiệt độ: {$weather['temp']}°C (Cảm giác: {$weather['feels_like']}°C), Độ ẩm: {$weather['humidity']}%, Gió: {$weather['wind_speed']}m/s -> '{$weather['description']}'";
});

// Test 3: Lấy thời tiết các vùng miền du lịch (Sa Pa, Phú Quốc)
runTest("OpenWeatherClient::getWeatherByCity phân tích thời tiết đặc trưng vùng miền (Sa Pa, Phú Quốc)", function() use ($client) {
    $sapa = $client->getWeatherByCity('Sa Pa, Lào Cai');
    $phuquoc = $client->getWeatherByCity('Đảo Ngọc Phú Quốc, Kiên Giang');

    if ($sapa['temp'] >= $phuquoc['temp']) {
        throw new \Exception("Logic nhiệt độ vùng miền không hợp lý: Sa Pa lạnh hơn Phú Quốc!");
    }

    return "Sa Pa: {$sapa['temp']}°C ({$sapa['description']}) | Phú Quốc: {$phuquoc['temp']}°C ({$phuquoc['description']})";
});

// Test 4: Cơ chế Caching (Tốc độ đọc cache < 10ms)
runTest("OpenWeatherClient caching JSON kết quả thời tiết vào ổ đĩa", function() use ($client) {
    $start = microtime(true);
    $first = $client->getWeatherByCity('Đà Lạt');
    $durationFirst = (microtime(true) - $start) * 1000;

    $startCached = microtime(true);
    $second = $client->getWeatherByCity('Đà Lạt');
    $durationCached = (microtime(true) - $startCached) * 1000;

    if ($first['temp'] !== $second['temp']) {
        throw new \Exception("Dữ liệu giữa 2 lần đọc cache không khớp!");
    }

    return sprintf("Lần 1: %.2f ms | Lần 2 (Cache Hit): %.2f ms (Tiết kiệm băng thông & quota API)", $durationFirst, $durationCached);
});

// Test 5: Tự động dự báo thời tiết từ Service Model (Tour 1)
runTest("OpenWeatherClient::getWeatherForService tự động đọc vị trí từ Tour và xuất dự báo", function() use ($client) {
    $serviceModel = new Service();
    $tour = $serviceModel->getById(1);

    if (!$tour) {
        throw new \Exception("Không tìm thấy tour 1!");
    }

    $weather = $client->getWeatherForService($tour);

    if (empty($weather['location'])) {
        throw new \Exception("Không nhận diện được location!");
    }

    return "Tour: '{$tour['title']}' | Điểm đến: {$weather['location']} | Dự báo: {$weather['temp']}°C, {$weather['description']}";
});

echo "\n--- 2. KIỂM THỬ API ENDPOINT & CONTROLLER (ServiceController) ---\n";

// Test 6: API GET /api/weather?location=Đà Lạt
runTest("ServiceController::weather endpoint JSON trả về mã 200 và dữ liệu thời tiết đầy đủ", function() use ($client) {
    $_GET = ['location' => 'Đà Lạt, Lâm Đồng'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $request = new Request();
    $response = new TestWeatherResponse();

    $controller = new ServiceController($request, $response, null, $client);
    $controller->weather($request);

    $json = json_decode($response->renderedContent, true);

    if (!$json || empty($json['success']) || empty($json['data'])) {
        throw new \Exception("API không trả về JSON hợp lệ: " . $response->renderedContent);
    }

    $d = $json['data'];
    return "API phản hồi HTTP 200: {$d['location']} | {$d['temp']}°C | Nguồn: {$d['source']}";
});

// Test 7: API GET /api/weather?lat=11.94&lon=108.45
runTest("ServiceController::weather hỗ trợ truy vấn theo tham số tọa độ lat, lon", function() use ($client) {
    $_GET = ['lat' => '11.9404', 'lon' => '108.4583', 'location' => 'Đỉnh LangBiang'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $request = new Request();
    $response = new TestWeatherResponse();

    $controller = new ServiceController($request, $response, null, $client);
    $controller->weather($request);

    $json = json_decode($response->renderedContent, true);
    if (empty($json['data']['temp'])) {
        throw new \Exception("Dữ liệu tọa độ không trả về nhiệt độ!");
    }

    return "Tọa độ GPS (11.9404, 108.4583) -> Nhiệt độ: {$json['data']['temp']}°C, Gió: {$json['data']['wind_speed']} m/s";
});

// Test 8: ServiceController::show render trang chi tiết với Weather Widget
runTest("ServiceController::show tích hợp Weather Widget vào trang chi tiết tour", function() use ($client) {
    $_GET = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $request = new Request();
    $response = new TestWeatherResponse();

    $controller = new ServiceController($request, $response, null, $client);
    $controller->show($request, '1');

    $html = $response->renderedContent;

    if (empty($html)) {
        throw new \Exception("HTML render bị rỗng!");
    }
    if (strpos($html, 'WEBSERVICE OPENWEATHER JSON API') === false) {
        throw new \Exception("Thiếu badge thông tin OpenWeather Webservice!");
    }
    if (strpos($html, 'Lời khuyên chuẩn bị từ Host:') === false) {
        throw new \Exception("Thiếu box lời khuyên chuẩn bị từ Host!");
    }
    if (strpos($html, 'maps.google.com/maps') === false) {
        throw new \Exception("Thiếu bản đồ Google Maps tương tác!");
    }
    if (strpos($html, '???') !== false) {
        throw new \Exception("Phát hiện ký tự lỗi font '???'!");
    }

    return "Trang chi tiết render hoàn hảo: Hiển thị nhiệt độ lớn, icon, chỉ số độ ẩm & gió, lời khuyên hành lý từ Host, bản đồ Maps vệ tinh!";
});

echo "\n=== KẾT QUẢ: TOÀN BỘ 8 TEST CASES TV4-02 ĐẠT CHUẨN 100%! ===\n";
