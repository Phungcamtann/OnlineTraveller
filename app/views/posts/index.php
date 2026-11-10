<?php
/**
 * View: Danh Sách Bản Tin Cộng Đồng & Tin Tức Báo Chí Du Lịch
 * File: app/views/posts/index.php
 *
 * @var array $posts Danh sách bài viết & tin tức (đã phân trang)
 * @var string $currentTab Tab hiện tại (all | community | rss)
 * @var int $currentPage Trang hiện tại
 * @var int $totalPages Tổng số trang
 * @var int $totalCount Tổng số lượng bài
 * @var int $communityCount Số bài cộng đồng
 * @var int $rssCount Số tin tức VnExpress
 * @var array|null $user Người dùng hiện tại
 */

$currentTab = $currentTab ?? 'all';
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
?>

<div class="container py-5">
    <!-- HERO HEADER -->
    <div class="text-center mb-4">
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 mb-2">
            <i class="bi bi-compass-fill me-1"></i> Bản Tin Du Lịch & Cộng Đồng Bản Địa
        </span>
        <h2 class="fw-bold font-editorial text-dark mb-2">Góc Nhìn & Kinh Nghiệm Đi Tour</h2>
        <p class="text-muted mx-auto mb-3" style="max-width: 640px;">
            Không gian kết nối những câu chuyện chân thực từ du khách, cẩm nang của người bản địa và dòng chảy tin tức du lịch thời sự mới nhất.
        </p>

        <!-- CTA Button for All 3 Roles -->
        <div>
            <a href="/posts/create" class="btn btn-editorial rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-pencil-square me-2"></i> Chia Sẻ Câu Chuyện / Viết Bài Mới
            </a>
        </div>
    </div>

    <!-- FILTER TABS -->
    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-4">
        <a href="/posts?tab=all" class="btn btn-sm rounded-pill px-3 py-2 <?= $currentTab === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <i class="bi bi-grid-fill me-1"></i> Tất Cả Dòng Tin 
            <span class="badge bg-white text-dark ms-1 rounded-pill"><?= (int)($totalCount ?? count($posts)) ?></span>
        </a>
        <a href="/posts?tab=community" class="btn btn-sm rounded-pill px-3 py-2 <?= $currentTab === 'community' ? 'btn-success' : 'btn-outline-secondary' ?>">
            <i class="bi bi-people-fill me-1"></i> Bài Viết Cộng Đồng 
            <span class="badge bg-white text-dark ms-1 rounded-pill"><?= (int)($communityCount ?? 0) ?></span>
        </a>
        <a href="/posts?tab=rss" class="btn btn-sm rounded-pill px-3 py-2 <?= $currentTab === 'rss' ? 'btn-warning text-dark fw-semibold' : 'btn-outline-secondary' ?>">
            <i class="bi bi-newspaper me-1"></i> Tin Tức VnExpress Du Lịch 
            <span class="badge bg-white text-dark ms-1 rounded-pill"><?= (int)($rssCount ?? 0) ?></span>
        </a>
    </div>

    <!-- POSTS GRID -->
    <?php if (empty($posts)): ?>
        <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white my-4">
            <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
            <h5 class="fw-bold text-dark">Chưa có bài viết nào trong mục này</h5>
            <p class="text-muted small mb-3">Hãy là người đầu tiên chia sẻ câu chuyện du lịch kỳ thú của bạn!</p>
            <div>
                <a href="/posts/create" class="btn btn-editorial rounded-pill px-4 py-2 btn-sm">
                    <i class="bi bi-pencil me-1"></i> Viết bài ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4 mb-5">
            <?php foreach ($posts as $post): ?>
                <?php
                $isRss = ($post['type'] ?? '') === 'rss';
                $thumb = $post['thumbnail'] ?: '/assets/images/default-blog.jpg';
                $link = $post['link'] ?? ('/posts/' . ($post['slug'] ?? ''));
                $authorRole = $post['author_role'] ?? 'tourist';
                $roleLabel = match($authorRole) {
                    'host' => 'Host Bản Địa',
                    'admin' => 'Ban Quản Trị',
                    'rss' => 'Báo Chí',
                    default => 'Du Khách',
                };
                $roleBadge = match($authorRole) {
                    'host' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                    'admin' => 'bg-danger-subtle text-danger border border-danger-subtle',
                    'rss' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                    default => 'bg-primary-subtle text-primary border border-primary-subtle',
                };
                ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden bg-white d-flex flex-column transition-hover">
                        <!-- Thumbnail Image -->
                        <div class="position-relative" style="height: 210px; overflow: hidden;">
                            <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" 
                                 class="card-img-top w-100 h-100 object-fit-cover" 
                                 alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600&auto=format&fit=crop';">
                            
                            <!-- Source Badge Top Left -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <?php if ($isRss): ?>
                                    <span class="badge bg-warning text-dark border-0 rounded-pill px-3 py-1 small fw-bold shadow-sm">
                                        <i class="bi bi-newspaper me-1"></i> VnExpress
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success text-white border-0 rounded-pill px-3 py-1 small fw-bold shadow-sm">
                                        <i class="bi bi-chat-quote-fill me-1"></i> Bản Tin Cộng Đồng
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Views count top right (if internal) -->
                            <?php if (!$isRss && !empty($post['views_count'])): ?>
                                <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 rounded-pill small">
                                    <i class="bi bi-eye me-1"></i><?= (int)$post['views_count'] ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <!-- Author & Role -->
                            <div class="d-flex align-items-center justify-content-between gap-2 small text-muted mb-2">
                                <div class="d-flex align-items-center gap-1 text-truncate">
                                    <i class="bi bi-person-circle"></i>
                                    <span class="fw-bold text-dark text-truncate" style="max-width: 140px;">
                                        <?= htmlspecialchars($post['author_name'] ?? 'Ẩn danh', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="badge rounded-pill <?= $roleBadge ?> ms-1" style="font-size: 0.7rem;">
                                        <?= $roleLabel ?>
                                    </span>
                                </div>
                                <div class="text-nowrap" style="font-size: 0.75rem;">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= is_numeric(strtotime($post['created_at'])) ? date('d/m/Y', strtotime($post['created_at'])) : htmlspecialchars($post['created_at']) ?>
                                </div>
                            </div>

                            <!-- Title -->
                            <h5 class="card-title fw-bold text-dark font-editorial mb-2 line-clamp-2">
                                <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>" 
                                   <?= $isRss ? 'target="_blank" rel="noopener noreferrer"' : '' ?>
                                   class="text-decoration-none text-dark hover-primary">
                                    <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h5>

                            <!-- Excerpt -->
                            <p class="card-text text-muted small flex-grow-1 line-clamp-3 mb-3">
                                <?= htmlspecialchars(mb_substr(strip_tags((string)($post['content'] ?? '')), 0, 130, 'UTF-8') . '...', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <!-- Footer & Read Button -->
                            <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between">
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <?= htmlspecialchars($post['source_label'] ?? 'OnlineTraveller') ?>
                                </small>
                                <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>" 
                                   <?= $isRss ? 'target="_blank" rel="noopener noreferrer"' : '' ?>
                                   class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold">
                                    <?php if ($isRss): ?>
                                        Đọc Tin VnExpress <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    <?php else: ?>
                                        Đọc Chi Tiết <i class="bi bi-arrow-right ms-1"></i>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- PAGINATION CONTROLS -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center align-items-center gap-2">
                    <!-- Prev Page -->
                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link rounded-pill px-3 py-2 border shadow-sm" href="/posts?tab=<?= urlencode($currentTab) ?>&page=<?= $currentPage - 1 ?>">
                            <i class="bi bi-chevron-left me-1"></i> Trang trước
                        </a>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link border-0 text-muted small fw-bold">
                            Trang <?= $currentPage ?> / <?= $totalPages ?>
                        </span>
                    </li>

                    <!-- Next Page -->
                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link rounded-pill px-3 py-2 border shadow-sm" href="/posts?tab=<?= urlencode($currentTab) ?>&page=<?= $currentPage + 1 ?>">
                            Trang sau <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
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
