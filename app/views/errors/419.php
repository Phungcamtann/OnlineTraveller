<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Phiên Làm Việc Hết Hạn | OnlineTraveller</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Unified Dark-Alpine Custom Theme CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/custom-theme.css">
</head>
<body class="theme-dark-alpine" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at 50% 30%, rgba(245, 158, 11, 0.12), transparent 70%), #070d17; color: #f1f5f9; font-family: 'Roboto', sans-serif;">
    <div class="container py-5 text-center" style="max-width: 600px;">
        <div class="card p-4 p-md-5" style="background: rgba(13, 26, 45, 0.78); border: 1px solid rgba(255, 255, 255, 0.12); backdrop-filter: blur(20px); border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);">
            <!-- Icon Phiên hết hạn -->
            <div class="mb-4 d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; border-radius: 50%; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; font-size: 2.5rem;">
                <i class="bi bi-clock-history"></i>
            </div>
            
            <!-- Badge 419 -->
            <div class="mb-3">
                <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.35); font-size: 0.82rem; font-weight: 600; padding: 6px 14px; border-radius: 50px; letter-spacing: 0.08em;">
                    HTTP 419 PAGE EXPIRED
                </span>
            </div>

            <h1 class="h3 fw-bold text-white mb-3">Phiên Bảo Mật Đã Hết Hạn</h1>
            <p class="text-secondary mb-4" style="line-height: 1.6; font-size: 0.95rem;">
                Mã xác thực bảo mật (CSRF Token) đã hết hiệu lực do phiên làm việc kéo dài quá lâu hoặc trang web chưa được làm mới. Vui lòng tải lại trang để cấp mã mới và tiếp tục thao tác.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button type="button" onclick="location.reload()" class="btn" style="background: linear-gradient(135deg, #0284c7, #06b6d4); color: white; border: none; border-radius: 50px; font-weight: 600; padding: 10px 24px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                    <i class="bi bi-arrow-clockwise me-1"></i> Tải Lại Trang
                </button>
                <a href="/" class="btn" style="background: rgba(255, 255, 255, 0.08); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 50px; font-weight: 500; padding: 10px 22px;">
                    <i class="bi bi-house-door me-1"></i> Về Trang Chủ
                </a>
                <a href="/login" class="btn" style="background: rgba(14, 165, 233, 0.12); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.25); border-radius: 50px; font-weight: 500; padding: 10px 22px;">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập Lại
                </a>
            </div>
        </div>

        <div class="mt-4 text-secondary small">
            &copy; <?= date('Y') ?> OnlineTraveller &bull; Bảo Mật Biểu Mẫu Chuẩn CSRF
        </div>
    </div>
</body>
</html>
