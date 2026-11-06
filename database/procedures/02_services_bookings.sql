-- ==============================================================================
-- PHÂN HỆ TV2 (Phạm Thiên Long): QUẢN LÝ TOUR, LƯU TRÚ & LỊCH KHỞI HÀNH
-- Mã Commit: TV2-01
-- File: database/procedures/02_services_bookings.sql
-- Gồm 6 Stored Procedures:
--   1. sp_services_create: Local Host tạo gói trải nghiệm (kèm tọa độ Maps)
--   2. sp_services_update: Local Host cập nhật thông tin gói tour & tọa độ Maps
--   3. sp_services_moderate: Admin kiểm duyệt tour (approved, rejected, pending)
--   4. sp_accommodations_save: Thiết lập Homestay nhà dân bản địa (1-1)
--   5. sp_schedules_create: Local Host mở lịch khởi hành mới cho tour
--   6. sp_schedules_cancel: Host hủy lịch khởi hành (tự hủy booking pending)
-- ==============================================================================

SET NAMES utf8mb4;
DELIMITER $$

-- ------------------------------------------------------------------------------
-- 1. sp_services_create: Local Host tạo gói trải nghiệm (kèm tọa độ Google Maps)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_services_create$$
CREATE PROCEDURE sp_services_create(
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
-- 2. sp_services_update: Local Host cập nhật thông tin gói tour & tọa độ Maps
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_services_update$$
CREATE PROCEDURE sp_services_update(
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
-- 3. sp_services_moderate: Admin kiểm duyệt tour (Duyệt / Từ chối mở bán)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_services_moderate$$
CREATE PROCEDURE sp_services_moderate(
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
-- 4. sp_accommodations_save: Thiết lập Homestay nhà dân bản địa (1-1)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_accommodations_save$$
CREATE PROCEDURE sp_accommodations_save(
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
-- 5. sp_schedules_create: Local Host mở lịch khởi hành mới cho tour
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_schedules_create$$
CREATE PROCEDURE sp_schedules_create(
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
-- 6. sp_schedules_cancel: Host hủy một lịch khởi hành (do thời tiết/bất khả kháng)
-- ------------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_schedules_cancel$$
CREATE PROCEDURE sp_schedules_cancel(
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
