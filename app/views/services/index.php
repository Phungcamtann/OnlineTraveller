<?php
/** @var array $services */
/** @var array $categories */
/** @var int $selectedCategoryId */
/** @var string $keyword */
/** @var int $maxPrice */
/** @var string $priceRange */
$services = $services ?? [];
$categories = $categories ?? [];
$selectedCategoryId = $selectedCategoryId ?? 0;
$keyword = $keyword ?? '';
$maxPrice = isset($maxPrice) && $maxPrice > 0 ? (int)$maxPrice : 5000000;
$priceRange = $priceRange ?? '';
?>

<div class="main-container">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-light opacity-75"><i class="bi bi-house"></i> Trang chủ</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Trải Nghiệm Bản Địa</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="font-editorial display-6 fw-bold text-white mb-1">Khám Phá Trải Nghiệm Bản Địa</h2>
                <p class="text-light opacity-75 small mb-0">Du lịch cùng người bản xứ, hòa mình vào không gian văn hóa thực tế và nghỉ dưỡng tại Homestay nhà dân</p>
            </div>
            <span class="glass-pill px-3 py-2 fs-6 text-white">
                <i class="bi bi-compass text-info me-1"></i> <?= count($services) ?> Gói Trải Nghiệm Có Sẵn
            </span>
        </div>

        <!-- Filter Bar with Range Slider -->
        <div class="glass-panel p-3 p-lg-4 mb-4 shadow-lg">
            <form action="/services" method="GET" class="row g-3 align-items-center" id="serviceSearchForm">
                <input type="hidden" name="price_range" id="inputServicePriceRange" value="<?= htmlspecialchars($priceRange, ENT_QUOTES, 'UTF-8') ?>">

                <!-- 1. Điểm đến -->
                <div class="col-lg-4 col-md-4">
                    <label class="d-block small fw-bold text-white mb-1">Điểm đến / Địa danh</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-search"></i></span>
                        <input type="text" name="location" class="form-control bg-dark border-0 text-white" placeholder="Tìm theo tên tour, địa danh (Đà Lạt, Hội An...)" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <!-- 2. Danh mục văn hóa -->
                <div class="col-lg-3 col-md-3">
                    <label class="d-block small fw-bold text-white mb-1">Danh mục văn hóa</label>
                    <select name="category_id" class="form-select bg-dark border-0 text-white">
                        <option value="" class="bg-dark text-white">-- Tất cả danh mục văn hóa --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" class="bg-dark text-white" <?= $selectedCategoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 3. Thanh trượt ngân sách Range Slider -->
                <div class="col-lg-3 col-md-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="small fw-bold text-white mb-0" for="service-price-slider">
                            Ngân sách tối đa
                        </label>
                        <span class="small fw-bold text-info" id="service-price-display">
                            <?= ($maxPrice < 5000000) ? FormatHelper::formatVnd($maxPrice) : '5.000.000 ₫+' ?>
                        </span>
                    </div>
                    <input type="range" 
                           class="form-range custom-price-range" 
                           id="service-price-slider" 
                           name="max_price" 
                           min="200000" 
                           max="5000000" 
                           step="100000" 
                           value="<?= $maxPrice ?>">
                </div>

                <!-- 4. Nút lọc -->
                <div class="col-lg-2 col-md-2">
                    <label class="d-block small fw-bold text-transparent mb-1 d-none d-md-block">&nbsp;</label>
                    <button type="submit" class="btn btn-alpine w-100 py-2">
                        <i class="bi bi-filter me-1"></i> Lọc Kết Quả
                    </button>
                </div>
            </form>

            <!-- Popular Destination Tags -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top border-light border-opacity-10 small text-light opacity-90">
                <span class="fw-semibold text-white"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Điểm đến hot:</span>
                <a href="/services?location=Đà+Lạt" class="glass-pill px-3 py-1 text-white text-decoration-none">Đà Lạt</a>
                <a href="/services?location=Sa+Pa" class="glass-pill px-3 py-1 text-white text-decoration-none">Sa Pa</a>
                <a href="/services?location=Hà+Giang" class="glass-pill px-3 py-1 text-white text-decoration-none">Hà Giang</a>
                <a href="/services?location=Hội+An" class="glass-pill px-3 py-1 text-white text-decoration-none">Hội An</a>
                <a href="/services?location=Phú+Quốc" class="glass-pill px-3 py-1 text-white text-decoration-none">Phú Quốc</a>
                <?php if (!empty($keyword) || $selectedCategoryId > 0 || $maxPrice < 5000000): ?>
                    <a href="/services" class="badge bg-danger-subtle text-danger text-decoration-none border border-danger-subtle ms-auto px-3 py-2 rounded-pill">
                        <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Experiences Grid (Card Dark-Alpine Tràn Ảnh 3/4) -->
        <div class="row g-4">
            <?php if (empty($services)): ?>
                <div class="col-12 text-center py-5 glass-panel p-5">
                    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-white font-editorial mb-2">Không Tìm Thấy Trải Nghiệm Phù Hợp</h5>
                    <p class="small text-light opacity-75 mb-4">Vui lòng thử tìm kiếm với từ khóa khác hoặc điều chỉnh lại thanh trượt khoảng giá.</p>
                    <a href="/services" class="btn btn-alpine rounded-pill px-4 py-2">
                        <i class="bi bi-arrow-repeat me-1"></i> Xem Tất Cả Trải Nghiệm
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($services as $srv): ?>
                    <?php
                        $srvPrice = (float)($srv['base_price'] ?? 0);
                        $priceLabel = ($srvPrice >= 1000) ? 'Giá ' . round($srvPrice / 1000) . 'k' : number_format($srvPrice, 0, ',', '.') . ' ₫';
                        $srvImg = !empty($srv['banner_image']) ? $srv['banner_image'] : '/assets/images/tours/dalat-san-may.jpg';
                    ?>
                    <div class="col-lg-3 col-md-6 col-12">
                        <a href="/services/<?= (int)$srv['id'] ?>" class="text-decoration-none d-block h-100">
                            <div class="card-tour experience-poster-card shadow">
                                <img src="<?= htmlspecialchars($srvImg, ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>"
                                     onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&auto=format&fit=crop&q=80'">
                                
                                <!-- Badge Danh mục góc trên trái -->
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="glass-pill px-2 py-1 small fw-semibold text-white" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($srv['category_name'] ?? 'Bản địa', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <!-- Badge rating góc trên phải -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="glass-pill px-2 py-1 small fw-semibold text-white" style="font-size: 0.75rem;">
                                        ★ <?= number_format((float)($srv['avg_rating'] ?? 5.0), 1) ?>
                                    </span>
                                </div>

                                <!-- Lớp phủ overlay chân ảnh -->
                                <div class="experience-poster-overlay">
                                    <div></div>
                                    <div>
                                        <div class="fw-bold fs-6 text-white text-truncate mb-1">
                                            <i class="bi bi-geo-alt-fill text-danger me-1" style="font-size: 0.85rem;"></i><?= htmlspecialchars($srv['location'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="small text-light opacity-90 fw-medium text-truncate mb-1" title="<?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="small text-light opacity-75 text-truncate mb-1" style="font-size: 0.78rem;">
                                            <i class="bi bi-person text-info me-1"></i>Host: <?= htmlspecialchars($srv['host_name'] ?? 'Dân bản địa', ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-1">
                                            <div>
                                                <div class="fw-bold text-info" style="font-size: 0.95rem;">
                                                    <?= FormatHelper::formatVnd((int)$srv['base_price']) ?>
                                                </div>
                                                <small class="text-light opacity-60" style="font-size: 0.7rem;">Đã gồm 8% VAT</small>
                                            </div>
                                            <div class="small text-light opacity-75">
                                                <i class="bi bi-clock me-1"></i><?= (int)($srv['duration_days'] ?? 1) ?> ngày
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CLIENT JAVASCRIPT CHO RANGE SLIDER TẠI TRANG KHÁM PHÁ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceSlider = document.getElementById('service-price-slider');
    const serviceDisplay = document.getElementById('service-price-display');
    const hiddenRange = document.getElementById('inputServicePriceRange');

    if (serviceSlider && serviceDisplay) {
        serviceSlider.addEventListener('input', function() {
            const num = parseInt(this.value, 10);
            if (num >= 5000000) {
                serviceDisplay.textContent = '5.000.000 ₫+';
                if (hiddenRange) hiddenRange.value = '';
            } else {
                serviceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
                if (hiddenRange) {
                    if (num <= 500000) hiddenRange.value = 'under_500';
                    else if (num <= 1000000) hiddenRange.value = '500_1000';
                    else hiddenRange.value = 'over_1000';
                }
            }
        });
    }
});
</script>
