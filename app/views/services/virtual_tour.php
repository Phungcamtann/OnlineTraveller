<?php
/**
 * DEDICATED FULLSCREEN 3D VIRTUAL TOUR PAGE (THREE.JS Z-SCROLL)
 * OnlineTraveller - Trình Chiếu Không Gian 3D Đa Lớp Có Chiều Sâu
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
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #070e17;
            font-family: var(--font-body, system-ui, -apple-system, sans-serif);
            color: #f1f5f9;
        }

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

        /* Top Bar */
        .tour-top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            padding: 16px 24px;
            background: linear-gradient(to bottom, rgba(7, 14, 23, 0.92) 0%, rgba(7, 14, 23, 0.4) 75%, transparent 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            pointer-events: none;
        }

        .tour-top-bar > * {
            pointer-events: auto;
        }

        /* Glass HUD Panel */
        .tour-hud-card {
            position: fixed;
            bottom: 32px;
            left: 32px;
            max-width: 520px;
            z-index: 50;
            background: rgba(11, 23, 38, 0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 20px;
            padding: 24px 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.65);
            transition: opacity 0.25s ease, transform 0.35s cubic-bezier(0.2, 0.9, 0.3, 1);
        }

        /* Floating Controls */
        .tour-controls-floating {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 50;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Bottom Timeline Rail */
        .tour-station-rail {
            position: fixed;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            background: rgba(11, 23, 38, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.14);
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
        }

        .tour-rail-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .tour-rail-btn.active {
            color: #070e17;
            background: #38bdf8;
            font-weight: 700;
            box-shadow: 0 0 14px rgba(56, 189, 248, 0.6);
        }

        .btn-nav-glass {
            background: rgba(15, 29, 48, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 9999px;
            transition: all 0.22s ease;
        }

        .btn-nav-glass:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        .btn-nav-next {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 9999px;
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.45);
            transition: all 0.22s ease;
        }

        .btn-nav-next:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(56, 189, 248, 0.6);
        }

        /* Keyboard & scroll hints */
        .hint-pill {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 9999px;
            padding: 4px 12px;
        }

        @media (max-width: 991px) {
            .tour-station-rail {
                display: none !important;
            }
            .tour-hud-card {
                left: 16px;
                right: 16px;
                bottom: 84px;
                max-width: none;
                padding: 18px 20px;
            }
            .tour-controls-floating {
                right: 16px;
                bottom: 20px;
                width: calc(100% - 32px);
                justify-content: space-between;
            }
            .btn-nav-glass, .btn-nav-next {
                flex: 1;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <header class="tour-top-bar">
        <div class="d-flex align-items-center gap-3">
            <a href="/services/<?= (int)($service['id'] ?? 1) ?>" class="btn btn-nav-glass py-2 px-3 small">
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
                <i class="bi bi-mouse"></i> Lăn chuột
                <span class="opacity-40">|</span>
                <i class="bi bi-arrows"></i> Phím &larr; / &rarr;
            </div>
            <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill fw-bold" id="topStationCounter">
                Trạm 1 / <?= count($stops) ?>
            </span>
        </div>
    </header>

    <!-- WEBGL CANVAS VIEWPORT -->
    <div id="threejs-viewport">
        <canvas id="virtualTourCanvas" style="width: 100%; height: 100%; display: block;"></canvas>
    </div>

    <!-- STATION STORY GLASS HUD CARD -->
    <div class="tour-hud-card" id="stationHudCard">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-30 rounded-pill px-3 py-1" id="hudBadge">
                Trạm 1 / <?= count($stops) ?>
            </span>
            <small class="text-white opacity-50" id="hudIndexIndicator">1 of <?= count($stops) ?></small>
        </div>
        <h3 class="fw-bold text-white font-editorial mb-2" id="hudCaption">
            <?= htmlspecialchars($stops[0]['caption'] ?? 'Điểm Dừng Chân') ?>
        </h3>
        <p class="text-light opacity-80 mb-3" id="hudStory" style="line-height: 1.6; font-size: 0.95rem;">
            <?= htmlspecialchars($stops[0]['story_text'] ?? 'Khám phá vẻ đẹp bản địa nguyên sơ và văn hóa đặc sắc cùng người dân địa phương.') ?>
        </p>
        <div class="d-flex align-items-center gap-2" id="hudVideoWrapper">
            <?php if (!empty($stops[0]['video_url'])): ?>
                <a href="<?= htmlspecialchars($stops[0]['video_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1" id="hudVideoBtn">
                    <i class="bi bi-play-circle-fill me-1"></i> Xem Video Thực Tế
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- QUICK STATION TIMELINE RAIL (CENTER) -->
    <div class="tour-station-rail d-none d-md-flex" id="tourStationRail">
        <?php foreach ($stops as $idx => $st): ?>
            <button type="button" 
                    class="tour-rail-btn <?= $idx === 0 ? 'active' : '' ?>" 
                    data-station-idx="<?= $idx ?>">
                <i class="bi bi-geo-alt me-1"></i> Trạm <?= $idx + 1 ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- FLOATING NAVIGATION BUTTONS (RIGHT) -->
    <div class="tour-controls-floating">
        <button type="button" class="btn btn-nav-glass d-inline-flex align-items-center gap-2" id="btnPrevStation">
            <i class="bi bi-chevron-left"></i> <span>Trạm Trước</span>
        </button>
        <button type="button" class="btn btn-nav-next d-inline-flex align-items-center gap-2" id="btnNextStation">
            <span>Trạm Tiếp</span> <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <!-- THREE.JS Z-SCROLL INTERACTIVE SCRIPT -->
    <script>
    (function() {
        const tourStops = <?= json_encode($stops, JSON_UNESCAPED_UNICODE) ?>;
        if (!tourStops || !tourStops.length) return;

        // Check if specific station was requested via query param ?station=X
        const urlParams = new URLSearchParams(window.location.search);
        let currentStopIndex = parseInt(urlParams.get('station'), 10);
        if (isNaN(currentStopIndex) || currentStopIndex < 0 || currentStopIndex >= tourStops.length) {
            currentStopIndex = 0;
        }

        const viewportEl = document.getElementById('threejs-viewport');
        const canvasEl = document.getElementById('virtualTourCanvas');
        const hudCard = document.getElementById('stationHudCard');
        const hudBadge = document.getElementById('hudBadge');
        const hudCaption = document.getElementById('hudCaption');
        const hudStory = document.getElementById('hudStory');
        const hudVideoWrapper = document.getElementById('hudVideoWrapper');
        const topCounter = document.getElementById('topStationCounter');
        const hudIndexIndicator = document.getElementById('hudIndexIndicator');
        const railButtons = document.querySelectorAll('[data-station-idx]');
        const btnPrev = document.getElementById('btnPrevStation');
        const btnNext = document.getElementById('btnNextStation');

        const SPACING_Z = 18;
        const CAMERA_OFFSET_Z = 6.2;

        let scene, camera, renderer, animId;
        const stationGroups = [];
        let targetCameraZ = (-currentStopIndex * SPACING_Z) + CAMERA_OFFSET_Z;
        let mouseX = 0, mouseY = 0;
        let targetMouseX = 0, targetMouseY = 0;

        // Wheel dampening & smoothing
        let wheelAccumulator = 0;
        let lastWheelTime = 0;
        const WHEEL_THRESHOLD = 90;
        const WHEEL_COOLDOWN = 650; // ms

        // Update HUD card & timeline rail with smooth entrance
        function updateHud(idx) {
            const st = tourStops[idx];
            if (!st) return;

            if (topCounter) topCounter.innerText = `Trạm ${idx + 1} / ${tourStops.length}`;
            if (hudIndexIndicator) hudIndexIndicator.innerText = `${idx + 1} of ${tourStops.length}`;

            // Sync timeline rail active state
            railButtons.forEach((btn, i) => {
                btn.classList.toggle('active', i === idx);
            });

            // Animate HUD card exit and enter
            if (hudCard) {
                hudCard.style.opacity = '0';
                hudCard.style.transform = 'translateY(20px)';
            }

            setTimeout(() => {
                if (hudBadge) hudBadge.innerText = `Trạm ${idx + 1} / ${tourStops.length}`;
                if (hudCaption) hudCaption.innerText = st.caption || `Trạm ${idx + 1}`;
                if (hudStory) hudStory.innerText = st.story_text || 'Khám phá vẻ đẹp bản địa nguyên sơ và văn hóa đặc sắc.';

                if (hudVideoWrapper) {
                    if (st.video_url) {
                        hudVideoWrapper.innerHTML = `
                            <a href="${st.video_url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1">
                                <i class="bi bi-play-circle-fill me-1"></i> Xem Video Thực Tế
                            </a>
                        `;
                    } else {
                        hudVideoWrapper.innerHTML = '';
                    }
                }

                if (hudCard) {
                    hudCard.style.opacity = '1';
                    hudCard.style.transform = 'translateY(0)';
                }
            }, 150);
        }

        // Navigate camera to station index (clamped, no violent wrapping)
        function goToStation(idx) {
            const clamped = Math.max(0, Math.min(tourStops.length - 1, idx));
            if (clamped === currentStopIndex && Math.abs(camera.position.z - targetCameraZ) < 0.1) return;
            currentStopIndex = clamped;
            targetCameraZ = (-currentStopIndex * SPACING_Z) + CAMERA_OFFSET_Z;
            updateHud(currentStopIndex);
        }

        // Initialize Three.js scene
        function initScene() {
            const width = window.innerWidth;
            const height = window.innerHeight;

            scene = new THREE.Scene();
            scene.fog = new THREE.FogExp2(0x070e17, 0.034);

            camera = new THREE.PerspectiveCamera(54, width / height, 0.1, 160);
            camera.position.set(0, 0, targetCameraZ);

            renderer = new THREE.WebGLRenderer({ canvas: canvasEl, antialias: true, alpha: true });
            renderer.setSize(width, height);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

            const textureLoader = new THREE.TextureLoader();

            tourStops.forEach((st, i) => {
                const group = new THREE.Group();
                const zPos = -i * SPACING_Z;
                const xOffset = (i % 2 === 0 ? -0.4 : 0.4);

                // Deep layers initialize at lower Y to rise up smoothly as camera approaches
                group.position.set(xOffset, i === currentStopIndex ? 0 : -3.8, zPos);

                // Layer 1 (Back-Plate Z = -0.48): Frosted glass panel with subtle cyan/blue glow
                const backGeo = new THREE.PlaneGeometry(8.2, 5.0);
                const backMat = new THREE.MeshBasicMaterial({
                    color: 0x38bdf8,
                    transparent: true,
                    opacity: 0.12,
                    side: THREE.DoubleSide
                });
                const backMesh = new THREE.Mesh(backGeo, backMat);
                backMesh.position.z = -0.48;
                group.add(backMesh);

                // Layer 2 (Main Photo Z = 0): High-resolution tour station photography
                const photoGeo = new THREE.PlaneGeometry(7.6, 4.5);
                const photoMat = new THREE.MeshBasicMaterial({
                    color: 0xffffff,
                    transparent: true,
                    opacity: 1
                });

                if (st.image_url) {
                    textureLoader.load(st.image_url, (tex) => {
                        photoMat.map = tex;
                        photoMat.needsUpdate = true;
                    });
                }
                const photoMesh = new THREE.Mesh(photoGeo, photoMat);
                photoMesh.position.z = 0;
                group.add(photoMesh);

                // Layer 3 (Foreground Depth Frame Z = +0.38): Glowing depth frame
                const borderGeo = new THREE.EdgesGeometry(new THREE.PlaneGeometry(7.88, 4.7));
                const borderMat = new THREE.LineBasicMaterial({
                    color: 0xffffff,
                    transparent: true,
                    opacity: 0.42
                });
                const borderLines = new THREE.LineSegments(borderGeo, borderMat);
                borderLines.position.z = 0.38;
                group.add(borderLines);

                group.userData = { index: i, backMesh, photoMesh, borderLines };
                scene.add(group);
                stationGroups.push(group);
            });

            // Mouse parallax tracking
            window.addEventListener('mousemove', (e) => {
                targetMouseX = ((e.clientX / window.innerWidth) - 0.5) * 2;
                targetMouseY = ((e.clientY / window.innerHeight) - 0.5) * 2;
            });

            // Smooth Wheel Scrolling with accumulated delta & threshold (NO wild jumping)
            window.addEventListener('wheel', (e) => {
                e.preventDefault();
                const now = Date.now();
                if (now - lastWheelTime < WHEEL_COOLDOWN) return;

                wheelAccumulator += e.deltaY;

                if (Math.abs(wheelAccumulator) >= WHEEL_THRESHOLD) {
                    if (wheelAccumulator > 0) {
                        goToStation(currentStopIndex + 1);
                    } else {
                        goToStation(currentStopIndex - 1);
                    }
                    wheelAccumulator = 0;
                    lastWheelTime = now;
                }
            }, { passive: false });

            // Keyboard navigation (Arrow keys)
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    goToStation(currentStopIndex + 1);
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    goToStation(currentStopIndex - 1);
                }
            });

            // Touch Swipe navigation for mobile
            let touchStartY = 0;
            let touchStartX = 0;
            window.addEventListener('touchstart', (e) => {
                touchStartY = e.touches[0].clientY;
                touchStartX = e.touches[0].clientX;
            }, { passive: true });

            window.addEventListener('touchend', (e) => {
                const deltaY = touchStartY - e.changedTouches[0].clientY;
                const deltaX = touchStartX - e.changedTouches[0].clientX;
                if (Math.abs(deltaY) > 45 || Math.abs(deltaX) > 45) {
                    if (Math.abs(deltaY) >= Math.abs(deltaX)) {
                        goToStation(deltaY > 0 ? currentStopIndex + 1 : currentStopIndex - 1);
                    } else {
                        goToStation(deltaX > 0 ? currentStopIndex + 1 : currentStopIndex - 1);
                    }
                }
            }, { passive: true });

            // Animation loop with silky smooth lerp
            function animate() {
                animId = requestAnimationFrame(animate);

                // Smooth mouse interpolation
                mouseX += (targetMouseX - mouseX) * 0.05;
                mouseY += (targetMouseY - mouseY) * 0.05;

                // Smooth camera motion along Z-axis
                camera.position.z += (targetCameraZ - camera.position.z) * 0.055;
                camera.position.x += (mouseX * 0.4 - camera.position.x) * 0.05;
                camera.position.y += (-mouseY * 0.28 - camera.position.y) * 0.05;

                // Depth layer Y-Rise effect: deep layers emerge from below
                stationGroups.forEach((grp) => {
                    const distFromCam = camera.position.z - grp.position.z;
                    const relativeDist = distFromCam - CAMERA_OFFSET_Z;

                    let targetY = 0;
                    let targetRotX = 0;

                    if (relativeDist > 1.2) {
                        // Deep upcoming station: sits below, rising up as camera approaches
                        targetY = -Math.min(relativeDist * 0.22, 4.0);
                        targetRotX = Math.min(relativeDist * 0.016, 0.25);
                    } else if (relativeDist < -1.2) {
                        // Passed station: flies gently upward
                        targetY = Math.min(Math.abs(relativeDist) * 0.15, 2.0);
                    }

                    grp.position.y += (targetY - grp.position.y) * 0.08;
                    grp.rotation.x += (targetRotX - grp.rotation.x) * 0.08;
                    grp.rotation.y += (-mouseX * 0.06 - grp.rotation.y) * 0.08;

                    // Parallax shift between sublayers
                    if (grp.userData.backMesh) {
                        grp.userData.backMesh.position.x = -mouseX * 0.22;
                        grp.userData.backMesh.position.y = mouseY * 0.16;
                    }
                    if (grp.userData.borderLines) {
                        grp.userData.borderLines.position.x = mouseX * 0.18;
                        grp.userData.borderLines.position.y = -mouseY * 0.14;
                    }
                });

                renderer.render(scene, camera);
            }

            animate();
            updateHud(currentStopIndex);
        }

        // Navigation button listeners
        if (btnPrev) btnPrev.addEventListener('click', () => goToStation(currentStopIndex - 1));
        if (btnNext) btnNext.addEventListener('click', () => goToStation(currentStopIndex + 1));

        // Station rail pill listeners
        railButtons.forEach((btn) => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-station-idx'), 10);
                if (!isNaN(idx)) goToStation(idx);
            });
        });

        // Window resize handler
        window.addEventListener('resize', () => {
            if (renderer && camera) {
                const w = window.innerWidth;
                const h = window.innerHeight;
                camera.aspect = w / h;
                camera.updateProjectionMatrix();
                renderer.setSize(w, h);
            }
        });

        // Initialize on load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initScene);
        } else {
            initScene();
        }
    })();
    </script>
</body>
</html>
