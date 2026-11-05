<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item"><a href="/admin/categories" class="text-decoration-none text-muted">Danh mục trải nghiệm</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Thêm danh mục mới</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                            <i class="bi bi-folder-plus me-1"></i> Admin Phân Hệ TV1
                        </span>
                        <h3 class="font-editorial fw-bold text-dark mb-1">Thêm Danh Mục Trải Nghiệm Mới</h3>
                        <p class="text-muted small mb-0">Tạo chủ đề văn hóa/du lịch để người dân bản địa phân loại tour</p>
                    </div>
                </div>

                <form action="/admin/categories" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <!-- Category Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tên Danh Mục Văn Hóa / Trải Nghiệm <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control bg-light border-0" placeholder="Ví dụ: Làng Nghề Cổ Truyền & Thủ Công" required autofocus>
                    </div>

                    <!-- Slug & Icon -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Đường dẫn tĩnh (Slug)</label>
                            <input type="text" name="slug" id="categorySlug" class="form-control bg-light border-0 font-monospace" placeholder="lang-nghe-co-truyen (để trống tự tạo)">
                            <small class="text-muted" style="font-size: 0.75rem;">Nếu để trống, hệ thống sẽ tự động chuyển đổi từ Tên danh mục.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Mã Icon Bootstrap Icons</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-tag" id="iconPreview"></i></span>
                                <input type="text" name="icon" id="categoryIcon" class="form-control bg-light border-0 font-monospace" value="bi-tag" placeholder="bi-bank, bi-cup-hot, bi-tree, bi-water...">
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Gợi ý: <code>bi-bank</code> (văn hóa), <code>bi-cup-hot</code> (ẩm thực), <code>bi-tree</code> (núi rừng), <code>bi-water</code> (biển đảo).</small>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mô tả tóm tắt ý nghĩa danh mục</label>
                        <textarea name="description" rows="3" class="form-control bg-light border-0" placeholder="Khám phá nét đẹp làng gốm, dệt chiếu, làm đèn lồng cùng người dân địa phương..."></textarea>
                    </div>

                    <!-- Status -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label fw-bold text-dark small" for="isActive">
                                Kích hoạt và hiển thị công khai ngay trên Trang chủ & Thanh tìm kiếm
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <a href="/admin/categories" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                        <button type="submit" class="btn btn-editorial px-4 py-2">
                            <i class="bi bi-check-lg me-1"></i> Lưu Danh Mục Mới
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto update slug & icon preview
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');
    const iconInput = document.getElementById('categoryIcon');
    const iconPreview = document.getElementById('iconPreview');

    if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function () {
            iconPreview.className = 'bi ' + (this.value.trim() || 'bi-tag');
        });
    }
});
</script>
