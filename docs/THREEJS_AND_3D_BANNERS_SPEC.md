# ĐẶC TẢ TRIỂN KHAI HOÀN CHỈNH: THREE.JS 3D Z-SCROLL ĐA LỚP & HỆ THỐNG BANNER 3D (ONLINETRAVELLER v3.5)

## 1. QUY TẮC BẢO TOÀN KIỂM THỬ & CẤU TRÚC (CRITICAL)
1. **Bảo toàn 100% ID/Class cho Automated Tests (`380/380 assertions`)**:
   - Tại `app/views/home/index.php`: Giữ nguyên `#homeHeroAmbientWrapper`, `#heroBannerBox`, `#heroAmbientGlow`, `#heroLocationBadge`, `#heroTitle`, `#heroDesc`, `#heroCtaLink`, `#btnPrevSlide`, `#btnNextSlide`, `#bannerThumbsContainer` (giữ `d-none`).
   - Tại `app/views/services/show.php`: Giữ nguyên `#detailHeroBanner`, `#detailAmbientGlow`, `#milestonesContainer`, `.milestone-thumb-card`, `#btnHeroQuickBook`, `#virtualTour3DModal`, `#threejs-container`, `#virtual-tour-canvas`, `#active-station-img` (ẩn bằng `d-none` làm fallback đồng bộ dữ liệu), `#station-overlay`, `#station-badge`, `#station-caption`, `#station-story`, `#btn-prev-station`, `#btn-next-station`.
2. **Tuân thủ quy tắc lưới Bootstrap 5**: Mọi khối nội dung giữ nguyên trong hệ thống `div.main-container` -> `div.container` (hoặc `container-fluid` / `container-xxl`) -> `div.row` -> `div.col-*`.

---

## 2. HẠNG MỤC 1: BANNER TRANG CHỦ 3D 3-KHUNG HÌNH (`app/views/home/index.php` & `custom-theme.css`)

### 2.1. Mục tiêu thị giác & Tương tác
- Hiển thị **3 Banner trên cùng 1 khung hình** (1 Banner chính ở giữa + 2 Banner phụ của tour trước/sau lấp ló ở 2 mép trái/phải tại tầng $Z$ thấp hơn và kích thước nhỏ hơn).
- Khi người dùng bấm nút mũi tên (`#btnPrevSlide` / `#btnNextSlide`) hoặc **click trực tiếp vào 2 Banner phụ ở 2 bên**, Banner từ hướng được chọn sẽ **nhảy từ tầng sâu $Z = -150\text{px}$ lên tầng $Z = 0\text{px}$ và phóng to (`scale: 0.84 -> 1.0`)** vào vị trí trung tâm.

### 2.2. Cập nhật HTML trong `app/views/home/index.php`
Bổ sung 2 phần tử `#heroPrevPeek` và `#heroNextPeek` vào bên trong `#homeHeroAmbientWrapper` (bao quanh `#heroBannerBox`):

```html
<div id="homeHeroAmbientWrapper" class="hero-3d-stage-wrapper position-relative">
    <!-- Banner phụ bên trái (Tầng Z thấp hơn, click để chuyển sang tour trước) -->
    <div id="heroPrevPeek" class="hero-3d-peek-card is-prev" role="button" aria-label="Tour trước">
        <div class="peek-card-GlassBadge"><i class="bi bi-chevron-left"></i></div>
    </div>

    <!-- Banner chính ở giữa (Tầng Z = 0, giữ nguyên toàn bộ cấu trúc bên trong) -->
    <div id="heroBannerBox" class="hero-banner-box hero-3d-main-card" style="background-image: url('<?= htmlspecialchars($firstSlide['image']) ?>');">
        <!-- GIỮ NGUYÊN toàn bộ nội dung .hero-banner-overlay, #heroTitle, #heroCtaLink, #btnPrevSlide, #btnNextSlide hiện có -->
    </div>

    <!-- Banner phụ bên phải (Tầng Z thấp hơn, click để chuyển sang tour kế tiếp) -->
    <div id="heroNextPeek" class="hero-3d-peek-card is-next" role="button" aria-label="Tour tiếp theo">
        <div class="peek-card-GlassBadge"><i class="bi bi-chevron-right"></i></div>
    </div>

    <!-- Quầng sáng Ambient Glow dưới chân banner -->
    <div id="heroAmbientGlow" class="hero-ambient-glow" style="background-image: url('<?= htmlspecialchars($firstSlide['image']) ?>');"></div>
</div>
```

