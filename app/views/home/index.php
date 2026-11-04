<?php
/** @var array|null $heroService */
/** @var array $categories */
/** @var array $services */
?>

<div class="container">
    <!-- 1. HERO BANNER: ĐIỂM HOT ĐÁNH GIÁ CAO -->
    <div class="hero-editorial">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="hero-badge mb-3 d-inline-block">
                    <i class="bi bi-star-fill text-warning me-1"></i> Trải Nghiệm Bản Địa Nổi Bật Nhất
                </span>
                
                <?php if ($heroService): ?>
                    <h1 class="font-editorial text-white mb-3">
                        <?= htmlspecialchars($heroService['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    
                    <p class="text-white-50 fs-6 mb-4" style="max-width: 650px;">
                        <?= htmlspecialchars($heroService['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="/services/<?= (int)$heroService['id'] ?>" class="btn btn-pill-amber px-4 py-2 fs-6">
                            Khám phá ngay <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="/services/<?= (int)$heroService['id'] ?>#virtual-tour" class="btn btn-outline-light rounded-pill px-4 py-2 fs-6">
                            <i class="bi bi-badge-3d text-warning me-1"></i> Xem Trình Chiếu 3D Từ Xa
                        </a>
                        <div class="text-white-50 small ps-2">
                            <i class="bi bi-person-circle text-white me-1"></i> Host: <strong><?= htmlspecialchars($heroService['host_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span class="mx-2">•</span>
                            <i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($heroService['location'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="mx-2">•</span>
                            <span class="text-warning fw-bold">★ <?= number_format((float)$heroService['avg_rating'], 1) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <h1 class="font-editorial text-white mb-3">
                        OnlineTraveller — Du Lịch & Khám Phá Cùng Dân Bản Địa
                    </h1>
                    <p class="text-white-50 fs-6 mb-4">
                        Khám phá vẻ đẹp thực tế qua không gian trình chiếu 3D có chiều sâu, trò chuyện từ xa và đặt lịch trải nghiệm thực tế cùng Homestay nhà dân bản xứ.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. FLOATING SEARCH BAR -->
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="floating-search-bar">
                <form action="/services" method="GET" class="row g-3 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted fw-bold mb-1"><i class="bi bi-geo-alt text-danger me-1"></i> Điểm đến / Vùng đất</label>
                        <input type="text" name="location" class="form-control" placeholder="Đà Lạt, Hội An, Tây Bắc...">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted fw-bold mb-1"><i class="bi bi-grid text-primary me-1"></i> Danh mục văn hóa</label>
                        <select name="category_id" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted fw-bold mb-1"><i class="bi bi-cash-stack text-success me-1"></i> Ngân sách</label>
                        <select name="price_range" class="form-select">
                            <option value="">Mọi mức giá</option>
                            <option value="under_500">Dưới 500.000 ₫</option>
                            <option value="500_1000">500.000 ₫ – 1.000.000 ₫</option>
                            <option value="over_1000">Trên 1.000.000 ₫</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-editorial w-100 py-2">
                            <i class="bi bi-search me-1"></i> Tìm Kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. DANH MỤC TRẢI NGHIỆM VĂN HÓA (CATEGORY CHIPS) -->
    <div class="mt-5 pt-3 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 mb-1">
                    <i class="bi bi-compass me-1"></i> Bản Sắc Địa Phương
                </span>
                <h3 class="font-editorial fw-bold text-dark mb-0">Khám Phá Theo Chủ Đề</h3>
            </div>
            <a href="/services" class="text-primary text-decoration-none fw-semibold small">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($categories as $cat): ?>
                <a href="/services?category_id=<?= (int)$cat['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 text-dark bg-white border">
                    <i class="bi <?= htmlspecialchars($cat['icon'] ?? 'bi-tag', ENT_QUOTES, 'UTF-8') ?> text-primary me-1"></i>
                    <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. DANH SÁCH GÓI TRẢI NGHIỆM BẢN ĐỊA (EDITORIAL CARDS GRID) -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="font-editorial fw-bold text-dark mb-1">Trải Nghiệm Cùng Dân Bản Địa Mới Nhất</h3>
                <p class="text-muted small mb-0">Những chuyến đi chân thực, đậm chất văn hóa địa phương và hỗ trợ lưu trú Homestay nhà dân</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (empty($services)): ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Chưa có gói trải nghiệm nào được kích hoạt.
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
                                        Xem <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 5. 3D VIRTUAL TOUR TEASER (THREE.JS Z-SCROLL) -->
    <div class="card border-0 rounded-4 shadow-sm p-4 mb-5" style="background: linear-gradient(135deg, #0f2922 0%, #0f172a 100%); color: #ffffff;">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2 fw-semibold">
                    <i class="bi bi-badge-3d-fill me-1"></i> Đột Phá Công Nghệ Du Lịch Từ Xa
                </span>
                <h3 class="font-editorial text-white fw-bold mb-2">
                    Không Gian Cuộn 3D Three.js Z-Scroll
                </h3>
                <p class="text-light opacity-75 small mb-3">
                    Du khách ngồi tại nhà vẫn có thể trải nghiệm góc nhìn như đang thực sự dạo bước tại điểm đến qua trục chiều sâu Camera 3D, đọc những câu chuyện văn hóa chân thực của người bản xứ và xem video quay thực tế tại từng trạm dừng chân.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/services/1#virtual-tour" class="btn btn-pill-amber btn-sm px-3 py-2">
                        <i class="bi bi-play-circle me-1"></i> Trải Nghiệm Thử Trạm 3D LangBiang
                    </a>
                    <a href="/services/2#virtual-tour" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                        <i class="bi bi-play-circle me-1"></i> Trải Nghiệm Thử Trạm 3D Hội An
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25">
                    <i class="bi bi-camera-reels text-warning fs-1 d-block mb-2"></i>
                    <h6 class="fw-bold text-white mb-1">Tích Hợp 4 Webservices</h6>
                    <small class="text-light opacity-75 d-block">
                        Google Maps (JSON) • OpenWeather (JSON) • RSS Du Lịch (XML) • MoMo Sandbox
                    </small>
                </div>
            </div>
        </div>
    </div>

</div>
