<?php
/**
 * View: Xác Nhận Đặt Chỗ Tour Trải Nghiệm (TV3-04)
 * File: app/views/bookings/create.php
 *
 * @var array $quote Dữ liệu báo giá chi tiết từ PricingService
 * @var array $availableSchedules Danh sách các lịch khởi hành khả dụng
 * @var array $user Thông tin người dùng đăng nhập
 */
?>

<div class="container py-4">
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted"><i class="bi bi-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/services" class="text-decoration-none text-muted">Khám phá bản địa</a></li>
            <li class="breadcrumb-item"><a href="/services/<?= (int)$quote['service_id'] ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($quote['service_title'], ENT_QUOTES, 'UTF-8') ?></a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Đặt Chỗ & Báo Giá</li>
        </ol>
    </nav>

    <!-- STEP PROGRESS INDICATOR -->
    <div class="card border-0 rounded-4 shadow-sm p-3 mb-4 bg-white">
        <div class="row text-center g-2 g-md-0 align-items-center">
            <div class="col-4 position-relative">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle fw-bold shadow-sm mb-1" style="width: 36px; height: 36px;">
                        1
                    </span>
                    <span class="small fw-bold text-dark">Chọn Lịch & Báo Giá</span>
                </div>
            </div>
            <div class="col-4 position-relative">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted border rounded-circle fw-bold mb-1" style="width: 36px; height: 36px;">
                        2
                    </span>
                    <span class="small text-muted">Host Giữ Chỗ (15p)</span>
                </div>
            </div>
            <div class="col-4 position-relative">
                <div class="d-flex flex-column align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted border rounded-circle fw-bold mb-1" style="width: 36px; height: 36px;">
                        3
                    </span>
                    <span class="small text-muted">Thanh Toán Cọc MoMo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN FORM GRID -->
    <form action="/bookings" method="POST" id="booking-form">
        <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
        <input type="hidden" name="csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

        <div class="row g-4">
            <!-- LEFT COLUMN: BOOKING OPTIONS -->
            <div class="col-lg-7">
                <!-- Tour Header Card -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                    <div class="d-flex align-items-start gap-3">
                        <img src="/assets/images/logo.svg" 
                             alt="OnlineTraveller" 
                             width="48" 
                             height="48" 
                             class="rounded-circle shadow-sm flex-shrink-0"
                             style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2">
                                <i class="bi bi-star-fill text-warning me-1"></i> Trải Nghiệm Bản Địa Đích Thực
                            </span>
                            <h4 class="fw-bold font-editorial text-dark mb-1">
                                <?= htmlspecialchars($quote['service_title'], ENT_QUOTES, 'UTF-8') ?>
                            </h4>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Điểm đón: <?= htmlspecialchars($quote['meeting_point'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Chọn đợt khởi hành -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-calendar-event text-primary me-2"></i> 1. Chọn Đợt Khởi Hành
                        </h6>
                        <span class="badge bg-light text-dark border">
                            <?= count($availableSchedules ?? []) ?> lịch khả dụng
                        </span>
                    </div>

                    <div class="mb-3">
                        <label for="schedule_id" class="form-label small fw-semibold text-muted">Lịch trình & Giờ đón khách</label>
                        <select class="form-select rounded-3 py-2" id="schedule_id" name="schedule_id" required>
                            <?php foreach ($availableSchedules as $sch): ?>
                                <?php 
                                    $isSelected = ((int)$sch['id'] === (int)$quote['schedule_id']);
                                    $startStr = date('H:i - d/m/Y', strtotime($sch['starts_at']));
                                    $endStr = date('H:i - d/m/Y', strtotime($sch['ends_at']));
                                ?>
                                <option value="<?= (int)$sch['id'] ?>" 
                                        data-capacity="<?= (int)$sch['capacity'] ?>" 
                                        data-available="<?= (int)$sch['available_seats'] ?>"
                                        data-starts="<?= $startStr ?>"
                                        data-ends="<?= $endStr ?>"
                                        data-meeting="<?= htmlspecialchars($sch['meeting_point'], ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $isSelected ? 'selected' : '' ?>>
                                    Khởi hành: <?= $startStr ?> | Còn trống <?= (int)$sch['available_seats'] ?> chỗ
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Selected Schedule Badge Details -->
                    <div class="p-3 bg-light rounded-3 border small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-clock me-1"></i> Bắt đầu chuyến đi:</span>
                            <strong id="display-schedule-start"><?= date('H:i - d/m/Y', strtotime($quote['starts_at'])) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-clock-history me-1"></i> Kết thúc chuyến đi:</span>
                            <strong id="display-schedule-end"><?= date('H:i - d/m/Y', strtotime($quote['ends_at'])) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-pin-map me-1"></i> Điểm tập trung:</span>
                            <span class="fw-semibold text-end" id="display-schedule-meeting"><?= htmlspecialchars($quote['meeting_point'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Chọn số lượng người tham gia -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="bi bi-people text-primary me-2"></i> 2. Số Lượng Khách Tham Gia
                    </h6>

                    <div class="row align-items-center">
                        <div class="col-sm-7 mb-2 mb-sm-0">
                            <label class="form-label small fw-semibold text-muted d-block mb-1">Số khách đăng ký</label>
                            <small class="text-muted d-block">Giá tính theo từng cá nhân tham gia trải nghiệm.</small>
                        </div>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary px-3" id="btn-decrease">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <input type="number" 
                                       name="participants_count" 
                                       id="participants_count" 
                                       class="form-control text-center fw-bold fs-5" 
                                       value="<?= (int)$quote['participants_count'] ?>" 
                                       min="1" 
                                       max="30" 
                                       required>
                                <button type="button" class="btn btn-outline-secondary px-3" id="btn-increase">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Phương thức & Kế hoạch thanh toán -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="bi bi-credit-card-2-front text-primary me-2"></i> 3. Kế Hoạch Thanh Toán Giữ Chỗ
                    </h6>

                    <?php if ($quote['price_mode'] === 'free'): ?>
                        <div class="alert alert-success border-0 rounded-3 mb-0">
                            <i class="bi bi-gift-fill me-2 fs-5"></i> 
                            <strong>Trải nghiệm hoàn toàn miễn phí!</strong> Bạn không cần thanh toán bất kỳ khoản phí nào.
                            <input type="hidden" name="payment_plan" value="host_direct">
                        </div>
                    <?php else: ?>
                        <div class="vstack gap-3">
                            <!-- Plan 1: Deposit 50% -->
                            <label class="card border rounded-3 p-3 cursor-pointer plan-card <?= $quote['payment_plan'] === 'deposit_50' ? 'border-primary bg-primary-subtle bg-opacity-10' : '' ?>" style="cursor: pointer;">
                                <div class="d-flex align-items-start gap-3">
                                    <input type="radio" 
                                           name="payment_plan" 
                                           value="deposit_50" 
                                           class="form-check-input mt-1 flex-shrink-0"
                                           <?= $quote['payment_plan'] === 'deposit_50' ? 'checked' : '' ?>>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark">Đặt cọc trước 50% online qua MoMo</strong>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Khuyên dùng</span>
                                        </div>
                                        <p class="small text-muted mb-0">
                                            Giữ chỗ chắc chắn với Host. 50% số tiền còn lại bạn thanh toán trực tiếp khi gặp Host tại điểm hẹn.
                                        </p>
                                    </div>
                                </div>
                            </label>

                            <!-- Plan 2: Full online 100% -->
                            <label class="card border rounded-3 p-3 cursor-pointer plan-card <?= $quote['payment_plan'] === 'full_online' ? 'border-primary bg-primary-subtle bg-opacity-10' : '' ?>" style="cursor: pointer;">
                                <div class="d-flex align-items-start gap-3">
                                    <input type="radio" 
                                           name="payment_plan" 
                                           value="full_online" 
                                           class="form-check-input mt-1 flex-shrink-0"
                                           <?= $quote['payment_plan'] === 'full_online' ? 'checked' : '' ?>>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark">Thanh toán toàn phần 100% qua MoMo</strong>
                                            <span class="badge bg-light text-muted border rounded-pill px-2">Tiện lợi</span>
                                        </div>
                                        <p class="small text-muted mb-0">
                                            Thanh toán đầy đủ 1 lần trước chuyến đi. Không cần mang theo tiền mặt trong suốt hành trình.
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Step 4: Mã ưu đãi / Khuyến mãi -->
                <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="bi bi-tag text-primary me-2"></i> 4. Mã Khuyến Mãi & Ưu Đãi
                    </h6>

                    <div class="input-group mb-2">
                        <input type="text" 
                               id="promo_code_input" 
                               name="promo_code" 
                               class="form-control text-uppercase" 
                               placeholder="Nhập mã ưu đãi (VD: CHAO2026, VIPLOCAL)..."
                               value="<?= htmlspecialchars($quote['applied_promotion']['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button class="btn btn-outline-dark px-4 fw-semibold" type="button" id="btn-apply-promo">
                            <i class="bi bi-check2-circle me-1"></i> Áp Dụng
                        </button>
                    </div>

                    <div id="promo-message-box">
                        <?php if (!empty($quote['applied_promotion'])): ?>
                            <div class="alert alert-success py-2 px-3 mb-0 small rounded-3 d-flex align-items-center justify-content-between">
                                <span>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                                    Đang áp dụng ưu đãi: <strong><?= htmlspecialchars($quote['applied_promotion']['title']) ?></strong> (-<?= $quote['applied_promotion']['discount_percent'] ?>%)
                                </span>
                                <span class="badge bg-success">Tự động chọn ưu đãi tốt nhất</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: STICKY PRICE SUMMARY & CONFIRMATION -->
            <div class="col-lg-5">
                <div class="sticky-top" style="top: 5.5rem;">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">
                            BẢNG TỔNG HỢP CHI PHÍ
                        </span>
                        <h5 class="fw-bold font-editorial text-dark mb-3">Tóm Tắt Đơn Đặt Chỗ</h5>

                        <!-- Price Breakdown Rows -->
                        <div class="vstack gap-2 border-bottom pb-3 mb-3 small">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Đơn giá / người:</span>
                                <strong id="summary-unit-price"><?= $quote['formatted']['unit_price'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Số lượng khách:</span>
                                <strong id="summary-count"><?= (int)$quote['participants_count'] ?> người</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Tạm tính tiền tour:</span>
                                <strong id="summary-gross"><?= $quote['formatted']['gross_total'] ?></strong>
                            </div>
                            <div class="d-flex justify-content-between text-success" id="row-discount" style="<?= $quote['discount_amount'] > 0 ? '' : 'display: none;' ?>">
                                <span><i class="bi bi-tag-fill me-1"></i> Giảm giá ưu đãi:</span>
                                <strong id="summary-discount">-<?= $quote['formatted']['discount_amount'] ?></strong>
                            </div>
                        </div>

                        <!-- Total & Payment Plan Split -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-baseline mb-2">
                                <span class="fw-bold text-dark">Tổng giá trị đơn:</span>
                                <span class="fs-4 fw-bold font-editorial text-dark" id="summary-total">
                                    <?= $quote['formatted']['total_price'] ?>
                                </span>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-success">
                                        <i class="bi bi-shield-check me-1"></i> Tiền cọc giữ chỗ (Online):
                                    </span>
                                    <span class="fs-5 fw-bold text-success" id="summary-deposit">
                                        <?= $quote['formatted']['deposit_amount'] ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span>Còn lại thanh toán cho Host:</span>
                                    <span class="fw-semibold" id="summary-remaining">
                                        <?= $quote['formatted']['remaining_amount'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Submit Button -->
                        <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-semibold shadow-sm mb-3" id="btn-submit-booking">
                            <i class="bi bi-calendar-check me-2"></i> Gửi Yêu Cầu Đặt Chỗ Ngay
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted d-block" style="font-size: 0.8rem;">
                                <i class="bi bi-shield-lock-fill text-success me-1"></i>
                                An toàn 100% • Giữ chỗ 15 phút sau khi Host phê duyệt
                            </small>
                        </div>
                    </div>

                    <!-- Trust & Guarantee Card -->
                    <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle flex-shrink-0" style="width: 42px; height: 42px;">
                                <i class="bi bi-shield-fill-check fs-5"></i>
                            </div>
                            <div class="small">
                                <strong class="text-dark d-block">Cam Kết Hoàn Tiền 100%</strong>
                                <span class="text-muted">Hoàn tiền đầy đủ nếu chuyến đi bị hủy do thời tiết xấu hoặc bất khả kháng.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- CLIENT-SIDE DYNAMIC QUOTATION SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scheduleSelect = document.getElementById('schedule_id');
    const participantsInput = document.getElementById('participants_count');
    const btnDecrease = document.getElementById('btn-decrease');
    const btnIncrease = document.getElementById('btn-increase');
    const btnApplyPromo = document.getElementById('btn-apply-promo');
    const promoInput = document.getElementById('promo_code_input');
    const promoMessageBox = document.getElementById('promo-message-box');
    const planRadios = document.querySelectorAll('input[name="payment_plan"]');

    // Display fields
    const displayStart = document.getElementById('display-schedule-start');
    const displayEnd = document.getElementById('display-schedule-end');
    const displayMeeting = document.getElementById('display-schedule-meeting');

    // Summary fields
    const summaryUnitPrice = document.getElementById('summary-unit-price');
    const summaryCount = document.getElementById('summary-count');
    const summaryGross = document.getElementById('summary-gross');
    const rowDiscount = document.getElementById('row-discount');
    const summaryDiscount = document.getElementById('summary-discount');
    const summaryTotal = document.getElementById('summary-total');
    const summaryDeposit = document.getElementById('summary-deposit');
    const summaryRemaining = document.getElementById('summary-remaining');

    function getSelectedPlan() {
        const checked = document.querySelector('input[name="payment_plan"]:checked');
        return checked ? checked.value : 'deposit_50';
    }

    function updateQuote() {
        const scheduleId = scheduleSelect.value;
        const count = participantsInput.value;
        const plan = getSelectedPlan();
        const promoCode = promoInput.value.trim();

        // Update schedule metadata display
        const opt = scheduleSelect.options[scheduleSelect.selectedIndex];
        if (opt) {
            displayStart.textContent = opt.dataset.starts || '';
            displayEnd.textContent = opt.dataset.ends || '';
            displayMeeting.textContent = opt.dataset.meeting || '';
            const maxCap = parseInt(opt.dataset.available || '30', 10);
            participantsInput.max = maxCap;
        }

        // Send AJAX quotation request
        fetch('/api/bookings/quote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                schedule_id: scheduleId,
                participants_count: count,
                payment_plan: plan,
                promo_code: promoCode
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) {
                const q = res.data;
                summaryUnitPrice.textContent = q.formatted.unit_price;
                summaryCount.textContent = q.participants_count + ' người';
                summaryGross.textContent = q.formatted.gross_total;

                if (q.discount_amount > 0) {
                    rowDiscount.style.display = 'flex';
                    summaryDiscount.textContent = '-' + q.formatted.discount_amount;
                } else {
                    rowDiscount.style.display = 'none';
                }

                summaryTotal.textContent = q.formatted.total_price;
                summaryDeposit.textContent = q.formatted.deposit_amount;
                summaryRemaining.textContent = q.formatted.remaining_amount;

                if (q.applied_promotion) {
                    promoMessageBox.innerHTML = `
                        <div class="alert alert-success py-2 px-3 mb-0 small rounded-3 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                Đang áp dụng: <strong>${q.applied_promotion.title}</strong> (-${q.applied_promotion.discount_percent}%)
                            </span>
                            <span class="badge bg-success">${q.applied_promotion.code}</span>
                        </div>
                    `;
                } else if (promoCode === '') {
                    promoMessageBox.innerHTML = '';
                }
            } else {
                promoMessageBox.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 mb-0 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                        ${res.message || 'Mã ưu đãi không hợp lệ.'}
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error('Error fetching quote:', err);
        });
    }

    // Number stepper
    btnDecrease.addEventListener('click', function () {
        let val = parseInt(participantsInput.value, 10) || 1;
        if (val > 1) {
            participantsInput.value = val - 1;
            updateQuote();
        }
    });

    btnIncrease.addEventListener('click', function () {
        let val = parseInt(participantsInput.value, 10) || 1;
        let max = parseInt(participantsInput.max, 10) || 30;
        if (val < max) {
            participantsInput.value = val + 1;
            updateQuote();
        }
    });

    scheduleSelect.addEventListener('change', updateQuote);
    participantsInput.addEventListener('change', updateQuote);
    btnApplyPromo.addEventListener('click', updateQuote);

    planRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.plan-card').forEach(c => {
                c.classList.remove('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
            });
            this.closest('.plan-card').classList.add('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
            updateQuote();
        });
    });
});
</script>
