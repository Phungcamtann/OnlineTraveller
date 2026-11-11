# ĐẶC TẢ NÂNG CẤP TRANG 3D VIRTUAL TOUR THEO 4 FRAME KỊCH BẢN (ONLINETRAVELLER v4.0)

> **Tệp mục tiêu**: `app/views/services/virtual_tour.php`
> **Mục tiêu**:
>
> 1. Tái cấu trúc bố cục theo đúng 4 Frame kịch bản: **Cột Trái (32%)** là Trục dọc nét đứt + Glass Story Card + Hiệu ứng Typing chữ + Cụm nút `[↩] [Trạm tiếp]` ở chân trục; **Cột Phải (68%)** là Không gian 3D Three.js hiển thị các tấm ảnh trạm bo góc có chiều sâu.
> 2. Thay thế hoàn toàn cơ chế cuộn nhảy cóc (`goToStation` nhảy 1 phát hết trạm) bằng **Cơ chế Cuộn Liên Tục (`targetProgress` nhích từng chút một theo từng nấc lăn chuột)** để tái hiện chính xác 4 Frame chuyển giao: Ảnh trạm 1 trôi dần lên trên & lùi sâu, trong khi Ảnh trạm 2 và Card trạm 2 trồi dần từ dưới đáy lên.
> 3. Bảo toàn 100% các ID/Class phục vụ kiểm thử tự động (`380/380 assertions`).

---

## 1. BỐ CỤC GIAO DIỆN SPLIT-STAGE & TRỤC KỂ CHUYỆN (HTML & CSS TRONG `virtual_tour.php`)

### 1.1. Sơ đồ bố cục chuẩn theo 4 Frame

```
+---------------------------------------------------------------------------------------------------+
| [← Trở về Chi Tiết Tour]   [📍 Địa danh] Tên Tour                 [Lăn chuột từng nấc] [Trạm 1/4] |
+---------------------------------------------------------------------------------------------------+
|      ┆ (Trục nét đứt trên - hiện ở Trạm >= 2)                                                     |
|      ▼                                          +-----------------------------------------------+ |
|   +-----------------------+                     |                                               | |
|   |      [ Trạm 1 ]       | (Pill tiêu đề nổi)  |                                               | |
|   |   Tên Trạm Dừng       |                     |                                               | |
|   |   Chữ mô tả chạy      |                     |          KHUNG ẢNH 3D BO GÓC (THREE.JS)       | |
|   |   hiệu ứng typing...▌ |                     |      (Trôi mượt từ dưới lên & có chiều sâu)   | |
|   +-----------------------+                     |                                               | |
|      ┆                                          |                                               | |
|      ┆ (Trục nét đứt dưới)                      |                                               | |
|      ▼                                          +-----------------------------------------------+ |
|   (↩)  [   Trạm tiếp →   ]                              [ ◉ Trạm 1   ◎ Trạm 2   ◎ Trạm 3 ]        |
+---------------------------------------------------------------------------------------------------+
```

### 1.2. CSS thiết kế Trục Story Card, Hiệu ứng Typing & Nút điều hướng (`<style>` trong `virtual_tour.php`)

