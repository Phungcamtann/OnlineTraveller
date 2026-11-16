# Online Traveller (Nền Tảng Du Lịch Bản Địa & Trải Nghiệm 3D Trực Tuyến)

<p align="center">
  <img src="public/assets/images/logo.png" alt="Online Traveller Logo" width="120" onerror="this.style.display='none'"/>
</p>

<p align="center">
  <b>Hệ thống kết nối Du khách với Người dân bản địa – Khám phá văn hóa qua Không gian 3D tương tác & Đặt tour linh hoạt</b>
</p>

<p align="center">
  <a href="https://onlinetraveller.alwaysdata.net/"><img src="https://img.shields.io/badge/Live_Demo-onlinetraveller.alwaysdata.net-2ea44f?style=for-the-badge&logo=google-chrome&logoColor=white" alt="Live Demo"></a>
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Database-MySQL%20%7C%20MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/3D_Engine-Three.js-000000?style=for-the-badge&logo=threedotjs&logoColor=white" alt="Three.js">
  <img src="https://img.shields.io/badge/Payment-MoMo%20Sandbox-A50064?style=for-the-badge&logo=momo&logoColor=white" alt="MoMo">
  <img src="https://img.shields.io/badge/Tests-22%20Suites%20%7C%20400%2B%20Assertions-brightgreen?style=for-the-badge" alt="Tests">
</p>

---

## 📌 1. Tên Đề Tài & Mô Tả Ngắn

- **Tên đề tài:** **Online Traveller - Nền tảng kết nối du lịch trải nghiệm từ xa và dân bản địa** *(Online Travel & Local Experience Platform)*.
- **Mô tả ngắn:** **Online Traveller** là hệ sinh thái web du lịch độc đáo, được xây dựng theo kiến trúc MVC chuẩn mực bằng PHP thuần và 22 MariaDB Stored Procedures. Dự án giải quyết bài toán cầu nối giữa **Du khách (Tourist)** tìm kiếm trải nghiệm chân thực và **Người dân bản địa (Local Host)** có mong muốn chia sẻ văn hóa địa phương. Điểm nhấn đột phá của nền tảng là tính năng **Tham quan không gian 3D tương tác (Three.js Z-Scroll)** giúp du khách "du hành ảo" qua các trạm trải nghiệm trước khi đặt tour, kết hợp cùng quy trình đặt tour minh bạch, giữ chỗ chống trùng lặp (concurrency), thanh toán **MoMo Sandbox** linh hoạt (cọc 50% hoặc trả đủ 100%) và chính sách hủy hoàn tiền rõ ràng.

---

## 🌐 2. Giới Thiệu Dự Án & Trải Nghiệm Trực Tuyến

