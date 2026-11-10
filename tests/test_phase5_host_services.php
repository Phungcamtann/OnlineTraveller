<?php

declare(strict_types=1);

/**
 * ============================================================================
 * KIỂM THỬ GIAI ĐOẠN 5: CẢI TIẾN FORM ĐĂNG TOUR CHO HOST (GEOCODING & UPLOAD ẢNH)
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../app/helpers/FormatHelper.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/models/Service.php';
require_once __DIR__ . '/../app/models/ServiceSchedule.php';
require_once __DIR__ . '/../app/services/TourService.php';
require_once __DIR__ . '/../app/controllers/HostController.php';

use App\Helpers\FormatHelper;
use App\Helpers\SecurityHelper;
use App\Models\Service;
use App\Services\TourService;
use App\Controllers\HostController;
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
echo "   CHẠY KIỂM THỬ GIAI ĐOẠN 5: GEOCODING TỰ ĐỘNG & UPLOAD ẢNH TRẠM TOUR CHO HOST   \n";
echo str_repeat('=', 84) . "\n\n";

$db = Core\Database::getConnection();
$tourService = new TourService();
$serviceModel = new Service();
$createdTourIds = [];
$mockFilePaths = [];

// ==============================================================================
// NHÓM 1: TỪ ĐIỂN TỌA ĐỘ VÀ GEOCODING TỰ ĐỘNG (FormatHelper)
// ==============================================================================
echo "--- NHÓM 1: TỪ ĐIỂN TỌA ĐỘ VÀ GEOCODING TỰ ĐỘNG (FormatHelper) ---\n";

$provinces = FormatHelper::getProvincesList();
assertTest("Từ điển Tỉnh/Thành phố du lịch có ít nhất 25 địa bàn trọng điểm", count($provinces) >= 25);
assertTest("Từ điển có đầy đủ thông tin Tỉnh Lâm Đồng (Đà Lạt)", 
    isset($provinces['Lâm Đồng']) && abs($provinces['Lâm Đồng']['lat'] - 11.9404) < 0.01);
assertTest("Từ điển có đầy đủ thông tin Tỉnh Lào Cai (Sa Pa)", 
    isset($provinces['Lào Cai']) && abs($provinces['Lào Cai']['lat'] - 22.3364) < 0.01);

$geoCauDat = FormatHelper::lookupCoordinates("Hái chè sớm tại Đồi Chè Cầu Đất, Lâm Đồng");
assertTest("Geocoding nhận diện chính xác điểm Cầu Đất (Lat: ~11.8596, Lng: ~108.5776)", 
    abs($geoCauDat['latitude'] - 11.8596) < 0.01 && abs($geoCauDat['longitude'] - 108.5776) < 0.01);

$geoFansipan = FormatHelper::lookupCoordinates("Chinh phục Nóc nhà Đông Dương Fansipan");
assertTest("Geocoding nhận diện chính xác điểm Fansipan (Lat: ~22.3033, Lng: ~103.7753)", 
    abs($geoFansipan['latitude'] - 22.3033) < 0.01 && abs($geoFansipan['longitude'] - 103.7753) < 0.01);

$geoHaGiang = FormatHelper::lookupCoordinates("Phượt Cột Cờ Lũng Cú, Tỉnh Hà Giang");
assertTest("Geocoding nhận diện chính xác Tỉnh Hà Giang", 
    abs($geoHaGiang['latitude'] - 22.8233) < 0.01 || abs($geoHaGiang['latitude'] - 23.2778) < 0.5);

// ==============================================================================
// NHÓM 2: GIAO DIỆN TẠO MỚI TOUR HOST (app/views/host/services/create.php)
// ==============================================================================
echo "\n--- NHÓM 2: GIAO DIỆN KHỞI TẠO TOUR (create.php) ---\n";

$categories = $serviceModel->getCategories();
$user = ['id' => 2, 'name' => 'Host Test', 'role' => 'host'];

ob_start();
include __DIR__ . '/../app/views/host/services/create.php';
$createHtml = ob_get_clean();

assertTest("Giao diện có combobox chọn Tỉnh / Thành phố du lịch (id='provinceSelect')", 
    str_contains($createHtml, 'id="provinceSelect"'));
assertTest("Giao diện có ô nhập Địa điểm đón / Điểm hẹn cụ thể (id='locationInput')", 
    str_contains($createHtml, 'id="locationInput"'));
assertTest("Trường Vĩ độ & Kinh độ đã được chuyển thành hidden input an toàn", 
    str_contains($createHtml, '<input type="hidden" name="latitude"') && str_contains($createHtml, '<input type="hidden" name="longitude"'));
assertTest("Giao diện có thẻ hiển thị trạng thái tọa độ tự động (id='geoCoordsDisplay')", 
    str_contains($createHtml, 'id="geoCoordsDisplay"'));
assertTest("Giao diện có ô tải file ảnh điểm dừng chân (stop_files[])", 
    str_contains($createHtml, 'name="stop_files[]"'));
assertTest("Giao diện hiển thị ghi chú giá đã bao gồm 8% thuế GTGT", 
    str_contains($createHtml, 'Giá niêm yết đã bao gồm 8% thuế GTGT'));
assertTest("Đã loại bỏ hoàn toàn cụm từ kỹ thuật '(Three.js)' trong tiêu đề mục 4", 
    !str_contains($createHtml, '(Three.js)') && str_contains($createHtml, '4. Lịch Trình & Điểm Dừng Chân Không Gian (3D Tour)'));

// ==============================================================================
// NHÓM 3: GIAO DIỆN CHỈNH SỬA TOUR HOST (app/views/host/services/edit.php)
// ==============================================================================
echo "\n--- NHÓM 3: GIAO DIỆN CHỈNH SỬA TOUR (edit.php) ---\n";

$mockTour = [
    'id' => 9999,
    'title' => 'Chèo Thuyền Khám Phá Rừng Ngập Mặn Cần Giờ',
    'slug' => 'cheo-thuyen-can-gio',
    'status' => 'approved',
    'category_id' => $categories[0]['id'] ?? 1,
    'duration_days' => 1,
    'price_mode' => 'fixed_choice',
    'base_price' => 600000,
    'min_price' => 0,
    'max_price' => 0,
    'location' => 'Khu du lịch Vàm Sát, Cần Giờ, TP. Hồ Chí Minh',
    'latitude' => 10.8231,
    'longitude' => 106.6297,
    'description' => 'Mô tả chuyến đi sinh thái tuyệt vời...',
    'banner_image' => '/assets/images/tours/sample.jpg',
    'accommodation' => null,
    'stops' => [
        [
            'id' => 1,
            'image_url' => '/assets/images/tours/stops/sample1.jpg',
            'caption' => 'Rừng Đước Xanh Mát',
            'story_text' => 'Hệ sinh thái dự trữ sinh quyển thế giới',
            'video_url' => null,
            'sort_order' => 1,
        ]
    ]
];

$tour = $mockTour;
ob_start();
include __DIR__ . '/../app/views/host/services/edit.php';
$editHtml = ob_get_clean();

assertTest("Giao diện Edit có combobox Tỉnh/Thành phố tự nhận diện đúng TP. Hồ Chí Minh", 
    str_contains($editHtml, 'value="TP. Hồ Chí Minh"') && str_contains($editHtml, 'selected'));
assertTest("Giao diện Edit có hidden inputs mang giá trị tọa độ hiện tại của tour", 
    str_contains($editHtml, 'value="10.8231"') && str_contains($editHtml, 'value="106.6297"'));
assertTest("Giao diện Edit hỗ trợ tải file ảnh thay thế cho trạm dừng chân (stop_files[])", 
    str_contains($editHtml, 'Thay ảnh mới từ máy tính'));
assertTest("Đã loại bỏ cụm từ '(Three.js)' trong tiêu đề mục 4 của trang Edit", 
    !str_contains($editHtml, '(Three.js)'));

// ==============================================================================
// NHÓM 4: XỬ LÝ UPLOAD ẢNH ĐIỂM DỪNG (TourService::handleStopImageUpload)
// ==============================================================================
echo "\n--- NHÓM 4: XỬ LÝ TẢI TỆP ẢNH ĐIỂM DỪNG CHÂN (TourService) ---\n";

// Create a dummy image file in temp directory for testing
$tempImgPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_stop_' . uniqid() . '.jpg';
// A minimal valid 1x1 JPEG image payload
$minimalJpg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
file_put_contents($tempImgPath, $minimalJpg);
$mockFilePaths[] = $tempImgPath;

$mockUpload = [
    'name' => 'stop_test.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $tempImgPath,
    'error' => UPLOAD_ERR_OK,
    'size' => strlen($minimalJpg),
];

$uploadedUrl = $tourService->handleStopImageUpload($mockUpload);
assertTest("Upload ảnh điểm dừng chân trả về đường dẫn hợp lệ trong /assets/images/tours/stops/", 
    str_starts_with($uploadedUrl, '/assets/images/tours/stops/'));

$savedLocalFile = dirname(__DIR__) . '/public' . $uploadedUrl;
assertTest("Tệp ảnh điểm dừng chân được lưu thực tế trên đĩa cứng", file_exists($savedLocalFile));
if (file_exists($savedLocalFile)) {
    $mockFilePaths[] = $savedLocalFile;
}

// ==============================================================================
// NHÓM 5: TỰ ĐỘNG GEOCODING KHI HOST TẠO TOUR QUA CONTROLLER (HostController)
// ==============================================================================
echo "\n--- NHÓM 5: TỰ ĐỘNG TÍNH TOÁN TỌA ĐỘ KHI SUBMIT TOUR (HostController) ---\n";

$hostUser = $db->query("SELECT id, name, email FROM tbl_users WHERE role = 'host' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$hostId = (int)$hostUser['id'];

$hostCtrl = new HostController(new Request());
$_SESSION['user'] = $hostUser;
$_SESSION['user_id'] = $hostId;
$_SESSION['role'] = 'host';
$_SESSION['user_name'] = $hostUser['name'];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    '_csrf_token' => SecurityHelper::csrfToken(),
    'csrf_token' => SecurityHelper::csrfToken(),
    'title' => 'Tour Kiểm Thử Tự Động Geocoding ' . time(),
    'category_id' => $categories[0]['id'] ?? 1,
    'duration_days' => 2,
    'province' => 'Lâm Đồng',
    'location' => 'Bến Thuyền Hồ Tuyền Lâm', // Does NOT provide lat/lng
    'latitude' => '',                       // Empty on purpose to test auto-geocoding!
    'longitude' => '',                      // Empty on purpose!
    'description' => 'Tour trải nghiệm cắm trại và ngắm hoàng hôn Tuyền Lâm thơ mộng.',
    'price_mode' => 'fixed_choice',
    'base_price' => 850000,
    'stop_image_urls' => [$uploadedUrl],
    'stop_captions' => ['Hoàng Hôn Tuyền Lâm'],
    'stop_stories' => ['Không gian bình yên bên hồ nước trong xanh.'],
];

// Execute storeService
ob_start();
$hostCtrl->storeService(new Request());
ob_end_clean();

// Check if tour was created and auto-geocoded
$createdTour = $db->query("
    SELECT id, title, location, latitude, longitude, status 
    FROM tbl_services 
    WHERE host_id = {$hostId} 
    ORDER BY id DESC 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

assertTest("Tour mới được tạo thành công trong cơ sở dữ liệu", !empty($createdTour['id']));
$createdTourIds[] = (int)$createdTour['id'];

assertTest("Hệ thống tự động nối Tỉnh/Thành vào địa chỉ ('Bến Thuyền Hồ Tuyền Lâm, Lâm Đồng')", 
    str_contains($createdTour['location'], 'Lâm Đồng'));
assertTest("Hệ thống tự động tính toán Vĩ độ (Latitude ~11.8906 hoặc 11.9404) dù Host để trống", 
    !empty($createdTour['latitude']) && abs((float)$createdTour['latitude'] - 11.8906) < 0.1);
assertTest("Hệ thống tự động tính toán Kinh độ (Longitude ~108.4287 hoặc 108.4583) dù Host để trống", 
    !empty($createdTour['longitude']) && abs((float)$createdTour['longitude'] - 108.4287) < 0.1);

// Verify stop was registered
$stopRecord = $db->query("
    SELECT id, service_id, image_url, caption 
    FROM tbl_service_images 
    WHERE service_id = {$createdTour['id']} 
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

assertTest("Điểm dừng chân (stop) được lưu thành công với ảnh đã tải lên", 
    !empty($stopRecord['id']) && $stopRecord['image_url'] === $uploadedUrl);

// Clean up created records & mock files
if (!empty($createdTourIds)) {
    $idsStr = implode(',', $createdTourIds);
    $db->exec("DELETE FROM tbl_service_images WHERE service_id IN ({$idsStr})");
    $db->exec("DELETE FROM tbl_services WHERE id IN ({$idsStr})");
}
foreach ($mockFilePaths as $filePath) {
    if (file_exists($filePath)) {
        @unlink($filePath);
    }
}
echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi và tệp kiểm thử Giai đoạn 5.\n";

// ==============================================================================
// TỔNG KẾT
// ==============================================================================
echo "\n" . str_repeat('=', 84) . "\n";
echo "KẾT QUẢ KIỂM THỬ GIAI ĐOẠN 5: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ GIAI ĐOẠN 5 ĐẠT CHUẨN 100%! <<<\n";
} else {
    echo ">>> CÓ BÀI KIỂM THỬ THẤT BẠI! <<<\n";
}
echo str_repeat('=', 84) . "\n\n";

exit($passedTests === $totalTests ? 0 : 1);
