# Online Traveler

Nền tảng kết nối khách du lịch với dân bản địa.
Online Traveler là đồ án môn Web, giúp khách du lịch tìm hiểu trải nghiệm địa phương, gửi yêu cầu đặt lịch với Local Host và đánh giá sau khi trải nghiệm hoàn thành.
Dự án được định hướng xây dựng bằng PHP OOP + MVC, MySQL và Bootstrap 5, kết hợp AJAX JSON, OpenWeather JSON và RSS du lịch XML.
Trạng thái: Giai đoạn lập kế hoạch, chuẩn bị triển khai. Các chức năng, cấu trúc và lộ trình dưới đây là phạm vi dự kiến, chưa phải xác nhận đã hoàn thành.

- Quy mô nhóm: 4 thành viên.
  + Phùng Cẩm Tân ( Leader)
  + Nguyễn Duy Thành Tài
  + Dương Hoàng Sâm
  + Phạm Thiên Long
- Mục tiêu: Hoàn thiện luồng đặt lịch, kiểm soát quyền truy cập và minh chứng các kỹ thuật của môn học.

Giới thiệu
Local Host giới thiệu các trải nghiệm văn hóa, ẩm thực và tham quan tại địa phương. Tourist tìm kiếm dịch vụ phù hợp, gửi yêu cầu đặt lịch và theo dõi phản hồi của Host.
Website bổ sung thời tiết hiện tại và tin du lịch từ nguồn ngoài để người dùng tham khảo. “Kết nối từ xa” trong phạm vi dự án là tìm kiếm và đặt lịch trực tuyến.
Công nghệ dự kiến

Thành phần	Công nghệ
Giao diện	HTML, CSS, Bootstrap 5
Tương tác phía trình duyệt	JavaScript, Fetch API
Backend	PHP hướng đối tượng, kiến trúc MVC
Truy cập dữ liệu	PDO, Prepared Statements
Database	MySQL, InnoDB, utf8mb4
Tích hợp bên ngoài	OpenWeather Current Weather API, RSS du lịch
Triển khai	Shared hosting và shared database
Làm việc nhóm	GitHub, Git branch, Microsoft Teams


Phiên bản PHP/MySQL cụ thể sẽ được thống nhất sau khi kiểm tra môi trường hosting.
Chức năng trong phạm vi chính
Khách truy cập và Tourist
- Xem danh sách và chi tiết dịch vụ đã được Admin duyệt.
- Tìm kiếm, lọc theo từ khóa, danh mục, khu vực và giá; phân trang bằng AJAX.
- Đăng ký Tourist, đăng nhập và đăng xuất bằng email/mật khẩu.
- Gửi yêu cầu đặt lịch, xem lịch sử và trạng thái booking của mình.
- Đánh giá dịch vụ sau khi booking hoàn thành.
- Đọc bài viết, tin du lịch RSS và thời tiết tham khảo.
Local Host
- Tạo và quản lý dịch vụ thuộc sở hữu của mình.
- Mỗi dịch vụ có một ảnh đại diện trong MVP.
- Sửa, gửi duyệt lại, ẩn hoặc xóa dịch vụ theo điều kiện.
- Xem yêu cầu đặt lịch thuộc dịch vụ của mình.
- Xác nhận, hủy và đánh dấu booking hoàn thành.
Admin
- Thêm, xem, sửa và xóa danh mục.
- Thêm, xem, sửa và xóa bài viết.
- Duyệt, từ chối hoặc ẩn dịch vụ.
Mỗi tài khoản có một vai trò. Tourist tự đăng ký; tài khoản Host và Admin được khởi tạo sẵn cho đồ án. Mã vai trò trong database là tourist, local và admin.
Luồng nghiệp vụ
1. Host tạo dịch vụ và gửi duyệt.
2. Admin duyệt để dịch vụ xuất hiện công khai.
3. Tourist tìm kiếm, xem chi tiết và gửi yêu cầu đặt lịch.
4. Host xác nhận hoặc hủy yêu cầu.
5. Sau khi trải nghiệm kết thúc, Host đánh dấu hoàn thành.
6. Tourist đánh giá booking đã hoàn thành của mình.
Trạng thái booking
Trạng thái hiện tại	Thao tác của Host sở hữu	Trạng thái tiếp theo
pending	Xác nhận	confirmed
pending	Hủy	cancelled
confirmed	Hủy	cancelled
confirmed	Xác nhận trải nghiệm đã kết thúc, sau thời điểm hẹn	completed


