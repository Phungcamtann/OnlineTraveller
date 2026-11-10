# ĐẶC TẢ ĐẠI TRÙNG TU GIAO DIỆN TOÀN HỆ THỐNG (ONLINETRAVELLER v3.0)

## 1. NGUYÊN TẮC CỐT LÕI (BẮT BUỘC TUÂN THỦ)
1. **BỎ HOÀN TOÀN TÔNG MÀU XANH CŨ**: Xóa bỏ việc ép nền xanh đen `#0B1924` và các khung xanh `#184E6C`, `#387EA2` đang làm giao diện thiếu đồng bộ.
2. **CHẾ ĐỘ SÁNG / TỐI (DAY MODE & NIGHT MODE) — MẶC ĐỊNH LÀ DAY MODE**:
   - Mặc định khi vào web là **Day Mode (`data-theme="light"`)** mang phong cách sáng kem thanh lịch (mẫu *Roamora*).
   - Khi gạt nút sang **Night Mode (`data-theme="dark"`)**, toàn bộ hệ thống chuyển sang nền đen khói điện ảnh (`#0E0E10` - mẫu *Japan Tours*).
   - Lưu trạng thái vào `localStorage.getItem('ot_theme')` để đồng bộ xuyên suốt **TẤT CẢ các trang** (Trang chủ, Khám phá, Chi tiết, Đặt chỗ, Bản tin, Hồ sơ, Đăng nhập/Đăng ký) và cả **Modal thông báo (`notification-modal.php`)**.
3. **QUY TẮC LƯỚI BOOTSTRAP 5**: Mọi section đều phải nằm gọn trong cấu trúc `div.main-container` -> `div.container` (hoặc `container-xxl`) -> `div.row` -> `div.col-*`. Không để bất kỳ khối nào (đặc biệt là khối Đánh giá cuối trang chi tiết) bị tràn ra ngoài `.container`.

---

## 2. HỆ THỐNG CSS TOÀN CỤC & NÚT GẠT DAY/NIGHT MODE (`public/assets/css/custom-theme.css`)

