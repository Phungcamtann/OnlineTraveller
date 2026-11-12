<?php
/** @var array $categories */
/** @var array|null $user */
/** @var array|null $provinces */
$provinces = $provinces ?? FormatHelper::getProvincesList();
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Host Portal</a></li>
            <li class="breadcrumb-item"><a href="/host/services" class="text-decoration-none text-muted">Gói trải nghiệm</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Đăng Ký Tour Mới</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-4">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mb-1">
            <i class="bi bi-pencil-square me-1"></i> Biểu Mẫu Khởi Tạo
        </span>
        <h2 class="font-editorial fw-bold text-dark mb-1">Đăng Ký Gói Trải Nghiệm Bản Địa Mới</h2>
        <p class="text-muted small mb-0">
            Giới thiệu nét văn hóa đặc sắc, ẩm thực hay cảnh sắc thiên nhiên quê hương bạn đến du khách từ xa.
        </p>
    </div>

    <form action="/host/services" method="POST" enctype="multipart/form-data" id="createTourForm">
        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
        <input type="hidden" name="csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

        <div class="row g-4">
            <!-- Left Column: Main Tour Information -->
            <div class="col-lg-8">
                <!-- 1. Basic Information -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                    <h5 class="fw-bold font-editorial text-dark mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>1. Thông Tin Trải Nghiệm Cơ Bản
                    </h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tiêu đề trải nghiệm <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="tourTitle" class="form-control rounded-3 py-2" required minlength="5" maxlength="255" placeholder="Ví dụ: Săn Mây Đồi Chè Cầu Đất & Học Hái Trà Sớm">
                        <small class="text-muted" style="font-size: 0.75rem;">Đặt tiêu đề hấp dẫn, thể hiện tính bản địa độc đáo (5 - 255 ký tự).</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Danh mục phân loại <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select rounded-3 py-2" required>
                                <option value="">-- Chọn danh mục phù hợp --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Thời lượng tổ chức (ngày) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control rounded-3 py-2" value="1" min="1" max="30" required>
                        </div>
                    </div>

                    <!-- Geocoding & Location Selection -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>Tỉnh / Thành phố <span class="text-danger">*</span>
                            </label>
                            <select name="province" id="provinceSelect" class="form-select rounded-3 py-2" required>
                                <option value="">-- Chọn Tỉnh / Thành phố --</option>
                                <?php foreach ($provinces as $provKey => $pInfo): ?>
                                    <option value="<?= htmlspecialchars($provKey) ?>" data-lat="<?= $pInfo['lat'] ?>" data-lng="<?= $pInfo['lng'] ?>">
                                        <?= htmlspecialchars($pInfo['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-muted">
                                <i class="bi bi-pin-map-fill text-primary me-1"></i>Điểm đón / Điểm hẹn cụ thể <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="location" id="locationInput" class="form-control rounded-3 py-2" required placeholder="Ví dụ: Cổng bến thuyền Hồ Tuyền Lâm, Đà Lạt">
                        </div>
                    </div>

                    <!-- Hidden Geocoding Inputs & Coordinates Status -->
                    <input type="hidden" name="latitude" id="latitudeInput" value="11.9404">
                    <input type="hidden" name="longitude" id="longitudeInput" value="108.4583">
                    <div class="p-2 px-3 rounded-3 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="small d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                <i class="bi bi-compass me-1"></i>Tọa độ bản đồ tự động
                            </span>
                            <span id="geoCoordsDisplay" class="font-monospace text-muted" style="font-size: 0.82rem;">11.9404, 108.4583</span>
                        </div>
                        <small class="text-muted fst-italic" style="font-size: 0.75rem;">Hệ thống tự định vị ghim trên bản đồ du khách</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Câu chuyện & Mô tả trải nghiệm <span class="text-danger">*</span></label>
                        <textarea name="description" rows="5" class="form-control rounded-3" required placeholder="Kể cho du khách nghe về những điều kỳ thú họ sẽ được trải nghiệm cùng bạn..."></textarea>
                    </div>
                </div>

                <!-- 2. Pricing Mode Configuration -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                    <h5 class="fw-bold font-editorial text-dark mb-3">
                        <i class="bi bi-tag text-success me-2"></i>2. Chế Độ Định Giá Trải Nghiệm
                    </h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-2">Chọn phương thức định giá <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label class="pricing-mode-card border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 h-100">
                                    <input type="radio" name="price_mode" value="fixed_choice" checked class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold small pricing-mode-title">Giá Cố Định (Cọc 50%)</div>
                                        <small class="pricing-mode-desc" style="font-size: 0.75rem;">Mức giá niêm yết duy nhất. Du khách cọc 50% online, 50% trả khi gặp.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="pricing-mode-card border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 h-100">
                                    <input type="radio" name="price_mode" value="host_range" class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold small pricing-mode-title">Khoảng Giá Linh Hoạt (Cọc 50%)</div>
                                        <small class="pricing-mode-desc" style="font-size: 0.75rem;">Mức giá dao động theo đoàn. Du khách cọc 50% theo giá sàn.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="pricing-mode-card border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 h-100">
                                    <input type="radio" name="price_mode" value="free" class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold small pricing-mode-title">Miễn Phí (Free Tour - 0đ)</div>
                                        <small class="pricing-mode-desc" style="font-size: 0.75rem;">Trải nghiệm giao lưu, phi lợi nhuận (0đ). Không thu cọc.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="pricing-mode-card border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 h-100">
                                    <input type="radio" name="price_mode" value="prepaid_full" class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold small pricing-mode-title">Trả Trước 100% (Trọn Gói)</div>
                                        <small class="pricing-mode-desc" style="font-size: 0.75rem;">Du khách thanh toán trọn gói 100% trước khi giữ chỗ.</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Input price blocks -->
                    <div id="fixedPriceBlock" class="mb-0">
                        <label class="form-label small fw-bold text-muted">Mức giá trải nghiệm (VNĐ / khách) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="base_price" id="basePriceInput" class="form-control rounded-start-3 py-2" placeholder="500000" min="0" step="10000">
                            <span class="input-group-text rounded-end-3 bg-light text-muted">₫</span>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">* Giá niêm yết đã bao gồm 8% thuế GTGT theo quy định.</small>
                    </div>

                    <div id="rangePriceBlock" class="d-none">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Giá tối thiểu (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="min_price" id="minPriceInput" class="form-control rounded-start-3 py-2" placeholder="300000" min="0" step="10000">
                                    <span class="input-group-text rounded-end-3 bg-light text-muted">₫</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Giá tối đa (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="max_price" id="maxPriceInput" class="form-control rounded-start-3 py-2" placeholder="800000" min="0" step="10000">
                                    <span class="input-group-text rounded-end-3 bg-light text-muted">₫</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Homestay Accommodation (1-1) -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold font-editorial text-dark mb-0">
                            <i class="bi bi-house-heart text-danger me-2"></i>3. Lưu Trú Homestay Nhà Dân Bản Địa
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="has_homestay" value="1" id="toggleHomestay">
                            <label class="form-check-label small fw-bold text-muted" for="toggleHomestay">Đính kèm lưu trú</label>
                        </div>
                    </div>

                    <div id="homestayFields" class="d-none pt-2 border-top">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Tên Homestay / Nhà vườn</label>
                                <input type="text" name="homestay_name" class="form-control rounded-3 py-2" placeholder="Ví dụ: Homestay Nhà Gỗ Cầu Đất View Mây">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Số đêm lưu trú</label>
                                <input type="number" name="nights" class="form-control rounded-3 py-2" value="1" min="1" max="14">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Địa chỉ Homestay</label>
                                <input type="text" name="homestay_address" class="form-control rounded-3 py-2" placeholder="Để trống sẽ lấy theo địa điểm tour">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Loại phòng</label>
                                <input type="text" name="room_type" class="form-control rounded-3 py-2" placeholder="Ví dụ: Phòng Áp Mái Giường Đôi">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Tiện nghi & Lưu ý cho du khách</label>
                            <input type="text" name="homestay_notes" class="form-control rounded-3 py-2" placeholder="Ví dụ: Bao gồm ăn sáng đặc sản và trà atiso nóng ấm">
                        </div>
                    </div>
                </div>

                <!-- 4. Virtual Tour Stations (3D Stops) -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="fw-bold font-editorial text-dark mb-0">
                                <i class="bi bi-geo-alt-fill text-primary me-2"></i>4. Lịch Trình & Điểm Dừng Chân Không Gian (3D Tour)
                            </h5>
                            <small class="text-muted">Các điểm dừng chân ấn tượng để du khách trải nghiệm hình ảnh không gian sống động.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addStopBtn">
                            <i class="bi bi-plus-lg me-1"></i> Thêm Điểm Dừng Chân
                        </button>
                    </div>

                    <div id="stopsContainer">
                        <!-- Stop 1 Default -->
                        <div class="stop-item p-3 rounded-3 bg-light border mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary text-white rounded-pill stop-index-badge">Điểm dừng 1</span>
                            </div>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">
                                        <i class="bi bi-image text-success me-1"></i>Tải ảnh điểm dừng từ máy tính <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="stop_files[]" class="form-control form-control-sm rounded-3 stop-file-input" accept="image/jpeg,image/png,image/webp">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Hoặc dán URL ảnh có sẵn</label>
                                    <input type="text" name="stop_image_urls[]" class="form-control form-control-sm rounded-3 stop-url-input" placeholder="/assets/images/tours/sample.jpg hoặc link web">
                                </div>
                            </div>

                            <div class="stop-preview-box mb-2 d-none">
                                <img src="" alt="Stop Preview" class="rounded-3 shadow-sm stop-preview-img border" style="max-height: 120px; object-fit: cover;">
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Tên điểm dừng chân <span class="text-danger">*</span></label>
                                    <input type="text" name="stop_captions[]" class="form-control form-control-sm rounded-3" placeholder="Ví dụ: Đón bình minh biển mây Đồi Chè Cầu Đất" required>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-muted mb-1">Câu chuyện / Nét văn hóa bản địa</label>
                                    <input type="text" name="stop_stories[]" class="form-control form-control-sm rounded-3" placeholder="Kể đôi nét hấp dẫn về điểm dừng chân này...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted mb-1">URL Video ngắn (tùy chọn)</label>
                                    <input type="text" name="stop_videos[]" class="form-control form-control-sm rounded-3" placeholder="https://youtube.com/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Banner & Submit -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white sticky-top" style="top: 100px;">
                    <h6 class="fw-bold text-dark mb-3">Ảnh Bìa Trải Nghiệm (Banner)</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tải ảnh bìa lên từ máy tính</label>
                        <input type="file" name="banner_file" id="bannerFileInput" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Hỗ trợ JPG, PNG, WEBP tối đa 5MB.</small>
                    </div>

                    <div id="bannerPreviewBox" class="mb-3 d-none text-center">
                        <img id="bannerPreviewImg" src="" alt="Banner Preview" class="img-fluid rounded-3 shadow-sm border" style="max-height: 180px; width: 100%; object-fit: cover;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Hoặc nhập URL ảnh có sẵn</label>
                        <input type="text" name="banner_url" id="bannerUrlInput" class="form-control rounded-3" placeholder="/assets/images/tours/sample.jpg">
                    </div>

                    <hr class="my-3">

                    <button type="submit" class="btn btn-editorial w-100 rounded-pill py-2 shadow-sm mb-2">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Đăng Ký Trải Nghiệm
                    </button>
                    <a href="/host/services" class="btn btn-outline-secondary w-100 rounded-pill py-2">
                        Hủy Bỏ
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle Pricing Mode Inputs
    const radios = document.querySelectorAll('.price-mode-radio');
    const fixedBlock = document.getElementById('fixedPriceBlock');
    const rangeBlock = document.getElementById('rangePriceBlock');
    const baseInput = document.getElementById('basePriceInput');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'free') {
                fixedBlock.classList.add('d-none');
                rangeBlock.classList.add('d-none');
                if (baseInput) baseInput.value = '0';
            } else if (this.value === 'host_range') {
                fixedBlock.classList.add('d-none');
                rangeBlock.classList.remove('d-none');
            } else {
                fixedBlock.classList.remove('d-none');
                rangeBlock.classList.add('d-none');
            }
        });
    });

    // 2. Toggle Homestay block
    const toggleHomestay = document.getElementById('toggleHomestay');
    const homestayFields = document.getElementById('homestayFields');
    if (toggleHomestay && homestayFields) {
        toggleHomestay.addEventListener('change', function() {
            if (this.checked) {
                homestayFields.classList.remove('d-none');
            } else {
                homestayFields.classList.add('d-none');
            }
        });
    }

    // 3. Geocoding auto-update
    const provinceSelect = document.getElementById('provinceSelect');
    const locationInput = document.getElementById('locationInput');
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');
    const geoCoordsDisplay = document.getElementById('geoCoordsDisplay');

    const hotspots = {
        'cầu đất': [11.8596, 108.5776],
        'đà lạt': [11.9404, 108.4583],
        'langbiang': [12.0444, 108.4414],
        'tuyền lâm': [11.8906, 108.4287],
        'sa pa': [22.3364, 103.8438],
        'fansipan': [22.3033, 103.7753],
        'hội an': [15.8801, 108.3380],
        'bà nà': [15.9988, 107.9961],
        'phú quốc': [10.2289, 103.9572],
        'nha trang': [12.2388, 109.1967],
        'huế': [16.4637, 107.5909],
        'tràng an': [20.2506, 105.9145],
        'đồng văn': [23.2778, 105.3611],
        'mù cang chải': [21.8488, 104.0863],
        'bản giốc': [22.8533, 106.7236],
        'hạ long': [20.9505, 107.0733],
        'mũi né': [10.9333, 108.2833],
        'măng đen': [14.6015, 108.2980],
        'quy nhơn': [13.7820, 109.2197],
        'phong nha': [17.5833, 106.2833],
        'mộc châu': [20.8436, 104.6473],
        'tà xùa': [21.2858, 104.4697]
    };

    function updateCoordinates() {
        const loc = (locationInput ? locationInput.value.toLowerCase() : '');
        let matched = false;

        for (const [key, coords] of Object.entries(hotspots)) {
            if (loc.includes(key)) {
                latInput.value = coords[0];
                lngInput.value = coords[1];
                if (geoCoordsDisplay) geoCoordsDisplay.innerText = coords[0] + ', ' + coords[1];
                matched = true;
                break;
            }
        }

        if (!matched && provinceSelect && provinceSelect.selectedIndex > 0) {
            const opt = provinceSelect.options[provinceSelect.selectedIndex];
            const lat = opt.getAttribute('data-lat');
            const lng = opt.getAttribute('data-lng');
            if (lat && lng) {
                latInput.value = lat;
                lngInput.value = lng;
                if (geoCoordsDisplay) geoCoordsDisplay.innerText = lat + ', ' + lng;
            }
        }
    }

    if (provinceSelect) provinceSelect.addEventListener('change', updateCoordinates);
    if (locationInput) locationInput.addEventListener('input', updateCoordinates);

    // 4. Banner file preview
    const bannerFileInput = document.getElementById('bannerFileInput');
    const bannerPreviewBox = document.getElementById('bannerPreviewBox');
    const bannerPreviewImg = document.getElementById('bannerPreviewImg');
    const bannerUrlInput = document.getElementById('bannerUrlInput');

    if (bannerFileInput) {
        bannerFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                bannerPreviewImg.src = URL.createObjectURL(this.files[0]);
                bannerPreviewBox.classList.remove('d-none');
            }
        });
    }
    if (bannerUrlInput) {
        bannerUrlInput.addEventListener('input', function() {
            if (this.value && this.value.trim().length > 5) {
                bannerPreviewImg.src = this.value.trim();
                bannerPreviewBox.classList.remove('d-none');
            }
        });
    }

    // 5. Dynamic Stop Image Preview helper
    function bindStopEvents(item) {
        const fileInput = item.querySelector('.stop-file-input');
        const urlInput = item.querySelector('.stop-url-input');
        const previewBox = item.querySelector('.stop-preview-box');
        const previewImg = item.querySelector('.stop-preview-img');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    previewImg.src = URL.createObjectURL(this.files[0]);
                    previewBox.classList.remove('d-none');
                }
            });
        }
        if (urlInput) {
            urlInput.addEventListener('input', function() {
                if (this.value && this.value.trim().length > 5) {
                    previewImg.src = this.value.trim();
                    previewBox.classList.remove('d-none');
                }
            });
        }
    }

    // Bind for existing stop 1
    document.querySelectorAll('.stop-item').forEach(bindStopEvents);

    // 6. Dynamic Stops Repeater
    const addStopBtn = document.getElementById('addStopBtn');
    const stopsContainer = document.getElementById('stopsContainer');

    if (addStopBtn && stopsContainer) {
        addStopBtn.addEventListener('click', function() {
            const count = stopsContainer.querySelectorAll('.stop-item').length + 1;
            const item = document.createElement('div');
            item.className = 'stop-item p-3 rounded-3 bg-light border mb-3 position-relative';
            item.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-primary text-white rounded-pill stop-index-badge">Điểm dừng ${count}</span>
                    <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-stop-btn"><i class="bi bi-trash"></i> Xóa</button>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-image text-success me-1"></i>Tải ảnh điểm dừng</label>
                        <input type="file" name="stop_files[]" class="form-control form-control-sm rounded-3 stop-file-input" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted mb-1">Hoặc dán URL ảnh</label>
                        <input type="text" name="stop_image_urls[]" class="form-control form-control-sm rounded-3 stop-url-input" placeholder="/assets/images/tours/sample.jpg">
                    </div>
                </div>
                <div class="stop-preview-box mb-2 d-none">
                    <img src="" alt="Stop Preview" class="rounded-3 shadow-sm stop-preview-img border" style="max-height: 120px; object-fit: cover;">
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted mb-1">Tên điểm dừng chân</label>
                        <input type="text" name="stop_captions[]" class="form-control form-control-sm rounded-3" placeholder="Tiêu đề điểm dừng chân">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted mb-1">Câu chuyện bản địa</label>
                        <input type="text" name="stop_stories[]" class="form-control form-control-sm rounded-3" placeholder="Câu chuyện bản địa ngắn...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">Video URL</label>
                        <input type="text" name="stop_videos[]" class="form-control form-control-sm rounded-3" placeholder="URL Video Youtube">
                    </div>
                </div>
            `;
            stopsContainer.appendChild(item);
            bindStopEvents(item);

            item.querySelector('.remove-stop-btn').addEventListener('click', function() {
                item.remove();
                // Update badge indices
                stopsContainer.querySelectorAll('.stop-item').forEach((elem, idx) => {
                    elem.querySelector('.stop-index-badge').innerText = `Điểm dừng ${idx + 1}`;
                });
            });
        });
    }
});
</script>