completed và cancelled là trạng thái kết thúc.
Quy tắc chính
- Chỉ dịch vụ approved được tạo booking mới. Dịch vụ đã duyệt khi sửa phải được duyệt lại.
- Một booking là một lượt yêu cầu với mức giá trọn lượt; chưa quản lý số lượng khách.
- Server lấy giá từ database và lưu tại thời điểm tạo booking. Đổi giá dịch vụ không làm đổi giá các booking cũ.
- Thời điểm đặt phải nằm trong tương lai; hệ thống dùng múi giờ Asia/Ho_Chi_Minh.
- Tourist chỉ xem booking của mình; Host chỉ xử lý dịch vụ và booking thuộc mình.
- Chỉ Tourist sở hữu booking completed được đánh giá, tối đa một lần cho mỗi booking.
- Dịch vụ đã có booking được chuyển sang hidden khi ngừng nhận khách; lịch sử được giữ lại.
- Trong MVP, Tourist chưa tự hủy hoặc đổi lịch; việc hủy do Host xử lý.
AJAX và tích hợp bên ngoài
Tính năng	Luồng xử lý dự kiến	Dữ liệu
Tìm/lọc và phân trang dịch vụ	Browser gọi endpoint nội bộ GET /api/services/search	JSON
Thời tiết hiện tại	PHP gọi OpenWeather theo tọa độ dịch vụ	JSON
Tin du lịch	PHP tải và phân tích RSS từ nguồn cố định	XML


OpenWeather và RSS đều thuộc phạm vi cam kết, được thực hiện sau khi luồng nghiệp vụ cơ bản hoạt động.
OpenWeather
Hiển thị nhiệt độ, mô tả thời tiết, thời điểm dữ liệu và nguồn tại trang chi tiết dịch vụ có tọa độ hợp lệ.
Widget ghi rõ “Thời tiết hiện tại — thông tin tham khảo”. Dữ liệu này không dùng để dự báo cho ngày booking trong tương lai hoặc quyết định việc xác nhận booking.
API key được giữ ở phía server. Phần tích hợp có timeout, cache và thông báo khi không lấy được dữ liệu.
RSS du lịch
Nguồn dự kiến là RSS Du lịch của VnExpress. Trang bài viết hiển thị tối đa 5 tin với tiêu đề, ngày đăng nếu có, nguồn và liên kết bài gốc.
Khối RSS được phân biệt với bài viết do Admin tạo. Không lưu toàn bộ bài báo vào database hoặc hiển thị nguyên HTML từ feed.
Lỗi của hai nguồn ngoài không làm mất chức năng đặt lịch; hệ thống sử dụng cache phù hợp hoặc hiển thị thông báo tạm chưa có dữ liệu.
Kiến trúc và cấu trúc dự kiến
Mọi request đi qua public/index.php. Router điều hướng tới Controller; Controller kiểm tra dữ liệu và quyền, gọi Model hoặc lớp tích hợp, sau đó trả HTML hoặc JSON.
Đường dẫn	Trách nhiệm
app/controllers/	Xử lý tài khoản, dịch vụ, booking, review, bài viết, Host và Admin
app/models/	User, Category, Service, Booking, Review, Post
app/views/	Giao diện Bootstrap và layout dùng chung
app/integrations/	WeatherClient và RssClient
core/	Router, Autoloader, Database, BaseController và BaseModel
config/	Cấu hình ứng dụng, database và cấu hình riêng theo môi trường
database/	schema.sql, seed_data.sql và changes/
public/	Front Controller, tài nguyên giao diện và ảnh upload
storage/	Cache và log ngoài web root
docs/	Tài liệu thiết kế, demo và minh chứng