```css
/* ==========================================================================
   1. HỆ BIẾN MÀU TRUNG TÍNH ĐỒNG BỘ (DAY MODE MẶC ĐỊNH & NIGHT MODE)
   ========================================================================== */
:root,
[data-theme="light"] {
    --bg-body: #FAF9F6;                  /* Nền kem ấm Roamora (Day Mode) */
    --bg-surface: #FFFFFF;               /* Nền thẻ, thanh search, modal */
    --bg-surface-subtle: #F3F2EE;        /* Nền phụ nhẹ */
    --bg-glass: rgba(255, 255, 255, 0.86);
    --text-main: #18181B;                /* Chữ chính đen than */
    --text-muted: #64748B;               /* Chữ phụ xám trung tính */
    --border-subtle: rgba(24, 24, 27, 0.12);
    --border-strong: rgba(24, 24, 27, 0.25);
    --btn-primary-bg: #18181B;           /* Nút bấm chính màu đen tuyền */
    --btn-primary-text: #FFFFFF;
    --btn-primary-hover: #3F3F46;
    --timeline-line: rgba(24, 24, 27, 0.22);
    --timeline-dot: #18181B;
    --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.06);
}

[data-theme="dark"] {
    --bg-body: #0E0E10;                  /* Nền đen khói Japan Tours (Night Mode) */
    --bg-surface: #18181B;               /* Nền thẻ tối trung tính */
    --bg-surface-subtle: #222226;
    --bg-glass: rgba(24, 24, 27, 0.82);
    --text-main: #F4F4F5;                /* Chữ chính trắng ngà */
    --text-muted: #A1A1AA;               /* Chữ phụ xám bạc */
    --border-subtle: rgba(255, 255, 255, 0.14);
    --border-strong: rgba(255, 255, 255, 0.3);
    --btn-primary-bg: #F4F4F5;           /* Nút bấm chính màu trắng ngà */
    --btn-primary-text: #0E0E10;
    --btn-primary-hover: #E4E4E7;
    --timeline-line: rgba(255, 255, 255, 0.28);
    --timeline-dot: #FFFFFF;
    --shadow-soft: 0 12px 32px rgba(0, 0, 0, 0.45);
}

body {
    background-color: var(--bg-body) !important;
    color: var(--text-main) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Đồng bộ Card, Surface & Modal Thông báo toàn hệ thống */
.surface-card,
.glass-panel,
.modal-content {
    background-color: var(--bg-surface) !important;
    color: var(--text-main) !important;
    border: 1px solid var(--border-subtle) !important;
    border-radius: 18px;
    box-shadow: var(--shadow-soft);
}

/* Nút bấm chủ đạo đồng bộ Sáng/Tối */
.btn-theme-primary,
.btn-alpine {
    background-color: var(--btn-primary-bg) !important;
    color: var(--btn-primary-text) !important;
    border: 1px solid var(--btn-primary-bg) !important;
    border-radius: 999px;
    font-weight: 600;
    padding: 0.6rem 1.5rem;
    transition: all 0.25s ease;
}
.btn-theme-primary:hover,
.btn-alpine:hover {
    background-color: var(--btn-primary-hover) !important;
    transform: translateY(-1px);
}

/* ==========================================================================
   2. THANH TOP-NAV NỔI BÁM THEO MÀN HÌNH KHI CUỘN (FIXED FLOATING HEADER)
   ========================================================================== */
.top-floating-header {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1050;
    padding: 0.9rem 0;
    transition: all 0.3s ease;
}

.top-floating-header.navbar-scrolled {
    padding: 0.55rem 0;
    background: var(--bg-glass);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border-subtle);
    box-shadow: var(--shadow-soft);
}

/* ==========================================================================
   3. NÚT CHUYỂN CHẾ ĐỘ DAY MODE / NIGHT MODE (ĐÚNG MẪU ẢNH GỬI)
   ========================================================================== */
.theme-mode-switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 138px;
    height: 38px;
    border-radius: 999px;
    padding: 3px;
    cursor: pointer;
    user-select: none;
    border: 1px solid var(--border-subtle);
    background-color: #E5E7EB; /* Nền xám sáng ở Day Mode */
    color: #111827;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.theme-mode-switch .switch-label {
    width: 100%;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding-left: 14px;
    padding-right: 38px;
    text-align: left;
    transition: all 0.35s ease;
}

.theme-mode-switch .switch-knob {
    position: absolute;
    right: 3px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #FFFFFF;
    color: #111827;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.18);
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Khi ở chế độ NIGHT MODE: Nền đen, nút tròn trắng nhảy sang trái, chữ sang phải */
[data-theme="dark"] .theme-mode-switch {
    background-color: #09090B;
    color: #FFFFFF;
    border-color: rgba(255, 255, 255, 0.25);
}

[data-theme="dark"] .theme-mode-switch .switch-label {
    padding-left: 38px;
    padding-right: 12px;
    text-align: right;
}

[data-theme="dark"] .theme-mode-switch .switch-knob {
    right: calc(100% - 33px);
    background-color: #FFFFFF;
    color: #09090B;
}

/* ==========================================================================
   4. ADAPTIVE AMBIENT GLOW (TỰ ĐỘNG TỎA MÀU THEO BỨC ẢNH BANNER)
   ========================================================================== */
.hero-ambient-glow {
    position: absolute;
    bottom: -65px;
    left: 4%;
    width: 92%;
    height: 150px;
    background-size: cover;
    background-position: center bottom;
    filter: blur(80px);
    opacity: 0.38;
    z-index: -1;
    transition: background-image 0.5s ease-in-out;
    pointer-events: none;
}

/* ==========================================================================
   5. TIMELINE NGHỆ THUẬT SO LE TRÁI-PHẢI (CHUẨN MẪU JAPAN TOURS)
   ========================================================================== */
.japan-timeline {
    position: relative;
    padding: 1rem 0;
}
/* Đường kẻ dọc mảnh chạy ở trục giữa cột phải */
.japan-timeline::before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: 50%;
    width: 1px;
    background-color: var(--timeline-line);
    transform: translateX(-50%);
}

.japan-timeline-step {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3.5rem;
}

.japan-timeline-dot {
    position: absolute;
    left: 50%;
    top: 24px;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background-color: var(--timeline-dot);
    transform: translateX(-50%);
    z-index: 2;
}

.japan-timeline-col {
    width: 45%;
}

/* Ô viền mảnh phần "THÔNG TIN & TIỆN ÍCH BẢN ĐỊA" (Chuẩn mẫu What's Included) */
.included-outline-box {
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 1.35rem;
    height: 100%;
    background: transparent;
    transition: border-color 0.25s ease, background-color 0.25s ease;
}
.included-outline-box:hover {
    background-color: var(--bg-surface);
}
```

---

## 3. CẬP NHẬT THANH TOP-NAV & SCRIPT CHUYỂN DAY/NIGHT MODE (`app/views/layouts/main.php`)

