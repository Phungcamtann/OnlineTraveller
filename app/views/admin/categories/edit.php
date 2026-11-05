<?php
/** @var array $category */
$category = $category ?? [];
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item"><a href="/admin/categories" class="text-decoration-none text-muted">Danh mục trải nghiệm</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Chỉnh sửa danh mục</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                            <i class="bi bi-pencil-square me-1"></i> Admin Phân Hệ TV1
                        </span>
                        <h3 class="font-editorial fw-bold text-dark mb-1">Chỉnh Sửa Danh Mục: <?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-muted small mb-0">Cập nhật qua thủ tục <code>sp_categories_save</code></p>
                    </div>
                </div>

                <form action="/admin/categories/<?= (int)$category['id'] ?>/edit" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <!-- Category Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tên Danh Mục Văn Hóa / Trải Nghiệm <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control bg-light border-0" value="<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus>
                    </div>

                    <!-- Slug & Icon -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Đường dẫn tĩnh (Slug)</label>
                            <input type="text" name="slug" id="categorySlug" class="form-control bg-light border-0 font-monospace" value="<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <small class="text-muted" style="font-size: 0.75rem;">Định danh duy nhất trên URL.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Mã Icon Bootstrap Icons</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi <?= htmlspecialchars($category['icon'] ?? 'bi-tag') ?>" id="iconPreview"></i></span>
                                <input type="text" name="icon" id="categoryIcon" class="form-control bg-light border-0 font-monospace" value="<?= htmlspecialchars($category['icon'] ?? 'bi-tag', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Ví dụ: <code>bi-bank</code>, <code>bi-cup-hot</code>, <code>bi-tree</code>, <code>bi-water</code>.</small>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mô tả tóm tắt ý nghĩa danh mục</label>
                        <textarea name="description" rows="3" class="form-control bg-light border-0"><?= htmlspecialchars($category['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <!-- Status -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= ((int)($category['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark small" for="isActive">
                                Hiển thị danh mục này công khai trên Trang chủ và bộ lọc tìm kiếm
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <a href="/admin/categories" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Hủy & Quay lại
                        </a>
                        <button type="submit" class="btn btn-editorial px-4 py-2">
                            <i class="bi bi-check-lg me-1"></i> Lưu Cập Nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const iconInput = document.getElementById('categoryIcon');
    const iconPreview = document.getElementById('iconPreview');

    if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function () {
            iconPreview.className = 'bi ' + (this.value.trim() || 'bi-tag');
        });
    }
});
</script>
