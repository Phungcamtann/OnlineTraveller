# Kiến trúc và quy ước nghiệp vụ

## Trách nhiệm

Request HTTP → public/index.php → bootstrap → Router → middleware → Controller → Model/Service → Response/View.

Controller xử lý giao tiếp HTTP và kiểm tra quyền sở hữu. Model thực hiện lời gọi stored procedure. Service điều phối nhiều model; chỉ service cấp ngoài cùng quản lý transaction. Integration gọi hệ thống ngoài, không quyết định quyền hay tự ghi nhận booking.

`app/models/Service.php` là model tour. `app/services/` là thư mục lớp nghiệp vụ, không thay thế model này.

## Booking dùng chung

Mã chế độ đề xuất: `approval_only`, `payment_required`; lưu trên tour và snapshot vào booking lúc tạo. Không tạo Booking/BookingService riêng cho từng chế độ.

| Trạng thái | Sự kiện | Kết quả |
| --- | --- | --- |
| Chưa có booking | Tourist đặt lịch còn chỗ | pending_host; giữ số chỗ và snapshot giá/chế độ |
| pending_host | Host duyệt approval_only | confirmed |
| pending_host | Host duyệt payment_required | awaiting_payment; đặt hạn thanh toán |
| pending_host | Host từ chối/hủy | cancelled; giải phóng chỗ đúng một lần |
| awaiting_payment | Kết quả thanh toán hợp lệ, thành công, đúng hạn | confirmed; chỗ đã giữ không bị trừ lại |
| awaiting_payment | Hết hạn/hủy | expired/cancelled; giải phóng chỗ đúng một lần |
| confirmed | Host xác nhận sau khi tour kết thúc | completed |
| confirmed | Hủy theo chính sách cho phép | cancelled; giải phóng chỗ đúng một lần |

completed, cancelled và expired là trạng thái kết thúc. Không chuyển pending_host trực tiếp thành completed. Chỉ approval_only hoặc payment thành công đã xác minh được phép chuyển sang confirmed.

Quy ước giữ chỗ khi tạo pending_host cần có hạn duyệt cấu hình được; job hết hạn xử lý cả pending_host quá hạn và awaiting_payment quá hạn. Chuyển từ pending_host sang awaiting_payment cập nhật hạn thanh toán, không giữ chỗ thêm. Nếu nhóm thay đổi thời điểm giữ chỗ, phải cập nhật đồng thời procedure, service và tài liệu này.

Booking lưu schedule_id, số khách dương, giá gốc/giảm/tổng tiền VND và chế độ tour. Server tính giá; không tin tổng tiền gửi từ browser. Không cho đặt tour chưa approved hoặc lịch đã khởi hành/đóng.

IPN thành công đến sau khi booking đã hủy/hết hạn phải được ghi nhận để đối soát, không tự mở lại booking hay chiếm chỗ. Hoàn tiền và hủy booking đã trả tiền cần chính sách riêng trước khi mở endpoint; chưa thuộc chức năng triển khai hiện tại.

## Tour, lưu trú và community

ServiceSchedule quản lý ngày khởi hành và sức chứa. Accommodation thuộc tour và do Host quản lý riêng; khách không đặt phòng độc lập. Tour trọn gói là đơn vị tính giá/booking.

ServiceController cung cấp dữ liệu cho show.php; trang này include schedule-picker, accommodation, discussion, reviews và weather-map. DiscussionController nhận yêu cầu đăng/trả lời; kiểm tra parent message thuộc đúng tour, giới hạn đầu vào và escape nội dung khi render. Admin có khu vực kiểm duyệt discussions.

## Route và bảo mật

| Manifest | Request dự kiến | Chính sách |
| --- | --- | --- |
| web.php | GET trang, POST form | Trang riêng cần session/vai trò/quyền sở hữu; mọi mutation cần CSRF |
| api.php | GET search, AJAX đăng/trả lời/review/booking | Chỉ search công khai; mutation cần session/vai trò/quyền sở hữu và CSRF |
| callbacks.php | POST MoMo IPN | Không dùng session/CSRF; bắt buộc xác thực chữ ký, order, amount và trạng thái |

Các manifest hiện trả mảng rỗng; chưa đăng ký endpoint nào. Không dùng dispatch động tên controller/action từ query string. GET không thay đổi dữ liệu. Trang kết quả browser chỉ đọc trạng thái trong database; query string thành công không phải bằng chứng thanh toán.

Phiên đăng nhập dùng cookie HttpOnly, SameSite phù hợp, Secure trên HTTPS; đổi session ID sau đăng nhập. Đăng ký công khai chỉ tạo Tourist; Host/Admin được cấp theo quy trình nhóm. AuthMiddleware phải kiểm tra cả trạng thái tài khoản.

DocumentController đọc metadata từ Receipt/Booking, kiểm tra quyền rồi phục vụ file từ storage/documents; không nhận đường dẫn file tùy ý từ client. Log không chứa secret, chữ ký đầy đủ hoặc dữ liệu cá nhân không cần thiết.

## Triển khai theo giai đoạn

1. Chốt schema, hợp đồng procedure, trạng thái và mã chế độ.
2. Triển khai core, bootstrap, route, session và middleware.
3. Tour/lịch/lưu trú, PricingService và BookingService với khóa/transaction.
4. MoMo sandbox, PaymentService, đối soát và tài liệu.
5. Community/review/post, OpenWeather/RSS, giao diện và kiểm thử phân quyền.

Đây là hợp đồng triển khai; các lớp hiện vẫn là scaffold.
