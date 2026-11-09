<?php
/**
 * OnlineTraveller v2.2 — Home Page (Trang Chủ)
 * UI Specification: docs/UI_SPEC_FOR_AGENT.md (Section 3B - 3.2) & Mockup Image
 *
 * @var array|null $heroService
 * @var array $categories
 * @var array $services
 * @var array $travelNews
 */

$heroService = $heroService ?? null;
$categories = $categories ?? [];
$services = $services ?? [];
$travelNews = $travelNews ?? [];

// Resolve Featured Service for Hero Banner (from $heroService or first service in $services)
$featuredService = $heroService ?? ($services[0] ?? null);
$featuredTitle = $featuredService['title'] ?? 'Bình Minh Bên Hồ Tuyền Lâm';
$featuredDesc = $featuredService['short_description'] ?? ($featuredService['description'] ?? 'Trải nghiệm sương mù Đà Lạt, đón bình minh mộng mơ bên hồ cùng người dân bản địa.');
$featuredImage = $featuredService['banner_image'] ?? '/assets/images/tours/dalat-san-may.jpg';
$featuredHost = $featuredService['host_name'] ?? 'Nguyễn Văn Bản Địa (Đà Lạt)';
$featuredLocation = $featuredService['location'] ?? 'Đà Lạt, Lâm Đồng';
$featuredRating = (float)($featuredService['avg_rating'] ?? ($featuredService['rating'] ?? 5.0));
$featuredId = (int)($featuredService['id'] ?? 1);
?>

