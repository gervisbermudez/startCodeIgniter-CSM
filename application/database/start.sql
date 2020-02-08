-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.34-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             10.3.0.5771
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(75) NOT NULL,
  `descripcion` tinytext NOT NULL,
  `fecha` datetime NOT NULL,
  `path` varchar(125) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.album: ~0 rows (approximately)
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
/*!40000 ALTER TABLE `album` ENABLE KEYS */;

-- Dumping structure for table start_cms.album_items
DROP TABLE IF EXISTS `album_items`;
CREATE TABLE IF NOT EXISTS `album_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_album` int(11) NOT NULL,
  `nombre` varchar(125) NOT NULL,
  `titulo` varchar(75) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_album` (`id_album`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.album_items: ~0 rows (approximately)
/*!40000 ALTER TABLE `album_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `album_items` ENABLE KEYS */;

-- Dumping structure for table start_cms.categorias
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `tipo` varchar(600) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categorias del sistema';

-- Dumping data for table start_cms.categorias: ~0 rows (approximately)
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;

-- Dumping structure for table start_cms.contacts
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(600) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contacts';

-- Dumping data for table start_cms.contacts: ~0 rows (approximately)
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;

-- Dumping structure for table start_cms.contactsdata
DROP TABLE IF EXISTS `contactsdata`;
CREATE TABLE IF NOT EXISTS `contactsdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `_key` varchar(200) NOT NULL,
  `_value` varchar(600) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_contact` (`id_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contactsdata';

-- Dumping data for table start_cms.contactsdata: ~0 rows (approximately)
/*!40000 ALTER TABLE `contactsdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `contactsdata` ENABLE KEYS */;

-- Dumping structure for table start_cms.datauserstorage
DROP TABLE IF EXISTS `datauserstorage`;
CREATE TABLE IF NOT EXISTS `datauserstorage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `_key` varchar(100) NOT NULL,
  `_value` varchar(600) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.datauserstorage: ~18 rows (approximately)
/*!40000 ALTER TABLE `datauserstorage` DISABLE KEYS */;
INSERT INTO `datauserstorage` (`id`, `id_user`, `_key`, `_value`) VALUES
	(1, 0, 'nombre', 'Yule'),
	(43, 18, 'nombre', 'Gervis'),
	(44, 18, 'apellido', 'Mora'),
	(45, 18, 'direccion', 'Mara'),
	(46, 18, 'telefono', '0414-1672173'),
	(47, 18, 'create by', 'gerber'),
	(64, 27, 'nombre', 'Miguel'),
	(65, 27, 'apellido', 'Urdaneta'),
	(66, 27, 'direccion', 'Los Puertos'),
	(67, 27, 'telefono', '0414-1672173'),
	(68, 27, 'create by', 'gerber'),
	(81, 27, 'avatar', 'murdanetas.jpg'),
	(84, 18, 'avatar', 'gerber.jpg'),
	(85, 19, 'nombre', 'Yule'),
	(86, 19, 'apellido', 'Duran'),
	(87, 19, 'direccion', 'Mara'),
	(88, 19, 'telefono', '0412-9873920'),
	(89, 19, 'create by', 'gerber');
/*!40000 ALTER TABLE `datauserstorage` ENABLE KEYS */;

-- Dumping structure for table start_cms.eventos
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(70) NOT NULL,
  `titulo` varchar(70) NOT NULL,
  `texto` mediumtext NOT NULL,
  `imagen` text NOT NULL,
  `thumb` text NOT NULL,
  `ciudad` tinytext NOT NULL,
  `fecha` varchar(70) NOT NULL,
  `lugar` varchar(70) NOT NULL,
  `publishdate` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Eventos a publicar';

-- Dumping data for table start_cms.eventos: ~0 rows (approximately)
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_custom
DROP TABLE IF EXISTS `form_custom`;
CREATE TABLE IF NOT EXISTS `form_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_user` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
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
  `field_placeholder` varchar(250) DEFAULT NULL,
  `field_api_id` varchar(250) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`field_id`),
  KEY `form_tab_id` (`form_tab_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.form_fields: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_field_config
DROP TABLE IF EXISTS `form_field_config`;
CREATE TABLE IF NOT EXISTS `form_field_config` (
  `form_tab_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT NULL,
  `tab_name` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT NULL,
  `date_update` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`form_tab_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dumping data for table start_cms.form_field_config: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_field_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_field_config` ENABLE KEYS */;

-- Dumping structure for table start_cms.form_tab
DROP TABLE IF EXISTS `form_tab`;
CREATE TABLE IF NOT EXISTS `form_tab` (
  `field_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_field_id` int(11) DEFAULT NULL,
  `config_name` varchar(200) DEFAULT NULL,
  `config_value` varchar(200) DEFAULT NULL,
  `date_create` timestamp NULL DEFAULT NULL,
  `date_update` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`field_config_id`),
  KEY `form_id` (`form_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.form_tab: ~0 rows (approximately)
/*!40000 ALTER TABLE `form_tab` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_tab` ENABLE KEYS */;

-- Dumping structure for table start_cms.mailfolder
DROP TABLE IF EXISTS `mailfolder`;
CREATE TABLE IF NOT EXISTS `mailfolder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namefolder` varchar(60) NOT NULL,
  `description` tinytext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
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

-- Dumping structure for table start_cms.mensajes
DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `_from` text NOT NULL,
  `_to` text NOT NULL,
  `_subject` varchar(255) NOT NULL,
  `_mensaje` longtext NOT NULL,
  `_cc` text NOT NULL,
  `_bcc` text NOT NULL,
  `fecha` datetime NOT NULL,
  `folder` int(11) NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `folder` (`folder`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.mensajes: ~3 rows (approximately)
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
INSERT INTO `mensajes` (`id`, `_from`, `_to`, `_subject`, `_mensaje`, `_cc`, `_bcc`, `fecha`, `folder`, `estatus`) VALUES
	(1, 'ana24@mail.org', 'admin@mail.org', 'Lorem ipsum dolor sit amet', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\n			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\n			consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\n			cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\n			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '', '2016-09-08 00:00:00', 2, 1),
	(4, 'juan45@mail.org', 'Lore@gmail.org', 'Lorem ipsum dolor ', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '', '2016-09-06 06:29:44', 2, 1),
	(5, 'migul@mail.org', 'Lore@gmail.org', 'Lorem ipsum dolor ', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '', '2016-09-06 06:29:44', 2, 1);
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;

-- Dumping structure for table start_cms.mensajesdata
DROP TABLE IF EXISTS `mensajesdata`;
CREATE TABLE IF NOT EXISTS `mensajesdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) NOT NULL,
  `_key` varchar(200) COLLATE utf8_bin NOT NULL,
  `_value` varchar(600) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mensaje` (`id_mensaje`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='mensajesdata';

-- Dumping data for table start_cms.mensajesdata: ~0 rows (approximately)
/*!40000 ALTER TABLE `mensajesdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajesdata` ENABLE KEYS */;

-- Dumping structure for table start_cms.notificaciones
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.notificaciones: ~0 rows (approximately)
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;

-- Dumping structure for table start_cms.page
DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(2048) DEFAULT NULL,
  `title` text,
  `content` longtext,
  `author` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.page: ~1 rows (approximately)
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
/*!40000 ALTER TABLE `page` ENABLE KEYS */;

-- Dumping structure for table start_cms.relations
DROP TABLE IF EXISTS `relations`;
CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `tablename` tinytext NOT NULL,
  `id_row` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `action` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.relations: ~0 rows (approximately)
/*!40000 ALTER TABLE `relations` DISABLE KEYS */;
INSERT INTO `relations` (`id`, `id_user`, `tablename`, `id_row`, `date`, `action`) VALUES
	(11, 18, 'video', 2, '2017-03-06 00:09:51', 'crear');
/*!40000 ALTER TABLE `relations` ENABLE KEYS */;

-- Dumping structure for table start_cms.suscriptores
DROP TABLE IF EXISTS `suscriptores`;
CREATE TABLE IF NOT EXISTS `suscriptores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(75) NOT NULL,
  `email` varchar(125) NOT NULL,
  `fecha` datetime NOT NULL,
  `codigo` varchar(75) NOT NULL,
  `estado` set('Verificado','No verificado') NOT NULL DEFAULT 'No verificado',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.suscriptores: ~0 rows (approximately)
/*!40000 ALTER TABLE `suscriptores` DISABLE KEYS */;
/*!40000 ALTER TABLE `suscriptores` ENABLE KEYS */;

-- Dumping structure for table start_cms.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL DEFAULT '1234',
  `email` varchar(255) NOT NULL,
  `lastseen` datetime NOT NULL,
  `usergroup` int(11) NOT NULL DEFAULT '3',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

-- Dumping data for table start_cms.user: ~2 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `password`, `email`, `lastseen`, `usergroup`, `status`) VALUES
	(18, 'gerber', '1245', 'gerber@gmail.com', '2016-09-03 03:22:31', 2, 1),
	(19, 'yduran', '1234', 'yduran@gmail.com', '2017-03-05 17:12:06', 3, 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping structure for table start_cms.userdatapermisions
DROP TABLE IF EXISTS `userdatapermisions`;
CREATE TABLE IF NOT EXISTS `userdatapermisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usergroup` int(11) NOT NULL,
  `permision` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.userdatapermisions: ~0 rows (approximately)
/*!40000 ALTER TABLE `userdatapermisions` DISABLE KEYS */;
/*!40000 ALTER TABLE `userdatapermisions` ENABLE KEYS */;

-- Dumping structure for table start_cms.usergroup
DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE IF NOT EXISTS `usergroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `level` int(11) NOT NULL,
  `description` tinytext NOT NULL,
  `createdate` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table start_cms.usergroup: ~5 rows (approximately)
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` (`id`, `name`, `level`, `description`, `createdate`, `status`) VALUES
	(1, 'root', 1, 'All permisions allowed', '2016-08-27 09:22:22', 1),
	(2, 'Administrador', 2, 'All configurations allowed', '2016-08-27 09:22:22', 1),
	(3, 'Estandar', 3, 'Not delete permisions allowed', '2016-08-27 08:32:49', 1),
	(7, 'Publisher', 4, 'Only Insert and Update permisions allowed', '2016-08-28 07:35:50', 1),
	(8, 'Editor', 5, 'Only insert permisions allowed', '2016-08-29 03:21:39', 1);
/*!40000 ALTER TABLE `usergroup` ENABLE KEYS */;

-- Dumping structure for table start_cms.video
DROP TABLE IF EXISTS `video`;
CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `duration` varchar(50) NOT NULL,
  `youtubeid` varchar(6000) NOT NULL,
  `preview` varchar(2000) NOT NULL,
  `payinfo` text NOT NULL,
  `fecha` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table start_cms.video-categoria: ~0 rows (approximately)
/*!40000 ALTER TABLE `video-categoria` DISABLE KEYS */;
INSERT INTO `video-categoria` (`id`, `id_video`, `id_categoria`) VALUES
	(1, 0, 2);
/*!40000 ALTER TABLE `video-categoria` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