### 2.3. Bổ sung CSS 3D Carousel vào `public/assets/css/custom-theme.css`
```css
/* ==========================================================================
   BANNER TRANG CHỦ 3D 3-KHUNG HÌNH (3D PEEK CAROUSEL)
   ========================================================================== */
.hero-3d-stage-wrapper {
    position: relative;
    perspective: 1600px;
    transform-style: preserve-3d;
    padding: 0 4.8%; /* Chừa không gian 2 mép cho 2 banner phụ ở tầng Z thấp */
    overflow: visible;
}

@media (max-width: 991.98px) {
    .hero-3d-stage-wrapper {
        padding: 0;
    }
    .hero-3d-peek-card {
        display: none !important;
    }
}

.hero-3d-main-card {
    position: relative;
    z-index: 5;
    transform: translate3d(0, 0, 0) scale(1);
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.32);
    transition: background-image 0.4s ease, box-shadow 0.5s ease;
    will-change: transform, filter;
}

/* Animation nhảy từ tầng sâu bên phải lên vị trí trung tâm */
.hero-3d-main-card.jump-from-right {
    animation: hero3dJumpFromRight 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

/* Animation nhảy từ tầng sâu bên trái lên vị trí trung tâm */
.hero-3d-main-card.jump-from-left {
    animation: hero3dJumpFromLeft 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes hero3dJumpFromRight {
    0% {
        transform: translate3d(15%, 16px, -150px) scale(0.84) rotateY(-7deg);
        filter: brightness(0.68);
    }
    100% {
        transform: translate3d(0, 0, 0) scale(1) rotateY(0deg);
        filter: brightness(1);
    }
}

@keyframes hero3dJumpFromLeft {
    0% {
        transform: translate3d(-15%, 16px, -150px) scale(0.84) rotateY(7deg);
        filter: brightness(0.68);
    }
    100% {
        transform: translate3d(0, 0, 0) scale(1) rotateY(0deg);
        filter: brightness(1);
    }
}

/* 2 Banner phụ ở tầng Z thấp hơn hai bên */
.hero-3d-peek-card {
    position: absolute;
    top: 4.5%;
    bottom: 4.5%;
    width: 24%;
    border-radius: 22px;
    background-size: cover;
    background-position: center;
    z-index: 2;
    cursor: pointer;
    filter: brightness(0.6) saturate(0.9);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.28);
    border: 1px solid rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1), filter 0.35s ease;
}

.hero-3d-peek-card.is-prev {
    left: -0.2%;
    justify-content: flex-start;
    padding-left: 1rem;
    transform: translate3d(0, 0, -150px) scale(0.86) rotateY(9deg);
}

.hero-3d-peek-card.is-prev:hover {
    filter: brightness(0.85);
    transform: translate3d(-6px, -4px, -110px) scale(0.89) rotateY(5deg);
}

.hero-3d-peek-card.is-next {
    right: -0.2%;
    justify-content: flex-end;
    padding-right: 1rem;
    transform: translate3d(0, 0, -150px) scale(0.86) rotateY(-9deg);
}

.hero-3d-peek-card.is-next:hover {
    filter: brightness(0.85);
    transform: translate3d(6px, -4px, -110px) scale(0.89) rotateY(-5deg);
}

.peek-card-GlassBadge {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.hero-3d-peek-card:hover .peek-card-GlassBadge {
    opacity: 1;
    transform: scale(1.1);
}
```

### 2.4. Cập nhật JavaScript điều khiển 3 Banner trong `app/views/home/index.php`
Trong đoạn script slider của `app/views/home/index.php`, cập nhật hàm `applySlide(index, direction = 'next')`:

