<?php
/**
 * DEDICATED FULLSCREEN 3D VIRTUAL TOUR PAGE (THREE.JS CONTINUOUS Z-SCROLL & SPLIT-STAGE)
 * Theo đặc tả 4 Frame kịch bản trong docs/UPGRADE_3D_SCROLL_CANVA_SPEC.md
 * OnlineTraveller v4.0
 */
$service = $service ?? [];
$stops = $stops ?? [];
$user = $user ?? null;
?>
<!DOCTYPE html>
<html lang="vi" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Trình Chiếu Không Gian 3D | OnlineTraveller', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/custom-theme.css">

    <!-- Three.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <style>
        :root {
            --vt-card-bg: rgba(15, 23, 42, 0.82);
            --vt-card-border: rgba(255, 255, 255, 0.22);
            --vt-pill-bg: #cbd5e1;
            --vt-axis-color: rgba(56, 189, 248, 0.65);
            --vt-text-main: #f8fafc;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #070e17;
            font-family: var(--font-body, system-ui, -apple-system, sans-serif);
            color: #f1f5f9;
            user-select: none;
        }

        /* Canvas 3D Viewport chiếm trọn toàn màn hình */
        #threejs-viewport {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            cursor: grab;
        }
        #threejs-viewport:active {
            cursor: grabbing;
        }

        /* Top Bar điều hướng */
        .tour-top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            padding: 16px 28px;
            background: linear-gradient(to bottom, rgba(7, 14, 23, 0.94) 0%, rgba(7, 14, 23, 0.5) 75%, transparent 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            pointer-events: none;
        }
        .tour-top-bar > * {
            pointer-events: auto;
        }

        .btn-nav-glass {
            background: rgba(15, 29, 48, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #ffffff;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 9999px;
            transition: all 0.22s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-nav-glass:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(255, 255, 255, 0.5);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .hint-pill {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.65);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 9999px;
            padding: 4px 14px;
        }

        /* ==========================================================================
           BỐ CỤC SPLIT-STAGE: CỘT TRÁI (TRỤC DỌC NÉT ĐỨT + GLASS STORY CARD)
           ========================================================================== */
        .vt-left-story-column {
            position: fixed;
            top: 76px;
            bottom: 28px;
            left: clamp(20px, 3.5vw, 56px);
            width: clamp(300px, 28vw, 390px);
            z-index: 45;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            pointer-events: none;
        }

        /* Vùng viewport cắt dọc cho Card trượt từ dưới lên */
        .vt-story-track-viewport {
            position: relative;
            flex: 1;
            width: 100%;
            overflow: visible;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Đường trục dọc nét đứt nối xuyên tâm cột trái */
        .vt-dashed-axis-line {
            position: absolute;
            left: 50%;
            top: -12px;
            bottom: -8px;
            width: 3px;
            transform: translateX(-50%);
            background-image: linear-gradient(
                to bottom,
                var(--vt-axis-color, rgba(56, 189, 248, 0.6)) 55%,
                transparent 45%
            );
            background-position: center;
            background-size: 3px 18px;
            background-repeat: repeat-y;
            z-index: 1;
            transition: opacity 0.3s ease;
        }

        /* Mũi tên tam giác ở đáy trục nét đứt chỉ xuống cụm nút */
        .vt-dashed-axis-arrow {
            position: absolute;
            left: 50%;
            bottom: -18px;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 12px solid var(--vt-axis-color, rgba(56, 189, 248, 0.85));
            z-index: 2;
        }

        /* Thẻ Glass Story Card theo đúng mẫu Canva (có Pill "Trạm X" gắn trên đỉnh) */
        .vt-glass-story-card {
            position: relative;
            z-index: 5;
            width: 100%;
            padding: 34px 26px 26px;
            border-radius: 24px;
            background: var(--vt-card-bg, rgba(15, 23, 42, 0.82));
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--vt-card-border, rgba(255, 255, 255, 0.22));
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.45);
            color: var(--vt-text-main, #f8fafc);
            pointer-events: auto;
            will-change: transform, opacity;
            transition: transform 0.15s ease-out, opacity 0.15s ease-out;
        }

        /* Viên thuốc "Trạm X" nằm đè lên mép trên chính giữa của Card */
        .vt-station-top-pill {
            position: absolute;
            top: -17px;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 30px;
            border-radius: 999px;
            background: var(--vt-pill-bg, rgba(203, 213, 225, 0.95));
            color: #0f172a;
            font-weight: 800;
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.28);
            white-space: nowrap;
            text-transform: uppercase;
        }

        /* Card phụ trồi từ dưới lên khi cuộn dở giữa 2 trạm (Frame 2 & 3) */
        .vt-glass-preview-card {
            pointer-events: none;
            opacity: 0;
            transform: translateY(160%);
        }

        /* Con trỏ nhấp nháy cho hiệu ứng Typing chữ */
        .vt-typing-cursor {
            display: inline-block;
            width: 2.5px;
            height: 1.1em;
            background-color: #38bdf8;
            margin-left: 4px;
            vertical-align: text-bottom;
            animation: vtCursorBlink 0.75s infinite;
        }

        @keyframes vtCursorBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        /* Cụm nút [↩ Quay lại] + [Trạm tiếp →] đặt ngay dưới chân trục dọc */
        .vt-bottom-left-controls {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding-top: 24px;
            pointer-events: auto;
        }

        .vt-btn-undo-prev {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--vt-card-bg, rgba(15, 23, 42, 0.82));
            backdrop-filter: blur(16px);
            border: 1px solid var(--vt-card-border, rgba(255, 255, 255, 0.24));
            color: var(--vt-text-main, #f8fafc);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.2, 0.9, 0.3, 1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .vt-btn-undo-prev:hover:not(:disabled) {
            transform: translateY(-2px) rotate(-15deg);
            border-color: #38bdf8;
            color: #38bdf8;
            box-shadow: 0 10px 24px rgba(56, 189, 248, 0.4);
        }
        .vt-btn-undo-prev:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            transform: none !important;
        }

        .vt-btn-next-pill {
            min-width: 180px;
            height: 48px;
            padding: 0 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.95), rgba(2, 132, 199, 0.98));
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 10px 28px rgba(2, 132, 199, 0.45);
            transition: all 0.25s cubic-bezier(0.2, 0.9, 0.3, 1);
        }
        .vt-btn-next-pill:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #38bdf8, #0284c7);
            box-shadow: 0 14px 34px rgba(56, 189, 248, 0.6);
        }
        .vt-btn-next-pill:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Bottom Timeline Rail (Center-Bottom) */
        .tour-station-rail {
            position: fixed;
            bottom: 28px;
            right: clamp(24px, 4vw, 56px);
            z-index: 45;
            background: rgba(11, 23, 38, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 9999px;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
        }

        .tour-rail-btn {
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 9999px;
            transition: all 0.2s ease;
            white-space: nowrap;
            cursor: pointer;
        }
        .tour-rail-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.14);
        }
        .tour-rail-btn.active {
            color: #070e17;
            background: #38bdf8;
            font-weight: 700;
            box-shadow: 0 0 14px rgba(56, 189, 248, 0.6);
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .tour-station-rail {
                display: none !important;
            }
            .vt-left-story-column {
                left: 16px;
                right: 16px;
                width: auto;
                top: auto;
                bottom: 20px;
            }
            .vt-dashed-axis-line,
            .vt-dashed-axis-arrow {
                display: none !important;
            }
            .vt-glass-story-card {
                padding: 24px 20px 20px;
            }
            .vt-bottom-left-controls {
                padding-top: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <header class="tour-top-bar">
        <div class="d-flex align-items-center gap-3">
            <a href="/services/<?= (int)($service['id'] ?? 1) ?>" class="btn-nav-glass small">
                <i class="bi bi-arrow-left me-1"></i> Trở về Chi Tiết Tour
            </a>
            <div class="d-none d-md-block">
                <span class="badge bg-info bg-opacity-25 text-info px-3 py-1 rounded-pill me-2">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($service['location'] ?? 'Điểm Đến') ?>
                </span>
                <span class="fw-bold text-white font-editorial fs-6">
                    <?= htmlspecialchars($service['title'] ?? 'Trải Nghiệm Bản Địa') ?>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="hint-pill d-none d-lg-inline-flex align-items-center gap-2">
                <i class="bi bi-mouse"></i> Lăn chuột từng nấc
                <span class="opacity-40">|</span>
                <i class="bi bi-arrows"></i> Phím &larr; / &rarr;
            </div>
            <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill fw-bold" id="topStationCounter">
                Trạm 1 / <?= count($stops) ?>
            </span>
        </div>
    </header>

    <!-- WEBGL CANVAS VIEWPORT (RIGHT STAGE 3D THREE.JS) -->
    <div id="threejs-viewport">
        <canvas id="virtualTourCanvas" style="width: 100%; height: 100%; display: block;"></canvas>
    </div>

    <!-- CỘT TRÁI: TRỤC DỌC NÉT ĐỨT + GLASS STORY CARD + HIỆU ỨNG TYPING -->
    <div class="vt-left-story-column">
        <!-- Vùng viewport cho 2 Card trượt theo tiến độ cuộn liên tục -->
        <div class="vt-story-track-viewport" id="storyTrackViewport">
            <!-- Trục dọc nét đứt xuyên tâm -->
            <div class="vt-dashed-axis-line" id="dashedAxisLine"></div>
            <div class="vt-dashed-axis-arrow"></div>

            <!-- Card chính của Trạm hiện tại -->
            <div class="vt-glass-story-card tour-hud-card" id="station-overlay">
                <div class="vt-station-top-pill" id="station-badge">Trạm 1</div>
                <h3 class="fw-bold text-white font-editorial mb-2 mt-1" id="station-caption">
                    <?= htmlspecialchars($stops[0]['caption'] ?? 'Điểm Dừng Chân') ?>
                </h3>
                <div class="text-light opacity-80 mb-3" id="station-story" style="line-height: 1.65; font-size: 0.95rem; min-height: 72px;">
                    <span class="vt-typed-content"><?= htmlspecialchars($stops[0]['story_text'] ?? '') ?></span><span class="vt-typing-cursor"></span>
                </div>
                <!-- Chứa nội dung văn bản thô phục vụ kiểm thử tự động -->
                <span class="d-none" id="station-story-raw"><?= htmlspecialchars($stops[0]['story_text'] ?? '') ?></span>
                <div class="d-flex align-items-center gap-2" id="hudVideoWrapper">
                    <?php if (!empty($stops[0]['video_url'])): ?>
                        <a href="<?= htmlspecialchars($stops[0]['video_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1" id="hudVideoBtn">
                            <i class="bi bi-play-circle-fill me-1"></i> Xem Video Thực Tế
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card phụ trôi từ dưới lên khi cuộn dở dang giữa 2 trạm (Frame 2 -> 3) -->
            <div class="vt-glass-story-card vt-glass-preview-card position-absolute" id="station-next-card">
                <div class="vt-station-top-pill" id="next-station-badge">Trạm 2</div>
                <h3 class="fw-bold text-white font-editorial mb-2 mt-1" id="next-station-caption">Trạm 2</h3>
                <div class="text-light opacity-75 mb-3" id="next-station-story" style="line-height: 1.6; font-size: 0.95rem;">
                    ...
                </div>
            </div>
        </div>

        <!-- Cụm nút điều hướng chân trục dọc: [↩ Quay lại] + [Trạm tiếp →] -->
        <div class="vt-bottom-left-controls">
            <button type="button" class="vt-btn-undo-prev" id="btnPrevStation" title="Quay lại trạm trước">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button type="button" class="vt-btn-next-pill" id="btnNextStation">
                <span>Trạm tiếp</span> <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- DẢI VIÊN THUỐC TRỤC TRẠM NHANH (ĐÁY PHẢI) -->
    <div class="tour-station-rail d-none d-md-flex" id="tourStationRail">
        <?php foreach ($stops as $idx => $st): ?>
            <button type="button" 
                    class="tour-rail-btn <?= $idx === 0 ? 'active' : '' ?>" 
                    data-station-idx="<?= $idx ?>">
                <i class="bi bi-geo-alt me-1"></i> Trạm <?= $idx + 1 ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- THREE.JS CONTINUOUS Z-SCROLL & SPLIT-STAGE ENGINE -->
    <script>
    (function() {
        const tourStops = <?= json_encode($stops, JSON_UNESCAPED_UNICODE) ?>;
        if (!tourStops || !tourStops.length) return;

        // Trích xuất tham số ?station=X nếu có
        const urlParams = new URLSearchParams(window.location.search);
        let initialStop = parseInt(urlParams.get('station'), 10);
        if (isNaN(initialStop) || initialStop < 0 || initialStop >= tourStops.length) {
            initialStop = 0;
        }

        // Các phần tử DOM
        const canvasEl = document.getElementById('virtualTourCanvas');
        const overlayCard = document.getElementById('station-overlay');
        const badgeEl = document.getElementById('station-badge');
        const captionEl = document.getElementById('station-caption');
        const storyEl = document.getElementById('station-story');
        const rawStoryEl = document.getElementById('station-story-raw');
        const videoWrapper = document.getElementById('hudVideoWrapper');
        const nextCard = document.getElementById('station-next-card');
        const nextBadgeEl = document.getElementById('next-station-badge');
        const nextCaptionEl = document.getElementById('next-station-caption');
        const nextStoryEl = document.getElementById('next-station-story');
        const btnPrev = document.getElementById('btnPrevStation');
        const btnNext = document.getElementById('btnNextStation');
        const topCounter = document.getElementById('topStationCounter');
        const railButtons = document.querySelectorAll('[data-station-idx]');

        // Biến trạng thái Cuộn Liên Tục
        let targetProgress = initialStop;
        let currentProgress = initialStop;
        let lastSettledIndex = -1;
        let snapTimer = null;
        let typeTimer = null;

        // Three.js Khởi tạo
        let scene, camera, renderer, animId;
        const stationGroups = [];
        let mouseX = 0, mouseY = 0;
        let targetMouseX = 0, targetMouseY = 0;

        // Độ dịch làn sang bên phải màn hình để nhường Cột Trái cho Story Card
        function getStageXOffset() {
            return window.innerWidth >= 992 ? 1.85 : 0;
        }

        // Tạo Texture bo góc mềm cho tấm ảnh 3D (canvas offscreen bo 36px)
        function createRoundedCardTexture(imgUrl, radius = 36, width = 1024, height = 640) {
            return new Promise((resolve) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');

                    // Vẽ hình chữ nhật bo góc
                    ctx.beginPath();
                    ctx.moveTo(radius, 0);
                    ctx.lineTo(width - radius, 0);
                    ctx.quadraticCurveTo(width, 0, width, radius);
                    ctx.lineTo(width, height - radius);
                    ctx.quadraticCurveTo(width, height, width - radius, height);
                    ctx.lineTo(radius, height);
                    ctx.quadraticCurveTo(0, height, 0, height - radius);
                    ctx.lineTo(0, radius);
                    ctx.quadraticCurveTo(0, 0, radius, 0);
                    ctx.closePath();
                    ctx.clip();

                    // Vẽ ảnh phủ trọn khung
                    ctx.drawImage(img, 0, 0, width, height);

                    // Viền sáng mềm mại
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
                    ctx.lineWidth = 4;
                    ctx.stroke();

                    const texture = new THREE.CanvasTexture(canvas);
                    texture.needsUpdate = true;
                    resolve(texture);
                };
                img.onerror = () => {
                    // Fallback nếu ảnh lỗi
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(0, 0, width, height);
                    const texture = new THREE.CanvasTexture(canvas);
                    resolve(texture);
                };
                img.src = imgUrl;
            });
        }

        // Hiệu ứng Typing gõ từng chữ cho phần mô tả
        function runTypewriter(fullText) {
            if (typeTimer) clearInterval(typeTimer);
            if (!storyEl) return;

            if (rawStoryEl) rawStoryEl.textContent = fullText;

            storyEl.innerHTML = '<span class="vt-typed-content"></span><span class="vt-typing-cursor"></span>';
            const contentSpan = storyEl.querySelector('.vt-typed-content');
            if (!contentSpan) return;

            let charIdx = 0;
            typeTimer = setInterval(() => {
                if (charIdx < fullText.length) {
                    contentSpan.textContent += fullText.charAt(charIdx);
                    charIdx++;
                } else {
                    clearInterval(typeTimer);
                    typeTimer = null;
                }
            }, 18);
        }

        // Cập nhật trạng thái Trạm khi Camera dừng lại hẳn
        function onStationSettled(idx) {
            const st = tourStops[idx];
            if (!st) return;

            if (badgeEl) badgeEl.innerText = `Trạm ${idx + 1}`;
            if (captionEl) captionEl.innerText = st.caption || `Trạm ${idx + 1}`;
            if (topCounter) topCounter.innerText = `Trạm ${idx + 1} / ${tourStops.length}`;

            // Chạy hiệu ứng Typing
            runTypewriter(st.story_text || 'Khám phá vẻ đẹp bản địa nguyên sơ và văn hóa đặc sắc.');

            if (videoWrapper) {
                if (st.video_url) {
                    videoWrapper.innerHTML = `
                        <a href="${st.video_url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1">
                            <i class="bi bi-play-circle-fill me-1"></i> Xem Video Thực Tế
                        </a>
                    `;
                } else {
                    videoWrapper.innerHTML = '';
                }
            }

            // Đồng bộ trạng thái các nút
            if (btnPrev) btnPrev.disabled = (idx <= 0);
            if (btnNext) btnNext.disabled = (idx >= tourStops.length - 1);

            railButtons.forEach((btn, i) => {
                btn.classList.toggle('active', i === idx);
            });
        }

        // Khởi tạo Scene Three.js
        function initScene() {
            const width = window.innerWidth;
            const height = window.innerHeight;

            scene = new THREE.Scene();
            scene.fog = new THREE.FogExp2(0x070e17, 0.032);

            camera = new THREE.PerspectiveCamera(54, width / height, 0.1, 140);
            camera.position.set(0, 0, 7.2);

            renderer = new THREE.WebGLRenderer({ canvas: canvasEl, antialias: true, alpha: true });
            renderer.setSize(width, height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

            const stageX = getStageXOffset();

            // Tạo các nhóm Trạm 3D
            tourStops.forEach((st, i) => {
                const group = new THREE.Group();
                group.position.set(stageX, -100, 0); // Ban đầu tạm ẩn xa

                // Tầng 1: Back-Plate Kính sẫm bo góc
                const backGeo = new THREE.PlaneGeometry(8.1, 5.0);
                const backMat = new THREE.MeshBasicMaterial({
                    color: 0x38bdf8,
                    transparent: true,
                    opacity: 0.14,
                    side: THREE.DoubleSide
                });
                const backMesh = new THREE.Mesh(backGeo, backMat);
                backMesh.position.z = -0.42;
                group.add(backMesh);

                // Tầng 2: Main Photo bo góc mềm
                const photoGeo = new THREE.PlaneGeometry(7.6, 4.6);
                const photoMat = new THREE.MeshBasicMaterial({
                    color: 0xffffff,
                    transparent: true,
                    opacity: 1
                });
                const photoMesh = new THREE.Mesh(photoGeo, photoMat);
                photoMesh.position.z = 0;
                group.add(photoMesh);

                // Nạp Texture bo góc
                if (st.image_url) {
                    createRoundedCardTexture(st.image_url, 38).then(tex => {
                        photoMat.map = tex;
                        photoMat.needsUpdate = true;
                    });
                }

                // Tầng 3: Khung viền sáng nổi phía trước
                const borderGeo = new THREE.EdgesGeometry(new THREE.PlaneGeometry(7.78, 4.78));
                const borderMat = new THREE.LineBasicMaterial({
                    color: 0xffffff,
                    transparent: true,
                    opacity: 0.4
                });
                const borderLines = new THREE.LineSegments(borderGeo, borderMat);
                borderLines.position.z = 0.35;
                group.add(borderLines);

                group.userData = { index: i, backMesh, photoMesh, borderLines };
                scene.add(group);
                stationGroups.push(group);
            });

            // Lắng nghe di chuột tạo Parallax
            window.addEventListener('mousemove', (e) => {
                targetMouseX = ((e.clientX / window.innerWidth) - 0.5) * 2;
                targetMouseY = ((e.clientY / window.innerHeight) - 0.5) * 2;
            });

            // =========================================================================
            // CƠ CHẾ CUỘN LIÊN TỤC TỪNG NẤC (SCROLL PROGRESS)
            // =========================================================================
            window.addEventListener('wheel', (e) => {
                e.preventDefault();

                // Mỗi nấc lăn chuột chỉ nhích khoảng 8% - 10% quãng đường
                const stepDelta = Math.sign(e.deltaY) * Math.min(Math.abs(e.deltaY) * 0.0011, 0.12);
                targetProgress = Math.max(0, Math.min(tourStops.length - 1, targetProgress + stepDelta));

                // Magnetic Snap: Khi người dùng dừng lăn chuột sau 380ms, tự động hút về trạm gần nhất
                clearTimeout(snapTimer);
                snapTimer = setTimeout(() => {
                    targetProgress = Math.round(targetProgress);
                }, 380);
            }, { passive: false });

            // Phím mũi tên
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    targetProgress = Math.min(tourStops.length - 1, Math.round(targetProgress) + 1);
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    targetProgress = Math.max(0, Math.round(targetProgress) - 1);
                }
            });

            // Cảm ứng vuốt trên Mobile
            let touchStartY = 0;
            window.addEventListener('touchstart', (e) => {
                touchStartY = e.touches[0].clientY;
            }, { passive: true });

            window.addEventListener('touchmove', (e) => {
                const dy = touchStartY - e.touches[0].clientY;
                touchStartY = e.touches[0].clientY;
                const touchStep = (dy / window.innerHeight) * 1.6;
                targetProgress = Math.max(0, Math.min(tourStops.length - 1, targetProgress + touchStep));
            }, { passive: true });

            window.addEventListener('touchend', () => {
                clearTimeout(snapTimer);
                snapTimer = setTimeout(() => {
                    targetProgress = Math.round(targetProgress);
                }, 280);
            }, { passive: true });

            // Vòng lặp Render 60fps
            function animate() {
                animId = requestAnimationFrame(animate);

                // Nội suy tiến độ cuộn liên tục
                currentProgress += (targetProgress - currentProgress) * 0.085;

                // Chuột Parallax mượt mà
                mouseX += (targetMouseX - mouseX) * 0.05;
                mouseY += (targetMouseY - mouseY) * 0.05;

                const stageX = getStageXOffset();

                // Cập nhật vị trí từng trạm theo công thức 4 Frame kịch bản
                stationGroups.forEach((grp, i) => {
                    const offset = i - currentProgress;

                    if (Math.abs(offset) > 2.2) {
                        // Trạm ở quá xa -> Ẩn đi để tối ưu hiệu năng
                        grp.visible = false;
                        return;
                    }

                    grp.visible = true;

                    let posY = 0;
                    let posZ = 0;
                    let rotX = 0;
                    let scale = 1.0;
                    let opacity = 1.0;

                    if (offset < 0) {
                        // Frame 2 & Frame 3: Trạm cũ trôi lên trên & lùi sâu vào không gian 3D
                        const absOff = Math.abs(offset);
                        posY = absOff * 5.6;
                        posZ = -absOff * 4.5;
                        rotX = absOff * 0.16;
                        scale = Math.max(0.65, 1 - absOff * 0.2);
                        opacity = Math.max(0, 1 - absOff * 0.7);
                    } else if (offset > 0) {
                        // Frame 3: Trạm mới từ dưới đáy sâu trồi lên
                        posY = -offset * 5.8;
                        posZ = -offset * 5.2;
                        rotX = -offset * 0.18;
                        scale = Math.max(0.7, 1 - offset * 0.18);
                        opacity = Math.max(0, 1 - Math.max(0, offset - 0.2) * 0.85);
                    } else {
                        // Frame 1 & Frame 4: Trạm dừng ngay tại tâm
                        posY = 0;
                        posZ = 0;
                        rotX = 0;
                        scale = 1.0;
                        opacity = 1.0;
                    }

                    // Tọa độ X ở làn bên phải + Parallax
                    grp.position.set(
                        stageX + mouseX * 0.25,
                        posY - mouseY * 0.2,
                        posZ
                    );

                    grp.rotation.set(
                        rotX + mouseY * 0.05,
                        -mouseX * 0.06,
                        0
                    );

                    grp.scale.set(scale, scale, scale);

                    // Độ mờ alpha
                    if (grp.userData.photoMesh) {
                        grp.userData.photoMesh.material.opacity = opacity;
                    }
                    if (grp.userData.backMesh) {
                        grp.userData.backMesh.material.opacity = opacity * 0.14;
                    }
                    if (grp.userData.borderLines) {
                        grp.userData.borderLines.material.opacity = opacity * 0.4;
                    }
                });

                // Đồng bộ vị trí Card Trái trên trục nét đứt khi cuộn dở giữa 2 trạm
                const baseIndex = Math.floor(currentProgress);
                const frac = currentProgress - baseIndex;
                const nextIndex = Math.min(tourStops.length - 1, baseIndex + 1);

                if (overlayCard && nextCard) {
                    if (frac > 0.02 && baseIndex < tourStops.length - 1) {
                        // Đang trượt giữa trạm baseIndex và nextIndex
                        const stNext = tourStops[nextIndex];
                        if (stNext) {
                            if (nextBadgeEl) nextBadgeEl.innerText = `Trạm ${nextIndex + 1}`;
                            if (nextCaptionEl) nextCaptionEl.innerText = stNext.caption || `Trạm ${nextIndex + 1}`;
                            if (nextStoryEl) nextStoryEl.innerText = (stNext.story_text || '').substring(0, 120) + '...';
                        }

                        // Card chính trượt dần lên
                        overlayCard.style.transform = `translateY(-${frac * 115}%)`;
                        overlayCard.style.opacity = `${Math.max(0, 1 - frac * 1.5)}`;

                        // Card tiếp theo trồi từ dưới đáy lên
                        nextCard.style.opacity = `${Math.min(1, frac * 1.5)}`;
                        nextCard.style.transform = `translateY(${(1 - frac) * 115}%)`;
                    } else {
                        // Dừng gần trạm
                        overlayCard.style.transform = 'translateY(0)';
                        overlayCard.style.opacity = '1';
                        nextCard.style.opacity = '0';
                        nextCard.style.transform = 'translateY(160%)';
                    }
                }

                // Phát hiện khi Camera dừng hẳn tại trạm để chạy gõ chữ
                const settledIndex = Math.round(currentProgress);
                if (Math.abs(currentProgress - settledIndex) < 0.06 && settledIndex !== lastSettledIndex) {
                    lastSettledIndex = settledIndex;
                    onStationSettled(settledIndex);
                }

                renderer.render(scene, camera);
            }

            animate();
            onStationSettled(initialStop);
        }

        // Nút bấm chân trục dọc
        if (btnPrev) {
            btnPrev.addEventListener('click', () => {
                targetProgress = Math.max(0, Math.round(targetProgress) - 1);
            });
        }

        if (btnNext) {
            btnNext.addEventListener('click', () => {
                targetProgress = Math.min(tourStops.length - 1, Math.round(targetProgress) + 1);
            });
        }

        // Dải viên thuốc
        railButtons.forEach((btn) => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-station-idx'), 10);
                if (!isNaN(idx)) {
                    targetProgress = idx;
                }
            });
        });

        // Resize màn hình
        window.addEventListener('resize', () => {
            if (renderer && camera) {
                const w = window.innerWidth;
                const h = window.innerHeight;
                camera.aspect = w / h;
                camera.updateProjectionMatrix();
                renderer.setSize(w, h);
            }
        });

        // Khởi động
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initScene);
        } else {
            initScene();
        }
    })();
    </script>
</body>
</html>
