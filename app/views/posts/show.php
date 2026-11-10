<?php
/**
 * View: Chi Tiết Bài Viết Cẩm Nang Du Lịch (Public)
 * File: app/views/posts/show.php
 *
 * @var array $post Chi tiết bài viết
 * @var array $user Người dùng hiện tại
 */

$title = htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8');
$thumb = $post['thumbnail'] ?: '/assets/images/default-blog.jpg';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <!-- BREADCRUMB -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="/posts" class="text-decoration-none text-muted">Cẩm nang du lịch</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars(mb_substr($post['title'], 0, 30) . '...', ENT_QUOTES, 'UTF-8') ?></li>
                </ol>
            </nav>

            <article class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <!-- ARTICLE HEADER -->
                <header class="border-bottom pb-4 mb-4">
                    <h1 class="fw-bold font-editorial text-dark mb-3"><?= $title ?></h1>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 32px; height: 32px;">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <span>Tác giả: <strong><?= htmlspecialchars($post['author_name'] ?? 'Ban Quản Trị', ENT_QUOTES, 'UTF-8') ?></strong></span>
                            <?php
                            $authorRole = $post['author_role'] ?? 'tourist';
                            $roleLabel = match($authorRole) {
                                'host' => 'Host Bản Địa',
                                'admin' => 'Ban Quản Trị',
                                default => 'Du Khách',
                            };
                            $roleBadge = match($authorRole) {
                                'host' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                'admin' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                default => 'bg-primary-subtle text-primary border border-primary-subtle',
                            };
                            ?>
                            <span class="badge rounded-pill <?= $roleBadge ?> px-2 py-1" style="font-size: 0.72rem;"><?= $roleLabel ?></span>
                            <span class="mx-1">•</span>
                            <span><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
                        </div>
                        <div>
                            <span class="badge bg-light text-muted border rounded-pill px-3 py-1">
                                <i class="bi bi-eye me-1"></i><?= (int)$post['views_count'] ?> lượt xem
                            </span>
                        </div>
                    </div>
                </header>

                <!-- THUMBNAIL BANNER -->
                <?php if (!empty($post['thumbnail'])): ?>
                    <div class="rounded-4 overflow-hidden mb-4 shadow-sm" style="max-height: 420px;">
                        <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" 
                             class="w-100 h-100 object-fit-cover" 
                             alt="<?= $title ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=800&auto=format&fit=crop';">
                    </div>
                <?php endif; ?>

                <!-- ARTICLE CONTENT BODY -->
                <div class="article-content text-dark fs-6 lh-lg mb-5">
                    <?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')) ?>
                </div>

                <!-- FOOTER & SHARING -->
                <footer class="border-top pt-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <a href="/posts" class="btn btn-outline-dark rounded-pill px-4 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Tất Cả Bài Viết
                    </a>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Chia sẻ:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết bài viết!');">
                            <i class="bi bi-share"></i>
                        </button>
                    </div>
                </footer>
            </article>
        </div>
    </div>
</div>
