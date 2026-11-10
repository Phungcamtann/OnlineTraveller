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
    
    <!-- Google Fonts: Roboto (Unified Single Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap" rel="stylesheet">
    
    <!-- Legacy App CSS (Backwards Compatibility) -->
    <link rel="stylesheet" href="/assets/css/app.css">
    <!-- Unified Dark-Alpine & Ambient Blur Custom Theme CSS (Docs UI Spec Section 2) -->
    <link rel="stylesheet" href="/assets/css/custom-theme.css">
</head>
<body class="theme-dark-alpine">

    <!-- 1. TOP FLOATING NAVBAR (KÍNH MỜ, ĐÈ LÊN BANNER, KHÔNG ICON Ở MENU GIỮA) -->
    <header class="top-floating-header">
        <div class="container-xxl">
            <div class="row align-items-center justify-content-between g-2">
                <!-- Cột 1: Logo thương hiệu núi & sóng bản địa -->
                <div class="col-auto">
                    <a href="/" class="d-flex align-items-center gap-2 text-decoration-none text-white fw-bold fs-5">
                        <img src="/assets/images/logo.svg" 
                             alt="OnlineTraveller" 
                             width="36" 
                             height="36" 
                             class="rounded-circle"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                        <span class="navbar-brand-logo" style="display: none;">
                            <i class="bi bi-compass"></i>
                        </span>
                        <span class="font-editorial text-white tracking-wide">OnlineTraveller</span>
                    </a>
                </div>

                <!-- Cột 2: Menu trung tâm dạng viên thuốc (CHỈ TEXT THUẦN, KHÔNG ICON - MỤC 3.1) -->
                <div class="col-auto d-none d-lg-block">
                    <nav class="navbar-center-pill">
                        <?php 
                            $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
                            $currentUser = $_SESSION['user'] ?? null;
                            $userRole = $currentUser['role'] ?? '';
                        ?>
                        <a href="/" class="nav-text-link <?= $currentUri === '/' ? 'active text-white' : '' ?>">Trang chủ</a>
                        <a href="/services" class="nav-text-link <?= str_starts_with($currentUri, '/services') ? 'active text-white' : '' ?>">Khám Phá</a>
                        <a href="/posts" class="nav-text-link <?= str_starts_with($currentUri, '/posts') ? 'active text-white' : '' ?>">Bản tin</a>
                        
                        <?php // Phân quyền hiển thị theo role: Host -> Quản lý; Admin -> Quản trị (Mục 3.1) ?>
                        <?php if ($userRole === 'host'): ?>
                            <a href="/host" class="nav-text-link text-info fw-semibold">Quản lý</a>
                        <?php elseif ($userRole === 'admin'): ?>
                            <a href="/admin" class="nav-text-link text-warning fw-semibold">Quản trị</a>
                        <?php endif; ?>
                    </nav>
                </div>

                <!-- Cột 3: Cụm User góc phải tối giản (1 khối pill duy nhất - Mục 3.1) -->
                <div class="col-auto d-flex align-items-center gap-2">
                    <?php if (!empty($currentUser)): ?>
                        <div class="dropdown">
                            <button class="navbar-user-pill border-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle" style="width: 26px; height: 26px;">
                                    <i class="bi bi-person-fill" style="font-size: 0.85rem;"></i>
                                </span>
                                <span class="small fw-semibold text-white">
                                    <?= htmlspecialchars($currentUser['name'] ?? 'Tài khoản', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border border-secondary border-opacity-25 rounded-3 py-2 mt-2" style="background: rgba(15, 25, 36, 0.95); backdrop-filter: blur(12px);">
                                <li class="px-3 py-1">
                                    <small class="text-secondary d-block" style="font-size: 0.72rem;">ĐĂNG NHẬP VỚI VAI TRÒ</small>
                                    <span class="badge bg-primary-subtle text-primary text-uppercase" style="font-size: 0.75rem;">
                                        <?= htmlspecialchars($userRole ?: 'tourist') ?>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                                <li>
                                    <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="/profile">
                                        <i class="bi bi-person text-info"></i> Hồ sơ của tôi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="/bookings">
                                        <i class="bi bi-calendar-check text-success"></i> Chuyến đi của tôi
                                    </a>
                                </li>
                                <?php if ($userRole === 'host'): ?>
                                    <li>
                                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="/host">
                                            <i class="bi bi-kanban text-info"></i> Bàn làm việc Host
                                        </a>
                                    </li>
                                <?php elseif ($userRole === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="/admin">
                                            <i class="bi bi-speedometer2 text-warning"></i> Trung tâm Admin
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="/admin/services">
                                            <i class="bi bi-shield-check text-warning"></i> Quản lý & Duyệt Tour
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                                <li>
                                    <a class="dropdown-item small py-2 d-flex align-items-center gap-2 text-danger" href="/logout">
                                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="navbar-user-pill">
                            <span class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 text-info rounded-circle" style="width: 26px; height: 26px;">
                                <i class="bi bi-person" style="font-size: 0.9rem;"></i>
                            </span>
                            <span class="small fw-semibold text-white">Đăng nhập</span>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggler (Chỉ hiển thị trên màn hình nhỏ < lg) -->
                    <button class="btn btn-sm glass-pill p-1 d-lg-none text-white border-0" 
                            type="button" 
                            data-bs-toggle="offcanvas" 
                            data-bs-target="#mobileOffcanvasNav" 
                            aria-controls="mobileOffcanvasNav"
                            aria-label="Toggle navigation">
                        <i class="bi bi-list fs-4 px-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- OFFCANVAS CHO THIẾT BỊ DI ĐỘNG -->
    <div class="offcanvas offcanvas-end text-bg-dark border-start border-secondary border-opacity-25" tabindex="-1" id="mobileOffcanvasNav" aria-labelledby="mobileNavLabel" style="background: rgba(11, 25, 36, 0.96) !important; backdrop-filter: blur(16px);">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2">
                <img src="/assets/images/logo.svg" alt="OnlineTraveller" width="30" height="30" class="rounded-circle">
                <h5 class="offcanvas-title font-editorial text-white fw-bold" id="mobileNavLabel">OnlineTraveller</h5>
            </div>
            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="d-flex flex-column gap-2 mb-4">
                <a href="/" class="btn glass-pill text-start px-3 py-2 text-white text-decoration-none">
                    <i class="bi bi-house me-2 text-info"></i> Trang chủ
                </a>
                <a href="/services" class="btn glass-pill text-start px-3 py-2 text-white text-decoration-none">
                    <i class="bi bi-compass me-2 text-primary"></i> Khám Phá
                </a>
                <a href="/posts" class="btn glass-pill text-start px-3 py-2 text-white text-decoration-none">
                    <i class="bi bi-newspaper me-2 text-success"></i> Bản tin & Cẩm nang
                </a>
                <?php if ($userRole === 'host'): ?>
                    <a href="/host" class="btn glass-pill text-start px-3 py-2 text-info text-decoration-none fw-semibold">
                        <i class="bi bi-kanban me-2"></i> Quản lý Host
                    </a>
                <?php elseif ($userRole === 'admin'): ?>
                    <a href="/admin" class="btn glass-pill text-start px-3 py-2 text-warning text-decoration-none fw-semibold">
                        <i class="bi bi-shield-lock me-2"></i> Quản trị Admin
                    </a>
                    <a href="/admin/services" class="btn glass-pill text-start px-3 py-2 text-warning text-decoration-none fw-semibold">
                        <i class="bi bi-shield-check me-2"></i> Duyệt Tour Mới
                    </a>
                <?php endif; ?>
            </nav>
            <div class="pt-3 border-top border-secondary border-opacity-25 small text-secondary">
                OnlineTraveller v2.2 • Dân Bản Địa & Khách Du Lịch
            </div>
        </div>
    </div>

    <!-- 2. MAIN BODY CONTENT CONTAINER -->
    <?php
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
        $isFullBleedHero = ($currentUri === '/' || preg_match('#^/services/\d+#', $currentUri));
    ?>
    <main class="<?= $isFullBleedHero ? '' : 'content-has-top-padding' ?>">
        <?= $content ?>
    </main>

    <!-- 3. FOOTER TỐI GIẢN (THEO ĐÚNG MỤC 3.1 & ẢNH MOCKUP) -->
    <footer class="footer-minimal mt-5">
        <div class="container-xxl">
            <div class="row align-items-center justify-content-between text-center text-md-start g-3">
                <!-- Cột trái: Logo + OnlineTraveller -->
                <div class="col-md-auto d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                    <img src="/assets/images/logo.svg" 
                         alt="OnlineTraveller" 
                         width="28" 
                         height="28" 
                         class="rounded-circle"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <span class="navbar-brand-logo" style="width: 28px; height: 28px; display: none;">
                        <i class="bi bi-compass" style="font-size: 0.75rem;"></i>
                    </span>
                    <span class="fw-bold font-editorial text-white fs-6">OnlineTraveller</span>
                </div>

                <!-- Cột giữa: Links ngang + Dòng kiến trúc -->
                <div class="col-md-auto text-center">
                    <div class="d-flex align-items-center justify-content-center gap-3 small mb-1">
                        <a href="/" class="text-decoration-none">Trang chủ</a>
                        <span class="text-secondary opacity-50">•</span>
                        <a href="/services" class="text-decoration-none">Khám Phá</a>
                        <span class="text-secondary opacity-50">•</span>
                        <a href="/posts" class="text-decoration-none">Bản tin</a>
                    </div>
                    <small class="text-secondary opacity-75 d-block" style="font-size: 0.75rem;">
                        OOP + MVC, 15 bảng, 22 Stored Procedures
                    </small>
                </div>

                <!-- Cột phải: Bản quyền & Nút quay lại lên đầu trang (Mockup góc phải) -->
                <div class="col-md-auto text-center text-md-end d-flex align-items-center justify-content-center justify-content-md-end gap-3">
                    <small class="text-secondary">© 2026 OnlineTraveller</small>
                    <a href="#" 
                       class="btn btn-sm glass-pill py-1 px-3 text-light text-decoration-none small d-none d-md-inline-flex align-items-center gap-1" 
                       onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
                       title="Cuộn lên đầu trang">
                        Quay lại <i class="bi bi-stars text-info ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Reusable Notification Pop-up Modal -->
    <?php include dirname(__DIR__) . '/partials/notification-modal.php'; ?>
</body>
</html>
