<?php
/**
 * View: Phiếu Theo Dõi Hoàn Tiền (TV3-06)
 * File: app/views/refunds/show.php
 *
 * @var array $refund Chi tiết hồ sơ hoàn tiền
 * @var array $user Người dùng hiện tại
 */

use App\Helpers\FormatHelper;

$refundCode = htmlspecialchars($refund['refund_code'], ENT_QUOTES, 'UTF-8');
$bookingCode = htmlspecialchars($refund['booking_code'], ENT_QUOTES, 'UTF-8');
$amount = (float)$refund['amount'];
$status = (string)$refund['status'];

$statusMeta = match ($status) {
    'approved' => ['badge' => 'bg-success', 'label' => 'Đã phê duyệt hoàn tiền', 'icon' => 'bi-check-circle-fill'],
    'succeeded' => ['badge' => 'bg-primary', 'label' => 'Đã chuyển hoàn thành công', 'icon' => 'bi-patch-check-fill'],
    'processing' => ['badge' => 'bg-info', 'label' => 'Đang xử lý hoàn tiền', 'icon' => 'bi-arrow-repeat'],
    'requested' => ['badge' => 'bg-warning text-dark', 'label' => 'Đang chờ Ban Quản Trị duyệt', 'icon' => 'bi-hourglass-split'],
    'rejected' => ['badge' => 'bg-danger', 'label' => 'Yêu cầu hoàn tiền bị từ chối', 'icon' => 'bi-x-circle-fill'],
    default => ['badge' => 'bg-secondary', 'label' => ucfirst($status), 'icon' => 'bi-info-circle'],
};
?>

<div class="container py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/bookings/<?= (int)$refund['booking_id'] ?>" class="text-decoration-none text-muted">Đơn #<?= $bookingCode ?></a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Hoàn tiền #<?= $refundCode ?></li>
            </ol>
        </nav>

        <div>
            <a href="/bookings/<?= (int)$refund['booking_id'] ?>" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Quay Lại Đơn Đặt
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-4 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-dark text-white rounded-pill px-3 py-1 font-monospace">
                                #<?= $refundCode ?>
                            </span>
                            <span class="badge <?= $statusMeta['badge'] ?> rounded-pill px-3 py-1">
                                <i class="bi <?= $statusMeta['icon'] ?> me-1"></i> <?= $statusMeta['label'] ?>
                            </span>
                        </div>
                        <h4 class="fw-bold text-dark font-editorial mb-0">Phiếu Theo Dõi Hoàn Tiền</h4>
                        <small class="text-muted">
                            Thời gian tạo: <?= date('H:i - d/m/Y', strtotime($refund['created_at'])) ?>
                        </small>
                    </div>
                    <div class="text-md-end">
                        <small class="text-muted d-block">Số tiền hoàn:</small>
                        <span class="fs-4 fw-bold text-success font-monospace">
                            <?= FormatHelper::formatVnd($amount) ?>
                        </span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-4 mb-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Mã đơn đặt hủy:</small>
                            <strong class="text-dark">#<?= $bookingCode ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Gói trải nghiệm:</small>
                            <strong class="text-dark"><?= htmlspecialchars($refund['service_title'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Người nhận hoàn tiền:</small>
                            <strong class="text-dark"><?= htmlspecialchars($refund['tourist_name'], ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($refund['tourist_email'], ENT_QUOTES, 'UTF-8') ?>)
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Kênh hoàn tiền:</small>
                            <strong class="text-dark">Cổng MoMo Sandbox (Trả về ví/tài khoản nguồn)</strong>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Căn cứ & Lý do hoàn tiền:</h6>
                    <div class="p-3 border rounded-3 bg-light bg-opacity-25 small text-dark">
                        <?= nl2br(htmlspecialchars($refund['reason'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                </div>

                <?php if (!empty($refund['admin_note'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Ghi chú từ Ban Quản Trị:</h6>
                        <div class="p-3 border rounded-3 bg-info-subtle border-info-subtle text-dark small">
                            <i class="bi bi-shield-check text-info me-1"></i>
                            <?= nl2br(htmlspecialchars($refund['admin_note'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($refund['processed_at'])): ?>
                    <div class="text-muted small">
                        <i class="bi bi-clock-history me-1"></i> Thời điểm phê duyệt/xử lý: <strong><?= date('H:i - d/m/Y', strtotime($refund['processed_at'])) ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
