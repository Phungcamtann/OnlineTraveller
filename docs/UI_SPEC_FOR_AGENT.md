# UI/UX REFACTORING SPECIFICATION FOR LOCAL AI AGENT (OnlineTraveller v2.2)

## 1. NGUYÊN TẮC BẤT DI BẤT DỊCH (CRITICAL RULES)

1. **KHÔNG PHÁ VỠ BACKEND HIỆN TẠI**: Giữ nguyên 100% tên biến PHP, vòng lặp `foreach`, đường dẫn route (`href`, `action`), CSRF token và logic kiểm tra quyền (`$_SESSION['user']`) đang có trong các file View hiện tại. Chỉ thay đổi cấu trúc HTML/CSS hiển thị.
2. **QUY TẮC BOOTSTRAP 5 GRID CỦA GIẢNG VIÊN (BẮT BUỘC)**:
   Mọi trang phải tuân thủ nghiêm ngặt phân cấp:
   `div.main-container` -> `div.container` (hoặc `container-xxl`) -> `div.row` -> `div.col-*`.
   Tuyệt đối không đặt nội dung trực tiếp trong `.row` mà không có `.col-*`.
3. **PHẠM VI ÁP DỤNG THEME**:
   - **Giao diện Khách (Layout chung, Trang chủ, Khám phá, Chi tiết trải nghiệm, Đặt chỗ, Bản tin)**: Áp dụng hệ màu đồng bộ **Unified Dark-Alpine & Ambient Blur** (`#0B1924`).
   - **Giao diện Quản lý Host (`/host/*`) và Admin (`/admin/*`)**: Giữ nguyên giao diện Bootstrap 5 cơ bản, rõ ràng, đủ ô nhập liệu, không cần làm màu cầu kỳ.

---

## 2. HỆ THỐNG MÀU SẮC & TYPOGRAPHY TOÀN CỤC (CUSTOM CSS)

Cập nhật hoặc tạo mới trong `public/assets/css/custom-theme.css` (nhúng sau Bootstrap 5 CSS trong `<head>`):

