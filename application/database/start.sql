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

-- Volcando estructura para tabla start_cms.album
CREATE TABLE IF NOT EXISTS `album` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `date_publish` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.album: ~0 rows (aproximadamente)
DELETE FROM `album`;
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
INSERT INTO `album` (`album_id`, `name`, `description`, `date_publish`, `user_id`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(1, 'Fotografias del Concierto de Queen', 'Queen are a British rock band formed in London in 1970. Their classic line-up was Freddie Mercury, Brian May, Roger Taylor and John Deacon.', '2020-10-04 19:01:17', 1, 1, '2020-10-12 14:50:32', '2020-10-08 16:11:35', NULL),
	(9, 'Fotografias del Concierto de Queen', 'Queen are a British rock band formed in London in 1970. Their classic line-up was Freddie Mercury, Brian May, Roger Taylor and John Deacon.', '2020-10-04 19:01:17', 1, 0, '2020-10-12 14:50:32', '2020-11-18 12:23:28', '2020-11-18 16:26:18');
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
  KEY `file_id` (`file_id`),
  CONSTRAINT `FK_album_items_album` FOREIGN KEY (`album_id`) REFERENCES `album` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.album_items: ~7 rows (aproximadamente)
DELETE FROM `album_items`;
/*!40000 ALTER TABLE `album_items` DISABLE KEYS */;
INSERT INTO `album_items` (`album_item_id`, `album_id`, `file_id`, `name`, `description`, `status`, `date_create`, `date_update`) VALUES
	(46, 1, 680, '', '', 1, '2020-10-12 14:44:38', '2020-10-12 09:44:38'),
	(47, 1, 682, '', '', 1, '2020-10-12 14:50:32', '2020-10-12 09:44:38'),
	(48, 1, 683, '', '', 1, '2020-10-12 14:50:32', '2020-10-12 09:44:38'),
	(49, 1, 684, '', '', 1, '2020-10-12 14:50:32', '2020-10-12 09:44:38'),
	(50, 1, 686, '', '', 1, '2020-10-12 14:50:32', '2020-10-12 09:44:38'),
	(51, 1, 680, '', '', 1, '2020-10-12 14:50:32', '2020-10-12 09:50:32'),
	(52, 1, 681, '', '', 1, '2020-10-12 14:50:32', '2020-10-12 09:50:32');
/*!40000 ALTER TABLE `album_items` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `categorie_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(600) NOT NULL,
  `parent_id` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COMMENT='Categorias del sistema';

-- Volcando datos para la tabla start_cms.categories: ~10 rows (aproximadamente)
DELETE FROM `categories`;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`categorie_id`, `user_id`, `name`, `description`, `type`, `parent_id`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(4, 1, 'Web Design', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 0, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:45', NULL),
	(5, 1, 'Tutorials', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 0, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:46', NULL),
	(6, 1, 'Programing languages', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 0, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:47', NULL),
	(7, 1, 'Sub Categoria 1', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 4, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:49', NULL),
	(8, 2, 'Sub Categoria 2', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 4, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:50', NULL),
	(9, 1, 'Sub Categoria 3', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 4, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:51', NULL),
	(10, 1, 'Sub Categoria 1', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 5, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:52', NULL),
	(11, 2, 'Sub Categoria 2', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 5, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:53', NULL),
	(12, 2, 'Sub Categoria 3', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!', 'page', 5, 1, '2020-04-19 10:36:14', '2020-09-01 08:57:54', NULL),
	(16, 1, 'WebAPIs', '<p style="margin: 0px 0px 24px; padding: 0px; border: 0px; box-sizing: border-box; max-width: 42rem; color: #333333; font-family: Arial, x-locale-body, sans-serif; font-size: 16px; letter-spacing: -0.04448px;"><span class="seoSummary" style="margin: 0px; padding: 0px; border: 0px;">When writing code for the Web, there are a large number of Web APIs available. Below is a list of all the APIs and interfaces (object types) that you may be able to use while developing your Web app or site.</span></p>\n<p style="margin: 0px 0px 24px; padding: 0px; border: 0px; box-sizing: border-box; max-width: 42rem; color: #333333; font-family: Arial, x-locale-body, sans-serif; font-size: 16px; letter-spacing: -0.04448px;">Web APIs are typically used with JavaScript, although this doesn\'t always have to be the case.</p>', 'page', 0, 0, '2020-09-04 00:05:27', '2020-11-18 09:25:06', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=1290 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.file: ~600 rows (aproximadamente)
DELETE FROM `file`;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
INSERT INTO `file` (`file_id`, `rand_key`, `file_name`, `file_path`, `file_type`, `parent_name`, `user_id`, `shared_user_group_id`, `share_link`, `featured`, `date_create`, `date_update`, `status`) VALUES
	(1, '9bjUypTC1FPQuow8', '1tnvrt0m6nob3yq8jkfp', './public/img/', 'jpg', './', 1, 1, 'admin/archivos/shared_file/9bjUypTC1FPQuow8', 1, '2020-10-07 20:09:47', '2020-10-07 20:34:01', 1),
	(2, 'QFwf5KOXz98ZHDmG', '43900439-368fa600-9be5-11e8-8f9c-d209784de1ef', './public/img/', 'jpg', './', 1, 1, 'admin/archivos/shared_file/QFwf5KOXz98ZHDmG', 0, '2020-10-07 20:09:47', '2020-10-07 20:38:49', 1),
	(599, 'ms4YnrqwV8yIStJF', 'apidoc', './', 'json', './', 1, 1, 'admin/archivos/shared_file/ms4YnrqwV8yIStJF', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(600, 'OSA3D4NBhmryvV2d', 'blog3', './public/img/', 'jpg', './', 1, 1, 'admin/archivos/shared_file/OSA3D4NBhmryvV2d', 1, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(601, 'ZYUjQXInkDiT3hug', 'composer', './', 'json', './', 1, 1, 'admin/archivos/shared_file/ZYUjQXInkDiT3hug', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(602, 'CRIyV12Ef5dHqalX', 'devnotes', './', 'txt', './', 1, 1, 'admin/archivos/shared_file/CRIyV12Ef5dHqalX', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(603, 'WL1kJt2hAjsNc3SH', 'gulpfile', './', 'js', './', 1, 1, 'admin/archivos/shared_file/WL1kJt2hAjsNc3SH', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(604, 'NTEpOs3eLRm5gqAz', 'index', './', 'php', './', 1, 1, 'admin/archivos/shared_file/NTEpOs3eLRm5gqAz', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(605, 'dnPivuoZCjQpKl0k', 'LICENSE', './', 'txt', './', 1, 1, 'admin/archivos/shared_file/dnPivuoZCjQpKl0k', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(606, 'AgOM3WFrUlyJ7ocu', 'package-lock', './', 'json', './', 1, 1, 'admin/archivos/shared_file/AgOM3WFrUlyJ7ocu', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(607, 'omPD3beVCdWNh8uv', 'package', './', 'json', './', 1, 1, 'admin/archivos/shared_file/omPD3beVCdWNh8uv', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(608, '1kdy9t3UABXDvzOK', 'public', './', 'folder', './', 1, 1, 'admin/archivos/shared_file/1kdy9t3UABXDvzOK', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(609, 'JgX8976YUR34lGwD', 'bootstrap', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/JgX8976YUR34lGwD', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(610, 'XyrdD6GKnRJSI7NM', 'css', './public/bootstrap/', 'folder', 'bootstrap', 1, 1, 'admin/archivos/shared_file/XyrdD6GKnRJSI7NM', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(611, 'GXHzet5DbQp2ZfFW', 'bootstrap-grid', './public/bootstrap/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/GXHzet5DbQp2ZfFW', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(612, '38LhFlqrteKzWuJ5', 'bootstrap-reboot', './public/bootstrap/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/38LhFlqrteKzWuJ5', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(613, 'w2TjsYx7IfHbpGq9', 'bootstrap', './public/bootstrap/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/w2TjsYx7IfHbpGq9', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(614, 'EiDTr7LmtglaFbZO', 'js', './public/bootstrap/', 'folder', 'bootstrap', 1, 1, 'admin/archivos/shared_file/EiDTr7LmtglaFbZO', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(615, 'AofKDrhxgEjJdcGO', 'bootstrap', './public/bootstrap/js/', 'bundle.js', 'js', 1, 1, 'admin/archivos/shared_file/AofKDrhxgEjJdcGO', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(616, 'r2uYbeHjE5SsPF3B', 'css', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/r2uYbeHjE5SsPF3B', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(617, 'UuYXzlFImSki48DG', 'admin', './public/css/', 'folder', 'css', 1, 1, 'admin/archivos/shared_file/UuYXzlFImSki48DG', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(618, 'X6qtJn7o45g9VFLN', 'dashboard', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/X6qtJn7o45g9VFLN', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(619, 'V5mG3fY9yaEqDT80', 'file_explorer', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/V5mG3fY9yaEqDT80', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(620, 'qbIavydfkGDhK1FT', 'fonts', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/qbIavydfkGDhK1FT', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(621, 'HY60MgTqlQRSztue', 'form', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/HY60MgTqlQRSztue', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(622, '9kuGDZs6od4BirRQ', 'gallery', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/9kuGDZs6od4BirRQ', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(623, 'wIohE5OANiDxyatz', 'login', './public/css/admin/', 'css', 'admin', 1, 1, 'admin/archivos/shared_file/wIohE5OANiDxyatz', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(624, '2k5tXTl93rh6dgPw', 'materialize', './public/css/admin/', 'css', 'admin', 1, 1, 'admin/archivos/shared_file/2k5tXTl93rh6dgPw', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(625, 'YIfsKaReUOGELZ2o', 'message', './public/css/admin/', 'css', 'admin', 1, 1, 'admin/archivos/shared_file/YIfsKaReUOGELZ2o', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(626, '0v92CeZOqSKTbaEk', 'mixins', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/0v92CeZOqSKTbaEk', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(627, 'UYvAQ7jy3tJ5dXgx', 'skeleton-loader', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/UYvAQ7jy3tJ5dXgx', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(628, 'f7uDer8AwzsWpqFI', 'start', './public/css/admin/', 'css', 'admin', 1, 1, 'admin/archivos/shared_file/f7uDer8AwzsWpqFI', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(629, 'j47qSKr2aYe5bMIn', 'userprofile', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/j47qSKr2aYe5bMIn', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(630, 'NAZvPknhQpKigHGD', 'materialize', './public/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/NAZvPknhQpKigHGD', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(631, 'TcQxRvZpgE3hGA65', 'message', './public/css/', 'min.css', 'css', 1, 1, 'admin/archivos/shared_file/TcQxRvZpgE3hGA65', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(632, 'dBKfTk2gqtMmYe0s', 'modern-business', './public/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/dBKfTk2gqtMmYe0s', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(633, 'XtP09Awa5WBoiFqL', 'font', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/XtP09Awa5WBoiFqL', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(634, 'kxuCnwNshy4OzQ1l', 'coda', './public/font/', 'folder', 'font', 1, 1, 'admin/archivos/shared_file/kxuCnwNshy4OzQ1l', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(635, '1O4irjYwgDmEfI6s', 'Coda-Regular', './public/font/coda/', 'eot', 'coda', 1, 1, 'admin/archivos/shared_file/1O4irjYwgDmEfI6s', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(636, 'ginJMG6SxaDjI7VX', 'material-design-icons', './public/font/', 'folder', 'font', 1, 1, 'admin/archivos/shared_file/ginJMG6SxaDjI7VX', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(637, '5IBNd9KRV4YgPW7L', 'LICENSE', './public/font/material-design-icons/', 'txt', 'material-design-icons', 1, 1, 'admin/archivos/shared_file/5IBNd9KRV4YgPW7L', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(638, 'XP3mLw6tWbFK5Gic', 'Material-Design-Icons', './public/font/material-design-icons/', 'eot', 'material-design-icons', 1, 1, 'admin/archivos/shared_file/XP3mLw6tWbFK5Gic', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(639, 'Q5bvFyexR1kTjfs9', 'roboto', './public/font/', 'folder', 'font', 1, 1, 'admin/archivos/shared_file/Q5bvFyexR1kTjfs9', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(640, 'ORj2DpbUaKGhvSWN', 'Roboto-Bold', './public/font/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/ORj2DpbUaKGhvSWN', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(641, 'eWrMl3EJ8Ri4jbNK', 'Roboto-Light', './public/font/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/eWrMl3EJ8Ri4jbNK', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(642, 'VCQIrayFXk5t4iU9', 'Roboto-Medium', './public/font/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/VCQIrayFXk5t4iU9', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(643, 'upd71ibtIFBRW6cs', 'Roboto-Regular', './public/font/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/upd71ibtIFBRW6cs', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(644, 'eHMRg6YjbVyo9vT0', 'Roboto-Thin', './public/font/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/eHMRg6YjbVyo9vT0', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(645, 'aJmeAKQ6iLUX9RTO', 'font-awesome', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/aJmeAKQ6iLUX9RTO', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(646, 'U05sfrmivS1oWuD8', 'css', './public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/U05sfrmivS1oWuD8', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(647, '8R6fXioq0WGYAEzC', 'all', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/8R6fXioq0WGYAEzC', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(648, 'seNPJpLIdEDXG6Vo', 'brands', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/seNPJpLIdEDXG6Vo', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(649, 'HbJCNFr6AVMRSEDw', 'font-awesome', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/HbJCNFr6AVMRSEDw', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(650, 'iu8BqYVm3dkncPxb', 'fontawesome', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/iu8BqYVm3dkncPxb', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(651, 'Aq5y63kFo9r0KELI', 'regular', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/Aq5y63kFo9r0KELI', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(652, 'StpIHF6zUNTymach', 'solid', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/StpIHF6zUNTymach', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(653, 'ph6RUl52J4PSTEa7', 'svg-with-js', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/ph6RUl52J4PSTEa7', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(654, 'OkPUyR65QVr4aDW2', 'v4-shims', './public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/OkPUyR65QVr4aDW2', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(655, 'RmjNxTtGFaSY5MOy', 'fonts', './public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/RmjNxTtGFaSY5MOy', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(656, 'FU2fDRWaO9GXdiEV', 'fontawesome-webfont', './public/font-awesome/fonts/', 'eot', 'fonts', 1, 1, 'admin/archivos/shared_file/FU2fDRWaO9GXdiEV', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(657, 'x1anFdZGtmEcfqow', 'FontAwesome', './public/font-awesome/fonts/', 'otf', 'fonts', 1, 1, 'admin/archivos/shared_file/x1anFdZGtmEcfqow', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(658, 'asZChLe3PH9ToYyU', 'LICENSE', './public/font-awesome/', 'txt', 'font-awesome', 1, 1, 'admin/archivos/shared_file/asZChLe3PH9ToYyU', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(659, 'sME1WF9w6QYPqAB2', 'metadata', './public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/sME1WF9w6QYPqAB2', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(660, 'DkUyTN3HOjAgVbvF', 'categories', './public/font-awesome/metadata/', 'yml', 'metadata', 1, 1, 'admin/archivos/shared_file/DkUyTN3HOjAgVbvF', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(661, 'oRZULiFc9OjwgTBX', 'icons', './public/font-awesome/metadata/', 'json', 'metadata', 1, 1, 'admin/archivos/shared_file/oRZULiFc9OjwgTBX', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(662, 'ux1U5JwCbaGrTAZq', 'shims', './public/font-awesome/metadata/', 'json', 'metadata', 1, 1, 'admin/archivos/shared_file/ux1U5JwCbaGrTAZq', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(663, '0SVfXDklJqLHOQ9u', 'sponsors', './public/font-awesome/metadata/', 'yml', 'metadata', 1, 1, 'admin/archivos/shared_file/0SVfXDklJqLHOQ9u', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(664, 'UCf07YWmxgFZDcO8', 'webfonts', './public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/UCf07YWmxgFZDcO8', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(665, 'q0D8ANZQm1ChTrj2', 'fa-brands-400', './public/font-awesome/webfonts/', 'eot', 'webfonts', 1, 1, 'admin/archivos/shared_file/q0D8ANZQm1ChTrj2', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(666, 'R6PJhsCFZqTvDpUw', 'fa-regular-400', './public/font-awesome/webfonts/', 'eot', 'webfonts', 1, 1, 'admin/archivos/shared_file/R6PJhsCFZqTvDpUw', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(667, '5xDiPY2rp4IbalR9', 'fa-solid-900', './public/font-awesome/webfonts/', 'eot', 'webfonts', 1, 1, 'admin/archivos/shared_file/5xDiPY2rp4IbalR9', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(668, 'gr3TNxqi2uZGz5hI', 'fonts', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/gr3TNxqi2uZGz5hI', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(669, 'RCvbf1Esq7YahpZG', 'glyphicons-halflings-regular', './public/fonts/', 'eot', 'fonts', 1, 1, 'admin/archivos/shared_file/RCvbf1Esq7YahpZG', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(670, 'jIBUNiRea7oruwOA', 'material-icons-', './public/fonts/', 'woff2', 'fonts', 1, 1, 'admin/archivos/shared_file/jIBUNiRea7oruwOA', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(671, 'sWXpxS5YUfBjhgEH', 'material-icons', './public/fonts/', 'woff2', 'fonts', 1, 1, 'admin/archivos/shared_file/sWXpxS5YUfBjhgEH', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(672, 'tzs7uFBw8RmxOJhL', 'roboto', './public/fonts/', 'folder', 'fonts', 1, 1, 'admin/archivos/shared_file/tzs7uFBw8RmxOJhL', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(673, 'X9Qft0o5gae8NLOR', 'Roboto-Bold', './public/fonts/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/X9Qft0o5gae8NLOR', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(674, '5OMce8FZ0zdS64IR', 'Roboto-Light', './public/fonts/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/5OMce8FZ0zdS64IR', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(675, 'chOQPN5Y1dzkrTJW', 'Roboto-Medium', './public/fonts/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/chOQPN5Y1dzkrTJW', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(676, 'Apv4mJKMD9tCwP2c', 'Roboto-Regular', './public/fonts/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/Apv4mJKMD9tCwP2c', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(677, 'yz5garkDNBtW1bCG', 'Roboto-Thin', './public/fonts/roboto/', 'eot', 'roboto', 1, 1, 'admin/archivos/shared_file/yz5garkDNBtW1bCG', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(678, 'yfmwgVqzQNMvdkxR', 'img', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/yfmwgVqzQNMvdkxR', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(679, 'radlKORvHt0kZpEy', 'about', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/radlKORvHt0kZpEy', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(680, 'vN2she9cp3D0ZFSA', 'about', './public/img/about/', 'jpg', 'about', 1, 1, 'admin/archivos/shared_file/vN2she9cp3D0ZFSA', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(681, 'KinCfPbMdYG1gjQp', 'about2', './public/img/about/', 'jpg', 'about', 1, 1, 'admin/archivos/shared_file/KinCfPbMdYG1gjQp', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(682, 'AsqKinSvu5PUO1VT', 'about3', './public/img/about/', 'jpg', 'about', 1, 1, 'admin/archivos/shared_file/AsqKinSvu5PUO1VT', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(683, 'acziJSqnTsKIxd9X', 'about4', './public/img/about/', 'jpg', 'about', 1, 1, 'admin/archivos/shared_file/acziJSqnTsKIxd9X', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(684, 'TCV2rhzS6Jv9ZLQ5', 'about5', './public/img/about/', 'jpg', 'about', 1, 1, 'admin/archivos/shared_file/TCV2rhzS6Jv9ZLQ5', 0, '2020-10-07 20:09:47', '2020-10-07 20:09:47', 1),
	(685, 'FSdJ3PRBtV7Os0XN', 'customers', './public/img/about/', 'folder', 'about', 1, 1, 'admin/archivos/shared_file/FSdJ3PRBtV7Os0XN', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(686, 'zhy2UMOGlD79Lwxj', 'customers-2', './public/img/about/customers/', 'jpg', 'customers', 1, 1, 'admin/archivos/shared_file/zhy2UMOGlD79Lwxj', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(687, 'bVajo0OvUrBpAiml', 'customers-4', './public/img/about/customers/', 'jpg', 'customers', 1, 1, 'admin/archivos/shared_file/bVajo0OvUrBpAiml', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(688, 'rjgt81pmBkV9CMS3', 'customers-5', './public/img/about/customers/', 'jpg', 'customers', 1, 1, 'admin/archivos/shared_file/rjgt81pmBkV9CMS3', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(689, 'uCFGVEvoMcaxnt96', 'customers-6', './public/img/about/customers/', 'jpg', 'customers', 1, 1, 'admin/archivos/shared_file/uCFGVEvoMcaxnt96', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(690, 'IxKTEZXMpe2sAyb4', 'admin', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/IxKTEZXMpe2sAyb4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(691, 'czBDiAfP7jmJK9QM', 'favicon', './public/img/admin/', 'folder', 'admin', 1, 1, 'admin/archivos/shared_file/czBDiAfP7jmJK9QM', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(692, 'Of0orVsCFBES5XI8', 'android-icon-144x144', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/Of0orVsCFBES5XI8', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(693, 'QNM69Gy28dFkIg4C', 'android-icon-192x192', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/QNM69Gy28dFkIg4C', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(694, 'uZ9irtzEwNXa8HMn', 'android-icon-36x36', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/uZ9irtzEwNXa8HMn', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(695, 'fy9FdqpMD27HjbEn', 'android-icon-48x48', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/fy9FdqpMD27HjbEn', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(696, 'MBlinHCDZ794qrK5', 'android-icon-72x72', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/MBlinHCDZ794qrK5', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(697, 'QycYTvNABS0bpMJf', 'android-icon-96x96', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/QycYTvNABS0bpMJf', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(698, 'bpreQRVCN0kqxGUn', 'apple-icon-114x114', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/bpreQRVCN0kqxGUn', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(699, 'yKhrjT2XimMN4o3E', 'apple-icon-120', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/yKhrjT2XimMN4o3E', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(700, 'gyq9Zp0btnOzPIlJ', 'apple-icon-120x120', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/gyq9Zp0btnOzPIlJ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(701, 'Ei3YeObaIJfhcs7Q', 'apple-icon-144x144', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/Ei3YeObaIJfhcs7Q', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(702, 'yo4PEc0FLasrQORI', 'apple-icon-152', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/yo4PEc0FLasrQORI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(703, 'rT6KqY891MIARltu', 'apple-icon-152x152', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/rT6KqY891MIARltu', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(704, 'RO659PT3JmraQfzq', 'apple-icon-167', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/RO659PT3JmraQfzq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(705, '5VLsOJqgbAfujyHQ', 'apple-icon-180', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/5VLsOJqgbAfujyHQ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(706, 'k5fZ2Frgay84R3lu', 'apple-icon-180x180', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/k5fZ2Frgay84R3lu', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(707, '8jnSgBzaIJryeiGW', 'apple-icon-57x57', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/8jnSgBzaIJryeiGW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(708, '7V4tcJhXmTolPYyA', 'apple-icon-60x60', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/7V4tcJhXmTolPYyA', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(709, 'Y4u9D1K3T285QEIG', 'apple-icon-72x72', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/Y4u9D1K3T285QEIG', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(710, 'dDaAWib1BrRU9Gtx', 'apple-icon-76x76', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/dDaAWib1BrRU9Gtx', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(711, 'f9MIKRtB1enLQ3cw', 'apple-icon-precomposed', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/f9MIKRtB1enLQ3cw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(712, 'PI6q2gw3A0npFQjs', 'apple-icon', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/PI6q2gw3A0npFQjs', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(713, '5bQmsPqdL6Y3CRXn', 'apple-splash-1080-1920', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/5bQmsPqdL6Y3CRXn', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(714, 'toOlxpuyXci7aQvW', 'apple-splash-1125-2436', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/toOlxpuyXci7aQvW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(715, 'G9c4aeXoW3utsh2I', 'apple-splash-1136-640', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/G9c4aeXoW3utsh2I', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(716, 'GRBAiyxk0dsKvtJ8', 'apple-splash-1242-2688', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/GRBAiyxk0dsKvtJ8', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(717, 'refyam2KWLVbq5D0', 'apple-splash-1334-750', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/refyam2KWLVbq5D0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(718, 'GDuO4PIdbfZzFhYE', 'apple-splash-1536-2048', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/GDuO4PIdbfZzFhYE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(719, 'xarWfo6Udy0YlHBg', 'apple-splash-1620-2160', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/xarWfo6Udy0YlHBg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(720, 'PSdwnBbRJ6Tk3ZWD', 'apple-splash-1668-2224', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/PSdwnBbRJ6Tk3ZWD', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(721, 'RFldvna8ZUDEe1Wz', 'apple-splash-1668-2388', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/RFldvna8ZUDEe1Wz', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(722, 'S2d7l5ktrcTsqwN9', 'apple-splash-1792-828', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/S2d7l5ktrcTsqwN9', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(723, 'TBxcn7wHWiYaONjP', 'apple-splash-1920-1080', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/TBxcn7wHWiYaONjP', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(724, 'ND9S4clQ20wFGvAP', 'apple-splash-2048-1536', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/ND9S4clQ20wFGvAP', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(725, '029GnJtAyY4r87ZL', 'apple-splash-2048-2732', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/029GnJtAyY4r87ZL', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(726, 'wrInbFqgty4sBG0o', 'apple-splash-2160-1620', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/wrInbFqgty4sBG0o', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(727, 'kSZ9FiXNwPKYlTDg', 'apple-splash-2224-1668', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/kSZ9FiXNwPKYlTDg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(728, '9xpCziV3PUnametB', 'apple-splash-2388-1668', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/9xpCziV3PUnametB', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(729, 'hJaq3McU5ljdzYAS', 'apple-splash-2436-1125', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/hJaq3McU5ljdzYAS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(730, '5sgcPKtFdl4LNrUG', 'apple-splash-2688-1242', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/5sgcPKtFdl4LNrUG', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(731, '4JuYDIF5vWgHfa7h', 'apple-splash-2732-2048', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/4JuYDIF5vWgHfa7h', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(732, 'nboUsklMWNG42avd', 'apple-splash-640-1136', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/nboUsklMWNG42avd', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(733, 'hy3xS0OaVFojdYrR', 'apple-splash-750-1334', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/hy3xS0OaVFojdYrR', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(734, 'MtwbRYHLAKGulixq', 'apple-splash-828-1792', './public/img/admin/favicon/', 'jpg', 'favicon', 1, 1, 'admin/archivos/shared_file/MtwbRYHLAKGulixq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(735, 'P9064DByFLRMr28H', 'browserconfig', './public/img/admin/favicon/', 'xml', 'favicon', 1, 1, 'admin/archivos/shared_file/P9064DByFLRMr28H', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(736, 'A9haJK4UtB1cj78P', 'favicon-16x16', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/A9haJK4UtB1cj78P', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(737, 'FPZWJhX5M6jGOmNn', 'favicon-32x32', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/FPZWJhX5M6jGOmNn', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(738, 'UntSm2NwTjkLguiD', 'favicon-96x96', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/UntSm2NwTjkLguiD', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(739, 'cZLp9D7nNlASHE1z', 'favicon', './public/img/admin/favicon/', 'ico', 'favicon', 1, 1, 'admin/archivos/shared_file/cZLp9D7nNlASHE1z', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(740, 'umTWQ1gkbhFwBpEc', 'manifest-icon-192', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/umTWQ1gkbhFwBpEc', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(741, 'ABqgznrvhotpEVG9', 'manifest-icon-512', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/ABqgznrvhotpEVG9', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(742, 'LZt6yD7IrcuaE1j5', 'manifest', './public/img/admin/favicon/', 'json', 'favicon', 1, 1, 'admin/archivos/shared_file/LZt6yD7IrcuaE1j5', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(743, 'kYo6OUawqdbuiICt', 'ms-icon-144x144', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/kYo6OUawqdbuiICt', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(744, '0Chp4FyWm5tHPnG9', 'ms-icon-150x150', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/0Chp4FyWm5tHPnG9', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(745, 'a28DM7jelsFwJ01q', 'ms-icon-310x310', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/a28DM7jelsFwJ01q', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(746, 'OXeIWpdTU7khoVm5', 'ms-icon-70x70', './public/img/admin/favicon/', 'png', 'favicon', 1, 1, 'admin/archivos/shared_file/OXeIWpdTU7khoVm5', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(747, 'W32mAxLnFVKyISEU', 'userwallpaper-7', './public/img/admin/', 'jpg', 'admin', 1, 1, 'admin/archivos/shared_file/W32mAxLnFVKyISEU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(748, '85mEINDrTlGzksyF', 'blog', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/85mEINDrTlGzksyF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(749, 'ix0wnvzDWZMCoVLq', 'blog-post', './public/img/blog/', 'folder', 'blog', 1, 1, 'admin/archivos/shared_file/ix0wnvzDWZMCoVLq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(750, 'E0liw6tWTSXbcjKp', 'blog-post', './public/img/blog/blog-post/', 'png', 'blog-post', 1, 1, 'admin/archivos/shared_file/E0liw6tWTSXbcjKp', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(751, 'hSgQJCdA3BkZE91T', 'blog-post2', './public/img/blog/blog-post/', 'png', 'blog-post', 1, 1, 'admin/archivos/shared_file/hSgQJCdA3BkZE91T', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(752, 'Mp6Brw9D15bPChSg', 'blog-post4', './public/img/blog/blog-post/', 'png', 'blog-post', 1, 1, 'admin/archivos/shared_file/Mp6Brw9D15bPChSg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(753, 'gJrmaO5kRS6fNVIK', 'blog-post5', './public/img/blog/blog-post/', 'png', 'blog-post', 1, 1, 'admin/archivos/shared_file/gJrmaO5kRS6fNVIK', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(754, '9JynOi0M4tmWKd82', 'blog-post6', './public/img/blog/blog-post/', 'png', 'blog-post', 1, 1, 'admin/archivos/shared_file/9JynOi0M4tmWKd82', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(755, '1wCOc39QJdZVbhN4', 'blog1', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/1wCOc39QJdZVbhN4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(756, 'rK6s0YjwO5SJctNW', 'brand', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/rK6s0YjwO5SJctNW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(757, 'JWPRfdmwTtUC2GyO', 'logo', './public/img/brand/', 'png', 'brand', 1, 1, 'admin/archivos/shared_file/JWPRfdmwTtUC2GyO', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(758, 'M2JHgvZoGDER3V4l', 'default', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/M2JHgvZoGDER3V4l', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(759, '2gFU0zwd4fG7mRZT', 'gallery', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/2gFU0zwd4fG7mRZT', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(760, 'DZe5J43N8xr7piST', 'gallery-2', './public/img/gallery/', 'png', 'gallery', 1, 1, 'admin/archivos/shared_file/DZe5J43N8xr7piST', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(761, 'PJEMp5WUbz8DQuef', 'gallery-4', './public/img/gallery/', 'png', 'gallery', 1, 1, 'admin/archivos/shared_file/PJEMp5WUbz8DQuef', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(762, 'OtDPCZzi3oWSvX6b', 'gallery-5', './public/img/gallery/', 'png', 'gallery', 1, 1, 'admin/archivos/shared_file/OtDPCZzi3oWSvX6b', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(763, 'YMKG6XzDgCvsnlwk', 'gallery-6', './public/img/gallery/', 'png', 'gallery', 1, 1, 'admin/archivos/shared_file/YMKG6XzDgCvsnlwk', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(764, 'CaqwPIZMAe8i67vX', 'pages', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/CaqwPIZMAe8i67vX', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(765, '7luqkNgPjaORET2I', 'blog3', './public/img/pages/', 'jpg', 'pages', 1, 1, 'admin/archivos/shared_file/7luqkNgPjaORET2I', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(766, 'A3RNIC7ckv4FLWob', 'portfolio', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/A3RNIC7ckv4FLWob', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(767, 'h9mLJE53evFGVQIk', '1', './public/img/portfolio/', 'jpg', 'portfolio', 1, 1, 'admin/archivos/shared_file/h9mLJE53evFGVQIk', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(768, 'OftvjnBq7ao0VhsF', '2', './public/img/portfolio/', 'jpg', 'portfolio', 1, 1, 'admin/archivos/shared_file/OftvjnBq7ao0VhsF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(769, '10QmtFBKnJAovz2e', '3', './public/img/portfolio/', 'jpg', 'portfolio', 1, 1, 'admin/archivos/shared_file/10QmtFBKnJAovz2e', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(770, 'mfGFH3yOQpxteqi8', '4', './public/img/portfolio/', 'jpg', 'portfolio', 1, 1, 'admin/archivos/shared_file/mfGFH3yOQpxteqi8', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(771, 'rxLy9GiHu3SOWk0F', '6', './public/img/portfolio/', 'jpg', 'portfolio', 1, 1, 'admin/archivos/shared_file/rxLy9GiHu3SOWk0F', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(772, 'xYELfQBCcA4VRgeG', 'portfolio-item', './public/img/portfolio/', 'folder', 'portfolio', 1, 1, 'admin/archivos/shared_file/xYELfQBCcA4VRgeG', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(773, 'bWDpGfsSdwN1j8vH', 'portfolio-item-1', './public/img/portfolio/portfolio-item/', 'jpg', 'portfolio-item', 1, 1, 'admin/archivos/shared_file/bWDpGfsSdwN1j8vH', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(774, 'lCcx5JIWvmEaHiwX', 'portfolio-item-2', './public/img/portfolio/portfolio-item/', 'jpg', 'portfolio-item', 1, 1, 'admin/archivos/shared_file/lCcx5JIWvmEaHiwX', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(775, 'n87YEDyerVT26IMo', 'portfolio-item-3', './public/img/portfolio/portfolio-item/', 'jpg', 'portfolio-item', 1, 1, 'admin/archivos/shared_file/n87YEDyerVT26IMo', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(776, 'rH4W9QILTlswRmq1', 'related', './public/img/portfolio/portfolio-item/', 'folder', 'portfolio-item', 1, 1, 'admin/archivos/shared_file/rH4W9QILTlswRmq1', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(777, 'G4RJcDCrlzso1Yxg', 'related-1', './public/img/portfolio/portfolio-item/related/', 'jpg', 'related', 1, 1, 'admin/archivos/shared_file/G4RJcDCrlzso1Yxg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(778, 'X2u40R1pEN6ZWyHt', 'related-2', './public/img/portfolio/portfolio-item/related/', 'jpg', 'related', 1, 1, 'admin/archivos/shared_file/X2u40R1pEN6ZWyHt', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(779, 'szXri6fpS3MA9wHI', 'related-3', './public/img/portfolio/portfolio-item/related/', 'jpg', 'related', 1, 1, 'admin/archivos/shared_file/szXri6fpS3MA9wHI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(780, 'lfMW1j0UdXICoVwS', 'related-4', './public/img/portfolio/portfolio-item/related/', 'jpg', 'related', 1, 1, 'admin/archivos/shared_file/lfMW1j0UdXICoVwS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(781, 'PK61BZWDkw8SEHeJ', 'portfolio-list', './public/img/portfolio/', 'folder', 'portfolio', 1, 1, 'admin/archivos/shared_file/PK61BZWDkw8SEHeJ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(782, 'ibBVASCd4nQMtWT5', 'proj-1-thumb', './public/img/portfolio/portfolio-list/', 'png', 'portfolio-list', 1, 1, 'admin/archivos/shared_file/ibBVASCd4nQMtWT5', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(783, 'pm0nZgisPfWc37RV', 'proj-2-thumb', './public/img/portfolio/portfolio-list/', 'png', 'portfolio-list', 1, 1, 'admin/archivos/shared_file/pm0nZgisPfWc37RV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(784, 'htRdJ4IjQHbDXU3l', 'proj-4-thumb', './public/img/portfolio/portfolio-list/', 'png', 'portfolio-list', 1, 1, 'admin/archivos/shared_file/htRdJ4IjQHbDXU3l', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(785, 'nfrtLMUiBQukpRA3', 'proj-5-thumb', './public/img/portfolio/portfolio-list/', 'png', 'portfolio-list', 1, 1, 'admin/archivos/shared_file/nfrtLMUiBQukpRA3', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(786, 'do0OI1Z4fFaAQUKp', 'profile', './public/img/', 'folder', 'img', 1, 1, 'admin/archivos/shared_file/do0OI1Z4fFaAQUKp', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(787, 'nlu1fOwjQL2SsPh7', 'default', './public/img/profile/', 'png', 'profile', 1, 1, 'admin/archivos/shared_file/nlu1fOwjQL2SsPh7', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(788, 'APZkp9HWL1iCjI6d', 'default_profile_1', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/APZkp9HWL1iCjI6d', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(789, 'onz6ud7gkBmGRhsa', 'default_profile_2', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/onz6ud7gkBmGRhsa', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(790, 'dFcbSUa14hxjlRwY', 'default_profile_3', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/dFcbSUa14hxjlRwY', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(791, 'D5aPCdJYlyEFRoXx', 'default_profile_4', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/D5aPCdJYlyEFRoXx', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(792, '5ZqsfGWaUEnFMBy4', 'forest-patrol', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/5ZqsfGWaUEnFMBy4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(793, 'lWYkITZp834NSafC', 'gerber', './public/img/profile/', 'folder', 'profile', 1, 1, 'admin/archivos/shared_file/lWYkITZp834NSafC', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(794, 'XiWkb7QAJCvhy8HL', '300_3', './public/img/profile/gerber/', 'jpg', 'gerber', 1, 1, 'admin/archivos/shared_file/XiWkb7QAJCvhy8HL', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(795, 'qso9zBxnUG2jfaiS', 'Google IO Plane Design_HD', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/qso9zBxnUG2jfaiS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(796, '53wDFoLfNi0AyhUK', 'Google-Now-Wallpaper-2', './public/img/profile/', 'png', 'profile', 1, 1, 'admin/archivos/shared_file/53wDFoLfNi0AyhUK', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(797, 'qrCEwRUIWBhjxHFd', 'image_new-31', './public/img/profile/', 'png', 'profile', 1, 1, 'admin/archivos/shared_file/qrCEwRUIWBhjxHFd', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(798, 'P61BzaoXk0UO8G3Z', 'low_polygon15', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/P61BzaoXk0UO8G3Z', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(799, 'UfaQbw25yqRL8TGr', 'Material-Wallpaper', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/UfaQbw25yqRL8TGr', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(800, 'nA26jdeqoa7lDOHI', 'piccit_polygon_version_i_drew_of_th_233592283', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/nA26jdeqoa7lDOHI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(801, 'tgLHX6hAWmOk4Njo', 'Ultimate-Material-Lollipop-Collection-65', './public/img/profile/', 'png', 'profile', 1, 1, 'admin/archivos/shared_file/tgLHX6hAWmOk4Njo', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(802, '3RUoCqMV17AZa5Gw', 'user-1', './public/img/profile/', 'png', 'profile', 1, 1, 'admin/archivos/shared_file/3RUoCqMV17AZa5Gw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(803, 'E5cgH4B8QAhkI1pZ', 'user-2', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/E5cgH4B8QAhkI1pZ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(804, 'Gmgj9XCeLZ83w2f1', 'usertop', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/Gmgj9XCeLZ83w2f1', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(805, 'nBTDx86F0NYVflb9', 'userwallpaper-7', './public/img/profile/', 'jpg', 'profile', 1, 1, 'admin/archivos/shared_file/nBTDx86F0NYVflb9', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(806, '7oMVXy308UhJLeqv', 'wallpapers-Wall1', './public/img/profile/', 'png', 'profile', 1, 1, 'admin/archivos/shared_file/7oMVXy308UhJLeqv', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(807, 'rPGDbxLYTgvc8jpw', 'yduran', './public/img/profile/', 'folder', 'profile', 1, 1, 'admin/archivos/shared_file/rPGDbxLYTgvc8jpw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(808, 'j24xUCt1Yunbzhec', '300_10', './public/img/profile/yduran/', 'jpg', 'yduran', 1, 1, 'admin/archivos/shared_file/j24xUCt1Yunbzhec', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(809, 'wjHhV2xEYTslbD7k', '300_11', './public/img/profile/yduran/', 'jpg', 'yduran', 1, 1, 'admin/archivos/shared_file/wjHhV2xEYTslbD7k', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(810, 'aO1etf8TmnuB24dN', '300_4', './public/img/profile/yduran/', 'jpg', 'yduran', 1, 1, 'admin/archivos/shared_file/aO1etf8TmnuB24dN', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(811, 'ktZAxrYpqJem16CV', 'services', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/ktZAxrYpqJem16CV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(812, 'hiSbkP2AdEztWJpI', 'slide1', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/hiSbkP2AdEztWJpI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(813, 'JAwsaD5rQNn8tThv', 'slide2', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/JAwsaD5rQNn8tThv', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(814, 'mxTChs8GWNOfc97M', 'slide3', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/mxTChs8GWNOfc97M', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(815, '3pMIsQwazCloV78F', 'sort_asc', './public/img/', 'png', 'img', 1, 1, 'admin/archivos/shared_file/3pMIsQwazCloV78F', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(816, 'umNQgiJ41EHA9n3I', 'sort_both', './public/img/', 'png', 'img', 1, 1, 'admin/archivos/shared_file/umNQgiJ41EHA9n3I', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(817, 'uYDv06b54V1ARw8r', 'sort_desc', './public/img/', 'png', 'img', 1, 1, 'admin/archivos/shared_file/uYDv06b54V1ARw8r', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(818, 'G6TsyFPX59MQcJbv', 'user1', './public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/G6TsyFPX59MQcJbv', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(819, 'cjQH8VYESKrCNRF0', 'jquery', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/cjQH8VYESKrCNRF0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(820, 'FCbZAydvDoQEq0H6', 'jquery', './public/jquery/', 'js', 'jquery', 1, 1, 'admin/archivos/shared_file/FCbZAydvDoQEq0H6', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(821, 'PTBNicf9jKZm2UYk', 'js', './public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/PTBNicf9jKZm2UYk', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(822, 'dqb7NBWxyUvPp1Vk', 'bootstrap', './public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/dqb7NBWxyUvPp1Vk', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(823, 'PkZdxvri8b3cB5RS', 'chips', './public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/PkZdxvri8b3cB5RS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(824, '4R7d29CXk3Q8WAfT', 'components', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/4R7d29CXk3Q8WAfT', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(825, 'AJ4VnbQeciw3pgX0', 'AlbumsItemsLists', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/AJ4VnbQeciw3pgX0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(826, 'fqRNySs2QPFkuCg6', 'AlbumsLists', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/fqRNySs2QPFkuCg6', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(827, 'VULviIO7A2XHnKDe', 'CategoriaNewForm', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/VULviIO7A2XHnKDe', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(828, 'r4ekjRxMQCXn0zyL', 'CategoriesLists', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/r4ekjRxMQCXn0zyL', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(829, 'kKeI1fbxGPESqFyw', 'ConfiguracionList', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/kKeI1fbxGPESqFyw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(830, 'BsdRz6lrb4IhfS0m', 'configurationComponent', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/BsdRz6lrb4IhfS0m', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(831, 'WxGDr9Vs5vuPjylt', 'CustomFormLists', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/WxGDr9Vs5vuPjylt', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(832, 'byhWdzEwIBirM86V', 'dashboardBundle', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/byhWdzEwIBirM86V', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(833, 't3dY2n6Hg5UWPQJl', 'dashboardModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/t3dY2n6Hg5UWPQJl', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(834, 'Pkf9ZFxAmG7WCtg0', 'dataFormModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/Pkf9ZFxAmG7WCtg0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(835, 'GNkx5tgdC8m0AnI1', 'fileExplorerModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/GNkx5tgdC8m0AnI1', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(836, 'vSleKd6DNH4IMRkq', 'FileExplorerSelector', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/vSleKd6DNH4IMRkq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(837, '3JUEqOX8xyBMRpmW', 'fileUploaderModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/3JUEqOX8xyBMRpmW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(838, 'RINonQHv5plW1AX2', 'formContent', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/RINonQHv5plW1AX2', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(839, 'GaRvTl68c01KXJxW', 'FormContentList', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/GaRvTl68c01KXJxW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(840, 'MY5CfzaR0DKqPsbl', 'FormContentNewModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/MY5CfzaR0DKqPsbl', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(841, '2rfuSOKv6jeAaX0l', 'FormContentNewModuleBundle', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/2rfuSOKv6jeAaX0l', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(842, 'xLQdgBaOFNyl3nAt', 'formModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/xLQdgBaOFNyl3nAt', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(843, 'G3NLvW1xcDuPryIf', 'FormNewModule', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/G3NLvW1xcDuPryIf', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(844, 'TUVinc3tDpXqkhBN', 'FormNewModuleBundle', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/TUVinc3tDpXqkhBN', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(845, 'u0Z1E2BsPdlFI96i', 'loginForm', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/u0Z1E2BsPdlFI96i', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(846, 'tUv6hrQLZ4WlJom0', 'PageNewForm', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/tUv6hrQLZ4WlJom0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(847, 'PxeayL3jHSdRiYJU', 'PagesLists', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/PxeayL3jHSdRiYJU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(848, 'BSkbrz1Y9OWasXPN', 'userComponent', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/BSkbrz1Y9OWasXPN', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(849, 'lrmUXBL6P3bhONga', 'UserGroupsComponent', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/lrmUXBL6P3bhONga', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(850, '0tuvcTNV1Z5I3Ong', 'UserNewForm', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/0tuvcTNV1Z5I3Ong', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(851, '8WOIXvsKQZpiAPNe', 'userProfileComponent', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/8WOIXvsKQZpiAPNe', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(852, '2knL0KmAUSsjOeRT', 'draggable', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/2knL0KmAUSsjOeRT', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(853, 'LQsMGX6nqBiDUFbp', 'bower', './public/js/draggable/', 'json', 'draggable', 1, 1, 'admin/archivos/shared_file/LQsMGX6nqBiDUFbp', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(854, 'T95rcszK7nQdVS2g', 'examples', './public/js/draggable/', 'folder', 'draggable', 1, 1, 'admin/archivos/shared_file/T95rcszK7nQdVS2g', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(855, 'Y6erAusbQHxUhKFj', 'asp.net', './public/js/draggable/examples/', 'net\\', 'examples', 1, 1, 'admin/archivos/shared_file/Y6erAusbQHxUhKFj', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(856, 'KwYNhjvqMyuFn7rS', 'html', './public/js/draggable/examples/', 'folder', 'examples', 1, 1, 'admin/archivos/shared_file/KwYNhjvqMyuFn7rS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(857, 'Aktc6Z40TFbYNeBl', 'example', './public/js/draggable/examples/html/', 'html', 'html', 1, 1, 'admin/archivos/shared_file/Aktc6Z40TFbYNeBl', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(858, 'NdLCoIkjsthpR3Mw', 'php', './public/js/draggable/examples/', 'folder', 'examples', 1, 1, 'admin/archivos/shared_file/NdLCoIkjsthpR3Mw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(859, 'vgL7D8qGlHuaJZB4', 'example', './public/js/draggable/examples/php/', 'php', 'php', 1, 1, 'admin/archivos/shared_file/vgL7D8qGlHuaJZB4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(860, 'eDJ4c5GzjBHsRKpQ', 'index', './public/js/draggable/', 'html', 'draggable', 1, 1, 'admin/archivos/shared_file/eDJ4c5GzjBHsRKpQ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(861, '49ijgu57KPd8pYtW', 'package', './public/js/draggable/', 'json', 'draggable', 1, 1, 'admin/archivos/shared_file/49ijgu57KPd8pYtW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(862, '1vRYAeqk6MNxLUby', 'readme', './public/js/draggable/', 'md', 'draggable', 1, 1, 'admin/archivos/shared_file/1vRYAeqk6MNxLUby', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(863, 'FwtNli2YvJRbe0QD', 'fileinput-master', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/FwtNli2YvJRbe0QD', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(864, 'P6F8U3tk9KudsNTI', 'bower', './public/js/fileinput-master/', 'json', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/P6F8U3tk9KudsNTI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(865, 'cdAIjDPOwflQXhq0', 'CHANGE', './public/js/fileinput-master/', 'md', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/cdAIjDPOwflQXhq0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(866, 'YFxNlkLHOjWPzuQe', 'composer', './public/js/fileinput-master/', 'json', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/YFxNlkLHOjWPzuQe', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(867, 'WKLd8PlA4ZJGFsqT', 'css', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/WKLd8PlA4ZJGFsqT', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(868, 'Aw9kixVQGcYhp3JW', 'fileinput-rtl', './public/js/fileinput-master/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/Aw9kixVQGcYhp3JW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(869, 'Wt27SLYrqo6XxOGU', 'fileinput', './public/js/fileinput-master/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/Wt27SLYrqo6XxOGU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(870, 'zYP7HfZAKFNGvm9M', 'examples', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/zYP7HfZAKFNGvm9M', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(871, '0KuVCBaS7s46Mre3', 'index', './public/js/fileinput-master/examples/', 'html', 'examples', 1, 1, 'admin/archivos/shared_file/0KuVCBaS7s46Mre3', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(872, '9Qut2CPyK6flARkW', 'img', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/9Qut2CPyK6flARkW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(873, 'HNig8CFbh6A7OuEd', 'loading-sm', './public/js/fileinput-master/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/HNig8CFbh6A7OuEd', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(874, '9Zu3IhcTrUiDysmC', 'loading', './public/js/fileinput-master/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/9Zu3IhcTrUiDysmC', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(875, 'afmzAjTvigwlqbS4', 'js', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/afmzAjTvigwlqbS4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(876, '4RYGMi9lynPcQ10S', 'fileinput', './public/js/fileinput-master/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/4RYGMi9lynPcQ10S', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(877, 'ZzQVbDmuf8lC7N0B', 'locales', './public/js/fileinput-master/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/ZzQVbDmuf8lC7N0B', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(878, 'I9zG5lMUmJZhj4TF', 'ar', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/I9zG5lMUmJZhj4TF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(879, 'KRPz2VprgTmMjL5W', 'az', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/KRPz2VprgTmMjL5W', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(880, 'QSiRwKWvOjLoVaTA', 'bg', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/QSiRwKWvOjLoVaTA', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(881, 'dwLvsxCftEM76GmY', 'ca', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/dwLvsxCftEM76GmY', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(882, '0htbjqJO6HmYyrRz', 'cr', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/0htbjqJO6HmYyrRz', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(883, 'KvkbD5F0HTCJBIMy', 'cs', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/KvkbD5F0HTCJBIMy', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(884, 'IKWpD72XJPewt054', 'da', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/IKWpD72XJPewt054', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(885, 'jUwaehYXqF908cGT', 'de', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/jUwaehYXqF908cGT', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(886, 'SrBfjRqZnh9Np6Wd', 'el', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/SrBfjRqZnh9Np6Wd', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(887, 'fkcUYjyqh6abEBsH', 'es', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/fkcUYjyqh6abEBsH', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(888, 'xU4KEcG7zukHpoFW', 'et', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/xU4KEcG7zukHpoFW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(889, 'rau6ZlWx9LtCysEg', 'fa', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/rau6ZlWx9LtCysEg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(890, 'SFRABZ9puioKlvIy', 'fi', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/SFRABZ9puioKlvIy', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(891, 'GvjOF51u7xJHVw6g', 'fr', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/GvjOF51u7xJHVw6g', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(892, 'joMsuiw41UH39SZh', 'gl', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/joMsuiw41UH39SZh', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(893, 'E6LspPHkzynfW83O', 'he', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/E6LspPHkzynfW83O', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(894, 'PaFKsGjBXVCmRgDE', 'hu', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/PaFKsGjBXVCmRgDE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(895, 'pbmxTFBLckfKlj7r', 'id', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/pbmxTFBLckfKlj7r', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(896, 'BTanFy7bgYPKNcei', 'it', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/BTanFy7bgYPKNcei', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(897, 'UXlKNdiCF2Yq8IPA', 'ja', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/UXlKNdiCF2Yq8IPA', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(898, '7CTivdYhs29MzlAg', 'ka', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/7CTivdYhs29MzlAg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(899, 'MP1CF3eJAOkTz6pQ', 'kr', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/MP1CF3eJAOkTz6pQ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(900, '2Z0FljTEAPSHGy8m', 'kz', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/2Z0FljTEAPSHGy8m', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(901, 'MY1WnEUylJa5t4mF', 'LANG', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/MY1WnEUylJa5t4mF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(902, 'mJVTYCrftE8q9hbx', 'lt', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/mJVTYCrftE8q9hbx', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(903, 'SYyx4Ko1Ua7npVvB', 'nl', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/SYyx4Ko1Ua7npVvB', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(904, 'D4xLSrkHIv79ZGn2', 'no', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/D4xLSrkHIv79ZGn2', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(905, 'EhU2TqeX7wlD40KO', 'pl', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/EhU2TqeX7wlD40KO', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(906, 'tSr0lHhXcuGpzPRU', 'pt-BR', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/tSr0lHhXcuGpzPRU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(907, 'sPuWtmAqhFneH3zX', 'pt', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/sPuWtmAqhFneH3zX', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(908, 'nc10mVZHN6tvu32Y', 'ro', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/nc10mVZHN6tvu32Y', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(909, 'aEprqMYo5u7zGXch', 'ru', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/aEprqMYo5u7zGXch', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(910, 'hIaC1m2c4RrpjQoJ', 'sk', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/hIaC1m2c4RrpjQoJ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(911, '5HpMrk7UsSWBK6lu', 'sl', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/5HpMrk7UsSWBK6lu', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(912, 'wHAzcjoKYJmG5PLW', 'sv', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/wHAzcjoKYJmG5PLW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(913, 'zqeXp0avrnNbA9I3', 'th', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/zqeXp0avrnNbA9I3', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(914, '07ZfKUPBylCYW5H2', 'tr', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/07ZfKUPBylCYW5H2', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(915, 'DYeaU6BbJZQW2rcs', 'uk', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/DYeaU6BbJZQW2rcs', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(916, 'TV7phNxuqlQSKZRH', 'uz', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/TV7phNxuqlQSKZRH', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(917, 'z0dyFXTtCW8kDPf9', 'vi', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/z0dyFXTtCW8kDPf9', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(918, 'tcIpn697VruD3TdA', 'zh-TW', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/tcIpn697VruD3TdA', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(919, 'NJ4Dz6kQMuELA9rW', 'zh', './public/js/fileinput-master/js/locales/', 'js', 'locales', 1, 1, 'admin/archivos/shared_file/NJ4Dz6kQMuELA9rW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(920, 'IWCfgcwiGuL16Ab4', 'plugins', './public/js/fileinput-master/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/IWCfgcwiGuL16Ab4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(921, '9iNlOy4nhzmbRF6U', 'canvas-to-blob', './public/js/fileinput-master/js/plugins/', 'js', 'plugins', 1, 1, 'admin/archivos/shared_file/9iNlOy4nhzmbRF6U', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(922, 'OPwdeBT8nGAbh2IC', 'piexif', './public/js/fileinput-master/js/plugins/', 'js', 'plugins', 1, 1, 'admin/archivos/shared_file/OPwdeBT8nGAbh2IC', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(923, 'r0tD5vRIpFMsdc8i', 'purify', './public/js/fileinput-master/js/plugins/', 'js', 'plugins', 1, 1, 'admin/archivos/shared_file/r0tD5vRIpFMsdc8i', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(924, 'FekYxOBtC7l4UznH', 'sortable', './public/js/fileinput-master/js/plugins/', 'js', 'plugins', 1, 1, 'admin/archivos/shared_file/FekYxOBtC7l4UznH', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(925, 'TuUe6w9sfJgpCa1i', 'LICENSE', './public/js/fileinput-master/', 'md', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/TuUe6w9sfJgpCa1i', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(926, 'vwB5CuM8jLhybcH0', 'nuget', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/vwB5CuM8jLhybcH0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(927, 'YN6gfv3HobGLJAqu', 'build', './public/js/fileinput-master/nuget/', 'bat', 'nuget', 1, 1, 'admin/archivos/shared_file/YN6gfv3HobGLJAqu', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(928, 'SxzFJW50meR6DPYB', 'Package', './public/js/fileinput-master/nuget/', 'nuspec', 'nuget', 1, 1, 'admin/archivos/shared_file/SxzFJW50meR6DPYB', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(929, '7bL3Tzt8cKDEeH4C', 'package', './public/js/fileinput-master/', 'json', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/7bL3Tzt8cKDEeH4C', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(930, 'Mjt8UkZaW7NIcJ1D', 'README', './public/js/fileinput-master/', 'md', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/Mjt8UkZaW7NIcJ1D', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(931, 'wLKSpbENfBDYFT7l', 'scss', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/wLKSpbENfBDYFT7l', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(932, 'yFlKA43VP1GhoZ8g', 'fileinput-rtl', './public/js/fileinput-master/scss/', 'scss', 'scss', 1, 1, 'admin/archivos/shared_file/yFlKA43VP1GhoZ8g', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(933, 'TlSfCPmiLxwDIt1B', 'fileinput', './public/js/fileinput-master/scss/', 'scss', 'scss', 1, 1, 'admin/archivos/shared_file/TlSfCPmiLxwDIt1B', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(934, '7NiH1p3DExdOK82g', 'themes', './public/js/fileinput-master/scss/', 'folder', 'scss', 1, 1, 'admin/archivos/shared_file/7NiH1p3DExdOK82g', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(935, 'fcbrFUkKeTtCj1NW', 'explorer', './public/js/fileinput-master/scss/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/fcbrFUkKeTtCj1NW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(936, 'W62QvcaTemxSIrO8', 'theme', './public/js/fileinput-master/scss/themes/explorer/', 'scss', 'explorer', 1, 1, 'admin/archivos/shared_file/W62QvcaTemxSIrO8', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(937, 'zomnqbVFltjPRGJ4', 'explorer-fa', './public/js/fileinput-master/scss/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/zomnqbVFltjPRGJ4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(938, 'qucLMdVJHihmAs8D', 'theme', './public/js/fileinput-master/scss/themes/explorer-fa/', 'scss', 'explorer-fa', 1, 1, 'admin/archivos/shared_file/qucLMdVJHihmAs8D', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(939, 'RSIXY7AKmLUThylE', 'explorer-fas', './public/js/fileinput-master/scss/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/RSIXY7AKmLUThylE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(940, 'Xnv9rUajZHhO53zg', 'theme', './public/js/fileinput-master/scss/themes/explorer-fas/', 'scss', 'explorer-fas', 1, 1, 'admin/archivos/shared_file/Xnv9rUajZHhO53zg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(941, 'AvRPL1lHTh4iexz2', 'themes', './public/js/fileinput-master/', 'folder', 'fileinput-master', 1, 1, 'admin/archivos/shared_file/AvRPL1lHTh4iexz2', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(942, '7xLvihz9A1Fsgl83', 'explorer', './public/js/fileinput-master/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/7xLvihz9A1Fsgl83', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(943, '8qlkKYn1rpwxdauZ', 'theme', './public/js/fileinput-master/themes/explorer/', 'css', 'explorer', 1, 1, 'admin/archivos/shared_file/8qlkKYn1rpwxdauZ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(944, 'mHtDhfgxU7IzWZKJ', 'explorer-fa', './public/js/fileinput-master/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/mHtDhfgxU7IzWZKJ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(945, 'EZXRjp2ulsf0r1CW', 'theme', './public/js/fileinput-master/themes/explorer-fa/', 'css', 'explorer-fa', 1, 1, 'admin/archivos/shared_file/EZXRjp2ulsf0r1CW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(946, 'fXFjgs6xal8YmHRn', 'explorer-fas', './public/js/fileinput-master/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/fXFjgs6xal8YmHRn', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(947, 'vd4t92SfqsoXwhYe', 'theme', './public/js/fileinput-master/themes/explorer-fas/', 'css', 'explorer-fas', 1, 1, 'admin/archivos/shared_file/vd4t92SfqsoXwhYe', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(948, 'IlcSChjzxnvYgKVX', 'fa', './public/js/fileinput-master/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/IlcSChjzxnvYgKVX', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(949, 'R2Jx5GXqslP4dQab', 'theme', './public/js/fileinput-master/themes/fa/', 'js', 'fa', 1, 1, 'admin/archivos/shared_file/R2Jx5GXqslP4dQab', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(950, 'W7oGenmAiRYFHOE6', 'fas', './public/js/fileinput-master/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/W7oGenmAiRYFHOE6', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(951, 'KW74Jl3epcB6fzwA', 'theme', './public/js/fileinput-master/themes/fas/', 'js', 'fas', 1, 1, 'admin/archivos/shared_file/KW74Jl3epcB6fzwA', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(952, 'aZHe6kKvNus7r23X', 'gly', './public/js/fileinput-master/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/aZHe6kKvNus7r23X', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(953, 'OFDrNHY5zjCfkhvW', 'theme', './public/js/fileinput-master/themes/gly/', 'js', 'gly', 1, 1, 'admin/archivos/shared_file/OFDrNHY5zjCfkhvW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(954, 'fMaDyOj49VbZwL2Q', 'jqBootstrapValidation', './public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/fMaDyOj49VbZwL2Q', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(955, 'l1hxf4OjSaBZWqro', 'jquery', './public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/l1hxf4OjSaBZWqro', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(956, 'FPLc9OthJn0pZ2H1', 'lightbox2-master', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/FPLc9OthJn0pZ2H1', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(957, 'Ab8WGlnN4FYVgO9v', 'bower', './public/js/lightbox2-master/', 'json', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/Ab8WGlnN4FYVgO9v', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(958, 'yBdpWce5ZfrFEj61', 'CONTRIBUTING', './public/js/lightbox2-master/', 'md', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/yBdpWce5ZfrFEj61', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(959, 'psMyhLDBtW2m8wCQ', 'dist', './public/js/lightbox2-master/', 'folder', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/psMyhLDBtW2m8wCQ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(960, 'mvWfFhdipt5Is0yj', 'css', './public/js/lightbox2-master/dist/', 'folder', 'dist', 1, 1, 'admin/archivos/shared_file/mvWfFhdipt5Is0yj', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(961, '6lVx3GhAybmjsEYq', 'lightbox', './public/js/lightbox2-master/dist/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/6lVx3GhAybmjsEYq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(962, 'qPLYHtywxpzABI0c', 'images', './public/js/lightbox2-master/dist/', 'folder', 'dist', 1, 1, 'admin/archivos/shared_file/qPLYHtywxpzABI0c', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(963, 'bKZE2NWu7CtpdzSm', 'close', './public/js/lightbox2-master/dist/images/', 'png', 'images', 1, 1, 'admin/archivos/shared_file/bKZE2NWu7CtpdzSm', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(964, 'o87QFdWPkTufMtD1', 'loading', './public/js/lightbox2-master/dist/images/', 'gif', 'images', 1, 1, 'admin/archivos/shared_file/o87QFdWPkTufMtD1', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(965, 'tgfTWF95ckPOMxrj', 'next', './public/js/lightbox2-master/dist/images/', 'png', 'images', 1, 1, 'admin/archivos/shared_file/tgfTWF95ckPOMxrj', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(966, 'ZUbnpaYw90PBihGF', 'prev', './public/js/lightbox2-master/dist/images/', 'png', 'images', 1, 1, 'admin/archivos/shared_file/ZUbnpaYw90PBihGF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(967, 'VL18Gd2jiogWUaNz', 'js', './public/js/lightbox2-master/dist/', 'folder', 'dist', 1, 1, 'admin/archivos/shared_file/VL18Gd2jiogWUaNz', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(968, 'cUzQA8lK7w1TmjsS', 'lightbox-plus-jquery', './public/js/lightbox2-master/dist/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/cUzQA8lK7w1TmjsS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(969, '1cjoxDHB0qXKWRPN', 'lightbox', './public/js/lightbox2-master/dist/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/1cjoxDHB0qXKWRPN', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(970, 'Kzf31T9DWwqMx57U', 'examples', './public/js/lightbox2-master/', 'folder', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/Kzf31T9DWwqMx57U', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(971, 'KsgMeCxT4O9WnprI', 'index', './public/js/lightbox2-master/examples/', 'html', 'examples', 1, 1, 'admin/archivos/shared_file/KsgMeCxT4O9WnprI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(972, 'ZkbHRpJrj5XyiFKE', 'Gruntfile', './public/js/lightbox2-master/', 'js', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/ZkbHRpJrj5XyiFKE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(973, 'rpx83DfKzJQ1lRM0', 'package', './public/js/lightbox2-master/', 'json', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/rpx83DfKzJQ1lRM0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(974, 'b5oEJHqF9KnIMlde', 'README', './public/js/lightbox2-master/', 'md', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/b5oEJHqF9KnIMlde', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(975, 'pSR0cOWM9a7bCmTu', 'src', './public/js/lightbox2-master/', 'folder', 'lightbox2-master', 1, 1, 'admin/archivos/shared_file/pSR0cOWM9a7bCmTu', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(976, '4WLTgFhiwD3rEoHP', 'css', './public/js/lightbox2-master/src/', 'folder', 'src', 1, 1, 'admin/archivos/shared_file/4WLTgFhiwD3rEoHP', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(977, 'rdnYFiV7BPAfgWoH', 'lightbox', './public/js/lightbox2-master/src/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/rdnYFiV7BPAfgWoH', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(978, 'yoAb3Mnqa2FKB5Cs', 'images', './public/js/lightbox2-master/src/', 'folder', 'src', 1, 1, 'admin/archivos/shared_file/yoAb3Mnqa2FKB5Cs', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(979, 'XWaUOwoGhyv9sIRm', 'close', './public/js/lightbox2-master/src/images/', 'png', 'images', 1, 1, 'admin/archivos/shared_file/XWaUOwoGhyv9sIRm', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(980, 'RJwGSWprmNAIqDsd', 'loading', './public/js/lightbox2-master/src/images/', 'gif', 'images', 1, 1, 'admin/archivos/shared_file/RJwGSWprmNAIqDsd', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(981, 'j6feuRst1dSchvZ9', 'next', './public/js/lightbox2-master/src/images/', 'png', 'images', 1, 1, 'admin/archivos/shared_file/j6feuRst1dSchvZ9', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(982, 'FgUckZl4s5ztEq2D', 'prev', './public/js/lightbox2-master/src/images/', 'png', 'images', 1, 1, 'admin/archivos/shared_file/FgUckZl4s5ztEq2D', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(983, 'HsTuSyMb7n6JirCc', 'js', './public/js/lightbox2-master/src/', 'folder', 'src', 1, 1, 'admin/archivos/shared_file/HsTuSyMb7n6JirCc', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(984, '46O59Ufi3lQwadpI', 'lightbox', './public/js/lightbox2-master/src/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/46O59Ufi3lQwadpI', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(985, 'MuUaGYgqO30cJnBF', 'materialize', './public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/MuUaGYgqO30cJnBF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(986, 'z37BMpmOEsbNHKZw', 'mensajes', './public/js/', 'min.js', 'js', 1, 1, 'admin/archivos/shared_file/z37BMpmOEsbNHKZw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(987, 'ZRXpfOmFdw9WMha3', 'parallax', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/ZRXpfOmFdw9WMha3', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(988, 'hqjmrbnE9AZyiQtF', 'bower', './public/js/parallax/', 'json', 'parallax', 1, 1, 'admin/archivos/shared_file/hqjmrbnE9AZyiQtF', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(989, 'XFrIG1xBO0L3CWva', 'Parallax', './public/js/parallax/', 'html', 'parallax', 1, 1, 'admin/archivos/shared_file/XFrIG1xBO0L3CWva', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(990, 'o49ia6HeFCVKAl0b', 'README', './public/js/parallax/', 'md', 'parallax', 1, 1, 'admin/archivos/shared_file/o49ia6HeFCVKAl0b', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(991, '8YZ0gdw7xqeKTWGS', 'service-worker', './public/js/', 'min.js', 'js', 1, 1, 'admin/archivos/shared_file/8YZ0gdw7xqeKTWGS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(992, 'oY6BNXfh8SeLJg9W', 'start', './public/js/', 'min.js', 'js', 1, 1, 'admin/archivos/shared_file/oY6BNXfh8SeLJg9W', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(993, 'afFwxEWZOk4tV8Jr', 'tinymce', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/afFwxEWZOk4tV8Jr', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(994, '8a91YxqIB6cjl4oL', 'changelog', './public/js/tinymce/', 'txt', 'tinymce', 1, 1, 'admin/archivos/shared_file/8a91YxqIB6cjl4oL', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(995, '2RfC8gGyohF1wB3r', 'js', './public/js/tinymce/', 'folder', 'tinymce', 1, 1, 'admin/archivos/shared_file/2RfC8gGyohF1wB3r', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(996, 'x7kKypsqdatlFE9o', 'tinymce', './public/js/tinymce/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/x7kKypsqdatlFE9o', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(997, 'SVZPUvwXHshT8axD', 'jquery', './public/js/tinymce/js/tinymce/', 'tinymce.min.js', 'tinymce', 1, 1, 'admin/archivos/shared_file/SVZPUvwXHshT8axD', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(998, 'SVWMY3IHD7RodPXq', 'langs', './public/js/tinymce/js/tinymce/', 'folder', 'tinymce', 1, 1, 'admin/archivos/shared_file/SVWMY3IHD7RodPXq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(999, 'J45MniZLXlmNeQhY', 'readme', './public/js/tinymce/js/tinymce/langs/', 'md', 'langs', 1, 1, 'admin/archivos/shared_file/J45MniZLXlmNeQhY', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1000, 'VE9MIPqoWKS56kuz', 'license', './public/js/tinymce/js/tinymce/', 'txt', 'tinymce', 1, 1, 'admin/archivos/shared_file/VE9MIPqoWKS56kuz', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1001, 'ab6HDQSnXIA80UZh', 'plugins', './public/js/tinymce/js/tinymce/', 'folder', 'tinymce', 1, 1, 'admin/archivos/shared_file/ab6HDQSnXIA80UZh', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1002, 'wOiCvoy8TS2Lnd3l', 'advlist', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/wOiCvoy8TS2Lnd3l', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1003, 'WR2jsYMJn19BCwz4', 'plugin', './public/js/tinymce/js/tinymce/plugins/advlist/', 'min.js', 'advlist', 1, 1, 'admin/archivos/shared_file/WR2jsYMJn19BCwz4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1004, 'sYDCE5wbeQAzdyuT', 'anchor', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/sYDCE5wbeQAzdyuT', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1005, 's493pVkLzylqDn7f', 'plugin', './public/js/tinymce/js/tinymce/plugins/anchor/', 'min.js', 'anchor', 1, 1, 'admin/archivos/shared_file/s493pVkLzylqDn7f', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1006, 'm4n8DslajVp5gu1v', 'autolink', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/m4n8DslajVp5gu1v', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1007, 'WJcgTQAtmye0X56z', 'plugin', './public/js/tinymce/js/tinymce/plugins/autolink/', 'min.js', 'autolink', 1, 1, 'admin/archivos/shared_file/WJcgTQAtmye0X56z', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1008, 'QkhYLwnG2m3a6vC5', 'autoresize', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/QkhYLwnG2m3a6vC5', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1009, 'BKjpgdM0WNOkZHJ6', 'plugin', './public/js/tinymce/js/tinymce/plugins/autoresize/', 'min.js', 'autoresize', 1, 1, 'admin/archivos/shared_file/BKjpgdM0WNOkZHJ6', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1010, 'fMhNdlySmp4ILvDO', 'autosave', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/fMhNdlySmp4ILvDO', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1011, 'cNYRy18wCvDBhKuU', 'plugin', './public/js/tinymce/js/tinymce/plugins/autosave/', 'min.js', 'autosave', 1, 1, 'admin/archivos/shared_file/cNYRy18wCvDBhKuU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1012, 'gmQtbXGoAS4Lc8zi', 'bbcode', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/gmQtbXGoAS4Lc8zi', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1013, 'U4Gz2PoA3V7SeQYE', 'plugin', './public/js/tinymce/js/tinymce/plugins/bbcode/', 'min.js', 'bbcode', 1, 1, 'admin/archivos/shared_file/U4Gz2PoA3V7SeQYE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1014, 'r1OxUnVFDwL7tf50', 'charmap', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/r1OxUnVFDwL7tf50', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1015, 'hIgJcXQLUfadZCPv', 'plugin', './public/js/tinymce/js/tinymce/plugins/charmap/', 'min.js', 'charmap', 1, 1, 'admin/archivos/shared_file/hIgJcXQLUfadZCPv', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1016, 'rWkbHupUqoLPhOXV', 'code', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/rWkbHupUqoLPhOXV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1017, 'rROGxo0PqXE1AK7B', 'plugin', './public/js/tinymce/js/tinymce/plugins/code/', 'min.js', 'code', 1, 1, 'admin/archivos/shared_file/rROGxo0PqXE1AK7B', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1018, 'bMR0ZhxupHywdQYk', 'codesample', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/bMR0ZhxupHywdQYk', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1019, '9nmk2J0DtQUFqM5X', 'css', './public/js/tinymce/js/tinymce/plugins/codesample/', 'folder', 'codesample', 1, 1, 'admin/archivos/shared_file/9nmk2J0DtQUFqM5X', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1020, '4JPlb6zdgOHxokcE', 'prism', './public/js/tinymce/js/tinymce/plugins/codesample/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/4JPlb6zdgOHxokcE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1021, 'S1tCNAnQsgw9Xxca', 'plugin', './public/js/tinymce/js/tinymce/plugins/codesample/', 'min.js', 'codesample', 1, 1, 'admin/archivos/shared_file/S1tCNAnQsgw9Xxca', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1022, 'yPYRFeEap0KuMxhs', 'colorpicker', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/yPYRFeEap0KuMxhs', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1023, 'vqkfR5LlJ8MOByEV', 'plugin', './public/js/tinymce/js/tinymce/plugins/colorpicker/', 'min.js', 'colorpicker', 1, 1, 'admin/archivos/shared_file/vqkfR5LlJ8MOByEV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1024, 'Ep0ubXkNInZq65Jm', 'contextmenu', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/Ep0ubXkNInZq65Jm', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1025, 'x2bE71QW4Kdu6ZMs', 'plugin', './public/js/tinymce/js/tinymce/plugins/contextmenu/', 'min.js', 'contextmenu', 1, 1, 'admin/archivos/shared_file/x2bE71QW4Kdu6ZMs', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1026, '0Smc8V5Tus6DaxdN', 'directionality', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/0Smc8V5Tus6DaxdN', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1027, 'zIfx2jQ3sDedn7pZ', 'plugin', './public/js/tinymce/js/tinymce/plugins/directionality/', 'min.js', 'directionality', 1, 1, 'admin/archivos/shared_file/zIfx2jQ3sDedn7pZ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1028, 'Rul9IPLzoKcfXEtY', 'emoticons', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/Rul9IPLzoKcfXEtY', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1029, 'CWhTYl6HRNnf1Lj4', 'img', './public/js/tinymce/js/tinymce/plugins/emoticons/', 'folder', 'emoticons', 1, 1, 'admin/archivos/shared_file/CWhTYl6HRNnf1Lj4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1030, 'hz0dW94aO5UlKkZe', 'smiley-cool', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/hz0dW94aO5UlKkZe', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1031, 't7JgZYRMnOI928NB', 'smiley-cry', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/t7JgZYRMnOI928NB', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1032, 'wxKV0mTzhpgJaEqv', 'smiley-embarassed', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/wxKV0mTzhpgJaEqv', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1033, 'ywc0dBUnhmv3skTp', 'smiley-foot-in-mouth', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/ywc0dBUnhmv3skTp', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1034, 'jL3zWwQH1otNevmg', 'smiley-frown', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/jL3zWwQH1otNevmg', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1035, 'e41Cm0F2JKOl3Sr7', 'smiley-innocent', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/e41Cm0F2JKOl3Sr7', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1036, 'fXeGbnpRzUV3yS0H', 'smiley-kiss', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/fXeGbnpRzUV3yS0H', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1037, '0x89UQrl52Cgnqvp', 'smiley-laughing', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/0x89UQrl52Cgnqvp', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1038, 'iNFe1tjQdDWSHB0Z', 'smiley-money-mouth', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/iNFe1tjQdDWSHB0Z', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1039, 'bOGSKqC15y6XeZTV', 'smiley-sealed', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/bOGSKqC15y6XeZTV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1040, 'lNeuAYPxOS5Fr6oU', 'smiley-smile', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/lNeuAYPxOS5Fr6oU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1041, 'N3cFoiOD1wQpnGYV', 'smiley-surprised', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/N3cFoiOD1wQpnGYV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1042, 'LfED2iUeQ9JBHxcm', 'smiley-tongue-out', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/LfED2iUeQ9JBHxcm', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1043, 'CZQAB9s3jRl0FrUE', 'smiley-undecided', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/CZQAB9s3jRl0FrUE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1044, 'i2a5ortYOSgbXWj4', 'smiley-wink', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/i2a5ortYOSgbXWj4', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1045, 'NARH3pwWI1lrCmJE', 'smiley-yell', './public/js/tinymce/js/tinymce/plugins/emoticons/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/NARH3pwWI1lrCmJE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1046, 'ZQ3icqw4jbBJ9Mf8', 'plugin', './public/js/tinymce/js/tinymce/plugins/emoticons/', 'min.js', 'emoticons', 1, 1, 'admin/archivos/shared_file/ZQ3icqw4jbBJ9Mf8', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1047, '4OwSitsCnNWh71A2', 'example', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/4OwSitsCnNWh71A2', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1048, 'jdYqGJ60OkcVE3Uy', 'dialog', './public/js/tinymce/js/tinymce/plugins/example/', 'html', 'example', 1, 1, 'admin/archivos/shared_file/jdYqGJ60OkcVE3Uy', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1049, 'uJ0qG5oNeDlkTZFU', 'plugin', './public/js/tinymce/js/tinymce/plugins/example/', 'min.js', 'example', 1, 1, 'admin/archivos/shared_file/uJ0qG5oNeDlkTZFU', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1050, 'kKczyHDBLNvW75JQ', 'example_dependency', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/kKczyHDBLNvW75JQ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1051, 'ovkLzxNKFDCidOMG', 'plugin', './public/js/tinymce/js/tinymce/plugins/example_dependency/', 'min.js', 'example_dependency', 1, 1, 'admin/archivos/shared_file/ovkLzxNKFDCidOMG', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1052, 'eQTctAkH0lrf6Pid', 'fullpage', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/eQTctAkH0lrf6Pid', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1053, 'NsZpAvarek9on24y', 'plugin', './public/js/tinymce/js/tinymce/plugins/fullpage/', 'min.js', 'fullpage', 1, 1, 'admin/archivos/shared_file/NsZpAvarek9on24y', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1054, '5bwFpTPknAiSHQDc', 'fullscreen', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/5bwFpTPknAiSHQDc', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1055, 'mDjKcEtxi6gnyIe5', 'plugin', './public/js/tinymce/js/tinymce/plugins/fullscreen/', 'min.js', 'fullscreen', 1, 1, 'admin/archivos/shared_file/mDjKcEtxi6gnyIe5', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1056, 'wcuF3X56pfN7j4Ox', 'hr', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/wcuF3X56pfN7j4Ox', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1057, '7ok3aL6fOnURXFKh', 'plugin', './public/js/tinymce/js/tinymce/plugins/hr/', 'min.js', 'hr', 1, 1, 'admin/archivos/shared_file/7ok3aL6fOnURXFKh', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1058, '7SVBf92ghdeEa3Nw', 'image', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/7SVBf92ghdeEa3Nw', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1059, 'zrRvwe6NhlBXI2ZV', 'plugin', './public/js/tinymce/js/tinymce/plugins/image/', 'min.js', 'image', 1, 1, 'admin/archivos/shared_file/zrRvwe6NhlBXI2ZV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1060, 'WGkFiDsyAf5eVLnr', 'imagetools', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/WGkFiDsyAf5eVLnr', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1061, 'meEUubhg9zFXypT3', 'plugin', './public/js/tinymce/js/tinymce/plugins/imagetools/', 'min.js', 'imagetools', 1, 1, 'admin/archivos/shared_file/meEUubhg9zFXypT3', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1062, '94bB1AyOtLif5K2W', 'importcss', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/94bB1AyOtLif5K2W', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1063, 'Ml39cQrDJLIb56EZ', 'plugin', './public/js/tinymce/js/tinymce/plugins/importcss/', 'min.js', 'importcss', 1, 1, 'admin/archivos/shared_file/Ml39cQrDJLIb56EZ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1064, 'WfqzaF4i1CsNd2Bm', 'insertdatetime', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/WfqzaF4i1CsNd2Bm', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1065, 'NYFeV8ZObHkBTcEW', 'plugin', './public/js/tinymce/js/tinymce/plugins/insertdatetime/', 'min.js', 'insertdatetime', 1, 1, 'admin/archivos/shared_file/NYFeV8ZObHkBTcEW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1066, 'Iq036jlBf19sJAiO', 'layer', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/Iq036jlBf19sJAiO', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1067, 'y9Kqod5HGAjIeTiL', 'plugin', './public/js/tinymce/js/tinymce/plugins/layer/', 'min.js', 'layer', 1, 1, 'admin/archivos/shared_file/y9Kqod5HGAjIeTiL', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1068, '4tlL5jeX3VGvNgHS', 'legacyoutput', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/4tlL5jeX3VGvNgHS', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1069, 'wLWFa7sBnNStpOEq', 'plugin', './public/js/tinymce/js/tinymce/plugins/legacyoutput/', 'min.js', 'legacyoutput', 1, 1, 'admin/archivos/shared_file/wLWFa7sBnNStpOEq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1070, '4rCoqenLZXT1hMKE', 'link', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/4rCoqenLZXT1hMKE', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1071, '9ZCXIoAW8NVR5wDa', 'plugin', './public/js/tinymce/js/tinymce/plugins/link/', 'min.js', 'link', 1, 1, 'admin/archivos/shared_file/9ZCXIoAW8NVR5wDa', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1072, 'Pxu0mDrFIvBlif61', 'lists', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/Pxu0mDrFIvBlif61', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1073, 'IyDt6mqUeohV89pK', 'plugin', './public/js/tinymce/js/tinymce/plugins/lists/', 'min.js', 'lists', 1, 1, 'admin/archivos/shared_file/IyDt6mqUeohV89pK', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1074, 'nhAszyfrZu0v8VaK', 'media', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/nhAszyfrZu0v8VaK', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1075, '3OohUKuiP2mes8fA', 'moxieplayer', './public/js/tinymce/js/tinymce/plugins/media/', 'swf', 'media', 1, 1, 'admin/archivos/shared_file/3OohUKuiP2mes8fA', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1076, 'hilGMckvnVHRC38O', 'plugin', './public/js/tinymce/js/tinymce/plugins/media/', 'min.js', 'media', 1, 1, 'admin/archivos/shared_file/hilGMckvnVHRC38O', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1077, '9OSuPl2xHsi3BAJV', 'nonbreaking', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/9OSuPl2xHsi3BAJV', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1078, 'jf7vDTQmlJVFYw2W', 'plugin', './public/js/tinymce/js/tinymce/plugins/nonbreaking/', 'min.js', 'nonbreaking', 1, 1, 'admin/archivos/shared_file/jf7vDTQmlJVFYw2W', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1079, 'g7CwzMxIU4s1TZbh', 'noneditable', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/g7CwzMxIU4s1TZbh', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1080, 'Vewd36v14IFTUkm0', 'plugin', './public/js/tinymce/js/tinymce/plugins/noneditable/', 'min.js', 'noneditable', 1, 1, 'admin/archivos/shared_file/Vewd36v14IFTUkm0', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1081, 'TUxNKD5u6Lh9RqOj', 'pagebreak', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/TUxNKD5u6Lh9RqOj', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1082, 'Li2gYlrEmcetwvXZ', 'plugin', './public/js/tinymce/js/tinymce/plugins/pagebreak/', 'min.js', 'pagebreak', 1, 1, 'admin/archivos/shared_file/Li2gYlrEmcetwvXZ', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1083, 'WA7v5zrcBjyqdLf6', 'paste', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/WA7v5zrcBjyqdLf6', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1084, 'SXFq3OJp0WHctiB2', 'plugin', './public/js/tinymce/js/tinymce/plugins/paste/', 'min.js', 'paste', 1, 1, 'admin/archivos/shared_file/SXFq3OJp0WHctiB2', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1085, 'XUIGQT8HJghDc2rK', 'preview', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/XUIGQT8HJghDc2rK', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1086, '8wabDFveJX9MlQkq', 'plugin', './public/js/tinymce/js/tinymce/plugins/preview/', 'min.js', 'preview', 1, 1, 'admin/archivos/shared_file/8wabDFveJX9MlQkq', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1087, 'zcjMxgk87oulORLW', 'print', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/zcjMxgk87oulORLW', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1088, '2vDu0s3ZpOCUtbJ7', 'plugin', './public/js/tinymce/js/tinymce/plugins/print/', 'min.js', 'print', 1, 1, 'admin/archivos/shared_file/2vDu0s3ZpOCUtbJ7', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1089, 'DhCiouYajQZLp3nk', 'save', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/DhCiouYajQZLp3nk', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1090, 'rgcWqCHx0kINGE7T', 'plugin', './public/js/tinymce/js/tinymce/plugins/save/', 'min.js', 'save', 1, 1, 'admin/archivos/shared_file/rgcWqCHx0kINGE7T', 0, '2020-10-07 20:09:48', '2020-10-07 20:09:48', 1),
	(1091, 'F8aBoEYxVOlmwvyt', 'searchreplace', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/F8aBoEYxVOlmwvyt', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1092, 'fCinVDLEkgFBNxmj', 'plugin', './public/js/tinymce/js/tinymce/plugins/searchreplace/', 'min.js', 'searchreplace', 1, 1, 'admin/archivos/shared_file/fCinVDLEkgFBNxmj', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1093, 'ywBr3VsOblCMei54', 'spellchecker', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/ywBr3VsOblCMei54', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1094, 'UB5IHEOtcVvuzlhK', 'plugin', './public/js/tinymce/js/tinymce/plugins/spellchecker/', 'min.js', 'spellchecker', 1, 1, 'admin/archivos/shared_file/UB5IHEOtcVvuzlhK', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1095, '6tv9UuErLkWefNPc', 'tabfocus', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/6tv9UuErLkWefNPc', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1096, 'ITniqEAuw2kzM4CO', 'plugin', './public/js/tinymce/js/tinymce/plugins/tabfocus/', 'min.js', 'tabfocus', 1, 1, 'admin/archivos/shared_file/ITniqEAuw2kzM4CO', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1097, 'CZ7dpNF5RDlMeYWP', 'table', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/CZ7dpNF5RDlMeYWP', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1098, 'tcTV9HjYK4g3NBFy', 'plugin', './public/js/tinymce/js/tinymce/plugins/table/', 'min.js', 'table', 1, 1, 'admin/archivos/shared_file/tcTV9HjYK4g3NBFy', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1099, 'rsDwQOSkol56i3ej', 'template', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/rsDwQOSkol56i3ej', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1100, 'qnJEDdcoNRvQlKIZ', 'plugin', './public/js/tinymce/js/tinymce/plugins/template/', 'min.js', 'template', 1, 1, 'admin/archivos/shared_file/qnJEDdcoNRvQlKIZ', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1101, '8P05D2WEoIzS6T9g', 'textcolor', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/8P05D2WEoIzS6T9g', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1102, 'EusSkq36pICVZyd4', 'plugin', './public/js/tinymce/js/tinymce/plugins/textcolor/', 'min.js', 'textcolor', 1, 1, 'admin/archivos/shared_file/EusSkq36pICVZyd4', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1103, 'lVFhanPECR98Uo1G', 'textpattern', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/lVFhanPECR98Uo1G', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1104, 'TUsBcedtfbGvkRa7', 'plugin', './public/js/tinymce/js/tinymce/plugins/textpattern/', 'min.js', 'textpattern', 1, 1, 'admin/archivos/shared_file/TUsBcedtfbGvkRa7', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1105, 'FpVxlPA7CqvHnreK', 'visualblocks', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/FpVxlPA7CqvHnreK', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1106, 'rp84nylFjToJu3ks', 'css', './public/js/tinymce/js/tinymce/plugins/visualblocks/', 'folder', 'visualblocks', 1, 1, 'admin/archivos/shared_file/rp84nylFjToJu3ks', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1107, 'eR8zH94VJwad5QZI', 'visualblocks', './public/js/tinymce/js/tinymce/plugins/visualblocks/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/eR8zH94VJwad5QZI', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1108, 'eCsoTGJIDMEH9WNF', 'plugin', './public/js/tinymce/js/tinymce/plugins/visualblocks/', 'min.js', 'visualblocks', 1, 1, 'admin/archivos/shared_file/eCsoTGJIDMEH9WNF', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1109, 't0PwfGDyYTWgJK2m', 'visualchars', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/t0PwfGDyYTWgJK2m', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1110, 'HxS2zEmgbJUWqMOo', 'plugin', './public/js/tinymce/js/tinymce/plugins/visualchars/', 'min.js', 'visualchars', 1, 1, 'admin/archivos/shared_file/HxS2zEmgbJUWqMOo', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1111, 'lbiKZnVESwoN06k4', 'wordcount', './public/js/tinymce/js/tinymce/plugins/', 'folder', 'plugins', 1, 1, 'admin/archivos/shared_file/lbiKZnVESwoN06k4', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1112, 'eXMRn3wJPpDaEufz', 'plugin', './public/js/tinymce/js/tinymce/plugins/wordcount/', 'min.js', 'wordcount', 1, 1, 'admin/archivos/shared_file/eXMRn3wJPpDaEufz', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1113, 'U9hNJInRYo8mXcxu', 'skins', './public/js/tinymce/js/tinymce/', 'folder', 'tinymce', 1, 1, 'admin/archivos/shared_file/U9hNJInRYo8mXcxu', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1114, '71Vi2DGtT0Js4vrS', 'lightgray', './public/js/tinymce/js/tinymce/skins/', 'folder', 'skins', 1, 1, 'admin/archivos/shared_file/71Vi2DGtT0Js4vrS', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1115, 'NwWafkcChbRBpSDx', 'content', './public/js/tinymce/js/tinymce/skins/lightgray/', 'inline.min.css', 'lightgray', 1, 1, 'admin/archivos/shared_file/NwWafkcChbRBpSDx', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1116, 'togwEeFpcjZzQRYd', 'fonts', './public/js/tinymce/js/tinymce/skins/lightgray/', 'folder', 'lightgray', 1, 1, 'admin/archivos/shared_file/togwEeFpcjZzQRYd', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1117, 'GIXgZQbT2OaRFv7l', 'tinymce-small', './public/js/tinymce/js/tinymce/skins/lightgray/fonts/', 'eot', 'fonts', 1, 1, 'admin/archivos/shared_file/GIXgZQbT2OaRFv7l', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1118, 'fLM41zjtWZOSixXF', 'tinymce', './public/js/tinymce/js/tinymce/skins/lightgray/fonts/', 'eot', 'fonts', 1, 1, 'admin/archivos/shared_file/fLM41zjtWZOSixXF', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1119, 'fkg49EOdBGtIyWrP', 'img', './public/js/tinymce/js/tinymce/skins/lightgray/', 'folder', 'lightgray', 1, 1, 'admin/archivos/shared_file/fkg49EOdBGtIyWrP', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1120, 'VeCXFDiMGlLY6nO3', 'anchor', './public/js/tinymce/js/tinymce/skins/lightgray/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/VeCXFDiMGlLY6nO3', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1121, 'tnfVTY849OSoulq0', 'loader', './public/js/tinymce/js/tinymce/skins/lightgray/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/tnfVTY849OSoulq0', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1122, 's3xw9MA4umXlqrHg', 'object', './public/js/tinymce/js/tinymce/skins/lightgray/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/s3xw9MA4umXlqrHg', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1123, 'lpiA4YoaGrfVv5PO', 'trans', './public/js/tinymce/js/tinymce/skins/lightgray/img/', 'gif', 'img', 1, 1, 'admin/archivos/shared_file/lpiA4YoaGrfVv5PO', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1124, 'j30dMo18JarFE5hA', 'skin', './public/js/tinymce/js/tinymce/skins/lightgray/', 'ie7.min.css', 'lightgray', 1, 1, 'admin/archivos/shared_file/j30dMo18JarFE5hA', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1125, 'R8qZVnB2z5SLGiDj', 'themes', './public/js/tinymce/js/tinymce/', 'folder', 'tinymce', 1, 1, 'admin/archivos/shared_file/R8qZVnB2z5SLGiDj', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1126, 'k8fLwGhQjqBMtv5s', 'modern', './public/js/tinymce/js/tinymce/themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/k8fLwGhQjqBMtv5s', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1127, '0W7eSmkD8FIsaVML', 'theme', './public/js/tinymce/js/tinymce/themes/modern/', 'min.js', 'modern', 1, 1, 'admin/archivos/shared_file/0W7eSmkD8FIsaVML', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1128, 'ABxoKLp1W268DsOh', 'tinymce', './public/js/tinymce/js/tinymce/', 'min.js', 'tinymce', 1, 1, 'admin/archivos/shared_file/ABxoKLp1W268DsOh', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1129, 'lg6yGOqAhuEHPIx7', 'LICENSE', './public/js/tinymce/', 'TXT', 'tinymce', 1, 1, 'admin/archivos/shared_file/lg6yGOqAhuEHPIx7', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1130, 'mhoJ7Pl1sDGeM2Ia', 'validateForm', './public/js/', 'min.js', 'js', 1, 1, 'admin/archivos/shared_file/mhoJ7Pl1sDGeM2Ia', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1131, 'WqfSz79y6pGZVEtP', 'vue', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/WqfSz79y6pGZVEtP', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1132, 'kC2yZGM8gQfF4B7l', 'vue', './public/js/vue/', 'js', 'vue', 1, 1, 'admin/archivos/shared_file/kC2yZGM8gQfF4B7l', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1133, 'Ks4QVLRGP0kwCW5D', 'wysihtml5', './public/js/', 'folder', 'js', 1, 1, 'admin/archivos/shared_file/Ks4QVLRGP0kwCW5D', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1134, '7EyzV6KSC51YflkW', 'bootstrap3-wysihtml5', './public/js/wysihtml5/', 'all.js', 'wysihtml5', 1, 1, 'admin/archivos/shared_file/7EyzV6KSC51YflkW', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1135, '5tjZl30QYCTPA8uw', 'README', './', 'md', './', 1, 1, 'admin/archivos/shared_file/5tjZl30QYCTPA8uw', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1136, 'hgOcAH4NLJnWtyqP', 'service-worker', './', 'min.js', './', 1, 1, 'admin/archivos/shared_file/hgOcAH4NLJnWtyqP', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1137, '4PUGaFe9kWNm8Htn', 'Start CMS', './', 'postman_collection.json', './', 1, 1, 'admin/archivos/shared_file/4PUGaFe9kWNm8Htn', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1138, 'DUJgXid6cEf0FytO', 'Start CSM API', './', 'postman_collection.json', './', 1, 1, 'admin/archivos/shared_file/DUJgXid6cEf0FytO', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1139, '6er0kVA9FHt78B1h', 'themes', './', 'folder', './', 1, 1, 'admin/archivos/shared_file/6er0kVA9FHt78B1h', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1140, 'NyrE3VXUlY4uRLqI', 'awesomeTheme', './themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/NyrE3VXUlY4uRLqI', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1141, 'PA6OqLKkUfbE3r2Q', 'index', './themes/awesomeTheme/', 'html', 'awesomeTheme', 1, 1, 'admin/archivos/shared_file/PA6OqLKkUfbE3r2Q', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1142, 'doDTvKmVMLPrwf2Y', 'public', './themes/awesomeTheme/', 'folder', 'awesomeTheme', 1, 1, 'admin/archivos/shared_file/doDTvKmVMLPrwf2Y', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1143, 'GwW1HYvlSxUbJ02Q', 'css', './themes/awesomeTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/GwW1HYvlSxUbJ02Q', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1144, '28JyYMdxiC1amWFK', 'bootstrap', './themes/awesomeTheme/public/css/', 'min.css', 'css', 1, 1, 'admin/archivos/shared_file/28JyYMdxiC1amWFK', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1145, 'eFflwv0zubrns8mA', 'modern-business', './themes/awesomeTheme/public/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/eFflwv0zubrns8mA', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1146, 'aoSrHReUpEMfAZcb', 'font-awesome', './themes/awesomeTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/aoSrHReUpEMfAZcb', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1147, 'hNjG1UZ4HSgWI79D', 'css', './themes/awesomeTheme/public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/hNjG1UZ4HSgWI79D', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1148, 'P2YIh5Aclzp6GfC4', 'font-awesome', './themes/awesomeTheme/public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/P2YIh5Aclzp6GfC4', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1149, 'V8kEoPW3jAlSZMC1', 'fonts', './themes/awesomeTheme/public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/V8kEoPW3jAlSZMC1', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1150, 'NbwtmjMqlo4W7VE5', 'fontawesome-webfont', './themes/awesomeTheme/public/font-awesome/fonts/', 'eot', 'fonts', 1, 1, 'admin/archivos/shared_file/NbwtmjMqlo4W7VE5', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1151, 'kgUacxXQ6jDLYfSd', 'FontAwesome', './themes/awesomeTheme/public/font-awesome/fonts/', 'otf', 'fonts', 1, 1, 'admin/archivos/shared_file/kgUacxXQ6jDLYfSd', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1152, 'rDSyQNH7lT0MZa4s', 'js', './themes/awesomeTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/rDSyQNH7lT0MZa4s', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1153, 'KvWFuPjoMBDqEHy4', '0', './themes/awesomeTheme/public/js/', 'file', 'js', 1, 1, 'admin/archivos/shared_file/KvWFuPjoMBDqEHy4', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1154, '4TXIoC1R9VH3bOpQ', 'views', './themes/awesomeTheme/', 'folder', 'awesomeTheme', 1, 1, 'admin/archivos/shared_file/4TXIoC1R9VH3bOpQ', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1155, '58fZ1P9UABNepiI3', 'site', './themes/awesomeTheme/views/', 'folder', 'views', 1, 1, 'admin/archivos/shared_file/58fZ1P9UABNepiI3', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1156, '32E9MWSiCszhdIlJ', 'error404', './themes/awesomeTheme/views/site/', 'php', 'site', 1, 1, 'admin/archivos/shared_file/32E9MWSiCszhdIlJ', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1157, 'OY7Ctc5Ev8PmDfun', 'home', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/OY7Ctc5Ev8PmDfun', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1158, 'GZNb6DkWg1KnLlQY', 'layouts', './themes/awesomeTheme/views/site/', 'folder', 'site', 1, 1, 'admin/archivos/shared_file/GZNb6DkWg1KnLlQY', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1159, 'UuO8WiECtX1VZwa0', 'site', './themes/awesomeTheme/views/site/layouts/', 'blade.php', 'layouts', 1, 1, 'admin/archivos/shared_file/UuO8WiECtX1VZwa0', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1160, 'i5FghRqVkdJK7OcI', 'shared', './themes/awesomeTheme/views/site/', 'folder', 'site', 1, 1, 'admin/archivos/shared_file/i5FghRqVkdJK7OcI', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1161, '2YqKxuy46VzAb1rG', 'footer', './themes/awesomeTheme/views/site/shared/', 'blade.php', 'shared', 1, 1, 'admin/archivos/shared_file/2YqKxuy46VzAb1rG', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1162, 'h1iYgjkZvaPzcR4F', 'head', './themes/awesomeTheme/views/site/shared/', 'blade.php', 'shared', 1, 1, 'admin/archivos/shared_file/h1iYgjkZvaPzcR4F', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1163, 'NFhwKasPjbZlz4Li', 'navbar', './themes/awesomeTheme/views/site/shared/', 'blade.php', 'shared', 1, 1, 'admin/archivos/shared_file/NFhwKasPjbZlz4Li', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1164, 'hRQoGxcUHPSbj2Kd', 'template', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/hRQoGxcUHPSbj2Kd', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1165, '9mkw1UM3QAES0Nca', 'index', './themes/', 'html', 'themes', 1, 1, 'admin/archivos/shared_file/9mkw1UM3QAES0Nca', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1166, '6FUyeBQfz9NOAIDu', 'myGreatTheme', './themes/', 'folder', 'themes', 1, 1, 'admin/archivos/shared_file/6FUyeBQfz9NOAIDu', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1167, 'Xl0Pmj7ELSiIzgy2', 'cache', './themes/myGreatTheme/', 'folder', 'myGreatTheme', 1, 1, 'admin/archivos/shared_file/Xl0Pmj7ELSiIzgy2', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1168, 'edLj4YZU7unCSEiy', 'index', './themes/myGreatTheme/', 'html', 'myGreatTheme', 1, 1, 'admin/archivos/shared_file/edLj4YZU7unCSEiy', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1169, 'dPypet7M6RSHOU5T', 'public', './themes/myGreatTheme/', 'folder', 'myGreatTheme', 1, 1, 'admin/archivos/shared_file/dPypet7M6RSHOU5T', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1170, 'JqVz375Ehu2MC19Q', 'css', './themes/myGreatTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/JqVz375Ehu2MC19Q', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1171, '2VcRZxQlqT3phPd8', 'bootstrap', './themes/myGreatTheme/public/css/', 'min.css', 'css', 1, 1, 'admin/archivos/shared_file/2VcRZxQlqT3phPd8', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1172, 'j7BHLVR8kYMaxqe9', 'modern-business', './themes/myGreatTheme/public/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/j7BHLVR8kYMaxqe9', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1173, 'oJEMp7LdsTVfrzGP', 'font-awesome', './themes/myGreatTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/oJEMp7LdsTVfrzGP', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1174, 'k2VxhTD98UeOnZFP', 'css', './themes/myGreatTheme/public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/k2VxhTD98UeOnZFP', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1175, 'Lbl1NdHRf0Jhor62', 'font-awesome', './themes/myGreatTheme/public/font-awesome/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/Lbl1NdHRf0Jhor62', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1176, 'WfOK4iIXG1YcDPru', 'fonts', './themes/myGreatTheme/public/font-awesome/', 'folder', 'font-awesome', 1, 1, 'admin/archivos/shared_file/WfOK4iIXG1YcDPru', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1177, 'lKudcH8yp7ig6vnY', 'fontawesome-webfont', './themes/myGreatTheme/public/font-awesome/fonts/', 'eot', 'fonts', 1, 1, 'admin/archivos/shared_file/lKudcH8yp7ig6vnY', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1178, 'Dygns923e1CoIzaG', 'FontAwesome', './themes/myGreatTheme/public/font-awesome/fonts/', 'otf', 'fonts', 1, 1, 'admin/archivos/shared_file/Dygns923e1CoIzaG', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1179, '1sqwlgS9NKZkHJCo', 'js', './themes/myGreatTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/1sqwlgS9NKZkHJCo', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1180, 'UQO4cHg6hSKws8fV', '0', './themes/myGreatTheme/public/js/', 'file', 'js', 1, 1, 'admin/archivos/shared_file/UQO4cHg6hSKws8fV', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1181, 'aOW2mJPtGNZB9yVS', 'views', './themes/myGreatTheme/', 'folder', 'myGreatTheme', 1, 1, 'admin/archivos/shared_file/aOW2mJPtGNZB9yVS', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1182, '1gPqSYIp9aTylrV8', 'site', './themes/myGreatTheme/views/', 'folder', 'views', 1, 1, 'admin/archivos/shared_file/1gPqSYIp9aTylrV8', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1183, 'eOo16jFDy945EWtG', 'error404', './themes/myGreatTheme/views/site/', 'php', 'site', 1, 1, 'admin/archivos/shared_file/eOo16jFDy945EWtG', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1184, 'ru5DZgJT2UO0m3Yx', 'home', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/ru5DZgJT2UO0m3Yx', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1185, 'j6mv2CrKdX5RhTNf', 'layouts', './themes/myGreatTheme/views/site/', 'folder', 'site', 1, 1, 'admin/archivos/shared_file/j6mv2CrKdX5RhTNf', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1186, 'ZAJOKqW1BHNrzDgx', 'site', './themes/myGreatTheme/views/site/layouts/', 'blade.php', 'layouts', 1, 1, 'admin/archivos/shared_file/ZAJOKqW1BHNrzDgx', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1187, '65amL8sHqRrbDCfu', 'shared', './themes/myGreatTheme/views/site/', 'folder', 'site', 1, 1, 'admin/archivos/shared_file/65amL8sHqRrbDCfu', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1188, 'D09rORKzscGxwtYk', 'footer', './themes/myGreatTheme/views/site/shared/', 'blade.php', 'shared', 1, 1, 'admin/archivos/shared_file/D09rORKzscGxwtYk', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1189, 'XTLQdnwFaroWph9A', 'head', './themes/myGreatTheme/views/site/shared/', 'blade.php', 'shared', 1, 1, 'admin/archivos/shared_file/XTLQdnwFaroWph9A', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1190, '2FylguI73V6EzYCo', 'navbar', './themes/myGreatTheme/views/site/shared/', 'blade.php', 'shared', 1, 1, 'admin/archivos/shared_file/2FylguI73V6EzYCo', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1191, 'XYIOCkvth7b5qGuE', 'template', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/XYIOCkvth7b5qGuE', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1192, 'gbOoYWv710AzLEmh', 'trash', './', 'folder', './', 1, 1, 'admin/archivos/shared_file/gbOoYWv710AzLEmh', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1193, 'vD3ZUpl8gTKMfVcY', '0', './trash/', 'file', 'trash', 1, 1, 'admin/archivos/shared_file/vD3ZUpl8gTKMfVcY', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1194, 't5ivdxn0MzXjAIym', 'updater', './', 'php', './', 1, 1, 'admin/archivos/shared_file/t5ivdxn0MzXjAIym', 0, '2020-10-07 20:09:49', '2020-10-07 20:09:49', 1),
	(1196, 'e43D0jWZTCdslzS5', 'header', './public/css/admin/', 'min.css', 'admin', 1, 1, 'admin/archivos/shared_file/e43D0jWZTCdslzS5', 0, '2020-10-12 09:56:32', '2020-10-12 09:56:32', 1),
	(1197, '8CxfmisQrqbEDh9e', 'AlbumNewForm', './public/js/components/', 'min.js', 'components', 1, 1, 'admin/archivos/shared_file/8CxfmisQrqbEDh9e', 0, '2020-10-12 09:56:32', '2020-10-12 09:56:32', 1),
	(1203, 'fs3z1juYWFRqop5X', 'uploads', './', 'folder', './', 1, 1, 'admin/archivos/shared_file/fs3z1juYWFRqop5X', 0, '2020-10-25 16:10:51', '2020-10-25 13:06:11', 1),
	(1210, '3t5rPo2aElAc8wms', '01-vuejs', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/3t5rPo2aElAc8wms', 0, '2020-10-25 13:30:32', '2020-10-25 13:30:32', 1),
	(1211, 'kfReVAiF7wyEvgOZ', 'Flutter-Cover', './uploads/', 'png', 'uploads', 1, 1, 'admin/archivos/shared_file/kfReVAiF7wyEvgOZ', 0, '2020-10-25 13:41:48', '2020-10-25 13:41:48', 1),
	(1212, 'xe8InLXCicTtmS6s', 'Flutter-Cover', './public/img/', 'png', 'public', 1, 1, 'admin/archivos/shared_file/xe8InLXCicTtmS6s', 0, '2020-10-25 17:57:02', '2020-10-25 17:57:02', 1),
	(1214, 'H2vGQ4TYdOemgbIL', 'database', './backups/', 'folder', 'backups', 1, 1, 'admin/archivos/shared_file/H2vGQ4TYdOemgbIL', 0, '2020-10-25 17:57:02', '2020-10-25 17:57:02', 1),
	(1222, 'Z7DazviU4CGNd3Ax', 'changePassword', './public/js/components/', 'Component.min.js', 'components', 1, 1, 'admin/archivos/shared_file/Z7DazviU4CGNd3Ax', 0, '2020-11-11 12:17:19', '2020-11-11 12:17:19', 1),
	(1223, 'F9WL5ca0j7EPmw62', 'theme_info', './themes/awesomeTheme/', 'json', 'awesomeTheme', 1, 1, 'admin/archivos/shared_file/F9WL5ca0j7EPmw62', 0, '2020-11-11 12:17:19', '2020-11-11 12:17:19', 1),
	(1224, '81FTyGocwinXgRpJ', 'theme_info', './themes/myGreatTheme/', 'json', 'myGreatTheme', 1, 1, 'admin/archivos/shared_file/81FTyGocwinXgRpJ', 0, '2020-11-11 12:17:19', '2020-11-11 12:17:19', 1),
	(1225, 'borsC2VifUQyAemv', '20201111161717', './backups/database/', 'gz', 'database', 1, 1, 'admin/archivos/shared_file/borsC2VifUQyAemv', 0, '2020-11-11 12:17:19', '2020-11-11 12:17:19', 1),
	(1226, 'OLR8YZFMvumfXoed', 'img', './themes/awesomeTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/OLR8YZFMvumfXoed', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1227, '4fKdeVm73qj5rxHp', 'photo-2', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/4fKdeVm73qj5rxHp', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1228, 'iytBCek4MIjEQpXZ', 'photo-1', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/iytBCek4MIjEQpXZ', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1229, 'IltJ3DR06m9iWube', 'photo-3', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/IltJ3DR06m9iWube', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1230, 'psPCo1fnB38XlVKr', 'photo-700x450', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/psPCo1fnB38XlVKr', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1231, 'AtrGWTfiasX7ZgM4', 'photo-700x400', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/AtrGWTfiasX7ZgM4', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1232, 'f9ZQhtK3DBCr6dvz', 'photo-700x400-2', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/f9ZQhtK3DBCr6dvz', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1233, 'feEDrB1MaOlA7Yum', 'photo-700x400-3', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/feEDrB1MaOlA7Yum', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1234, 'TKV3gi8pYlRnWaHO', 'photo-4', './themes/awesomeTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/TKV3gi8pYlRnWaHO', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1235, 'hJIyru51ODft4aGp', 'siteWithoutFooter', './themes/awesomeTheme/views/site/layouts/', 'blade.php', 'layouts', 1, 1, 'admin/archivos/shared_file/hJIyru51ODft4aGp', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1236, 'yVDTb3XNkHfG6Qrs', 'templates', './themes/awesomeTheme/views/site/', 'folder', 'site', 1, 1, 'admin/archivos/shared_file/yVDTb3XNkHfG6Qrs', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1237, 'eKvzsA03hf4coO5m', 'sideBarTemplate', './themes/awesomeTheme/views/site/templates/', 'blade.php', 'templates', 1, 1, 'admin/archivos/shared_file/eKvzsA03hf4coO5m', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1238, 'AWQNgUPTMvzX45nL', 'template', './themes/awesomeTheme/views/site/templates/', 'blade.php', 'templates', 1, 1, 'admin/archivos/shared_file/AWQNgUPTMvzX45nL', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1239, 'l36dgsyqCbL0Ikv9', 'about', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/l36dgsyqCbL0Ikv9', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1240, 'Khe4HQ5AfTZJ0PRc', 'blogList', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/Khe4HQ5AfTZJ0PRc', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1241, 'bVyA2vd4lY3UhjMg', 'blogPost', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/bVyA2vd4lY3UhjMg', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1242, 'RLIreoUsJH8iupK0', 'contact', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/RLIreoUsJH8iupK0', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1243, '7vAhUI6iqytgCXbR', 'faq', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/7vAhUI6iqytgCXbR', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1244, 'capZT2wRhENO8eqF', 'fullwidth', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/capZT2wRhENO8eqF', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1245, 'inatEK7o1uXghCSe', 'portfolioItem', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/inatEK7o1uXghCSe', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1246, 'h6oc0nx8uHZMzrEP', 'portfolioList', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/h6oc0nx8uHZMzrEP', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1247, 'ZaoHpFw5n1APITix', 'pricing', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/ZaoHpFw5n1APITix', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1248, 'OHA4LmPRovrqKzGM', 'services', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/OHA4LmPRovrqKzGM', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1249, 'Zay302d9DrN7lVPk', 'sidebar', './themes/awesomeTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/Zay302d9DrN7lVPk', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1250, 'IhB3bwyHzC9Mg8Wm', 'theme_preview', './themes/awesomeTheme/', 'jpg', 'awesomeTheme', 1, 1, 'admin/archivos/shared_file/IhB3bwyHzC9Mg8Wm', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1251, 'W8InmSEYaJ3CVtwe', 'cache', './themes/awesomeTheme/', 'folder', 'awesomeTheme', 1, 1, 'admin/archivos/shared_file/W8InmSEYaJ3CVtwe', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1252, 'DtLMfARcZiO4n3Cq', 'materialize', './themes/myGreatTheme/public/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/DtLMfARcZiO4n3Cq', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1253, '7VUEAJyChaFLYOKW', 'style', './themes/myGreatTheme/public/css/', 'css', 'css', 1, 1, 'admin/archivos/shared_file/7VUEAJyChaFLYOKW', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1254, 'moIEgwOXi5Lf6zVc', 'init', './themes/myGreatTheme/public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/moIEgwOXi5Lf6zVc', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1255, 'HrzwqoyVETK5PM7p', 'materialize', './themes/myGreatTheme/public/js/', 'js', 'js', 1, 1, 'admin/archivos/shared_file/HrzwqoyVETK5PM7p', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1256, 'qnE9JaYDVGZOkMrS', 'img', './themes/myGreatTheme/public/', 'folder', 'public', 1, 1, 'admin/archivos/shared_file/qnE9JaYDVGZOkMrS', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1257, 'ZCi2XHmcJ7upoMqS', 'background1', './themes/myGreatTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/ZCi2XHmcJ7upoMqS', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1258, 'l5bKqsjQ9Z2McPCf', 'background2', './themes/myGreatTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/l5bKqsjQ9Z2McPCf', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1259, 'TH4B5iq8Itok12jN', 'background3', './themes/myGreatTheme/public/img/', 'jpg', 'img', 1, 1, 'admin/archivos/shared_file/TH4B5iq8Itok12jN', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1260, 'ZOP6QLwsecg19tD7', 'siteWithoutFooter', './themes/myGreatTheme/views/site/layouts/', 'blade.php', 'layouts', 1, 1, 'admin/archivos/shared_file/ZOP6QLwsecg19tD7', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1261, 'mCZB1x0AG4QeXskr', 'templates', './themes/myGreatTheme/views/site/', 'folder', 'site', 1, 1, 'admin/archivos/shared_file/mCZB1x0AG4QeXskr', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1262, '9YkeUgaGJ8MS3Wov', 'sideBarTemplate', './themes/myGreatTheme/views/site/templates/', 'blade.php', 'templates', 1, 1, 'admin/archivos/shared_file/9YkeUgaGJ8MS3Wov', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1263, 'HMPNDXa2olObTu5C', 'template', './themes/myGreatTheme/views/site/templates/', 'blade.php', 'templates', 1, 1, 'admin/archivos/shared_file/HMPNDXa2olObTu5C', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1264, 'AkNavYBcu2hUIqHQ', 'about', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/AkNavYBcu2hUIqHQ', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1265, 'MkyjeS2lVncfOvrX', 'blogList', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/MkyjeS2lVncfOvrX', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1266, 'kB9wbHJxa0XWs8c7', 'blogPost', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/kB9wbHJxa0XWs8c7', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1267, 'BTLeVMpZRmCXuw83', 'contact', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/BTLeVMpZRmCXuw83', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1268, 'zB1LclH4u89hMPaI', 'faq', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/zB1LclH4u89hMPaI', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1269, 'qGD35FIOnc0hB6Qb', 'fullwidth', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/qGD35FIOnc0hB6Qb', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1270, '6b8oxtkeZSKw4DJg', 'portfolioItem', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/6b8oxtkeZSKw4DJg', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1271, '4NSYuQHXFy87DOsh', 'portfolioList', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/4NSYuQHXFy87DOsh', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1272, 'eOnzZuBUKxNbCQaS', 'pricing', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/eOnzZuBUKxNbCQaS', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1273, 'GWe0YQ51UJLutqFo', 'services', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/GWe0YQ51UJLutqFo', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1274, 'Ld4aAnbBKlGzo8Rp', 'sidebar', './themes/myGreatTheme/views/site/', 'blade.php', 'site', 1, 1, 'admin/archivos/shared_file/Ld4aAnbBKlGzo8Rp', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1275, 'wFEtThjpVZWAn5NJ', 'theme_preview', './themes/myGreatTheme/', 'jpg', 'myGreatTheme', 1, 1, 'admin/archivos/shared_file/wFEtThjpVZWAn5NJ', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1277, 'UAr6MTFcYZsEqpQH', 'cache', './THEME_PATH/', 'folder', 'THEME_PATH', 1, 1, 'admin/archivos/shared_file/UAr6MTFcYZsEqpQH', 0, '2020-11-11 16:07:51', '2020-11-11 16:07:51', 1),
	(1278, 'l1Ru0IZatvWXmLdM', '20201111231922', './backups/database/', 'gz', 'database', 1, 1, 'admin/archivos/shared_file/l1Ru0IZatvWXmLdM', 0, '2020-11-12 13:31:14', '2020-11-12 13:31:14', 1),
	(1280, '2RiONtoe6hJd0aDC', 'Start CMS API', './', 'postman_collection.json', './', 1, 1, 'admin/archivos/shared_file/2RiONtoe6hJd0aDC', 0, '2020-11-12 13:31:14', '2020-11-12 13:31:14', 1),
	(1281, 'gtUdAYMQyk4TeRXJ', 'startcms_info', './', 'json', './', 1, 1, 'admin/archivos/shared_file/gtUdAYMQyk4TeRXJ', 0, '2020-11-12 13:31:14', '2020-11-12 13:31:14', 1),
	(1282, 'Xgxi4qFb2J8BIsWm', 'PVuKqgJQ_400x400', './public/img/profile/nestor/', 'png', 'nestor', 1, 1, 'admin/archivos/shared_file/Xgxi4qFb2J8BIsWm', 0, '2020-11-17 08:47:12', '2020-11-17 08:47:12', 1),
	(1283, 'p9wfAcJBHVOvk1Pn', 'IMG_3951', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/p9wfAcJBHVOvk1Pn', 0, '2020-11-25 13:18:54', '2020-11-25 21:57:19', 1),
	(1285, 'f3FTz7ZN9vpjus8G', 'photo-1547571031-4c1023b95da6', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/f3FTz7ZN9vpjus8G', 0, '2020-11-26 12:59:46', '2020-11-26 17:18:23', 1),
	(1286, 'O6EfBVS19Q5RZctF', 'photo-1550666253-d6649814e4bf', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/O6EfBVS19Q5RZctF', 0, '2020-11-26 13:26:02', '2020-11-26 13:26:02', 1),
	(1287, 'IYCzOuLqiBdVSmn5', 'photo-1585811514872-ca76aea3bf79', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/IYCzOuLqiBdVSmn5', 0, '2020-11-26 13:26:07', '2020-11-26 13:26:07', 1),
	(1288, 'pq2bca8QrmyvBwYk', 'diciembre-2018-materialize', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/pq2bca8QrmyvBwYk', 0, '2020-11-26 13:40:51', '2020-11-27 21:01:59', 1),
	(1289, 'rAEelgDShiL0IG42', '269638_08f4_3', './uploads/', 'jpg', 'uploads', 1, 1, 'admin/archivos/shared_file/rAEelgDShiL0IG42', 0, '2020-11-26 19:05:21', '2020-11-27 20:57:52', 1);
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
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`form_content_id`),
  KEY `form_custom_id` (`form_custom_id`),
  CONSTRAINT `FK_form_content_form_custom` FOREIGN KEY (`form_custom_id`) REFERENCES `form_custom` (`form_custom_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.form_content: ~0 rows (aproximadamente)
DELETE FROM `form_content`;
/*!40000 ALTER TABLE `form_content` DISABLE KEYS */;
INSERT INTO `form_content` (`form_content_id`, `form_custom_id`, `form_tab_id`, `user_id`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(40, 22, 47, 1, 1, '2020-11-16 11:03:23', '2020-11-18 09:11:05', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.form_content_data: ~2 rows (aproximadamente)
DELETE FROM `form_content_data`;
/*!40000 ALTER TABLE `form_content_data` DISABLE KEYS */;
INSERT INTO `form_content_data` (`form_custom_data_id`, `form_content_id`, `form_field_id`, `form_value`, `date_create`, `date_update`, `status`) VALUES
	(61, 40, 55, '{"title":"Basic Card"}', '2020-11-16 11:03:23', '2020-11-16 11:03:23', 1),
	(62, 40, 56, '{"text":"I am a very simple card. I am good at containing small bits of information. I am convenient because I require little markup to use effectively."}', '2020-11-16 11:03:23', '2020-11-16 11:03:23', 1);
/*!40000 ALTER TABLE `form_content_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.form_custom
CREATE TABLE IF NOT EXISTS `form_custom` (
  `form_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(250) DEFAULT NULL,
  `form_description` varchar(250) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `date_create` timestamp NULL DEFAULT current_timestamp(),
  `date_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`form_custom_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_custom: ~1 rows (aproximadamente)
DELETE FROM `form_custom`;
/*!40000 ALTER TABLE `form_custom` DISABLE KEYS */;
INSERT INTO `form_custom` (`form_custom_id`, `form_name`, `form_description`, `user_id`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(22, 'Card', 'Cards are a convenient means of displaying content composed of different types of objects. They’re also well-suited for presenting similar objects whose size or supported actions can vary considerably, like photos with captions of variable length.', 1, 1, '2020-11-16 11:02:33', '2020-11-16 11:02:53', NULL),
	(24, 'Nuevo Formulario', '', 1, 0, '2020-11-18 08:59:23', '2020-11-18 08:59:23', NULL);
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
  KEY `form_tab_id` (`form_tab_id`),
  CONSTRAINT `FK_form_fields_form_tabs` FOREIGN KEY (`form_tab_id`) REFERENCES `form_tabs` (`form_tab_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_fields: ~2 rows (aproximadamente)
DELETE FROM `form_fields`;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;
INSERT INTO `form_fields` (`form_field_id`, `form_tab_id`, `field_name`, `displayName`, `icon`, `component`, `date_create`, `date_update`, `status`) VALUES
	(55, 47, 'title', 'Titulo', 'format_color_text', 'formFieldTitle', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(56, 47, 'text', 'Texto', 'short_text', 'formFieldTextArea', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(58, 49, 'title', 'Titulo', 'format_color_text', 'formFieldTitle', '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1);
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
  KEY `form_id` (`form_field_id`),
  CONSTRAINT `FK_form_fields_data_form_fields` FOREIGN KEY (`form_field_id`) REFERENCES `form_fields` (`form_field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.form_fields_data: ~12 rows (aproximadamente)
DELETE FROM `form_fields_data`;
/*!40000 ALTER TABLE `form_fields_data` DISABLE KEYS */;
INSERT INTO `form_fields_data` (`form_field_config_id`, `form_field_id`, `_key`, `_value`, `date_create`, `date_update`, `status`) VALUES
	(273, 55, 'fieldPlaceholder', 'Card Title', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(274, 55, 'fieldID', 'ZRswElNHjm', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(275, 55, 'fieldName', 'card_title', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(276, 55, 'fielApiID', 'card_title', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(277, 55, 'form_custom_data_id', NULL, '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(278, 56, 'fieldID', '9MFrM65Upr', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(279, 56, 'fieldName', 'Card content', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(280, 56, 'fielApiID', 'card_content', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(281, 56, 'form_custom_data_id', NULL, '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(287, 58, 'fieldPlaceholder', '', '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1),
	(288, 58, 'fieldID', 'bq5Qj5O0fV', '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1),
	(289, 58, 'fieldName', '', '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1),
	(290, 58, 'fielApiID', '', '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1),
	(291, 58, 'form_custom_data_id', NULL, '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Volcando datos para la tabla start_cms.form_tabs: ~1 rows (aproximadamente)
DELETE FROM `form_tabs`;
/*!40000 ALTER TABLE `form_tabs` DISABLE KEYS */;
INSERT INTO `form_tabs` (`form_tab_id`, `form_custom_id`, `tab_name`, `date_create`, `date_update`, `status`) VALUES
	(47, 22, 'Tab 1', '2020-11-16 11:02:33', '2020-11-16 11:02:33', 1),
	(49, 24, 'Tab 1', '2020-11-18 08:59:23', '2020-11-18 08:59:23', 1);
/*!40000 ALTER TABLE `form_tabs` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.logger
CREATE TABLE IF NOT EXISTS `logger` (
  `logger_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type_id` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`logger_id`),
  KEY `user_id` (`user_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- Volcando datos para la tabla start_cms.logger: ~23 rows (aproximadamente)
DELETE FROM `logger`;
/*!40000 ALTER TABLE `logger` DISABLE KEYS */;
INSERT INTO `logger` (`logger_id`, `user_id`, `type_id`, `type`, `token`, `comment`, `date_create`, `date_update`, `status`) VALUES
	(2, 1, 104, 'pages', 'updated', 'A page has been updated', '2020-11-26 22:17:03', '2020-11-26 18:17:03', 1),
	(3, 1, 104, 'pages', 'updated', 'A page has been updated', '2020-11-26 22:53:34', '2020-11-26 18:53:34', 1),
	(4, 1, 102, 'pages', 'deleted', 'A page has been deleted', '2020-11-26 23:03:44', '2020-11-26 19:03:44', 1),
	(5, 1, 100, 'pages', 'deleted', 'A page has been deleted', '2020-11-26 23:03:51', '2020-11-26 19:03:52', 1),
	(6, 1, 105, 'pages', 'created', 'A page has been created', '2020-11-26 23:04:16', '2020-11-26 19:04:16', 1),
	(7, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:07:44', '2020-11-26 19:07:44', 1),
	(8, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:07:54', '2020-11-26 19:07:54', 1),
	(9, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:08:04', '2020-11-26 19:08:04', 1),
	(10, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:08:17', '2020-11-26 19:08:17', 1),
	(11, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:09:39', '2020-11-26 19:09:39', 1),
	(12, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:10:25', '2020-11-26 19:10:25', 1),
	(13, 1, 105, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:12:18', '2020-11-26 19:12:18', 1),
	(14, 1, 102, 'pages', 'updated', 'A page has been updated', '2020-11-26 23:13:12', '2020-11-26 19:13:12', 1),
	(15, 1, 106, 'pages', 'created', 'A page has been created', '2020-12-01 20:01:03', '2020-12-01 16:01:03', 1),
	(16, 1, 106, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:02:49', '2020-12-01 16:02:49', 1),
	(17, 1, 106, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:07:19', '2020-12-01 16:07:19', 1),
	(18, 1, 107, 'pages', 'created', 'A page has been created', '2020-12-01 20:09:08', '2020-12-01 16:09:08', 1),
	(19, 1, 107, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:09:48', '2020-12-01 16:09:48', 1),
	(20, 1, 108, 'pages', 'created', 'A page has been created', '2020-12-01 20:12:02', '2020-12-01 16:12:02', 1),
	(21, 1, 108, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:12:59', '2020-12-01 16:12:59', 1),
	(22, 1, 108, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:13:17', '2020-12-01 16:13:17', 1),
	(23, 1, 108, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:13:37', '2020-12-01 16:13:37', 1),
	(24, 1, 109, 'pages', 'created', 'A page has been created', '2020-12-01 20:16:41', '2020-12-01 16:16:41', 1),
	(25, 1, 110, 'pages', 'created', 'A page has been created', '2020-12-01 20:27:50', '2020-12-01 16:27:50', 1),
	(26, 1, 110, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:27:54', '2020-12-01 16:27:54', 1),
	(27, 1, 110, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:28:10', '2020-12-01 16:28:10', 1),
	(28, 1, 110, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:28:20', '2020-12-01 16:28:20', 1),
	(29, 1, 110, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:28:47', '2020-12-01 16:28:47', 1),
	(30, 1, 111, 'pages', 'created', 'A page has been created', '2020-12-01 20:29:34', '2020-12-01 16:29:34', 1),
	(31, 1, 111, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:29:59', '2020-12-01 16:29:59', 1),
	(32, 1, 112, 'pages', 'created', 'A page has been created', '2020-12-01 20:30:30', '2020-12-01 16:30:30', 1),
	(33, 1, 112, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:30:34', '2020-12-01 16:30:34', 1),
	(34, 1, 112, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:30:37', '2020-12-01 16:30:37', 1),
	(35, 1, 112, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:31:13', '2020-12-01 16:31:13', 1),
	(36, 1, 112, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:31:31', '2020-12-01 16:31:31', 1),
	(37, 1, 112, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:32:34', '2020-12-01 16:32:34', 1),
	(38, 1, 113, 'pages', 'created', 'A page has been created', '2020-12-01 20:34:57', '2020-12-01 16:34:57', 1),
	(39, 1, 113, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:35:30', '2020-12-01 16:35:30', 1),
	(40, 1, 114, 'pages', 'created', 'A page has been created', '2020-12-01 20:36:15', '2020-12-01 16:36:15', 1),
	(41, 1, 114, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:36:23', '2020-12-01 16:36:23', 1),
	(42, 1, 114, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:37:32', '2020-12-01 16:37:32', 1),
	(43, 1, 114, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:37:49', '2020-12-01 16:37:49', 1),
	(44, 1, 114, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:38:23', '2020-12-01 16:38:23', 1),
	(45, 1, 102, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:38:45', '2020-12-01 16:38:45', 1),
	(46, 1, 113, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:39:08', '2020-12-01 16:39:08', 1),
	(47, 1, 113, 'pages', 'updated', 'A page has been updated', '2020-12-01 20:39:16', '2020-12-01 16:39:16', 1);
/*!40000 ALTER TABLE `logger` ENABLE KEYS */;

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

-- Volcando estructura para tabla start_cms.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `template` varchar(600) NOT NULL,
  `position` varchar(600) DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_delete` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`menu_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COMMENT='Menus del sistema';

-- Volcando datos para la tabla start_cms.menu: ~3 rows (aproximadamente)
DELETE FROM `menu`;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` (`menu_id`, `user_id`, `name`, `template`, `position`, `date_create`, `date_update`, `date_delete`, `status`) VALUES
	(1, 1, 'top_nav', 'menu', '', '2020-12-01 20:46:29', '2020-11-13 11:42:27', '2020-11-13 11:18:24', 1),
	(39, 1, 'top_nav1', 'menu', NULL, '2020-11-14 00:34:26', '2020-11-16 10:48:09', '2020-11-16 14:50:38', 0),
	(40, 1, 'top_nav2', 'menu', NULL, '2020-11-14 00:34:26', '2020-11-16 10:48:10', '2020-11-16 14:49:12', 0);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.menu_items
CREATE TABLE IF NOT EXISTS `menu_items` (
  `menu_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL DEFAULT 0,
  `menu_item_parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) DEFAULT 0,
  `model_id` int(11) DEFAULT NULL,
  `model` varchar(250) DEFAULT NULL,
  `item_type` varchar(250) DEFAULT NULL,
  `item_name` varchar(250) DEFAULT NULL,
  `item_label` varchar(250) DEFAULT NULL,
  `item_link` varchar(600) DEFAULT NULL,
  `item_title` varchar(250) DEFAULT NULL,
  `item_target` varchar(250) DEFAULT NULL,
  `date_publish` datetime NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`menu_item_id`) USING BTREE,
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `FK_menu_items_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=latin1 COMMENT='Menus del sistema';

-- Volcando datos para la tabla start_cms.menu_items: ~6 rows (aproximadamente)
DELETE FROM `menu_items`;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` (`menu_item_id`, `menu_id`, `menu_item_parent_id`, `order`, `model_id`, `model`, `item_type`, `item_name`, `item_label`, `item_link`, `item_title`, `item_target`, `date_publish`, `date_create`, `date_update`, `status`) VALUES
	(236, 1, 0, 0, 106, 'page', 'static_link', 'about-rau', 'About', 'http://localhost:8000/about', 'About', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(237, 1, 0, 1, 107, 'page', 'static_link', 'services-xaw', 'Services', 'http://localhost:8000/services', 'Services', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(238, 1, 0, 2, 108, 'page', 'static_link', 'contact-6gr', 'Contact ', 'http://localhost:8000/contact', 'Contact ', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(239, 1, 0, 3, 0, '', 'static_link', 'blog', 'Latest Blog', '/blog', 'Blog List', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(240, 1, 0, 4, 113, 'page', 'static_link', 'portfolio-vru', 'Portfolio', 'http://localhost:8000/portfolio', 'Portfolio', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(241, 1, 240, 0, 0, '', 'static_link', 'single-portfolio', 'Single Portfolio Item', '/portfolio-item', 'Portfolio Item', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(242, 1, 0, 5, 0, '', 'static_link', 'others-pages', 'Other Pages', '#!', 'Other Pages', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(243, 1, 242, 0, 0, '', 'static_link', 'full-width', 'Full Width', '/full-width', 'Full Width', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(244, 1, 242, 1, 0, '', 'static_link', 'faq', 'FAQ', '/frequently-asked-questions', 'FAQ', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(245, 1, 242, 2, 0, '', 'static_link', 'sidebar', 'Sidebar Page', '/sidebar', 'Sidebar Page', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(246, 1, 242, 3, 0, '', 'static_link', '404', '404', '/404', '404', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1),
	(247, 1, 242, 4, 0, '', 'static_link', 'pricing', 'Pricing Table', '/pricing', 'Pricing Table', '_self', '2020-11-13 11:25:05', '2020-11-13 11:25:06', '2020-12-01 16:46:29', 1);
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;

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
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`page_id`),
  KEY `author` (`user_id`) USING BTREE,
  KEY `type` (`page_type_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.page: ~9 rows (aproximadamente)
DELETE FROM `page`;
/*!40000 ALTER TABLE `page` DISABLE KEYS */;
INSERT INTO `page` (`page_id`, `path`, `template`, `title`, `subtitle`, `content`, `page_type_id`, `user_id`, `visibility`, `categorie_id`, `subcategorie_id`, `status`, `layout`, `mainImage`, `date_publish`, `date_update`, `date_create`, `date_delete`) VALUES
	(100, 'blade-templates', 'default', 'Blade Templates', '', '<!-- wp:paragraph -->\n<p>Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. In fact, all Blade views are compiled into plain PHP code and cached until they are modified, meaning Blade adds essentially zero overhead to your application. Blade view files use the&nbsp;<code>.blade.php</code>&nbsp;file extension and are typically stored in the&nbsp;<code>resources/views</code>&nbsp;directory.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->', 1, 1, 1, 0, 0, 0, 'default', NULL, '2020-11-26 17:46:24', '2020-11-16 10:55:53', '2020-11-26 17:46:24', '2020-11-26 23:03:51'),
	(102, 'typescript-the-best-resources-to-learn-it', 'default', 'TypeScript: The Best Resources to Learn It', '', 'Content of the page...', 1, 1, 1, 0, 0, 2, 'default', NULL, '2020-12-01 20:38:45', NULL, '2020-12-01 20:38:45', '2020-11-26 23:03:44'),
	(103, 'blog/que-nos-aporta-materialize-css-en-desarrollo-web', 'sideBarTemplate', '¿Qué nos aporta Materialize CSS en Desarrollo Web?', '', '<!-- wp:paragraph -->\n<p>\n\nComenzamos año en este blog analizando un recurso para desarrolladores web que está dando mucho de qué hablar:&nbsp;<a href="https://materializecss.com/" target="_blank" rel="noreferrer noopener">Materialize CSS</a>.&nbsp;<strong>Concebido especialmente para proyectos que hagan del&nbsp; Material Design su bandera, este framework CSS nos permite ahorrar mucho tiempo y esfuerzo al implementar y optimizar nuestros proyectos web.</strong>&nbsp;No sólo contiene multitud de clases CSS ya configuradas,&nbsp; además incorpora código JavaScript para añadir a nuestras interfaces. En este artículo analizamos sus ventajas e inconvenientes, lo comparamos con otras alternativas, como&nbsp; Bootstrap o Fundation y damos nuestros primeros pasos prácticos con&nbsp;Materialize CSS.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Por un lado,<strong>&nbsp;las ventajas de Materialize CSS</strong>:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:list -->\n<ul><li>El tiempo de desarrollo es menor, pues la mayor parte del código nos lo encontramos ya escrito.</li><li>Si no somos muy hábiles en el diseño podemos aplicar un framework CSS para conseguir una bonita estética en nuestros proyectos.</li><li>Los diseños son más robustos y la estética final es más homogénea.</li><li>Materialize sólo ocupa aproximadamente unos 140 KB con su CSS, a lo que hay que añadirle 180 KB con el JS.</li></ul>\n<!-- /wp:list -->\n\n<!-- wp:paragraph -->\n<p>Y, como todo tiene su lado negativo,<strong>&nbsp;algunos de sus inconvenientes:</strong></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:list -->\n<ul><li>Tal vez se cuela más código del que vas a utilizar, aunque siempre podrás quitar lo que no te interese.</li><li>Si alguna vez quieres cambiar de framework su adaptación será más liosa.</li><li>El diseño que realizas no es del todo exclusivo y podrás ver, casi con seguridad, algo parecido en otros sitios.</li></ul>\n<!-- /wp:list -->\n\n<!-- wp:heading -->\n<h2>Materialize vs otros frameworks</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Otras alternativas bastante conocidas a Materialize CSS son&nbsp;<a href="https://www.arsys.es/blog/programacion/diseno-web/bootstrap-responsive/">Bootstrap</a>&nbsp;o&nbsp;<a href="https://foundation.zurb.com/" target="_blank" rel="noreferrer noopener">Fundation</a>&nbsp;pero ¿por qué Materialize CSS es mejor? En pocas palabras:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:list -->\n<ul><li>No requiere jQuery para funcionar.</li><li>Usa desde el principio recomendaciones CSS que otros frameworks aún no implementan (o que han tardado bastante tiempo en incorporar).</li><li>Al usar Material Design de Google, los usuarios están más familiarizados.</li></ul>\n<!-- /wp:list -->\n\n<!-- wp:heading -->\n<h2><strong>Primeros pasos con Materialize CSS</strong></h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Hemos de incluir los archivos con la hoja de estilos y el JavaScript. En caso de hacerlo mediante el CDN incluiremos un par de líneas de código en nuestra página.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:preformatted -->\n<pre class="wp-block-preformatted">&lt;link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css"&gt;\n&lt;script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"&gt;&lt;/script&gt;</pre>\n<!-- /wp:preformatted -->\n\n<!-- wp:paragraph -->\n<p>Si lo hacemos con CDN tenemos la ventaja de que nos ahorra la descarga de los archivos desde nuestro servidor y la respuesta suele ser más rápida.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Para hacer un botón estilizado, usaremos una etiqueta&nbsp;<em>a</em>, con unas clases CSS determinadas.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:preformatted -->\n<pre class="wp-block-preformatted">&lt;a class="waves-effect waves-light btn"&gt;button&lt;/a&gt;</pre>\n<!-- /wp:preformatted -->\n\n<!-- wp:paragraph -->\n<p>Y si queremos hacer un icono, podremos usar el siguiente código:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:preformatted -->\n<pre class="wp-block-preformatted">&lt;i class="material-icons prefix"&gt;add_a_photo&lt;/i&gt;</pre>\n<!-- /wp:preformatted -->\n\n<!-- wp:paragraph -->\n<p>Para algo más complejo, como por ejemplo hacer una tarjeta, podemos aplicar algunas clases CSS.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:preformatted -->\n<pre class="wp-block-preformatted">&lt;div class="row"&gt;\n &nbsp; &lt;div class="col s12 m7"&gt;\n &nbsp;&nbsp;&nbsp; &lt;div class="card"&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;div class="card-image"&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;img src="arsys-backend.png"&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;span class="card-title"&gt;Visita Arsys&lt;/span&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;/div&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;div class="card-content"&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;p&gt;Con Arsys, podrás aprender a cómo iniciarte en Materalize CSS.&lt;/p&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;/div&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;div class="card-action"&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;a href="#"&gt;Arsys&lt;/a&gt;\n &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;/div&gt;\n &nbsp;&nbsp;&nbsp; &lt;/div&gt;\n &nbsp;&nbsp;&lt;/div&gt;\n &lt;/div&gt;</pre>\n<!-- /wp:preformatted -->', 2, 1, 1, 0, 0, 2, 'default', 1288, '2020-11-26 17:43:48', NULL, '2020-11-26 17:43:48', NULL),
	(104, 'materialize-css', 'default', 'Materialize css', '', '<!-- wp:heading -->\n<h2>Material Design</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Created and designed by Google, Material Design is a design language that combines the classic principles of successful design along with innovation and technology. Google\'s goal is to develop a system of design that allows for a unified user experience across all their products on any platform.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading {"level":3} -->\n<h3>Principles</h3>\n<!-- /wp:heading -->\n\n<!-- wp:image {"align":"left","width":295,"height":295} -->\n<div class="wp-block-image"><figure class="alignleft is-resized"><img src="https://materializecss.com/images/metaphor.png" alt="" width="295" height="295"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:heading {"level":4} -->\n<h4>Material is the metaphor</h4>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>The metaphor of material defines the relationship between space and motion. The idea is that the technology is inspired by paper and ink and is utilized to facilitate creativity and innovation. Surfaces and edges provide familiar visual cues that allow users to quickly understand the technology beyond the physical world.<br></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:spacer {"height":167} -->\n<div style="height:167px" aria-hidden="true" class="wp-block-spacer"></div>\n<!-- /wp:spacer -->\n\n<!-- wp:image {"align":"left","width":301,"height":302} -->\n<div class="wp-block-image"><figure class="alignleft is-resized"><img src="https://materializecss.com/images/bold.png" alt="" width="301" height="302"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:heading {"level":4} -->\n<h4>Bold, graphic, intentional</h4>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Elements and components such as grids, typography, color, and imagery are not only visually pleasing, but also create a sense of hierarchy, meaning, and focus. Emphasis on different actions and components create a visual guide for users.<br></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:spacer {"height":168} -->\n<div style="height:168px" aria-hidden="true" class="wp-block-spacer"></div>\n<!-- /wp:spacer -->\n\n<!-- wp:image {"align":"left","width":309,"height":308} -->\n<div class="wp-block-image"><figure class="alignleft is-resized"><img src="https://materializecss.com/images/motion.png" alt="" width="309" height="308"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:heading {"level":4} -->\n<h4>Motion provides meaning</h4>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>Motion allows the user to draw a parallel between what they see on the screen and in real life. By providing both feedback and familiarity, this allows the user to fully immerse him or herself into unfamiliar technology. Motion contains consistency and continuity in addition to giving users additional subconscious information about objects and transformations.</p>\n<!-- /wp:paragraph -->', 1, 1, 1, 0, 0, 1, 'default', 1288, '2020-11-26 22:53:34', NULL, '2020-11-26 22:53:34', NULL),
	(105, 'blog/codeigniter', 'sideBarTemplate', 'CodeIgniter', '', '<!-- wp:paragraph -->\n<p><strong>CodeIgniter</strong> is an <a href="https://en.wikipedia.org/wiki/Open-source_software">open-source software</a> rapid development <a href="https://en.wikipedia.org/wiki/Web_framework">web framework</a>, for use in building dynamic web sites with <a href="https://en.wikipedia.org/wiki/PHP">PHP</a>.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>Popularity</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>CodeIgniter is loosely based on the popular <a href="https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">model–view–controller</a> (MVC) development pattern. While controller classes are a necessary part of development under CodeIgniter, models and views are optional. CodeIgniter can be also modified to use Hierarchical Model View Controller (HMVC) which allows the developers to maintain modular grouping of Controller, Models and View arranged in a sub-directory format.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>CodeIgniter is most often noted for its speed when compared to other PHP frameworks.n a critical take on PHP frameworks in general, PHP creator <a href="https://en.wikipedia.org/wiki/Rasmus_Lerdorf">Rasmus Lerdorf</a> spoke at <a href="https://en.wikipedia.org/wiki/Bonn-Rhein-Sieg_University_of_Applied_Sciences_(BRSU)#FrOSCon">frOSCon</a> in August 2008, noting that he liked CodeIgniter "<em>because it is faster, lighter and the least like a framework.</em></p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading -->\n<h2>Source code and license</h2>\n<!-- /wp:heading -->\n\n<!-- wp:image {"className":"img-fluid rounded"} -->\n<figure class="wp-block-image img-fluid rounded"><img src="https://www.mindfiresolutions.com/blog/wp-content/uploads/Pros-and-Cons-of-CodeIgniter-Framework.jpg" alt="Pros and Cons of CodeIgniter Framework | Blogs @ Mindfire Solutions"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:heading -->\n<h2>History</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>The first public version of CodeIgniter was released by <a href="https://en.wikipedia.org/wiki/EllisLab">EllisLab</a> on February 28, 2006.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>On July 9, 2013, <a href="https://en.wikipedia.org/wiki/EllisLab">EllisLab</a> announced that it was seeking a new owner for CodeIgniter, citing a lack of resources to give the framework the attention they felt it deserved.On October 6, 2014, EllisLab announced that CodeIgniter would continue development under the stewardship of the <a href="https://en.wikipedia.org/wiki/British_Columbia_Institute_of_Technology">British Columbia Institute of Technology</a>.As of October 23, 2019, with CodeIgniter Foundation taken the mantle, CodeIgniter no longer under foster care of <a href="https://en.wikipedia.org/wiki/British_Columbia_Institute_of_Technology">British Columbia Institute of Technology</a>.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>Codeigniter 4 was released On February 24, 2020, birthday of Jim Parry who was the project lead of Codeigniter 4 and died on January 15, 2020. After that, the project continues until today with other project lead.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p><br></p>\n<!-- /wp:paragraph -->', 2, 1, 1, 0, 0, 1, 'default', 1289, '2020-11-26 23:12:18', NULL, '2020-11-26 23:12:18', NULL),
	(106, 'about', 'default', 'About', 'About Us', '<!-- wp:html -->\n<ol class="breadcrumb">\n <li class="breadcrumb-item">\n <a href="index.html">Home</a>\n </li>\n <li class="breadcrumb-item active">About</li>\n </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="row">\n      <div class="col-lg-6">\n        <img class="img-fluid rounded mb-4" src="http://placehold.it/750x450" alt="">\n      </div>\n      <div class="col-lg-6">\n        <h2>About Modern Business</h2>\n        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sed voluptate nihil eum consectetur similique? Consectetur, quod, incidunt, harum nisi dolores delectus reprehenderit voluptatem perferendis dicta dolorem non blanditiis ex fugiat.</p>\n        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe, magni, aperiam vitae illum voluptatum aut sequi impedit non velit ab ea pariatur sint quidem corporis eveniet. Odit, temporibus reprehenderit dolorum!</p>\n        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Et, consequuntur, modi mollitia corporis ipsa voluptate corrupti eum ratione ex ea praesentium quibusdam? Aut, in eum facere corrupti necessitatibus perspiciatis quis?</p>\n      </div>\n    </div>\n<!-- /wp:html -->\n\n<!-- wp:heading -->\n<h2>Our Team</h2>\n<!-- /wp:heading -->\n\n<!-- wp:html -->\n<div class="row">\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100 text-center">\n          <img class="card-img-top" src="http://placehold.it/750x450" alt="">\n          <div class="card-body">\n            <h4 class="card-title">Team Member</h4>\n            <h6 class="card-subtitle mb-2 text-muted">Position</h6>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>\n          </div>\n          <div class="card-footer">\n            <a href="#">name@example.com</a>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100 text-center">\n          <img class="card-img-top" src="http://placehold.it/750x450" alt="">\n          <div class="card-body">\n            <h4 class="card-title">Team Member</h4>\n            <h6 class="card-subtitle mb-2 text-muted">Position</h6>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>\n          </div>\n          <div class="card-footer">\n            <a href="#">name@example.com</a>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100 text-center">\n          <img class="card-img-top" src="http://placehold.it/750x450" alt="">\n          <div class="card-body">\n            <h4 class="card-title">Team Member</h4>\n            <h6 class="card-subtitle mb-2 text-muted">Position</h6>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Possimus aut mollitia eum ipsum fugiat odio officiis odit.</p>\n          </div>\n          <div class="card-footer">\n            <a href="#">name@example.com</a>\n          </div>\n        </div>\n      </div>\n    </div>\n<!-- /wp:html -->\n\n<!-- wp:heading -->\n<h2>Our Customers</h2>\n<!-- /wp:heading -->\n\n<!-- wp:html -->\n<div class="row">\n      <div class="col-lg-2 col-sm-4 mb-4">\n        <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n      </div>\n      <div class="col-lg-2 col-sm-4 mb-4">\n        <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n      </div>\n      <div class="col-lg-2 col-sm-4 mb-4">\n        <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n      </div>\n      <div class="col-lg-2 col-sm-4 mb-4">\n        <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n      </div>\n      <div class="col-lg-2 col-sm-4 mb-4">\n        <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n      </div>\n      <div class="col-lg-2 col-sm-4 mb-4">\n        <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n      </div>\n    </div>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:07:19', NULL, '2020-12-01 20:07:19', NULL),
	(107, 'services', 'default', 'Services', 'Our Services', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">Services</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:image {"id":9} -->\n<figure class="wp-block-image"><img src="http://localhost:8000//./themes/awesomeTheme/public/img/photo-1.jpg" class="wp-image-9"/></figure>\n<!-- /wp:image -->\n\n<!-- wp:html -->\n<div class="row">\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100">\n          <h4 class="card-header">Card Title</h4>\n          <div class="card-body">\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus neque.</p>\n          </div>\n          <div class="card-footer">\n            <a href="#" class="btn btn-primary">Learn More</a>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100">\n          <h4 class="card-header">Card Title</h4>\n          <div class="card-body">\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis ipsam eos, nam perspiciatis natus commodi similique totam consectetur praesentium molestiae atque exercitationem ut consequuntur, sed eveniet, magni nostrum sint fuga.</p>\n          </div>\n          <div class="card-footer">\n            <a href="#" class="btn btn-primary">Learn More</a>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100">\n          <h4 class="card-header">Card Title</h4>\n          <div class="card-body">\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente esse necessitatibus neque.</p>\n          </div>\n          <div class="card-footer">\n            <a href="#" class="btn btn-primary">Learn More</a>\n          </div>\n        </div>\n      </div>\n    </div>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:09:48', NULL, '2020-12-01 20:09:48', NULL),
	(108, 'contact', 'default', 'Contact ', '', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">Contact</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="row">\n      <!-- Map Column -->\n      <div class="col-lg-8 mb-4">\n        <!-- Embedded Google Map -->\n        <iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?hl=en&amp;ie=UTF8&amp;ll=37.0625,-95.677068&amp;spn=56.506174,79.013672&amp;t=m&amp;z=4&amp;output=embed"></iframe>\n      </div>\n      <!-- Contact Details Column -->\n      <div class="col-lg-4 mb-4">\n        <h3>Contact Details</h3>\n        <p>\n          3481 Melrose Place\n          <br>Beverly Hills, CA 90210\n          <br>\n        </p>\n        <p>\n          <abbr title="Phone">P</abbr>: (123) 456-7890\n        </p>\n        <p>\n          <abbr title="Email">E</abbr>:\n          <a href="mailto:name@example.com">name@example.com\n          </a>\n        </p>\n        <p>\n          <abbr title="Hours">H</abbr>: Monday - Friday: 9:00 AM to 5:00 PM\n        </p>\n      </div>\n    </div>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="row">\n      <div class="col-lg-8 mb-4">\n        <h3>Send us a Message</h3>\n        <form name="sentMessage" id="contactForm" novalidate>\n          <div class="control-group form-group">\n            <div class="controls">\n              <label>Full Name:</label>\n              <input type="text" class="form-control" id="name" required data-validation-required-message="Please enter your name.">\n              <p class="help-block"></p>\n            </div>\n          </div>\n          <div class="control-group form-group">\n            <div class="controls">\n              <label>Phone Number:</label>\n              <input type="tel" class="form-control" id="phone" required data-validation-required-message="Please enter your phone number.">\n            </div>\n          </div>\n          <div class="control-group form-group">\n            <div class="controls">\n              <label>Email Address:</label>\n              <input type="email" class="form-control" id="email" required data-validation-required-message="Please enter your email address.">\n            </div>\n          </div>\n          <div class="control-group form-group">\n            <div class="controls">\n              <label>Message:</label>\n              <textarea rows="10" cols="100" class="form-control" id="message" required data-validation-required-message="Please enter your message" maxlength="999" style="resize:none"></textarea>\n            </div>\n          </div>\n          <div id="success"></div>\n          <!-- For success/fail messages -->\n          <button type="submit" class="btn btn-primary" id="sendMessageButton">Send Message</button>\n        </form>\n      </div>\n\n    </div>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:13:37', NULL, '2020-12-01 20:13:37', NULL),
	(109, 'frequently-asked-questions', 'default', 'Frequently Asked Questions ', 'FAQ', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">FAQ</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="mb-4" id="accordion" role="tablist" aria-multiselectable="true">\n      <div class="card">\n        <div class="card-header" role="tab" id="headingOne">\n          <h5 class="mb-0">\n            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Collapsible Group Item #1</a>\n          </h5>\n        </div>\n\n        <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">\n          <div class="card-body">\n            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven\'t heard of them accusamus labore sustainable VHS.\n          </div>\n        </div>\n      </div>\n      <div class="card">\n        <div class="card-header" role="tab" id="headingTwo">\n          <h5 class="mb-0">\n            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Collapsible Group Item #2\n            </a>\n          </h5>\n        </div>\n        <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">\n          <div class="card-body">\n            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven\'t heard of them accusamus labore sustainable VHS.\n          </div>\n        </div>\n      </div>\n      <div class="card">\n        <div class="card-header" role="tab" id="headingThree">\n          <h5 class="mb-0">\n            <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">Collapsible Group Item #3</a>\n          </h5>\n        </div>\n        <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">\n          <div class="card-body">\n            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven\'t heard of them accusamus labore sustainable VHS.\n          </div>\n        </div>\n      </div>\n    </div>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:16:41', NULL, '2020-12-01 20:16:41', NULL),
	(110, 'pricing', 'default', 'Pricing', '', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">Pricing</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n <div class="row">\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100">\n          <h3 class="card-header">Basic</h3>\n          <div class="card-body">\n            <div class="display-4">$19.99</div>\n            <div class="font-italic">per month</div>\n          </div>\n          <ul class="list-group list-group-flush">\n            <li class="list-group-item">Cras justo odio</li>\n            <li class="list-group-item">Dapibus ac facilisis in</li>\n            <li class="list-group-item">Vestibulum at eros</li>\n            <li class="list-group-item">\n              <a href="#" class="btn btn-primary">Sign Up!</a>\n            </li>\n          </ul>\n        </div>\n      </div>\n      <div class="col-lg-4 mb-4">\n        <div class="card card-outline-primary h-100">\n          <h3 class="card-header bg-primary text-white">Plus</h3>\n          <div class="card-body">\n            <div class="display-4">$39.99</div>\n            <div class="font-italic">per month</div>\n          </div>\n          <ul class="list-group list-group-flush">\n            <li class="list-group-item">Cras justo odio</li>\n            <li class="list-group-item">Dapibus ac facilisis in</li>\n            <li class="list-group-item">Vestibulum at eros</li>\n            <li class="list-group-item">\n              <a href="#" class="btn btn-primary">Sign Up!</a>\n            </li>\n          </ul>\n        </div>\n      </div>\n      <div class="col-lg-4 mb-4">\n        <div class="card h-100">\n          <h3 class="card-header">Ultra</h3>\n          <div class="card-body">\n            <div class="display-4">$159.99</div>\n            <div class="font-italic">per month</div>\n          </div>\n          <ul class="list-group list-group-flush">\n            <li class="list-group-item">Cras justo odio</li>\n            <li class="list-group-item">Dapibus ac facilisis in</li>\n            <li class="list-group-item">Vestibulum at eros</li>\n            <li class="list-group-item">\n              <a href="#" class="btn btn-primary">Sign Up!</a>\n            </li>\n          </ul>\n        </div>\n      </div>\n    </div>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:28:47', NULL, '2020-12-01 20:28:47', NULL),
	(111, 'sidebar', 'default', 'Sidebar', '', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">About</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="row">\n      <!-- Sidebar Column -->\n      <div class="col-lg-3 mb-4">\n        <div class="list-group">\n          <a href="index.html" class="list-group-item">Home</a>\n          <a href="about.html" class="list-group-item">About</a>\n          <a href="services.html" class="list-group-item">Services</a>\n          <a href="contact.html" class="list-group-item">Contact</a>\n          <a href="portfolio-1-col.html" class="list-group-item">1 Column Portfolio</a>\n          <a href="portfolio-2-col.html" class="list-group-item">2 Column Portfolio</a>\n          <a href="portfolio-3-col.html" class="list-group-item">3 Column Portfolio</a>\n          <a href="portfolio-4-col.html" class="list-group-item">4 Column Portfolio</a>\n          <a href="portfolio-item.html" class="list-group-item">Single Portfolio Item</a>\n          <a href="blog-home-1.html" class="list-group-item">Blog Home 1</a>\n          <a href="blog-home-2.html" class="list-group-item">Blog Home 2</a>\n          <a href="blog-post.html" class="list-group-item">Blog Post</a>\n          <a href="full-width.html" class="list-group-item">Full Width Page</a>\n          <a href="sidebar.html" class="list-group-item active">Sidebar Page</a>\n          <a href="faq.html" class="list-group-item">FAQ</a>\n          <a href="404.html" class="list-group-item">404</a>\n          <a href="pricing.html" class="list-group-item">Pricing Table</a>\n        </div>\n      </div>\n      <!-- Content Column -->\n      <div class="col-lg-9 mb-4">\n        <h2>Section Heading</h2>\n        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta, et temporibus, facere perferendis veniam beatae non debitis, numquam blanditiis necessitatibus vel mollitia dolorum laudantium, voluptate dolores iure maxime ducimus fugit.</p>\n      </div>\n    </div>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:29:59', NULL, '2020-12-01 20:29:59', NULL),
	(112, 'full-width', 'default', 'Full Width', '', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">Full Width</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:paragraph -->\n<p>Most of Start Bootstrap\'s unstyled templates can be directly integrated into the Modern Business template. You can view all of our unstyled templates on our website at\n      <a href="http://startbootstrap.com/template-categories/unstyled">http://startbootstrap.com/template-categories/unstyled</a>.</p>\n<!-- /wp:paragraph -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:32:34', NULL, '2020-12-01 20:32:34', NULL),
	(113, 'portfolio', 'default', 'Portfolio', '', '<!-- wp:html -->\n<ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">Portfolio 3</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="row">\n      <div class="col-lg-4 col-sm-6 portfolio-item">\n        <div class="card h-100">\n          <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>\n          <div class="card-body">\n            <h4 class="card-title">\n              <a href="#">Project One</a>\n            </h4>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet numquam aspernatur eum quasi sapiente nesciunt? Voluptatibus sit, repellat sequi itaque deserunt, dolores in, nesciunt, illum tempora ex quae? Nihil, dolorem!</p>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 col-sm-6 portfolio-item">\n        <div class="card h-100">\n          <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>\n          <div class="card-body">\n            <h4 class="card-title">\n              <a href="#">Project Two</a>\n            </h4>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 col-sm-6 portfolio-item">\n        <div class="card h-100">\n          <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>\n          <div class="card-body">\n            <h4 class="card-title">\n              <a href="#">Project Three</a>\n            </h4>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos quisquam, error quod sed cumque, odio distinctio velit nostrum temporibus necessitatibus et facere atque iure perspiciatis mollitia recusandae vero vel quam!</p>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 col-sm-6 portfolio-item">\n        <div class="card h-100">\n          <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>\n          <div class="card-body">\n            <h4 class="card-title">\n              <a href="#">Project Four</a>\n            </h4>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 col-sm-6 portfolio-item">\n        <div class="card h-100">\n          <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>\n          <div class="card-body">\n            <h4 class="card-title">\n              <a href="#">Project Five</a>\n            </h4>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae.</p>\n          </div>\n        </div>\n      </div>\n      <div class="col-lg-4 col-sm-6 portfolio-item">\n        <div class="card h-100">\n          <a href="#"><img class="card-img-top" src="http://placehold.it/700x400" alt=""></a>\n          <div class="card-body">\n            <h4 class="card-title">\n              <a href="#">Project Six</a>\n            </h4>\n            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Itaque earum nostrum suscipit ducimus nihil provident, perferendis rem illo, voluptate atque, sit eius in voluptates, nemo repellat fugiat excepturi! Nemo, esse.</p>\n          </div>\n        </div>\n      </div>\n    </div>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<!-- Pagination -->\n    <ul class="pagination justify-content-center">\n      <li class="page-item">\n        <a class="page-link" href="#" aria-label="Previous">\n          <span aria-hidden="true">«</span>\n          <span class="sr-only">Previous</span>\n        </a>\n      </li>\n      <li class="page-item">\n        <a class="page-link" href="#">1</a>\n      </li>\n      <li class="page-item">\n        <a class="page-link" href="#">2</a>\n      </li>\n      <li class="page-item">\n        <a class="page-link" href="#">3</a>\n      </li>\n      <li class="page-item">\n        <a class="page-link" href="#" aria-label="Next">\n          <span aria-hidden="true">»</span>\n          <span class="sr-only">Next</span>\n        </a>\n      </li>\n    </ul>\n<!-- /wp:html -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:39:16', NULL, '2020-12-01 20:39:16', NULL),
	(114, 'portfolio-item', 'default', 'Portfolio Item', '', '<!-- wp:html -->\n <ol class="breadcrumb">\n      <li class="breadcrumb-item">\n        <a href="index.html">Home</a>\n      </li>\n      <li class="breadcrumb-item active">Portfolio Item</li>\n    </ol>\n<!-- /wp:html -->\n\n<!-- wp:html -->\n<div class="row">\n\n      <div class="col-md-8">\n        <img class="img-fluid" src="http://placehold.it/750x500" alt="">\n      </div>\n\n      <div class="col-md-4">\n        <h3 class="my-3">Project Description</h3>\n        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra euismod odio, gravida pellentesque urna varius vitae. Sed dui lorem, adipiscing in adipiscing et, interdum nec metus. Mauris ultricies, justo eu convallis placerat, felis enim.</p>\n        <h3 class="my-3">Project Details</h3>\n        <ul>\n          <li>Lorem Ipsum</li>\n          <li>Dolor Sit Amet</li>\n          <li>Consectetur</li>\n          <li>Adipiscing Elit</li>\n        </ul>\n      </div>\n\n    </div>\n<!-- /wp:html -->\n\n<!-- wp:html /-->\n\n<!-- wp:heading {"level":3} -->\n<h3>Related Projects</h3>\n<!-- /wp:heading -->\n\n<!-- wp:html -->\n<div class="row">\n\n      <div class="col-md-3 col-sm-6 mb-4">\n        <a href="#">\n          <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n        </a>\n      </div>\n\n      <div class="col-md-3 col-sm-6 mb-4">\n        <a href="#">\n          <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n        </a>\n      </div>\n\n      <div class="col-md-3 col-sm-6 mb-4">\n        <a href="#">\n          <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n        </a>\n      </div>\n\n      <div class="col-md-3 col-sm-6 mb-4">\n        <a href="#">\n          <img class="img-fluid" src="http://placehold.it/500x300" alt="">\n        </a>\n      </div>\n\n    </div>\n<!-- /wp:html -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->', 1, 1, 1, 0, 0, 1, 'default', NULL, '2020-12-01 20:38:23', NULL, '2020-12-01 20:38:23', NULL);
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
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`page_data_id`) USING BTREE,
  KEY `user` (`page_id`) USING BTREE,
  CONSTRAINT `FK_page_data_page` FOREIGN KEY (`page_id`) REFERENCES `page` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Volcando datos para la tabla start_cms.page_data: ~20 rows (aproximadamente)
DELETE FROM `page_data`;
/*!40000 ALTER TABLE `page_data` DISABLE KEYS */;
INSERT INTO `page_data` (`page_data_id`, `page_id`, `_key`, `_value`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(35, 100, 'title', 'Blade Templates', 0, '2020-11-13 13:24:02', '2020-11-26 19:03:51', '2020-11-18 08:28:19'),
	(36, 100, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Blade Templates"},{"name":"description","content":"Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. In fact, al..."},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Blade Templates"},{"property":"og:description","content":"Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. In fact, al..."},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/blade-templates"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Blade Templates"},{"property":"twitter:description","content":"Blade is the simple, yet powerful templating engine provided with Laravel. Unlike other popular PHP templating engines, Blade does not restrict you from using plain PHP code in your views. In fact, al..."},{"property":"twitter:image","content":""}]', 0, '2020-11-13 13:24:02', '2020-11-26 19:03:51', '2020-11-18 08:28:19'),
	(37, 100, 'tags', '["php","laravel","development"]', 0, '2020-11-13 13:24:15', '2020-11-26 19:03:51', '2020-11-18 08:28:19'),
	(45, 102, 'title', 'TypeScript: The Best Resources to Learn It', 0, '2020-11-25 07:53:25', '2020-11-26 19:03:44', NULL),
	(46, 102, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"TypeScript: The Best Resources to Learn It"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"TypeScript: The Best Resources to Learn It"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/typescript-the-best-resources-to-learn-it"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"TypeScript: The Best Resources to Learn It"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 0, '2020-11-25 07:53:25', '2020-11-26 19:03:44', NULL),
	(47, 103, 'title', '¿Qué nos aporta Materialize CSS en Desarrollo Web?', 1, '2020-11-26 13:41:00', '2020-11-26 13:41:00', NULL),
	(48, 103, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"\\u00bfQu\\u00e9 nos aporta Materialize CSS en Desarrollo Web?"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"\\u00bfQu\\u00e9 nos aporta Materialize CSS en Desarrollo Web?"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/que-nos-aporta-materialize-css-en-desarrollo-web"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"\\u00bfQu\\u00e9 nos aporta Materialize CSS en Desarrollo Web?"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-11-26 13:41:00', '2020-11-26 13:41:00', NULL),
	(49, 104, 'title', 'Materialize css', 1, '2020-11-26 15:15:34', '2020-11-26 17:58:39', NULL),
	(50, 104, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Materialize css"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Materialize css"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/materialize-css"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Materialize css"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-11-26 15:15:34', '2020-11-26 17:58:39', NULL),
	(51, 105, 'title', 'CodeIgniter', 1, '2020-11-26 19:04:16', '2020-11-26 19:04:16', NULL),
	(52, 105, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"CodeIgniter"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"CodeIgniter"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/codeigniter"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"CodeIgniter"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-11-26 19:04:16', '2020-11-26 19:04:16', NULL),
	(53, 105, 'tags', '["php","frameworks"]', 1, '2020-11-26 19:07:44', '2020-11-26 19:07:44', NULL),
	(54, 106, 'title', 'About', 1, '2020-12-01 16:01:03', '2020-12-01 16:01:03', NULL),
	(55, 106, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"About"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"About"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/about"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"About"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:01:03', '2020-12-01 16:01:03', NULL),
	(56, 107, 'title', 'Services', 1, '2020-12-01 16:09:08', '2020-12-01 16:09:08', NULL),
	(57, 107, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Services"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Services"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/services"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Services"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:09:08', '2020-12-01 16:09:08', NULL),
	(58, 108, 'title', 'Contact ', 1, '2020-12-01 16:12:02', '2020-12-01 16:12:02', NULL),
	(59, 108, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Contact "},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Contact "},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/contact"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Contact "},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:12:02', '2020-12-01 16:12:02', NULL),
	(60, 109, 'title', 'Frequently Asked Questions ', 1, '2020-12-01 16:16:41', '2020-12-01 16:16:41', NULL),
	(61, 109, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Frequently Asked Questions "},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Frequently Asked Questions "},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/frequently-asked-questions"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Frequently Asked Questions "},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:16:41', '2020-12-01 16:16:41', NULL),
	(62, 110, 'title', 'Pricing', 1, '2020-12-01 16:27:50', '2020-12-01 16:27:54', NULL),
	(63, 110, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Pricing"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Pricing"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/pricing"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Pricing"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:27:50', '2020-12-01 16:27:54', NULL),
	(64, 111, 'title', 'Sidebar', 1, '2020-12-01 16:29:34', '2020-12-01 16:29:34', NULL),
	(65, 111, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Sidebar"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Sidebar"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/sidebar"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Sidebar"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:29:34', '2020-12-01 16:29:34', NULL),
	(66, 112, 'title', 'Full Width', 1, '2020-12-01 16:30:30', '2020-12-01 16:30:37', NULL),
	(67, 112, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Full Width"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Full Width"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/full-width"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Full Width"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:30:30', '2020-12-01 16:30:37', NULL),
	(68, 113, 'title', 'Portfolio', 1, '2020-12-01 16:34:57', '2020-12-01 16:35:30', NULL),
	(69, 113, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Portfolio"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Portfolio"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/portfolio"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Portfolio"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:34:57', '2020-12-01 16:35:30', NULL),
	(70, 114, 'title', 'Portfolio Item', 1, '2020-12-01 16:36:15', '2020-12-01 16:37:32', NULL),
	(71, 114, 'meta', '[{"name":"author","content":"Gervis Mora"},{"name":"keywords","content":"Portfolio Item"},{"name":"description","content":""},{"name":"ROBOTS","content":"NOODP"},{"name":"GOOGLEBOT","content":"INDEX, FOLLOW"},{"property":"og:title","content":"Portfolio Item"},{"property":"og:description","content":""},{"property":"og:site_name","content":"Modern Business"},{"property":"og:url","content":"http:\\/\\/localhost:8000\\/portfolio-item"},{"property":"og:image","content":""},{"property":"og:type","content":"article"},{"property":"twitter:card","content":"summary"},{"property":"twitter:title","content":"Portfolio Item"},{"property":"twitter:description","content":""},{"property":"twitter:image","content":""}]', 1, '2020-12-01 16:36:15', '2020-12-01 16:37:32', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;

-- Volcando datos para la tabla start_cms.permisions: ~35 rows (aproximadamente)
DELETE FROM `permisions`;
/*!40000 ALTER TABLE `permisions` DISABLE KEYS */;
INSERT INTO `permisions` (`permisions_id`, `permision_name`, `date_create`, `date_update`, `status`) VALUES
	(1, 'CREATE_USER', '2020-09-20 09:51:14', '2020-11-22 18:18:15', 1),
	(2, 'UPDATE_USER', '2020-09-20 09:51:27', '2020-11-22 18:18:16', 1),
	(3, 'DELETE_USER', '2020-09-20 09:51:34', '2020-11-22 18:18:17', 1),
	(4, 'SELECT_USERS', '2020-09-20 09:52:01', '2020-11-22 18:18:17', 1),
	(5, 'UPDATE_USERS', '2020-09-20 12:13:54', '2020-11-22 18:18:19', 1),
	(6, 'CREATE_PAGE', '2020-09-20 09:51:14', '2020-11-22 18:18:24', 1),
	(7, 'UPDATE_PAGE', '2020-09-20 09:51:27', '2020-11-22 18:18:26', 1),
	(8, 'DELETE_PAGE', '2020-09-20 09:51:34', '2020-11-22 18:18:27', 1),
	(9, 'SELECT_PAGES', '2020-09-20 09:52:01', '2020-11-22 18:18:28', 1),
	(10, 'UPDATE_PAGES', '2020-09-20 12:13:54', '2020-11-22 18:18:31', 1),
	(11, 'CREATE_FORM_CUSTOM', '2020-09-20 09:51:14', '2020-11-22 18:18:34', 1),
	(12, 'UPDATE_FORM_CUSTOM', '2020-09-20 09:51:27', '2020-11-22 18:18:35', 1),
	(13, 'DELETE_FORM_CUSTOM', '2020-09-20 09:51:34', '2020-11-22 18:18:35', 1),
	(14, 'SELECT_FORM_CUSTOMS', '2020-09-20 09:52:01', '2020-09-20 09:52:06', 1),
	(16, 'CREATE_MENU', '2020-09-20 09:51:14', '2020-09-20 09:52:03', 1),
	(17, 'UPDATE_MENU', '2020-09-20 09:51:27', '2020-09-20 09:52:04', 1),
	(18, 'DELETE_MENU', '2020-09-20 09:51:34', '2020-09-20 09:52:05', 1),
	(19, 'SELECT_MENUS', '2020-09-20 09:52:01', '2020-09-20 09:52:06', 1),
	(20, 'UPDATE_MENUS', '2020-09-20 12:13:54', '2020-09-20 12:13:54', 1),
	(21, 'CREATE_FILE', '2020-09-20 09:51:14', '2020-09-20 09:52:03', 1),
	(22, 'UPDATE_FILE', '2020-09-20 09:51:27', '2020-09-20 09:52:04', 1),
	(23, 'DELETE_FILE', '2020-09-20 09:51:34', '2020-09-20 09:52:05', 1),
	(24, 'SELECT_FILES', '2020-09-20 09:52:01', '2020-09-20 09:52:06', 1),
	(25, 'UPDATE_FILES', '2020-09-20 12:13:54', '2020-09-20 12:13:54', 1),
	(26, 'CREATE_CATEGORIE', '2020-09-20 09:51:14', '2020-09-20 09:52:03', 1),
	(27, 'UPDATE_CATEGORIE', '2020-09-20 09:51:27', '2020-09-20 09:52:04', 1),
	(28, 'DELETE_CATEGORIE', '2020-09-20 09:51:34', '2020-09-20 09:52:05', 1),
	(29, 'SELECT_CATEGORIES', '2020-09-20 09:52:01', '2020-09-20 09:52:06', 1),
	(30, 'UPDATE_CATEGORIES', '2020-09-20 12:13:54', '2020-09-20 12:13:54', 1),
	(31, 'PUBLISH_PAGES', '2020-09-20 12:13:54', '2020-09-20 12:13:54', 1),
	(32, 'PUBLISH_FORM_CUSTOM', '2020-09-20 12:13:54', '2020-09-20 12:13:54', 1),
	(33, 'CREATE_CONTENT_DATA', '2020-09-20 09:51:14', '2020-11-22 18:13:45', 1),
	(34, 'UPDATE_CONTENT_DATA', '2020-09-20 09:51:27', '2020-11-22 18:14:00', 1),
	(35, 'DELETE_CONTENT_DATA', '2020-09-20 09:51:34', '2020-09-20 09:52:05', 1),
	(36, 'SELECT_CONTENT_DATA', '2020-09-20 09:52:01', '2020-09-20 09:52:06', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

-- Volcando datos para la tabla start_cms.site_config: ~20 rows (aproximadamente)
DELETE FROM `site_config`;
/*!40000 ALTER TABLE `site_config` DISABLE KEYS */;
INSERT INTO `site_config` (`site_config_id`, `user_id`, `config_name`, `config_value`, `config_type`, `config_data`, `readonly`, `date_create`, `date_update`, `status`) VALUES
	(1, 1, 'SITE_TITLE', 'Gervis Bermúdez', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-24 20:06:44', '2020-10-12 13:17:34', 1),
	(2, 1, 'SITE_DESCRIPTION', 'My Great website made by Start Codeigneiter ', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-11-11 16:55:13', '2020-10-12 11:48:11', 1),
	(3, 1, 'SITE_ADMIN_EMAIL', 'gervisbermudez@outlook.com', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "email",\r\n  "max_lenght": "150",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "email",\r\n  "perm_values": null\r\n}', 0, '2020-10-24 20:07:37', '2020-10-12 11:48:11', 1),
	(4, 1, 'SITE_LANGUAGE', 'en', 'seo', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "0",\r\n  "handle_as": "select",\r\n  "input_type": "select",\r\n  "perm_values": ["en", "esp"]\r\n}', 0, '2020-10-12 22:24:21', '2020-10-12 11:48:11', 1),
	(5, 1, 'SITE_TIME_ZONE', 'UTC-10', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "0",\r\n  "handle_as": "select",\r\n  "input_type": "select",\r\n  "perm_values": {\r\n    "UTC-12": "UTC-12",\r\n    "UTC-11.5": "UTC-11:30",\r\n    "UTC-11": "UTC-11",\r\n    "UTC-10.5": "UTC-10:30",\r\n    "UTC-10": "UTC-10",\r\n    "UTC-9.5": "UTC-9:30",\r\n    "UTC-9": "UTC-9",\r\n    "UTC-8.5": "UTC-8:30",\r\n    "UTC-8": "UTC-8",\r\n    "UTC-7.5": "UTC-7:30",\r\n    "UTC-7": "UTC-7",\r\n    "UTC-6.5": "UTC-6:30",\r\n    "UTC-6": "UTC-6",\r\n    "UTC-5.5": "UTC-5:30",\r\n    "UTC-5": "UTC-5",\r\n    "UTC-4.5": "UTC-4:30",\r\n    "UTC-4": "UTC-4",\r\n    "UTC-3.5": "UTC-3:30",\r\n    "UTC-3": "UTC-3",\r\n    "UTC-2.5": "UTC-2:30",\r\n    "UTC-2": "UTC-2",\r\n    "UTC-1.5": "UTC-1:30",\r\n    "UTC-1": "UTC-1",\r\n    "UTC-0.5": "UTC-0:30",\r\n    "UTC+0": "UTC+0",\r\n    "UTC+0.5": "UTC+0:30",\r\n    "UTC+1": "UTC+1",\r\n    "UTC+1.5": "UTC+1:30",\r\n    "UTC+2": "UTC+2",\r\n    "UTC+2.5": "UTC+2:30",\r\n    "UTC+3": "UTC+3",\r\n    "UTC+3.5": "UTC+3:30",\r\n    "UTC+4": "UTC+4",\r\n    "UTC+4.5": "UTC+4:30",\r\n    "UTC+5": "UTC+5",\r\n    "UTC+5.5": "UTC+5:30",\r\n    "UTC+5.75": "UTC+5:45",\r\n    "UTC+6": "UTC+6",\r\n    "UTC+6.5": "UTC+6:30",\r\n    "UTC+7": "UTC+7",\r\n    "UTC+7.5": "UTC+7:30",\r\n    "UTC+8": "UTC+8",\r\n    "UTC+8.5": "UTC+8:30",\r\n    "UTC+8.75": "UTC+8:45",\r\n    "UTC+9": "UTC+9",\r\n    "UTC+9.5": "UTC+9:30",\r\n    "UTC+10": "UTC+10",\r\n    "UTC+10.5": "UTC+10:30",\r\n    "UTC+11": "UTC+11",\r\n    "UTC+11.5": "UTC+11:30",\r\n    "UTC+12": "UTC+12",\r\n    "UTC+12.75": "UTC+12:45",\r\n    "UTC+13": "UTC+13",\r\n    "UTC+13.75": "UTC+13:45",\r\n    "UTC+14": "UTC+14"\r\n  }\r\n}', 0, '2020-09-06 18:15:28', '2020-10-12 11:49:03', 1),
	(6, 0, 'SITE_DATE_FORMAT', 'j \\d\\e F \\d\\e Y', 'general', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-09-05 15:48:17', '2020-10-12 11:49:07', 1),
	(7, 1, 'SITE_TIME_FORMAT', 'H:i', 'general', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"0"}', 0, '2020-09-06 01:44:30', '2020-10-12 11:49:15', 1),
	(9, 1, 'SITE_LIST_MAX_ENTRY', '10', 'general', '{"type_value":"number","validate_as":"number","max_lenght":"50","min_lenght":"0"}', 0, '2020-09-06 18:28:31', '2020-10-12 11:49:20', 1),
	(11, 1, 'SITE_LIST_PUBLIC', 'No', 'general', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-09-06 19:30:40', '2020-10-12 11:49:30', 1),
	(12, 1, 'SITE_AUTHOR', 'Gervis Bermudez', 'seo', '{"type_value":"string","validate_as":"name","max_lenght":"50","min_lenght":"5"}', 0, '2020-09-06 18:29:01', '2020-10-12 11:48:11', 1),
	(13, 0, 'LAST_UPDATE_FILEMANAGER', '2020-11-12 17:34:42', 'general', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-01 12:02:11', '2020-11-12 13:34:42', 1),
	(14, 1, 'ANALYTICS_ACTIVE', 'On', 'analytics', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["Off", "On"],\r\n  "true": "On"\r\n}\r\n', 0, '2020-10-12 22:33:36', '2020-10-12 17:21:11', 1),
	(15, 1, 'ANALYTICS_ID', 'UA-XXXXX-Y', 'analytics', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "50",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-12 22:28:02', '2020-10-12 17:00:44', 1),
	(16, 1, 'ANALYTICS_CODE', '<script> window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date; ga(\'create\', \'UA-XXXXX-Y\', \'auto\'); ga(\'send\', \'pageview\'); </script> <script async src=\'https://www.google-analytics.com/analytics.js\'></script>', 'analytics', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-12 22:28:18', '2020-10-12 17:49:52', 1),
	(17, 1, 'PIXEL_ACTIVE', 'On', 'pixel', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["Off", "On"],\r\n  "true": "On"\r\n}\r\n', 0, '2020-11-19 16:25:06', '2020-10-12 17:59:40', 1),
	(18, 1, 'PIXEL_CODE', '', 'analytics', '{\r\n  "type_value": "string",\r\n  "validate_as": "text",\r\n  "max_lenght": "",\r\n  "min_lenght": "5",\r\n  "handle_as": "input",\r\n  "input_type": "text",\r\n  "perm_values": null\r\n}', 0, '2020-10-12 22:28:18', '2020-10-12 17:50:02', 1),
	(19, 1, 'THEME_PATH', 'myGreatTheme', 'theme', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-12-01 20:44:48', '2020-11-11 13:38:15', 1),
	(20, 1, 'UPDATER_LAST_CHECK_UPDATE', '2020-11-19 16:19:09', 'updater', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-11-11 19:49:23', '2020-11-11 19:28:47', 1),
	(21, 1, 'UPDATER_MANUAL_CHECK', 'On', 'updater', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["Off", "On"],\r\n  "true": "On"\r\n}\r\n', 0, '2020-10-12 22:33:36', '2020-11-11 19:31:03', 1),
	(22, 1, 'UPDATER_LAST_CHECK_DATA', '{"name":"Start CMS","version":"1.5.6","description":"A simple theme building for StartCMS","url":"https://github.com/gervisbermudez/startCodeIgniter-CSM.git","updated":"11/18/2020 12:30:25"}', 'updater', '{"type_value":"string","validate_as":"text","max_lenght":"50","min_lenght":"5"}', 0, '2020-11-11 19:49:23', '2020-11-11 19:28:47', 1),
	(23, 1, 'SYSTEM_LOGGER', 'Si', 'general', '{\r\n  "type_value": "boolean",\r\n  "validate_as": "boolean",\r\n  "handle_as": "switch",\r\n  "input_type": "switch",\r\n  "perm_values": ["No", "Si"],\r\n  "true": "Si"\r\n}\r\n', 0, '2020-11-26 21:07:56', '2020-11-26 17:59:34', 1);
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
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `usergroup_id` (`usergroup_id`),
  CONSTRAINT `FK_user_usergroup` FOREIGN KEY (`usergroup_id`) REFERENCES `usergroup` (`usergroup_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

-- Volcando datos para la tabla start_cms.user: ~3 rows (aproximadamente)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `username`, `password`, `email`, `lastseen`, `usergroup_id`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(1, 'gerber', '$2y$10$Qqz3Uy0KGbgHq7WNP2wvsOYkCBFjsFgtbT2sYRx8nYN/9m7IKmQ5G', 'gerber@gmail.com', '2020-12-01 11:45:10', 1, 1, '2020-03-01 16:11:25', '2020-11-17 08:18:25', NULL),
	(2, 'yduran', '$2y$10$.Rd9Ke7opDn2zvjc70DESuilWjm2mIMB9R2qyHyKTQbYQRYxGI6A2', 'yduran@gmail.com', '2020-11-22 21:52:36', 2, 1, '2020-02-01 16:11:25', '2020-11-17 08:41:49', NULL),
	(3, 'nestor', '$2y$10$todx7BAG8S1cSoKOYxtrPuF412C1FvKuuaJWU1jNb/28ahu0a30GW', 'nestor@email.com', '2020-11-23 14:45:43', 4, 1, '2020-09-20 19:22:31', '2020-11-17 08:43:31', NULL);
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
  `date_delete` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`usergroup_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.usergroup: ~5 rows (aproximadamente)
DELETE FROM `usergroup`;
/*!40000 ALTER TABLE `usergroup` DISABLE KEYS */;
INSERT INTO `usergroup` (`usergroup_id`, `name`, `level`, `description`, `user_id`, `parent_id`, `status`, `date_create`, `date_update`, `date_delete`) VALUES
	(1, 'root', 1, 'All permisions allowed', 1, 1, 1, '2020-11-23 12:43:30', '2020-11-22 15:57:16', NULL),
	(2, 'Administrador', 2, 'All configurations allowed', 1, 1, 1, '2020-11-23 12:49:39', '2020-11-22 16:22:46', NULL),
	(3, 'Estandar', 3, 'Not delete permisions allowed', 1, 1, 1, '2020-11-23 14:01:33', '2020-11-22 17:31:42', NULL),
	(4, 'Publisher', 2, 'Only Insert and Update permisions allowed', 1, 1, 1, '2020-11-23 14:02:31', '2020-11-22 17:31:43', NULL),
	(5, 'Editor', 5, 'Only insert permisions allowed', 1, 1, 1, '2020-11-23 14:06:28', '2020-11-22 17:31:44', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=513 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.usergroup_permisions: ~114 rows (aproximadamente)
DELETE FROM `usergroup_permisions`;
/*!40000 ALTER TABLE `usergroup_permisions` DISABLE KEYS */;
INSERT INTO `usergroup_permisions` (`usergroup_permisions_id`, `usergroup_id`, `permision_id`, `status`, `date_create`, `date_update`) VALUES
	(300, 1, 1, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(301, 1, 3, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(302, 1, 6, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(303, 1, 10, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(304, 1, 16, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(305, 1, 4, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(306, 1, 2, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(307, 1, 5, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(308, 1, 7, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(309, 1, 8, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(310, 1, 13, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(311, 1, 14, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(312, 1, 17, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(313, 1, 18, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(314, 1, 21, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(315, 1, 24, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(316, 1, 25, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(317, 1, 28, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(318, 1, 30, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(319, 1, 31, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(320, 1, 9, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(321, 1, 19, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(322, 1, 20, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(323, 1, 22, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(324, 1, 23, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(325, 1, 26, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(326, 1, 27, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(327, 1, 29, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(328, 1, 11, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(329, 1, 12, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(330, 1, 33, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(331, 1, 35, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(332, 1, 36, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(333, 1, 34, 1, '2020-11-23 08:43:30', '2020-11-23 08:43:30'),
	(334, 2, 1, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(335, 2, 3, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(336, 2, 6, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(337, 2, 10, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(338, 2, 16, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(339, 2, 4, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(340, 2, 2, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(341, 2, 5, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(342, 2, 7, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(343, 2, 8, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(344, 2, 13, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(345, 2, 14, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(346, 2, 17, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(347, 2, 18, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(348, 2, 21, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(349, 2, 24, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(350, 2, 25, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(351, 2, 28, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(352, 2, 30, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(353, 2, 31, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(354, 2, 9, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(355, 2, 19, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(356, 2, 20, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(357, 2, 22, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(358, 2, 23, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(359, 2, 26, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(360, 2, 27, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(361, 2, 29, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(362, 2, 11, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(363, 2, 12, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(364, 2, 33, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(365, 2, 35, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(366, 2, 36, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(367, 2, 34, 1, '2020-11-23 08:49:39', '2020-11-23 08:49:39'),
	(455, 3, 26, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(456, 3, 27, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(457, 3, 30, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(458, 3, 29, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(459, 3, 33, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(460, 3, 36, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(461, 3, 34, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(462, 3, 21, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(463, 3, 22, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(464, 3, 23, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(465, 3, 24, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(466, 3, 25, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(467, 3, 11, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(468, 3, 12, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(469, 3, 14, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(470, 3, 16, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(471, 3, 17, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(472, 3, 19, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(473, 3, 20, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(474, 3, 6, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(475, 3, 7, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(476, 3, 10, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(477, 3, 31, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(478, 3, 9, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(479, 3, 2, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(480, 3, 4, 1, '2020-11-23 10:01:33', '2020-11-23 10:01:33'),
	(481, 4, 26, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(482, 4, 27, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(483, 4, 30, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(484, 4, 29, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(485, 4, 33, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(486, 4, 36, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(487, 4, 34, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(488, 4, 21, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(489, 4, 22, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(490, 4, 24, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(491, 4, 25, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(492, 4, 11, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(493, 4, 12, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(494, 4, 14, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(495, 4, 6, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(496, 4, 7, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(497, 4, 10, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(498, 4, 31, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(499, 4, 9, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(500, 4, 2, 1, '2020-11-23 10:02:31', '2020-11-23 10:02:31'),
	(501, 5, 29, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(502, 5, 33, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(503, 5, 36, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(504, 5, 21, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(505, 5, 24, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(506, 5, 11, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(507, 5, 12, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(508, 5, 14, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(509, 5, 6, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(510, 5, 7, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(511, 5, 9, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28'),
	(512, 5, 2, 1, '2020-11-23 10:06:28', '2020-11-23 10:06:28');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla start_cms.user_data: ~19 rows (aproximadamente)
DELETE FROM `user_data`;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` (`user_data_id`, `user_id`, `_key`, `_value`, `status`, `date_create`, `date_update`) VALUES
	(1, 1, 'nombre', 'Gervis', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:30'),
	(2, 1, 'apellido', 'Mora', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:34'),
	(3, 1, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:35'),
	(4, 1, 'telefono', '0414-1672173', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:36'),
	(5, 1, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:38'),
	(6, 1, 'avatar', './public/img/profile/nestor/PVuKqgJQ_400x400.png', 1, '2020-03-01 16:31:46', '2020-11-20 11:29:14'),
	(7, 2, 'nombre', 'Yule', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:40'),
	(8, 2, 'apellido', 'Duran', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:42'),
	(9, 2, 'direccion', 'Mara', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:43'),
	(10, 2, 'telefono', '0412-9873920', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:46'),
	(11, 2, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:48'),
	(12, 2, 'avatar', './public/img/profile/yduran/300_11.jpg', 1, '2020-05-02 19:21:05', '2020-10-28 09:10:04'),
	(18, 2, 'telefono', '0412-9873920', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:46'),
	(19, 3, 'nombre', 'Nestor', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:40'),
	(20, 3, 'apellido', 'Barroso', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:42'),
	(21, 3, 'direccion', 'Buenos Aires', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:43'),
	(22, 3, 'telefono', '+54 11 57614678', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:46'),
	(23, 3, 'create by', 'gerber', 1, '2020-03-01 16:31:46', '2020-05-25 11:48:48'),
	(24, 3, 'avatar', './public/img/profile/default_profile_1.jpg', 1, '2020-05-02 19:21:05', '2020-11-20 11:29:03');
/*!40000 ALTER TABLE `user_data` ENABLE KEYS */;

-- Volcando estructura para tabla start_cms.user_tracking
CREATE TABLE IF NOT EXISTS `user_tracking` (
  `user_tracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL DEFAULT '0',
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
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
