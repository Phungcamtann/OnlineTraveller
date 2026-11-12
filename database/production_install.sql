-- ==============================================================================
-- ONLINETRAVELLER - BẢN CÀI ĐẶT CƠ SỞ DỮ LIỆU ĐẦY ĐỦ CHO PRODUCTION & PHPMYADMIN
-- Tích hợp: 15 Bảng, 22 Stored Procedures (Không DEFINER), 3 Bản nâng cấp, Seed Data (8 Tour Mẫu)
-- Hướng dẫn: Đăng nhập phpMyAdmin -> Chọn Database -> Tab Import -> Chọn file này
-- ==============================================================================

-- >>> BẮT ĐẦU NỘI DUNG: database/schema.sql <<<
-- ==============================================================================
-- CƠ SỞ DỮ LIỆU CHUẨN HÓA DỰ ÁN ONLINETRAVELLER
-- Phiên bản: 2.0 (Bản Tinh Chỉnh 15 Bảng)
-- Engine: MySQL InnoDB, Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. BẢNG tbl_users: Quản lý tài khoản 3 vai trò (tourist, host, admin)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_users;
CREATE TABLE tbl_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role ENUM('tourist', 'host', 'admin') NOT NULL DEFAULT 'tourist',
    name VARCHAR(150) NOT NULL,
    email VARCHAR(254) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NULL,
    avatar VARCHAR(255) NULL,
    living_area VARCHAR(255) NULL,
    bio TEXT NULL,
    status ENUM('active', 'inactive', 'locked') NOT NULL DEFAULT 'active',
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    INDEX idx_users_role_status (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. BẢNG tbl_categories: Danh mục phân loại tour (Biển, Núi, Ẩm thực, Văn hóa...)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_categories;
CREATE TABLE tbl_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(100) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. BẢNG tbl_services: Trải nghiệm bản địa do Local Host đăng ký (tọa độ Maps)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_services;
CREATE TABLE tbl_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    host_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    description LONGTEXT NOT NULL,
    price_mode ENUM('free', 'host_range', 'fixed_choice', 'prepaid_full') NOT NULL DEFAULT 'fixed_choice',
    base_price DECIMAL(14,0) NOT NULL DEFAULT 0,
    min_price DECIMAL(14,0) NOT NULL DEFAULT 0,
    max_price DECIMAL(14,0) NOT NULL DEFAULT 0,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    duration_days INT NOT NULL DEFAULT 1,
    banner_image VARCHAR(255) NULL,
    is_banner TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('draft', 'pending', 'approved', 'rejected', 'archived') NOT NULL DEFAULT 'draft',
    rejection_reason TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_services_host FOREIGN KEY (host_id) REFERENCES tbl_users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_services_category FOREIGN KEY (category_id) REFERENCES tbl_categories(id) ON DELETE RESTRICT,
    INDEX idx_services_status_category (status, category_id),
    INDEX idx_services_host (host_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. BẢNG tbl_service_images: Các Trạm Trình Chiếu 3D (Three.js Stops)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_service_images;
CREATE TABLE tbl_service_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NOT NULL DEFAULT '',
    story_text TEXT NULL,
    video_url VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_service_images_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE CASCADE,
    INDEX idx_service_images_sort (service_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. BẢNG tbl_service_accommodations: Lưu trú Homestay nhà dân bản địa (1-1)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_service_accommodations;
CREATE TABLE tbl_service_accommodations (
    service_id BIGINT UNSIGNED PRIMARY KEY,
    homestay_name VARCHAR(200) NOT NULL,
    address VARCHAR(255) NOT NULL,
    room_type VARCHAR(100) NOT NULL,
    nights INT NOT NULL DEFAULT 1,
    notes TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_accommodations_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. BẢNG tbl_service_schedules: Lịch khởi hành cụ thể của tour
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_service_schedules;
CREATE TABLE tbl_service_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    starts_at DATETIME(6) NOT NULL,
    ends_at DATETIME(6) NOT NULL,
    booking_closes_at DATETIME(6) NOT NULL,
    capacity INT NOT NULL,
    booked_seats INT NOT NULL DEFAULT 0,
    meeting_point VARCHAR(255) NOT NULL,
    status ENUM('open', 'closed', 'cancelled', 'completed') NOT NULL DEFAULT 'open',
    cancel_reason TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_schedules_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE RESTRICT,
    INDEX idx_schedules_service_starts (service_id, starts_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. BẢNG tbl_promotions: Mã khuyến mãi giảm giá theo phần trăm
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_promotions;
CREATE TABLE tbl_promotions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    discount_percent DECIMAL(5,2) NOT NULL,
    starts_at DATETIME(6) NOT NULL,
    ends_at DATETIME(6) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT chk_promotions_percent CHECK (discount_percent > 0 AND discount_percent <= 90)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. BẢNG tbl_promotion_services: Liên kết n-n giữa mã khuyến mãi và tour áp dụng
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_promotion_services;
CREATE TABLE tbl_promotion_services (
    promotion_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (promotion_id, service_id),
    CONSTRAINT fk_promo_services_promo FOREIGN KEY (promotion_id) REFERENCES tbl_promotions(id) ON DELETE CASCADE,
    CONSTRAINT fk_promo_services_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 9. BẢNG tbl_bookings: Hồ sơ đặt tour của du khách
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_bookings;
CREATE TABLE tbl_bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(32) NOT NULL UNIQUE,
    tourist_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    participants_count INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(14,0) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(14,0) NOT NULL DEFAULT 0,
    total_price DECIMAL(14,0) NOT NULL DEFAULT 0,
    deposit_amount DECIMAL(14,0) NOT NULL DEFAULT 0,
    remaining_amount DECIMAL(14,0) NOT NULL DEFAULT 0,
    payment_plan ENUM('none', 'host_direct', 'deposit_50', 'full_online') NOT NULL DEFAULT 'full_online',
    status ENUM('pending', 'awaiting_payment', 'confirmed', 'completed', 'cancelled', 'rejected', 'expired') NOT NULL DEFAULT 'pending',
    payment_expires_at DATETIME(6) NULL,
    rejection_reason TEXT NULL,
    cancel_reason TEXT NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_bookings_tourist FOREIGN KEY (tourist_id) REFERENCES tbl_users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_schedule FOREIGN KEY (schedule_id) REFERENCES tbl_service_schedules(id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE RESTRICT,
    INDEX idx_bookings_schedule_status (schedule_id, status),
    INDEX idx_bookings_tourist_created (tourist_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 10. BẢNG tbl_payments: Giao dịch thanh toán trực tuyến qua MoMo Sandbox
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_payments;
CREATE TABLE tbl_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    order_id VARCHAR(64) NOT NULL UNIQUE,
    request_id VARCHAR(64) NOT NULL UNIQUE,
    amount DECIMAL(14,0) NOT NULL,
    payment_method VARCHAR(32) NOT NULL DEFAULT 'momo_sandbox',
    status ENUM('created', 'pending', 'succeeded', 'failed', 'cancelled') NOT NULL DEFAULT 'created',
    momo_trans_id VARCHAR(64) NULL,
    pay_url TEXT NULL,
    response_message TEXT NULL,
    paid_at DATETIME(6) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES tbl_bookings(id) ON DELETE RESTRICT,
    INDEX idx_payments_booking_status (booking_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 11. BẢNG tbl_receipts: Biên nhận điện tử xác nhận thanh toán môi trường Sandbox
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_receipts;
CREATE TABLE tbl_receipts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    receipt_code VARCHAR(32) NOT NULL UNIQUE,
    payment_id BIGINT UNSIGNED NOT NULL UNIQUE,
    booking_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(14,0) NOT NULL,
    issued_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    note VARCHAR(255) NOT NULL DEFAULT 'BIÊN NHẬN THANH TOÁN MÔI TRƯỜNG SANDBOX',
    CONSTRAINT fk_receipts_payment FOREIGN KEY (payment_id) REFERENCES tbl_payments(id) ON DELETE RESTRICT,
    CONSTRAINT fk_receipts_booking FOREIGN KEY (booking_id) REFERENCES tbl_bookings(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 12. BẢNG tbl_refunds: Hồ sơ xử lý hoàn tiền sau khi hủy tour hợp lệ
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_refunds;
CREATE TABLE tbl_refunds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    refund_code VARCHAR(32) NOT NULL UNIQUE,
    payment_id BIGINT UNSIGNED NOT NULL,
    booking_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(14,0) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('requested', 'approved', 'processing', 'succeeded', 'failed', 'rejected') NOT NULL DEFAULT 'requested',
    momo_trans_id VARCHAR(64) NULL,
    admin_note TEXT NULL,
    processed_at DATETIME(6) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_refunds_payment FOREIGN KEY (payment_id) REFERENCES tbl_payments(id) ON DELETE RESTRICT,
    CONSTRAINT fk_refunds_booking FOREIGN KEY (booking_id) REFERENCES tbl_bookings(id) ON DELETE RESTRICT,
    INDEX idx_refunds_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 13. BẢNG tbl_reviews: Đánh giá & chấm điểm 1..5 sao sau chuyến đi
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_reviews;
CREATE TABLE tbl_reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL UNIQUE,
    tourist_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT NOT NULL,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES tbl_bookings(id) ON DELETE RESTRICT,
    CONSTRAINT fk_reviews_tourist FOREIGN KEY (tourist_id) REFERENCES tbl_users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_reviews_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE RESTRICT,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5),
    INDEX idx_reviews_service_visible (service_id, is_visible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 14. BẢNG tbl_discussions: Thảo luận hỏi đáp 1 cấp dưới tour (parent_id)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_discussions;
CREATE TABLE tbl_discussions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    message TEXT NOT NULL,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_discussions_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE RESTRICT,
    CONSTRAINT fk_discussions_user FOREIGN KEY (user_id) REFERENCES tbl_users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_discussions_parent FOREIGN KEY (parent_id) REFERENCES tbl_discussions(id) ON DELETE CASCADE,
    INDEX idx_discussions_service_parent (service_id, parent_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 15. BẢNG tbl_posts: Bài viết tin tức blog kinh nghiệm du lịch của Admin
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_posts;
CREATE TABLE tbl_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    thumbnail VARCHAR(255) NULL,
    views_count INT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES tbl_users(id) ON DELETE RESTRICT,
    INDEX idx_posts_published_created (is_published, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


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


-- >>> BẮT ĐẦU NỘI DUNG: database/seed_data.sql <<<
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
INSERT INTO tbl_services (id, host_id, category_id, title, slug, description, price_mode, base_price, min_price, max_price, location, latitude, longitude, duration_days, banner_image, is_banner, status) VALUES
(1, 6, 3, 'Săn Mây Cầu Đất & Khám Phá Nông Trại Cà Phê Arabica', 'san-may-cau-dat-kham-pha-nong-trai-ca-phe-arabica', 'Hành trình đưa du khách rời xa nhịp sống vội vã của phố thị để hòa mình vào đại ngàn thông reo vùng ngoại ô Cầu Đất. Bạn sẽ được tự tay hái những trái cà phê Arabica chín mọng dưới sương sớm, học cách rang xay thủ công cùng nông dân bản địa và thức giấc giữa biển mây bồng bềnh từ căn nhà gỗ trên sườn đồi.\n\n1. Ngày 1: Đón khách tại trung tâm – Tham quan đồi chè Cầu Đất – Trải nghiệm hái và rang cà phê tại nông trại – Nhận phòng homestay, tiệc nướng BBQ tối.\n2. Ngày 2: Săn mây bình minh tại thảm gỗ – Thưởng thức cà phê sáng và bữa sáng địa phương – Tham quan vườn hồng treo gió – Trả khách về điểm đón.', 'fixed_choice', 1450000, 0, 0, 'Quảng trường Lâm Viên (Cổng nụ hoa Atiso), Phường 10, TP. Đà Lạt, Lâm Đồng', 11.93600000, 108.44520000, 2, '/assets/images/tours/tour1/banner.jpg', 1, 'approved'),
(2, 6, 1, 'Dấu Ấn Cố Đô & Khám Phá Kiến Trúc Lăng Tẩm Triều Nguyễn', 'dau-an-co-do-kham-pha-kien-truc-lang-tam-trieu-nguyen', 'Chuyến du hành ngược dòng lịch sử trên chiếc thuyền rồng xuôi dòng sông Hương thơ mộng, lắng nghe những giai thoại cung đình ít người biết về các vị vua triều Nguyễn. Du khách không chỉ chiêm ngưỡng nghệ thuật khảm sành sứ tinh xảo mà còn thưởng thức nhã nhạc và bữa trưa cơm cung đình thanh tao.\n\n1. Sáng: Du thuyền sông Hương thăm chùa Thiên Mụ – Tham quan Đại Nội Huế (Ngọ Môn, Điện Thái Hòa).\n2. Trưa: Thưởng thức cơm cung đình và nghe ca Huế thính phòng.\n3. Chiều: Viếng Lăng Khải Định và Lăng Tự Đức – Ngắm hoàng hôn trên đồi Vọng Cảnh – Kết thúc tour.', 'fixed_choice', 650000, 0, 0, 'Bến thuyền Tòa Khâm, 49 Lê Lợi, Phú Hội, TP. Huế, Thừa Thiên Huế', 16.46870000, 107.59120000, 1, '/assets/images/tours/tour2/banner.jpg', 1, 'approved'),
(3, 6, 2, 'Food Tour Đêm Sài Gòn & Chuyện Hẻm Nhỏ Chợ Lớn', 'food-tour-dem-sai-gon-chuyen-hem-nho-cho-lon', 'Khám phá linh hồn ẩm thực đường phố Sài Gòn khi phố lên đèn qua những con hẻm trăm tuổi tại Quận 5 và Quận 10. Cùng người dẫn đường bản địa thưởng thức hơn 7 món đặc sản trứ danh từ sủi cảo gia truyền, hủ tiếu mì kéo tay đến chè người Hoa thanh mát, lắng nghe câu chuyện di cư và giao thoa văn hóa Việt – Hoa.\n\n1. 17:30: Tập trung tại Quận 1, di chuyển bằng xe máy cổ điển qua khu phố người Hoa.\n2. 18:30: Thưởng thức Sủi cảo Hà Tôn Quyền và phá lấu bò nước dừa.\n3. 20:00: Dạo phố đèn lồng Lương Nhữ Học – Ăn chè mè đen, chè hột gà trà.\n4. 21:30: Kết thúc hành trình tại chợ đêm Hồ Thị Kỷ với các món ăn vặt đường phố.', 'prepaid_full', 580000, 0, 0, 'Bưu điện Trung tâm Sài Gòn, 02 Công xã Paris, Bến Nghé, Quận 1, TP. Hồ Chí Minh', 10.77980000, 106.69900000, 1, '/assets/images/tours/tour3/banner.jpg', 1, 'approved'),
(4, 6, 5, 'Chạm Tay Vào Di Sản Gốm Thanh Hà & Làng Mộc Kim Bồng', 'cham-tay-vao-di-san-gom-thanh-ha-lang-moc-kim-bong', 'Rời khu phố cổ đông đúc để xuôi thuyền sang đôi bờ sông Thu Bồn, nơi những nghệ nhân vẫn ngày ngày giữ lửa cho các làng nghề truyền thống hơn 500 năm tuổi. Du khách được trực tiếp nhào đất sét trên bàn xoay thủ công, chạm khắc gỗ cùng thợ mộc Kim Bồng và mang về món quà lưu niệm do chính tay mình tạo tác.\n\n1. 08:00: Đón khách tại Chùa Cầu – Lên thuyền gỗ đi làng gốm Thanh Hà.\n2. 09:00: Học chuốt gốm thủ công cùng nghệ nhân cao niên và tham quan Công viên Đất nung.\n3. 11:30: Ăn trưa cao lầu và mì Quảng tại nhà dân bên sông.\n4. 13:30: Sang cù lao Cẩm Kim thăm làng mộc Kim Bồng và làng chiếu cói Bàn Thạch – 16:30 đưa khách về lại phố cổ.', 'fixed_choice', 520000, 0, 0, 'Chùa Cầu, Đường Nguyễn Thị Minh Khai, Phường Minh An, TP. Hội An, Quảng Nam', 15.87710000, 108.32590000, 1, '/assets/images/tours/tour4/banner.jpg', 1, 'approved'),
(5, 6, 4, 'Đảo Ngọc Hoang Sơ: Lặn Ngắm San Hô Hòn Móng Tay & Câu Mực Đêm', 'dao-ngoc-hoang-so-lan-ngam-san-ho-hon-mong-tay-cau-muc-dem', 'Trải nghiệm cuộc sống ngư dân miền biển đảo Nam An Thới với làn nước xanh ngọc bích trong vắt nhìn thấu đáy. Du khách sẽ lặn ngắm rạn san hô tự nhiên đầy màu sắc, tự tay chế biến hải sản vừa đánh bắt trên bè nổi và nghỉ đêm tại bungalow sinh thái sát bờ biển nghe tiếng sóng vỗ.\n\n1. Ngày 1: Đón khách tại cảng An Thới – Cano khám phá Hòn Móng Tay & Hòn Gầm Ghì – Lặn ngắm san hô – Nhận phòng Bungalow ven biển – Tối đi thuyền câu mực đêm.\n2. Ngày 2: Ngắm bình minh làng chài Hàm Ninh – Thưởng thức bún quậy Kiến Xây – Tham quan vườn tiêu sinh thái – Tiễn khách về điểm hẹn.', 'fixed_choice', 2150000, 0, 0, 'Cảng An Thới, Khu phố 1, Phường An Thới, TP. Phú Quốc, Kiên Giang', 10.02420000, 104.01180000, 2, '/assets/images/tours/tour5/banner.jpg', 0, 'approved'),
(6, 6, 3, 'Chinh Phục Cổng Trời Quản Bạ & Sống Chậm Bản Lô Lô Chải', 'chinh-phuc-cong-troi-quan-ba-song-cham-ban-lo-lo-chai', 'Hành trình qua Công viên Địa chất Toàn cầu Cao nguyên đá Đồng Văn hùng vĩ, chinh phục đèo Mã Pì Lèng hiểm trở và dừng chân tại ngôi làng cổ tích dưới chân cột cờ Lũng Cú. Bạn sẽ lưu trú trong ngôi nhà trình tường đất hàng trăm năm tuổi của người Lô Lô Hoa, quây quần bên bếp lửa hồng thưởng thức thắng cố và rượu ngô men lá.\n\n1. Ngày 1: Xuất phát từ Km0 – Dốc Bắc Sum – Cổng trời Quản Bạ – Phố Cáo – Nhận phòng homestay tại Lô Lô Chải.\n2. Ngày 2: Thăm Cột cờ Lũng Cú – Chinh phục đèo Mã Pì Lèng – Đi thuyền hẻm vực Tu Sản trên sông Nho Quế – Nghỉ đêm tại Đồng Văn.\n3. Ngày 3: Tham quan Dinh thự Vua Mèo – Phiên chợ vùng cao – Trở về TP. Hà Giang kết thúc hành trình.', 'host_range', 2890000, 2500000, 3200000, 'Cột mốc Km0 Hà Giang, Đường Nguyễn Trãi, Phường Nguyễn Trãi, TP. Hà Giang, Hà Giang', 22.82330000, 104.98390000, 3, '/assets/images/tours/tour6/banner.jpg', 0, 'approved'),
(7, 6, 4, 'Một Ngày Làm Nông Dân Xứ Dừa & Chèo Xuồng Rạch Nhỏ Bến Tre', 'mot-ngay-lam-nong-dan-xu-dua-cheo-xuong-rach-nho-ben-tre', 'Hành trình xanh mát đưa du khách len lỏi qua những con rạch nhỏ rợp bóng dừa nước đặc trưng của miền Tây sông nước. Bạn sẽ được tự tay hái trái cây miệt vườn, tát mương bắt cá lóc, thưởng thức đờn ca tài tử bên tách trà mật ong hoa nhãn và trải nghiệm làm kẹo dừa thủ công cùng bà con miệt vườn.\n\n1. 08:30: Lên thuyền máy trên sông Hàm Luông – Tham quan lò gạch thủ công và cơ sở kẹo dừa.\n2. 10:30: Đi xe lôi máy qua đường làng rợp bóng dừa – Tát mương bắt cá và nướng trui tại vườn.\n3. 12:30: Ăn trưa đặc sản cá tai tượng chiên xù, bánh xèo củ hủ dừa.\n4. 14:30: Chèo xuồng ba lá trong rạch dừa nước – Nghe đờn ca tài tử – 16:30 trở lại bến tàu.', 'fixed_choice', 490000, 0, 0, 'Bến tàu Du lịch Bến Tre, Đường Hùng Vương, Phường 5, TP. Bến Tre, Bến Tre', 10.24150000, 106.37580000, 1, '/assets/images/tours/tour7/banner.jpg', 0, 'approved'),
(8, 6, 5, 'Làng Lụa Vạn Phúc & Tinh Hoa Tranh Dân Gian Đông Hồ', 'lang-lua-van-phuc-tinh-hoa-tranh-dan-gian-dong-ho', 'Hành trình kết nối hai vùng đất di sản Kinh Kỳ – Kinh Bắc, đưa du khách tìm về cội nguồn của những tấm lụa Hà Đông mềm mại và những bức tranh khắc gỗ dân gian đầy triết lý. Du khách sẽ nghỉ đêm tại nhà vườn cổ vùng Kinh Bắc, nghe liền anh liền chị hát Quan họ giao duyên và tự tay in một bức tranh Đông Hồ trên giấy điệp óng ánh.\n\n1. Ngày 1: Đón khách tại Nhà hát Lớn – Tham quan Làng lụa Vạn Phúc (Hà Đông) – Di chuyển sang Bắc Ninh – Nhận phòng nhà vườn cổ Kinh Bắc – Thưởng thức cơm quê và giao lưu hát Quan họ tối.\n2. Ngày 2: Thăm Làng tranh Đông Hồ (Thuận Thành) – Tự tay in tranh trên giấy điệp – Viếng Chùa Bút Tháp – Trả khách về Hà Nội lúc 17:00.', 'fixed_choice', 1680000, 0, 0, 'Nhà hát Lớn Hà Nội, Số 01 Tràng Tiền, Phường Tràng Tiền, Quận Hoàn Kiếm, Hà Nội', 21.02450000, 105.85750000, 2, '/assets/images/tours/tour8/banner.jpg', 0, 'approved');

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


