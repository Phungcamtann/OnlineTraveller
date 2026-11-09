<?php
/** @var array $services */
/** @var array $schedules */
/** @var array $metrics */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Breadcrumb & Flash Messages -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Bảng Quản Trị Host</li>
        </ol>
    </nav>

    <!-- Top Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                    <i class="bi bi-person-badge-fill me-1"></i> Cổng Dân Bản Địa (Local Host)
                </span>
                <span class="badge bg-dark text-white rounded-pill px-2 py-1 small">
                    <?= htmlspecialchars($user['living_area'] ?? 'Địa phương', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <h2 class="font-editorial fw-bold text-dark mb-0">Không Gian Quản Lý Trải Nghiệm Bản Địa</h2>
            <p class="text-muted small mb-0">
                Xin chào <strong><?= htmlspecialchars($user['name'] ?? 'Host', ENT_QUOTES, 'UTF-8') ?></strong>! Chúc bạn có một ngày đón tiếp du khách thật tuyệt vời.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="/host/bookings" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-inbox-fill me-1"></i> Quản Lý Đặt Chỗ
            </a>
            <a href="/host/schedules/create" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-calendar-plus me-1"></i> Mở Lịch Khởi Hành
            </a>
            <a href="/host/services/create" class="btn btn-editorial rounded-pill px-3 py-2 shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Đăng Trải Nghiệm Mới
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">TỔNG TRẢI NGHIỆM</small>
                    <span class="badge bg-primary-subtle text-primary rounded-circle p-2">
                        <i class="bi bi-compass fs-5"></i>
                    </span>
                </div>
                <div class="fs-2 fw-bold text-dark font-editorial"><?= (int)($metrics['total_services'] ?? 0) ?></div>
                <small class="text-muted">
                    <a href="/host/services" class="text-decoration-none text-primary">Xem tất cả gói tour &rarr;</a>
                </small>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">HOMESTAY NHÀ DÂN</small>
                    <span class="badge bg-success-subtle text-success rounded-circle p-2">
                        <i class="bi bi-house-heart fs-5"></i>
                    </span>
                </div>
                <div class="fs-2 fw-bold text-success font-editorial"><?= (int)($metrics['homestays_count'] ?? 0) ?></div>
                <small class="text-muted">Gói tour có đính kèm lưu trú</small>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">LỊCH ĐANG MỞ BÁN</small>
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-circle p-2">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </span>
                </div>
                <div class="fs-2 fw-bold text-dark font-editorial"><?= (int)($metrics['active_schedules_count'] ?? 0) ?></div>
                <small class="text-muted">
                    <a href="/host/schedules" class="text-decoration-none text-warning-emphasis">Quản lý lịch trình &rarr;</a>
                </small>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">LƯỢT KHÁCH ĐẶT</small>
                    <span class="badge bg-info-subtle text-info-emphasis rounded-circle p-2">
                        <i class="bi bi-people-fill fs-5"></i>
                    </span>
                </div>
                <div class="fs-2 fw-bold text-info font-editorial"><?= (int)($metrics['total_bookings'] ?? 0) ?></div>
                <small class="text-muted">
                    <a href="/host/bookings" class="text-decoration-none text-info-emphasis">Duyệt & xử lý đơn &rarr;</a>
                </small>
            </div>
        </div>
    </div>

    <!-- Section 1: Host Services Overview -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold font-editorial text-dark mb-1">Gói Trải Nghiệm Của Tôi</h5>
                <p class="text-muted small mb-0">Các tour văn hóa, ẩm thực, trekking do bạn tự thiết kế và hướng dẫn.</p>
            </div>
            <a href="/host/services" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Xem tất cả (<?= count($services) ?>)
            </a>
        </div>

        <?php if (empty($services)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-compass fs-1 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-2">Bạn chưa đăng ký gói trải nghiệm bản địa nào.</p>
                <a href="/host/services/create" class="btn btn-editorial btn-sm rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Tạo trải nghiệm đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th style="min-width: 280px;">Tên gói trải nghiệm</th>
                            <th>Danh mục</th>
                            <th>Định giá</th>
                            <th>Lưu trú Homestay</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($services, 0, 4) as $srv): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($srv['banner_image'])): ?>
                                            <img src="<?= htmlspecialchars($srv['banner_image']) ?>" alt="Banner" class="rounded-3 object-fit-cover shadow-sm" style="width: 52px; height: 52px;">
                                        <?php else: ?>
                                            <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 52px; height: 52px;">
                                                <i class="bi bi-image fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="/host/services/<?= (int)$srv['id'] ?>/edit" class="fw-bold text-dark text-decoration-none d-block">
                                                <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($srv['location']) ?> • <?= (int)$srv['duration_days'] ?> ngày
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($srv['category_name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($srv['price_mode'] === 'free'): ?>
                                        <span class="badge bg-success-subtle text-success">Miễn phí</span>
                                    <?php elseif ($srv['price_mode'] === 'host_range'): ?>
                                        <div class="small fw-bold text-dark">
                                            <?= FormatHelper::formatVnd((int)$srv['min_price']) ?> - <?= FormatHelper::formatVnd((int)$srv['max_price']) ?>
                                        </div>
                                        <span class="badge bg-info-subtle text-info-emphasis" style="font-size: 0.68rem;">Thỏa thuận</span>
                                    <?php else: ?>
                                        <div class="fw-bold text-dark">
                                            <?= FormatHelper::formatVnd((int)$srv['base_price']) ?>
                                        </div>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">Cố định</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($srv['homestay_name'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-house-door me-1"></i><?= htmlspecialchars($srv['homestay_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Không kèm</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $badgeClass = match($srv['status']) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                        $statusLabel = match($srv['status']) {
                                            'approved' => 'Đã duyệt mở bán',
                                            'pending' => 'Chờ duyệt',
                                            'rejected' => 'Từ chối',
                                            default => 'Bản nháp',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/host/schedules/create?service_id=<?= (int)$srv['id'] ?>" class="btn btn-outline-success" title="Mở lịch">
                                            <i class="bi bi-calendar-plus"></i>
                                        </a>
                                        <a href="/host/services/<?= (int)$srv['id'] ?>/edit" class="btn btn-outline-primary" title="Sửa tour">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/services/<?= (int)$srv['id'] ?>" class="btn btn-outline-dark" title="Xem trang khách" target="_blank">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 2: Upcoming Departure Schedules -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold font-editorial text-dark mb-1">Các Đợt Khởi Hành Sắp Tới</h5>
                <p class="text-muted small mb-0">Theo dõi chỗ trống và lịch đón khách của bạn.</p>
            </div>
            <a href="/host/schedules" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Xem toàn bộ lịch trình &rarr;
            </a>
        </div>

        <?php if (empty($schedules)): ?>
            <div class="text-center py-4 text-muted small">
                Chưa có lịch khởi hành nào được thiết lập. Hãy <a href="/host/schedules/create" class="text-decoration-none">mở lịch khởi hành đầu tiên</a> để đón khách!
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th>Tour trải nghiệm</th>
                            <th>Khởi hành</th>
                            <th>Kết thúc</th>
                            <th>Điểm đón</th>
                            <th>Sức chứa & Đặt chỗ</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $sch): ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <?= htmlspecialchars($sch['service_title'] ?? 'Tour', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <i class="bi bi-calendar-event text-primary me-1"></i><?= FormatHelper::formatDateTime($sch['starts_at']) ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><?= FormatHelper::formatDateTime($sch['ends_at']) ?></small>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($sch['meeting_point']) ?></small>
                                </td>
                                <td>
                                    <?php
                                        $capacity = max(1, (int)$sch['capacity']);
                                        $booked = (int)$sch['booked_seats'];
                                        $percent = min(100, round(($booked / $capacity) * 100));
                                    ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%"></div>
                                        </div>
                                        <small class="fw-bold text-dark"><?= $booked ?>/<?= $capacity ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $schBadge = match($sch['status']) {
                                            'open' => 'bg-success',
                                            'closed' => 'bg-secondary',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-info',
                                        };
                                        $schLabel = match($sch['status']) {
                                            'open' => 'Đang mở',
                                            'closed' => 'Đã đóng',
                                            'cancelled' => 'Đã hủy',
                                            default => 'Hoàn thành',
                                        };
                                    ?>
                                    <span class="badge <?= $schBadge ?> rounded-pill px-2 py-1"><?= $schLabel ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
