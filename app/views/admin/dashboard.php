<?php
/** @var array $stats */
/** @var array $recentServices */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-shield-lock-fill me-1"></i> Trung Tâm Quản Trị Hệ Thống (Admin)
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Tổng Quan Vận Hành OnlineTraveller</h2>
            <p class="text-muted small mb-0">Phiên bản thiết kế tinh chỉnh v2.2 • Quản lý danh mục, duyệt tour và người dùng</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/categories" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-folder-fill me-1"></i> Danh Mục (Categories)
            </a>
            <a href="/admin/posts" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-file-text-fill me-1"></i> Cẩm Nang (Blog)
            </a>
        </div>
    </div>

    <!-- 5 Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">NGƯỜI DÙNG</small>
                <div class="fs-3 fw-bold text-dark font-editorial"><?= $stats['users'] ?></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">TRẢI NGHIỆM</small>
                <div class="fs-3 fw-bold text-primary font-editorial"><?= $stats['services'] ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">CHỜ DUYỆT (PENDING)</small>
                <div class="fs-3 fw-bold text-warning font-editorial"><?= $stats['pending_services'] ?></div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">DANH MỤC</small>
                <div class="fs-3 fw-bold text-success font-editorial"><?= $stats['categories'] ?></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-12">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">LƯỢT ĐẶT LỊCH</small>
                <div class="fs-3 fw-bold text-info font-editorial"><?= $stats['bookings'] ?></div>
            </div>
        </div>
    </div>

    <!-- Recent Services for Moderation -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">Trải Nghiệm Mới Đăng Ký Cần Kiểm Duyệt</h5>
            <span class="badge badge-soft-primary px-3 py-1 rounded-pill">CRUD Cơ bản</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên trải nghiệm</th>
                        <th>Host bản địa</th>
                        <th>Danh mục</th>
                        <th>Địa điểm</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Kiểm duyệt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentServices as $srv): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><i class="bi bi-person text-muted me-1"></i><?= htmlspecialchars($srv['host_name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($srv['category_name']) ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($srv['location']) ?></td>
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
                                <a href="/services/<?= (int)$srv['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
