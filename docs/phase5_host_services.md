# HƯỚNG DẪN KIỂM THỬ GIAI ĐOẠN 5: CẢI TIẾN FORM ĐĂNG TOUR HOST
> **Căn cứ:** Báo cáo kiểm thử & Góp ý cải tiến giao diện Host  
> **Trạng thái:** ✅ **ĐÃ HOÀN THÀNH & ĐẠT 24/24 BÀI TEST TỰ ĐỘNG (100% PASS)**  
> **Tài liệu tham chiếu:** [`docs/plan.md`](file:///e:/Dev/Study'/Web/OnlineTraveller/docs/plan.md)

---

## 1. TỔNG QUAN CÁC CẢI TIẾN ĐÃ THỰC HIỆN

1. **Tự động hóa Tọa độ bản đồ (Geocoding ngầm):**
   - Loại bỏ hoàn toàn việc bắt Host nhập tay 2 trường số thập phân phức tạp *Vĩ độ (Latitude)* và *Kinh độ (Longitude)*. Hai trường này đã được chuyển thành `<input type="hidden">`.
   - Bổ sung 2 trường nhập trực quan, thân thiện:
     1. **Combobox chọn Tỉnh / Thành phố:** Tích hợp sẵn 29 địa bàn du lịch trọng điểm Việt Nam (Lâm Đồng, Lào Cai, Quảng Nam, Đà Nẵng, Hà Giang, TP. Hồ Chí Minh...).
     2. **Điểm đón / Điểm hẹn cụ thể:** Ô nhập văn bản thông thường (ví dụ: *Cổng bến thuyền Hồ Tuyền Lâm, Đà Lạt*).
   - Tích hợp từ điển tọa độ chuẩn xác (`FormatHelper::lookupCoordinates` và JavaScript hotspots) tự động cập nhật tọa độ ngay khi Host gõ địa danh hoặc chọn tỉnh thành.
   - Hiển thị badge xanh *"Tọa độ bản đồ tự động"* kèm tọa độ để Host dễ dàng nhận biết vị trí được ghim.
   - Phía Backend (`HostController::storeService` & `updateService`) tự động bù đắp tọa độ và tự động ghép Tỉnh/Thành vào chuỗi địa chỉ nếu Host để trống.

2. **Upload trực tiếp ảnh các trạm dừng chân:**
   - Tại mục 4, bổ sung nút **Tải ảnh điểm dừng từ máy tính** (`<input type="file" name="stop_files[]">`).
   - Có khung ảnh xem trước (Preview) ngay khi chọn ảnh từ máy hoặc nhập URL có sẵn.
   - Backend `TourService::handleStopImageUpload()` tự động kiểm tra định dạng (JPG/PNG/WEBP), giới hạn dung lượng (tối đa 5MB) và lưu trữ tệp an toàn vào thư mục `public/assets/images/tours/stops/`.
   - Vẫn hỗ trợ song song dán URL ảnh có sẵn hoặc giữ nguyên ảnh cũ khi sửa tour.

3. **Làm sạch giao diện & Loại bỏ thuật ngữ kỹ thuật:**
   - Xóa bỏ hoàn toàn cụm từ kỹ thuật `(Three.js)` khỏi tiêu đề và phụ đề.
   - Đổi tên bước 4 thành: *"4. Lịch Trình & Điểm Dừng Chân Không Gian (3D Tour)"*.
   - Bổ sung ghi chú minh bạch *"Giá niêm yết đã bao gồm 8% thuế GTGT"*.

---

## 2. DANH SÁCH TỆP MÃ NGUỒN ĐÃ CẬP NHẬT

| STT | Đường dẫn tệp | Vai trò / Nội dung thay đổi |
| :---: | :--- | :--- |
| 1 | [`app/helpers/FormatHelper.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/helpers/FormatHelper.php) | Thêm `getProvincesList()` (29 tỉnh/thành) và `lookupCoordinates()` (Geocoding ngầm) |
| 2 | [`app/services/TourService.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/services/TourService.php) | Bổ sung xử lý upload tệp ảnh trạm dừng chân vào `public/assets/images/tours/stops/` |
| 3 | [`app/controllers/HostController.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/controllers/HostController.php) | Cập nhật `createService`, `storeService`, `editService`, `updateService` hỗ trợ Geocoding và upload file trạm |
| 4 | [`app/views/host/services/create.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/host/services/create.php) | Giao diện đăng tour mới: Combobox tỉnh/thành, hidden lat/lng, upload file trạm, bỏ Three.js |
| 5 | [`app/views/host/services/edit.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/host/services/edit.php) | Giao diện sửa tour: Tự động nhận diện tỉnh/thành, thay ảnh trạm từ máy tính |
| 6 | [`tests/test_phase5_host_services.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/tests/test_phase5_host_services.php) | Kịch bản kiểm thử tự động 24 bài test cho Giai đoạn 5 |
| 7 | [`tests/run_all_tests.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/tests/run_all_tests.php) | Tích hợp Phase 5 vào Master Runner (17 suite, 286 assertions PASS) |

---

## 3. HƯỚNG DẪN KIỂM THỬ THỦ CÔNG TRÊN TRÌNH DUYỆT

### Kịch bản 1: Đăng tour mới với Geocoding tự động
1. Đăng nhập với tài khoản Host: `host@onlinetraveller.local` / `Host@123`.
2. Vào **Host Portal** $\rightarrow$ **Quản lý tour** $\rightarrow$ Bấm nút **"Đăng Ký Tour Mới"** (`/host/services/create`).
3. Quan sát mục **1. Thông Tin Trải Nghiệm Cơ Bản**:
   - Xác nhận **không còn** thấy 2 ô nhập số "Vĩ độ" và "Kinh độ".
   - Chọn mục **Tỉnh / Thành phố**: Chọn *"Lâm Đồng (Đà Lạt, Lạc Dương)"*.
   - Nhập ô **Điểm đón**: Gõ *"Cổng bến thuyền Hồ Tuyền Lâm"*.
   - Quan sát góc dưới thấy huy hiệu xanh *"Tọa độ bản đồ tự động"* hiển thị `11.8906, 108.4287` hoặc `11.9404, 108.4583`.
4. Quan sát mục **4. Lịch Trình & Điểm Dừng Chân Không Gian (3D Tour)**:
   - Xác nhận tiêu đề không còn chữ `(Three.js)`.
   - Tại ô ảnh trạm dừng, bấm nút **"Chọn tệp"** để chọn 1 ảnh JPG/PNG bất kỳ từ máy tính.
   - Xác nhận khung ảnh xem trước (preview thumbnail) xuất hiện ngay lập tức bên dưới.
   - Bấm nút **"Thêm Điểm Dừng Chân"**, xác nhận trạm mới được bổ sung mượt mà và có thể tải ảnh riêng.
5. Bấm nút **"Đăng Ký Trải Nghiệm"**:
   - Hệ thống thông báo thành công. Tour xuất hiện trong danh sách tour của Host với địa chỉ và tọa độ đã được lưu hoàn chỉnh.

### Kịch bản 2: Chỉnh sửa tour đã có
1. Tại danh sách tour của Host, bấm nút **"Chỉnh sửa"** một tour bất kỳ (`/host/services/{id}/edit`).
2. Quan sát combobox **Tỉnh / Thành phố**: Hệ thống tự động nhận diện và pre-select đúng tỉnh của tour.
3. Quan sát các trạm dừng chân: Ảnh hiện tại được hiển thị thumbnail, có nút chọn file để thay ảnh mới từ máy.
4. Bấm **"Lưu Cập Nhật Tour"** và kiểm tra thông báo thành công.

---

## 4. KẾT QUẢ KIỂM THỬ TỰ ĐỘNG

Chạy lệnh:
```powershell
C:\xampp\php\php.exe tests/test_phase5_host_services.php
```
Kết quả:
```text
KẾT QUẢ KIỂM THỬ GIAI ĐOẠN 5: 24 / 24 TESTS PASSED (100%)
```

Chạy toàn bộ hệ thống:
```powershell
C:\xampp\php\php.exe tests/run_all_tests.php
```
Kết quả:
```text
Tổng số Suite kiểm thử:   17 (Thành công: 17, Thất bại: 0)
Tổng số Assertions test:  286 / 286 PASSED (Tỷ lệ: 100.0%)
```

---

## 5. LỆNH GIT COMMIT DÀNH CHO BRO TỰ THỰC HIỆN

Theo đúng nguyên tắc, bro vui lòng kiểm tra lại trên giao diện và chạy các lệnh sau để commit:

```bash
git add .
git commit -m "feat(phase-2-5): implement tour reviews verified badge, auto geocoding and stops image upload for host"
```
