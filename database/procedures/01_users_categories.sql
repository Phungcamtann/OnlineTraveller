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
    SELECT id, role, name, email, password_hash, phone, avatar, living_area, bio, status
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
    IN p_avatar VARCHAR(255)
)
BEGIN
    UPDATE tbl_users
    SET name = p_name,
        phone = p_phone,
        living_area = p_living_area,
        bio = p_bio,
        avatar = COALESCE(p_avatar, avatar),
        updated_at = NOW(6)
    WHERE id = p_user_id;

    SELECT id, role, name, email, phone, avatar, living_area, bio, status
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