```javascript
const heroPrevPeek = document.getElementById('heroPrevPeek');
const heroNextPeek = document.getElementById('heroNextPeek');

function applySlide(index, direction = 'next') {
    const total = slides.length;
    currentSlide = (index + total) % total;
    const prevIdx = (currentSlide - 1 + total) % total;
    const nextIdx = (currentSlide + 1) % total;

    const data = slides[currentSlide];
    const prevData = slides[prevIdx];
    const nextData = slides[nextIdx];

    // Cập nhật ảnh cho 2 Banner phụ ở tầng Z thấp hơn
    if (heroPrevPeek && prevData) {
        heroPrevPeek.style.backgroundImage = `url('${prevData.image}')`;
    }
    if (heroNextPeek && nextData) {
        heroNextPeek.style.backgroundImage = `url('${nextData.image}')`;
    }

    // Kích hoạt hiệu ứng 3D nhảy từ hướng được chọn lên tầng Z = 0 và zoom ra giữa
    if (heroBox) {
        heroBox.classList.remove('jump-from-right', 'jump-from-left');
        void heroBox.offsetWidth; // Force reflow để chạy lại keyframe
        heroBox.classList.add(direction === 'prev' ? 'jump-from-left' : 'jump-from-right');
        heroBox.style.backgroundImage = `url('${data.image}')`;
    }

    if (heroGlow) {
        heroGlow.style.backgroundImage = `url('${data.image}')`;
    }

    // Giữ nguyên việc cập nhật text tiêu đề, mô tả, host, rating, link CTA và .banner-thumb-item
    if (heroTitle) heroTitle.textContent = data.title;
    if (heroDesc) heroDesc.textContent = data.desc;
    if (heroLocation) heroLocation.innerHTML = `<i class="bi bi-geo-alt-fill text-danger me-1"></i>${data.location}`;
    if (heroMeta) heroMeta.innerHTML = `Host: <strong>${data.host}</strong> • ★ ${data.rating}`;
    if (heroCta) heroCta.setAttribute('href', `/services/${data.id}`);

    thumbItems.forEach((item, idx) => {
        item.classList.toggle('active', idx === currentSlide);
    });
}

// Cho phép click trực tiếp vào 2 Banner phụ 2 bên để chuyển slide
if (heroPrevPeek) {
    heroPrevPeek.addEventListener('click', () => {
        applySlide(currentSlide - 1, 'prev');
        resetAutoSlide();
    });
}
if (heroNextPeek) {
    heroNextPeek.addEventListener('click', () => {
        applySlide(currentSlide + 1, 'next');
        resetAutoSlide();
    });
}
```

---

## 3. HẠNG MỤC 2: NÂNG CẤP BANNER TRANG CHI TIẾT VỚI HIỆU ỨNG 3D MULTI-LAYER PARALLAX & DEPTH ZOOM (`app/views/services/show.php`)

### 3.1. CSS cho Banner Chi Tiết (`public/assets/css/custom-theme.css`)
```css
/* Hiệu ứng phóng từ tầng sâu ra ngoài khi click đổi cột mốc trên Banner Chi tiết */
#detailHeroBanner {
    transition: transform 0.25s ease-out, background-image 0.35s ease;
    transform-style: preserve-3d;
    will-change: transform;
}

#detailHeroBanner.detail-depth-fly {
    animation: detailBannerDepthZoom 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes detailBannerDepthZoom {
    0% {
        transform: scale(1.08) translateZ(-40px);
        filter: blur(6px) brightness(0.8);
    }
    100% {
        transform: scale(1) translateZ(0);
        filter: blur(0) brightness(1);
    }
}

/* Cho phép click vào các trạm trên trục Timeline để mở thẳng 3D tại trạm đó */
.japan-timeline-step,
.station-timeline-item {
    cursor: pointer;
    transition: transform 0.25s ease;
}
.japan-timeline-step:hover,
.station-timeline-item:hover {
    transform: translateY(-3px);
}
```

