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
