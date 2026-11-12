<?php
/**
 * View: Quản Lý Bài Viết Cẩm Nang Admin
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

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Bài viết cẩm nang</li>
        </ol>
    </nav>

    <!-- Header & Quick Nav -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-newspaper me-1"></i> Quản Trị Hệ Thống
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý Bài Viết & Cẩm Nang Du Lịch</h2>
            <p class="text-muted small mb-0">Biên tập nội dung cẩm nang, kinh nghiệm săn mây và văn hóa trải nghiệm bản địa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/posts/create" class="btn btn-editorial px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i> Viết Bài Mới
            </a>
            <a href="/posts" target="_blank" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-box-arrow-up-right me-1"></i> Xem Trang Blog
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">TỔNG BÀI VIẾT</small>
                <div class="fs-3 fw-bold text-dark font-editorial"><?= $totalPosts ?></div>
                <small class="text-muted">Bài viết trên hệ thống</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">ĐÃ XUẤT BẢN</small>
                <div class="fs-3 fw-bold text-success font-editorial"><?= $publishedCount ?></div>
                <small class="text-muted">Đang hiển thị công khai</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">BẢN NHÁP / ĐÃ ẨN</small>
                <div class="fs-3 fw-bold text-secondary font-editorial"><?= $draftCount ?></div>
                <small class="text-muted">Chờ hoàn thiện nội dung</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">TỔNG LƯỢT ĐỌC</small>
                <div class="fs-3 fw-bold text-info font-editorial"><?= number_format($totalViews) ?></div>
                <small class="text-muted">Lượt xem từ độc giả</small>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
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

    <!-- Table Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="fw-bold text-dark font-editorial mb-0">
                Danh Sách Bài Viết (<?= $totalPosts ?>)
            </h5>
        </div>

        <?php if (empty($posts)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Chưa có bài viết nào trong hệ thống.
                <div class="mt-3">
                    <a href="/admin/posts/create" class="btn btn-editorial btn-sm px-4">Tạo bài viết đầu tiên</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">Ảnh</th>
                            <th>Tiêu đề bài viết</th>
                            <th>Đường dẫn tĩnh (Slug)</th>
                            <th>Tác giả</th>
                            <th class="text-center" style="width: 110px;">Lượt xem</th>
                            <th style="width: 130px;">Trạng thái</th>
                            <th style="width: 130px;">Thời gian</th>
                            <th class="text-end" style="width: 140px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                            <?php
                            $pId = (int)$p['id'];
                            $thumb = $p['thumbnail'] ?: '/assets/images/default-blog.jpg';
                            $isPub = ((int)$p['is_published'] === 1);
                            ?>
                            <tr>
                                <td>
                                    <div class="rounded-3 overflow-hidden shadow-sm" style="width: 50px; height: 38px;">
                                        <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" 
                                             class="w-100 h-100 object-fit-cover" 
                                             alt="Thumbnail"
                                             onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=100&auto=format&fit=crop';">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.72rem;"><?= htmlspecialchars($p['slug'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($p['author_name'] ?? 'Quản Trị Viên', ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td class="text-center font-monospace small">
                                    <?= number_format((int)$p['views_count']) ?>
                                </td>
                                <td>
                                    <?php if ($isPub): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Xuất bản
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-pencil-fill me-1"></i> Bản nháp
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($p['created_at'])) ?></small>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="/posts/<?= htmlspecialchars($p['slug'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Xem trên web">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/posts/<?= $pId ?>/edit" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="/admin/posts/<?= $pId ?>/delete" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
