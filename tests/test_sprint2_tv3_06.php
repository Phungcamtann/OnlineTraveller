<?php

declare(strict_types=1);

/**
 * Test Suite: Sprint 2 - Task TV3-06
 * Nội dung: Biên nhận điện tử & Xử lý hủy đơn / Hoàn tiền (MoMo Sandbox)
 * 
 * Kiểm thử:
 * 1. Receipt Model: getFullReceipt, getByCode, getByBookingId.
 * 2. Hủy đơn chưa thanh toán: Nhả chỗ, không tạo hồ sơ hoàn tiền (Mục 3.6).
 * 3. Hủy đơn đã thanh toán TRƯỚC T24: Hủy hợp lệ, tự động sinh hồ sơ hoàn tiền status='approved' (Mục 3.6).
 * 4. Hủy đơn đã thanh toán SAU T24: Tiếp nhận xem xét, hồ sơ hoàn tiền status='requested' (Mục 3.6).
 * 5. Admin phê duyệt hồ sơ hoàn tiền via ndtt_sp_refunds_create_and_approve.
 * 6. Authorization: Chặn người lạ truy cập biên nhận và hồ sơ hoàn tiền (HTTP 403).
 * 7. Controller endpoints: ReceiptController::show, RefundController::show, AdminController::refunds.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/bootstrap/app.php';

use Core\Database;
use Core\Request;
use Core\Response;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Refund;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Integrations\MomoClient;
use App\Controllers\ReceiptController;
use App\Controllers\RefundController;
use App\Controllers\AdminController;

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

Response::$preventExit = true;

echo "========================================================================\n";
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV3-06: BIÊN NHẬN & HOÀN TIỀN T24     \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$bookingModel = new Booking();
$paymentModel = new Payment();
$receiptModel = new Receipt();
$refundModel = new Refund();
$scheduleModel = new ServiceSchedule();
$bookingService = new BookingService($bookingModel);
$momoClient = new MomoClient();
$paymentService = new PaymentService($momoClient, $paymentModel, $bookingModel);

// 1. Fetch test users
$adminUser = $db->fetchOne("SELECT id, name, email, role FROM tbl_users WHERE role = 'admin' LIMIT 1");
$hostUser = $db->fetchOne("SELECT id, name, email, role FROM tbl_users WHERE role = 'host' LIMIT 1");
$touristUser = $db->fetchOne("SELECT id, name, email, role FROM tbl_users WHERE role = 'tourist' LIMIT 1");

if (!$adminUser || !$hostUser || !$touristUser) {
    die("Không tìm đủ các vai trò admin, host, tourist để kiểm thử!\n");
}

$adminId = (int)$adminUser['id'];
$hostId = (int)$hostUser['id'];
$touristId = (int)$touristUser['id'];

// Get or create test service
$service = $db->fetchOne("SELECT id, base_price FROM tbl_services WHERE host_id = ? LIMIT 1", [$hostId]);
if (!$service) {
    $catId = (int)$db->fetchOne("SELECT id FROM tbl_categories LIMIT 1")['id'];
    $service = (new Service())->create(
        $hostId, $catId, 'Tour TV3-06 Test', 'tour-tv3-06-test', 'MoMo test',
        'prepaid_full', 500000, 0, 0, 'Đà Lạt', 11.9, 108.4, 1, '/assets/test.jpg'
    );
}
$serviceId = (int)$service['id'];

// Create Schedule 1: Starts in 4 days (> 24 hours -> Before T24)
$schedBeforeT24 = $scheduleModel->create(
    $hostId,
    $serviceId,
    date('Y-m-d H:i:s', strtotime('+4 days 09:00:00')),
    date('Y-m-d H:i:s', strtotime('+4 days 17:00:00')),
    date('Y-m-d H:i:s', strtotime('+3 days 12:00:00')),
    10,
    'Hồ Tuyền Lâm, Đà Lạt'
);
$schedBeforeT24Id = (int)$schedBeforeT24['id'];

// Create Schedule 2: Starts in 6 hours (< 24 hours -> After T24)
$schedAfterT24 = $scheduleModel->create(
    $hostId,
    $serviceId,
    date('Y-m-d H:i:s', strtotime('+6 hours')),
    date('Y-m-d H:i:s', strtotime('+10 hours')),
    date('Y-m-d H:i:s', strtotime('+2 hours')),
    10,
    'Hồ Tuyền Lâm, Đà Lạt'
);
$schedAfterT24Id = (int)$schedAfterT24['id'];

// ==============================================================================
// TEST GROUP 1: ELECTRONIC RECEIPT MODEL & DATA INTEGRITY
// ==============================================================================
echo "--- NHÓM 1: MÔ HÌNH BIÊN NHẬN ĐIỆN TỬ (Receipt.php) ---\n";

// Create and pay a booking on Schedule 1
$bk1 = $bookingService->createBooking($touristId, $schedBeforeT24Id, 2, 'deposit_50');
$bk1Id = (int)$bk1['id'];
$bookingService->approveBooking($hostId, $bk1Id);

$initPayment1 = $paymentService->initiateMomoPayment($bk1Id, $touristId);
$depositAmount1 = (float)$initPayment1['amount'];

// Simulate IPN webhook to confirm booking and issue receipt
$ipn1 = [
    'partnerCode' => $momoClient->getPartnerCode(),
    'orderId' => $initPayment1['order_id'],
    'requestId' => $initPayment1['request_id'],
    'amount' => (int)$depositAmount1,
    'orderInfo' => 'Thanh toan test TV3-06',
    'orderType' => 'momo_wallet',
    'transId' => 'MOMO_TRANS_TV306_1',
    'resultCode' => 0,
    'message' => 'Thành công.',
    'payType' => 'qr',
    'responseTime' => (int)(microtime(true) * 1000),
    'extraData' => '',
];
$ipn1['signature'] = $momoClient->generateIpnSignature($ipn1);
$paymentService->handleIpn($ipn1);

// Test Receipt retrieval
$receipt = $receiptModel->getByBookingId($bk1Id);
assertTest("Tìm thấy biên nhận điện tử theo booking_id", $receipt !== null && !empty($receipt['receipt_code']));
assertTest("Mã biên nhận có tiền tố RC (chuẩn OnlineTraveller)", str_starts_with($receipt['receipt_code'], 'RC'));
assertTest("Số tiền trong biên nhận khớp số tiền cọc thanh toán", (float)$receipt['total_amount'] === $depositAmount1);

$receiptByCode = $receiptModel->getByCode($receipt['receipt_code']);
assertTest("Tìm thấy biên nhận theo mã duy nhất (getByCode)", 
    $receiptByCode !== null && (int)$receiptByCode['id'] === (int)$receipt['id']);

$fullReceipt = $receiptModel->getFullReceipt($receipt['receipt_code']);
assertTest("getFullReceipt chứa đầy đủ thông tin: Tourist, Host, Tour, Lịch khởi hành", 
    !empty($fullReceipt['tourist_name']) && 
    !empty($fullReceipt['host_name']) && 
    !empty($fullReceipt['service_title']) && 
    !empty($fullReceipt['starts_at']));

// ==============================================================================
// TEST GROUP 2: CANCELLATION WITHOUT ONLINE PAYMENT (RULE 3.6)
// ==============================================================================
echo "\n--- NHÓM 2: HỦY ĐƠN CHƯA THANH TOÁN (Mục 3.6) ---\n";

// Booking created and approved (awaiting_payment) but not paid
$bkUnpaid = $bookingService->createBooking($touristId, $schedBeforeT24Id, 1, 'deposit_50');
$bkUnpaidId = (int)$bkUnpaid['id'];
$bookingService->approveBooking($hostId, $bkUnpaidId);

// Check seat reserved
$schedBefore = $scheduleModel->getById($schedBeforeT24Id);
$seatsBefore = (int)$schedBefore['booked_seats'];

// Tourist cancels unpaid booking
$cancelUnpaid = $bookingService->cancelBooking($touristId, $bkUnpaidId, 'Bận việc đột xuất');
assertTest("Hủy đơn chưa thanh toán thành công (status='cancelled')", 
    $cancelUnpaid['status'] === 'cancelled');

// Verify seat is released
$schedAfter = $scheduleModel->getById($schedBeforeT24Id);
assertTest("Số chỗ đã giữ được nhả lại vào lịch trình", 
    (int)$schedAfter['booked_seats'] === $seatsBefore - 1);

// Verify NO refund record was created
$refundUnpaid = $refundModel->getByBookingId($bkUnpaidId);
assertTest("Không tạo hồ sơ hoàn tiền khi chưa thu tiền online (chuẩn Mục 3.6)", 
    $refundUnpaid === null);

// ==============================================================================
// TEST GROUP 3: CANCELLATION BEFORE T24 WITH ONLINE PAYMENT (RULE 3.6)
// ==============================================================================
echo "\n--- NHÓM 3: HỦY ĐƠN ĐÃ THANH TOÁN TRƯỚC T24 (Mục 3.6) ---\n";

// $bk1 was confirmed with payment and schedule starts in 4 days (> 24h)
$seatsBeforeBk1Cancel = (int)$scheduleModel->getById($schedBeforeT24Id)['booked_seats'];

// Tourist cancels $bk1 before T24
$cancelBk1 = $bookingService->cancelBooking($touristId, $bk1Id, 'Khách xin hủy trước 4 ngày');
assertTest("Hủy đơn đã xác nhận trước T24 thành công (status='cancelled')", 
    $cancelBk1['status'] === 'cancelled');

// Verify seat released
$seatsAfterBk1Cancel = (int)$scheduleModel->getById($schedBeforeT24Id)['booked_seats'];
assertTest("Số chỗ của đơn đã thanh toán được hoàn trả đầy đủ vào lịch", 
    $seatsAfterBk1Cancel === $seatsBeforeBk1Cancel - 2);

// Verify automatic refund record created with status='approved'
$refundBk1 = $refundModel->getByBookingId($bk1Id);
assertTest("Hệ thống tự động tạo hồ sơ hoàn tiền trong tbl_refunds", 
    $refundBk1 !== null && str_starts_with($refundBk1['refund_code'], 'RF'));
assertTest("Trạng thái hoàn tiền là 'approved' (hủy hợp lệ trước T24 được duyệt hoàn toàn bộ)", 
    $refundBk1['status'] === 'approved');
assertTest("Số tiền hoàn khớp 100% khoản cọc online đã thu", 
    (float)$refundBk1['amount'] === $depositAmount1);
assertTest("Căn cứ hủy ghi nhận rõ 'Hủy hợp lệ trước T24'", 
    str_contains($refundBk1['reason'], 'Hủy hợp lệ trước T24'));

// ==============================================================================
// TEST GROUP 4: CANCELLATION AFTER T24 WITH ONLINE PAYMENT (RULE 3.6)
// ==============================================================================
echo "\n--- NHÓM 4: HỦY ĐƠN ĐÃ THANH TOÁN SAU T24 (Mục 3.6) ---\n";

// Create and confirm booking on Schedule 2 (starts in 6 hours < 24h)
$bkAfterT24 = $bookingService->createBooking($touristId, $schedAfterT24Id, 1, 'deposit_50');
$bkAfterT24Id = (int)$bkAfterT24['id'];
$bookingService->approveBooking($hostId, $bkAfterT24Id);

$initPayment2 = $paymentService->initiateMomoPayment($bkAfterT24Id, $touristId);
$depositAmount2 = (float)$initPayment2['amount'];

$ipn2 = [
    'partnerCode' => $momoClient->getPartnerCode(),
    'orderId' => $initPayment2['order_id'],
    'requestId' => $initPayment2['request_id'],
    'amount' => (int)$depositAmount2,
    'orderInfo' => 'Thanh toan test TV3-06 After T24',
    'orderType' => 'momo_wallet',
    'transId' => 'MOMO_TRANS_TV306_2',
    'resultCode' => 0,
    'message' => 'Thành công.',
    'payType' => 'qr',
    'responseTime' => (int)(microtime(true) * 1000),
    'extraData' => '',
];
$ipn2['signature'] = $momoClient->generateIpnSignature($ipn2);
$paymentService->handleIpn($ipn2);

// Tourist cancels booking after T24
$cancelBkAfter = $bookingService->cancelBooking($touristId, $bkAfterT24Id, 'Bận đột xuất sát giờ');
assertTest("Hủy đơn sau T24 thành công (status='cancelled')", 
    $cancelBkAfter['status'] === 'cancelled');

// Verify refund status is 'requested' (NOT auto-approved)
$refundAfter = $refundModel->getByBookingId($bkAfterT24Id);
assertTest("Hồ sơ hoàn tiền sau T24 được tạo với trạng thái 'requested' (chờ Admin xem xét)", 
    $refundAfter !== null && $refundAfter['status'] === 'requested');
assertTest("Lý do hoàn tiền ghi nhận rõ 'Hủy sau T24'", 
    str_contains($refundAfter['reason'], 'Hủy sau T24'));

// ==============================================================================
// TEST GROUP 5: ADMIN REFUND MODERATION & APPROVAL
// ==============================================================================
echo "\n--- NHÓM 5: ADMIN PHÊ DUYỆT HOÀN TIỀN (AdminController) ---\n";

$_SESSION['user'] = $adminUser;
$adminController = new AdminController(new Request());

// 5.1 Admin views refund requests list (GET /admin/refunds)
ob_start();
$adminController->refunds(new Request());
$renderedRefunds = ob_get_clean();

assertTest("AdminController::refunds render danh sách hồ sơ thành công", 
    strlen($renderedRefunds) > 500 && str_contains($renderedRefunds, 'Hồ Sơ Hủy Tour & Hoàn Tiền'));
assertTest("Giao diện Admin hiển thị đúng mã hoàn tiền #{$refundAfter['refund_code']}", 
    str_contains($renderedRefunds, $refundAfter['refund_code']));

// 5.2 Admin approves the requested refund (POST /admin/refunds/{id}/approve)
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'admin_note' => 'Admin phê duyệt lý do bất khả kháng có giấy xác nhận y tế.',
];
ob_start();
$adminController->approveRefund(new Request(), (string)$refundAfter['id']);
$approveOutput = ob_get_clean();

$updatedRefundAfter = $refundModel->getById((int)$refundAfter['id']);
assertTest("Admin duyệt hoàn tiền: Trạng thái chuyển thành 'approved'", 
    $updatedRefundAfter['status'] === 'approved');
assertTest("Ghi chú của Admin được lưu trữ chính xác", 
    str_contains($updatedRefundAfter['admin_note'], 'giấy xác nhận y tế'));

// 5.3 Test Stored Procedure ndtt_sp_refunds_create_and_approve directly
$payment2Record = $paymentModel->getByOrderId($initPayment2['order_id']);
$spRefund = $refundModel->approveRefundByAdmin(
    $adminId,
    (int)$payment2Record['id'],
    $depositAmount2,
    'Admin duyệt hoàn tiền qua Stored Procedure',
    'Phê duyệt trực tiếp'
);
assertTest("Thực thi Stored Procedure ndtt_sp_refunds_create_and_approve thành công", 
    !empty($spRefund['refund_code']) && $spRefund['status'] === 'approved');

// ==============================================================================
// TEST GROUP 6: ACCESS CONTROL & FORBIDDEN ACCESS (RBAC)
// ==============================================================================
echo "\n--- NHÓM 6: PHÂN QUYỀN TRUY CẬP (RBAC - RECEIPT & REFUND) ---\n";

$receiptController = new ReceiptController(new Request());
$refundController = new RefundController(new Request());

// 6.1 Tourist owner can access their own receipt
$_SESSION['user'] = $touristUser;
ob_start();
$receiptController->show(new Request(), (string)$receipt['receipt_code']);
$receiptViewOutput = ob_get_clean();
assertTest("Du khách xem được biên nhận điện tử của chính mình", 
    str_contains($receiptViewOutput, 'BIÊN NHẬN ĐIỆN TỬ') && str_contains($receiptViewOutput, $receipt['receipt_code']));

// 6.2 Host of the service can also view the receipt
$_SESSION['user'] = $hostUser;
ob_start();
$receiptController->show(new Request(), (string)$receipt['receipt_code']);
$hostReceiptView = ob_get_clean();
assertTest("Host bản địa xem được biên nhận điện tử của tour mình quản lý", 
    str_contains($hostReceiptView, $receipt['receipt_code']));

// 6.3 Unrelated tourist is blocked from viewing another tourist's receipt (HTTP 403)
$_SESSION['user'] = [
    'id' => 88888,
    'name' => 'Stranger Tourist',
    'email' => 'stranger@example.com',
    'role' => 'tourist',
    'status' => 'active',
];
ob_start();
$receiptController->show(new Request(), (string)$receipt['receipt_code']);
$strangerReceiptView = ob_get_clean();
assertTest("Người lạ bị chặn khi truy cập biên nhận của du khách khác (HTTP 403)", 
    str_contains($strangerReceiptView, '403') || str_contains($strangerReceiptView, 'từ chối'));

// 6.4 Unrelated tourist blocked from viewing refund ticket
ob_start();
$refundController->show(new Request(), (string)$refundBk1['refund_code']);
$strangerRefundView = ob_get_clean();
assertTest("Người lạ bị chặn khi truy cập phiếu hoàn tiền của du khách khác (HTTP 403)", 
    str_contains($strangerRefundView, '403') || str_contains($strangerRefundView, 'từ chối'));

// 6.5 Tourist owner can view their own refund ticket
$_SESSION['user'] = $touristUser;
ob_start();
$refundController->show(new Request(), (string)$refundBk1['refund_code']);
$touristRefundView = ob_get_clean();
assertTest("Du khách xem được phiếu hoàn tiền của chính mình (#{$refundBk1['refund_code']})", 
    str_contains($touristRefundView, 'Phiếu Theo Dõi Hoàn Tiền') && str_contains($touristRefundView, $refundBk1['refund_code']));

// Clean up all test records
$db->execute("DELETE FROM tbl_refunds WHERE booking_id IN (?, ?, ?)", [$bk1Id, $bkUnpaidId, $bkAfterT24Id]);
$db->execute("DELETE FROM tbl_receipts WHERE booking_id IN (?, ?, ?)", [$bk1Id, $bkUnpaidId, $bkAfterT24Id]);
$db->execute("DELETE FROM tbl_payments WHERE booking_id IN (?, ?, ?)", [$bk1Id, $bkUnpaidId, $bkAfterT24Id]);
$db->execute("DELETE FROM tbl_bookings WHERE id IN (?, ?, ?)", [$bk1Id, $bkUnpaidId, $bkAfterT24Id]);
$db->execute("DELETE FROM tbl_service_schedules WHERE id IN (?, ?)", [$schedBeforeT24Id, $schedAfterT24Id]);

echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi kiểm thử TV3-06.\n";

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV3-06 ĐẠT CHUẨN 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
