# TÀI LIỆU KỸ THUẬT & HƯỚNG DẪN KIỂM THỬ GIAI ĐOẠN 4
## NÂNG CẤP HỒ SƠ NGƯỜI DÙNG, BẢN IN ĐƠN ĐẶT CHỖ & NÚT HIỆN MẬT KHẨU ĐĂNG NHẬP

> **Phiên bản:** 1.0  
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (25/25 Assertions PASSED)**  
> **Master Test Suite:** 19/19 Suites PASSED (336/336 assertions, 100%)  
> **Tác giả:** Antigravity AI & Nhóm Phát Triển OnlineTraveller  

---

## 1. TỔNG QUAN YÊU CẦU & CẢI TIẾN

Dựa trên kết quả kiểm thử thực tế của người dùng và kế hoạch tại [`docs/plan.md`](file:///e:/Dev/Study'/Web/OnlineTraveller/docs/plan.md), Giai đoạn 4 tập trung giải quyết 3 nhóm vấn đề cốt lõi về trải nghiệm người dùng:

1. **Chuẩn hóa Hồ Sơ Cá Nhân (`/profile`):**
   - Bổ sung 2 trường dữ liệu nhân khẩu học quan trọng: **Giới tính** (`gender`) và **Ngày sinh** (`birth_date`).
   - Chuyển trường nhập *Địa bàn cư trú* tự do thành **Combobox chọn Tỉnh / Thành phố** đồng bộ 29 địa bàn du lịch Việt Nam từ `FormatHelper::getProvincesList()`.
   - Cá nhân hóa gợi ý trường *Bio/Tiểu sử* (Du khách: sở thích xê dịch; Dân bản địa: câu chuyện văn hóa, làng nghề).
   - Xóa bỏ triệt để câu ghi chú kỹ thuật CSDL thừa trên giao diện (`dhs_sp_users_update_profile...`).
   - Cập nhật hiển thị Giới tính và Ngày sinh trên thẻ tóm tắt hồ sơ bên trái.

2. **Tối Ưu Bản In Hóa Đơn / Phiếu Đặt Chỗ (`/bookings/{id}`):**
   - Thiết lập CSS `@media print` chuẩn khổ in A4 portrait.
   - Ẩn toàn bộ thành phần điều hướng (top floating navbar, breadcrumb, footer).
   - Ẩn toàn bộ nút bấm hành động tương tác: *"In Đơn"*, *"Danh sách chuyến đi"*, *"Thanh toán cọc MoMo"*, *"Hủy đặt chỗ này"*, *"Tìm kiếm gói tour khác"*, và Modal hủy.
   - Thêm phần tiêu đề in ấn trang trọng (**ONLINETRAVELLER - PHIẾU XÁC NHẬN ĐẶT TOUR**, Hotline 1900 6868, mã đơn và ngày giờ in).

3. **Nút Bật / Tắt Hiển Thị Mật Khẩu Đăng Nhập (`/login`):**
   - Thêm nút icon con mắt (`bi-eye` $\leftrightarrow$ `bi-eye-slash`) bên trong ô nhập mật khẩu.
   - Thao tác mượt mà bằng JavaScript thuần, hỗ trợ người dùng kiểm tra mật khẩu trước khi bấm Đăng nhập.

---

## 2. KIẾN TRÚC & CÁC TỆP TIN THAY ĐỔI

```
OnlineTraveller/
├── database/
│   ├── changes/
│   │   └── 07_user_profile_enhancement.sql    # Migration CSDL: Cột gender, birth_date & Stored Procedures
│   └── procedures/
│       └── 01_users_categories.sql            # Cập nhật dhs_sp_users_update_profile & dhs_sp_users_find_login
├── app/
│   ├── models/
│   │   └── User.php                           # Bổ sung gender, birth_date vào updateProfile, getById, getAllUsers
│   ├── services/
│   │   └── AuthService.php                    # updateProfile nhận thêm gender, birth_date và sync vào $_SESSION
│   ├── controllers/
│   │   └── AuthController.php                 # updateProfile trích xuất gender, birth_date từ Request POST
│   └── views/
│       ├── profile/
│       │   └── index.php                      # UI Combobox Tỉnh/Thành, Giới tính, Ngày sinh, Bio cá nhân hóa
│       ├── bookings/
│       │   └── detail.php                     # CSS @media print, .no-print và Print Header khổ A4
│       └── auth/
│           └── login.php                      # Nút con mắt hiện/ẩn mật khẩu & JS toggle
├── tests/
│   ├── test_phase4_profile_enhancements.php   # Test suite chuyên biệt 25 assertions
│   └── run_all_tests.php                      # Tích hợp bộ test vào Master Runner (19 suites, 336 assertions)
└── docs/
    ├── plan.md                                # Cập nhật trạng thái hoàn thành Giai đoạn 4
    └── phase4_profile_enhancements.md         # Tài liệu kỹ thuật chi tiết
```

---

## 3. CHI TIẾT CÁC THAY ĐỔI KỸ THUẬT

### 3.1. CSDL & Stored Procedure

- **Bảng `tbl_users`**:
  ```sql
  ALTER TABLE tbl_users
  ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other' AFTER bio,
  ADD COLUMN IF NOT EXISTS birth_date DATE NULL AFTER gender;
  ```

- **Thủ tục `dhs_sp_users_update_profile`**:
  ```sql
  CREATE PROCEDURE dhs_sp_users_update_profile(
      IN p_user_id BIGINT UNSIGNED,
      IN p_name VARCHAR(150),
      IN p_phone VARCHAR(32),
      IN p_living_area VARCHAR(255),
      IN p_bio TEXT,
      IN p_avatar VARCHAR(255),
      IN p_gender VARCHAR(16),
      IN p_birth_date DATE
  )
  ```
  Thủ tục tự động chuẩn hóa giới tính hợp lệ (`male`, `female`, `other`), cập nhật `updated_at = NOW(6)` và trả về toàn bộ thông tin tài khoản đồng bộ.

- **Thủ tục `dhs_sp_users_find_login`**:
  Bổ sung `gender` và `birth_date` vào danh sách cột SELECT để khi người dùng đăng nhập, phiên làm việc có sẵn thông tin đầy đủ.

---

### 3.2. Form Hồ Sơ Cá Nhân (`app/views/profile/index.php`)

1. **Combobox Tỉnh / Thành phố:**
   Tự động sinh từ `FormatHelper::getProvincesList()`. Nếu người dùng đã có địa chỉ cũ không trùng khớp, hệ thống vẫn hiển thị lựa chọn hiện tại `(Tùy chỉnh)` để không làm mất dữ liệu cũ.
2. **Combobox Giới tính & Date Picker Ngày sinh:**
   - Giới tính: Lựa chọn `Nam`, `Nữ`, `Khác / Không công khai`.
   - Ngày sinh: Trường chuẩn HTML5 `<input type="date" name="birth_date">`.
3. **Cơ chế Chuyển Đổi Chỉ Đọc $\leftrightarrow$ Chỉnh Sửa:**
   - Ở chế độ chỉ đọc: Các thẻ `<select>` được đặt `disabled`, các thẻ `<input>`, `<textarea>` được đặt `readonly`.
   - Khi bấm *"Chỉnh sửa hồ sơ"*: Mở khóa toàn bộ input và select, đổi viền trắng sáng, focus ô Họ và tên.
   - Khi bấm *"Hủy bỏ"*: Khôi phục lại giá trị ban đầu từ thuộc tính `data-original-val`.
   - Khi bấm *"Lưu thay đổi"*: JS tự động mở khóa `disabled` của các thẻ `<select>` trước khi submit form để dữ liệu POST gửi lên PHP đầy đủ.
4. **Cá nhân hóa Bio & Làm sạch giao diện:**
   - Tourist: *"Chia sẻ sở thích du lịch, phong cách xê dịch, các vùng đất bạn yêu thích và mong muốn trải nghiệm cùng dân bản địa..."*
   - Host: *"Chia sẻ đôi nét về bạn, sự gắn bó với quê hương, làng nghề truyền thống hoặc lý do bạn mong muốn dẫn dắt khách du lịch khám phá mảnh đất của mình..."*
   - Xóa bỏ hoàn toàn dòng ghi chú kỹ thuật `dhs_sp_users_update_profile`.

---

### 3.3. Tối Ưu Bản In `@media print` (`app/views/bookings/detail.php`)

Thêm khối style chuẩn in ấn:
```css
@media print {
    .top-floating-header, header, footer, nav[aria-label="breadcrumb"],
    .breadcrumb, .no-print, .btn, button, #cancelModal, .modal, .modal-backdrop {
        display: none !important;
    }
    @page {
        size: A4 portrait;
        margin: 12mm 10mm 12mm 10mm;
    }
    html, body {
        background: #ffffff !important;
        color: #111827 !important;
        font-size: 11pt !important;
    }
    .print-receipt-header {
        display: block !important;
    }
}
```
Và tiêu đề in ấn:
```html
<div class="print-receipt-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-0 text-dark">ONLINETRAVELLER</h3>
            <div class="small text-muted">Nền Tảng Du Lịch & Trải Nghiệm Cùng Dân Bản Địa</div>
            <div class="small text-muted">Hotline CSKH: 1900 6868 | Email: hotro@onlinetraveller.vn</div>
        </div>
        <div class="text-end">
            <span class="badge bg-dark text-white px-3 py-1 fs-6 font-monospace mb-1 d-inline-block">#<?= $code ?></span>
            <div class="fw-bold text-success">PHIẾU XÁC NHẬN ĐẶT TOUR</div>
            <div class="small text-muted">Ngày in: <?= date('d/m/Y H:i') ?></div>
        </div>
    </div>
</div>
```

---

### 3.4. Nút Hiện/Ẩn Mật Khẩu (`app/views/auth/login.php`)

```html
<div class="input-group">
    <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
    <input type="password" name="password" id="inputPassword" class="form-control bg-light border-0" placeholder="••••••••" required>
    <button type="button" class="btn btn-light border-0 text-muted" id="btnTogglePassword" title="Hiện/ẩn mật khẩu">
        <i class="bi bi-eye" id="iconEye"></i>
    </button>
</div>
```
Script JavaScript chuyển đổi tức thì giữa `type="password"` $\leftrightarrow$ `type="text"` và đổi icon `bi-eye` $\leftrightarrow$ `bi-eye-slash`.

---

## 4. KẾT QUẢ KIỂM THỬ TỰ ĐỘNG

Chạy bộ kiểm thử chuyên biệt:
```bash
C:\xampp\php\php.exe tests/test_phase4_profile_enhancements.php
```
Kết quả:
```
====================================================================================
   PHASE 4: KIỂM THỬ HỒ SƠ NGƯỜI DÙNG, BẢN IN ĐƠN VÀ NÚT HIỆN MẬT KHẨU ĐĂNG NHẬP   
====================================================================================

--- 1. Cấu trúc CSDL tbl_users & Stored Procedures ---
  [PASS] Assertion #1: tbl_users có cột 'gender' kiểu ENUM
  [PASS] Assertion #2: tbl_users có cột 'birth_date' kiểu DATE
  [PASS] Assertion #3: dhs_sp_users_update_profile trả về thông tin cập nhật thành công
  [PASS] Assertion #4: dhs_sp_users_update_profile lưu đúng gender = 'male'
  [PASS] Assertion #5: dhs_sp_users_update_profile lưu đúng birth_date = '1995-08-15'
  [PASS] Assertion #6: dhs_sp_users_find_login trả về cột gender và birth_date

--- 2. Model User & AuthService Profile Methods ---
  [PASS] Assertion #7: User::getById trả về đầy đủ gender và birth_date
  [PASS] Assertion #8: User::getAllUsers trả về trường gender và birth_date
  [PASS] Assertion #9: AuthService::updateProfile cập nhật thành công giới tính Nữ và ngày sinh
  [PASS] Assertion #10: AuthService::updateProfile đồng bộ thông tin mới vào $_SESSION['user']

--- 3. Giao diện Hồ sơ cá nhân (profile/index.php) ---
  [PASS] Assertion #11: View có Combobox Tỉnh / Thành phố cư trú (select name='living_area')
  [PASS] Assertion #12: View có Combobox Giới tính (select name='gender') gồm Nam, Nữ, Khác
  [PASS] Assertion #13: View có trường Ngày sinh (input type='date' name='birth_date')
  [PASS] Assertion #14: View đã xóa hoàn toàn câu ghi chú kỹ thuật (dhs_sp_users_update_profile)
  [PASS] Assertion #15: View có phân biệt bio placeholder cho Host và Tourist
  [PASS] Assertion #16: Thẻ tóm tắt bên trái hiển thị Giới tính và Ngày sinh
  [PASS] Assertion #17: JavaScript form submit tự động mở khóa disabled cho select để gửi dữ liệu

--- 4. Bản in Chi Tiết Đơn Đặt Chỗ (@media print trong bookings/detail.php) ---
  [PASS] Assertion #18: Trang chi tiết đơn có khối CSS @media print
  [PASS] Assertion #19: @media print ẩn toàn bộ breadcrumb, navbar, nút bấm và modal
  [PASS] Assertion #20: Trang có Print Header trang trọng (ONLINETRAVELLER, PHIẾU XÁC NHẬN ĐẶT TOUR)
  [PASS] Assertion #21: Các nút thao tác và điều hướng mang class no-print

--- 5. Nút Bật/Tắt Mật khẩu Đăng nhập (auth/login.php) ---
  [PASS] Assertion #22: Ô nhập mật khẩu có id='inputPassword'
  [PASS] Assertion #23: Có nút icon con mắt bật/tắt hiển thị mật khẩu (id='btnTogglePassword')
  [PASS] Assertion #24: Có JavaScript chuyển đổi qua lại type='password' và type='text'
  [PASS] Assertion #25: JavaScript toggle đổi icon bi-eye sang bi-eye-slash

====================================================================================
KẾT QUẢ KIỂM THỬ: 25 / 25 assertions đạt chuẩn (100%)
>>> XÁC NHẬN: GIAI ĐOẠN 4 ĐÃ HOÀN THÀNH VÀ ĐẠT CHUẨN 100%! <<<
====================================================================================
```

Chạy toàn bộ hệ thống kiểm thử:
```bash
C:\xampp\php\php.exe tests/run_all_tests.php
```
**Tổng kết:** **19/19 Suites PASS**, **336/336 Assertions (100.0%)**.

---

## 5. HƯỚNG DẪN KIỂM THỬ THỦ CÔNG (DÀNH CHO NGƯỜI DÙNG)

1. **Kiểm tra Trang Đăng Nhập (`/login`):**
   - Vào `/login`.
   - Gõ mật khẩu `password123`.
   - Nhấp vào biểu tượng icon con mắt $\rightarrow$ Mật khẩu hiển thị dạng văn bản rõ ràng, icon đổi thành con mắt gạch chéo.
   - Nhấp lần nữa $\rightarrow$ Mật khẩu ẩn lại thành các dấu chấm tròn (`••••••••`).

2. **Kiểm tra Hồ Sơ Cá Nhân (`/profile`):**
   - Đăng nhập với tài khoản `tourist@gmail.com` / `password123`.
   - Truy cập `/profile`.
   - Kiểm tra chế độ ban đầu: Nhãn *"Chỉ đọc"*, các trường bị khóa.
   - Bấm *"Chỉnh sửa hồ sơ"*:
     - Chọn Giới tính: *Nam* hoặc *Nữ*.
     - Chọn Ngày sinh: ví dụ *15/08/1995*.
     - Chọn Tỉnh/Thành: ví dụ *Lâm Đồng (Đà Lạt, Lạc Dương)*.
     - Nhập tiểu sử.
     - Kiểm tra không còn bất kỳ dòng chữ kỹ thuật `dhs_sp_users_update_profile`.
     - Bấm *"Lưu Thay Đổi"*.
   - Trang load lại: Thẻ bên trái và các ô bên phải hiển thị chính xác Giới tính, Ngày sinh, Tỉnh/Thành đã chọn.

3. **Kiểm tra Bản In Đơn Đặt Chỗ (`/bookings/{id}`):**
   - Truy cập vào 1 đơn đặt chỗ bất kỳ (ví dụ `/bookings/1` hoặc qua `/my-bookings`).
   - Bấm nút **"In Đơn"** (hoặc nhấn `Ctrl + P`).
   - Trên hộp thoại Print Preview của trình duyệt:
     - Thanh điều hướng kính mờ, breadcrumb, các nút *"In Đơn"*, *"Danh Sách Chuyến Đi"*, *"Thanh toán cọc MoMo"*, *"Hủy Đặt Chỗ"* đều biến mất hoàn toàn.
     - Hiển thị phần header trang trọng: **ONLINETRAVELLER - PHIẾU XÁC NHẬN ĐẶT TOUR** kèm Hotline 1900 6868 và Mã đơn.
     - Bố cục thông tin chi tiết tour và chi phí rõ ràng, không bị vỡ giao diện trên khổ A4.
