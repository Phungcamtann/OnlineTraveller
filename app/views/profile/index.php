<?php
/** @var array $user */
$user = $user ?? [];
$role = $user['role'] ?? 'tourist';
$currentGender = $user['gender'] ?? 'other';
$currentBirthDate = $user['birth_date'] ?? '';
$provinces = FormatHelper::getProvincesList();
$currentArea = trim((string)($user['living_area'] ?? ''));

$bioPlaceholder = ($role === 'host')
    ? 'Chia sẻ đôi nét về bạn, sự gắn bó với quê hương, làng nghề truyền thống hoặc lý do bạn mong muốn dẫn dắt khách du lịch khám phá mảnh đất của mình...'
    : 'Chia sẻ sở thích du lịch, phong cách xê dịch, các vùng đất bạn yêu thích và mong muốn trải nghiệm cùng dân bản địa...';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Hồ sơ cá nhân</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Left: Summary Avatar & Role Card -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white text-center">
                <div class="position-relative d-inline-block mx-auto mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle fs-1 shadow-sm" style="width: 88px; height: 88px;">
                        <?php if ($role === 'host'): ?>
                            <i class="bi bi-person-badge text-success"></i>
                        <?php elseif ($role === 'admin'): ?>
                            <i class="bi bi-shield-check text-warning"></i>
                        <?php else: ?>
                            <i class="bi bi-person text-light"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <h4 class="font-editorial fw-bold text-dark mb-1">
                    <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h4>
                <p class="small text-muted mb-3"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                <div class="mb-3">
                    <?php if ($role === 'host'): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-patch-check-fill me-1"></i> Dân Bản Địa (Local Host)
                        </span>
                    <?php elseif ($role === 'admin'): ?>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-shield-lock-fill me-1"></i> Quản Trị Viên
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-backpack2 me-1"></i> Du Khách Khám Phá
                        </span>
                    <?php endif; ?>
                </div>

                <div class="p-3 bg-light rounded-3 text-start small border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Trạng thái:</span>
                        <span class="badge bg-success">Đang hoạt động</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ngày tham gia:</span>
                        <span class="text-dark fw-semibold"><?= FormatHelper::formatDateTime($user['created_at'] ?? 'now', 'd/m/Y') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Giới tính:</span>
                        <span class="text-dark fw-semibold">
                            <?php 
                            if ($currentGender === 'male') echo 'Nam';
                            elseif ($currentGender === 'female') echo 'Nữ';
                            else echo 'Chưa cập nhật';
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($currentBirthDate)): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Ngày sinh:</span>
                            <span class="text-dark fw-semibold"><?= date('d/m/Y', strtotime($currentBirthDate)) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($currentArea)): ?>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Vùng cư trú:</span>
                            <span class="text-dark fw-semibold"><?= htmlspecialchars($currentArea) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($role === 'host'): ?>
                    <div class="mt-3">
                        <a href="/host" class="btn btn-outline-success btn-sm rounded-pill w-100 py-2">
                            <i class="bi bi-kanban me-1"></i> Vào Bảng Quản Lý Host
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Profile Details & Edit Form -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="font-editorial fw-bold text-dark mb-1">Thông Tin Hồ Sơ</h4>
                        <p class="text-muted small mb-0">Hồ sơ cá nhân và câu chuyện bản địa kết nối du khách cùng dân bản địa</p>
                    </div>

                    <!-- Mode Indicator & Edit Toggle Button -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill" id="badgeMode">
                            <i class="bi bi-lock me-1"></i> Chỉ đọc
                        </span>
                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 shadow-none" id="btnEnableEdit">
                            <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa hồ sơ
                        </button>
                    </div>
                </div>

                <form action="/profile" method="POST" id="profileForm">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <!-- Email (Always Readonly) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Địa chỉ Email (Tài khoản đăng nhập)</label>
                        <input type="email" class="form-control bg-light text-muted border-0" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" readonly disabled>
                        <small class="text-muted" style="font-size: 0.75rem;">Email là định danh cố định của tài khoản, không thể thay đổi.</small>
                    </div>

                    <!-- Name & Phone -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="inputName" 
                                   class="form-control profile-editable-input bg-light border-0" 
                                   value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   data-original-val="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   readonly required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Số điện thoại liên hệ</label>
                            <input type="tel" name="phone" id="inputPhone" 
                                   class="form-control profile-editable-input bg-light border-0" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   data-original-val="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   placeholder="0912345678" readonly>
                        </div>
                    </div>

                    <!-- Gender & Birth Date -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Giới tính</label>
                            <select name="gender" id="inputGender" 
                                    class="form-select profile-editable-input bg-light border-0" 
                                    data-original-val="<?= htmlspecialchars($currentGender) ?>" 
                                    disabled>
                                <option value="male" <?= $currentGender === 'male' ? 'selected' : '' ?>>Nam</option>
                                <option value="female" <?= $currentGender === 'female' ? 'selected' : '' ?>>Nữ</option>
                                <option value="other" <?= ($currentGender === 'other' || empty($currentGender)) ? 'selected' : '' ?>>Khác / Không công khai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Ngày sinh (Date of birth)</label>
                            <input type="date" name="birth_date" id="inputBirthDate" 
                                   class="form-control profile-editable-input bg-light border-0" 
                                   value="<?= htmlspecialchars($currentBirthDate) ?>" 
                                   data-original-val="<?= htmlspecialchars($currentBirthDate) ?>" 
                                   readonly>
                        </div>
                    </div>

                    <!-- KHỐI DÀNH RIÊNG CHO DÂN BẢN ĐỊA & CÂU CHUYỆN VĂN HÓA -->
                    <div class="p-4 rounded-4 mb-4 border <?= $role === 'host' ? 'border-success bg-success-subtle bg-opacity-25' : 'bg-light' ?>">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-geo-alt-fill <?= $role === 'host' ? 'text-success' : 'text-primary' ?> fs-5"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <?= $role === 'host' ? 'Thông Tin Bản Địa & Câu Chuyện Host' : 'Khu Vực & Nơi Sinh Sống' ?>
                                </h6>
                                <small class="text-muted">
                                    <?= $role === 'host' ? 'Hiển thị trên Hero Banner và trang chi tiết tour để du khách từ xa tin tưởng đặt chỗ' : 'Giúp hệ thống gợi ý các điểm đến và tour du lịch phù hợp với bạn' ?>
                                </small>
                            </div>
                        </div>

                        <!-- Combobox Tỉnh / Thành Phố Cư Trú -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Địa bàn / Tỉnh thành cư trú (Living Area)</label>
                            <select name="living_area" id="inputLivingArea" 
                                    class="form-select profile-editable-input bg-light border-0" 
                                    data-original-val="<?= htmlspecialchars($currentArea) ?>" 
                                    disabled>
                                <option value="">-- Chọn Tỉnh / Thành phố cư trú --</option>
                                <?php 
                                $matched = false;
                                foreach ($provinces as $pKey => $pInfo):
                                    $pName = $pInfo['name'];
                                    $isSelected = ($currentArea === $pKey || $currentArea === $pName || (strlen($pKey) > 3 && str_contains($currentArea, $pKey)));
                                    if ($isSelected) { $matched = true; }
                                ?>
                                    <option value="<?= htmlspecialchars($pKey) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pName) ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if (!empty($currentArea) && !$matched): ?>
                                    <option value="<?= htmlspecialchars($currentArea) ?>" selected>
                                        <?= htmlspecialchars($currentArea) ?> (Tùy chỉnh)
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Tiểu sử Bio với Placeholder Cá Nhân Hóa -->
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">
                                <?= $role === 'host' ? 'Tiểu sử & Câu chuyện bản địa (Host Bio)' : 'Giới thiệu bản thân & Phong cách du lịch' ?>
                            </label>
                            <textarea name="bio" id="inputBio" rows="4" 
                                      class="form-control profile-editable-input bg-light border-0" 
                                      data-original-val="<?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                      placeholder="<?= htmlspecialchars($bioPlaceholder) ?>" 
                                      readonly><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS: Only visible in edit mode -->
                    <div id="editActionButtons" class="d-none">
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2" id="btnCancelEdit">
                                <i class="bi bi-x-lg me-1"></i> Hủy Bỏ
                            </button>
                            <button type="submit" class="btn btn-editorial px-4 py-2" id="btnSaveProfile">
                                <i class="bi bi-check-lg me-1"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CLIENT-SIDE SCRIPT FOR TOGGLING READONLY / EDIT MODE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnEnableEdit = document.getElementById('btnEnableEdit');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const editActionButtons = document.getElementById('editActionButtons');
    const badgeMode = document.getElementById('badgeMode');
    const editableInputs = document.querySelectorAll('.profile-editable-input');
    const inputName = document.getElementById('inputName');
    const profileForm = document.getElementById('profileForm');

    function setEditMode(isEditing) {
        if (isEditing) {
            // Enable editing
            editableInputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.removeAttribute('disabled');
                } else {
                    input.removeAttribute('readonly');
                }
                input.classList.remove('bg-light', 'border-0');
                input.classList.add('bg-white', 'border');
            });

            // Update badge & buttons
            badgeMode.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill';
            badgeMode.innerHTML = '<i class="bi bi-pencil-fill me-1"></i> Đang chỉnh sửa';

            btnEnableEdit.classList.add('d-none');
            editActionButtons.classList.remove('d-none');

            if (inputName) {
                inputName.focus();
            }
        } else {
            // Revert back to readonly mode
            editableInputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.setAttribute('disabled', 'disabled');
                } else {
                    input.setAttribute('readonly', 'readonly');
                }
                input.classList.remove('bg-white', 'border');
                input.classList.add('bg-light', 'border-0');
                // Restore original value
                if (input.dataset.originalVal !== undefined) {
                    input.value = input.dataset.originalVal;
                }
            });

            // Update badge & buttons
            badgeMode.className = 'badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill';
            badgeMode.innerHTML = '<i class="bi bi-lock me-1"></i> Chỉ đọc';

            btnEnableEdit.classList.remove('d-none');
            editActionButtons.classList.add('d-none');
        }
    }

    if (btnEnableEdit) {
        btnEnableEdit.addEventListener('click', function () {
            setEditMode(true);
        });
    }

    if (btnCancelEdit) {
        btnCancelEdit.addEventListener('click', function () {
            setEditMode(false);
        });
    }

    if (profileForm) {
        profileForm.addEventListener('submit', function () {
            editableInputs.forEach(input => {
                input.removeAttribute('disabled');
            });
        });
    }
});
</script>
