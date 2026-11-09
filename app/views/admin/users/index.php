<?php
/**
 * Admin User Management View
 * TV1-06: Quản lý người dùng, phân quyền và khóa/mở tài khoản.
 * 
 * @var array $users
 * @var int $totalUsers
 * @var array $stats
 * @var array $filters
 * @var array|null $currentUser
 */
$users = $users ?? [];
$stats = $stats ?? ['total' => 0, 'hosts' => 0, 'tourists' => 0, 'admins' => 0, 'locked' => 0, 'active' => 0];
$filters = $filters ?? ['role' => null, 'status' => null, 'search' => null, 'page' => 1, 'totalPages' => 1];
$currentAdminId = (int)($currentUser['id'] ?? 0);
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/admin" class="text-decoration-none text-muted">Quản trị Admin</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Quản lý người dùng</li>
        </ol>
    </nav>

    <!-- Header & Quick Nav -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge bg-primary px-3 py-1 rounded-pill mb-1">
                <i class="bi bi-people-fill me-1"></i> Quản Trị Hệ Thống (TV1)
            </span>
            <h2 class="font-editorial fw-bold text-dark mb-0">Quản Lý Người Dùng & Phân Quyền</h2>
            <p class="text-muted small mb-0">Tra cứu thông tin, giám sát vai trò và khóa / mở khóa tài khoản người dùng an toàn</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-speedometer2 me-1"></i> Bảng Điều Khiển
            </a>
            <a href="/admin/categories" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-folder-fill me-1"></i> Danh Mục Tour
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">TỔNG NGƯỜI DÙNG</small>
                <div class="fs-3 fw-bold text-dark font-editorial"><?= number_format($stats['total']) ?></div>
                <small class="text-muted"><?= number_format($stats['active']) ?> đang hoạt động</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">DÂN BẢN ĐỊA (HOSTS)</small>
                <div class="fs-3 fw-bold text-primary font-editorial"><?= number_format($stats['hosts']) ?></div>
                <small class="text-muted">Chủ homestay & hướng dẫn tour</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">DU KHÁCH (TOURISTS)</small>
                <div class="fs-3 fw-bold text-info font-editorial"><?= number_format($stats['tourists']) ?></div>
                <small class="text-muted">Khách đặt tour trải nghiệm</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-white text-center">
                <small class="text-muted fw-bold d-block mb-1">BỊ KHÓA (LOCKED)</small>
                <div class="fs-3 fw-bold text-danger font-editorial"><?= number_format($stats['locked']) ?></div>
                <small class="text-muted">Tài khoản vi phạm chính sách</small>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="card border-0 rounded-4 shadow-sm p-3 p-md-4 bg-white mb-4">
        <form method="GET" action="/admin/users" class="row g-3 align-items-end">
            <!-- Keyword Search -->
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Tìm kiếm theo tên / email / SĐT</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" 
                           name="q" 
                           class="form-control bg-light border-start-0" 
                           placeholder="Nhập họ tên, email hoặc số điện thoại..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <!-- Role Filter -->
            <div class="col-lg-3 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Vai trò người dùng</label>
                <select name="role" class="form-select bg-light">
                    <option value="">-- Tất cả vai trò --</option>
                    <option value="tourist" <?= ($filters['role'] === 'tourist') ? 'selected' : '' ?>>Du khách (tourist)</option>
                    <option value="host" <?= ($filters['role'] === 'host') ? 'selected' : '' ?>>Dân bản địa (host)</option>
                    <option value="admin" <?= ($filters['role'] === 'admin') ? 'selected' : '' ?>>Quản trị viên (admin)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-lg-3 col-md-3 col-6">
                <label class="form-label small fw-bold text-muted mb-1">Trạng thái tài khoản</label>
                <select name="status" class="form-select bg-light">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" <?= ($filters['status'] === 'active') ? 'selected' : '' ?>>Hoạt động (active)</option>
                    <option value="locked" <?= ($filters['status'] === 'locked') ? 'selected' : '' ?>>Đã khóa (locked)</option>
                    <option value="inactive" <?= ($filters['status'] === 'inactive') ? 'selected' : '' ?>>Chưa kích hoạt (inactive)</option>
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="col-lg-2 col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-editorial flex-grow-1">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc
                </button>
                <a href="/admin/users" class="btn btn-outline-secondary rounded-pill" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="fw-bold text-dark font-editorial mb-0">
                Danh Sách Tài Khoản Người Dùng (<?= number_format($totalUsers) ?>)
            </h5>
            <small class="text-muted">Cập nhật trạng thái qua thủ tục <code>dhs_sp_users_set_status</code></small>
        </div>

        <?php if (empty($users)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                Không tìm thấy người dùng nào phù hợp với bộ lọc hiện tại.
                <div class="mt-3">
                    <a href="/admin/users" class="btn btn-outline-dark btn-sm rounded-pill px-4">Xóa bộ lọc tìm kiếm</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Người dùng</th>
                            <th>Thông tin liên hệ</th>
                            <th>Vai trò</th>
                            <th>Ngày tham gia</th>
                            <th>Trạng thái</th>
                            <th class="text-end" style="min-width: 160px;">Thao tác quản trị</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <?php 
                                $isSelf = ($currentAdminId === (int)$u['id']);
                                $roleBadge = match($u['role']) {
                                    'admin' => '<span class="badge bg-purple text-purple-emphasis border border-purple px-3 py-1 rounded-pill" style="background:#f3e8ff; color:#6b21a8; border-color:#d8b4fe !important;"><i class="bi bi-shield-check me-1"></i>Admin</span>',
                                    'host' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill"><i class="bi bi-house-door me-1"></i>Host Bản Địa</span>',
                                    default => '<span class="badge bg-light text-secondary border px-3 py-1 rounded-pill"><i class="bi bi-person me-1"></i>Du Khách</span>',
                                };

                                $statusBadge = match($u['status']) {
                                    'active' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Hoạt động</span>',
                                    'locked' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill"><i class="bi bi-lock-fill me-1"></i>Bị khóa</span>',
                                    default => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill"><i class="bi bi-pause-circle me-1"></i>Chưa kích hoạt</span>',
                                };
                            ?>
                            <tr>
                                <td class="fw-bold text-muted">#<?= (int)$u['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($u['avatar'])): ?>
                                            <img src="<?= htmlspecialchars($u['avatar']) ?>" alt="Avatar" class="rounded-circle border" width="38" height="38" style="object-fit: cover;">
                                        <?php else: ?>
                                            <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle border fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                <?= mb_strtoupper(mb_substr($u['name'] ?? 'U', 0, 1)) ?>
                                            </span>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                <?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?>
                                                <?php if ($isSelf): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary small ms-1">Bạn</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($u['living_area'])): ?>
                                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($u['living_area']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark small"><i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($u['email']) ?></div>
                                    <?php if (!empty($u['phone'])): ?>
                                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($u['phone']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $roleBadge ?></td>
                                <td class="small text-muted">
                                    <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td><?= $statusBadge ?></td>
                                <td class="text-end">
                                    <?php if ($isSelf): ?>
                                        <span class="text-muted small fst-italic">Tài khoản hiện tại</span>
                                    <?php else: ?>
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <?php if ($u['status'] === 'active'): ?>
                                                <!-- Form Khóa tài khoản -->
                                                <form method="POST" action="/admin/users/<?= (int)$u['id'] ?>/status" onsubmit="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản của <?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?>? Người dùng này sẽ bị đăng xuất ngay lập tức.');" class="d-inline">
                                                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                    <input type="hidden" name="status" value="locked">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Khóa tài khoản này">
                                                        <i class="bi bi-lock-fill me-1"></i> Khóa
                                                    </button>
                                                </form>
                                            <?php elseif ($u['status'] === 'locked'): ?>
                                                <!-- Form Mở khóa tài khoản -->
                                                <form method="POST" action="/admin/users/<?= (int)$u['id'] ?>/status" onsubmit="return confirm('MỞ KHÓA tài khoản cho <?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?> để người dùng có thể đăng nhập bình thường?');" class="d-inline">
                                                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Mở khóa tài khoản">
                                                        <i class="bi bi-unlock-fill me-1"></i> Mở khóa
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <!-- Form Kích hoạt tài khoản inactive -->
                                                <form method="POST" action="/admin/users/<?= (int)$u['id'] ?>/status" class="d-inline">
                                                    <input type="hidden" name="_csrf_token" value="<?= SecurityHelper::csrfToken() ?>">
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Kích hoạt tài khoản">
                                                        <i class="bi bi-check-lg me-1"></i> Kích hoạt
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (($filters['totalPages'] ?? 1) > 1): ?>
                <?php
                    $currentPage = (int)($filters['page'] ?? 1);
                    $totalPages = (int)$filters['totalPages'];
                    $queryParams = $_GET;
                ?>
                <nav class="mt-4 d-flex justify-content-center">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <?php $queryParams['page'] = $currentPage - 1; ?>
                            <a class="page-link" href="/admin/users?<?= http_build_query($queryParams) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php $queryParams['page'] = $p; ?>
                            <li class="page-item <?= ($p === $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="/admin/users?<?= http_build_query($queryParams) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <?php $queryParams['page'] = $currentPage + 1; ?>
                            <a class="page-link" href="/admin/users?<?= http_build_query($queryParams) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
