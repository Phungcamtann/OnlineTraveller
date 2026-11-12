<?php
/**
 * Host Bookings Management View
 * TV2-05: Quản lý đơn đặt tour, duyệt booking và giữ chỗ 15 phút.
 * 
 * @var array $bookings
 * @var array $stats
 * @var array $services
 * @var array $filters
 * @var array|null $user
 */
$bookings = $bookings ?? [];
$stats = $stats ?? ['total' => 0, 'pending' => 0, 'awaiting_payment' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0, 'revenue' => 0];
$services = $services ?? [];
$filters = $filters ?? ['status' => null, 'service_id' => null, 'search' => null];
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Quản trị Host</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Quản lý đặt chỗ</li>
        </ol>
    </nav>

    <!-- Header & Quick Nav -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                    <i class="bi bi-inbox-fill me-1"></i> Quản Lý Đơn Đặt Tour
                </span>
                <span class="badge bg-dark text-white rounded-pill px-2 py-1 small">
                    <?= htmlspecialchars($user['living_area'] ?? 'Địa phương', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý Đơn Đặt & Duyệt Giữ Chỗ</h2>
            <p class="text-muted small mb-0">Kiểm tra thông tin du khách, đối soát số chỗ khả dụng và duyệt giữ chỗ 15 phút thanh toán</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/host" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-speedometer2 me-1"></i> Bảng Tổng Quan
            </a>
            <a href="/host/schedules" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-calendar-check me-1"></i> Lịch Khởi Hành
            </a>
            <a href="/host/services" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2">
                <i class="bi bi-compass me-1"></i> Gói Tour Của Tôi
            </a>
        </div>
    </div>

    <!-- 5 KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center h-100">
                <small class="text-muted fw-bold d-block mb-1">TỔNG ĐƠN ĐẶT</small>
                <div class="fs-3 fw-bold text-dark font-editorial"><?= number_format($stats['total']) ?></div>
                <small class="text-muted">Tất cả yêu cầu</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center h-100 border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                    <span class="spinner-grow spinner-grow-sm text-warning" role="status" style="width: 10px; height: 10px;"></span>
                    <small class="text-warning-emphasis fw-bold">CHỜ DUYỆT (PENDING)</small>
                </div>
                <div class="fs-3 fw-bold text-warning font-editorial"><?= number_format($stats['pending']) ?></div>
                <small class="text-muted">Cần phản hồi sớm</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center h-100">
                <small class="text-info fw-bold d-block mb-1">GIỮ CHỖ 15P (AWAITING)</small>
                <div class="fs-3 fw-bold text-info font-editorial"><?= number_format($stats['awaiting_payment']) ?></div>
                <small class="text-muted">Đang chờ khách nộp tiền</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center h-100">
                <small class="text-success fw-bold d-block mb-1">ĐÃ XÁC NHẬN</small>
                <div class="fs-3 fw-bold text-success font-editorial"><?= number_format($stats['confirmed']) ?></div>
                <small class="text-muted">Sẵn sàng khởi hành</small>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-12">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center h-100">
                <small class="text-muted fw-bold d-block mb-1">DOANH THU TOUR</small>
                <div class="fs-4 fw-bold text-dark font-editorial"><?= FormatHelper::formatVnd((int)$stats['revenue']) ?></div>
                <small class="text-muted">Đã thanh toán/cọc</small>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white mb-4">
        <form method="GET" action="/host/bookings" class="row g-3 align-items-end">
            <!-- Keyword Search -->
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Tìm kiếm theo mã đơn / tên / email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" 
                           name="q" 
                           class="form-control bg-light border-start-0" 
                           placeholder="Nhập mã đơn (BK...), tên khách, SĐT..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <!-- Service Filter -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Gói trải nghiệm</label>
                <select name="service_id" class="form-select bg-light">
                    <option value="">-- Tất cả trải nghiệm --</option>
                    <?php foreach ($services as $srv): ?>
                        <option value="<?= (int)$srv['id'] ?>" <?= ($filters['service_id'] === (int)$srv['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Trạng thái đặt tour</label>
                <select name="status" class="form-select bg-light">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" <?= ($filters['status'] === 'pending') ? 'selected' : '' ?>>Chờ duyệt (pending)</option>
                    <option value="awaiting_payment" <?= ($filters['status'] === 'awaiting_payment') ? 'selected' : '' ?>>Đang giữ chỗ 15p (awaiting_payment)</option>
                    <option value="confirmed" <?= ($filters['status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận (confirmed)</option>
                    <option value="completed" <?= ($filters['status'] === 'completed') ? 'selected' : '' ?>>Hoàn thành (completed)</option>
                    <option value="cancelled" <?= ($filters['status'] === 'cancelled') ? 'selected' : '' ?>>Đã hủy (cancelled)</option>
                    <option value="rejected" <?= ($filters['status'] === 'rejected') ? 'selected' : '' ?>>Đã từ chối (rejected)</option>
                    <option value="expired" <?= ($filters['status'] === 'expired') ? 'selected' : '' ?>>Hết hạn 15p (expired)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-editorial flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc
                </button>
                <a href="/host/bookings" class="btn btn-outline-secondary rounded-pill" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bookings Table Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="fw-bold text-dark font-editorial mb-0">
                Danh Sách Yêu Cầu Đặt Chỗ (<?= number_format(count($bookings)) ?>)
            </h5>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Không tìm thấy đơn đặt tour nào phù hợp với bộ lọc.
                <div class="mt-3">
                    <a href="/host/bookings" class="btn btn-outline-dark btn-sm rounded-pill px-4">Xem tất cả đơn đặt</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 140px;">Mã đơn & Ngày</th>
                            <th style="min-width: 160px;">Du khách</th>
                            <th style="min-width: 200px;">Gói trải nghiệm</th>
                            <th style="min-width: 170px;">Khởi hành & Chỗ</th>
                            <th style="min-width: 130px;">Tổng tiền</th>
                            <th style="min-width: 140px;">Trạng thái</th>
                            <th class="text-end" style="min-width: 170px;">Thao tác Host</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <?php
                                $statusBadge = match($b['status']) {
                                    'pending' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Chờ duyệt</span>',
                                    'awaiting_payment' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1 rounded-pill"><i class="bi bi-clock-history me-1"></i>Giữ chỗ 15p</span>',
                                    'confirmed' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Đã xác nhận</span>',
                                    'completed' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill"><i class="bi bi-trophy-fill me-1"></i>Hoàn tất</span>',
                                    'cancelled' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>',
                                    'rejected' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill"><i class="bi bi-slash-circle me-1"></i>Đã từ chối</span>',
                                    'expired' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill"><i class="bi bi-alarm me-1"></i>Hết hạn 15p</span>',
                                    default => '<span class="badge bg-light text-dark border px-3 py-1 rounded-pill">' . htmlspecialchars($b['status']) . '</span>',
                                };

                                $planBadge = match($b['payment_plan']) {
                                    'deposit_50' => '<span class="badge bg-light text-primary border small">Cọc 50%</span>',
                                    'full_online' => '<span class="badge bg-light text-success border small">Trả 100%</span>',
                                    'host_direct' => '<span class="badge bg-light text-dark border small">Tiền mặt</span>',
                                    default => '<span class="badge bg-light text-secondary border small">Miễn phí</span>',
                                };
                            ?>
                            <tr>
                                <td>
                                    <a href="/host/bookings/<?= (int)$b['id'] ?>" class="fw-bold text-primary font-monospace text-decoration-none">
                                        <?= htmlspecialchars($b['booking_code']) ?>
                                    </a>
                                    <div class="small text-muted"><?= date('H:i d/m/Y', strtotime($b['created_at'])) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($b['tourist_avatar'])): ?>
                                            <img src="<?= htmlspecialchars($b['tourist_avatar']) ?>" alt="Avatar" class="rounded-circle border" width="34" height="34" style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded-circle border fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                                <?= mb_strtoupper(mb_substr($b['tourist_name'] ?? 'K', 0, 1)) ?>
                                            </span>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark small"><?= htmlspecialchars($b['tourist_name'] ?? 'Khách đặt') ?></div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($b['tourist_phone'] ?? 'Chưa có SĐT') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($b['service_title'] ?? '') ?>">
                                        <?= htmlspecialchars($b['service_title'] ?? '') ?>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($b['service_location'] ?? '') ?></small>
                                </td>
                                <td>
                                    <div class="small text-dark fw-semibold">
                                        <i class="bi bi-calendar3 me-1 text-primary"></i><?= date('d/m/Y H:i', strtotime($b['starts_at'])) ?>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="bi bi-people me-1"></i>Đăng ký: <strong><?= (int)$b['participants_count'] ?> chỗ</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= FormatHelper::formatVnd((int)$b['total_price']) ?></div>
                                    <div><?= $planBadge ?></div>
                                </td>
                                <td>
                                    <?= $statusBadge ?>
                                    <?php if ($b['status'] === 'confirmed'): ?>
                                        <?php if ($b['payment_plan'] === 'deposit_50'): ?>
                                            <div class="mt-1">
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-0" style="font-size: 0.72rem;">
                                                    <i class="bi bi-check2-circle me-1"></i>Đã cọc 50%
                                                </span>
                                            </div>
                                            <div class="small text-danger fw-semibold mt-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-cash-stack me-1"></i>Thu thêm: <?= FormatHelper::formatVnd((int)($b['total_price'] - $b['deposit_amount'])) ?>
                                            </div>
                                        <?php elseif ($b['payment_plan'] === 'full_online'): ?>
                                            <div class="mt-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-0" style="font-size: 0.72rem;">
                                                    <i class="bi bi-check2-all me-1"></i>Đã thanh toán 100%
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif ($b['status'] === 'awaiting_payment'): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-0" style="font-size: 0.72rem;">
                                                <i class="bi bi-hourglass-split me-1"></i>Chờ cọc MoMo
                                            </span>
                                        </div>
                                        <?php if (!empty($b['payment_expires_at'])): ?>
                                            <div class="small text-danger fw-semibold mt-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-stopwatch me-1"></i>Hết hạn: <?= date('H:i', strtotime($b['payment_expires_at'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <?php if ($b['status'] === 'pending'): ?>
                                            <!-- Nút Phê Duyệt (Kích hoạt 15 phút giữ chỗ) -->
                                            <form method="POST" action="/host/bookings/<?= (int)$b['id'] ?>/approve" onsubmit="return confirm('Duyệt đơn đặt tour của <?= htmlspecialchars($b['tourist_name'], ENT_QUOTES, 'UTF-8') ?>? Hệ thống sẽ giữ chỗ 15 phút để khách thanh toán.');" class="d-inline">
                                                <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" title="Duyệt đơn và giữ chỗ 15 phút">
                                                    <i class="bi bi-check-circle me-1"></i> Duyệt
                                                </button>
                                            </form>

                                            <!-- Nút Từ Chối -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejectModal<?= (int)$b['id'] ?>" 
                                                    title="Từ chối yêu cầu đặt tour">
                                                <i class="bi bi-x-circle me-1"></i> Từ chối
                                            </button>

                                            <!-- Modal Từ chối -->
                                            <div class="modal fade" id="rejectModal<?= (int)$b['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 border-0 shadow">
                                                        <form method="POST" action="/host/bookings/<?= (int)$b['id'] ?>/reject">
                                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                            <div class="modal-header border-bottom-0 pb-0">
                                                                <h5 class="modal-title fw-bold text-dark font-editorial">Từ Chối Đơn Đặt Tour</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <p class="small text-muted mb-3">
                                                                    Bạn đang từ chối đơn đặt <strong>#<?= htmlspecialchars($b['booking_code']) ?></strong> của khách <strong><?= htmlspecialchars($b['tourist_name']) ?></strong>.
                                                                </p>
                                                                <div class="mb-3">
                                                                    <label class="form-label small fw-bold text-dark">Lý do từ chối đón tiếp:</label>
                                                                    <textarea name="reason" class="form-control" rows="3" placeholder="Ví dụ: Đợt này thời tiết không thuận lợi, trùng lịch đón đoàn khác..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top-0 pt-0">
                                                                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-danger rounded-pill px-4">Xác Nhận Từ Chối</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <a href="/host/bookings/<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Xem chi tiết
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
