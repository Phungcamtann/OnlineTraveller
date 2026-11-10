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
* **Mục tiêu:** Hoàn thiện trải nghiệm thanh toán, bóc tách thuế chuẩn mực và hỗ trợ Host quản lý dòng tiền cọc.
* **Các công việc cụ thể:**
  1. **Bóc tách Thuế GTGT 8%:**
     - Công thức chuẩn:  
       $\text{Net} = \text{round}\left(\frac{\text{Total}}{1.08}\right)$  
       $\text{VAT} = \text{Total} - \text{Net}$  
       $\text{Gross} = \text{Total}$
     - Cập nhật [`app/helpers/FormatHelper.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/helpers/FormatHelper.php): bổ sung hàm tính toán và hiển thị phân rã VAT.
     - Cập nhật [`app/views/receipts/show.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/receipts/show.php): Thêm dòng chi tiết thuế GTGT 8% trong bảng chi phí hóa đơn.
     - Cập nhật [`app/views/bookings/detail.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/bookings/detail.php) và [`app/views/services/show.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/services/show.php): ghi chú rõ ràng *"Giá đã bao gồm 8% thuế GTGT"*.
  2. **Tối ưu Giao diện Hoàn tất Thanh toán (`app/views/payments/callback.php`):**
     - Bổ sung nút *"Về Trang Chủ"* với icon trang nhã.
     - Căn đều kích thước và padding giữa 2 nút: *"Xem Chi Tiết Vé & Lịch Trình"* và *"Xem Biên Nhận Điện Tử"*.
  3. **Hiển thị Tình trạng Cọc phía Host (`app/views/host/bookings/index.php` hoặc `host/bookings.php`):**
     - Bổ sung huy hiệu phân biệt:  
       - `Đã cọc 50% (MoMo)` (màu xanh lá)  
       - `Đã thanh toán 100%` (màu xanh dương)  
       - `Chờ thanh toán cọc` (màu vàng)  
     - Giúp Host nắm rõ tình trạng tài chính của khách ngay trên dashboard quản lý đơn.

---

### GIAI ĐOẠN 2: Khối Đánh Giá Tour Chi Tiết & Nhãn "Đã Tự Trải Nghiệm"
* **Mục tiêu:** Xây dựng khối đánh giá công khai ở chân trang tour, hỗ trợ gắn nhãn xác thực cho khách đã hoàn thành chuyến đi.
* **Các công việc cụ thể:**
  1. **Nâng cấp Model & Controller (`Review.php`, `ReviewController.php`):**
     - Cho phép nhận `booking_id` là `null` hoặc số cụ thể.
     - Tự động kiểm tra: Nếu du khách có đơn hàng `completed` cho tour này $\rightarrow$ Gán `is_verified = 1`, ngược lại `is_verified = 0`.
     - Chặn du khách chưa đăng nhập (bắt buộc session hợp lệ).
     - Ngăn chặn spam đánh giá liên tục trong khoảng thời gian ngắn.
  2. **Giao diện Khối Đánh Giá tại `app/views/services/show.php`:**
     - Thiết kế khối Đánh giá đặt ở cuối trang (ngay dưới phần Hỏi - Đáp).
     - Thanh tóm tắt: Điểm trung bình sao (ví dụ: `4.9 / 5.0 ⭐`) và tỷ lệ các mức sao (5 sao, 4 sao...).
     - Danh sách đánh giá: Avatar, Họ tên, Thời gian, Số sao vàng, Bình luận, và Huy hiệu xanh nổi bật **"Đã tự trải nghiệm"** đối với du khách đã đi tour thực tế.
     - Form gửi đánh giá trực tiếp via AJAX: Cho phép chọn số sao (1..5) và viết cảm nhận. Nếu chưa đăng nhập, hiển thị nút *"Đăng nhập để đánh giá"*.

---

### GIAI ĐOẠN 3: Mở Rộng Bản Tin Cộng Đồng (3 Vai Trò) & Tin Tức RSS VnExpress
* **Mục tiêu:** Mở rộng trang Bản tin thành cộng đồng mở cho cả Du khách, Host và Admin cùng chia sẻ; tích hợp tin tức du lịch thời sự.
* **Các công việc cụ thể:**
  1. **Quyền Đăng Bài Cho 3 Vai Trò (`PostController.php`, `Post.php`):**
     - Cho phép `tourist`, `host`, và `admin` cùng đăng bài viết mới tại `/posts/create`.
     - Trạng thái mặc định: `status = 'published'` (hiển thị ngay).
     - Admin vẫn giữ toàn quyền quản trị (sửa, xóa, ẩn bài vi phạm tại `/admin/posts`).
     - Bổ sung nút nổi bật **"Chia Sẻ Câu Chuyện / Viết Bài Mới"** ngay trên thanh tiêu đề trang Bản tin (`/posts`).
     - Hỗ trợ tải file ảnh đại diện bài viết từ máy tính (lưu trữ vào thư mục `public/assets/images/posts/`).
     - Dọn dẹp toàn bộ ghi chú kỹ thuật trên form đăng tin.
  2. **Tích hợp Tin tức RSS VnExpress trên `/posts`:**
     - Lấy song song: Bài viết nội bộ cộng đồng + Tin tức RSS báo chí từ VnExpress Du lịch.
     - Gộp chung hiển thị trên danh sách trang Bản tin với nhãn nhận diện rõ ràng:
       - Huy hiệu xanh: *"Cộng đồng OnlineTraveller"* (kèm tên tác giả và vai trò: Dân bản địa / Du khách).
       - Huy hiệu cam: *"Nguồn: VnExpress Du Lịch"*.
     - Phân trang cơ bản: ~6 tin/trang (có nút Trang trước / Trang sau).

