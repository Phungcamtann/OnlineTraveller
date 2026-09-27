# 🌍 Online Traveler

### Kết nối khách du lịch với dân bản địa

**Online Traveler** là đồ án môn Web, hỗ trợ khách du lịch tìm kiếm trải nghiệm địa phương, gửi yêu cầu đặt lịch với **Local Host** và đánh giá sau khi trải nghiệm hoàn thành.

> 🚧 **Trạng thái:** Dự án đang ở giai đoạn lập kế hoạch, chuẩn bị triển khai. Các chức năng dưới đây là phạm vi dự kiến, chưa phải xác nhận đã hoàn thành.

| Thông tin | Nội dung |
| --- | --- |
| **Loại dự án** | Đồ án môn Web |
| **Thời gian dự kiến** | 1 tháng |
| **Kiến trúc** | PHP hướng đối tượng kết hợp MVC |
| **Tích hợp chính** | AJAX JSON, OpenWeather JSON, RSS du lịch XML |
| **Mục tiêu** | Hoàn thiện luồng đặt lịch, kiểm soát quyền và đáp ứng yêu cầu kỹ thuật của môn học |

Nhóm thực hiện:
- Phùng Cẩm Tân (leader)
- Phạm Thiên Long
- Nguyễn Duy Thành Tài

---

## 🎯 1. Mục tiêu dự án

| Mục tiêu | Cách thực hiện |
| --- | --- |
| **Kết nối Tourist và Host** | Giới thiệu dịch vụ, tìm kiếm và gửi yêu cầu đặt lịch trực tuyến |
| **Quản lý trải nghiệm** | Theo dõi booking từ khi gửi yêu cầu đến khi hoàn thành |
| **Cung cấp thông tin tham khảo** | Hiển thị thời tiết hiện tại và tin du lịch |
| **Thể hiện kỹ thuật backend** | Áp dụng OOP, MVC, PDO và kiểm soát quyền truy cập |
| **Cải thiện tương tác** | Tìm kiếm, lọc và phân trang bằng AJAX |
| **Giữ phạm vi vừa sức** | Ưu tiên luồng chính; chỉ làm Bonus khi phần cam kết ổn định |

**Phạm vi “kết nối từ xa”:** tìm hiểu dịch vụ và đặt lịch trực tuyến với Host.

---

## 🛠️ 2. Công nghệ dự kiến

| Thành phần | Công nghệ |
| --- | --- |
| **Giao diện** | HTML, CSS, Bootstrap 5 |
| **Tương tác trình duyệt** | JavaScript, Fetch API |
| **Backend** | PHP OOP + MVC |
| **Truy cập database** | PDO, Prepared Statements |
| **Database** | MySQL, InnoDB, utf8mb4 |
| **Thời tiết** | OpenWeather Current Weather API — JSON |
| **Tin du lịch** | RSS — XML |
| **Triển khai** | Shared hosting và shared database |
| **Quản lý mã nguồn** | Git, GitHub, branch và pull request |
| **Trao đổi, theo dõi tiến độ** | Microsoft Teams |

> Phiên bản PHP/MySQL cụ thể sẽ được thống nhất sau khi kiểm tra môi trường hosting.

---

## 👥 3. Đối tượng sử dụng

| Đối tượng | Quyền sử dụng chính |
| --- | --- |
| **Khách chưa đăng nhập** | Xem, tìm kiếm dịch vụ; đọc review, bài viết và thông tin tham khảo |
| **Tourist** | Đặt lịch, theo dõi booking cá nhân và đánh giá trải nghiệm đã hoàn thành |
| **Local Host** | Quản lý dịch vụ và xử lý booking thuộc dịch vụ của mình |
| **Admin** | Quản lý danh mục, bài viết và kiểm duyệt dịch vụ |

### Quy ước tài khoản