```css
@import url("[https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap)");

:root {
  --bg-midnight: #0b1924; /* Nền chính toàn trang khách */
  --brand-deep: #184e6c; /* Xanh đậm chủ đạo */
  --brand-primary: #387ea2; /* Nút bấm chính & điểm nhấn */
  --brand-mid: #5ba3c6; /* Đường kẻ Timeline, viền sáng */
  --brand-light: #9bcbe5; /* Chữ phụ, icon thời tiết, chấm tròn */
  --brand-ice: #ddecf6; /* Chữ mô tả dịu mắt */
  --glass-surface: rgba(19, 42, 60, 0.65);
  --glass-border: rgba(155, 203, 229, 0.22);
}

body.theme-dark-alpine {
  font-family: "Plus Jakarta Sans", sans-serif;
  background-color: var(--bg-midnight);
  color: #f8fafc;
  overflow-x: hidden;
}

.font-editorial {
  font-family: "Playfair Display", serif;
}

/* Hiệu ứng Glassmorphism dùng chung cho Navbar, Card, Search Bar */
.glass-panel {
  background: var(--glass-surface);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--glass-border);
  border-radius: 16px;
}

.glass-pill {
  background: rgba(15, 25, 36, 0.58);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid rgba(155, 203, 229, 0.28);
  border-radius: 999px;
}

.btn-glass-hero {
  background: rgba(255, 255, 255, 0.16);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.45);
  color: #ffffff;
  border-radius: 999px;
  padding: 0.65rem 1.6rem;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-glass-hero:hover {
  background: var(--brand-primary);
  border-color: var(--brand-light);
  color: #ffffff;
}

.btn-alpine {
  background: linear-gradient(135deg, var(--brand-primary), var(--brand-deep));
  border: 1px solid var(--brand-mid);
  color: #ffffff;
  border-radius: 999px;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-alpine:hover {
  background: var(--brand-mid);
  color: #0b1924;
  box-shadow: 0 0 18px rgba(91, 163, 198, 0.5);
}

/* Hiệu ứng Adaptive Ambient Blur dưới chân Banner */
.hero-ambient-wrapper {
  position: relative;
  border-radius: 20px;
  overflow: visible;
  z-index: 1;
}

.hero-banner-box {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  min-height: 520px;
  max-height: 64vh;
  background-size: cover;
  background-position: center;
}

.hero-banner-overlay {
  background:
    linear-gradient(
      90deg,
      rgba(11, 25, 36, 0.85) 0%,
      rgba(11, 25, 36, 0.45) 50%,
      rgba(11, 25, 36, 0.15) 100%
    ),
    linear-gradient(
      180deg,
      rgba(11, 25, 36, 0.35) 0%,
      transparent 40%,
      rgba(11, 25, 36, 0.96) 100%
    );
}

.hero-ambient-glow {
  position: absolute;
  bottom: -55px;
  left: 4%;
  width: 92%;
  height: 130px;
  background-size: cover;
  background-position: center bottom;
  filter: blur(65px);
  opacity: 0.38;
  z-index: -1;
  pointer-events: none;
}

/* Card Trải nghiệm tràn ảnh (Experience Poster Card) */
.experience-poster-card {
  position: relative;
  aspect-ratio: 3 / 4;
  border-radius: 18px;
  overflow: hidden;
  border: 1px solid var(--glass-border);
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}

.experience-poster-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 28px rgba(0, 0, 0, 0.45);
  border-color: var(--brand-mid);
}

.experience-poster-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.experience-poster-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(11, 25, 36, 0.95) 0%,
    rgba(11, 25, 36, 0.3) 45%,
    transparent 100%
  );
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 1rem;
}

/* Trục dọc Timeline ở Trang Chi Tiết (About the Tour) */
.station-timeline {
  position: relative;
  padding-left: 2rem;
  border-left: 2px solid var(--brand-mid);
}

.station-timeline-item {
  position: relative;
  margin-bottom: 1.75rem;
}

.station-timeline-dot {
  position: absolute;
  left: -2.45rem;
  top: 1.25rem;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background-color: var(--brand-light);
  box-shadow: 0 0 10px var(--brand-mid);
}

/* Đường kẻ ngang 2 bên Tiêu đề Section */
.section-divider-title {
  display: flex;
  align-items: center;
  text-align: center;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 700;
  color: #f8fafc;
}

.section-divider-title::before,
.section-divider-title::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid rgba(155, 203, 229, 0.3);
}

.section-divider-title::before {
  margin-right: 1.5rem;
}
.section-divider-title::after {
  margin-left: 1.5rem;
}
```

---

## 3. ĐẶC TẢ CHI TIẾT TỪNG FILE GIAO DIỆN (VIEWS)

### 3.1. Khung Giao Diện Chung (`app/views/layouts/main.php`)

- Thẻ `<body>` của các trang khách gắn class `theme-dark-alpine`.
- **Top Floating Navbar (Đè lên trên Banner hoặc cố định phía trên)**:
  - Cấu trúc chuẩn:

    ```html
    <header class="position-absolute top-0 start-0 w-100 z-3 py-3">
      <div class="container-xxl">
        <div class="row align-items-center justify-content-between">
          <!-- Cột 1: Logo thương hiệu -->
          <div class="col-auto">
            <a
              href="/"
              class="d-flex align-items-center gap-2 text-decoration-none text-white fw-bold fs-5"
            >
              <img
                src="/assets/images/logo.png"
                alt="OnlineTraveller"
                width="38"
                height="38"
                class="rounded-circle"
              />
              <span>OnlineTraveller</span>
            </a>
          </div>

          <!-- Cột 2: Menu trung tâm dạng viên thuốc (CHỈ TEXT THUẦN, KHÔNG ICON) -->
          <div class="col-auto d-none d-lg-block">
            <nav class="glass-pill px-4 py-2 d-flex align-items-center gap-4">
              <a
                href="/"
                class="text-white text-decoration-none small fw-semibold"
                >Trang chủ</a
              >
              <a href="/services" class="text-light text-decoration-none small"
                >Khám Phá</a
              >
              <a href="/posts" class="text-light text-decoration-none small"
                >Bản tin</a
              >
              <!-- Chỉ hiển thị khi đăng nhập là Host hoặc Admin -->
              <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role']
              === 'host'): ?>
              <a
                href="/host"
                class="text-info text-decoration-none small fw-semibold"
                >Quản lý</a
              >
              <?php elseif (!empty($_SESSION['user']) &&
              $_SESSION['user']['role'] === 'admin'): ?>
              <a
                href="/admin"
                class="text-warning text-decoration-none small fw-semibold"
                >Quản trị</a
              >
              <?php endif; ?>
            </nav>
          </div>

          <!-- Cột 3: Menu người dùng góc phải tối giản (1 khối pill duy nhất) -->
          <div class="col-auto">
            <?php if (!empty($_SESSION['user'])): ?>
            <div class="dropdown">
              <button
                class="glass-pill px-3 py-1 text-white d-flex align-items-center gap-2 border-0 dropdown-toggle"
                data-bs-toggle="dropdown"
              >
                <i class="bi bi-person-circle fs-5 text-info"></i>
                <span class="small fw-medium"
                  ><?= htmlspecialchars($_SESSION['user']['name']) ?></span
                >
              </button>
              <ul
                class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow"
              >
                <li>
                  <a class="dropdown-item small" href="/profile"
                    >Hồ sơ của tôi</a
                  >
                </li>
                <li>
                  <a class="dropdown-item small" href="/bookings"
                    >Chuyến đi của tôi</a
                  >
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item small text-danger" href="/logout"
                    >Đăng xuất</a
                  >
                </li>
              </ul>
            </div>
            <?php else: ?>
            <a
              href="/login"
              class="glass-pill px-3 py-1 text-white text-decoration-none d-flex align-items-center gap-2"
            >
              <i class="bi bi-person-circle fs-5 text-info"></i>
              <span class="small fw-medium">Đăng nhập</span>
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </header>
    ```

