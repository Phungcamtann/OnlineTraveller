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
