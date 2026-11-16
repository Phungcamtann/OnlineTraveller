# BÁO CÁO TỔNG HỢP TOÀN BỘ KỸ THUẬT DỰ ÁN VÀ ĐỐI CHIẾU GIÁO TRÌNH
**Dự án:** Online Traveller (Nền Tảng Du Lịch Bản Địa & Trải Nghiệm 3D Trực Tuyến)  
**Môn học:** Phát triển ứng dụng Web — UEH  
**Thời điểm rà soát:** Tháng 11/2026  

---

## MỤC LỤC
1. [Tổng Quan Kiến Trúc & Toàn Bộ Hạng Mục Đã Triển Khai](#1-tổng-quan-kiến-trúc--toàn-bộ-hạng-mục-đã-triển-khai)
2. [Chi Tiết Các Kỹ Thuật Đã Áp Dụng Trong Đồ Án](#2-chi-tiết-các-kỹ-thuật-đã-áp-dụng-trong-đồ-án)
   - [2.1. Kiến Trúc Giao Diện Bootstrap, Responsive & Three.js 3D](#21-kiến-trúc-giao-diện-bootstrap-responsive--threejs-3d)
   - [2.2. Các Phương Thức Truyền Dữ Liệu ($_GET, $_POST, JSON, Spoofing, Headers)](#22-các-phương-thức-truyền-dữ-liệu-_get-_post-json-spoofing-headers)
   - [2.3. Kỹ Thuật Upload Dữ Liệu & Tệp Hình Ảnh](#23-kỹ-thuật-upload-dữ-liệu--tệp-hình-ảnh)
   - [2.4. Về Quản Lý Phiên Session & Cơ Chế Cookie](#24-về-quản-lý-phiên-session--cơ-chế-cookie)
   - [2.5. Kết Nối Cơ Sở Dữ Liệu & Thực Thi Truy Vấn (PDO + 23 Stored Procedures)](#25-kết-nối-cơ-sở-dữ-liệu--thực-thi-truy-vấn-pdo--23-stored-procedures)
   - [2.6. Kỹ Thuật Phân Trang (Tại Trang Bản Tin Cộng Đồng & Tin Tức)](#26-kỹ-thuật-phân-trang-tại-trang-bản-tin-cộng-đồng--tin-tức)
   - [2.7. Lập Trình Hướng Đối Tượng (OOP)](#27-lập-trình-hướng-đối-tượng-oop)
   - [2.8. Mô Hình MVC & Luồng Dữ Liệu Chi Tiết (Request-Response Lifecycle)](#28-mô-hình-mvc--luồng-dữ-liệu-chi-tiết-request-response-lifecycle)
   - [2.9. Kỹ Thuật Tương Tác Không Tải Lại Trang (AJAX)](#29-kỹ-thuật-tương-tác-không-tải-lại-trang-ajax)
   - [2.10. Kiến Trúc Web APIs (Nội Bộ & Tích Hợp Dịch Vụ Ngoài)](#210-kiến-trúc-web-apis-nội-bộ--tích-hợp-dịch-vụ-ngoài)
   - [2.11. Các Kỹ Thuật Bổ Trợ & Đột Phá Khác](#211-các-kỹ-thuật-bổ-trợ--đột-phá-khác)
3. [Bảng Đối Chiếu Kỹ Thuật Dự Án Với Bài Giảng Của Giảng Viên](#3-bảng-đối-chiếu-kỹ-thuật-dự-án-với-bài-giảng-của-giảng-viên)

---

## 1. TỔNG QUAN KIẾN TRÚC & TOÀN BỘ HẠNG MỤC ĐÃ TRIỂN KHAI

Hệ thống **Online Traveller** được xây dựng theo mô hình **Custom MVC Framework** hiện đại trên nền tảng **PHP 8.2+** kết hợp hệ quản trị cơ sở dữ liệu **MariaDB / MySQL**, giao diện người dùng theo ngôn ngữ **Dark-Alpine Glassmorphism** (hỗ trợ chuyển đổi Day/Night Mode).

### Các phân hệ nghiệp vụ đã hoàn thiện:
1. **Phân hệ Người dùng & Phân quyền RBAC (TV1):** Đăng ký, đăng nhập, hồ sơ cá nhân (Profile), kiểm soát 4 vai trò (*Guest, Tourist, Host, Admin*), bảo vệ phiên làm việc và bảo mật CSRF kép.
2. **Phân hệ Gói Trải Nghiệm, Lưu Trú & 3D (TV2):** Quản lý tour du lịch, gắn kết Homestay nhà dân (quan hệ 1-1), lịch khởi hành (Schedules), timeline trạm dừng chân và không gian trình diễn 3D WebGL (Three.js Z-Scroll).
3. **Phân hệ Đặt Chỗ, Thanh Toán & Hoàn Tiền (TV3):** Báo giá tự động, áp dụng mã khuyến mãi, cọc 50% hoặc trả đủ 100%, khóa giữ chỗ 15 phút, cổng thanh toán MoMo Sandbox (QR Code, Webhook IPN HMAC-SHA256), phát hành E-Receipt và xử lý hủy tour theo mốc 24h.
4. **Phân hệ Cộng Đồng & Dịch Vụ Ngoại Vi (TV4):** Kênh hỏi đáp thảo luận Q&A (AJAX 1 cấp), đánh giá sao & nhận xét sau chuyến đi (chống review chéo), Bản tin kết hợp bài viết cẩm nang và RSS VnExpress Du lịch, thời tiết thực tế OpenWeather API.

---

## 2. CHI TIẾT CÁC KỸ THUẬT ĐÃ ÁP DỤNG TRONG ĐỒ ÁN

### 2.1. Kiến Trúc Giao Diện Bootstrap, Responsive & Three.js 3D

#### a. Bootstrap 5 & Hệ thống Grid Layout
- **Thư viện tích hợp:** Bootstrap v5.3.3 CSS qua CDN và Bootstrap Icons v1.11.3.
  - Vị trí khai báo: [app/views/layouts/main.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/layouts/main.php#L22-L26).
- **Quy tắc phân cấp Grid:** Toàn bộ giao diện tuân thủ chặt chẽ mô hình phân cấp:
  $$\text{Container (container / container-fluid)} \longrightarrow \text{Row (row)} \longrightarrow \text{Column (col-*, col-md-*, col-lg-*)}$$
  - Ví dụ tại thanh điều hướng Top Navbar:
    ```html
    <header class="top-floating-header">
        <div class="container-fluid px-3 px-md-4 px-lg-4">
            <div class="row align-items-center justify-content-between g-2">
                <div class="col-auto">...Logo...</div>
                <div class="col-auto d-none d-lg-block">...Menu Pill...</div>
                <div class="col-auto d-flex align-items-center gap-2">...Actions...</div>
            </div>
        </div>
    </header>
    ```
  - Ví dụ tại trang danh sách tour [app/views/services/index.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/services/index.php): Chia cột lưới `row g-4` chứa các `col-12 col-md-6 col-lg-4` để hiển thị thẻ tour.
- **Tính đáp ứng (Responsive Design):**
  - Sử dụng các tiền tố kích thước màn hình Bootstrap: `col-12` (di động), `col-md-6` (máy tính bảng), `col-lg-4` (màn hình lớn).
  - Điều khiển hiển thị: `d-none d-lg-block` (ẩn trên mobile, hiện trên desktop), `d-flex`, `flex-column flex-md-row`.
  - Thanh Bottom Bar tối ưu riêng cho thiết bị cảm ứng di động (`mobile-bottom-nav`).

#### b. Đồ họa không gian 3D tương tác (Three.js Z-Scroll)
- **Thư viện:** Three.js r128 (`three.min.js`).
- **Vị trí tệp triển khai:**
  1. Modal trình chiếu 3D trong trang chi tiết tour: [app/views/services/show.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/services/show.php#L975-L1250).
  2. Trang chuyên biệt toàn màn hình Fullscreen 3D: [app/views/services/virtual_tour.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/services/virtual_tour.php#L1-L400).
- **Thuật toán & Cơ chế hoạt động (Trích xuất từ show.php):**
  - **Khởi tạo WebGL Scene:**
    ```javascript
    scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x0b131e, 0.032);
    camera = new THREE.PerspectiveCamera(55, width / height, 0.1, 130);
    renderer = new THREE.WebGLRenderer({ canvas: canvasEl, antialias: true, alpha: true });
    ```
  - **Bố trí các trạm dừng chân theo trục Z:**
    ```javascript
    stopsData.forEach((stop, index) => {
        const group = new THREE.Group();
        const zPos = -index * stationGap; // Sắp xếp trạm lùi sâu dần vào trục Z
        group.position.set(0, 0, zPos);
        // Nạp texture ảnh trạm dừng và dựng Mesh phẳng hiển thị ảnh
        textureLoader.load(stop.image_url, (texture) => {
            const material = new THREE.MeshBasicMaterial({ map: texture, transparent: true });
            const mesh = new THREE.Mesh(geometry, material);
            group.add(mesh);
        });
        scene.add(group);
    });
    ```
  - **Thuật toán cuộn chuột tịnh tiến Camera (Z-Scroll Navigation):**
    - Lắng nghe sự kiện `wheel` / `touchmove` / phím mũi tên.
    - Tính toán độ lệch đích `targetZ` dựa vào vị trí cuộn.
    - Cập nhật vị trí máy quay theo phương pháp nội suy chuyển động mượt (*Linear Interpolation - Lerp*):
      $$\text{camera.position.z} \mathrel{+}= (\text{targetZ} - \text{camera.position.z}) \times 0.08$$
    - Khi máy quay tiến gần tọa độ trạm dừng chân nào, hệ thống kích hoạt hiển thị thẻ thông tin văn hóa, âm thanh và câu chuyện của trạm đó.

---

### 2.2. Các Phương Thức Truyền Dữ Liệu ($_GET, $_POST, JSON, Spoofing, Headers)

Hệ thống đóng gói toàn bộ việc tiếp nhận và chuẩn hóa dữ liệu HTTP Request thông qua lớp [core/Request.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/Request.php).

#### 1. Phương thức `$_GET` (Query Parameters)
- **Cách thức hoạt động:** Trích xuất các tham số trên thanh địa chỉ URL sau dấu chấm hỏi `?key=value&...`.
- **Triển khai trong mã nguồn:** Lớp `Request` đọc biến `$_GET` thông qua phương thức `$request->query('key', $default)` hoặc gọi trực tiếp mảng siêu toàn cục.
- **Ví dụ thực tế trong dự án:**
  - Lọc tour: `GET /services?category=2&location=Đà+Lạt&price=paid`
  - Phân trang bản tin: `GET /posts?tab=community&page=2`
  - Xem chi tiết biên nhận điện tử: `GET /receipts/5`

#### 2. Phương thức `$_POST` (Form Body Parameters)
- **Cách thức hoạt động:** Thu thập dữ liệu gửi ngầm trong phần thân (Body) của HTTP Request từ các biểu mẫu HTML có thuộc tính `method="POST"`.
- **Triển khai trong mã nguồn:** Được chuẩn hóa qua `$request->post('key', $default)`.
- **Ví dụ thực tế trong dự án:**
  - Đăng nhập người dùng: `POST /login` (`email`, `password`)
  - Đăng ký tài khoản: `POST /register` (`role`, `name`, `email`, `phone`, `password`)
  - Tạo gói trải nghiệm mới: `POST /host/services`
  - Đặt tour giữ chỗ: `POST /bookings`

#### 3. Phương thức nạp chuỗi thô `php://input` (JSON Payload)
- **Cách thức hoạt động:** Đối với các yêu cầu API hiện đại hoặc Webhook từ bên thứ ba gửi dữ liệu `Content-Type: application/json`, mảng `$_POST` của PHP sẽ rỗng. Hệ thống đọc trực tiếp luồng dữ liệu thô:
  ```php
  // Vị trí: core/Request.php (dòng 52-62)
  $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
  if (str_contains($contentType, 'application/json')) {
      $rawInput = file_get_contents('php://input');
      if ($rawInput !== false && $rawInput !== '') {
          $this->jsonParams = json_decode($rawInput, true) ?? [];
      }
  }
  ```
- **Ví dụ thực tế:** Tiếp nhận Webhook IPN từ máy chủ MoMo (`POST /payments/ipn`) hoặc các yêu cầu API nội bộ.

#### 4. Kỹ thuật giả lập phương thức HTTP (Method Spoofing: PUT / DELETE)
- **Cách thức hoạt động:** Các form HTML chuẩn chỉ hỗ trợ `GET` và `POST`. Để hỗ trợ chuẩn RESTful, `core/Request.php` (dòng 30-35) kiểm tra trường ẩn `_method`:
  ```php
  if ($rawMethod === 'POST' && isset($this->postParams['_method'])) {
      $this->method = strtoupper((string)$this->postParams['_method']);
  }
  ```

#### 5. Truyền dữ liệu qua Tham số định tuyến (Route Parameters)
- **Cách thức hoạt động:** `core/Router.php` sử dụng Regex bắt các biến động trực tiếp trên URI thân thiện (Clean URL), ví dụ `/services/{id}` hay `/posts/{slug}` và nạp vào `$request->param('id')`.

#### 6. Truyền dữ liệu qua HTTP Headers
- Lấy thông tin Header: `$request->header('X-CSRF-TOKEN')`, `$request->header('Authorization')`.
- Nhận diện cuộc gọi AJAX: Kiểm tra `$this->server['HTTP_X_REQUESTED_WITH'] === 'xmlhttprequest'`.

---

### 2.3. Kỹ Thuật Upload Dữ Liệu & Tệp Hình Ảnh

Dự án áp dụng quy trình kiểm soát tệp tải lên bảo mật cao, từ chối việc tin tưởng tên file do client gửi lên nhằm triệt tiêu nguy cơ tấn công Shell Upload, Path Traversal hoặc Local File Inclusion (LFI).

#### a. Cấu trúc Form Upload
Tất cả các form tải tệp đều sử dụng cấu trúc:
```html
<form method="POST" action="..." enctype="multipart/form-data">
    <input type="hidden" name="_csrf_token" value="...">
    <input type="file" name="banner_file" accept="image/jpeg,image/png,image/webp">
</form>
```

#### b. Các vị trí triển khai upload trong dự án:
1. **Upload ảnh bài viết Bản tin:** [app/controllers/PostController.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/controllers/PostController.php#L259-L265) gọi [app/models/Post.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/models/Post.php#L94-L140) (`handleThumbnailUpload`).
2. **Upload Banner gói tour:** [app/controllers/HostController.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/controllers/HostController.php#L139-L142) gọi [app/services/TourService.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/services/TourService.php#L245-L248) (`handleBannerUpload`).
3. **Upload danh sách nhiều ảnh trạm dừng chân 3D:** [app/controllers/HostController.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/controllers/HostController.php#L196-L225) gọi [app/services/TourService.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/services/TourService.php#L253-L256) (`handleStopImageUpload`).

#### c. Trích xuất Thuật toán & Quy trình xử lý Upload an toàn
*(Trích xuất nguyên mẫu từ `app/services/TourService.php` dòng 261-307 và `app/models/Post.php` dòng 94-140)*

```php
private function processImageUpload(array $file, string $subfolder): string
{
    // BƯỚC 1: Kiểm tra cấu trúc mảng $_FILES và mã lỗi PHP upload
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Dữ liệu tệp tải lên không hợp lệ.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Lỗi tải tệp lên máy chủ: Mã lỗi ' . $file['error']);
    }

    // BƯỚC 2: Kiểm soát giới hạn dung lượng tệp (Tối đa 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Dung lượng ảnh vượt quá giới hạn cho phép (tối đa 5MB).');
    }

    // BƯỚC 3: Thẩm định mã MIME nhị phân thực tế (Không tin đuôi file mở rộng của client)
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!array_key_exists($mimeType, $allowedMimes)) {
        throw new RuntimeException('Định dạng ảnh không được hỗ trợ. Vui lòng chọn ảnh JPG, PNG hoặc WEBP.');
    }

    // BƯỚC 4: Tạo tên tệp ngẫu nhiên chống trùng lặp và ghi đè
    $ext = $allowedMimes[$mimeType];
    $filename = 'img_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

    // BƯỚC 5: Xác định đường dẫn thư mục đích và tạo mới nếu chưa tồn tại
    $projectRoot = dirname(__DIR__, 2);
    $targetDir = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subfolder);

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('Không thể khởi tạo thư mục lưu trữ ảnh.');
        }
    }

    // BƯỚC 6: Di chuyển tệp từ thư mục tạm sang thư mục public assets
    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;
    if (!@move_uploaded_file($file['tmp_name'], $targetPath) && !@copy($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Lưu tệp ảnh lên máy chủ thất bại.');
    }

    // Trả về đường dẫn URL tương đối để lưu vào Cơ sở dữ liệu
    return '/assets/images/' . $subfolder . '/' . $filename;
}
```

#### d. Luồng di chuyển dữ liệu upload:
$$\text{Người dùng chọn ảnh tại Form} \xrightarrow{\text{POST multipart/form-data}} \text{PHP Engine lưu vào /tmp} \xrightarrow{\text{\$_FILES}} \text{Controller tiếp nhận}$$
$$\longrightarrow \text{TourService / PostModel thẩm định MIME (finfo) \& Size} \xrightarrow{\text{move\_uploaded\_file()}} \text{public/assets/images/...}$$
$$\longrightarrow \text{Lấy URL ảnh đưa vào Stored Procedure} \xrightarrow{\text{CALL sp\_*}} \text{Bảng tbl\_services / tbl\_posts trong MySQL}$$

---

### 2.4. Về Quản Lý Phiên Session & Cơ Chế Cookie

#### a. Quản lý Session (`$_SESSION`)
- **Khởi tạo phiên tập trung:** Phiên làm việc được kích hoạt duy nhất tại điểm khởi động hệ thống: [bootstrap/app.php](file:///E:/Dev/Study'/Web/OnlineTraveller/bootstrap/app.php#L42-L52):
  ```php
  if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
      session_start([
          'cookie_httponly' => true,
          'cookie_samesite' => 'Lax',
      ]);
  }
  ```
  *Ý nghĩa bảo mật:* Cờ `cookie_httponly` ngăn chặn mã JavaScript độc hại đánh cắp session ID thông qua tấn công XSS; `cookie_samesite => 'Lax'` bảo vệ ứng dụng trước các cuộc tấn công CSRF chéo tên miền.
- **Các biến Session thực tế đang lưu trữ trong dự án:**
  1. `$_SESSION['user']`: Lưu giữ danh tính người dùng đăng nhập hiện hành (`id`, `name`, `email`, `phone`, `role`, `status`). Tại mỗi request, `bootstrap/app.php` tự động truy vấn lại DB; nếu tài khoản bị khóa (`status = 'locked'`), hệ thống tự động hủy session `unset($_SESSION['user'])` và đẩy ra trang đăng nhập.
  2. `$_SESSION['flash']`: Cơ chế thông báo nhanh (Flash Messages: `success`, `danger`, `warning`, `info`). Được ghi qua `$this->flash('success', '...')` và tự động dọn dẹp bằng `unset($_SESSION['flash'])` ngay sau khi View render xong (vị trí: [core/BaseController.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/BaseController.php#L93-L117)).
  3. `$_SESSION['csrf_token']`: Lưu trữ chuỗi mã ngẫu nhiên bảo mật 64 ký tự hex (`bin2hex(random_bytes(32))`) do [app/helpers/SecurityHelper.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/helpers/SecurityHelper.php#L21-L32) sinh ra để xác thực toàn bộ các tác vụ thay đổi dữ liệu.
  4. Hủy phiên đăng xuất: Khi gọi `GET /logout`, hệ thống thực thi `unset($_SESSION['user'])` trong `AuthService::logout()`.

#### b. Cơ chế Cookie (`$_COOKIE` / `setcookie`)
- **Tình trạng rà soát thực tế:** **DỰ ÁN KHÔNG SỬ DỤNG BẤT KỲ LỆNH `setcookie()` HOẶC BIẾN `$_COOKIE` TỰ TẠO NÀO.**
- Hệ thống chỉ duy trì duy nhất Cookie định danh phiên làm việc mặc định (`PHPSESSID`) do chính PHP Engine tự động trao đổi với trình duyệt phía client. Dự án không triển khai tính năng "Ghi nhớ mật khẩu" qua Cookie và không lưu giỏ hàng vào Cookie.

---

### 2.5. Kết Nối Cơ Sở Dữ Liệu & Thực Thi Truy Vấn (PDO + 23 Stored Procedures)

Thay vì sử dụng thư viện `mysqli` hướng thủ tục truyền thống, dự án áp dụng **PDO (PHP Data Objects)** hiện đại với mẫu thiết kế **Singleton** để quản trị kết nối tập trung, tối ưu tài nguyên và ngăn chặn lỗi tràn kết nối.

#### a. Quản lý kết nối Singleton
- Tệp định nghĩa: [core/Database.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/Database.php#L17-L80).
- Cấu hình: [config/database.php](file:///E:/Dev/Study'/Web/OnlineTraveller/config/database.php).
- **Thuật toán tạo kết nối:**
  ```php
  $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
  $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false, // Bắt buộc dùng native prepared statements
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
  ];
  $this->pdo = new PDO($dsn, $user, $password, $options);
  ```

#### b. Cơ chế thực thi Truy vấn & Stored Procedures
- **Truy vấn Prepared Statements thông thường:**
  Phương thức `$db->query($sql, $params)`, `$db->fetchOne()`, `$db->fetchAll()` tự động liên kết tham số (parameter binding), loại bỏ hoàn toàn nguy cơ SQL Injection.
- **Thực thi Stored Procedures:**
  *(Trích xuất từ `core/Database.php` dòng 146-163)*
  ```php
  public function callProcedure(string $procedureName, array $params = []): array
  {
      $placeholders = empty($params) ? '' : implode(', ', array_fill(0, count($params), '?'));
      $sql = "CALL {$procedureName}({$placeholders})";

      $stmt = $this->getPdo()->prepare($sql);
      $stmt->execute(array_values($params));

      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Giải phóng con trỏ kết quả để tránh lỗi MariaDB "Commands out of sync"
      while ($stmt->nextRowset()) {
          // drain extra result sets if any
      }
      $stmt->closeCursor();

      return $rows;
  }
  ```

#### c. Danh mục 23 Stored Procedures đã hiện thực trong CSDL:
Toàn bộ nghiệp vụ dữ liệu nhạy cảm được giao phó cho 23 Stored Procedures (phân bổ theo 4 thành viên):
- **TV1 (`database/procedures/01_users_categories.sql`):**
  1. `dhs_sp_users_register`: Đăng ký tài khoản mới kèm mật khẩu đã băm.
  2. `dhs_sp_users_find_login`: Tìm kiếm thông tin đăng nhập theo email.
  3. `dhs_sp_users_update_profile`: Cập nhật thông tin hồ sơ người dùng.
  4. `dhs_sp_users_set_status`: Admin khóa / kích hoạt trạng thái tài khoản.
  5. `dhs_sp_categories_save`: Tạo mới hoặc cập nhật danh mục du lịch.
- **TV2 (`database/procedures/02_services_bookings.sql`):**
  6. `ptl_sp_services_create`: Tạo mới gói tour, thông tin GPS, chính sách giá.
  7. `ptl_sp_services_update`: Cập nhật thông tin chi tiết gói tour.
  8. `ptl_sp_services_moderate`: Phê duyệt hoặc từ chối tour từ phía Admin.
  9. `ptl_sp_accommodations_save`: Lưu thông tin Homestay gắn liền gói tour.
  10. `ptl_sp_schedules_create`: Mở lịch khởi hành, quản lý sức chứa.
  11. `ptl_sp_schedules_cancel`: Hủy lịch khởi hành và kích hoạt hoàn tiền tự động.
- **TV3 (`database/procedures/03_promotions_payments.sql`):**
  12. `ndtt_sp_bookings_quote`: Báo giá tức thời, kiểm tra mã giảm giá và tính tiền cọc.
  13. `ndtt_sp_bookings_create`: Tạo đơn đặt tour, kích hoạt cơ chế giữ chỗ 15 phút.
  14. `ndtt_sp_bookings_approve`: Host chấp thuận đơn đặt chỗ của du khách.
  15. `ndtt_sp_bookings_cancel`: Du khách hủy đơn, tự động đối chiếu mốc 24h (T24).
  16. `ndtt_sp_payments_apply_verified`: Ghi nhận thanh toán MoMo thành công sau khi xác thực IPN.
  17. `ndtt_sp_refunds_create_and_approve`: Tạo và duyệt lệnh hoàn tiền.
- **TV4 (`database/procedures/04_community_content.sql`):**
  18. `pct_sp_discussions_create`: Đăng câu hỏi hoặc phản hồi Q&A dưới tour.
  19. `pct_sp_reviews_create`: Đánh giá 1-5 sao (kiểm tra chỉ cho phép du khách đã hoàn thành tour).
  20. `pct_sp_posts_save`: Thêm mới hoặc chỉnh sửa bài viết cẩm nang du lịch.
  21. `pct_sp_posts_delete`: Xóa bài viết cẩm nang du lịch.
  22. `pct_sp_promotions_save`: Quản trị viên lưu chương trình khuyến mãi.
  23. `pct_sp_community_posts_create`: Người dùng đăng bài chia sẻ kinh nghiệm cộng đồng.

---

### 2.6. Kỹ Thuật Phân Trang (Tại Trang Bản Tin Cộng Đồng & Tin Tức)

Hệ thống triển khai thuật toán phân trang tại trang Bản tin du lịch nhằm giải quyết việc hiển thị danh sách lớn bài viết mà vẫn giữ tốc độ tải trang tối ưu.

- **Vị trí tệp tin:**
  - Controller xử lý: [app/controllers/PostController.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/controllers/PostController.php#L90-L135).
  - View hiển thị giao diện phân trang: [app/views/posts/index.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/posts/index.php#L178-L208).

#### a. Thuật toán phân trang (Trích xuất nguyên mẫu từ `PostController.php` dòng 104-110):
```php
// 1. Khai báo số lượng bài viết cố định hiển thị trên mỗi trang
$perPage = 6;

// 2. Xác định tổng số lượng mục dữ liệu (Hợp nhất từ DB bài viết + Tin RSS)
$totalItems = count($allItems);

// 3. Tính toán tổng số trang bằng hàm làm tròn lên ceil()
$totalPages = max(1, (int)ceil($totalItems / $perPage));

// 4. Lấy số trang hiện tại từ URL ($_GET['page']), đảm bảo trong khoảng 1 đến $totalPages
$currentPage = max(1, min($totalPages, (int)$request->query('page', 1)));

// 5. Tính toán vị trí dịch chuyển (offset)
$offset = ($currentPage - 1) * $perPage;

// 6. Cắt mảng dữ liệu lấy các phần tử của trang hiện hành
$pagedItems = array_slice($allItems, $offset, $perPage);
```

#### b. Cơ chế hiển thị trên giao diện (View Pagination):
Tại [app/views/posts/index.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/posts/index.php#L178-L208), các nút điều hướng trang được bọc trong các thẻ `<a>` chuẩn Bootstrap Pagination với tham số giữ nguyên Tab hiện tại:
- **Nút lùi (Prev):** Đường dẫn `/posts?tab=<?= $currentTab ?>&page=<?= $currentPage - 1 ?>`. Nếu `$currentPage <= 1` thì gắn class `disabled` và thuộc tính `aria-disabled="true"`.
- **Hiển thị thông số:** Thẻ `span` hiển thị `Trang <?= $currentPage ?> / <?= $totalPages ?>`.
- **Nút tiến (Next):** Đường dẫn `/posts?tab=<?= $currentTab ?>&page=<?= $currentPage + 1 ?>`. Nếu `$currentPage >= $totalPages` thì gắn class `disabled`.

---

### 2.7. Lập Trình Hướng Đối Tượng (OOP)

Toàn bộ mã nguồn backend tuân thủ triệt để lập trình hướng đối tượng theo chuẩn PSR-4, ứng dụng đầy đủ 4 tính chất cơ bản:

1. **Tính kế thừa (Inheritance):**
   - Lớp cơ sở [core/BaseController.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/BaseController.php) cung cấp sẵn các phương thức `view()`, `json()`, `redirect()`, `flash()`, `user()` cho hàng loạt controller con: `HomeController`, `AuthController`, `ServiceController`, `BookingController`, `HostController`, `AdminController`, `PostController`...
   - Lớp cơ sở trừu tượng [core/BaseModel.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/BaseModel.php) cung cấp sẵn kết nối `Database`, các phương thức truy vấn `callProcedure()`, `fetchOne()`, `fetchAll()`, `execute()` cho toàn bộ các model: `User`, `Service`, `Booking`, `Post`, `Review`, `Discussion`, `Payment`...
2. **Tính trừu tượng (Abstraction):**
   - Khai báo `abstract class BaseController` và `abstract class BaseModel`: Không thể khởi tạo trực tiếp instance của lớp cơ sở mà buộc phải kế thừa thông qua lớp dẫn xuất cụ thể.
3. **Tính đa hình & Interface (Interfaces & Polymorphism):**
   - Interface [core/Middleware.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/Middleware.php) định nghĩa hợp đồng chung duy nhất:
     ```php
     interface Middleware {
         public function handle(Request $request, Closure $next): mixed;
     }
     ```
   - Các lớp middleware khác nhau (`AuthMiddleware`, `RoleMiddleware`, `CsrfMiddleware`, `HostMiddleware`, `AdminMiddleware`) cùng thực thi (`implements`) interface này theo các logic kiểm tra quyền khác nhau. Bộ điều phối `Router` có thể gọi đồng nhất `$middleware->handle($request, $next)`.
4. **Tính đóng gói (Encapsulation):**
   - Sử dụng các định từ truy xuất `private`, `protected`, `public` một cách chặt chẽ trên các thuộc tính kết nối `$pdo`, thông tin cấu hình `$config`, danh sách tham số `$queryParams`, `$postParams`.

---

### 2.8. Mô Hình MVC & Luồng Dữ Liệu Chi Tiết (Request-Response Lifecycle)

Ứng dụng được tổ chức theo chuẩn kiến trúc MVC phân tầng:

```
[Client Trình Duyệt]
       │  ▲
(1)    │  │ (10) Phản hồi HTML / JSON
       ▼  │
[public/index.php] (Front Controller)
       │
(2) Khởi tạo Request & Router
       ▼
[core/Router.php] 
       │
(3) Chạy qua Middleware Pipeline (CSRF, Auth, Role)
       ▼
[app/controllers/*Controller.php] (Tầng Controller)
       │                  ▲
(4) Gọi nghiệp vụ         │ (7) Dữ liệu Dataset
       ▼                  │
[app/models/*Model.php] (Tầng Model)
       │                  ▲
(5) Gọi CALL sp_*         │ (6) Trả về kết quả
       ▼                  │
[MariaDB / MySQL] ◄───────┘
       │
(8) Controller nạp View & Layout (Buffer ob_start)
       ▼
[app/views/*] & [app/views/layouts/main.php] (Tầng View)
       │
(9) Đóng gói HTTP Header & Body
       ▼
[core/Response.php]
```

#### Chi tiết các trạm trên luồng dữ liệu:
1. **Trạm 1 (Điểm đón đầu):** Yêu cầu từ người dùng gửi tới máy chủ web Apache/Nginx. Tệp `.htaccess` điều hướng mọi request không phải tệp tĩnh về [public/index.php](file:///E:/Dev/Study'/Web/OnlineTraveller/public/index.php) (Front Controller).
2. **Trạm 2 (Khởi động hệ thống):** `public/index.php` nạp [bootstrap/app.php](file:///E:/Dev/Study'/Web/OnlineTraveller/bootstrap/app.php) để khởi tạo Session an toàn, nạp `Autoloader`, kết nối `Database::init()`, nạp các file cấu hình `config/*.php`.
3. **Trạm 3 (Đóng gói Request & Định tuyến):** Tạo đối tượng `Core\Request`. Đối tượng `Core\Router` đối chiếu URI và HTTP Method với danh sách tuyến đường đã đăng ký trong `routes/web.php` và `routes/api.php`.
4. **Trạm 4 (Trạm lọc Middleware):** Trước khi vào Controller, request chạy qua đường ống Middleware:
   - `CsrfMiddleware`: So khớp token form/header với `$_SESSION['csrf_token']`.
   - `AuthMiddleware`: Kiểm tra người dùng đã đăng nhập chưa thông qua `$_SESSION['user']`.
   - `RoleMiddleware`: Kiểm tra vai trò (`tourist`, `host`, `admin`).
5. **Trạm 5 (Xử lý Controller):** Controller tương ứng (ví dụ `ServiceController::show`) tiếp nhận đối tượng `Request` và các tham số route. Controller gọi Model hoặc Service nghiệp vụ.
6. **Trạm 6 (Tầng Model & CSDL):** Model (ví dụ `App\Models\Service`) gọi phương thức `$this->callProcedure('ptl_sp_services_get_by_id', [$id])`. Lớp `Core\Database` chuẩn bị câu lệnh `CALL` trên kết nối PDO, thực thi trên MariaDB và nhận lại dataset dạng mảng kết hợp.
7. **Trạm 7 (Tầng View Render):** Controller nhận dữ liệu từ Model, thực thi hàm `$this->view('services/show', ['service' => $service])`. Tầng View sử dụng cơ chế đệm ngõ ra (`ob_start()`, `include $viewFile`, `ob_get_clean()`) để lồng nội dung trang con vào khung giao diện tổng thể (`layouts/main.php`).
8. **Trạm 8 (Trạm kết thúc - Response):** Đối tượng `Core\Response` thiết lập mã trạng thái HTTP (200, 302, 404), gắn Header tương ứng (`Content-Type: text/html` hoặc `application/json`) và xuất toàn bộ nội dung ra trình duyệt của du khách.

---

### 2.9. Kỹ Thuật Tương Tác Không Tải Lại Trang (AJAX)

Trong dự án thực tế, các tương tác AJAX được triển khai bằng **Modern JavaScript Fetch API** kết hợp `async/await` và định dạng trao đổi dữ liệu tiêu chuẩn **JSON** (thay vì đối tượng thuần `XMLHttpRequest` đời cũ). Các yêu cầu Fetch luôn đính kèm Header `X-Requested-With: XMLHttpRequest` để Controller nhận diện và trả về phản hồi JSON thay vì trang HTML hoàn chỉnh.

#### Danh sách các chức năng thực tế sử dụng AJAX:
1. **Gửi câu hỏi và thảo luận Q&A (1-cấp):**
   - Vị trí mã nguồn: [app/views/services/show.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/services/show.php#L1384-L1410).
   - Phương thức: `fetch('/discussions', { method: 'POST', body: formData, headers: {'X-Requested-With': 'XMLHttpRequest'} })`.
   - Xử lý: Khi người dùng gửi câu hỏi, AJAX đẩy dữ liệu lên server, nhận về phản hồi JSON và chèn ngay phần tử HTML bình luận mới vào danh sách mà không cần F5 tải lại trang.
2. **Gửi phản hồi của Host cho câu hỏi thảo luận:**
   - Vị trí mã nguồn: [app/views/services/show.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/services/show.php#L1480-L1510).
   - Phương thức: `fetch('/discussions', { method: 'POST', body: formData })`.
3. **Gửi đánh giá sao & Nhận xét tour (Reviews):**
   - Vị trí mã nguồn: [app/views/services/show.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/services/show.php#L1580-L1615).
   - Phương thức: `POST /reviews`. Trình duyệt gửi điểm sao (1-5), nội dung và ID tour; server gọi Stored Procedure `pct_sp_reviews_create` kiểm tra điều kiện hoàn thành tour và trả về kết quả JSON để cập nhật UI ngay lập tức.
4. **Báo giá trực tiếp & Tính toán khuyến mãi khi đặt tour (Live Quote):**
   - Vị trí mã nguồn: [app/views/bookings/create.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/bookings/create.php#L390-L435).
   - Phương thức: `fetch('/api/bookings/quote', { method: 'POST', body: new URLSearchParams(...) })`.
   - Xử lý: Khi du khách thay đổi số lượng người tham gia hoặc nhập mã giảm giá, hàm JS tự động gửi AJAX lên server. Stored Procedure `ndtt_sp_bookings_quote` tính toán chiết khấu, tổng tiền, tiền cọc 50% hoặc 100% và trả về JSON để cập nhật tức thì khối tóm tắt thanh toán trên màn hình.
5. **Nạp dữ liệu các trạm dừng chân cho không gian 3D Three.js:**
   - Tuyến đường: `GET /api/services/{id}/stops`.
   - Xử lý: Three.js fetch danh sách hình ảnh và toạ độ các trạm của tour về để khởi tạo các Mesh 3D trên WebGL Canvas.
6. **Giả lập xác nhận thanh toán Webhook IPN MoMo:**
   - Vị trí mã nguồn: [app/views/payments/sandbox_qr.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/payments/sandbox_qr.php#L130-L150).
   - Phương thức: `fetch('/payments/ipn', { method: 'POST', body: JSON.stringify(...) })` kích hoạt tiến trình kiểm tra trạng thái thanh toán tự động.

---

### 2.10. Kiến Trúc Web APIs (Nội Bộ & Tích Hợp Dịch Vụ Ngoài)

#### a. Hệ thống Internal REST-like APIs
Được định nghĩa tập trung tại tệp [routes/api.php](file:///E:/Dev/Study'/Web/OnlineTraveller/routes/api.php):
1. `GET /api/health`: API giám sát sức khỏe hệ thống, trả về phiên bản ứng dụng, số lượng bảng CSDL và số lượng Stored Procedures đang hoạt động.
2. `GET /api/services/{id}/stops`: Cung cấp danh sách JSON trạm dừng chân phục vụ WebGL Three.js.
3. `GET /api/services/{id}/schedules`: Cung cấp lịch khởi hành và số chỗ còn nhận của tour.
4. `POST /api/bookings/quote`: Web API tính toán báo giá, kiểm tra mã Voucher và phân chia kỳ hạn trả tiền.
5. `GET /api/weather`: Trả về dữ liệu thời tiết tại địa phương theo toạ độ địa lý.
6. `GET /api/rss/news`: Trả về dữ liệu dòng tin tức du lịch định dạng XML/JSON.

#### b. Tích hợp Dịch vụ & Cổng thanh toán bên thứ ba
1. **Cổng thanh toán điện tử MoMo Sandbox (v2 API):**
   - Tệp triển khai: [app/integrations/MomoClient.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/integrations/MomoClient.php).
   - Thuật toán ký số: Tạo chữ ký điện tử an toàn `HMAC-SHA256` dựa trên khóa bí mật `secretKey` và chuỗi tham số chuẩn tắc (`accessKey`, `amount`, `orderId`, `ipnUrl`...).
   - Quy trình: Gọi API MoMo để sinh mã QR thanh toán (`requestType = captureWallet`), đồng thời tiếp nhận Webhook IPN server-to-server và đối soát chữ ký số bằng hàm so sánh bất biến thời gian `hash_equals()` nhằm triệt tiêu nguy cơ giả mạo thanh toán (*Timing Attack*).
2. **Dịch vụ dự báo thời tiết OpenWeatherMap API:**
   - Tệp triển khai: [app/integrations/OpenWeatherClient.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/integrations/OpenWeatherClient.php).
   - Kết nối tới `api.openweathermap.org/data/2.5/weather` bằng tọa độ `latitude` và `longitude` của gói tour.
   - Kỹ thuật nâng cao: Trang bị cơ chế **File Cache** với thời gian sống (TTL) 1.800 giây (30 phút) nhằm tiết kiệm hạn ngạch gọi API và có cơ chế Fallback dự báo ngẫu nhiên thông minh khi mất kết nối Internet.
3. **Dòng tin tức du lịch VnExpress Du Lịch RSS (XML Webservice):**
   - Tệp triển khai: [app/integrations/RssClient.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/integrations/RssClient.php).
   - Tự động kéo dòng tin XML từ `https://vnexpress.net/rss/du-lich.rss`.
   - Phân tích cú pháp XML bằng thư viện PHP `SimpleXMLElement` với các cờ bảo mật chống tấn công XML External Entity (`LIBXML_NONET | LIBXML_NOERROR`).
   - Tách lọc ảnh đại diện và trích đoạn mô tả bằng biểu thức chính quy (Regex), lưu cache tệp tin trên đĩa với TTL 3.600 giây (1 giờ).

---

### 2.11. Các Kỹ Thuật Bổ Trợ & Đột Phá Khác

1. **Phân quyền vai trò RBAC 4 cấp (Role-Based Access Control):** Phân chia đặc quyền rạch ròi giữa Khách vãng lai (*Guest*), Du khách (*Tourist*), Người dân bản địa (*Host*) và Quản trị viên (*Admin*) thông qua tầng Middleware lọc trước khi vào Controller.
2. **Bảo mật CSRF Kép (Double Token CSRF Protection):** Kiểm tra bắt buộc trên mọi yêu cầu thay đổi trạng thái (`POST`, `PUT`, `DELETE`). Hỗ trợ nhận diện token qua cả trường ẩn trong form `<input name="_csrf_token">` và HTTP Request Header `X-CSRF-TOKEN` của các tác vụ AJAX.
3. **Mã hóa mật khẩu chuẩn Bcrypt:** Mật khẩu người dùng được băm bằng thuật toán một chiều `password_hash($password, PASSWORD_BCRYPT)` và so khớp bằng `password_verify()`.
4. **Phòng chống tấn công XSS (Cross-Site Scripting):** Sử dụng hàm helper `e()` bọc quanh `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` trên toàn bộ các điểm xuất dữ liệu ra View.
5. **Cơ chế giữ chỗ & Chống xung đột (Concurrency Lock):** Khi du khách đặt tour, hệ thống kích hoạt cơ chế khóa giữ chỗ tạm thời trong 15 phút. Nếu sau 15 phút không thanh toán hoặc Host không duyệt, chỗ sẽ tự động được giải phóng cho du khách khác.
6. **Quy trình hủy tour và hoàn tiền theo mốc 24h (Chính sách T24):** Stored Procedure `ndtt_sp_bookings_cancel` tự động tính khoảng cách giữa thời điểm hủy và giờ khởi hành: nếu hủy trước 24 giờ thì duyệt lệnh hoàn tiền 100%, nếu hủy trong vòng 24 giờ thì phạt toàn bộ tiền cọc để bảo vệ quyền lợi của Host bản địa.
7. **Biên nhận điện tử có thể in trực tiếp (Printable E-Receipt):** Trang [app/views/receipts/show.php](file:///E:/Dev/Study'/Web/OnlineTraveller/app/views/receipts/show.php) thiết kế bố cục chuẩn in ấn A4, tích hợp CSS `@media print` và nút bấm in hóa đơn trực tiếp bằng lệnh JavaScript `window.print()`.
8. **Hệ thống Kiểm thử tự động toàn diện (Automated Test Suites):** Toàn bộ dự án được bảo chứng chất lượng bởi 22 tệp kịch bản kiểm thử tự động với hơn 400 assertions, chạy trực tiếp từ dòng lệnh qua bộ điều phối [tests/run_all_tests.php](file:///E:/Dev/Study'/Web/OnlineTraveller/tests/run_all_tests.php).

---

## 3. BẢNG ĐỐI CHIẾU KỸ THUẬT DỰ ÁN VỚI BÀI GIẢNG CỦA GIẢNG VIÊN

Dưới đây là phần so sánh chi tiết giữa **Kỹ thuật được trình bày trong các Slide bài giảng của Thầy (GV Đặng Ngọc Hoàng Thành - UEH)** và **Kỹ thuật đã được triển khai thực tế trong mã nguồn đồ án Online Traveller**.

| TT | Hạng Mục Kỹ Thuật | Kỹ Thuật Trong Slide Bài Giảng Của Thầy | Kỹ Thuật Triển Khai Trong Đồ Án Thực Tế | Đánh Giá Tương Đồng / Sai Lệch |
|:--:|:---|:---|:---|:---|
| **1** | **Quản lý Session** | - Gọi hàm `session_start()` ở đỉnh từng file riêng lẻ (`index.php`, `make.php`, `del.php`).<br>- Lưu dữ liệu mảng vào `$_SESSION['tên']` qua hàm tự viết `loadData()` & `addData()` trong file `session_module.php`.<br>- Hủy session bằng `unset($_SESSION['...'])`. | - Gọi `session_start()` tập trung duy nhất tại [bootstrap/app.php](file:///E:/Dev/Study'/Web/OnlineTraveller/bootstrap/app.php) với tùy chọn an toàn `cookie_httponly => true`, `cookie_samesite => 'Lax'`.<br>- Lưu thông tin phiên: `$_SESSION['user']`, `$_SESSION['flash']`, `$_SESSION['csrf_token']`.<br>- Tự động xóa flash bằng `unset($_SESSION['flash'])` sau khi đọc. | **GIỐNG VỀ NGUYÊN LÝ NỀN TẢNG** (`session_start`, `$_SESSION`, `unset`).<br>**KHÁC BIỆT CÁCH TỔ CHỨC:** Đồ án không dùng `session_module.php` mà quản lý phiên tập trung tại tầng Bootstrap và đóng gói qua Controller/AuthService. |
| **2** | **Quản lý Cookie** | - Dùng hàm `setcookie($tên, $giá_trị, $chu_kỳ_sống, $path, $domain)`.<br>- Đọc qua `$_COOKIE['tên']`.<br>- Xóa cookie bằng cách đặt thời gian quá khứ `time()-3600`.<br>- Lưu đối tượng/mảng vào cookie thông qua tuần tự hóa `serialize()` và `unserialize()`.<br>- Viết thành file module `cookie_module.php`. | - **HOÀN TOÀN KHÔNG ÁP DỤNG** `setcookie()` hoặc biến siêu toàn cục `$_COOKIE` tự tạo trong bất kỳ chức năng nào.<br>- Hệ thống chỉ dựa vào Cookie phiên mặc định (`PHPSESSID`) do PHP tự động sinh ra và quản lý. | **CHƯA GIỐNG / KHÔNG TRIỂN KHAI**.<br>Đồ án không áp dụng kỹ thuật tạo và đọc Cookie tùy biến như bài giảng của Thầy. |
| **3** | **HTML Form & Upload dữ liệu** | - Form method `POST`, `enctype="multipart/form-data"`.<br>- Tạo file `upload_module.php` chứa 2 hàm: `uploadFileTo($uploaddir, &$oldfilename)` (giữ nguyên tên) và `uploadAndRenameFile($uploaddir, $newfilename)` (đổi tên).<br>- Dùng `move_uploaded_file()`, lấy kích thước bằng `filesize()`, lấy MIME bằng `mime_content_type()`.<br>- Thao tác file văn bản qua `fopen`, `fread`, `fwrite`, `fclose`, `rename`, `unlink`. | - Form method `POST`, `enctype="multipart/form-data"`.<br>- Dùng `move_uploaded_file()` di chuyển tệp.<br>- **Không dùng** `upload_module.php` hay các hàm `fopen`/`fread`/`fwrite`.<br>- Logic upload được đóng gói thành phương thức OOP trong `TourService` và `Post`: thẩm định MIME thực tế bằng đối tượng `finfo(FILEINFO_MIME_TYPE)`, giới hạn size 5MB, tự động đổi tên ngẫu nhiên bằng `bin2hex(random_bytes(8))`. | **GIỐNG VỀ CƠ CHẾ HTTP FORM & MOVE_UPLOADED_FILE**.<br>**KHÁC BIỆT CẤU TRÚC:** Đồ án không dùng file `upload_module.php` theo dạng hàm thủ tục mà chuyển thành Service/Model OOP; dùng `finfo` hiện đại thay vì `mime_content_type`. Không sử dụng `fopen`/`fread` cho upload. |
| **4** | **Phương thức truyền dữ liệu** | - Hướng dẫn chi tiết `$_GET` (qua query string URL `?a=b&c=d`) và `$_POST` (ngầm qua HTTP body).<br>- Các loại thẻ input: text, password, date, time, week, month, color, number, range, hidden, checkbox (`mau[]`), radio, image (`vn.x, vn.y`), select/option, textarea. | - Áp dụng đầy đủ `$_GET` và `$_POST`.<br>- Bổ sung thêm các phương thức hiện đại: nạp luồng nhị phân JSON qua `php://input`, giả lập phương thức `_method` (PUT, DELETE), trích xuất tham số định tuyến Route Param (`/services/{id}`) và đọc HTTP Headers (`X-CSRF-TOKEN`). | **GIỐNG HOÀN TOÀN VỀ CƠ BẢN**.<br>**MỞ RỘNG:** Đồ án có thêm các kỹ thuật truyền dữ liệu nâng cao chuẩn Web API (JSON body, Method Spoofing, Header CSRF). |
| **5** | **Kết nối DB & Truy vấn SQL** | - Cấu hình hằng số `define()` trong `config.php`.<br>- Dùng thư viện **MySQLi** thủ tục trong `db_module.php`: `mysqli_connect()`, `chayTruyVanTraVeDL()`, `chayTruyVanKhongTraVeDL()`, `giaiPhongBoNho()`.<br>- Duyệt dữ liệu bằng các hàm: `mysqli_fetch_assoc()`, `mysqli_fetch_array()`, `mysqli_fetch_object()`, `mysqli_fetch_row()`.<br>- Truy vấn trực tiếp các câu lệnh SQL tĩnh: `SELECT`, `INSERT`, `UPDATE`, `DELETE`. | - Dùng thư viện **PDO (PHP Data Objects)** hiện đại với mẫu thiết kế **Singleton** trong [core/Database.php](file:///E:/Dev/Study'/Web/OnlineTraveller/core/Database.php).<br>- Cấu hình mảng trả về trong `config/database.php`.<br>- Sử dụng **Prepared Statements** chống SQL Injection.<br>- **Không viết SQL CRUD phân tán trong code PHP:** Toàn bộ nghiệp vụ nhạy cảm chuyển thành **23 MariaDB Stored Procedures** và gọi thông qua lệnh `CALL sp_name(?, ?)` kết hợp `nextRowset()` giải phóng con trỏ. | **CHƯA GIỐNG VỀ THƯ VIỆN & CÁCH TRUY VẤN**.<br>- Bài giảng: Dùng thư viện `mysqli_*` dạng procedural + hàm bọc `db_module.php` + viết câu lệnh SQL thô.<br>- Đồ án: Dùng thư viện `PDO` dạng Class Singleton + Native Prepared Statements + 23 Stored Procedures. |
| **6** | **Kỹ thuật Phân trang** | - Bước 1: `select count(*) from tbl_sanpham`.<br>- Bước 2: Hằng số `SO_SP_TREN_TRANG` trong `config.php`.<br>- Bước 3: Tính tổng số trang bằng công thức `$total = ceil(Tổng / SO_SP)`.<br>- Bước 4: Tạo link phân trang `<a href="./?page=i">`.<br>- Bước 5: Cắt dữ liệu bằng SQL `LIMIT $from, SO_SP` với `$from = ($page - 1) * SO_SP`. | - Công thức tính tổng trang: `$totalPages = max(1, (int)ceil($totalItems / $perPage))` **HOÀN TOÀN TRÙNG KHỚP** công thức của Thầy.<br>- Công thức tính vị trí: `$offset = ($currentPage - 1) * $perPage` **HOÀN TOÀN TRÙNG KHỚP**.<br>- Hiển thị nút bấm liên kết URL `page` trên giao diện HTML **HOÀN TOÀN TRÙNG KHỚP**.<br>- **Khác biệt tại nguồn cắt:** Do trang Bản tin kết hợp cả bài viết trong DB và dòng tin tức từ RSS VnExpress, việc cắt trang được thực hiện bằng hàm PHP `array_slice($allItems, $offset, $perPage)` thay vì lệnh `LIMIT` trực tiếp trong SQL. | **GIỐNG VỀ NGUYÊN LÝ TOÁN HỌC & THUẬT TOÁN (CEIL, OFFSET)**.<br>**KHÁC BIỆT CƠ CHẾ LẤY DỮ LIỆU:** Slide của Thầy cắt trang ở tầng CSDL bằng `SQL LIMIT`; Đồ án cắt trang ở tầng ứng dụng bằng `array_slice()` vì phải hợp nhất 2 nguồn dữ liệu (Database + RSS XML). |
| **7** | **Giỏ hàng (Cart Module)** | - Khởi tạo giỏ hàng trong session `$_SESSION['giohang'] = array(...)`.<br>- Tạo file `cart_module.php` chứa các hàm: `themhangvaogio($hang)`, `xoahangkhoigio($key)`, `capnhathangtronggio($key, $soluong)`, `tinhtien()`.<br>- Hiển thị giỏ hàng, cập nhật số lượng và tổng tiền. | - **ĐỒ ÁN KHÔNG TRIỂN KHAI GIỎ HÀNG NHIỀU MÓN (ECOMMERCE CART)**.<br>- Do đặc thù là nền tảng Du lịch trải nghiệm (Online Travel Platform), dự án triển khai quy trình **Đặt tour trực tiếp (Direct Booking Flow)**: Du khách chọn tour $\to$ Chọn lịch khởi hành (Schedule) $\to$ Đặt chỗ kèm số lượng người $\to$ Khóa giữ chỗ 15 phút $\to$ Host duyệt $\to$ Thanh toán MoMo / Xuất biên nhận điện tử E-Receipt. | **KHÔNG ÁP DỤNG / KHÁC BIỆT MÔ HÌNH NGHIỆP VỤ**.<br>Đồ án không áp dụng giỏ hàng dạng `cart_module.php` của website bán hàng thương mại điện tử, mà thay thế bằng mô hình Đặt chỗ tour trực tiếp có quản lý sức chứa và thời gian khởi hành. |
| **8** | **Lập trình Hướng đối tượng (OOP)** | - Hướng dẫn tạo Class, khởi tạo Object (`new`).<br>- Thuộc tính private/protected, getter, setter, hàm khởi tạo `__construct()`, hàm chuyển chuỗi `__toString()`.<br>- Kế thừa: `class Teacher extends Human`.<br>- Lớp trừu tượng: `abstract class AHuman`, phương thức trừu tượng `abstract function showinfo()`.<br>- Giao diện: `interface IPhone`, `interface ISmarter`, `class SmartPhone implements IPhone, ISmarter`. | - Áp dụng toàn diện kiến trúc OOP chuẩn PSR-4 trên 100% tệp Backend.<br>- Kế thừa: `BaseController` (kế thừa bởi tất cả Controller), `BaseModel` (kế thừa bởi tất cả Model).<br>- Lớp trừu tượng: `abstract class BaseController`, `abstract class BaseModel`.<br>- Interface: `interface Middleware` định nghĩa contract `handle()` được thực thi bởi 5 lớp Middleware con (`AuthMiddleware`, `RoleMiddleware`, `CsrfMiddleware`, `HostMiddleware`, `AdminMiddleware`). | **HOÀN TOÀN GIỐNG VÀ ĐẦY ĐỦ CÁC THÀNH PHẦN OOP**.<br>Đồ án áp dụng đầy đủ toàn bộ các khái niệm: Class, Object, Encapsulation, Inheritance, Abstraction, Interface/Polymorphism đúng như nội dung bài giảng. |
| **9** | **Mô hình MVC** | - Bài giảng hướng dẫn mô hình MVC cơ bản gồm 3 thư mục: `models/` (chứa `book.php`, `model.php`), `views/` (chứa `booklist.php`, `viewbook.php`), `controllers/` (chứa `controller.php` có hàm `invoke()`), và `index.php` gọi `$controller->invoke()`.<br>- Model tương tác với CSDL qua `db_module.php`. | - Áp dụng mô hình MVC hoàn chỉnh cấp doanh nghiệp:<br>  + **Front Controller:** Duy nhất tại [public/index.php](file:///E:/Dev/Study'/Web/OnlineTraveller/public/index.php).<br>  + **Routing:** `core/Router` hỗ trợ RESTful routes động thay vì cơ chế `invoke()` đơn giản.<br>  + **Controller:** Chia theo từng domain chuyên biệt (`HomeController`, `ServiceController`, `BookingController`...).<br>  + **Model:** Kế thừa `BaseModel` và gọi 23 Stored Procedures.<br>  + **View:** Phân tách thư mục rõ ràng theo thực thể, hỗ trợ layout khung vỏ dùng chung (`layouts/main.php`) và các phần tử con (`partials/`). | **GIỐNG VỀ TƯ TƯỞNG CỐT LÕI CỦA MVC**.<br>**MỞ RỘNG & CHUYÊN NGHIỆP HƠN:** Đồ án không dùng một file `controller.php` gộp chung mà phân tách chuyên biệt theo domain, bổ sung Router động, Request/Response và Middleware pipeline. |
| **10** | **Tích hợp AJAX** | - Tạo bảng `tbl_user`.<br>- Viết code JavaScript phía client sử dụng đối tượng thuần **`XMLHttpRequest`**.<br>- Bắt sự kiện `xmlhttp.onreadystatechange = function()`.<br>- Kiểm tra trạng thái `this.readyState == 4 && this.status == 200`.<br>- Gửi yêu cầu qua `xmlhttp.open("GET", "getuser.php?id=" + str, true)` và `xmlhttp.send()`.<br>- Phía server (`getuser.php`) truy vấn CSDL và in trực tiếp đoạn mã HTML `<table>...</table>` để gán vào thuộc tính `innerHTML`. | - **Không dùng đối tượng thuần `XMLHttpRequest`**.<br>- Thay thế bằng **Modern JavaScript Fetch API** kết hợp cú pháp đồng bộ `async/await`.<br>- Truyền tải và nhận dữ liệu hai chiều chuẩn **JSON** (qua `res.json()`) thay vì trả về cả cục mã HTML thô.<br>- Gửi kèm HTTP Header nhận diện `X-Requested-With: XMLHttpRequest` và CSRF Token bảo mật.<br>- Ứng dụng trong thực tế: Gửi câu hỏi Q&A, trả lời bình luận, đánh giá sao, tính báo giá tự động khi đổi số người, nạp dữ liệu trạm 3D cho Three.js. | **GIỐNG VỀ MỤC TIÊU & TƯ TƯỞNG ASYNCHRONOUS (KHÔNG TẢI LẠI TRANG)**.<br>**KHÁC BIỆT CÔNG NGHỆ THỰC THI:**<br>- Bài giảng của Thầy: Dùng `XMLHttpRequest` thuần, nhận phản hồi là đoạn mã HTML thô và gán vào `innerHTML`.<br>- Đồ án: Dùng `Fetch API + async/await`, trao đổi dữ liệu chuẩn JSON hai chiều. |

---

## 4. TỔNG KẾT NHẬN ĐỊNH CHO BÁO CÁO ĐỒ ÁN

1. **Về mức độ đáp ứng chuẩn kiến thức môn học:** Đồ án đã vận dụng xuất sắc và nâng cao hầu hết các trụ cột kiến thức then chốt: **Bootstrap Grid Responsive, Lập trình hướng đối tượng (OOP), Mô hình kiến trúc MVC, Xử lý Form và Upload tệp đa phần, Quản lý phiên làm việc Session, Kỹ thuật phân trang toán học (Ceil & Offset), và Tương tác không đồng bộ AJAX**.
2. **Các điểm khác biệt mang tính cải tiến/nâng cấp:**
   - **Tầng CSDL:** Nâng cấp từ thư viện `mysqli` thủ tục sang đối tượng kết nối `PDO` chuẩn Singleton, đồng thời đưa toàn bộ 23 quy trình nghiệp vụ vào **Stored Procedures** thay cho các câu lệnh SQL viết trực tiếp trong PHP.
   - **Tầng Giao tiếp AJAX:** Nâng cấp từ `XMLHttpRequest` cổ điển nhận HTML sang `Fetch API` hiện đại trao đổi dữ liệu chuẩn `JSON`.
   - **Tầng Điều hướng:** Nâng cấp từ cơ chế include trang thủ công sang bộ định tuyến động `Router` và đường ống `Middleware` bảo mật.
3. **Các điểm bài giảng có nhưng đồ án không triển khai:**
   - **Không triển khai Cookie tùy biến (`setcookie`, `$_COOKIE`, `serialize`)**: Hệ thống bảo mật thông tin phiên bằng Session phía server để tránh rủi ro giả mạo client.
   - **Không triển khai Giỏ hàng nhiều món (`cart_module.php`)**: Thay thế bằng quy trình Đặt tour giữ chỗ trực tiếp (Direct Booking with Schedule & Capacity Locking) phù hợp với bản chất ngành dịch vụ du lịch.
