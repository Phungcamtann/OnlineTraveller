<?php
/** @var array $service */
/** @var array $stops */
/** @var array $weather */
$service = $service ?? [];
$stops = $stops ?? [];
$weather = $weather ?? [];
?>

<div class="container py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/services" class="text-decoration-none text-muted">Trải nghiệm</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page"><?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
    </nav>

    <!-- Header Block -->
    <div class="row align-items-end g-3 mb-4">
        <div class="col-lg-8">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                    <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">
                    ★ <?= number_format((float)$service['avg_rating'], 1) ?> (<?= (int)$service['reviews_count'] ?> đánh giá)
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                    <i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($service['location'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <h1 class="font-editorial fw-bold text-dark mb-1">
                <?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-muted small mb-0">
                Thời lượng: <strong><?= (int)$service['duration_days'] ?> ngày</strong> • Host bản xứ: <strong><?= htmlspecialchars($service['host_name'], ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="#booking-section" class="btn btn-editorial px-4 py-2">
                <i class="bi bi-calendar-check me-1"></i> Đặt Lịch Đi Thực Tế
            </a>
        </div>
    </div>

    <!-- 1. KHÔNG GIAN CUỘN TRÌNH CHIẾU 3D THREE.JS (VIRTUAL TOUR SECTION) -->
    <section id="virtual-tour" class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    <i class="bi bi-badge-3d me-1"></i> Giai Đoạn 1: Du Lịch & Khám Phá Từ Xa
                </span>
                <h3 class="font-editorial fw-bold text-dark mb-0">Không Gian Trình Chiếu 3D Three.js Z-Scroll</h3>
            </div>
            <div class="text-muted small d-none d-md-block">
                <i class="bi bi-mouse me-1"></i> Lăn chuột hoặc dùng nút bấm để bay qua từng trạm ảnh
            </div>
        </div>

        <div class="threejs-tour-viewport" id="threejs-container">
            <!-- Simulated 3D Scene Viewer Container -->
            <div id="virtual-tour-canvas" style="width: 100%; height: 100%; position: relative; background: #0f172a;">
                <?php if (!empty($stops)): ?>
                    <img id="active-station-img" 
                         src="<?= htmlspecialchars($stops[0]['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                         alt="<?= htmlspecialchars($stops[0]['caption'], ENT_QUOTES, 'UTF-8') ?>"
                         style="width: 100%; height: 100%; object-fit: cover; opacity: 0.85; transition: opacity 0.4s ease;"
                         onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&auto=format&fit=crop&q=80'">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100 text-white-50">
                        Chưa có trạm trình chiếu 3D nào cho tour này.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Station Story Overlay Card -->
            <?php if (!empty($stops)): ?>
                <div class="tour-station-overlay" id="station-overlay">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 mb-2" id="station-badge">
                        Trạm 1 / <?= count($stops) ?>
                    </span>
                    <h5 class="fw-bold text-dark font-editorial mb-2" id="station-caption">
                        <?= htmlspecialchars($stops[0]['caption'], ENT_QUOTES, 'UTF-8') ?>
                    </h5>
                    <p class="small text-muted mb-3" id="station-story">
                        <?= htmlspecialchars($stops[0]['story_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php if (!empty($stops[0]['video_url'])): ?>
                        <a href="<?= htmlspecialchars($stops[0]['video_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" id="station-video-btn">
                            <i class="bi bi-play-circle-fill me-1"></i> Xem Video Thực Tế
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Navigation Controls for Touch / Accessibility -->
                <div class="tour-controls-floating">
                    <button type="button" class="btn btn-dark btn-sm rounded-pill px-3 shadow" id="btn-prev-station">
                        <i class="bi bi-chevron-left me-1"></i> Trạm Trước
                    </button>
                    <button type="button" class="btn btn-editorial btn-sm px-3 shadow" id="btn-next-station">
                        Trạm Kế Tiếp <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- 2. MAIN CONTENT + SIDEBAR BOOKING -->
    <div class="row g-4">
        <!-- Left: Experience Story & Accommodations -->
        <div class="col-lg-8">
            <!-- Experience Story -->
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                <h4 class="font-editorial fw-bold text-dark mb-3">Câu Chuyện Trải Nghiệm Cùng Dân Bản Địa</h4>
                <div class="text-secondary" style="line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8')) ?>
                </div>
            </div>

            <!-- Homestay Accommodation Block (1-1) -->
            <?php if (!empty($service['homestay_name'])): ?>
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 34px; height: 34px;">
                            <i class="bi bi-house-door-fill"></i>
                        </span>
                        <div>
                            <h5 class="fw-bold font-editorial text-dark mb-0">Lưu Trú Homestay Nhà Dân Bản Địa</h5>
                            <small class="text-muted">Bao gồm trong trọn gói trải nghiệm thực tế</small>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">TÊN HOMESTAY:</small>
                                <strong class="text-dark"><?= htmlspecialchars($service['homestay_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">LOẠI PHÒNG & THỜI LƯỢNG:</small>
                                <strong class="text-dark"><?= htmlspecialchars($service['homestay_room_type'] ?? 'Phòng tiêu chuẩn', ENT_QUOTES, 'UTF-8') ?> (<?= (int)($service['homestay_nights'] ?? 1) ?> đêm)</strong>
                            </div>
                            <div class="col-12 mt-2">
                                <small class="text-muted d-block">ĐỊA CHỈ:</small>
                                <span class="text-secondary"><?= htmlspecialchars($service['homestay_address'] ?? $service['location'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <?php if (!empty($service['homestay_notes'])): ?>
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <small class="text-muted d-block">GHI CHÚ TỪ CHỦ NHÀ:</small>
                                    <span class="text-secondary small fst-italic"><?= htmlspecialchars($service['homestay_notes'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Google Maps & OpenWeather Live Weather Forecast -->
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white" id="weather-destination-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1 mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            <i class="bi bi-cloud-sun-fill me-1"></i> WEBSERVICE OPENWEATHER JSON API
                        </span>
                        <h5 class="fw-bold font-editorial text-dark mb-0">
                            Thời Tiết & Bản Đồ Điểm Đến: <?= htmlspecialchars($service['location'], ENT_QUOTES, 'UTF-8') ?>
                        </h5>
                    </div>
                    <?php if (!empty($service['latitude']) && !empty($service['longitude'])): ?>
                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-2">
                            <i class="bi bi-compass me-1 text-primary"></i> <?= number_format((float)$service['latitude'], 4) ?>, <?= number_format((float)$service['longitude'], 4) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Weather Banner Widget -->
                <?php if (!empty($weather)): ?>
                    <div class="rounded-4 p-3 p-md-4 mb-3 position-relative overflow-hidden shadow-sm" 
                         style="background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%); border: 1px solid #bae6fd;">
                        <div class="row align-items-center g-3">
                            <!-- Left: Big Temperature & Icon -->
                            <div class="col-sm-5 col-md-4 text-center text-sm-start border-end-md">
                                <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2">
                                    <img src="<?= htmlspecialchars($weather['icon_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="Weather Icon" 
                                         style="width: 64px; height: 64px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                                    <div>
                                        <div class="display-6 fw-bold text-dark font-editorial mb-0">
                                            <?= (int)($weather['temp'] ?? 25) ?>°C
                                        </div>
                                        <small class="text-muted d-block">
                                            Cảm giác như: <strong><?= (int)($weather['feels_like'] ?? 25) ?>°C</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Middle: Conditions & Indices -->
                            <div class="col-sm-7 col-md-8">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fw-bold text-dark fs-6 font-editorial">
                                        <?= htmlspecialchars($weather['description'] ?? 'Trời quang mây tạnh', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <div class="row g-2 text-center text-sm-start small">
                                    <div class="col-4">
                                        <span class="text-muted d-block"><i class="bi bi-droplet-fill text-primary me-1"></i> Độ ẩm</span>
                                        <strong class="text-dark"><?= (int)($weather['humidity'] ?? 75) ?>%</strong>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted d-block"><i class="bi bi-wind text-info me-1"></i> Tốc độ gió</span>
                                        <strong class="text-dark"><?= (float)($weather['wind_speed'] ?? 2.5) ?> m/s</strong>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted d-block"><i class="bi bi-thermometer-half text-danger me-1"></i> Biên độ</span>
                                        <strong class="text-dark"><?= (int)($weather['temp_min'] ?? 20) ?>° - <?= (int)($weather['temp_max'] ?? 28) ?>°C</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Travel Advice Box -->
                        <?php if (!empty($weather['advice'])): ?>
                            <div class="mt-3 pt-3 border-top border-info border-opacity-25 d-flex align-items-start gap-2 small text-dark">
                                <i class="bi bi-lightbulb-fill text-warning fs-5 flex-shrink-0"></i>
                                <div>
                                    <strong class="text-dark">Lời khuyên chuẩn bị từ Host:</strong>
                                    <span class="text-secondary"><?= htmlspecialchars($weather['advice'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="text-end mt-2">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-clock-history me-1"></i> Nguồn: <?= htmlspecialchars($weather['source'] ?? 'OpenWeather', ENT_QUOTES, 'UTF-8') ?> • Cập nhật: <?= htmlspecialchars($weather['updated_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Google Maps Interactive Embed -->
                <div class="rounded-3 overflow-hidden border shadow-sm position-relative" style="height: 260px; background: #e2e8f0;">
                    <?php 
                        $mapQuery = urlencode($service['location'] . ', Việt Nam');
                        if (!empty($service['latitude']) && !empty($service['longitude'])) {
                            $mapQuery = $service['latitude'] . ',' . $service['longitude'];
                        }
                    ?>
                    <iframe 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        loading="lazy" 
                        allowfullscreen 
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q=<?= $mapQuery ?>&t=&z=13&ie=UTF8&iwloc=&output=embed">
                    </iframe>
                </div>
            </div>

            <!-- Remote Discussions (Q&A Tourist <-> Local Host) -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white" id="discussions-section">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold font-editorial text-dark mb-0">
                        <i class="bi bi-chat-dots-fill text-primary me-2"></i> Kênh Hỏi Đáp Trực Tiếp Từ Xa Với Dân Bản Địa
                    </h5>
                    <span class="badge badge-soft-primary px-3 py-1 rounded-pill">Tương tác từ xa</span>
                </div>
                <p class="small text-muted mb-4">
                    Bạn có thắc mắc về thời tiết, hành lý hoặc văn hóa địa phương trước khi đi? Đặt câu hỏi trực tiếp để chính người dân bản xứ giải đáp tận tình!
                </p>

                <!-- Form Question -->
                <form action="/discussions" method="POST" class="mb-4">
                    <input type="hidden" name="service_id" value="<?= (int)$service['id'] ?>">
                    <div class="mb-2">
                        <textarea name="message" class="form-control rounded-3" rows="3" placeholder="Nhập câu hỏi của bạn gửi tới anh/chị Host bản địa..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-editorial btn-sm px-4">
                            <i class="bi bi-send me-1"></i> Gửi Câu Hỏi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Local Host Profile & Booking Action -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 6rem;">
                <!-- Booking Box -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white" id="booking-section">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mb-2 d-inline-block">
                        <i class="bi bi-check-circle me-1"></i> Giai Đoạn 2: Đặt Lịch Thực Tế
                    </span>
                    <small class="text-muted d-block">GIÁ TRỌN GÓI / KHÁCH</small>
                    <div class="d-flex align-items-baseline gap-2 mb-3">
                        <span class="fs-2 fw-bold text-dark font-editorial">
                            <?= FormatHelper::formatVnd((int)$service['base_price']) ?>
                        </span>
                        <small class="text-muted">/ người</small>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Chế độ giá:</span>
                            <strong class="text-uppercase"><?= htmlspecialchars($service['price_mode']) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Thanh toán giữ chỗ:</span>
                            <span class="text-success fw-bold">MoMo Sandbox</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Bảo đảm:</span>
                            <span class="text-muted">Hoàn tiền 100% nếu Host hủy</span>
                        </div>
                    </div>

                    <a href="/bookings/create?service_id=<?= (int)$service['id'] ?>" class="btn btn-editorial w-100 py-2 fs-6 mb-2">
                        <i class="bi bi-calendar-plus me-1"></i> Chọn Lịch & Đặt Ngay
                    </a>
                    <small class="text-muted text-center d-block">Giữ chỗ an toàn trong 15 phút qua MoMo</small>
                </div>

                <!-- Host Profile Card -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-3">Thông Tin Người Dân Bản Địa</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle fs-4" style="width: 52px; height: 52px;">
                            <i class="bi bi-person-check-fill text-warning"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($service['host_name'], ENT_QUOTES, 'UTF-8') ?></h6>
                            <small class="text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i> Dân Bản Địa Đã Xác Minh</small>
                            <small class="text-muted d-block"><?= htmlspecialchars($service['host_living_area'] ?? $service['location'], ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                    </div>

                    <?php if (!empty($service['host_bio'])): ?>
                        <p class="small text-muted mb-0 fst-italic bg-light p-3 rounded-3 border">
                            "<?= htmlspecialchars($service['host_bio'], ENT_QUOTES, 'UTF-8') ?>"
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Stops Carousel Script for 3D Viewport simulation -->
<?php if (!empty($stops)): ?>
<script>
    const tourStops = <?= json_encode($stops, JSON_UNESCAPED_UNICODE) ?>;
    let currentStopIndex = 0;

    const imgEl = document.getElementById('active-station-img');
    const badgeEl = document.getElementById('station-badge');
    const captionEl = document.getElementById('station-caption');
    const storyEl = document.getElementById('station-story');
    const prevBtn = document.getElementById('btn-prev-station');
    const nextBtn = document.getElementById('btn-next-station');

    function renderStation(idx) {
        if (!tourStops[idx]) return;
        const st = tourStops[idx];
        imgEl.style.opacity = '0.3';
        setTimeout(() => {
            imgEl.src = st.image_url;
            imgEl.alt = st.caption;
            badgeEl.innerText = `Trạm ${idx + 1} / ${tourStops.length}`;
            captionEl.innerText = st.caption;
            storyEl.innerText = st.story_text || '';
            imgEl.style.opacity = '0.9';
        }, 150);
    }

    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            currentStopIndex = (currentStopIndex - 1 + tourStops.length) % tourStops.length;
            renderStation(currentStopIndex);
        });

        nextBtn.addEventListener('click', () => {
            currentStopIndex = (currentStopIndex + 1) % tourStops.length;
            renderStation(currentStopIndex);
        });
    }
</script>
<?php endif; ?>