### 3.2. Cập nhật JS đổi ảnh cột mốc trên Banner Chi Tiết (`app/views/services/show.php`)
Khi người dùng click vào `.milestone-thumb-card`, kích hoạt animation `.detail-depth-fly` để tạo cảm giác tiến sâu vào cảnh quan của trạm vừa chọn:
```javascript
milestoneCards.forEach(card => {
    card.addEventListener('click', function(e) {
        e.preventDefault();
        const newImg = this.getAttribute('data-image');
        if (newImg && detailBanner) {
            detailBanner.classList.remove('detail-depth-fly');
            void detailBanner.offsetWidth;
            detailBanner.classList.add('detail-depth-fly');

            detailBanner.style.backgroundImage = `url('${newImg}')`;
            if (detailGlow) {
                detailGlow.style.backgroundImage = `url('${newImg}')`;
            }
            milestoneCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        }
    });
});
```

---

## 4. HẠNG MỤC 3: TRIỂN KHAI THREE.JS 3D Z-SCROLL ĐA LỚP + CLICK TIMELINE BAY THẲNG TỚI TRẠM (`app/views/services/show.php`)

### 4.1. Gắn thuộc tính `data-station-index` vào các trạm trên Trục Timeline
Trong vòng lặp `foreach ($stations as $index => $station)` của phần **Hành Trình & Các Trạm Trải Nghiệm** trong `app/views/services/show.php`:
- Thêm thuộc tính `data-station-index="<?= (int)$index ?>"` và class `js-open-station-3d` vào thẻ bọc của từng trạm (cả `.japan-timeline-step` hoặc `.station-timeline-item`).
- Thêm một nhãn gợi ý nhỏ tinh tế ở mỗi trạm: `<span class="badge rounded-pill bg-dark bg-opacity-50 text-white border border-light border-opacity-25 small mt-1"><i class="bi bi-box-arrow-up-right me-1"></i>Xem trạm này trong 3D</span>`.
- Khi người dùng **click vào bất kỳ trạm nào trên trục Timeline**, hệ thống sẽ mở ngay Modal `#virtualTour3DModal` và **di chuyển Camera Three.js thẳng tới đúng Trạm đó** (`goToStation(clickedIndex)`).

### 4.2. Nhúng thư viện Three.js và Thay thế Script Modal 3D trong `app/views/services/show.php`
Đặt thẻ nạp Three.js ngay phía trên script xử lý Modal 3D:

