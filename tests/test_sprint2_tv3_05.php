<?php

declare(strict_types=1);

/**
 * Sprint 2: TV3-05 Test Suite
 * Automated tests for MoMo Sandbox QR Checkout, HMAC-SHA256 Signatures,
 * IPN Webhook Verification, and Electronic Receipt Issuance.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/bootstrap/app.php';

use Core\Request;
use Core\Response;
use Core\Database;
use App\Integrations\MomoClient;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ServiceSchedule;
use App\Models\Service;
use App\Services\PaymentService;
use App\Services\BookingService;
use App\Controllers\PaymentController;

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
echo "   CHẠY KIỂM THỬ SPRINT 2 - TASK TV3-05: MOMO SANDBOX QR & HMAC-SHA256   \n";
echo "========================================================================\n\n";

$db = Database::getInstance();
$momoClient = new MomoClient();
$bookingModel = new Booking();
$paymentModel = new Payment();
$scheduleModel = new ServiceSchedule();
$paymentService = new PaymentService($momoClient, $paymentModel, $bookingModel);
$bookingService = new BookingService($bookingModel);

// Setup mock Tourist and Host
$hostUser = $db->fetchOne("SELECT id, name, email, role FROM tbl_users WHERE role = 'host' LIMIT 1");
$touristUser = $db->fetchOne("SELECT id, name, email, role FROM tbl_users WHERE role = 'tourist' LIMIT 1");

if (!$hostUser || !$touristUser) {
    die("Không tìm thấy user host hoặc tourist!\n");
}
$hostId = (int)$hostUser['id'];
$touristId = (int)$touristUser['id'];

// Create a service and schedule for checkout test
$service = $db->fetchOne("SELECT id FROM tbl_services WHERE host_id = ? LIMIT 1", [$hostId]);
if (!$service) {
    $catId = (int)$db->fetchOne("SELECT id FROM tbl_categories LIMIT 1")['id'];
    $service = (new Service())->create($hostId, $catId, 'Tour MoMo Test TV3-05', 'tour-momo-tv3-05', 'MoMo test', 'prepaid_full', 600000, 0, 0, 'Đà Lạt', 11.9, 108.4, 1, '/assets/test.jpg');
}
$serviceId = (int)$service['id'];

$schedule = $scheduleModel->create(
    $hostId,
    $serviceId,
    date('Y-m-d H:i:s', strtotime('+4 days')),
    date('Y-m-d H:i:s', strtotime('+4 days 4 hours')),
    date('Y-m-d H:i:s', strtotime('+3 days')),
    10,
    'Bến Thuyền Hồ Tuyền Lâm'
);
$scheduleId = (int)$schedule['id'];

// ==============================================================================
// TEST GROUP 1: HMAC-SHA256 SIGNATURE GENERATION & VERIFICATION
// ==============================================================================
echo "--- NHÓM 1: CHỮ KÝ BẢO MẬT HMAC-SHA256 (MomoClient) ---\n";

$testOrderId = 'MOMO_TEST_ORD_' . time();
$testRequestId = 'REQ_' . time();
$testAmount = 300000;
$testOrderInfo = 'Thanh toan test TV3-05';

// 1.1 Signature generation for Create Request
$createSig = $momoClient->generateCreateSignature($testOrderId, $testRequestId, $testAmount, $testOrderInfo);
assertTest("MomoClient sinh chữ ký Create Request HMAC-SHA256 thành công (64 ký tự hex)", 
    strlen($createSig) === 64 && ctype_xdigit($createSig));

// 1.2 Signature generation for IPN Webhook
$ipnData = [
    'partnerCode' => $momoClient->getPartnerCode(),
    'orderId' => $testOrderId,
    'requestId' => $testRequestId,
    'amount' => $testAmount,
    'orderInfo' => $testOrderInfo,
    'orderType' => 'momo_wallet',
    'transId' => 'TRANS_12345678',
    'resultCode' => 0,
    'message' => 'Thành công.',
    'payType' => 'qr',
    'responseTime' => 1700000000000,
    'extraData' => '',
];
$ipnSig = $momoClient->generateIpnSignature($ipnData);
$ipnData['signature'] = $ipnSig;

assertTest("MomoClient sinh chữ ký IPN Webhook HMAC-SHA256 thành công (64 ký tự hex)", 
    strlen($ipnSig) === 64 && ctype_xdigit($ipnSig));

// 1.3 Verify valid signature
assertTest("Xác thực chữ ký hợp lệ: verifySignature() trả về true", $momoClient->verifySignature($ipnData));

// 1.4 Tampered amount rejected
$tamperedAmount = $ipnData;
$tamperedAmount['amount'] = 1000; // Thay đổi số tiền từ 300k xuống 1k
assertTest("Chống gian lận: Thay đổi số tiền bị phát hiện sai chữ ký (verifySignature = false)", 
    !$momoClient->verifySignature($tamperedAmount));

// 1.5 Tampered orderId rejected
$tamperedOrder = $ipnData;
$tamperedOrder['orderId'] = 'FORGED_ORDER_XYZ';
assertTest("Chống giả mạo: Thay đổi mã đơn bị phát hiện sai chữ ký", 
    !$momoClient->verifySignature($tamperedOrder));

// 1.6 Blank signature rejected
$tamperedSig = $ipnData;
$tamperedSig['signature'] = '';
assertTest("Chữ ký trống bị từ chối", !$momoClient->verifySignature($tamperedSig));

// ==============================================================================
// TEST GROUP 2: INITIATE MOMO PAYMENT & 15-MINUTE RESERVATION WINDOW
// ==============================================================================
echo "\n--- NHÓM 2: KHỞI TẠO THANH TOÁN MOMO SANDBOX (PaymentService::initiateMomoPayment) ---\n";

// 2.1 Create booking (1 guest, deposit_50 -> total 600k, deposit 300k)
$booking = $bookingService->createBooking($touristId, $scheduleId, 1, 'deposit_50');
$bookingId = (int)$booking['id'];

// Try to initiate payment when still in 'pending' status -> Must be rejected
$pendingBlocked = false;
try {
    $paymentService->initiateMomoPayment($bookingId, $touristId);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'chờ Dân bản địa')) {
        $pendingBlocked = true;
    }
}
assertTest("Chặn thanh toán khi đơn chưa được Host duyệt giữ chỗ (pending)", $pendingBlocked);

// Host approves booking -> status becomes 'awaiting_payment' with 15-min countdown
$bookingService->approveBooking($hostId, $bookingId);

// Now initiate MoMo payment
$paymentResult = $paymentService->initiateMomoPayment($bookingId, $touristId);
$expectedDeposit = (int)$paymentResult['amount'];

assertTest("Khởi tạo thanh toán MoMo thành công cho đơn đã duyệt giữ chỗ", 
    !empty($paymentResult['pay_url']) && !empty($paymentResult['order_id']));
assertTest("Số tiền thanh toán tính đúng số tiền cọc 50% (" . number_format($expectedDeposit) . " VND)", 
    $expectedDeposit > 0 && (int)$paymentResult['amount'] === (int)$booking['deposit_amount']);

// Verify record created in tbl_payments
$paymentRecord = $paymentModel->getByOrderId($paymentResult['order_id']);
assertTest("Bản ghi giao dịch được lưu vào tbl_payments với status='pending'", 
    $paymentRecord !== null && $paymentRecord['status'] === 'pending' && (int)$paymentRecord['booking_id'] === $bookingId);

// Non-owner tourist cannot initiate payment for another tourist's booking
$strangerBlocked = false;
try {
    $paymentService->initiateMomoPayment($bookingId, 999999);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'không phải là người tạo đơn')) {
        $strangerBlocked = true;
    }
}
assertTest("Chặn người dùng lạ thanh toán đơn của du khách khác", $strangerBlocked);

// ==============================================================================
// TEST GROUP 3: MOMO IPN WEBHOOK & CONFIRMATION WITH RECEIPT ISSUANCE
// ==============================================================================
echo "\n--- NHÓM 3: XỬ LÝ IPN WEBHOOK & SINH BIÊN NHẬN (ndtt_sp_payments_apply_verified) ---\n";

$ipnPayload = [
    'partnerCode' => $momoClient->getPartnerCode(),
    'orderId' => $paymentResult['order_id'],
    'requestId' => $paymentResult['request_id'],
    'amount' => $expectedDeposit,
    'orderInfo' => 'Thanh toan don dat tour #' . $booking['booking_code'],
    'orderType' => 'momo_wallet',
    'transId' => 'MOMO_TRANS_999888',
    'resultCode' => 0,
    'message' => 'Thành công.',
    'payType' => 'qr',
    'responseTime' => (int)(microtime(true) * 1000),
    'extraData' => '',
];
$ipnPayload['signature'] = $momoClient->generateIpnSignature($ipnPayload);

// Execute handleIpn
$ipnResult = $paymentService->handleIpn($ipnPayload);

assertTest("Xử lý IPN Webhook thành công: Trả về resultCode = 0", 
    isset($ipnResult['resultCode']) && $ipnResult['resultCode'] === 0);

// Verify database mutation via ndtt_sp_payments_apply_verified
$updatedBooking = $bookingModel->getById($bookingId);
assertTest("Trạng thái đơn đặt chuyển sang 'confirmed' chính thức", 
    $updatedBooking['status'] === 'confirmed');
assertTest("Thời hạn giữ chỗ 15 phút (payment_expires_at) được xóa sạch về NULL", 
    empty($updatedBooking['payment_expires_at']));

$updatedPayment = $paymentModel->getByOrderId($paymentResult['order_id']);
assertTest("Trạng thái thanh toán chuyển sang 'succeeded' và lưu mã giao dịch MoMo", 
    $updatedPayment['status'] === 'succeeded' && $updatedPayment['momo_trans_id'] === 'MOMO_TRANS_999888');

// Verify electronic receipt in tbl_receipts
$receipt = $paymentModel->getReceiptByBookingId($bookingId);
assertTest("Tự động phát hành biên nhận điện tử trong tbl_receipts với mã RC...", 
    $receipt !== null && str_starts_with($receipt['receipt_code'], 'RC'));
assertTest("Số tiền trong biên nhận khớp chính xác số tiền cọc (" . number_format($expectedDeposit) . " VND)", 
    (int)$receipt['total_amount'] === $expectedDeposit);

// ==============================================================================
// TEST GROUP 4: DEDUPLICATION & TAMPERING GUARDS
// ==============================================================================
echo "\n--- NHÓM 4: KHỬ TRÙNG LẶP SỰ KIỆN & CHỐNG GIAN LẬN TIỀN ---\n";

// 4.1 Deduplication / Idempotency: Send the exact same IPN a second time
$duplicateIpnRes = $paymentService->handleIpn($ipnPayload);
assertTest("Idempotency: Gửi lại IPN đã thành công trả về resultCode=0 an toàn không bị nhân đôi biên nhận", 
    $duplicateIpnRes['resultCode'] === 0 && str_contains($duplicateIpnRes['message'], 'already processed'));

// Verify only 1 receipt exists for this booking
$receiptsCount = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tbl_receipts WHERE booking_id = ?", [$bookingId])['c'];
assertTest("Chỉ có duy nhất 1 biên nhận điện tử được phát hành (không bị trùng lặp)", $receiptsCount === 1);

// 4.2 Amount mismatch in IPN rejected
$mismatchPayload = $ipnPayload;
$mismatchPayload['amount'] = $expectedDeposit + 50000; // Khách/hacker gửi số tiền sai
$mismatchPayload['signature'] = $momoClient->generateIpnSignature($mismatchPayload);

$mismatchBlocked = false;
try {
    $paymentService->handleIpn($mismatchPayload);
} catch (\Throwable $e) {
    if (str_contains($e->getMessage(), 'AMOUNT_MISMATCH')) {
        $mismatchBlocked = true;
    }
}
assertTest("Chống gian lận: Số tiền IPN không khớp số tiền đơn bị chặn (AMOUNT_MISMATCH)", $mismatchBlocked);

// ==============================================================================
// TEST GROUP 5: CONTROLLER WEB & IPN ENDPOINTS
// ==============================================================================
echo "\n--- NHÓM 5: CONTROLLER ENDPOINTS & GIAO DIỆN (PaymentController) ---\n";

$_SESSION['user'] = $touristUser;
$paymentController = new PaymentController(new Request());

// 5.1 MoMo Return Callback View (GET /payments/callback)
$_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/payments/callback',
    'SCRIPT_NAME' => '/index.php',
];
$_GET = [
    'orderId' => $paymentResult['order_id'],
    'resultCode' => '0',
    'amount' => (string)$expectedDeposit,
    'transId' => 'MOMO_TRANS_999888',
];
ob_start();
$paymentController->callback(new Request());
$renderedCallback = ob_get_clean();

assertTest("PaymentController::callback render giao diện thành công", strlen($renderedCallback) > 500);
assertTest("Giao diện chứa thông báo 'THANH TOÁN THÀNH CÔNG (MOMO SANDBOX)'", 
    str_contains($renderedCallback, 'THANH TOÁN THÀNH CÔNG (MOMO SANDBOX)'));
assertTest("Giao diện hiển thị đúng mã biên nhận điện tử #{$receipt['receipt_code']}", 
    str_contains($renderedCallback, $receipt['receipt_code']));

// 5.2 MoMo IPN Endpoint (POST /payments/ipn)
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';
$_POST = [];
// Create dummy verified test IPN for response verification
$ipnJsonBody = json_encode($ipnPayload);
$ipnReq = new class($ipnPayload) extends Request {
    private array $mockJson;
    public function __construct(array $mock) {
        parent::__construct();
        $this->mockJson = $mock;
    }
    public function json(?string $key = null, mixed $default = null): mixed {
        if ($key === null) return $this->mockJson;
        return $this->mockJson[$key] ?? $default;
    }
};

ob_start();
$paymentController->ipn($ipnReq);
$ipnOutput = ob_get_clean();
$decodedIpn = json_decode($ipnOutput, true);

assertTest("PaymentController::ipn phản hồi HTTP 200 và JSON {resultCode: 0}", 
    isset($decodedIpn['resultCode']) && $decodedIpn['resultCode'] === 0);

// 5.3 Sandbox QR View (GET /payments/sandbox-qr)
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [
    'orderId' => $paymentResult['order_id'],
    'requestId' => $paymentResult['request_id'],
    'amount' => (string)$expectedDeposit,
    'orderInfo' => 'Thanh toan test',
];
ob_start();
$paymentController->sandboxQr(new Request());
$renderedQr = ob_get_clean();

assertTest("PaymentController::sandboxQr render giao diện quét mã QR MoMo Sandbox thành công", 
    str_contains($renderedQr, 'Cổng Thanh Toán MoMo Sandbox') && str_contains($renderedQr, 'Giả Lập Quét Mã'));

// Clean up test data
$db->execute("DELETE FROM tbl_receipts WHERE payment_id = ?", [(int)$paymentRecord['id']]);
$db->execute("DELETE FROM tbl_payments WHERE order_id = ?", [$paymentResult['order_id']]);
$db->execute("DELETE FROM tbl_bookings WHERE id = ?", [$bookingId]);
$db->execute("DELETE FROM tbl_service_schedules WHERE id = ?", [$scheduleId]);
echo "  [INFO] Đã dọn dẹp sạch sẽ toàn bộ bản ghi kiểm thử TV3-05.\n";

echo "\n========================================================================\n";
echo "KẾT QUẢ KIỂM THỬ: {$passedTests} / {$totalTests} TESTS PASSED\n";
if ($passedTests === $totalTests) {
    echo ">>> TẤT CẢ CÁC BÀI KIỂM THỬ TASK TV3-05 ĐẠT CHUẨN 100%! <<<\n";
    exit(0);
} else {
    echo ">>> CÓ BÀI TEST THẤT BẠI! VUI LÒNG KIỂM TRA LẠI. <<<\n";
    exit(1);
}
