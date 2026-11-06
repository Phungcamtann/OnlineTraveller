<?php
/** @var array $categories */
/** @var array|null $user */
$categories = $categories ?? [];
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Danh mục trải nghiệm</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-folder-fill me-1"></i> Quản Trị Hệ Thống (TV1)
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý Danh Mục Trải Nghiệm</h2>
            <p class="text-muted small mb-0">Phân loại các tour văn hóa bản địa (Ẩm thực, Khám phá di sản, Trekking, Biển đảo...)</p>
        </div>
        <div>
            <a href="/admin/categories/create" class="btn btn-editorial px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i> Thêm Danh Mục Mới
            </a>
        </div>
    </div>

    <!-- Categories Table Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark font-editorial mb-0">
                Danh Sách Danh Mục Hiện Có (<?= count($categories) ?>)
            </h5>
            <small class="text-muted">Cập nhật qua thủ tục <code>dhs_sp_categories_save</code></small>
        </div>

        <?php if (empty($categories)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                Chưa có danh mục nào trong hệ thống.
                <div class="mt-3">
                    <a href="/admin/categories/create" class="btn btn-editorial btn-sm px-4">Tạo danh mục đầu tiên</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">Icon</th>
                            <th>Tên danh mục & Đường dẫn (Slug)</th>
                            <th>Mô tả tóm tắt</th>
                            <th class="text-center" style="width: 120px;">Số Tour</th>
                            <th style="width: 130px;">Trạng thái</th>
                            <th class="text-end" style="width: 180px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle border" style="width: 44px; height: 44px;">
                                        <i class="bi <?= htmlspecialchars($cat['icon'] ?? 'bi-tag') ?> fs-5"></i>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <small class="text-muted font-monospace">/services?category_id=<?= (int)$cat['id'] ?> (<?= htmlspecialchars($cat['slug']) ?>)</small>
                                </td>
                                <td class="small text-muted" style="max-width: 320px;">
                                    <?= htmlspecialchars($cat['description'] ?? 'Không có mô tả', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">
                                        <?= (int)($cat['services_count'] ?? 0) ?> tour
                                    </span>
                                </td>
                                <td>
                                    <?php if ((int)$cat['is_active'] === 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Hiển thị
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-eye-slash-fill me-1"></i> Đang ẩn
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <!-- Edit button -->
                                        <a href="/admin/categories/<?= (int)$cat['id'] ?>/edit" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="Chỉnh sửa danh mục">
                                            <i class="bi bi-pencil-square me-1"></i> Sửa
                                        </a>

                                        <!-- Toggle Status button -->
                                        <form action="/admin/categories/<?= (int)$cat['id'] ?>/toggle" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn <?= (int)$cat['is_active'] === 1 ? 'ẩn' : 'hiển thị' ?> danh mục này?');">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <?php if ((int)$cat['is_active'] === 1): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2" title="Ẩn danh mục">
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Hiện danh mục">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
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
