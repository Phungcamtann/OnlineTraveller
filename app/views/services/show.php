<?php
/**
 * OnlineTraveller v2.2 — Experience Detail (Trang Chi Tiết Trải Nghiệm)
 * UI Specification: docs/UI_SPEC_FOR_AGENT.md (Section 3C - 3.3) & Mockup Image
 *
 * @var array $service
 * @var array $stops
 * @var array $weather
 */

$service = $service ?? [];
$stops = $stops ?? [];
$weather = $weather ?? [];

// Fallback sample stations if tour has no stops yet
$stations = !empty($stops) ? $stops : [
    [
        'id' => 1,
        'image_url' => '/assets/images/tours/dalat-san-may.jpg',
        'caption' => 'Đón bình minh bến thuyền',
        'story_text' => 'Trạm 1: Đón bình minh bến thuyền cùng bình minh nồng nàn bên người dân bản địa.',
        'video_url' => '',
    ],
    [
        'id' => 2,
        'image_url' => '/assets/images/tours/dalat-rung-thong.jpg',
        'caption' => 'Rừng thông & Cà phê bản địa',
        'story_text' => 'Trạm 2: Rừng thông & Cà phê bản địa giữa không gian núi rừng bạt ngàn mát lạnh.',
        'video_url' => '',
    ],
    [
        'id' => 3,
        'image_url' => '/assets/images/tours/dalat-am-thuc.jpg',
        'caption' => 'Ẩm thực chiều bên hồ',
        'story_text' => 'Trạm 3: Ẩm thực chiều bên hồ thưởng thức các món ăn ngon lành của quê hương.',
        'video_url' => '',
    ],
    [
        'id' => 4,
        'image_url' => '/assets/images/tours/dalat-ho-tuyen-lam.jpg',
        'caption' => 'Hoàng hôn Hồ Tuyền Lâm',
        'story_text' => 'Trạm 4: Chiêm ngưỡng vẻ đẹp thơ mộng khi hoàng hôn buông xuống mặt hồ êm ả.',
        'video_url' => '',
    ],
];

// Fallback Banner Image
$bannerImage = !empty($service['banner_image']) ? $service['banner_image'] : '/assets/images/tours/dalat-san-may.jpg';

// Google Maps Query
$mapQuery = urlencode(($service['location'] ?? 'Đà Lạt') . ', Việt Nam');
if (!empty($service['latitude']) && !empty($service['longitude'])) {
    $mapQuery = $service['latitude'] . ',' . $service['longitude'];
}
?>