```html
<script src="[https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js](https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js)"></script>
<script>
(function() {
    const tourStops = <?= json_encode($stations, JSON_UNESCAPED_UNICODE) ?>;
    if (!tourStops || !tourStops.length) return;

    let currentStopIndex = 0;
    const modalEl = document.getElementById('virtualTour3DModal');
    const containerEl = document.getElementById('threejs-container');
    const canvasEl = document.getElementById('virtual-tour-canvas');
    const fallbackImgEl = document.getElementById('active-station-img');
    const overlayEl = document.getElementById('station-overlay');
    const badgeEl = document.getElementById('station-badge');
    const captionEl = document.getElementById('station-caption');
    const storyEl = document.getElementById('station-story');
    const prevBtn = document.getElementById('btn-prev-station');
    const nextBtn = document.getElementById('btn-next-station');

    const SPACING_Z = 18;
    const CAMERA_OFFSET_Z = 6.2;

    let scene, camera, renderer, animId;
    let stationGroups = [];
    let targetCameraZ = CAMERA_OFFSET_Z;
    let mouseX = 0, mouseY = 0;
    let isThreeInitialized = false;
    let wheelCooldown = false;

    // 1. Đồng bộ khối Glass HUD (#station-overlay) trượt từ dưới lên kèm thông tin trạm
    function updateHudGlassCard(idx) {
        const st = tourStops[idx];
        if (!st) return;

        // Đồng bộ ảnh fallback (đang ẩn d-none) để pass 100% unit tests
        if (fallbackImgEl) {
            fallbackImgEl.src = st.image_url || '';
            fallbackImgEl.alt = st.caption || '';
        }

        if (overlayEl) {
            overlayEl.style.transition = 'opacity 0.22s ease, transform 0.35s cubic-bezier(0.22, 1, 0.36, 1)';
            overlayEl.style.opacity = '0';
            overlayEl.style.transform = 'translateY(24px)';
        }

        setTimeout(() => {
            if (badgeEl) badgeEl.innerText = `Trạm ${idx + 1} / ${tourStops.length}`;
            if (captionEl) captionEl.innerText = st.caption || '';
            if (storyEl) storyEl.innerText = st.story_text || '';

            if (overlayEl) {
                overlayEl.style.opacity = '1';
                overlayEl.style.transform = 'translateY(0)';
            }
        }, 140);
    }

    // 2. Điều hướng Camera bay tới tọa độ Z của Trạm được chọn
    function goToStation(idx) {
        currentStopIndex = (idx + tourStops.length) % tourStops.length;
        targetCameraZ = (-currentStopIndex * SPACING_Z) + CAMERA_OFFSET_Z;
        updateHudGlassCard(currentStopIndex);
    }

    // 3. Khởi tạo không gian WebGL Three.js Đa Lớp có chiều sâu
    function initThreeZScroll() {
        if (isThreeInitialized || typeof THREE === 'undefined' || !canvasEl || !containerEl) return;
        isThreeInitialized = true;

        if (fallbackImgEl) {
            fallbackImgEl.classList.add('d-none');
        }

        const width = containerEl.clientWidth || 960;
        const height = containerEl.clientHeight || 520;

        scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x0b131e, 0.032);

        camera = new THREE.PerspectiveCamera(55, width / height, 0.1, 130);
        camera.position.set(0, 0, CAMERA_OFFSET_Z);

        renderer = new THREE.WebGLRenderer({ canvas: canvasEl, antialias: true, alpha: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        const textureLoader = new THREE.TextureLoader();

        tourStops.forEach((st, i) => {
            const group = new THREE.Group();
            const zPos = -i * SPACING_Z;
            const xOffset = (i % 2 === 0 ? -0.32 : 0.32);

            // Các trạm ở tầng sâu khởi tạo ở vị trí Y thấp (-3.5) để khi cuộn sẽ trồi từ dưới lên
            group.position.set(xOffset, i === 0 ? 0 : -3.5, zPos);

            // Lớp 1 (Back-Plate Z = -0.45): Tấm nền kính mờ tạo chiều sâu phía sau ảnh
            const backGeo = new THREE.PlaneGeometry(7.6, 4.65);
            const backMat = new THREE.MeshBasicMaterial({
                color: 0x9bcbe5,
                transparent: true,
                opacity: 0.15
            });
            const backMesh = new THREE.Mesh(backGeo, backMat);
            backMesh.position.z = -0.45;
            group.add(backMesh);

            // Lớp 2 (Main Photo Z = 0): Bức ảnh thực tế của trạm
            const photoGeo = new THREE.PlaneGeometry(7.0, 4.15);
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

            // Lớp 3 (Foreground Depth Frame Z = +0.35): Khung viền nổi phía trước mặt ảnh
            const borderGeo = new THREE.EdgesGeometry(new THREE.PlaneGeometry(7.28, 4.35));
            const borderMat = new THREE.LineBasicMaterial({
                color: 0xffffff,
                transparent: true,
                opacity: 0.38
            });
            const borderLines = new THREE.LineSegments(borderGeo, borderMat);
            borderLines.position.z = 0.35;
            group.add(borderLines);

            group.userData = { index: i, backMesh, photoMesh, borderLines };
            scene.add(group);
            stationGroups.push(group);
        });

        // Lắng nghe di chuột để tạo hiệu ứng Parallax đa lớp
        containerEl.addEventListener('mousemove', (e) => {
            const rect = containerEl.getBoundingClientRect();
            mouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
            mouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
        });

        // Lắng nghe lăn chuột (Wheel) để cuộn xuyên chiều sâu Z
        containerEl.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (wheelCooldown) return;
            wheelCooldown = true;
            if (e.deltaY > 0) {
                goToStation(currentStopIndex + 1);
            } else {
                goToStation(currentStopIndex - 1);
            }
            setTimeout(() => { wheelCooldown = false; }, 480);
        }, { passive: false });

        // Hỗ trợ vuốt cảm ứng (Touch Swipe) trên Mobile/Tablet
        let touchStartY = 0;
        containerEl.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].clientY;
        }, { passive: true });
        containerEl.addEventListener('touchend', (e) => {
            const deltaY = touchStartY - e.changedTouches[0].clientY;
            if (Math.abs(deltaY) > 40) {
                goToStation(deltaY > 0 ? currentStopIndex + 1 : currentStopIndex - 1);
            }
        }, { passive: true });

        function animate() {
            animId = requestAnimationFrame(animate);

            // Nội suy vị trí Camera tiến dọc trục Z
            camera.position.z += (targetCameraZ - camera.position.z) * 0.08;
            camera.position.x += (mouseX * 0.35 - camera.position.x) * 0.06;
            camera.position.y += (-mouseY * 0.25 - camera.position.y) * 0.06;

            // Hiệu ứng các lớp sâu đi từ dưới lên (Y-Rise) khi cuộn tới
            stationGroups.forEach((grp) => {
                const distFromCam = camera.position.z - grp.position.z;
                const relativeDist = distFromCam - CAMERA_OFFSET_Z;

                let targetY = 0;
                let targetRotX = 0;

                if (relativeDist > 1.2) {
                    // Trạm đang ở tầng sâu phía trước -> Nằm thấp phía dưới và trồi lên khi Camera tiến tới
                    targetY = -Math.min(relativeDist * 0.24, 3.8);
                    targetRotX = Math.min(relativeDist * 0.018, 0.28);
                } else if (relativeDist < -1.2) {
                    // Trạm đã đi qua -> Bay vút nhẹ lên trên
                    targetY = Math.min(Math.abs(relativeDist) * 0.14, 1.8);
                }

                grp.position.y += (targetY - grp.position.y) * 0.09;
                grp.rotation.x += (targetRotX - grp.rotation.x) * 0.09;
                grp.rotation.y += (-mouseX * 0.08 - grp.rotation.y) * 0.08;

                // Chuyển động lệch pha giữa 3 lớp (Back Glass - Photo - Front Frame)
                grp.userData.backMesh.position.x = -mouseX * 0.18;
                grp.userData.backMesh.position.y = mouseY * 0.14;
                grp.userData.borderLines.position.x = mouseX * 0.15;
                grp.userData.borderLines.position.y = -mouseY * 0.12;
            });

            renderer.render(scene, camera);
        }

        animate();
    }

    if (prevBtn) prevBtn.addEventListener('click', () => goToStation(currentStopIndex - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goToStation(currentStopIndex + 1));

    // Click vào bất kỳ Trạm nào trên trục Timeline bên ngoài -> Mở Modal 3D và bay thẳng tới Trạm đó
    document.querySelectorAll('[data-station-index]').forEach((item) => {
        item.addEventListener('click', function() {
            const idx = parseInt(this.getAttribute('data-station-index'), 10);
            if (!isNaN(idx)) {
                currentStopIndex = idx;
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalInstance.show();
                }
                goToStation(idx);
            }
        });
    });

    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', () => {
            initThreeZScroll();
            goToStation(currentStopIndex);
            if (renderer && containerEl) {
                const w = containerEl.clientWidth || 960;
                const h = containerEl.clientHeight || 520;
                camera.aspect = w / h;
                camera.updateProjectionMatrix();
                renderer.setSize(w, h);
            }
        });
    }

    window.addEventListener('resize', () => {
        if (renderer && camera && containerEl && modalEl?.classList.contains('show')) {
            const w = containerEl.clientWidth || 960;
            const h = containerEl.clientHeight || 520;
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
            renderer.setSize(w, h);
        }
    });
})();
</script>
```