---

### GIAI ĐOẠN 4: Hồ Sơ Người Dùng (Giới Tính, Ngày Sinh), Bản In Đơn & Đăng Nhập
* **Mục tiêu:** Nâng cao chất lượng dữ liệu hồ sơ cá nhân, làm sạch bản in hóa đơn và tối ưu form đăng nhập.
* **Các công việc cụ thể:**
  1. **Mở rộng CSDL & Hồ sơ người dùng (`app/views/users/profile.php`):**
     - Tạo migration SQL thêm 2 cột vào `tbl_users`:  
       `gender ENUM('male', 'female', 'other') DEFAULT 'other'`  
       `birth_date DATE NULL`
     - Cập nhật Stored Procedure `dhs_sp_users_update_profile` nhận thêm 2 tham số mới.
     - Cập nhật Model [`app/models/User.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/models/User.php) và Controller [`app/controllers/UserController.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/controllers/UserController.php).
     - Thay thế ô nhập *Địa bàn* tự do bằng **Combobox chọn Tỉnh / Thành phố** chuẩn Việt Nam.
     - Thêm ô chọn *Giới tính* và *Ngày tháng năm sinh*.
     - Cá nhân hóa gợi ý trường *Bio*: Tourist thì gợi ý về sở thích du lịch; Host thì gợi ý về kinh nghiệm và bản sắc văn hóa.
     - Xóa toàn bộ câu ghi chú kỹ thuật thừa (`dhs_sp_users_update_profile trong CSDL...`).
  2. **Bản In Chi Tiết Đơn Đặt Hàng (`app/views/bookings/detail.php`):**
     - Bổ sung bộ lọc `@media print` trong CSS:
       - Ẩn thanh breadcrumb điều hướng.
       - Ẩn toàn bộ các nút thao tác: *"Hủy đặt đơn"*, *"In đơn"*, *"Thanh toán"*, *"Danh sách chuyến đi"*.
       - Định dạng lại khổ giấy in A4 gọn gàng, trang trọng.
  3. **Giao Diện Đăng Nhập (`app/views/auth/login.php`):**
     - Bổ sung nút icon con mắt bật/tắt hiển thị mật khẩu (`type="password"` $\leftrightarrow$ `type="text"`).

---

### GIAI ĐOẠN 5: Cải Tiến Form Đăng Tour Cho Host (Tọa Độ Tự Động & Upload Ảnh Trạm) - *ƯU TIÊN*
* **Mục tiêu:** Đơn giản hóa tối đa quy trình đăng tour cho người bản địa, không đòi hỏi kiến thức kỹ thuật.
* **Các công việc cụ thể:**
  1. **Tự Động Hóa Tọa Độ Bản Đồ (Geocoding ngầm):**
     - Tại form đăng tour ([`app/views/host/services/create.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/host/services/create.php) & `edit.php`):
       - Chuyển 2 ô nhập *Vĩ độ (Latitude)* và *Kinh độ (Longitude)* thành `input type="hidden"`.
       - Hiển thị 2 trường nhập trực quan:
         1. **Tỉnh / Thành phố**: Combobox chọn các vùng du lịch (Lâm Đồng, Quảng Nam, Lào Cai, Hà Giang, Kiên Giang...).
         2. **Địa điểm đón / Điểm hẹn cụ thể**: Ô nhập text dễ hiểu (ví dụ: *Cổng bến thuyền Hồ Tuyền Lâm, Đà Lạt*).
       - Tích hợp từ điển tọa độ chuẩn các tỉnh du lịch Việt Nam + Script tự động gán tọa độ ngầm tương ứng.
  2. **Upload Trực Tiếp Ảnh Các Trạm Dừng Chân:**
     - Thay thế ô nhập link text ảnh trạm bằng nút **Chọn file ảnh** (`input type="file" accept="image/*"`).
     - Hỗ trợ lưu ảnh tự động vào thư mục `public/assets/images/tours/stops/`.
     - Loại bỏ các thuật ngữ kỹ thuật như "(Three.js)" trong tiêu đề các bước.

---

### GIAI ĐOẠN 6: Bộ Lọc Khoảng Giá Slider, Đồng Bộ Card Tour & Banner Bố Cục Mới
* **Mục tiêu:** Đồng bộ ngôn ngữ thiết kế toàn hệ thống và cải tiến giao diện khám phá.
* **Các công việc cụ thể:**
  1. **Thanh Trượt Khoảng Giá (Range Slider):**
     - Thêm thanh trượt ngang chọn ngân sách `0đ -> 5.000.000đ+` tại cả Trang chủ (`home/index.php`) và Trang khám phá (`services/index.php`).
     - Hiển thị nhãn số tiền động nhảy theo vị trí kéo trượt.
  2. **Đồng Bộ Card Tour:**
     - Refactor lại danh sách tour ở trang Khám phá (`services/index.php`) theo phong cách Card Dark-Alpine tràn ảnh 3/4 đồng bộ 100% với Trang chủ.
  3. **Bố Cục Lại Banner:**
     - **Trang chủ:** Thiết kế Slider Banner có các thumbnail trượt nhỏ.
     - **Trang chi tiết tour:** Sửa lỗi mất chân ảnh banner; bố cục lại: chữ bên trái, dải card ảnh các trạm đặt dọc bên phải theo đúng ảnh mẫu.

---

## 3. TÍCH HỢP GOOGLE CLOUD APIS

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