1. **Mặc định thẻ `<html>` luôn khởi tạo `data-theme="light"` (Day Mode)** nếu người dùng chưa từng chọn:
   ```html
   <script>
       (function() {
           const savedTheme = localStorage.getItem('ot_theme') || 'light';
           document.documentElement.setAttribute('data-theme', savedTheme);
       })();
   </script>
   ```
2. **Đổi `<header>` sang class `.top-floating-header`** (luôn nổi và bám theo màn hình khi cuộn).
3. **Đặt Nút Gạt `DAY MODE / NIGHT MODE` ở cụm bên phải Top-Nav** (ngay cạnh nút User Pill):
   ```html
   <div class="col-auto d-flex align-items-center gap-2">
       <!-- Nút chuyển đổi DAY MODE / NIGHT MODE đúng mẫu -->
       <button type="button" id="themeToggleSwitch" class="theme-mode-switch" aria-label="Chuyển chế độ Sáng/Tối">
           <span class="switch-label" id="themeSwitchText">DAY MODE</span>
           <span class="switch-knob" id="themeSwitchIcon">
               <i class="bi bi-sun"></i>
           </span>
       </button>

       <!-- User Pill hiện tại giữ nguyên -->
   </div>
   ```
4. **Script xử lý cuộn Navbar & Nút gạt Day/Night Mode**:
   ```javascript
   document.addEventListener('DOMContentLoaded', () => {
       const htmlEl = document.documentElement;
       const toggleBtn = document.getElementById('themeToggleSwitch');
       const labelEl = document.getElementById('themeSwitchText');
       const iconEl = document.getElementById('themeSwitchIcon');
       const headerEl = document.querySelector('.top-floating-header');

       function syncSwitchUI(theme) {
           if (theme === 'dark') {
               labelEl.textContent = 'NIGHT MODE';
               iconEl.innerHTML = '<i class="bi bi-moon-stars"></i>';
           } else {
               labelEl.textContent = 'DAY MODE';
               iconEl.innerHTML = '<i class="bi bi-sun"></i>';
           }
       }

       syncSwitchUI(htmlEl.getAttribute('data-theme') || 'light');

       if (toggleBtn) {
           toggleBtn.addEventListener('click', () => {
               const nextTheme = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
               htmlEl.setAttribute('data-theme', nextTheme);
               localStorage.setItem('ot_theme', nextTheme);
               syncSwitchUI(nextTheme);
           });
       }

       window.addEventListener('scroll', () => {
           if (window.scrollY > 30) {
               headerEl?.classList.add('navbar-scrolled');
           } else {
               headerEl?.classList.remove('navbar-scrolled');
           }
       });
   });
   ```

---

## 4. ĐẠI TU BANNER TRANG CHỦ (`app/views/home/index.php` — CHUẨN MẪU ROAMORA)
- **Giữ nguyên** các Card trải nghiệm và Card tin tức hiện tại vì đã ổn.
- **Sửa lại Hero Banner (`#homeHeroAmbientWrapper`)**:
  - **XÓA BỎ dải `#bannerThumbsContainer` (các ô thumbnail nhỏ bên dưới banner)** đang làm rối bố cục.
  - Mở rộng chiều cao Banner thoáng đãng (`min-height: 75vh; border-radius: 24px;`).
  - Khối chữ nằm bên trái: Nhãn địa điểm nhỏ, Tiêu đề lớn của Tour, mô tả ngắn 2 dòng, nút viên thuốc **`Khám phá ngay →`** và dòng thông tin nhỏ bên dưới (`Host: ... • Địa điểm • ★ 5.0`).
  - **Nút chuyển Slide**: Chỉ đặt **2 nút mũi tên tròn nhỏ (`<` và `>`)** ở **góc dưới bên phải** của Banner. Khi bấm mũi tên, chuyển đổi mượt mà sang Banner của tour nổi bật tiếp theo (cập nhật đồng thời ảnh của `#heroBannerBox` và `#heroAmbientGlow`).
  - **Thanh Tìm Kiếm (`#homeSearchForm`)**: Đặt vắt ngang mép dưới của Banner (`margin-top: -42px; position: relative; z-index: 10;`), sử dụng class `.surface-card.rounded-pill.px-4.py-3` với nút `Tìm kiếm 🔍` bo tròn bên phải giống hệt mẫu *Roamora*.

---

## 5. ĐẠI TU TRANG CHI TIẾT TOUR (`app/views/services/show.php` — CHUẨN MẪU JAPAN TOURS)

