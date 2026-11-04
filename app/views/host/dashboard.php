<?php
/** @var array $services */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-success px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-person-badge-fill me-1"></i> Bảng Điều Khiển Dân Bản Địa
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý Trải Nghiệm & Homestay</h2>
            <p class="text-muted small mb-0">Xin chào <strong><?= htmlspecialchars($user['name'] ?? 'Host', ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($user['living_area'] ?? 'Địa phương') ?>)</p>
        </div>
        <div>
            <a href="/host/services/create" class="btn btn-editorial">
                <i class="bi bi-plus-circle me-1"></i> Đăng Trải Nghiệm Mới
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <small class="text-muted fw-bold">TỔNG TRẢI NGHIỆM ĐÃ TẠO</small>
                <div class="fs-2 fw-bold text-dark font-editorial"><?= count($services) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <small class="text-muted fw-bold">HOMESTAY NHÀ DÂN KÈM THEO</small>
                <div class="fs-2 fw-bold text-success font-editorial">
                    <?= count(array_filter($services, fn($s) => !empty($s['homestay_name']))) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <small class="text-muted fw-bold">TỔNG TRẠM TRÌNH CHIẾU 3D</small>
                <div class="fs-2 fw-bold text-primary font-editorial">
                    <?= array_sum(array_column($services, 'stops_count')) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Table -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <h5 class="fw-bold text-dark mb-3">Danh Sách Trải Nghiệm Đang Quản Lý</h5>
        
        <?php if (empty($services)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder2-open fs-1 d-block mb-2 text-secondary"></i>
                Bạn chưa đăng ký gói trải nghiệm nào.
                <div class="mt-3">
                    <a href="/host/services/create" class="btn btn-editorial btn-sm px-4">Tạo trải nghiệm đầu tiên</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tên trải nghiệm</th>
                            <th>Danh mục</th>
                            <th>Vị trí & Tọa độ</th>
                            <th>Homestay nhà dân</th>
                            <th>Trạm 3D</th>
                            <th>Giá cơ bản</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $srv): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($srv['category_name']) ?></span></td>
                                <td class="small text-muted">
                                    <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($srv['location']) ?>
                                    <?php if (!empty($srv['latitude']) && !empty($srv['longitude'])): ?>
                                        <div class="text-success small"><i class="bi bi-pin-map"></i> Đã có tọa độ Maps</div>
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
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?= (int)$srv['stops_count'] ?> trạm
                                    </span>
                                </td>
                                <td class="fw-bold text-dark"><?= FormatHelper::formatVnd((int)$srv['base_price']) ?></td>
                                <td>
                                    <?php
                                        $badgeClass = match($srv['status']) {
                                            'approved' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= strtoupper($srv['status']) ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="/services/<?= (int)$srv['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">Xem trang khách</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
