# Hợp đồng stored procedure và transaction

**Trạng thái:** đặc tả dự kiến. Các file SQL chưa định nghĩa bảng/procedure.

## Quyền sở hữu transaction

- Database cung cấp một PDO dùng chung cho các model tham gia một nghiệp vụ.
- BookingService sở hữu transaction tạo booking, Host duyệt, hủy, hết hạn và hoàn thành.
- PaymentService sở hữu transaction ghi nhận payment-result và xác nhận booking. Khi cần quy tắc BookingService, gọi một thao tác nội bộ tham gia transaction đang mở; không gọi hàm tự mở transaction lồng nhau.
- PricingService đọc/tính giá trong ngữ cảnh transaction của BookingService; không commit.
- Model/BaseModel và stored procedure không START TRANSACTION, COMMIT hoặc ROLLBACK. Procedure không thực hiện DDL gây implicit commit.
- Service rollback khi lỗi; retry deadlock có giới hạn và thực hiện lại toàn bộ nghiệp vụ bằng cùng khóa idempotency.
- Không gọi MoMo, tải RSS/thời tiết hay sinh file trong transaction. DocumentService chạy sau commit; file lỗi được retry mà không lặp lại thanh toán.

## Hợp đồng truy cập

Mỗi procedure phải ghi rõ tên, tham số và kiểu, result set/OUT, lỗi nghiệp vụ, quyền sở hữu cần kiểm tra và yêu cầu transaction/khóa. BaseModel dùng tham số bind, đóng cursor và đọc hết result set trước lời gọi kế tiếp trên cùng PDO.

Procedure cập nhật trả số dòng ảnh hưởng hoặc kết quả nghiệp vụ rõ ràng. Không coi 0 dòng là thành công. Trạng thái/vai trò không lấy trực tiếp từ payload client để bỏ qua kiểm tra.

## Giữ chỗ

Khi tạo booking: khóa tour/lịch và dữ liệu giá liên quan, kiểm tra approved/lịch tương lai, số khách và chỗ còn; lưu snapshot giá/chế độ, booking và số chỗ trong cùng transaction.

Dùng SELECT ... FOR UPDATE hoặc UPDATE có điều kiện capacity_remaining >= guest_count và xác nhận số dòng. Chống gửi form lặp bằng khóa duy nhất (tourist_id, booking_request_key). Khóa này do client giữ ổn định khi retry hoặc do server cấp; không dùng schedule_id làm khóa duy nhất vì khách có thể tạo lượt đặt mới hợp lệ.

Hủy/hết hạn chỉ giải phóng khi booking đang giữ chỗ và chuyển trạng thái thành công. Một IPN lặp hoặc job lặp không được cộng/trừ chỗ lần nữa. Khi complete, kết thúc quyền giữ chỗ theo mô hình sức chứa đã chốt; không tăng chỗ cho lịch đã kết thúc.

Chốt một thứ tự khóa chung khi triển khai để tránh deadlock giữa booking, IPN và expiry; mọi đường xử lý phải tuân thủ thứ tự này.

## Thanh toán

1. Tạo Payment attempt có order_id và request_id duy nhất trước khi gọi MoMo.
2. Gọi provider ngoài transaction. Timeout giữ trạng thái chưa rõ để đối soát, không tự coi là thất bại.
3. IPN được xác minh ở MomoClient trước khi cập nhật.
4. PaymentService khóa dữ liệu liên quan; đối chiếu partner/order, amount VND và booking; áp dụng chuyển trạng thái có điều kiện.
5. Payment thành công chỉ được ghi nhận một lần; provider_transaction_id phải có ràng buộc duy nhất khi có giá trị. Success không bị callback thất bại đến sau ghi đè.
6. Cập nhật payment, booking và receipt metadata trong cùng transaction. IPN lặp trả acknowledgement theo giao thức mà không lặp hiệu ứng.
7. IPN đến muộn/không khớp cần kết quả đối soát rõ ràng; không xác nhận booking không còn hợp lệ.

Job reconcile_payments dùng cùng xử lý kết quả đã xác minh như IPN. Kết quả browser không đi qua nhánh xác nhận thanh toán.

## Bảng và ràng buộc tối thiểu

| Bảng | Ràng buộc cần triển khai |
| --- | --- |
| users | Email duy nhất; role tourist/local/admin; trạng thái hoạt động |
| services | Host/category FK; mode approval_only/payment_required; trạng thái kiểm duyệt |
| service_images | Service FK; thứ tự và đường dẫn ảnh đã kiểm tra |
| service_schedules | Service FK; thời gian hợp lệ; sức chứa/số chỗ không âm |
| accommodations | Service FK; thông tin lưu trú thuộc gói |
| bookings | Tourist/schedule FK; số khách > 0; snapshot giá/mode; trạng thái, hạn giữ chỗ; request key duy nhất theo Tourist |
| promotions | Điều kiện, hạn dùng và quy tắc giảm giá; không tạo tổng âm |
| payments | Booking FK; order_id/request_id duy nhất; transaction provider duy nhất; số tiền và trạng thái |
| receipts | Payment FK; duy nhất theo payment và loại chứng từ; đường dẫn ngoài public |
| discussion_messages | Tour/user FK; parent cùng tour; trạng thái kiểm duyệt |
| reviews | Booking FK duy nhất; rating hợp lệ; kiểm tra booking completed và đúng Tourist |
| posts | Tác giả, nội dung và trạng thái xuất bản |

Kiểm tra liên bảng không biểu diễn được bằng constraint đơn phải nằm trong procedure và service khi giữ khóa thích hợp.

## Phân nhóm SQL

- 01_users_categories.sql: User, Category.
- 02_services_bookings.sql: Service, ServiceImage, ServiceSchedule, Accommodation, Booking.
- 03_promotions_payments.sql: Promotion, Payment, Receipt.
- 04_community_content.sql: DiscussionMessage, Review, Post.

Mỗi thay đổi procedure/schema đã dùng chung cần script nâng cấp có thứ tự trong database/changes; không chạy lại schema.sql trên database có dữ liệu.
