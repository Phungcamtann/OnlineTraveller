# CẨM NANG & MA TRẬN KIỂM THỬ HỆ THỐNG ONLINETRAVELLER
> **Dành cho:** Tester, Developer và Giảng viên đánh giá  
> **Phiên bản:** Hoàn tất Sprint 1 & Sprint 2 (100% Sẵn sàng cho Tinh chỉnh UI & Sprint 3)  
> **Ngày cập nhật:** 09/11/2026  

---

## 📌 MỤC LỤC
1. [Tổng Quan Hệ Thống & Bộ Dữ Liệu Kiểm Thử (Seed Accounts)](#1-tổng-quan-hệ-thống--bộ-dữ-liệu-kiểm-thử)
2. [Hướng Dẫn Chạy Bộ Kiểm Thử Tự Động (Automated CLI Suites)](#2-hướng-dẫn-chạy-bộ-kiểm-thử-tự-động)
3. [Ma Trận Test Cases Toàn Bộ 6 Phân Hệ Nghiệp Vụ](#3-ma-trận-test-cases-toàn-bộ-6-phân-hệ-nghiệp-vụ)
   - [Phân hệ 1: Xác Thực, Phân Quyền RBAC & Bảo Mật CSRF](#phân-hệ-1-xác-thực-phân-quyền-rbac--bảo-mật-csrf)
   - [Phân hệ 2: Khám Phá Trải Nghiệm Bản Địa & Trình Chiếu 3D](#phân-hệ-2-khám-phá-trải-nghiệm-bản-địa--trình-chiếu-3d)
   - [Phân hệ 3: Đặt Chỗ, Kiểm Tra Sức Chứa & Duyệt Chỗ 15 Phút](#phân-hệ-3-đặt-chỗ-kiểm-tra-sức-chứa--duyệt-chỗ-15-phút)
   - [Phân hệ 4: Thanh Toán MoMo Sandbox QR & Hóa Đơn Điện Tử](#phân-hệ-4-thanh-toán-momo-sandbox-qr--hóa-đơn-điện-tử)
   - [Phân hệ 5: Hủy Đặt Chỗ & Chính Sách Hoàn Tiền T24](#phân-hệ-5-hủy-đặt-chỗ--chính-sách-hoàn-tiền-t24)
   - [Phân hệ 6: Kênh Hỏi - Đáp 1 Cấp, Đánh Giá Sau Chuyến Đi & Blog](#phân-hệ-6-kênh-hỏi---đáp-1-cấp-đánh-giá-sau-chuyến-đi--blog)
4. [Kịch Bản Kiểm Thử Thủ Công Từng Bước Trên Giao Diện (Step-by-Step UI Guide)](#4-kịch-bản-kiểm-thử-thủ-công-từng-bước)
   - [Kịch bản A: Toàn bộ Vòng đời Đặt tour - Duyệt đơn - Thanh toán - Nhận hóa đơn](#kịch-bản-a-toàn-bộ-vòng-đời-đặt-tour---duyệt-đơn---thanh-toán)
   - [Kịch bản B: Quy trình Hủy đơn & Hoàn tiền T24 (Trước và Sau 24h)](#kịch-bản-b-quy-trình-hủy-đơn--hoàn-tiền-t24)
   - [Kịch bản C: Tương tác Hỏi đáp trực tiếp & Đánh giá sao sau tour](#kịch-bản-c-tương-tác-hỏi-đáp--đánh-giá-sao-sau-tour)
   - [Kịch bản D: Tác vụ Quản trị viên (Khóa User, Quản lý Bài viết & Duyệt Hoàn tiền)](#kịch-bản-d-tác-vụ-quản-trị-viên)

---

## 1. TỔNG QUAN HỆ THỐNG & BỘ DỮ LIỆU KIỂM THỬ

Hệ thống **OnlineTraveller** được xây dựng theo kiến trúc MVC chuẩn PHP 8+, kết hợp 22 Stored Procedures trong MariaDB, tích hợp bảo mật RBAC, chống CSRF Token kép (Header/POST) và giao diện Dark-Alpine Glassmorphism.

### 🔑 Danh Sách Tài Khoản Mẫu (Đã Nạp Sẵn Trong CSDL)
> [!NOTE]
> Tất cả các tài khoản mặc định dưới đây đều sử dụng mật khẩu chung: `password123`.

| Vai trò (Role) | Họ và Tên | Email Đăng Nhập | Mật Khẩu | Mục đích Kiểm thử |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | Quản Trị Viên (Dương Hoàng Sâm) | `admin@onlinetraveller.vn` | `password123` | Quản lý người dùng, khóa/mở tài khoản, duyệt hoàn tiền, quản trị cẩm nang blog |
| **Host 1** | Nguyễn Văn Bản Địa (Đà Lạt) | `host.dalat@onlinetraveller.vn` | `password123` | Quản lý tour Đà Lạt, duyệt đơn đặt chỗ, phản hồi câu hỏi thảo luận du khách |
| **Host 2** | Trần Thị Hướng Dẫn (Hội An) | `host.hoian@onlinetraveller.vn` | `password123` | Quản lý tour phố cổ Hội An & làng gốm Thanh Hà |
| **Tourist 1** | Lê Du Khách | `tourist@gmail.com` | `password123` | Tìm kiếm tour, đặt chỗ, thanh toán MoMo QR, hủy đơn T24, đặt câu hỏi, đánh giá sao |
| **Tourist 2** | Phạm Thích Du Lịch | `phamtourist@gmail.com` | `password123` | Kiểm tra bảo mật đa người dùng, chặn đánh giá chéo đơn hàng, kiểm tra concurrency |

---

## 2. HƯỚNG DẪN CHẠY BỘ KIỂM THỬ TỰ ĐỘNG

Hệ thống cung cấp một **Master Test Suite Runner** có thể chạy toàn bộ 14 bộ kiểm thử (UI, Stored Procedures, Security, Lifecycle) chỉ bằng **1 dòng lệnh duy nhất**.

### 🚀 Lệnh Chạy Toàn Bộ Test Suite (Master Runner):
Mở terminal tại thư mục gốc dự án và chạy:
```powershell
C:\xampp\php\php.exe tests/run_all_tests.php
```
*(Nếu PHP đã có trong biến môi trường PATH, bạn chỉ cần gõ `php tests/run_all_tests.php`)*.

### 📊 Bảng Danh Mục Các Test Suite Tự Động:

| STT | Mã Bộ Test | Tên File Kiểm Thử | Số Assertions | Mô Tả Nghiệp Vụ Kiểm Thử |
| :---: | :---: | :--- | :---: | :--- |
| 1 | `UI-01` | `tests/test_step1_layout_theme.php` | 4/4 | Token CSS Dark-Alpine, Glassmorphism & Menu RBAC Layout |
| 2 | `UI-02` | `tests/test_step2_home_index.php` | 8/8 | Hero Banner, Search Bar 3 trường, 5 Card Thời Tiết & Tin RSS |
| 3 | `UI-03` | `tests/test_step3_services_show.php` | 7/7 | Timeline trạm, Thẻ Homestay, Modal 3D Z-Scroll Three.js |
| 4 | `TV2-01` | `tests/test_tv2_01.php` | 8/8 | 6 Stored Procedures quản trị Tour, Homestay, Lịch trình |
| 5 | `TV3-01` | `tests/test_tv3_01.php` | 7/7 | 6 Stored Procedures báo giá, đặt chỗ, cọc 50% & hoàn tiền |
| 6 | `TV4-01` | `tests/test_tv4_01.php` | 12/12 | 5 Stored Procedures thảo luận Q&A, đánh giá sao, bài viết blog |
| 7 | `TV3-CSRF`| `tests/test_booking_flow_csrf.php` | 2/2 | Luồng đặt chỗ bảo vệ toàn vẹn bằng token CSRF kép |
| 8 | `TV1-05` | `tests/test_sprint2_tv1_05.php` | 37/37 | Middleware phân quyền RBAC & chống tấn công giả mạo CSRF |
| 9 | `TV1-06` | `tests/test_sprint2_tv1_06.php` | 17/17 | Admin khóa/mở khóa tài khoản via `dhs_sp_users_set_status` |
| 10 | `TV2-05` | `tests/test_sprint2_tv2_05.php` | 21/21 | Host duyệt đơn, khóa chỗ 15 phút via `ndtt_sp_bookings_approve` |
| 11 | `TV3-05` | `tests/test_sprint2_tv3_05.php` | 25/25 | MoMo Sandbox QR & Webhook IPN HMAC-SHA256 signature |
| 12 | `TV3-06` | `tests/test_sprint2_tv3_06.php` | 27/27 | Hóa đơn điện tử E-Receipt & Chính sách hoàn tiền hủy tour T24 |
| 13 | `TV4-05` | `tests/test_sprint2_tv4_05.php` | 22/22 | Kênh Hỏi - Đáp 1 cấp AJAX & Host phản hồi có huy hiệu bản địa |
| 14 | `TV4-06` | `tests/test_sprint2_tv4_06.php` | 24/24 | Đánh giá 1-5 sao sau tour & Quản trị Cẩm nang Blog Admin |
| **TỔNG**| **14 SUITES** | **TOÀN BỘ HỆ THỐNG** | **221 / 221** | **Tỷ Lệ Đạt: 100% (Thời gian chạy ~13 giây)** |

---

## 3. MA TRẬN TEST CASES TOÀN BỘ 6 PHÂN HỆ NGHIỆP VỤ

### Phân hệ 1: Xác Thực, Phân Quyền RBAC & Bảo Mật CSRF
| Mã TC | Tiêu Đề Kiểm Thử | Đầu Vào / Thao Tác | Kết Quả Kỳ Vọng | Trạng Thái |
| :--- | :--- | :--- | :--- | :---: |
| `TC-AUTH-01` | Đăng nhập đúng thông tin | Email & Mật khẩu hợp lệ (`tourist@gmail.com` / `password123`) | Đăng nhập thành công, session được khởi tạo, chuyển hướng về trang chủ | **PASS** |
| `TC-AUTH-02` | Đăng nhập sai mật khẩu | Nhập mật khẩu không chính xác | Báo lỗi đăng nhập, không tạo session | **PASS** |
| `TC-AUTH-03` | Chặn khách vãng lai (Guest) | Chưa đăng nhập truy cập `/bookings` | Bị chặn, redirect sang `/login` kèm thông báo flash cảnh báo | **PASS** |
| `TC-AUTH-04` | Phân quyền Tourist vào Host/Admin | Tourist cố tình vào `/host` hoặc `/admin/users` | Trả về mã lỗi HTTP 403 Forbidden hoặc redirect kèm flash cảnh báo | **PASS** |
| `TC-AUTH-05` | Phân quyền Host vào Admin | Host cố tình vào `/admin/users` | Trả về mã lỗi HTTP 403 Forbidden | **PASS** |
| `TC-AUTH-06` | Xác thực CSRF Form POST | Gửi form đặt chỗ thiếu `_csrf_token` | Bị chặn ngay tại `CsrfMiddleware`, không ghi dữ liệu vào CSDL | **PASS** |
| `TC-AUTH-07` | Xác thực CSRF AJAX Header | Gửi AJAX kèm header `X-CSRF-TOKEN` hợp lệ | Request được xử lý trơn tru | **PASS** |
| `TC-AUTH-08` | Khóa tài khoản người dùng | Admin gọi `POST /admin/users/{id}/status` với status `locked` | SP `dhs_sp_users_set_status` cập nhật CSDL, user bị khóa không thể đăng nhập | **PASS** |
| `TC-AUTH-09` | Mở khóa tài khoản | Admin gọi `POST /admin/users/{id}/status` với status `active` | User kích hoạt lại bình thường, có thể đăng nhập tiếp tục | **PASS** |

### Phân hệ 2: Khám Phá Trải Nghiệm Bản Địa & Trình Chiếu 3D
| Mã TC | Tiêu Đề Kiểm Thử | Đầu Vào / Thao Tác | Kết Quả Kỳ Vọng | Trạng Thái |
| :--- | :--- | :--- | :--- | :---: |
| `TC-DISC-01` | Hiển thị Trang chủ | Truy cập `GET /` | Banner Hero bên trái, nút ambient glow, thanh tìm kiếm tách biệt | **PASS** |
| `TC-DISC-02` | Card Thời Tiết Địa Phương | Xem khối thời tiết trên Trang chủ | Hiển thị 5 card thời tiết Đà Lạt, Sa Pa, Hà Giang, Hội An, Phú Quốc | **PASS** |
| `TC-DISC-03` | Chi tiết Tour & Homestay | Truy cập `GET /services/1` | Hiển thị thông tin Homestay nhà dân, tọa độ Google Maps, thời tiết | **PASS** |
| `TC-DISC-04` | Trục Dọc Timeline Trạm | Cuộn trang `services/show.php` | Timeline các trạm dừng chân nối dọc kèm hình ảnh và câu chuyện | **PASS** |
| `TC-DISC-05` | Modal 3D Z-Scroll Three.js | Nhấn nút "Trải Nghiệm Không Gian 3D (Z-Scroll)" | Modal Three.js mở lên, điều hướng cuộn chuột xuyên qua các trạm ảnh 3D | **PASS** |

### Phân hệ 3: Đặt Chỗ, Kiểm Tra Sức Chứa & Duyệt Chỗ 15 Phút
| Mã TC | Tiêu Đề Kiểm Thử | Đầu Vào / Thao Tác | Kết Quả Kỳ Vọng | Trạng Thái |
| :--- | :--- | :--- | :--- | :---: |
| `TC-BOOK-01` | Báo giá tự động | Gọi `ndtt_sp_bookings_quote` cho 2 khách | Tính đúng tổng tiền gốc, áp dụng giảm giá nếu có mã coupon | **PASS** |
| `TC-BOOK-02` | Tạo đơn đặt tour | Tourist gửi form đặt vé (khung giờ & số khách) | Tạo đơn hàng mới trạng thái `pending_approval`, mã BK... | **PASS** |
| `TC-BOOK-03` | Host xem danh sách duyệt | Host đăng nhập vào `/host/bookings` | Nhìn thấy đơn đặt chỗ của du khách đang ở trạng thái chờ duyệt | **PASS** |
| `TC-BOOK-04` | Host duyệt đơn giữ chỗ | Host bấm nút "Duyệt Đơn" | Gọi `ndtt_sp_bookings_approve`, trạng thái đổi sang `awaiting_payment` | **PASS** |
| `TC-BOOK-05` | Đồng hồ đếm ngược 15 phút | Xem chi tiết đơn sau khi Host duyệt | Hệ thống gán hạn chót thanh toán chính xác 15 phút kể từ thời điểm duyệt | **PASS** |
| `TC-BOOK-06` | Khóa Concurrency chống Overbooking | Nhiều khách đặt cùng lúc vượt sức chứa | Bị chặn, thông báo lịch trình đã hết chỗ | **PASS** |
| `TC-BOOK-07` | Chặn Host khác duyệt đơn | Host 2 cố tình duyệt đơn của Tour thuộc Host 1 | Trả về `FORBIDDEN_NOT_HOST`, không thể phê duyệt | **PASS** |

### Phân hệ 4: Thanh Toán MoMo Sandbox QR & Hóa Đơn Điện Tử
| Mã TC | Tiêu Đề Kiểm Thử | Đầu Vào / Thao Tác | Kết Quả Kỳ Vọng | Trạng Thái |
| :--- | :--- | :--- | :--- | :---: |
| `TC-PAY-01` | Mở trang thanh toán MoMo | Bấm nút "Thanh Toán Ngay" trên đơn `awaiting_payment` | Hiển thị mã QR MoMo Sandbox, hướng dẫn quét và số tiền cần thanh toán | **PASS** |
| `TC-PAY-02` | Chữ ký HMAC-SHA256 IPN | Gửi IPN webhook với chữ ký hợp lệ | Endpoint `/payments/momo/ipn` xác thực chữ ký chuẩn xác | **PASS** |
| `TC-PAY-03` | Chống giả mạo chữ ký IPN | Giả mạo chữ ký hoặc thay đổi số tiền | Bị từ chối với mã lỗi HTTP 400 Invalid Signature | **PASS** |
| `TC-PAY-04` | Cập nhật đơn & Xuất biên lai | IPN thành công gọi `ndtt_sp_payments_apply_verified` | Đơn chuyển sang `confirmed`, tạo giao dịch và tự động sinh mã hóa đơn `RC...` | **PASS** |
| `TC-PAY-05` | Xem Hóa đơn điện tử | Truy cập `GET /receipts/{id}` | Giao diện biên lai phong cách editorial, hiển thị đủ thông tin thuế, chi tiết, nút in PDF | **PASS** |
| `TC-PAY-06` | Bảo mật Hóa đơn điện tử | Khách lạ truy cập xem biên lai của người khác | Bị chặn với HTTP 403 Forbidden | **PASS** |

### Phân hệ 5: Hủy Đặt Chỗ & Chính Sách Hoàn Tiền T24
| Mã TC | Tiêu Đề Kiểm Thử | Đầu Vào / Thao Tác | Kết Quả Kỳ Vọng | Trạng Thái |
| :--- | :--- | :--- | :--- | :---: |
| `TC-REF-01` | Hủy trước 24 giờ (Trước T24) | Du khách hủy đơn đã thanh toán trước mốc 24h khởi hành | Đơn chuyển `cancelled`, số chỗ nhả lại lịch trình, tạo hoàn tiền `status = approved` | **PASS** |
| `TC-REF-02` | Hủy sau 24 giờ (Sau T24) | Du khách hủy đơn đã thanh toán sau mốc 24h khởi hành | Đơn chuyển `cancelled`, tạo bản ghi hoàn tiền với `status = requested` (chờ duyệt) | **PASS** |
| `TC-REF-03` | Hủy đơn chưa thanh toán | Du khách hủy đơn đang ở `pending` hoặc `awaiting_payment` | Hủy ngay lập tức, nhả chỗ, không sinh bản ghi hoàn tiền | **PASS** |
| `TC-REF-04` | Admin xem yêu cầu hoàn tiền | Admin đăng nhập vào `/admin/refunds` | Hiển thị danh sách các lệnh hoàn tiền kèm KPI và bộ lọc trạng thái | **PASS** |
| `TC-REF-05` | Admin phê duyệt hoàn tiền | Admin bấm "Phê duyệt hoàn tiền" | Gọi `ndtt_sp_refunds_create_and_approve`, lệnh chuyển `approved`, cập nhật CSDL | **PASS** |

### Phân hệ 6: Kênh Hỏi - Đáp 1 Cấp, Đánh Giá Sau Chuyến Đi & Blog
| Mã TC | Tiêu Đề Kiểm Thử | Đầu Vào / Thao Tác | Kết Quả Kỳ Vọng | Trạng Thái |
| :--- | :--- | :--- | :--- | :---: |
| `TC-COMM-01` | Gửi câu hỏi cho Host | Du khách nhập câu hỏi và bấm gửi qua AJAX | Tạo topic hỏi đáp mới via `pct_sp_discussions_create`, giao diện cập nhật ngay | **PASS** |
| `TC-COMM-02` | Host phản hồi câu hỏi | Host bấm "Trả lời" và gửi phản hồi qua AJAX | Phản hồi lồng đúng 1 cấp dưới câu hỏi cha, gắn huy hiệu `Host bản địa` | **PASS** |
| `TC-COMM-03` | Chống phản hồi đa cấp | Cố tình reply vào một câu reply đã có | Hệ thống chặn, duy trì cấu trúc phẳng 1 cấp thảo luận | **PASS** |
| `TC-COMM-04` | Đánh giá sao sau chuyến đi | Du khách gửi đánh giá (1..5 sao) cho đơn `completed` | Lưu vào CSDL via `pct_sp_reviews_create`, hiển thị số sao và bình luận | **PASS** |
| `TC-COMM-05` | Chặn đánh giá lần thứ 2 | Gửi tiếp đánh giá cho đơn đã đánh giá | Bị chặn với lỗi `REVIEW_ALREADY_EXISTS` | **PASS** |
| `TC-COMM-06` | Chặn đánh giá đơn chưa xong | Gửi đánh giá cho đơn đang `pending` hoặc `confirmed` | Bị chặn với lỗi `BOOKING_NOT_COMPLETED` | **PASS** |
| `TC-COMM-07` | Admin tạo bài viết Blog | Admin vào `/admin/posts/create` tạo bài cẩm nang | Lưu via `pct_sp_posts_save`, tự động sinh slug chuẩn SEO | **PASS** |
| `TC-COMM-08` | Độc giả đọc Blog & Tăng View | Truy cập `GET /posts/{slug}` | Bài viết hiển thị đẹp mắt, lượt xem `views_count` tự động tăng 1 | **PASS** |

---

## 4. KỊCH BẢN KIỂM THỬ THỦ CÔNG TỪNG BƯỚC

Khởi động web server cục bộ nếu chưa chạy (ví dụ qua Apache XAMPP hoặc PHP Built-in Server):
```powershell
# Chạy PHP Server tại cổng 8000 (nếu không dùng Apache)
php -S localhost:8000 -t public
```

---

### Kịch bản A: Toàn bộ Vòng đời Đặt tour - Duyệt đơn - Thanh toán
> **Mục tiêu:** Kiểm tra trọn vẹn luồng từ khi Du khách chọn tour, Host duyệt, thanh toán MoMo QR đến khi xuất biên nhận điện tử.

* **Bước 1: Du khách đăng nhập và chọn tour**
  - Mở trình duyệt truy cập: `http://localhost:8000/login`
  - Đăng nhập tài khoản: `tourist@gmail.com` / `password123`
  - Truy cập trang chi tiết tour: `http://localhost:8000/services/1`
  - Cuộn xuống **Khối 3: Đặt Chỗ**, chọn ngày/lịch trình khởi hành và số khách = `2`.
  - Nhấn nút **"Đặt Chỗ Trực Tuyến Ngay"** -> Hệ thống tạo đơn hàng thành công và chuyển sang trang chi tiết đơn `http://localhost:8000/bookings/{id}`.
  - *Kiểm tra:* Trạng thái đơn lúc này là `Chờ Host xác nhận` (`pending_approval`).

* **Bước 2: Host phê duyệt đơn & kích hoạt giữ chỗ 15 phút**
  - Mở tab ẩn danh (Incognito) hoặc đăng xuất, truy cập `http://localhost:8000/login`.
  - Đăng nhập tài khoản Host: `host.dalat@onlinetraveller.vn` / `password123`.
  - Truy cập danh sách đơn: `http://localhost:8000/host/bookings`.
  - Tìm đơn vừa tạo ở Bước 1 và nhấn nút **"Duyệt đơn"**.
  - *Kiểm tra:* Đơn chuyển sang trạng thái `Chờ thanh toán` (`awaiting_payment`), đồng hồ đếm ngược 15 phút xuất hiện.

* **Bước 3: Du khách thanh toán MoMo Sandbox QR**
  - Quay lại tài khoản Du khách, tải lại trang đơn hàng: `http://localhost:8000/bookings/{id}`.
  - Bấm nút **"Thanh toán ngay"**.
  - Màn hình hiển thị mã QR MoMo Sandbox và nút thanh toán.
  - Bấm nút mô phỏng hoàn tất thanh toán MoMo -> Hệ thống xử lý IPN HMAC-SHA256, chuyển trạng thái đơn sang `Đã xác nhận` (`confirmed`).

* **Bước 4: Nhận Hóa đơn điện tử (E-Receipt)**
  - Tại trang chi tiết đơn hàng, xuất hiện nút **"Xem Biên Nhận Điện Tử"**.
  - Bấm vào nút -> Chuyển đến `http://localhost:8000/receipts/{id}`.
  - *Kiểm tra:* Giao diện biên lai phong cách editorial hiển thị đầy đủ thông tin: Mã hóa đơn, Tên dịch vụ, Thuế GTGT 8%, Chi tiết Homestay nhà dân, Mã tra cứu và nút **"In Hóa Đơn / Lưu PDF"**.

---

### Kịch bản B: Quy trình Hủy đơn & Hoàn tiền T24
> **Mục tiêu:** Kiểm tra chính sách hoàn tiền tự động trước 24h và gửi yêu cầu Admin duyệt sau 24h.

* **Trường hợp 1: Hủy trước 24 giờ khởi hành (Trước T24)**
  - Đăng nhập bằng tài khoản Du khách: `tourist@gmail.com` / `password123`.
  - Vào chi tiết đơn đã xác nhận có ngày khởi hành xa (còn trên 24 giờ).
  - Bấm nút **"Hủy Đặt Chỗ"** -> Nhập lý do hủy (ví dụ: *Có việc bận gia đình*).
  - *Kiểm tra:* Đơn chuyển ngay sang `cancelled`. Hệ thống tự động tạo bản ghi hoàn tiền với trạng thái **Đã duyệt hoàn tiền** (`approved`). Chỗ trống được nhả lại vào lịch trình.

* **Trường hợp 2: Hủy sau 24 giờ khởi hành (Sau T24) & Admin phê duyệt**
  - Vào một đơn hàng có lịch khởi hành diễn ra trong vòng 24 giờ.
  - Bấm **"Hủy Đặt Chỗ"**.
  - *Kiểm tra:* Đơn chuyển sang `cancelled`, nhưng bản ghi hoàn tiền có trạng thái **Chờ xem xét** (`requested`).
  - Đăng nhập tài khoản Admin: `admin@onlinetraveller.vn` / `password123`.
  - Truy cập: `http://localhost:8000/admin/refunds`.
  - Tìm yêu cầu hoàn tiền vừa tạo và bấm nút **"Phê duyệt hoàn tiền"**.
  - *Kiểm tra:* Lệnh hoàn tiền đổi sang trạng thái `approved` và được lưu vết trong hệ thống.

---

### Kịch bản C: Tương tác Hỏi đáp & Đánh giá sao sau tour
> **Mục tiêu:** Kiểm tra tương tác cộng đồng thời gian thực và đánh giá sau chuyến đi.

* **Hỏi đáp 1 cấp (Q&A):**
  - Truy cập trang chi tiết tour: `http://localhost:8000/services/1`.
  - Cuộn đến mục **Kênh Hỏi - Đáp Trực Tiếp Từ Xa**.
  - Nhập nội dung: *"Cho em hỏi đoàn có hỗ trợ đón tại bến xe không ạ?"* và bấm **"Gửi Câu Hỏi"**.
  - *Kiểm tra:* Câu hỏi hiển thị ngay trên danh sách mà không cần tải lại toàn bộ trang (AJAX).
  - Đăng nhập Host (`host.dalat@onlinetraveller.vn`), quay lại trang tour, bấm nút **"Trả lời"** dưới câu hỏi vừa tạo.
  - Nhập câu trả lời: *"Dạ có bạn nhé, Host sẽ đón bạn tại bến xe liên tỉnh."* -> Bấm gửi.
  - *Kiểm tra:* Câu trả lời xuất hiện ngay bên dưới câu hỏi với huy hiệu **Host bản địa**.

* **Đánh giá sao (Reviews):**
  - Đăng nhập tài khoản Du khách, vào chi tiết một chuyến đi đã ở trạng thái **Đã hoàn thành** (`completed`).
  - Form đánh giá xuất hiện: Chọn `5 sao` và nhập cảm nghĩ: *"Chuyến đi săn mây và homestay tuyệt vời!"*.
  - Bấm **"Gửi Đánh Giá"**.
  - *Kiểm tra:* Form đánh giá biến mất, thay thế bằng thẻ Card Review đẹp mắt với 5 ngôi sao vàng. Thử gửi lại sẽ bị chặn.

---

### Kịch bản D: Tác vụ Quản trị viên (Khóa User & Quản trị Blog)
> **Mục tiêu:** Kiểm tra quyền hạn Admin trong việc bảo vệ an toàn hệ thống và xuất bản nội dung.

* **Khóa và Mở khóa tài khoản người dùng:**
  - Đăng nhập tài khoản Admin: `admin@onlinetraveller.vn` / `password123`.
  - Truy cập: `http://localhost:8000/admin/users`.
  - Tìm tài khoản `phamtourist@gmail.com` (ID #5), bấm nút **"Khóa tài khoản"**.
  - *Kiểm tra:* Trạng thái user đổi thành `locked`.
  - Thử mở trình duyệt ẩn danh đăng nhập bằng `phamtourist@gmail.com` -> Hệ thống từ chối đăng nhập với thông báo tài khoản đã bị khóa.
  - Quay lại trang Admin, bấm **"Mở khóa"** -> Tài khoản đổi về `active` và đăng nhập lại bình thường.

* **Quản trị Cẩm nang Blog:**
  - Tại menu Admin, vào mục **Cẩm nang du lịch** (`http://localhost:8000/admin/posts`).
  - Bấm **"Viết Bài Mới"**, nhập Tiêu đề, Tóm tắt, Nội dung và chọn trạng thái **Xuất bản**.
  - Bấm **"Lưu bài viết"**.
  - *Kiểm tra:* Bài viết được tạo với slug SEO tự động. Truy cập trang công khai `http://localhost:8000/posts` để thấy bài viết mới hiển thị trên giao diện cẩm nang.

---

## 5. KẾT LUẬN & SẴN SÀNG CHO BƯỚC TIẾP THEO

Toàn bộ 221 assertions kiểm thử tự động cùng các kịch bản kiểm thử giao diện người dùng thực tế đã được chuẩn hóa và sẵn sàng. 

### Bước kế tiếp:
1. **Tinh chỉnh giao diện (UI Polishing)**:
   - Đồng bộ font chữ toàn hệ thống về duy nhất font **Roboto** (loại bỏ font Montserrat ở các tiêu đề và tổng tiền).
   - Bổ sung icon mặt trời / mây / mưa sinh động vào các card thời tiết.
   - Bổ sung đầy đủ các mục chi tiết chiết tính chi phí tại khối đặt chỗ/thanh toán.
2. **Khởi động Sprint 3**:
   - Triển khai các tính năng chuyên sâu tiếp theo sau khi UI và toàn bộ các luồng đã hoàn toàn ăn khớp và đồng bộ!
