# KẾ HOẠCH NÂNG CẤP, TINH CHỈNH & HOÀN THIỆN HỆ THỐNG ONLINETRAVELLER
> **Căn cứ:** Kết quả kiểm thử thủ công thực tế & Báo cáo góp ý (PDF)  
> **Trạng thái:** Chờ phê duyệt trước khi thực thi  
> **Ngày lập:** 10/11/2026  
> **Nguyên tắc làm việc:** Không tự động git commit; sau mỗi giai đoạn kiểm thử đạt chuẩn 100% sẽ bàn giao để người dùng tự kiểm tra và commit.

---

## 📌 MỤC LỤC
1. [Tổng Quan Định Hướng & Thống Nhất Nghiệp Vụ](#1-tổng-quan-định-hướng--thống-nhất-nghiệp-vụ)
2. [Chi Tiết 6 Giai Đoạn Triển Khai (Execution Roadmap)](#2-chi-tiết-6-giai-đoạn-triển-khai)
   - [Giai đoạn 1: Minh Bạch Chi Phí (VAT 8%), Nút Điều Hướng & Quản Lý Đơn Của Host](#giai-đoạn-1-minh-bạch-chi-phí-vat-8-nút-điều-hướng--quản-lý-đơn-của-host)
   - [Giai đoạn 2: Khối Đánh Giá Tour Chi Tiết & Nhãn "Đã Tự Trải Nghiệm"](#giai-đoạn-2-khối-đánh-giá-tour-chi-tiết--nhãn-đã-tự-trải-nghiệm)
   - [Giai đoạn 3: Mở Rộng Bản Tin Cộng Đồng (3 Vai Trò) & Tin Tức RSS VnExpress](#giai-đoạn-3-mở-rộng-bản-tin-cộng-đồng-3-vai-trò--tin-tức-rss-vnexpress)
   - [Giai đoạn 4: Hồ Sơ Người Dùng (Giới Tính, Ngày Sinh), Bản In Đơn & Đăng Nhập](#giai-đoạn-4-hồ-sơ-người-dùng-giới-tính-ngày-sinh-bản-in-đơn--đăng-nhập)
   - [Giai đoạn 5: Cải Tiến Form Đăng Tour Cho Host (Tọa Độ Tự Động & Upload Ảnh Trạm)](#giai-đoạn-5-cải-tiến-form-đăng-tour-cho-host-tọa-độ-tự-động--upload-ảnh-trạm)
   - [Giai đoạn 6: Bộ Lọc Khoảng Giá Slider, Đồng Bộ Card Tour & Banner Bố Cục Mới](#giai-đoạn-6-bộ-lọc-khoảng-giá-slider-đồng-bộ-card-tour--banner-bố-cục-mới)
3. [Tích Hợp Google Cloud APIs (Maps JavaScript, Places, Geocoding)](#3-tích-hợp-google-cloud-apis)
4. [Kế Hoạch Kiểm Thử & Nghiệm Thu](#4-kế-hoạch-kiểm-thử--nghiệm-thu)

---

## 1. TỔNG QUAN ĐỊNH HƯỚNG & THỐNG NHẤT NGHIỆP VỤ

Dựa trên phản hồi kiểm thử thực tế và các thống nhất giữa hai bên:
- **Thuế GTGT 8% (VAT):** Áp dụng mô hình chuẩn sàn TMĐT: Giá tour niêm yết **đã bao gồm 8% thuế GTGT**. Trên giao diện đặt chỗ, hóa đơn điện tử E-Receipt và chi tiết đơn sẽ bóc tách minh bạch 3 dòng: *Tiền dịch vụ trước thuế (Net)*, *Tiền thuế GTGT 8%* và *Tổng tiền thanh toán (Gross)*.
- **Đánh giá tour (Reviews):** Bắt buộc đăng nhập để đánh giá. Du khách có thể đánh giá tự do trên trang tour; nếu có đơn hàng đã hoàn thành (`completed`), hệ thống tự động gắn huy hiệu bảo chứng **"Đã tự trải nghiệm"** (*Verified Traveler*).
- **Bản tin du lịch (Community Blog):** Mở quyền cho cả 3 vai trò (Tourist, Host, Admin) được viết bài chia sẻ kinh nghiệm kèm ảnh tải lên trực tiếp. Bài xuất bản ngay lập tức, Admin có quyền ẩn/xóa bài vi phạm.
- **Hồ sơ cá nhân:** Mở rộng CSDL thêm Giới tính và Ngày sinh; thay ô nhập địa bàn tự do bằng Combobox chọn Tỉnh/Thành phố chuẩn; loại bỏ toàn bộ ghi chú kỹ thuật thừa trên giao diện.
- **Đăng tour của Host (Ưu tiên):** Bỏ việc bắt Host nhập tay kinh độ/vĩ độ thập phân; hệ thống tự tính tọa độ qua lựa chọn Tỉnh/Thành và địa chỉ; hỗ trợ upload ảnh cho các trạm dừng chân.
- **Phạm vi hoãn lại:** Tạm thời chưa can thiệp sâu Three.js và tinh chỉnh CSS diện rộng; tập trung hoàn thiện logic nghiệp vụ và trải nghiệm người dùng theo đúng bản góp ý.

---

## 2. CHI TIẾT 6 GIAI ĐOẠN TRIỂN KHAI

### GIAI ĐOẠN 1: Minh Bạch Chi Phí (VAT 8%), Nút Điều Hướng & Quản Lý Đơn Của Host
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (22/22 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_phase1_improvements.php`

* **Mục tiêu:** Hoàn thiện trải nghiệm thanh toán, bóc tách thuế chuẩn mực và hỗ trợ Host quản lý dòng tiền cọc.
* **Các công việc đã hoàn thành:**
  1. **Bóc tách Thuế GTGT 8%:**
     - Công thức chuẩn:  
       $\text{Net} = \text{round}\left(\frac{\text{Total}}{1.08}\right)$  
       $\text{VAT} = \text{Total} - \text{Net}$  
       $\text{Gross} = \text{Total}$
     - Đã thêm `FormatHelper::calculateVat()`.
     - Cập nhật [`app/views/receipts/show.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/receipts/show.php), [`app/views/bookings/detail.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/bookings/detail.php) và [`app/views/services/show.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/services/show.php).
  2. **Tối ưu Giao diện Hoàn tất Thanh toán (`app/views/payments/callback.php`):**
     - Đã thêm nút "Về Trang Chủ", căn đều 2 nút điều hướng đối xứng.
  3. **Hiển thị Tình trạng Cọc phía Host (`app/views/host/bookings/index.php` & `show.php`):**
     - Hiển thị badge: `Đã cọc 50% (MoMo)`, `Đã thanh toán 100%`, `Chờ thanh toán cọc`.

---

### GIAI ĐOẠN 2: Khối Đánh Giá Tour Chi Tiết & Nhãn "Đã Tự Trải Nghiệm"
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (19/19 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_phase2_reviews.php`

* **Mục tiêu:** Xây dựng khối đánh giá công khai ở chân trang tour, hỗ trợ gắn nhãn xác thực cho khách đã hoàn thành chuyến đi.
* **Các công việc đã hoàn thành:**
  1. **CSDL & Stored Procedure:**
     - `database/changes/05_reviews_enhancement.sql`: Cho phép `booking_id NULL`, bổ sung cột `is_verified TINYINT(1) DEFAULT 0`.
     - Cập nhật SP `pct_sp_reviews_create` tự động gán `is_verified = 1` cho đơn hàng hoàn tất.
  2. **Nâng cấp Model & Controller (`Review.php`, `ReviewController.php`, `ServiceController.php`):**
     - Hỗ trợ đánh giá trực tiếp từ trang tour qua AJAX, chặn khách chưa đăng nhập, chống spam đánh giá lặp.
     - Tự động xác thực: Nếu du khách có đơn hàng `completed` cho tour này $\rightarrow$ Gán `is_verified = 1`, ngược lại `is_verified = 0`.
     - Sắp xếp ưu tiên: `is_verified DESC, created_at DESC`.
  3. **Giao diện Khối Đánh Giá tại `app/views/services/show.php`:**
     - Đặt tại Section 4 ngay dưới Hỏi - Đáp.
     - Thanh tóm tắt điểm sao trung bình, phân bổ tỷ lệ sao.
     - Huy hiệu xanh **"Đã tự trải nghiệm"** nổi bật.
     - Form AJAX chọn 1-5 sao và gửi nhận xét mượt mà.

---

### GIAI ĐOẠN 3: Mở Rộng Bản Tin Cộng Đồng (3 Vai Trò) & Tin Tức RSS VnExpress
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (24/24 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_phase3_community_posts.php`

* **Mục tiêu:** Mở rộng trang Bản tin thành cộng đồng mở cho cả Du khách, Host và Admin cùng chia sẻ; tích hợp tin tức du lịch thời sự.
* **Các công việc đã hoàn thành:**
  1. **Quyền Đăng Bài Cho 3 Vai Trò (`PostController.php`, `Post.php`):**
     - Đã thêm Stored Procedure `pct_sp_community_posts_create` cho phép cả 3 vai trò (`tourist`, `host`, `admin`) đăng bài chia sẻ, xuất bản ngay (`is_published = 1`).
     - Admin vẫn giữ toàn quyền quản trị (sửa, xóa, ẩn bài vi phạm tại `/admin/posts`).
     - Thêm nút nổi bật **"Chia Sẻ Câu Chuyện / Viết Bài Mới"** ngay trên thanh tiêu đề trang Bản tin (`/posts`).
     - Hỗ trợ tải file ảnh đại diện bài viết từ máy tính (`Post::handleThumbnailUpload`, lưu vào `public/assets/images/posts/`).
     - Tạo view mới [`app/views/posts/create.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/posts/create.php) với preview ảnh đại diện tức thì và làm sạch toàn bộ ghi chú kỹ thuật.
  2. **Tích hợp Tin tức RSS VnExpress trên `/posts`:**
     - Tích hợp `RssClient::getFeed(12)` từ VnExpress Du Lịch.
     - Gộp chung hiển thị trên danh sách trang Bản tin với nhãn nhận diện rõ ràng:
       - Huy hiệu xanh: *"Bản Tin Cộng Đồng"* (kèm tên tác giả và vai trò: Host Bản Địa / Du Khách / Ban Quản Trị).
       - Huy hiệu cam: *"VnExpress"* (kèm nguồn VnExpress Du Lịch và liên kết mở tab mới).
     - Bổ sung 3 Tab lọc: *Tất Cả Dòng Tin*, *Bài Viết Cộng Đồng*, *Tin Tức VnExpress Du Lịch*.
     - Phân trang chuẩn mực: 6 tin/trang (có nút Trang trước / Trang sau).

---

### GIAI ĐOẠN 4: Hồ Sơ Người Dùng (Giới Tính, Ngày Sinh), Bản In Đơn & Đăng Nhập
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (25/25 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_phase4_profile_enhancements.php`

* **Mục tiêu:** Nâng cao chất lượng dữ liệu hồ sơ cá nhân, làm sạch bản in hóa đơn và tối ưu form đăng nhập.
* **Các công việc đã hoàn thành:**
  1. **Mở rộng CSDL & Hồ sơ người dùng (`app/views/profile/index.php`):**
     - Tạo migration SQL [`database/changes/07_user_profile_enhancement.sql`](file:///e:/Dev/Study'/Web/OnlineTraveller/database/changes/07_user_profile_enhancement.sql) thêm 2 cột vào `tbl_users`:  
       `gender ENUM('male', 'female', 'other') DEFAULT 'other'`  
       `birth_date DATE NULL`
     - Cập nhật Stored Procedure `dhs_sp_users_update_profile` nhận 8 tham số (bổ sung `p_gender`, `p_birth_date`), trả về đồng bộ dữ liệu.
     - Cập nhật Stored Procedure `dhs_sp_users_find_login` trả về `gender` và `birth_date`.
     - Cập nhật Model [`app/models/User.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/models/User.php), Service [`app/services/AuthService.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/services/AuthService.php) và Controller [`app/controllers/AuthController.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/controllers/AuthController.php).
     - Thay thế ô nhập *Địa bàn* tự do bằng **Combobox chọn Tỉnh / Thành phố** chuẩn Việt Nam từ `FormatHelper::getProvincesList()`.
     - Thêm ô chọn *Giới tính* (Nam, Nữ, Khác) và *Ngày sinh* (`<input type="date">`).
     - Cá nhân hóa gợi ý trường *Bio*: Tourist thì gợi ý về sở thích du lịch; Host thì gợi ý về kinh nghiệm và bản sắc văn hóa.
     - Xóa hoàn toàn câu ghi chú kỹ thuật thừa (`dhs_sp_users_update_profile trong CSDL...`).
     - Hiển thị Giới tính và Ngày sinh trên thẻ tóm tắt hồ sơ bên trái.
  2. **Bản In Chi Tiết Đơn Đặt Hàng (`app/views/bookings/detail.php`):**
     - Bổ sung bộ lọc `@media print` trong CSS:
       - Ẩn thanh breadcrumb điều hướng, navbar, footer.
       - Ẩn toàn bộ các nút thao tác: *"Hủy đặt đơn"*, *"In đơn"*, *"Thanh toán"*, *"Danh sách chuyến đi"*, modal hủy.
       - Thêm Print Header trang trọng: ONLINETRAVELLER, Hotline CSKH 1900 6868, mã đơn, ngày in phiếu.
       - Giữ nguyên bố cục chia 2 cột chuẩn A4, viền xám trang trọng, chữ sắc nét.
  3. **Giao Diện Đăng Nhập (`app/views/auth/login.php`):**
     - Bổ sung nút icon con mắt bật/tắt hiển thị mật khẩu (`type="password"` $\leftrightarrow$ `type="text"`).
     - Đổi icon linh hoạt giữa `bi-eye` và `bi-eye-slash` cùng tooltip thân thiện.

---

### GIAI ĐOẠN 5: Cải Tiến Form Đăng Tour Cho Host (Tọa Độ Tự Động & Upload Ảnh Trạm) - *ƯU TIÊN*
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (24/24 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_phase5_host_services.php`

* **Mục tiêu:** Đơn giản hóa tối đa quy trình đăng tour cho người bản địa, không đòi hỏi kiến thức kỹ thuật.
* **Các công việc đã hoàn thành:**
  1. **Tự Động Hóa Tọa Độ Bản Đồ (Geocoding ngầm):**
     - Đã thêm `FormatHelper::getProvincesList()` (29 địa bàn trọng điểm) và `FormatHelper::lookupCoordinates()` (nhận diện tự động theo địa danh và tỉnh).
     - Form tạo & chỉnh sửa tour ([`create.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/host/services/create.php), [`edit.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/host/services/edit.php)):
       - Vĩ độ và Kinh độ được chuyển thành `input type="hidden"`.
       - Thay thế bằng Combobox chọn Tỉnh/Thành phố và ô nhập điểm đón thân thiện.
       - Hiển thị badge định vị bản đồ tự động cho Host nắm được kết quả ghim vị trí.
       - Backend `HostController::storeService` & `updateService` tự động bù đắp tọa độ nếu Host để trống.
  2. **Upload Trực Tiếp Ảnh Các Trạm Dừng Chân:**
     - Cho phép chọn file ảnh trực tiếp (`input type="file" name="stop_files[]"`).
     - Có thumbnail preview ngay khi chọn ảnh hoặc nhập URL.
     - `TourService::handleStopImageUpload()` tự động lưu ảnh an toàn vào `public/assets/images/tours/stops/`.
  3. **Làm Sạch Giao Diện & Loại Bỏ Thuật Ngữ Kỹ Thuật:**
     - Đã xóa toàn bộ từ ngữ `(Three.js)` khỏi tiêu đề và mô tả mục 4.
     - Đổi tên thành *"4. Lịch Trình & Điểm Dừng Chân Không Gian (3D Tour)"*.
     - Bổ sung ghi chú *"Giá niêm yết đã bao gồm 8% thuế GTGT"*.

---

### GIAI ĐOẠN 6: Bộ Lọc Khoảng Giá Slider, Đồng Bộ Card Tour & Banner Bố Cục Mới
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (26/26 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_phase6_slider_cards_banner.php`

* **Mục tiêu:** Đồng bộ ngôn ngữ thiết kế toàn hệ thống và cải tiến giao diện khám phá.
* **Các công việc đã hoàn thành:**
  1. **Thanh Trượt Khoảng Giá (Range Slider):**
     - Bổ sung thanh trượt ngang chọn ngân sách tối đa `200.000đ -> 5.000.000đ+` tại cả Trang chủ ([`app/views/home/index.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/home/index.php)) và Trang khám phá ([`app/views/services/index.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/services/index.php)).
     - Hiển thị nhãn số tiền động `X.XXX.XXX ₫` nhảy real-time theo vị trí kéo trượt của người dùng.
     - Backend [`Service::getAllApproved`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/models/Service.php) & [`ServiceController::index`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/controllers/ServiceController.php) hỗ trợ lọc `base_price <= max_price` và đảm bảo tương thích ngược với `price_range`.
  2. **Đồng Bộ Card Tour Tại Trang Khám Phá:**
     - Refactor lại toàn bộ danh sách tour ở trang Khám phá (`services/index.php`) theo phong cách Card Dark-Alpine tràn ảnh 3/4 poster đồng bộ 100% với Trang chủ.
     - Lớp phủ overlay chân ảnh hiển thị địa danh, thời lượng, tên Host, đơn giá nổi bật màu `text-info` và ghi chú *"Đã gồm 8% VAT"*.
     - Huy hiệu danh mục và số sao đánh giá góc trên được bo tròn kính mờ `glass-pill`.
     - Bộ lọc tìm kiếm được đưa vào `glass-panel` mờ kính chuẩn Dark-Alpine.
  3. **Bố Cục Lại Banner:**
     - **Trang chủ:** Thiết kế Slider Banner tương tác với dải thumbnails thu nhỏ (`banner-thumbs-container`), hỗ trợ click chuyển slide mượt mà, nút điều hướng Prev/Next và tự động trượt sau mỗi 6 giây (tạm dừng khi hover).
     - **Trang chi tiết tour (`services/show.php`):** Sửa triệt để lỗi mất chân ảnh banner bằng `background-position: center bottom` và `background-size: cover`. Bố cục lại thành 2 cột: Cột trái chữ + host + nút đặt lịch ngay, Cột phải là dải card các trạm dừng chân xếp dọc theo chiều cao banner (`vertical-station-card`) đúng chuẩn ảnh mẫu.

---

### GIAI ĐOẠN BỔ SUNG: Kiểm Duyệt & Phê Duyệt Trải Nghiệm Mới Cho Admin (Tour Moderation)
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (18/18 Tests PASSED)**  
> **Bộ kiểm thử:** `tests/test_admin_service_moderation.php`  
> **Tài liệu chi tiết:** `docs/admin_service_moderation.md`

* **Mục tiêu:** Xử lý triệt để vấn đề tour mới của Host đăng ký bị treo vĩnh viễn ở trạng thái `pending` do Admin không có công cụ phê duyệt trên giao diện.
* **Các công việc đã hoàn thành:**
  1. **Route & Controller:**
     - Đăng ký `GET /admin/services` và `POST /admin/services/{id}/moderate` bảo vệ bởi `AdminMiddleware` & `CsrfMiddleware`.
     - Viết `AdminController::services()` (thống kê 4 trạng thái, tìm kiếm, lọc) và `AdminController::moderateService()` kết nối Stored Procedure `ptl_sp_services_moderate`.
  2. **Giao diện Dashboard (`app/views/admin/dashboard.php`):**
     - Cột Kiểm duyệt có nút xanh **[Duyệt]** mở bán ngay và nút đỏ **[Từ chối]** bật modal nhập lý do gửi cho Host.
  3. **Trang Quản lý Riêng (`app/views/admin/services/index.php`):**
     - Bảng tra cứu, thanh lọc trạng thái và các nút duyệt/từ chối/duyệt lại trực quan.
  4. **Banner Kiểm Duyệt Tại Trang Tour (`app/views/services/show.php`):**
     - Khi Admin xem tour đang `pending`, thanh thông báo màu vàng nổi bật ở đầu trang cho phép **[Phê Duyệt Mở Bán]** hoặc **[Từ Chối]** ngay tại chỗ.

---

Hiện tại đồ án đang sử dụng iframe Google Maps nhúng miễn phí (`https://maps.google.com/maps?q=...&output=embed`). Vì bro đã có tài khoản Google Cloud có Billing, chúng ta có thể nâng cấp trải nghiệm đồ án lên tầm doanh nghiệp với 3 API sau:

| API Của Google Cloud | Ứng Dụng Trong Dự Án OnlineTraveller | Lợi Ích Mang Lại |
| :--- | :--- | :--- |
| **Maps JavaScript API** | Hiển thị bản đồ tương tác vector Dark Mode trên trang chi tiết tour | Màu sắc bản đồ hòa hợp 100% với giao diện Dark-Alpine, có marker động các trạm |
| **Places API (Autocomplete)** | Gợi ý địa chỉ tự động khi Host gõ điểm hẹn tạo tour | Người dùng gõ vài ký tự là Google gợi ý chuẩn địa chỉ Việt Nam |
| **Geocoding API** | Chuyển đổi tên địa điểm thành tọa độ chính xác từng mét | Tự động sinh Lat/Lng mà Host không cần biết bất kỳ con số nào |

> [!TIP]
> **Hướng dẫn lấy API Key từ Google Cloud:**
> 1. Truy cập [Google Cloud Console](https://console.cloud.google.com/).
> 2. Tạo một Project mới (ví dụ: `OnlineTraveller-Maps`).
> 3. Vào mục **APIs & Services** $\rightarrow$ **Library**: Bấm kích hoạt (Enable) 3 API:
>    - *Maps JavaScript API*
>    - *Places API*
>    - *Geocoding API*
> 4. Vào mục **Credentials** $\rightarrow$ Bấm **Create Credentials** $\rightarrow$ **API key**.
> 5. Sao chép chuỗi Key và cấu hình vào file `.env` hoặc `config/services.php`:  
>    `GOOGLE_MAPS_API_KEY="AIzaSy..."`
> *(Hệ thống vẫn sẽ được thiết kế theo cơ chế dự phòng: nếu chưa có key thì dùng chế độ tự động tính toán cục bộ, có key thì tự động kích hoạt tính năng cao cấp của Google)*.

---

## 4. KẾ HOẠCH KIỂM THỬ & NGHIỆM THU

1. **Quy trình nghiệm thu từng giai đoạn:**
   - Sau khi hoàn thành code của từng giai đoạn, viết kịch bản test tương ứng.
   - Chạy kiểm thử tự động đạt 100% PASS.
   - Báo cáo kết quả và cung cấp lệnh git commit để bro kiểm tra thực tế và tự commit.
2. **Kế hoạch thực hiện theo thứ tự ưu tiên:**
   - **Bước 1:** Thực hiện **Giai đoạn 1** (Thuế GTGT 8%, Nút điều hướng, Trạng thái cọc Host).
   - **Bước 2:** Thực hiện **Giai đoạn 2** (Khối đánh giá tour chi tiết & Nhãn Đã tự trải nghiệm).
   - **Bước 3:** Thực hiện **Giai đoạn 5** (Cải tiến form đăng tour Host - Geocoding & Upload ảnh).
   - **Bước 4:** Thực hiện **Giai đoạn 3** (Bản tin 3 vai trò & RSS VnExpress).
   - **Bước 5:** Thực hiện **Giai đoạn 4** (Hồ sơ người dùng, Bản in đơn, Đăng nhập).
   - **Bước 6:** Thực hiện **Giai đoạn 6** (Slider giá, Đồng bộ Card tour, Banner bố cục mới).
