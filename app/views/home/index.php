<?php
/** @var array|null $heroService */
/** @var array $categories */
/** @var array $services */
/** @var array $travelNews */
$heroService = $heroService ?? null;
$categories = $categories ?? [];
$services = $services ?? [];
$travelNews = $travelNews ?? [];
?>

<div class="container">
    <!-- 1. HERO BANNER: ĐIỂM HOT ĐÁNH GIÁ CAO -->
    <div class="hero-editorial mb-4">
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
                    <a href="/services" class="btn btn-pill-amber px-4 py-2 fs-6">
                        Khám phá tất cả trải nghiệm <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. FLOATING SEARCH BAR -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-12">
            <div class="floating-search-bar shadow-sm">
                <form action="/services" method="GET" class="row g-3 align-items-center">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted fw-bold mb-1"><i class="bi bi-geo-alt text-danger me-1"></i> Điểm đến / Vùng đất</label>
                        <input type="text" name="location" class="form-control" placeholder="Đà Lạt, Hội An, Tây Bắc, Phú Quốc...">
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
                        <label class="form-label small text-muted fw-bold mb-1"><i class="bi bi-cash-stack text-success me-1"></i> Ngân sách dự kiến</label>
                        <select name="price_range" class="form-select">
                            <option value="">Mọi mức giá</option>
                            <option value="under_500">Dưới 500.000 ₫</option>
                            <option value="500_1000">500.000 ₫ – 1.000.000 ₫</option>
                            <option value="over_1000">Trên 1.000.000 ₫</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-editorial w-100 py-2">
                            <i class="bi bi-search me-1"></i> Tìm Tour
                        </button>
                    </div>
                </form>

                <!-- Quick search tags -->
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2 border-top small text-muted">
                    <span class="fw-semibold"><i class="bi bi-fire text-danger me-1"></i> Tìm nhanh:</span>
                    <a href="/services?location=Đà+Lạt" class="badge bg-light text-secondary text-decoration-none border px-2 py-1">#Đà Lạt</a>
                    <a href="/services?location=Sa+Pa" class="badge bg-light text-secondary text-decoration-none border px-2 py-1">#Sa Pa</a>
                    <a href="/services?location=Hà+Giang" class="badge bg-light text-secondary text-decoration-none border px-2 py-1">#Hà Giang</a>
                    <a href="/services?location=Hội+An" class="badge bg-light text-secondary text-decoration-none border px-2 py-1">#Hội An</a>
                    <a href="/services?location=Phú+Quốc" class="badge bg-light text-secondary text-decoration-none border px-2 py-1">#Phú Quốc</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. WEBSERVICE WEATHER EXPLORER (DỰ BÁO THỜI TIẾT ĐIỂM ĐẾN NỔI BẬT) -->
    <div class="card border-0 rounded-4 shadow-sm p-4 mb-5 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1 mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <i class="bi bi-cloud-sun me-1"></i> WEBSERVICE OPENWEATHER JSON API
                </span>
                <h4 class="font-editorial fw-bold text-dark mb-0">Thời Tiết Điểm Đến Hôm Nay</h4>
            </div>
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i> Lựa chọn chuyến đi theo khí hậu lý tưởng của bạn
            </small>
        </div>

        <div class="row g-3">
            <!-- 1. Đà Lạt -->
            <div class="col-lg col-md-4 col-sm-6">
                <a href="/services?location=Đà+Lạt" class="card border rounded-3 p-3 h-100 text-decoration-none text-dark hover-shadow transition" style="background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark">Đà Lạt</strong>
                        <span class="badge bg-success-subtle text-success">Cao nguyên</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="https://openweathermap.org/img/wn/02d.png" alt="weather" style="width: 36px; height: 36px;">
                        <span class="fs-4 fw-bold font-editorial text-dark">18°C</span>
                    </div>
                    <div class="small text-muted mb-1">Se lạnh mù sương</div>
                    <small class="text-success d-block" style="font-size: 0.75rem;">Săn mây & cà phê đồi</small>
                </a>
            </div>

            <!-- 2. Sa Pa -->
            <div class="col-lg col-md-4 col-sm-6">
                <a href="/services?location=Sa+Pa" class="card border rounded-3 p-3 h-100 text-decoration-none text-dark hover-shadow transition" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark">Sa Pa</strong>
                        <span class="badge bg-secondary-subtle text-secondary">Vùng cao</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="https://openweathermap.org/img/wn/50d.png" alt="weather" style="width: 36px; height: 36px;">
                        <span class="fs-4 fw-bold font-editorial text-dark">15°C</span>
                    </div>
                    <div class="small text-muted mb-1">Mây mù mát lạnh</div>
                    <small class="text-primary d-block" style="font-size: 0.75rem;">Chinh phục Fansipan</small>
                </a>
            </div>

            <!-- 3. Hà Giang -->
            <div class="col-lg col-md-4 col-sm-6">
                <a href="/services?location=Hà+Giang" class="card border rounded-3 p-3 h-100 text-decoration-none text-dark hover-shadow transition" style="background: linear-gradient(180deg, #fefce8 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark">Hà Giang</strong>
                        <span class="badge bg-warning-subtle text-warning-emphasis">Núi đá</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="https://openweathermap.org/img/wn/01d.png" alt="weather" style="width: 36px; height: 36px;">
                        <span class="fs-4 fw-bold font-editorial text-dark">21°C</span>
                    </div>
                    <div class="small text-muted mb-1">Nắng dịu ráo trời</div>
                    <small class="text-warning-emphasis d-block" style="font-size: 0.75rem;">Đèo Mã Pí Lèng</small>
                </a>
            </div>

            <!-- 4. Hội An -->
            <div class="col-lg col-md-4 col-sm-6">
                <a href="/services?location=Hội+An" class="card border rounded-3 p-3 h-100 text-decoration-none text-dark hover-shadow transition" style="background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark">Hội An</strong>
                        <span class="badge bg-danger-subtle text-danger">Phố cổ</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="https://openweathermap.org/img/wn/02d.png" alt="weather" style="width: 36px; height: 36px;">
                        <span class="fs-4 fw-bold font-editorial text-dark">27°C</span>
                    </div>
                    <div class="small text-muted mb-1">Nắng ấm gió nhẹ</div>
                    <small class="text-danger d-block" style="font-size: 0.75rem;">Đèn lồng sông Hoài</small>
                </a>
            </div>

            <!-- 5. Phú Quốc -->
            <div class="col-lg col-md-4 col-sm-6">
                <a href="/services?location=Phú+Quốc" class="card border rounded-3 p-3 h-100 text-decoration-none text-dark hover-shadow transition" style="background: linear-gradient(180deg, #e0f2fe 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <strong class="text-dark">Phú Quốc</strong>
                        <span class="badge bg-primary-subtle text-primary">Biển đảo</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="https://openweathermap.org/img/wn/01d.png" alt="weather" style="width: 36px; height: 36px;">
                        <span class="fs-4 fw-bold font-editorial text-dark">29°C</span>
                    </div>
                    <div class="small text-muted mb-1">Nắng vàng biển êm</div>
                    <small class="text-primary d-block" style="font-size: 0.75rem;">Lặn ngắm san hô</small>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. DANH MỤC TRẢI NGHIỆM VĂN HÓA (CATEGORY CHIPS) -->
    <div class="mb-4">
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

    <!-- 5. DANH SÁCH GÓI TRẢI NGHIỆM BẢN ĐỊA (EDITORIAL CARDS GRID) -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="font-editorial fw-bold text-dark mb-1">Trải Nghiệm Cùng Dân Bản Địa Mới Nhất</h3>
                <p class="text-muted small mb-0">Những chuyến đi chân thực, đậm chất văn hóa địa phương và hỗ trợ lưu trú Homestay nhà dân</p>
            </div>
            <a href="/services" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 d-none d-sm-inline-block">
                Xem Thêm <i class="bi bi-arrow-right ms-1"></i>
            </a>
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
                                    <div class="small text-success bg-success-subtle px-2 py-1 rounded-2 mb-3 text-truncate" title="<?= htmlspecialchars($srv['homestay_name'], ENT_QUOTES, 'UTF-8') ?>">
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

    <!-- 6. 3D VIRTUAL TOUR TEASER (THREE.JS Z-SCROLL) -->
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

    <!-- 7. TRAVEL NEWS & TIPS (WEBSERVICE XML VNEXPRESS RSS FEED) -->
    <div class="mb-5" id="travel-news-section">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <i class="bi bi-rss-fill me-1"></i> WEBSERVICE XML RSS FEED
                </span>
                <h3 class="font-editorial fw-bold text-dark mb-0">Tin Tức & Cẩm Nang Du Lịch</h3>
                <p class="text-muted small mb-0">Cập nhật tin tức du lịch, xu hướng khám phá và cẩm nang hữu ích từ VnExpress Du Lịch</p>
            </div>
            <a href="https://vnexpress.net/du-lich" target="_blank" rel="noopener" class="text-danger text-decoration-none fw-semibold small">
                Nguồn: VnExpress Du Lịch <i class="bi bi-box-arrow-up-right ms-1"></i>
            </a>
        </div>

        <?php if (!empty($travelNews)): ?>
            <div class="row g-4">
                <?php foreach ($travelNews as $news): ?>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden bg-white hover-shadow transition">
                            <div class="position-relative" style="height: 200px; overflow: hidden; background: #f1f5f9;">
                                <img src="<?= htmlspecialchars($news['thumbnail'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                     class="w-100 h-100 object-fit-cover transition-transform"
                                     onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80'">
                                <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-3 rounded-pill px-2 py-1 small">
                                    <i class="bi bi-newspaper me-1"></i> <?= htmlspecialchars($news['source'] ?? 'VnExpress', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-clock me-1 text-primary"></i> <?= htmlspecialchars($news['pub_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <h5 class="fw-bold font-editorial text-dark mb-2" style="font-size: 1.05rem; line-height: 1.4;">
                                    <a href="<?= htmlspecialchars($news['link'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="text-decoration-none text-dark hover-text-primary">
                                        <?= htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.6;">
                                    <?= htmlspecialchars($news['summary'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <div class="pt-2 border-top">
                                    <a href="<?= htmlspecialchars($news['link'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        Đọc chi tiết <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 rounded-4 shadow-sm p-4 text-center text-muted bg-white">
                <i class="bi bi-rss fs-1 text-danger mb-2"></i>
                <p class="mb-0">Đang cập nhật nguồn tin tức du lịch...</p>
            </div>
        <?php endif; ?>
    </div>

</div>