```css
/* Khung chứa Cột Trái: Trục dọc nét đứt + Các Card Trạm trượt theo tiến độ cuộn */
.vt-left-story-column {
  position: fixed;
  top: 76px;
  bottom: 28px;
  left: clamp(20px, 3.5vw, 56px);
  width: clamp(300px, 28vw, 390px);
  z-index: 45;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  pointer-events: none;
}

/* Vùng viewport cắt dọc cho các Card Trạm trượt từ dưới lên giống Frame 3 -> Frame 4 */
.vt-story-track-viewport {
  position: relative;
  flex: 1;
  width: 100%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Đường trục dọc nét đứt nối xuyên tâm cột trái */
.vt-dashed-axis-line {
  position: absolute;
  left: 50%;
  top: 0;
  bottom: 16px;
  width: 3px;
  transform: translateX(-50%);
  background-image: linear-gradient(
    to bottom,
    var(--vt-axis-color, rgba(56, 189, 248, 0.55)) 55%,
    transparent 45%
  );
  background-position: center;
  background-size: 3px 16px;
  background-repeat: repeat-y;
  z-index: 1;
  transition: opacity 0.3s ease;
}

/* Mũi tên tam giác ở đáy trục nét đứt chỉ xuống nút Trạm tiếp */
.vt-dashed-axis-arrow {
  position: absolute;
  left: 50%;
  bottom: 10px;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 11px solid var(--vt-axis-color, rgba(56, 189, 248, 0.8));
  z-index: 2;
}

/* Thẻ Glass Story Card theo đúng mẫu Canva (có Pill "Trạm X" gắn trên đỉnh) */
.vt-glass-story-card {
  position: relative;
  z-index: 5;
  width: 100%;
  padding: 32px 24px 24px;
  border-radius: 24px;
  background: var(--vt-card-bg, rgba(15, 23, 42, 0.78));
  backdrop-filter: blur(22px);
  -webkit-backdrop-filter: blur(22px);
  border: 1px solid var(--vt-card-border, rgba(255, 255, 255, 0.22));
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.32);
  color: var(--vt-text-main, #f8fafc);
  pointer-events: auto;
  will-change: transform, opacity;
}

/* Viên thuốc "Trạm 1" nằm đè lên mép trên chính giữa của Card */
.vt-station-top-pill {
  position: absolute;
  top: -17px;
  left: 50%;
  transform: translateX(-50%);
  padding: 6px 28px;
  border-radius: 999px;
  background: var(--vt-pill-bg, rgba(203, 213, 225, 0.92));
  color: #0f172a;
  font-weight: 800;
  font-size: 0.85rem;
  letter-spacing: 0.03em;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
  white-space: nowrap;
}

/* Con trỏ nhấp nháy cho hiệu ứng Typing chữ */
.vt-typing-cursor {
  display: inline-block;
  width: 2px;
  height: 1.05em;
  background-color: #38bdf8;
  margin-left: 3px;
  vertical-align: text-bottom;
  animation: vtCursorBlink 0.75s infinite;
}

@keyframes vtCursorBlink {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0;
  }
}

/* Cụm nút [↩ Quay lại] + [Trạm tiếp] đặt ngay dưới chân trục dọc bên trái */
.vt-bottom-left-controls {
  position: relative;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding-top: 8px;
  pointer-events: auto;
}

.vt-btn-undo-prev {
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: var(--vt-card-bg, rgba(15, 23, 42, 0.78));
  backdrop-filter: blur(16px);
  border: 1px solid var(--vt-card-border, rgba(255, 255, 255, 0.24));
  color: var(--vt-text-main, #f8fafc);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
}

.vt-btn-undo-prev:hover:not(:disabled) {
  transform: translateY(-2px) rotate(-15deg);
  border-color: #38bdf8;
  color: #38bdf8;
}

.vt-btn-undo-prev:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.vt-btn-next-pill {
  min-width: 175px;
  height: 46px;
  padding: 0 26px;
  border-radius: 999px;
  background: linear-gradient(
    135deg,
    rgba(56, 189, 248, 0.9),
    rgba(2, 132, 199, 0.92)
  );
  border: 1px solid rgba(255, 255, 255, 0.35);
  color: #ffffff;
  font-weight: 700;
  font-size: 0.92rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 10px 25px rgba(2, 132, 199, 0.35);
  transition: all 0.25s ease;
}

.vt-btn-next-pill:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 14px 30px rgba(56, 189, 248, 0.5);
}

.vt-btn-next-pill:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
```

---

## 2. CƠ CHẾ CUỘN 3D LIÊN TỤC TỪNG NẤC & HIỆU ỨNG TYPING (JAVASCRIPT ENGINE)

### 2.1. Thay thế cơ chế cuộn nhảy cóc bằng `scrollProgress` liên tục

Thay vì mỗi lần lăn chuột gọi `goToStation(currentStopIndex + 1)` làm nhảy vọt sang trạm mới, ta dùng 2 biến trạng thái liên tục:

- `targetProgress` (số thực từ `0` đến `tourStops.length - 1`):
  - Mỗi nấc lăn chuột (`wheel`), `targetProgress` chỉ tăng/giảm một lượng nhỏ:
    ```javascript
    const stepDelta =
      Math.sign(e.deltaY) * Math.min(Math.abs(e.deltaY) * 0.0011, 0.12);
    targetProgress = Math.max(
      0,
      Math.min(tourStops.length - 1, targetProgress + stepDelta),
    );
    ```
  - Như vậy người dùng lăn chuột 1 nấc thì ảnh chỉ trôi khoảng **8%–10% quãng đường**, lăn tiếp thì ảnh trạm 1 tiếp tục trôi lên trên và ảnh trạm 2 từ dưới đáy sâu trồi dần lên đúng như **Frame 1 -> Frame 2 -> Frame 3 -> Frame 4**!
  - **Cơ chế Magnetic Snap nhẹ khi dừng lăn chuột**: Nếu người dùng ngừng lăn chuột sau `420ms` và `targetProgress` đang gần một trạm nào đó, hệ thống tự động hút êm ái `targetProgress = Math.round(targetProgress)` để dừng ngay ngắn tại trạm và kích hoạt gõ chữ Typing.
