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
-- 3. BẢNG tbl_services: Gói tour trải nghiệm do Local Host đăng ký
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
    duration_days INT NOT NULL DEFAULT 1,
    banner_image VARCHAR(255) NULL,
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
-- 4. BẢNG tbl_service_images: Bộ sưu tập hình ảnh chi tiết của tour
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_service_images;
CREATE TABLE tbl_service_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_service_images_service FOREIGN KEY (service_id) REFERENCES tbl_services(id) ON DELETE CASCADE,
    INDEX idx_service_images_sort (service_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. BẢNG tbl_service_accommodations: Khối thông tin lưu trú kèm theo tour (1-1)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS tbl_service_accommodations;
CREATE TABLE tbl_service_accommodations (
    service_id BIGINT UNSIGNED PRIMARY KEY,
    hotel_name VARCHAR(200) NOT NULL,
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
