<?php
/**
 * MoMo Return Callback View
 * TV3-05: Trang hiển thị kết quả giao dịch thanh toán MoMo Sandbox.
 * 
 * @var string $orderId
 * @var string $resultCode
 * @var float $amount
 * @var string $transId
 * @var string $message
 * @var array|null $payment
 * @var array|null $receipt
 * @var array|null $user
 */

$isSuccess = ($resultCode === '0' || ($payment && $payment['status'] === 'succeeded'));
?>

<div class="container py-5" style="max-width: 680px;">
    <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white text-center">
        <?php if ($isSuccess): ?>
            <!-- Icon Thành công -->
            <div class="mb-4 d-inline-flex align-items-center justify-content-center mx-auto bg-success-subtle text-success rounded-circle" style="width: 80px; height: 80px;">
                <i class="bi bi-check-circle-fill fs-1"></i>
            </div>

            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold mb-2">
                THANH TOÁN THÀNH CÔNG (MOMO SANDBOX)
            </span>
            <h3 class="font-editorial fw-bold text-dark mb-2">Giao Dịch Đã Hoàn Tất</h3>
            <p class="text-muted small mb-4">
                Khoản cọc giữ chỗ của bạn đã được cổng thanh toán MoMo Sandbox ghi nhận. Chuyến đi đã được xác nhận chính thức!
            </p>

            <!-- Bảng chi tiết hóa đơn -->
            <div class="bg-light rounded-4 p-3 text-start mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Mã đơn hàng:</span>
                    <span class="fw-bold text-dark font-monospace small"><?= htmlspecialchars($orderId) ?></span>
                </div>
                <?php if (!empty($transId)): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Mã giao dịch MoMo:</span>
                        <span class="text-dark font-monospace small"><?= htmlspecialchars($transId) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($receipt && !empty($receipt['receipt_code'])): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span class="small fw-semibold">Biên nhận điện tử:</span>
                        <span class="fw-bold font-monospace small">#<?= htmlspecialchars($receipt['receipt_code']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="fw-bold text-dark">Số tiền đã cọc:</span>
                    <span class="fw-bold text-primary fs-5"><?= FormatHelper::formatVnd((int)$amount) ?></span>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <?php if ($payment && !empty($payment['booking_id'])): ?>
                    <a href="/bookings/<?= (int)$payment['booking_id'] ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-ticket-detailed me-1"></i> Xem Chi Tiết Vé & Lịch Trình
                    </a>
                <?php endif; ?>
                <a href="/my-bookings" class="btn btn-outline-dark rounded-pill px-4 py-2">
                    <i class="bi bi-briefcase me-1"></i> Chuyến Đi Của Tôi
                </a>
            </div>
        <?php else: ?>
            <!-- Icon Thất bại / Hủy -->
            <div class="mb-4 d-inline-flex align-items-center justify-content-center mx-auto bg-danger-subtle text-danger rounded-circle" style="width: 80px; height: 80px;">
                <i class="bi bi-exclamation-octagon-fill fs-1"></i>
            </div>

            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold mb-2">
                CHƯA HOÀN TẤT THANH TOÁN
            </span>
            <h3 class="font-editorial fw-bold text-dark mb-2">Giao Dịch Đã Bị Hủy Hoặc Gián Đoạn</h3>
            <p class="text-muted small mb-4">
                <?= !empty($message) ? htmlspecialchars($message) : 'Bạn chưa hoàn tất thanh toán cọc trên cổng MoMo Sandbox hoặc đã chủ động hủy giao dịch.' ?>
            </p>

            <div class="bg-light rounded-4 p-3 text-start mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Mã đơn hàng:</span>
                    <span class="fw-bold text-dark font-monospace small"><?= htmlspecialchars($orderId) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Mã phản hồi MoMo:</span>
                    <span class="text-danger font-monospace small"><?= htmlspecialchars($resultCode) ?></span>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <?php if ($payment && !empty($payment['booking_id'])): ?>
                    <a href="/payments/checkout?booking_id=<?= (int)$payment['booking_id'] ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i> Thử Thanh Toán Lại
                    </a>
                    <a href="/bookings/<?= (int)$payment['booking_id'] ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                        Quay Lại Đơn Đặt
                    </a>
                <?php else: ?>
                    <a href="/my-bookings" class="btn btn-outline-dark rounded-pill px-4 py-2">
                        Về Chuyến Đi Của Tôi
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
