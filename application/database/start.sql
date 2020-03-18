-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.12-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for start_cms
DROP DATABASE IF EXISTS `start_cms`;
CREATE DATABASE IF NOT EXISTS `start_cms` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `start_cms`;

-- Dumping structure for table start_cms.album
DROP TABLE IF EXISTS `album`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.album: ~0 rows (approximately)
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
/*!40000 ALTER TABLE `album` ENABLE KEYS */;

-- Dumping structure for table start_cms.album_items
DROP TABLE IF EXISTS `album_items`;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.album_items: ~0 rows (approximately)
/*!40000 ALTER TABLE `album_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `album_items` ENABLE KEYS */;

-- Dumping structure for table start_cms.categories
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `categorie_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(600) NOT NULL,
  `date_publish` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Categorias del sistema';

-- Dumping data for table start_cms.categories: ~0 rows (approximately)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Dumping structure for table start_cms.contacts
DROP TABLE IF EXISTS `contacts`;
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

-- Dumping data for table start_cms.contacts: ~0 rows (approximately)
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;

-- Dumping structure for table start_cms.contacts_data
DROP TABLE IF EXISTS `contacts_data`;
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

-- Dumping data for table start_cms.contacts_data: ~0 rows (approximately)
/*!40000 ALTER TABLE `contacts_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts_data` ENABLE KEYS */;

-- Dumping structure for table start_cms.events
DROP TABLE IF EXISTS `events`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Eventos a publicar';

-- Dumping data for table start_cms.events: ~0 rows (approximately)
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_content
DROP TABLE IF EXISTS `form_content`;
CREATE TABLE IF NOT EXISTS `form_content` (
  `form_content_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_custom_id` int(11) NOT NULL DEFAULT 0,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Dumping data for table start_cms.form_content: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_content` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_content_data
