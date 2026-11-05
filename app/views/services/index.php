<?php
/** @var array $services */
/** @var array $categories */
/** @var int $selectedCategoryId */
/** @var string $keyword */
$services = $services ?? [];
$categories = $categories ?? [];
$selectedCategoryId = $selectedCategoryId ?? 0;
$keyword = $keyword ?? '';
?>

<div class="container py-3">
    <!-- Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="font-editorial fw-bold text-dark mb-1">Khám Phá Trải Nghiệm Bản Địa</h2>
            <p class="text-muted small mb-0">Du lịch cùng người bản xứ, hòa mình vào không gian văn hóa thực tế và nghỉ dưỡng tại Homestay nhà dân</p>
        </div>
        <span class="badge badge-soft-success px-3 py-2 fs-6">
            <i class="bi bi-compass me-1"></i> <?= count($services) ?> Gói Trải Nghiệm Có Sẵn
        </span>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
        <form action="/services" method="GET" class="row g-2 align-items-center">
            <div class="col-lg-5 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="location" class="form-control bg-light border-0" placeholder="Tìm theo tên tour, địa danh (Đà Lạt, Hội An...)" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <select name="category_id" class="form-select bg-light border-0">
                    <option value="">-- Tất cả danh mục văn hóa --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= $selectedCategoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-2">
                <button type="submit" class="btn btn-editorial w-100">
                    Lọc Kết Quả
                </button>
            </div>
        </form>
    </div>

    <!-- Experiences Grid -->
    <div class="row g-4">
        <?php if (empty($services)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                <h5 class="fw-bold text-muted">Không tìm thấy trải nghiệm phù hợp</h5>
                <p class="small text-muted">Vui lòng thử tìm kiếm với từ khóa khác hoặc bỏ bộ lọc danh mục.</p>
                <a href="/services" class="btn btn-sm btn-outline-dark rounded-pill px-4">Xem tất cả trải nghiệm</a>
            </div>
        <?php else: ?>
            <?php foreach ($services as $srv): ?>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="card-tour">
                        <div class="tour-thumbnail">
                            <img src="<?= htmlspecialchars($srv['banner_image'] ?? '/assets/images/tours/dalat-san-may.jpg', ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80'">
                            <span class="tour-badge-category">
                                <?= htmlspecialchars($srv['category_name'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="tour-rating">
                                <i class="bi bi-star-fill text-warning me-1"></i><?= number_format((float)$srv['avg_rating'], 1) ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <div class="host-meta">
                                <i class="bi bi-person-badge text-success"></i>
                                <span>Host: <strong><?= htmlspecialchars($srv['host_name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                            </div>

                            <a href="/services/<?= (int)$srv['id'] ?>" class="tour-title">
                                <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>

                            <div class="small text-muted mb-2">
                                <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($srv['location'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="mx-1">•</span>
                                <i class="bi bi-clock me-1"></i><?= (int)$srv['duration_days'] ?> ngày
                            </div>

                            <?php if (!empty($srv['homestay_name'])): ?>
                                <div class="small text-success bg-success-subtle px-2 py-1 rounded-2 mb-3">
                                    <i class="bi bi-house-door me-1"></i> Homestay: <?= htmlspecialchars($srv['homestay_name'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">GIÁ TRẢI NGHIỆM</small>
                                    <span class="tour-price">
                                        <?= FormatHelper::formatVnd((int)$srv['base_price']) ?>
                                    </span>
                                </div>
                                <a href="/services/<?= (int)$srv['id'] ?>" class="btn btn-sm btn-editorial px-3 py-1">
                                    Chi tiết <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
