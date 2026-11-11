<?php
/**
 * Test Suite: Phase 4 UX Improvements
 * File: tests/test_phase4_profile_enhancements.php
 *
 * Kiểm thử toàn diện:
 * 1. Cấu trúc CSDL: Cột gender, birth_date trong tbl_users
 * 2. Stored Procedure: dhs_sp_users_update_profile & dhs_sp_users_find_login
 * 3. Model User & AuthService: updateProfile, getById, getAllUsers
 * 4. Giao diện Hồ sơ (profile/index.php): Combobox Tỉnh/Thành, Giới tính, Ngày sinh, Bio cá nhân hóa, Xóa note kỹ thuật
 * 5. Giao diện In Đơn (@media print trong bookings/detail.php): Ẩn navigation, action buttons, có print header
 * 6. Nút Hiện/Ẩn Mật khẩu (auth/login.php): Nút icon con mắt và JS toggle type password/text
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../app/helpers/SecurityHelper.php';
require_once __DIR__ . '/../app/helpers/FormatHelper.php';

use Core\Database;
use App\Models\User;
use App\Services\AuthService;
use App\Helpers\FormatHelper;

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

echo "====================================================================================\n";
echo "   PHASE 4: KIỂM THỬ HỒ SƠ NGƯỜI DÙNG, BẢN IN ĐƠN VÀ NÚT HIỆN MẬT KHẨU ĐĂNG NHẬP   \n";
echo "====================================================================================\n\n";

$db = Database::getInstance();
$userModel = new User();
$authService = new AuthService($userModel);

// ------------------------------------------------------------------------------
// PHẦN 1: CẤU TRÚC BẢNG tbl_users & STORED PROCEDURES
// ------------------------------------------------------------------------------
echo "--- 1. Cấu trúc CSDL tbl_users & Stored Procedures ---\n";

$columns = $db->fetchAll("DESCRIBE tbl_users");
$colMap = [];
foreach ($columns as $c) {
    $colMap[$c['Field']] = $c;
}

assertCondition(
    "tbl_users có cột 'gender' kiểu ENUM",
    isset($colMap['gender']) && str_contains($colMap['gender']['Type'], "enum('male','female','other')"),
    isset($colMap['gender']) ? $colMap['gender']['Type'] : 'Cột không tồn tại'
);

assertCondition(
    "tbl_users có cột 'birth_date' kiểu DATE",
    isset($colMap['birth_date']) && str_starts_with($colMap['birth_date']['Type'], 'date'),
    isset($colMap['birth_date']) ? $colMap['birth_date']['Type'] : 'Cột không tồn tại'
);

// Tìm 1 tourist hoặc admin để test procedure update profile
$testUser = $db->fetchOne("SELECT * FROM tbl_users WHERE email = 'tourist@gmail.com' LIMIT 1");
if (!$testUser) {
    $testUser = $db->fetchOne("SELECT * FROM tbl_users LIMIT 1");
}

$testUserId = (int)$testUser['id'];

// Test gọi Stored Procedure dhs_sp_users_update_profile trực tiếp
$spResultRows = $db->callProcedure('dhs_sp_users_update_profile', [
    $testUserId,
    'Nguyễn Văn Du Khách',
    '0901234567',
    'Lâm Đồng',
    'Yêu thích trekking Đà Lạt và săn mây Cầu Đất',
    null,
    'male',
    '1995-08-15'
]);
$spResult = $spResultRows[0] ?? null;

assertCondition(
    "dhs_sp_users_update_profile trả về thông tin cập nhật thành công",
    !empty($spResult) && (int)$spResult['id'] === $testUserId,
    json_encode($spResult, JSON_UNESCAPED_UNICODE)
);

assertCondition(
    "dhs_sp_users_update_profile lưu đúng gender = 'male'",
    ($spResult['gender'] ?? '') === 'male',
    "Actual: " . ($spResult['gender'] ?? 'null')
);

assertCondition(
    "dhs_sp_users_update_profile lưu đúng birth_date = '1995-08-15'",
    ($spResult['birth_date'] ?? '') === '1995-08-15',
    "Actual: " . ($spResult['birth_date'] ?? 'null')
);

// Test Stored Procedure dhs_sp_users_find_login
$loginResultRows = $db->callProcedure('dhs_sp_users_find_login', [$testUser['email']]);
$loginResult = $loginResultRows[0] ?? null;
assertCondition(
    "dhs_sp_users_find_login trả về cột gender và birth_date",
    isset($loginResult['gender']) && array_key_exists('birth_date', $loginResult),
    json_encode($loginResult, JSON_UNESCAPED_UNICODE)
);

// ------------------------------------------------------------------------------
// PHẦN 2: USER MODEL & AUTH SERVICE
// ------------------------------------------------------------------------------
echo "\n--- 2. Model User & AuthService Profile Methods ---\n";

$fetchedUser = $userModel->getById($testUserId);
assertCondition(
    "User::getById trả về đầy đủ gender và birth_date",
    isset($fetchedUser['gender']) && array_key_exists('birth_date', $fetchedUser),
    json_encode($fetchedUser, JSON_UNESCAPED_UNICODE)
);

$allUsers = $userModel->getAllUsers(null, null, null, 5, 0);
assertCondition(
    "User::getAllUsers trả về trường gender và birth_date",
    !empty($allUsers) && isset($allUsers[0]['gender']) && array_key_exists('birth_date', $allUsers[0]),
    "First user keys: " . implode(', ', array_keys($allUsers[0] ?? []))
);

// Cập nhật thông qua AuthService
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$_SESSION['user'] = ['id' => $testUserId, 'name' => 'Old Name'];

$serviceUpdated = $authService->updateProfile(
    $testUserId,
    'Nguyễn Văn Du Khách New',
    '0909999888',
    'Đà Nẵng',
    'Đam mê ẩm thực và khám phá văn hóa miền Trung',
    null,
    'female',
    '1998-12-25'
);

assertCondition(
    "AuthService::updateProfile cập nhật thành công giới tính Nữ và ngày sinh",
    $serviceUpdated['gender'] === 'female' && $serviceUpdated['birth_date'] === '1998-12-25',
    "Gender: {$serviceUpdated['gender']}, Birth: {$serviceUpdated['birth_date']}"
);

assertCondition(
    "AuthService::updateProfile đồng bộ thông tin mới vào \$_SESSION['user']",
    isset($_SESSION['user']['gender']) && $_SESSION['user']['gender'] === 'female' && $_SESSION['user']['name'] === 'Nguyễn Văn Du Khách New',
    json_encode($_SESSION['user'], JSON_UNESCAPED_UNICODE)
);

// ------------------------------------------------------------------------------
// PHẦN 3: GIAO DIỆN HỒ SƠ CÁ NHÂN (app/views/profile/index.php)
// ------------------------------------------------------------------------------
echo "\n--- 3. Giao diện Hồ sơ cá nhân (profile/index.php) ---\n";

$profileViewContent = file_get_contents(__DIR__ . '/../app/views/profile/index.php');

assertCondition(
    "View có Combobox Tỉnh / Thành phố cư trú (select name='living_area')",
    str_contains($profileViewContent, '<select name="living_area"') && str_contains($profileViewContent, 'FormatHelper::getProvincesList()'),
    "Không tìm thấy combobox living_area sử dụng FormatHelper::getProvincesList()"
);

assertCondition(
    "View có Combobox Giới tính (select name='gender') gồm Nam, Nữ, Khác",
    str_contains($profileViewContent, '<select name="gender"') && str_contains($profileViewContent, 'value="male"') && str_contains($profileViewContent, 'value="female"'),
    "Thiếu select gender hoặc các options male/female"
);

assertCondition(
    "View có trường Ngày sinh (input type='date' name='birth_date')",
    str_contains($profileViewContent, 'type="date"') && str_contains($profileViewContent, 'name="birth_date"'),
    "Thiếu input date birth_date"
);

assertCondition(
    "View đã xóa hoàn toàn câu ghi chú kỹ thuật (dhs_sp_users_update_profile)",
    !str_contains($profileViewContent, 'dhs_sp_users_update_profile') && !str_contains($profileViewContent, 'trong CSDL'),
    "Vẫn còn sót câu ghi chú kỹ thuật dhs_sp_users_update_profile trong view!"
);

assertCondition(
    "View có phân biệt bio placeholder cho Host và Tourist",
    str_contains($profileViewContent, '$bioPlaceholder') && str_contains($profileViewContent, 'sở thích du lịch'),
    "Thiếu logic phân biệt bio placeholder"
);

assertCondition(
    "Thẻ tóm tắt bên trái hiển thị Giới tính và Ngày sinh",
    str_contains($profileViewContent, 'Giới tính:') && str_contains($profileViewContent, 'Ngày sinh:'),
    "Thẻ tóm tắt bên trái thiếu hiển thị Giới tính hoặc Ngày sinh"
);

assertCondition(
    "JavaScript form submit tự động mở khóa disabled cho select để gửi dữ liệu",
    str_contains($profileViewContent, "removeAttribute('disabled')") && str_contains($profileViewContent, 'profileForm'),
    "Thiếu JS handler submit mở khóa disabled"
);

// ------------------------------------------------------------------------------
// PHẦN 4: BẢN IN CHI TIẾT ĐƠN ĐẶT CHỖ (app/views/bookings/detail.php)
// ------------------------------------------------------------------------------
echo "\n--- 4. Bản in Chi Tiết Đơn Đặt Chỗ (@media print trong bookings/detail.php) ---\n";

$bookingDetailContent = file_get_contents(__DIR__ . '/../app/views/bookings/detail.php');

assertCondition(
    "Trang chi tiết đơn có khối CSS @media print",
    str_contains($bookingDetailContent, '@media print') && str_contains($bookingDetailContent, '@page'),
    "Thiếu @media print hoặc @page trong bookings/detail.php"
);

assertCondition(
    "@media print ẩn toàn bộ breadcrumb, navbar, nút bấm và modal",
    str_contains($bookingDetailContent, '.no-print') && str_contains($bookingDetailContent, 'display: none !important;'),
    "CSS @media print thiếu ẩn .no-print hoặc display: none"
);

assertCondition(
    "Trang có Print Header trang trọng (ONLINETRAVELLER, PHIẾU XÁC NHẬN ĐẶT TOUR)",
    str_contains($bookingDetailContent, 'print-receipt-header') && str_contains($bookingDetailContent, 'PHIẾU XÁC NHẬN ĐẶT TOUR'),
    "Thiếu print-receipt-header hoặc PHIẾU XÁC NHẬN ĐẶT TOUR"
);

assertCondition(
    "Các nút thao tác và điều hướng mang class no-print",
    str_contains($bookingDetailContent, 'class="d-flex align-items-center gap-2 no-print"') && str_contains($bookingDetailContent, 'class="modal fade no-print"'),
    "Các khối nút thao tác hoặc modal chưa có class no-print"
);

// ------------------------------------------------------------------------------
// PHẦN 5: GIAO DIỆN ĐĂNG NHẬP (app/views/auth/login.php)
// ------------------------------------------------------------------------------
echo "\n--- 5. Nút Bật/Tắt Mật khẩu Đăng nhập (auth/login.php) ---\n";

$loginViewContent = file_get_contents(__DIR__ . '/../app/views/auth/login.php');

assertCondition(
    "Ô nhập mật khẩu có id='inputPassword'",
    str_contains($loginViewContent, 'id="inputPassword"'),
    "Ô mật khẩu thiếu id='inputPassword'"
);

assertCondition(
    "Có nút icon con mắt bật/tắt hiển thị mật khẩu (id='btnTogglePassword')",
    str_contains($loginViewContent, 'id="btnTogglePassword"') && str_contains($loginViewContent, 'id="iconEye"'),
    "Thiếu btnTogglePassword hoặc iconEye"
);

assertCondition(
    "Có JavaScript chuyển đổi qua lại type='password' và type='text'",
    str_contains($loginViewContent, "inputPass.setAttribute('type', isPassword ? 'text' : 'password')") ||
    (str_contains($loginViewContent, "type === 'password'") && str_contains($loginViewContent, "'text'")),
    "Thiếu JS toggle mật khẩu type text/password"
);

assertCondition(
    "JavaScript toggle đổi icon bi-eye sang bi-eye-slash",
    str_contains($loginViewContent, 'bi-eye-slash') && str_contains($loginViewContent, 'bi-eye'),
    "Thiếu logic đổi class icon con mắt"
);

// ------------------------------------------------------------------------------
// TỔNG KẾT
// ------------------------------------------------------------------------------
echo "\n====================================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passed} / {$total} assertions đạt chuẩn (" . round(($passed / $total) * 100, 1) . "%)\n";
if ($passed === $total) {
    echo ">>> XÁC NHẬN: GIAI ĐOẠN 4 ĐÃ HOÀN THÀNH VÀ ĐẠT CHUẨN 100%! <<<\n";
} else {
    echo ">>> CẢNH BÁO: CÓ " . ($total - $passed) . " ASSERTIONS THẤT BẠI! <<<\n";
}
echo "====================================================================================\n";

exit($passed === $total ? 0 : 1);
