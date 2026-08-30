-- =====================================================
-- Migration: Analytics module permissions + page_id on user_tracking
-- Date: 2026-08-30
-- =====================================================

-- 1. page_id on pageviews (filter from admin bar / ?page_id=)
SET @page_id_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'user_tracking'
    AND COLUMN_NAME = 'page_id'
);

SET @sql_add_page_id := IF(
  @page_id_exists = 0,
  'ALTER TABLE `user_tracking` ADD COLUMN `page_id` INT(11) DEFAULT NULL AFTER `session_id`, ADD INDEX `idx_page_id` (`page_id`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql_add_page_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Permission SELECT_ANALYTICS (seed does not include it)
INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_ANALYTICS', 'View analytics', 'analytics', 1
FROM DUAL
WHERE NOT EXISTS (
  SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_ANALYTICS'
);

-- 3. Assign to root (1) and Administrador (2)
INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ug.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup` ug
INNER JOIN `permisions` p ON p.`permision_name` = 'SELECT_ANALYTICS'
WHERE ug.`usergroup_id` IN (1, 2)
  AND NOT EXISTS (
    SELECT 1
    FROM `usergroup_permisions` ugp
    WHERE ugp.`usergroup_id` = ug.`usergroup_id`
      AND ugp.`permision_id` = p.`permisions_id`
  );
