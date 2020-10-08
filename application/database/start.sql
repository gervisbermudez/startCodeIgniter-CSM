-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.13-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura para tabla start_cms.album
CREATE TABLE IF NOT EXISTS `album` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `date_publish` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`album_id`),
  UNIQUE KEY `nombre` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.album: ~1 rows (aproximadamente)
DELETE FROM `album`;
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
INSERT INTO `album` (`album_id`, `name`, `description`, `date_publish`, `user_id`, `status`, `date_create`, `date_update`) VALUES
	(1, 'Hola', 'Mundo', '2020-10-07 19:01:17', 1, 1, '2020-10-07 19:01:17', '2020-10-07 19:18:26');
/*!40000 ALTER TABLE `album` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.album_items
CREATE TABLE IF NOT EXISTS `album_items` (
  `album_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`album_item_id`),
  KEY `id_album` (`album_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.album_items: ~1 rows (aproximadamente)
DELETE FROM `album_items`;
/*!40000 ALTER TABLE `album_items` DISABLE KEYS */;
INSERT INTO `album_items` (`album_item_id`, `album_id`, `file_id`, `name`, `description`, `status`, `date_create`, `date_update`) VALUES
	(1, 1, 1, 'hola', '0hola', 1, '2020-10-07 19:03:11', '2020-10-07 19:03:19');
/*!40000 ALTER TABLE `album_items` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `categorie_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(600) NOT NULL,
  `parent_id` tinyint(1) NOT NULL DEFAULT 0,
  `date_publish` datetime NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COMMENT='Categorias del sistema';

-- Volcando datos para la tabla start_cms.categories: ~10 rows (aproximadamente)
DELETE FROM `categories`;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`categorie_id`, `user_id`, `name`, `description`, `type`, `parent_id`, `date_publish`, `date_create`, `date_update`, `status`) VALUES
	(4, 1, 'Web Design', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 0, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:45', 1),
	(5, 1, 'Tutorials', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 0, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:46', 1),
	(6, 1, 'Programing languages', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 0, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:47', 1),
	(7, 1, 'Sub Categoria 1', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 4, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:49', 1),
	(8, 2, 'Sub Categoria 2', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 4, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:50', 1),
	(9, 1, 'Sub Categoria 3', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 4, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:51', 1),
	(10, 1, 'Sub Categoria 1', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 5, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:52', 1),
	(11, 2, 'Sub Categoria 2', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 5, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:53', 1),
	(12, 2, 'Sub Categoria 3', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 5, '2020-04-19 10:36:10', '2020-04-19 10:36:14', '2020-09-01 08:57:54', 1),
	(16, 1, 'WebAPIs', '<p style="margin: 0px 0px 24px; padding: 0px; border: 0px; box-sizing: border-box; max-width: 42rem; color: #333333; font-family: Arial, x-locale-body, sans-serif; font-size: 16px; letter-spacing: -0.04448px;"><span class="seoSummary" style="margin: 0px; padding: 0px; border: 0px;">When writing code for the Web, there are a large number of Web APIs available. Below is a list of all the APIs and interfaces (object types) that you may be able to use while developing your Web app or site.</span></p>\n<p style="margin: 0px 0px 24px; padding: 0px; border: 0px; box-sizing: border-box; max-width: 42rem; color: #333333; font-family: Arial, x-locale-body, sans-serif; font-size: 16px; letter-spacing: -0.04448px;">Web APIs are typically used with JavaScript, although this doesn\'t always have to be the case.</p>', 'page', 0, '2020-09-04 00:05:27', '2020-09-04 00:05:27', '2020-09-03 14:35:55', 1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.contacts
CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(600) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`contact_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contacts';

-- Volcando datos para la tabla start_cms.contacts: ~0 rows (aproximadamente)
DELETE FROM `contacts`;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.contacts_data
CREATE TABLE IF NOT EXISTS `contacts_data` (
  `contacts_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `contacts_data_key` varchar(200) NOT NULL,
  `contacts_data_value` varchar(600) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`contacts_data_id`),
  KEY `id_contact` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contactsdata';

-- Volcando datos para la tabla start_cms.contacts_data: ~0 rows (aproximadamente)
DELETE FROM `contacts_data`;
/*!40000 ALTER TABLE `contacts_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.events
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `title` varchar(70) NOT NULL,
  `description` mediumtext NOT NULL,
  `image` text NOT NULL,
  `thumb` text NOT NULL,
  `adress` tinytext NOT NULL,
  `description_date` varchar(70) NOT NULL,
  `place` varchar(70) NOT NULL,
  `date_publish` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Eventos a publicar';

-- Volcando datos para la tabla start_cms.events: ~0 rows (aproximadamente)
DELETE FROM `events`;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.file
CREATE TABLE IF NOT EXISTS `file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `rand_key` varchar(250) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shared_user_group_id` int(11) DEFAULT NULL,
  `share_link` varchar(500) DEFAULT NULL,
  `featured` tinyint(4) DEFAULT 0,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`file_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=597 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.file: ~563 rows (aproximadamente)
DELETE FROM `file`;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
/*!40000 ALTER TABLE `file` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.file_activity
CREATE TABLE IF NOT EXISTS `file_activity` (
  `file_activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`file_activity_id`),
  KEY `file_id` (`file_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_file_activity_file` FOREIGN KEY (`file_id`) REFERENCES `file` (`file_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_file_activity_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Volcando datos para la tabla start_cms.file_activity: ~0 rows (aproximadamente)
DELETE FROM `file_activity`;
/*!40000 ALTER TABLE `file_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `file_activity` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_content
CREATE TABLE IF NOT EXISTS `form_content` (
  `form_content_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_custom_id` int(11) NOT NULL DEFAULT 0,
  `form_tab_id` int(11) NOT NULL DEFAULT 0,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.form_content: ~1 rows (aproximadamente)
DELETE FROM `form_content`;
/*!40000 ALTER TABLE `form_content` DISABLE KEYS */;
INSERT INTO `form_content` (`form_content_id`, `form_custom_id`, `form_tab_id`, `date_create`, `date_update`, `user_id`, `status`) VALUES
	(17, 6, 6, '2020-09-14 18:05:36', '2020-09-14 18:05:36', 1, 1);
