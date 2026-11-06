<?php
/** @var array $services */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Host Portal</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Gói Trải Nghiệm Của Tôi</li>
        </ol>
    </nav>

    <!-- Top Action Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="font-editorial fw-bold text-dark mb-1">Gói Trải Nghiệm Bản Địa</h2>
            <p class="text-muted small mb-0">Quản lý nội dung, hình ảnh trạm 3D, mức giá và homestay lưu trú đi kèm.</p>
        </div>
        <div>
            <a href="/host/services/create" class="btn btn-editorial rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Đăng Trải Nghiệm Mới
            </a>
        </div>
    </div>

    <!-- Tour List Table -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <?php if (empty($services)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-compass fs-1 d-block mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark mb-1">Bạn chưa có gói trải nghiệm nào</h5>
                <p class="small text-muted mb-3">Hãy tạo gói tour đầu tiên để bắt đầu kết nối và đón tiếp du khách từ xa.</p>
                <a href="/host/services/create" class="btn btn-editorial rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Đăng trải nghiệm ngay
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th style="min-width: 280px;">Trải nghiệm</th>
                            <th>Danh mục</th>
                            <th>Địa điểm</th>
                            <th>Chế độ giá</th>
                            <th>Lưu trú Homestay</th>
                            <th>Lịch / Khách</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $srv): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($srv['banner_image'])): ?>
                                            <img src="<?= htmlspecialchars($srv['banner_image']) ?>" alt="Banner" class="rounded-3 object-fit-cover shadow-sm" style="width: 58px; height: 58px;">
                                        <?php else: ?>
                                            <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 58px; height: 58px;">
                                                <i class="bi bi-image fs-4"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="/host/services/<?= (int)$srv['id'] ?>/edit" class="fw-bold text-dark text-decoration-none d-block">
                                                <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                            <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                                /<?= htmlspecialchars($srv['slug']) ?>
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
                                    <div class="small fw-semibold text-dark">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($srv['location']) ?>
                                    </div>
                                    <small class="text-muted"><?= (int)$srv['duration_days'] ?> ngày</small>
                                </td>
                                <td>
                                    <?php if ($srv['price_mode'] === 'free'): ?>
                                        <span class="badge bg-success-subtle text-success">Miễn phí</span>
                                    <?php elseif ($srv['price_mode'] === 'host_range'): ?>
                                        <div class="small fw-bold text-dark">
                                            <?= FormatHelper::formatVnd((int)$srv['min_price']) ?> - <?= FormatHelper::formatVnd((int)$srv['max_price']) ?>
                                        </div>
                                        <span class="badge bg-info-subtle text-info-emphasis" style="font-size: 0.65rem;">Khoảng giá</span>
                                    <?php else: ?>
                                        <div class="fw-bold text-dark">
                                            <?= FormatHelper::formatVnd((int)$srv['base_price']) ?>
                                        </div>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Cố định</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($srv['homestay_name'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center">
                                            <i class="bi bi-house-door me-1"></i><?= htmlspecialchars($srv['homestay_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Không kèm</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="d-block text-dark fw-semibold">
                                        <i class="bi bi-calendar3 me-1 text-primary"></i><?= (int)($srv['schedules_count'] ?? 0) ?> đợt lịch
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-person-check me-1 text-success"></i><?= (int)($srv['bookings_count'] ?? 0) ?> khách
                                    </small>
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
                                            'approved' => 'Đã duyệt',
                                            'pending' => 'Chờ duyệt',
                                            'rejected' => 'Bị từ chối',
                                            default => 'Bản nháp',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1"><?= $statusLabel ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/host/schedules/create?service_id=<?= (int)$srv['id'] ?>" class="btn btn-outline-success" title="Mở lịch khởi hành">
                                            <i class="bi bi-calendar-plus me-1"></i> Mở lịch
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
</div>
