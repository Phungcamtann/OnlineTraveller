<?php
/**
 * View: Biên Nhận Điện Tử Thanh Toán MoMo Sandbox (TV3-06)
 * File: app/views/receipts/show.php
 *
 * @var array $receipt Chi tiết biên nhận kèm liên kết booking, service, tourist, host
 * @var array $user Thông tin người dùng hiện tại
 */

use App\Helpers\FormatHelper;

$code = htmlspecialchars($receipt['receipt_code'], ENT_QUOTES, 'UTF-8');
$bookingCode = htmlspecialchars($receipt['booking_code'], ENT_QUOTES, 'UTF-8');
$momoTrans = htmlspecialchars($receipt['momo_trans_id'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$orderId = htmlspecialchars($receipt['order_id'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$amount = (float)$receipt['receipt_amount'];
$totalPrice = (float)$receipt['total_price'];
$remaining = (float)$receipt['remaining_amount'];
$count = (int)$receipt['participants_count'];
$unitPrice = (float)$receipt['unit_price'];
?>

<div class="container py-4">
    <!-- TOP TOOLBAR (NO PRINT) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 d-print-none">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/bookings/<?= (int)$receipt['booking_id'] ?>" class="text-decoration-none text-muted">Đơn #<?= $bookingCode ?></a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Biên nhận #<?= $code ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <a href="/" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
                <i class="bi bi-house me-1"></i> Trang Chủ
            </a>
            <a href="/bookings/<?= (int)$receipt['booking_id'] ?>" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Chi Tiết Đơn Đặt
            </a>
            <button type="button" class="btn btn-dark rounded-pill px-4 py-1 btn-sm fw-semibold shadow-sm" onclick="window.print();">
                <i class="bi bi-printer me-1"></i> In Biên Nhận / Lưu PDF
            </button>
        </div>
    </div>

    <!-- MAIN RECEIPT DOCUMENT -->
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white position-relative overflow-hidden print-document">
                <!-- WATERMARK STAMP -->
                <div class="position-absolute end-0 top-0 p-4 pe-5 opacity-25 d-none d-md-block pointer-events-none" style="transform: rotate(-12deg); z-index: 1;">
                    <div class="border border-success border-4 rounded-4 px-4 py-2 text-success text-center">
                        <div class="fs-4 fw-black text-uppercase tracking-wider">PAID & VERIFIED</div>
                        <small class="fw-bold font-monospace">MOMO SANDBOX GATEWAY</small>
                    </div>
                </div>

                <!-- RECEIPT HEADER -->
                <div class="row align-items-start border-bottom pb-4 mb-4">
                    <div class="col-sm-7 mb-3 mb-sm-0">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fs-4 fw-bold text-dark font-editorial">OnlineTraveller</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0 small fw-bold">
                                MoMo Sandbox
                            </span>
                        </div>
                        <p class="small text-muted mb-0">
                            Hệ thống kết nối Du khách & Trải nghiệm Bản địa Việt Nam.<br>
                            Chứng từ điện tử lưu hành nội bộ hệ thống OnlineTraveller.
                        </p>
                    </div>
                    <div class="col-sm-5 text-sm-end">
                        <span class="badge bg-dark text-white rounded-pill px-3 py-1 font-monospace mb-2">
                            #<?= $code ?>
                        </span>
                        <h4 class="fw-bold text-dark font-editorial mb-1">BIÊN NHẬN ĐIỆN TỬ</h4>
                        <small class="text-muted d-block">
                            Thời gian: <?= date('H:i - d/m/Y', strtotime($receipt['issued_at'])) ?>
                        </small>
                    </div>
                </div>

                <!-- 2 COLUMNS: TOURIST & HOST DETAILS -->
                <div class="row g-4 mb-4 pb-3 border-bottom">
                    <div class="col-sm-6">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Người Đại Diện Đặt Tour</h6>
                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($receipt['tourist_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($receipt['tourist_email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php if (!empty($receipt['tourist_phone'])): ?>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($receipt['tourist_phone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h6 class="text-uppercase text-muted small fw-bold mb-2">Dân Bản Địa Tiếp Đón (Host)</h6>
                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($receipt['host_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($receipt['service_location'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php if (!empty($receipt['host_phone'])): ?>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($receipt['host_phone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TOUR EXPERIENCE SUMMARY -->
                <div class="p-3 rounded-4 bg-light mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <span class="badge bg-secondary rounded-pill px-2 py-0 small mb-1">Gói trải nghiệm</span>
                            <h5 class="fw-bold text-dark mb-1 font-editorial"><?= htmlspecialchars($receipt['service_title'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check me-1"></i> Khởi hành: <strong><?= date('H:i - d/m/Y', strtotime($receipt['starts_at'])) ?></strong>
                            </small>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="small text-muted">Điểm tập trung:</div>
                            <strong class="text-dark small"><?= htmlspecialchars($receipt['meeting_point'] ?? $receipt['service_location'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    </div>
                </div>

                <!-- TRANSACTION & PRICING TABLE -->
                <div class="table-responsive mb-4">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Nội dung thanh toán</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">Phí tham gia trải nghiệm bản địa</div>
                                    <small class="text-muted">Mã đơn đặt: #<?= $bookingCode ?> (Gói: <?= $receipt['payment_plan'] === 'deposit_50' ? 'Cọc 50%' : 'Toàn phần 100%' ?>)</small>
                                </td>
                                <td class="text-center font-monospace"><?= $count ?> người</td>
                                <td class="text-end font-monospace"><?= FormatHelper::formatVnd($unitPrice) ?></td>
                                <td class="text-end font-monospace fw-semibold"><?= FormatHelper::formatVnd($unitPrice * $count) ?></td>
                            </tr>
                            <?php if (!empty($receipt['discount_amount']) && (float)$receipt['discount_amount'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-muted small">Khuyến mãi coupon giảm giá:</td>
                                    <td class="text-end text-success font-monospace">-<?= FormatHelper::formatVnd((float)$receipt['discount_amount']) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php
                            $vatInfo = FormatHelper::calculateVat($totalPrice, 0.08);
                            $netAmount = $vatInfo['net'];
                            $vatAmount = $vatInfo['vat'];
                            ?>
                            <tr>
                                <td colspan="3" class="text-end text-muted small">
                                    Tiền dịch vụ trước thuế (Net):
                                </td>
                                <td class="text-end font-monospace small text-muted">
                                    <?= FormatHelper::formatVnd($netAmount) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end text-muted small">
                                    Thuế GTGT (VAT 8%):
                                </td>
                                <td class="text-end font-monospace small text-muted">
                                    <?= FormatHelper::formatVnd($vatAmount) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-semibold text-dark">
                                    Tổng chi phí gói tour (Đã bao gồm 8% thuế GTGT):
                                </td>
                                <td class="text-end font-monospace fw-bold text-dark fs-6"><?= FormatHelper::formatVnd($totalPrice) ?></td>
                            </tr>
                            <tr class="table-success bg-opacity-25">
                                <td colspan="3" class="text-end fw-bold text-success">
                                    <i class="bi bi-check-circle-fill me-1"></i> Số tiền đã thanh toán trực tuyến (MoMo Sandbox):
                                </td>
                                <td class="text-end font-monospace fw-bold text-success fs-5">
                                    <?= FormatHelper::formatVnd($amount) ?>
                                </td>
                            </tr>
                            <?php if ($remaining > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-muted small">
                                        Số tiền còn lại (Du khách thanh toán trực tiếp cho Host khi tham gia tour):
                                    </td>
                                    <td class="text-end font-monospace fw-semibold text-danger">
                                        <?= FormatHelper::formatVnd($remaining) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TRANSACTION AUDIT AUDIT TRAIL -->
                <div class="p-3 border rounded-3 bg-light bg-opacity-50 small mb-4">
                    <div class="row g-2 font-monospace">
                        <div class="col-sm-6">
                            <span class="text-muted">Mã đơn cổng MoMo:</span> <strong><?= $orderId ?></strong>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-muted">Mã giao dịch MoMo:</span> <strong class="text-primary"><?= $momoTrans ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted">Phương thức:</span> <strong>MoMo Sandbox (QR / Gateway)</strong>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-muted">Trạng thái:</span> <span class="badge bg-success">SUCCEEDED</span>
                        </div>
                    </div>
                </div>

                <!-- FOOTER NOTICE -->
                <div class="border-top pt-3 text-center text-muted small">
                    <p class="mb-1">
                        Biên nhận điện tử này được hệ thống OnlineTraveller tự động phát hành và lưu trữ sau khi xác thực chữ ký HMAC-SHA256 hợp lệ từ MoMo.
                    </p>
                    <small>Mọi thắc mắc hoặc yêu cầu trợ giúp, vui lòng liên hệ Ban Quản Trị hoặc Host bản địa trước ngày khởi hành.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .d-print-none {
        display: none !important;
    }
    .print-document {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        padding: 0 !important;
    }
}
</style>
