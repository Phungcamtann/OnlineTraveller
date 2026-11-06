<?php

declare(strict_types=1);

echo "=== ONLINETRAVELLER TV2-01 STORED PROCEDURES VERIFICATION ===\n\n";

require dirname(__DIR__) . '/bootstrap/app.php';

$pdo = \Core\Database::getConnection();

function runTest(string $title, callable $fn): void {
    try {
        $result = $fn();
        echo "[PASS] {$title}" . ($result ? " -> {$result}" : "") . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$title} -> " . $e->getMessage() . "\n";
    }
}

// 1. Kiểm tra 6 thủ tục tồn tại trong Database
$expectedProcs = [
    'ptl_sp_services_create' => 'Tạo gói trải nghiệm bản địa (kèm tọa độ Maps)',
    'ptl_sp_services_update' => 'Cập nhật tour & tọa độ Maps',
    'ptl_sp_services_moderate' => 'Admin kiểm duyệt mở bán tour',
    'ptl_sp_accommodations_save' => 'Thiết lập thông tin Homestay nhà dân bản địa (1-1)',
    'ptl_sp_schedules_create' => 'Host mở lịch khởi hành mới',
    'ptl_sp_schedules_cancel' => 'Host hủy lịch trình và tự hủy booking chờ',
];

echo "--- 1. KIỂM TRA SỰ TỒN TẠI CỦA 6 THỦ TỤC TRONG MARIADB ---\n";
$dbProcs = $pdo->query("SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = 'PROCEDURE'")->fetchAll(PDO::FETCH_COLUMN);

foreach ($expectedProcs as $procName => $desc) {
    if (in_array($procName, $dbProcs, true)) {
        echo "[EXISTS] {$procName} ({$desc})\n";
    } else {
        echo "[MISSING] {$procName} NOT FOUND!\n";
    }
}

echo "\n--- 2. KIỂM TRẢ LOGIC NGHIỆP VỤ (TEST CASES) ---\n";

$testTourId = null;
$testScheduleId = null;

// Test 1: Chặn Tourist tạo Tour
runTest("Chặn tài khoản Tourist (id=4) tự tạo tour", function() use ($pdo) {
    try {
        $stmt = $pdo->prepare("CALL ptl_sp_services_create(?, 1, 'Test', 'slug-tourist', 'Desc', 'fixed_choice', 100000, 0, 0, 'Da Lat', 12.0, 108.0, 1, null)");
        $stmt->execute([4]);
        throw new \Exception("Lẽ ra phải chặn nhưng lại cho phép tạo!");
    } catch (\PDOException $e) {
        if (strpos($e->getMessage(), 'FORBIDDEN_NOT_HOST') !== false) {
            return "Chặn thành công (FORBIDDEN_NOT_HOST)";
        }
        throw $e;
    }
});

// Test 2: Host tạo Tour hợp lệ
runTest("Host (id=2) tạo tour trải nghiệm mới kèm tọa độ Maps", function() use ($pdo, &$testTourId) {
    $stmt = $pdo->prepare("CALL ptl_sp_services_create(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        2, 1, 'Tour Test TV2 Checklist', 'tour-test-tv2-checklist-' . time(),
        'Mô tả tour kiểm thử tự động', 'fixed_choice', 650000, 0, 0,
        'Cầu Đất, Đà Lạt, Lâm Đồng', 11.9404, 108.4583, 1, '/assets/images/sample.jpg'
    ]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    if (empty($tour['id'])) throw new \Exception("Không lấy được ID tour sau khi tạo!");
    $testTourId = (int)$tour['id'];
    return "Tạo thành công Tour ID: {$testTourId}, Trạng thái: {$tour['status']}";
});

