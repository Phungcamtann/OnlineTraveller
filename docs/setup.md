# Môi trường và triển khai

## Bộ khung hiện tại

PHP/SQL hiện là scaffold. public/index.php nạp cấu hình rồi trả HTTP 503; scripts CLI báo chưa triển khai và thoát mã 1. Chưa có router, session, middleware, database hay giao diện hoạt động.

Chuẩn bị PHP với PDO MySQL, cURL, SimpleXML/libxml, Fileinfo và session; MySQL/InnoDB/utf8mb4; Apache hỗ trợ mod_rewrite hoặc Nginx cấu hình tương đương. Phiên bản cụ thể cần chốt trước khi viết nghiệp vụ.

## Cấu hình

Từ thư mục gốc, sao chép config/local.example.php thành config/local.php rồi điền giá trị riêng. Bootstrap nạp app/database/payment/integrations, sau đó merge mảng local theo tên nhóm. File local.php không commit; ưu tiên biến môi trường, chỉ dùng local để override khi cần.

| Nhóm | Biến môi trường |
| --- | --- |
| App | APP_ENV, APP_URL |
| Database | DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS |
| MoMo sandbox | MOMO_ENDPOINT, MOMO_PARTNER_CODE, MOMO_ACCESS_KEY, MOMO_SECRET_KEY, MOMO_REDIRECT_URL, MOMO_IPN_URL |
| Weather | OPENWEATHER_API_KEY |

Timezone mặc định Asia/Ho_Chi_Minh. RSS dùng nguồn cố định trong config/integrations.php. Không đưa khóa API vào JavaScript hoặc file public.

## Web root

DocumentRoot phải là public/, không phải thư mục gốc repository. public/.htaccess chuyển URL không phải file/thư mục thật về index.php. public/uploads/.htaccess chỉ cho phép định dạng ảnh raster đã liệt kê và chặn tên có phần mở rộng script.

Nếu dùng Nginx, cấu hình tương đương để chỉ chạy public/index.php, route URL ứng dụng về front controller và tuyệt đối không chạy script trong uploads. .htaccess không áp dụng cho Nginx hoặc PHP built-in server.

Chạy xem trạng thái bộ khung bằng PHP development server, từ thư mục gốc:

```powershell
php -S localhost:8000 -t public public/index.php
```

Lệnh trên đưa mọi request qua index.php; hiện chỉ trả 503. Khi triển khai assets với development server, bổ sung router phát static có kiểm tra đường dẫn trong public hoặc dùng Apache/Nginx.

Để lint PHP:

```powershell
Get-ChildItem app,core,bootstrap,config,routes,scripts,public -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Nếu hosting chỉ có public_html, đặt nội dung public tại đó và mã ứng dụng ngoài web root; điều chỉnh đường dẫn bootstrap trong index.php theo bố cục thực tế.

## Database

Sau khi hoàn thiện SQL, tạo database trống riêng và chạy bằng MySQL CLI từ root:

```powershell
mysql -u your_user -p online_traveller
```

Trong MySQL CLI chạy `SOURCE database/install.sql;`. Manifest dùng SOURCE, không dùng trực tiếp với PDO/phpMyAdmin; với phpMyAdmin import từng file theo thứ tự manifest.

Hiện các SQL chỉ là TODO, chạy manifest không tạo database ứng dụng. Không dùng manifest để nâng cấp shared database có dữ liệu; dùng database/changes theo thứ tự đã review và sao lưu.

## Storage và job

Cấp quyền ghi tối thiểu cho storage/cache, logs, documents, tmp và các thư mục ảnh uploads. Không công khai storage hoặc config. Chứng từ phải tải qua DocumentController có kiểm tra quyền.

Sau khi nghiệp vụ được triển khai, scheduler chạy scripts/expire_bookings.php và scripts/reconcile_payments.php bằng CLI. Job phải an toàn khi retry/chạy chồng nhờ transaction và chuyển trạng thái có điều kiện; hiện chưa được lên lịch hoặc thực thi nghiệp vụ.