| Nội dung | Quy ước |
| --- | --- |
| **Đăng ký công khai** | Chỉ tạo tài khoản Tourist |
| **Host và Admin** | Khởi tạo sẵn phục vụ đồ án |
| **Vai trò mỗi tài khoản** | Một vai trò |
| **Mã vai trò trong database** | `tourist`, `local`, `admin` |
| **Phương thức đăng nhập** | Email và mật khẩu |

---

## 📦 4. Phạm vi chức năng chính

| Nhóm chức năng | Nội dung cam kết |
| --- | --- |
| **Tài khoản** | Đăng ký Tourist, đăng nhập và đăng xuất |
| **Danh sách dịch vụ** | Xem dịch vụ đã duyệt; tìm/lọc theo từ khóa, danh mục, khu vực và giá |
| **Chi tiết dịch vụ** | Ảnh đại diện, mô tả, giá, thời lượng, thông tin Host và review |
| **Quản lý dịch vụ** | Host tạo, sửa, gửi duyệt lại, ẩn hoặc xóa theo điều kiện |
| **Kiểm duyệt** | Admin duyệt, từ chối hoặc ẩn dịch vụ |
| **Booking** | Tourist gửi yêu cầu và xem lịch sử; Host xác nhận, hủy hoặc hoàn thành |
| **Review** | Tourist đánh giá booking của mình sau khi hoàn thành |
| **Danh mục** | Admin thêm, xem, sửa và xóa Category |
| **Bài viết** | Admin quản lý Post; người dùng xem danh sách và chi tiết |
| **Thông tin tham khảo** | Thời tiết hiện tại và tin du lịch từ nguồn ngoài |

**Giới hạn giao diện MVP:**

- Mỗi dịch vụ có **một ảnh đại diện**.
- Trang Host và Admin ưu tiên **bảng, form và thông báo rõ ràng**.
- Booking/review sử dụng **form POST thông thường**; AJAX tập trung vào tìm kiếm.

---

## 🔄 5. Luồng nghiệp vụ

1. **Host** tạo dịch vụ và gửi duyệt.
2. **Admin** duyệt để dịch vụ xuất hiện công khai.
3. **Tourist** tìm kiếm, xem chi tiết và gửi yêu cầu đặt lịch.
4. **Host** xác nhận hoặc hủy yêu cầu.
5. Sau khi trải nghiệm kết thúc, **Host** đánh dấu hoàn thành.
6. **Tourist** đánh giá booking đã hoàn thành của mình.

### Trạng thái booking

| Trạng thái hiện tại | Thao tác của Host sở hữu | Trạng thái tiếp theo |
| --- | --- | --- |
| `pending` | Xác nhận yêu cầu | `confirmed` |
| `pending` | Hủy yêu cầu | `cancelled` |
| `confirmed` | Hủy booking | `cancelled` |
| `confirmed` | Xác nhận trải nghiệm đã kết thúc, sau thời điểm hẹn | `completed` |

> **`completed` và `cancelled` là trạng thái kết thúc.** Không cho phép chuyển trực tiếp từ `pending` sang `completed`.

### Quy tắc nghiệp vụ quan trọng

| Nội dung | Quy tắc |
| --- | --- |
| **Dịch vụ được đặt** | Chỉ dịch vụ có trạng thái `approved` |
| **Sửa dịch vụ đã duyệt** | Phải được Admin duyệt lại |
| **Ý nghĩa booking** | Một lượt yêu cầu trải nghiệm với giá trọn lượt; chưa quản lý số lượng khách |
| **Giá booking** | Server lấy từ database và lưu tại thời điểm tạo đơn |
| **Thay đổi giá dịch vụ** | Không làm thay đổi giá của booking đã tạo |
| **Thời gian đặt** | Phải nằm trong tương lai; dùng múi giờ `Asia/Ho_Chi_Minh` |
| **Quyền Tourist** | Chỉ xem và đánh giá booking của mình |
| **Quyền Host** | Chỉ quản lý dịch vụ và xử lý booking thuộc mình |
| **Điều kiện review** | Booking đã `completed`; mỗi booking tối đa một review |
| **Ngừng nhận khách** | Dịch vụ đã có booking chuyển sang `hidden`, giữ lịch sử |
| **Hủy hoặc đổi lịch** | Trong MVP, Host xử lý hủy; Tourist tạo yêu cầu mới nếu cần đổi lịch |

