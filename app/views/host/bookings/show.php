<?php
/**
 * Host Booking Detail View
 * TV2-05: Chi tiết đơn đặt tour phía Host.
 * 
 * @var array $booking
 * @var array|null $user
 */
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Quản trị Host</a></li>
            <li class="breadcrumb-item"><a href="/host/bookings" class="text-decoration-none text-muted">Quản lý đặt chỗ</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">#<?= htmlspecialchars($booking['booking_code']) ?></li>
        </ol>
    </nav>

    <!-- Top Action Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-light text-primary border font-monospace px-3 py-1 rounded-pill">
                        Mã đơn: <?= htmlspecialchars($booking['booking_code']) ?>
                    </span>
                    <?php
                        $statusBadge = match($booking['status']) {
                            'pending' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Chờ Host Duyệt</span>',
                            'awaiting_payment' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1 rounded-pill"><i class="bi bi-clock-history me-1"></i>Đang Giữ Chỗ 15 Phút</span>',
                            'confirmed' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Đã Xác Nhận</span>',
                            'completed' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill"><i class="bi bi-trophy-fill me-1"></i>Hoàn Tất</span>',
                            'cancelled' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i>Đã Hủy</span>',
                            'rejected' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill"><i class="bi bi-slash-circle me-1"></i>Đã Từ Chối</span>',
                            'expired' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill"><i class="bi bi-alarm me-1"></i>Hết Hạn Giữ Chỗ 15 Phút</span>',
                            default => '<span class="badge bg-light text-dark border px-3 py-1 rounded-pill">' . htmlspecialchars($booking['status']) . '</span>',
                        };
                        echo $statusBadge;
                    ?>
                </div>
                <h3 class="font-editorial fw-bold text-dark mb-0">Hồ Sơ Yêu Cầu Đặt Chỗ</h3>
                <small class="text-muted">Khách đặt lúc: <?= date('H:i, d/m/Y', strtotime($booking['created_at'])) ?></small>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <?php if ($booking['status'] === 'pending'): ?>
                    <form method="POST" action="/host/bookings/<?= (int)$booking['id'] ?>/approve" onsubmit="return confirm('Duyệt đơn và giữ chỗ 15 phút cho khách thanh toán?');" class="d-inline">
                        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-check-circle me-1"></i> Phê Duyệt Ngay
                        </button>
                    </form>
                <?php endif; ?>
                <a href="/host/bookings" class="btn btn-outline-dark rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Quay Lại Danh Sách
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Thông tin Du khách & Trải nghiệm -->
        <div class="col-lg-8">
            <!-- Thông tin du khách -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark font-editorial mb-3">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Thông Tin Du Khách Đặt Chỗ
                </h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1">Họ và tên khách</small>
                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($booking['tourist_name'] ?? 'Du khách') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1">Số điện thoại liên hệ</small>
                        <div class="fw-bold text-dark fs-6">
                            <i class="bi bi-telephone me-1 text-success"></i><?= htmlspecialchars($booking['tourist_phone'] ?? 'Chưa cung cấp') ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1">Email</small>
                        <div class="text-dark">
                            <i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($booking['tourist_email'] ?? '') ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1">Số lượng khách đăng ký</small>
                        <div class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-1 rounded-pill">
                            <i class="bi bi-people-fill me-1"></i><?= (int)$booking['participants_count'] ?> người tham gia
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gói trải nghiệm & Lịch khởi hành -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark font-editorial mb-3">
                    <i class="bi bi-compass me-2 text-primary"></i>Gói Trải Nghiệm & Lịch Khởi Hành
                </h5>
                <div class="p-3 bg-light rounded-4 mb-3">
                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($booking['service_title']) ?></h6>
                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($booking['service_location']) ?></small>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1">Thời gian khởi hành</small>
                        <div class="fw-bold text-primary">
                            <i class="bi bi-clock me-1"></i><?= date('H:i - d/m/Y', strtotime($booking['starts_at'])) ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block mb-1">Thời gian kết thúc</small>
                        <div class="fw-bold text-secondary">
                            <i class="bi bi-clock-history me-1"></i><?= date('H:i - d/m/Y', strtotime($booking['ends_at'])) ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block mb-1">Điểm hẹn đón khách</small>
                        <div class="text-dark bg-white p-2 rounded border">
                            <i class="bi bi-pin-map-fill text-danger me-1"></i><?= htmlspecialchars($booking['meeting_point'] ?? 'Theo thỏa thuận của Host') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thanh toán & Giữ chỗ 15 phút -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark font-editorial mb-3">
                    <i class="bi bi-receipt me-2 text-primary"></i>Báo Giá & Thanh Toán
                </h5>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Đơn giá / người:</span>
                    <span class="fw-semibold text-dark"><?= FormatHelper::formatVnd((int)$booking['unit_price']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Số lượng khách:</span>
                    <span class="fw-semibold text-dark">&times; <?= (int)$booking['participants_count'] ?></span>
                </div>
                <?php if ((int)$booking['discount_amount'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Khuyến mãi áp dụng:</span>
                        <span>-<?= FormatHelper::formatVnd((int)$booking['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="fw-bold text-dark">Tổng tiền tour:</span>
                    <span class="fs-4 fw-bold text-primary font-editorial"><?= FormatHelper::formatVnd((int)$booking['total_price']) ?></span>
                </div>

                <!-- Kế hoạch trả -->
                <div class="p-3 bg-light rounded-4 mb-3">
                    <small class="text-muted fw-bold d-block mb-1">KẾ HOẠCH THANH TOÁN</small>
                    <div class="fw-bold text-dark mb-1">
                        <?php
                            echo match($booking['payment_plan']) {
                                'deposit_50' => 'Đặt cọc 50% trực tuyến (MoMo Sandbox)',
                                'full_online' => 'Thanh toán toàn bộ 100% qua MoMo Sandbox',
                                'host_direct' => 'Thanh toán trực tiếp bằng tiền mặt khi đón khách',
                                default => 'Trải nghiệm miễn phí',
                            };
                        ?>
                    </div>
                    <?php if ((int)$booking['deposit_amount'] > 0): ?>
                        <div class="small text-muted">
                            Cần cọc trước: <strong><?= FormatHelper::formatVnd((int)$booking['deposit_amount']) ?></strong>
                            <?php if ((int)$booking['remaining_amount'] > 0): ?>
                                <br>Còn lại thanh toán sau: <strong><?= FormatHelper::formatVnd((int)$booking['remaining_amount']) ?></strong>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 15-Minute Countdown if Awaiting Payment -->
                <?php if ($booking['status'] === 'awaiting_payment' && !empty($booking['payment_expires_at'])): ?>
                    <div class="p-3 rounded-4 bg-warning-subtle border border-warning text-center">
                        <small class="text-warning-emphasis fw-bold d-block mb-1">
                            <i class="bi bi-stopwatch-fill me-1"></i>CỬA SỔ GIỮ CHỖ 15 PHÚT
                        </small>
                        <div class="fs-5 fw-bold text-danger font-monospace">
                            Hết hạn: <?= date('H:i', strtotime($booking['payment_expires_at'])) ?>
                        </div>
                        <small class="text-muted">
                            Nếu du khách không thanh toán trước thời điểm này, hệ thống sẽ tự động hủy đơn và hoàn trả chỗ ngồi.
                        </small>
                    </div>
                <?php endif; ?>

                <?php if (!empty($booking['rejection_reason'])): ?>
                    <div class="p-3 rounded-4 bg-danger-subtle border border-danger small text-danger mt-3">
                        <strong>Lý do từ chối:</strong> <?= htmlspecialchars($booking['rejection_reason']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($booking['cancel_reason'])): ?>
                    <div class="p-3 rounded-4 bg-secondary-subtle border border-secondary small text-muted mt-3">
                        <strong>Lý do hủy:</strong> <?= htmlspecialchars($booking['cancel_reason']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
