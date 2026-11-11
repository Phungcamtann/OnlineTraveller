# TÀI LIỆU KỸ THUẬT & HƯỚNG DẪN KIỂM THỬ GIAI ĐOẠN 6
## BỘ LỌC KHOẢNG GIÁ RANGE SLIDER, ĐỒNG BỘ CARD TOUR & SLIDER BANNER THUMBNAILS

> **Phiên bản:** 1.0  
> **Trạng thái:** ✅ **HOÀN THÀNH 100% (26/26 Assertions PASSED)**  
> **Master Test Suite:** 20/20 Suites PASSED (362/362 assertions, 100%)  
> **Tác giả:** Antigravity AI & Nhóm Phát Triển OnlineTraveller  

---

## 1. TỔNG QUAN YÊU CẦU & CẢI TIẾN

Dựa trên phản hồi thực tế của người dùng sau khi kiểm thử giao diện và kế hoạch tại [`docs/plan.md`](file:///e:/Dev/Study'/Web/OnlineTraveller/docs/plan.md), Giai đoạn 6 đã giải quyết 3 nhóm vấn đề then chốt:

1. **Thanh Trượt Khoảng Giá (Range Slider) Tại Trang Chủ & Trang Khám Phá:**
   - Thay thế việc chọn khoảng giá tĩnh bằng thanh trượt ngang `200.000đ -> 5.000.000đ+` (bước nhảy `100.000đ`).
   - Nhãn hiển thị số tiền nhảy động real-time theo vị trí kéo trượt (`X.XXX.XXX ₫` hoặc `5.000.000 ₫+`).
   - Tương thích ngược 100% giữa tham số `max_price` và `price_range` (`under_500`, `500_1000`, `over_1000`).

2. **Đồng Bộ Hoàn Toàn Card Tour Dark-Alpine Tại Trang Khám Phá (`/services`):**
   - Chuyển đổi toàn bộ danh sách tour từ card trắng kiểu cũ sang **Card Dark-Alpine tỷ lệ 3/4 tràn ảnh (Poster Card)** đồng bộ 100% với Trang chủ.
   - Thêm huy hiệu danh mục góc trên trái và số sao đánh giá góc trên phải (`glass-pill`).
   - Lớp phủ overlay chân ảnh hiển thị địa danh, tên tour, Host, thời lượng và đơn giá nổi bật màu `text-info` kèm ghi chú *"Đã gồm 8% VAT"*.
   - Đưa bộ lọc tìm kiếm vào khối kính mờ `glass-panel` sang trọng.

3. **Bố Cục Lại Banner & Slider Banner Tương Tác:**
   - **Trang chủ (`home/index.php`):** Nâng cấp Hero Banner thành Slider Banner tương tác với dải thumbnails thu nhỏ (`banner-thumbs-container`), hỗ trợ click chuyển slide tức thì, nút điều hướng Prev/Next và tự động trượt sau mỗi 6 giây (tạm dừng khi rê chuột).
   - **Trang chi tiết tour (`services/show.php`):** Khắc phục lỗi mất chân ảnh banner nhờ `background-position: center bottom` và `background-size: cover`. Bố cục lại thành 2 cột cân xứng: Cột trái chữ + Host + nút Đặt Lịch Ngay, Cột phải là dải card các trạm dừng chân xếp dọc (`vertical-station-card`) chuẩn ảnh mẫu.

---

## 2. KIẾN TRÚC & CÁC TỆP TIN THAY ĐỔI

```
OnlineTraveller/
├── public/
│   └── assets/css/
│       └── custom-theme.css                # Style .custom-price-range, .banner-thumb-item, .vertical-station-card
├── app/
│   ├── models/
│   │   └── Service.php                     # getAllApproved hỗ trợ tham số maxPrice và minPrice
│   ├── controllers/
│   │   └── ServiceController.php           # index trích xuất max_price, hỗ trợ tương thích ngược price_range
│   └── views/
│       ├── home/
│       │   └── index.php                   # Slider Banner tương tác với thumbnails & Range Slider
│       ├── services/
│       │   ├── index.php                   # Range Slider & Khám phá card Dark-Alpine 3/4 poster
│       │   └── show.php                    # Banner 2 cột: Cột trái chữ, cột phải trạm dọc
├── tests/
│   ├── test_phase6_slider_cards_banner.php # Test suite chuyên biệt 26 assertions
│   └── run_all_tests.php                   # Tích hợp vào Master Runner (20 suites, 362 assertions)
└── docs/
    ├── plan.md                             # Cập nhật trạng thái hoàn thành Giai đoạn 6
    └── phase6_slider_cards_banner.md       # Tài liệu kỹ thuật chi tiết
```

---

## 3. CHI TIẾT CÁC THAY ĐỔI KỸ THUẬT

### 3.1. Backend Lọc Ngân Sách

Trong [`app/models/Service.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/models/Service.php):
```php
public function getAllApproved(
    ?int $categoryId = null,
    ?string $keyword = null,
    int $limit = 12,
    ?int $maxPrice = null,
    ?int $minPrice = null
): array
```
Khi người dùng kéo trượt chọn ngân sách tối đa, câu truy vấn SQL tự động áp dụng điều kiện:
```sql
AND s.base_price <= ?
```
Trong [`app/controllers/ServiceController.php`](file:///e:/Dev/Study'/Web/OnlineTraveller/app/controllers/ServiceController.php):
- Trích xuất `max_price` từ `Request`.
- Nếu có `price_range` cũ (`under_500`, `500_1000`, `over_1000`), hệ thống tự động ánh xạ sang cận giá trị tương ứng để đảm bảo mọi liên kết cũ hoặc bookmark đều hoạt động mượt mà.

---

### 3.2. Range Slider Trên Giao Diện

- **HTML Range Slider:**
```html
<input type="range" 
       class="form-range custom-price-range" 
       id="home-price-slider" 
       name="max_price" 
       min="200000" 
       max="5000000" 
       step="100000" 
       value="5000000">
```
- **CSS Giao Diện Kính Mờ & Đầu Kéo Neon (`custom-theme.css`):**
```css
.custom-price-range {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 6px;
  background: rgba(255, 255, 255, 0.22);
  border-radius: 999px;
  outline: none;
  cursor: pointer;
}
.custom-price-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #5ba3c6;
  border: 2px solid #ffffff;
  box-shadow: 0 0 10px rgba(91, 163, 198, 0.7);
}
```
- **JavaScript Cập Nhật Số Tiền Tức Thì:**
```javascript
slider.addEventListener('input', function() {
    const num = parseInt(this.value, 10);
    display.textContent = (num >= 5000000) 
        ? '5.000.000 ₫+' 
        : new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
});
```

---

### 3.3. Đồng Bộ Card Tour Khám Phá

Trước đây trang Khám phá sử dụng card trắng (`bg-white`), gây mất tính liền mạch với phong cách Dark-Alpine. Nay toàn bộ danh sách tour được đưa về dạng **Poster Card tràn ảnh 3/4**:
- Thẻ chính: `<div class="card-tour experience-poster-card shadow">`
- Ảnh tour tràn khung, zoom nhẹ 1.04 khi hover.
- Huy hiệu danh mục và số sao góc trên dạng viên thuốc kính mờ `glass-pill`.
- Overlay chân ảnh hiển thị địa danh, thời lượng, Host, giá tiền `text-info` và ghi chú thuế VAT rõ ràng.

---

### 3.4. Slider Banner & Dải Trạm Dọc

1. **Trang Chủ (`home/index.php`):**
   - Hỗ trợ hiển thị 4 tour tiêu biểu trong slider.
   - Dải thumbnails thu nhỏ (`banner-thumb-item`) nằm gọn gàng bên dưới banner.
   - Nhấp vào thumbnail sẽ chuyển đổi tức thì ảnh nền, ambient glow, tiêu đề, mô tả và liên kết khám phá.
   - Tự động chuyển slide sau mỗi 6 giây và tạm dừng khi người dùng đưa chuột vào.

2. **Trang Chi Tiết Tour (`services/show.php`):**
   - Sửa lỗi mất chân ảnh: `min-height: 540px; background-position: center bottom; background-size: cover;`.
   - Bố cục 2 cột cân xứng:
     - **Cột trái (`col-lg-7`):** Tiêu đề tour lớn, Host info, điểm nổi bật và nút CTA *"Đặt Lịch Ngay"*.
     - **Cột phải (`col-lg-5`):** Khối kính mờ chứa dải card các trạm dừng chân xếp dọc (`vertical-station-card`), mỗi trạm có ảnh thu nhỏ sắc nét, số thứ tự *"Trạm X"* và tên trạm, click vào sẽ cuộn mượt xuống trạm tương ứng.

---

## 4. KẾT QUẢ KIỂM THỬ TỰ ĐỘNG

Chạy bộ kiểm thử chuyên biệt Phase 6:
```bash
C:\xampp\php\php.exe tests/test_phase6_slider_cards_banner.php
```
Kết quả:
```
====================================================================================
   PHASE 6: KIỂM THỬ RANGE SLIDER, ĐỒNG BỘ CARD TOUR & SLIDER BANNER THUMBNAILS    
====================================================================================

--- 1. Backend Lọc Theo Ngân Sách Tối Đa (Range Slider) ---
  [PASS] Assertion #1: Service::getAllApproved lọc chính xác tour có base_price <= 500.000đ
  [PASS] Assertion #2: Service::getAllApproved lọc chính xác tour có base_price <= 1.000.000đ
  [PASS] Assertion #3: ServiceController::index nhận query max_price và render đúng giá trị vào slider
  [PASS] Assertion #4: ServiceController::index tương thích ngược với price_range=under_500

--- 2. Giao Diện Range Slider (home/index.php & services/index.php) ---
  [PASS] Assertion #5: Trang chủ có Range Slider ngân sách với id='home-price-slider' và name='max_price'
  [PASS] Assertion #6: Trang chủ có nhãn hiển thị số tiền nhảy động id='home-price-display'
  [PASS] Assertion #7: Trang chủ có JS event listener 'input' cập nhật giá trị số tiền nhảy động
  [PASS] Assertion #8: Trang khám phá có Range Slider ngân sách với id='service-price-slider'
  [PASS] Assertion #9: Trang khám phá có nhãn hiển thị số tiền nhảy động id='service-price-display'
  [PASS] Assertion #10: CSS custom-price-range được định nghĩa cho giao diện Dark-Alpine

--- 3. Đồng Bộ Card Tour Dark-Alpine Tại Trang Khám Phá ---
  [PASS] Assertion #11: Trang khám phá dùng class 'experience-poster-card' tỷ lệ 3/4 tràn ảnh
  [PASS] Assertion #12: Trang khám phá dùng lớp phủ 'experience-poster-overlay' chân ảnh
  [PASS] Assertion #13: Card tour hiển thị huy hiệu danh mục góc trên trái (glass-pill)
  [PASS] Assertion #14: Card tour hiển thị số sao đánh giá góc trên phải (glass-pill)
  [PASS] Assertion #15: Card tour hiển thị giá tiền nổi bật màu text-info và ghi chú thuế VAT
  [PASS] Assertion #16: Bộ lọc trang khám phá dùng glass-panel mờ kính sang trọng

--- 4. Slider Banner Thumbnails Tại Trang Chủ ---
  [PASS] Assertion #17: Trang chủ có container thumbnails id='bannerThumbsContainer'
  [PASS] Assertion #18: Các thumbnail chứa đầy đủ data attributes (data-slide-index, data-image, data-title)
  [PASS] Assertion #19: Trang chủ có 2 nút điều hướng chuyển slide (btnPrevSlide & btnNextSlide)
  [PASS] Assertion #20: Có JavaScript điều khiển chuyển slide mượt mà và tự động đổi slide theo chu kỳ
  [PASS] Assertion #21: CSS banner-thumb-item hỗ trợ trạng thái active và hover kính mờ

--- 5. Bố Cục Lại Banner & Dải Trạm Xếp Dọc (services/show.php) ---
  [PASS] Assertion #22: Banner chi tiết đặt background-position: center bottom để không bị mất chân ảnh
  [PASS] Assertion #23: Bố cục Banner chia 2 cột rõ ràng: Cột trái chữ + host, Cột phải dải card trạm dừng
  [PASS] Assertion #24: Dải trạm dừng bên phải sử dụng class 'vertical-station-card'
  [PASS] Assertion #25: Mỗi thẻ trạm có ảnh thu nhỏ, số thứ tự 'Trạm X' và tên trạm
  [PASS] Assertion #26: CSS vertical-station-card được thiết kế với hiệu ứng hover và backdrop-filter

====================================================================================
KẾT QUẢ KIỂM THỬ: 26 / 26 assertions đạt chuẩn (100%)
>>> XÁC NHẬN: GIAI ĐOẠN 6 ĐÃ HOÀN THÀNH VÀ ĐẠT CHUẨN 100%! <<<
====================================================================================
```

Chạy toàn bộ hệ thống kiểm thử:
```bash
C:\xampp\php\php.exe tests/run_all_tests.php
```
**Tổng kết:** **20/20 Suites PASS**, **362/362 Assertions (100.0%)**.

---

## 5. HƯỚNG DẪN KIỂM THỬ THỦ CÔNG (DÀNH CHO NGƯỜI DÙNG)

1. **Kiểm tra Slider Banner tại Trang Chủ (`/`):**
   - Truy cập trang chủ `http://localhost:8000/` (hoặc cổng web dev).
   - Quan sát Hero Banner:
     - Dải thumbnail nhỏ hiển thị phía dưới banner.
     - Nhấp vào từng thumbnail $\rightarrow$ Banner đổi ảnh nền, hiệu ứng ambient glow và tiêu đề tương ứng.
     - Nhấp nút Prev/Next tròn $\rightarrow$ Chuyển qua lại các tour mượt mà.
     - Chờ 6 giây $\rightarrow$ Slide tự động chuyển tiếp.

2. **Kiểm tra Range Slider Ngân Sách (`/` & `/services`):**
   - Tại thanh tìm kiếm Trang chủ: Kéo thanh trượt $\rightarrow$ Nhãn tiền nhảy động từ `200.000 ₫` đến `5.000.000 ₫+`.
   - Kéo về mức 500.000đ và bấm **Tìm kiếm** $\rightarrow$ Hệ thống chuyển sang trang `/services?max_price=500000` và chỉ hiển thị các tour dưới 500k.
   - Tại trang `/services`: Kéo thanh trượt lên 1.000.000đ và bấm **Lọc Kết Quả** $\rightarrow$ Danh sách cập nhật các tour dưới 1 triệu.

3. **Kiểm tra Giao Diện Card Tour Khám Phá (`/services`):**
   - Quan sát danh sách tour: Toàn bộ card đã chuyển sang tone Dark-Alpine tràn ảnh 3/4.
   - Hover chuột vào card $\rightarrow$ Card nhấc lên nhẹ nhàng (`hover-lift`), ảnh zoom nhẹ.
   - Hiển thị đầy đủ huy hiệu danh mục, rating sao, Host, địa danh, giá tiền xanh neon và nhãn *"Đã gồm 8% VAT"*.

4. **Kiểm tra Banner Chi Tiết Tour (`/services/1`):**
   - Truy cập vào tour chi tiết bất kỳ.
   - Kiểm tra ảnh banner: Chân ảnh không còn bị cắt xén, giữ trọn khung cảnh bến thuyền và hồ nước.
   - Kiểm tra bố cục 2 cột:
     - Cột trái: Chữ tiêu đề lớn, Host info, tóm tắt và nút *"Đặt Lịch Ngay"*.
     - Cột phải: Dải card các trạm dừng chân xếp dọc gọn gàng với ảnh nhỏ và nhãn *"Trạm 1, 2, 3..."*.
     - Nhấp vào một trạm bất kỳ $\rightarrow$ Trang cuộn mượt xuống trạm dừng chân tương ứng.
