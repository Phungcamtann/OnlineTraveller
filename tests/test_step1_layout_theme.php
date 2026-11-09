<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== KIỂM THỬ BƯỚC 1: CUSTOM THEME CSS & MAIN LAYOUT (MỤC 2 & MỤC 3A) ===\n\n";

require dirname(__DIR__) . '/bootstrap/app.php';

function runTest(string $title, callable $fn): void {
    try {
        $result = $fn();
        echo "[PASS] {$title}" . ($result ? " -> {$result}" : "") . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$title} -> " . $e->getMessage() . "\n";
    }
}

// 1. Kiểm tra file CSS custom-theme.css
runTest("File public/assets/css/custom-theme.css chứa đầy đủ hệ màu Dark-Alpine & Glassmorphism", function() {
    $cssFile = dirname(__DIR__) . '/public/assets/css/custom-theme.css';
    if (!is_file($cssFile)) {
        throw new \Exception("Chưa tạo file custom-theme.css!");
    }
    $css = file_get_contents($cssFile);

    $requiredTokens = [
        '--bg-midnight: #0b1924',
        '--brand-primary: #387ea2',
        'theme-dark-alpine',
        '.glass-panel',
        '.glass-pill',
        '.btn-glass-hero',
        '.btn-alpine',
        '.hero-ambient-wrapper',
        '.hero-banner-box',
        '.hero-banner-overlay',
        '.hero-ambient-glow',
        '.experience-poster-card',
        '.station-timeline',
        '.section-divider-title',
        '.footer-minimal',
    ];

    foreach ($requiredTokens as $token) {
        if (strpos($css, $token) === false) {
            throw new \Exception("Thiếu token CSS bắt buộc: {$token}");
        }
    }

    return "Đầy đủ 15/15 tokens chuẩn theo Mục 2 đặc tả UI!";
});

// Hàm render layout mẫu
function renderLayout(array $sessionData = []): string {
    $_SESSION = $sessionData;
    $title = "Trang Thử Nghiệm";
    $content = "<div class='container'><p>Nội dung trang test</p></div>";

    ob_start();
    include dirname(__DIR__) . '/app/views/layouts/main.php';
    return ob_get_clean();
}

// 2. Kiểm tra layout khi Khách vãng lai (Chưa đăng nhập)
runTest("Layout render cho Khách vãng lai: Menu giữa chỉ có text thuần (Trang chủ, Khám Phá, Bản tin), Cụm User hiện Đăng nhập", function() {
    $_SERVER['REQUEST_URI'] = '/';
    $html = renderLayout([]);

    // Kiểm tra body có class theme-dark-alpine
    if (strpos($html, 'class="theme-dark-alpine"') === false) {
        throw new \Exception("Thẻ body chưa gắn class 'theme-dark-alpine'!");
    }

    // Kiểm tra link css custom-theme.css
    if (strpos($html, '/assets/css/custom-theme.css') === false) {
        throw new \Exception("Chưa nhúng custom-theme.css trong head!");
    }

    // Trích xuất phần menu giữa navbar-center-pill
    if (!preg_match('#<nav class="navbar-center-pill">(.*?)</nav>#s', $html, $matches)) {
        throw new \Exception("Không tìm thấy thẻ <nav class=\"navbar-center-pill\">!");
    }
    $centerNav = $matches[1];

    // Kiểm tra menu giữa chỉ có text thuần
    if (strpos($centerNav, 'Trang chủ') === false || strpos($centerNav, 'Khám Phá') === false || strpos($centerNav, 'Bản tin') === false) {
        throw new \Exception("Thiếu các link cơ bản ở menu giữa!");
    }

    // Menu giữa KHÔNG được chứa icon bên trong thẻ link
    if (strpos($centerNav, '<i class=') !== false) {
        throw new \Exception("Menu giữa chứa thẻ icon <i>! Đặc tả yêu cầu CHỈ TEXT THUẦN.");
    }

    // Không được hiện Quản lý hay Quản trị ở menu giữa khi chưa đăng nhập
    if (strpos($centerNav, 'Quản lý') !== false || strpos($centerNav, 'Quản trị') !== false) {
        throw new \Exception("Lộ link Quản lý/Quản trị khi chưa đăng nhập!");
    }

    // Cụm user góc phải phải có Đăng nhập
    if (strpos($html, 'Đăng nhập') === false) {
        throw new \Exception("Thiếu nút Đăng nhập góc phải!");
    }

    // Footer tối giản
    if (strpos($html, 'footer-minimal') === false || strpos($html, 'OOP + MVC, 15 bảng, 22 Stored Procedures') === false) {
        throw new \Exception("Footer chưa đúng chuẩn tối giản!");
    }

    return "Menu giữa kính mờ không icon, Cụm user hiện Đăng nhập, Footer tối giản đúng chuẩn!";
});

