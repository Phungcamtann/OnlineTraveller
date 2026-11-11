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

<?php 
$currentUser = $user ?? ($_SESSION['user'] ?? null);
$isAdmin = !empty($currentUser) && (($currentUser['role'] ?? '') === 'admin');
$srvStatus = $service['status'] ?? 'approved';
?>

<?php if ($isAdmin): ?>
    <section class="container-xxl pt-3 pb-2">
        <?php if ($srvStatus === 'pending'): ?>
            <div class="alert alert-warning border-0 rounded-4 shadow-sm p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <div class="fs-2 text-warning-emphasis">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold">CHỜ DUYỆT (PENDING)</span>
                            <span class="badge bg-dark text-white px-2 py-1 rounded-pill">Quản Trị Viên</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0">Trải nghiệm này đang ở trạng thái CHỜ KIỂM DUYỆT</h6>
                        <small class="text-muted">Tour chưa hiển thị công khai trên sàn cho du khách. Bạn có thể duyệt mở bán hoặc từ chối yêu cầu của Host.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form action="/admin/services/<?= (int)$service['id'] ?>/moderate" method="POST" class="d-inline">
                        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT mở bán trải nghiệm này?')">
                            <i class="bi bi-check-circle-fill me-1"></i> Phê Duyệt Mở Bán
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-3 shadow-none fw-semibold" data-bs-toggle="modal" data-bs-target="#adminRejectTourModal">
                        <i class="bi bi-x-circle me-1"></i> Từ Chối
                    </button>
                    <a href="/admin/services" class="btn btn-outline-dark rounded-pill px-3 shadow-none">
                        <i class="bi bi-arrow-left me-1"></i> Danh Sách Duyệt
                    </a>
                </div>
            </div>

            <!-- Modal Từ Chối Cho Admin Ngay Tại Trang Tour -->
            <div class="modal fade" id="adminRejectTourModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <form action="/admin/services/<?= (int)$service['id'] ?>/moderate" method="POST">
                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                            <input type="hidden" name="status" value="rejected">
                            <div class="modal-header border-bottom-0 pb-0">
                                <h5 class="modal-title font-editorial fw-bold text-danger">Từ Chối Phê Duyệt Tour</h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-2">
                                <p class="small text-muted mb-2">Vui lòng cung cấp lý do để Host nắm được và cập nhật lại:</p>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Lý do từ chối:</label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Nhập lý do chi tiết..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Xác Nhận Từ Chối</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php elseif ($srvStatus === 'rejected'): ?>
            <div class="alert alert-danger border-0 rounded-4 shadow-sm p-3 p-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <div class="fs-2 text-danger">
                        <i class="bi bi-x-octagon-fill"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-danger px-2 py-1 rounded-pill">ĐÃ TỪ CHỐI (REJECTED)</span>
                            <span class="badge bg-dark text-white px-2 py-1 rounded-pill">Quản Trị Viên</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0">Trải nghiệm này đã bị từ chối phê duyệt</h6>
                        <small class="text-danger">Lý do: <?= htmlspecialchars($service['rejection_reason'] ?? 'Chưa cung cấp lý do') ?></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form action="/admin/services/<?= (int)$service['id'] ?>/moderate" method="POST" class="d-inline">
                        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-outline-success rounded-pill px-4 fw-semibold shadow-sm" onclick="return confirm('Bạn có chắc chắn muốn DUYỆT LẠI và mở bán tour này?')">
                            <i class="bi bi-arrow-repeat me-1"></i> Phê Duyệt Lại
                        </button>
                    </form>
                    <a href="/admin/services" class="btn btn-outline-dark rounded-pill px-3 shadow-none">
                        <i class="bi bi-arrow-left me-1"></i> Danh Sách Duyệt
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

    <!-- 1. HERO BANNER + CỘT MỐC ĐỔI ẢNH NỀN (INTERACTIVE BANNER SWITCHER - CHUẨN JAPAN TOURS) -->
    <section class="container-fluid px-2 px-md-3 px-lg-4 pt-2">
        <div class="row">
            <div class="col-12">
                <div class="hero-ambient-wrapper" id="detailHeroAmbientWrapper">
                    <div class="hero-banner-box position-relative" id="detailHeroBanner" style="background-image: url('<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>'); min-height: 76vh; border-radius: 24px; background-position: center bottom; background-size: cover;">
                        <div class="hero-banner-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-4 p-lg-5" style="border-radius: 24px;">
                            <!-- Nửa trên Banner: Tiêu đề lớn, Host & Nhãn địa danh -->
                            <div class="container-fluid p-0">
                                <div class="row align-items-start justify-content-between g-4 pt-2 pt-lg-3">
                                    <div class="col-lg-7 col-md-8">
                                        <div class="d-inline-flex align-items-center gap-2 mb-2">
                                            <span class="glass-pill px-3 py-1 small fw-semibold text-info">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($service['location'] ?? 'Đà Lạt') ?>
                                            </span>
                                            <span class="glass-pill px-3 py-1 small fw-semibold text-white">
                                                <i class="bi bi-clock me-1"></i><?= (int)($service['duration_days'] ?? 1) ?> ngày
                                            </span>
                                        </div>
                                        <h1 class="font-editorial display-3 fw-bold text-white mb-2 lh-sm">
                                            <?= htmlspecialchars($service['title'] ?? 'Bình Minh Bên Hồ Tuyền Lâm', ENT_QUOTES, 'UTF-8') ?>
                                        </h1>
                                        <div class="small text-light opacity-90 fs-6 mb-3">
                                            Host: <strong><?= htmlspecialchars($service['host_name'] ?? 'Nguyễn Văn Bản Địa', ENT_QUOTES, 'UTF-8') ?></strong> 
                                            (<?= htmlspecialchars($service['host_living_area'] ?? ($service['location'] ?? 'Đà Lạt')) ?>) &bull; 
                                            <span class="text-warning fw-bold">&#9733; <?= number_format((float)($service['avg_rating'] ?? 5.0), 1) ?></span>
                                        </div>
                                        <p class="text-light opacity-75 small mb-0" style="max-width: 560px; line-height: 1.6;">
                                            <?= htmlspecialchars(mb_substr(strip_tags($service['short_description'] ?? ($service['description'] ?? 'Hòa mình vào không gian bình minh trong lành cùng người dân bản địa.')), 0, 160, 'UTF-8') . '...') ?>
                                        </p>
                                    </div>

                                    <!-- Cột phải: Khoảng thoáng đãng khoe trọn cảnh quan chuẩn Japan Tours -->
                                    <div class="col-lg-5 col-md-4 text-end d-none d-md-block">
                                        <div class="d-inline-flex gap-2">
                                            <span class="glass-pill px-3 py-1 small text-white opacity-75">
                                                <i class="bi bi-camera me-1"></i> <?= count($stations) ?> cột mốc
                                            </span>
                                            <span class="glass-pill px-3 py-1 small text-white opacity-75">
                                                <i class="bi bi-shield-check text-success me-1"></i> Bản địa xác minh
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nửa dưới chân Banner: Hàng thẻ ảnh cột mốc bên trái + Nút Đặt lịch bên phải -->
                            <div class="row align-items-end justify-content-between mt-auto pt-4 g-3">
                                <!-- Bên trái: 4-5 thẻ ảnh dọc nhỏ cột mốc tương tác đổi ảnh nền -->
                                <div class="col-lg-8 col-md-9">
                                    <div class="d-flex align-items-center gap-2 gap-md-3 overflow-auto pb-1 row-cols-4" id="milestonesContainer">
                                        <!-- Thẻ 0: Ảnh bìa chính -->
                                        <div class="vertical-station-card milestone-thumb-card active" 
                                             data-image="<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>"
                                             data-caption="Toàn cảnh <?= htmlspecialchars($service['location'] ?? 'Trải nghiệm', ENT_QUOTES, 'UTF-8') ?>"
                                             title="Ảnh chính: <?= htmlspecialchars($service['title'] ?? '') ?>">
                                            <img src="<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>" 
                                                 alt="Ảnh chính"
                                                 onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200&auto=format&fit=crop&q=80'">
                                            <div class="milestone-thumb-overlay">
                                                <span class="badge bg-white bg-opacity-25 text-white rounded-pill mb-1" style="font-size: 0.6rem; width: fit-content;">
                                                    Toàn cảnh
                                                </span>
                                                <div class="milestone-thumb-title">
                                                    <?= htmlspecialchars($service['location'] ?? 'Điểm Đến') ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thẻ 1..N: Các trạm trải nghiệm -->
                                        <?php foreach (array_slice($stations, 0, 4) as $stIndex => $station): ?>
                                            <div class="vertical-station-card milestone-thumb-card" 
                                                 data-station-id="<?= (int)($station['id'] ?? ($stIndex + 1)) ?>"
                                                 data-image="<?= htmlspecialchars($station['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                                                 data-caption="<?= htmlspecialchars($station['caption'] ?? ('Trạm ' . ($stIndex + 1)), ENT_QUOTES, 'UTF-8') ?>"
                                                 title="Trạm <?= $stIndex + 1 ?>: <?= htmlspecialchars($station['caption'] ?? '') ?>">
                                                <img src="<?= htmlspecialchars($station['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                                                     alt="<?= htmlspecialchars($station['caption'] ?? ('Trạm ' . ($stIndex + 1)), ENT_QUOTES, 'UTF-8') ?>"
                                                     onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=200&auto=format&fit=crop&q=80'">
                                                <div class="milestone-thumb-overlay">
                                                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill mb-1" style="font-size: 0.6rem; width: fit-content;">
                                                        Trạm <?= $stIndex + 1 ?>
                                                    </span>
                                                    <div class="milestone-thumb-title">
                                                        <?= htmlspecialchars($station['caption'] ?? ('Điểm dừng ' . ($stIndex + 1)), ENT_QUOTES, 'UTF-8') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Bên phải: Nút Đặt Lịch Ngay chuẩn nút Book bên phải của Japan Tours -->
                                <div class="col-lg-4 col-md-3 text-end">
                                    <a href="#bookingSection" class="btn btn-theme-primary rounded-pill px-4 py-2 fs-6 shadow">
                                        Đặt Lịch Ngay <i class="bi bi-arrow-down-circle ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ambient Glow Blur dưới chân Banner -->
                    <div class="hero-ambient-glow" id="detailAmbientGlow" style="background-image: url('<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>');"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. HÀNH TRÌNH & CÁC TRẠM TRẢI NGHIỆM (ABOUT THE TOUR - CHUẨN JAPAN TOURS) -->
    <section class="container my-5 pt-4">
        <!-- Hàng tiêu đề có đường kẻ ngang 2 bên chuẩn Japan Tours -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h3 class="section-divider-title font-editorial fw-bold tracking-wider">
                    <span>HÀNH TRÌNH &amp; CÁC TRẠM TRẢI NGHIỆM</span>
                </h3>
            </div>
        </div>

        <div class="row g-5 align-items-start">
            <!-- Cột trái (col-lg-5): CHỈ CHỨA MÔ TẢ TOUR VÀ LỜI NHẮN TỪ HOST -->
            <div class="col-lg-5 d-flex flex-column justify-content-between py-2" style="min-height: 480px;">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-info bg-opacity-15 text-info rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            GIỚI THIỆU CHUYẾN ĐI
                        </span>
                    </div>
                    <h4 class="fw-bold font-editorial mb-3" style="color: var(--text-main);">
                        Trải nghiệm trọn vẹn văn hóa &amp; thiên nhiên bản địa
                    </h4>
                    <div class="lh-lg mb-4" style="color: var(--text-muted); font-size: 1rem;">
                        <?= nl2br(htmlspecialchars(!empty($service['short_description']) ? $service['short_description'] : ($service['description'] ?? 'Hành trình được thiết kế thuận tiện và mộc mạc nhất để du khách khám phá trọn vẹn vẻ đẹp địa phương cùng người dân bản xứ.'), ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                </div>

                <!-- Khoảng nghỉ thoáng & Đoạn mô tả chi tiết thứ 2 chuẩn mẫu Japan Tours -->
                <div class="pt-4 border-top border-subtle">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-chat-heart text-info"></i>
                        <span class="small fw-bold text-uppercase tracking-wider" style="color: var(--text-muted); font-size: 0.75rem;">LỜI NHẮN TỪ HOST BẢN ĐỊA</span>
                    </div>
                    <div class="lh-lg" style="color: var(--text-muted); font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($service['description'] ?? 'Bạn không cần lo lắng về đường đi hay lịch trình — người dân bản địa sẽ dẫn bạn tới những góc đẹp nhất, thưởng thức ẩm thực chuẩn vị và tận hưởng chuyến đi.', ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                    <?php if (!empty($service['host_bio'])): ?>
                        <div class="small p-3 rounded-3 mt-3 surface-card border-subtle fst-italic">
                            "<?= htmlspecialchars($service['host_bio'], ENT_QUOTES, 'UTF-8') ?>"
                            <div class="mt-1 fw-bold not-italic" style="color: var(--text-main); font-style: normal;">
                                — <?= htmlspecialchars($service['host_name'] ?? 'Host bản địa', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải (col-lg-7): TRỤC DỌC SO LE (ZIGZAG) CHUẨN JAPAN TOURS -->
            <div class="col-lg-7">
                <div class="japan-timeline station-timeline position-relative">
                    <?php foreach ($stations as $index => $station): ?>
                        <?php 
                            $isEven = ($index % 2 === 0);
                            $stId = (int)($station['id'] ?? ($index + 1));
                            $stationTitle = 'Trạm ' . ($index + 1) . ': ' . ($station['caption'] ?? '');
                        ?>
                        <div class="japan-timeline-step station-timeline-item position-relative mb-5" id="station-<?= $stId ?>">
                            <span class="japan-timeline-dot station-timeline-dot"></span>

                            <?php if ($isEven): ?>
                                <!-- Nhánh bên TRÁI trục giữa -->
                                <div class="japan-timeline-col text-end pe-3">
                                    <span class="badge bg-info bg-opacity-15 text-info rounded-pill px-2 py-0 mb-1" style="font-size: 0.7rem;">
                                        Trạm <?= $index + 1 ?>
                                    </span>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-main);">
                                        <?= htmlspecialchars($stationTitle, ENT_QUOTES, 'UTF-8') ?>
                                    </h6>
                                    <p class="small mb-2 line-clamp-2" style="color: var(--text-muted);">
                                        <?= htmlspecialchars($station['story_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <div class="rounded-3 overflow-hidden shadow-sm d-inline-block" style="height: 130px; width: 100%; max-width: 210px;">
                                        <img src="<?= htmlspecialchars($station['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                                             class="w-100 h-100 object-fit-cover hover-lift" 
                                             alt="<?= htmlspecialchars($station['caption'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                             onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&auto=format&fit=crop&q=80'">
                                    </div>
                                </div>
                                <div class="japan-timeline-col d-none d-md-block"></div>
                            <?php else: ?>
                                <!-- Nhánh bên PHẢI trục giữa -->
                                <div class="japan-timeline-col d-none d-md-block"></div>
                                <div class="japan-timeline-col text-start ps-3">
                                    <span class="badge bg-info bg-opacity-15 text-info rounded-pill px-2 py-0 mb-1" style="font-size: 0.7rem;">
                                        Trạm <?= $index + 1 ?>
                                    </span>
                                    <h6 class="fw-bold mb-1" style="color: var(--text-main);">
                                        <?= htmlspecialchars($stationTitle, ENT_QUOTES, 'UTF-8') ?>
                                    </h6>
                                    <p class="small mb-2 line-clamp-2" style="color: var(--text-muted);">
                                        <?= htmlspecialchars($station['story_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <div class="rounded-3 overflow-hidden shadow-sm d-inline-block" style="height: 130px; width: 100%; max-width: 210px;">
                                        <img src="<?= htmlspecialchars($station['image_url'], ENT_QUOTES, 'UTF-8') ?>" 
                                             class="w-100 h-100 object-fit-cover hover-lift" 
                                             alt="<?= htmlspecialchars($station['caption'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                             onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&auto=format&fit=crop&q=80'">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <!-- Nút mở Modal Trình Chiếu 3D Three.js Z-Scroll ở cuối trục Timeline -->
                    <div class="mt-4 pt-2 text-center">
                        <button type="button" 
                                class="btn btn-theme-primary rounded-pill w-100 py-3 fs-6 shadow-lg d-flex align-items-center justify-content-center gap-2"
                                data-bs-toggle="modal" 
                                data-bs-target="#virtualTour3DModal">
                            <i class="bi bi-stars"></i>
                            <span>✨ Trải Nghiệm Không Gian 3D (Z-Scroll) &rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. TIỆN ÍCH & THÔNG TIN BẢN ĐỊA (CHUẨN MẪU WHAT'S INCLUDED CỦA JAPAN TOURS) -->
    <section class="container my-5 pt-3">
        <!-- Hàng tiêu đề có đường kẻ ngang bên phải -->
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center gap-3">
                <h4 class="fw-bold font-editorial tracking-wider mb-0" style="color: var(--text-main); white-space: nowrap;">
                    TIỆN ÍCH &amp; THÔNG TIN BẢN ĐỊA
                </h4>
                <div class="flex-grow-1" style="height: 1px; background-color: var(--border-subtle);"></div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 align-items-stretch">
            <!-- Ô 1: 👤 Dân Bản Địa Đồng Hành -->
            <div class="col">
                <div class="included-outline-box d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-15 text-info" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                Đã xác minh
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-main);">Dân Bản Địa Dẫn Đường</h6>
                        <div class="fw-semibold text-info small mb-2"><?= htmlspecialchars($service['host_name'] ?? 'Nguyễn Văn Bản Địa', ENT_QUOTES, 'UTF-8') ?></div>
                        <p class="small mb-3" style="color: var(--text-muted); line-height: 1.5;">
                            Khu vực sinh sống: <strong><?= htmlspecialchars($service['host_living_area'] ?? ($service['location'] ?? 'Đà Lạt')) ?></strong>. Am hiểu tường tận văn hóa và những góc chụp ảnh đẹp nhất.
                        </p>
                    </div>
                    <div class="pt-2 border-top border-subtle small d-flex justify-content-between" style="color: var(--text-muted);">
                        <span><i class="bi bi-clock me-1"></i><?= (int)($service['duration_days'] ?? 1) ?> ngày</span>
                        <span><i class="bi bi-people me-1"></i>Tối đa <?= (int)($service['max_guests'] ?? 8) ?> khách</span>
                    </div>
                </div>
            </div>

            <!-- Ô 2: 🏠 Lưu Trú Homestay Nhà Dân -->
            <div class="col">
                <div class="included-outline-box d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-15 text-success" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-house-door"></i>
                            </span>
                            <span class="badge bg-info bg-opacity-15 text-info rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                <?= (int)($service['homestay_nights'] ?? 1) ?> đêm
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-main);">Homestay Nhà Dân</h6>
                        <div class="fw-semibold text-success small mb-2"><?= htmlspecialchars($service['homestay_name'] ?? 'Homestay Nhà Dân Bản Địa', ENT_QUOTES, 'UTF-8') ?></div>
                        <p class="small mb-3" style="color: var(--text-muted); line-height: 1.5;">
                            Loại phòng: <strong><?= htmlspecialchars($service['homestay_room_type'] ?? 'Phòng riêng hướng vườn/hồ') ?></strong>. Bao gồm bữa sáng đặc sản, wifi và không gian sinh hoạt chung ấm cúng.
                        </p>
                    </div>
                    <div class="pt-2 border-top border-subtle small" style="color: var(--text-muted);">
                        <i class="bi bi-check2-circle text-success me-1"></i>Trải nghiệm nếp sống gia đình địa phương
                    </div>
                </div>
            </div>

            <!-- Ô 3: ⛅ Thời Tiết Điểm Đến Thực Tế (OpenWeather API) -->
            <div class="col">
                <div class="included-outline-box d-flex flex-column justify-content-between" id="weatherDestinationCard">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-15 text-warning" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-cloud-sun"></i>
                            </span>
                            <span class="badge bg-secondary bg-opacity-15 rounded-pill px-2 py-1" style="font-size: 0.68rem; color: var(--text-muted);">
                                OpenWeather JSON
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1" style="color: var(--text-main);">Thời Tiết Điểm Đến</h6>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fs-2 fw-bold text-info lh-1"><?= (int)($weather['temp'] ?? 18) ?>°C</span>
                            <span class="small" style="color: var(--text-muted);"><?= htmlspecialchars($weather['description'] ?? 'Se lạnh mù sương', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <p class="small mb-2" style="color: var(--text-muted); line-height: 1.4;">
                            Điểm đến: <strong><?= htmlspecialchars($service['location'] ?? 'Đà Lạt') ?></strong>
                        </p>
                    </div>
                    <div class="pt-2 border-top border-subtle small" style="color: var(--text-muted); font-size: 0.75rem;">
                        <i class="bi bi-info-circle text-info me-1"></i><?= htmlspecialchars($weather['advice'] ?? 'Thời tiết mát dịu, hãy chuẩn bị áo ấm và máy ảnh!', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>

            <!-- Ô 4: 📍 Vị Trí Bản Đồ Google Maps -->
            <div class="col">
                <div class="included-outline-box d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-15 text-danger" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <span class="badge bg-secondary bg-opacity-15 rounded-pill px-2 py-1" style="font-size: 0.68rem; color: var(--text-muted);">
                                Google Maps
                            </span>
                        </div>
                        <h6 class="fw-bold mb-2" style="color: var(--text-main);">Vị Trí Bản Đồ</h6>
                        <div class="rounded-3 overflow-hidden mb-2" style="height: 105px;">
                            <iframe width="100%" 
                                    height="100%" 
                                    style="border:0;" 
                                    loading="lazy" 
                                    allowfullscreen 
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://maps.google.com/maps?q=<?= $mapQuery ?>&t=&z=13&ie=UTF8&iwloc=&output=embed">
                            </iframe>
                        </div>
                    </div>
                    <div class="pt-1">
                        <a href="https://maps.google.com/maps?q=<?= $mapQuery ?>" target="_blank" rel="noopener" class="small text-info text-decoration-none d-flex align-items-center gap-1">
                            <span>Mở rộng trên Google Maps</span> <i class="bi bi-box-arrow-up-right" style="font-size: 0.75rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. KẾT NỐI TRỰC TIẾP & ĐẶT LỊCH -->
    <section class="container my-5 pt-3" id="bookingSection">
        <!-- Hàng tiêu đề có đường kẻ ngang 2 bên -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3 class="section-divider-title font-editorial fw-bold tracking-wider">KẾT NỐI TRỰC TIẾP &amp; ĐẶT LỊCH</h3>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <!-- Cột trái: Kênh Hỏi Đánh Giá Trực Tiếp Từ Xa -->
            <div class="col-lg-7">
                <div class="glass-panel p-4 h-100">
                    <h5 class="fw-bold text-white mb-3">Kênh Hỏi Đánh Giá Trực Tiếp Từ Xa</h5>
                    
                    <!-- Form gửi câu hỏi (TV4-05 AJAX) -->
                    <form id="discussion-main-form" action="/discussions" method="POST" class="mb-4">
                        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                        <input type="hidden" name="service_id" value="<?= (int)($service['id'] ?? 1) ?>">
                        <div class="mb-3">
                            <textarea name="message" 
                                      id="discussion-message-input"
                                      class="form-control bg-dark bg-opacity-50 text-white border border-secondary border-opacity-50 rounded-3 p-3 shadow-none search-field-input" 
                                      rows="3" 
                                      placeholder="Nhập nội dung câu hỏi cho Dân bản địa?..." 
                                      required></textarea>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-light opacity-50" id="discussion-char-hint">
                                <i class="bi bi-chat-quote me-1"></i>Hỏi đáp trực tiếp cùng Dân bản địa
                            </small>
                            <button type="submit" class="btn btn-alpine btn-sm px-4 py-2" id="discussion-submit-btn">
                                <i class="bi bi-send me-1"></i> Gửi Câu Hỏi
                            </button>
                        </div>
                    </form>

                    <!-- Danh sách bình luận & phản hồi 1 cấp thời gian thực (TV4-05) -->
                    <div id="discussions-thread-container" class="d-flex flex-column gap-3">
                        <?php if (empty($discussions)): ?>
                            <div id="no-discussions-notice" class="text-center py-4 text-light opacity-50 small">
                                <i class="bi bi-chat-dots fs-3 d-block mb-2 opacity-50"></i>
                                Chưa có câu hỏi nào. Hãy là người đầu tiên đặt câu hỏi cho Host bản địa!
                            </div>
                        <?php else: ?>
                            <?php foreach ($discussions as $disc): ?>
                                <?php
                                $discId = (int)$disc['id'];
                                $isHost = (($disc['user_role'] ?? '') === 'host');
                                $roleBadge = $isHost ? 'Host bản địa' : 'Du khách';
                                $roleBadgeClass = $isHost ? 'bg-success bg-opacity-25 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-25 text-light opacity-75';
                                $timeStr = date('H:i d/m', strtotime($disc['created_at']));
                                ?>
                                <div class="discussion-item p-3 rounded-3" id="discussion-item-<?= $discId ?>" style="background: rgba(15, 25, 36, 0.6); border: 1px solid rgba(155, 203, 229, 0.15);">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                                <i class="bi <?= $isHost ? 'bi-house-heart-fill text-success' : 'bi-person-fill' ?>"></i>
                                            </span>
                                            <strong class="text-white small"><?= htmlspecialchars($disc['user_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <span class="badge <?= $roleBadgeClass ?>" style="font-size: 0.7rem;"><?= $roleBadge ?></span>
                                        </div>
                                        <small class="text-light opacity-50" style="font-size: 0.72rem;">
                                            <i class="bi bi-clock me-1"></i><?= $timeStr ?>
                                        </small>
                                    </div>
                                    <p class="small text-light opacity-85 mb-2 ps-4">
                                        <?= htmlspecialchars($disc['message'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>

                                    <!-- Khối phản hồi 1 cấp -->
                                    <div class="replies-container ms-4 d-flex flex-column gap-2" id="replies-for-<?= $discId ?>">
                                        <?php if (!empty($disc['replies'])): ?>
                                            <?php foreach ($disc['replies'] as $reply): ?>
                                                <?php
                                                $replyIsHost = (($reply['user_role'] ?? '') === 'host');
                                                $replyRoleBadge = $replyIsHost ? 'Host phản hồi' : 'Thành viên';
                                                ?>
                                                <div class="p-2 rounded-2" style="background: rgba(24, 78, 108, 0.25); border-left: 2px solid var(--brand-mid);">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <strong class="text-info small" style="font-size: 0.78rem;"><?= htmlspecialchars($reply['user_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                            <span class="badge <?= $replyIsHost ? 'bg-success bg-opacity-25 text-success' : 'bg-secondary bg-opacity-25 text-light' ?>" style="font-size: 0.65rem;">
                                                                <?= $replyRoleBadge ?>
                                                            </span>
                                                        </div>
                                                        <small class="text-light opacity-50" style="font-size: 0.7rem;">
                                                            <?= date('H:i d/m', strtotime($reply['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                    <small class="text-light opacity-80 d-block ps-1"><?= htmlspecialchars($reply['message'], ENT_QUOTES, 'UTF-8') ?></small>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Nút & Khung Trả lời -->
                                    <div class="text-end mt-2 pt-1 border-top border-secondary border-opacity-25">
                                        <button type="button" class="btn btn-link btn-sm text-info text-decoration-none p-0 reply-toggle-btn" style="font-size: 0.75rem;" data-target="reply-form-<?= $discId ?>">
                                            <i class="bi bi-reply-fill me-1"></i> Trả lời
                                        </button>
                                        <form id="reply-form-<?= $discId ?>" class="discussion-reply-form mt-2 d-none" data-parent-id="<?= $discId ?>" action="/discussions" method="POST">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <input type="hidden" name="service_id" value="<?= (int)($service['id'] ?? 1) ?>">
                                            <input type="hidden" name="parent_id" value="<?= $discId ?>">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="message" class="form-control bg-dark bg-opacity-50 text-white border-secondary border-opacity-50 rounded-start-pill px-3 shadow-none" placeholder="Nhập câu trả lời..." required>
                                                <button type="submit" class="btn btn-alpine btn-sm rounded-end-pill px-3">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <span class="fs-2 fw-bold text-info lh-1">
                            <?= number_format((float)($service['base_price'] ?? 450000), 0, ',', '.') ?>đ
                        </span>
                        <span class="small text-light opacity-75">/ người</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-light opacity-60" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-check text-success me-1"></i> Giá đã bao gồm 8% thuế GTGT (VAT)
                        </small>
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
                            <span class="text-light opacity-75"><i class="bi bi-receipt text-info me-1"></i> Thuế &amp; Phí:</span>
                            <span class="text-white">Đã gồm 8% thuế GTGT</span>
                        </div>
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
                            <i class="bi bi-calendar-event me-1 text-info"></i> Lịch trình &amp; Giờ khởi hành
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

    <!-- 5. KHỐI ĐÁNH GIÁ TRẢI NGHIỆM & CẢM NHẬN DU KHÁCH (NẰM GỌN TRONG CONTAINER) -->
    <section class="container my-5 pb-4" id="reviewsSection">
        <div class="row">
            <div class="col-12">
                <div class="section-divider-title w-100 text-center mb-4 d-flex align-items-center justify-content-center">
                    <span class="divider-line flex-grow-1" style="height: 1px; background-color: var(--border-subtle);"></span>
                    <span class="mx-3 font-editorial fw-bold text-uppercase tracking-wider" style="color: var(--text-main);">ĐÁNH GIÁ TỪ DU KHÁCH</span>
                    <span class="divider-line flex-grow-1" style="height: 1px; background-color: var(--border-subtle);"></span>
                </div>

                <div class="row g-4">
                    <!-- Cột trái: Tóm tắt điểm số & Form gửi đánh giá -->
                    <div class="col-lg-4">
                        <div class="glass-panel p-4 mb-4">
                            <h5 class="fw-bold text-white mb-3">Đánh Giá Tổng Quan</h5>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="fs-1 fw-bold text-warning lh-1 font-editorial">
                                    <?= number_format((float)($reviewStats['avg_rating'] ?? 5.0), 1) ?>
                                </div>
                                <div>
                                    <div class="text-warning mb-1" style="font-size: 0.95rem;">
                                        <?php
                                        $roundedAvg = (int)round((float)($reviewStats['avg_rating'] ?? 5.0));
                                        for ($i = 1; $i <= 5; $i++):
                                        ?>
                                            <i class="bi <?= $i <= $roundedAvg ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-light opacity-75">
                                        Dựa trên <strong><?= (int)($reviewStats['reviews_count'] ?? count($reviews ?? [])) ?></strong> đánh giá
                                    </small>
                                </div>
                            </div>

                            <!-- Thanh phân bổ sao -->
                            <div class="vstack gap-2 small text-light opacity-75 border-top border-secondary border-opacity-25 pt-3 mb-4">
                                <?php
                                $totalRev = max((int)($reviewStats['reviews_count'] ?? 1), 1);
                                for ($star = 5; $star >= 1; $star--):
                                    $starCount = (int)($reviewStats['count_' . $star] ?? 0);
                                    $starPercent = round(($starCount / $totalRev) * 100);
                                ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="width: 42px; font-size: 0.75rem;"><?= $star ?> sao</span>
                                        <div class="progress flex-grow-1 bg-dark bg-opacity-50" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: <?= $starPercent ?>%;"></div>
                                        </div>
                                        <span class="text-end" style="width: 25px; font-size: 0.72rem;"><?= $starCount ?></span>
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <!-- Form gửi đánh giá -->
                            <div class="border-top border-secondary border-opacity-25 pt-3">
                                <h6 class="fw-bold text-white mb-2">Gửi Cảm Nhận Của Bạn</h6>
                                <?php if (!empty($user)): ?>
                                    <?php if (!empty($hasReviewed)): ?>
                                        <div class="p-3 rounded-3 bg-success bg-opacity-20 border border-success border-opacity-50 text-white small">
                                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                                            Bạn đã gửi đánh giá cho chuyến đi này rồi. Cảm ơn bạn đã đóng góp cho cộng đồng!
                                        </div>
                                    <?php else: ?>
                                        <form id="tourReviewForm" action="/reviews" method="POST">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <input type="hidden" name="service_id" value="<?= (int)($service['id'] ?? 1) ?>">
                                            
                                            <?php if (!empty($isEligibleVerified)): ?>
                                                <div class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-3 py-1 mb-2 d-inline-flex align-items-center" style="font-size: 0.72rem;">
                                                    <i class="bi bi-patch-check-fill me-1"></i> Đánh giá sẽ có huy hiệu: Đã tự trải nghiệm
                                                </div>
                                            <?php endif; ?>

                                            <div class="mb-2">
                                                <label class="small text-light opacity-75 d-block mb-1">Số sao đánh giá:</label>
                                                <div class="rating-stars-select d-flex gap-2 text-warning fs-5" id="ratingStars">
                                                    <i class="bi bi-star-fill star-option" data-value="1" style="cursor: pointer;"></i>
                                                    <i class="bi bi-star-fill star-option" data-value="2" style="cursor: pointer;"></i>
                                                    <i class="bi bi-star-fill star-option" data-value="3" style="cursor: pointer;"></i>
                                                    <i class="bi bi-star-fill star-option" data-value="4" style="cursor: pointer;"></i>
                                                    <i class="bi bi-star-fill star-option" data-value="5" style="cursor: pointer;"></i>
                                                </div>
                                                <input type="hidden" name="rating" id="reviewRatingInput" value="5">
                                            </div>

                                            <div class="mb-3">
                                                <textarea name="comment" id="reviewCommentInput" rows="3" class="form-control bg-dark bg-opacity-50 text-white border-secondary border-opacity-50 rounded-3 p-2 small shadow-none" placeholder="Chia sẻ cảm nhận chi tiết về chuyến đi, người bản địa, cảnh đẹp..." required></textarea>
                                            </div>

                                            <button type="submit" id="btnSubmitReview" class="btn btn-alpine w-100 py-2 btn-sm rounded-pill fw-bold text-white shadow">
                                                <i class="bi bi-send-fill me-1"></i> Gửi Đánh Giá Ngay
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="p-3 rounded-3 text-center border border-secondary border-opacity-25" style="background: rgba(15, 25, 36, 0.55);">
                                        <p class="small text-light opacity-75 mb-2">
                                            Vui lòng đăng nhập tài khoản để gửi đánh giá và nhận xét về tour này.
                                        </p>
                                        <a href="/login" class="btn btn-alpine btn-sm rounded-pill px-4">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Để Đánh Giá
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Danh sách các đánh giá từ du khách -->
                    <div class="col-lg-8">
                        <div class="glass-panel p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary border-opacity-25">
                                <h5 class="fw-bold text-white mb-0">Cảm Nhận Thực Tế</h5>
                                <span class="badge bg-light bg-opacity-10 text-light border border-secondary border-opacity-25 rounded-pill px-3 py-1 small" id="reviewsCountBadge">
                                    <?= count($reviews ?? []) ?> nhận xét
                                </span>
                            </div>

                            <div class="vstack gap-3" id="reviewsContainer">
                                <?php if (empty($reviews)): ?>
                                    <div class="text-center py-5 text-light opacity-60" id="noReviewsNotice">
                                        <i class="bi bi-chat-heart fs-1 d-block mb-2 opacity-50"></i>
                                        <p class="mb-1">Chưa có đánh giá nào cho chuyến đi này.</p>
                                        <small>Hãy là người đầu tiên chia sẻ cảm nhận trải nghiệm thực tế!</small>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($reviews as $rev): ?>
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 review-card" style="background: rgba(15, 25, 36, 0.55);">
                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                        <?= mb_strtoupper(mb_substr($rev['tourist_name'] ?? 'K', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span class="fw-bold text-white small"><?= htmlspecialchars($rev['tourist_name'] ?? 'Du khách', ENT_QUOTES, 'UTF-8') ?></span>
                                                            <?php if (!empty($rev['is_verified'])): ?>
                                                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-2 py-0" style="font-size: 0.68rem;">
                                                                    <i class="bi bi-patch-check-fill me-1"></i> Đã tự trải nghiệm
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <small class="text-light opacity-50" style="font-size: 0.72rem;">
                                                            <?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="text-warning small">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="bi <?= $i <= (int)$rev['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <p class="text-light opacity-90 small mb-0 ps-1">
                                                <?= nl2br(htmlspecialchars($rev['comment'], ENT_QUOTES, 'UTF-8')) ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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

    // =========================================================================
    // TV4-05: AJAX Discussion & 1-Level Host Reply Handler
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle reply input form
        document.addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('.reply-toggle-btn');
            if (toggleBtn) {
                const targetId = toggleBtn.getAttribute('data-target');
                const form = document.getElementById(targetId);
                if (form) {
                    form.classList.toggle('d-none');
                    if (!form.classList.contains('d-none')) {
                        const input = form.querySelector('input[name="message"]');
                        if (input) input.focus();
                    }
                }
            }
        });

        // Helper: Format date to H:i d/m
        function formatShortDate(dateStr) {
            try {
                const d = new Date(dateStr);
                const h = String(d.getHours()).padStart(2, '0');
                const m = String(d.getMinutes()).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                return `${h}:${m} ${day}/${month}`;
            } catch (e) {
                return 'vừa xong';
            }
        }

        // Helper: Escape HTML
        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str || '';
            return div.innerHTML;
        }

        // Handle Main Question Form AJAX Submit
        const mainForm = document.getElementById('discussion-main-form');
        if (mainForm) {
            mainForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = document.getElementById('discussion-submit-btn');
                const textarea = document.getElementById('discussion-message-input');
                const msg = textarea ? textarea.value.trim() : '';
                if (!msg) return;

                const formData = new FormData(mainForm);
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang gửi...';
                }

                try {
                    const res = await fetch('/discussions', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const data = await res.json();

                    if (!res.ok || data.error) {
                        alert(data.error || 'Có lỗi xảy ra khi gửi câu hỏi.');
                    } else if (data.success && data.discussion) {
                        const emptyNotice = document.getElementById('no-discussions-notice');
                        if (emptyNotice) emptyNotice.remove();

                        const container = document.getElementById('discussions-thread-container');
                        const d = data.discussion;
                        const discId = d.id;
                        const isHost = (d.user_role === 'host');
                        const roleBadge = isHost ? 'Host bản địa' : 'Du khách';
                        const roleBadgeClass = isHost ? 'bg-success bg-opacity-25 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-25 text-light opacity-75';
                        const timeStr = formatShortDate(d.created_at);

                        const cardHtml = `
                            <div class="discussion-item p-3 rounded-3" id="discussion-item-${discId}" style="background: rgba(15, 25, 36, 0.6); border: 1px solid rgba(155, 203, 229, 0.3); animation: fadeIn 0.4s ease;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                            <i class="bi ${isHost ? 'bi-house-heart-fill text-success' : 'bi-person-fill'}"></i>
                                        </span>
                                        <strong class="text-white small">${escapeHtml(d.user_name)}</strong>
                                        <span class="badge ${roleBadgeClass}" style="font-size: 0.7rem;">${roleBadge}</span>
                                    </div>
                                    <small class="text-light opacity-50" style="font-size: 0.72rem;">
                                        <i class="bi bi-clock me-1"></i>${timeStr}
                                    </small>
                                </div>
                                <p class="small text-light opacity-85 mb-2 ps-4">
                                    ${escapeHtml(d.message)}
                                </p>
                                <div class="replies-container ms-4 d-flex flex-column gap-2" id="replies-for-${discId}"></div>
                                <div class="text-end mt-2 pt-1 border-top border-secondary border-opacity-25">
                                    <button type="button" class="btn btn-link btn-sm text-info text-decoration-none p-0 reply-toggle-btn" style="font-size: 0.75rem;" data-target="reply-form-${discId}">
                                        <i class="bi bi-reply-fill me-1"></i> Trả lời
                                    </button>
                                    <form id="reply-form-${discId}" class="discussion-reply-form mt-2 d-none" data-parent-id="${discId}" action="/discussions" method="POST">
                                        <input type="hidden" name="_csrf_token" value="${mainForm.querySelector('input[name=\"_csrf_token\"]').value}">
                                        <input type="hidden" name="service_id" value="${d.service_id}">
                                        <input type="hidden" name="parent_id" value="${discId}">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="message" class="form-control bg-dark bg-opacity-50 text-white border-secondary border-opacity-50 rounded-start-pill px-3 shadow-none" placeholder="Nhập câu trả lời..." required>
                                            <button type="submit" class="btn btn-alpine btn-sm rounded-end-pill px-3">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('afterbegin', cardHtml);
                        if (textarea) textarea.value = '';
                    }
                } catch (err) {
                    alert('Lỗi kết nối máy chủ. Vui lòng thử lại!');
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-send me-1"></i> Gửi Câu Hỏi';
                    }
                }
            });
        }

        // Handle Reply Forms AJAX Submit
        document.addEventListener('submit', async function(e) {
            const replyForm = e.target.closest('.discussion-reply-form');
            if (!replyForm) return;
            e.preventDefault();

            const input = replyForm.querySelector('input[name="message"]');
            const msg = input ? input.value.trim() : '';
            if (!msg) return;

            const btn = replyForm.querySelector('button[type="submit"]');
            const parentId = replyForm.getAttribute('data-parent-id');
            const formData = new FormData(replyForm);

            if (btn) btn.disabled = true;

            try {
                const res = await fetch('/discussions', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const data = await res.json();

                if (!res.ok || data.error) {
                    alert(data.error || 'Có lỗi xảy ra khi gửi phản hồi.');
                } else if (data.success && data.discussion) {
                    const repliesContainer = document.getElementById('replies-for-' + parentId);
                    if (repliesContainer) {
                        const rep = data.discussion;
                        const repIsHost = (rep.user_role === 'host');
                        const repBadge = repIsHost ? 'Host phản hồi' : 'Thành viên';
                        const timeStr = formatShortDate(rep.created_at);

                        const replyHtml = `
                            <div class="p-2 rounded-2" style="background: rgba(24, 78, 108, 0.25); border-left: 2px solid var(--brand-mid); animation: fadeIn 0.3s ease;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <strong class="text-info small" style="font-size: 0.78rem;">${escapeHtml(rep.user_name)}</strong>
                                        <span class="badge ${repIsHost ? 'bg-success bg-opacity-25 text-success' : 'bg-secondary bg-opacity-25 text-light'}" style="font-size: 0.65rem;">
                                            ${repBadge}
                                        </span>
                                    </div>
                                    <small class="text-light opacity-50" style="font-size: 0.7rem;">${timeStr}</small>
                                </div>
                                <small class="text-light opacity-80 d-block ps-1">${escapeHtml(rep.message)}</small>
                            </div>
                        `;
                        repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                    }
                    if (input) input.value = '';
                    replyForm.classList.add('d-none');
                }
            } catch (err) {
                alert('Lỗi kết nối máy chủ khi gửi phản hồi.');
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    });

    // -------------------------------------------------------------
    // PHASE 2: STAR RATING INTERACTION & AJAX REVIEW SUBMIT
    // -------------------------------------------------------------
    const ratingStars = document.querySelectorAll('#ratingStars .star-option');
    const ratingInput = document.getElementById('reviewRatingInput');

    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const val = parseInt(this.getAttribute('data-value'), 10);
            if (ratingInput) ratingInput.value = val;
            ratingStars.forEach(s => {
                const sVal = parseInt(s.getAttribute('data-value'), 10);
                if (sVal <= val) {
                    s.classList.remove('bi-star');
                    s.classList.add('bi-star-fill');
                } else {
                    s.classList.remove('bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
    });

    const tourReviewForm = document.getElementById('tourReviewForm');
    if (tourReviewForm) {
        tourReviewForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitReview');
            const commentInput = document.getElementById('reviewCommentInput');
            const commentVal = commentInput ? commentInput.value.trim() : '';

            if (!commentVal) {
                alert('Vui lòng nhập cảm nhận về chuyến đi.');
                return;
            }

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang gửi...';
            }

            const formData = new FormData(tourReviewForm);

            try {
                const res = await fetch('/reviews', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok || data.error) {
                    alert(data.error || 'Có lỗi xảy ra khi gửi đánh giá.');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Gửi Đánh Giá Ngay';
                    }
                } else if (data.success && data.review) {
                    const rev = data.review;
                    const verifiedHtml = rev.is_verified ? `
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-2 py-0" style="font-size: 0.68rem;">
                            <i class="bi bi-patch-check-fill me-1"></i> Đã tự trải nghiệm
                        </span>
                    ` : '';

                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        starsHtml += `<i class="bi ${i <= parseInt(rev.rating, 10) ? 'bi-star-fill' : 'bi-star'}"></i> `;
                    }

                    const reviewCardHtml = `
                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 review-card" style="background: rgba(15, 25, 36, 0.55); animation: fadeIn 0.4s ease;">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                        ${escapeHtml((rev.tourist_name || 'K').substring(0, 1).toUpperCase())}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="fw-bold text-white small">${escapeHtml(rev.tourist_name || 'Du khách')}</span>
                                            ${verifiedHtml}
                                        </div>
                                        <small class="text-light opacity-50" style="font-size: 0.72rem;">Vừa xong</small>
                                    </div>
                                </div>
                                <div class="text-warning small">
                                    ${starsHtml}
                                </div>
                            </div>
                            <p class="text-light opacity-90 small mb-0 ps-1">
                                ${escapeHtml(rev.comment)}
                            </p>
                        </div>
                    `;

                    const noNotice = document.getElementById('noReviewsNotice');
                    if (noNotice) noNotice.remove();

                    const container = document.getElementById('reviewsContainer');
                    if (container) {
                        container.insertAdjacentHTML('afterbegin', reviewCardHtml);
                    }

                    // Replace form with success notice
                    tourReviewForm.innerHTML = `
                        <div class="p-3 rounded-3 bg-success bg-opacity-20 border border-success border-opacity-50 text-white small">
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            Cảm ơn bạn đã gửi đánh giá trải nghiệm chuyến đi!
                        </div>
                    `;
                }
            } catch (err) {
                alert('Lỗi kết nối máy chủ khi gửi đánh giá.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Gửi Đánh Giá Ngay';
                }
            }
        });
    }

    // -------------------------------------------------------------
    // INTERACTIVE MILESTONE BANNER SWITCHER (CHUẨN JAPAN TOURS)
    // -------------------------------------------------------------
    const milestoneCards = document.querySelectorAll('.milestone-thumb-card');
    const detailBanner = document.getElementById('detailHeroBanner');
    const detailGlow = document.getElementById('detailAmbientGlow');

    if (milestoneCards.length && detailBanner) {
        milestoneCards.forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const newImg = this.getAttribute('data-image');
                if (newImg) {
                    detailBanner.style.backgroundImage = `url('${newImg}')`;
                    if (detailGlow) {
                        detailGlow.style.backgroundImage = `url('${newImg}')`;
                    }
                    milestoneCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }
</script>