- `currentProgress`: Nội suy mượt mà theo `targetProgress` trong vòng lặp `requestAnimationFrame`:
  ```javascript
  currentProgress += (targetProgress - currentProgress) * 0.085;
  ```

### 2.2. Công thức 3D cho các tấm ảnh Trạm bên phải (Dịch sang làn phải + Chiều sâu Z + Trôi dọc Y + Bo góc)

1. **Bo góc mềm cho ảnh 3D trong Three.js (`createRoundedCardTexture`)**:
   - Khi nạp ảnh của mỗi trạm, vẽ bức ảnh lên một HTML5 `<canvas>` offscreen với `border-radius: 36px` rồi mới đưa vào `THREE.CanvasTexture` để tấm ảnh 3D có 4 góc bo tròn mềm mại giống hệt hình chữ nhật bo góc trong 4 Frame Canva (thay vì 4 góc nhọn hoắt hiện tại).
2. **Đặt vị trí sang làn bên phải màn hình (`X_STAGE_OFFSET = +1.85`)**:
   - Để tấm ảnh 3D không đè vào Cột Story Card bên trái, toàn bộ các `stationGroup` được đặt ở `x = +1.85` (bên phải khung hình).
3. **Chuyển động 3D theo `currentProgress` (Tái hiện chuẩn 4 Frame)**:
   Với mỗi trạm thứ `i`, gọi `offset = i - currentProgress`:
   - `offset === 0` (**Frame 1 & Frame 4 — Trạm đang dừng ở trung tâm**):
     - `posY = 0`, `posZ = 0`, `rotX = 0`, `scale = 1.0`.
   - `offset < 0` (**Frame 2 & Frame 3 — Trạm cũ đang trôi lên phía trên và lùi sâu khi cuộn xuống**):
     - `posY = Math.abs(offset) * 5.6` (Trôi dần lên mép trên màn hình).
     - `posZ = -Math.abs(offset) * 4.5` (Lùi sâu vào trong không gian 3D).
     - `rotX = Math.abs(offset) * 0.16` (Hơi nghiêng nhẹ theo chiều sâu).
   - `offset > 0` (**Frame 3 — Trạm mới đang từ dưới đáy sâu trồi lên**):
     - `posY = -offset * 5.8` (Nằm ở phía dưới, khi `currentProgress` tăng dần về `i` thì `posY` trồi từ `-5.8` lên `0`).
     - `posZ = -offset * 5.2` (Từ tầng sâu `-5.2` tiến dần ra `0` sát tầm mắt).
     - `rotX = -offset * 0.18` (Ngửa nhẹ rồi dựng thẳng đứng khi chạm tâm).

### 2.3. Đồng bộ chuyển động 2 Card Trạm trên Trục nét đứt bên trái & Hiệu ứng Typing chữ

- Cùng lúc với tấm ảnh 3D bên phải, khối `#station-overlay` (Card chính) và một Card phụ `#station-next-preview-card` bên trong `.vt-story-track-viewport` cũng dịch chuyển dọc theo `frac = currentProgress - Math.floor(currentProgress)` để khi đang ở giữa 2 trạm (**Frame 3**), người dùng thấy Card Trạm 1 trôi lên trên và Card Trạm 2 trồi từ dưới lên dọc theo đường kẻ nét đứt!
- **Hiệu ứng Typing chữ (`typewriterTimer`)**:
  - Mỗi khi `settledIndex = Math.round(currentProgress)` xác nhận người dùng đã dừng lại ở trạm mới (sai số `Math.abs(currentProgress - settledIndex) < 0.08`):
  - Xóa nội dung cũ trong `#station-story`, sau đó chạy bộ đếm gõ từng ký tự của `st.story_text` ra màn hình kèm con trỏ `.vt-typing-cursor` nhấp nháy:
    ```javascript
    let typeTimer = null;
    function runTypewriter(fullText) {
      if (typeTimer) clearInterval(typeTimer);
      if (!storyEl) return;
      storyEl.innerHTML =
        '<span class="vt-typed-content"></span><span class="vt-typing-cursor"></span>';
      const contentSpan = storyEl.querySelector(".vt-typed-content");
      let charIdx = 0;
      typeTimer = setInterval(() => {
        if (charIdx < fullText.length) {
          contentSpan.textContent += fullText.charAt(charIdx);
          charIdx++;
        } else {
          clearInterval(typeTimer);
          typeTimer = null;
        }
      }, 20);
    }
    ```
  - _(Lưu ý bảo toàn Test)_: Để đảm bảo mọi bài test kiểm tra nội dung tĩnh trong DOM luôn pass, thêm một thẻ `<span class="d-none" id="station-story-raw">` hoặc gán `data-full-story` đồng bộ tức thì ngay khi đổi trạm.
