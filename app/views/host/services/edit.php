<?php
/** @var array $tour */
/** @var array $categories */
/** @var array|null $user */
$acc = $tour['accommodation'] ?? null;
$stops = $tour['stops'] ?? [];
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Host Portal</a></li>
            <li class="breadcrumb-item"><a href="/host/services" class="text-decoration-none text-muted">Gói trải nghiệm</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Chỉnh Sửa Tour</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                    <i class="bi bi-pencil-fill me-1"></i> ID #<?= (int)$tour['id'] ?>
                </span>
                <?php
                    $badgeClass = match($tour['status']) {
                        'approved' => 'bg-success',
                        'pending' => 'bg-warning text-dark',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                ?>
                <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1"><?= strtoupper($tour['status']) ?></span>
            </div>
            <h2 class="font-editorial fw-bold text-dark mb-1"><?= htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-muted small mb-0 font-monospace">/services?slug=<?= htmlspecialchars($tour['slug']) ?></p>
        </div>
        <div>
            <a href="/host/schedules/create?service_id=<?= (int)$tour['id'] ?>" class="btn btn-outline-success rounded-pill px-3 py-2">
                <i class="bi bi-calendar-plus me-1"></i> Mở Lịch Khởi Hành
            </a>
        </div>
    </div>

    <form action="/host/services/<?= (int)$tour['id'] ?>/edit" method="POST" enctype="multipart/form-data" id="editTourForm">
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
                        <input type="text" name="title" class="form-control rounded-3 py-2" required minlength="5" maxlength="255" value="<?= htmlspecialchars($tour['title'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Danh mục phân loại <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select rounded-3 py-2" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>" <?= (int)$tour['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Thời lượng tổ chức (ngày) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control rounded-3 py-2" value="<?= (int)$tour['duration_days'] ?>" min="1" max="30" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Địa điểm tổ chức <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control rounded-3 py-2" required value="<?= htmlspecialchars($tour['location'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Vĩ độ (Latitude)</label>
                            <input type="number" step="0.00000001" name="latitude" class="form-control rounded-3 py-2" value="<?= htmlspecialchars((string)($tour['latitude'] ?? '')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Kinh độ (Longitude)</label>
                            <input type="number" step="0.00000001" name="longitude" class="form-control rounded-3 py-2" value="<?= htmlspecialchars((string)($tour['longitude'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted">Câu chuyện & Mô tả trải nghiệm <span class="text-danger">*</span></label>
                        <textarea name="description" rows="5" class="form-control rounded-3" required><?= htmlspecialchars($tour['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
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
                                    <input type="radio" name="price_mode" value="fixed_choice" class="form-check-input mt-0 price-mode-radio" <?= $tour['price_mode'] === 'fixed_choice' ? 'checked' : '' ?>>
                                    <div>
                                        <div class="fw-bold text-dark small">Giá Cố Định (Fixed Choice)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Một mức giá trọn gói niêm yết duy nhất.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="host_range" class="form-check-input mt-0 price-mode-radio" <?= $tour['price_mode'] === 'host_range' ? 'checked' : '' ?>>
                                    <div>
                                        <div class="fw-bold text-dark small">Khoảng Giá (Host Range)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Mức giá dao động tùy quy mô đoàn.</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="free" class="form-check-input mt-0 price-mode-radio" <?= $tour['price_mode'] === 'free' ? 'checked' : '' ?>>
                                    <div>
                                        <div class="fw-bold text-dark small">Miễn Phí (Free Tour)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Trải nghiệm giao lưu (0đ).</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-3 cursor-pointer w-100 bg-light-subtle h-100">
                                    <input type="radio" name="price_mode" value="prepaid_full" class="form-check-input mt-0 price-mode-radio" <?= $tour['price_mode'] === 'prepaid_full' ? 'checked' : '' ?>>
                                    <div>
                                        <div class="fw-bold text-dark small">Trả Trước 100% (Prepaid)</div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Thanh toán toàn bộ trước.</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="fixedPriceBlock" class="<?= $tour['price_mode'] === 'host_range' || $tour['price_mode'] === 'free' ? 'd-none' : '' ?>">
                        <label class="form-label small fw-bold text-muted">Mức giá trải nghiệm (VNĐ / khách)</label>
                        <div class="input-group">
                            <input type="number" name="base_price" id="basePriceInput" class="form-control rounded-start-3 py-2" value="<?= (int)$tour['base_price'] ?>" min="0" step="10000">
                            <span class="input-group-text rounded-end-3 bg-light text-muted">₫</span>
                        </div>
                    </div>

                    <div id="rangePriceBlock" class="<?= $tour['price_mode'] === 'host_range' ? '' : 'd-none' ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Giá tối thiểu (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="min_price" id="minPriceInput" class="form-control rounded-start-3 py-2" value="<?= (int)$tour['min_price'] ?>" min="0" step="10000">
                                    <span class="input-group-text rounded-end-3 bg-light text-muted">₫</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Giá tối đa (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="max_price" id="maxPriceInput" class="form-control rounded-start-3 py-2" value="<?= (int)$tour['max_price'] ?>" min="0" step="10000">
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
                            <input class="form-check-input" type="checkbox" name="has_homestay" value="1" id="toggleHomestay" <?= !empty($acc['homestay_name']) ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-bold text-muted" for="toggleHomestay">Đính kèm lưu trú</label>
                        </div>
                    </div>

                    <div id="homestayFields" class="<?= empty($acc['homestay_name']) ? 'd-none' : '' ?> pt-2 border-top">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Tên Homestay / Nhà vườn</label>
                                <input type="text" name="homestay_name" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($acc['homestay_name'] ?? '') ?>" placeholder="Ví dụ: Homestay Gió Núi LangBiang">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Số đêm lưu trú</label>
                                <input type="number" name="nights" class="form-control rounded-3 py-2" value="<?= (int)($acc['nights'] ?? 1) ?>" min="1" max="14">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Địa chỉ Homestay</label>
                                <input type="text" name="homestay_address" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($acc['address'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Loại phòng</label>
                                <input type="text" name="room_type" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($acc['room_type'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Tiện nghi & Lưu ý</label>
                            <input type="text" name="homestay_notes" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($acc['notes'] ?? '') ?>">
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
                            <small class="text-muted">Các điểm dừng chân tương tác cuộn 3D.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="addStopBtn">
                            <i class="bi bi-plus-lg me-1"></i> Thêm Trạm 3D
                        </button>
                    </div>

                    <div id="stopsContainer">
                        <?php if (empty($stops)): ?>
                            <div class="stop-item p-3 rounded-3 bg-light border mb-3">
                                <span class="badge bg-primary text-white rounded-pill mb-2">Trạm 1</span>
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
                            </div>
                        <?php else: ?>
                            <?php foreach ($stops as $idx => $st): ?>
                                <div class="stop-item p-3 rounded-3 bg-light border mb-3 position-relative">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-primary text-white rounded-pill">Trạm <?= $idx + 1 ?></span>
                                        <button type="button" class="btn btn-sm text-danger p-0 border-0 remove-stop-btn"><i class="bi bi-trash"></i> Xóa</button>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="stop_image_urls[]" class="form-control rounded-3 form-control-sm" value="<?= htmlspecialchars($st['image_url']) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="stop_captions[]" class="form-control rounded-3 form-control-sm" value="<?= htmlspecialchars($st['caption']) ?>">
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-8">
                                            <input type="text" name="stop_stories[]" class="form-control rounded-3 form-control-sm" value="<?= htmlspecialchars($st['story_text'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="stop_videos[]" class="form-control rounded-3 form-control-sm" value="<?= htmlspecialchars($st['video_url'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Banner & Submit -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white sticky-top" style="top: 100px;">
                    <h6 class="fw-bold text-dark mb-3">Ảnh Bìa Trải Nghiệm</h6>
                    
                    <?php if (!empty($tour['banner_image'])): ?>
                        <div class="mb-3 text-center">
                            <img src="<?= htmlspecialchars($tour['banner_image']) ?>" alt="Banner" class="img-fluid rounded-3 shadow-sm" style="max-height: 180px; width: 100%; object-fit: cover;">
                            <small class="text-muted d-block mt-1">Ảnh bìa hiện tại</small>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Thay ảnh bìa mới từ máy tính</label>
                        <input type="file" name="banner_file" class="form-control rounded-3" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Hoặc URL ảnh mới</label>
                        <input type="text" name="banner_url" class="form-control rounded-3" value="<?= htmlspecialchars($tour['banner_image'] ?? '') ?>">
                    </div>

                    <hr class="my-3">

                    <button type="submit" class="btn btn-editorial w-100 rounded-pill py-2 shadow-sm mb-2">
                        <i class="bi bi-check2-circle me-1"></i> Lưu Cập Nhật Tour
                    </button>
                    <a href="/host/services" class="btn btn-outline-secondary w-100 rounded-pill py-2">
                        Quay Lại
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    const addStopBtn = document.getElementById('addStopBtn');
    const stopsContainer = document.getElementById('stopsContainer');
    let stopCount = stopsContainer.querySelectorAll('.stop-item').length;

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
                        <input type="text" name="stop_image_urls[]" class="form-control rounded-3 form-control-sm" placeholder="Đường dẫn ảnh trạm">
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

        stopsContainer.querySelectorAll('.remove-stop-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.stop-item').remove();
            });
        });
    }
});
</script>
