-- =====================================================
-- Migration: Performance indexes (path, config, fragments, page_data)
-- Date: 2026-09-01
-- Additive only. Re-runnable on MySQL 5.7 (no ADD INDEX IF NOT EXISTS).
-- =====================================================

SET @exist := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'page' AND index_name = 'idx_page_path_status'
);
SET @sqlstmt := IF(@exist = 0,
  'ALTER TABLE `page` ADD KEY `idx_page_path_status` (`path`(191), `status`)',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'site_config' AND index_name = 'idx_site_config_name'
);
SET @sqlstmt := IF(@exist = 0,
  'ALTER TABLE `site_config` ADD KEY `idx_site_config_name` (`config_name`(191), `status`)',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'fragmentos' AND index_name = 'idx_fragmentos_name_status'
);
SET @sqlstmt := IF(@exist = 0,
  'ALTER TABLE `fragmentos` ADD KEY `idx_fragmentos_name_status` (`name`, `status`)',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'page_data' AND index_name = 'idx_page_data_key'
);
SET @sqlstmt := IF(@exist = 0,
  'ALTER TABLE `page_data` ADD KEY `idx_page_data_key` (`_key`)',
  'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
