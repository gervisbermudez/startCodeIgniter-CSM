-- =====================================================
-- Migration: Albums + Videos cut A (date_delete, status, permisions)
-- Date: 2026-08-31
-- MySQL 5.7. Idempotent. Apply by hand (CI3 migrations is not the flow).
-- =====================================================

-- date_delete in video (real soft delete)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'video' AND COLUMN_NAME = 'date_delete'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `video` ADD `date_delete` datetime DEFAULT NULL AFTER `date_update`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill: status 0 that is not a delete → draft 2
UPDATE `video` SET `status` = 2 WHERE `status` = 0 AND `date_delete` IS NULL;

-- Permisions (typo histórico)
INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_GALLERY', 'View albums', 'gallery', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_GALLERY');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_GALLERY', 'Add album', 'gallery', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_GALLERY');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_GALLERY', 'Update album', 'gallery', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_GALLERY');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_GALLERY', 'Delete album', 'gallery', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_GALLERY');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_VIDEOS', 'View videos', 'videos', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_VIDEOS');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_VIDEO', 'Add video', 'videos', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_VIDEO');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_VIDEO', 'Update video', 'videos', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_VIDEO');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_VIDEO', 'Delete video', 'videos', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_VIDEO');

-- Same usergroups that already have SELECT_PAGES
INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ugp.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup_permisions` ugp
INNER JOIN `permisions` sp ON sp.`permisions_id` = ugp.`permision_id` AND sp.`permision_name` = 'SELECT_PAGES'
INNER JOIN `permisions` p ON p.`permision_name` IN (
  'SELECT_GALLERY', 'CREATE_GALLERY', 'UPDATE_GALLERY', 'DELETE_GALLERY',
  'SELECT_VIDEOS', 'CREATE_VIDEO', 'UPDATE_VIDEO', 'DELETE_VIDEO'
)
WHERE NOT EXISTS (
  SELECT 1 FROM `usergroup_permisions` x
  WHERE x.`usergroup_id` = ugp.`usergroup_id`
    AND x.`permision_id` = p.`permisions_id`
);
