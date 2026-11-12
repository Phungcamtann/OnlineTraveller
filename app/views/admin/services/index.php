<?php
/**
 * Admin Service Management & Moderation View
 * OnlineTraveller — Quản lý và kiểm duyệt trải nghiệm tour của Host bản địa.
 * 
 * @var array $services
 * @var array $counts
 * @var string|null $currentStatus
 * @var string|null $search
 * @var array|null $user
 */

$services = $services ?? [];
$counts = $counts ?? ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
$currentStatus = $currentStatus ?? null;
$search = $search ?? null;
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Quản lý trải nghiệm</li>
        </ol>
    </nav>

    <!-- Header & Quick Nav -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-shield-check me-1"></i> Kiểm Duyệt Trải Nghiệm (Tours)
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý & Phê Duyệt Trải Nghiệm</h2>
            <p class="text-muted small mb-0">Xét duyệt nội dung tour của Host bản địa, kiểm soát chất lượng điểm đến trước khi mở bán</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/banner" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold text-dark shadow-sm">
                <i class="bi bi-images me-1"></i> Quản Lý Banner
            </a>
            <a href="/admin" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-speedometer2 me-1"></i> Bảng Điều Khiển
            </a>
            <a href="/admin/users" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-people-fill me-1"></i> Người Dùng
            </a>
            <a href="/admin/categories" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-folder-fill me-1"></i> Danh Mục
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards / Quick Filter -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6">
            <a href="/admin/services" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition <?= $currentStatus === null ? 'border-primary border-2 border' : '' ?>">
                    <small class="text-muted fw-bold d-block mb-1">TẤT CẢ TRẢI NGHIỆM</small>
                    <div class="fs-3 fw-bold text-dark font-editorial"><?= number_format($counts['all'] ?? 0) ?></div>
                    <small class="text-muted">Toàn bộ tour trong hệ thống</small>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="/admin/services?status=pending" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition border-warning border-opacity-75 <?= $currentStatus === 'pending' ? 'border-2 border' : 'border' ?>">
                    <small class="text-warning fw-bold d-block mb-1">
                        <i class="bi bi-hourglass-split me-1"></i>CHỜ PHÊ DUYỆT
                    </small>
                    <div class="fs-3 fw-bold text-warning font-editorial"><?= number_format($counts['pending'] ?? 0) ?></div>
                    <small class="text-muted">Host mới đăng ký cần duyệt</small>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="/admin/services?status=approved" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition <?= $currentStatus === 'approved' ? 'border-success border-2 border' : '' ?>">
                    <small class="text-success fw-bold d-block mb-1">
                        <i class="bi bi-check-circle me-1"></i>ĐANG MỞ BÁN
                    </small>
                    <div class="fs-3 fw-bold text-success font-editorial"><?= number_format($counts['approved'] ?? 0) ?></div>
                    <small class="text-muted">Đã duyệt hiển thị trên sàn</small>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-sm-6">
            <a href="/admin/services?status=rejected" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition <?= $currentStatus === 'rejected' ? 'border-danger border-2 border' : '' ?>">
                    <small class="text-danger fw-bold d-block mb-1">
                        <i class="bi bi-x-circle me-1"></i>ĐÃ TỪ CHỐI
                    </small>
                    <div class="fs-3 fw-bold text-danger font-editorial"><?= number_format($counts['rejected'] ?? 0) ?></div>
                    <small class="text-muted">Chờ host bổ sung thông tin</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white mb-4">
        <form method="GET" action="/admin/services" class="row g-3 align-items-center">
            <div class="col-lg-5 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill text-muted ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control bg-light border-start-0 rounded-end-pill py-2" 
                           placeholder="Tìm theo tên tour, địa điểm, tên Host..." 
                           value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <select name="status" class="form-select bg-light rounded-pill py-2">
                    <option value="">— Tất cả trạng thái kiểm duyệt —</option>
                    <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Chờ phê duyệt (Pending)</option>
                    <option value="approved" <?= $currentStatus === 'approved' ? 'selected' : '' ?>>Đang mở bán (Approved)</option>
                    <option value="rejected" <?= $currentStatus === 'rejected' ? 'selected' : '' ?>>Đã từ chối (Rejected)</option>
                </select>
            </div>

            <div class="col-lg-3 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
                <?php if ($search || $currentStatus): ?>
                    <a href="/admin/services" class="btn btn-outline-secondary rounded-pill px-3" title="Đặt lại bộ lọc">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Services Table Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">
                Danh Sách Trải Nghiệm 
                <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2" style="font-size: 0.8rem;">
                    <?= count($services) ?> kết quả
                </span>
            </h5>
            <?php if ($currentStatus === 'pending'): ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-1 rounded-pill">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Đang lọc các tour CẦN DUYỆT
                </span>
            <?php endif; ?>
        </div>

        <?php if (empty($services)): ?>
            <div class="text-center py-5">
                <div class="display-6 text-muted mb-3"><i class="bi bi-inbox"></i></div>
                <h6 class="fw-bold text-dark">Không tìm thấy gói trải nghiệm nào</h6>
                <p class="text-muted small">Thử thay đổi từ khóa tìm kiếm hoặc bỏ chọn bộ lọc trạng thái</p>
                <a href="/admin/services" class="btn btn-outline-dark btn-sm rounded-pill px-4 mt-2">Xem tất cả tour</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 280px;">Trải nghiệm</th>
                            <th style="min-width: 170px;">Host bản địa</th>
                            <th>Danh mục</th>
                            <th>Địa điểm</th>
                            <th>Giá gốc</th>
                            <th>Trạng thái</th>
                            <th class="text-end" style="min-width: 180px;">Thao tác kiểm duyệt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $srv): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php 
                                            $imgUrl = !empty($srv['banner_image']) ? $srv['banner_image'] : '/assets/images/tours/dalat-san-may.jpg';
                                        ?>
                                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Tour" class="rounded-3 object-fit-cover shadow-sm flex-shrink-0" style="width: 54px; height: 54px;" onerror="this.src='/assets/images/tours/dalat-san-may.jpg'">
                                        <div>
                                            <a href="/services/<?= (int)$srv['id'] ?>" class="text-decoration-none text-dark fw-bold hover-primary d-block line-clamp-1" title="<?= htmlspecialchars($srv['title']) ?>">
                                                <?= htmlspecialchars($srv['title']) ?>
                                            </a>
                                            <small class="text-muted d-block">Mã tour: #<?= (int)$srv['id'] ?> • <?= date('d/m/Y', strtotime($srv['created_at'] ?? 'now')) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($srv['host_name'] ?? 'Chưa rõ') ?></div>
                                    <small class="text-muted d-block"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($srv['host_email'] ?? '') ?></small>
                                    <?php if (!empty($srv['host_phone'])): ?>
                                        <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($srv['host_phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                        <?= htmlspecialchars($srv['category_name'] ?? 'Chung') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($srv['location'] ?? 'Việt Nam') ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark"><?= FormatHelper::formatVnd((int)$srv['base_price']) ?></span>
                                </td>
                                <td>
                                    <?php if ($srv['status'] === 'approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Đang mở bán
                                        </span>
                                    <?php elseif ($srv['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold">
                                            <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                                        </span>
                                    <?php elseif ($srv['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger px-2 py-1 rounded-pill" data-bs-toggle="tooltip" title="<?= htmlspecialchars($srv['rejection_reason'] ?? 'Không rõ lý do') ?>">
                                            <i class="bi bi-x-circle-fill me-1"></i> Bị từ chối
                                        </span>
                                        <?php if (!empty($srv['rejection_reason'])): ?>
                                            <small class="text-danger d-block mt-1 font-monospace" style="font-size: 0.72rem;">
                                                Lý do: <?= htmlspecialchars(mb_strimwidth($srv['rejection_reason'], 0, 30, '...')) ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary px-2 py-1 rounded-pill"><?= strtoupper($srv['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <?php if ($srv['status'] === 'pending'): ?>
                                            <!-- Phê duyệt ngay -->
                                            <form action="/admin/services/<?= (int)$srv['id'] ?>/moderate" method="POST" class="d-inline">
                                                <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-none fw-semibold" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT mở bán tour: <?= htmlspecialchars(addslashes($srv['title'])) ?>?')">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Duyệt
                                                </button>
                                            </form>

                                            <!-- Mở modal từ chối -->
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#rejectModalList<?= (int)$srv['id'] ?>" title="Từ chối mở bán">
                                                <i class="bi bi-x-circle"></i> Từ chối
                                            </button>

                                            <!-- Modal từ chối -->
                                            <div class="modal fade text-start" id="rejectModalList<?= (int)$srv['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 border-0 shadow">
                                                        <form action="/admin/services/<?= (int)$srv['id'] ?>/moderate" method="POST">
                                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                            <input type="hidden" name="status" value="rejected">
                                                            <div class="modal-header border-bottom-0 pb-0">
                                                                <h5 class="modal-title font-editorial fw-bold text-danger">Từ Chối Phê Duyệt Tour</h5>
                                                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body py-2">
                                                                <div class="alert alert-warning small rounded-3 mb-3">
                                                                    <strong>Tour:</strong> <?= htmlspecialchars($srv['title']) ?><br>
                                                                    <strong>Host:</strong> <?= htmlspecialchars($srv['host_name']) ?>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label small fw-bold text-dark">Lý do từ chối (bắt buộc):</label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Ví dụ: Hình ảnh trạm trải nghiệm chưa rõ ràng, lịch trình chưa cụ thể..." required></textarea>
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
                                        <?php elseif ($srv['status'] === 'rejected'): ?>
                                            <!-- Nút Phê duyệt lại -->
                                            <form action="/admin/services/<?= (int)$srv['id'] ?>/moderate" method="POST" class="d-inline">
                                                <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-none fw-semibold" onclick="return confirm('Bạn có chắc muốn duyệt lại và mở bán tour này?')">
                                                    <i class="bi bi-arrow-repeat me-1"></i> Duyệt lại
                                                </button>
                                            </form>
                                        <?php elseif ($srv['status'] === 'approved'): ?>
                                            <!-- Nút Từ chối/Gỡ xuống khi cần -->
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#rejectModalList<?= (int)$srv['id'] ?>" title="Tạm ngưng/Gỡ mở bán">
                                                <i class="bi bi-pause-circle"></i> Gỡ xuống
                                            </button>

                                            <!-- Modal gỡ xuống -->
                                            <div class="modal fade text-start" id="rejectModalList<?= (int)$srv['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 border-0 shadow">
                                                        <form action="/admin/services/<?= (int)$srv['id'] ?>/moderate" method="POST">
                                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                            <input type="hidden" name="status" value="rejected">
                                                            <div class="modal-header border-bottom-0 pb-0">
                                                                <h5 class="modal-title font-editorial fw-bold text-danger">Tạm Ngưng Mở Bán Tour</h5>
                                                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body py-2">
                                                                <p class="small text-muted mb-2">Chuyển trạng thái tour thành "Đã từ chối" kèm lý do để Host điều chỉnh lại:</p>
                                                                <div class="mb-3">
                                                                    <label class="form-label small fw-bold text-dark">Lý do gỡ mở bán:</label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Lý do gỡ xuống..." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top-0 pt-0">
                                                                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Xác Nhận</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Xem trang tour -->
                                        <a href="/services/<?= (int)$srv['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Xem trang chi tiết tour">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
