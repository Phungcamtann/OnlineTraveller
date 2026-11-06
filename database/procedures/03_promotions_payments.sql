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
