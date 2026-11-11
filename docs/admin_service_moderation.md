# QUẢN LÝ & KIỂM DUYỆT TRẢI NGHIỆM TOUR (ADMIN SERVICE MODERATION)
> **Mục tiêu:** Khắc phục lỗi thiếu nút phê duyệt trải nghiệm mới của Host, giúp Admin dễ dàng kiểm duyệt, mở bán hoặc từ chối tour có lý do.  
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (18/18 Tests PASSED — 380/380 Toàn hệ thống)**  
> **Bộ kiểm thử:** `tests/test_admin_service_moderation.php` & `tests/run_all_tests.php`  
> **Ngày thực hiện:** 10/11/2026  

---

## 1. NGUYÊN NHÂN VÀ HIỆN TRẠNG TRƯỚC SỬA LỖI

- **Hiện trạng:**
  - Khi Local Host đăng ký một gói trải nghiệm mới (tại `/host/services/create`), tour được tạo và lưu vào CSDL với trạng thái mặc định là `pending` (chờ duyệt).
  - Tuy nhiên, trong giao diện Quản trị (`/admin`), Admin không có nút hay trang riêng để bấm **"Phê duyệt (Duyệt)"** hoặc **"Từ chối"**, chỉ có link xem chi tiết `/services/{id}`.
  - Do đó, tour mới bị "kẹt" ở trạng thái `pending` vĩnh viễn, không thể chuyển sang `approved` để hiển thị mở bán công khai trên sàn khám phá cho du khách.
- **Nghiệp vụ nền tảng đã có sẵn trong CSDL & Model:**
  - Stored Procedure `ptl_sp_services_moderate(p_admin_id, p_id, p_status, p_rejection_reason)` đã được tạo trong `database/procedures/02_services_bookings.sql`.
  - Phương thức `Service::moderate(int $adminId, int $id, string $status, ?string $rejectionReason = null): array` đã có trong `app/models/Service.php`.
  - Nhưng controller chưa đăng ký route xử lý và view chưa có các nút thao tác.

---

## 2. CÁC NÂNG CẤP VÀ GIẢI PHÁP ĐÃ TRIỂN KHAI

### 2.1. Đăng Ký Route Quản Trị & Kiểm Duyệt Mới (`routes/web.php`)
- `GET /admin/services`: Xem danh sách tất cả các gói trải nghiệm, lọc theo trạng thái (`pending`, `approved`, `rejected`), tìm kiếm theo tên tour, địa điểm, Host. Được bảo vệ bởi `[AuthMiddleware::class, AdminMiddleware::class]`.
- `POST /admin/services/{id}/moderate`: Thao tác cập nhật trạng thái kiểm duyệt (`approved`, `rejected`, `pending`, `archived`) kèm lý do từ chối nếu có. Được bảo vệ bởi `[AuthMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]`.

### 2.2. Bổ Sung Logic Xử Lý Trong `AdminController.php`
- `services(Request $request)`:
  - Kiểm tra quyền Admin (redirect `/login` nếu không phải Admin hoặc trả về 403 nếu là AJAX).
  - Thống kê số lượng tour theo 4 trạng thái: Tất cả (`all`), Chờ duyệt (`pending`), Đang mở bán (`approved`), Đã từ chối (`rejected`).
  - Lọc và tìm kiếm linh hoạt theo từ khóa và trạng thái.
  - Truyền dữ liệu sang view `admin/services/index`.
- `moderateService(Request $request, string $id)`:
  - Xác thực quyền Admin bảo mật chặt chẽ.
  - Nhận tham số `status` (`approved` / `rejected`) và `rejection_reason`.
  - Gọi `Service::moderate()` thực thi Stored Procedure `ptl_sp_services_moderate`.
  - Hỗ trợ cả phản hồi JSON (AJAX) và chuyển hướng web (Redirect kèm Flash message thành công).

### 2.3. Bổ Sung Nút Duyệt Ngay Tại Bảng Điều Khiển (`app/views/admin/dashboard.php`)
- Cột **"Kiểm duyệt"** trong bảng *Trải Nghiệm Mới Đăng Ký Cần Kiểm Duyệt*:
  - **Tour trạng thái `pending`:**
    - Nút xanh lá **[<i class="bi bi-check-circle-fill"></i> Duyệt]**: Bấm duyệt mở bán ngay lập tức có popup xác nhận.
    - Nút đỏ **[<i class="bi bi-x-circle"></i>]**: Bật Modal yêu cầu Admin nhập lý do từ chối (ví dụ: *Ảnh mờ, thông tin trạm chưa chi tiết*) gửi thông báo cho Host sửa đổi.
  - **Tour trạng thái `approved`:** Hiển thị huy hiệu `Đang mở bán`.
  - **Tour trạng thái `rejected`:** Hiển thị nút `Duyệt lại` nếu Host đã cập nhật.
