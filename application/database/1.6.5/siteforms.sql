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
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`siteform_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Formularios del sitio';

-- Volcando datos para la tabla start_cms.siteform: ~1 rows (aproximadamente)
DELETE FROM `siteform`;
/*!40000 ALTER TABLE `siteform` DISABLE KEYS */;
INSERT INTO `siteform` (`siteform_id`, `user_id`, `name`, `template`, `date_create`, `date_update`, `date_delete`, `status`) VALUES
	(1, 1, 'hire_me', 'form', '2020-12-08 00:42:39', '2020-12-07 18:42:21', '2020-12-07 18:42:21', 1);
/*!40000 ALTER TABLE `siteform` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COMMENT='Items del formularios del Sitio';

-- Volcando datos para la tabla start_cms.siteform_items: ~5 rows (aproximadamente)
DELETE FROM `siteform_items`;
/*!40000 ALTER TABLE `siteform_items` DISABLE KEYS */;
INSERT INTO `siteform_items` (`siteform_item_id`, `siteform_id`, `order`, `item_type`, `item_name`, `item_label`, `item_class`, `item_title`, `item_placeholder`, `properties`, `data`, `date_publish`, `date_create`, `date_update`, `status`) VALUES
	(1, 1, 0, 'text', 'name', 'Your Name', 'form-control', 'Your Name', 'Your Name', '"\\"\\\\\\"\\\\\\\\\\\\\\"null\\\\\\\\\\\\\\"\\\\\\"\\""', '"\\"\\\\\\"\\\\\\\\\\\\\\"null\\\\\\\\\\\\\\"\\\\\\"\\""', '0000-00-00 00:00:00', '2020-12-07 18:55:04', '2020-12-07 19:33:35', 1),
	(2, 1, 2, 'email', 'email', 'Your Email', 'form-control', 'Link Title', 'Your Email', '"\\"\\\\\\"\\\\\\\\\\\\\\"{\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"aria-describedby\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\":\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"emailHelp\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"}\\\\\\\\\\\\\\"\\\\\\"\\""', '"\\"\\\\\\"\\\\\\\\\\\\\\"{}\\\\\\\\\\\\\\"\\\\\\"\\""', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2020-12-07 19:33:43', 1),
	(3, 1, 3, 'text', 'subject', 'Subject', 'form-control', 'Subject', 'Subject', '"\\"\\\\\\"\\\\\\\\\\\\\\"{}\\\\\\\\\\\\\\"\\\\\\"\\""', '"\\"\\\\\\"\\\\\\\\\\\\\\"{}\\\\\\\\\\\\\\"\\\\\\"\\""', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2020-12-07 19:27:27', 1),
	(4, 1, 4, 'textarea', 'message', 'Message', 'form-control', 'Message', 'Message', '"\\"\\\\\\"\\\\\\\\\\\\\\"{}\\\\\\\\\\\\\\"\\\\\\"\\""', '"\\"\\\\\\"\\\\\\\\\\\\\\"{}\\\\\\\\\\\\\\"\\\\\\"\\""', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2020-12-07 19:27:28', 1),
	(11, 1, 1, 'number', 'telephone', 'Telephone', 'form-control', 'Telephone', 'Telephone', '"\\"\\\\\\"{}\\\\\\"\\""', '"\\"\\\\\\"{}\\\\\\"\\""', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2020-12-07 20:02:22', 1);
/*!40000 ALTER TABLE `siteform_items` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
