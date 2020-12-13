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

-- Volcando estructura para tabla start_cms.siteform
CREATE TABLE IF NOT EXISTS `siteform` (
  `siteform_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `template` varchar(600) NOT NULL,
  `properties` text NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`siteform_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Formularios del sitio';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla start_cms.siteform_items
CREATE TABLE IF NOT EXISTS `siteform_items` (
  `siteform_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `siteform_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) DEFAULT 0,
  `item_type` varchar(250) DEFAULT NULL,
  `item_name` varchar(250) DEFAULT NULL,
  `item_label` varchar(250) DEFAULT NULL,
  `item_class` varchar(600) DEFAULT NULL,
  `item_title` varchar(250) DEFAULT NULL,
  `item_placeholder` varchar(250) DEFAULT NULL,
  `properties` text DEFAULT NULL,
  `data` text DEFAULT NULL,
  `date_publish` datetime NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`siteform_item_id`),
  KEY `siteform_id` (`siteform_id`),
  CONSTRAINT `FK_siteform_items_siteform` FOREIGN KEY (`siteform_id`) REFERENCES `siteform` (`siteform_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COMMENT='Items del formularios del Sitio';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla start_cms.siteform_submit
CREATE TABLE IF NOT EXISTS `siteform_submit` (
  `siteform_submit_id` int(11) NOT NULL AUTO_INCREMENT,
  `siteform_id` int(11) DEFAULT NULL,
  `user_tracking_id` int(11) DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`siteform_submit_id`) USING BTREE,
  KEY `siteform_id` (`siteform_id`),
  CONSTRAINT `FK_siteform_submit_siteform` FOREIGN KEY (`siteform_id`) REFERENCES `siteform` (`siteform_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Formularios enviados en el sitio';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla start_cms.siteform_submit_data
CREATE TABLE IF NOT EXISTS `siteform_submit_data` (
  `siteform_submit_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `siteform_submit_id` int(11) DEFAULT NULL,
  `_key` varchar(200) DEFAULT NULL,
  `_value` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`siteform_submit_data_id`) USING BTREE,
  KEY `form_id` (`siteform_submit_id`) USING BTREE,
  CONSTRAINT `FK_siteform_submit_data_siteform_submit` FOREIGN KEY (`siteform_submit_id`) REFERENCES `siteform_submit` (`siteform_submit_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- La exportación de datos fue deseleccionada.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
