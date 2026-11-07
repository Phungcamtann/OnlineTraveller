<?php
/** @var array $schedules */
/** @var array $tours */
/** @var int $selectedServiceId */
/** @var string|null $status */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/host" class="text-decoration-none text-muted">Host Portal</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Quản Lý Lịch Khởi Hành</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="font-editorial fw-bold text-dark mb-1">Kế Hoạch & Lịch Khởi Hành</h2>
            <p class="text-muted small mb-0">Thiết lập các đợt đón khách, theo dõi sức chứa còn trống và điều phối lịch trình.</p>
        </div>
        <div>
            <a href="/host/schedules/create" class="btn btn-editorial rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-calendar-plus-fill me-1"></i> Mở Lịch Khởi Hành Mới
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 rounded-4 shadow-sm p-3 bg-white mb-4">
        <form action="/host/schedules" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="service_id" class="form-select rounded-pill small py-2" onchange="this.form.submit()">
                    <option value="">-- Lọc theo tất cả tour trải nghiệm --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= (int)$t['id'] ?>" <?= $selectedServiceId === (int)$t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select rounded-pill small py-2" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="open" <?= ($status ?? '') === 'open' ? 'selected' : '' ?>>Đang mở bán (Open)</option>
                    <option value="closed" <?= ($status ?? '') === 'closed' ? 'selected' : '' ?>>Đã đóng sổ (Closed)</option>
                    <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy (Cancelled)</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <a href="/host/schedules" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 w-100">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt Lại
                </a>
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <?php if (empty($schedules)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark mb-1">Chưa có lịch khởi hành nào</h5>
                <p class="small text-muted mb-3">Mở các đợt đón khách vào dịp cuối tuần hoặc các ngày đẹp trời để nhận khách đặt chỗ.</p>
                <a href="/host/schedules/create" class="btn btn-editorial rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Mở lịch đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th style="min-width: 240px;">Tour trải nghiệm</th>
                            <th>Thời gian khởi hành</th>
                            <th>Điểm đón khách</th>
                            <th>Hạn chốt đặt</th>
                            <th style="min-width: 170px;">Sức chứa đoàn</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $sch): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <?= htmlspecialchars($sch['service_title'] ?? 'Tour', ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($sch['service_location'] ?? '') ?></small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <i class="bi bi-calendar2-check text-primary me-1"></i><?= FormatHelper::formatDateTime($sch['starts_at']) ?>
                                    </div>
                                    <small class="text-muted">Đến <?= FormatHelper::formatDateTime($sch['ends_at']) ?></small>
                                </td>
                                <td>
                                    <small class="text-dark"><i class="bi bi-pin-map text-danger me-1"></i><?= htmlspecialchars($sch['meeting_point']) ?></small>
                                </td>
                                <td>
                                    <small class="text-muted"><?= FormatHelper::formatDateTime($sch['booking_closes_at']) ?></small>
                                </td>
                                <td>
                                    <?php
                                        $capacity = max(1, (int)$sch['capacity']);
                                        $booked = (int)$sch['booked_seats'];
                                        $avail = (int)$sch['available_seats'];
                                        $percent = min(100, round(($booked / $capacity) * 100));
                                    ?>
                                    <div class="d-flex align-items-center justify-content-between mb-1 small">
                                        <span class="text-muted">Đã đặt: <strong><?= $booked ?></strong></span>
                                        <span class="text-success fw-bold">Còn <?= $avail ?> chỗ</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $schBadge = match($sch['status']) {
                                            'open' => 'bg-success',
                                            'closed' => 'bg-secondary',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-info',
                                        };
                                        $schLabel = match($sch['status']) {
                                            'open' => 'Đang mở',
                                            'closed' => 'Đã đóng',
                                            'cancelled' => 'Đã hủy',
                                            default => 'Hoàn thành',
                                        };
                                    ?>
                                    <span class="badge <?= $schBadge ?> rounded-pill px-2 py-1"><?= $schLabel ?></span>
                                    <?php if (!empty($sch['cancel_reason'])): ?>
                                        <div class="small text-danger mt-1" style="font-size: 0.7rem;" title="<?= htmlspecialchars($sch['cancel_reason']) ?>">
                                            <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars(mb_substr($sch['cancel_reason'], 0, 20)) ?>...
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($sch['status'] === 'open'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelScheduleModal" data-schedule-id="<?= (int)$sch['id'] ?>" data-tour-title="<?= htmlspecialchars($sch['service_title'] ?? '') ?>">
                                            <i class="bi bi-x-circle me-1"></i> Hủy lịch
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Hủy Lịch Khởi Hành -->
<div class="modal fade" id="cancelScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" id="cancelScheduleForm" class="modal-content rounded-4 border-0 shadow">
            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
            <input type="hidden" name="csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark font-editorial">Hủy Lịch Khởi Hành</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning small rounded-3 mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Khi hủy lịch, toàn bộ các đơn đặt chỗ đang chờ (pending) sẽ tự động bị hủy và thông báo đến du khách.
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Lý do hủy lịch trình <span class="text-danger">*</span></label>
                    <textarea name="cancel_reason" class="form-control rounded-3" rows="3" required minlength="5" placeholder="Ví dụ: Mưa bão lớn bất khả kháng, sạt lở đường đèo không đảm bảo an toàn..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4">Xác Nhận Hủy Lịch</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = document.getElementById('cancelScheduleModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const scheduleId = button.getAttribute('data-schedule-id');
            const form = document.getElementById('cancelScheduleForm');
            form.action = '/host/schedules/' + scheduleId + '/cancel';
        });
    }
});
</script>
