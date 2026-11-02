-- ==============================================================================
-- DỮ LIỆU MẪU BAN ĐẦU CHO HỆ THỐNG ONLINETRAVELLER
-- Mật khẩu mặc định cho tất cả tài khoản mẫu: password123
-- Hash Bcrypt: $2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tài khoản mẫu
TRUNCATE TABLE tbl_users;
INSERT INTO tbl_users (id, role, name, email, password_hash, phone, status) VALUES
(1, 'admin', 'Quản Trị Viên Hệ Thống', 'admin@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0901234567', 'active'),
(2, 'host', 'Nguyễn Văn Bản Địa (Đà Lạt)', 'host.dalat@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0912345678', 'active'),
(3, 'host', 'Trần Thị Hướng Dẫn (Hội An)', 'host.hoian@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0923456789', 'active'),
(4, 'tourist', 'Lê Du Khách', 'tourist@gmail.com', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0934567890', 'active'),
(5, 'tourist', 'Phạm Thích Du Lịch', 'phamtourist@gmail.com', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0945678901', 'active');

-- 2. Danh mục tour
TRUNCATE TABLE tbl_categories;
INSERT INTO tbl_categories (id, name, slug, description, icon, is_active) VALUES
(1, 'Khám Phá Văn Hóa & Lịch Sử', 'van-hoa-lich-su', 'Trải nghiệm đời sống bản địa, làng nghề cổ truyền và di tích văn hóa lâu đời.', 'bi-bank', 1),
(2, 'Ẩm Thực Bản Địa Độc Đáo', 'am-thuc-ban-dia', 'Thưởng thức các món ngon truyền thống cùng người dân địa phương tại chợ và hẻm phố.', 'bi-cup-hot', 1),
(3, 'Trekking & Vùng Cao Hùng Vĩ', 'trekking-vung-cao', 'Chinh phục cung đường núi, đèo ngoạn mục, cắm trại săn mây tại Tây Bắc và Tây Nguyên.', 'bi-tree', 1),
(4, 'Biển Đảo & Nghỉ Dưỡng Sinh Thái', 'bien-dao-sinh-thai', 'Lặn ngắm san hô, du thuyền trên vịnh và trải nghiệm làng chài ven biển mộc mạc.', 'bi-water', 1);

-- 3. Tour trải nghiệm mẫu
TRUNCATE TABLE tbl_services;
INSERT INTO tbl_services (id, host_id, category_id, title, slug, description, price_mode, base_price, min_price, max_price, location, duration_days, banner_image, status) VALUES
(1, 2, 3, 'Săn Mây Đỉnh LangBiang & Thưởng Thức Cà Phê Cầu Đất', 'san-may-dinh-langbiang-ca-phe-cau-dat', 'Khám phá Đà Lạt buổi sớm mai tinh mơ, đón bình minh trên thảm mây bồng bềnh và học cách thu hoạch, pha chế cà phê Arabica Cầu Đất cùng nông dân bản địa.', 'fixed_choice', 750000, 0, 0, 'Da Lat', 1, '/assets/images/tours/dalat-san-may.jpg', 'approved'),
(2, 3, 1, 'Hội An Về Đêm: Đèn Lồng Sông Hoài & Học Làm Gốm Thanh Hà', 'hoi-an-ve-dem-den-long-gom-thanh-ha', 'Tản bộ phố cổ ngắm hoa đăng rực rỡ, nghe hát bài chòi dân gian, đi thuyền trên sông Hoài và tự tay nhào nặn chiếc bát gốm mang về làm kỷ niệm.', 'prepaid_full', 600000, 0, 0, 'Hoi An', 1, '/assets/images/tours/hoian-dem.jpg', 'approved'),
(3, 2, 2, 'Tour Ẩm Thực Phố Núi: Bánh Căn, Lẩu Gà Lá É & Sữa Đậu Nành', 'am-thuc-pho-nui-da-lat', 'Oanh tạc các quán ăn núp hẻm chỉ người bản xứ biết. Thưởng thức bánh căn giòn rụm chấm xíu mại cay nồng và nồi lẩu gà lá é ấm sực giữa trời đêm se lạnh.', 'host_range', 0, 300000, 500000, 'Da Lat', 1, '/assets/images/tours/dalat-food.jpg', 'approved'),
(4, 3, 4, 'Khám Phá Rừng Dừa Bảy Mẫu Bằng Thuyền Thúng', 'kham-pha-rung-dua-bay-mau-thuyen-thung', 'Trải nghiệm lắc thúng điệu nghệ của các ngư dân Cẩm Thanh, giăng lưới bắt cá và thưởng thức dừa nước ngọt mát ngay trên sông.', 'free', 0, 0, 0, 'Hoi An', 1, '/assets/images/tours/rung-dua.jpg', 'approved');

-- 4. Khối lưu trú kèm tour
TRUNCATE TABLE tbl_service_accommodations;
INSERT INTO tbl_service_accommodations (service_id, hotel_name, address, room_type, nights, notes) VALUES
(2, 'Homestay Làng Gốm Thanh Hà', 'Khu 5, Phường Thanh Hà, TP. Hội An, Quảng Nam', 'Phòng Đôi Mộc Mạc View Sông', 1, 'Bao gồm bữa sáng bánh mì Hội An và trà thảo mộc Cù Lao Chàm.');

-- 5. Lịch khởi hành
TRUNCATE TABLE tbl_service_schedules;
INSERT INTO tbl_service_schedules (id, service_id, starts_at, ends_at, booking_closes_at, capacity, booked_seats, meeting_point, status) VALUES
(1, 1, '2026-11-15 04:30:00', '2026-11-15 12:00:00', '2026-11-14 18:00:00', 10, 2, 'Cổng chợ Đà Lạt, đường Nguyễn Thị Minh Khai', 'open'),
(2, 1, '2026-11-20 04:30:00', '2026-11-20 12:00:00', '2026-11-19 18:00:00', 12, 0, 'Cổng chợ Đà Lạt, đường Nguyễn Thị Minh Khai', 'open'),
(3, 2, '2026-11-18 15:00:00', '2026-11-18 21:00:00', '2026-11-17 20:00:00', 8, 0, 'Chùa Cầu Hội An, đường Nguyễn Thị Minh Khai', 'open'),
(4, 3, '2026-11-16 17:30:00', '2026-11-16 21:30:00', '2026-11-16 12:00:00', 6, 0, 'Hồ Xuân Hương, bến đạp vịt Ánh Sáng', 'open'),
(5, 4, '2026-11-22 08:30:00', '2026-11-22 11:30:00', '2026-11-21 17:00:00', 15, 0, 'Bến thuyền Rừng Dừa Bảy Mẫu, Cẩm Thanh', 'open');

-- 6. Khuyến mãi mẫu
TRUNCATE TABLE tbl_promotions;
INSERT INTO tbl_promotions (id, code, title, discount_percent, starts_at, ends_at, is_active) VALUES
(1, 'CHAO2026', 'Ưu Đãi Chào Bạn Mới Khám Phá', 10.00, '2026-10-01 00:00:00', '2026-12-31 23:59:59', 1),
(2, 'VIPLOCAL', 'Tri Ân Khách Đi Tour Lần 2', 15.00, '2026-11-01 00:00:00', '2026-12-31 23:59:59', 1);

INSERT INTO tbl_promotion_services (promotion_id, service_id) VALUES
(1, 1),
(1, 2),
(2, 1);

-- 7. Bài viết blog tin tức du lịch
TRUNCATE TABLE tbl_posts;
INSERT INTO tbl_posts (id, author_id, title, slug, content, thumbnail, views_count, is_published) VALUES
(1, 1, 'Bỏ Túi 5 Kinh Nghiệm Đón Bình Minh Săn Mây Đà Lạt Cực Chuẩn', 'kinh-nghiem-san-may-da-lat', '<p>Săn mây Đà Lạt luôn là trải nghiệm đứng đầu danh sách phải thử khi đến xứ sở sương mù. Hãy dậy từ 4h sáng, chọn cung đường Cầu Đất hoặc đỉnh Hòn Bồ và đừng quên mang áo ấm nhiều lớp...</p>', '/assets/images/posts/san-may-tips.jpg', 128, 1),
(2, 1, 'Hội An 24 Giờ: Ăn Gì, Đi Đâu Để Cảm Hết Cái Tình Của Phố Cổ?', 'hoi-an-24-gio-an-gi-choi-dau', '<p>Không chỉ có lồng đèn lung linh, Hội An còn quyến rũ bởi tô cao lầu giếng Bá Lễ đậm đà, tách trà Mót sả chanh thơm nức mũi và nụ cười hiền hậu của những người chèo thuyền thúng...</p>', '/assets/images/posts/hoian-guide.jpg', 95, 1);

-- 8. Thảo luận & hỏi đáp mẫu
TRUNCATE TABLE tbl_discussions;
INSERT INTO tbl_discussions (id, service_id, user_id, parent_id, message, is_visible) VALUES
(1, 1, 4, NULL, 'Chào anh/chị Host, cho em hỏi sáng sớm đi săn mây thời tiết tháng 11 có lạnh lắm không ạ? Có cần chuẩn bị giày leo núi chuyên dụng không?', 1),
(2, 1, 2, 1, 'Chào bạn! Tháng 11 sáng sớm nhiệt độ khoảng 14-16 độ C, trời se lạnh nhiều sương. Bạn chỉ cần mặc áo khoác giữ ấm và đi giày thể thao đế bám êm chân là đi rất thoải mái nhé!', 1);

-- 9. Đánh giá review mẫu
TRUNCATE TABLE tbl_reviews;
INSERT INTO tbl_reviews (id, booking_id, tourist_id, service_id, rating, comment, is_visible) VALUES
(1, 1, 4, 1, 5, 'Chuyến đi săn mây trên cả tuyệt vời! Anh Host cực kỳ nhiệt tình, chụp hình có tâm và am hiểu từng gốc cà phê ở Cầu Đất. Nhất định sẽ quay lại!', 1);

SET FOREIGN_KEY_CHECKS = 1;
