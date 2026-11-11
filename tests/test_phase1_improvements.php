<?php

declare(strict_types=1);

/**
 * ============================================================================
 * KIỂM THỬ GIAI ĐOẠN 1: MINH BẠCH CHI PHÍ (VAT 8%), NÚT ĐIỀU HƯỚNG & HOST BADGES
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../app/helpers/FormatHelper.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';

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

echo "\n" . str_repeat('=', 80) . "\n";
echo "   CHẠY KIỂM THỬ GIAI ĐOẠN 1: VAT 8%, NÚT ĐIỀU HƯỚNG & TRẠNG THÁI CỌC HOST   \n";
echo str_repeat('=', 80) . "\n\n";

// ==============================================================================
// NHÓM 1: HÀM TÍNH TOÁN THUẾ GTGT (FormatHelper::calculateVat)
// ==============================================================================
echo "--- NHÓM 1: CÔNG THỨC BÓC TÁCH THUẾ GTGT 8% (FormatHelper) ---\n";

$vatSample1 = FormatHelper::calculateVat(1080000, 0.08);
assertTest("VAT 8% của 1.080.000đ: Net = 1.000.000đ", (int)$vatSample1['net'] === 1000000);
assertTest("VAT 8% của 1.080.000đ: Thuế = 80.000đ", (int)$vatSample1['vat'] === 80000);
assertTest("VAT 8% của 1.080.000đ: Gross = 1.080.000đ", (int)$vatSample1['gross'] === 1080000);
assertTest("VAT 8% của 1.080.000đ: Tỷ lệ thuế = 8%", $vatSample1['rate_percent'] === 8);

$vatSample2 = FormatHelper::calculateVat(1275000, 0.08);
assertTest("VAT 8% của 1.275.000đ: Net + VAT = Tổng tiền", ((int)$vatSample2['net'] + (int)$vatSample2['vat']) === 1275000);

// ==============================================================================
// NHÓM 2: GIAO DIỆN BIÊN NHẬN ĐIỆN TỬ (receipts/show.php)
// ==============================================================================
echo "\n--- NHÓM 2: GIAO DIỆN BIÊN NHẬN ĐIỆN TỬ (receipts/show.php) ---\n";

$mockReceipt = [
    'id' => 99,
    'receipt_code' => 'RC2611100001',
    'created_at' => date('Y-m-d H:i:s'),
    'issued_at' => date('Y-m-d H:i:s'),
    'booking_id' => 88,
    'booking_code' => 'BK2611108888',
    'tourist_name' => 'Lê Du Khách',
    'tourist_email' => 'tourist@gmail.com',
    'tourist_phone' => '0934567890',
    'host_name' => 'Nguyễn Văn Bản Địa',
    'host_phone' => '0912345678',
    'service_title' => 'Săn Mây Đỉnh LangBiang',
    'service_location' => 'Đà Lạt, Lâm Đồng',
    'starts_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
    'meeting_point' => 'Cổng Hồ Tuyền Lâm',
    'payment_plan' => 'deposit_50',
    'participants_count' => 2,
    'unit_price' => 750000,
    'discount_amount' => 225000,
    'total_price' => 1275000,
    'receipt_amount' => 637500,
    'remaining_amount' => 637500,
    'amount' => 637500,
    'order_id' => 'MOMO_BK000088_123456',
    'momo_trans_id' => 'MOMO_TRANS_123456',
    'homestay_name' => 'Homestay Gió Núi',
    'homestay_address' => 'Chân đồi LangBiang',
    'room_type' => 'Phòng Gỗ Áp Mái',
    'nights_count' => 1,
];

ob_start();
$receipt = $mockReceipt;
include __DIR__ . '/../app/views/receipts/show.php';
$receiptHtml = ob_get_clean();

assertTest("Biên nhận điện tử hiển thị dòng 'Tiền dịch vụ trước thuế (Net)'", 
    str_contains($receiptHtml, 'Tiền dịch vụ trước thuế (Net)'));
assertTest("Biên nhận điện tử hiển thị dòng 'Thuế GTGT (VAT 8%)'", 
    str_contains($receiptHtml, 'Thuế GTGT (VAT 8%)'));
assertTest("Biên nhận điện tử ghi rõ 'Đã bao gồm 8% thuế GTGT'", 
    str_contains($receiptHtml, 'Đã bao gồm 8% thuế GTGT'));
assertTest("Biên nhận điện tử có nút 'Trang Chủ'", 
    str_contains($receiptHtml, 'Trang Chủ') && str_contains($receiptHtml, 'href="/"'));

// ==============================================================================
// NHÓM 3: GIAO DIỆN HOÀN TẤT THANH TOÁN (payments/callback.php)
// ==============================================================================
echo "\n--- NHÓM 3: GIAO DIỆN HOÀN TẤT THANH TOÁN (payments/callback.php) ---\n";

ob_start();
$orderId = 'MOMO_BK000088_123456';
$resultCode = '0';
$amount = 637500;
$transId = 'MOMO_TRANS_123456';
$message = 'Thành công.';
$payment = ['id' => 1, 'booking_id' => 88, 'status' => 'succeeded'];
$receipt = ['id' => 99, 'receipt_code' => 'RC2611100001'];
$user = ['id' => 4, 'name' => 'Lê Du Khách'];
include __DIR__ . '/../app/views/payments/callback.php';
$callbackHtml = ob_get_clean();

assertTest("Callback hiển thị nút 'Xem Chi Tiết Vé'", 
    str_contains($callbackHtml, 'Xem Chi Tiết Vé'));
assertTest("Callback hiển thị nút 'Xem Biên Nhận Điện Tử'", 
    str_contains($callbackHtml, 'Xem Biên Nhận Điện Tử'));
assertTest("Callback có nút 'Về Trang Chủ'", 
    str_contains($callbackHtml, 'Về Trang Chủ') && str_contains($callbackHtml, 'href="/"'));
assertTest("Hai nút chính được bố cục căn đều trong grid row", 
    str_contains($callbackHtml, 'row g-2 justify-content-center'));

// ==============================================================================
// NHÓM 4: CHI TIẾT ĐƠN ĐẶT DU KHÁCH (bookings/detail.php) & CHI TIẾT TOUR
// ==============================================================================
echo "\n--- NHÓM 4: CHI TIẾT ĐƠN HÀNG (bookings/detail.php) & TOUR (services/show.php) ---\n";

ob_start();
$booking = [
    'id' => 88,
    'booking_code' => 'BK2611108888',
    'status' => 'confirmed',
    'created_at' => date('Y-m-d H:i:s'),
    'unit_price' => 750000,
    'participants_count' => 2,
    'discount_amount' => 225000,
    'total_price' => 1275000,
    'deposit_amount' => 637500,
    'remaining_amount' => 637500,
    'payment_plan' => 'deposit_50',
    'service_id' => 1,
    'service_title' => 'Săn Mây Đỉnh LangBiang',
    'service_location' => 'Đà Lạt',
    'duration_days' => 1,
    'price_mode' => 'fixed_choice',
    'starts_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
    'ends_at' => date('Y-m-d H:i:s', strtotime('+3 days')),
    'meeting_point' => 'Cổng Hồ Tuyền Lâm',
    'host_id' => 2,
    'host_name' => 'Nguyễn Văn Bản Địa',
    'host_email' => 'host@dalat.vn',
    'host_phone' => '0912345678',
    'tourist_id' => 4,
    'tourist_name' => 'Lê Du Khách',
    'tourist_email' => 'tourist@gmail.com',
    'tourist_phone' => '0934567890',
    'payment_expires_at' => null,
];
$payments = [];
$isHost = false;
$isAdmin = false;
include __DIR__ . '/../app/views/bookings/detail.php';
$bookingDetailHtml = ob_get_clean();

assertTest("Chi tiết đơn đặt hiển thị 'Đã bao gồm 8% thuế GTGT'", 
    str_contains($bookingDetailHtml, 'Đã bao gồm 8% thuế GTGT'));
assertTest("Chi tiết đơn đặt bóc tách dòng 'Tiền dịch vụ trước thuế (Net)'", 
    str_contains($bookingDetailHtml, 'Tiền dịch vụ trước thuế (Net)'));
assertTest("Chi tiết đơn đặt bóc tách dòng 'Thuế GTGT (VAT 8%)'", 
    str_contains($bookingDetailHtml, 'Thuế GTGT (VAT 8%)'));

ob_start();
$service = [
    'id' => 1,
    'title' => 'Bình Minh Bên Hồ Tuyền Lâm',
    'base_price' => 450000,
    'price_mode' => 'per_person',
    'duration_days' => 1,
    'location' => 'Đà Lạt',
];
$stops = [];
$weather = [];
$discussions = [];
include __DIR__ . '/../app/views/services/show.php';
$serviceShowHtml = ob_get_clean();

assertTest("Khối đặt chỗ trang tour hiển thị 'Giá đã bao gồm 8% thuế GTGT (VAT)'", 
    str_contains($serviceShowHtml, 'Giá đã bao gồm 8% thuế GTGT (VAT)'));

// ==============================================================================
// NHÓM 5: TRẠNG THÁI THANH TOÁN PHÍA HOST (host/bookings/index.php & show.php)
// ==============================================================================
echo "\n--- NHÓM 5: TÌNH TRẠNG CỌC PHÍA HOST (host/bookings/index.php & show.php) ---\n";

ob_start();
$bookings = [
    [
        'id' => 88,
        'booking_code' => 'BK2611108888',
        'status' => 'confirmed',
        'payment_plan' => 'deposit_50',
        'total_price' => 1275000,
        'deposit_amount' => 637500,
        'created_at' => date('Y-m-d H:i:s'),
        'starts_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
        'participants_count' => 2,
        'tourist_name' => 'Lê Du Khách',
        'tourist_phone' => '0934567890',
        'tourist_avatar' => null,
        'service_title' => 'Săn Mây Đỉnh LangBiang',
        'service_location' => 'Đà Lạt',
        'payment_expires_at' => null,
    ]
];
$stats = ['total' => 1, 'pending' => 0, 'awaiting_payment' => 0, 'confirmed' => 1, 'completed' => 0, 'cancelled' => 0, 'revenue' => 637500];
$services = [];
$filters = ['status' => null, 'service_id' => null, 'search' => null];
include __DIR__ . '/../app/views/host/bookings/index.php';
$hostIndexHtml = ob_get_clean();

assertTest("Danh sách đơn của Host hiển thị huy hiệu 'Đã cọc 50%'", 
    str_contains($hostIndexHtml, 'Đã cọc 50%'));
assertTest("Danh sách đơn của Host hiển thị số tiền 'Thu thêm' tại tour", 
    str_contains($hostIndexHtml, 'Thu thêm: 637.500 ₫'));

ob_start();
$booking = $bookings[0];
$booking['unit_price'] = 750000;
$booking['discount_amount'] = 225000;
$booking['remaining_amount'] = 637500;
$booking['ends_at'] = date('Y-m-d H:i:s', strtotime('+3 days'));
$booking['meeting_point'] = 'Cổng Hồ Tuyền Lâm';
$booking['tourist_email'] = 'tourist@gmail.com';
include __DIR__ . '/../app/views/host/bookings/show.php';
$hostShowHtml = ob_get_clean();

assertTest("Chi tiết đơn của Host hiển thị khối trạng thái 'Đã cọc 50% qua MoMo'", 
    str_contains($hostShowHtml, 'Đã cọc 50% qua MoMo'));
assertTest("Chi tiết đơn của Host hiển thị 'Host thu trực tiếp khi đón khách'", 
    str_contains($hostShowHtml, 'Host thu trực tiếp khi đón khách'));
assertTest("Chi tiết đơn của Host bóc tách Thuế GTGT 8%", 
    str_contains($hostShowHtml, 'Thuế GTGT (VAT 8%)') && str_contains($hostShowHtml, 'Đã gồm 8% thuế GTGT'));

// ==============================================================================
// TỔNG KẾT
// ==============================================================================
echo "\n" . str_repeat('=', 80) . "\n";
echo "KẾT QUẢ KIỂM THỬ GIAI ĐOẠN 1: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ GIAI ĐOẠN 1 ĐẠT CHUẨN 100%! <<<\n";
} else {
    echo ">>> CÓ BÀI KIỂM THỬ THẤT BẠI! <<<\n";
}
echo str_repeat('=', 80) . "\n\n";

exit($passedTests === $totalTests ? 0 : 1);
