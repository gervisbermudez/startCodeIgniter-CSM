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

-- Volcando estructura para tabla gervisbermudez.com.site_config
CREATE TABLE IF NOT EXISTS `site_config` (
  `site_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `config_name` varchar(500) NOT NULL,
  `config_value` varchar(500) NOT NULL,
  `config_description` varchar(500) NOT NULL,
  `config_type` varchar(500) DEFAULT NULL,
  `config_data` text DEFAULT NULL,
  `readonly` int(11) NOT NULL DEFAULT 0,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`site_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla gervisbermudez.com.site_config: ~22 rows (aproximadamente)
DELETE FROM `site_config`;
/*!40000 ALTER TABLE `site_config` DISABLE KEYS */;
INSERT INTO `site_config` (`site_config_id`, `user_id`, `config_name`, `config_value`, `config_description`, `config_type`, `config_data`, `readonly`, `date_create`, `date_update`, `status`) VALUES
	(1, 1, 'SITE_TITLE', 'Gervis Bermúdez', '', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-24 20:06:44', '2020-10-12 13:17:34', 1),
	(2, 1, 'SITE_DESCRIPTION', 'My Great website made by Start Codeigneiter ', '', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-11-11 16:55:13', '2020-10-12 11:48:11', 1),
	(3, 1, 'SITE_ADMIN_EMAIL', 'gervisbermudez@outlook.com', '', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "email",\r\n  "max_lenght": "150",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "email",\r\n  "perm_values": null\r\n}', 0, '2020-10-24 20:07:37', '2020-10-12 11:48:11', 1),
	(4, 1, 'SITE_LANGUAGE', 'en', '', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "0",\r\n  "handle_as": "select",\r\n  "input_type": "select",\r\n  "perm_values": ["en", "esp"]\r\n}', 0, '2020-10-12 22:24:21', '2020-10-12 11:48:11', 1),
	(5, 1, 'SITE_TIME_ZONE', 'UTC-10', '', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "0",\r\n  "handle_as": "select",\r\n  "input_type": "select",\r\n  "perm_values": {\r\n    "UTC-12": "UTC-12",\r\n    "UTC-11.5": "UTC-11:30",\r\n    "UTC-11": "UTC-11",\r\n    "UTC-10.5": "UTC-10:30",\r\n    "UTC-10": "UTC-10",\r\n    "UTC-9.5": "UTC-9:30",\r\n    "UTC-9": "UTC-9",\r\n    "UTC-8.5": "UTC-8:30",\r\n    "UTC-8": "UTC-8",\r\n    "UTC-7.5": "UTC-7:30",\r\n    "UTC-7": "UTC-7",\r\n    "UTC-6.5": "UTC-6:30",\r\n    "UTC-6": "UTC-6",\r\n    "UTC-5.5": "UTC-5:30",\r\n    "UTC-5": "UTC-5",\r\n    "UTC-4.5": "UTC-4:30",\r\n    "UTC-4": "UTC-4",\r\n    "UTC-3.5": "UTC-3:30",\r\n    "UTC-3": "UTC-3",\r\n    "UTC-2.5": "UTC-2:30",\r\n    "UTC-2": "UTC-2",\r\n    "UTC-1.5": "UTC-1:30",\r\n    "UTC-1": "UTC-1",\r\n    "UTC-0.5": "UTC-0:30",\r\n    "UTC+0": "UTC+0",\r\n    "UTC+0.5": "UTC+0:30",\r\n    "UTC+1": "UTC+1",\r\n    "UTC+1.5": "UTC+1:30",\r\n    "UTC+2": "UTC+2",\r\n    "UTC+2.5": "UTC+2:30",\r\n    "UTC+3": "UTC+3",\r\n    "UTC+3.5": "UTC+3:30",\r\n    "UTC+4": "UTC+4",\r\n    "UTC+4.5": "UTC+4:30",\r\n    "UTC+5": "UTC+5",\r\n    "UTC+5.5": "UTC+5:30",\r\n    "UTC+5.75": "UTC+5:45",\r\n    "UTC+6": "UTC+6",\r\n    "UTC+6.5": "UTC+6:30",\r\n    "UTC+7": "UTC+7",\r\n    "UTC+7.5": "UTC+7:30",\r\n    "UTC+8": "UTC+8",\r\n    "UTC+8.5": "UTC+8:30",\r\n    "UTC+8.75": "UTC+8:45",\r\n    "UTC+9": "UTC+9",\r\n    "UTC+9.5": "UTC+9:30",\r\n    "UTC+10": "UTC+10",\r\n    "UTC+10.5": "UTC+10:30",\r\n    "UTC+11": "UTC+11",\r\n    "UTC+11.5": "UTC+11:30",\r\n    "UTC+12": "UTC+12",\r\n    "UTC+12.75": "UTC+12:45",\r\n    "UTC+13": "UTC+13",\r\n    "UTC+13.75": "UTC+13:45",\r\n    "UTC+14": "UTC+14"\r\n  }\r\n}', 0, '2020-09-06 18:15:28', '2020-10-12 11:49:03', 1),
	(6, 0, 'SITE_DATE_FORMAT', 'j \\d\\e F \\d\\e Y', '', 'general', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-09-05 15:48:17', '2020-10-12 11:49:07', 1),
	(7, 1, 'SITE_TIME_FORMAT', 'H:i', '', 'general', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"0"}', 0, '2020-09-06 01:44:30', '2020-10-12 11:49:15', 1),
	(9, 1, 'SITE_LIST_MAX_ENTRY', '10', '', 'general', '{"type_value":"number","validate_as":"number","max_lenght":"50","min_lenght":"0"}', 0, '2020-09-06 18:28:31', '2020-10-12 11:49:20', 1),
	(11, 1, 'SITE_LIST_PUBLIC', 'Si', '', 'general', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-12-02 16:52:18', '2020-10-12 11:49:30', 1),
	(12, 1, 'SITE_AUTHOR', 'Gervis Bermudez', '', 'seo', '{"type_value":"string","validate_as":"name","max_lenght":"50","min_lenght":"5"}', 0, '2020-09-06 18:29:01', '2020-10-12 11:48:11', 1),
	(13, 0, 'LAST_UPDATE_FILEMANAGER', '2020-11-12 17:34:42', '', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-01 12:02:11', '2020-11-12 13:34:42', 1),
	(14, 1, 'ANALYTICS_ACTIVE', 'Off', '', 'analytics', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["Off", "On"],\r\n  "true": "On"\r\n}\r\n', 0, '2020-12-03 22:37:34', '2020-10-12 17:21:11', 1),
	(15, 1, 'ANALYTICS_ID', 'UA-XXXXX-Y', '', 'analytics', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-12 22:28:02', '2020-10-12 17:00:44', 1),
	(16, 1, 'ANALYTICS_CODE', '<script> window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date; ga(\'create\', \'UA-XXXXX-Y\', \'auto\'); ga(\'send\', \'pageview\'); </script> <script async src=\'https://www.google-analytics.com/analytics.js\'></script>', '', 'analytics', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-12 22:28:18', '2020-10-12 17:49:52', 1),
	(17, 1, 'PIXEL_ACTIVE', 'Off', '', 'pixel', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["Off", "On"],\r\n  "true": "On"\r\n}\r\n', 0, '2020-12-03 22:37:32', '2020-10-12 17:59:40', 1),
	(18, 1, 'PIXEL_CODE', '', '', 'analytics', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-12 22:28:18', '2020-10-12 17:50:02', 1),
	(19, 1, 'THEME_PATH', 'awesomeTheme', '', 'theme', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-12-01 21:30:18', '2020-11-11 13:38:15', 1),
	(20, 1, 'UPDATER_LAST_CHECK_UPDATE', '2020-11-19 16:19:09', '', 'updater', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-11-11 19:49:23', '2020-11-11 19:28:47', 1),
	(21, 1, 'UPDATER_MANUAL_CHECK', 'On', '', 'updater', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["Off", "On"],\r\n  "true": "On"\r\n}\r\n', 0, '2020-10-12 22:33:36', '2020-11-11 19:31:03', 1),
	(22, 1, 'UPDATER_LAST_CHECK_DATA', '{"name":"Start CMS","version":"1.5.6","description":"A simple theme building for StartCMS","url":"https://github.com/gervisbermudez/startCodeIgniter-CSM.git","updated":"11/18/2020 12:30:25"}', '', 'updater', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-11-11 19:49:23', '2020-11-11 19:28:47', 1),
	(23, 1, 'SYSTEM_LOGGER', 'Si', '', 'logger', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-12-03 22:51:21', '2020-12-03 18:18:34', 1),
	(25, 1, 'SITEM_TRACK_VISITORS', 'Si', '', 'logger', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-12-03 22:51:20', '2020-12-03 18:18:36', 1),
	(26, 1, 'SYSTEM_API_LOGGER', 'Si', '', 'logger', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-12-03 22:51:19', '2020-12-03 18:18:52', 1),
	(27, 0, 'LAST_UPDATE_FILEMANAGER', '2020-12-03 22:52:43', '', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-12-03 18:52:43', '2020-12-04 12:28:23', 1);
/*!40000 ALTER TABLE `site_config` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
