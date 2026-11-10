# TÀI LIỆU TOÀN DIỆN VỀ THIẾT KẾ GIAO DIỆN (UI/UX LAYOUT SPECIFICATION)
## HỆ THỐNG DU LỊCH & TRẢI NGHIỆM BẢN ĐỊA — ONLINETRAVELLER v2.2

> **Phiên bản:** 2.2  
> **Cập nhật:** Tháng 11/2026  
> **Mục đích:** Tài liệu kỹ thuật chi tiết phục vụ việc rà soát, đồng bộ và tinh chỉnh toàn bộ giao diện (UI) của hệ thống OnlineTraveller.

---

## 📑 MỤC LỤC
1. [Hệ Thống Màu Sắc & Typography Toàn Hệ Thống](#1-hệ-thống-màu-sắc--typography-toàn-hệ-thống)
2. [Nguyên Tắc Bố Cục & Ngôn Ngữ Thiết Kế Dùng Chung](#2-nguyên-tắc-bố-cục--ngôn-ngữ-thiết-kế-dùng-chung)
3. [Khung Layout Toàn Cục (Global Layout & Components)](#3-khung-layout-toàn-cục-global-layout--components)
4. [Giao Diện Du Khách & Người Dùng Đại Chúng (Tourist & Public Facing)](#4-giao-diện-du-khách--người-dùng-đại-chúng-tourist--public-facing)
   - 4.1. [Trang Chủ (Home Page - `/`)](#41-trang-chủ-home-page---)
   - 4.2. [Trang Khám Phá Trải Nghiệm (Services Directory - `/services`)](#42-trang-khám-phá-trải-nghiệm-services-directory---services)
   - 4.3. [Trang Chi Tiết Trải Nghiệm (Experience Detail - `/services/{id}`)](#43-trang-chi-tiết-trải-nghiệm-experience-detail---servicesid)
   - 4.4. [Trang Đặt Chỗ & Báo Giá (Booking Create - `/bookings/create`)](#44-trang-đặt-chỗ--báo-giá-booking-create---bookingscreate)
   - 4.5. [Trang Chi Tiết Đơn Đặt Chỗ & Bản In (Booking Detail - `/bookings/{id}`)](#45-trang-chi-tiết-đơn-đặt-chỗ--bản-in-booking-detail---bookingsid)
   - 4.6. [Trang Chuyến Đi Của Tôi (My Bookings - `/my-bookings`)](#46-trang-chuyến-đi-của-tôi-my-bookings---my-bookings)
   - 4.7. [Trang Cổng Giả Lập MoMo Sandbox QR (`/payments/checkout`)](#47-trang-cổng-giả-lập-momo-sandbox-qr-paymentscheckout)
   - 4.8. [Trang Kết Quả Giao Dịch Callback (`/payments/callback`)](#48-trang-kết-quả-giao-dịch-callback-paymentscallback)
   - 4.9. [Trang Hóa Đơn Điện Tử E-Receipt (`/receipts/{id}`)](#49-trang-hóa-đơn-điện-tử-e-receipt-receiptsid)
   - 4.10. [Trang Phiếu Theo Dõi Hoàn Tiền (`/refunds/{id}`)](#410-trang-phiếu-theo-dõi-hoàn-tiền-refundsid)
   - 4.11. [Trang Bản Tin & Cẩm Nang Du Lịch (`/posts`)](#411-trang-bản-tin--cẩm-nang-du-lịch-posts)
   - 4.12. [Trang Chi Tiết Bài Viết (`/posts/{slug}`)](#412-trang-chi-tiết-bài-viết-postsslug)
   - 4.13. [Trang Soạn Thảo & Chia Sẻ Bài Viết Mới (`/posts/create`)](#413-trang-soạn-thảo--chia-sẻ-bài-viết-mới-postscreate)
   - 4.14. [Trang Hồ Sơ Cá Nhân (`/profile`)](#414-trang-hồ-sơ-cá-nhân-profile)
   - 4.15. [Trang Đăng Nhập (`/login`)](#415-trang-đăng-nhập-login)
   - 4.16. [Trang Đăng Ký Tài Khoản (`/register`)](#416-trang-đăng-ký-tài-khoản-register)
5. [Giao Diện Bàn Làm Việc Dân Bản Địa (Host Portal - `/host/*`)](#5-giao-diện-bàn-làm-việc-dân-bản-địa-host-portal---host)
   - 5.1. [Bảng Điều Khiển Host (Dashboard - `/host`)](#51-bảng-điều-khiển-host-dashboard---host)
   - 5.2. [Quản Lý Đơn Đặt & Duyệt Giữ Chỗ (`/host/bookings`)](#52-quản-lý-đơn-đặt--duyệt-giữ-chỗ-hostbookings)
   - 5.3. [Chi Tiết Đơn Đặt Phía Host (`/host/bookings/{id}`)](#53-chi-tiết-đơn-đặt-phía-host-hostbookingsid)
   - 5.4. [Đăng Ký & Sửa Tour Trải Nghiệm (`/host/services/create` & `edit`)](#54-đăng-ký--sửa-tour-trải-nghiệm-hostservicescreate--edit)
   - 5.5. [Quản Lý Lịch Trình Khởi Hành (`/host/schedules` & `create`)](#55-quản-lý-lịch-trình-khởi-hành-hostschedules--create)
6. [Giao Diện Quản Trị Hệ Thống (Admin Portal - `/admin/*`)](#6-giao-diện-quản-trị-hệ-thống-admin-portal---admin)
   - 6.1. [Bảng Quản Trị Tổng Quan (`/admin`)](#61-bảng-quản-trị-tổng-quan-admin)
   - 6.2. [Trung Tâm Kiểm Duyệt & Quản Lý Tour (`/admin/services`)](#62-trung-tâm-kiểm-duyệt--quản-lý-tour-adminservices)
   - 6.3. [Quản Lý Người Dùng & Phân Quyền (`/admin/users`)](#63-quản-lý-người-dùng--phân-quyền-adminusers)
   - 6.4. [Quản Lý Danh Mục Du Lịch (`/admin/categories`)](#64-quản-lý-danh-mục-du-lịch-admincategories)
   - 6.5. [Quản Trị Bài Viết & Cẩm Nang (`/admin/posts`)](#65-quản-trị-bài-viết--cẩm-nang-adminposts)
   - 6.6. [Quản Lý Hồ Sơ Hủy & Hoàn Tiền (`/admin/refunds`)](#66-quản-lý-hồ-sơ-hủy--hoàn-tiền-adminrefunds)
7. [Các Trang Báo Lỗi Hệ Thống & Modal Toàn Cục](#7-các-trang-báo-lỗi-hệ-thống--modal-toàn-cục)
8. [Bảng Tra Cứu Nhanh Các Thành Phần Giao Diện (Design Tokens Cheat Sheet)](#8-bảng-tra-cứu-nhanh-các-thành-phần-giao-diện-design-tokens-cheat-sheet)

---

## 1. HỆ THỐNG MÀU SẮC & TYPOGRAPHY TOÀN HỆ THỐNG

### 1.1. Bảng Màu Dark-Alpine & Ambient Blur (Dành Cho Giao Diện Du Khách)
Giao diện khách hàng được thiết kế theo phong cách **Dark-Alpine Glassmorphism** lấy cảm hứng từ màn đêm sương mù vùng cao Tây Bắc và hồ nước cao nguyên Đà Lạt.

| Tên Biến CSS / Token | Mã Màu (HEX / RGBA) | Vai Trò & Vị Trí Áp Dụng Trong Giao Diện |
| :--- | :--- | :--- |
| `--bg-midnight` | `#0B1924` | **Nền chính toàn trang khách** (Body, bao phủ toàn bộ trải nghiệm người dùng, tạo chiều sâu thị giác). |
| `--brand-deep` | `#184E6C` | **Xanh dương đậm**: Lớp chuyển sắc gradient cho các nút CTA, nền submenu dropdown, đổ bóng panel. |
| `--brand-primary` | `#387EA2` | **Xanh điểm nhấn chủ đạo**: Nút bấm chính (`.btn-alpine`), trạng thái active trên navbar, viền nổi bật. |
| `--brand-mid` | `#5BA3C6` | **Xanh dương băng trung gian**: Trục kẻ dọc Timeline trạm dừng, viền phát sáng khi hover, nút trượt Range Slider. |
| `--brand-light` | `#9BCBE5` | **Xanh phát quang phụ**: Chấm mốc trạm dừng (`station-timeline-dot`), icon thời tiết, nhãn thời gian. |
| `--brand-ice` | `#DDECF6` | **Xanh đá dịu mắt**: Chữ mô tả hành trình, link footer, văn bản phụ giúp đọc lâu không bị mỏi mắt. |
| `--glass-surface` | `rgba(19, 42, 60, 0.65)` | **Mặt kính mờ (Panel Glass)**: Nền các khối Card, Search bar, Container bộ lọc (`backdrop-filter: blur(12px)`). |
| `--glass-border` | `rgba(155, 203, 229, 0.22)`| **Viền kính mờ**: Đường viền siêu mảnh của thẻ glass-panel, phản chiếu ánh sáng nhẹ. |
| `.glass-pill` | `rgba(15, 25, 36, 0.58)` | **Nền viên thuốc kính mờ**: Bo tròn `999px`, viền `rgba(155, 203, 229, 0.28)`, dùng cho Navbar, Badge, Tag. |
| Ambient Glow | `rgba(11, 25, 36, 0.90)` | Lớp phát sáng nhòe mềm chân Banner (`filter: blur(65px)`), hòa tan ảnh nền vào màu nền đêm. |

### 1.2. Bảng Màu Bổ Trợ & Trạng Thái Nghiệp Vụ (Semantic Colors)

| Màu Nhận Diện | Mã Màu HEX | Biểu Tượng & Ý Nghĩa Áp Dụng |
| :--- | :--- | :--- |
| **Vàng Kim (Amber / Gold)** | `#F59E0B` (`text-warning`) | Số sao đánh giá (★ Rating), Cảnh báo đơn chờ duyệt (`pending`), Chờ thanh toán cọc. |
| **Xanh Ngọc Bảo (Emerald)** | `#10B981` (`text-success`) | Huy hiệu bảo chứng **"Đã tự trải nghiệm"**, Thanh toán thành công, Đơn đã xác nhận (`confirmed`). |
| **Đỏ San Hô (Ruby / Crimson)** | `#EF4444` (`text-danger`) | Ghim vị trí bản đồ, Giá hủy/phạt, Nút Hủy đơn, Tour bị từ chối phê duyệt (`rejected`). |
| **Xanh Biển Tươi (Sky Blue)** | `#38BDF8` (`text-info`) | Đơn giá tour, Số tiền cọc, Nhiệt độ thời tiết, Thẻ Host, Badge phân loại. |
| **Hồng MoMo (MoMo Magenta)** | `#A50064` $\rightarrow$ `#D82D8B` | Header cổng thanh toán MoMo Sandbox, nút giả lập quét mã QR, hóa đơn điện tử. |
| **Trắng Tuyết (Crisp White)** | `#FFFFFF` / `#F8FAFC` | Tiêu đề H1/H2, tên địa danh, nhãn nổi bật, đảm bảo độ tương phản chuẩn WCAG 2.1 AAA. |

### 1.3. Bảng Màu Dành Cho Host Portal & Admin Center (Clean Management Theme)
Khu vực quản lý hậu đài áp dụng giao diện hiện đại, nền sáng nhằm tối ưu hóa sự tập trung khi nhập liệu và đối soát:
- **Nền trang quản lý:** `#F8FAFC` (Xám bạc dịu mắt)
- **Thẻ nội dung (Cards):** `#FFFFFF` (Trắng tinh khiết, bo góc tròn `16px`, đổ bóng `0 2px 12px rgba(0,0,0,0.06)`)
- **Màu chữ chính:** `#1E293B` (Slate đậm, sắc nét)
- **Nút bấm quản trị:** `.btn-editorial` (`#0F2922` xanh lục rừng sẫm hoặc `#0F172A` than đen)

### 1.4. Quy Chuẩn Phông Chữ (Typography System)
Toàn bộ hệ thống đã được hợp nhất **100% sang font chữ Google Roboto** duy nhất:
- **Font gia đình:** `'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`
- **Mã nhúng:** `https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap`
- **Phông hiển thị mã code / Tọa độ / Mã đơn hàng:** `'JetBrains Mono', SFMono-Regular, Menlo, Monaco, Consolas, monospace`

---

## 2. NGUYÊN TẮC BỐ CỤC & NGÔN NGỮ THIẾT KẾ DÙNG CHUNG

### 2.1. Quy Tắc Lưới Bootstrap 5 (Giảng Viên Bắt Buộc)
Mọi trang trong toàn hệ thống đều tuân thủ nghiêm ngặt hệ thống phân cấp:
$$\text{div.main-container} \longrightarrow \text{div.container (hoặc container-xxl)} \longrightarrow \text{div.row} \longrightarrow \text{div.col-*}$$
- Tuyệt đối không đặt nội dung tự do trực tiếp trong `.row` mà không qua thẻ bọc `.col-*`.
- Sử dụng khoảng cách rãnh cột tiêu chuẩn: `g-3` hoặc `g-4` để đảm bảo độ thoáng và liền mạch.

### 2.2. Hiệu Ứng Kính Mờ (Glassmorphism & Ambient Glow)
- **Glass Panel (`.glass-panel`):** Sử dụng `backdrop-filter: blur(12px)` kết hợp viền mờ `border: 1px solid rgba(155, 203, 229, 0.22)`.
- **Glass Pill (`.glass-pill`):** Dạng viên thuốc bo tròn 999px, sử dụng cho Menu, Badge, Tag, Button phụ.
- **Ambient Glow (`.hero-ambient-glow`):** Kỹ thuật trích xuất ảnh nền banner, đặt phía dưới đáy container với `filter: blur(65px)` và `opacity: 0.38` để ánh sáng từ ảnh tràn tự nhiên xuống các khối nội dung bên dưới.

### 2.3. Quy Chuẩn Các Nút Bấm Chính (Button Components)
1. **`.btn-alpine`:** Nút bấm gradient chính cho trải nghiệm du lịch.  
   - *Gradient:* `linear-gradient(135deg, #387ea2, #184e6c)`
   - *Viền:* 1px `#5ba3c6`
   - *Bo góc:* `rounded-pill` (999px)
   - *Hover:* Đổi màu `#5ba3c6`, chữ đen `#0b1924`, phát sáng `box-shadow: 0 0 18px rgba(91, 163, 198, 0.5)`.
2. **`.btn-glass-hero`:** Nút bấm kính mờ đặt trên ảnh banner.  
   - *Nền:* `rgba(255, 255, 255, 0.16)`, blur 10px, viền `rgba(255, 255, 255, 0.45)`.
   - *Hover:* Chuyển sang màu `--brand-primary` (`#387ea2`), viền `--brand-light`.
3. **`.btn-editorial`:** Nút bấm khối đặc thanh lịch màu tối cho các thao tác gửi biểu mẫu và quản trị.
4. **`.btn-pill-amber`:** Nút bấm màu hổ phách/cam ấm (`#d97706`) tạo điểm nhấn kêu gọi hành động khẩn thiết.

---

## 3. KHUNG LAYOUT TOÀN CỤC (GLOBAL LAYOUT & COMPONENTS)
> **Tệp tin nguồn:** `app/views/layouts/main.php` & `app/views/partials/notification-modal.php`

```
+---------------------------------------------------------------------------------------------------+
|  [Logo OnlineTraveller]     [ Trang chủ  Khám Phá  Bản tin  (Quản lý/Quản trị) ]    [ User Pill v ] |  <-- Top Floating Header
+---------------------------------------------------------------------------------------------------+
|                                                                                                   |
|                                       MAIN CONTENT AREA                                           |
|               (Full-bleed cho Trang chủ & Chi tiết Tour; hoặc có đệm lùi 5.5rem)                  |
|                                                                                                   |
+---------------------------------------------------------------------------------------------------+
|  OnlineTraveller        • Trang chủ • Khám Phá • Bản tin •        © 2026 OnlineTraveller  [Quay lại ^]|  <-- Minimal Footer
+---------------------------------------------------------------------------------------------------+
```

### 3.1. Thanh Điều Hướng Nổi Trên Cùng (Top Floating Header)
- **Vị trí & Bố cục:** Nằm nổi phía trên banner (`position: absolute; top: 0; left: 0; width: 100%; z-index: 1030`).
- **Thành phần cột 1 (Bên trái):**
  - Logo tròn núi & sóng bản địa (`/assets/images/logo.svg`, kích thước 36x36px).
  - Tên thương hiệu: `OnlineTraveller` (Chữ trắng, font Roboto, `tracking-wide`).
- **Thành phần cột 2 (Ở giữa - Desktop `d-none d-lg-block`):**
  - Khối viên thuốc kính mờ (`.navbar-center-pill`).
  - **Quy tắc tuyệt đối:** Chỉ chứa Text thuần, **không chứa icon** rườm rà.
  - Các liên kết:
    - `Trang chủ` (`/`)
    - `Khám Phá` (`/services`)
    - `Bản tin` (`/posts`)
    - `Quản lý` (Màu `text-info`, chỉ hiển thị khi tài khoản có role `host`)
    - `Quản trị` (Màu `text-warning`, chỉ hiển thị khi tài khoản có role `admin`)
- **Thành phần cột 3 (Bên phải):**
  - **Khi chưa đăng nhập:** Nút pill kính mờ `Đăng nhập` (Icon người dùng mini tròn + Text "Đăng nhập").
  - **Khi đã đăng nhập:** Nút dropdown pill hiển thị avatar mini + Họ tên người dùng. Khi click mở menu xổ xuống mờ kính gồm:
    - Nhãn vai trò đăng nhập (Badge role: `tourist` / `host` / `admin`).
    - Link `Hồ sơ của tôi` (`/profile`, icon `bi-person`).
    - Link `Chuyến đi của tôi` (`/bookings`, icon `bi-calendar-check`).
    - Link `Bàn làm việc Host` (`/host`, icon `bi-kanban`, role Host).
    - Link `Trung tâm Admin` & `Quản lý & Duyệt Tour` (`/admin`, role Admin).
    - Link `Đăng xuất` (Màu đỏ `text-danger`, icon `bi-box-arrow-right`).
  - **Nút Menu Di Động (Mobile Toggler):** Icon `bi-list` mở Offcanvas bên phải trên thiết bị nhỏ (`< lg`).

### 3.2. Menu Trượt Cho Thiết Bị Di Động (Mobile Offcanvas Nav)
- **ID:** `#mobileOffcanvasNav` (Trượt từ phải sang `offcanvas-end`).
- **Giao diện:** Kính mờ siêu tối `rgba(11, 25, 36, 0.96)` kết hợp `backdrop-filter: blur(16px)`.
- **Nội dung:** Danh sách các nút dạng pill xếp dọc có icon màu, mục điều hướng theo vai trò và ghi chú bản quyền.

### 3.3. Chân Trang Tối Giản (Minimal Footer)
- **Lớp CSS:** `.footer-minimal` (Nền `rgba(11, 25, 36, 0.85)`, viền trên mờ 1px).
- **Cột trái:** Logo 28px + Tên `OnlineTraveller`.
- **Cột giữa:** Cụm liên kết ngang ngăn cách bằng dấu chấm mờ (`Trang chủ • Khám Phá • Bản tin`) và dòng kiến trúc *"OOP + MVC, 15 bảng, 22 Stored Procedures"*.
- **Cột phải:** Bản quyền `© 2026 OnlineTraveller` và nút cuộn mượt lên đầu trang (`Quay lại ★` với icon sao xanh).

### 3.4. Modal Thông Báo Toàn Cục (Global Reusable Notification Modal)
- **Tệp tin:** `app/views/partials/notification-modal.php`
- **Cơ chế:** Tự động lắng nghe Session Flash Message (`success`, `error`, `warning`, `info`) từ Backend và kích hoạt pop-up kính mờ không làm gián đoạn luồng duyệt web.
- **Thành phần:** Icon trạng thái lớn đổi màu theo loại thông báo, tiêu đề thông báo, nội dung giải thích, nút đóng "Đã hiểu".

---

## 4. GIAO DIỆN DU KHÁCH & NGƯỜI DÙNG ĐẠI CHÚNG (TOURIST & PUBLIC FACING)

### 4.1. Trang Chủ (Home Page - `/`)
> **Tệp tin nguồn:** `app/views/home/index.php`

```
+---------------------------------------------------------------------------------------------------+
|  HERO BANNER SLIDER                                                                               |
|  [Badge Điểm đến: Đà Lạt]                                                                         |
|  Bình Minh Bên Hồ Tuyền Lâm                                                                       |
|  Trải nghiệm sương mù Đà Lạt, đón bình minh mộng mơ...                                            |
|  [ Khám phá ngay -> ]   Host: Nguyễn Văn Bản Địa * 4.9                                            |
|  -----------------------------------------------------------------------------------------------  |
|  [Thumb 1 (Active)]  [Thumb 2]  [Thumb 3]  [Thumb 4]                                   [ < ] [ > ]|
+---------------------------------------------------------------------------------------------------+
|  [ Điểm đến?      ] | [ Danh mục văn hóa v ] | [ Ngân sách tối đa: 2.500.000d --o-- ] | [Tìm kiếm]|  <-- Glass Search Bar
+---------------------------------------------------------------------------------------------------+
|  THỜI TIẾT ĐIỂM ĐẾN HÔM NAY (5 Cards: Đà Lạt 18°C | Sa Pa 15°C | Hà Giang 21°C | Hội An | Phú Quốc)|
+---------------------------------------------------------------------------------------------------+
|  KHÁM PHÁ ĐỊA PHƯƠNG                             [ Tìm theo chủ đề v ] (Tất cả, Ẩm thực, Săn mây...)|
|  [ Card Poster 1 ]        [ Card Poster 2 ]        [ Card Poster 3 ]        [ Card Poster 4 ]     |
|  (Ảnh tràn 3/4 + ★ Rating + Địa danh + Tên tour + Giá 450k)                                       |
+---------------------------------------------------------------------------------------------------+
|  TIN TỨC DU LỊCH & BẢN TIN RSS (Trái: Giới thiệu + Nút xem thêm | Phải: 2 Card bài viết VnExpress)|
+---------------------------------------------------------------------------------------------------+
```

#### Chi tiết các Section & Component trên Trang Chủ:
1. **Section 1: Hero Banner Slider tương tác (`#homeHeroAmbientWrapper`):**
   - **Thẻ Banner Box (`#heroBannerBox`):** Chiều cao tối thiểu `520px`, ảnh nền trượt cover mượt mà.
   - **Ambient Glow (`#heroAmbientGlow`):** Nằm âm dưới banner, đồng bộ ảnh nền với hiệu ứng làm mờ 65px.
   - **Overlay gradient:** Phủ 2 lớp chuyển sắc đen để chữ luôn rõ nét trên mọi màu nền ảnh.
   - **Các nút & phần tử:**
     - Badge địa danh kính mờ `#heroLocationBadge` (Icon ghim đỏ + Tên địa điểm).
     - Tiêu đề H1 font Roboto `#heroTitle`.
     - Đoạn văn mô tả `#heroDesc`.
     - Nút CTA `.btn-glass-hero` `#heroCtaLink` ("Khám phá ngay →").
     - Dải thumbnails trượt `#bannerThumbsContainer`: Thẻ con `.banner-thumb-item` gồm ảnh vuông nhỏ 36px, tên địa danh, số sao; có thể click chuyển slide ngay lập tức; slide đang xem mang class `.active` viền sáng `#5ba3c6`.
     - Cặp nút điều hướng `#btnPrevSlide` & `#btnNextSlide` (Nút tròn nhỏ viền mờ).
     - Script tự động trượt (Auto-slide) mỗi 6 giây, tự động tạm dừng khi rê chuột vào banner.
2. **Section 2: Thanh tìm kiếm nổi kính mờ (`#homeSearchForm`):**
   - Đặt ngay dưới banner với lớp kính mờ `glass-panel` chia 4 cột ngăn cách bởi vạch mờ:
     - **Cột 1 (Điểm đến?):** Ô input không viền `#search-location`, placeholder trắng mờ.
     - **Cột 2 (Danh mục):** Dropdown `#search-category` nền kính, option nền tối.
     - **Cột 3 (Ngân sách tối đa):** Thanh trượt ngang Range Slider `#home-price-slider` (min 200.000đ - max 5.000.000đ+). Hiển thị số tiền nhảy động real-time tại nhãn `#home-price-display`.
     - **Cột 4 (Nút hành động):** Nút `.btn-alpine` chữ đậm "Tìm kiếm".
3. **Section 3: Thời tiết điểm đến hôm nay:**
   - Tiêu đề Section trắng sáng `<h4>Thời Tiết Điểm Đến Hôm Nay</h4>`.
   - Lưới 5 cột gồm 5 thẻ kính mờ hover-lift đại diện 5 địa bàn trọng điểm: Đà Lạt, Sa Pa, Hà Giang, Hội An, Phú Quốc.
   - Mỗi thẻ gồm: Tên thành phố, Icon thời tiết OpenWeatherMap (hoặc Bootstrap Icons dự phòng), Nhiệt độ lớn màu xanh `--brand-light`, Mô tả thời tiết ngắn. Click vào thẻ sẽ lọc ngay danh sách tour tại địa danh đó.
4. **Section 4: Khám phá địa phương & Lưới Card Trải Nghiệm tràn ảnh:**
   - Header Section chứa tiêu đề H3 và nút `.btn-alpine` dạng thu gọn `Tìm theo chủ đề ▾` (`data-bs-toggle="collapse"`).
   - Vùng Collapse chứa các Tag chủ đề kính mờ (`.glass-pill`): Tất cả, Ẩm thực bản địa, Săn mây & Trekking, Làng nghề di sản, Homestay nhà dân...
   - Lưới 4 Card Trải Nghiệm tràn ảnh tỉ lệ 3/4 (`.experience-poster-card`):
     - Ảnh bìa tràn viền, hiệu ứng zoom nhẹ 1.04 khi hover.
     - Góc trên phải: Badge rating kính mờ (★ 4.9).
     - Lớp phủ overlay chân ảnh: Địa danh, Tên trải nghiệm, Đơn giá rút gọn (VD: `Giá 450k` hoặc `Giá 1.200.000 ₫`).
5. **Section 5: Khối Tin tức du lịch RSS & Cẩm nang:**
   - Khung thẻ kính mờ lớn chia tỉ lệ 4 : 8:
     - **Cột trái:** Nhãn `CẨM NANG & BẢN TIN RSS`, Tiêu đề "Tin tức du lịch", tóm tắt và nút xem thêm `.btn-glass-hero` dẫn sang `/posts`.
     - **Cột phải:** Lưới 2 card bài báo mới nhất từ RSS VnExpress Du Lịch (ảnh thumbnail tỉ lệ 16:9, tiêu đề giới hạn 2 dòng, nguồn báo chí, thời gian đọc).

---

### 4.2. Trang Khám Phá Trải Nghiệm (Services Directory - `/services`)
> **Tệp tin nguồn:** `app/views/services/index.php`

- **Breadcrumb:** `Trang chủ` > `Trải Nghiệm Bản Địa`
- **Page Header:** Tiêu đề H2 lớn, đoạn giới thiệu và Badge kính mờ thông báo tổng số gói trải nghiệm hiện có (`X Gói Trải Nghiệm Có Sẵn`).
- **Thanh lọc tìm kiếm toàn diện (`glass-panel`):**
  - Ô tìm kiếm từ khóa địa danh kèm icon kính lúp.
  - Dropdown lọc danh mục văn hóa.
  - Thanh trượt khoảng giá Range Slider (`#service-price-slider`, min 200k - max 5tr+) có số tiền nhảy real-time.
  - Nút submit `.btn-alpine` ("Lọc Kết Quả").
  - Dải Tag các điểm đến thịnh hành (Đà Lạt, Sa Pa, Hà Giang, Hội An, Phú Quốc) & Nút "Xóa bộ lọc" (`badge bg-danger-subtle`).
- **Lưới danh sách Tour (Card Dark-Alpine Tràn Ảnh 3/4):**
  - Sử dụng chung cấu trúc `.experience-poster-card` như Trang Chủ để đảm bảo đồng bộ thị giác 100%.
  - Góc trên trái: Badge danh mục văn hóa (`.glass-pill`).
  - Góc trên phải: Badge điểm đánh giá sao (`.glass-pill`).
  - Overlay đáy ảnh: Tên địa phương, Tiêu đề tour, Đơn giá/người nổi bật màu xanh `text-info` kèm ghi chú nhỏ *"Đã gồm 8% VAT"*, Tên host đón tiếp.
- **Trạng thái không có kết quả:** Khối kính mờ thông báo thân thiện + Nút "Xem Tất Cả Trải Nghiệm".

---

### 4.3. Trang Chi Tiết Trải Nghiệm (Experience Detail - `/services/{id}`)
> **Tệp tin nguồn:** `app/views/services/show.php` (kèm các partials chuyên trách)

```
+---------------------------------------------------------------------------------------------------+
|  [NẾU ADMIN & TOUR PENDING]: BANNER CẢNH BÁO KIỂM DUYỆT (Nút Duyệt Mở Bán | Nút Từ Chối | Nút Lại)  |
+---------------------------------------------------------------------------------------------------+
|  HERO BANNER TOUR DETAIL (2 CỘT)                                                                  |
|  [CỘT TRÁI - col-lg-8]                                    | [CỘT PHẢI - col-lg-4]                 |
|  Badge Địa danh: Đà Lạt                                   | DẢI 4 CARD TRẠM DỌC THEO CHIỀU CAO    |
|  Tiêu đề: Săn Mây & Cà Phê Bản Địa                        | [Trạm 1: Đón bình minh bến thuyền   ] |
|  Mô tả: Khám phá thiên nhiên mộng mơ...                   | [Trạm 2: Rừng thông & Cà phê bản địa] |
|  Giá: 450.000 ₫ / người (Đã bao gồm 8% VAT)               | [Trạm 3: Ẩm thực chiều bên hồ       ] |
|  Host: Dân Bản Địa * 4.9                                  | [Trạm 4: Hoàng hôn Hồ Tuyền Lâm     ] |
|  [ Đặt lịch ngay v ]   [ Trải nghiệm 3D Tour ]            | (Click mở trạm trực tiếp trong 3D)    |
+---------------------------------------------------------------------------------------------------+
|  HÀNH TRÌNH & CHI TIẾT (col-lg-7)                         | THÔNG TIN HOST & QUY ĐỊNH (col-lg-5)  |
|  - Timeline Trục Dọc các trạm dừng chân (Ảnh + Thuyết minh) | - Card Host Bản Địa (Avatar, Bio, SĐT)|
|  - Thẻ Homestay Nhà Dân (Tiện nghi, Ảnh phòng)             | - Box Quy định an toàn & Hủy cọc T24  |
|  - Thẻ Thời tiết & Iframe Google Maps tương tác            |                                       |
+---------------------------------------------------------------------------------------------------+
|  HỎI - ĐÁP CỘNG ĐỒNG & CHỌN LỊCH KHỞI HÀNH (Discussion Q&A + Schedule Picker Calendar)             |
+---------------------------------------------------------------------------------------------------+
|  KHỐI ĐÁNH GIÁ TRẢI NGHIỆM (Sao TB + Phân bổ sao + Form gửi review + Nhãn "Đã tự trải nghiệm")    |
+---------------------------------------------------------------------------------------------------+
|  MODAL TRÌNH CHIẾU KHÔNG GIAN 3D TOUR (Virtual 3D Viewport + Bàn phím điều hướng trạm)             |
+---------------------------------------------------------------------------------------------------+
```

#### Các Thành Phần Nổi Bật:
1. **Banner Kiểm Duyệt Của Admin (Moderation Alert Banner):**
   - Chỉ xuất hiện khi người dùng đăng nhập là Admin và tour đang ở trạng thái `pending` hoặc `rejected`.
   - Nút xanh lá: `Phê Duyệt Mở Bán` (Mở bán công khai tức thì).
   - Nút đỏ outline: `Từ Chối` (Bật modal nhập lý do chi tiết gửi lại cho Host).
   - Nút xám: `Danh Sách Duyệt` (Trở về `/admin/services`).
2. **Hero Banner 2 Cột Bố Cục Mới (Phase 6):**
   - Cột trái: Thông tin tổng quan, giá niêm yết có dòng chữ *"Giá niêm yết đã bao gồm 8% thuế GTGT"*, nút CTA "Đặt lịch ngay" cuộn mượt xuống bảng chọn lịch và nút "Khám phá 3D Tour".
   - Cột phải: Dải card các trạm dừng chân xếp dọc (`.vertical-station-card`): Mỗi card có ảnh vuông 52px, số thứ tự trạm, tên trạm; click vào sẽ tự động mở modal 3D tại đúng trạm đó.
3. **Station Timeline (Hành trình dọc các trạm):**
   - Đường kẻ trục dọc màu `--brand-mid`, các chấm tròn phát sáng `--brand-light`.
   - Mỗi trạm gồm: Tên trạm, Thuyết minh văn hóa truyền thống, Ảnh chụp thực tế của trạm.
4. **Thẻ Homestay Nhà Dân Bản Địa:**
   - Tên nhà dân, Địa chỉ, Danh mục tiện nghi (Wifi, tắm nóng lạnh, ăn sáng bản địa, đưa đón xe máy), Ảnh phòng nghỉ.
5. **Thẻ Thời Tiết & Bản Đồ Google Maps:**
   - Widget nhiệt độ, độ ẩm, sức gió và iframe bản đồ Google Maps tương tác.
6. **Khối Đánh Giá Chi Tiết & Nhãn Bảo Chứng (Phase 2):**
   - Tóm tắt điểm số trung bình (Ví dụ: `4.9 ★` / 28 nhận xét).
   - Thanh phân bổ tỷ lệ sao (5★ $\rightarrow$ 1★).
   - Form gửi nhận xét AJAX: Đánh giá 1-5 sao, khung nhập cảm nghĩ, nút "Gửi Đánh Giá".
   - Danh sách đánh giá: Avatar, Tên du khách, Thời gian, Số sao, Nội dung nhận xét, và đặc biệt là **Huy hiệu bảo chứng màu xanh lá "Đã tự trải nghiệm"** (*Verified Traveler*) tự động xuất hiện nếu du khách đã từng hoàn tất đơn hàng tour này.

---

### 4.4. Trang Đặt Chỗ & Báo Giá (Booking Create - `/bookings/create`)
> **Tệp tin nguồn:** `app/views/bookings/create.php`

- **Breadcrumb:** `Trang chủ` > `Khám phá bản địa` > `Tên tour` > `Đặt Chỗ & Báo Giá`
- **Thanh tiến trình 3 bước (Step Indicator):**
  - Bước 1: `Chọn Lịch & Báo Giá` (Vòng tròn đen số 1, đang kích hoạt).
  - Bước 2: `Host Giữ Chỗ (15p)` (Vòng tròn xám số 2).
  - Bước 3: `Thanh Toán Cọc MoMo` (Vòng tròn xám số 3).
- **Cột trái (col-lg-7):**
  - **Tour Header Card:** Icon la bàn vàng, Badge "Trải Nghiệm Bản Địa Đích Thực", Tiêu đề tour, Điểm đón khách.
  - **Khối 1 (Chọn đợt khởi hành):** Dropdown lịch khởi hành còn trống chỗ, box tóm tắt giờ bắt đầu, giờ kết thúc, điểm hẹn.
  - **Khối 2 (Số lượng khách):** Bộ nút tăng/giảm số lượng (`-` / `+`), ô input số lượng khách.
  - **Khối 3 (Kế hoạch thanh toán):** Lựa chọn Radio:
    - *Đặt cọc trước 50% online qua MoMo (Khuyên dùng - Badge xanh).*
    - *Thanh toán toàn phần 100% qua MoMo (Tiện lợi).*
  - **Khối 4 (Mã ưu đãi / Khuyến mãi):** Ô nhập mã giảm giá, nút "Áp Dụng", Alert thông báo mức chiết khấu được áp dụng.
- **Cột phải (col-lg-5 - Sticky Summary):**
  - Card bảng tóm tắt chi phí: Đơn giá/người, Số lượng khách, Tạm tính tiền tour, Giảm giá ưu đãi, Tổng giá trị đơn.
  - Box màu xanh: **Tiền cọc giữ chỗ (Online MoMo)** và **Số tiền còn lại thanh toán trực tiếp cho Host**.
  - Nút submit `.btn-dark` lớn: **"Gửi Yêu Cầu Đặt Chỗ Ngay"**.
  - Cam kết an toàn & hoàn tiền 100% khi thời tiết xấu.

---

### 4.5. Trang Chi Tiết Đơn Đặt Chỗ & Bản In (Booking Detail - `/bookings/{id}`)
> **Tệp tin nguồn:** `app/views/bookings/detail.php`

- **Header phiếu in chuyên dụng (`@media print`):** Thiết kế chuẩn khổ A4, logo OnlineTraveller, Hotline CSKH 1900 6868, mã đơn hàng, ngày giờ in phiếu. Tự động ẩn toàn bộ navbar, footer, nút bấm khi in.
- **Header màn hình thường:** Mã đơn `#BK...` kèm nút copy nhanh, tiêu đề, nút "In Đơn", nút "Danh Sách Chuyến Đi".
- **Banner trạng thái đơn tương ứng:**
  - `pending`: Cảnh báo vàng "Đang chờ Host duyệt giữ chỗ".
  - `awaiting_payment`: Cảnh báo xanh "Host đã duyệt — Chờ thanh toán tiền cọc", đồng hồ đếm ngược 15 phút, nút "Thanh Toán Cọc MoMo".
  - `confirmed`: Cảnh báo xanh lá "Đã xác nhận đặt chỗ thành công", nút "Xem Biên Nhận Điện Tử", nút "Hủy Đặt Chỗ".
  - `cancelled`: Cảnh báo xám "Đơn đặt chỗ đã bị hủy", nút "Theo Dõi Hoàn Tiền" (nếu có cọc).
  - `completed`: Thông báo hoàn tất hành trình, nút "Viết Đánh Giá".
- **Bảng bóc tách chi phí & Thuế GTGT 8% (Phase 1):**
  - Hiển thị minh bạch 3 dòng:
    1. *Tiền dịch vụ trước thuế (Net)*
    2. *Thuế GTGT (VAT 8%)*
    3. *Tổng chi phí tour (Gross - Đã bao gồm 8% VAT)*
  - Hiển thị số tiền đã cọc MoMo và số tiền còn lại trả trực tiếp cho Host tại điểm hẹn.
- **Modal Hủy đặt chỗ (`#cancelModal`):** Cho phép du khách chọn lý do hủy đơn, cảnh báo quy định hoàn tiền T24.

---

### 4.6. Trang Chuyến Đi Của Tôi (My Bookings - `/my-bookings`)
> **Tệp tin nguồn:** `app/views/bookings/my_bookings.php`

- **Header:** Tiêu đề "Chuyến Đi & Đơn Đặt Chỗ Của Tôi" + Nút "Khám Phá Tour Mới".
- **Thanh Tab lọc trạng thái:**
  - `Tất Cả (X)`
  - `Chờ Duyệt (pending)`
  - `Chờ Thanh Toán (awaiting_payment)`
  - `Đã Xác Nhận (confirmed)`
  - `Đã Hủy (cancelled)`
- **Danh sách thẻ đơn hàng:**
  - Ảnh thumbnail tour (cao 100px, bo góc).
  - Mã đơn `#BK...`, Badge trạng thái màu.
  - Tên tour, Tên Host, Giờ khởi hành, Điểm đón, Số lượng khách.
  - Tổng tiền & Số tiền cọc.
  - Nút "Xem Chi Tiết Đơn", Nút "Thanh toán cọc ngay" (nếu đang awaiting_payment).
- **Trạng thái rỗng:** Card icon lịch trống + Nút "Khám Phá Trải Nghiệm Ngay".

---

### 4.7. Trang Cổng Giả Lập MoMo Sandbox QR (`/payments/checkout`)
> **Tệp tin nguồn:** `app/views/payments/sandbox_qr.php`

- **Header MoMo:** Gradient hồng mận `#A50064` $\rightarrow$ `#D82D8B`, icon ví MoMo, nhãn "Cổng Thanh Toán MoMo Sandbox (API v2)".
- **Số tiền cần thanh toán:** Hiển thị cỡ chữ lớn màu hồng MoMo.
- **Khung mã QR thực tế:** Hiển thị mã QR động tạo từ API sinh mã test.
- **Bảng thông tin:** Mã đơn hàng, Nội dung thanh toán, Mã đối tác `MOMOBKUN20180529`.
- **Nút hành động:**
  - Nút chính màu hồng MoMo: "Giả Lập Quét Mã & Thanh Toán Thành Công" (Tự động tính chữ ký HMAC-SHA256 và trigger webhook IPN).
  - Nút phụ: "Hủy Giao Dịch & Quay Lại".

---

### 4.8. Trang Kết Quả Giao Dịch Callback (`/payments/callback`)
> **Tệp tin nguồn:** `app/views/payments/callback.php`

- **Trường hợp thành công:**
  - Icon tròn xanh lá lớn với dấu tick trắng.
  - Tiêu đề "Giao Dịch Đã Hoàn Tất", ghi chú tiền cọc đã được ghi nhận.
  - Bảng chi tiết: Mã đơn hàng, Mã giao dịch MoMo transId, Mã biên nhận điện tử, Số tiền đã cọc.
  - Hàng nút chính: Nút "Xem Chi Tiết Vé" & Nút "Xem Biên Nhận Điện Tử".
  - Hàng nút phụ đối xứng: Nút "Chuyến Đi Của Tôi" & Nút "Về Trang Chủ".
- **Trường hợp thất bại / hủy:**
  - Icon cảnh báo đỏ, tiêu đề "Giao Dịch Đã Bị Hủy Hoặc Gián Đoạn", nút "Thử Lại Thanh Toán" & nút "Về Trang Chủ".

---

### 4.9. Trang Hóa Đơn Điện Tử E-Receipt (`/receipts/{id}`)
> **Tệp tin nguồn:** `app/views/receipts/show.php`

- **Thanh công cụ đầu trang (Không in):** Breadcrumb, nút "Trang Chủ", nút "Chi Tiết Đơn Đặt", nút "In Biên Nhận / Lưu PDF".
- **Văn bản chứng từ điện tử:**
  - Con dấu Watermark mờ đóng chéo góc: `PAID & VERIFIED - MOMO SANDBOX GATEWAY`.
  - Header: Logo OnlineTraveller, Badge MoMo Sandbox, Mã biên nhận `#RC...`, Thời gian lập.
  - Thông tin 2 bên: Người đại diện đặt tour & Dân bản địa tiếp đón (Host).
  - Khối tóm tắt tour: Tên trải nghiệm, Giờ khởi hành, Điểm đón tập trung.
  - Bảng kê chi tiết thanh toán & Bóc tách thuế GTGT (VAT 8%): Dòng phí dịch vụ, Dòng giảm giá, Dòng Tiền dịch vụ trước thuế (Net), Dòng Thuế GTGT 8% (VAT), Dòng Tổng chi phí tour (Gross), Dòng Số tiền đã thanh toán online MoMo Sandbox, Dòng Số tiền còn lại trả cho Host.
  - Khối Audit Trail: Mã giao dịch MoMo, Mã đơn, Chữ ký số bảo mật.

---

### 4.10. Trang Phiếu Theo Dõi Hoàn Tiền (`/refunds/{id}`)
> **Tệp tin nguồn:** `app/views/refunds/show.php`

- **Header:** Mã hoàn tiền `#RF...`, Badge trạng thái tiến độ (Chờ Admin duyệt / Đang xử lý / Đã hoàn thành công / Từ chối), Số tiền hoàn nổi bật màu xanh lá.
- **Khối thông tin đối soát:** Mã đơn hủy, Tên tour, Người nhận, Kênh hoàn tiền (Cổng MoMo Sandbox trả về tài khoản nguồn).
- **Khối căn cứ hoàn tiền:** Lý do hủy đơn của du khách, chính sách hoàn hủy T24.
- **Khối ghi chú từ Ban Quản Trị:** Phản hồi của Admin khi xét duyệt hồ sơ.

---

### 4.11. Trang Bản Tin & Cẩm Nang Du Lịch (`/posts`)
> **Tệp tin nguồn:** `app/views/posts/index.php`

- **Hero Header:** Badge "Bản Tin Du Lịch & Cộng Đồng Bản Địa", Tiêu đề "Góc Nhìn & Kinh Nghiệm Đi Tour", Nút nổi bật **"Chia Sẻ Câu Chuyện / Viết Bài Mới"** (`.btn-editorial`).
- **3 Tab phân loại dòng tin:**
  - Tab 1: `Tất Cả Dòng Tin` (Kèm badge đếm tổng số).
  - Tab 2: `Bài Viết Cộng Đồng` (Huy hiệu xanh lá, đếm bài của Du khách/Host/Admin).
  - Tab 3: `Tin Tức VnExpress Du Lịch` (Huy hiệu cam, đếm tin RSS từ VnExpress).
- **Lưới bài viết:**
  - Ảnh đại diện 16:9, zoom nhẹ khi hover.
  - Badge góc trên: "Bản Tin Cộng Đồng" hoặc "VnExpress".
  - Huy hiệu vai trò tác giả: `Host Bản Địa` (xanh ngọc), `Du Khách` (xanh dương), `Ban Quản Trị` (đỏ), `Báo Chí` (cam).
  - Tiêu đề bài viết, Đoạn trích dẫn ngắn, Tác giả, Ngày đăng, Lượt xem / Thời gian đọc.
- **Phân trang (Pagination):** 6 tin / trang, có nút Trang trước, các số trang và nút Trang sau.

---

### 4.12. Trang Chi Tiết Bài Viết (`/posts/{slug}`)
> **Tệp tin nguồn:** `app/views/posts/show.php`

- **Breadcrumb:** `Trang chủ` > `Cẩm nang du lịch` > `Tên bài viết`
- **Header:** Tiêu đề H1 lớn, Tác giả kèm badge vai trò, Ngày xuất bản, Số lượt xem.
- **Ảnh bìa đại diện lớn:** Chiều cao tối đa 420px, bo góc 16px.
- **Nội dung bài viết:** Cỡ chữ vừa vặn, giãn dòng thoáng `lh-lg`, hỗ trợ ngắt đoạn tự nhiên.
- **Footer:** Nút "Tất Cả Bài Viết" & Nút chia sẻ (Sao chép URL bài viết vào clipboard).

---

### 4.13. Trang Soạn Thảo & Chia Sẻ Bài Viết Mới (`/posts/create`)
> **Tệp tin nguồn:** `app/views/posts/create.php`

- **Đối tượng sử dụng:** Mở rộng cho cả 3 vai trò: Du khách, Host bản địa, Ban quản trị.
- **Header:** Badge "Góc Chia Sẻ Cộng Đồng", Tiêu đề "Kể Lại Chuyến Đi Của Bạn".
- **Form soạn bài:**
  - Ô nhập tiêu đề bài viết (5 - 255 ký tự).
  - Khung tải ảnh bìa: Hỗ trợ 2 phương thức:
    - *Tải ảnh lên từ máy tính* qua `<input type="file">` kèm khung preview hiển thị ảnh đại diện tức thì.
    - *Hoặc dán URL ảnh có sẵn*.
  - Khung soạn nội dung trải nghiệm (Textarea lớn 10 dòng, tối thiểu 20 ký tự).
  - Nút "Xuất Bản Bài Viết Ngay" (Bài đăng xuất bản ngay lập tức trên sàn).
  - Nút "Hủy & Quay Lại".

---

### 4.14. Trang Hồ Sơ Cá Nhân (`/profile`)
> **Tệp tin nguồn:** `app/views/profile/index.php`

- **Cột trái (Card tóm tắt hồ sơ):**
  - Avatar tròn lớn 88px (icon phân biệt theo vai trò).
  - Họ tên & Email người dùng.
  - Badge vai trò chính thức ("Du Khách Khám Phá", "Dân Bản Địa (Local Host)", "Quản Trị Viên").
  - Box tóm tắt: Trạng thái tài khoản, Ngày tham gia, Giới tính, Ngày sinh, Tỉnh/Thành phố cư trú.
  - Nút tắt: "Vào Bảng Quản Lý Host" (nếu là Host) hoặc "Trung Tâm Admin" (nếu là Admin).
- **Cột phải (Form cập nhật thông tin & Đổi mật khẩu):**
  - **Form 1: Cập nhật hồ sơ cá nhân:**
    - Họ và tên.
    - Email (Readonly có icon khóa).
    - Số điện thoại.
    - **Giới tính:** Combobox chọn Nam, Nữ, Khác.
    - **Ngày sinh:** Ô chọn ngày chuẩn HTML5 `<input type="date">`.
    - **Tỉnh / Thành phố cư trú:** Combobox chọn 29 tỉnh thành phố chuẩn Việt Nam.
    - **Giới thiệu bản thân (Bio):** Textarea với nội dung gợi ý cá nhân hóa theo vai trò.
    - Nút "Lưu Thay Đổi Hồ Sơ".
  - **Form 2: Đổi mật khẩu tài khoản:**
    - Mật khẩu hiện tại, Mật khẩu mới, Xác nhận mật khẩu mới.
    - Nút "Cập Nhật Mật Khẩu".

---

### 4.15. Trang Đăng Nhập (`/login`)
> **Tệp tin nguồn:** `app/views/auth/login.php`

- **Card đăng nhập căn giữa:** Bo góc tròn 24px, đổ bóng nhẹ.
- **Header:** Icon la bàn vàng, tiêu đề "Đăng Nhập".
- **Các trường nhập liệu:**
  - Địa chỉ Email (có icon bì thư).
  - Mật khẩu (có icon ổ khóa + **Nút con mắt bật/tắt hiển thị mật khẩu** `type="password"` $\leftrightarrow$ `type="text"`, tự động đổi icon `bi-eye` / `bi-eye-slash`).
  - Checkbox "Ghi nhớ đăng nhập".
- **Nút đăng nhập:** `.btn-editorial` full-width màu tối thanh lịch.
- **Liên kết:** "Chưa có tài khoản? Đăng ký ngay".
- **Box tài khoản mẫu thử nghiệm nhanh:** Cung cấp sẵn tài khoản mẫu (Admin, Host Đà Lạt, Tourist) kèm mật khẩu `password123`.

---

### 4.16. Trang Đăng Ký Tài Khoản (`/register`)
> **Tệp tin nguồn:** `app/views/auth/register.php`

- **Card đăng ký căn giữa.**
- **Các trường nhập liệu:** Họ và tên, Địa chỉ Email, Số điện thoại, Mật khẩu.
- **Mục chọn vai trò đăng ký (Role Picker):** 2 Radio button dạng thẻ lớn trực quan:
  - Thẻ 1: **Du Khách** (Icon balo, phụ đề: "Xem 3D từ xa & đặt lịch tour").
  - Thẻ 2: **Dân Bản Địa** (Icon ngôi nhà, phụ đề: "Đăng tour & đón tiếp Homestay").
- **Nút hành động:** "Hoàn Tất Đăng Ký".
- **Liên kết:** "Đã có tài khoản? Đăng nhập".

---

## 5. GIAO DIỆN BÀN LÀM VIỆC DÂN BẢN ĐỊA (HOST PORTAL - `/host/*`)

### 5.1. Bảng Điều Khiển Host (Dashboard - `/host`)
> **Tệp tin nguồn:** `app/views/host/dashboard.php`

- **Header:** Badge "Cổng Dân Bản Địa", Badge địa bàn cư trú, Lời chào cá nhân hóa, 3 nút thao tác nhanh:
  - `Quản Lý Đặt Chỗ` (`/host/bookings`)
  - `Mở Lịch Khởi Hành` (`/host/schedules/create`)
  - `Đăng Trải Nghiệm Mới` (`/host/services/create` - nút `.btn-editorial` nổi bật)
- **4 Thẻ chỉ số KPI:**
  - *TỔNG TRẢI NGHIỆM* (Số tour đang quản lý)
  - *HOMESTAY NHÀ DÂN* (Số gói tour có lưu trú)
  - *LỊCH ĐANG MỞ BÁN* (Số đợt khởi hành đang active)
  - *LƯỢT KHÁCH ĐẶT* (Tổng lượt khách đã đăng ký)
- **Bảng danh sách trải nghiệm của bạn:** Tên tour, Danh mục, Đơn giá, Lịch trình, Tình trạng kiểm duyệt (Badge Đang mở bán / Chờ duyệt / Bị từ chối), Cột thao tác (Sửa tour, Tạo lịch, Xem ngoài sàn).
- **Bảng các đợt khởi hành sắp tới:** Giờ đón, Điểm tập trung, Sức chứa chỗ, Tình trạng đặt.

---

### 5.2. Quản Lý Đơn Đặt & Duyệt Giữ Chỗ (`/host/bookings`)
> **Tệp tin nguồn:** `app/views/host/bookings/index.php`

- **5 Thẻ chỉ số KPI:**
  - *TỔNG ĐƠN ĐẶT*
  - *CHỜ DUYỆT (PENDING)* (Viền vàng cảnh báo nhấp nháy, cần phản hồi sớm)
  - *GIỮ CHỖ 15P (AWAITING)* (Đang chờ khách thanh toán cọc)
  - *ĐÃ XÁC NHẬN (CONFIRMED)* (Sẵn sàng đón khách)
  - *DOANH THU TOUR* (Tổng tiền tour đã cọc/thanh toán)
- **Thanh công cụ lọc:** Tìm kiếm từ khóa, Dropdown lọc theo tour, Dropdown lọc theo trạng thái đơn.
- **Bảng quản lý đơn đặt của khách:**
  - Mã đơn `#BK...`.
  - Tên du khách & Số điện thoại.
  - Tên tour & Giờ khởi hành.
  - Số lượng khách tham gia.
  - Tổng tiền & **Tình trạng tiền cọc** (Badge: `Đã cọc 50% (MoMo)`, `Đã thanh toán 100%`, `Chờ thanh toán cọc`).
  - Trạng thái đơn (Badge màu).
  - Cột thao tác: Nút "Duyệt giữ chỗ 15p" (nếu pending), Nút "Từ chối" (modal), Nút "Xem chi tiết".

---

### 5.3. Chi Tiết Đơn Đặt Phía Host (`/host/bookings/{id}`)
> **Tệp tin nguồn:** `app/views/host/bookings/show.php`

- Chi tiết thông tin liên hệ của du khách (Tên, Email, SĐT, ghi chú).
- Lịch trình tour, số khách, điểm đón.
- Bảng bóc tách tiền cọc online và số tiền còn lại thu tiền mặt tại điểm hẹn.
- Các nút thao tác nghiệp vụ:
  - Phê duyệt giữ chỗ 15 phút (cho phép khách thanh toán cọc).
  - Từ chối nhận đơn (bật modal nhập lý do).
  - Xác nhận hoàn tất chuyến đi (khi tour kết thúc).

---

### 5.4. Đăng Ký & Sửa Tour Trải Nghiệm (`/host/services/create` & `edit.php`)
> **Tệp tin nguồn:** `app/views/host/services/create.php` & `app/views/host/services/edit.php`

Biểu mẫu đăng tour được thiết kế thân thiện, đơn giản hóa tối đa không đòi hỏi kiến thức kỹ thuật:
- **Phần 1: Thông tin trải nghiệm cơ bản:**
  - Tiêu đề tour, Danh mục phân loại, Thời lượng (ngày).
  - **Tỉnh / Thành phố:** Dropdown chọn 29 tỉnh thành trọng điểm.
  - **Điểm đón / Điểm hẹn:** Ô nhập địa chỉ thân thiện (VD: Cổng bến thuyền Hồ Tuyền Lâm).
  - **Tọa độ tự động ngầm (Geocoding):** Hệ thống tự động tính Vĩ độ / Kinh độ ngầm và hiển thị badge xác nhận định vị thành công, Host không phải gõ bất kỳ con số thập phân nào.
  - Mô tả ngắn & Mô tả chi tiết hành trình.
- **Phần 2: Chính sách giá & Đặt cọc:**
  - Giá cơ bản / người (Kèm chú thích rõ ràng *"Giá niêm yết đã bao gồm 8% thuế GTGT"*).
  - Tỷ lệ cọc tối thiểu (mặc định 50%).
  - Số lượng khách tối đa cho mỗi đợt tour.
- **Phần 3: Ảnh bìa đại diện:**
  - Tải file ảnh trực tiếp từ máy tính kèm xem trước (preview) hoặc dán URL ảnh.
- **Phần 4: Lịch trình & Điểm dừng chân không gian (3D Tour):**
  - Danh sách các trạm dừng chân trong tour.
  - Mỗi trạm gồm: Tên trạm, Thuyết minh văn hóa truyền thống, **Upload file ảnh trạm dừng trực tiếp từ máy tính** (có thumbnail preview tức thì) hoặc dán link ảnh.
  - Nút "+ Thêm trạm dừng chân" & Nút "Xóa trạm".
- **Phần 5: Thông tin Homestay & Lưu trú nhà dân:**
  - Toggle bật/tắt homestay.
  - Tên homestay, Địa chỉ, Danh sách tiện nghi (Wifi, nước nóng, bữa ăn, đưa đón...), Ảnh phòng nghỉ.
- **Nút gửi:** "Lưu & Gửi Phê Duyệt" (Gửi tour lên cho Admin duyệt mở bán).

---

### 5.5. Quản Lý Lịch Trình Khởi Hành (`/host/schedules` & `create.php`)
> **Tệp tin nguồn:** `app/views/host/schedules/index.php` & `create.php`

- **Trang danh sách lịch:** Bảng thống kê các đợt khởi hành của từng tour, số chỗ tối đa, số chỗ đã đặt, trạng thái đóng/mở.
- **Form tạo lịch mới (`create.php`):**
  - Chọn gói tour trải nghiệm.
  - Thời gian khởi hành (`starts_at` - datetime picker).
  - Thời gian kết thúc (`ends_at` - datetime picker).
  - Sức chứa khách (`capacity`).
  - Điểm tập trung khách cụ thể.
  - Nút "Mở Bán Lịch Khởi Hành".

---

## 6. GIAO DIỆN QUẢN TRỊ HỆ THỐNG (ADMIN PORTAL - `/admin/*`)

### 6.1. Bảng Quản Trị Tổng Quan (`/admin`)
> **Tệp tin nguồn:** `app/views/admin/dashboard.php`

- **Header Admin:** Thanh chuyển nhanh các phân hệ: Trải Nghiệm (Tours), Người Dùng (Users), Danh Mục (Categories), Cẩm Nang (Blog).
- **5 Thẻ thống kê hệ thống:**
  - *NGƯỜI DÙNG* (Tổng tài khoản)
  - *TRẢI NGHIỆM* (Tổng số tour)
  - *CHỜ DUYỆT (PENDING)* (Viền cam nổi bật, số tour Host mới đăng ký cần duyệt)
  - *DANH MỤC* (Tổng danh mục tour)
  - *LƯỢT ĐẶT LỊCH* (Tổng số booking)
- **Bảng duyệt nhanh tour mới đăng ký:**
  - Cột Tên tour, Host bản địa, Danh mục, Giá, Trạng thái.
  - Cột Kiểm duyệt nhanh: Nút xanh **[Duyệt]** mở bán ngay và nút đỏ **[Từ chối]** bật modal nhập lý do gửi lại cho Host.

---

### 6.2. Trung Tâm Kiểm Duyệt & Quản Lý Tour (`/admin/services`)
> **Tệp tin nguồn:** `app/views/admin/services/index.php`

- **4 Thẻ lọc nhanh trạng thái:**
  - *TẤT CẢ TRẢI NGHIỆM*
  - *CHỜ PHÊ DUYỆT (PENDING)* (Có số lượng tour chờ duyệt)
  - *ĐANG MỞ BÁN (APPROVED)*
  - *ĐÃ TỪ CHỐI (REJECTED)*
- **Toolbar tra cứu:** Ô tìm kiếm tên tour/host, Dropdown lọc theo trạng thái.
- **Bảng quản lý tour chi tiết:** Ảnh thumbnail, Tên trải nghiệm, Tên Host, Danh mục, Đơn giá, Trạng thái, Thao tác:
  - Nút xanh lá: `Phê duyệt mở bán`
  - Nút đỏ: `Từ chối` (bật modal nhập lý do chi tiết)
  - Nút xanh dương: `Phê duyệt lại` (đối với tour đã từng bị từ chối)
  - Nút xem trực tiếp ngoài sàn.

---

### 6.3. Quản Lý Người Dùng & Phân Quyền (`/admin/users`)
> **Tệp tin nguồn:** `app/views/admin/users/index.php`

- **4 Thẻ thống kê:** Tổng người dùng, Dân bản địa (Hosts), Du khách (Tourists), Bị khóa (Locked).
- **Toolbar lọc:** Tìm kiếm họ tên/email/SĐT, Dropdown lọc vai trò (`tourist` / `host` / `admin`), Dropdown lọc trạng thái (`active` / `locked`).
- **Bảng danh sách người dùng:**
  - Avatar tròn, Họ tên, Email, Số điện thoại.
  - Badge vai trò chính thức.
  - Vùng cư trú, Ngày tham gia hệ thống.
  - Cột thao tác: Nút Khóa tài khoản (Lock) / Mở khóa tài khoản (Unlock), Phân quyền người dùng.

---

### 6.4. Quản Lý Danh Mục Du Lịch (`/admin/categories`)
> **Tệp tin nguồn:** `app/views/admin/categories/index.php`, `create.php`, `edit.php`

- **Trang danh sách:**
  - Header & Nút "Thêm Danh Mục Mới".
  - Bảng danh mục: Icon biểu trưng (Bootstrap Icons), Tên danh mục, Đường dẫn Slug, Mô tả tóm tắt, Số lượng tour trong danh mục, Badge trạng thái (Hiển thị / Đang ẩn).
  - Nút Sửa & Ẩn danh mục.
- **Form tạo/sửa danh mục (`create.php`, `edit.php`):**
  - Tên danh mục, Đường dẫn (Slug), Icon class (ví dụ `bi-cup-hot`), Mô tả tóm tắt, Checkbox kích hoạt hiển thị. Nút "Lưu Danh Mục".

---

### 6.5. Quản Trị Bài Viết & Cẩm Nang (`/admin/posts`)
> **Tệp tin nguồn:** `app/views/admin/posts/index.php` & `form.php`

- **4 Thẻ thống kê:** Tổng số bài viết, Đã xuất bản, Bản nháp/Đã ẩn, Lượt đọc bài viết.
- **Bảng quản trị bài viết:**
  - Ảnh thumbnail 16:9, Tiêu đề bài viết.
  - Tác giả & Vai trò tác giả.
  - Lượt xem, Ngày đăng, Trạng thái (Đã xuất bản / Đã ẩn).
  - Nút thao tác: Sửa bài, Ẩn/Hiện bài viết vi phạm, Xóa bài viết.
- **Form thêm/sửa bài viết (`form.php`):** Tiêu đề, Slug, Ảnh bìa, Tóm tắt, Nội dung chi tiết, Trạng thái xuất bản.

---

### 6.6. Quản Lý Hồ Sơ Hủy & Hoàn Tiền (`/admin/refunds`)
> **Tệp tin nguồn:** `app/views/admin/refunds/index.php`

- **4 Thẻ chỉ số:** Tổng số hồ sơ, Chờ Admin duyệt, Đã duyệt hoàn, Tổng tiền hoàn qua MoMo Sandbox.
- **Bảng hồ sơ hoàn tiền:**
  - Mã hoàn tiền `#RF...`, Mã đơn `#BK...`.
  - Tên du khách, Tên gói tour hủy.
  - Số tiền yêu cầu hoàn tiền.
  - Lý do hủy tour & Căn cứ hoàn tiền T24.
  - Cột thao tác: Nút "Phê duyệt hoàn tiền qua MoMo API Sandbox", Nút "Từ chối hoàn tiền" (kèm lý do).

---

## 7. CÁC TRANG BÁO LỖI HỆ THỐNG & MODAL TOÀN CỤC

### 7.1. Các Trang Lỗi Hệ Thống (`/errors/403.php`, `404.php`, `419.php`, `500.php`)
Tất cả các trang lỗi đều được khoác lên ngôn ngữ Dark-Alpine cao cấp:
- **Nền trang:** `background: radial-gradient(circle at 50% 30%, rgba(6, 182, 212, 0.15), transparent 70%), #070d17`
- **Card kính mờ trung tâm:** `rgba(13, 26, 45, 0.78)` kết hợp `backdrop-filter: blur(20px)`, bo góc 24px.
- **Thành phần trên thẻ:**
  - Icon lớn phát quang tương ứng từng loại lỗi:
    - `403`: Icon ổ khóa `bi-shield-lock-fill` màu đỏ/cam.
    - `404`: Icon la bàn `bi-compass-fill` màu cyan.
    - `419`: Icon đồng hồ `bi-clock-history` (Hết hạn phiên CSRF).
    - `500`: Icon bánh răng lỗi `bi-exclamation-triangle-fill` màu đỏ.
  - Badge mã lỗi HTTP bo tròn viền phát quang.
  - Tiêu đề thông báo lỗi rõ ràng và đoạn giải thích nguyên nhân thân thiện.
  - Cặp nút điều hướng bo tròn: Nút "Quay Lại" (`history.back()`) & Nút "Về Trang Chủ" (`/`).

---

## 8. BẢNG TRA CỨU NHANH CÁC THÀNH PHẦN GIAO DIỆN (DESIGN TOKENS CHEAT SHEET)

| Thành Phần (Component) | Class CSS Chuẩn / Thuộc Tính | Mô Tả & Vị Trí Áp Dụng |
| :--- | :--- | :--- |
| **Nền Toàn Trang** | `body.theme-dark-alpine` | Nền `#0B1924`, chữ `#F8FAFC`, áp dụng toàn bộ phân hệ du khách. |
| **Đệm Đầu Trang** | `.content-has-top-padding` | `padding-top: 5.5rem` dành cho các trang không có Hero full-bleed. |
| **Mặt Kính Mờ Lớn** | `.glass-panel` | Nền `rgba(19, 42, 60, 0.65)`, blur 12px, viền `rgba(155, 203, 229, 0.22)`, bo góc 16px. |
| **Viên Thuốc Kính Mờ**| `.glass-pill` | Nền `rgba(15, 25, 36, 0.58)`, blur 14px, viền mờ, bo tròn 999px. |
| **Nút Gradient Chính** | `.btn-alpine` | Gradient xanh đậm, viền xanh băng, hover phát sáng và đổi màu chữ tối. |
| **Nút Kính Trên Banner**| `.btn-glass-hero` | Nền mờ trắng 16%, viền trắng 45%, hover đổi sang `--brand-primary`. |
| **Nút Quản Trị / Form** | `.btn-editorial` | Nền xanh đen `#0F2922`, chữ trắng, bo tròn thanh lịch. |
| **Card Poster Tràn Ảnh** | `.experience-poster-card` | Tỉ lệ 3/4, bo góc 18px, ảnh zoom 1.04 khi hover, overlay đen chân ảnh. |
| **Card Trạm Xếp Dọc** | `.vertical-station-card` | Nằm cột phải hero banner chi tiết tour, ảnh vuông 52px, click mở 3D. |
| **Thanh Trượt Ngân Sách**| `.custom-price-range` | Range slider tùy biến, thumb tròn viền trắng phát sáng, nhãn giá real-time. |
| **Trục Dọc Timeline** | `.station-timeline` | Kẻ viền `--brand-mid`, chấm tròn phát quang `station-timeline-dot`. |
| **Huy Hiệu Bảo Chứng** | `badge bg-success-subtle` | Nhãn **"Đã tự trải nghiệm"** bảo chứng đánh giá du khách đã hoàn tất tour. |
| **Hóa Đơn Chuẩn A4** | `@media print` | Tự động ẩn navbar/footer/nút bấm, chuyển nền trắng, chữ đen, viền sắc nét. |
| **Cổng MoMo Sandbox** | Gradient hồng `#A50064` | Header ví MoMo, QR code giả lập, nút trigger IPN tự động. |

---
> *Tài liệu được biên soạn phục vụ cho đợt tinh chỉnh toàn diện giao diện hệ thống OnlineTraveller v2.2.*