<div class="main-container">

    <!-- 1. HERO BANNER: CHỮ BÊN TRÁI + DẢI 4 ẢNH TRẠM + NÚT ĐẶT LỊCH NGAY -->
    <section class="container-xxl pt-2">
        <div class="row">
            <div class="col-12">
                <div class="hero-ambient-wrapper">
                    <div class="hero-banner-box position-relative" style="background-image: url('<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>'); min-height: 540px;">
                        <div class="hero-banner-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-4 p-lg-5">
                            <!-- Tiêu đề & Host Info ở phần trên/giữa bên trái -->
                            <div class="row">
                                <div class="col-lg-8 col-md-10 pt-4">
                                    <h1 class="font-editorial display-4 fw-bold text-white mb-2 lh-sm">
                                        <?= htmlspecialchars($service['title'] ?? 'Bình Minh Bên Hồ Tuyền Lâm', ENT_QUOTES, 'UTF-8') ?>
                                    </h1>
                                    <div class="small text-light opacity-90 fs-6">
                                        Host: <strong><?= htmlspecialchars($service['host_name'] ?? 'Nguyễn Văn Bản Địa', ENT_QUOTES, 'UTF-8') ?></strong> 
                                        (<?= htmlspecialchars($service['host_living_area'] ?? ($service['location'] ?? 'Đà Lạt')) ?>) &bull; 
                                        <span class="text-warning fw-bold">&#9733; <?= number_format((float)($service['avg_rating'] ?? 5.0), 1) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Dải 4 ảnh trạm thu nhỏ & Nút Đặt lịch ngay ở chân Banner -->
                            <div class="row align-items-end justify-content-between mt-4 pt-3 g-3">
                                <!-- Cột trái: Dải 4 ảnh trạm thu nhỏ -->
                                <div class="col-lg-8 col-md-8">
                                    <div class="row row-cols-4 g-2 g-md-3">
                                        <?php foreach (array_slice($stations, 0, 4) as $stIndex => $station): ?>
                                            <div class="col">
                                                <div class="rounded-3 rounded-md-4 overflow-hidden border border-light border-opacity-25 shadow-sm" style="aspect-ratio: 3/4;">
                                                    <img src="<?= htmlspecialchars($station['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                                                         class="w-100 h-100 object-fit-cover transition-transform" 
                                                         alt="<?= htmlspecialchars($station['caption'] ?? 'Trạm ' . ($stIndex + 1), ENT_QUOTES, 'UTF-8') ?>"
                                                         onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&auto=format&fit=crop&q=80'">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Cột phải: Nút Đặt Lịch Ngay cuộn xuống -->
                                <div class="col-lg-4 col-md-4 text-md-end">
                                    <a href="#bookingSection" class="btn btn-glass-hero px-4 py-2 fs-6">
                                        Đặt Lịch Ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ambient Glow Blur dưới chân Banner -->
                    <div class="hero-ambient-glow" style="background-image: url('<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>');"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. HÀNH TRÌNH & CÁC TRẠM TRẢI NGHIỆM (ABOUT THE TOUR) -->
    <section class="container my-5 pt-4">
        <!-- Hàng tiêu đề có đường kẻ ngang 2 bên -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h3 class="section-divider-title">HÀNH TRÌNH & CÁC TRẠM TRẢI NGHIỆM</h3>
            </div>
        </div>

        <div class="row g-5 align-items-start">
            <!-- Cột trái: Mô tả + Thẻ Homestay + Thời tiết & Maps -->
            <div class="col-lg-5">
                <!-- 1. Mô tả hành trình -->
                <div class="text-light opacity-75 lh-lg mb-4" style="font-size: 0.95rem;">
                    <?= nl2br(htmlspecialchars($service['description'] ?? 'Câu chuyện hành trình của Host bản địa mang lại trải nghiệm du lịch chân thực và đáng nhớ nhất.', ENT_QUOTES, 'UTF-8')) ?>
                </div>

                <!-- 2. Thẻ Homestay Nhà Dân Bản Địa -->
                <div class="glass-panel p-3 mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-4">
                            <div class="rounded-3 overflow-hidden shadow-sm" style="aspect-ratio: 4/3; background: rgba(11, 25, 36, 0.6);">
                                <img src="/assets/images/tours/dalat-san-may.jpg" 
                                     alt="Homestay Nhà Dân" 
                                     class="w-100 h-100 object-fit-cover"
                                     onerror="this.src='https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400&auto=format&fit=crop&q=80'">
                            </div>
                        </div>
                        <div class="col-8">
                            <h6 class="fw-bold text-white mb-1"><?= htmlspecialchars($service['homestay_name'] ?? 'Homestay Nhà Dân Bản Địa', ENT_QUOTES, 'UTF-8') ?></h6>
                            <div class="small text-light opacity-75 mb-1">Lưu trú tại Nhà Dân Bản Địa</div>
                            <div class="small text-light opacity-75">Người bản xứ: <?= htmlspecialchars($service['host_name'] ?? 'Dân địa phương', ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="small text-info mt-1" style="font-size: 0.8rem;">
                                <?= htmlspecialchars($service['homestay_room_type'] ?? 'Phòng riêng') ?> &bull; <?= (int)($service['homestay_nights'] ?? 1) ?> đêm
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Thẻ Thời Tiết & Google Maps -->
                <div class="glass-panel p-3 mb-4 overflow-hidden" id="weatherDestinationCard">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                            WEBSERVICE OPENWEATHER JSON API
                        </span>
                        <small class="text-secondary" style="font-size: 0.72rem;">Dự báo trực tiếp</small>
                    </div>
                    <div class="row g-2 align-items-stretch mb-2">
                        <div class="col-5 p-2 d-flex flex-column justify-content-center text-center text-sm-start rounded-3" style="background: rgba(15, 25, 36, 0.4);">
                            <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-1 mb-1">
                                <?php if (!empty($weather['icon_url'])): ?>
                                    <img src="<?= htmlspecialchars($weather['icon_url'], ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="Weather" width="38" height="38"
                                         style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                    <i class="bi bi-cloud-sun fs-2 text-info" style="display: none;"></i>
                                <?php else: ?>
                                    <img src="https://openweathermap.org/img/wn/02d.png" 
                                         alt="Weather" width="38" height="38"
                                         style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                    <i class="bi bi-cloud-sun fs-2 text-info" style="display: none;"></i>
                                <?php endif; ?>
                                <span class="fs-2 fw-bold text-info lh-1"><?= (int)($weather['temp'] ?? 18) ?>°C</span>
                            </div>
                            <div class="small text-white fw-semibold mb-0"><?= htmlspecialchars($service['location'] ?? 'Đà Lạt') ?></div>
                            <div class="small text-secondary" style="font-size: 0.72rem;"><?= htmlspecialchars($weather['description'] ?? 'Thời tiết hôm nay') ?></div>
                        </div>
                        <div class="col-7" style="min-height: 130px;">
                            <div class="rounded-3 overflow-hidden h-100" style="min-height: 130px;">
                                <iframe width="100%" 
                                        height="100%" 
                                        style="border:0; min-height: 130px;" 
                                        loading="lazy" 
                                        allowfullscreen 
                                        referrerpolicy="no-referrer-when-downgrade"
                                        src="https://maps.google.com/maps?q=<?= $mapQuery ?>&t=&z=13&ie=UTF8&iwloc=&output=embed">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($weather['advice'])): ?>
                        <div class="pt-2 border-top border-secondary border-opacity-25 small text-light opacity-90">
                            <strong class="text-info">Lời khuyên chuẩn bị từ Host:</strong> <?= htmlspecialchars($weather['advice'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Trục dọc Timeline các trạm & Nút Không Gian 3D ở cuối trục -->
            <div class="col-lg-7">
                <div class="station-timeline ps-4 position-relative">
                    <?php foreach ($stations as $index => $station): ?>
                        <div class="station-timeline-item position-relative mb-4">
                            <span class="station-timeline-dot"></span>
                            <div class="glass-panel p-3">
                                <div class="row g-3 align-items-center">
                                    <div class="col-sm-4">
                                        <div class="rounded-3 overflow-hidden shadow-sm" style="height: 95px;">
                                            <img src="<?= htmlspecialchars($station['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                                                 class="w-100 h-100 object-fit-cover" 
                                                 alt="<?= htmlspecialchars($station['caption'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                 onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&auto=format&fit=crop&q=80'">
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <h6 class="fw-bold text-white mb-1">
                                            Trạm <?= $index + 1 ?>: <?= htmlspecialchars($station['caption'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </h6>
                                        <p class="small text-light opacity-75 mb-0 line-clamp-2">
                                            <?= htmlspecialchars($station['story_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Nút mở Modal Trình Chiếu 3D Three.js Z-Scroll ở cuối trục Timeline -->
                    <div class="mt-4">
                        <button type="button" 
                                class="btn btn-alpine w-100 py-3 fs-6 shadow-lg d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal" 
                                data-bs-target="#virtualTour3DModal">
                            <i class="bi bi-stars"></i>
                            <span>Trải Nghiệm Không Gian 3D (Z-Scroll) &rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. KẾT NỐI TRỰC TIẾP & ĐẶT LỊCH -->
    <section class="container my-5 pt-3" id="bookingSection">
        <!-- Hàng tiêu đề có đường kẻ ngang 2 bên -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3 class="section-divider-title">KẾT NỐI TRỰC TIẾP & ĐẶT LỊCH</h3>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <!-- Cột trái: Kênh Hỏi Đánh Giá Trực Tiếp Từ Xa -->
            <div class="col-lg-7">
                <div class="glass-panel p-4 h-100">
                    <h5 class="fw-bold text-white mb-3">Kênh Hỏi Đánh Giá Trực Tiếp Từ Xa</h5>
                    
                    <!-- Form gửi câu hỏi -->
                    <form action="/discussions" method="POST" class="mb-4">
                        <input type="hidden" name="service_id" value="<?= (int)($service['id'] ?? 1) ?>">
                        <div class="mb-3">
                            <textarea name="message" 
                                      class="form-control bg-dark bg-opacity-50 text-white border border-secondary border-opacity-50 rounded-3 p-3 shadow-none search-field-input" 
                                      rows="3" 
                                      placeholder="Nhập nội dung câu hỏi?..." 
                                      required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-alpine btn-sm px-4 py-2">
                                <i class="bi bi-send me-1"></i> Gửi Câu Hỏi
                            </button>
                        </div>
                    </form>

                    <!-- Danh sách bình luận & phản hồi mẫu chuẩn mockup -->
                    <div class="d-flex flex-column gap-2">
                        <?php
                        $discussionList = [
                            [
                                'author' => 'Lê Du Khách',
                                'content' => 'Tour này có đón tận nơi ở trung tâm Đà Lạt không anh chị Host?',
                                'likes' => '7 người',
                                'reply' => 'Host: Dạ có xe đón tại khách sạn trung tâm lúc 5:00 sáng bạn nhé!',
                            ],
                            [
                                'author' => 'Trần Văn Hoàng',
                                'content' => 'Thời gian săn mây lý tưởng nhất là những tháng nào trong năm ạ?',
                                'likes' => '5 người',
                                'reply' => 'Host: Mùa săn mây đẹp nhất là từ tháng 10 đến tháng 3 năm sau nhé!',
                            ],
                        ];
                        ?>
                        <?php foreach ($discussionList as $item): ?>
                            <div class="p-3 rounded-3" style="background: rgba(15, 25, 36, 0.6); border: 1px solid rgba(155, 203, 229, 0.15);">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <strong class="text-white small"><?= htmlspecialchars($item['author'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span class="badge bg-secondary bg-opacity-25 text-light opacity-75" style="font-size: 0.7rem;">Bình luận</span>
                                    </div>
                                    <small class="text-info opacity-75" style="font-size: 0.75rem;">
                                        <i class="bi bi-people me-1"></i><?= htmlspecialchars($item['likes'], ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </div>
                                <p class="small text-light opacity-85 mb-1 ps-4">
                                    <?= htmlspecialchars($item['content'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <?php if (!empty($item['reply'])): ?>
                                    <div class="ms-4 p-2 rounded-2 mt-2" style="background: rgba(24, 78, 108, 0.25); border-left: 2px solid var(--brand-mid);">
                                        <small class="text-info fw-semibold d-block">Phản hồi từ Dân Bản Địa:</small>
                                        <small class="text-light opacity-75"><?= htmlspecialchars($item['reply'], ENT_QUOTES, 'UTF-8') ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Khung Đặt Chỗ (Booking Box) & Thông Tin Host -->
            <div class="col-lg-5">
                <div class="glass-panel p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-shield-check me-1"></i> GIAI ĐOẠN 2: ĐẶT LỊCH THỰC TẾ
                        </span>
                        <small class="text-secondary" style="font-size: 0.75rem;">Xác nhận tức thì</small>
                    </div>

                    <h5 class="fw-bold text-white mb-2">Đặt Chỗ Trải Nghiệm</h5>
                    <div class="d-flex align-items-baseline gap-2 mb-3">
                        <span class="fs-2 fw-bold text-info lh-1">
                            <?= number_format((float)($service['base_price'] ?? 450000), 0, ',', '.') ?>đ
                        </span>
                        <span class="small text-light opacity-75">/ người</span>
                    </div>

                    <!-- Bảng chi tiết các mục dịch vụ & cam kết thanh toán -->
                    <div class="p-3 rounded-3 mb-3 border border-secondary border-opacity-25 small" style="background: rgba(15, 25, 36, 0.55);">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                            <span class="text-light opacity-75"><i class="bi bi-tag text-info me-1"></i> Chế độ giá:</span>
                            <strong class="text-white text-uppercase"><?= htmlspecialchars($service['price_mode'] ?? 'per_person') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                            <span class="text-light opacity-75"><i class="bi bi-clock-history text-primary me-1"></i> Thời lượng:</span>
                            <strong class="text-white"><?= (int)($service['duration_days'] ?? 1) ?> ngày trải nghiệm</strong>
                        </div>
                        <?php if (!empty($service['homestay_name'])): ?>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                                <span class="text-light opacity-75"><i class="bi bi-house-door text-success me-1"></i> Lưu trú:</span>
                                <strong class="text-white text-truncate ps-2" title="<?= htmlspecialchars($service['homestay_name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($service['homestay_name'], ENT_QUOTES, 'UTF-8') ?> (<?= (int)($service['homestay_nights'] ?? 1) ?> đêm)
                                </strong>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                            <span class="text-light opacity-75"><i class="bi bi-wallet2 text-warning me-1"></i> Thanh toán giữ chỗ:</span>
                            <span class="text-success fw-bold">MoMo Sandbox</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-light opacity-75"><i class="bi bi-patch-check text-info me-1"></i> Cam kết:</span>
                            <span class="text-white">Hoàn tiền 100% nếu Host hủy</span>
                        </div>
                    </div>

                    <!-- Dropdown chọn lịch trình -->
                    <div class="mb-3">
                        <label class="small text-light opacity-75 d-block mb-2" for="scheduleSelect">
                            <i class="bi bi-calendar-event me-1 text-info"></i> Lịch trình & Giờ khởi hành
                        </label>
                        <select class="form-select bg-dark bg-opacity-50 text-white border border-secondary border-opacity-50 rounded-3 py-2 px-3 shadow-none search-field-select" id="scheduleSelect">
                            <option value="<?= (int)($service['id'] ?? 1) ?>" class="bg-dark text-white">🗓️ Chọn chuyến đi khả dụng</option>
                            <option value="<?= (int)($service['id'] ?? 1) ?>" class="bg-dark text-white">Khởi hành cuối tuần này (Còn 4 chỗ)</option>
                            <option value="<?= (int)($service['id'] ?? 1) ?>" class="bg-dark text-white">Khởi hành giữa tuần tới (Còn 8 chỗ)</option>
                        </select>
                    </div>

                    <a href="/bookings/create?service_id=<?= (int)($service['id'] ?? 1) ?>" class="btn btn-alpine w-100 py-3 fw-bold text-white fs-6 shadow">
                        <i class="bi bi-calendar-plus me-1"></i> Đặt Chỗ Trực Tuyến Ngay
                    </a>
                    <small class="text-secondary opacity-75 text-center d-block mt-2" style="font-size: 0.78rem;">
                        Giữ chỗ an toàn trong 15 phút qua MoMo • Hỗ trợ tư vấn 24/7
                    </small>
                </div>

                <!-- Host Profile Card -->
                <div class="glass-panel p-3 p-lg-4">
                    <h6 class="fw-bold text-white mb-3">Thông Tin Người Dân Bản Địa</h6>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle fs-4 flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white"><?= htmlspecialchars($service['host_name'] ?? 'Dân bản địa', ENT_QUOTES, 'UTF-8') ?></div>
                            <small class="text-success fw-semibold d-block">
                                <i class="bi bi-patch-check-fill me-1"></i> Dân Bản Địa Đã Xác Minh
                            </small>
                            <small class="text-light opacity-75">
                                <?= htmlspecialchars($service['host_living_area'] ?? ($service['location'] ?? 'Việt Nam'), ENT_QUOTES, 'UTF-8') ?>
                            </small>
                        </div>
                    </div>
                    <?php if (!empty($service['host_bio'])): ?>
                        <div class="small text-light opacity-75 fst-italic p-2 rounded-2 mt-2" style="background: rgba(15, 25, 36, 0.45); border-left: 2px solid var(--brand-mid);">
                            "<?= htmlspecialchars($service['host_bio'], ENT_QUOTES, 'UTF-8') ?>"
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- 4. MODAL TRÌNH CHIẾU KHÔNG GIAN 3D THREE.JS (Z-SCROLL) -->
<div class="modal fade" id="virtualTour3DModal" tabindex="-1" aria-labelledby="virtualTour3DModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border border-secondary border-opacity-25 rounded-4 overflow-hidden shadow-lg" style="background: rgba(11, 25, 36, 0.98); backdrop-filter: blur(16px);">
            <div class="modal-header border-bottom border-secondary border-opacity-25 px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info bg-opacity-25 text-info px-3 py-1 rounded-pill">
                        <i class="bi bi-badge-3d me-1"></i> Three.js Z-Scroll
                    </span>
                    <h5 class="modal-title font-editorial text-white fw-bold mb-0" id="virtualTour3DModalLabel">
                        Trình Chiếu Không Gian 3D — <?= htmlspecialchars($service['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </h5>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 position-relative" style="min-height: 520px; background: #0b1924;">
                <!-- 3D Viewport Simulation / Canvas Container -->
                <div class="threejs-tour-viewport position-relative" id="threejs-container" style="height: 520px;">
                    <div id="virtual-tour-canvas" style="width: 100%; height: 100%; position: relative;">
                        <img id="active-station-img" 
                             src="<?= htmlspecialchars($stations[0]['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                             alt="<?= htmlspecialchars($stations[0]['caption'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                             style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9; transition: opacity 0.4s ease;"
                             onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&auto=format&fit=crop&q=80'">
                    </div>

                    <!-- Station Story Overlay Card (Kính mờ) -->
                    <div class="tour-station-overlay position-absolute bottom-0 start-0 m-4 p-4 glass-panel shadow-lg" id="station-overlay" style="max-width: 480px; z-index: 10;">
                        <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 rounded-pill px-3 py-1 mb-2 d-inline-block" id="station-badge">
                            Trạm 1 / <?= count($stations) ?>
                        </span>
                        <h5 class="fw-bold text-white font-editorial mb-2" id="station-caption">
                            <?= htmlspecialchars($stations[0]['caption'] ?? '') ?>
                        </h5>
                        <p class="small text-light opacity-75 mb-3" id="station-story">
                            <?= htmlspecialchars($stations[0]['story_text'] ?? '') ?>
                        </p>
                        <?php if (!empty($stations[0]['video_url'])): ?>
                            <a href="<?= htmlspecialchars($stations[0]['video_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1" id="station-video-btn">
                                <i class="bi bi-play-circle-fill me-1"></i> Xem Video Thực Tế
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Navigation Controls for Touch / Prev-Next -->
                    <div class="tour-controls-floating position-absolute bottom-0 end-0 m-4 d-flex gap-2" style="z-index: 10;">
                        <button type="button" class="btn btn-glass-hero btn-sm px-3 shadow" id="btn-prev-station">
                            <i class="bi bi-chevron-left me-1"></i> Trạm Trước
                        </button>
                        <button type="button" class="btn btn-alpine btn-sm px-3 shadow" id="btn-next-station">
                            Trạm Kế Tiếp <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Stops Carousel Script for 3D Viewport simulation -->
<script>
    const tourStops = <?= json_encode($stations, JSON_UNESCAPED_UNICODE) ?>;
    let currentStopIndex = 0;

    const imgEl = document.getElementById('active-station-img');
    const badgeEl = document.getElementById('station-badge');
    const captionEl = document.getElementById('station-caption');
    const storyEl = document.getElementById('station-story');
    const prevBtn = document.getElementById('btn-prev-station');
    const nextBtn = document.getElementById('btn-next-station');

    function renderStation(idx) {
        if (!tourStops || !tourStops[idx]) return;
        const st = tourStops[idx];
        if (imgEl) {
            imgEl.style.opacity = '0.3';
            setTimeout(() => {
                imgEl.src = st.image_url;
                imgEl.alt = st.caption || '';
                if (badgeEl) badgeEl.innerText = `Trạm ${idx + 1} / ${tourStops.length}`;
                if (captionEl) captionEl.innerText = st.caption || '';
                if (storyEl) storyEl.innerText = st.story_text || '';
                imgEl.style.opacity = '0.9';
            }, 150);
        }
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
