<?php
/**
 * MoMo Sandbox Simulation QR Checkout View
 * TV3-05: Giao diện giả lập thanh toán MoMo Sandbox QR Code trong môi trường phát triển & kiểm thử.
 * 
 * @var string $orderId
 * @var string $requestId
 * @var float $amount
 * @var string $orderInfo
 * @var array|null $payment
 * @var array|null $user
 */

use App\Integrations\MomoClient;

$momoClient = new MomoClient();
$transId = 'MOMO_' . time() . '_' . rand(100, 999);
$responseTime = (int)(microtime(true) * 1000);
$gatewayUrl = $gatewayUrl ?? '';

// Build proper extraData if booking and user are known
$bookingId = $payment ? (int)($payment['booking_id'] ?? 0) : 0;
$touristId = $user ? (int)($user['id'] ?? 0) : 0;
$extraData = ($bookingId > 0 && $touristId > 0)
    ? base64_encode((string)json_encode(['booking_id' => $bookingId, 'tourist_id' => $touristId]))
    : '';

// Pre-calculate valid HMAC-SHA256 signature for the simulated IPN webhook
$ipnData = [
    'partnerCode' => $momoClient->getPartnerCode(),
    'orderId' => $orderId,
    'requestId' => $requestId,
    'amount' => (int)round($amount),
    'orderInfo' => $orderInfo,
    'orderType' => 'momo_wallet',
    'transId' => $transId,
    'resultCode' => 0,
    'message' => 'Thành công.',
    'payType' => 'qr',
    'responseTime' => $responseTime,
    'extraData' => $extraData,
];
$signature = $momoClient->generateIpnSignature($ipnData);
$ipnData['signature'] = $signature;
?>

<div class="container py-5" style="max-width: 600px;">
    <div class="card border-0 rounded-4 shadow-lg overflow-hidden bg-white">
        <!-- Header màu MoMo -->
        <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #a50064 0%, #d82d8b 100%);">
            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle p-2 mb-2 shadow-sm" style="width: 52px; height: 52px;">
                <i class="bi bi-wallet2 fs-3" style="color: #a50064;"></i>
            </div>
            <h4 class="fw-bold mb-1">Cổng Thanh Toán MoMo Sandbox</h4>
            <span class="badge bg-white text-dark rounded-pill px-3 py-1 small">
                Môi trường Giả lập & Kiểm thử (API v2)
            </span>
        </div>

        <div class="card-body p-4 p-md-5 text-center">
            <!-- Số tiền thanh toán -->
            <small class="text-muted fw-bold d-block mb-1">SỐ TIỀN CẦN THANH TOÁN</small>
            <div class="fs-1 fw-bold font-editorial text-dark mb-3" style="color: #a50064 !important;">
                <?= FormatHelper::formatVnd((int)$amount) ?>
            </div>

            <!-- Khung QR Code mô phỏng -->
            <div class="p-4 bg-light rounded-4 border d-inline-block mx-auto mb-4 position-relative" style="max-width: 260px;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('momo://payment?orderId=' . $orderId . '&amount=' . (int)$amount) ?>" 
                     alt="MoMo Sandbox QR Code" 
                     width="200" 
                     height="200" 
                     class="img-fluid rounded-3"
                     onerror="this.src='/assets/images/logo.svg';">
                <div class="small text-muted mt-2">
                    <i class="bi bi-qr-code-scan me-1"></i> Quét mã qua App MoMo Test
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="bg-light rounded-4 p-3 text-start small mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Mã đơn hàng:</span>
                    <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($orderId) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Nội dung:</span>
                    <span class="text-dark"><?= htmlspecialchars($orderInfo) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Mã định danh MoMo:</span>
                    <span class="font-monospace text-secondary">MOMOBKUN20180529</span>
                </div>
            </div>

            <!-- Nút hành động giả lập -->
            <div class="d-grid gap-2">
                <button type="button" id="btn-pay-success" class="btn btn-lg text-white rounded-pill shadow" style="background: linear-gradient(135deg, #a50064, #d82d8b); font-weight: 600;">
                    <i class="bi bi-check-circle-fill me-1"></i> Giả Lập Quét Mã & Thanh Toán Thành Công
                </button>
                <a href="/payments/callback?orderId=<?= urlencode($orderId) ?>&resultCode=1006&message=<?= urlencode('Giao dịch bị hủy bởi người dùng') ?>" class="btn btn-outline-secondary rounded-pill">
                    Hủy Giao Dịch
                </a>
            </div>

            <?php if (!empty($gatewayUrl)): ?>
                <div class="mt-3 pt-3 border-top text-center">
                    <a href="<?= htmlspecialchars($gatewayUrl) ?>" target="_blank" class="small text-muted text-decoration-none">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Mở cổng MoMo Sandbox chính thức (dành cho thiết bị có App MoMo Test)
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer bg-light text-center small text-muted py-3">
            Hệ thống tự động kích hoạt IPN Webhook có chữ ký bảo mật <strong>HMAC-SHA256</strong> chuẩn MoMo.
        </div>
    </div>
</div>

<script>
const ipnPayload = <?= json_encode($ipnData, JSON_UNESCAPED_UNICODE) ?>;

document.getElementById('btn-pay-success').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang gửi xác thực IPN HMAC-SHA256...';

    // Send verified IPN webhook to /payments/ipn
    fetch('/payments/ipn', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(ipnPayload)
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (res.ok && (data.resultCode === 0 || data.success)) {
            // Redirect browser to callback
            window.location.href = '/payments/callback?orderId=' + encodeURIComponent('<?= $orderId ?>') +
                '&resultCode=0&transId=' + encodeURIComponent('<?= $transId ?>') +
                '&amount=' + encodeURIComponent('<?= (int)$amount ?>');
        } else {
            alert('Lỗi xử lý IPN: ' + (data.message || 'Không thể xác thực giao dịch.'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Thử Lại';
        }
    })
    .catch(err => {
        alert('Lỗi kết nối tới webhook IPN: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Thử Lại';
    });
});
</script>
