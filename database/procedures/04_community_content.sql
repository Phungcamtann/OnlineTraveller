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

DELIMITER ;