Sắp xếp lại Trang Chi Tiết theo đúng **4 Khối** từ trên xuống dưới (nằm gọn trong `.container`):

### KHỐI 1: HERO BANNER + CỘT MỐC ĐỔI ẢNH NỀN (INTERACTIVE BANNER SWITCHER)
- **XÓA BỎ cột phải `vertical-station-card` đang che kín mặt ảnh Banner hiện tại**.
- Ảnh nền `#detailHeroBanner` hiển thị rộng toàn khung (`min-height: 76vh; border-radius: 24px;`), không bị bất kỳ khối dọc nào che ở góc phải.
- Phần nửa trên hiển thị Tên địa danh / Tiêu đề Tour lớn và thông tin Host.
- Phần dưới chân Banner (`row align-items-end justify-content-between mt-auto pt-4`):
  - **Bên trái (`col-lg-8`)**: Hàng ngang gồm **4–5 thẻ ảnh dọc nhỏ (`aspect-ratio: 3/4.2; max-width: 115px; cursor: pointer;`)** lấy từ ảnh bìa chính và ảnh các trạm dừng (`$stations`), giống hệt 5 ô ảnh dọc trên mẫu *Japan Tours*. Mỗi thẻ hiển thị ảnh + tên trạm ngắn gọn ở đáy thẻ.
  - **Cơ chế tương tác bắt buộc**: Khi người dùng **CLICK vào bất kỳ thẻ ảnh cột mốc nào**, JavaScript sẽ **đổi ngay ảnh nền của `#detailHeroBanner` và `#detailAmbientGlow` sang bức ảnh của cột mốc đó** (và đổi class `.active` viền sáng cho thẻ vừa bấm). Tuyệt đối không mở popup 3D khi bấm vào các thẻ này.
  - **Bên phải (`col-lg-4 text-lg-end`)**: Đặt gọn nút **`Đặt lịch ngay`** (giống nút `Book` bên phải trên mẫu *Japan Tours*) cuộn mượt xuống phần đặt lịch.

### KHỐI 2: HÀNH TRÌNH & CÁC TRẠM TRẢI NGHIỆM (CHUẨN BỐ CỤC "ABOUT THE TOUR" CỦA JAPAN TOURS)
```
+---------------------------------------------------------------------------------------------------+
|                        ──────── HÀNH TRÌNH & CÁC TRẠM TRẢI NGHIỆM ────────                        |
|                                                                                                   |
|  [CỘT TRÁI - col-lg-5: CHỈ CHỨA MÔ TẢ TOUR]   |  [CỘT PHẢI - col-lg-7: SƠ ĐỒ TRỤC DỌC SO LE]     |
|                                               |                                                   |
|  Đoạn 1: Giới thiệu tổng quan về chuyến đi    |                    Trạm 1 ●                       |
|  cùng người dân bản địa, lịch trình thuận     |     [Ảnh Trạm 1]     Núi 1 / Bình minh            |
|  tiện khám phá trọn vẹn văn hóa địa phương... |                            |                      |
|                                               |                            ● Trạm 2               |
|  (Khoảng nghỉ thoáng giống mẫu Japan Tours)   |                            | Vườn hoa Đà Lạt      |
|                                               |                            |   [Ảnh Trạm 2]       |
|  Đoạn 2: Bạn không cần lo lắng về đường đi    |                            |                      |
|  hay lịch trình — người dân bản địa sẽ dẫn    |                    Trạm 3 ●                       |
|  bạn tới những góc đẹp nhất, thưởng thức      |     [Ảnh Trạm 3]     Thác Đà Lạt                  |
|  ẩm thực chuẩn vị và tận hưởng chuyến đi.     |                                                   |
|                                               |      [ ✨ Trải Nghiệm Không Gian 3D (Z-Scroll) ]   |
+---------------------------------------------------------------------------------------------------+
```
- **Cột Trái (`col-lg-5 d-flex flex-column justify-content-between py-3`)**:
  - **CHỈ HIỂN THỊ PHẦN MÔ TẢ CỦA TOUR** (gồm đoạn mô tả mở đầu `$service['short_description']` ở phần trên và đoạn mô tả chi tiết `$service['description']` / lời nhắn từ Host ở phần dưới, ở giữa có khoảng nghỉ thoáng đúng như mẫu *Japan Tours*).
  - **Di chuyển** khối Homestay, Thời tiết và Bản đồ Google Maps xuống **Khối 3** bên dưới.