---

## 🌐 6. AJAX và tích hợp bên ngoài

| Tính năng | Luồng xử lý | Định dạng |
| --- | --- | --- |
| **Tìm kiếm dịch vụ** | Browser gọi `GET /api/services/search` của website | JSON |
| **Thời tiết hiện tại** | PHP gọi OpenWeather theo tọa độ dịch vụ | JSON |
| **Tin du lịch** | PHP tải và phân tích RSS từ nguồn cố định | XML |

> 📌 **OpenWeather và RSS đều thuộc phạm vi cam kết**, không phải Bonus.

### 🔎 AJAX tìm kiếm

| Nội dung | Thiết kế dự kiến |
| --- | --- |
| **Bộ lọc** | Từ khóa, danh mục, khu vực và khoảng giá |
| **Phân trang** | Cập nhật vùng kết quả mà không tải lại toàn bộ trang |
| **Trạng thái giao diện** | Đang tải, không có kết quả và lỗi kết nối |
| **Phạm vi dữ liệu** | Chỉ trả dịch vụ đã được duyệt |

Đây là **AJAX nội bộ**, được trình bày riêng với việc sử dụng dịch vụ bên ngoài.

### 🌤️ OpenWeather JSON

| Nội dung | Thiết kế dự kiến |
| --- | --- |
| **Vị trí hiển thị** | Trang chi tiết dịch vụ có tọa độ hợp lệ |
| **Thông tin** | Nhiệt độ, mô tả thời tiết, thời điểm dữ liệu và nguồn |
| **Mục đích** | Tham khảo thời tiết hiện tại tại địa phương |
| **API key** | Lưu và sử dụng ở phía server |
| **Xử lý lỗi** | Timeout, cache và thông báo khi chưa lấy được dữ liệu |

**Nhãn hiển thị:** “Thời tiết hiện tại — thông tin tham khảo”.

Dữ liệu này **không phải dự báo cho ngày booking trong tương lai** và không quyết định việc xác nhận booking.

### 📰 RSS du lịch XML

| Nội dung | Thiết kế dự kiến |
| --- | --- |
| **Nguồn** | RSS Du lịch của VnExpress |
| **Vị trí hiển thị** | Khối tin từ nguồn ngoài tại trang bài viết |
| **Số lượng** | Tối đa 5 tin |
| **Thông tin** | Tiêu đề, ngày đăng nếu có, tên nguồn và liên kết bài gốc |
| **Cách sử dụng** | Ghi rõ nguồn; phân biệt với bài viết do Admin tạo |

Không nhập toàn bộ bài báo vào database hoặc hiển thị nguyên HTML từ feed.

**Khi nguồn ngoài gặp lỗi:** dùng cache phù hợp hoặc thông báo tạm chưa có dữ liệu; chức năng đặt lịch tiếp tục hoạt động.

---

## 🏗️ 7. Kiến trúc và cấu trúc dự kiến

Mọi request đi qua **`public/index.php`**. Router điều hướng tới Controller; Controller kiểm tra dữ liệu và quyền, gọi Model hoặc lớp tích hợp rồi trả HTML/JSON.

| Thành phần | Trách nhiệm |
| --- | --- |
| **Router** | Ánh xạ URL và HTTP method tới action |
| **Controller** | Kiểm tra request, quyền truy cập và điều phối xử lý |
| **Model** | Truy vấn và thao tác dữ liệu |
| **View** | Hiển thị giao diện Bootstrap |
| **Integration Client** | Gọi nguồn ngoài, xử lý response, cache và lỗi |
| **Database** | Quản lý kết nối PDO |

### Cấu trúc thư mục