Controller được tổ chức theo nghiệp vụ, bao gồm ReviewController và PostController. Search JSON thuộc ServiceController; không gom tất cả request AJAX vào một Controller chung.
Database chính gồm 6 bảng: users, categories, services, bookings, reviews, posts.
Yêu cầu bảo mật khi triển khai
- Hash mật khẩu bằng password_hash, xác thực bằng password_verify.
- Kiểm tra session, vai trò, trạng thái tài khoản và quyền sở hữu tại server.
- Dùng CSRF token cho request thay đổi dữ liệu.
- Truy vấn bằng PDO Prepared Statements; giới hạn trường được phép cập nhật.
- Escape nội dung khi hiển thị để phòng XSS.
- Upload ảnh kiểm tra MIME/nội dung, dung lượng và tên file; chặn thực thi script trong thư mục upload.
- RSS dùng nguồn cố định và cấu hình parser XML an toàn.
- Không commit mật khẩu database, API key, cấu hình riêng hoặc log nhạy cảm.
Môi trường và cấu hình
Phần này mô tả yêu cầu dự kiến. Hướng dẫn cài đặt và lệnh chạy cụ thể sẽ được bổ sung sau khi bộ khung ứng dụng được triển khai và kiểm tra.
Môi trường cần chuẩn bị
- PHP với PDO MySQL, cURL, SimpleXML/libxml, Fileinfo và session.
- MySQL database.
- Web server trỏ document root tới public/ và hỗ trợ điều hướng về index.php.
- Kết nối HTTPS tới OpenWeather và nguồn RSS.
- API key phù hợp cho OpenWeather Current Weather.
Cấu hình ứng dụng
Các thông số dự kiến: APP_ENV, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, OPENWEATHER_API_KEY.
Ưu tiên biến môi trường; nếu cần, sử dụng config/local.php không commit và cung cấp config/local.example.php làm mẫu. APP_ENV được khai báo rõ, không suy ra từ địa chỉ IP người truy cập.
Database dùng chung cần được kiểm tra quyền kết nối ngay từ đầu. Các thay đổi schema được quản lý bằng script có thứ tự trong database/changes/; không chạy lại schema hoặc seed tùy tiện trên dữ liệu chung.
Nếu hosting cố định web root là public_html, đặt nội dung của public/ tại đó và giữ mã ứng dụng, cấu hình, database script, cache và log bên ngoài web root.
Quy trình làm việc nhóm
Nhánh	Mục đích
main	Phiên bản ổn định để nộp và demo
dev	Tích hợp các chức năng
feature/*	Phát triển từng công việc


- Tạo pull request về dev và kiểm tra trước khi merge.
- Đồng bộ nhánh thường xuyên; thay đổi database đi cùng script và ghi chú.
- Dùng tiền tố commit như feat:, fix:, docs: và refactor:.
- Trao đổi và theo dõi tiến độ trên Teams.
- Lưu ảnh minh chứng GitHub, shared database và quá trình làm việc; che thông tin nhạy cảm trước khi đưa vào báo cáo.
Lộ trình dự kiến
Giai đoạn	Mục tiêu
Tuần 1	Chốt thiết kế; dựng MVC, Bootstrap, đăng nhập; kiểm tra host/shared database và hai nguồn ngoài
Tuần 2	Quản lý và duyệt dịch vụ; search AJAX; tạo và xem booking
Tuần 3	Hoàn thiện booking/review, Category/Post CRUD, OpenWeather và RSS; deploy bản tích hợp
Tuần 4	Kiểm tra, sửa lỗi, hoàn thiện báo cáo và demo


Tính năng Bonus
Chỉ xem xét khi phạm vi chính đã ổn định:
- Bản đồ vị trí dạng nhúng.
- Xuất voucher booking XML.
- Gallery nhiều ảnh.
- Thống kê cơ bản cho Host.
- Admin khóa/mở tài khoản hoặc xem booking toàn hệ thống.
- Gửi form booking/review bằng AJAX.
Voucher XML là dữ liệu do website tự sinh, không được tính là nguồn Webservice bên ngoài.
Giới hạn của MVP
Booking là yêu cầu chờ Host xét duyệt; hệ thống chưa tự giữ chỗ hoặc bảo đảm lịch trống. Phạm vi hiện tại chưa bao gồm thanh toán, hoàn tiền, chat/video, đăng nhập Google, quản lý sức chứa hoặc quy trình nâng cấp tài khoản Host.
Tài liệu tham khảo
- OpenWeather — Current Weather API
- VnExpress — RSS
- PHP — PDO
- Bootstrap 5
