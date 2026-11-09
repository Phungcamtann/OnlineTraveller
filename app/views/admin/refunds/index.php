<?php
/**
 * View: Quản Lý Hồ Sơ Hoàn Tiền Admin (TV3-06)
 * File: app/views/admin/refunds/index.php
 *
 * @var array $refunds Danh sách hồ sơ hoàn tiền
 * @var array $stats Thống kê tổng quan
 * @var ?string $currentStatus Bộ lọc trạng thái hiện tại
 * @var array $user Thông tin Admin
 */

use App\Helpers\FormatHelper;
use App\Helpers\SecurityHelper;

$totalCount = (int)($stats['total_count'] ?? 0);
$requestedCount = (int)($stats['requested_count'] ?? 0);
$approvedCount = (int)($stats['approved_count'] ?? 0);
$totalAmount = (float)($stats['total_amount'] ?? 0);
?>

<div class="container-fluid py-4">
    <!-- PAGE TITLE -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="/admin/dashboard" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Quản Lý Hoàn Tiền</li>
                </ol>
            </nav>
            <h3 class="fw-bold font-editorial text-dark mb-0">Hồ Sơ Hủy Tour & Hoàn Tiền (MoMo Sandbox)</h3>
            <small class="text-muted">Theo dõi và phê duyệt các nghĩa vụ hoàn tiền theo quy định T24 và ngoại lệ.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/admin/dashboard" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="/admin/users" class="btn btn-outline-secondary rounded-pill px-3 py-1 btn-sm">
                <i class="bi bi-people me-1"></i> Người Dùng
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Tổng số hồ sơ</small>
                        <h4 class="fw-bold text-dark mb-0"><?= $totalCount ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Chờ Admin duyệt</small>
                        <h4 class="fw-bold text-warning mb-0"><?= $requestedCount ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Đã duyệt hoàn</small>
                        <h4 class="fw-bold text-success mb-0"><?= $approvedCount ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Tổng tiền hoàn</small>
                        <h5 class="fw-bold text-info mb-0 font-monospace"><?= FormatHelper::formatVnd($totalAmount) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BUTTONS -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="/admin/refunds" class="btn btn-sm rounded-pill <?= empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Tất cả (<?= $totalCount ?>)
        </a>
        <a href="/admin/refunds?status=requested" class="btn btn-sm rounded-pill <?= $currentStatus === 'requested' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' ?>">
            Chờ xem xét (<?= $requestedCount ?>)
        </a>
        <a href="/admin/refunds?status=approved" class="btn btn-sm rounded-pill <?= $currentStatus === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
            Đã duyệt (<?= $approvedCount ?>)
        </a>
    </div>

    <!-- REFUNDS TABLE -->
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Mã hoàn tiền</th>
                        <th>Đơn đặt / Tour</th>
                        <th>Du khách</th>
                        <th>Số tiền hoàn</th>
                        <th>Trạng thái</th>
                        <th>Lý do hủy</th>
                        <th>Thời gian</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($refunds)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                Không tìm thấy hồ sơ hoàn tiền nào phù hợp.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($refunds as $rf): ?>
                            <?php
                            $rfStatus = (string)$rf['status'];
                            $badgeClass = match($rfStatus) {
                                'approved' => 'bg-success',
                                'succeeded' => 'bg-primary',
                                'requested' => 'bg-warning text-dark',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                            ?>
                            <tr>
                                <td>
                                    <span class="font-monospace fw-bold text-dark">#<?= htmlspecialchars($rf['refund_code']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">#<?= htmlspecialchars($rf['booking_code']) ?></div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                        <?= htmlspecialchars($rf['service_title']) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small"><?= htmlspecialchars($rf['tourist_name']) ?></div>
                                </td>
                                <td>
                                    <span class="font-monospace fw-bold text-success">
                                        <?= FormatHelper::formatVnd((float)$rf['amount']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1 small">
                                        <?= ucfirst($rfStatus) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;" title="<?= htmlspecialchars($rf['reason']) ?>">
                                        <?= htmlspecialchars($rf['reason']) ?>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('H:i d/m/Y', strtotime($rf['created_at'])) ?></small>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="/refunds/<?= (int)$rf['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" title="Xem chi tiết phiếu hoàn">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if ($rfStatus === 'requested'): ?>
                                            <form method="POST" action="/admin/refunds/<?= (int)$rf['id'] ?>/approve" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt hoàn số tiền này không?');">
                                                <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                <input type="hidden" name="admin_note" value="Quản trị viên đã phê duyệt hoàn tiền ngoại lệ sau T24.">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-0" title="Phê duyệt hoàn tiền">
                                                    <i class="bi bi-check-lg"></i> Duyệt
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
