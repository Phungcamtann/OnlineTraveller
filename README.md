# 🌍 OnlineTraveller

OnlineTraveller là đồ án Web kết nối khách du lịch với Host địa phương, đặt tour trọn gói và thảo luận ngay trong trang chi tiết tour.

**Trạng thái:** bộ khung thư mục đã được chuẩn hóa. Controller, model, service, middleware, integration, giao diện và SQL hiện là scaffold/TODO; chưa có luồng booking, giữ chỗ hoặc thanh toán hoạt động. Front controller trả HTTP 503 cho đến khi triển khai router và middleware.

Nhóm thực hiện: Phùng Cẩm Tân (leader), Phạm Thiên Long, Nguyễn Duy Thành Tài, Dương Hoàng Sâm.

## Kiến trúc

PHP OOP + MVC, PDO MySQL, InnoDB/utf8mb4; HTML/CSS/Bootstrap, JavaScript Fetch; tích hợp MoMo sandbox, OpenWeather JSON và RSS XML.

| Đường dẫn | Trách nhiệm |
| --- | --- |
| `app/controllers/` | Nhận request, kiểm tra đầu vào/quyền, gọi model hoặc service và trả response |
| `app/models/Service.php` | Dữ liệu **tour**, không phải lớp nghiệp vụ |
| `app/models/` | Truy cập dữ liệu bằng stored procedure |
| `app/services/` | Nghiệp vụ phối hợp nhiều model: booking, tính giá, thanh toán, chứng từ |
| `app/integrations/` | MoMo, OpenWeather và RSS; khóa bí mật chỉ dùng ở server |
| `app/middleware/` | Session, tài khoản hoạt động, vai trò và CSRF |
| `app/helpers/` | Escape, định dạng VND và kiểm tra upload |
| `app/views/` | Layout, trang Tourist/Host/Admin và các partial |
| `core/` | Autoloader, Router, PDO, BaseController/BaseModel, Request/Response |
| `bootstrap/app.php` | Nạp cấu hình; vị trí triển khai session, autoloader và router |
| `config/` | Cấu hình; `local.php` là file riêng, không commit |
| `routes/` | Manifest trang/form, AJAX và callback cùng qua front controller |
| `database/` | Schema, seed, procedure và thay đổi schema có thứ tự |
| `scripts/` | Job CLI hết hạn booking và đối soát thanh toán |
| `public/` | **Web root duy nhất**: index, assets và ảnh upload |
| `storage/` | Cache, log, chứng từ và file tạm ngoài web root |
| `docs/` | Cây thư mục, kiến trúc, hợp đồng procedure, cài đặt và phân công |

Cây thư mục đầy đủ: [docs/project-structure.md](docs/project-structure.md).

## Quy ước đã chốt

- Hai chế độ tour dùng chung `Booking` và `BookingService`: `approval_only` xác nhận khi Host duyệt; `payment_required` chờ thanh toán sau khi duyệt. Tên mã là quy ước để nhóm triển khai đồng bộ.
- Khách chọn lịch khởi hành và số khách, đặt tour trọn gói. Lưu trú thuộc gói tour; Host quản lý riêng qua `AccommodationController`.
- Community nằm trong `app/views/services/partials/discussion.php`, được include từ `show.php`. Đăng và trả lời qua `DiscussionController`.
- Trang, AJAX và IPN đi qua `public/index.php`. Form và AJAX thay đổi dữ liệu dùng session/CSRF; IPN dùng chữ ký MoMo.
- Model gọi procedure trên cùng kết nối PDO. Service cấp ngoài cùng sở hữu transaction; model/procedure không tự commit.
- Giữ/giải phóng chỗ và ghi nhận thanh toán phải dùng khóa, chuyển trạng thái có điều kiện và khóa duy nhất để chống xử lý trùng.
- Lưu giá server tại thời điểm đặt; dùng số nguyên VND. Giá tour thay đổi không sửa giá booking cũ.
- Vai trò giữ mã `tourist`, `local` (Host), `admin`. Host chỉ sửa tour/booking thuộc mình; Tourist chỉ truy cập booking/chứng từ của mình.
- Review chỉ dành cho booking đã hoàn thành, tối đa một review mỗi booking.
- Chỉ ảnh raster đã kiểm tra được đưa vào `public/uploads/`; chứng từ tải qua controller kiểm tra quyền.

Luồng trạng thái, chính sách route và transaction được mô tả tại [docs/architecture.md](docs/architecture.md) và [hợp đồng procedure](docs/database/procedure-contracts.md).

## Bắt đầu

Đọc [docs/setup.md](docs/setup.md). Cấu hình bằng biến môi trường hoặc sao chép `config/local.example.php` thành `config/local.php`; file thực tế không được tạo sẵn để tránh commit bí mật.

`database/install.sql` chỉ là manifest MySQL CLI: các file SQL chưa tạo bảng/procedure hoặc dữ liệu. Cần hoàn thiện hợp đồng database trước khi triển khai nghiệp vụ.

## Tài liệu nhóm

- [Cấu trúc thư mục](docs/project-structure.md)
- [Kiến trúc và luồng booking](docs/architecture.md)
- [Hợp đồng stored procedure và transaction](docs/database/procedure-contracts.md)
- [Môi trường và triển khai](docs/setup.md)
- [Thanh toán sandbox](docs/payment-sandbox.md)
- [Phân công nhóm](docs/team-assignment.md)

Tài liệu phân tích mới đặt trong `docs/analysis-design/`; ERD và sơ đồ booking trong `docs/diagrams/`. Mọi thay đổi schema đi kèm script có thứ tự trong `database/changes/`. Các chức năng cũ được mô tả là chưa quản lý sức chứa/chưa thanh toán được thay bằng phạm vi mới ở trên; đây vẫn là kế hoạch triển khai, không phải xác nhận chức năng đã hoàn thành.
