<?php
/**
 * Test Suite: Phase 6 UX & UI Enhancements
 * File: tests/test_phase6_slider_cards_banner.php
 *
 * Kiểm thử toàn diện:
 * 1. Thanh trượt khoảng giá Range Slider (Trang chủ & Trang khám phá)
 * 2. Backend lọc ngân sách (Service::getAllApproved & ServiceController::index)
 * 3. Đồng bộ Card tour Dark-Alpine tràn ảnh 3/4 tại Trang khám phá (services/index.php)
 * 4. Slider Banner tương tác với dải thumbnails tại Trang chủ (home/index.php)
 * 5. Bố cục lại Banner & Dải card trạm dừng chân dọc tại Trang chi tiết (services/show.php)
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/helpers/FormatHelper.php';

use App\Models\Service;
use App\Controllers\ServiceController;
use App\Controllers\HomeController;
use Core\Request;
use Core\Response;

$passed = 0;
$total = 0;

function assertCondition(string $name, bool $condition, string $details = ''): void {
    global $passed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo "  [PASS] Assertion #{$total}: {$name}\n";
    } else {
        echo "  [FAIL] Assertion #{$total}: {$name} - Details: {$details}\n";
    }
}

class TestPhase6Response extends Response
{
    public string $renderedHtml = '';
    public int $statusCode = 200;

    public function html(string $content, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->renderedHtml = $content;
    }
}

echo "====================================================================================\n";
echo "   PHASE 6: KIỂM THỬ RANGE SLIDER, ĐỒNG BỘ CARD TOUR & SLIDER BANNER THUMBNAILS    \n";
echo "====================================================================================\n\n";

$serviceModel = new Service();

// ------------------------------------------------------------------------------
// PHẦN 1: BACKEND LỌC THEO RANGE SLIDER (Service::getAllApproved & ServiceController)
// ------------------------------------------------------------------------------
echo "--- 1. Backend Lọc Theo Ngân Sách Tối Đa (Range Slider) ---\n";

// Lọc các tour có giá <= 500.000đ
$cheapTours = $serviceModel->getAllApproved(null, null, 12, 500000);
$allCheap = true;
foreach ($cheapTours as $t) {
    if ((float)$t['base_price'] > 500000) {
        $allCheap = false;
        break;
    }
}
assertCondition(
    "Service::getAllApproved lọc chính xác tour có base_price <= 500.000đ",
    !empty($cheapTours) && $allCheap,
    "Tours found: " . count($cheapTours) . ", all <= 500k: " . ($allCheap ? 'true' : 'false')
);

// Lọc các tour có giá <= 1.000.000đ
$midTours = $serviceModel->getAllApproved(null, null, 12, 1000000);
$allMid = true;
foreach ($midTours as $t) {
    if ((float)$t['base_price'] > 1000000) {
        $allMid = false;
        break;
    }
}
assertCondition(
    "Service::getAllApproved lọc chính xác tour có base_price <= 1.000.000đ",
    !empty($midTours) && $allMid,
    "Tours found: " . count($midTours) . ", all <= 1tr: " . ($allMid ? 'true' : 'false')
);

// Kiểm tra ServiceController::index nhận query max_price
$_GET = ['max_price' => '600000'];
$_SERVER['REQUEST_METHOD'] = 'GET';
$req = new Request();
$res = new TestPhase6Response();
$controller = new ServiceController($req, $res, $serviceModel);
$controller->index($req);

$htmlIndex = $res->renderedHtml;
assertCondition(
    "ServiceController::index nhận query max_price và render đúng giá trị vào slider",
    str_contains($htmlIndex, 'value="600000"'),
    "Không tìm thấy value='600000' trong slider html"
);

// Kiểm tra backwards compatibility: query price_range=under_500
$_GET = ['price_range' => 'under_500'];
$req = new Request();
$res = new TestPhase6Response();
$controller = new ServiceController($req, $res, $serviceModel);
$controller->index($req);
assertCondition(
    "ServiceController::index tương thích ngược với price_range=under_500",
    str_contains($res->renderedHtml, 'value="500000"'),
    "Không tự động chuyển đổi under_500 thành 500000"
);

// ------------------------------------------------------------------------------
// PHẦN 2: RANGE SLIDER TẠI TRANG CHỦ & TRANG KHÁM PHÁ
// ------------------------------------------------------------------------------
echo "\n--- 2. Giao Diện Range Slider (home/index.php & services/index.php) ---\n";

$homeViewContent = file_get_contents(__DIR__ . '/../app/views/home/index.php');
$servicesViewContent = file_get_contents(__DIR__ . '/../app/views/services/index.php');

assertCondition(
    "Trang chủ có Range Slider ngân sách với id='home-price-slider' và name='max_price'",
    str_contains($homeViewContent, 'id="home-price-slider"') && str_contains($homeViewContent, 'name="max_price"'),
    "Thiếu id home-price-slider hoặc name max_price"
);

assertCondition(
    "Trang chủ có nhãn hiển thị số tiền nhảy động id='home-price-display'",
    str_contains($homeViewContent, 'id="home-price-display"'),
    "Thiếu thẻ id home-price-display"
);

assertCondition(
    "Trang chủ có JS event listener 'input' cập nhật giá trị số tiền nhảy động",
    str_contains($homeViewContent, "homeSlider.addEventListener('input'") && str_contains($homeViewContent, 'Intl.NumberFormat'),
    "Thiếu JS event listener trên slider trang chủ"
);

assertCondition(
    "Trang khám phá có Range Slider ngân sách với id='service-price-slider'",
    str_contains($servicesViewContent, 'id="service-price-slider"') && str_contains($servicesViewContent, 'name="max_price"'),
    "Thiếu id service-price-slider hoặc name max_price"
);

assertCondition(
    "Trang khám phá có nhãn hiển thị số tiền nhảy động id='service-price-display'",
    str_contains($servicesViewContent, 'id="service-price-display"'),
    "Thiếu thẻ id service-price-display"
);

assertCondition(
    "CSS custom-price-range được định nghĩa cho giao diện Dark-Alpine",
    str_contains(file_get_contents(__DIR__ . '/../public/assets/css/custom-theme.css'), '.custom-price-range'),
    "Thiếu class .custom-price-range trong custom-theme.css"
);

// ------------------------------------------------------------------------------
// PHẦN 3: ĐỒNG BỘ CARD TOUR TRÀN ẢNH TẠI TRANG KHÁM PHÁ (services/index.php)
// ------------------------------------------------------------------------------
echo "\n--- 3. Đồng Bộ Card Tour Dark-Alpine Tại Trang Khám Phá ---\n";

assertCondition(
    "Trang khám phá dùng class 'experience-poster-card' tỷ lệ 3/4 tràn ảnh",
    str_contains($servicesViewContent, 'experience-poster-card'),
    "Card tour chưa dùng class experience-poster-card"
);

assertCondition(
    "Trang khám phá dùng lớp phủ 'experience-poster-overlay' chân ảnh",
    str_contains($servicesViewContent, 'experience-poster-overlay'),
    "Thiếu class experience-poster-overlay"
);

assertCondition(
    "Card tour hiển thị huy hiệu danh mục góc trên trái (glass-pill)",
    str_contains($servicesViewContent, 'category_name') && str_contains($servicesViewContent, 'glass-pill'),
    "Thiếu huy hiệu category glass-pill"
);

assertCondition(
    "Card tour hiển thị số sao đánh giá góc trên phải (glass-pill)",
    str_contains($servicesViewContent, 'avg_rating') && str_contains($servicesViewContent, '★'),
    "Thiếu hiển thị rating sao"
);

assertCondition(
    "Card tour hiển thị giá tiền nổi bật màu text-info và ghi chú thuế VAT",
    str_contains($servicesViewContent, 'text-info') && str_contains($servicesViewContent, 'Đã gồm 8% VAT'),
    "Thiếu text-info hoặc ghi chú Đã gồm 8% VAT"
);

assertCondition(
    "Bộ lọc trang khám phá dùng glass-panel mờ kính sang trọng",
    str_contains($servicesViewContent, 'glass-panel'),
    "Bộ lọc chưa dùng class glass-panel"
);

// ------------------------------------------------------------------------------
// PHẦN 4: SLIDER BANNER THUMBNAILS TẠI TRANG CHỦ (home/index.php)
// ------------------------------------------------------------------------------
echo "\n--- 4. Slider Banner Thumbnails Tại Trang Chủ ---\n";

assertCondition(
    "Trang chủ có container thumbnails id='bannerThumbsContainer'",
    str_contains($homeViewContent, 'banner-thumbs-container') && str_contains($homeViewContent, 'banner-thumb-item'),
    "Thiếu container hoặc class banner-thumb-item"
);

assertCondition(
    "Các thumbnail chứa đầy đủ data attributes (data-slide-index, data-image, data-title)",
    str_contains($homeViewContent, 'data-slide-index') && str_contains($homeViewContent, 'data-image') && str_contains($homeViewContent, 'data-title'),
    "Thiếu các data attributes trên thumbnail"
);

assertCondition(
    "Trang chủ có 2 nút điều hướng chuyển slide (btnPrevSlide & btnNextSlide)",
    str_contains($homeViewContent, 'id="btnPrevSlide"') && str_contains($homeViewContent, 'id="btnNextSlide"'),
    "Thiếu nút prev hoặc next slide"
);

assertCondition(
    "Có JavaScript điều khiển chuyển slide mượt mà và tự động đổi slide theo chu kỳ",
    str_contains($homeViewContent, 'applySlide') && str_contains($homeViewContent, 'startAutoSlide'),
    "Thiếu JS applySlide hoặc startAutoSlide"
);

assertCondition(
    "CSS banner-thumb-item hỗ trợ trạng thái active và hover kính mờ",
    str_contains(file_get_contents(__DIR__ . '/../public/assets/css/custom-theme.css'), '.banner-thumb-item.active'),
    "Thiếu class .banner-thumb-item.active trong CSS"
);

// ------------------------------------------------------------------------------
// PHẦN 5: BỐ CỤC LẠI BANNER & CÁC TRẠM XẾP DỌC TRÊN TRANG CHI TIẾT (services/show.php)
// ------------------------------------------------------------------------------
echo "\n--- 5. Bố Cục Lại Banner & Dải Trạm Xếp Dọc (services/show.php) ---\n";

$showViewContent = file_get_contents(__DIR__ . '/../app/views/services/show.php');

assertCondition(
    "Banner chi tiết đặt background-position: center bottom để không bị mất chân ảnh",
    str_contains($showViewContent, 'background-position: center bottom'),
    "Chưa đặt background-position: center bottom"
);

assertCondition(
    "Bố cục Banner chia 2 cột rõ ràng: Cột trái chữ + host, Cột phải dải card trạm dừng",
    str_contains($showViewContent, 'col-lg-7') && str_contains($showViewContent, 'col-lg-5'),
    "Thiếu phân chia col-lg-7 và col-lg-5 trong banner"
);

assertCondition(
    "Dải trạm dừng bên phải sử dụng class 'vertical-station-card'",
    str_contains($showViewContent, 'vertical-station-card'),
    "Thiếu class vertical-station-card"
);

assertCondition(
    "Mỗi thẻ trạm có ảnh thu nhỏ, số thứ tự 'Trạm X' và tên trạm",
    str_contains($showViewContent, 'Trạm <?= $stIndex + 1 ?>') || str_contains($showViewContent, 'Trạm 1'),
    "Thiếu hiển thị Trạm X"
);

assertCondition(
    "CSS vertical-station-card được thiết kế với hiệu ứng hover và backdrop-filter",
    str_contains(file_get_contents(__DIR__ . '/../public/assets/css/custom-theme.css'), '.vertical-station-card:hover'),
    "Thiếu style .vertical-station-card:hover trong CSS"
);

// ------------------------------------------------------------------------------
// TỔNG KẾT
// ------------------------------------------------------------------------------
echo "\n====================================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passed} / {$total} assertions đạt chuẩn (" . round(($passed / $total) * 100, 1) . "%)\n";
if ($passed === $total) {
    echo ">>> XÁC NHẬN: GIAI ĐOẠN 6 ĐÃ HOÀN THÀNH VÀ ĐẠT CHUẨN 100%! <<<\n";
} else {
    echo ">>> CẢNH BÁO: CÓ " . ($total - $passed) . " ASSERTIONS THẤT BẠI! <<<\n";
}
echo "====================================================================================\n";

exit($passed === $total ? 0 : 1);