- **Footer tối giản**:
  - Đặt trong `<footer class="mt-5 py-4 border-top border-secondary border-opacity-25">` -> `div.container` -> `div.row.align-items-center.justify-content-between.text-center.text-md-start`:
  - Cột trái: Logo + `OnlineTraveller`.
  - Cột giữa: Liên kết ngang `Trang chủ | Khám Phá | Bản tin` và dòng chữ nhỏ `OOP + MVC, 15 bảng, 22 Stored Procedures`.
  - Cột phải: `© 2026 OnlineTraveller`.

---

### 3.2. Trang Chủ (`app/views/home/index.php`)

Toàn bộ nội dung bọc trong `<div class="main-container">`:

1. **Khối 1 - Hero Banner (`container-xxl pt-2`)**:
   - Sử dụng `.hero-ambient-wrapper` và `.hero-banner-box` với ảnh nền của trải nghiệm nổi bật nhất.
   - Bên dưới `.hero-banner-box` đặt `<div class="hero-ambient-glow" style="background-image: url('...')"></div>` để tạo ánh sáng chuyển tiếp mượt xuống nền tối.
   - Cấu trúc nội dung bên trong Banner đặt ở **BÊN TRÁI**:
     ```html
     <div class="row h-100 align-items-center pt-5 pb-4 px-lg-4">
       <div class="col-lg-6 col-md-8">
         <h1 class="font-editorial display-4 fw-bold text-white mb-3">
           <?= htmlspecialchars($featuredService['title']) ?>
         </h1>
         <p class="text-light opacity-75 mb-4">
           <?= htmlspecialchars($featuredService['short_description']) ?>
         </p>
         <a
           href="/services/<?= $featuredService['id'] ?>"
           class="btn btn-glass-hero"
         >
           Khám phá ngay &rarr;
         </a>
         <div class="small text-light opacity-75 mt-3">
           Host: <?= htmlspecialchars($featuredService['host_name']) ?> &bull;
           <?= htmlspecialchars($featuredService['location']) ?> &bull;
           <span class="text-warning"
             >&#9733; <?= number_format($featuredService['rating'], 1) ?></span
           >
         </div>
       </div>
     </div>
     ```

2. **Khối 2 - Thanh Tìm Kiếm Nổi (`container mt-4` - Tách biệt ngay dưới Banner, KHÔNG đè lên Banner)**:
   - Cấu trúc chuẩn:
     `div.container.mt-4` -> `form.glass-panel.p-3` -> `div.row.g-3.align-items-center`:
     - `div.col-lg-4.col-md-4`: Nhãn `Điểm đến?` + Ô input không viền trong suốt (`Tìm địa danh`).
     - `div.col-lg-3.col-md-3`: Nhãn `Danh mục` + Select dropdown (`Văn hóa, ẩm thực`).
     - `div.col-lg-3.col-md-3`: Nhãn `Ngân sách` + Select dropdown (`Khoảng giá`).
     - `div.col-lg-2.col-md-2`: Nút submit `.btn-alpine.w-100.py-2` (`Tìm kiếm`).

