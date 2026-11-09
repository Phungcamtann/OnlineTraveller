<?php
/**
 * View: Danh Sách Bài Viết Cẩm Nang Du Lịch (Public)
 * File: app/views/posts/index.php
 *
 * @var array $posts Danh sách bài viết đã xuất bản
 * @var array $user Người dùng hiện tại
 */
?>

<div class="container py-5">
    <!-- HERO HEADER -->
    <div class="text-center mb-5">
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 mb-2">
            <i class="bi bi-journal-bookmark me-1"></i> Cẩm Nang Bản Địa
        </span>
        <h2 class="fw-bold font-editorial text-dark mb-2">Góc Nhìn & Kinh Nghiệm Đi Tour</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">
            Những câu chuyện chân thực, kinh nghiệm săn mây, văn hóa ẩm thực và bí quyết trải nghiệm từ các hướng dẫn viên và dân bản địa.
        </p>
    </div>

    <!-- POSTS GRID -->
    <?php if (empty($posts)): ?>
        <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
            <h5 class="fw-bold text-dark">Chưa có bài viết nào được xuất bản</h5>
            <p class="text-muted small mb-0">Các bài viết cẩm nang mới sẽ sớm được Ban Quản Trị cập nhật tại đây.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <?php
                $thumb = $post['thumbnail'] ?: '/assets/images/default-blog.jpg';
                $slug = htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8');
                ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden bg-white d-flex flex-column transition-hover">
                        <div class="position-relative" style="height: 200px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" 
                                 class="card-img-top w-100 h-100 object-fit-cover" 
                                 alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop';">
                            <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 rounded-pill small">
                                <i class="bi bi-eye me-1"></i><?= (int)$post['views_count'] ?>
                            </span>
                        </div>
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($post['author_name'] ?? 'Ban Quản Trị', ENT_QUOTES, 'UTF-8') ?>
                                <span class="mx-1">•</span>
                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($post['created_at'])) ?>
                            </div>
                            <h5 class="card-title fw-bold text-dark font-editorial mb-2">
                                <a href="/posts/<?= $slug ?>" class="text-decoration-none text-dark hover-primary">
                                    <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1 line-clamp-3">
                                <?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 140, 'UTF-8') . '...', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <div class="pt-3 border-top mt-auto">
                                <a href="/posts/<?= $slug ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold">
                                    Đọc Bài Viết <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.hover-primary:hover {
    color: #198754 !important;
}
.transition-hover {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.transition-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
</style>
