-- =====================================================
-- Migration: Events core loop (date_start/end, slug, permisions)
-- Date: 2026-08-30
-- MySQL 5.7. Idempotent. Apply by hand (CI3 migrations is not the flow).
-- =====================================================

-- name / subtitle: seed names were truncated at 70
ALTER TABLE `events`
  MODIFY `name` varchar(250) NOT NULL,
  MODIFY `subtitle` varchar(250) NOT NULL DEFAULT '',
  MODIFY `date_publish` datetime DEFAULT NULL;

-- slug
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'slug'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `slug` varchar(191) NOT NULL DEFAULT '''' AFTER `name`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- date_start
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'date_start'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `date_start` datetime DEFAULT NULL AFTER `address`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- date_end
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'date_end'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `date_end` datetime DEFAULT NULL AFTER `date_start`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- all_day
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'all_day'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `all_day` tinyint(1) NOT NULL DEFAULT 0 AFTER `date_end`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- location_type
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'location_type'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `location_type` varchar(20) NOT NULL DEFAULT ''physical'' AFTER `all_day`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- online_url
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'online_url'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `online_url` varchar(500) DEFAULT NULL AFTER `location_type`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- date_delete (soft delete)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = 'date_delete'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `events` ADD `date_delete` datetime DEFAULT NULL AFTER `date_update`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill happening date: date_publish if set, else date_create. Published rows must not stay NULL.
UPDATE `events`
SET `date_start` = COALESCE(`date_publish`, `date_create`)
WHERE `date_start` IS NULL;

-- Unique ASCII slugs for existing rows (event-{id} is collision-free)
UPDATE `events`
SET `slug` = CONCAT('event-', `event_id`)
WHERE `slug` = '' OR `slug` IS NULL;

-- Unique slug (after backfill so empty duplicates cannot exist)
SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND INDEX_NAME = 'uq_events_slug'
);
SET @sql := IF(@idx_exists = 0,
  'ALTER TABLE `events` ADD UNIQUE KEY `uq_events_slug` (`slug`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Public listing index
SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND INDEX_NAME = 'idx_events_public'
);
SET @sql := IF(@idx_exists = 0,
  'ALTER TABLE `events` ADD KEY `idx_events_public` (`status`, `visibility`, `date_start`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Permisions (typo histórico)
INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'CREATE_EVENT', 'Add event', 'events', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'CREATE_EVENT');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'UPDATE_EVENT', 'Update event', 'events', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'UPDATE_EVENT');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'DELETE_EVENT', 'Delete event', 'events', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'DELETE_EVENT');

INSERT INTO `permisions` (`permision_name`, `label`, `module`, `status`)
SELECT 'SELECT_EVENTS', 'View events', 'events', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permisions` WHERE `permision_name` = 'SELECT_EVENTS');

-- Same usergroups that already have SELECT_PAGES
INSERT INTO `usergroup_permisions` (`usergroup_id`, `permision_id`, `status`)
SELECT ugp.`usergroup_id`, p.`permisions_id`, 1
FROM `usergroup_permisions` ugp
INNER JOIN `permisions` sp ON sp.`permisions_id` = ugp.`permision_id` AND sp.`permision_name` = 'SELECT_PAGES'
INNER JOIN `permisions` p ON p.`permision_name` IN ('CREATE_EVENT', 'UPDATE_EVENT', 'DELETE_EVENT', 'SELECT_EVENTS')
WHERE NOT EXISTS (
  SELECT 1 FROM `usergroup_permisions` x
  WHERE x.`usergroup_id` = ugp.`usergroup_id`
    AND x.`permision_id` = p.`permisions_id`
);