> 🔗 **Truy cập phiên bản Demo đang hoạt động thực tế:**  
> 👉 [**https://onlinetraveller.alwaysdata.net/**](https://onlinetraveller.alwaysdata.net/)

### 🎯 Bối Cảnh & Mục Tiêu Dự Án
Các nền tảng du lịch truyền thống thường tập trung vào các tour đại trà, thiếu đi sự gắn kết sâu sắc với đời sống bản địa và khiến du khách khó hình dung thực tế cung đường. **Online Traveller** ra đời nhằm:
1. **Tôn vinh giá trị bản địa:** Giúp người dân địa phương (Đà Lạt, Hội An, Sài Gòn, Huế, Phú Quốc, Hà Giang...) tự thiết kế và chia sẻ các gói trải nghiệm đặc sắc, homestay ấm cúng và những câu chuyện làng nghề.
2. **Du lịch trực quan với công nghệ 3D:** Tái hiện hành trình tour qua trục timeline hình ảnh, âm thanh, câu chuyện và đặc biệt là chế độ **3D Virtual Tour** tương tác chuyển động theo trục Z (Three.js).
3. **Thanh toán & Đặt chỗ thông minh:** Hỗ trợ tính toán khuyến mãi tự động, lựa chọn đặt cọc 50% hoặc thanh toán trực tuyến 100% qua ví điện tử MoMo, tạo mã QR tức thì và phát hành Biên nhận điện tử (E-Receipt).
4. **Bảo vệ quyền lợi hai chiều:** Cơ chế duyệt đơn và khóa giữ chỗ 15 phút tránh quá tải, quy trình hủy và hoàn tiền tự động theo mốc thời gian 24 giờ trước khởi hành (Mốc T24).

### ✨ Các Tính Năng Nổi Bật
- 🕶️ **Trình diễn Không gian 3D Z-Scroll (Three.js):** Du khách chỉ cần cuộn chuột để máy quay 3D đưa bạn lướt qua từng trạm dừng chân, ngắm nhìn hình ảnh thực tế và lắng nghe câu chuyện văn hóa.
- 💳 **Cổng Thanh Toán MoMo Sandbox:** Sinh mã QR thanh toán nhanh, đồng bộ trạng thái đơn hàng thông qua Webhook IPN có chữ ký số bảo mật `HMAC-SHA256`.
- 🏡 **Gắn kết Lưu trú Homestay:** Tích hợp trực tiếp thông tin homestay nhà dân trong tour, hiển thị bản đồ vị trí thực tế.
- 💬 **Cộng đồng Thảo luận Q&A & Đánh giá Sao:** Du khách có thể hỏi đáp trực tiếp với Host bản địa (AJAX 1-cấp) và để lại đánh giá 1–5 sao sau khi hoàn tất chuyến đi.
- 🌤️ **Tích hợp Dữ liệu Thời gian Thực:** Hiển thị thời tiết thực địa qua **OpenWeather API** và cập nhật tin tức báo chí du lịch qua dòng tin RSS **VnExpress Du Lịch**.
- 🛡️ **Bảo mật & Phân quyền RBAC 4 cấp:** Phân tách rõ ràng quyền hạn giữa *Khách vãng lai (Guest)*, *Du khách (Tourist)*, *Người bản địa (Host)* và *Quản trị viên (Admin)*, bảo vệ toàn diện với cơ chế Token CSRF kép.

---

## 👥 3. Thông Tin Các Thành Viên Nhóm Phát Triển & Bảng Phân Công Nhiệm Vụ

Dự án được phân chia theo 4 phân hệ chuyên trách (**TV1 – TV4**) tương ứng với 4 thành viên nhóm. Dựa trên toàn bộ lịch sử **71 commits** thực tế trên nhánh `main`, mỗi thành viên đã trực tiếp thiết kế cơ sở dữ liệu, viết Stored Procedures riêng biệt, xây dựng Controller, View, xử lý tích hợp API và viết bộ kiểm thử tự động tương ứng:

### 📊 3.1. Bảng Tổng Hợp Phân Công Nhiệm Vụ & Phân Hệ Chuyên Trách

| STT | Họ và Tên | MSSV / Email | Phân Hệ Phụ Trách | Mã Stored Procedures | Vai Trò & Nhiệm Vụ Chính Đảm Nhiệm |
|:---:|:---|:---|:---:|:---:|:---|
| **1** | **Phùng Cẩm Tân**<br>*(Nhóm trưởng)* | `phungcamtann@gmail.com` | **TV4** | `pct_sp_*`<br>*(6 SPs)* | **Kiến Trúc Hệ Thống, Bảo Mật RBAC, Cộng Đồng, Blog & Tích Hợp Ngoại Vi**<br>- Khởi tạo kiến trúc Core MVC PSR-4 thuần (`Router`, `Request`, `Response`, `Database PDO Singleton`, `Autoloader`).<br>- Xây dựng hệ thống phân quyền RBAC 4 cấp (`RoleMiddleware`, `AuthMiddleware`, `CsrfMiddleware`, `SecurityHelper`).<br>- Kênh hỏi đáp thảo luận Q&A 1-cấp thời gian thực dưới tour (AJAX có huy hiệu Host bản địa).<br>- Đánh giá 1–5 sao sau khi hoàn tất chuyến đi (cơ chế chặn review chéo khi chưa đi tour).<br>- Phân hệ Blog cẩm nang du lịch: gộp chung dòng tin cộng đồng & tin tức báo chí thời sự.<br>- Tích hợp 2 API ngoại vi: thời tiết **OpenWeatherMap** & dòng tin tức **VnExpress Du Lịch RSS**.<br>- Quản lý GitHub workflow, review/merge PRs, đóng gói CSDL `production_install.sql` & Triển khai máy chủ AlwaysData. |
| **2** | **Dương Hoàng Sâm** | **31241026707**<br>`samduong.31241026707@st.ueh.edu.vn` | **TV1** | `dhs_sp_*`<br>*(5 SPs)* | **Cơ Sở Dữ Liệu, Xác Thực, Admin Quản Trị, 3D Hero Banner & QA Test Lead**<br>- Thiết kế, chuẩn hóa toàn bộ lược đồ CSDL (`schema.sql`, `install.sql`, `seed_data.sql`).<br>- Viết script đồ sộ `database/fix_alwaysdata_collation.sql` (1.254 dòng) khắc phục triệt để lỗi xung đột collation trên AlwaysData.<br>- Thiết kế module Xác thực người dùng (Đăng ký mã hóa Bcrypt, Đăng nhập an toàn, Quên mật khẩu).<br>- Nâng cấp hồ sơ người dùng (`07_user_profile_enhancement.sql`): cập nhật avatar, số điện thoại, CCCD định danh.<br>- Quản trị Admin: CRUD danh mục trải nghiệm (`CategoryController`) & Khóa/Mở khóa tài khoản vi phạm.<br>- Thiết kế hệ thống 3D Hero Banners trên Trang chủ & Trang chi tiết tour (`Update 3D banner`, `Fix_Detail_Banner`).<br>- Lập trình **Master Test Suite Runner** (`tests/run_all_tests.php`) chạy 22+ test suites & soạn thảo `SYSTEM_TESTING_GUIDE.md`.<br>- Hỗ trợ xây dựng các phân hệ chéo: Thảo luận (`Discussion`), Bài viết Admin (`Post`), Đánh giá (`Review`). |
| **3** | **Phạm Thiên Long** | **31241021371**<br>`longpham.31241021371@st.ueh.edu.vn` | **TV2** | `ptl_sp_*`<br>*(5 SPs)* | **Đồ Họa 3D Three.js Z-Scroll, Quản Lý Tour, Lưu Trú Homestay & Phân Hệ Host**<br>- Trực tiếp nghiên cứu, lập trình tính năng đột phá **Tham quan thực tế ảo 3D (Three.js Z-Scroll Virtual Tour)** với hơn 1.200 dòng mã WebGL.<br>- Tích hợp hệ thống Camera Z-Scroll cuộn mượt xuyên không gian qua các trạm dừng chân 3D, hiệu ứng ánh sáng ambient, billboard thông tin và particle bụi không gian.<br>- Tinh chỉnh giao diện tối (Dark mode) sang trọng cho trải nghiệm 3D và viết 2 tài liệu đặc tả đồ họa 3D.<br>- Xây dựng trọn vẹn phân hệ Dân bản địa (Host Module): tạo và cập nhật gói trải nghiệm bản địa đa trạm dừng chân (`stops`).<br>- Quản lý cơ sở lưu trú Homestay nhà dân tích hợp trực tiếp trong tour (quan hệ 1-1).<br>- Quản lý lịch trình khởi hành (`ScheduleController`): mở lịch, kiểm soát sức chứa tối đa và xử lý hủy lịch tour.<br>- Xây dựng tính năng Host tiếp nhận, theo dõi và duyệt đơn đặt chỗ của du khách theo thời gian thực.<br>- Cải tiến Form đăng tour, giao diện chi tiết tour (`services/show.php`) và khối thông tin Đánh giá sao. |
| **4** | **Nguyễn Duy Thành Tài** | `nguyenduythanhtai050706@gmail.com` | **TV3** | `ndtt_sp_*`<br>*(6 SPs)* | **Báo Giá, Đặt Chỗ, Cổng MoMo Sandbox, Hoàn Tiền T24, UI Dark-Alpine & Đặc Tả Hệ Thống**<br>- Xây dựng công cụ Báo giá thông minh (`PricingService`): tính khuyến mãi, voucher, lựa chọn trả cọc 50% hoặc trả đủ 100%.<br>- Quy trình đặt chỗ của Du khách (`BookingController`): tạo đơn, cơ chế khóa giữ chỗ tạm thời (Hold concurrency) 15 phút chống trùng chỗ.<br>- Tích hợp cổng thanh toán trực tuyến **MoMo Sandbox** (`MomoClient`): sinh mã QR, trang quét mã giả lập, xác thực Webhook IPN HMAC-SHA256.<br>- Xử lý quy trình hủy tour và hoàn tiền tự động theo mốc thời gian T24 (trước 24h) & Giao diện Admin duyệt lệnh hoàn tiền MoMo.<br>- Phát hành Biên nhận điện tử (E-Receipt) minh bạch với đầy đủ thông tin thanh toán.<br>- Lập kế hoạch và hiện thực hóa đại tu giao diện **Dark-Alpine Glassmorphism** (UI Refactor 2) & Tái cấu trúc bộ trang lỗi hệ thống (`403`, `404`, `419`, `500`).<br>- Chủ biên soạn thảo tài liệu phân tích thiết kế đồ sộ `docs/SYSTEM_SPECIFICATION.md` (3.000 dòng đặc tả) và các tài liệu thiết kế UI. |

---

### 🔍 3.2. Bảng Thống Kê Chi Tiết Đóng Góp Kỹ Thuật Theo Lịch Sử Git Commit (Main Branch)

Dưới đây là bảng tổng hợp định lượng và đối chiếu kỹ thuật dựa trên toàn bộ **71 commits** đã được kiểm duyệt và đưa vào nhánh `main`:

| Tiêu Chí Đánh Giá | TV1 – Dương Hoàng Sâm | TV2 – Phạm Thiên Long | TV3 – Nguyễn Duy Thành Tài | TV4 – Phùng Cẩm Tân *(Lead)* |
|:---|:---|:---|:---|:---|
| **Số Commit Trên `main`** | **16 commits** | **17 commits** | **16 commits** | **22 commits** *(+ 3 PR Merges)* |
| **Phân Hệ Đảm Nhiệm** | TV1 (`dhs_sp_*`) | TV2 (`ptl_sp_*`) | TV3 (`ndtt_sp_*`) | TV4 (`pct_sp_*`) |
| **Số Stored Procedures** | **5 Procedures** (`dhs_sp_register_user`, `authenticate_user`, `update_user_profile`, `admin_set_user_status`, `manage_category`) | **5 Procedures** (`ptl_sp_create_service_with_stops`, `update_service`, `add_schedule`, `cancel_schedule`, `get_service_detail`) | **6 Procedures** (`ndtt_sp_calculate_quote`, `create_booking_hold`, `confirm_payment`, `request_cancel_booking`, `admin_process_refund`, `get_receipt`) | **6 Procedures** (`pct_sp_create_discussion`, `reply_discussion`, `create_review`, `create_post`, `list_posts`, `get_post_detail`) |
| **Tầng Kiến Trúc & CSDL** | - Thiết kế DDL CSDL `schema.sql`<br>- Viết `fix_alwaysdata_collation.sql` (1.254 dòng)<br>- Chuẩn hóa `install.sql` & `seed.php` | - Thiết kế lược đồ quan hệ Tour - Điểm dừng (`stops`) - Homestay lưu trú (1-1)<br>- Quản lý lịch khởi hành | - Thiết kế quan hệ Booking - Payments - Refunds<br>- Xây dựng cơ chế khóa chỗ tạm thời 15 phút (Hold lock) | - Khởi tạo Framework Core MVC PSR-4 (`Router`, `Database`, `Request`, `Response`)<br>- Đóng gói `production_install.sql` (1.666 dòng) |
| **Tầng Logic Nghiệp Vụ (Backend)** | - `AuthController`, `AuthService`<br>- `AdminController` (Users)<br>- `CategoryController`, `Category`<br>- `User` Model | - `HostController`, `TourService`<br>- `ScheduleController`, `ScheduleService`<br>- `Service`, `Accommodation`<br>- `ServiceSchedule` | - `BookingController`, `BookingService`<br>- `PricingService`, `PaymentService`<br>- `PaymentController`, `ReceiptController`<br>- `RefundController`, `Booking`, `Payment` | - `DiscussionController`, `PostController`<br>- `ReviewController`, `HomeController`<br>- `SecurityHelper`, `Discussion`, `Post`<br>- Phân quyền RBAC Middleware |
| **Tầng Giao Diện (Frontend & 3D)** | - Giao diện Đăng nhập / Đăng ký<br>- Giao diện Hồ sơ cá nhân (`profile`)<br>- Quản lý Admin Users & Categories<br>- Thiết kế 3D Hero Banners Trang chủ & Chi tiết | - **Đồ họa 3D Three.js Z-Scroll (1.200+ dòng WebGL)**<br>- Giao diện Host Dashboard & Quản lý tour<br>- Form đăng tour đa trạm dừng chân<br>- Khối Homestay & Chi tiết tour Dark mode | - **Đại tu UI Dark-Alpine Glassmorphism**<br>- Giao diện Đặt chỗ (`bookings/create`)<br>- Chi tiết đơn hàng & E-Receipt<br>- Bộ trang lỗi hệ thống (`403`, `404`, `419`, `500`) | - Giao diện Trang chủ (Hero, Dòng tin tức)<br>- Giao diện Blog cẩm nang du lịch<br>- Khung Thảo luận Q&A (AJAX có huy hiệu Host)<br>- Bộ nhận diện Logo SVG, Favicon |
| **Tích Hợp API Ngoại Vi** | - Đồng bộ Collation máy chủ MariaDB AlwaysData | - Thư viện Three.js WebGL Engine | - Cổng thanh toán **MoMo Sandbox API** (QR Code, Webhook IPN HMAC-SHA256) | - **OpenWeatherMap API** (Thời tiết thực địa)<br>- **VnExpress RSS Du Lịch** (Tin tức báo chí) |
| **Kiểm Thử Tự Động (Tests)** | - **Master Test Runner** (`run_all_tests.php`)<br>- `test_sprint2_tv1_06`<br>- `test_phase4_profile_enhancements`<br>- `test_step3_services_show`<br>- `test_sprint2_tv4_06` | - `test_tv2_01` -> `test_tv2_04`<br>- `test_sprint2_tv2_05`<br>- `test_phase5_host_services`<br>- `test_phase2_reviews` | - `test_tv3_01` -> `test_tv3_04`<br>- `test_sprint2_tv3_05`<br>- `test_sprint2_tv3_06`<br>- `test_admin_service_moderation`<br>- `test_booking_flow_csrf` | - `test_tv4_01` -> `test_tv4_04`<br>- `test_phase3_community_posts`<br>- `test_phase0`<br>- `test_sprint3_integration` |
| **Tài Liệu Kỹ Thuật (Docs)** | - `SYSTEM_TESTING_GUIDE.md`<br>- `THREEJS_AND_3D_BANNERS_SPEC.md`<br>- `THREEJS_3D_SCROLL_AND_BANNERS_SPEC.md`<br>- `phase4_profile_enhancements.md` | - `DEDICATED_3D_VIRTUAL_TOUR_SPEC.md`<br>- `UPGRADE_3D_SCROLL_CANVA_SPEC.md`<br>- `thiet-ke-tinh-chinh.html`<br>- `phase5_host_services.md` | - `SYSTEM_SPECIFICATION.md` (3.000 dòng)<br>- `UI_LAYOUT_DESIGN_SYSTEM.md`<br>- `UI_OVERHAUL_SPEC.md`<br>- `admin_service_moderation.md`<br>- `phase6_slider_cards_banner.md` | - `README.md` chính thức dự án<br>- `UI_SPEC_FOR_AGENT.md`<br>- `phase3_community_posts.md` |
| **Mức Độ Hoàn Thành** | **100% (Xuất sắc)** | **100% (Xuất sắc)** | **100% (Xuất sắc)** | **100% (Xuất sắc)** |

---

### 📝 3.3. Chi Tiết Đầu Việc & Đóng Góp Kỹ Thuật Thực Tế Của Từng Thành Viên

#### 1. Phùng Cẩm Tân *(Nhóm trưởng – Phụ trách TV4)*
- **Điều phối & Quản trị mã nguồn:** Khởi tạo repository, thiết lập quy chuẩn cấu trúc thư mục MVC, điều phối tiến độ của 4 phân hệ, phụ trách review code và hợp nhất các nhánh tính năng qua Pull Requests (`PR #1`, `PR #2`, `PR #3`).
- **Nền tảng Framework Core MVC:** Tự xây dựng toàn bộ tầng Core nhẹ chuẩn PSR-4 không dùng thư viện ngoài:
  - `core/Autoloader.php`: Nạp tự động các Class theo cấu trúc thư mục.
  - `core/Router.php`, `core/Request.php`, `core/Response.php`: Điều phối request HTTP, bóc tách tham số GET/POST/JSON và hỗ trợ RESTful routing.
  - `core/Database.php`: Quản lý kết nối PDO theo mẫu thiết kế Singleton, tối ưu hóa thực thi Stored Procedures với tham số IN/OUT.
  - `core/BaseController.php`, `core/BaseModel.php`: Cung cấp hàm render view, nạp partial, xử lý JSON response và đóng gói truy vấn.
- **Bảo mật & Phân quyền RBAC 4 cấp:**
  - Thiết kế hệ thống Middleware bảo mật: `AuthMiddleware`, `CsrfMiddleware`, `RoleMiddleware`, `SecurityHelper`.
  - Phân tách quyền hạn chặt chẽ: *Guest (Khách vãng lai)*, *Tourist (Du khách)*, *Host (Dân bản địa)*, *Admin (Quản trị viên)*.
  - Cơ chế Token CSRF kép chống tấn công giả mạo (bảo vệ cả Form HTML và AJAX Header `X-CSRF-TOKEN`).
- **Phân hệ Cộng đồng, Đánh giá, Blog & Tích hợp Ngoại vi (`pct_sp_*`):**
  - Viết 6 Stored Procedures phân hệ TV4 quản lý thảo luận, đánh giá sao và cẩm nang du lịch.
  - Tích hợp **OpenWeatherMap REST API** (`OpenWeatherClient.php`): bóc tách thông tin thời tiết thời gian thực tại các tọa độ du lịch.
  - Tích hợp **VnExpress RSS Du Lịch** (`RssClient.php`): bộ bóc tách XML tự động nạp các tin tức du lịch trong nước mới nhất.
  - Xây dựng luồng Thảo luận hỏi đáp (Q&A) 1 cấp tương tác AJAX nhanh chóng, tự động nhận diện và gắn huy hiệu **Host bản địa**.
  - Xây dựng cơ chế Đánh giá 1–5 sao sau khi hoàn tất chuyến đi, chặn hành vi đánh giá chéo khi chưa có đơn hàng thành công.
- **Tối ưu Triển khai Hosting AlwaysData:**
  - Đóng gói script cài đặt hoàn chỉnh `database/production_install.sql` (1.666 dòng), cập nhật dữ liệu thực tế 8 tour mẫu (`database/seed_data.sql`).
  - Thiết kế logo SVG, Favicon chuẩn kích thước và cấu hình tệp định tuyến `.htaccess` tương thích hoàn hảo với shared hosting Apache.

#### 2. Dương Hoàng Sâm *(Phụ trách TV1)*
- **Thiết kế & Tối ưu Cơ sở Dữ liệu Toàn Hệ Thống:**
  - Soạn thảo và chuẩn hóa lược đồ CSDL DDL (`database/schema.sql`), script cài đặt ban đầu (`database/install.sql`), dữ liệu mẫu (`database/seed.php`, `seed_data.sql`).
  - Viết tệp script `database/fix_alwaysdata_collation.sql` (1.254 dòng) khắc phục triệt để lỗi xung đột collation `utf8mb4_unicode_ci` / `utf8mb4_0900_ai_ci` trên máy chủ MariaDB AlwaysData.
  - Đồng bộ và chuẩn hóa toàn bộ 22 Stored Procedures của 4 thành viên đảm bảo tính toàn vẹn dữ liệu.
- **Phân hệ Xác thực, Hồ sơ Người dùng & Phân quyền (`dhs_sp_*`):**
  - Viết 5 Stored Procedures phân hệ TV1 quản lý đăng ký, đăng nhập, hồ sơ người dùng, phân quyền và danh mục tour.
  - Xây dựng module Xác thực: `AuthController.php`, `AuthService.php`, `User.php`; bảo mật mật khẩu một chiều với thuật toán `Bcrypt`.
  - Phát triển giao diện Đăng nhập (`auth/login.php`), Đăng ký (`auth/register.php`), Hồ sơ người dùng (`profile/index.php`), Modal thông báo dùng chung (`notification-modal.php`).
  - Nâng cấp hồ sơ người dùng (`07_user_profile_enhancement.sql`): hỗ trợ cập nhật avatar, số điện thoại, định danh căn cước công dân.
- **Phân hệ Quản trị Admin:**
  - Quản lý danh mục gói trải nghiệm: `CategoryController.php`, `Category.php`, giao diện Admin CRUD danh mục (`admin/categories/index.php`, `create.php`, `edit.php`).
  - Quản trị tài khoản người dùng: `AdminController.php`, giao diện danh sách người dùng (`admin/users/index.php`), tính năng Admin khóa (Lock) hoặc mở khóa (Active) tài khoản vi phạm.
- **Thiết kế 3D Hero Banners & Hỗ trợ Đa Phân Hệ:**
  - Thiết kế hệ thống 3D Hero Banners trên Trang chủ và Trang chi tiết tour (`Update 3D banner`, `Fix_Detail_Banner`).
  - Soạn thảo 2 tài liệu đặc tả đồ họa banner: `docs/THREEJS_AND_3D_BANNERS_SPEC.md` và `docs/THREEJS_3D_SCROLL_AND_BANNERS_SPEC.md`.
  - Hỗ trợ xây dựng giao diện Thảo luận (`DiscussionController.php`), quản trị Bài viết tin tức (`PostController.php`), và tinh chỉnh khối Đánh giá sao (`Fix_Ui đánh giá`).
- **Hệ thống Kiểm thử Tự động (QA Lead):**
  - Lập trình **Master Test Suite Runner** (`tests/run_all_tests.php`) chạy tự động 22+ test suites với hơn 400+ assertions.
  - Soạn thảo cẩm nang hướng dẫn kiểm thử toàn diện `docs/SYSTEM_TESTING_GUIDE.md` (255 dòng).

#### 3. Phạm Thiên Long *(Phụ trách TV2)*
- **Đột phá Đồ họa 3D Three.js Z-Scroll (Virtual Tour):**
  - Trực tiếp nghiên cứu kiến trúc WebGL, thiết kế và lập trình tính năng **Tham quan thực tế ảo 3D (Three.js Z-Scroll Virtual Tour)** tại `app/views/services/virtual_tour.php` với hơn 1.200 dòng mã JavaScript/WebGL.
  - Tích hợp kỹ thuật Camera Z-Scroll: người dùng cuộn chuột để máy quay 3D di chuyển mượt mà xuyên không gian qua các trạm dừng chân hình ảnh.
  - Tích hợp hệ thống chiếu sáng (Ambient & Directional light), các tấm bảng thông tin trạm dừng (billboard), và hệ thống hạt particle mô phỏng bụi mờ lơ lửng tạo cảm giác chiều sâu không gian chân thực.
  - Tinh chỉnh giao diện tối (Dark mode) sang trọng cho trải nghiệm 3D (`Tích hợp giao diện tối cho 3D scroll`, `Fix màu UI`, `Sửa nút mở trang 3D`, `Update 3D scroll`).
  - Soạn thảo 2 tài liệu đặc tả đồ họa 3D chuyên sâu: `docs/DEDICATED_3D_VIRTUAL_TOUR_SPEC.md` và `docs/UPGRADE_3D_SCROLL_CANVA_SPEC.md`.
- **Phân hệ Quản lý Tour & Dân bản địa (`ptl_sp_*`):**
  - Viết 5 Stored Procedures phân hệ TV2 quản lý gói tour, các trạm dừng chân (`stops`), homestay và lịch khởi hành.
  - Xây dựng tầng Models & Services: `Service.php`, `Accommodation.php`, `ServiceSchedule.php`, `TourService.php`, `ScheduleService.php`.
  - Xây dựng toàn bộ giao diện và nghiệp vụ dành cho Local Host:
    - Host Dashboard (`app/views/host/dashboard.php`): thống kê tour, lịch và đơn đặt chỗ.
    - Tạo và chỉnh sửa gói tour đa trạm dừng chân (`host/services/create.php`, `edit.php`, `index.php`).
    - Quản lý cơ sở lưu trú Homestay nhà dân tích hợp trực tiếp trong tour (quan hệ 1-1).
    - Mở lịch khởi hành, quản lý sức chứa tối đa và hạn đăng ký (`host/schedules/create.php`, `index.php`).
    - Tính năng Host tiếp nhận, theo dõi và duyệt đơn đặt chỗ của du khách theo thời gian thực (`host/bookings/index.php`, `show.php`).
- **Cải tiến Giao diện & Trải nghiệm Tour:**
  - Tinh chỉnh trang chi tiết tour (`services/show.php`), tích hợp khối Homestay, timeline các trạm dừng và khối Đánh Giá Tour.
  - Hỗ trợ sửa UI thanh toán (`Fix_UI_Thanh_Toán`), đồng bộ nút chuyển đổi giữa Timeline tour và Không gian 3D thực tế ảo.
  - Soạn thảo tài liệu thiết kế tinh chỉnh `docs/thiet-ke-tinh-chinh.html` (722 dòng) và tài liệu vận hành bảo mật.
  - Viết các bộ kiểm thử tự động phân hệ TV2: `test_tv2_01.php` đến `04`, `test_sprint2_tv2_05.php`, `test_phase5_host_services.php`, `test_phase2_reviews.php`.

#### 4. Nguyễn Duy Thành Tài *(Phụ trách TV3)*
- **Cổng Thanh toán Trực tuyến MoMo Sandbox:**
  - Xây dựng lớp tích hợp cổng thanh toán `app/integrations/MomoClient.php`: sinh mã QR thanh toán nhanh, đồng bộ trạng thái thanh toán qua Webhook IPN bảo mật với chữ ký số `HMAC-SHA256`.
  - Phát triển giao diện quét mã thanh toán giả lập `app/views/payments/sandbox_qr.php` và trang xử lý kết quả thanh toán `app/views/payments/callback.php`.
  - Tinh chỉnh công thức tính thuế/phí minh bạch trong báo giá booking (`Fix thuế thanh toán`).
- **Quy trình Đặt chỗ, Báo giá Thông minh & Hoàn tiền T24 (`ndtt_sp_*`):**
  - Viết 6 Stored Procedures phân hệ TV3 quản lý báo giá, đặt chỗ, xác nhận thanh toán, hoàn tiền và biên nhận.
  - Xây dựng công cụ Báo giá thông minh (`PricingService.php`): tính toán tiền phòng, phụ phí, áp dụng mã giảm giá, hỗ trợ 2 phương thức thanh toán (*Đặt cọc 50%* hoặc *Thanh toán 100%*).
  - Xây dựng cơ chế đặt tour (`BookingController.php`, `BookingService.php`): tạo đơn đặt chỗ và kích hoạt cơ chế khóa giữ chỗ tạm thời (Hold concurrency) trong 15 phút chống trùng chỗ.
  - Quy trình Hủy tour và Hoàn tiền tự động theo mốc thời gian T24 (trước khởi hành 24 giờ), xây dựng màn hình Admin xét duyệt và thực hiện lệnh hoàn tiền qua MoMo (`admin/refunds/index.php`).
  - Phát hành Biên nhận điện tử (E-Receipt) minh bạch tại `receipts/show.php` xác nhận chi tiết giao dịch.
  - Khắc phục lỗi tương tác trạng thái nút duyệt đơn booking cho Host (`Fix nút_Duyệt`).
- **Đại tu Toàn diện Giao diện Dark-Alpine Glassmorphism (UI Refactor 2):**
  - Lập kế hoạch và hiện thực hóa ngôn ngữ thiết kế **Dark-Alpine Glassmorphism** (`UI_Refactor_2`, commit `327582c`), tái thiết kế các card tour, slider, banner, form đặt tour sang trọng.
  - Thiết kế lại toàn bộ bộ trang lỗi hệ thống (`Refactor_Error_Page`): `403.php`, `404.php`, `419.php`, `500.php` theo nhận diện thương hiệu Online Traveller.
- **Chủ biên Tài liệu Đặc tả Hệ thống & Kiến trúc:**
  - Chủ biên tài liệu đặc tả phân tích & thiết kế hệ thống đồ sộ `docs/SYSTEM_SPECIFICATION.md` (gần 3.000 dòng đặc tả nghiệp vụ chi tiết).
  - Biên soạn các tài liệu hướng dẫn thiết kế: `docs/UI_LAYOUT_DESIGN_SYSTEM.md`, `docs/UI_OVERHAUL_SPEC.md`, `docs/admin_service_moderation.md`, `docs/phase6_slider_cards_banner.md`, `docs/plan_refactor_ui.md`.
  - Viết các bộ kiểm thử tự động phân hệ TV3: `test_tv3_01.php` đến `04`, `test_sprint2_tv3_05.php`, `test_sprint2_tv3_06.php`, `test_admin_service_moderation.php`, `test_booking_flow_csrf.php`, `test_phase6_slider_cards_banner.php`.

---

## 💻 4. Công Nghệ Sử Dụng (Tech Stack)

Dự án được xây dựng với phương châm **tối ưu hiệu năng, bảo mật cao và giảm thiểu phụ thuộc thư viện cồng kềnh**:

### ⚙️ Backend
- **Ngôn ngữ:** `PHP 8.2+` (Strict typing, áp dụng đầy đủ tính năng hiện đại của PHP 8).
- **Kiến trúc:** Custom MVC Framework nhẹ, cấu trúc rõ ràng (`Core\Router`, `Core\Request`, `Core\Response`, `Core\Database`, `Core\Middleware`).
- **Cơ sở dữ liệu:** `MySQL` / `MariaDB` (Hỗ trợ chuẩn mã hóa `utf8mb4_unicode_ci`).
- **Database Logic:** **22 Stored Procedures** xử lý toàn bộ nghiệp vụ phức tạp, bảo đảm tính toàn vẹn dữ liệu và chống tấn công SQL Injection ở tầng sâu nhất.

### 🎨 Frontend & Hiệu Ứng
- **Giao diện:** HTML5, CSS3 hiện đại theo ngôn ngữ **Dark-Alpine Glassmorphism** (nền tối sang trọng, thẻ kính mờ, ambient glow, đổ bóng tinh tế).
- **Mã nguồn tương tác:** Vanilla JavaScript (ES6+), Fetch API cho các tương tác thời gian thực không tải lại trang.
- **Đồ họa 3D WebGL:** **Three.js** kết hợp hiệu ứng cuộn camera Z-Scroll độc đáo để tham quan các trạm dừng chân.
- **Biểu tượng:** Bootstrap Icons v1.11+.

### 🔌 Tích Hợp API & Dịch Vụ Ngoài
- **Cổng thanh toán MoMo (Sandbox):** Tạo yêu cầu thanh toán quét mã QR, xử lý IPN Webhook với chữ ký số `HMAC-SHA256`.
- **OpenWeatherMap API:** Hiển thị thời tiết thời gian thực (nhiệt độ, độ ẩm, biểu tượng thời tiết) tại các địa danh du lịch.
- **VnExpress Du Lịch RSS:** Tự động lấy và cập nhật những dòng tin tức du lịch mới nhất.
- **Bản đồ:** Tích hợp toạ độ GPS và nhúng bản đồ vị trí trực quan.

### 🛡️ Bảo Mật & Kiểm Thử
- **Bảo mật:** CSRF Token kép (chống CSRF trên cả Form POST và AJAX Header), chống XSS qua cơ chế escape dữ liệu, băm mật khẩu chuẩn `Bcrypt`, kiểm soát phiên đăng nhập chặt chẽ.
- **Kiểm thử tự động:** **Master Test Suite Runner** (`tests/run_all_tests.php`) chạy 22 bộ kiểm thử tự động với hơn **400+ assertions** bao quát toàn bộ chức năng từ giao diện UI, Stored Procedures, Bảo mật CSRF/RBAC đến các kịch bản tích hợp đa phân hệ.

---

## 📂 5. Cấu Trúc Mã Nguồn (Project Structure)

Mã nguồn được phân bổ khoa học theo mô hình MVC:

```text
OnlineTraveller/
├── app/                        # Mã nguồn tầng nghiệp vụ ứng dụng
│   ├── controllers/            # Bộ điều hướng xử lý Request (Home, Auth, Service, Booking, Host, Admin, ...)
│   ├── helpers/                # Các hàm trợ giúp dùng chung (Security, Flash, View, Format...)
│   ├── integrations/           # Lớp tích hợp dịch vụ ngoài (MoMo Gateway, Weather API, RSS Feed...)
│   ├── middleware/             # Bộ lọc trung gian (AuthMiddleware, CsrfMiddleware, RoleMiddleware)
│   ├── models/                 # Lớp tương tác CSDL & gọi Stored Procedures
│   ├── services/               # Xử lý logic nghiệp vụ bổ trợ
│   └── views/                  # Giao diện hiển thị (Layouts, Partials, Tour, Booking, Profile, Admin, Host...)
├── bootstrap/                  # Nạp cấu hình khởi động ứng dụng (app.php)
├── config/                     # Thư mục cấu hình hệ thống
│   ├── app.php                 # Cấu hình môi trường, URL gốc, múi giờ
│   ├── database.php            # Cấu hình kết nối PDO MariaDB
│   ├── integrations.php        # Cấu hình API OpenWeather, RSS VnExpress
│   ├── payment.php             # Cấu hình thông số MoMo Sandbox (Partner Code, Secret Key...)
│   └── local.example.php       # File mẫu cấu hình môi trường phát triển cục bộ
├── core/                       # Khung sườn kiến trúc cốt lõi (Framework Core tự viết)
│   ├── Autoloader.php          # Tự động nạp Class chuẩn PSR-4
│   ├── BaseController.php      # Controller cơ sở hỗ trợ render view, trả json, redirect
│   ├── BaseModel.php           # Model cơ sở đóng gói thao tác truy vấn CSDL
│   ├── Database.php            # Quản lý kết nối PDO Singleton & thực thi Stored Procedures
│   ├── Middleware.php          # Interface cho các tầng middleware
│   ├── Request.php             # Đóng gói dữ liệu HTTP Request, GET/POST/JSON, CSRF
│   ├── Response.php            # Đóng gói HTTP Response, thiết lập Header, mã trạng thái
│   └── Router.php              # Xử lý định tuyến URL động theo mẫu Restful
├── database/                   # Toàn bộ mã nguồn Cơ sở dữ liệu
│   ├── procedures/             # 22 Stored Procedures phân theo 4 thành viên
│   │   ├── 01_users_categories.sql     # TV1 (dhs_sp_*): Tài khoản & Danh mục
│   │   ├── 02_services_bookings.sql    # TV2 (ptl_sp_*): Tour & Lịch trình
│   │   ├── 03_promotions_payments.sql  # TV3 (ndtt_sp_*): Báo giá, Booking & MoMo
│   │   └── 04_community_content.sql    # TV4 (pct_sp_*): Thảo luận, Đánh giá & Blog
│   ├── install.sql             # Script cài đặt nạp toàn bộ CSDL cục bộ
│   ├── production_install.sql  # Script tổng hợp dùng để deploy máy chủ AlwaysData
│   ├── schema.sql              # Cấu trúc bảng CSDL (DDL)
│   └── seed_data.sql           # Dữ liệu mẫu ban đầu (8 tour, hình ảnh trạm 3D, users)
├── docs/                       # Tài liệu kỹ thuật dự án & Hướng dẫn kiểm thử
│   ├── SYSTEM_SPECIFICATION.md # Đặc tả hệ thống chi tiết (Bản phân tích thiết kế đầy đủ)
│   ├── SYSTEM_TESTING_GUIDE.md # Cẩm nang hướng dẫn kiểm thử tự động & ma trận 6 phân hệ
│   └── ...                     # Các tài liệu đặc tả giai đoạn (Phase 1 -> Phase 6, Three.js 3D)
├── public/                     # Thư mục công khai (Web Root)
│   ├── assets/                 # Tài nguyên tĩnh: CSS, JS, hình ảnh tour, biểu tượng
│   ├── index.php               # Điểm đón đầu mọi request (Front Controller)
│   └── .htaccess               # Cấu hình định tuyến Rewrite sang index.php
├── routes/                     # Khai báo tuyến đường URL
│   ├── web.php                 # Các route giao diện web chính
│   ├── api.php                 # Các route API nội bộ (AJAX)
│   └── callbacks.php           # Các route lắng nghe phản hồi từ MoMo (IPN / Return)
├── storage/                    # Thư mục chứa file tải lên, log, cache
├── tests/                      # Bộ kiểm thử tự động toàn diện
│   ├── run_all_tests.php       # Master Test Suite Runner (chạy 14 bộ test)
│   └── ...                     # Các file test case chi tiết từng phân hệ
├── .htaccess                   # Bảo vệ thư mục gốc & chuyển tiếp vào public/ trên shared hosting
└── README.md                   # Tài liệu hướng dẫn tổng quan dự án
```

---

## 🚀 6. Hướng Dẫn Cài Đặt & Chạy Thử Trên Máy Cá Nhân

### 📋 Yêu Cầu Hệ Thống
- **Hệ điều hành:** Windows 10/11, macOS hoặc Linux.
- **Web Server & PHP:** `PHP >= 8.1` hoặc `PHP 8.2+` (Khuyến nghị sử dụng XAMPP, Laragon hoặc PHP CLI có sẵn pdo_mysql, curl, mbstring, openssl).
- **Hệ quản trị CSDL:** `MySQL >= 5.7` hoặc `MariaDB >= 10.4`.

---

### 🔧 Các Bước Triển Khai Cục Bộ

#### Bước 1: Sao chép mã nguồn về máy
```bash
git clone https://github.com/phungcamtann/OnlineTraveller.git
cd OnlineTraveller
```

#### Bước 2: Thiết lập cấu hình môi trường
Tạo file cấu hình cá nhân `config/local.php` từ file mẫu:
```powershell
# Trên Windows PowerShell:
Copy-Item config\local.example.php config\local.php
```
Mở file `config/local.php` và cập nhật thông tin kết nối CSDL của bạn (thường `root` và mật khẩu rỗng nếu dùng XAMPP):
```php
return [
    'database' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'online_traveller',
        'user' => 'root',
        'password' => '',
    ],
    // Các thông số MoMo Sandbox và API Weather đã được cấu hình sẵn giá trị mặc định
];
```

#### Bước 3: Khởi tạo Cơ sở Dữ liệu & Nạp Dữ liệu Mẫu
Mở MySQL Console (hoặc phpMyAdmin):
1. Tạo cơ sở dữ liệu `online_traveller`:
   ```sql
   CREATE DATABASE IF NOT EXISTS online_traveller CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Thực thi file cài đặt tổng hợp:
   - **Cách 1 (Khuyên dùng):** Sử dụng MySQL Command Line:
     ```bash
     mysql -u root -p online_traveller < database/production_install.sql
     ```
   - **Cách 2:** Mở giao diện **phpMyAdmin** (`http://localhost/phpmyadmin`), chọn cơ sở dữ liệu `online_traveller`, chuyển sang thẻ **Import** và chọn file `database/production_install.sql` (hoặc lần lượt `database/schema.sql`, 4 file trong `database/procedures/`, và `database/seed_data.sql`).

#### Bước 4: Khởi chạy Máy chủ Web

- **Cách 1: Sử dụng máy chủ tích hợp của PHP (Nhanh nhất):**
  Mở terminal tại thư mục gốc dự án và chạy:
  ```bash
  php -S localhost:8000 -t public
  ```
  Truy cập trình duyệt tại: 👉 [**http://localhost:8000**](http://localhost:8000)

- **Cách 2: Sử dụng XAMPP / Laragon:**
  Đặt toàn bộ thư mục `OnlineTraveller` vào thư mục `htdocs` (của XAMPP) hoặc `www` (của Laragon). Bật Apache và MySQL.  
  Truy cập qua đường dẫn: 👉 [**http://localhost/OnlineTraveller**](http://localhost/OnlineTraveller)  
  *(Hệ thống đã có sẵn file `.htaccess` tự động định tuyến toàn bộ request vào thư mục `public/` một cách an toàn).*

---

### 🧪 Chạy Bộ Kiểm Thử Tự Động (Automated Testing)
Dự án trang bị bộ kiểm thử tự động toàn diện giúp xác nhận tính đúng đắn của toàn bộ 22 Stored Procedures, phân quyền RBAC và luồng đặt chỗ.

Mở terminal và thực thi lệnh:
```bash
php tests/run_all_tests.php
```
*(Nếu sử dụng XAMPP trên Windows chưa cài PHP vào PATH, bạn có thể chạy: `C:\xampp\php\php.exe tests/run_all_tests.php`)*.

---

## 📖 7. Hướng Dẫn Sử Dụng & Kịch Bản Mẫu

### 🔑 Danh Sách Tài Khoản Mẫu (Đã Nạp Sẵn Trong CSDL)
> Mật khẩu đăng nhập mặc định cho **tất cả** các tài khoản dưới đây là: `password123`

| Vai trò (Role) | Họ và Tên | Email Đăng Nhập | Mục Đích Trải Nghiệm |
|:---|:---|:---|:---|
| **Admin** | Quản Trị Viên Hệ Thống | `admin@onlinetraveller.vn` | Quản trị người dùng, khóa/mở tài khoản, kiểm duyệt gói tour mới, duyệt hoàn tiền MoMo Sandbox |
| **Host 1** | Nguyễn Dân Bản Địa (Host Demo) | `hostdemo@onlinetraveller.vn` | Quản lý 8 tour mẫu, mở lịch khởi hành, duyệt booking của khách, phản hồi câu hỏi thảo luận |
| **Host 2** | Nguyễn Văn Bản Địa (Đà Lạt) | `host.dalat@onlinetraveller.vn` | Quản lý tour khám phá Cầu Đất, săn mây |
| **Host 3** | Trần Thị Hướng Dẫn (Hội An) | `host.hoian@onlinetraveller.vn` | Quản lý tour làng gốm Thanh Hà & Phố cổ Hội An |
| **Tourist 1** | Lê Du Khách | `tourist@gmail.com` | Tìm kiếm tour, trải nghiệm 3D Z-Scroll, đặt chỗ, thanh toán MoMo QR, hủy đơn T24, hỏi đáp |
| **Tourist 2** | Phạm Thích Du Lịch | `phamtourist@gmail.com` | Kiểm tra tính độc lập đa người dùng, chặn đánh giá chéo đơn hàng |

---

### 🚶 Các Kịch Bản Trải Nghiệm Mẫu

#### 1. Dành cho Du Khách (Tourist)
1. **Khám phá trang chủ:** Truy cập [Trang chủ](https://onlinetraveller.alwaysdata.net/) để tìm kiếm theo điểm đến, xem thông tin thời tiết 5 tỉnh thành và cập nhật tin tức VnExpress.
2. **Chi tiết tour & Trải nghiệm 3D:**
   - Chọn một tour (ví dụ: *Săn Mây Cầu Đất*).
   - Xem timeline các trạm dừng chân và thông tin homestay nhà dân.
   - Bấm nút **"Trải Nghiệm Không Gian 3D (Z-Scroll)"** để bước vào mô hình 3D tương tác, cuộn chuột để di chuyển camera xuyên không gian qua các trạm ảnh.
3. **Đặt tour & Thanh toán MoMo:**
   - Đăng nhập với tài khoản `tourist@gmail.com` / `password123`.
   - Chọn ngày khởi hành mong muốn, số lượng khách, chọn phương án thanh toán (*Đặt cọc 50%* hoặc *Thanh toán 100%*).
   - Gửi yêu cầu đặt tour. Sau khi Host duyệt, quét mã MoMo Sandbox QR để thanh toán giả lập.
   - Hệ thống tự động chuyển sang trang **Biên nhận điện tử (E-Receipt)** xác nhận đơn hàng thành công.
4. **Hỏi đáp & Đánh giá:**
   - Nhập câu hỏi ở mục Thảo luận dưới chân trang tour.
   - Khi tour hoàn tất, để lại đánh giá số sao (1-5 sao) và nhận xét thực tế.

#### 2. Dành cho Dân Bản Địa (Local Host)
1. Đăng nhập với tài khoản `hostdemo@onlinetraveller.vn` / `password123`.
2. Truy cập thanh điều hướng chọn **Quản Lý Của Tôi (Host)**.
3. Xem danh sách các tour mình quản lý, mở thêm lịch khởi hành mới với số chỗ và ngày đóng đăng ký.
4. Quản lý danh sách đơn đặt chỗ: Khi có du khách đăng ký, Host xem xét và nhấn **Duyệt đơn** (hệ thống sẽ giữ chỗ 15 phút cho khách thanh toán).
5. Trả lời các câu hỏi thảo luận của du khách ngay dưới tour với huy hiệu **"Dân bản địa"**.

#### 3. Dành cho Quản Trị Viên (Admin)
1. Đăng nhập với tài khoản `admin@onlinetraveller.vn` / `password123`.
2. Truy cập mục **Quản Trị (Admin)** trên thanh điều hướng.
3. **Quản lý tài khoản:** Xem danh sách toàn bộ người dùng trong hệ thống, thực hiện thao tác Khóa (Lock) hoặc Mở khóa (Active) tài khoản vi phạm.
4. **Kiểm duyệt tour:** Phê duyệt (Approve) hoặc Từ chối (Reject) các tour mới do Host gửi lên trước khi hiển thị công khai.
5. **Duyệt lệnh hoàn tiền:** Theo dõi các yêu cầu hủy tour hợp lệ trước 24 giờ (T24) và duyệt lệnh hoàn tiền MoMo Sandbox cho du khách.
6. **Quản trị cẩm nang:** Đăng và chỉnh sửa các bài viết cẩm nang du lịch bản địa.

---

## 📚 8. Tài Liệu Kỹ Thuật Bổ Sung (Technical Docs)

Để tìm hiểu sâu hơn về kiến trúc thiết kế chi tiết, các bạn có thể tham khảo các tài liệu tại thư mục `docs/`:
- [docs/SYSTEM_SPECIFICATION.md](file:///E:/Dev/Study'/Web/OnlineTraveller/docs/SYSTEM_SPECIFICATION.md): Tài liệu đặc tả phân tích & thiết kế chi tiết toàn bộ nghiệp vụ (bản đầy đủ 3.000 dòng).
- [docs/SYSTEM_TESTING_GUIDE.md](file:///E:/Dev/Study'/Web/OnlineTraveller/docs/SYSTEM_TESTING_GUIDE.md): Cẩm nang hướng dẫn kiểm thử toàn diện, ma trận test case cho 6 phân hệ lớn.
- [docs/DEDICATED_3D_VIRTUAL_TOUR_SPEC.md](file:///E:/Dev/Study'/Web/OnlineTraveller/docs/DEDICATED_3D_VIRTUAL_TOUR_SPEC.md): Tài liệu kỹ thuật nguyên lý đồ họa 3D Three.js Z-Scroll.
- [docs/UI_LAYOUT_DESIGN_SYSTEM.md](file:///E:/Dev/Study'/Web/OnlineTraveller/docs/UI_LAYOUT_DESIGN_SYSTEM.md): Đặc tả hệ thống thiết kế Dark-Alpine Glassmorphism.

---

<p align="center">
  <b>Online Traveller</b> • Kết nối văn hóa bản địa, nâng tầm trải nghiệm du lịch số  
  <br>
  <i>Đồ án môn học Phát triển Ứng dụng Web – Đại học Kinh tế TP. Hồ Chí Minh (UEH)</i>
</p>