DROP TABLE IF EXISTS `form_content_data`;
CREATE TABLE IF NOT EXISTS `form_content_data` (
  `form_custom_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_content_id` int(11) DEFAULT NULL,
  `form_key` varchar(200) DEFAULT NULL,
  `form_value` text DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_custom_data_id`),
  KEY `form_id` (`form_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Dumping data for table start_cms.form_content_data: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_content_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_content_data` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_custom
DROP TABLE IF EXISTS `form_custom`;
CREATE TABLE IF NOT EXISTS `form_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.form_custom: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_custom` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_custom` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_fields
DROP TABLE IF EXISTS `form_fields`;
CREATE TABLE IF NOT EXISTS `form_fields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_tab_id` int(11) DEFAULT NULL,
  `field_name` varchar(250) DEFAULT NULL,
  `displayName` varchar(250) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `component` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`field_id`),
  KEY `form_tab_id` (`form_tab_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.form_fields: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_field_config
DROP TABLE IF EXISTS `form_field_config`;
CREATE TABLE IF NOT EXISTS `form_field_config` (
  `form_field_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_field_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `config_name` varchar(200) DEFAULT NULL,
  `config_value` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_field_config_id`),
  KEY `form_id` (`form_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.form_field_config: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_field_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_field_config` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_tabs
DROP TABLE IF EXISTS `form_tabs`;
CREATE TABLE IF NOT EXISTS `form_tabs` (
  `form_tab_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT NULL,
  `tab_name` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`form_tab_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table start_cms.form_tabs: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_tabs` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_tabs` ENABLE KEYS */;

-- Dumping structure for table start_cms.mailfolder
DROP TABLE IF EXISTS `mailfolder`;
CREATE TABLE IF NOT EXISTS `mailfolder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namefolder` varchar(60) NOT NULL,
  `description` tinytext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namefolder` (`namefolder`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.mailfolder: ~7 rows (approximately)
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

-- Dumping structure for table start_cms.messages
DROP TABLE IF EXISTS `messages`;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.messages: ~0 rows (approximately)
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;

-- Dumping structure for table start_cms.messages_data
DROP TABLE IF EXISTS `messages_data`;
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

-- Dumping data for table start_cms.messages_data: ~0 rows (approximately)
/*!40000 ALTER TABLE `messages_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages_data` ENABLE KEYS */;

-- Dumping structure for table start_cms.notificacions
DROP TABLE IF EXISTS `notificacions`;
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

-- Dumping data for table start_cms.notificacions: ~0 rows (approximately)
/*!40000 ALTER TABLE `notificacions` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificacions` ENABLE KEYS */;

-- Dumping structure for table start_cms.page
DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(2048) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`page_id`),
  KEY `author` (`author`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.page: ~0 rows (approximately)
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
/*!40000 ALTER TABLE `page` ENABLE KEYS */;

-- Dumping structure for table start_cms.relations
DROP TABLE IF EXISTS `relations`;
CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tablename` tinytext NOT NULL,
  `id_row` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `action` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.relations: ~0 rows (approximately)
/*!40000 ALTER TABLE `relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `relations` ENABLE KEYS */;

-- Dumping structure for table start_cms.site_config
DROP TABLE IF EXISTS `site_config`;
CREATE TABLE IF NOT EXISTS `site_config` (
  `site_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `config_name` varchar(500) NOT NULL,
  `config_value` varchar(500) NOT NULL,
  `perm_values` varchar(500) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`site_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.site_config: ~1 rows (approximately)
/*!40000 ALTER TABLE `site_config` DISABLE KEYS */;
INSERT INTO `site_config` (`site_config_id`, `user_id`, `config_name`, `config_value`, `perm_values`, `status`, `date_create`, `date_update`) VALUES
	(1, 18, 'site_theme', 'myGreatTheme', '', 1, '2020-01-30 16:00:23', '2020-01-30 17:30:55');
/*!40000 ALTER TABLE `site_config` ENABLE KEYS */;

-- Dumping structure for table start_cms.suscriptions
DROP TABLE IF EXISTS `suscriptions`;
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

-- Dumping data for table start_cms.suscriptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `suscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `suscriptions` ENABLE KEYS */;

-- Dumping structure for table start_cms.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL DEFAULT '1234',
  `email` varchar(255) NOT NULL,
  `lastseen` datetime NOT NULL,
  `usergroup_id` int(11) NOT NULL DEFAULT 3,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

-- Dumping data for table start_cms.user: ~2 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `lastseen`, `usergroup_id`, `status`, `date_create`, `date_update`) VALUES
	(18, 'gerber', '1245', 'gerber@gmail.com', '2016-09-03 03:22:31', 2, 1, '2020-03-01 16:11:25', '2020-03-01 16:11:25'),
	(19, 'yduran', '1234', 'yduran@gmail.com', '2017-03-05 17:12:06', 3, 1, '2020-03-01 16:11:25', '2020-03-01 16:11:25');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping structure for table start_cms.userdatapermisions
DROP TABLE IF EXISTS `userdatapermisions`;
CREATE TABLE IF NOT EXISTS `userdatapermisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_id` int(11) NOT NULL,
  `permision` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.userdatapermisions: ~0 rows (approximately)
/*!40000 ALTER TABLE `userdatapermisions` DISABLE KEYS */;
/*!40000 ALTER TABLE `userdatapermisions` ENABLE KEYS */;

-- Dumping structure for table start_cms.usergroup
DROP TABLE IF EXISTS `usergroup`;
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

-- Dumping data for table start_cms.usergroup: ~5 rows (approximately)
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` (`usergroup_id`, `name`, `level`, `description`, `status`, `date_create`, `date_update`) VALUES
	(1, 'root', 1, 'All permisions allowed', 1, '2016-08-27 09:22:22', '2020-03-01 16:10:01'),
	(2, 'Administrador', 2, 'All configurations allowed', 1, '2016-08-27 09:22:22', '2020-03-01 16:10:01'),
	(3, 'Estandar', 3, 'Not delete permisions allowed', 1, '2016-08-27 08:32:49', '2020-03-01 16:10:01'),
	(7, 'Publisher', 4, 'Only Insert and Update permisions allowed', 1, '2016-08-28 07:35:50', '2020-03-01 16:10:01'),
	(8, 'Editor', 5, 'Only insert permisions allowed', 1, '2016-08-29 03:21:39', '2020-03-01 16:10:01');
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;

-- Dumping structure for table start_cms.user_data
DROP TABLE IF EXISTS `user_data`;
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
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.user_data: ~11 rows (approximately)
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` (`user_data_id`, `user_id`, `_key`, `_value`, `status`, `date_create`, `date_update`) VALUES
	(43, 18, 'nombre', 'Gervis', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(44, 18, 'apellido', 'Mora', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(45, 18, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(46, 18, 'telefono', '0414-1672173', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(47, 18, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(84, 18, 'avatar', 'gerber.jpg', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(85, 19, 'nombre', 'Yule', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(86, 19, 'apellido', 'Duran', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(87, 19, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(88, 19, 'telefono', '0412-9873920', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46'),
	(89, 19, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-03-01 16:31:46');
/*!40000 ALTER TABLE `user_data` ENABLE KEYS */;

-- Dumping structure for table start_cms.video
DROP TABLE IF EXISTS `video`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.video: ~0 rows (approximately)
/*!40000 ALTER TABLE `video` DISABLE KEYS */;
/*!40000 ALTER TABLE `video` ENABLE KEYS */;

-- Dumping structure for table start_cms.video-categoria
DROP TABLE IF EXISTS `video-categoria`;
CREATE TABLE IF NOT EXISTS `video-categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_video` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.video-categoria: ~0 rows (approximately)
/*!40000 ALTER TABLE `video-categoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `video-categoria` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
