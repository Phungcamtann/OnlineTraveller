<?php
/**
 * View: Quản Lý Bài Viết Cẩm Nang Admin (TV4-06)
 * File: app/views/admin/posts/index.php
 *
 * @var array $posts Danh sách bài viết
 * @var array $stats Thống kê tổng quan
 * @var ?string $currentStatus Bộ lọc trạng thái
 * @var array $user Thông tin Admin
 */

use App\Helpers\SecurityHelper;

$totalPosts = (int)($stats['total_posts'] ?? 0);
$publishedCount = (int)($stats['published_count'] ?? 0);
$draftCount = (int)($stats['draft_count'] ?? 0);
$totalViews = (int)($stats['total_views'] ?? 0);
?>

<div class="container-fluid py-4">
    <!-- PAGE HEADER -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Bài Viết Cẩm Nang</li>
                </ol>
            </nav>
            <h3 class="fw-bold font-editorial text-dark mb-0">Quản Trị Bài Viết & Cẩm Nang Du Lịch</h3>
            <small class="text-muted">Biên tập nội dung cẩm nang, kinh nghiệm săn mây và văn hóa trải nghiệm bản địa.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/admin/posts/create" class="btn btn-success rounded-pill px-3 py-1 btn-sm fw-semibold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Viết Bài Mới
            </a>
            <a href="/posts" target="_blank" class="btn btn-outline-dark rounded-pill px-3 py-1 btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Xem Trang Blog
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Tổng số bài viết</small>
                        <h4 class="fw-bold text-dark mb-0"><?= $totalPosts ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Đã xuất bản</small>
                        <h4 class="fw-bold text-success mb-0"><?= $publishedCount ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-3">
                        <i class="bi bi-file-earmark-lock fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Bản nháp / Đã ẩn</small>
                        <h4 class="fw-bold text-secondary mb-0"><?= $draftCount ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-eye fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Lượt đọc bài viết</small>
                        <h4 class="fw-bold text-info mb-0 font-monospace"><?= number_format($totalViews) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BUTTONS -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="/admin/posts" class="btn btn-sm rounded-pill <?= empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Tất cả (<?= $totalPosts ?>)
        </a>
        <a href="/admin/posts?status=published" class="btn btn-sm rounded-pill <?= $currentStatus === 'published' ? 'btn-success' : 'btn-outline-success' ?>">
            Đã xuất bản (<?= $publishedCount ?>)
        </a>
        <a href="/admin/posts?status=draft" class="btn btn-sm rounded-pill <?= $currentStatus === 'draft' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
            Bản nháp (<?= $draftCount ?>)
        </a>
    </div>

    <!-- ARTICLES TABLE -->
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tiêu đề bài viết</th>
                        <th>Đường dẫn tĩnh (Slug)</th>
                        <th>Tác giả</th>
                        <th class="text-center">Lượt xem</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                Chưa có bài viết nào. Hãy bấm "Viết Bài Mới" để bắt đầu!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $p): ?>
                            <?php
                            $pId = (int)$p['id'];
                            $thumb = $p['thumbnail'] ?: '/assets/images/default-blog.jpg';
                            $isPub = ((int)$p['is_published'] === 1);
                            ?>
                            <tr>
                                <td>
                                    <div class="rounded-3 overflow-hidden" style="width: 50px; height: 38px;">
                                        <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" 
                                             class="w-100 h-100 object-fit-cover" 
                                             alt="Thumbnail"
                                             onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=100&auto=format&fit=crop';">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <code class="small text-muted">/posts/<?= htmlspecialchars($p['slug'], ENT_QUOTES, 'UTF-8') ?></code>
                                </td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($p['author_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td class="text-center font-monospace small">
                                    <?= number_format((int)$p['views_count']) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $isPub ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-2 py-1 small">
                                        <?= $isPub ? 'Xuất bản' : 'Bản nháp' ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($p['created_at'])) ?></small>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="/posts/<?= htmlspecialchars($p['slug'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" title="Xem trên web">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/posts/<?= $pId ?>/edit" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="/admin/posts/<?= $pId ?>/delete" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
