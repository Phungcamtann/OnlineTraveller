<?php
/**
 * View: Form Tạo Mới / Chỉnh Sửa Bài Viết Cẩm Nang (Admin)
 * File: app/views/admin/posts/form.php
 *
 * @var ?array $post Bài viết cần chỉnh sửa (nếu có)
 * @var array $user Thông tin Admin
 */

use App\Helpers\SecurityHelper;

$isEdit = !empty($post);
$actionUrl = $isEdit ? "/admin/posts/{$post['id']}/edit" : "/admin/posts";
$titleVal = $isEdit ? htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') : '';
$slugVal = $isEdit ? htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') : '';
$contentVal = $isEdit ? htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8') : '';
$thumbVal = $isEdit ? htmlspecialchars($post['thumbnail'] ?? '', ENT_QUOTES, 'UTF-8') : '';
$isPub = $isEdit ? ((int)$post['is_published'] === 1) : true;
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- BREADCRUMB -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
                    <li class="breadcrumb-item"><a href="/admin/posts" class="text-decoration-none text-muted">Bài viết cẩm nang</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= $isEdit ? 'Chỉnh sửa bài viết' : 'Viết bài mới' ?></li>
                </ol>
            </nav>

            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h4 class="fw-bold font-editorial text-dark mb-1">
                            <?= $isEdit ? 'Chỉnh Sửa Bài Viết' : 'Viết Bài Cẩm Nang Mới' ?>
                        </h4>
                        <small class="text-muted">Biên tập nội dung cẩm nang chia sẻ kinh nghiệm du lịch cho du khách.</small>
                    </div>
                    <a href="/admin/posts" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                </div>

                <form action="<?= $actionUrl ?>" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <div class="mb-3">
                        <label for="post_title" class="form-label small fw-semibold text-dark">Tiêu đề bài viết (*)</label>
                        <input type="text" 
                               class="form-control rounded-3 p-3 shadow-none" 
                               id="post_title" 
                               name="title" 
                               value="<?= $titleVal ?>" 
                               placeholder="VD: Kinh nghiệm săn mây đỉnh LangBiang từ 4h sáng..." 
                               required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="post_slug" class="form-label small fw-semibold text-dark">Đường dẫn tĩnh (Slug)</label>
                            <input type="text" 
                                   class="form-control rounded-3 shadow-none" 
                                   id="post_slug" 
                                   name="slug" 
                                   value="<?= $slugVal ?>" 
                                   placeholder="Để trống để hệ thống tự sinh từ tiêu đề">
                            <small class="text-muted">Định dạng URL: <code>/posts/kinh-nghiem-san-may</code></small>
                        </div>
                        <div class="col-md-6">
                            <label for="post_thumb" class="form-label small fw-semibold text-dark">URL Ảnh đại diện (Thumbnail)</label>
                            <input type="text" 
                                   class="form-control rounded-3 shadow-none" 
                                   id="post_thumb" 
                                   name="thumbnail" 
                                   value="<?= $thumbVal ?>" 
                                   placeholder="https://... hoặc /assets/images/...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="post_content" class="form-label small fw-semibold text-dark">Nội dung bài viết (*)</label>
                        <textarea class="form-control rounded-3 p-3 shadow-none" 
                                  id="post_content" 
                                  name="content" 
                                  rows="12" 
                                  placeholder="Nhập nội dung chia sẻ, lưu ý chuẩn bị hành lý, thời tiết, kinh nghiệm ẩm thực..." 
                                  required><?= $contentVal ?></textarea>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?= $isPub ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold text-dark" for="is_published">
                                Xuất bản bài viết ngay (Hiển thị công khai trên website)
                            </label>
                        </div>
                        <small class="text-muted d-block ps-4">Nếu tắt, bài viết sẽ được lưu dưới dạng Bản nháp và chỉ Admin mới nhìn thấy.</small>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="/admin/posts" class="btn btn-light rounded-pill px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-semibold shadow-sm">
                            <i class="bi bi-cloud-arrow-up me-1"></i> <?= $isEdit ? 'Lưu Thay Đổi' : 'Xuất Bản Bài Viết' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
