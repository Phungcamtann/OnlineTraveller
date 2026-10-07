# MoMo sandbox

**Trạng thái:** MomoClient, PaymentController, PaymentService và procedure thanh toán chưa triển khai. Không có endpoint IPN hoạt động.

## Cấu hình và route dự kiến

Lưu credential sandbox trong biến môi trường hoặc config/local.php. Endpoint, redirect URL và IPN URL phải cấu hình ở server. IPN cần URL HTTPS truy cập được từ MoMo, đi qua public/index.php và routes/callbacks.php.

Chọn sản phẩm/request type MoMo cụ thể và kiểm tra tài liệu chính thức hiện hành trước khi triển khai. Không dùng chung một chuỗi ký cho mọi loại request: thứ tự trường và cách biểu diễn dữ liệu phải đúng giao thức của từng thao tác.

## Trách nhiệm

- MomoClient gọi API, tạo chữ ký request và xác minh response/IPN bằng so sánh constant-time.
- PaymentController chỉ đọc request/response; IPN không yêu cầu session/CSRF nhưng bắt buộc xác minh chữ ký.
- PaymentService kiểm tra order/partner/amount, trạng thái, idempotency và cập nhật database trong transaction.
- Trang redirect chỉ hiển thị trạng thái database, không đánh dấu đã trả tiền từ query string.
- DocumentService tạo chứng từ ngoài public sau commit; retry không lặp lại hiệu ứng thanh toán.

## Tình huống phải kiểm thử trước khi mở route

| Tình huống | Kết quả mong đợi |
| --- | --- |
| Chữ ký sai, thiếu hoặc payload lỗi | Từ chối; không thay đổi dữ liệu |
| Success hợp lệ, đúng order/amount, chưa quá hạn | Payment success và booking confirmed đúng một lần |
| IPN lặp hoặc job đối soát cùng kết quả | Không trừ thêm chỗ, không ghi receipt trùng |
| Callback lỗi đến sau success | Không ghi đè trạng thái success |
| Order/amount/partner không khớp | Không xác nhận booking |
| Browser giả mạo kết quả success | Chỉ đọc trạng thái server |
| IPN đến sau hủy/hết hạn | Ghi nhận phục vụ đối soát; không tự mở lại booking |
| API timeout/kết quả chưa rõ | Giữ trạng thái unresolved để đối soát |
| IPN và expiry chạy đồng thời | Khóa và chuyển trạng thái quyết định một kết quả nhất quán |

Mã acknowledgement/HTTP status và thuật toán canonical signature phải được chốt theo sản phẩm MoMo đã chọn. Không triển khai xác thực giả hoặc chấp nhận callback chỉ dựa vào resultCode.
