-- =====================================================
-- Migration: Mejorar User Tracking System
-- Fecha: 2025-12-19
-- Descripción: Mejoras en tracking de usuarios con analytics avanzados
-- =====================================================

-- 1. Mejorar tabla user_tracking
-- Agregar nuevos campos para analytics mejorados
ALTER TABLE `user_tracking` 
ADD COLUMN `session_id` VARCHAR(100) DEFAULT NULL AFTER `user_tracking_id`,
ADD COLUMN `browser` VARCHAR(100) DEFAULT NULL AFTER `user_agent`,
ADD COLUMN `browser_version` VARCHAR(50) DEFAULT NULL AFTER `browser`,
ADD COLUMN `platform` VARCHAR(100) DEFAULT NULL AFTER `browser_version`,
ADD COLUMN `device_type` ENUM('desktop', 'mobile', 'tablet', 'bot') DEFAULT 'desktop' AFTER `platform`,
ADD COLUMN `screen_resolution` VARCHAR(20) DEFAULT NULL AFTER `device_type`,
ADD COLUMN `language` VARCHAR(10) DEFAULT NULL AFTER `screen_resolution`,
ADD COLUMN `country_code` VARCHAR(5) DEFAULT NULL AFTER `language`,
ADD COLUMN `city` VARCHAR(100) DEFAULT NULL AFTER `country_code`,
ADD COLUMN `time_on_page` INT DEFAULT 0 COMMENT 'Time in seconds' AFTER `query_string`,
ADD COLUMN `is_bounce` TINYINT(1) DEFAULT 0 AFTER `time_on_page`,
ADD COLUMN `exit_page` TINYINT(1) DEFAULT 0 AFTER `is_bounce`,
ADD COLUMN `conversion` TINYINT(1) DEFAULT 0 AFTER `exit_page`,
ADD INDEX `idx_session_id` (`session_id`),
ADD INDEX `idx_client_ip` (`client_ip`),
ADD INDEX `idx_page_name` (`page_name`),
ADD INDEX `idx_date_create` (`date_create`),
ADD INDEX `idx_device_type` (`device_type`),
ADD INDEX `idx_country_code` (`country_code`),
ADD INDEX `idx_status` (`status`);

-- 2. Crear tabla de sesiones para mejor tracking
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `session_id` VARCHAR(100) NOT NULL,
  `first_visit` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_pages` INT DEFAULT 1,
  `total_time` INT DEFAULT 0 COMMENT 'Total time in seconds',
  `is_new_visitor` TINYINT(1) DEFAULT 1,
  `client_ip` VARCHAR(50) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `browser` VARCHAR(100) DEFAULT NULL,
  `platform` VARCHAR(100) DEFAULT NULL,
  `device_type` ENUM('desktop', 'mobile', 'tablet', 'bot') DEFAULT 'desktop',
  `entry_page` VARCHAR(500) DEFAULT NULL,
  `exit_page` VARCHAR(500) DEFAULT NULL,
  `referer_source` VARCHAR(500) DEFAULT NULL,
  `campaign_source` VARCHAR(100) DEFAULT NULL,
  `campaign_medium` VARCHAR(100) DEFAULT NULL,
  `campaign_name` VARCHAR(100) DEFAULT NULL,
  `country_code` VARCHAR(5) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `converted` TINYINT(1) DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`session_id`),
  INDEX `idx_first_visit` (`first_visit`),
  INDEX `idx_device_type` (`device_type`),
  INDEX `idx_is_new_visitor` (`is_new_visitor`),
  INDEX `idx_converted` (`converted`),
  INDEX `idx_country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Crear tabla para eventos personalizados