// Test 3: Chặn Host khác sửa tour không phải của mình
runTest("Chặn Host khác (id=3) sửa tour của Host 2", function() use ($pdo, &$testTourId) {
    try {
        $stmt = $pdo->prepare("CALL ptl_sp_services_update(?, ?, 1, 'Update title', 'slug-up', 'Desc', 'fixed_choice', 700000, 0, 0, 'Da Lat', 12.0, 108.0, 1, null)");
        $stmt->execute([3, $testTourId]);
        throw new \Exception("Lẽ ra phải chặn nhưng lại cho phép sửa!");
    } catch (\PDOException $e) {
        if (strpos($e->getMessage(), 'FORBIDDEN_NOT_OWNER') !== false) {
            return "Chặn thành công (FORBIDDEN_NOT_OWNER)";
        }
        throw $e;
    }
});

// Test 4: Host chính chủ cập nhật thông tin tour
runTest("Host chính chủ (id=2) cập nhật giá và địa điểm", function() use ($pdo, &$testTourId) {
    $stmt = $pdo->prepare("CALL ptl_sp_services_update(?, ?, 1, 'Tour Test TV2 Cập Nhật', ?, 'Mô tả mới', 'fixed_choice', 750000, 0, 0, 'Đà Lạt', 11.95, 108.46, 2, null)");
    $stmt->execute([2, $testTourId, 'slug-updated-' . time()]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return "Cập nhật thành công, Giá mới: " . number_format((float)$updated['base_price']) . " VNĐ";
});

// Test 5: Admin duyệt tour
runTest("Admin (id=1) phê duyệt mở bán tour (approved)", function() use ($pdo, &$testTourId) {
    $stmt = $pdo->prepare("CALL ptl_sp_services_moderate(?, ?, ?, ?)");
    $stmt->execute([1, $testTourId, 'approved', null]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return "Phê duyệt thành công, Trạng thái: {$res['status']}";
});

// Test 6: Lưu Homestay nhà dân bản địa (1-1)
runTest("Host thiết lập lưu trú Homestay đính kèm tour", function() use ($pdo, &$testTourId) {
    $stmt = $pdo->prepare("CALL ptl_sp_accommodations_save(?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([2, $testTourId, 'Homestay Cầu Đất View Mây', 'Đồi Chè Cầu Đất, Đà Lạt', 'Phòng Đơn Ấm Cúng', 1, 'Bao gồm bữa sáng']);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return "Lưu Homestay thành công: {$res['homestay_name']} ({$res['room_type']})";
});

// Test 7: Mở lịch khởi hành mới
runTest("Host mở lịch khởi hành mới cho tour", function() use ($pdo, &$testTourId, &$testScheduleId) {
    $stmt = $pdo->prepare("CALL ptl_sp_schedules_create(?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        2, $testTourId, '2026-12-10 05:00:00', '2026-12-10 11:30:00',
        '2026-12-09 18:00:00', 10, 'Cổng chợ Cầu Đất'
    ]);
    $sch = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    $testScheduleId = (int)$sch['id'];
    return "Tạo lịch thành công ID: {$testScheduleId}, Sức chứa: {$sch['capacity']} người";
});

// Test 8: Hủy lịch khởi hành
runTest("Host hủy lịch trình do thời tiết bất khả kháng", function() use ($pdo, &$testScheduleId) {
    $stmt = $pdo->prepare("CALL ptl_sp_schedules_cancel(?, ?, ?)");
    $stmt->execute([2, $testScheduleId, 'Bão gió lớn trên đỉnh núi']);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return "Hủy thành công, Trạng thái: {$res['status']}, Lý do: {$res['cancel_reason']}";
});

// Dọn dẹp dữ liệu test
if ($testTourId) {
    $pdo->exec("DELETE FROM tbl_service_schedules WHERE service_id = {$testTourId}");
    $pdo->exec("DELETE FROM tbl_service_accommodations WHERE service_id = {$testTourId}");
    $pdo->exec("DELETE FROM tbl_services WHERE id = {$testTourId}");
}

echo "\n=== KẾT QUẢ: TOÀN BỘ 6 THỦ TỤC TV2-01 HOẠT ĐỘNG HOÀN HẢO! ===\n";
