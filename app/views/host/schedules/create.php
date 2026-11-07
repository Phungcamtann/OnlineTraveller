<?php
/** @var array $tours */
/** @var int $selectedServiceId */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Host Portal</a></li>
            <li class="breadcrumb-item"><a href="/host/schedules" class="text-decoration-none text-muted">Lịch khởi hành</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Mở Lịch Mới</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white">
                <div class="mb-4 text-center">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill mb-2">
                        <i class="bi bi-calendar-plus-fill me-1"></i> Điều Phối Tour
                    </span>
                    <h3 class="font-editorial fw-bold text-dark mb-1">Mở Đợt Khởi Hành Mới</h3>
                    <p class="text-muted small mb-0">Thiết lập thời gian khởi hành, điểm đón và số lượng khách tối đa nhận phục vụ.</p>
                </div>

                <form action="/host/schedules" method="POST" id="createScheduleForm">
                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                    <input type="hidden" name="csrf_token" value="<?= SecurityHelper::csrfToken() ?>">

                    <!-- 1. Select Tour -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Chọn gói trải nghiệm <span class="text-danger">*</span></label>
                        <select name="service_id" class="form-select rounded-3 py-2" required>
                            <option value="">-- Chọn gói trải nghiệm bạn muốn mở lịch --</option>
                            <?php foreach ($tours as $t): ?>
                                <option value="<?= (int)$t['id'] ?>" <?= $selectedServiceId === (int)$t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['title']) ?> (<?= htmlspecialchars($t['location']) ?> - <?= (int)$t['duration_days'] ?> ngày)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 2. Timings -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Thời điểm khởi hành (Starts at) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="starts_at" class="form-control rounded-3 py-2" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Thời gian bắt đầu tour đón khách.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Thời điểm kết thúc (Ends at) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ends_at" class="form-control rounded-3 py-2" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Phải diễn ra sau thời điểm khởi hành.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Hạn chốt đăng ký (Booking closes at) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="booking_closes_at" class="form-control rounded-3 py-2" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Sau thời điểm này hệ thống sẽ tự khóa đơn đặt.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Sức chứa tối đa (Capacity) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="capacity" class="form-control rounded-start-3 py-2" value="10" min="1" max="500" required>
                                <span class="input-group-text rounded-end-3 bg-light text-muted">người</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Meeting point -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Điểm hẹn đón khách tập trung <span class="text-danger">*</span></label>
                        <input type="text" name="meeting_point" class="form-control rounded-3 py-2" required placeholder="Ví dụ: Cổng chợ Đà Lạt, đường Nguyễn Thị Minh Khai">
                        <small class="text-muted" style="font-size: 0.75rem;">Mô tả chi tiết để du khách dễ dàng tìm thấy bạn vào ngày khởi hành.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-editorial flex-grow-1 rounded-pill py-2 shadow-sm">
                            <i class="bi bi-calendar-check me-1"></i> Xác Nhận Mở Lịch Khởi Hành
                        </button>
                        <a href="/host/schedules" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            Hủy Bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
