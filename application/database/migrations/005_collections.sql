-- =====================================================
-- Migration: Collections (custom_model slug/template + item identity)
-- Date: 2026-08-30
-- slug uniqueness among non-deleted rows is enforced in PHP (MySQL 5.7 has no partial UNIQUE).
-- =====================================================

-- custom_model.slug
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model' AND COLUMN_NAME = 'slug'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model` ADD COLUMN `slug` varchar(120) NULL AFTER `form_name`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- custom_model.template
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model' AND COLUMN_NAME = 'template'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model` ADD COLUMN `template` varchar(80) NOT NULL DEFAULT ''default'' AFTER `form_description`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- custom_model.title_field
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model' AND COLUMN_NAME = 'title_field'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model` ADD COLUMN `title_field` varchar(80) NULL AFTER `template`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `custom_model`
SET `slug` = LOWER(REPLACE(REPLACE(TRIM(`form_name`), ' ', '_'), '-', '_'))
WHERE `slug` IS NULL OR `slug` = '';

UPDATE `custom_model` SET `slug` = 'card', `template` = 'cards', `form_name` = 'Cards'
  WHERE `custom_model_id` = 22;
UPDATE `custom_model` SET `slug` = 'home_portfolio', `template` = 'portfolio'
  WHERE `custom_model_id` = 25;

-- custom_model_content.title
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model_content' AND COLUMN_NAME = 'title'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model_content` ADD COLUMN `title` varchar(250) NULL AFTER `custom_model_id`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- custom_model_content.sort_order
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model_content' AND COLUMN_NAME = 'sort_order'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model_content` ADD COLUMN `sort_order` int(11) NOT NULL DEFAULT 0 AFTER `title`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- custom_model_content.featured
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model_content' AND COLUMN_NAME = 'featured'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model_content` ADD COLUMN `featured` tinyint(1) NOT NULL DEFAULT 0 AFTER `sort_order`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- custom_model_content.date_publish
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custom_model_content' AND COLUMN_NAME = 'date_publish'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `custom_model_content` ADD COLUMN `date_publish` timestamp NULL DEFAULT NULL AFTER `date_update`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `permisions` SET `label` = 'Add collection' WHERE `permision_name` = 'CREATE_FORM_CUSTOM';
UPDATE `permisions` SET `label` = 'Update collection' WHERE `permision_name` = 'UPDATE_FORM_CUSTOM';
UPDATE `permisions` SET `label` = 'Delete collection' WHERE `permision_name` = 'DELETE_FORM_CUSTOM';
UPDATE `permisions` SET `label` = 'View collections' WHERE `permision_name` = 'SELECT_FORM_CUSTOMS';
UPDATE `permisions` SET `label` = 'Publish collection' WHERE `permision_name` = 'PUBLISH_FORM_CUSTOM';
UPDATE `permisions` SET `label` = 'Add collection item' WHERE `permision_name` = 'CREATE_CONTENT_DATA';
UPDATE `permisions` SET `label` = 'Update collection item' WHERE `permision_name` = 'UPDATE_CONTENT_DATA';
UPDATE `permisions` SET `label` = 'Delete collection item' WHERE `permision_name` = 'DELETE_CONTENT_DATA';
UPDATE `permisions` SET `label` = 'View collection items' WHERE `permision_name` = 'SELECT_CONTENT_DATA';
