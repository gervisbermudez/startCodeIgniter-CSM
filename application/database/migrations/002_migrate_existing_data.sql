-- Script de migración de datos existentes
-- Ejecutar DESPUÉS de la migración 001_improve_user_tracking.sql

-- 1. Generar session_id para registros existentes que no lo tienen
UPDATE user_tracking 
SET session_id = CONCAT('legacy_', user_tracking_id, '_', UNIX_TIMESTAMP(date_create))
WHERE session_id IS NULL;

-- 2. Detectar device_type basado en user_agent existente
UPDATE user_tracking 
SET device_type = CASE
    WHEN LOWER(user_agent) LIKE '%mobile%' OR 
         LOWER(user_agent) LIKE '%android%' OR
         LOWER(user_agent) LIKE '%iphone%' THEN 'mobile'
    WHEN LOWER(user_agent) LIKE '%ipad%' OR 
         LOWER(user_agent) LIKE '%tablet%' THEN 'tablet'
    WHEN LOWER(user_agent) LIKE '%bot%' OR 
         LOWER(user_agent) LIKE '%crawler%' OR
         LOWER(user_agent) LIKE '%spider%' THEN 'bot'
    ELSE 'desktop'
END
WHERE device_type = 'desktop';

-- 3. Extraer browser de user_agent
UPDATE user_tracking 
SET browser = CASE
    WHEN LOWER(user_agent) LIKE '%chrome/%' AND LOWER(user_agent) NOT LIKE '%edge%' THEN 'Chrome'
    WHEN LOWER(user_agent) LIKE '%firefox%' THEN 'Firefox'
    WHEN LOWER(user_agent) LIKE '%safari%' AND LOWER(user_agent) NOT LIKE '%chrome%' THEN 'Safari'
    WHEN LOWER(user_agent) LIKE '%edge%' OR LOWER(user_agent) LIKE '%edg/%' THEN 'Edge'
    WHEN LOWER(user_agent) LIKE '%msie%' OR LOWER(user_agent) LIKE '%trident%' THEN 'Internet Explorer'
    WHEN LOWER(user_agent) LIKE '%opera%' OR LOWER(user_agent) LIKE '%opr/%' THEN 'Opera'
    ELSE 'Unknown'
END
WHERE browser IS NULL;

-- 4. Extraer platform de user_agent
UPDATE user_tracking 
SET platform = CASE
    WHEN LOWER(user_agent) LIKE '%windows%' THEN 'Windows'
    WHEN LOWER(user_agent) LIKE '%mac%' OR LOWER(user_agent) LIKE '%macintosh%' THEN 'macOS'
    WHEN LOWER(user_agent) LIKE '%linux%' THEN 'Linux'
    WHEN LOWER(user_agent) LIKE '%android%' THEN 'Android'
    WHEN LOWER(user_agent) LIKE '%iphone%' OR LOWER(user_agent) LIKE '%ipad%' THEN 'iOS'
    ELSE 'Unknown'
END
WHERE platform IS NULL;

-- 5. Crear sesiones para datos legacy (agrupando por IP y día)
INSERT INTO user_sessions (
    session_id,
    first_visit,
    last_activity,
    total_pages,
    is_new_visitor,
    client_ip,
    user_agent,
    browser,
    platform,
    device_type,
    entry_page,
    exit_page,
    referer_source,
    status
)
SELECT 
    CONCAT('legacy_', client_ip, '_', DATE(date_create)) as session_id,
    MIN(date_create) as first_visit,
    MAX(date_create) as last_activity,
    COUNT(*) as total_pages,
    1 as is_new_visitor,
    client_ip,
    MAX(user_agent) as user_agent,
    MAX(browser) as browser,
    MAX(platform) as platform,
    MAX(device_type) as device_type,
    (SELECT requested_url FROM user_tracking ut2 
     WHERE ut2.client_ip = ut.client_ip 
     AND DATE(ut2.date_create) = DATE(ut.date_create)
     ORDER BY ut2.date_create ASC LIMIT 1) as entry_page,
    (SELECT requested_url FROM user_tracking ut2 
     WHERE ut2.client_ip = ut.client_ip 
     AND DATE(ut2.date_create) = DATE(ut.date_create)
     ORDER BY ut2.date_create DESC LIMIT 1) as exit_page,
    MAX(referer_page) as referer_source,
    1 as status
FROM user_tracking ut
WHERE date_create < NOW() - INTERVAL 1 DAY
GROUP BY client_ip, DATE(date_create)
ON DUPLICATE KEY UPDATE total_pages = VALUES(total_pages);

-- 6. Marcar bounces (sesiones con solo 1 página)
UPDATE user_tracking ut
INNER JOIN (
    SELECT session_id
    FROM user_tracking
    GROUP BY session_id
    HAVING COUNT(*) = 1
) single_page ON ut.session_id = single_page.session_id
SET ut.is_bounce = 1;

-- 7. Marcar páginas de salida (última página de cada sesión)
UPDATE user_tracking ut
INNER JOIN (
    SELECT session_id, MAX(date_create) as last_visit
    FROM user_tracking
    GROUP BY session_id
) last_pages ON ut.session_id = last_pages.session_id 
    AND ut.date_create = last_pages.last_visit
SET ut.exit_page = 1;

-- 8. Verificar resultados
SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT session_id) as unique_sessions,
    COUNT(DISTINCT client_ip) as unique_ips,
    SUM(CASE WHEN device_type = 'mobile' THEN 1 ELSE 0 END) as mobile,
    SUM(CASE WHEN device_type = 'desktop' THEN 1 ELSE 0 END) as desktop,
    SUM(CASE WHEN device_type = 'tablet' THEN 1 ELSE 0 END) as tablet,
    SUM(CASE WHEN device_type = 'bot' THEN 1 ELSE 0 END) as bots,
    SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounces
FROM user_tracking;

-- 9. Calcular estadísticas diarias para datos históricos
-- (Ejecutar para cada día que tengas datos)
-- CALL calculate_daily_stats('2025-12-01');
-- CALL calculate_daily_stats('2025-12-02');
-- ... etc

-- O usar un loop (MySQL 8.0+)
DELIMITER //
CREATE PROCEDURE migrate_historical_stats(IN days_back INT)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE target_date DATE;
    
    WHILE i < days_back DO
        SET target_date = DATE_SUB(CURDATE(), INTERVAL i DAY);
        CALL calculate_daily_stats(target_date);
        SET i = i + 1;
    END WHILE;
END//
DELIMITER ;

-- Ejecutar para los últimos 90 días
-- CALL migrate_historical_stats(90);

COMMIT;
