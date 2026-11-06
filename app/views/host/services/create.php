<?php
/** @var array $categories */
/** @var array|null $user */
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
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

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

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Địa điểm tổ chức <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control rounded-3 py-2" required placeholder="Ví dụ: Đồi Chè Cầu Đất, Đà Lạt, Lâm Đồng">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Vĩ độ (Latitude)</label>
                            <input type="number" step="0.00000001" name="latitude" class="form-control rounded-3 py-2" placeholder="11.9404">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Kinh độ (Longitude)</label>
                            <input type="number" step="0.00000001" name="longitude" class="form-control rounded-3 py-2" placeholder="108.4583">
                        </div>
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
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="fixed_choice" checked class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold text-dark small">Giá Cố Định (Fixed Choice)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Một mức giá trọn gói niêm yết duy nhất.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="host_range" class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold text-dark small">Khoảng Giá (Host Range)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Mức giá dao động tùy quy mô đoàn.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="free" class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold text-dark small">Miễn Phí (Free Tour)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Trải nghiệm giao lưu, phi lợi nhuận (0đ).</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="prepaid_full" class="form-check-input mt-0 price-mode-radio">
                                    <div>
                                        <div class="fw-bold text-dark small">Trả Trước 100% (Prepaid)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Thanh toán toàn bộ trước khi giữ chỗ.</small>
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

                <!-- 4. 3D Virtual Tour Stations (Three.js Stops) -->
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="fw-bold font-editorial text-dark mb-0">
                                <i class="bi bi-badge-3d text-primary me-2"></i>4. Trạm Trình Chiếu 3D Bản Địa (Three.js)
                            </h5>
                            <small class="text-muted">Các điểm dừng chân đẹp để du khách trải nghiệm tương tác không gian 3D.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addStopBtn">
                            <i class="bi bi-plus-lg me-1"></i> Thêm Trạm 3D
                        </button>
                    </div>

                    <div id="stopsContainer">
                        <!-- Stop 1 Default -->
                        <div class="stop-item p-3 rounded-3 bg-light border mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-primary text-white rounded-pill">Trạm 1</span>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <input type="text" name="stop_image_urls[]" class="form-control rounded-3 form-control-sm" placeholder="Đường dẫn ảnh trạm (/assets/images/... hoặc URL)">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="stop_captions[]" class="form-control rounded-3 form-control-sm" placeholder="Tiêu đề trạm (VD: Đón bình minh biển mây)">
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <input type="text" name="stop_stories[]" class="form-control rounded-3 form-control-sm" placeholder="Câu chuyện bản địa ngắn tại trạm dừng này...">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="stop_videos[]" class="form-control rounded-3 form-control-sm" placeholder="URL Video Youtube (tùy chọn)">
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
                        <input type="file" name="banner_file" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Hỗ trợ JPG, PNG, WEBP tối đa 5MB.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Hoặc nhập URL ảnh có sẵn</label>
                        <input type="text" name="banner_url" class="form-control rounded-3" placeholder="/assets/images/tours/sample.jpg">
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

    // 3. Dynamic 3D Stops Repeater
    const addStopBtn = document.getElementById('addStopBtn');
    const stopsContainer = document.getElementById('stopsContainer');
    let stopCount = 1;

    if (addStopBtn && stopsContainer) {
        addStopBtn.addEventListener('click', function() {
            stopCount++;
            const item = document.createElement('div');
            item.className = 'stop-item p-3 rounded-3 bg-light border mb-3 position-relative';
            item.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-primary text-white rounded-pill">Trạm ${stopCount}</span>
                    <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-stop-btn"><i class="bi bi-trash"></i> Xóa</button>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <input type="text" name="stop_image_urls[]" class="form-control rounded-3 form-control-sm" placeholder="Đường dẫn ảnh trạm (/assets/images/... hoặc URL)">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="stop_captions[]" class="form-control rounded-3 form-control-sm" placeholder="Tiêu đề trạm">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="stop_stories[]" class="form-control rounded-3 form-control-sm" placeholder="Câu chuyện bản địa ngắn...">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="stop_videos[]" class="form-control rounded-3 form-control-sm" placeholder="URL Video Youtube">
                    </div>
                </div>
            `;
            stopsContainer.appendChild(item);

            item.querySelector('.remove-stop-btn').addEventListener('click', function() {
                item.remove();
            });
        });
    }
});
</script>