- Nút điều hướng nhanh **"Trải Nghiệm (Tours)"** trên thanh header của Admin và click trực tiếp từ Card thống kê **"CHỜ DUYỆT (PENDING)"** để xem danh sách cần xử lý.

### 2.4. Xây Dựng Trang Quản Lý Riêng Biệt (`app/views/admin/services/index.php`)
- Giao diện Dark-Alpine kết hợp bảng trắng phẳng trực quan, sang trọng.
- 4 Card thống kê trạng thái tương tác nhanh: Tất cả, Chờ phê duyệt, Đang mở bán, Đã từ chối.
- Bộ lọc kết hợp tìm kiếm theo từ khóa và combobox lọc trạng thái.
- Danh sách hiển thị thumbnail trạm, tên tour, thông tin liên hệ của Host (Họ tên, SĐT, Email), địa điểm, giá và trạng thái badge tương ứng.
- Cột thao tác kiểm duyệt đầy đủ với nút Duyệt, nút Từ chối kèm Modal nhập lý do, nút Gỡ xuống/Tạm ngưng, và nút Xem trang tour.

### 2.5. Thanh Thông Báo & Duyệt Nhanh Trên Trang Chi Tiết (`app/views/services/show.php`)
- Khi Admin đăng nhập và truy cập vào bất kỳ tour nào đang ở trạng thái `pending`:
  - Đầu trang lập tức xuất hiện khối cảnh báo màu vàng nổi bật:  
    *<i class="bi bi-shield-exclamation"></i> "Trải nghiệm này đang ở trạng thái CHỜ DUYỆT (PENDING) và chưa hiển thị công khai cho du khách."*
  - Đi kèm 3 nút hành động nhanh:
    1. **[Phê Duyệt Mở Bán]**: Duyệt ngay tại chỗ mà không cần quay về dashboard.
    2. **[Từ Chối]**: Mở Modal nhập lý do từ chối.
    3. **[Danh Sách Duyệt]**: Liên kết nhanh về `/admin/services`.
- Nếu tour ở trạng thái `rejected`: Hiển thị thanh màu đỏ kèm lý do từ chối và nút **[Phê Duyệt Lại]**.
- Khách vãng lai và Du khách thường sẽ hoàn toàn **không nhìn thấy** thanh quản trị này.

### 2.6. Bổ Sung Menu Điều Hướng Nhanh (`app/views/layouts/main.php`)
- Thêm mục **"Quản lý & Duyệt Tour"** trong Dropdown menu avatar góc phải khi Admin đăng nhập.
- Thêm link **"Duyệt Tour Mới"** trong Offcanvas Menu trên thiết bị di động.

---

## 3. KẾT QUẢ KIỂM THỬ TỰ ĐỘNG

- Tạo bộ kiểm thử độc lập: `tests/test_admin_service_moderation.php` gồm 18 assertions:
  1. Tạo tour mẫu ở trạng thái `pending`.
  2. Kiểm tra phân quyền: Guest, Tourist, Host đều bị chặn truy cập và gọi thao tác duyệt (Status 302 / 403).
  3. Admin truy cập trang `/admin/services` thành công (Status 200, hiển thị đủ thành phần).
  4. Admin phê duyệt tour thành công: CSDL cập nhật `status = 'approved'`.
  5. Admin từ chối tour thành công: CSDL cập nhật `status = 'rejected'` và lưu đúng `rejection_reason`.
  6. Trang chi tiết `/services/{id}` hiển thị đúng banner kiểm duyệt cho Admin, ẩn hoàn toàn đối với Du khách.
  7. Dọn dẹp dữ liệu kiểm thử.
- **Kết quả chạy:**
  - `test_admin_service_moderation.php`: **18/18 PASSED (100%)**
  - Master Runner `run_all_tests.php`: **21/21 Suites PASSED, 380/380 Assertions PASSED (100%)**
