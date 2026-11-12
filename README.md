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

## 👥 3. Thông Tin Các Thành Viên Nhóm Phát Triển

Dự án được phân chia theo 4 phân hệ chuyên trách (TV1 – TV4), mỗi thành viên trực tiếp thiết kế cơ sở dữ liệu, viết Stored Procedures riêng biệt, xây dựng Controller, View và viết bộ kiểm thử tự động tương ứng:

| STT | Họ và Tên | MSSV / Email | Phân Hệ Phụ Trách | Mã Stored Procedures | Nhiệm Vụ Chính Đảm Nhiệm |
|:---:|:---|:---|:---:|:---:|:---|
| **1** | **Phùng Cẩm Tân** *(Nhóm trưởng)* | `phungcamtann@gmail.com` | **TV4** | `pct_sp_*` | **Cộng Đồng, Đánh Giá, Blog & Tích Hợp Ngoại Vi**<br>- Điều phối kiến trúc tổng thể và triển khai hệ thống.<br>- Kênh hỏi đáp thảo luận 1 cấp dưới tour (AJAX có huy hiệu Host).<br>- Đánh giá 1–5 sao sau khi hoàn thành tour (chặn review chéo).<br>- Quản trị bài viết Cẩm nang du lịch (Blog tin tức).<br>- Tích hợp VnExpress RSS, thời tiết OpenWeather & Tối ưu triển khai Hosting. |
| **2** | **Dương Hoàng Sâm** | **31241026707**<br>`samduong.31241026707@st.ueh.edu.vn` | **TV1** | `dhs_sp_*` | **Xác thực, Phân quyền RBAC & Quản trị Hệ thống**<br>- Thiết kế xác thực người dùng, Đăng ký / Đăng nhập, Profile.<br>- Phân quyền RBAC 4 cấp (Guest, Tourist, Host, Admin).<br>- Bảo mật Token CSRF kép (Header AJAX & Input Form).<br>- Quản lý danh mục & chức năng Admin khóa/mở tài khoản. |
| **3** | **Phạm Thiên Long** | **31241021371**<br>`longpham.31241021371@st.ueh.edu.vn` | **TV2** | `ptl_sp_*` | **Quản Lý Tour, Lưu Trú & Trải Nghiệm 3D**<br>- Nghiệp vụ tạo, cập nhật gói trải nghiệm bản địa & tọa độ GPS.<br>- Quản lý cơ sở lưu trú Homestay nhà dân (quan hệ 1-1).<br>- Mở lịch khởi hành, quản lý sức chứa và hủy lịch tour.<br>- Xây dựng Timeline trạm dừng chân & Không gian 3D Three.js Z-Scroll. |
| **4** | **Nguyễn Duy Thành Tài** | `nguyenduythanhtai050706@gmail.com` | **TV3** | `ndtt_sp_*` | **Báo Giá, Đặt Chỗ, Thanh Toán MoMo & Hoàn Tiền**<br>- Tính toán báo giá, áp dụng khuyến mãi, chia đợt trả (50% / 100%).<br>- Quy trình đặt chỗ, Host duyệt đơn, khóa giữ chỗ 15 phút.<br>- Tích hợp cổng MoMo Sandbox (QR Code, Webhook IPN HMAC-SHA256).<br>- Xuất Biên nhận điện tử (E-Receipt) & Xử lý hủy hoàn tiền theo mốc T24. |

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
