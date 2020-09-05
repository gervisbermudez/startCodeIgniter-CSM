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


-- Volcando estructura de base de datos para start_cms
CREATE DATABASE IF NOT EXISTS `start_cms` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `start_cms`;

-- Volcando estructura para tabla start_cms.album
CREATE TABLE IF NOT EXISTS `album` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `description` tinytext NOT NULL,
  `date_publish` datetime NOT NULL,
  `path` varchar(125) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`album_id`),
  UNIQUE KEY `nombre` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.album: ~0 rows (aproximadamente)
DELETE FROM `album`;
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
/*!40000 ALTER TABLE `album` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.album_items
CREATE TABLE IF NOT EXISTS `album_items` (
  `album_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `nombre` varchar(125) NOT NULL,
  `titulo` varchar(75) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`album_item_id`),
  KEY `id_album` (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.album_items: ~0 rows (aproximadamente)
DELETE FROM `album_items`;
/*!40000 ALTER TABLE `album_items` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.file: ~0 rows (aproximadamente)
DELETE FROM `file`;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
/*!40000 ALTER TABLE `file` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.form_content: ~0 rows (aproximadamente)
DELETE FROM `form_content`;
/*!40000 ALTER TABLE `form_content` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.form_content_data: ~0 rows (aproximadamente)
DELETE FROM `form_content_data`;
/*!40000 ALTER TABLE `form_content_data` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_custom: ~1 rows (aproximadamente)
DELETE FROM `form_custom`;
/*!40000 ALTER TABLE `form_custom` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_fields: ~0 rows (aproximadamente)
DELETE FROM `form_fields`;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_fields_data: ~0 rows (aproximadamente)
DELETE FROM `form_fields_data`;
/*!40000 ALTER TABLE `form_fields_data` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Volcando datos para la tabla start_cms.form_tabs: ~0 rows (aproximadamente)
DELETE FROM `form_tabs`;
/*!40000 ALTER TABLE `form_tabs` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.page: ~0 rows (aproximadamente)
DELETE FROM `page`;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` (`page_id`, `path`, `template`, `title`, `subtitle`, `content`, `page_type_id`, `user_id`, `visibility`, `categorie_id`, `subcategorie_id`, `status`, `layout`, `mainImage`, `date_publish`, `date_update`, `date_create`) VALUES
	(66, 'componentes-funcionales-y-de-clase', 'default', 'Componentes funcionales y de clase', '', '<p style="margin: 30px 0px 0px; padding: 0px; box-sizing: inherit; font-size: 17px; line-height: 1.7; max-width: 42em; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;">La forma m&aacute;s sencilla de definir un componente es escribir una funci&oacute;n de JavaScript:</p>\n<div class="gatsby-highlight" style="margin: 25px -30px; padding: 0px 15px; box-sizing: inherit; background: #282c34; color: #ffffff; border-radius: 10px; overflow: auto; tab-size: 1.5em; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif; font-size: medium;" data-language="jsx">\n<pre class="gatsby-code-jsx" style="margin: 1rem; padding: 0px; box-sizing: inherit; font-family: source-code-pro, Menlo, Monaco, Consolas, \'Courier New\', monospace; font-size: 14px; line-height: 20px; white-space: pre-wrap; word-break: break-word; height: auto !important;"><code class="gatsby-code-jsx" style="margin: 0px; padding: 0px; box-sizing: inherit; font-family: source-code-pro, Menlo, Monaco, Consolas, \'Courier New\', monospace; font-size: 1em;"><span class="token keyword" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #c5a5c5;">function</span> <span class="token function" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #79b6f2;">Welcome</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">(</span><span class="token parameter" style="margin: 0px; padding: 0px; box-sizing: inherit;">props</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">)</span> <span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">{</span>\n  <span class="token keyword" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #c5a5c5;">return</span> <span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #fc929e;"><span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit;"><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&lt;</span>h1</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&gt;</span></span><span class="token plain-text" style="margin: 0px; padding: 0px; box-sizing: inherit;">Hello, </span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">{</span>props<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">.</span>name<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">}</span><span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #fc929e;"><span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit;"><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&lt;/</span>h1</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&gt;</span></span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">;</span>\n<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">}</span></code></pre>\n</div>\n<p style="margin: 30px 0px 0px; padding: 0px; box-sizing: inherit; font-size: 17px; line-height: 1.7; max-width: 42em; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;">Esta funci&oacute;n es un componente de React v&aacute;lido porque acepta un solo argumento de objeto &ldquo;props&rdquo; (que proviene de propiedades) con datos y devuelve un elemento de React. Llamamos a dichos componentes &ldquo;funcionales&rdquo; porque literalmente son funciones JavaScript.</p>\n<p style="margin: 30px 0px 0px; padding: 0px; box-sizing: inherit; font-size: 17px; line-height: 1.7; max-width: 42em; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;">Tambi&eacute;n puedes utilizar una&nbsp;<a style="margin: 0px; padding: 0px; box-sizing: inherit; background-color: rgba(187, 239, 253, 0.3); color: #1a1a1a; text-decoration-line: none; border-bottom: 1px solid rgba(0, 0, 0, 0.2);" href="https://developer.mozilla.org/es/docs/Web/JavaScript/Referencia/Classes" target="_blank" rel="nofollow noopener noreferrer">clase de ES6</a>&nbsp;para definir un componente:</p>\n<div class="gatsby-highlight" style="margin: 25px -30px; padding: 0px 15px; box-sizing: inherit; background: #282c34; color: #ffffff; border-radius: 10px; overflow: auto; tab-size: 1.5em; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif; font-size: medium;" data-language="jsx">\n<pre class="gatsby-code-jsx" style="margin: 1rem; padding: 0px; box-sizing: inherit; font-family: source-code-pro, Menlo, Monaco, Consolas, \'Courier New\', monospace; font-size: 14px; line-height: 20px; white-space: pre-wrap; word-break: break-word; height: auto !important;"><code class="gatsby-code-jsx" style="margin: 0px; padding: 0px; box-sizing: inherit; font-family: source-code-pro, Menlo, Monaco, Consolas, \'Courier New\', monospace; font-size: 1em;"><span class="token keyword" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #c5a5c5;">class</span> <span class="token class-name" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #fac863;">Welcome</span> <span class="token keyword" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #c5a5c5;">extends</span> <span class="token class-name" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #fac863;">React<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">.</span>Component</span> <span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">{</span>\n  <span class="token function" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #79b6f2;">render</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">(</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">)</span> <span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">{</span>\n    <span class="token keyword" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #c5a5c5;">return</span> <span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #fc929e;"><span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit;"><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&lt;</span>h1</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&gt;</span></span><span class="token plain-text" style="margin: 0px; padding: 0px; box-sizing: inherit;">Hello, </span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">{</span><span class="token keyword" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #c5a5c5;">this</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">.</span>props<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">.</span>name<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">}</span><span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #fc929e;"><span class="token tag" style="margin: 0px; padding: 0px; box-sizing: inherit;"><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&lt;/</span>h1</span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">&gt;</span></span><span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">;</span>\n  <span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">}</span>\n<span class="token punctuation" style="margin: 0px; padding: 0px; box-sizing: inherit; color: #88c6be;">}</span></code></pre>\n</div>\n<p style="margin: 30px 0px 0px; padding: 0px; box-sizing: inherit; font-size: 17px; line-height: 1.7; max-width: 42em; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;">Los dos componentes anteriores son equivalentes desde el punto de vista de React.</p>', 1, 1, 1, 6, 0, 1, 'default', 310, '2020-08-24 19:15:41', NULL, '2020-08-24 19:15:41');
/*!40000 ALTER TABLE `page` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.page_data
CREATE TABLE IF NOT EXISTS `page_data` (
  `page_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `_key` varchar(100) NOT NULL,
  `_value` varchar(600) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`page_data_id`) USING BTREE,
  KEY `user` (`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.page_data: ~0 rows (aproximadamente)
DELETE FROM `page_data`;
/*!40000 ALTER TABLE `page_data` DISABLE KEYS */;
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
  `perm_values` text DEFAULT NULL,
  `config_type` varchar(500) DEFAULT NULL,
  `config_data` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`site_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.site_config: ~10 rows (aproximadamente)
DELETE FROM `site_config`;
/*!40000 ALTER TABLE `site_config` DISABLE KEYS */;
INSERT INTO `site_config` (`site_config_id`, `user_id`, `config_name`, `config_value`, `perm_values`, `config_type`, `config_data`, `status`, `date_create`, `date_update`) VALUES
	(1, 1, 'SITE_TITLE', 'Start Codeigneiter CMS', 'null', 'general', '"{"type_value":"string"}"', 1, '2020-09-05 22:05:58', '2020-09-05 16:30:35'),
	(2, 1, 'SITE_DESCRIPTION', 'My Great website made by Start Codeigneiter CMS', 'null', 'general', '"{"type_value":"string"}"', 1, '2020-09-05 22:07:38', '2020-09-05 16:30:47'),
	(3, 0, 'SITE_ADMIN_EMAIL', 'gerber@email.com', 'null', 'general', '"{"type_value":"string"}"', 1, '2020-09-04 12:40:40', '2020-09-05 16:01:08'),
	(4, 0, 'SITE_LANGUAGE', 'en', '["en","esp"]', 'general', '"{"ENGLISH":{"LANG_NAME":"ENGLISH","LANG_LABEL":"ENGLISH","lANG_CODE":"en","LANG_HTML_ATTR":"en"},"ESPANISH":', 1, '2020-09-04 12:40:40', '2020-09-05 17:20:04'),
	(5, 0, 'SITE_TIME_ZONE', 'UTC +0', '{"UTC-12":"UTC-12","UTC-11.5":"UTC-11:30","UTC-11":"UTC-11","UTC-10.5":"UTC-10:30","UTC-10":"UTC-10","UTC-9.5":"UTC-9:30","UTC-9":"UTC-9","UTC-8.5":"UTC-8:30","UTC-8":"UTC-8","UTC-7.5":"UTC-7:30","UTC-7":"UTC-7","UTC-6.5":"UTC-6:30","UTC-6":"UTC-6","UTC-5.5":"UTC-5:30","UTC-5":"UTC-5","UTC-4.5":"UTC-4:30","UTC-4":"UTC-4","UTC-3.5":"UTC-3:30","UTC-3":"UTC-3","UTC-2.5":"UTC-2:30","UTC-2":"UTC-2","UTC-1.5":"UTC-1:30","UTC-1":"UTC-1","UTC-0.5":"UTC-0:30","UTC+0":"UTC+0","UTC+0.5":"UTC+0:30","UTC+1":"UTC+1","UTC+1.5":"UTC+1:30","UTC+2":"UTC+2","UTC+2.5":"UTC+2:30","UTC+3":"UTC+3","UTC+3.5":"UTC+3:30","UTC+4":"UTC+4","UTC+4.5":"UTC+4:30","UTC+5":"UTC+5","UTC+5.5":"UTC+5:30","UTC+5.75":"UTC+5:45","UTC+6":"UTC+6","UTC+6.5":"UTC+6:30","UTC+7":"UTC+7","UTC+7.5":"UTC+7:30","UTC+8":"UTC+8","UTC+8.5":"UTC+8:30","UTC+8.75":"UTC+8:45","UTC+9":"UTC+9","UTC+9.5":"UTC+9:30","UTC+10":"UTC+10","UTC+10.5":"UTC+10:30","UTC+11":"UTC+11","UTC+11.5":"UTC+11:30","UTC+12":"UTC+12","UTC+12.75":"UTC+12:45","UTC+13":"UTC+13","UTC+13.75":"UTC+13:45","UTC+14":"UTC+14","America/Adak":"Adak","America/Anchorage":"Anchorage","America/Anguilla":"Anguila","America/Antigua":"Antigua","America/Araguaina":"Araguaina","America/Argentina/Buenos_Aires":"Argentina - Buenos Aires","America/Argentina/Catamarca":"Argentina - Catamarca","America/Argentina/Cordoba":"Argentina - Córdoba","America/Argentina/Jujuy":"Argentina - Jujuy","America/Argentina/La_Rioja":"Argentina - La Rioja","America/Argentina/Mendoza":"Argentina - Mendoza","America/Argentina/Rio_Gallegos":"Argentina - Río Gallegos","America/Argentina/Salta":"Argentina - Salta","America/Argentina/San_Juan":"Argentina - San Juan","America/Argentina/San_Luis":"Argentina - San Luis","America/Argentina/Tucuman":"Argentina - Tucumán","America/Argentina/Ushuaia":"Argentina - Ushuaia","America/Aruba":"Aruba","America/Asuncion":"Asunción","America/Atikokan":"Atikokan","America/Bahia":"Bahía","America/Bahia_Banderas":"Bahía Banderas","America/Barbados":"Barbados","America/Belize":"Belice","America/Belem":"Belén","America/Blanc-Sablon":"Blanc-Sablon","America/Boa_Vista":"Boa Vista","America/Bogota":"Bogotá","America/Boise":"Boise","America/Cayman":"Caimán","America/Cambridge_Bay":"Cambridge Bay","America/Campo_Grande":"Campo Grande","America/Cancun":"Cancún","America/Caracas":"Caracas","America/Cayenne":"Cayena","America/Chicago":"Chicago","America/Chihuahua":"Chihuahua","America/Mexico_City":"Ciudad de México","America/Costa_Rica":"Costa Rica","America/Creston":"Creston","America/Cuiaba":"Cuiaba","America/Curacao":"Curazao","America/North_Dakota/Beulah":"Dakota del Norte - Beulah","America/North_Dakota/Center":"Dakota del Norte - Center","America/North_Dakota/New_Salem":"Dakota del Norte - New Salem","America/Danmarkshavn":"Danmarkshavn","America/Dawson":"Dawson","America/Dawson_Creek":"Dawson Creek","America/Denver":"Denver","America/Detroit":"Detroit","America/Dominica":"Dominica","America/Edmonton":"Edmonton","America/Eirunepe":"Eirunepe","America/El_Salvador":"El Salvador","America/Fortaleza":"Fortaleza","America/Fort_Nelson":"Fuerte Nelson","America/Glace_Bay":"Glace Bay","America/Godthab":"Godthab","America/Goose_Bay":"Goose Bay","America/Grand_Turk":"Gran Turca","America/Grenada":"Grenada","America/Guadeloupe":"Guadalupe","America/Guatemala":"Guatemala","America/Guayaquil":"Guayaquil","America/Guyana":"Guyana","America/Halifax":"Halifax","America/Hermosillo":"Hermosillo","America/Indiana/Indianapolis":"Indiana - Indianápolis","America/Indiana/Knox":"Indiana - Knox","America/Indiana/Marengo":"Indiana - Marengo","America/Indiana/Petersburg":"Indiana - San Petersburgo","America/Indiana/Tell_City":"Indiana - Tell City","America/Indiana/Vevay":"Indiana - Vevay","America/Indiana/Vincennes":"Indiana - Vincennes","America/Indiana/Winamac":"Indiana - Winamac","America/Inuvik":"Inuvik","America/Iqaluit":"Iqaluit","America/Jamaica":"Jamaica","America/Juneau":"Juneau","America/Kentucky/Louisville":"Kentucky - Louisville","America/Kentucky/Monticello":"Kentucky - Monticello","America/Kralendijk":"Kralendijk","America/Havana":"La Habana","America/La_Paz":"La Paz","America/Lima":"Lima","America/Los_Angeles":"Los Ángeles","America/Lower_Princes":"Lower Princes","America/Maceio":"Maceio","America/Managua":"Managua","America/Manaus":"Manaos","America/Marigot":"Marigot","America/Martinique":"Martinica","America/Matamoros":"Matamoros","America/Mazatlan":"Mazatlán","America/Menominee":"Menominee","America/Metlakatla":"Metlakatla","America/Miquelon":"Miquelon","America/Moncton":"Moncton","America/Monterrey":"Monterrey","America/Montevideo":"Montevideo","America/Montserrat":"Montserrat","America/Merida":"Mérida","America/Nassau":"Nassau","America/Nipigon":"Nipigon","America/Nome":"Nome","America/Noronha":"Noronha","America/New_York":"Nueva York","America/Ojinaga":"Ojinaga","America/Panama":"Panamá","America/Pangnirtung":"Pangnirtung","America/Paramaribo":"Paramaribo","America/Phoenix":"Phoenix","America/Porto_Velho":"Porto Velho","America/Port_of_Spain":"Puerto España","America/Port-au-Prince":"Puerto Príncipe","America/Puerto_Rico":"Puerto Rico","America/Punta_Arenas":"Punta Arenas","America/Rainy_River":"Rainy River","America/Rankin_Inlet":"Rankin Inlet","America/Recife":"Recife","America/Regina":"Regina","America/Resolute":"Resolute","America/Rio_Branco":"Río Branco","America/St_Lucia":"Santa Lucía","America/Santarem":"Santarem","America/Santiago":"Santiago","America/Santo_Domingo":"Santo Domingo","America/St_Thomas":"Santo Tomás","America/St_Vincent":"San Vincente","America/Sao_Paulo":"Sao Paulo","America/Scoresbysund":"Scoresbysund","America/Sitka":"Sitka","America/St_Barthelemy":"St Barthelemy","America/St_Johns":"St Johns","America/St_Kitts":"St Kitts","America/Swift_Current":"Swift Current","America/Tegucigalpa":"Tegucigalpa","America/Thule":"Thule","America/Thunder_Bay":"Thunder Bay","America/Tijuana":"Tijuana","America/Toronto":"Toronto","America/Tortola":"Tortola","America/Vancouver":"Vancouver","America/Whitehorse":"Whitehorse","America/Winnipeg":"Winnipeg","America/Yakutat":"Yakutat","America/Yellowknife":"Yellowknife","Antarctica/Casey":"Casey","Antarctica/Davis":"Davis","Antarctica/DumontDUrville":"DumontDUrville","Antarctica/Macquarie":"Macquarie","Antarctica/Mawson":"Mawson","Antarctica/McMurdo":"McMurdo","Antarctica/Palmer":"Palmer","Antarctica/Rothera":"Rothera","Antarctica/Syowa":"Syowa","Antarctica/Troll":"Troll","Antarctica/Vostok":"Vostok","Asia/Aden":"Aden","Asia/Almaty":"Almaty","Asia/Amman":"Amman","Asia/Anadyr":"Anadyr","Asia/Aqtau":"Aqtau","Asia/Aqtobe":"Aqtobe","Asia/Ashgabat":"Ashgabat","Asia/Atyrau":"Atyrau","Asia/Baghdad":"Bagdad","Asia/Baku":"Bakú","Asia/Bangkok":"Bangkok","Asia/Barnaul":"Barnaul","Asia/Bahrain":"Baréin","Asia/Beirut":"Beirut","Asia/Bishkek":"Bishkek","Asia/Brunei":"Brunei","Asia/Qatar":"Catar","Asia/Chita":"Chita","Asia/Choibalsan":"Choibalsan","Asia/Colombo":"Colombo","Asia/Damascus":"Damasco","Asia/Dhaka":"Dhaka","Asia/Dili":"Dili","Asia/Dubai":"Dubai","Asia/Dushanbe":"Dushanbe","Asia/Yekaterinburg":"Ekaterinburgo","Asia/Yerevan":"Ereván","Asia/Famagusta":"Famagusta","Asia/Gaza":"Gaza","Asia/Hebron":"Hebrón","Asia/Ho_Chi_Minh":"Ho Chi Minh","Asia/Hong_Kong":"Hong Kong","Asia/Hovd":"Hovd","Asia/Irkutsk":"Irkutsk","Asia/Jakarta":"Jakarta","Asia/Jayapura":"Jayapura","Asia/Jerusalem":"Jerusalén","Asia/Kabul":"Kabul","Asia/Kamchatka":"Kamchatka","Asia/Karachi":"Karachi","Asia/Kathmandu":"Kathmandu","Asia/Khandyga":"Khandyga","Asia/Kolkata":"Kolkata","Asia/Krasnoyarsk":"Krasnoyarsk","Asia/Kuala_Lumpur":"Kuala Lumpur","Asia/Kuching":"Kuching","Asia/Kuwait":"Kuwait","Asia/Macau":"Macao","Asia/Magadan":"Magadan","Asia/Makassar":"Makassar","Asia/Manila":"Manila","Asia/Muscat":"Mascate","Asia/Nicosia":"Nicosia","Asia/Novokuznetsk":"Novokuznetsk","Asia/Novosibirsk":"Novosibirsk","Asia/Omsk":"Omsk","Asia/Oral":"Oral","Asia/Phnom_Penh":"Phnom Penh","Asia/Pontianak":"Pontianak","Asia/Pyongyang":"Pyongyang","Asia/Qostanay":"Qostanay","Asia/Qyzylorda":"Qyzylorda","Asia/Riyadh":"Riad","Asia/Sakhalin":"Sakhalin","Asia/Samarkand":"Samarcanda","Asia/Seoul":"Seúl","Asia/Shanghai":"Shanghái","Asia/Singapore":"Singapur","Asia/Srednekolymsk":"Srednekolymsk","Asia/Taipei":"Taipei","Asia/Tashkent":"Tashkent","Asia/Tbilisi":"Tbilisi","Asia/Tehran":"Teherán","Asia/Thimphu":"Thimphu","Asia/Tokyo":"Tokio","Asia/Tomsk":"Tomsk","Asia/Ulaanbaatar":"Ulaanbaatar","Asia/Urumqi":"Urumqi","Asia/Ust-Nera":"Ust-Nera","Asia/Vientiane":"Vientiane","Asia/Vladivostok":"Vladivostok","Asia/Yakutsk":"Yakutsk","Asia/Yangon":"Yangon","Atlantic/Azores":"Azores","Atlantic/Bermuda":"Bermudas","Atlantic/Cape_Verde":"Cabo Verde","Atlantic/Canary":"Canarias","Atlantic/Faroe":"Faroe","Atlantic/South_Georgia":"Georgia el Sur","Atlantic/Madeira":"Madeira","Atlantic/Reykjavik":"Reikiavik","Atlantic/St_Helena":"Santa Elena","Atlantic/Stanley":"Stanley","Australia/Adelaide":"Adelaida","Australia/Brisbane":"Brisbane","Australia/Broken_Hill":"Broken Hill","Australia/Currie":"Currie","Australia/Darwin":"Darwin","Australia/Eucla":"Eucla","Australia/Hobart":"Hobart","Australia/Lindeman":"Lindeman","Australia/Lord_Howe":"Lord Howe","Australia/Melbourne":"Melbourne","Australia/Perth":"Perth","Australia/Sydney":"Sidney","Europe/Andorra":"Andorra","Europe/Astrakhan":"Astrakhan","Europe/Athens":"Atenas","Europe/Belgrade":"Belgrado","Europe/Berlin":"Berlín","Europe/Bratislava":"Bratislava","Europe/Brussels":"Bruselas","Europe/Bucharest":"Bucarest","Europe/Budapest":"Budapest","Europe/Busingen":"Busingen","Europe/Chisinau":"Chisinau","Europe/Copenhagen":"Copenhague","Europe/Dublin":"Dublín","Europe/Istanbul":"Estambul","Europe/Stockholm":"Estocolmo","Europe/Gibraltar":"Gibraltar","Europe/Guernsey":"Guernsey","Europe/Helsinki":"Helsinki","Europe/Isle_of_Man":"Isla de Man","Europe/Jersey":"Jersey","Europe/Kaliningrad":"Kaliningrado","Europe/Kiev":"Kiev","Europe/Kirov":"Kirov","Europe/Lisbon":"Lisboa","Europe/Ljubljana":"Liubliana","Europe/London":"Londres","Europe/Luxembourg":"Luxemburgo","Europe/Madrid":"Madrid","Europe/Malta":"Malta","Europe/Mariehamn":"Mariehamn","Europe/Minsk":"Minsk","Europe/Moscow":"Moscú","Europe/Monaco":"Mónaco","Europe/Oslo":"Oslo","Europe/Paris":"París","Europe/Podgorica":"Podgorica","Europe/Prague":"Praga","Europe/Riga":"Riga","Europe/Rome":"Roma","Europe/Samara":"Samara","Europe/San_Marino":"San Marino","Europe/Sarajevo":"Sarajevo","Europe/Saratov":"Saratov","Europe/Simferopol":"Simferopol","Europe/Skopje":"Skopje","Europe/Sofia":"Sofía","Europe/Tallinn":"Tallín","Europe/Tirane":"Tirana","Europe/Ulyanovsk":"Ulyanovsk","Europe/Uzhgorod":"Uzhgorod","Europe/Vaduz":"Vaduz","Europe/Warsaw":"Varsovia","Europe/Vatican":"Vaticano","Europe/Vienna":"Viena","Europe/Vilnius":"Vilna","Europe/Volgograd":"Volgogrado","Europe/Zagreb":"Zagreb","Europe/Zaporozhye":"Zaporozhye","Europe/Zurich":"Zurich","Europe/Amsterdam":"Ámsterdam","Indian/Antananarivo":"Antananarivo","Indian/Chagos":"Chagos","Indian/Cocos":"Cocos","Indian/Comoro":"Comoro","Indian/Kerguelen":"Kerguelen","Indian/Mahe":"Mahe","Indian/Maldives":"Maldivas","Indian/Mauritius":"Mauricio","Indian/Mayotte":"Mayotte","Indian/Christmas":"Navidad","Indian/Reunion":"Reunión","Pacific/Apia":"Apia","Pacific/Auckland":"Auckland","Pacific/Bougainville":"Bougainville","Pacific/Chatham":"Chatham","Pacific/Chuuk":"Chuuk","Pacific/Efate":"Efate","Pacific/Enderbury":"Enderbury","Pacific/Fakaofo":"Fakaofo","Pacific/Fiji":"Fiyi","Pacific/Funafuti":"Funafuti","Pacific/Galapagos":"Galápagos","Pacific/Gambier":"Gambier","Pacific/Guadalcanal":"Guadalcanal","Pacific/Guam":"Guam","Pacific/Honolulu":"Honolulu","Pacific/Kiritimati":"Kiritimati","Pacific/Kosrae":"Kosrae","Pacific/Kwajalein":"Kwajalein","Pacific/Majuro":"Majuro","Pacific/Marquesas":"Marquesas","Pacific/Midway":"Midway","Pacific/Nauru":"Nauru","Pacific/Niue":"Niue","Pacific/Norfolk":"Norfolk","Pacific/Noumea":"Noumea","Pacific/Pago_Pago":"Pago Pago","Pacific/Palau":"Palaos","Pacific/Easter":"Pascua","Pacific/Pitcairn":"Pitcairn","Pacific/Pohnpei":"Pohnpei","Pacific/Port_Moresby":"Puerto Moresby","Pacific/Rarotonga":"Rarotonga","Pacific/Saipan":"Saipán","Pacific/Tahiti":"Tahití","Pacific/Tarawa":"Tarawa","Pacific/Tongatapu":"Tongatapu","Pacific/Wake":"Wake","Pacific/Wallis":"Wallis","Africa/Abidjan":"Abidjan","Africa/Accra":"Accra","Africa/Addis_Ababa":"Addis Abeba","Africa/Algiers":"Argel","Africa/Asmara":"Asmara","Africa/Bamako":"Bamako","Africa/Bangui":"Bangui","Africa/Banjul":"Banjul","Africa/Bissau":"Bisáu","Africa/Blantyre":"Blantyre","Africa/Brazzaville":"Brazzaville","Africa/Bujumbura":"Bujumbura","Africa/Casablanca":"Casablanca","Africa/Ceuta":"Ceuta","Africa/Conakry":"Conakry","Africa/Dakar":"Dakar","Africa/Dar_es_Salaam":"Dar es Salaam","Africa/Douala":"Douala","Africa/El_Aaiun":"El Aaiún","Africa/Cairo":"El Cairo","Africa/Freetown":"Freetown","Africa/Gaborone":"Gaborone","Africa/Harare":"Harare","Africa/Khartoum":"Jartum","Africa/Johannesburg":"Johanesburgo","Africa/Juba":"Juba","Africa/Kampala":"Kampala","Africa/Kigali":"Kigali","Africa/Kinshasa":"Kinshasa","Africa/Lagos":"Lagos","Africa/Libreville":"Libreville","Africa/Lome":"Lome","Africa/Luanda":"Luanda","Africa/Lubumbashi":"Lubumbashi","Africa/Lusaka":"Lusaka","Africa/Malabo":"Malabo","Africa/Maputo":"Maputo","Africa/Maseru":"Maseru","Africa/Mbabane":"Mbabane","Africa/Mogadishu":"Mogadiscio","Africa/Monrovia":"Monrovia","Africa/Nairobi":"Nairobi","Africa/Ndjamena":"Ndjamena","Africa/Niamey":"Niamey","Africa/Nouakchott":"Nouakchott","Africa/Ouagadougou":"Ouagadougou","Africa/Porto-Novo":"Porto-Novo","Africa/Sao_Tome":"Santo Tomé","Africa/Tripoli":"Trípoli","Africa/Tunis":"Túnez","Africa/Windhoek":"Windhoek","Africa/Djibouti":"Yibuti","Arctic/Longyearbyen":"Longyearbyen","UTC":"UTC"}', 'general', '"{"type_value":"string"}"', 1, '2020-09-04 12:40:40', '2020-09-04 12:59:56'),
	(6, 0, 'SITE_DATE_FORMAT', 'j \\d\\e F \\d\\e Y', 'null', 'general', '"{"type_value":"string"}"', 1, '2020-09-05 15:48:17', '2020-09-05 16:00:16'),
	(7, 0, 'SITE_TIME_FORMAT', 'H:i', 'null', 'general', '"{"type_value":"string"}"', 1, '2020-09-05 15:48:17', '2020-09-05 16:00:21'),
	(9, 0, 'SITE_LIST_MAX_ENTRY', '10', 'null', 'lectura', '"{"type_value":"number"}"', 1, '2020-09-05 15:50:42', '2020-09-05 16:00:24'),
	(11, 0, 'SITE_LIST_PUBLIC', '1', '[0,1]', 'lectura', 'boolean', 1, '2020-09-05 15:50:42', '2020-09-05 15:55:30'),
	(12, 1, 'SITE_AUTHOR', 'Gervis Bermudez', 'null', 'general', '"{"type_value":"string"}"', 1, '2020-09-05 22:00:09', '2020-09-05 16:30:47');
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
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

-- Volcando datos para la tabla start_cms.user: ~2 rows (aproximadamente)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `lastseen`, `usergroup_id`, `status`, `date_create`, `date_update`) VALUES
	(1, 'gerber', '$2y$10$r0DE7OWmcoEsziRfl.qor.PhSYpYq.p0K6C.xY4oDoF10W5JCPbiq', 'gerber@gmail.com', '2020-05-10 15:41:04', 1, 1, '2020-03-01 16:11:25', '2020-05-25 11:53:20'),
	(2, 'yduran', '$2y$10$.Rd9Ke7opDn2zvjc70DESuilWjm2mIMB9R2qyHyKTQbYQRYxGI6A2', 'yduran@gmail.com', '2017-03-05 17:12:06', 3, 1, '2020-03-01 16:11:25', '2020-05-25 11:47:36');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.userdatapermisions
CREATE TABLE IF NOT EXISTS `userdatapermisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_id` int(11) NOT NULL,
  `permision` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.userdatapermisions: ~0 rows (aproximadamente)
DELETE FROM `userdatapermisions`;
/*!40000 ALTER TABLE `userdatapermisions` DISABLE KEYS */;
/*!40000 ALTER TABLE `userdatapermisions` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.usergroup
CREATE TABLE IF NOT EXISTS `usergroup` (
  `usergroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `level` int(11) NOT NULL,
  `description` tinytext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`usergroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.usergroup: ~5 rows (aproximadamente)
DELETE FROM `usergroup`;
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` (`usergroup_id`, `name`, `level`, `description`, `status`, `date_create`, `date_update`) VALUES
	(1, 'root', 1, 'All permisions allowed', 1, '2016-08-27 09:22:22', '2020-03-01 16:10:01'),
	(2, 'Administrador', 2, 'All configurations allowed', 1, '2016-08-27 09:22:22', '2020-03-01 16:10:01'),
	(3, 'Estandar', 3, 'Not delete permisions allowed', 1, '2016-08-27 08:32:49', '2020-03-01 16:10:01'),
	(7, 'Publisher', 4, 'Only Insert and Update permisions allowed', 1, '2016-08-28 07:35:50', '2020-03-01 16:10:01'),
	(8, 'Editor', 5, 'Only insert permisions allowed', 1, '2016-08-29 03:21:39', '2020-03-01 16:10:01');
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.user_data: ~12 rows (aproximadamente)
DELETE FROM `user_data`;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` (`user_data_id`, `user_id`, `_key`, `_value`, `status`, `date_create`, `date_update`) VALUES
	(1, 1, 'nombre', 'Gervis', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:30'),
	(2, 1, 'apellido', 'Mora', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:34'),
	(3, 1, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:35'),
	(4, 1, 'telefono', '0414-1672173', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:36'),
	(5, 1, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:38'),
	(6, 1, 'avatar', '300_3.jpg', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:40'),
	(7, 2, 'nombre', 'Yule', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:40'),
	(8, 2, 'apellido', 'Duran', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:42'),
	(9, 2, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:43'),
	(10, 2, 'telefono', '0412-9873920', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:46'),
	(11, 2, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:48'),
	(12, 2, 'avatar', '300_4.jpg', 1, '2020-05-02 19:21:05', '2020-05-25 11:48:50');
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