/*!40000 ALTER TABLE `form_content` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_content_data
CREATE TABLE IF NOT EXISTS `form_content_data` (
  `form_custom_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_content_id` int(11) DEFAULT NULL,
  `form_field_id` int(11) DEFAULT NULL,
  `form_value` text DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_custom_data_id`),
  KEY `form_id` (`form_content_id`),
  CONSTRAINT `FK_form_content_data_form_content` FOREIGN KEY (`form_content_id`) REFERENCES `form_content` (`form_content_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.form_content_data: ~2 rows (aproximadamente)
DELETE FROM `form_content_data`;
/*!40000 ALTER TABLE `form_content_data` DISABLE KEYS */;
INSERT INTO `form_content_data` (`form_custom_data_id`, `form_content_id`, `form_field_id`, `form_value`, `date_create`, `date_update`, `status`) VALUES
	(21, 17, 9, '{"title":"Kiss"}', '2020-09-14 18:05:36', '2020-09-14 18:05:36', 1),
	(22, 17, 10, '{"title":"Keep It Simple, Stupid"}', '2020-09-14 18:05:36', '2020-09-14 18:05:36', 1);
/*!40000 ALTER TABLE `form_content_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_custom
CREATE TABLE IF NOT EXISTS `form_custom` (
  `form_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(250) DEFAULT NULL,
  `form_description` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_custom_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_custom: ~1 rows (aproximadamente)
DELETE FROM `form_custom`;
/*!40000 ALTER TABLE `form_custom` DISABLE KEYS */;
INSERT INTO `form_custom` (`form_custom_id`, `form_name`, `form_description`, `date_create`, `date_update`, `user_id`, `status`) VALUES
	(6, 'Card', 'Cards are a convenient means of displaying content composed of different types of objects. They’re also well-suited for presenting similar objects whose size or supported actions can vary considerably, like photos with captions of variable length.', '2020-09-01 18:14:06', '2020-09-13 13:57:43', 1, 1);
