-- ==============================================================================
-- ONLINETRAVELLER - SỬA LỖI COLLATION TRÊN ALWAYSDATA / MARIADB 11.X
-- Lỗi: 1267 Illegal mix of collations (utf8mb4_unicode_ci,IMPLICIT) and (utf8mb4_uca1400_ai_ci,IMPLICIT)
-- Hướng dẫn:
--   CÁCH 1 (KHUYÊN DÙNG - TAB IMPORT):
--     1. Click chọn tên Database của bạn ở menu bên trái phpMyAdmin (để URL có /database/...)
--     2. Bấm vào tab "Import" (Nhập) ở hàng trên cùng
--     3. Chọn file này và bấm "Import" (hoặc "Go")
--
--   CÁCH 2 (TAB SQL):
--     1. BẮT BUỘC: Click chọn Database của bạn ở menu bên trái phpMyAdmin trước!
--        (Nếu không chọn DB sẽ bị lỗi #1046 - No database selected)
--     2. Vào tab "SQL"
--     3. Dán toàn bộ nội dung file này và nhấn "Go" (Thực hiện)
-- ==============================================================================

ALTER DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET collation_connection = 'utf8mb4_unicode_ci';
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Chuẩn hóa Collation cho 15 bảng hiện có (không làm mất dữ liệu)
ALTER TABLE tbl_users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_service_images CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_service_accommodations CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_service_schedules CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_promotions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_promotion_services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_bookings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_payments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_receipts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_refunds CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_reviews CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_discussions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE tbl_posts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Tái tạo toàn bộ Stored Procedures với chuẩn utf8mb4_unicode_ci

-- >>> BẮT ĐẦU NỘI DUNG: database/procedures/01_users_categories.sql <<<
-- ==============================================================================
-- PHÂN HỆ TV1 (Dương Hoàng Sâm): USERS & CATEGORIES STORED PROCEDURES
-- File: database/procedures/01_users_categories.sql
-- Naming: dhs_sp_*
-- ==============================================================================

DELIMITER $$

-- ------------------------------------------------------------------------------
-- 1. dhs_sp_users_register: Đăng ký tài khoản Du khách hoặc Dân bản địa
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS dhs_sp_users_register$$
CREATE PROCEDURE dhs_sp_users_register(
    IN p_email VARCHAR(254),
    IN p_name VARCHAR(150),
    IN p_password_hash VARCHAR(255),
    IN p_phone VARCHAR(32),
    IN p_role VARCHAR(16)
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    DECLARE v_role VARCHAR(16) DEFAULT 'tourist';

    IF p_role = 'host' THEN
        SET v_role = 'host';
    ELSE
        SET v_role = 'tourist';
    END IF;

    SELECT COUNT(*) INTO v_count FROM tbl_users WHERE email = p_email;
    IF v_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EMAIL_ALREADY_EXISTS';
    END IF;

    INSERT INTO tbl_users (role, name, email, password_hash, phone, status, created_at, updated_at)
    VALUES (v_role, p_name, p_email, p_password_hash, p_phone, 'active', NOW(6), NOW(6));

    SELECT id, role, name, email, phone, avatar, living_area, bio, status, created_at
    FROM tbl_users
    WHERE id = LAST_INSERT_ID();
END$$

-- ------------------------------------------------------------------------------
-- 2. dhs_sp_users_find_login: Lấy thông tin đăng nhập theo email để PHP verify mật khẩu
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS dhs_sp_users_find_login$$
CREATE PROCEDURE dhs_sp_users_find_login(
    IN p_email VARCHAR(254)
)
BEGIN
    SELECT id, role, name, email, password_hash, phone, avatar, living_area, bio, gender, birth_date, status
    FROM tbl_users
    WHERE email = p_email
    LIMIT 1;
END$$

-- ------------------------------------------------------------------------------
-- 3. dhs_sp_users_update_profile: Cập nhật hồ sơ & câu chuyện của dân bản địa
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS dhs_sp_users_update_profile$$
CREATE PROCEDURE dhs_sp_users_update_profile(
    IN p_user_id BIGINT UNSIGNED,
    IN p_name VARCHAR(150),
    IN p_phone VARCHAR(32),
    IN p_living_area VARCHAR(255),
    IN p_bio TEXT,
    IN p_avatar VARCHAR(255),
    IN p_gender VARCHAR(16),
    IN p_birth_date DATE
)
BEGIN
    DECLARE v_clean_gender VARCHAR(16) DEFAULT 'other';

    IF p_gender IN ('male', 'female', 'other') THEN
        SET v_clean_gender = p_gender;
    END IF;

    UPDATE tbl_users
    SET name = p_name,
        phone = p_phone,
        living_area = p_living_area,
        bio = p_bio,
        avatar = COALESCE(p_avatar, avatar),
        gender = v_clean_gender,
        birth_date = p_birth_date,
        updated_at = NOW(6)
    WHERE id = p_user_id;

    SELECT id, role, name, email, phone, avatar, living_area, bio, gender, birth_date, status, created_at, updated_at
    FROM tbl_users
    WHERE id = p_user_id;
END$$

-- ------------------------------------------------------------------------------
-- 4. dhs_sp_users_set_status: Admin khóa / mở tài khoản người dùng
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS dhs_sp_users_set_status$$
CREATE PROCEDURE dhs_sp_users_set_status(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_user_id BIGINT UNSIGNED,
    IN p_status VARCHAR(16)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    UPDATE tbl_users
    SET status = p_status,
        updated_at = NOW(6)
    WHERE id = p_user_id;

    SELECT id, name, email, role, status
    FROM tbl_users
    WHERE id = p_user_id;
END$$

-- ------------------------------------------------------------------------------
-- 5. dhs_sp_categories_save: Admin thêm mới hoặc cập nhật danh mục tour
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS dhs_sp_categories_save$$
CREATE PROCEDURE dhs_sp_categories_save(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_id BIGINT UNSIGNED,
    IN p_name VARCHAR(100),
    IN p_slug VARCHAR(120),
    IN p_description TEXT,
    IN p_icon VARCHAR(100),
    IN p_is_active TINYINT(1)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    IF p_id IS NULL OR p_id = 0 THEN
        INSERT INTO tbl_categories (name, slug, description, icon, is_active, created_at)
        VALUES (p_name, p_slug, p_description, p_icon, COALESCE(p_is_active, 1), NOW(6));

        SELECT id, name, slug, description, icon, is_active, created_at
        FROM tbl_categories
        WHERE id = LAST_INSERT_ID();
    ELSE
        UPDATE tbl_categories
        SET name = p_name,
            slug = p_slug,
            description = p_description,
            icon = p_icon,
            is_active = COALESCE(p_is_active, is_active)
        WHERE id = p_id;

        SELECT id, name, slug, description, icon, is_active, created_at
        FROM tbl_categories
        WHERE id = p_id;
    END IF;
END$$

DELIMITER ;


-- >>> BẮT ĐẦU NỘI DUNG: database/procedures/02_services_bookings.sql <<<
-- ==============================================================================
-- PHÂN HỆ TV2 (Phạm Thiên Long): QUẢN LÝ TOUR, LƯU TRÚ & LỊCH KHỞI HÀNH
-- Mã Commit: TV2-01
-- File: database/procedures/02_services_bookings.sql
-- Naming: ptl_sp_*
-- Gồm 6 Stored Procedures:
--   1. ptl_sp_services_create: Local Host tạo gói trải nghiệm (kèm tọa độ Maps)
--   2. ptl_sp_services_update: Local Host cập nhật thông tin gói tour & tọa độ Maps
--   3. ptl_sp_services_moderate: Admin kiểm duyệt tour (approved, rejected, pending)
--   4. ptl_sp_accommodations_save: Thiết lập Homestay nhà dân bản địa (1-1)
--   5. ptl_sp_schedules_create: Local Host mở lịch khởi hành mới cho tour
--   6. ptl_sp_schedules_cancel: Host hủy lịch khởi hành (tự hủy booking pending)
-- ==============================================================================

SET NAMES utf8mb4;
DELIMITER $$

-- ------------------------------------------------------------------------------
-- 1. ptl_sp_services_create: Local Host tạo gói trải nghiệm (kèm tọa độ Google Maps)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ptl_sp_services_create$$
CREATE PROCEDURE ptl_sp_services_create(
    IN p_host_id BIGINT UNSIGNED,
    IN p_category_id BIGINT UNSIGNED,
    IN p_title VARCHAR(255),
    IN p_slug VARCHAR(280),
    IN p_description LONGTEXT,
    IN p_price_mode VARCHAR(20),
    IN p_base_price DECIMAL(14,0),
    IN p_min_price DECIMAL(14,0),
    IN p_max_price DECIMAL(14,0),
    IN p_location VARCHAR(255),
    IN p_latitude DECIMAL(10, 8),
    IN p_longitude DECIMAL(11, 8),
    IN p_duration_days INT,
    IN p_banner_image VARCHAR(255)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_host_id;
    IF v_role != 'host' AND v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_HOST';
    END IF;

    IF p_price_mode NOT IN ('free', 'host_range', 'fixed_choice', 'prepaid_full') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_PRICE_MODE';
    END IF;

    IF p_duration_days < 1 THEN
        SET p_duration_days = 1;
    END IF;

    INSERT INTO tbl_services (
        host_id, category_id, title, slug, description,
        price_mode, base_price, min_price, max_price,
        location, latitude, longitude, duration_days, banner_image, status,
        created_at, updated_at
    ) VALUES (
        p_host_id, p_category_id, p_title, p_slug, p_description,
        p_price_mode, p_base_price, p_min_price, p_max_price,
        p_location, p_latitude, p_longitude, p_duration_days, p_banner_image, 'pending',
        NOW(6), NOW(6)
    );

    SELECT * FROM tbl_services WHERE id = LAST_INSERT_ID();
END$$

-- ------------------------------------------------------------------------------
-- 2. ptl_sp_services_update: Local Host cập nhật thông tin gói tour & tọa độ Maps
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ptl_sp_services_update$$
CREATE PROCEDURE ptl_sp_services_update(
    IN p_host_id BIGINT UNSIGNED,
    IN p_id BIGINT UNSIGNED,
    IN p_category_id BIGINT UNSIGNED,
    IN p_title VARCHAR(255),
    IN p_slug VARCHAR(280),
    IN p_description LONGTEXT,
    IN p_price_mode VARCHAR(20),
    IN p_base_price DECIMAL(14,0),
    IN p_min_price DECIMAL(14,0),
    IN p_max_price DECIMAL(14,0),
    IN p_location VARCHAR(255),
    IN p_latitude DECIMAL(10, 8),
    IN p_longitude DECIMAL(11, 8),
    IN p_duration_days INT,
    IN p_banner_image VARCHAR(255)
)
BEGIN
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_role VARCHAR(16);

    SELECT host_id INTO v_owner FROM tbl_services WHERE id = p_id;
    IF v_owner IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SERVICE_NOT_FOUND';
    END IF;

    SELECT role INTO v_role FROM tbl_users WHERE id = p_host_id;

    IF v_owner != p_host_id AND v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_OWNER';
    END IF;

    IF p_price_mode NOT IN ('free', 'host_range', 'fixed_choice', 'prepaid_full') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_PRICE_MODE';
    END IF;

    IF p_duration_days < 1 THEN
        SET p_duration_days = 1;
    END IF;

    UPDATE tbl_services
    SET category_id = p_category_id,
        title = p_title,
        slug = p_slug,
        description = p_description,
        price_mode = p_price_mode,
        base_price = p_base_price,
        min_price = p_min_price,
        max_price = p_max_price,
        location = p_location,
        latitude = p_latitude,
        longitude = p_longitude,
        duration_days = p_duration_days,
        banner_image = CASE WHEN p_banner_image IS NOT NULL AND p_banner_image != '' THEN p_banner_image ELSE banner_image END,
        updated_at = NOW(6)
    WHERE id = p_id;

    SELECT * FROM tbl_services WHERE id = p_id;
END$$

-- ------------------------------------------------------------------------------
-- 3. ptl_sp_services_moderate: Admin kiểm duyệt tour (Duyệt / Từ chối mở bán)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ptl_sp_services_moderate$$
CREATE PROCEDURE ptl_sp_services_moderate(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_id BIGINT UNSIGNED,
    IN p_status VARCHAR(20),
    IN p_rejection_reason TEXT
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    IF NOT EXISTS(SELECT 1 FROM tbl_services WHERE id = p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SERVICE_NOT_FOUND';
    END IF;

    IF p_status NOT IN ('draft', 'pending', 'approved', 'rejected', 'archived') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_STATUS';
    END IF;

    UPDATE tbl_services
    SET status = p_status,
        rejection_reason = p_rejection_reason,
        updated_at = NOW(6)
    WHERE id = p_id;

    SELECT id, title, status, rejection_reason, updated_at
    FROM tbl_services
    WHERE id = p_id;
END$$

-- ------------------------------------------------------------------------------
-- 4. ptl_sp_accommodations_save: Thiết lập Homestay nhà dân bản địa (1-1)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ptl_sp_accommodations_save$$
CREATE PROCEDURE ptl_sp_accommodations_save(
    IN p_host_id BIGINT UNSIGNED,
    IN p_service_id BIGINT UNSIGNED,
    IN p_homestay_name VARCHAR(200),
    IN p_address VARCHAR(255),
    IN p_room_type VARCHAR(100),
    IN p_nights INT,
    IN p_notes TEXT
)
BEGIN
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_role VARCHAR(16);

    SELECT host_id INTO v_owner FROM tbl_services WHERE id = p_service_id;
    IF v_owner IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SERVICE_NOT_FOUND';
    END IF;

    SELECT role INTO v_role FROM tbl_users WHERE id = p_host_id;

    IF v_owner != p_host_id AND v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_OWNER';
    END IF;

    IF p_nights < 1 THEN
        SET p_nights = 1;
    END IF;

    INSERT INTO tbl_service_accommodations (service_id, homestay_name, address, room_type, nights, notes, created_at)
    VALUES (p_service_id, p_homestay_name, p_address, p_room_type, p_nights, p_notes, NOW(6))
    ON DUPLICATE KEY UPDATE
        homestay_name = VALUES(homestay_name),
        address = VALUES(address),
        room_type = VALUES(room_type),
        nights = VALUES(nights),
        notes = VALUES(notes);

    SELECT * FROM tbl_service_accommodations WHERE service_id = p_service_id;
END$$

-- ------------------------------------------------------------------------------
-- 5. ptl_sp_schedules_create: Local Host mở lịch khởi hành mới cho tour
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ptl_sp_schedules_create$$
CREATE PROCEDURE ptl_sp_schedules_create(
    IN p_host_id BIGINT UNSIGNED,
    IN p_service_id BIGINT UNSIGNED,
    IN p_starts_at DATETIME(6),
    IN p_ends_at DATETIME(6),
    IN p_booking_closes_at DATETIME(6),
    IN p_capacity INT,
    IN p_meeting_point VARCHAR(255)
)
BEGIN
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_role VARCHAR(16);

    SELECT host_id INTO v_owner FROM tbl_services WHERE id = p_service_id;
    IF v_owner IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SERVICE_NOT_FOUND';
    END IF;

    SELECT role INTO v_role FROM tbl_users WHERE id = p_host_id;

    IF v_owner != p_host_id AND v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_OWNER';
    END IF;

    IF p_starts_at >= p_ends_at OR p_booking_closes_at > p_starts_at THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_SCHEDULE_TIME';
    END IF;

    IF p_capacity < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_CAPACITY';
    END IF;

    INSERT INTO tbl_service_schedules (
        service_id, starts_at, ends_at, booking_closes_at,
        capacity, booked_seats, meeting_point, status, created_at
    ) VALUES (
        p_service_id, p_starts_at, p_ends_at, p_booking_closes_at,
        p_capacity, 0, p_meeting_point, 'open', NOW(6)
    );

    SELECT * FROM tbl_service_schedules WHERE id = LAST_INSERT_ID();
END$$

-- ------------------------------------------------------------------------------
-- 6. ptl_sp_schedules_cancel: Host hủy một lịch khởi hành (do thời tiết/bất khả kháng)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ptl_sp_schedules_cancel$$
CREATE PROCEDURE ptl_sp_schedules_cancel(
    IN p_host_id BIGINT UNSIGNED,
    IN p_schedule_id BIGINT UNSIGNED,
    IN p_cancel_reason TEXT
)
BEGIN
    DECLARE v_service_id BIGINT UNSIGNED;
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_role VARCHAR(16);

    SELECT service_id INTO v_service_id FROM tbl_service_schedules WHERE id = p_schedule_id;
    IF v_service_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SCHEDULE_NOT_FOUND';
    END IF;

    SELECT host_id INTO v_owner FROM tbl_services WHERE id = v_service_id;
    SELECT role INTO v_role FROM tbl_users WHERE id = p_host_id;

    IF v_owner != p_host_id AND v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_OWNER';
    END IF;

    UPDATE tbl_service_schedules
    SET status = 'cancelled',
        cancel_reason = p_cancel_reason
    WHERE id = p_schedule_id;

    -- Tự động hủy các booking đang chờ/chưa hoàn tất của lịch này
    UPDATE tbl_bookings
    SET status = 'cancelled',
        cancel_reason = CONCAT('Lịch trình bị hủy bởi Host: ', COALESCE(p_cancel_reason, ''))
    WHERE schedule_id = p_schedule_id AND status IN ('pending', 'awaiting_payment');

    SELECT id, service_id, starts_at, status, cancel_reason
    FROM tbl_service_schedules
    WHERE id = p_schedule_id;
END$$

DELIMITER ;


-- >>> BẮT ĐẦU NỘI DUNG: database/procedures/03_promotions_payments.sql <<<
-- ==============================================================================
-- PHÂN HỆ TV3 (Nguyễn Duy Thành Tài): ĐẶT CHỖ, BÁO GIÁ & THANH TOÁN MOMO
-- Mã Commit: TV3-01
-- File: database/procedures/03_promotions_payments.sql
-- Naming: ndtt_sp_*
-- Gồm 6 Stored Procedures:
--   1. ndtt_sp_bookings_quote: Tính toán báo giá, khuyến mãi áp dụng và chia khoản trả (50%/100%)
--   2. ndtt_sp_bookings_create: Du khách gửi yêu cầu đặt tour (trạng thái pending)
--   3. ndtt_sp_bookings_approve: Host duyệt booking và giữ chỗ 15 phút (khóa concurrency)
--   4. ndtt_sp_bookings_cancel: Hủy booking và nhả lại số chỗ đã giữ vào lịch trình
--   5. ndtt_sp_payments_apply_verified: Áp dụng MoMo IPN thành công và sinh biên nhận điện tử
--   6. ndtt_sp_refunds_create_and_approve: Admin duyệt lệnh hoàn tiền MoMo Sandbox
-- ==============================================================================

SET NAMES utf8mb4;
DELIMITER $$

-- ------------------------------------------------------------------------------
-- 1. ndtt_sp_bookings_quote: Tính toán báo giá, khuyến mãi áp dụng và chia khoản trả
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ndtt_sp_bookings_quote$$
CREATE PROCEDURE ndtt_sp_bookings_quote(
    IN p_schedule_id BIGINT UNSIGNED,
    IN p_count INT,
    IN p_plan VARCHAR(20)
)
BEGIN
    DECLARE v_service_id BIGINT UNSIGNED;
    DECLARE v_base_price DECIMAL(14,0);
    DECLARE v_price_mode VARCHAR(20);
    DECLARE v_discount_percent DECIMAL(5,2) DEFAULT 0;
    DECLARE v_gross_total DECIMAL(14,0);
    DECLARE v_discount_amount DECIMAL(14,0) DEFAULT 0;
    DECLARE v_total_price DECIMAL(14,0);
    DECLARE v_deposit_amount DECIMAL(14,0) DEFAULT 0;
    DECLARE v_remaining_amount DECIMAL(14,0) DEFAULT 0;

    IF p_count < 1 THEN
        SET p_count = 1;
    END IF;

    SELECT s.id, s.base_price, s.price_mode
    INTO v_service_id, v_base_price, v_price_mode
    FROM tbl_services s
    JOIN tbl_service_schedules sch ON s.id = sch.service_id
    WHERE sch.id = p_schedule_id;

    IF v_service_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SCHEDULE_NOT_FOUND';
    END IF;

    -- Tìm khuyến mãi cao nhất còn hiệu lực cho dịch vụ này
    SELECT COALESCE(MAX(p.discount_percent), 0) INTO v_discount_percent
    FROM tbl_promotions p
    JOIN tbl_promotion_services ps ON p.id = ps.promotion_id
    WHERE ps.service_id = v_service_id
      AND p.is_active = 1
      AND NOW(6) BETWEEN p.starts_at AND p.ends_at;

    IF v_price_mode = 'free' THEN
        SET v_base_price = 0;
        SET v_gross_total = 0;
        SET v_discount_amount = 0;
        SET v_total_price = 0;
    ELSE
        SET v_gross_total = v_base_price * p_count;
        SET v_discount_amount = ROUND(v_gross_total * (v_discount_percent / 100));
        SET v_total_price = v_gross_total - v_discount_amount;
    END IF;

    IF p_plan = 'deposit_50' THEN
        SET v_deposit_amount = ROUND(v_total_price / 2);
        SET v_remaining_amount = v_total_price - v_deposit_amount;
    ELSEIF p_plan = 'full_online' THEN
        SET v_deposit_amount = v_total_price;
        SET v_remaining_amount = 0;
    ELSE
        SET v_deposit_amount = 0;
        SET v_remaining_amount = v_total_price;
    END IF;

    SELECT
        v_service_id AS service_id,
        p_schedule_id AS schedule_id,
        p_count AS participants_count,
        v_base_price AS unit_price,
        v_discount_percent AS discount_percent,
        v_discount_amount AS discount_amount,
        v_total_price AS total_price,
        v_deposit_amount AS deposit_amount,
        v_remaining_amount AS remaining_amount,
        p_plan AS payment_plan;
END$$

-- ------------------------------------------------------------------------------
-- 2. ndtt_sp_bookings_create: Du khách gửi yêu cầu đặt tour (trạng thái pending)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ndtt_sp_bookings_create$$
CREATE PROCEDURE ndtt_sp_bookings_create(
    IN p_tourist_id BIGINT UNSIGNED,
    IN p_schedule_id BIGINT UNSIGNED,
    IN p_participants_count INT,
    IN p_payment_plan VARCHAR(20)
)
BEGIN
    DECLARE v_service_id BIGINT UNSIGNED;
    DECLARE v_base_price DECIMAL(14,0);
    DECLARE v_price_mode VARCHAR(20);
    DECLARE v_discount_percent DECIMAL(5,2) DEFAULT 0;
    DECLARE v_gross_total DECIMAL(14,0);
    DECLARE v_discount_amount DECIMAL(14,0) DEFAULT 0;
    DECLARE v_total_price DECIMAL(14,0);
    DECLARE v_deposit_amount DECIMAL(14,0) DEFAULT 0;
    DECLARE v_remaining_amount DECIMAL(14,0) DEFAULT 0;
    DECLARE v_code VARCHAR(32);
    DECLARE v_sched_status VARCHAR(16);
    DECLARE v_closes_at DATETIME(6);

    IF p_participants_count < 1 THEN
        SET p_participants_count = 1;
    END IF;

    -- Kiểm tra lịch trình còn nhận đăng ký không
    SELECT service_id, status, booking_closes_at
    INTO v_service_id, v_sched_status, v_closes_at
    FROM tbl_service_schedules
    WHERE id = p_schedule_id;

    IF v_service_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SCHEDULE_NOT_FOUND';
    END IF;

    IF v_sched_status != 'open' OR NOW(6) >= v_closes_at THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'SCHEDULE_CLOSED';
    END IF;

    SELECT base_price, price_mode INTO v_base_price, v_price_mode FROM tbl_services WHERE id = v_service_id;

    SELECT COALESCE(MAX(p.discount_percent), 0) INTO v_discount_percent
    FROM tbl_promotions p
    JOIN tbl_promotion_services ps ON p.id = ps.promotion_id
    WHERE ps.service_id = v_service_id
      AND p.is_active = 1
      AND NOW(6) BETWEEN p.starts_at AND p.ends_at;

    IF v_price_mode = 'free' THEN
        SET v_base_price = 0;
        SET v_gross_total = 0;
        SET v_discount_amount = 0;
        SET v_total_price = 0;
    ELSE
        SET v_gross_total = v_base_price * p_participants_count;
        SET v_discount_amount = ROUND(v_gross_total * (v_discount_percent / 100));
        SET v_total_price = v_gross_total - v_discount_amount;
    END IF;

    IF p_payment_plan = 'deposit_50' THEN
        SET v_deposit_amount = ROUND(v_total_price / 2);
        SET v_remaining_amount = v_total_price - v_deposit_amount;
    ELSEIF p_payment_plan = 'full_online' THEN
        SET v_deposit_amount = v_total_price;
        SET v_remaining_amount = 0;
    ELSE
        SET v_deposit_amount = 0;
        SET v_remaining_amount = v_total_price;
    END IF;

    SET v_code = CONCAT('BK', DATE_FORMAT(NOW(), '%y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));

    INSERT INTO tbl_bookings (
        booking_code, tourist_id, schedule_id, service_id,
        participants_count, unit_price, discount_amount,
        total_price, deposit_amount, remaining_amount,
        payment_plan, status, created_at, updated_at
    ) VALUES (
        v_code, p_tourist_id, p_schedule_id, v_service_id,
        p_participants_count, v_base_price, v_discount_amount,
        v_total_price, v_deposit_amount, v_remaining_amount,
        p_payment_plan, 'pending', NOW(6), NOW(6)
    );

    SELECT * FROM tbl_bookings WHERE id = LAST_INSERT_ID();
END$$

-- ------------------------------------------------------------------------------
-- 3. ndtt_sp_bookings_approve: Host duyệt booking và giữ chỗ (có khóa concurrency)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ndtt_sp_bookings_approve$$
CREATE PROCEDURE ndtt_sp_bookings_approve(
    IN p_host_id BIGINT UNSIGNED,
    IN p_booking_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_schedule_id BIGINT UNSIGNED;
    DECLARE v_service_id BIGINT UNSIGNED;
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_count INT;
    DECLARE v_capacity INT;
    DECLARE v_booked INT;
    DECLARE v_plan VARCHAR(20);
    DECLARE v_status VARCHAR(20);
    DECLARE v_new_status VARCHAR(20);
    DECLARE v_expires DATETIME(6) DEFAULT NULL;

    SELECT b.schedule_id, b.service_id, b.participants_count, b.payment_plan, b.status, s.host_id
    INTO v_schedule_id, v_service_id, v_count, v_plan, v_status, v_owner
    FROM tbl_bookings b
    JOIN tbl_services s ON b.service_id = s.id
    WHERE b.id = p_booking_id;

    IF v_schedule_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'BOOKING_NOT_FOUND';
    END IF;

    IF v_owner != p_host_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_HOST';
    END IF;

    IF v_status != 'pending' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'BOOKING_NOT_PENDING';
    END IF;

    -- Khóa hàng lịch trình để kiểm tra sức chứa đồng thời chống overbooking
    SELECT capacity, booked_seats INTO v_capacity, v_booked
    FROM tbl_service_schedules
    WHERE id = v_schedule_id
    FOR UPDATE;

    IF (v_booked + v_count) > v_capacity THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'NOT_ENOUGH_SEATS';
    END IF;

    -- Tăng số chỗ đã đặt trong lịch
    UPDATE tbl_service_schedules
    SET booked_seats = booked_seats + v_count
    WHERE id = v_schedule_id;

    IF v_plan = 'none' OR v_plan = 'host_direct' THEN
        SET v_new_status = 'confirmed';
    ELSE
        SET v_new_status = 'awaiting_payment';
        SET v_expires = DATE_ADD(NOW(6), INTERVAL 15 MINUTE);
    END IF;

    UPDATE tbl_bookings
    SET status = v_new_status,
        payment_expires_at = v_expires,
        updated_at = NOW(6)
    WHERE id = p_booking_id;

    SELECT id, booking_code, status, payment_expires_at, total_price, deposit_amount
    FROM tbl_bookings
    WHERE id = p_booking_id;
END$$

-- ------------------------------------------------------------------------------
-- 4. ndtt_sp_bookings_cancel: Hủy booking và nhả lại số chỗ đã giữ
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ndtt_sp_bookings_cancel$$
CREATE PROCEDURE ndtt_sp_bookings_cancel(
    IN p_actor_id BIGINT UNSIGNED,
    IN p_booking_id BIGINT UNSIGNED,
    IN p_reason TEXT
)
BEGIN
    DECLARE v_tourist_id BIGINT UNSIGNED;
    DECLARE v_schedule_id BIGINT UNSIGNED;
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_count INT;
    DECLARE v_status VARCHAR(20);
    DECLARE v_role VARCHAR(16);

    SELECT b.tourist_id, b.schedule_id, b.participants_count, b.status, s.host_id
    INTO v_tourist_id, v_schedule_id, v_count, v_status, v_owner
    FROM tbl_bookings b
    JOIN tbl_services s ON b.service_id = s.id
    WHERE b.id = p_booking_id
    FOR UPDATE;

    IF v_schedule_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'BOOKING_NOT_FOUND';
    END IF;

    SELECT role INTO v_role FROM tbl_users WHERE id = p_actor_id;

    IF p_actor_id != v_tourist_id AND p_actor_id != v_owner AND v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    IF v_status = 'cancelled' OR v_status = 'completed' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_BOOKING_STATUS';
    END IF;

    -- Nếu đã từng giữ chỗ thì nhả lại số chỗ trống vào lịch
    IF v_status IN ('awaiting_payment', 'confirmed') THEN
        UPDATE tbl_service_schedules
        SET booked_seats = GREATEST(0, booked_seats - v_count)
        WHERE id = v_schedule_id;
    END IF;

    UPDATE tbl_bookings
    SET status = 'cancelled',
        cancel_reason = p_reason,
        updated_at = NOW(6)
    WHERE id = p_booking_id;

    SELECT id, booking_code, status, cancel_reason
    FROM tbl_bookings
    WHERE id = p_booking_id;
END$$

-- ------------------------------------------------------------------------------
-- 5. ndtt_sp_payments_apply_verified: Áp dụng kết quả MoMo IPN thành công và phát hành biên nhận
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ndtt_sp_payments_apply_verified$$
CREATE PROCEDURE ndtt_sp_payments_apply_verified(
    IN p_booking_id BIGINT UNSIGNED,
    IN p_order_id VARCHAR(64),
    IN p_request_id VARCHAR(64),
    IN p_amount DECIMAL(14,0),
    IN p_momo_trans_id VARCHAR(64)
)
BEGIN
    DECLARE v_payment_id BIGINT UNSIGNED;
    DECLARE v_receipt_code VARCHAR(32);

    IF NOT EXISTS(SELECT 1 FROM tbl_bookings WHERE id = p_booking_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'BOOKING_NOT_FOUND';
    END IF;

    -- Cập nhật hoặc tạo bản ghi payment thành công
    INSERT INTO tbl_payments (
        booking_id, order_id, request_id, amount,
        payment_method, status, momo_trans_id, paid_at, created_at
    ) VALUES (
        p_booking_id, p_order_id, p_request_id, p_amount,
        'momo_sandbox', 'succeeded', p_momo_trans_id, NOW(6), NOW(6)
    )
    ON DUPLICATE KEY UPDATE
        status = 'succeeded',
        momo_trans_id = VALUES(momo_trans_id),
        paid_at = NOW(6);

    SELECT id INTO v_payment_id FROM tbl_payments WHERE order_id = p_order_id LIMIT 1;

    -- Chuyển trạng thái booking sang confirmed
    UPDATE tbl_bookings
    SET status = 'confirmed',
        payment_expires_at = NULL,
        updated_at = NOW(6)
    WHERE id = p_booking_id;

    -- Tự động sinh biên nhận điện tử
    SET v_receipt_code = CONCAT('RC', DATE_FORMAT(NOW(), '%y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));

    INSERT INTO tbl_receipts (receipt_code, payment_id, booking_id, total_amount, issued_at, note)
    VALUES (v_receipt_code, v_payment_id, p_booking_id, p_amount, NOW(6), 'BIÊN NHẬN THANH TOÁN MÔI TRƯỜNG SANDBOX');

    SELECT p.id AS payment_id, p.status AS payment_status, r.receipt_code, b.status AS booking_status
    FROM tbl_payments p
    JOIN tbl_receipts r ON p.id = r.payment_id
    JOIN tbl_bookings b ON p.booking_id = b.id
    WHERE p.id = v_payment_id;
END$$

-- ------------------------------------------------------------------------------
-- 6. ndtt_sp_refunds_create_and_approve: Admin duyệt lệnh hoàn tiền MoMo Sandbox
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS ndtt_sp_refunds_create_and_approve$$
CREATE PROCEDURE ndtt_sp_refunds_create_and_approve(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_payment_id BIGINT UNSIGNED,
    IN p_amount DECIMAL(14,0),
    IN p_reason TEXT,
    IN p_admin_note TEXT
)
BEGIN
    DECLARE v_role VARCHAR(16);
    DECLARE v_booking_id BIGINT UNSIGNED;
    DECLARE v_refund_code VARCHAR(32);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    SELECT booking_id INTO v_booking_id FROM tbl_payments WHERE id = p_payment_id;
    IF v_booking_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'PAYMENT_NOT_FOUND';
    END IF;

    SET v_refund_code = CONCAT('RF', DATE_FORMAT(NOW(), '%y%m%d'), LPAD(FLOOR(RAND() * 10000), 4, '0'));

    INSERT INTO tbl_refunds (
        refund_code, payment_id, booking_id, amount,
        reason, status, admin_note, processed_at, created_at
    ) VALUES (
        v_refund_code, p_payment_id, v_booking_id, p_amount,
        p_reason, 'approved', p_admin_note, NOW(6), NOW(6)
    );

    SELECT * FROM tbl_refunds WHERE id = LAST_INSERT_ID();
END$$

DELIMITER ;


-- >>> BẮT ĐẦU NỘI DUNG: database/procedures/04_community_content.sql <<<
-- ==============================================================================
-- PHÂN HỆ TV4 (Phùng Cẩm Tân): COMMUNITY, REVIEWS, POSTS & PROMOTIONS STORED PROCEDURES
-- File: database/procedures/04_community_content.sql
-- Naming: pct_sp_*
-- ==============================================================================

DELIMITER $$

-- ------------------------------------------------------------------------------
-- 1. pct_sp_discussions_create: Thảo luận hỏi đáp 1 cấp dưới tour
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pct_sp_discussions_create$$
CREATE PROCEDURE pct_sp_discussions_create(
    IN p_user_id BIGINT UNSIGNED,
    IN p_service_id BIGINT UNSIGNED,
    IN p_parent_id BIGINT UNSIGNED,
    IN p_message TEXT
)
BEGIN
    DECLARE v_parent_service_id BIGINT UNSIGNED;

    -- Nếu là trả lời thì kiểm tra topic cha có cùng tour không
    IF p_parent_id IS NOT NULL AND p_parent_id > 0 THEN
        SELECT service_id INTO v_parent_service_id FROM tbl_discussions WHERE id = p_parent_id;
        IF v_parent_service_id != p_service_id THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'MISMATCH_DISCUSSION_PARENT';
        END IF;
    END IF;

    INSERT INTO tbl_discussions (service_id, user_id, parent_id, message, is_visible, created_at)
    VALUES (p_service_id, p_user_id, p_parent_id, p_message, 1, NOW(6));

    SELECT d.*, u.name AS user_name, u.avatar AS user_avatar
    FROM tbl_discussions d
    JOIN tbl_users u ON d.user_id = u.id
    WHERE d.id = LAST_INSERT_ID();
END$$

-- ------------------------------------------------------------------------------
-- 2. pct_sp_reviews_create: Du khách gửi đánh giá sau khi hoàn thành chuyến đi
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pct_sp_reviews_create$$
CREATE PROCEDURE pct_sp_reviews_create(
    IN p_tourist_id BIGINT UNSIGNED,
    IN p_booking_id BIGINT UNSIGNED,
    IN p_rating TINYINT,
    IN p_comment TEXT
)
BEGIN
    DECLARE v_owner BIGINT UNSIGNED;
    DECLARE v_service_id BIGINT UNSIGNED;
    DECLARE v_status VARCHAR(20);

    SELECT tourist_id, service_id, status
    INTO v_owner, v_service_id, v_status
    FROM tbl_bookings
    WHERE id = p_booking_id;

    IF v_owner != p_tourist_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN_NOT_BOOKING_OWNER';
    END IF;

    IF p_rating < 1 OR p_rating > 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_RATING_VALUE';
    END IF;

    INSERT INTO tbl_reviews (booking_id, tourist_id, service_id, rating, comment, is_visible, is_verified, created_at)
    VALUES (p_booking_id, p_tourist_id, v_service_id, p_rating, p_comment, 1, 1, NOW(6));

    SELECT r.*, u.name AS tourist_name, u.avatar AS tourist_avatar
    FROM tbl_reviews r
    JOIN tbl_users u ON r.tourist_id = u.id
    WHERE r.id = LAST_INSERT_ID();
END$$

-- ------------------------------------------------------------------------------
-- 3. pct_sp_posts_save: Admin thêm mới hoặc chỉnh sửa bài viết blog du lịch
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pct_sp_posts_save$$
CREATE PROCEDURE pct_sp_posts_save(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_id BIGINT UNSIGNED,
    IN p_title VARCHAR(255),
    IN p_slug VARCHAR(280),
    IN p_content LONGTEXT,
    IN p_thumbnail VARCHAR(255),
    IN p_is_published TINYINT(1)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    IF p_id IS NULL OR p_id = 0 THEN
        INSERT INTO tbl_posts (author_id, title, slug, content, thumbnail, is_published, created_at, updated_at)
        VALUES (p_admin_id, p_title, p_slug, p_content, p_thumbnail, COALESCE(p_is_published, 1), NOW(6), NOW(6));

        SELECT * FROM tbl_posts WHERE id = LAST_INSERT_ID();
    ELSE
        UPDATE tbl_posts
        SET title = p_title,
            slug = p_slug,
            content = p_content,
            thumbnail = COALESCE(p_thumbnail, thumbnail),
            is_published = COALESCE(p_is_published, is_published),
            updated_at = NOW(6)
        WHERE id = p_id;

        SELECT * FROM tbl_posts WHERE id = p_id;
    END IF;
END$$

-- ------------------------------------------------------------------------------
-- 4. pct_sp_posts_delete: Admin xóa bài viết blog du lịch
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pct_sp_posts_delete$$
CREATE PROCEDURE pct_sp_posts_delete(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    DELETE FROM tbl_posts WHERE id = p_id;
    SELECT p_id AS deleted_id;
END$$

-- ------------------------------------------------------------------------------
-- 5. pct_sp_promotions_save: Admin tạo mới hoặc cập nhật mã giảm giá coupon
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pct_sp_promotions_save$$
CREATE PROCEDURE pct_sp_promotions_save(
    IN p_admin_id BIGINT UNSIGNED,
    IN p_id BIGINT UNSIGNED,
    IN p_code VARCHAR(50),
    IN p_title VARCHAR(150),
    IN p_discount_percent DECIMAL(5,2),
    IN p_starts_at DATETIME(6),
    IN p_ends_at DATETIME(6),
    IN p_is_active TINYINT(1)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_admin_id;
    IF v_role != 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN';
    END IF;

    IF p_discount_percent <= 0 OR p_discount_percent > 90 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'INVALID_DISCOUNT_PERCENT';
    END IF;

    IF p_id IS NULL OR p_id = 0 THEN
        INSERT INTO tbl_promotions (code, title, discount_percent, starts_at, ends_at, is_active, created_at)
        VALUES (p_code, p_title, p_discount_percent, p_starts_at, p_ends_at, COALESCE(p_is_active, 1), NOW(6));

        SELECT * FROM tbl_promotions WHERE id = LAST_INSERT_ID();
    ELSE
        UPDATE tbl_promotions
        SET code = p_code,
            title = p_title,
            discount_percent = p_discount_percent,
            starts_at = p_starts_at,
            ends_at = p_ends_at,
            is_active = COALESCE(p_is_active, is_active)
        WHERE id = p_id;

        SELECT * FROM tbl_promotions WHERE id = p_id;
    END IF;
END$$

-- ------------------------------------------------------------------------------
-- 6. pct_sp_community_posts_create: 3 Vai trò (Tourist, Host, Admin) đăng bài chia sẻ
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pct_sp_community_posts_create$$
CREATE PROCEDURE pct_sp_community_posts_create(
    IN p_user_id BIGINT UNSIGNED,
    IN p_title VARCHAR(255),
    IN p_slug VARCHAR(280),
    IN p_content LONGTEXT,
    IN p_thumbnail VARCHAR(255)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_user_id;
    IF v_role IS NULL OR v_role NOT IN ('tourist', 'host', 'admin') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN: Chỉ thành viên đã đăng nhập (Tourist, Host, Admin) mới được đăng bài.';
    END IF;

    INSERT INTO tbl_posts (author_id, title, slug, content, thumbnail, is_published, views_count, created_at, updated_at)
    VALUES (p_user_id, p_title, p_slug, p_content, p_thumbnail, 1, 0, NOW(6), NOW(6));

    SELECT * FROM tbl_posts WHERE id = LAST_INSERT_ID();
END$$

DELIMITER ;


-- >>> BẮT ĐẦU NỘI DUNG: database/changes/05_reviews_enhancement.sql <<<
-- Migration 05: Enhance tbl_reviews for open reviews and verified traveler badge
ALTER TABLE tbl_reviews MODIFY booking_id BIGINT UNSIGNED NULL;

-- Add is_verified column if not exists
SET @dbname = DATABASE();
SET @tablename = 'tbl_reviews';
SET @columnname = 'is_verified';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE tbl_reviews ADD COLUMN is_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER is_visible'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


-- >>> BẮT ĐẦU NỘI DUNG: database/changes/06_community_posts.sql <<<
-- ==============================================================================
-- MIGRATION 06: STORED PROCEDURE CHO PHÉP 3 VAI TRÒ (TOURIST, HOST, ADMIN) ĐĂNG BÀI
-- ==============================================================================

DROP PROCEDURE IF EXISTS pct_sp_community_posts_create;

DELIMITER $$

CREATE PROCEDURE pct_sp_community_posts_create(
    IN p_user_id BIGINT UNSIGNED,
    IN p_title VARCHAR(255),
    IN p_slug VARCHAR(280),
    IN p_content LONGTEXT,
    IN p_thumbnail VARCHAR(255)
)
BEGIN
    DECLARE v_role VARCHAR(16);

    SELECT role INTO v_role FROM tbl_users WHERE id = p_user_id;
    IF v_role IS NULL OR v_role NOT IN ('tourist', 'host', 'admin') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'FORBIDDEN: Chỉ thành viên đã đăng nhập (Tourist, Host, Admin) mới được đăng bài.';
    END IF;

    INSERT INTO tbl_posts (author_id, title, slug, content, thumbnail, is_published, views_count, created_at, updated_at)
    VALUES (p_user_id, p_title, p_slug, p_content, p_thumbnail, 1, 0, NOW(6), NOW(6));

    SELECT * FROM tbl_posts WHERE id = LAST_INSERT_ID();
END$$

DELIMITER ;


-- >>> BẮT ĐẦU NỘI DUNG: database/changes/07_user_profile_enhancement.sql <<<
-- ==============================================================================
-- Migration: 07_user_profile_enhancement.sql
-- Thêm trường giới tính (gender) và ngày sinh (birth_date) cho tbl_users
-- Cập nhật Stored Procedure dhs_sp_users_update_profile & dhs_sp_users_find_login
-- ==============================================================================

-- 1. Bổ sung 2 cột gender và birth_date vào bảng tbl_users
ALTER TABLE tbl_users
ADD COLUMN IF NOT EXISTS gender ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other' AFTER bio,
ADD COLUMN IF NOT EXISTS birth_date DATE NULL AFTER gender;

-- 2. Cập nhật Stored Procedure dhs_sp_users_update_profile
DELIMITER $$

DROP PROCEDURE IF EXISTS dhs_sp_users_update_profile$$
CREATE PROCEDURE dhs_sp_users_update_profile(
    IN p_user_id BIGINT UNSIGNED,
    IN p_name VARCHAR(150),
    IN p_phone VARCHAR(32),
    IN p_living_area VARCHAR(255),
    IN p_bio TEXT,
    IN p_avatar VARCHAR(255),
    IN p_gender VARCHAR(16),
    IN p_birth_date DATE
)
BEGIN
    DECLARE v_clean_gender VARCHAR(16) DEFAULT 'other';

    IF p_gender IN ('male', 'female', 'other') THEN
        SET v_clean_gender = p_gender;
    END IF;

    UPDATE tbl_users
    SET name = p_name,
        phone = p_phone,
        living_area = p_living_area,
        bio = p_bio,
        avatar = COALESCE(p_avatar, avatar),
        gender = v_clean_gender,
        birth_date = p_birth_date,
        updated_at = NOW(6)
    WHERE id = p_user_id;

    SELECT id, role, name, email, phone, avatar, living_area, bio, gender, birth_date, status, created_at, updated_at
    FROM tbl_users
    WHERE id = p_user_id;
END$$

-- 3. Cập nhật Stored Procedure dhs_sp_users_find_login
DROP PROCEDURE IF EXISTS dhs_sp_users_find_login$$
CREATE PROCEDURE dhs_sp_users_find_login(
    IN p_email VARCHAR(254)
)
BEGIN
    SELECT id, role, name, email, password_hash, phone, avatar, living_area, bio, gender, birth_date, status
    FROM tbl_users
    WHERE email = p_email
    LIMIT 1;
END$$

DELIMITER ;



SET FOREIGN_KEY_CHECKS = 1;
-- HOÀN TẤT: Toàn bộ Database, Bảng và Stored Procedures đã đồng bộ utf8mb4_unicode_ci!
