-- ==============================================================================
-- DỮ LIỆU MẪU BAN ĐẦU CHO HỆ THỐNG ONLINETRAVELLER
-- Mật khẩu mặc định cho tất cả tài khoản mẫu: password123
-- Hash Bcrypt: $2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tài khoản mẫu (Bao gồm Host Demo độc quyền)
TRUNCATE TABLE tbl_users;
INSERT INTO tbl_users (id, role, name, email, password_hash, phone, living_area, bio, gender, birth_date, status) VALUES
(1, 'admin', 'Quản Trị Viên Hệ Thống', 'admin@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0901234567', 'TP. Hồ Chí Minh', 'Ban quản trị nền tảng OnlineTraveller kết nối du lịch từ xa và dân bản địa.', 'other', '1988-08-18', 'active'),
(2, 'host', 'Nguyễn Văn Bản Địa (Đà Lạt)', 'host.dalat@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0912345678', 'Đà Lạt, Lâm Đồng', 'Người con xứ trà và cà phê Cầu Đất, hơn 10 năm kinh nghiệm dẫn khách du lịch săn mây và trải nghiệm đời sống nông dân bản địa.', 'male', '1985-05-15', 'active'),
(3, 'host', 'Trần Thị Hướng Dẫn (Hội An)', 'host.hoian@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0923456789', 'Phố cổ Hội An, Quảng Nam', 'Sinh ra và lớn lên bên dòng sông Hoài, say mê gìn giữ văn hóa làng nghề gốm Thanh Hà và nghệ thuật làm đèn lồng truyền thống.', 'female', '1992-10-20', 'active'),
(4, 'tourist', 'Lê Du Khách', 'tourist@gmail.com', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0934567890', 'Hà Nội', 'Đam mê khám phá văn hóa các vùng miền từ xa và đặt chân đến những vùng đất mới.', 'male', '1996-03-12', 'active'),
(5, 'tourist', 'Phạm Thích Du Lịch', 'phamtourist@gmail.com', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0945678901', 'Đà Nẵng', 'Thích tìm hiểu phong tục tập quán địa phương qua hình ảnh 3D và các buổi trò chuyện trực tiếp cùng người bản xứ.', 'female', '1998-11-25', 'active'),
(6, 'host', 'Nguyễn Dân Bản Địa (Host Demo)', 'hostdemo@onlinetraveller.vn', '$2y$10$hQLACbMKREt.ABdU7fbj3.YAJXYKlYi1WRMzT0ZdK8l6eoRx63kMe', '0988889999', 'Việt Nam', 'Tài khoản Host Dân Bản Địa Demo chính thức của OnlineTraveller. Hơn 10 năm kinh nghiệm kết nối du khách với văn hóa, ẩm thực, làng nghề và thiên nhiên kỳ vĩ khắp 3 miền Bắc - Trung - Nam.', 'male', '1990-01-01', 'active');

-- 2. Danh mục tour
TRUNCATE TABLE tbl_categories;
INSERT INTO tbl_categories (id, name, slug, description, icon, is_active) VALUES
(1, 'Khám Phá Văn Hóa & Lịch Sử', 'van-hoa-lich-su', 'Trải nghiệm đời sống bản địa, làng nghề cổ truyền và di tích văn hóa lâu đời.', 'bi-bank', 1),
(2, 'Ẩm Thực Bản Địa Độc Đáo', 'am-thuc-ban-dia', 'Thưởng thức các món ngon truyền thống cùng người dân địa phương tại chợ và hẻm phố.', 'bi-cup-hot', 1),
(3, 'Trekking & Vùng Cao Hùng Vĩ', 'trekking-vung-cao', 'Chinh phục cung đường núi, đèo ngoạn mục, cắm trại săn mây tại Tây Bắc và Tây Nguyên.', 'bi-tree', 1),
(4, 'Biển Đảo & Nghỉ Dưỡng Sinh Thái', 'bien-dao-sinh-thai', 'Lặn ngắm san hô, du thuyền trên vịnh và trải nghiệm làng chài ven biển mộc mạc.', 'bi-water', 1),
(5, 'Làng Nghề Cổ Truyền', 'lang-nghe-co-truyen', 'Khám phá các làng nghề dệt lụa, gốm sứ, tranh dân gian và chạm khắc truyền thống.', 'bi-palette', 1);

-- 3. Trải nghiệm bản địa (8 Tour Mẫu Hoàn Chỉnh từ Host Demo)
TRUNCATE TABLE tbl_services;
INSERT INTO tbl_services (id, host_id, category_id, title, slug, description, price_mode, base_price, min_price, max_price, location, latitude, longitude, duration_days, banner_image, status) VALUES
(1, 6, 3, 'Săn Mây Cầu Đất & Khám Phá Nông Trại Cà Phê Arabica', 'san-may-cau-dat-kham-pha-nong-trai-ca-phe-arabica', 'Hành trình đưa du khách rời xa nhịp sống vội vã của phố thị để hòa mình vào đại ngàn thông reo vùng ngoại ô Cầu Đất. Bạn sẽ được tự tay hái những trái cà phê Arabica chín mọng dưới sương sớm, học cách rang xay thủ công cùng nông dân bản địa và thức giấc giữa biển mây bồng bềnh từ căn nhà gỗ trên sườn đồi.\n\n1. Ngày 1: Đón khách tại trung tâm – Tham quan đồi chè Cầu Đất – Trải nghiệm hái và rang cà phê tại nông trại – Nhận phòng homestay, tiệc nướng BBQ tối.\n2. Ngày 2: Săn mây bình minh tại thảm gỗ – Thưởng thức cà phê sáng và bữa sáng địa phương – Tham quan vườn hồng treo gió – Trả khách về điểm đón.', 'fixed_choice', 1450000, 0, 0, 'Quảng trường Lâm Viên (Cổng nụ hoa Atiso), Phường 10, TP. Đà Lạt, Lâm Đồng', 11.93600000, 108.44520000, 2, '/assets/images/tours/tour1/banner.jpg', 'approved'),
(2, 6, 1, 'Dấu Ấn Cố Đô & Khám Phá Kiến Trúc Lăng Tẩm Triều Nguyễn', 'dau-an-co-do-kham-pha-kien-truc-lang-tam-trieu-nguyen', 'Chuyến du hành ngược dòng lịch sử trên chiếc thuyền rồng xuôi dòng sông Hương thơ mộng, lắng nghe những giai thoại cung đình ít người biết về các vị vua triều Nguyễn. Du khách không chỉ chiêm ngưỡng nghệ thuật khảm sành sứ tinh xảo mà còn thưởng thức nhã nhạc và bữa trưa cơm cung đình thanh tao.\n\n1. Sáng: Du thuyền sông Hương thăm chùa Thiên Mụ – Tham quan Đại Nội Huế (Ngọ Môn, Điện Thái Hòa).\n2. Trưa: Thưởng thức cơm cung đình và nghe ca Huế thính phòng.\n3. Chiều: Viếng Lăng Khải Định và Lăng Tự Đức – Ngắm hoàng hôn trên đồi Vọng Cảnh – Kết thúc tour.', 'fixed_choice', 650000, 0, 0, 'Bến thuyền Tòa Khâm, 49 Lê Lợi, Phú Hội, TP. Huế, Thừa Thiên Huế', 16.46870000, 107.59120000, 1, '/assets/images/tours/tour2/banner.jpg', 'approved'),
(3, 6, 2, 'Food Tour Đêm Sài Gòn & Chuyện Hẻm Nhỏ Chợ Lớn', 'food-tour-dem-sai-gon-chuyen-hem-nho-cho-lon', 'Khám phá linh hồn ẩm thực đường phố Sài Gòn khi phố lên đèn qua những con hẻm trăm tuổi tại Quận 5 và Quận 10. Cùng người dẫn đường bản địa thưởng thức hơn 7 món đặc sản trứ danh từ sủi cảo gia truyền, hủ tiếu mì kéo tay đến chè người Hoa thanh mát, lắng nghe câu chuyện di cư và giao thoa văn hóa Việt – Hoa.\n\n1. 17:30: Tập trung tại Quận 1, di chuyển bằng xe máy cổ điển qua khu phố người Hoa.\n2. 18:30: Thưởng thức Sủi cảo Hà Tôn Quyền và phá lấu bò nước dừa.\n3. 20:00: Dạo phố đèn lồng Lương Nhữ Học – Ăn chè mè đen, chè hột gà trà.\n4. 21:30: Kết thúc hành trình tại chợ đêm Hồ Thị Kỷ với các món ăn vặt đường phố.', 'prepaid_full', 580000, 0, 0, 'Bưu điện Trung tâm Sài Gòn, 02 Công xã Paris, Bến Nghé, Quận 1, TP. Hồ Chí Minh', 10.77980000, 106.69900000, 1, '/assets/images/tours/tour3/banner.jpg', 'approved'),
(4, 6, 5, 'Chạm Tay Vào Di Sản Gốm Thanh Hà & Làng Mộc Kim Bồng', 'cham-tay-vao-di-san-gom-thanh-ha-lang-moc-kim-bong', 'Rời khu phố cổ đông đúc để xuôi thuyền sang đôi bờ sông Thu Bồn, nơi những nghệ nhân vẫn ngày ngày giữ lửa cho các làng nghề truyền thống hơn 500 năm tuổi. Du khách được trực tiếp nhào đất sét trên bàn xoay thủ công, chạm khắc gỗ cùng thợ mộc Kim Bồng và mang về món quà lưu niệm do chính tay mình tạo tác.\n\n1. 08:00: Đón khách tại Chùa Cầu – Lên thuyền gỗ đi làng gốm Thanh Hà.\n2. 09:00: Học chuốt gốm thủ công cùng nghệ nhân cao niên và tham quan Công viên Đất nung.\n3. 11:30: Ăn trưa cao lầu và mì Quảng tại nhà dân bên sông.\n4. 13:30: Sang cù lao Cẩm Kim thăm làng mộc Kim Bồng và làng chiếu cói Bàn Thạch – 16:30 đưa khách về lại phố cổ.', 'fixed_choice', 520000, 0, 0, 'Chùa Cầu, Đường Nguyễn Thị Minh Khai, Phường Minh An, TP. Hội An, Quảng Nam', 15.87710000, 108.32590000, 1, '/assets/images/tours/tour4/banner.jpg', 'approved'),
(5, 6, 4, 'Đảo Ngọc Hoang Sơ: Lặn Ngắm San Hô Hòn Móng Tay & Câu Mực Đêm', 'dao-ngoc-hoang-so-lan-ngam-san-ho-hon-mong-tay-cau-muc-dem', 'Trải nghiệm cuộc sống ngư dân miền biển đảo Nam An Thới với làn nước xanh ngọc bích trong vắt nhìn thấu đáy. Du khách sẽ lặn ngắm rạn san hô tự nhiên đầy màu sắc, tự tay chế biến hải sản vừa đánh bắt trên bè nổi và nghỉ đêm tại bungalow sinh thái sát bờ biển nghe tiếng sóng vỗ.\n\n1. Ngày 1: Đón khách tại cảng An Thới – Cano khám phá Hòn Móng Tay & Hòn Gầm Ghì – Lặn ngắm san hô – Nhận phòng Bungalow ven biển – Tối đi thuyền câu mực đêm.\n2. Ngày 2: Ngắm bình minh làng chài Hàm Ninh – Thưởng thức bún quậy Kiến Xây – Tham quan vườn tiêu sinh thái – Tiễn khách về điểm hẹn.', 'fixed_choice', 2150000, 0, 0, 'Cảng An Thới, Khu phố 1, Phường An Thới, TP. Phú Quốc, Kiên Giang', 10.02420000, 104.01180000, 2, '/assets/images/tours/tour5/banner.jpg', 'approved'),
(6, 6, 3, 'Chinh Phục Cổng Trời Quản Bạ & Sống Chậm Bản Lô Lô Chải', 'chinh-phuc-cong-troi-quan-ba-song-cham-ban-lo-lo-chai', 'Hành trình qua Công viên Địa chất Toàn cầu Cao nguyên đá Đồng Văn hùng vĩ, chinh phục đèo Mã Pì Lèng hiểm trở và dừng chân tại ngôi làng cổ tích dưới chân cột cờ Lũng Cú. Bạn sẽ lưu trú trong ngôi nhà trình tường đất hàng trăm năm tuổi của người Lô Lô Hoa, quây quần bên bếp lửa hồng thưởng thức thắng cố và rượu ngô men lá.\n\n1. Ngày 1: Xuất phát từ Km0 – Dốc Bắc Sum – Cổng trời Quản Bạ – Phố Cáo – Nhận phòng homestay tại Lô Lô Chải.\n2. Ngày 2: Thăm Cột cờ Lũng Cú – Chinh phục đèo Mã Pì Lèng – Đi thuyền hẻm vực Tu Sản trên sông Nho Quế – Nghỉ đêm tại Đồng Văn.\n3. Ngày 3: Tham quan Dinh thự Vua Mèo – Phiên chợ vùng cao – Trở về TP. Hà Giang kết thúc hành trình.', 'host_range', 2890000, 2500000, 3200000, 'Cột mốc Km0 Hà Giang, Đường Nguyễn Trãi, Phường Nguyễn Trãi, TP. Hà Giang, Hà Giang', 22.82330000, 104.98390000, 3, '/assets/images/tours/tour6/banner.jpg', 'approved'),
(7, 6, 4, 'Một Ngày Làm Nông Dân Xứ Dừa & Chèo Xuồng Rạch Nhỏ Bến Tre', 'mot-ngay-lam-nong-dan-xu-dua-cheo-xuong-rach-nho-ben-tre', 'Hành trình xanh mát đưa du khách len lỏi qua những con rạch nhỏ rợp bóng dừa nước đặc trưng của miền Tây sông nước. Bạn sẽ được tự tay hái trái cây miệt vườn, tát mương bắt cá lóc, thưởng thức đờn ca tài tử bên tách trà mật ong hoa nhãn và trải nghiệm làm kẹo dừa thủ công cùng bà con miệt vườn.\n\n1. 08:30: Lên thuyền máy trên sông Hàm Luông – Tham quan lò gạch thủ công và cơ sở kẹo dừa.\n2. 10:30: Đi xe lôi máy qua đường làng rợp bóng dừa – Tát mương bắt cá và nướng trui tại vườn.\n3. 12:30: Ăn trưa đặc sản cá tai tượng chiên xù, bánh xèo củ hủ dừa.\n4. 14:30: Chèo xuồng ba lá trong rạch dừa nước – Nghe đờn ca tài tử – 16:30 trở lại bến tàu.', 'fixed_choice', 490000, 0, 0, 'Bến tàu Du lịch Bến Tre, Đường Hùng Vương, Phường 5, TP. Bến Tre, Bến Tre', 10.24150000, 106.37580000, 1, '/assets/images/tours/tour7/banner.jpg', 'approved'),
(8, 6, 5, 'Làng Lụa Vạn Phúc & Tinh Hoa Tranh Dân Gian Đông Hồ', 'lang-lua-van-phuc-tinh-hoa-tranh-dan-gian-dong-ho', 'Hành trình kết nối hai vùng đất di sản Kinh Kỳ – Kinh Bắc, đưa du khách tìm về cội nguồn của những tấm lụa Hà Đông mềm mại và những bức tranh khắc gỗ dân gian đầy triết lý. Du khách sẽ nghỉ đêm tại nhà vườn cổ vùng Kinh Bắc, nghe liền anh liền chị hát Quan họ giao duyên và tự tay in một bức tranh Đông Hồ trên giấy điệp óng ánh.\n\n1. Ngày 1: Đón khách tại Nhà hát Lớn – Tham quan Làng lụa Vạn Phúc (Hà Đông) – Di chuyển sang Bắc Ninh – Nhận phòng nhà vườn cổ Kinh Bắc – Thưởng thức cơm quê và giao lưu hát Quan họ tối.\n2. Ngày 2: Thăm Làng tranh Đông Hồ (Thuận Thành) – Tự tay in tranh trên giấy điệp – Viếng Chùa Bút Tháp – Trả khách về Hà Nội lúc 17:00.', 'fixed_choice', 1680000, 0, 0, 'Nhà hát Lớn Hà Nội, Số 01 Tràng Tiền, Phường Tràng Tiền, Quận Hoàn Kiếm, Hà Nội', 21.02450000, 105.85750000, 2, '/assets/images/tours/tour8/banner.jpg', 'approved');

-- 4. Các Trạm Trình Chiếu 3D (Three.js Stops) Đầy Đủ 8 Tour
TRUNCATE TABLE tbl_service_images;
INSERT INTO tbl_service_images (id, service_id, image_url, caption, story_text, video_url, sort_order) VALUES
-- Tour 1
(1, 1, '/assets/images/tours/tour1/tram1.jpg', 'Trạm 1: Đón bình minh biển mây Đồi Chè Cầu Đất', 'Đồi chè hơn 100 năm tuổi với những luống chè xanh mướt uốn lượn theo triền đồi, là nơi giao thoa giữa ánh nắng bình minh đầu tiên và biển mây trắng xóa đặc trưng của cao nguyên Langbiang.', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1),
(2, 1, '/assets/images/tours/tour1/tram2.jpg', 'Trạm 2: Nông trại Cà phê Arabica Trạm Hành', 'Trải nghiệm thu hoạch những trái cà phê Arabica chín mọng dưới sương sớm, học cách rang xay thủ công cùng nông dân bản địa và thưởng thức tách cà phê nguyên chất đậm đà.', NULL, 2),
(3, 1, '/assets/images/tours/tour1/tram3.jpg', 'Trạm 3: Vườn Hồng Treo Gió Công Nghệ Nhật Bản', 'Những giàn hồng vàng cam treo lơ lửng dưới mái hiên nhà gỗ, đón gió trời se lạnh để kết tinh mật ngọt tự nhiên mà không cần chất bảo quản.', NULL, 3),

-- Tour 2
(4, 2, '/assets/images/tours/tour2/tram1.webp', 'Trạm 1: Chùa Thiên Mụ & Bến Sông Hương', 'Ngôi cổ tự linh thiêng xây dựng từ năm 1601 trên đồi Hà Khê với tháp Phước Duyên 7 tầng soi bóng xuống dòng Hương Giang êm đềm.', NULL, 1),
(5, 2, '/assets/images/tours/tour2/tram2.jpg', 'Trạm 2: Ngọ Môn & Điện Thái Hòa - Hoàng Thành Huế', 'Trung tâm quyền lực của vương triều Nguyễn suốt 143 năm, nổi bật với kiến trúc "trùng thiềm điệp ốc" và hệ thống cột gỗ lim chạm khắc rồng mây tinh xảo.', NULL, 2),
(6, 2, '/assets/images/tours/tour2/tram3.jpg', 'Trạm 3: Lăng Vua Khải Định (Ứng Lăng)', 'Công trình giao thoa độc đáo giữa kiến trúc Á – Âu, nổi tiếng với nghệ thuật ghép tranh sành sứ và thủy tinh màu kỳ công bậc nhất Việt Nam.', NULL, 3),

-- Tour 3
(7, 3, '/assets/images/tours/tour3/tram1.jpg', 'Trạm 1: Phố Sủi Cảo Hà Tôn Quyền', 'Con phố nhộn nhịp bậc nhất Chợ Lớn mỗi tối với những xửng sủi cảo nhân tôm thịt tươi rói được gói thủ công qua 3 thế hệ gia đình người Hoa.', NULL, 1),
(8, 3, '/assets/images/tours/tour3/tram2.jpg', 'Trạm 2: Chùa Bà Thiên Hậu & Phố Lồng Đèn', 'Ngôi miếu cổ kính mang đậm kiến trúc Phúc Kiến với những vòng nhang trầm treo cao nghi ngút khói, lưu giữ tín ngưỡng của cộng đồng người Hoa tại Sài Gòn.', NULL, 2),
(9, 3, '/assets/images/tours/tour3/tram3.jpg', 'Trạm 3: Tiệm Chè Cột Điện Hơn 80 Năm Tuổi', 'Quán chè bình dân gắn liền với ký ức người Sài Gòn xưa qua các món chè dưỡng nhan, quy linh cao và chè đậu xanh phổ tai nấu theo bí quyết gia truyền.', NULL, 3),
(10, 3, '/assets/images/tours/tour3/tram4.jpg', 'Trạm 4: Chợ Đêm Ẩm Thực Hồ Thị Kỷ', 'Thiên đường ẩm thực giao thoa Việt – Campuchia sôi động giữa lòng Quận 10 với hàng trăm gian hàng bánh tráng nướng, nem bò nướng sả và hủ tiếu Campuchia.', NULL, 4),

-- Tour 4
(11, 4, '/assets/images/tours/tour4/tram1.jpg', 'Trạm 1: Làng Gốm Cổ Thanh Hà Bên Sông Thu Bồn', 'Làng gốm hình thành từ thế kỷ XVI, nổi tiếng với kỹ thuật chuốt gốm bằng bàn xoay đạp chân không tráng men và nung củi truyền thống.', NULL, 1),
(12, 4, '/assets/images/tours/tour4/tram2.jpg', 'Trạm 2: Xưởng Chạm Khắc Gỗ Làng Mộc Kim Bồng', 'Cái nôi của những nghệ nhân từng xây dựng nên những ngôi nhà cổ Hội An và cung đình Huế với kỹ thuật ghép mộng gỗ không cần đinh sắt.', NULL, 2),
(13, 4, '/assets/images/tours/tour4/tram3.jpg', 'Trạm 3: Xưởng Dệt Chiếu Cói Thủ Công Cẩm Kim', 'Tiếng khung cửi lách cách vang lên trong những ngôi nhà vườn rợp bóng cau, nơi sợi cói nhuộm màu tự nhiên được dệt thành những lá chiếu hoa bền đẹp.', NULL, 3),

-- Tour 5
(14, 5, '/assets/images/tours/tour5/tram1.jpg', 'Trạm 1: Bãi Cát Trắng Hoang Sơ Hòn Móng Tay', 'Viên ngọc ẩn mình phía Nam đảo Phú Quốc với hàng dừa nghiêng soi bóng xuống làn nước màu ngọc lục bảo tĩnh lặng, không bị bê tông hóa.', NULL, 1),
(15, 5, '/assets/images/tours/tour5/tram2.jpg', 'Trạm 2: Rạn San Hô Tự Nhiên Hòn Gầm Ghì', 'Khu bảo tồn san hô mật độ dày đặc sát mặt nước, nơi du khách có thể dễ dàng chiêm ngưỡng hàng chục loài san hô cứng, san hô mềm và cá biển rực rỡ.', NULL, 2),
(16, 5, '/assets/images/tours/tour5/tram3.jpg', 'Trạm 3: Làng Chài Cổ Hàm Ninh Lúc Bình Minh', 'Làng chài lâu đời nhất đảo Ngọc với cây cầu gỗ vươn dài ra biển đón những chiếc ghe ghẹ tươi rói trở về sau chuyến ra khơi đêm.', NULL, 3),

-- Tour 6
(17, 6, '/assets/images/tours/tour6/tram1.jpg', 'Trạm 1: Cổng Trời Quản Bạ & Núi Đôi Cô Tiên', 'Cửa ngõ đầu tiên bước vào Cao nguyên đá Đồng Văn ở độ cao 1.500m, mở ra tầm nhìn bao quát thung lũng Tam Sơn và truyền thuyết về tình mẫu tử của người Mông.', NULL, 1),
(18, 6, '/assets/images/tours/tour6/tram2.jpg', 'Trạm 2: Bản Cổ Tích Lô Lô Chải Dưới Chân Cột Cờ Lũng Cú', 'Ngôi làng của đồng bào Lô Lô Hoa với những nếp nhà trình tường vàng đất, mái ngói âm dương phủ rêu phong và hàng rào đá xếp tay thủ công.', NULL, 2),
(19, 6, '/assets/images/tours/tour6/tram3.jpg', 'Trạm 3: Đèo Mã Pì Lèng & Hẻm Vực Tu Sản Sông Nho Quế', 'Một trong "Tứ đại đỉnh đèo" hiểm trở nhất Việt Nam uốn lượn bên vách đá dựng đứng, nhìn xuống dòng sông Nho Quế xanh ngắt uốn quanh hẻm vực sâu nhất Đông Nam Á.', NULL, 3),
(20, 6, '/assets/images/tours/tour6/tram4.jpg', 'Trạm 4: Dinh Thự Vua Mèo Vương Chính Đức', 'Khu dinh thự cổ nằm giữa thung lũng Sà Phìn được bao bọc bởi hàng sa mộc cổ thụ, kết hợp hài hòa kiến trúc Mông, Thanh và Pháp.', NULL, 4),

-- Tour 7
(21, 7, '/assets/images/tours/tour7/tram1.jpg', 'Trạm 1: Rạch Dừa Nước Miệt Vườn Nhơn Thạnh', 'Những con rạch chằng chịt với hai hàng dừa nước đan vòm xanh mát, nơi chiếc xuồng ba lá nhẹ nhàng rẽ nước mang đậm nét văn hóa giao thông sông nước Nam Bộ.', NULL, 1),
(22, 7, '/assets/images/tours/tour7/tram2.jpg', 'Trạm 2: Lò Kẹo Dừa Thủ Công Truyền Thống Bến Tre', 'Nơi lưu giữ nghề sên kẹo dừa bằng chảo gang trên bếp củi gáo dừa, quyện hương thơm béo ngậy của nước cốt dừa xiêm và mạch nha nếp.', NULL, 1),
(23, 7, '/assets/images/tours/tour7/tram3.jpg', 'Trạm 3: Vườn Cây Ăn Trái & Sân Khấu Đờn Ca Tài Tử', 'Không gian nhà vườn Nam Bộ mộc mạc với chôm chôm, sầu riêng trĩu quả, nơi du khách vừa nhâm nhi trà mật ong vừa thưởng thức di sản văn hóa phi vật thể Đờn ca tài tử.', NULL, 3),

-- Tour 8
(24, 8, '/assets/images/tours/tour8/tram1.jpg', 'Trạm 1: Con Đường Ô Sắc Màu & Xưởng Dệt Lụa Vạn Phúc', 'Làng nghề dệt lụa tơ tằm lâu đời nhất Việt Nam với hơn 1.000 năm lịch sử, nổi tiếng với dòng lụa Vân hoa văn chìm tinh xảo từng tiến vua.', NULL, 1),
(25, 8, '/assets/images/tours/tour8/tram2.jpg', 'Trạm 2: Xưởng In Tranh Dân Gian Đông Hồ', 'Nghệ thuật in ván khắc gỗ độc đáo sử dụng hoàn toàn màu sắc tự nhiên từ lá chàm, hoa hòe, than rơm nếp trên nền giấy điệp làm từ vỏ cây dó và vỏ sò điệp biển.', NULL, 2),
(26, 8, '/assets/images/tours/tour8/tram3.jpg', 'Trạm 3: Chùa Bút Tháp Cổ Kính Bên Sông Đuống', 'Ngôi danh lam cổ tự thế kỷ XVII lưu giữ bảo vật quốc gia tượng Phật Bà Quan Âm nghìn mắt nghìn tay bằng gỗ mít lớn nhất Việt Nam.', NULL, 3);

-- 5. Khối lưu trú Homestay nhà dân bản địa kèm tour
TRUNCATE TABLE tbl_service_accommodations;
INSERT INTO tbl_service_accommodations (service_id, homestay_name, address, room_type, nights, notes) VALUES
(1, 'Homestay Nhà Gỗ Cầu Đất View Mây', 'Thôn Trường Thọ, Xã Trạm Hành, TP. Đà Lạt, Lâm Đồng', 'Phòng Áp Mái Giường Đôi', 1, 'Bao gồm ăn sáng đặc sản, trà atiso nóng ấm, lò sưởi gỗ và khăn tắm; nhiệt độ ban đêm xuống thấp nên mang theo áo ấm dày.'),
(5, 'Bungalow Sinh Thái Bãi Sao Xanh', 'Ấp Bãi Sao, Phường An Thới, TP. Phú Quốc, Kiên Giang', 'Bungalow Gỗ Hướng Biển Giường Đôi', 1, 'Bao gồm ăn sáng hải sản, xe đạp dạo biển, dụng cụ lặn ống thở (snorkel) miễn phí; khuyến khích sử dụng kem chống nắng thân thiện với san hô.'),
(6, 'Nhà Trình Tường Cổ Lô Lô Chải', 'Thôn Lô Lô Chải, Xã Lũng Cú, Huyện Đồng Văn, Hà Giang', 'Phòng Truyền Thống Tường Đất (2 Giường Đôi)', 2, 'Bao gồm bữa sáng bánh cuốn trứng nóng, bữa tối cơm gia đình bản địa và trà Shan Tuyết cổ thụ; cần mang giày trekking bám tốt và giấy tờ tùy thân để khai báo vùng biên.'),
(8, 'Nhà Vườn Cổ Kinh Bắc & Ao Sen', 'Thôn Đông Khê, Xã Song Hồ, Thị xã Thuận Thành, Bắc Ninh', 'Phòng Nhà Gỗ Ba Gian Cổ Truyền', 1, 'Bao gồm bữa sáng phở gà ta, trà sen ướp xổi và trải nghiệm mặc trang phục áo tứ thân chụp ảnh; giữ yên tĩnh sau 22:00 để bảo tồn không gian làng quê.');

-- 6. Lịch khởi hành cụ thể cho từng tour
TRUNCATE TABLE tbl_service_schedules;
INSERT INTO tbl_service_schedules (id, service_id, starts_at, ends_at, booking_closes_at, capacity, booked_seats, meeting_point, status) VALUES
(1, 1, '2026-11-20 04:30:00', '2026-11-21 16:30:00', '2026-11-19 18:00:00', 10, 0, 'Quảng trường Lâm Viên (Cổng nụ hoa Atiso), Phường 10, TP. Đà Lạt', 'open'),
(2, 1, '2026-11-27 04:30:00', '2026-11-28 16:30:00', '2026-11-26 18:00:00', 12, 0, 'Quảng trường Lâm Viên (Cổng nụ hoa Atiso), Phường 10, TP. Đà Lạt', 'open'),
(3, 2, '2026-11-22 07:30:00', '2026-11-22 17:30:00', '2026-11-21 20:00:00', 15, 0, 'Bến thuyền Tòa Khâm, 49 Lê Lợi, Phú Hội, TP. Huế', 'open'),
(4, 2, '2026-11-29 07:30:00', '2026-11-29 17:30:00', '2026-11-28 20:00:00', 15, 0, 'Bến thuyền Tòa Khâm, 49 Lê Lợi, Phú Hội, TP. Huế', 'open'),
(5, 3, '2026-11-21 17:30:00', '2026-11-21 21:30:00', '2026-11-21 12:00:00', 8, 0, 'Bưu điện Trung tâm Sài Gòn, 02 Công xã Paris, Bến Nghé, Quận 1', 'open'),
(6, 3, '2026-11-28 17:30:00', '2026-11-28 21:30:00', '2026-11-28 12:00:00', 8, 0, 'Bưu điện Trung tâm Sài Gòn, 02 Công xã Paris, Bến Nghé, Quận 1', 'open'),
(7, 4, '2026-11-23 08:00:00', '2026-11-23 16:30:00', '2026-11-22 18:00:00', 12, 0, 'Chùa Cầu, Đường Nguyễn Thị Minh Khai, Phường Minh An, TP. Hội An', 'open'),
(8, 5, '2026-11-25 08:00:00', '2026-11-26 16:00:00', '2026-11-24 17:00:00', 10, 0, 'Cảng An Thới, Khu phố 1, Phường An Thới, TP. Phú Quốc', 'open'),
(9, 6, '2026-11-26 06:30:00', '2026-11-28 17:00:00', '2026-11-25 12:00:00', 8, 0, 'Cột mốc Km0 Hà Giang, Đường Nguyễn Trãi, Phường Nguyễn Trãi, TP. Hà Giang', 'open'),
(10, 7, '2026-11-24 08:30:00', '2026-11-24 16:30:00', '2026-11-23 18:00:00', 15, 0, 'Bến tàu Du lịch Bến Tre, Đường Hùng Vương, Phường 5, TP. Bến Tre', 'open'),
(11, 8, '2026-11-27 08:00:00', '2026-11-28 17:00:00', '2026-11-26 18:00:00', 10, 0, 'Nhà hát Lớn Hà Nội, Số 01 Tràng Tiền, Phường Tràng Tiền, Quận Hoàn Kiếm, Hà Nội', 'open');

-- 7. Khuyến mãi mẫu
TRUNCATE TABLE tbl_promotions;
INSERT INTO tbl_promotions (id, code, title, discount_percent, starts_at, ends_at, is_active) VALUES
(1, 'CHAO2026', 'Ưu Đãi Chào Bạn Mới Khám Phá', 10.00, '2026-10-01 00:00:00', '2026-12-31 23:59:59', 1),
(2, 'VIPLOCAL', 'Tri Ân Khách Đi Tour Lần 2', 15.00, '2026-11-01 00:00:00', '2026-12-31 23:59:59', 1);

TRUNCATE TABLE tbl_promotion_services;
INSERT INTO tbl_promotion_services (promotion_id, service_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1);

-- 8. Bài viết blog tin tức du lịch
TRUNCATE TABLE tbl_posts;
INSERT INTO tbl_posts (id, author_id, title, slug, content, thumbnail, views_count, is_published) VALUES
(1, 1, 'Bỏ Túi 5 Kinh Nghiệm Đón Bình Minh Săn Mây Cầu Đất Cực Chuẩn', 'kinh-nghiem-san-may-cau-dat', '<p>Săn mây Đà Lạt luôn là trải nghiệm đứng đầu danh sách phải thử khi đến xứ sở sương mù. Hãy dậy từ 4h sáng, chọn cung đường Cầu Đất hoặc đỉnh Hòn Bồ và đừng quên mang áo ấm nhiều lớp để đón trọn vẹn biển mây bồng bềnh dưới ánh nắng bình minh đầu tiên...</p>', '/assets/images/tours/tour1/banner.jpg', 128, 1),
(2, 1, 'Khám Phá Ẩm Thực Chợ Lớn: Nét Giao Thoa Độc Đáo Giữa Sài Gòn Hoa Lệ', 'am-thuc-cho-lon-sai-gon', '<p>Không chỉ có những tòa nhà chọc trời, Sài Gòn còn quyến rũ bởi những con hẻm cổ kính trăm năm tuổi tại Quận 5. Nơi đây lưu giữ nguyên vẹn tinh hoa ẩm thực người Hoa từ sủi cảo gia truyền, phá lấu bò đến những tiệm chè cổ truyền hơn 80 năm tuổi...</p>', '/assets/images/tours/tour3/banner.jpg', 95, 1);

-- 9. Thảo luận & hỏi đáp mẫu
TRUNCATE TABLE tbl_discussions;
INSERT INTO tbl_discussions (id, service_id, user_id, parent_id, message, is_visible) VALUES
(1, 1, 4, NULL, 'Chào anh Host, cho em hỏi sáng sớm đi săn mây tại Đồi chè Cầu Đất thời tiết có lạnh lắm không ạ? Có cần chuẩn bị giày leo núi chuyên dụng không?', 1),
(2, 1, 6, 1, 'Chào bạn! Sáng sớm nhiệt độ Cầu Đất khoảng 14-16 độ C, trời se lạnh và sương sớm. Bạn chỉ cần mặc áo khoác ấm và đi giày thể thao đế êm là di chuyển rất dễ dàng nhé!', 1),
(3, 3, 5, NULL, 'Cho em hỏi tour ẩm thực đêm Chợ Lớn có phù hợp cho người ăn chay một số món không ạ?', 1),
(4, 3, 6, 3, 'Chào bạn! Tour có hỗ trợ linh hoạt các món thanh đạm, chè ngọt dưỡng nhan và sủi cảo chay theo yêu cầu của du khách nhé!', 1);

-- 10. Đánh giá review mẫu (Có huy hiệu bảo chứng Đã tự trải nghiệm)
TRUNCATE TABLE tbl_reviews;
INSERT INTO tbl_reviews (id, booking_id, tourist_id, service_id, rating, comment, is_visible, is_verified) VALUES
(1, NULL, 4, 1, 5, 'Chuyến đi săn mây Cầu Đất trên cả tuyệt vời! Anh Host cực kỳ nhiệt tình, chụp hình có tâm và am hiểu từng gốc chè, vườn cà phê. Phòng homestay nhà gỗ ấm cúng view mây sáng sớm siêu đỉnh!', 1, 1),
(2, NULL, 5, 2, 5, 'Đi du thuyền sông Hương nghe ca Huế và thăm lăng Khải Định rất ấn tượng. Hướng dẫn viên thuyết minh cực kỳ sâu sắc về lịch sử triều Nguyễn.', 1, 1),
(3, NULL, 4, 3, 5, 'Các món ăn trong hẻm Chợ Lớn ngon tuyệt đỉnh, sủi cảo tươi ngọt thịt và chè cột điện thanh mát đậm đà ký ức.', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