| Đường dẫn | Nội dung |
| --- | --- |
| `app/controllers/` | Controller theo từng nghiệp vụ |
| `app/models/` | User, Category, Service, Booking, Review, Post |
| `app/views/` | Giao diện và layout dùng chung |
| `app/integrations/` | WeatherClient và RssClient |
| `core/` | Router, Autoloader, Database, BaseController, BaseModel |
| `config/` | Cấu hình ứng dụng, database và môi trường |
| `database/` | Schema, seed và các script thay đổi database |
| `public/` | Front Controller, tài nguyên giao diện và ảnh upload |
| `storage/` | Cache và log ngoài web root |
| `docs/` | Tài liệu thiết kế, demo và minh chứng |

Các nghiệp vụ review và bài viết có **ReviewController** và **PostController** riêng. Search JSON thuộc **ServiceController**.

### Các bảng dữ liệu chính

| Bảng | Mục đích |
| --- | --- |
| `users` | Tài khoản và vai trò |
| `categories` | Danh mục dịch vụ |
| `services` | Dịch vụ của Host |
| `bookings` | Yêu cầu đặt lịch và trạng thái |
| `reviews` | Đánh giá gắn với booking |
| `posts` | Bài viết do Admin quản lý |

---

## 🔐 8. Yêu cầu bảo mật

| Nội dung | Biện pháp dự kiến |
| --- | --- |
| **Mật khẩu** | `password_hash()` và `password_verify()` |
| **Phiên đăng nhập** | Kiểm tra session; đổi session ID sau đăng nhập |
| **Phân quyền** | Kiểm tra vai trò, trạng thái tài khoản và quyền sở hữu trên server |
| **CSRF** | Token cho request thay đổi dữ liệu |
| **SQL Injection** | PDO Prepared Statements; giới hạn trường được phép cập nhật |
| **XSS** | Escape dữ liệu khi hiển thị |
| **Upload ảnh** | Kiểm tra MIME/nội dung, dung lượng, tên file và chặn thực thi script |
| **XML/RSS** | Nguồn cố định, parser an toàn, kiểm tra dữ liệu trước khi hiển thị |
| **Thông tin nhạy cảm** | Không commit mật khẩu database, API key, cấu hình riêng hoặc log |

---

## ⚙️ 9. Môi trường và cấu hình

> 🛠️ Hướng dẫn cài đặt và lệnh chạy cụ thể sẽ được bổ sung sau khi bộ khung ứng dụng được triển khai và kiểm tra.

### Môi trường cần chuẩn bị

| Thành phần | Yêu cầu dự kiến |
| --- | --- |
| **PHP** | PDO MySQL, cURL, SimpleXML/libxml, Fileinfo và session |
| **Database** | MySQL |
| **Web server** | Document root trỏ tới `public/`; hỗ trợ điều hướng về `index.php` |
| **Kết nối ngoài** | HTTPS tới OpenWeather và nguồn RSS |
| **OpenWeather** | API key phù hợp cho Current Weather |

### Thông số cấu hình

| Thông số | Mục đích |
| --- | --- |
| `APP_ENV` | Môi trường development hoặc production |
| `DB_HOST`, `DB_PORT` | Địa chỉ và cổng database |
| `DB_NAME` | Tên database |
| `DB_USER`, `DB_PASS` | Thông tin xác thực database |
| `OPENWEATHER_API_KEY` | API key OpenWeather |

### Quy ước triển khai

| Nội dung | Quy ước |
| --- | --- |
| **Cấu hình riêng** | Ưu tiên biến môi trường; có thể dùng `config/local.php` không commit |
| **Cấu hình mẫu** | Cung cấp `config/local.example.php` |
| **Xác định môi trường** | Khai báo `APP_ENV`, không suy ra từ IP người truy cập |
| **Shared database** | Kiểm tra quyền và cách kết nối ngay từ đầu |
| **Thay đổi schema** | Quản lý bằng script có thứ tự trong `database/changes/` |
| **Dữ liệu chung** | Không chạy lại schema/seed tùy tiện |
| **Hosting dùng public_html** | Đặt nội dung `public/` tại đó; giữ mã ứng dụng, cấu hình, SQL, cache và log ngoài web root |

