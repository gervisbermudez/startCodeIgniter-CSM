-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.13-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.1.0.6116
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando estructura para tabla start_cms.user_tracking
CREATE TABLE IF NOT EXISTS `user_tracking` (
  `user_tracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `site_log_id` int(11) NOT NULL DEFAULT 0,
  `visits_count` int(11) NOT NULL DEFAULT 0,
  `no_of_visits` int(11) NOT NULL DEFAULT 0,
  `client_ip` varchar(500) NOT NULL DEFAULT '0',
  `user_agent` varchar(500) NOT NULL DEFAULT '0',
  `requested_url` varchar(500) NOT NULL DEFAULT '0',
  `referer_page` varchar(500) NOT NULL DEFAULT '0',
  `page_name` varchar(500) NOT NULL DEFAULT '0',
  `query_string` varchar(500) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT 1,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_tracking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Volcando datos para la tabla start_cms.user_tracking: ~0 rows (aproximadamente)
DELETE FROM `user_tracking`;
/*!40000 ALTER TABLE `user_tracking` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_tracking` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
