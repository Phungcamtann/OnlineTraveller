<?php
/**
 * View: Chia Sẻ Bài Viết / Câu Chuyện Du Lịch (Tourist, Host, Admin)
 * File: app/views/posts/create.php
 *
 * @var array|null $user Thông tin người dùng hiện tại
 */

use App\Helpers\SecurityHelper;
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/posts" class="text-decoration-none text-muted">Bản tin cộng đồng</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Chia Sẻ Trải Nghiệm</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <!-- Header -->
                <div class="border-bottom pb-3 mb-4">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-chat-heart-fill me-1"></i> Góc Chia Sẻ Cộng Đồng
                    </span>
                    <h3 class="font-editorial fw-bold text-dark mb-1">Kể Lại Chuyến Đi Của Bạn</h3>
                    <p class="text-muted small mb-0">
                        Chia sẻ cảm xúc chân thật, kinh nghiệm săn mây, mẹo ăn uống hay câu chuyện văn hóa bản địa độc đáo cùng mọi người.
                    </p>
                </div>

                <form action="/posts" method="POST" enctype="multipart/form-data" id="createPostForm">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                    <input type="hidden" name="csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="postTitle" class="form-label small fw-bold text-muted">
                            Tiêu đề bài viết <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="postTitle" 
                               class="form-control rounded-3 py-2 px-3" 
                               required 
                               minlength="5" 
                               maxlength="255" 
                               placeholder="Ví dụ: 3 Ngày 2 Đêm Săn Mây Y Tý Cùng Gia Đình Người Bản Địa">
                        <small class="text-muted" style="font-size: 0.75rem;">Đặt tiêu đề gợi mở, ngắn gọn và truyền cảm hứng (5 - 255 ký tự).</small>
                    </div>

                    <!-- Thumbnail Image -->
                    <div class="mb-4 p-3 rounded-4 bg-light border">
                        <h6 class="fw-bold text-dark small mb-2">
                            <i class="bi bi-image-fill text-primary me-1"></i> Ảnh Bìa Đại Diện Cho Bài Viết
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tải ảnh lên từ máy tính</label>
                                <input type="file" 
                                       name="thumbnail_file" 
                                       id="thumbnailFileInput" 
                                       class="form-control rounded-3 form-control-sm" 
                                       accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Hỗ trợ JPG, PNG, WEBP (tối đa 5MB).</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Hoặc dán URL ảnh có sẵn</label>
                                <input type="text" 
                                       name="thumbnail" 
                                       id="thumbnailUrlInput" 
                                       class="form-control rounded-3 form-control-sm" 
                                       placeholder="https://... hoặc /assets/images/...">
                            </div>
                        </div>

                        <!-- Preview Thumbnail Box -->
                        <div id="postThumbPreviewBox" class="mt-3 text-center d-none">
                            <img id="postThumbPreviewImg" src="" alt="Thumbnail Preview" class="rounded-3 shadow-sm border" style="max-height: 180px; width: 100%; object-fit: cover;">
                            <small class="text-success d-block mt-1 fw-semibold" style="font-size: 0.75rem;">Ảnh xem trước đại diện</small>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label for="postContent" class="form-label small fw-bold text-muted">
                            Nội dung câu chuyện & kinh nghiệm chuyến đi <span class="text-danger">*</span>
                        </label>
                        <textarea name="content" 
                                  id="postContent" 
                                  rows="10" 
                                  class="form-control rounded-3 p-3" 
                                  required 
                                  minlength="20" 
                                  placeholder="Hãy chia sẻ những kỷ niệm đáng nhớ: phương tiện đi lại, quán ăn ngon, thời điểm săn mây đẹp nhất, con người địa phương thân thiện ra sao..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top">
                        <a href="/posts" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            <i class="bi bi-arrow-left me-1"></i> Quay Lại
                        </a>
                        <button type="submit" class="btn btn-editorial rounded-pill px-4 py-2 shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Xuất Bản Bài Viết Ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('thumbnailFileInput');
    const urlInput = document.getElementById('thumbnailUrlInput');
    const previewBox = document.getElementById('postThumbPreviewBox');
    const previewImg = document.getElementById('postThumbPreviewImg');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                previewImg.src = URL.createObjectURL(this.files[0]);
                previewBox.classList.remove('d-none');
            }
        });
    }

    if (urlInput) {
        urlInput.addEventListener('input', function() {
            if (this.value && this.value.trim().length > 8) {
                previewImg.src = this.value.trim();
                previewBox.classList.remove('d-none');
            }
        });
    }
});
</script>
