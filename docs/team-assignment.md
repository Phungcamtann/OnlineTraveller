# Phân công và phối hợp

Đây là phân công theo TV1–TV4 trong cấu trúc đề xuất. Nhóm tự gán tên thành viên vào mã TV; chưa suy ra ai sở hữu nghiệp vụ nào.

| Nhóm | Phạm vi chính | Procedure |
| --- | --- | --- |
| TV1 | User/Auth, Category, middleware và tài khoản Admin | 01_users_categories.sql |
| TV2 | Tour/images, lịch/sức chứa, lưu trú, BookingService và màn hình Host | 02_services_bookings.sql |
| TV3 | Promotion, PricingService, PaymentService, MoMo, DocumentService/Receipt và jobs đối soát | 03_promotions_payments.sql |
| TV4 | Discussion, Review, Post, OpenWeather/RSS và partial community | 04_community_content.sql |

Core, bootstrap, routing, bảo mật public/uploads và tài liệu là phần phối hợp chung. TV2 và TV3 cùng review transaction booking/thanh toán trước khi tích hợp; PricingService thuộc TV3 nhưng chạy trong transaction tạo booking do TV2 sở hữu.

## Điều kiện bàn giao

- Chốt tên procedure/tham số/result/lỗi và schema trước khi controller gọi.
- Model chỉ truy cập dữ liệu; không đẩy tính giá/giữ chỗ/thanh toán vào controller.
- Không tự commit trong model/procedure; dùng chung PDO theo hợp đồng transaction.
- Review quyền sở hữu bên cạnh vai trò; AJAX mutation vẫn kiểm tra CSRF.
- Cập nhật docs/analysis-design, docs/diagrams và database/changes cùng thay đổi.
- Kiểm thử race giữa đặt tour, Host duyệt, IPN và expiry; kiểm tra retry không tạo booking/payment/chứng từ trùng.
- Không commit config/local.php, log, chứng từ hoặc ảnh upload thật.

Dùng feature branches và PR về nhánh tích hợp theo quy trình nhóm; không chạy lại schema/seed trên database dùng chung.
