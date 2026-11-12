<?php
$title = $title ?? '500 — Lỗi Máy Chủ Nội Bộ | OnlineTraveller';
$errorMessage = $errorMessage ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo.svg">
    <link rel="alternate icon" type="image/x-icon" href="/favicon.ico">

    <!-- Theme Early Initialization Script -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('ot_theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Bootstrap 5.3.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/custom-theme.css">

    <style>
        body.error-page-body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: var(--bg-surface-subtle);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .error-stage-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 28px;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        [data-theme="dark"] .error-stage-card {
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.45);
        }
    </style>
</head>
<body class="error-page-body">

    <!-- Top floating bar with Logo and Day/Night Switch -->
    <header class="py-3 px-4 d-flex align-items-center justify-content-between">
        <a href="/" class="text-decoration-none d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 36px; height: 36px;">
                <i class="bi bi-compass-fill"></i>
            </span>
            <span class="fw-bold font-editorial fs-5" style="color: var(--text-main);">OnlineTraveller</span>
        </a>

        <button type="button" id="themeToggleSwitch" class="theme-mode-switch" aria-label="Chuyển chế độ Sáng/Tối" title="Chuyển chế độ Sáng / Tối">
            <span class="switch-label" id="themeSwitchText">DAY MODE</span>
            <span class="switch-knob" id="themeSwitchIcon">
                <i class="bi bi-sun"></i>
            </span>
        </button>
    </header>

    <!-- Center Content Card -->
    <main class="container py-4 my-auto" style="max-width: 620px;">
        <div class="error-stage-card p-4 p-md-5 text-center">
            <!-- Icon 500 -->
            <div class="mb-4 d-inline-flex align-items-center justify-content-center mx-auto" style="width: 86px; height: 86px; border-radius: 50%; background: rgba(239, 68, 68, 0.12); border: 2px solid rgba(239, 68, 68, 0.35); color: #ef4444; font-size: 2.75rem; box-shadow: 0 0 28px rgba(239, 68, 68, 0.25);">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            
            <!-- Badge 500 -->
            <div class="mb-3">
                <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.85rem; font-weight: 700; padding: 6px 16px; border-radius: 999px; letter-spacing: 0.08em;">
                    HTTP 500 SERVER ERROR
                </span>
            </div>

            <h1 class="h3 fw-bold font-editorial mb-3" style="color: var(--text-main);">
                Đã Xảy Ra Sự Cố Hệ Thống
            </h1>
            <p class="mb-4" style="color: var(--text-muted); line-height: 1.65; font-size: 0.95rem;">
                Hệ thống đang gặp sự cố gián đoạn tạm thời khi xử lý yêu cầu của bạn. Đội ngũ kỹ thuật đã được thông báo để khắc phục sớm nhất. Vui lòng tải lại trang hoặc quay lại sau ít phút.
            </p>

            <?php if (!empty($errorMessage) && (getenv('APP_ENV') === 'development' || true)): ?>
                <div class="text-start p-3 rounded-3 mb-4 small" style="background: rgba(239, 68, 68, 0.08); border: 1px dashed rgba(239, 68, 68, 0.3); color: #dc2626; font-family: monospace; word-break: break-all;">
                    <i class="bi bi-info-circle me-1"></i> Chi tiết sự cố: <?= htmlspecialchars((string)$errorMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button type="button" onclick="location.reload()" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-clockwise me-1"></i> Tải Lại Trang
                </button>
                <a href="/" class="btn btn-theme-primary rounded-pill px-4 py-2 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;">
                    <i class="bi bi-house-door me-1"></i> Về Trang Chủ
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 small" style="color: var(--text-muted);">
        &copy; <?= date('Y') ?> OnlineTraveller &bull; Hotline Hỗ Trợ 24/7: 1900 6868
    </footer>

    <!-- Script Theme Toggle -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const htmlEl = document.documentElement;
        const toggleBtn = document.getElementById('themeToggleSwitch');
        const labelEl = document.getElementById('themeSwitchText');
        const iconEl = document.getElementById('themeSwitchIcon');

        function syncSwitchUI(theme) {
            if (theme === 'dark') {
                if (labelEl) labelEl.textContent = 'NIGHT MODE';
                if (iconEl) iconEl.innerHTML = '<i class="bi bi-moon-stars"></i>';
            } else {
                if (labelEl) labelEl.textContent = 'DAY MODE';
                if (iconEl) iconEl.innerHTML = '<i class="bi bi-sun"></i>';
            }
        }

        syncSwitchUI(htmlEl.getAttribute('data-theme') || 'light');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const currentTheme = htmlEl.getAttribute('data-theme') || 'light';
                const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                htmlEl.setAttribute('data-theme', nextTheme);
                htmlEl.setAttribute('data-bs-theme', nextTheme);
                localStorage.setItem('ot_theme', nextTheme);
                syncSwitchUI(nextTheme);
            });
        }
    });
    </script>
</body>
</html>