CREATE TABLE IF NOT EXISTS `user_tracking_events` (
  `event_id` INT(11) NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(100) NOT NULL,
  `user_tracking_id` INT(11) DEFAULT NULL,
  `event_category` VARCHAR(100) NOT NULL,
  `event_action` VARCHAR(100) NOT NULL,
  `event_label` VARCHAR(200) DEFAULT NULL,
  `event_value` INT DEFAULT NULL,
  `page_url` VARCHAR(500) DEFAULT NULL,
  `metadata` TEXT DEFAULT NULL COMMENT 'JSON with additional data',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_event_category` (`event_category`),
  INDEX `idx_event_action` (`event_action`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_tracking_id`) REFERENCES `user_tracking`(`user_tracking_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Crear tabla para resúmenes diarios (para reportes más rápidos)
CREATE TABLE IF NOT EXISTS `user_tracking_daily_stats` (
  `stat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `stat_date` DATE NOT NULL,
  `total_visits` INT DEFAULT 0,
  `unique_visitors` INT DEFAULT 0,
  `new_visitors` INT DEFAULT 0,
  `returning_visitors` INT DEFAULT 0,
  `total_pageviews` INT DEFAULT 0,
  `avg_time_on_site` INT DEFAULT 0,
  `bounce_rate` DECIMAL(5,2) DEFAULT 0.00,
  `conversion_rate` DECIMAL(5,2) DEFAULT 0.00,
  `desktop_visits` INT DEFAULT 0,
  `mobile_visits` INT DEFAULT 0,
  `tablet_visits` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stat_id`),
  UNIQUE KEY `unique_stat_date` (`stat_date`),
  INDEX `idx_stat_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Crear vista para analytics rápidos
CREATE OR REPLACE VIEW `v_user_tracking_analytics` AS
SELECT 
  DATE(ut.date_create) as visit_date,
  COUNT(DISTINCT ut.session_id) as unique_sessions,
  COUNT(ut.user_tracking_id) as total_pageviews,
  COUNT(DISTINCT ut.client_ip) as unique_ips,
  AVG(ut.time_on_page) as avg_time_on_page,
  SUM(CASE WHEN ut.is_bounce = 1 THEN 1 ELSE 0 END) as bounces,
  SUM(CASE WHEN ut.conversion = 1 THEN 1 ELSE 0 END) as conversions,
  SUM(CASE WHEN ut.device_type = 'mobile' THEN 1 ELSE 0 END) as mobile_visits,
  SUM(CASE WHEN ut.device_type = 'desktop' THEN 1 ELSE 0 END) as desktop_visits,
  SUM(CASE WHEN ut.device_type = 'tablet' THEN 1 ELSE 0 END) as tablet_visits
FROM user_tracking ut
WHERE ut.status = 1
GROUP BY DATE(ut.date_create);

-- 6. Crear vista para páginas más populares
CREATE OR REPLACE VIEW `v_popular_pages` AS
SELECT 
  page_name,
  COUNT(*) as visits,
  COUNT(DISTINCT session_id) as unique_visits,
  AVG(time_on_page) as avg_time,
  SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounces,
  SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as conversions
FROM user_tracking
WHERE status = 1
GROUP BY page_name
ORDER BY visits DESC;

-- 7. Crear stored procedure para calcular estadísticas diarias
DELIMITER //
CREATE PROCEDURE `calculate_daily_stats`(IN target_date DATE)
BEGIN
  INSERT INTO user_tracking_daily_stats (
    stat_date,
    total_visits,
    unique_visitors,
    new_visitors,
    returning_visitors,
    total_pageviews,
    avg_time_on_site,
    bounce_rate,
    conversion_rate,
    desktop_visits,
    mobile_visits,
    tablet_visits
  )
  SELECT 
    target_date,
    COUNT(DISTINCT us.session_id),
    COUNT(DISTINCT us.client_ip),
    SUM(CASE WHEN us.is_new_visitor = 1 THEN 1 ELSE 0 END),
    SUM(CASE WHEN us.is_new_visitor = 0 THEN 1 ELSE 0 END),
    (SELECT COUNT(*) FROM user_tracking WHERE DATE(date_create) = target_date),
    AVG(us.total_time),
    (SELECT 
      ROUND((SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
     FROM user_tracking WHERE DATE(date_create) = target_date),
    (SELECT 
      ROUND((SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
     FROM user_tracking WHERE DATE(date_create) = target_date),
    (SELECT COUNT(*) FROM user_tracking WHERE DATE(date_create) = target_date AND device_type = 'desktop'),
    (SELECT COUNT(*) FROM user_tracking WHERE DATE(date_create) = target_date AND device_type = 'mobile'),
    (SELECT COUNT(*) FROM user_tracking WHERE DATE(date_create) = target_date AND device_type = 'tablet')
  FROM user_sessions us
  WHERE DATE(us.first_visit) = target_date
  ON DUPLICATE KEY UPDATE
    total_visits = VALUES(total_visits),
    unique_visitors = VALUES(unique_visitors),
    new_visitors = VALUES(new_visitors),
    returning_visitors = VALUES(returning_visitors),
    total_pageviews = VALUES(total_pageviews),
    avg_time_on_site = VALUES(avg_time_on_site),
    bounce_rate = VALUES(bounce_rate),
    conversion_rate = VALUES(conversion_rate),
    desktop_visits = VALUES(desktop_visits),
    mobile_visits = VALUES(mobile_visits),
    tablet_visits = VALUES(tablet_visits);
END//
DELIMITER ;
