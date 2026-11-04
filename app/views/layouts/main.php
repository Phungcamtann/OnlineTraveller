<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'OnlineTraveller — Du Lịch & Trải Nghiệm Cùng Dân Bản Địa', ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom Editorial UI Styles -->
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

    <!-- TOP FLOATING NAVBAR -->
    <div class="container sticky-top">
        <nav class="navbar navbar-expand-lg navbar-floating">
            <div class="container-fluid px-0">
                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="/">
                    <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle" style="width: 38px; height: 38px;">
                        <i class="bi bi-compass fs-5 text-warning"></i>
                    </span>
                    <div>
                        <span class="fw-bold font-editorial fs-5 text-dark d-block lh-1">OnlineTraveller</span>
                        <small class="text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.04em;">DÂN BẢN ĐỊA & DU KHÁCH</small>
                    </div>
                </a>

                <!-- Hamburger Toggler (Offcanvas) -->
                <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                    <i class="bi bi-list fs-3"></i>
                </button>

                <!-- Navigation Content -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header border-bottom">
                        <h5 class="offcanvas-title font-editorial fw-bold" id="offcanvasNavbarLabel">OnlineTraveller</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body align-items-center justify-content-between">
                        <!-- Center Links -->
                        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-1">
                            <li class="nav-item">
                                <a class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/' ? 'active' : '' ?>" href="/">
                                    <i class="bi bi-house me-1"></i> Trang chủ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/services') ? 'active' : '' ?>" href="/services">
                                    <i class="bi bi-geo-alt me-1"></i> Khám phá bản địa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/services/1#virtual-tour">
                                    <i class="bi bi-badge-3d me-1 text-primary"></i> Trải nghiệm 3D
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/posts') ? 'active' : '' ?>" href="/posts">
                                    <i class="bi bi-newspaper me-1"></i> Bản tin & Cẩm nang
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-success fw-semibold" href="/host">
                                    <i class="bi bi-person-badge me-1"></i> Trở thành Host
                                </a>
                            </li>
                        </ul>

                        <!-- Right Auth Controls -->
                        <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                            <?php $currentUser = $_SESSION['user'] ?? null; ?>
                            <?php if ($currentUser): ?>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm rounded-pill dropdown-toggle px-3 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-circle me-1"></i>
                                        <?= htmlspecialchars($currentUser['name'] ?? 'Tài khoản', ENT_QUOTES, 'UTF-8') ?>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                        <li><h6 class="dropdown-header">Vai trò: <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($currentUser['role'] ?? 'tourist') ?></span></h6></li>
                                        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                            <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i>Quản trị Admin</a></li>
                                        <?php elseif (($currentUser['role'] ?? '') === 'host'): ?>
                                            <li><a class="dropdown-item" href="/host"><i class="bi bi-kanban me-2"></i>Quản lý Host</a></li>
                                        <?php endif; ?>
                                        <li><a class="dropdown-item" href="/bookings"><i class="bi bi-calendar-check me-2"></i>Lịch hẹn của tôi</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="/login" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1">Đăng nhập</a>
                                <a href="/register" class="btn btn-sm btn-editorial px-3 py-1">Đăng ký</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- FLASH MESSAGES -->
    <div class="container mt-3">
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $messages): ?>
                <?php
                    $alertClass = match($type) {
                        'success' => 'alert-success',
                        'warning' => 'alert-warning',
                        'danger', 'error' => 'alert-danger',
                        default => 'alert-info',
                    };
                ?>
                <?php foreach ((array)$messages as $msg): ?>
                    <div class="alert <?= $alertClass ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
    </div>

    <!-- MAIN BODY CONTENT -->
    <main class="py-4">
        <?= $content ?>
    </main>

    <!-- FOOTER -->
    <footer class="footer-editorial mt-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-circle" style="width: 32px; height: 32px;">
                            <i class="bi bi-compass text-warning"></i>
                        </span>
                        <span class="fw-bold font-editorial fs-5 text-dark">OnlineTraveller</span>
                    </div>
                    <p class="small text-muted mb-3">
                        Nền tảng tiên phong kết nối du khách từ xa và người dân bản địa. Khám phá chiều sâu văn hóa qua không gian trình chiếu 3D Three.js và đặt lịch trải nghiệm thực tế cùng Homestay nhà dân.
                    </p>
                    <div class="d-flex gap-2 text-muted">
                        <span class="badge badge-soft-success"><i class="bi bi-shield-check me-1"></i> MoMo Sandbox</span>
                        <span class="badge badge-soft-primary"><i class="bi bi-geo-alt me-1"></i> Google Maps</span>
                        <span class="badge badge-soft-warning"><i class="bi bi-cloud-sun me-1"></i> OpenWeather</span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold text-dark mb-3">Khám Phá</h6>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                        <li><a href="/" class="text-muted text-decoration-none">Trang chủ</a></li>
                        <li><a href="/services" class="text-muted text-decoration-none">Trải nghiệm bản địa</a></li>
                        <li><a href="/services/1" class="text-muted text-decoration-none">Trình chiếu 3D</a></li>
                        <li><a href="/posts" class="text-muted text-decoration-none">Cẩm nang du lịch</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold text-dark mb-3">Dành Cho Host</h6>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                        <li><a href="/host" class="text-muted text-decoration-none">Đăng ký người bản địa</a></li>
                        <li><a href="/host" class="text-muted text-decoration-none">Quản lý trạm 3D</a></li>
                        <li><a href="/host" class="text-muted text-decoration-none">Thiết lập Homestay</a></li>
                        <li><a href="/host" class="text-muted text-decoration-none">Lịch đón tiếp</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-12">
                    <h6 class="fw-bold text-dark mb-3">Đồ Án Web Chuyên Sâu v2.2</h6>
                    <p class="small text-muted mb-2">
                        Kiến trúc OOP + MVC thuần PHP 8 • Bootstrap 5.3 Editorial UI • 15 Bảng chuẩn hóa • 22 Stored Procedures • 4 Webservices tích hợp (Google Maps, OpenWeather, RSS XML, MoMo).
                    </p>
                    <small class="text-muted opacity-75">Thực hiện: Nhóm 4 thành viên (TV1 – TV4) • Tháng 11/2026</small>
                </div>
            </div>
            <div class="pt-3 border-top text-center text-muted small">
                © 2026 OnlineTraveller. Nền tảng kết nối du lịch từ xa và dân bản địa. Mọi quyền được bảo lưu.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