3. **Khối 3 - Thời Tiết Điểm Đến Hôm Nay (`container mt-5`)**:
   - `div.row.mb-3` -> `div.col-12`: `<h4 class="fw-bold">Thời Tiết Điểm Đến Hôm Nay</h4>`.
   - `div.row.row-cols-2.row-cols-md-3.row-cols-lg-5.g-3`:
     - Lặp qua 5 địa điểm (Đà Lạt, Sa Pa, Hà Giang, Hội An, Phú Quốc), mỗi cột `div.col` chứa một thẻ `.glass-panel.p-3.h-100` tách biệt:
       - Tên địa danh (`fw-semibold`).
       - Nhiệt độ lớn (`fs-2 fw-bold text-info` — VD: `18°C`) kèm icon thời tiết.
       - Dòng mô tả ngắn (`small text-light opacity-75` — VD: `se lạnh, sương mù`).

4. **Khối 4 - Khám Phá Địa Phương + Nút Mở Rộng Tag + Lưới 4 Card (`container mt-5`)**:
   - **Hàng tiêu đề & Nút mở rộng Tag**:
     ```html
     <div class="row align-items-center justify-content-between mb-3">
       <div class="col-auto">
         <h3 class="fw-bold mb-0">Khám Phá Địa Phương</h3>
       </div>
       <div class="col-auto">
         <button
           class="btn btn-alpine btn-sm px-3 py-2"
           type="button"
           data-bs-toggle="collapse"
           data-bs-target="#topicTagsCollapse"
         >
           Tìm theo chủ đề &#9662;
         </button>
       </div>
     </div>
     ```
   - **Hàng Collapse chứa các Tag chủ đề (`div.collapse.show#topicTagsCollapse`)**:
     - `div.row.mb-4` -> `div.col-12.d-flex.flex-wrap.gap-2`: Hiển thị các nút lọc `.glass-pill.px-3.py-1.small` (`Tất cả`, `Ẩm thực bản địa`, `Săn mây & Trekking`, `Làng nghề di sản`, `Homestay nhà dân`).
   - **Hàng Lưới 4 Card Trải Nghiệm tràn ảnh (`div.row.row-cols-1.row-cols-sm-2.row-cols-lg-4.g-4`)**:
     - Mỗi `div.col` chứa một `.experience-poster-card`:
       - Ảnh bìa trải nghiệm phủ kín thẻ.
       - Góc trên phải: Badge `.glass-pill.px-2.py-1.small` hiển thị `★ 4.9`.
       - Góc dưới trái (trong `.experience-poster-overlay`):
         - Địa điểm in đậm (`fw-bold fs-6 text-white`).
         - Tên trải nghiệm rút gọn 1 dòng (`small text-light opacity-75 text-truncate`).
         - Dòng giá tiền nổi bật (`fw-bold text-info` — VD: `Giá 450k`).

5. **Khối 5 - Tin Tức Du Lịch RSS (`container my-5`)**:
   - Bọc trong `div.glass-panel.p-4.p-lg-5`:
   - `div.row.align-items-center.g-4`:
     - **Cột trái (`div.col-lg-4`)**:
       - Nhãn nhỏ: `<div class="small text-info fw-semibold text-uppercase mb-1">Cẩm nang & Bản tin RSS</div>`
       - Tiêu đề: `<h3 class="fw-bold mb-2">Tin tức du lịch</h3>`
       - Mô tả: `<p class="small text-light opacity-75 mb-4">Nhịp sống bản địa, kinh nghiệm xê dịch và những vấn đề du lịch đáng quan tâm nhất hôm nay.</p>`
       - Nút: `<a href="/posts" class="btn btn-glass-hero btn-sm px-4">Xem thêm tin &rarr;</a>`
     - **Cột phải (`div.col-lg-8`)**:
       - `div.row.row-cols-1.row-cols-md-2.g-4`: Chỉ lấy **2 bài tin mới nhất** (`array_slice($posts, 0, 2)`), mỗi bài nằm trong 1 Card nền sáng (`bg-white text-dark rounded-4 overflow-hidden shadow-sm h-100`) gồm ảnh ngang ở trên, tiêu đề bài viết in đậm và dòng `VnExpress RSS • 5 phút đọc` ở dưới.

