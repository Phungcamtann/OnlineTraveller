<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Lỗi Máy Chủ Nội Bộ | OnlineTraveller</title>
    
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
<body class="theme-dark-alpine" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at 50% 30%, rgba(239, 68, 68, 0.12), transparent 70%), #070d17; color: #f1f5f9; font-family: 'Roboto', sans-serif;">
    <div class="container py-5 text-center" style="max-width: 600px;">
        <div class="card p-4 p-md-5" style="background: rgba(13, 26, 45, 0.78); border: 1px solid rgba(255, 255, 255, 0.12); backdrop-filter: blur(20px); border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);">
            <!-- Icon 500 -->
            <div class="mb-4 d-inline-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; border-radius: 50%; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; font-size: 2.5rem;">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            
            <!-- Badge 500 -->
            <div class="mb-3">
                <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.35); font-size: 0.82rem; font-weight: 600; padding: 6px 14px; border-radius: 50px; letter-spacing: 0.08em;">
                    HTTP 500 SERVER ERROR
                </span>
            </div>

            <h1 class="h3 fw-bold text-white mb-3">Đã Xảy Ra Sự Cố Hệ Thống</h1>
            <p class="text-secondary mb-4" style="line-height: 1.6; font-size: 0.95rem;">
                Hệ thống đang gặp sự cố gián đoạn tạm thời khi xử lý yêu cầu của bạn. Đội ngũ kỹ thuật đã được thông báo để khắc phục sớm nhất. Vui lòng thử lại sau ít phút.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button type="button" onclick="history.back()" class="btn" style="background: rgba(255, 255, 255, 0.08); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 50px; font-weight: 500; padding: 10px 22px;">
                    <i class="bi bi-arrow-left me-1"></i> Quay Lại
                </button>
                <a href="/" class="btn" style="background: linear-gradient(135deg, #0284c7, #06b6d4); color: white; border: none; border-radius: 50px; font-weight: 600; padding: 10px 24px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                    <i class="bi bi-house-door me-1"></i> Về Trang Chủ
                </a>
            </div>
        </div>

        <div class="mt-4 text-secondary small">
            &copy; <?= date('Y') ?> OnlineTraveller &bull; Hệ Thống Giám Sát Tự Động
        </div>
    </div>
</body>
</html>