- **Cột Phải (`col-lg-7`)**:
  - **Xóa bỏ 4 cái hộp viền chữ nhật đang nhốt các trạm hiện tại**.
  - Dựng trục dọc `.japan-timeline` chạy ở giữa cột phải. Với mỗi trạm (`$index => $station`):
    - Chấm tròn `.japan-timeline-dot` nằm ngay trên đường kẻ dọc.
    - Các trạm chẵn/lẻ hiển thị **so le (xen kẽ trái – phải)** quanh trục dọc: Nhãn `Trạm <?= $index + 1 ?>` + **Tên trạm in đậm** + **Mô tả ngắn của trạm** + **Ảnh thực tế bo góc nhẹ (`border-radius: 10px`, cao `130px–150px`)** giống hệt sơ đồ *Osaka -> Kyoto -> Tokyo* trong ảnh mẫu *Japan Tours*.
  - Dưới chân trục Timeline đặt nút bấm `.btn-theme-primary`: **`✨ Trải Nghiệm Không Gian 3D (Z-Scroll) →`**.

### KHỐI 3: TIỆN ÍCH & THÔNG TIN BẢN ĐỊA (NẰM NGAY DƯỚI MÔ TẢ & CÁC TRẠM — TRÊN PHẦN KẾT NỐI & ĐẶT LỊCH)
Áp dụng đúng thiết kế hàng ngang 4 ô viền mảnh của mục **"WHAT'S INCLUDED"** trong mẫu *Japan Tours*:
```
+---------------------------------------------------------------------------------------------------+
|  TIỆN ÍCH & THÔNG TIN BẢN ĐỊA ──────────────────────────────────────────────────────────────────  |
|                                                                                                   |
|  +-----------------------+  +-----------------------+  +-----------------------+  +-------------+ |
|  | 👤 Dân Bản Địa Dẫn Đường|  | 🏠 Homestay Nhà Dân   |  | ⛅ Thời Tiết Hiện Tại  |  | 📍 Bản Đồ   | |
|  | Host: Nguyễn Văn A    |  | Phòng riêng • 1 đêm   |  | 18°C • Chợ Đà Lạt     |  | [Google Map]| |
|  | Khu vực: Đà Lạt       |  | Bao gồm ăn sáng, wifi |  | Se lạnh, sương mù nhẹ |  | Xem vị trí  | |
|  +-----------------------+  +-----------------------+  +-----------------------+  +-------------+ |
+---------------------------------------------------------------------------------------------------+
```
- Cấu trúc chuẩn Bootstrap 5 (`container my-5`):
  - Tiêu đề có đường kẻ ngang bên phải: `TIỆN ÍCH & THÔNG TIN BẢN ĐỊA ──────────`.
  - Lưới 4 cột bằng nhau (`row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4`):
    1. **Ô 1 (`.included-outline-box` - Dân Bản Địa Đồng Hành)**: Icon người bản xứ, Tên Host, Khu vực sinh sống, Thời lượng tour & Số khách tối đa.
    2. **Ô 2 (`.included-outline-box` - Lưu Trú Homestay Nhà Dân)**: Tên Homestay (`homestay_name`), Loại phòng, Số đêm, Tiện nghi đi kèm (kèm thumbnail nhỏ gọn).
    3. **Ô 3 (`.included-outline-box` - Thời Tiết Điểm Đến Thực Tế)**: Nhiệt độ lớn (`18°C`) từ OpenWeather JSON API, tình trạng thời tiết và lời khuyên chuẩn bị trang phục từ Host.
    4. **Ô 4 (`.included-outline-box` - Vị Trí Bản Đồ Google Maps)**: Khung Google Maps tương tác bo góc gọn gàng bên trong ô kèm link *"Mở rộng trên Google Maps"*.

### KHỐI 4: KẾT NỐI TRỰC TIẾP & ĐẶT LỊCH + KHỐI ĐÁNH GIÁ CUỐI TRANG (FIX TRÀN LỀ)
- Ngay dưới Khối 3 là mục **`KẾT NỐI TRỰC TIẾP & ĐẶT LỊCH`** (Cột trái `col-lg-7` là Hỏi đáp cộng đồng; Cột phải `col-lg-5` là Chọn lịch & Đặt chỗ).
- **Sửa dứt điểm lỗi khối Đánh Giá (Reviews) bị nhảy tràn ra 2 bên**:
  - Kiểm tra thẻ đóng `</div>` của khối trên và đảm bảo toàn bộ phần Đánh giá cuối trang (`reviews`) nằm gọn bên trong `<section class="container my-5"><div class="row"><div class="col-12">...</div></div></section>`, thẳng hàng 100% với các khối phía trên.