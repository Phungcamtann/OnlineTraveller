# HƯỚNG DẪN KIỂM THỬ GIAI ĐOẠN 3: BẢN TIN CỘNG ĐỒNG 3 VAI TRÒ & TIN TỨC RSS VNEXPRESS
> **Căn cứ:** Báo cáo kiểm thử & Góp ý cải tiến bản tin cộng đồng  
> **Trạng thái:** ✅ **ĐÃ HOÀN THÀNH & ĐẠT 24/24 BÀI TEST TỰ ĐỘNG (100% PASS)**  
> **Tài liệu tham chiếu:** [`docs/plan.md`](file:///e:/Dev/Study'/Web/OnlineTraveller/docs/plan.md)

---

## 1. TỔNG QUAN CÁC CẢI TIẾN ĐÃ THỰC HIỆN

1. **Quyền Đăng Bài Mở Cho Cả 3 Vai Trò (Tourist, Host, Admin):**
   - Đã tạo Stored Procedure `pct_sp_community_posts_create` cho phép cả Du khách (`tourist`), Người bản địa (`host`), và Ban Quản Trị (`admin`) đăng bài viết mới tại `/posts/create`.
   - Bài viết được xuất bản ngay lập tức (`is_published = 1`) để tạo sân chơi tương tác sôi nổi.
   - Admin vẫn giữ toàn quyền quản trị (xem thống kê, chỉnh sửa, ẩn hoặc xóa bài viết vi phạm tại `/admin/posts`).
   - Khách vãng lai chưa đăng nhập bị chặn an toàn và được điều hướng đến trang Đăng nhập.

2. **Upload Trực Tiếp Ảnh Đại Diện Bài Viết:**
   - Tại form đăng bài, cho phép chọn file ảnh từ máy tính (`<input type="file" name="thumbnail_file">`).
   - Có khung ảnh xem trước (Preview) ngay lập tức khi chọn file hoặc nhập URL.
   - Ảnh được kiểm tra định dạng (JPG/PNG/WEBP), dung lượng (tối đa 5MB) và lưu tự động vào `public/assets/images/posts/`.

3. **Gộp Chung Dòng Tin Cộng Đồng & Tin Tức Báo Chí RSS VnExpress:**
   - Lấy đồng thời bài viết nội bộ từ cộng đồng và tin tức thời sự từ VnExpress Du Lịch (`https://vnexpress.net/rss/du-lich.rss`).
   - Hiển thị nhãn nhận diện trực quan:
     - Huy hiệu xanh lá: **"Bản Tin Cộng Đồng"** (kèm họ tên tác giả và vai trò: Du Khách / Host Bản Địa / Ban Quản Trị).
     - Huy hiệu cam: **"VnExpress"** (kèm nguồn *"VnExpress Du Lịch"*, bấm vào mở bài báo gốc trên tab mới).
   - 3 Tab chuyển đổi linh hoạt: *Tất Cả Dòng Tin*, *Bài Viết Cộng Đồng*, *Tin Tức VnExpress Du Lịch*.
   - Phân trang gọn gàng: 6 bài/trang với nút *Trang trước* và *Trang sau*.

4. **Làm Sạch Giao Diện:**
   - Xóa bỏ toàn bộ các ghi chú kỹ thuật thừa về Stored Procedure hoặc CSDL trên form đăng tin.

---

## 2. DANH SÁCH TỆP MÃ NGUỒN CẬP NHẬT

| STT | Đường dẫn tệp | Vai trò / Nội dung thay đổi |
| :---: | :--- | :--- |
| 1 | [`database/changes/06_community_posts.sql`](file:///e:/Dev/Study'/Web/OnlineTraveller/database/changes/06_community_posts.sql) | Tạo Stored Procedure `pct_sp_community_posts_create` cho 3 vai trò |
| 2 | [`database/procedures/04_community_content.sql`](file:///e:/Dev/Study'/Web/OnlineTraveller/database/procedures/04_community_content.sql) | Đồng bộ định nghĩa Stored Procedure trong file thủ tục chung |
| 3 | [`app/models/Post.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/models/Post.php) | Thêm `createCommunityPost()`, `handleThumbnailUpload()` và lấy thêm `author_role` |
| 4 | [`app/controllers/PostController.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/controllers/PostController.php) | Chuẩn hóa Constructor tương thích Router, cập nhật `index()` (gộp tin + phân trang + tab), `create()`, `store()` cho 3 vai trò |
| 5 | [`app/views/posts/create.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/posts/create.php) | Form chia sẻ bài viết mới thân thiện, upload file ảnh và preview tức thì |
| 6 | [`app/views/posts/index.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/posts/index.php) | Giao diện bản tin gộp VnExpress + Tabs lọc + Phân trang 6 tin/trang |
| 7 | [`app/views/posts/show.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/views/posts/show.php) | Hiển thị chi tiết bài viết kèm huy hiệu vai trò tác giả |
| 8 | [`routes/web.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/routes/web.php) | Đăng ký route `GET /posts/create` và `POST /posts` |
| 9 | [`tests/test_phase3_community_posts.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/tests/test_phase3_community_posts.php) | Kịch bản kiểm thử tự động 24 bài test cho Giai đoạn 3 |
| 10 | [`tests/run_all_tests.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/tests/run_all_tests.php) | Tích hợp Phase 3 vào Master Runner (18 suite, 310 assertions PASS) |

---

## 3. HƯỚNG DẪN KIỂM THỬ THỦ CÔNG TRÊN TRÌNH DUYỆT

### Kịch bản 1: Du khách đăng bài viết mới
1. Đăng nhập với tài khoản Tourist: `tourist@onlinetraveller.local` / `Tourist@123`.
2. Truy cập vào menu **Bản tin** (`/posts`).
3. Bấm vào nút nổi bật **"Chia Sẻ Câu Chuyện / Viết Bài Mới"**.
4. Điền tiêu đề: *"Trải nghiệm săn mây Cầu Đất 4h sáng"* và nội dung chuyến đi.
5. Tại ô ảnh bìa, bấm **"Chọn tệp"** để tải 1 bức ảnh từ máy tính (hoặc dán URL ảnh).
6. Quan sát thấy ảnh xem trước (thumbnail preview) xuất hiện ngay bên dưới.
7. Bấm **"Xuất Bản Bài Viết Ngay"**.
8. Xác nhận hệ thống chuyển đến trang xem chi tiết bài viết (`/posts/{slug}`), hiển thị họ tên tác giả và huy hiệu xanh **"Du Khách"**.

### Kịch bản 2: Khám phá Bản tin tổng hợp & Tin tức VnExpress
1. Truy cập trang **Bản tin** (`/posts`).
2. Quan sát các Card bài viết:
   - Bài viết cộng đồng: Có huy hiệu xanh *"Bản Tin Cộng Đồng"*, hiển thị tên tác giả và vai trò (*Du Khách* hoặc *Host Bản Địa*).
   - Tin tức báo chí: Có huy hiệu vàng *"VnExpress"*, nhãn *"Nguồn: VnExpress Du Lịch"*. Bấm vào nút *"Đọc Tin VnExpress"* sẽ mở bài báo gốc trên tab mới.
3. Bấm thử các tab:
   - Bấm tab **"Bài Viết Cộng Đồng"**: Chỉ hiển thị các bài do người dùng đăng.
   - Bấm tab **"Tin Tức VnExpress Du Lịch"**: Chỉ hiển thị các tin thời sự từ báo chí.
   - Bấm tab **"Tất Cả Dòng Tin"**: Hiển thị đan xen cả hai nguồn tin.
4. Bấm thử nút phân trang **"Trang sau"** / **"Trang trước"** ở cuối trang.

### Kịch bản 3: Admin quản trị và kiểm duyệt bài viết
1. Đăng nhập với tài khoản Admin: `admin@onlinetraveller.local` / `Admin@123`.
2. Truy cập `/admin/posts`.
3. Kiểm tra danh sách bài viết: Admin thấy đầy đủ bài do Tourist và Host viết.
4. Admin có thể bấm nút **Sửa** (đổi sang bản nháp) hoặc **Xóa** bất kỳ bài viết nào vi phạm quy chuẩn.

---

## 4. KẾT QUẢ KIỂM THỬ TỰ ĐỘNG

Chạy lệnh:
```powershell
C:\xampp\php\php.exe tests/test_phase3_community_posts.php
```
Kết quả:
```text
KẾT QUẢ KIỂM THỬ GIAI ĐOẠN 3: 24 / 24 TESTS PASSED (100%)
```

Chạy toàn bộ hệ thống:
```powershell
C:\xampp\php\php.exe tests/run_all_tests.php
```
Kết quả:
```text
Tổng số Suite kiểm thử:   18 (Thành công: 18, Thất bại: 0)
Tổng số Assertions test:  310 / 310 PASSED (Tỷ lệ: 100.0%)
```

---

## 5. LỆNH GIT COMMIT DÀNH CHO BRO TỰ THỰC HIỆN

Bro vui lòng kiểm tra thực tế trên web và chạy lệnh sau để commit:

```bash
git add .
git commit -m "feat(phase-3): implement community blog for 3 roles and VnExpress travel news feed integration"
```
