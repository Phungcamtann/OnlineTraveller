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
