-- =====================================================
-- Migration: Notifications inbox (per-user rows)
-- Date: 2026-08-30
-- =====================================================

-- 1. date_delete nullable (soft delete; do not stamp CURRENT_TIMESTAMP on insert)
SET @col_nullable := (
  SELECT IS_NULLABLE
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'notifications'
    AND COLUMN_NAME = 'date_delete'
);

SET @sql_date_delete := IF(
  @col_nullable = 'NO',
  'ALTER TABLE `notifications` MODIFY `date_delete` timestamp NULL DEFAULT NULL',
  'SELECT 1'
);
PREPARE stmt FROM @sql_date_delete;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Inbox lookup index
SET @idx_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'notifications'
    AND INDEX_NAME = 'idx_notifications_inbox'
);

SET @sql_add_idx := IF(
  @idx_exists = 0,
  'ALTER TABLE `notifications` ADD INDEX `idx_notifications_inbox` (`user_id`, `status`, `date_create`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql_add_idx;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