---

## 🤝 10. Quy trình làm việc nhóm

### Quản lý branch

| Nhánh | Mục đích |
| --- | --- |
| `main` | Phiên bản ổn định để nộp và demo |
| `dev` | Tích hợp chức năng |
| `feature/*` | Phát triển từng công việc |

### Quy ước phối hợp

| Nội dung | Cách thực hiện |
| --- | --- |
| **Tích hợp code** | Tạo pull request về `dev`, kiểm tra trước khi merge |
| **Đồng bộ** | Cập nhật nhánh thường xuyên |
| **Commit** | Dùng tiền tố rõ ràng: `feat:`, `fix:`, `docs:`, `refactor:` |
| **Database** | Thay đổi đi cùng script và ghi chú |
| **Tiến độ, trao đổi** | Theo dõi trên Teams |
| **Minh chứng** | Lưu ảnh GitHub, shared database và quá trình làm việc |
| **Thông tin riêng tư** | Che mật khẩu, API key và dữ liệu nhạy cảm trong ảnh báo cáo |

---

## 🗓️ 11. Lộ trình dự kiến

| Giai đoạn | Mục tiêu |
| --- | --- |
| **Tuần 1 — Nền tảng** | Chốt thiết kế; dựng MVC, Bootstrap, đăng nhập; kiểm tra host, shared database và hai nguồn ngoài |
| **Tuần 2 — Luồng chính** | Quản lý/duyệt dịch vụ, search AJAX, tạo và xem booking |
| **Tuần 3 — Tích hợp** | Hoàn thiện booking/review, Category/Post CRUD, OpenWeather và RSS; deploy bản tích hợp |
| **Tuần 4 — Hoàn thiện** | Kiểm tra, sửa lỗi, hoàn thiện báo cáo và chuẩn bị demo |

---

## ✨ 12. Tính năng Bonus

Chỉ xem xét khi phạm vi chính đã ổn định.

| Tính năng | Phạm vi dự kiến |
| --- | --- |
| **Bản đồ** | Nhúng vị trí dịch vụ |
| **Voucher XML** | Xuất phiếu xác nhận booking |
| **Gallery** | Nhiều ảnh cho dịch vụ |
| **Thống kê Host** | Số liệu cơ bản về booking |
| **Quản lý User** | Admin khóa/mở tài khoản |
| **Giám sát booking** | Admin xem danh sách toàn hệ thống |
| **AJAX mở rộng** | Gửi form booking/review |

> **Voucher XML** là dữ liệu do website tự sinh, không phải nguồn Webservice bên ngoài.

---

## 📍 13. Giới hạn của MVP

| Nội dung | Giới hạn |
| --- | --- |
| **Đặt lịch** | Yêu cầu chờ Host duyệt; chưa tự giữ chỗ hoặc bảo đảm lịch trống |
| **Sức chứa** | Chưa quản lý số lượng khách |
| **Thanh toán** | Chưa tích hợp thanh toán hoặc hoàn tiền |
| **Trao đổi trực tiếp** | Chưa có chat/video |
| **Tài khoản** | Chưa có đăng nhập Google hoặc quy trình nâng cấp Host |
| **Thời tiết** | Chỉ tham khảo hiện tại, không dự báo theo ngày booking |

---

## 📚 14. Tài liệu tham khảo

| Tài liệu | Liên kết |
| --- | --- |
| **OpenWeather Current Weather** | [Tài liệu API](https://openweathermap.org/api/current) |
| **VnExpress RSS** | [Danh sách nguồn RSS](https://vnexpress.net/rss) |
| **PHP PDO** | [PHP Manual](https://www.php.net/manual/en/book.pdo.php) |
| **Bootstrap 5** | [Tài liệu Bootstrap](https://getbootstrap.com/docs/5.3/getting-started/introduction/) |