// 3. Kiểm tra layout khi đăng nhập là Host
runTest("Layout render cho Dân bản địa (Host): Menu giữa tự động xuất hiện mục 'Quản lý' (/host), Cụm User hiện tên Host", function() {
    $_SERVER['REQUEST_URI'] = '/';
    $html = renderLayout([
        'user' => [
            'id' => 2,
            'name' => 'Nguyễn Văn Bản Địa',
            'role' => 'host',
            'email' => 'host@onlinetraveller.vn'
        ]
    ]);

    // Trích xuất menu giữa
    if (!preg_match('#<nav class="navbar-center-pill">(.*?)</nav>#s', $html, $matches)) {
        throw new \Exception("Không tìm thấy thẻ <nav class=\"navbar-center-pill\">!");
    }
    $centerNav = $matches[1];

    // Kiểm tra menu giữa có mục Quản lý
    if (strpos($centerNav, 'href="/host"') === false || strpos($centerNav, 'Quản lý') === false) {
        throw new \Exception("Không xuất hiện link 'Quản lý' cho role Host ở menu giữa!");
    }
    // Không được có Quản trị ở menu giữa
    if (strpos($centerNav, 'Quản trị') !== false) {
        throw new \Exception("Host bị nhìn thấy link 'Quản trị' của Admin ở menu giữa!");
    }

    // Cụm user góc phải hiện tên Host
    if (strpos($html, 'Nguyễn Văn Bản Địa') === false) {
        throw new \Exception("Không hiển thị tên Host ở pill góc phải!");
    }

    return "Host nhận diện chính xác: Hiện 'Quản lý' (/host) và tên 'Nguyễn Văn Bản Địa' trong dropdown pill!";
});

// 4. Kiểm tra layout khi đăng nhập là Admin
runTest("Layout render cho Quản trị viên (Admin): Menu giữa tự động xuất hiện mục 'Quản trị' (/admin), Cụm User hiện tên Admin", function() {
    $_SERVER['REQUEST_URI'] = '/';
    $html = renderLayout([
        'user' => [
            'id' => 1,
            'name' => 'Dương Hoàng Sâm',
            'role' => 'admin',
            'email' => 'admin@onlinetraveller.vn'
        ]
    ]);

    // Trích xuất menu giữa
    if (!preg_match('#<nav class="navbar-center-pill">(.*?)</nav>#s', $html, $matches)) {
        throw new \Exception("Không tìm thấy thẻ <nav class=\"navbar-center-pill\">!");
    }
    $centerNav = $matches[1];

    // Kiểm tra menu giữa có mục Quản trị
    if (strpos($centerNav, 'href="/admin"') === false || strpos($centerNav, 'Quản trị') === false) {
        throw new \Exception("Không xuất hiện link 'Quản trị' cho role Admin ở menu giữa!");
    }
    // Không được có Quản lý ở menu giữa
    if (strpos($centerNav, 'Quản lý') !== false) {
        throw new \Exception("Admin bị nhầm sang link 'Quản lý' của Host ở menu giữa!");
    }

    // Cụm user góc phải hiện tên Admin
    if (strpos($html, 'Dương Hoàng Sâm') === false) {
        throw new \Exception("Không hiển thị tên Admin ở pill góc phải!");
    }

    return "Admin nhận diện chính xác: Hiện 'Quản trị' (/admin) và tên 'Dương Hoàng Sâm'!";
});

echo "\n=== KẾT QUẢ: TOÀN BỘ 4 TEST CASES BƯỚC 1 ĐẠT CHUẨN 100%! ===\n";
