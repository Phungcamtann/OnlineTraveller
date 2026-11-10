<?php
/**
 * OnlineTraveller v2.2 — Home Page (Trang Chủ)
 * UI Specification: docs/UI_SPEC_FOR_AGENT.md (Section 3B - 3.2) & Mockup Image
 * Phase 6: Slider Banner với thumbnails trượt & Range Slider khoảng giá
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

// Resolve Top Slides for Slider Banner
$sliderSlides = [];
if ($heroService) {
    $sliderSlides[] = [
        'id' => (int)($heroService['id'] ?? 1),
        'title' => $heroService['title'] ?? 'Bình Minh Bên Hồ Tuyền Lâm',
        'desc' => $heroService['short_description'] ?? ($heroService['description'] ?? 'Trải nghiệm sương mù Đà Lạt, đón bình minh mộng mơ bên hồ cùng người dân bản địa.'),
        'image' => $heroService['banner_image'] ?? '/assets/images/tours/dalat-san-may.jpg',
        'host' => $heroService['host_name'] ?? 'Nguyễn Văn Bản Địa (Đà Lạt)',
        'location' => $heroService['location'] ?? 'Đà Lạt, Lâm Đồng',
        'rating' => (float)($heroService['avg_rating'] ?? ($heroService['rating'] ?? 5.0)),
    ];
}

if (!empty($services)) {
    foreach ($services as $srv) {
        if (!empty($sliderSlides) && (int)$srv['id'] === (int)$sliderSlides[0]['id']) {
            continue;
        }
        $sliderSlides[] = [
            'id' => (int)$srv['id'],
            'title' => $srv['title'],
            'desc' => !empty($srv['short_description']) ? $srv['short_description'] : (!empty($srv['description']) ? mb_substr(strip_tags($srv['description']), 0, 120, 'UTF-8') . '...' : 'Trải nghiệm du lịch bản địa độc đáo cùng người dân địa phương.'),
            'image' => !empty($srv['banner_image']) ? $srv['banner_image'] : '/assets/images/tours/dalat-san-may.jpg',
            'host' => $srv['host_name'] ?? 'Dân bản địa',
            'location' => $srv['location'] ?? 'Việt Nam',
            'rating' => (float)($srv['avg_rating'] ?? 5.0),
        ];
        if (count($sliderSlides) >= 4) {
            break;
        }
    }
}

// Fallback slides if services list is sparse
if (count($sliderSlides) < 3) {
    $fallbackSlides = [
        [
            'id' => 1,
            'title' => 'Bình Minh Bên Hồ Tuyền Lâm',
            'desc' => 'Trải nghiệm sương mù Đà Lạt, đón bình minh mộng mơ bên hồ cùng người dân bản địa.',
            'image' => '/assets/images/tours/dalat-san-may.jpg',
            'host' => 'Nguyễn Văn Bản Địa (Đà Lạt)',
            'location' => 'Đà Lạt, Lâm Đồng',
            'rating' => 4.9,
        ],
        [
            'id' => 2,
            'title' => 'Đêm Phố Cổ & Làng Gốm Thanh Hà',
            'desc' => 'Khám phá vẻ đẹp trầm mặc của di sản Hội An, hòa mình vào nghề làm gốm nghìn năm tuổi.',
            'image' => '/assets/images/tours/hoian-dem-pho-co.jpg',
            'host' => 'Lê Thị Thu (Hội An)',
            'location' => 'Hội An, Quảng Nam',
            'rating' => 4.8,
        ],
        [
            'id' => 3,
            'title' => 'Cung Đường Núi Đá Nở Hoa Đồng Văn',
            'desc' => 'Vượt đèo Mã Pí Lèng hùng vĩ, thưởng thức chén rượu ngô và khám phá bản sắc cao nguyên đá.',
            'image' => '/assets/images/tours/hagiang-nui-da.jpg',
            'host' => 'Vàng A Súa (Hà Giang)',
            'location' => 'Đồng Văn, Hà Giang',
            'rating' => 5.0,
        ],
        [
            'id' => 4,
            'title' => 'Hoàng Hôn Làng Chài Rạch Vẹm Phú Quốc',
            'desc' => 'Ngắm sao biển đỏ, thưởng thức hải sản tươi rói vừa đánh bắt và đón hoàng hôn thơ mộng.',
            'image' => '/assets/images/tours/phuquoc-lang-chai.jpg',
            'host' => 'Trần Văn Hải (Phú Quốc)',
            'location' => 'Phú Quốc, Kiên Giang',
            'rating' => 4.7,
        ],
    ];
    foreach ($fallbackSlides as $fb) {
        $exists = false;
        foreach ($sliderSlides as $s) {
            if ($s['title'] === $fb['title']) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $sliderSlides[] = $fb;
        }
        if (count($sliderSlides) >= 4) {
            break;
        }
    }
}

$firstSlide = $sliderSlides[0];
$featuredTitle = $firstSlide['title'];
$featuredDesc = $firstSlide['desc'];
$featuredImage = $firstSlide['image'];
$featuredHost = $firstSlide['host'];
$featuredLocation = $firstSlide['location'];
$featuredRating = (float)$firstSlide['rating'];
$featuredId = (int)$firstSlide['id'];

// Default max price from GET
$defaultMaxPrice = !empty($_GET['max_price']) ? (int)$_GET['max_price'] : 5000000;
?>

<div class="main-container">

    <!-- 1. SLIDER BANNER: CHỮ BÊN TRÁI + NÚT GLASS + THUMBNAILS TRƯỢT NHỎ -->
    <section class="container-xxl pt-2">
        <div class="row">
            <div class="col-12">
                <div class="hero-ambient-wrapper" id="homeHeroAmbientWrapper">
                    <div class="hero-banner-box position-relative" id="heroBannerBox" style="background-image: url('<?= htmlspecialchars($featuredImage, ENT_QUOTES, 'UTF-8') ?>');">
                        <div class="hero-banner-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-4 p-lg-5">
                            <div class="container-fluid p-0">
                                <div class="row align-items-center pt-4">
                                    <div class="col-lg-8 col-md-10">
                                        <div class="d-inline-flex align-items-center gap-2 mb-2">
                                            <span class="glass-pill px-3 py-1 small fw-semibold text-info" id="heroLocationBadge">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($featuredLocation, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                        <h1 class="font-editorial display-4 fw-bold text-white mb-3 lh-sm" id="heroTitle">
                                            <?= htmlspecialchars($featuredTitle, ENT_QUOTES, 'UTF-8') ?>
                                        </h1>
                                        <p class="text-light opacity-75 mb-4 fs-6" id="heroDesc" style="max-width: 600px;">
                                            <?= htmlspecialchars($featuredDesc, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                            <a href="/services/<?= $featuredId ?>" class="btn btn-glass-hero" id="heroCtaLink">
                                                Khám phá ngay <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                            <div class="small text-light opacity-75" id="heroMeta">
                                                Host: <strong class="text-white"><?= htmlspecialchars($featuredHost, ENT_QUOTES, 'UTF-8') ?></strong> &bull; 
                                                <span class="text-warning">&#9733; <?= number_format($featuredRating, 1) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dải Thumbnail Trượt Nhỏ Dưới Chân Banner -->
                            <div class="mt-4 pt-3 border-top border-light border-opacity-10 d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="banner-thumbs-container" id="bannerThumbsContainer">
                                    <?php foreach ($sliderSlides as $idx => $slide): ?>
                                        <div class="banner-thumb-item <?= $idx === 0 ? 'active' : '' ?>" 
                                             data-slide-index="<?= $idx ?>"
                                             data-id="<?= $slide['id'] ?>"
                                             data-title="<?= htmlspecialchars($slide['title'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-desc="<?= htmlspecialchars($slide['desc'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-image="<?= htmlspecialchars($slide['image'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-host="<?= htmlspecialchars($slide['host'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-location="<?= htmlspecialchars($slide['location'], ENT_QUOTES, 'UTF-8') ?>"
                                             data-rating="<?= number_format($slide['rating'], 1) ?>">
                                            <img src="<?= htmlspecialchars($slide['image'], ENT_QUOTES, 'UTF-8') ?>" 
                                                 alt="<?= htmlspecialchars($slide['location'], ENT_QUOTES, 'UTF-8') ?>"
                                                 onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100&auto=format&fit=crop&q=80'">
                                            <div class="text-start">
                                                <div class="fw-semibold small text-white text-truncate" style="max-width: 120px;">
                                                    <?= htmlspecialchars($slide['location'], ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                                <div class="text-light opacity-75" style="font-size: 0.72rem;">
                                                    ★ <?= number_format($slide['rating'], 1) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Nút Điều Hướng Chuyển Slide Nhỏ -->
                                <div class="d-none d-md-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-light btn-sm rounded-circle p-2 shadow-none border-opacity-25" id="btnPrevSlide" title="Tour trước" style="width: 36px; height: 36px;">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-sm rounded-circle p-2 shadow-none border-opacity-25" id="btnNextSlide" title="Tour kế tiếp" style="width: 36px; height: 36px;">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ambient Glow Blur dưới chân Banner -->
                    <div class="hero-ambient-glow" id="heroAmbientGlow" style="background-image: url('<?= htmlspecialchars($featuredImage, ENT_QUOTES, 'UTF-8') ?>');"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. THANH TÌM KIẾM NỔI TÁCH BIỆT DƯỚI BANNER (TÍCH HỢP RANGE SLIDER) -->
    <section class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <form action="/services" method="GET" class="glass-panel p-3 p-lg-4 shadow-lg" id="homeSearchForm">
                    <!-- Backward compatibility hidden input for price_range -->
                    <input type="hidden" name="price_range" id="inputHomePriceRange" value="<?= htmlspecialchars($_GET['price_range'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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

                        <!-- Trường 3: Ngân sách Range Slider -->
                        <div class="col-lg-3 col-md-3 search-col-divider">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="small fw-bold text-white mb-0" for="home-price-slider">
                                    Ngân sách tối đa
                                </label>
                                <span class="small fw-bold text-info" id="home-price-display">
                                    <?= ($defaultMaxPrice < 5000000) ? FormatHelper::formatVnd($defaultMaxPrice) : '5.000.000 ₫+' ?>
                                </span>
                            </div>
                            <input type="range" 
                                   class="form-range custom-price-range" 
                                   id="home-price-slider" 
                                   name="max_price" 
                                   min="200000" 
                                   max="5000000" 
                                   step="100000" 
                                   value="<?= $defaultMaxPrice ?>">
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
                                            'title' => 'Kinh nghiệm săn mây Đà Lạt mùa đông',
                                            'thumbnail' => '/assets/images/tours/dalat-san-may.jpg',
                                            'link' => '/posts',
                                            'source' => 'VnExpress RSS',
                                        ],
                                        [
                                            'title' => 'Những lưu ý khi trải nghiệm homestay nhà dân',
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

<!-- CLIENT JAVASCRIPT CHO SLIDER BANNER & RANGE SLIDER -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. RANGE SLIDER NGÂN SÁCH TẠI TRANG CHỦ
    const homeSlider = document.getElementById('home-price-slider');
    const homeDisplay = document.getElementById('home-price-display');
    const hiddenRange = document.getElementById('inputHomePriceRange');

    if (homeSlider && homeDisplay) {
        function updateHomePrice(val) {
            const num = parseInt(val, 10);
            if (num >= 5000000) {
                homeDisplay.textContent = '5.000.000 ₫+';
                if (hiddenRange) hiddenRange.value = '';
            } else {
                homeDisplay.textContent = new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
                if (hiddenRange) {
                    if (num <= 500000) hiddenRange.value = 'under_500';
                    else if (num <= 1000000) hiddenRange.value = '500_1000';
                    else hiddenRange.value = 'over_1000';
                }
            }
        }

        homeSlider.addEventListener('input', function() {
            updateHomePrice(this.value);
        });
    }

    // 2. SLIDER BANNER TƯƠNG TÁC VỚI THUMBNAIL
    const heroBox = document.getElementById('heroBannerBox');
    const heroGlow = document.getElementById('heroAmbientGlow');
    const heroTitle = document.getElementById('heroTitle');
    const heroDesc = document.getElementById('heroDesc');
    const heroLocation = document.getElementById('heroLocationBadge');
    const heroMeta = document.getElementById('heroMeta');
    const heroCta = document.getElementById('heroCtaLink');
    const thumbItems = document.querySelectorAll('.banner-thumb-item');
    const btnPrev = document.getElementById('btnPrevSlide');
    const btnNext = document.getElementById('btnNextSlide');

    let currentSlideIdx = 0;
    const totalSlides = thumbItems.length;
    let autoSlideTimer = null;

    function applySlide(index) {
        if (!totalSlides) return;
        currentSlideIdx = (index + totalSlides) % totalSlides;
        const activeThumb = thumbItems[currentSlideIdx];
        if (!activeThumb) return;

        // Update active class on thumbnails
        thumbItems.forEach(t => t.classList.remove('active'));
        activeThumb.classList.add('active');

        // Scroll active thumbnail into view smoothly
        activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

        const data = activeThumb.dataset;
        if (heroBox && data.image) {
            heroBox.style.backgroundImage = `url('${data.image}')`;
        }
        if (heroGlow && data.image) {
            heroGlow.style.backgroundImage = `url('${data.image}')`;
        }
        if (heroTitle && data.title) {
            heroTitle.textContent = data.title;
        }
        if (heroDesc && data.desc) {
            heroDesc.textContent = data.desc;
        }
        if (heroLocation && data.location) {
            heroLocation.innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i>${data.location}`;
        }
        if (heroMeta && data.host && data.rating) {
            heroMeta.innerHTML = `Host: <strong class="text-white">${data.host}</strong> &bull; <span class="text-warning">&#9733; ${data.rating}</span>`;
        }
        if (heroCta && data.id) {
            heroCta.setAttribute('href', `/services/${data.id}`);
        }
    }

    thumbItems.forEach((thumb, idx) => {
        thumb.addEventListener('click', function() {
            applySlide(idx);
            resetAutoSlide();
        });
    });

    if (btnPrev) {
        btnPrev.addEventListener('click', function() {
            applySlide(currentSlideIdx - 1);
            resetAutoSlide();
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function() {
            applySlide(currentSlideIdx + 1);
            resetAutoSlide();
        });
    }

    function startAutoSlide() {
        if (totalSlides > 1) {
            autoSlideTimer = setInterval(() => {
                applySlide(currentSlideIdx + 1);
            }, 6000);
        }
    }

    function resetAutoSlide() {
        if (autoSlideTimer) {
            clearInterval(autoSlideTimer);
            startAutoSlide();
        }
    }

    const sliderWrapper = document.getElementById('homeHeroAmbientWrapper');
    if (sliderWrapper) {
        sliderWrapper.addEventListener('mouseenter', () => {
            if (autoSlideTimer) clearInterval(autoSlideTimer);
        });
        sliderWrapper.addEventListener('mouseleave', () => {
            resetAutoSlide();
        });
    }

    startAutoSlide();
});
</script>
