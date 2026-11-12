<?php
/**
 * Admin Banner Management View
 * OnlineTraveller — Quyết định 4 tour đưa lên Slider Banner & Khám phá Trang chủ.
 * 
 * @var array $services
 * @var array $currentBannerTours
 * @var array|null $user
 */

$services = $services ?? [];
$currentBannerTours = $currentBannerTours ?? [];
$selectedCount = count($currentBannerTours);
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item"><a href="/admin/services" class="text-decoration-none text-muted">Trải nghiệm</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Quản lý Banner Trang Chủ</li>
        </ol>
    </nav>

    <!-- Header & Quick Nav -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-images me-1"></i> Cấu Hình Banner &amp; Khám Phá Trang Chủ
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý 4 Tour Trưng Bày Trang Chủ</h2>
            <p class="text-muted small mb-0">Chủ động tick chọn đúng 4 tour nổi bật nhất để đưa lên Slider Banner và 4 Card Khám phá ở Trang chủ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">
                <i class="bi bi-eye me-1"></i> Xem Trang Chủ
            </a>
            <a href="/admin/services" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-card-checklist me-1"></i> Danh Sách Tour
            </a>
            <a href="/admin" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-speedometer2 me-1"></i> Bảng Điều Khiển
            </a>
        </div>
    </div>

    <!-- KHỐI 1: PREVIEW 4 TOUR ĐANG ĐƯỢC CHỌN LÀM BANNER HIỆN TẠI -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h5 class="fw-bold font-editorial text-dark mb-0">
                <i class="bi bi-stars text-warning me-2"></i> 4 Tour Đang Được Ghim Trên Banner Hiện Tại
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-1">
                Đang hiển thị: <?= count($currentBannerTours) ?> / 4 tour
            </span>
        </div>

        <?php if (empty($currentBannerTours)): ?>
            <div class="alert alert-info border-0 rounded-3 mb-0">
                <i class="bi bi-info-circle me-1"></i> Chưa có tour nào được ghim. Hệ thống đang tự động lấy 4 tour có đánh giá cao nhất. Hãy tick chọn 4 tour bên dưới để chủ động quyết định!
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                <?php foreach ($currentBannerTours as $bIndex => $bTour): ?>
                    <div class="col">
                        <div class="card border rounded-3 overflow-hidden h-100 shadow-sm position-relative">
                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white rounded-pill px-2 py-1" style="z-index: 2; font-size: 0.72rem;">
                                Vị trí #<?= $bIndex + 1 ?>
                            </span>
                            <img src="<?= htmlspecialchars($bTour['banner_image'] ?? '/assets/images/tours/dalat-san-may.jpg', ENT_QUOTES, 'UTF-8') ?>" 
                                 class="card-img-top object-fit-cover" 
                                 style="height: 120px;" 
                                 alt="<?= htmlspecialchars($bTour['title'], ENT_QUOTES, 'UTF-8') ?>"
                                 onerror="this.src='/assets/images/tours/dalat-san-may.jpg'">
                            <div class="card-body p-2.5">
                                <h6 class="fw-bold text-dark mb-1 line-clamp-1" title="<?= htmlspecialchars($bTour['title'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($bTour['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h6>
                                <p class="small text-muted mb-1 line-clamp-1">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($bTour['location'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-info">
                                        <?= number_format((float)($bTour['base_price'] ?? 0), 0, ',', '.') ?> đ
                                    </span>
                                    <span class="small text-warning">
                                        &#9733; <?= number_format((float)($bTour['avg_rating'] ?? 5.0), 1) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- KHỐI 2: FORM TICK CHỌN 4 TOUR ĐỂ ĐƯA LÊN BANNER -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <form method="POST" action="/admin/banner" id="bannerConfigForm">
            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 border-bottom pb-3">
                <div>
                    <h5 class="fw-bold font-editorial text-dark mb-1">
                        <i class="bi bi-check2-square text-success me-2"></i> Danh Sách Tour Đang Mở Bán
                    </h5>
                    <p class="small text-muted mb-0">Tick chọn đúng 4 tour bạn muốn đưa lên Trang chủ (Số lượng đã chọn: <strong id="counterDisplay" class="text-primary fs-6"><?= $selectedCount ?></strong> / 4)</p>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" id="btnSaveBanner">
                        <i class="bi bi-check-circle-fill me-1"></i> Lưu 4 Tour Lên Banner
                    </button>
                </div>
            </div>

            <?php if (empty($services)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    Không có tour nào đang ở trạng thái Đang mở bán (Approved). Hãy duyệt tour trước.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;" class="text-center">Chọn</th>
                                <th style="width: 90px;">Hình ảnh</th>
                                <th>Tên Tour Trải Nghiệm</th>
                                <th>Địa danh tổ chức</th>
                                <th>Dân bản địa (Host)</th>
                                <th>Giá trải nghiệm</th>
                                <th>Đánh giá</th>
                                <th style="width: 100px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $srv): ?>
                                <?php $isTicked = !empty($srv['is_banner']); ?>
                                <tr class="tour-row <?= $isTicked ? 'table-warning table-opacity-10' : '' ?>" id="tour-row-<?= $srv['id'] ?>">
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center m-0">
                                            <input type="checkbox" 
                                                   name="banner_tour_ids[]" 
                                                   value="<?= (int)$srv['id'] ?>" 
                                                   class="form-check-input banner-checkbox cursor-pointer" 
                                                   id="check-srv-<?= $srv['id'] ?>"
                                                   <?= $isTicked ? 'checked' : '' ?>
                                                   style="width: 22px; height: 22px;">
                                        </div>
                                    </td>
                                    <td>
                                        <img src="<?= htmlspecialchars($srv['banner_image'] ?? '/assets/images/tours/dalat-san-may.jpg', ENT_QUOTES, 'UTF-8') ?>" 
                                             alt="<?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>" 
                                             class="rounded-3 object-fit-cover shadow-sm" 
                                             width="70" height="46"
                                             onerror="this.src='/assets/images/tours/dalat-san-may.jpg'">
                                    </td>
                                    <td>
                                        <label for="check-srv-<?= $srv['id'] ?>" class="cursor-pointer mb-0">
                                            <strong class="text-dark d-block"><?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">
                                                <?= htmlspecialchars($srv['category_name']) ?>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($srv['location'], ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-dark fw-semibold">
                                            <?= htmlspecialchars($srv['host_name']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-info">
                                            <?= number_format((float)$srv['base_price'], 0, ',', '.') ?> đ
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="text-warning small">&#9733; <?= number_format((float)($srv['avg_rating'] ?? 5.0), 1) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="/services/<?= $srv['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" title="Xem trước tour">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <span class="text-muted small">
                        * Mẹo: Chọn đúng 4 tour tiêu biểu nhất. 4 tour này sẽ xuất hiện trên thanh Slider Banner lớn và 4 Thẻ Khám phá trên Trang chủ.
                    </span>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-semibold shadow">
                        <i class="bi bi-save me-1"></i> Lưu Cấu Hình Banner Ngay
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.banner-checkbox');
    const counterDisplay = document.getElementById('counterDisplay');
    const MAX_ALLOWED = 4;

    function updateCounter() {
        const checkedCount = document.querySelectorAll('.banner-checkbox:checked').length;
        if (counterDisplay) {
            counterDisplay.textContent = checkedCount;
            if (checkedCount === MAX_ALLOWED) {
                counterDisplay.className = 'text-success fw-bold fs-6';
            } else if (checkedCount > MAX_ALLOWED) {
                counterDisplay.className = 'text-danger fw-bold fs-6';
            } else {
                counterDisplay.className = 'text-primary fw-bold fs-6';
            }
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const checked = document.querySelectorAll('.banner-checkbox:checked');
            const row = document.getElementById('tour-row-' + this.value);
            
            if (this.checked && checked.length > MAX_ALLOWED) {
                alert('Bạn chỉ được chọn tối đa ' + MAX_ALLOWED + ' tour để hiển thị trên Banner Trang chủ!');
                this.checked = false;
                if (row) row.classList.remove('table-warning', 'table-opacity-10');
            } else {
                if (row) {
                    if (this.checked) {
                        row.classList.add('table-warning', 'table-opacity-10');
                    } else {
                        row.classList.remove('table-warning', 'table-opacity-10');
                    }
                }
            }
            updateCounter();
        });
    });

    const form = document.getElementById('bannerConfigForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const checkedCount = document.querySelectorAll('.banner-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Vui lòng tick chọn ít nhất 1 tour (tối đa 4 tour) cho Banner trang chủ!');
            }
        });
    }

    updateCounter();
});
</script>
