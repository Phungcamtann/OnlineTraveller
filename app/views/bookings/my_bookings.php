<?php
/**
 * View: Danh Sách Chuyến Đi Của Tôi (Du Khách)
 * File: app/views/bookings/my_bookings.php
 *
 * @var array $bookings Danh sách các đơn đặt chỗ của du khách
 * @var string|null $status Bộ lọc trạng thái đang chọn
 * @var array $user Thông tin người dùng
 */
?>

<div class="container py-4">
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Chuyến Đi Của Tôi</li>
        </ol>
    </nav>

    <!-- TITLE -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold font-editorial text-dark mb-1">Chuyến Đi & Đơn Đặt Chỗ Của Tôi</h3>
            <p class="text-muted small mb-0">Theo dõi tiến độ duyệt chỗ, thanh toán cọc và lịch trình cùng Dân bản địa.</p>
        </div>
        <a href="/services" class="btn btn-dark rounded-pill px-4 py-2">
            <i class="bi bi-compass me-1"></i> Khám Phá Tour Mới
        </a>
    </div>

    <!-- FILTER TABS -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="/my-bookings" class="btn btn-sm rounded-pill px-3 py-1 <?= empty($status) ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Tất Cả (<?= count($bookings) ?>)
        </a>
        <a href="/my-bookings?status=pending" class="btn btn-sm rounded-pill px-3 py-1 <?= $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-secondary' ?>">
            <i class="bi bi-hourglass-split me-1"></i> Chờ Duyệt
        </a>
        <a href="/my-bookings?status=awaiting_payment" class="btn btn-sm rounded-pill px-3 py-1 <?= $status === 'awaiting_payment' ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <i class="bi bi-clock-history me-1"></i> Chờ Thanh Toán
        </a>
        <a href="/my-bookings?status=confirmed" class="btn btn-sm rounded-pill px-3 py-1 <?= $status === 'confirmed' ? 'btn-success' : 'btn-outline-secondary' ?>">
            <i class="bi bi-check-circle me-1"></i> Đã Xác Nhận
        </a>
        <a href="/my-bookings?status=cancelled" class="btn btn-sm rounded-pill px-3 py-1 <?= $status === 'cancelled' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
            <i class="bi bi-x-circle me-1"></i> Đã Hủy
        </a>
    </div>

    <!-- BOOKINGS LIST -->
    <?php if (empty($bookings)): ?>
        <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mx-auto mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
            </div>
            <h5 class="fw-bold text-dark font-editorial mb-2">Chưa Có Chuyến Đi Nào</h5>
            <p class="text-muted small mx-auto mb-4" style="max-width: 460px;">
                Bạn chưa gửi yêu cầu đặt chỗ nào trong mục này. Hãy cùng người dân bản địa khám phá những địa điểm tuyệt đẹp nhé!
            </p>
            <div>
                <a href="/services" class="btn btn-dark rounded-pill px-4 py-2">
                    <i class="bi bi-compass me-1"></i> Khám Phá Trải Nghiệm Ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="vstack gap-3">
            <?php foreach ($bookings as $b): ?>
                <?php 
                    $bCode = htmlspecialchars($b['booking_code'], ENT_QUOTES, 'UTF-8');
                    $bStatus = (string)$b['status'];
                    $bStart = date('H:i - d/m/Y', strtotime($b['starts_at']));
                ?>
                <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white">
                    <div class="row g-3 align-items-center">
                        <!-- Thumbnail -->
                        <div class="col-sm-3 col-md-2">
                            <?php if (!empty($b['service_banner'])): ?>
                                <img src="<?= htmlspecialchars($b['service_banner'], ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($b['service_title'], ENT_QUOTES, 'UTF-8') ?>" 
                                     class="rounded-3 w-100 object-fit-cover shadow-sm"
                                     style="height: 100px;">
                            <?php else: ?>
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="height: 100px;">
                                    <i class="bi bi-image fs-3"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info -->
                        <div class="col-sm-6 col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-dark text-white rounded-pill px-2 py-1 font-monospace" style="font-size: 0.72rem;">
                                    #<?= $bCode ?>
                                </span>
                                <?php if ($bStatus === 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill">⏳ Chờ Host Duyệt</span>
                                <?php elseif ($bStatus === 'awaiting_payment'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">⚡ Chờ Thanh Toán Cọc</span>
                                <?php elseif ($bStatus === 'confirmed'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">✅ Đã Xác Nhận</span>
                                <?php elseif ($bStatus === 'cancelled'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">❌ Đã Hủy</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border rounded-pill"><?= htmlspecialchars($bStatus) ?></span>
                                <?php endif; ?>
                            </div>

                            <h5 class="fw-bold font-editorial text-dark mb-1">
                                <a href="/bookings/<?= (int)$b['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($b['service_title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h5>

                            <div class="small text-muted mb-0">
                                <span><i class="bi bi-clock me-1 text-primary"></i> Khởi hành: <strong><?= $bStart ?></strong></span> • 
                                <span><i class="bi bi-people me-1"></i> <?= (int)$b['participants_count'] ?> khách</span> • 
                                <span><i class="bi bi-person me-1"></i> Host: <?= htmlspecialchars($b['host_name'] ?? 'Dân bản địa', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="col-sm-3 col-md-3 text-sm-end border-start-sm">
                            <small class="text-muted d-block">TỔNG TIỀN</small>
                            <h5 class="fw-bold text-dark font-editorial mb-1">
                                <?= FormatHelper::formatVnd((int)$b['total_price']) ?>
                            </h5>
                            <small class="text-success d-block mb-2">
                                Cọc: <?= FormatHelper::formatVnd((int)$b['deposit_amount']) ?>
                            </small>
                            <a href="/bookings/<?= (int)$b['id'] ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 w-100 w-sm-auto">
                                Xem Chi Tiết <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