---

### 3.3. Trang Chi Tiết Trải Nghiệm (`app/views/services/show.php`)

Toàn bộ nội dung bọc trong `<div class="main-container">`, thiết kế theo đúng bố cục mẫu _Japan Tours_:

1. **Khối 1 - Hero Banner & Dải Slider Ảnh Các Trạm (`container-xxl pt-2`)**:
   - Khung `.hero-ambient-wrapper` hiển thị ảnh bìa lớn của tour kèm lớp phủ `.hero-banner-overlay` và `.hero-ambient-glow`.
   - Phần chữ nằm bên trái:
     - Tiêu đề lớn: `<h1 class="font-editorial display-4 fw-bold text-white"><?= htmlspecialchars($service['title']) ?></h1>`
     - Dòng thông tin phụ ngay dưới tiêu đề: `Host: <?= htmlspecialchars($service['host_name']) ?> (<?= htmlspecialchars($service['living_area']) ?>) • ★ <?= number_format($service['rating'], 1) ?>`.
   - **Dải Slider ảnh các trạm & Nút Đặt lịch ngay (Nằm ở phần chân Banner)**:
     ```html
     <div class="row align-items-end justify-content-between mt-4 pt-2">
       <!-- Cột trái: Dải 4 thẻ ảnh dọc thu nhỏ của các trạm -->
       <div class="col-lg-8">
         <div class="row row-cols-2 row-cols-sm-4 g-3">
           <?php foreach (array_slice($stations, 0, 4) as $station): ?>
           <div class="col">
             <div
               class="rounded-4 overflow-hidden border border-light border-opacity-25 shadow"
               style="aspect-ratio: 3/4;"
             >
               <img
                 src="<?= htmlspecialchars($station['image_url']) ?>"
                 class="w-100 h-100 object-fit-cover"
                 alt=""
               />
             </div>
           </div>
           <?php endforeach; ?>
         </div>
       </div>
       <!-- Cột phải: Nút cuộn nhanh xuống khung Đặt chỗ -->
       <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
         <a href="#bookingSection" class="btn btn-glass-hero px-4 py-2"
           >Đặt Lịch Ngay</a
         >
       </div>
     </div>
     ```