<div class="main-container">

    <!-- 1. HERO BANNER: CHỮ BÊN TRÁI + NÚT GLASS + HIỆU ỨNG AMBIENT BLUR -->
    <section class="container-xxl pt-2">
        <div class="row">
            <div class="col-12">
                <div class="hero-ambient-wrapper">
                    <div class="hero-banner-box position-relative" style="background-image: url('<?= htmlspecialchars($featuredImage, ENT_QUOTES, 'UTF-8') ?>');">
                        <div class="hero-banner-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
                            <div class="container">
                                <div class="row align-items-center pt-5 pb-4 px-lg-4">
                                    <div class="col-lg-7 col-md-9 pt-4">
                                        <h1 class="font-editorial display-4 fw-bold text-white mb-3 lh-sm">
                                            <?= htmlspecialchars($featuredTitle, ENT_QUOTES, 'UTF-8') ?>
                                        </h1>
                                        <p class="text-light opacity-75 mb-4 fs-6" style="max-width: 580px;">
                                            <?= htmlspecialchars($featuredDesc, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <div class="mb-3">
                                            <a href="/services/<?= $featuredId ?>" class="btn btn-glass-hero">
                                                Khám phá ngay <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                        <div class="small text-light opacity-75">
                                            Host: <?= htmlspecialchars($featuredHost, ENT_QUOTES, 'UTF-8') ?> &bull; 
                                            <?= htmlspecialchars($featuredLocation, ENT_QUOTES, 'UTF-8') ?> &bull; 
                                            <span class="text-warning">&#9733; <?= number_format($featuredRating, 1) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ambient Glow Blur dưới chân Banner -->
                    <div class="hero-ambient-glow" style="background-image: url('<?= htmlspecialchars($featuredImage, ENT_QUOTES, 'UTF-8') ?>');"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. THANH TÌM KIẾM NỔI TÁCH BIỆT DƯỚI BANNER -->
    <section class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="/services" method="GET" class="glass-panel p-3 p-lg-4 shadow-lg">
                    <div class="row g-3 align-items-center">
                        <!-- Trường 1: Điểm đến -->
                        <div class="col-lg-4 col-md-4 search-col-divider">
                            <label class="d-block small fw-bold text-white mb-1" for="search-location">
                                Điểm đến?
                            </label>
                            <input type="text" 
                                   id="search-location"
                                   name="location" 
                                   class="form-control bg-transparent border-0 text-white p-0 shadow-none search-field-input" 
                                   placeholder="Tìm địa danh" 
                                   value="<?= htmlspecialchars($_GET['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <!-- Trường 2: Danh mục -->
                        <div class="col-lg-3 col-md-3 search-col-divider">
                            <label class="d-block small fw-bold text-white mb-1" for="search-category">
                                Danh mục
                            </label>
                            <select id="search-category" 
                                    name="category_id" 
                                    class="form-select bg-transparent border-0 text-white p-0 shadow-none search-field-select">
                                <option value="" class="bg-dark text-white">Văn hóa, ẩm thực</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>" class="bg-dark text-white" <?= (isset($_GET['category_id']) && (int)$_GET['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Trường 3: Ngân sách -->
                        <div class="col-lg-3 col-md-3 search-col-divider">
                            <label class="d-block small fw-bold text-white mb-1" for="search-price">
                                Ngân sách
                            </label>
                            <select id="search-price" 
                                    name="price_range" 
                                    class="form-select bg-transparent border-0 text-white p-0 shadow-none search-field-select">
                                <option value="" class="bg-dark text-white">Khoảng giá</option>
                                <option value="under_500" class="bg-dark text-white" <?= (($_GET['price_range'] ?? '') === 'under_500') ? 'selected' : '' ?>>Dưới 500k</option>
                                <option value="500_1000" class="bg-dark text-white" <?= (($_GET['price_range'] ?? '') === '500_1000') ? 'selected' : '' ?>>500k – 1 triệu</option>
                                <option value="over_1000" class="bg-dark text-white" <?= (($_GET['price_range'] ?? '') === 'over_1000') ? 'selected' : '' ?>>Trên 1 triệu</option>
                            </select>
                        </div>

                        <!-- Trường 4: Nút Tìm kiếm -->
                        <div class="col-lg-2 col-md-2">
                            <button type="submit" class="btn btn-alpine w-100 py-2">
                                Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 3. THỜI TIẾT ĐIỂM ĐẾN HÔM NAY (5 CARD KÍNH MỜ) -->
    <section class="container mt-5">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="fw-bold text-white mb-0">Thời Tiết Điểm Đến Hôm Nay</h4>
            </div>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php
            $destinationsWeather = [
                ['city' => 'Đà Lạt', 'temp' => '18°C', 'desc' => 'se lạnh, sương mù', 'query' => 'Đà Lạt', 'icon' => '02d', 'bs_icon' => 'bi-cloud-sun text-info'],
                ['city' => 'Sa Pa', 'temp' => '15°C', 'desc' => 'mù sương', 'query' => 'Sa Pa', 'icon' => '50d', 'bs_icon' => 'bi-cloud-fog2 text-info'],
                ['city' => 'Hà Giang', 'temp' => '21°C', 'desc' => 'nắng nhẹ', 'query' => 'Hà Giang', 'icon' => '01d', 'bs_icon' => 'bi-sun text-warning'],
                ['city' => 'Hội An', 'temp' => '27°C', 'desc' => 'nắng ấm', 'query' => 'Hội An', 'icon' => '02d', 'bs_icon' => 'bi-cloud-sun text-warning'],
                ['city' => 'Phú Quốc', 'temp' => '29°C', 'desc' => 'nắng vàng, biển êm', 'query' => 'Phú Quốc', 'icon' => '01d', 'bs_icon' => 'bi-brightness-high text-warning'],
            ];
            ?>
            <?php foreach ($destinationsWeather as $w): ?>
                <div class="col">
                    <a href="/services?location=<?= urlencode($w['query']) ?>" class="glass-panel p-3 h-100 d-block text-decoration-none hover-lift">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-semibold text-white" style="font-size: 0.95rem;"><?= htmlspecialchars($w['city'], ENT_QUOTES, 'UTF-8') ?></span>
                            <img src="https://openweathermap.org/img/wn/<?= $w['icon'] ?>.png" 
                                 alt="<?= htmlspecialchars($w['desc'], ENT_QUOTES, 'UTF-8') ?>" 
                                 width="36" height="36"
                                 style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                            <i class="bi <?= $w['bs_icon'] ?> fs-5" style="display: none;"></i>
                        </div>
                        <div class="fs-2 fw-bold text-info mb-1 lh-1"><?= htmlspecialchars($w['temp'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-light opacity-75"><?= htmlspecialchars($w['desc'], ENT_QUOTES, 'UTF-8') ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 4. KHÁM PHÁ ĐỊA PHƯƠNG + NÚT MỞ RỘNG TAG + 4 CARD TRẢI NGHIỆM TRÀN ẢNH -->
    <section class="container mt-5">
        <!-- Hàng tiêu đề & Nút mở rộng Tag -->
        <div class="row align-items-center justify-content-between mb-3">
            <div class="col-auto">
                <h3 class="fw-bold text-white mb-0">Khám Phá Địa Phương</h3>
            </div>
            <div class="col-auto">
                <button class="btn btn-alpine btn-sm px-3 py-2" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#topicTagsCollapse"
                        aria-expanded="true"
                        aria-controls="topicTagsCollapse">
                    Tìm theo chủ đề &#9662;
                </button>
            </div>
        </div>

        <!-- Hàng Collapse chứa các Tag chủ đề -->
        <div class="collapse show" id="topicTagsCollapse">
            <div class="row mb-4">
                <div class="col-12 d-flex flex-wrap gap-2">
                    <a href="/services" class="glass-pill px-3 py-1 small text-decoration-none text-white">Tất cả</a>
                    <a href="/services?category_id=1" class="glass-pill px-3 py-1 small text-decoration-none text-white">Ẩm thực bản địa</a>
                    <a href="/services?category_id=2" class="glass-pill px-3 py-1 small text-decoration-none text-white">Săn mây & Trekking</a>
                    <a href="/services?category_id=3" class="glass-pill px-3 py-1 small text-decoration-none text-white">Làng nghề di sản</a>
                    <a href="/services?category_id=4" class="glass-pill px-3 py-1 small text-decoration-none text-white">Homestay nhà dân</a>
                    <?php foreach ($categories as $cat): ?>
                        <?php if (!in_array((int)$cat['id'], [1, 2, 3, 4], true)): ?>
                            <a href="/services?category_id=<?= (int)$cat['id'] ?>" class="glass-pill px-3 py-1 small text-decoration-none text-white">
                                <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Hàng Lưới 4 Card Trải Nghiệm tràn ảnh -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php
            // Lấy 4 dịch vụ mẫu chuẩn mockup nếu có trong database, hoặc fallback mẫu
            $displayServices = array_slice($services, 0, 4);
            if (empty($displayServices)) {
                $displayServices = [
                    [
                        'id' => 1,
                        'title' => 'Săn mây & Cà phê bản địa',
                        'location' => 'Đà Lạt, Lâm Đồng',
                        'base_price' => 450000,
                        'avg_rating' => 4.9,
                        'banner_image' => '/assets/images/tours/dalat-san-may.jpg',
                    ],
                    [
                        'id' => 2,
                        'title' => 'Đêm phố cổ & Làng gốm',
                        'location' => 'Hội An, Quảng Nam',
                        'base_price' => 380000,
                        'avg_rating' => 4.8,
                        'banner_image' => '/assets/images/tours/hoian-dem-pho-co.jpg',
                    ],
                    [
                        'id' => 3,
                        'title' => 'Cung đường đá nở hoa',
                        'location' => 'Hà Giang',
                        'base_price' => 650000,
                        'avg_rating' => 5.0,
                        'banner_image' => '/assets/images/tours/hagiang-nui-da.jpg',
                    ],
                    [
                        'id' => 4,
                        'title' => 'Làng chài & Hoàng hôn',
                        'location' => 'Phú Quốc',
                        'base_price' => 520000,
                        'avg_rating' => 4.7,
                        'banner_image' => '/assets/images/tours/phuquoc-lang-chai.jpg',
                    ],
                ];
            }
            ?>
            <?php foreach ($displayServices as $srv): ?>
                <?php
                    $srvPrice = (float)($srv['base_price'] ?? 0);
                    $priceLabel = ($srvPrice >= 1000) ? 'Giá ' . round($srvPrice / 1000) . 'k' : number_format($srvPrice, 0, ',', '.') . ' ₫';
                    $srvImg = !empty($srv['banner_image']) ? $srv['banner_image'] : '/assets/images/tours/dalat-san-may.jpg';
                ?>
                <div class="col">
                    <a href="/services/<?= (int)$srv['id'] ?>" class="text-decoration-none d-block h-100">
                        <div class="experience-poster-card shadow">
                            <img src="<?= htmlspecialchars($srvImg, ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80'">
                            
                            <!-- Badge rating góc trên phải -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="glass-pill px-2 py-1 small fw-semibold text-white">
                                    ★ <?= number_format((float)($srv['avg_rating'] ?? 5.0), 1) ?>
                                </span>
                            </div>

                            <!-- Lớp phủ overlay chân ảnh -->
                            <div class="experience-poster-overlay">
                                <div></div>
                                <div>
                                    <div class="fw-bold fs-6 text-white text-truncate mb-1">
                                        <?= htmlspecialchars($srv['location'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="small text-light opacity-75 text-truncate mb-1">
                                        <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="fw-bold text-info" style="font-size: 0.95rem;">
                                        <?= htmlspecialchars($priceLabel, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 5. TIN TỨC DU LỊCH RSS (2 CỘT: TRÁI GIỚI THIỆU, PHẢI 2 CARD BÀI BÁO) -->
    <section class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="glass-panel p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <!-- Cột trái: Tiêu đề & Giới thiệu -->
                        <div class="col-lg-4">
                            <div class="small text-info fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">
                                CẨM NANG & BẢN TIN RSS
                            </div>
                            <h3 class="fw-bold text-white mb-2 font-editorial">
                                Tin tức du lịch
                            </h3>
                            <p class="small text-light opacity-75 mb-4">
                                Nhịp sống bản địa, kinh nghiệm xê dịch và những cẩm nang du lịch đáng quan tâm nhất hôm nay.
                            </p>
                            <a href="/posts" class="btn btn-glass-hero btn-sm px-4">
                                Xem thêm tin &rarr;
                            </a>
                        </div>

                        <!-- Cột phải: 2 bài tin mới nhất -->
                        <div class="col-lg-8">
                            <div class="row row-cols-1 row-cols-md-2 g-4">
                                <?php
                                $rssItems = array_slice($travelNews, 0, 2);
                                if (empty($rssItems)) {
                                    $rssItems = [
                                        [
                                            'title' => 'Kinh nghiệm săn mây Đà Lạt mùa đông...',
                                            'thumbnail' => '/assets/images/tours/dalat-san-may.jpg',
                                            'link' => '/posts',
                                            'source' => 'VnExpress RSS',
                                        ],
                                        [
                                            'title' => 'Những lưu ý khi trải nghiệm homestay nhà dân...',
                                            'thumbnail' => '/assets/images/tours/phuquoc-lang-chai.jpg',
                                            'link' => '/posts',
                                            'source' => 'VnExpress RSS',
                                        ],
                                    ];
                                }
                                ?>
                                <?php foreach ($rssItems as $post): ?>
                                    <div class="col">
                                        <a href="<?= htmlspecialchars($post['link'] ?? '/posts', ENT_QUOTES, 'UTF-8') ?>" 
                                           class="glass-panel p-3 h-100 d-flex flex-column text-decoration-none hover-lift"
                                           <?= (!empty($post['link']) && str_starts_with($post['link'], 'http')) ? 'target="_blank" rel="noopener"' : '' ?>>
                                            <div class="rounded-3 overflow-hidden mb-3" style="aspect-ratio: 16/9; background: rgba(11, 25, 36, 0.6);">
                                                <img src="<?= htmlspecialchars($post['thumbnail'] ?? '/assets/images/tours/dalat-san-may.jpg', ENT_QUOTES, 'UTF-8') ?>" 
                                                     alt="<?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                     class="w-100 h-100 object-fit-cover"
                                                     onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80'">
                                            </div>
                                            <h6 class="fw-bold text-white mb-2 line-clamp-2" style="font-size: 0.95rem; line-height: 1.4;">
                                                <?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </h6>
                                            <div class="small text-secondary opacity-75 mt-auto" style="font-size: 0.78rem;">
                                                <?= htmlspecialchars($post['source'] ?? 'VnExpress RSS', ENT_QUOTES, 'UTF-8') ?> &bull; 5 phút đọc
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
