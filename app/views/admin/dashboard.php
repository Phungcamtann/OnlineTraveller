<?php
/** @var array $stats */
/** @var array $recentServices */
/** @var array|null $user */
?>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-shield-lock-fill me-1"></i> Trung Tâm Quản Trị Hệ Thống (Admin)
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Tổng Quan Vận Hành OnlineTraveller</h2>
            <p class="text-muted small mb-0">Phiên bản thiết kế tinh chỉnh v2.2 • Quản lý danh mục, duyệt tour và người dùng</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/services" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-compass-fill me-1"></i> Trải Nghiệm (Tours)
            </a>
            <a href="/admin/users" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-people-fill me-1"></i> Người Dùng (Users)
            </a>
            <a href="/admin/categories" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-folder-fill me-1"></i> Danh Mục (Categories)
            </a>
            <a href="/admin/posts" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-file-text-fill me-1"></i> Cẩm Nang (Blog)
            </a>
        </div>
    </div>

    <!-- 5 Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <a href="/admin/users" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition">
                    <small class="text-muted fw-bold d-block mb-1">NGƯỜI DÙNG</small>
                    <div class="fs-3 fw-bold text-dark font-editorial"><?= $stats['users'] ?></div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <a href="/admin/services" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition">
                    <small class="text-muted fw-bold d-block mb-1">TRẢI NGHIỆM</small>
                    <div class="fs-3 fw-bold text-primary font-editorial"><?= $stats['services'] ?></div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-6">
            <a href="/admin/services?status=pending" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition border border-warning border-opacity-50">
                    <small class="text-warning fw-bold d-block mb-1"><i class="bi bi-hourglass-split me-1"></i>CHỜ DUYỆT (PENDING)</small>
                    <div class="fs-3 fw-bold text-warning font-editorial"><?= $stats['pending_services'] ?></div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <a href="/admin/categories" class="text-decoration-none">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center hover-shadow transition">
                    <small class="text-muted fw-bold d-block mb-1">DANH MỤC</small>
                    <div class="fs-3 fw-bold text-success font-editorial"><?= $stats['categories'] ?></div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-12">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">LƯỢT ĐẶT LỊCH</small>
                <div class="fs-3 fw-bold text-info font-editorial"><?= $stats['bookings'] ?></div>
            </div>
        </div>
    </div>

    <!-- Recent Services for Moderation -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">Trải Nghiệm Mới Đăng Ký Cần Kiểm Duyệt</h5>
                <p class="small text-muted mb-0">Xem xét và phê duyệt hoặc từ chối các tour do Dân bản địa gửi lên</p>
            </div>
            <a href="/admin/services" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                Xem tất cả tour <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên trải nghiệm</th>
                        <th>Host bản địa</th>
                        <th>Danh mục</th>
                        <th>Địa điểm</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Kiểm duyệt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentServices as $srv): ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <a href="/services/<?= (int)$srv['id'] ?>" class="text-decoration-none text-dark hover-primary">
                                    <?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td><i class="bi bi-person text-muted me-1"></i><?= htmlspecialchars($srv['host_name']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($srv['category_name']) ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($srv['location']) ?></td>
                            <td class="fw-bold text-dark"><?= FormatHelper::formatVnd((int)$srv['base_price']) ?></td>
                            <td>
                                <?php
                                    $badgeClass = match($srv['status']) {
                                        'approved' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= strtoupper($srv['status']) ?></span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <?php if ($srv['status'] === 'pending'): ?>
                                        <!-- Nút Phê Duyệt Nhanh -->
                                        <form action="/admin/services/<?= (int)$srv['id'] ?>/moderate" method="POST" class="d-inline">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-none fw-semibold" onclick="return confirm('Bạn có chắc chắn muốn phê duyệt mở bán tour: <?= htmlspecialchars(addslashes($srv['title'])) ?>?')">
                                                <i class="bi bi-check-circle-fill me-1"></i> Duyệt
                                            </button>
                                        </form>

                                        <!-- Nút Mở Modal Từ Chối -->
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 shadow-none" data-bs-toggle="modal" data-bs-target="#rejectModalDashboard<?= (int)$srv['id'] ?>" title="Từ chối mở bán">
                                            <i class="bi bi-x-circle"></i>
                                        </button>

                                        <!-- Modal Từ Chối -->
                                        <div class="modal fade text-start" id="rejectModalDashboard<?= (int)$srv['id'] ?>" tabindex="-1" aria-hidden="true">
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
                                                            <p class="small text-muted mb-3">
                                                                Vui lòng nhập lý do từ chối để thông báo cho Host <strong><?= htmlspecialchars($srv['host_name']) ?></strong> điều chỉnh:
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold text-dark">Lý do từ chối:</label>
                                                                <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Ví dụ: Hình ảnh trạm chưa rõ nét, thiếu điểm hẹn..." required></textarea>
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
                                    <?php elseif ($srv['status'] === 'approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill me-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-check2"></i> Đang mở bán
                                        </span>
                                    <?php elseif ($srv['status'] === 'rejected'): ?>
                                        <form action="/admin/services/<?= (int)$srv['id'] ?>/moderate" method="POST" class="d-inline">
                                            <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 shadow-none me-1" title="Duyệt lại" onclick="return confirm('Phê duyệt lại tour này?')">
                                                <i class="bi bi-arrow-repeat me-1"></i> Duyệt lại
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="/services/<?= (int)$srv['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Xem chi tiết tour">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