2. **Khối 2 - Hành Trình & Các Trạm Trải Nghiệm (`container my-5 pt-4` - Tương ứng phần "ABOUT THE TOUR")**:
   - **Hàng tiêu đề**:
     ```html
     <div class="row mb-5">
       <div class="col-12">
         <h3 class="section-divider-title">
           Hành Trình & Các Trạm Trải Nghiệm
         </h3>
       </div>
     </div>
     ```
   - **Hàng nội dung 2 cột (`div.row.g-5.align-items-start`)**:
     - **Cột Trái (`div.col-lg-5`) — Gồm 3 phần nối tiếp nhau**:
       1. **Mô tả hành trình**: Đoạn văn giới thiệu chi tiết câu chuyện trải nghiệm của người dân bản địa (`text-light opacity-75 lh-lg`).
       2. **Thẻ Homestay Nhà Dân Bản Địa (`.glass-panel.p-3.mt-4`)**:
          - `div.row.g-3.align-items-center`: Cột ảnh nhỏ bên trái (`col-4`) + Cột thông tin bên phải (`col-8`) hiển thị tiêu đề `Homestay Nhà Dân Bản Địa`, tên nhà dân (`homestay_name`), loại phòng (`room_type`), số đêm (`nights`) và địa chỉ.
       3. **Thẻ Thời Tiết OpenWeather + Bản Đồ Google Maps (`.glass-panel.p-0.mt-4.overflow-hidden`)**:
          - `div.row.g-0`:
            - `div.col-5.p-3`: Hiển thị icon thời tiết, nhiệt độ lớn (`18°C`), tên địa phương và trạng thái thời tiết từ `OpenWeatherClient`.
            - `div.col-7`: Nhúng khung bản đồ Google Maps (`#serviceGoogleMap` hoặc iframe theo tọa độ `latitude, longitude` của tour) chiều cao tối thiểu `150px`.
     - **Cột Phải (`div.col-lg-7`) — Trục dọc Timeline các Trạm + Nút Trải Nghiệm 3D**:
       - Bọc danh sách các trạm trong `<div class="station-timeline">`:
         ```html
         <?php foreach ($stations as $index =>
         $station): ?>
         <div class="station-timeline-item">
           <span class="station-timeline-dot"></span>
           <div class="glass-panel p-3">
             <div class="row g-3 align-items-center">
               <div class="col-sm-4">
                 <img
                   src="<?= htmlspecialchars($station['image_url']) ?>"
                   class="w-100 rounded-3 object-fit-cover"
                   style="height: 110px;"
                   alt=""
                 />
               </div>
               <div class="col-sm-8">
                 <h6 class="fw-bold text-white mb-1">
                   Trạm <?= $index + 1 ?>: <?=
                   htmlspecialchars($station['caption']) ?>
                 </h6>
                 <p class="small text-light opacity-75 mb-0">
                   <?= htmlspecialchars($station['story_text']) ?>
                 </p>
               </div>
             </div>
           </div>
         </div>
         <?php endforeach; ?>
         ```
       - **Nút mở Không Gian 3D ở cuối trục Timeline**:
         ```html
         <div class="mt-4">
           <button
             type="button"
             class="btn btn-alpine w-100 py-3 fs-6 shadow-lg"
             data-bs-toggle="modal"
             data-bs-target="#virtualTour3DModal"
           >
             &#10024; Trải Nghiệm Không Gian 3D (Z-Scroll) &rarr;
           </button>
         </div>
         ```
   - **Modal / Khung Trình Chiếu 3D Three.js (`#virtualTour3DModal`)**:
     - Khi người dùng bấm nút trên, Modal cỡ lớn (`modal-xl` hoặc `modal-fullscreen-lg-down`) mở ra, khởi tạo Canvas Three.js (`virtual-tour-3d.js`) cho phép lăn chuột bay dọc trục `Z` qua từng bức ảnh trạm, hiển thị hộp câu chuyện (`caption`, `story_text`), nút xem video thực tế (`video_url`) và 2 nút điều hướng `◀ Trạm Trước` / `Trạm Kế Tiếp ▶`.

3. **Khối 3 - Kết Nối Trực Tiếp & Đặt Lịch (`container my-5 pt-3#bookingSection`)**:
   - **Hàng tiêu đề**:
     ```html
     <div class="row mb-4">
       <div class="col-12">
         <h3 class="section-divider-title">Kết Nối Trực Tiếp & Đặt Lịch</h3>
       </div>
     </div>
     ```
   - **Hàng nội dung 2 cột (`div.row.g-4`)**:
     - **Cột Trái (`div.col-lg-7`) — Kênh Hỏi Đáp Trực Tiếp Từ Xa (`tbl_discussions`)**:
       - Đặt trong `.glass-panel.p-4.h-100`:
       - Tiêu đề `Kênh Hỏi Đáp Trực Tiếp Từ Xa`.
       - Form gửi câu hỏi mới (`textarea` + nút gửi câu hỏi tới Host).
       - Danh sách các bình luận hỏi đáp bên dưới (hiện Avatar, tên người hỏi, nội dung câu hỏi và phản hồi thụt lề 1 cấp của Host bản địa).
     - **Cột Phải (`div.col-lg-5`) — Khung Đặt Chỗ (`Booking Box`)**:
       - Đặt trong `.glass-panel.p-4.h-100`:
       - Tiêu đề `Đặt Chỗ`.
       - Giá niêm yết nổi bật: `<div class="fs-2 fw-bold text-info mb-3"><?= number_format($service['base_price'], 0, ',', '.') ?>đ <span class="fs-6 fw-normal text-light opacity-75">/ người</span></div>`.
       - Form/Dropdown chọn đợt khởi hành (`tbl_service_schedules`) hiển thị ngày giờ và số chỗ còn trống.
       - Nút hành động chính: `<a href="/bookings/create?service_id=<?= $service['id'] ?>" class="btn btn-alpine w-100 py-3 mt-3">Đặt Chỗ Trực Tuyến Ngay</a>`.
