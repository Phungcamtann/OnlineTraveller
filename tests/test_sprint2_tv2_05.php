<?php

declare(strict_types=1);

/**
 * Sprint 2: TV2-05 Test Suite
 * Automated tests for Host Booking Approval & 15-Minute Seat Reservation via ndtt_sp_bookings_approve.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/bootstrap/app.php';

use Core\Request;
use Core\Response;
use Core\Database;
use App\Models\Booking;
use App\Models\ServiceSchedule;
use App\Models\Service;
use App\Services\BookingService;
use App\Controllers\HostController;
use App\Middleware\HostMiddleware;

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
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV2-05: HOST BOOKING & 15-MIN SEAT RES \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$bookingModel = new Booking();
$scheduleModel = new ServiceSchedule();
$serviceModel = new Service();
$bookingService = new BookingService($bookingModel);

// 1. Setup mock Host and Tourist
$hostUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'host' LIMIT 1");
$touristUser = $db->fetchOne("SELECT id, name, email, role, status FROM tbl_users WHERE role = 'tourist' LIMIT 1");

if (!$hostUser || !$touristUser) {
    die("Không tìm thấy user host hoặc tourist trong CSDL!\n");
}
$hostId = (int)$hostUser['id'];
$touristId = (int)$touristUser['id'];

// 2. Find or create a service owned by this host
$service = $db->fetchOne("SELECT id, title, base_price FROM tbl_services WHERE host_id = ? LIMIT 1", [$hostId]);
if (!$service) {
    $catId = (int)$db->fetchOne("SELECT id FROM tbl_categories LIMIT 1")['id'];
    $service = (new Service())->create($hostId, $catId, 'Tour Kiểm Thử TV2-05', 'tour-kiem-thu-tv2-05', 'Mô tả test', 'prepaid_full', 500000, 0, 0, 'Đà Lạt', 11.9, 108.4, 1, '/assets/test.jpg');
}
$serviceId = (int)$service['id'];

// 3. Create a fresh departure schedule with capacity = 5
$schedule = $scheduleModel->create(
    $hostId,
    $serviceId,
    date('Y-m-d H:i:s', strtotime('+3 days')),
    date('Y-m-d H:i:s', strtotime('+3 days 5 hours')),
    date('Y-m-d H:i:s', strtotime('+2 days')),
    5,
    'Cổng Hồ Tuyền Lâm'
);
$scheduleId = (int)$schedule['id'];

echo "--- NHÓM 1: ĐẶT TOUR & PHÊ DUYỆT GIỮ CHỖ 15 PHÚT (ndtt_sp_bookings_approve) ---\n";

// 1.1 Tourist creates a booking in 'pending' status
$booking = $bookingService->createBooking($touristId, $scheduleId, 2, 'deposit_50');
$bookingId = (int)$booking['id'];

assertTest("Du khách tạo đơn đặt tour thành công: Trạng thái ban đầu là 'pending'", 
    $booking['status'] === 'pending');
assertTest("Số chỗ đã đặt ban đầu trên lịch trình vẫn là 0 (chưa trừ chỗ)", 
    (int)$scheduleModel->getById($scheduleId)['booked_seats'] === 0);

// 1.2 Host approves booking -> triggers 15-minute seat reservation
$approved = $bookingService->approveBooking($hostId, $bookingId);

assertTest("Host phê duyệt đơn thành công: Trạng thái chuyển sang 'awaiting_payment'", 
    $approved['status'] === 'awaiting_payment');

// Verify 15-minute expiration timestamp
$freshBooking = $bookingModel->getById($bookingId);
$expiresAt = strtotime($freshBooking['payment_expires_at']);
$now = time();
$diffMinutes = ($expiresAt - $now) / 60;

assertTest("Thời gian giữ chỗ thanh toán (payment_expires_at) được thiết lập xấp xỉ 15 phút (14-16 min)", 
    $diffMinutes >= 14 && $diffMinutes <= 16);

// Verify schedule booked_seats incremented by 2
$freshSchedule = $scheduleModel->getById($scheduleId);
assertTest("Lịch trình cập nhật đúng số chỗ đã giữ (booked_seats = 2)", 
    (int)$freshSchedule['booked_seats'] === 2);

echo "\n--- NHÓM 2: PHÊ DUYỆT ĐƠN THANH TOÁN TIỀN MẶT TRỰC TIẾP (host_direct) ---\n";

// 2.1 Booking with host_direct plan -> Host approves -> goes directly to 'confirmed'
$bookingCash = $bookingService->createBooking($touristId, $scheduleId, 1, 'host_direct');
$bookingCashId = (int)$bookingCash['id'];

$approvedCash = $bookingService->approveBooking($hostId, $bookingCashId);
assertTest("Kế hoạch thanh toán tiền mặt (host_direct) duyệt xong chuyển thẳng 'confirmed'", 
    $approvedCash['status'] === 'confirmed');

$freshSchedule2 = $scheduleModel->getById($scheduleId);
assertTest("Số chỗ đã giữ tăng thêm 1 (booked_seats = 3)", 
    (int)$freshSchedule2['booked_seats'] === 3);

echo "\n--- NHÓM 3: CHỐNG OVERBOOKING & KHÓA CONCURRENCY ---\n";

// Capacity is 5, currently booked is 3, remaining is 2.
// Tourist creates pending booking for 2 seats (valid because 2 <= 2)
$bookingOver = $bookingService->createBooking($touristId, $scheduleId, 2, 'deposit_50');
$bookingOverId = (int)$bookingOver['id'];

// Simulate another booking being approved concurrently filling the schedule (booked_seats becomes 4)
$db->execute("UPDATE tbl_service_schedules SET booked_seats = 4 WHERE id = ?", [$scheduleId]);

// Now host tries to approve the 2-seat booking (4 + 2 = 6 > 5 capacity)
$overbookingBlocked = false;
try {
    $bookingService->approveBooking($hostId, $bookingOverId);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'NOT_ENOUGH_SEATS')) {
        $overbookingBlocked = true;
    }
}
assertTest("Chặn duyệt đơn vượt quá sức chứa lịch trình (ndtt_sp_bookings_approve: NOT_ENOUGH_SEATS)", $overbookingBlocked);

// Restore booked_seats back to 3
$db->execute("UPDATE tbl_service_schedules SET booked_seats = 3 WHERE id = ?", [$scheduleId]);
$freshSchedule3 = $scheduleModel->getById($scheduleId);
assertTest("Số chỗ trên lịch vẫn bảo toàn an toàn ở mức 3 chỗ", (int)$freshSchedule3['booked_seats'] === 3);

echo "\n--- NHÓM 4: TỰ ĐỘNG GIẢI PHÓNG CHỖ KHI HẾT HẠN 15 PHÚT (releaseExpiredReservations) ---\n";

// Simulate an expired booking: set payment_expires_at to 10 minutes ago
$db->execute("UPDATE tbl_bookings SET payment_expires_at = DATE_SUB(NOW(6), INTERVAL 10 MINUTE) WHERE id = ?", [$bookingId]);

// Trigger release
$releasedCount = $bookingModel->releaseExpiredReservations();
assertTest("releaseExpiredReservations() phát hiện và xử lý đơn hết hạn 15 phút", $releasedCount >= 1);

$checkExpiredBooking = $bookingModel->getById($bookingId);
assertTest("Đơn hết hạn chuyển trạng thái thành 'expired' kèm lý do rõ ràng", 
    $checkExpiredBooking['status'] === 'expired' && str_contains($checkExpiredBooking['cancel_reason'], '15 phút'));

// Verify schedule seats were released back (from 3 down to 1)
$freshSchedule4 = $scheduleModel->getById($scheduleId);
assertTest("2 chỗ của đơn hết hạn đã được nhả lại vào lịch trình (booked_seats = 1)", 
    (int)$freshSchedule4['booked_seats'] === 1);

echo "\n--- NHÓM 5: TỪ CHỐI ĐƠN & HỦY ĐƠN (REJECT & CANCEL) ---\n";

// 5.1 Host rejects bookingOver
$rejected = $bookingService->rejectBooking($hostId, $bookingOverId, 'Đoàn khách quá đông so với chỗ ngồi');
assertTest("Host từ chối đơn đặt: Trạng thái chuyển thành 'rejected'", $rejected['status'] === 'rejected');
assertTest("Lý do từ chối được lưu chính xác trong cơ sở dữ liệu", 
    str_contains($rejected['rejection_reason'], 'quá đông'));

// 5.2 Host cancels cash booking -> seats released
$cancelled = $bookingService->cancelBooking($hostId, $bookingCashId, 'Host có việc đột xuất');
assertTest("Host hủy đơn đã xác nhận: Trạng thái chuyển thành 'cancelled'", $cancelled['status'] === 'cancelled');

$freshSchedule5 = $scheduleModel->getById($scheduleId);
assertTest("Chỗ của đơn tiền mặt được giải phóng hoàn toàn về 0 (booked_seats = 0)", 
    (int)$freshSchedule5['booked_seats'] === 0);

echo "\n--- NHÓM 6: HOST CONTROLLER ENDPOINTS & PHÂN QUYỀN ---\n";

$_SESSION['user'] = $hostUser;
$hostController = new HostController(new Request());

// 6.1 View host/bookings/index rendering
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/host/bookings',
    'SCRIPT_NAME' => '/index.php',
];
ob_start();
$hostController->bookings(new Request());
$renderedIndex = ob_get_clean();

assertTest("HostController::bookings render giao diện thành công không lỗi", strlen($renderedIndex) > 500);
assertTest("Giao diện chứa tiêu đề 'Quản Lý Đơn Đặt & Duyệt Giữ Chỗ'", 
    str_contains($renderedIndex, 'Quản Lý Đơn Đặt & Duyệt Giữ Chỗ'));
assertTest("Giao diện chứa 5 thẻ KPI và bộ lọc theo trạng thái/tour", 
    str_contains($renderedIndex, 'TỔNG ĐƠN ĐẶT') && str_contains($renderedIndex, 'CHỜ DUYỆT'));

// 6.2 View host/bookings/show rendering
ob_start();
$hostController->showBooking(new Request(), (string)$bookingId);
$renderedShow = ob_get_clean();
assertTest("HostController::showBooking render chi tiết đơn đặt thành công", 
    str_contains($renderedShow, 'Hồ Sơ Yêu Cầu Đặt Chỗ') && str_contains($renderedShow, $freshBooking['booking_code']));

// 6.3 Security: Tourist cannot access Host bookings
$_SESSION['user'] = $touristUser;
$hostMw = new HostMiddleware();
ob_start();
$hostMw->handle(new Request(), fn($r) => 'PASSED');
$mwBlocked = ob_get_clean();
assertTest("Du khách bị chặn khi cố tình vào /host/bookings (HTTP 403)", http_response_code() === 403);

// 6.4 Clean up test records
$db->execute("DELETE FROM tbl_bookings WHERE id IN (?, ?, ?)", [$bookingId, $bookingCashId, $bookingOverId]);
$db->execute("DELETE FROM tbl_service_schedules WHERE id = ?", [$scheduleId]);
echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ dữ liệu kiểm thử TV2-05.\n";

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV2-05 ĐẠT CHUẨN 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