/*!40000 ALTER TABLE `form_custom` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_fields
CREATE TABLE IF NOT EXISTS `form_fields` (
  `form_field_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_tab_id` int(11) DEFAULT NULL,
  `field_name` varchar(250) DEFAULT NULL,
  `displayName` varchar(250) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `component` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_field_id`) USING BTREE,
  KEY `form_tab_id` (`form_tab_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_fields: ~2 rows (aproximadamente)
DELETE FROM `form_fields`;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
INSERT INTO `form_fields` (`form_field_id`, `form_tab_id`, `field_name`, `displayName`, `icon`, `component`, `date_create`, `date_update`, `status`) VALUES
	(9, 6, 'title', 'Titulo', 'format_color_text', 'formFieldTitle', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(10, 6, 'title', 'Titulo', 'format_color_text', 'formFieldTitle', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1);
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_fields_data
CREATE TABLE IF NOT EXISTS `form_fields_data` (
  `form_field_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_field_id` int(11) DEFAULT NULL,
  `_key` varchar(200) DEFAULT NULL,
  `_value` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_field_config_id`),
  KEY `form_id` (`form_field_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_fields_data: ~10 rows (aproximadamente)
DELETE FROM `form_fields_data`;
/*!40000 ALTER TABLE `form_fields_data` DISABLE KEYS */;
INSERT INTO `form_fields_data` (`form_field_config_id`, `form_field_id`, `_key`, `_value`, `date_create`, `date_update`, `status`) VALUES
	(35, 9, 'fieldPlaceholder', 'card_title', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(36, 9, 'fieldID', 'OdNIy02wLe', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(37, 9, 'fieldName', 'card_title', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(38, 9, 'fielApiID', 'card_title', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(39, 9, 'form_custom_data_id', NULL, '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(40, 10, 'fieldPlaceholder', 'card_content', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(41, 10, 'fieldID', 'u3R26SluXo', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(42, 10, 'fieldName', 'card_content', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(43, 10, 'fielApiID', 'card_content', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1),
	(44, 10, 'form_custom_data_id', NULL, '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1);
/*!40000 ALTER TABLE `form_fields_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_tabs
CREATE TABLE IF NOT EXISTS `form_tabs` (
  `form_tab_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_custom_id` int(11) DEFAULT NULL,
  `tab_name` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_tab_id`),
  KEY `form_id` (`form_custom_id`) USING BTREE,
  CONSTRAINT `FK_form_tabs_form_custom` FOREIGN KEY (`form_custom_id`) REFERENCES `form_custom` (`form_custom_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Volcando datos para la tabla start_cms.form_tabs: ~1 rows (aproximadamente)
DELETE FROM `form_tabs`;
/*!40000 ALTER TABLE `form_tabs` DISABLE KEYS */;
INSERT INTO `form_tabs` (`form_tab_id`, `form_custom_id`, `tab_name`, `date_create`, `date_update`, `status`) VALUES
	(6, 6, 'Tab 1', '2020-09-09 18:14:06', '2020-09-09 18:14:06', 1);
/*!40000 ALTER TABLE `form_tabs` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.mailfolder
CREATE TABLE IF NOT EXISTS `mailfolder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namefolder` varchar(60) NOT NULL,
  `description` tinytext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namefolder` (`namefolder`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.mailfolder: ~7 rows (aproximadamente)
DELETE FROM `mailfolder`;
/*!40000 ALTER TABLE `mailfolder` DISABLE KEYS */;
INSERT INTO `mailfolder` (`id`, `namefolder`, `description`, `status`) VALUES
	(1, 'Inbox', 'The main folder', 1),
	(2, 'Archived', 'Archived folder', 1),
	(3, 'Sent', 'Sent folder', 1),
	(4, 'Deleted', 'Deleted folder', 1),
	(5, 'Spam', 'Spam folder', 1),
	(6, 'Starred', 'Starred folder', 1),
	(7, 'Drafts', 'The drafts folder', 1);
/*!40000 ALTER TABLE `mailfolder` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `messages_id` int(11) NOT NULL AUTO_INCREMENT,
  `_from` text NOT NULL,
  `_to` text NOT NULL,
  `_subject` varchar(255) NOT NULL,
  `_mensaje` longtext NOT NULL,
  `_cc` text NOT NULL,
  `_bcc` text NOT NULL,
  `date_publish` datetime NOT NULL,
  `folder` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`messages_id`),
  KEY `folder` (`folder`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.messages: ~0 rows (aproximadamente)
DELETE FROM `messages`;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.messages_data
CREATE TABLE IF NOT EXISTS `messages_data` (
  `messages_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) NOT NULL,
  `_key` varchar(200) COLLATE utf8_bin NOT NULL,
  `_value` varchar(600) COLLATE utf8_bin NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`messages_data_id`),
  KEY `id_mensaje` (`id_mensaje`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='mensajesdata';

-- Volcando datos para la tabla start_cms.messages_data: ~0 rows (aproximadamente)
DELETE FROM `messages_data`;
/*!40000 ALTER TABLE `messages_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.notificacions
CREATE TABLE IF NOT EXISTS `notificacions` (
  `notificacion_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notificacion_id`),
  KEY `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.notificacions: ~0 rows (aproximadamente)
DELETE FROM `notificacions`;
/*!40000 ALTER TABLE `notificacions` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificacions` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.page
CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(2048) DEFAULT NULL,
  `template` varchar(125) DEFAULT 'default',
  `title` text DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `page_type_id` int(11) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL,
  `visibility` int(11) DEFAULT 1,
  `categorie_id` int(11) DEFAULT NULL,
  `subcategorie_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 2,
  `layout` varchar(250) DEFAULT 'default',
  `mainImage` int(11) DEFAULT NULL,
  `date_publish` timestamp NULL DEFAULT NULL,
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`page_id`),
  KEY `author` (`user_id`) USING BTREE,
  KEY `type` (`page_type_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.page: ~1 rows (aproximadamente)
DELETE FROM `page`;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` (`page_id`, `path`, `template`, `title`, `subtitle`, `content`, `page_type_id`, `user_id`, `visibility`, `categorie_id`, `subcategorie_id`, `status`, `layout`, `mainImage`, `date_publish`, `date_update`, `date_create`) VALUES
	(98, 'materialize', 'default', 'Materialize', '', '<p>A modern responsive front-end framework based on Material Design</p>\n<p>&nbsp;</p>\n<div id="gtx-trans" style="position: absolute; left: -17px; top: 31.6354px;">&nbsp;</div>', 1, 1, 1, 0, 0, 2, 'default', NULL, '2020-10-03 00:39:54', NULL, '2020-10-03 00:39:54');
/*!40000 ALTER TABLE `page` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.page_data
CREATE TABLE IF NOT EXISTS `page_data` (
  `page_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `_key` varchar(100) NOT NULL,
  `_value` text NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`page_data_id`) USING BTREE,
  KEY `user` (`page_id`) USING BTREE,
  CONSTRAINT `FK_page_data_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.page_data: ~5 rows (aproximadamente)
DELETE FROM `page_data`;
/*!40000 ALTER TABLE `page_data` DISABLE KEYS */;
INSERT INTO `page_data` (`page_data_id`, `page_id`, `_key`, `_value`, `status`, `date_create`, `date_update`) VALUES
	(28, 98, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Materialize"},{"name":"description","content":"A modern responsive front-end framework based on Material Design\\u00a0\\u00a0..."},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Materialize"},{"property":"og:description","content":"A modern responsive front-end framework based on Material Design\\u00a0\\u00a0..."},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/materialize"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Materialize"},{"property":"twitter:description","content":"A modern responsive front-end framework based on Material Design\\u00a0\\u00a0..."},{"property":"twitter:image","content":""}]', 1, '2020-09-17 21:05:59', '2020-09-17 21:10:40'),
	(29, 98, 'tags', '["css","frontend"]', 1, '2020-09-17 21:07:35', '2020-09-17 21:07:35'),
	(30, 98, 'title', 'Materialize', 1, '2020-09-17 21:09:13', '2020-09-17 21:09:13'),
	(31, 98, 'headers_includes', '', 1, '2020-09-17 21:12:06', '2020-09-17 21:33:11'),
	(32, 98, 'footer_includes', '', 1, '2020-09-17 21:12:16', '2020-09-17 21:33:25');
/*!40000 ALTER TABLE `page_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.page_type
CREATE TABLE IF NOT EXISTS `page_type` (
  `page_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_type_name` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`page_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.page_type: ~3 rows (aproximadamente)
DELETE FROM `page_type`;
/*!40000 ALTER TABLE `page_type` DISABLE KEYS */;
INSERT INTO `page_type` (`page_type_id`, `page_type_name`, `date_create`, `date_update`, `status`) VALUES
	(1, 'page', '2020-04-18 21:17:02', '2020-04-18 21:18:29', 1),
	(2, 'blog', '2020-04-18 21:18:36', '2020-04-18 21:18:36', 1),
	(3, 'news', '2020-04-18 21:18:45', '2020-04-18 21:18:45', 1);
/*!40000 ALTER TABLE `page_type` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.permisions
CREATE TABLE IF NOT EXISTS `permisions` (
  `permisions_id` int(11) NOT NULL AUTO_INCREMENT,
  `permision_name` varchar(50) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`permisions_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Volcando datos para la tabla start_cms.permisions: ~5 rows (aproximadamente)
DELETE FROM `permisions`;
/*!40000 ALTER TABLE `permisions` DISABLE KEYS */;
INSERT INTO `permisions` (`permisions_id`, `permision_name`, `date_create`, `date_update`, `status`) VALUES
	(1, 'CREATE_USER', '2020-09-20 09:51:14', '2020-09-20 09:52:03', 1),
	(2, 'UPDATE_USER', '2020-09-20 09:51:27', '2020-09-20 09:52:04', 1),
	(3, 'DELETE_USER', '2020-09-20 09:51:34', '2020-09-20 09:52:05', 1),
	(4, 'SELECT_USERS', '2020-09-20 09:52:01', '2020-09-20 09:52:06', 1),
	(5, 'UPDATE_USERS', '2020-09-20 12:13:54', '2020-09-20 12:13:54', 1);
/*!40000 ALTER TABLE `permisions` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.relations
CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tablename` tinytext NOT NULL,
  `id_row` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `action` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.relations: ~0 rows (aproximadamente)
DELETE FROM `relations`;
/*!40000 ALTER TABLE `relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `relations` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.site_config
CREATE TABLE IF NOT EXISTS `site_config` (
  `site_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `config_name` varchar(500) NOT NULL,
  `config_value` varchar(500) NOT NULL,
  `config_type` varchar(500) DEFAULT NULL,
  `config_data` text DEFAULT NULL,
  `readonly` int(11) NOT NULL DEFAULT 0,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`site_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.site_config: ~11 rows (aproximadamente)
DELETE FROM `site_config`;
/*!40000 ALTER TABLE `site_config` DISABLE KEYS */;
INSERT INTO `site_config` (`site_config_id`, `user_id`, `config_name`, `config_value`, `config_type`, `config_data`, `readonly`, `date_create`, `date_update`, `status`) VALUES
	(1, 1, 'SITE_TITLE', 'Start Codeigneiter Hola', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-09-08 15:43:59', '2020-09-06 11:49:24', 1),
	(2, 1, 'SITE_DESCRIPTION', 'My Great website made by Start Codeigneiter', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-09-06 18:38:13', '2020-09-06 11:49:12', 1),
	(3, 1, 'SITE_ADMIN_EMAIL', 'gerber@email.com', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "email",\r\n  "max_lenght": "150",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "email",\r\n  "perm_values": null\r\n}', 0, '2020-09-06 18:28:44', '2020-09-06 11:49:02', 1),
	(4, 1, 'SITE_LANGUAGE', 'esp', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "0",\r\n  "handle_as": "select",\r\n  "input_type": "select",\r\n  "perm_values": ["en", "esp"]\r\n}', 0, '2020-09-06 18:38:20', '2020-09-06 13:26:38', 1),
	(5, 1, 'SITE_TIME_ZONE', 'UTC-10', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "0",\r\n  "handle_as": "select",\r\n  "input_type": "select",\r\n  "perm_values": {\r\n    "UTC-12": "UTC-12",\r\n    "UTC-11.5": "UTC-11:30",\r\n    "UTC-11": "UTC-11",\r\n    "UTC-10.5": "UTC-10:30",\r\n    "UTC-10": "UTC-10",\r\n    "UTC-9.5": "UTC-9:30",\r\n    "UTC-9": "UTC-9",\r\n    "UTC-8.5": "UTC-8:30",\r\n    "UTC-8": "UTC-8",\r\n    "UTC-7.5": "UTC-7:30",\r\n    "UTC-7": "UTC-7",\r\n    "UTC-6.5": "UTC-6:30",\r\n    "UTC-6": "UTC-6",\r\n    "UTC-5.5": "UTC-5:30",\r\n    "UTC-5": "UTC-5",\r\n    "UTC-4.5": "UTC-4:30",\r\n    "UTC-4": "UTC-4",\r\n    "UTC-3.5": "UTC-3:30",\r\n    "UTC-3": "UTC-3",\r\n    "UTC-2.5": "UTC-2:30",\r\n    "UTC-2": "UTC-2",\r\n    "UTC-1.5": "UTC-1:30",\r\n    "UTC-1": "UTC-1",\r\n    "UTC-0.5": "UTC-0:30",\r\n    "UTC+0": "UTC+0",\r\n    "UTC+0.5": "UTC+0:30",\r\n    "UTC+1": "UTC+1",\r\n    "UTC+1.5": "UTC+1:30",\r\n    "UTC+2": "UTC+2",\r\n    "UTC+2.5": "UTC+2:30",\r\n    "UTC+3": "UTC+3",\r\n    "UTC+3.5": "UTC+3:30",\r\n    "UTC+4": "UTC+4",\r\n    "UTC+4.5": "UTC+4:30",\r\n    "UTC+5": "UTC+5",\r\n    "UTC+5.5": "UTC+5:30",\r\n    "UTC+5.75": "UTC+5:45",\r\n    "UTC+6": "UTC+6",\r\n    "UTC+6.5": "UTC+6:30",\r\n    "UTC+7": "UTC+7",\r\n    "UTC+7.5": "UTC+7:30",\r\n    "UTC+8": "UTC+8",\r\n    "UTC+8.5": "UTC+8:30",\r\n    "UTC+8.75": "UTC+8:45",\r\n    "UTC+9": "UTC+9",\r\n    "UTC+9.5": "UTC+9:30",\r\n    "UTC+10": "UTC+10",\r\n    "UTC+10.5": "UTC+10:30",\r\n    "UTC+11": "UTC+11",\r\n    "UTC+11.5": "UTC+11:30",\r\n    "UTC+12": "UTC+12",\r\n    "UTC+12.75": "UTC+12:45",\r\n    "UTC+13": "UTC+13",\r\n    "UTC+13.75": "UTC+13:45",\r\n    "UTC+14": "UTC+14"\r\n  }\r\n}', 0, '2020-09-06 18:15:28', '2020-09-06 13:26:48', 1),
	(6, 0, 'SITE_DATE_FORMAT', 'j \\d\\e F \\d\\e Y', 'general', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-09-05 15:48:17', '2020-09-05 21:13:53', 1),
	(7, 1, 'SITE_TIME_FORMAT', 'H:i', 'general', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"0"}', 0, '2020-09-06 01:44:30', '2020-09-05 21:14:06', 1),
	(9, 1, 'SITE_LIST_MAX_ENTRY', '10', 'lectura', '{"type_value":"number","validate_as":"number","max_lenght":"50","min_lenght":"0"}', 0, '2020-09-06 18:28:31', '2020-09-05 21:09:53', 1),
	(11, 1, 'SITE_LIST_PUBLIC', 'No', 'lectura', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-09-06 19:30:40', '2020-09-06 14:03:10', 1),
	(12, 1, 'SITE_AUTHOR', 'Gervis Bermudez', 'general', '{"type_value":"string","validate_as":"name","max_lenght":"50","min_lenght":"5"}', 0, '2020-09-06 18:29:01', '2020-09-05 21:05:52', 1),
	(13, 0, 'LAST_UPDATE_FILEMANAGER', '2020-10-03 21:19:46', 'timestamp', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-01 12:02:11', '2020-10-03 16:19:46', 1);
/*!40000 ALTER TABLE `site_config` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.suscriptions
CREATE TABLE IF NOT EXISTS `suscriptions` (
  `suscriptions_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(75) NOT NULL,
  `email` varchar(125) NOT NULL,
  `fecha` datetime NOT NULL,
  `codigo` varchar(75) NOT NULL,
  `estado` set('Verificado','No verificado') NOT NULL DEFAULT 'No verificado',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`suscriptions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.suscriptions: ~0 rows (aproximadamente)
DELETE FROM `suscriptions`;
/*!40000 ALTER TABLE `suscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `suscriptions` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(500) NOT NULL DEFAULT '1234',
  `email` varchar(255) NOT NULL,
  `lastseen` datetime NOT NULL,
  `usergroup_id` int(11) NOT NULL DEFAULT 3,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  KEY `usergroup_id` (`usergroup_id`),
  CONSTRAINT `FK_user_usergroup` FOREIGN KEY (`usergroup_id`) REFERENCES `usergroup` (`usergroup_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

-- Volcando datos para la tabla start_cms.user: ~3 rows (aproximadamente)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `lastseen`, `usergroup_id`, `status`, `date_create`, `date_update`) VALUES
	(1, 'gerber', '$2y$10$r0DE7OWmcoEsziRfl.qor.PhSYpYq.p0K6C.xY4oDoF10W5JCPbiq', 'gerber@gmail.com', '2020-10-07 22:34:06', 1, 1, '2020-03-01 16:11:25', '2020-09-09 14:56:41'),
	(2, 'yduran', '$2y$10$.Rd9Ke7opDn2zvjc70DESuilWjm2mIMB9R2qyHyKTQbYQRYxGI6A2', 'yduran@gmail.com', '2020-10-07 20:09:26', 2, 1, '2020-03-01 16:11:25', '2020-09-20 19:05:39'),
	(3, 'nestor', '$2y$10$todx7BAG8S1cSoKOYxtrPuF412C1FvKuuaJWU1jNb/28ahu0a30GW', 'nestor@email.com', '2020-09-21 00:22:31', 4, 1, '2020-09-20 19:22:31', '2020-09-20 19:22:31');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.usergroup
CREATE TABLE IF NOT EXISTS `usergroup` (
  `usergroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `level` int(11) NOT NULL,
  `description` tinytext NOT NULL,
  `user_id` tinyint(4) NOT NULL DEFAULT 0,
  `parent_id` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`usergroup_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.usergroup: ~5 rows (aproximadamente)
DELETE FROM `usergroup`;
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` (`usergroup_id`, `name`, `level`, `description`, `user_id`, `parent_id`, `status`, `date_create`, `date_update`) VALUES
	(1, 'root', 1, 'All permisions allowed', 0, 0, 1, '2016-08-27 09:22:22', '2020-03-01 16:10:01'),
	(2, 'Administrador', 2, 'All configurations allowed', 1, 0, 1, '2016-08-27 09:22:22', '2020-10-07 15:27:08'),
	(3, 'Estandar', 3, 'Not delete permisions allowed', 1, 2, 0, '2016-08-27 08:32:49', '2020-10-07 15:26:30'),
	(4, 'Publisher', 4, 'Only Insert and Update permisions allowed', 1, 3, 1, '2016-08-28 07:35:50', '2020-10-07 15:42:49'),
	(5, 'Editor', 5, 'Only insert permisions allowed', 1, 3, 0, '2016-08-29 03:21:39', '2020-10-07 15:42:51');
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.usergroup_permisions
CREATE TABLE IF NOT EXISTS `usergroup_permisions` (
  `usergroup_permisions_id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_id` int(11) NOT NULL,
  `permision_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`usergroup_permisions_id`) USING BTREE,
  KEY `usergroup_id` (`usergroup_id`),
  KEY `permision_id` (`permision_id`),
  CONSTRAINT `FK_usergroup_permisions_permisions` FOREIGN KEY (`permision_id`) REFERENCES `permisions` (`permisions_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usergroup_permisions_usergroup` FOREIGN KEY (`usergroup_id`) REFERENCES `usergroup` (`usergroup_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.usergroup_permisions: ~8 rows (aproximadamente)
DELETE FROM `usergroup_permisions`;
/*!40000 ALTER TABLE `usergroup_permisions` DISABLE KEYS */;
INSERT INTO `usergroup_permisions` (`usergroup_permisions_id`, `usergroup_id`, `permision_id`, `status`, `date_create`, `date_update`) VALUES
	(1, 1, 1, 1, '2020-09-20 09:55:35', '2020-09-20 09:55:35'),
	(2, 1, 2, 1, '2020-09-20 09:55:48', '2020-09-20 09:55:48'),
	(3, 1, 3, 1, '2020-09-20 09:56:09', '2020-09-20 09:56:09'),
	(4, 1, 4, 1, '2020-09-20 09:56:20', '2020-09-20 09:56:20'),
	(5, 2, 1, 1, '2020-09-20 12:07:34', '2020-09-20 12:07:34'),
	(6, 2, 4, 1, '2020-09-20 12:07:52', '2020-09-20 12:07:52'),
	(7, 2, 2, 1, '2020-09-20 12:08:01', '2020-09-20 12:08:07'),
	(8, 3, 4, 1, '2020-09-20 12:10:55', '2020-09-20 12:10:55');
/*!40000 ALTER TABLE `usergroup_permisions` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.user_data
CREATE TABLE IF NOT EXISTS `user_data` (
  `user_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `_key` varchar(100) NOT NULL,
  `_value` varchar(600) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_data_id`),
  KEY `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.user_data: ~17 rows (aproximadamente)
DELETE FROM `user_data`;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` (`user_data_id`, `user_id`, `_key`, `_value`, `status`, `date_create`, `date_update`) VALUES
	(1, 1, 'nombre', 'Gervis', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:30'),
	(2, 1, 'apellido', 'Mora', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:34'),
	(3, 1, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:35'),
	(4, 1, 'telefono', '0414-1672173', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:36'),
	(5, 1, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:38'),
	(6, 1, 'avatar', '300_3.jpg', 1, '2020-03-01 16:31:46', '2020-09-09 16:03:29'),
	(7, 2, 'nombre', 'Yule', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:40'),
	(8, 2, 'apellido', 'Duran', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:42'),
	(9, 2, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:43'),
	(10, 2, 'telefono', '0412-9873920', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:46'),
	(11, 2, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:48'),
	(12, 2, 'avatar', '300_4.jpg', 1, '2020-05-02 19:21:05', '2020-05-25 11:48:50'),
	(13, 3, 'nombre', 'Nestor', 1, '2020-09-20 19:22:31', '2020-09-20 19:22:31'),
	(14, 3, 'apellido', 'Barroso', 1, '2020-09-20 19:22:31', '2020-09-20 19:22:31'),
	(15, 3, 'direccion', 'Gascon 1610', 1, '2020-09-20 19:22:31', '2020-09-20 19:22:31'),
	(16, 3, 'telefono', '1157614678', 1, '2020-09-20 19:22:31', '2020-09-20 19:22:31'),
	(17, 3, 'create_by_id', '2', 1, '2020-09-20 19:22:31', '2020-09-20 19:22:31');
/*!40000 ALTER TABLE `user_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.video
CREATE TABLE IF NOT EXISTS `video` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT,
  `nam` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `duration` varchar(50) NOT NULL,
  `youtube_id` varchar(6000) NOT NULL,
  `preview` varchar(2000) NOT NULL,
  `payinfo` text NOT NULL,
  `date_publish` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.video: ~0 rows (aproximadamente)
DELETE FROM `video`;
/*!40000 ALTER TABLE `video` DISABLE KEYS */;
/*!40000 ALTER TABLE `video` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.video-categoria
CREATE TABLE IF NOT EXISTS `video-categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_video` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.video-categoria: ~0 rows (aproximadamente)
DELETE FROM `video-categoria`;
/*!40000 ALTER TABLE `video-categoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `video-categoria` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
