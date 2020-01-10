-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-03-2017 a las 03:21:26
-- Versión del servidor: 10.1.9-MariaDB
-- Versión de PHP: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `start`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `album`
--

CREATE TABLE `album` (
  `id` int(11) NOT NULL,
  `nombre` varchar(75) NOT NULL,
  `descripcion` tinytext NOT NULL,
  `fecha` datetime NOT NULL,
  `path` varchar(125) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `album_items`
--

CREATE TABLE `album_items` (
  `id` int(11) NOT NULL,
  `id_album` int(11) NOT NULL,
  `nombre` varchar(125) NOT NULL,
  `titulo` varchar(75) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `tipo` varchar(600) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categorias del sistema';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(600) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contacts';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactsdata`
--

CREATE TABLE `contactsdata` (
  `id` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `_key` varchar(200) NOT NULL,
  `_value` varchar(600) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='contactsdata';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datauserstorage`
--

CREATE TABLE `datauserstorage` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `_key` varchar(100) NOT NULL,
  `_value` varchar(600) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `datauserstorage`
--

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `titulo` varchar(70) NOT NULL,
  `texto` mediumtext NOT NULL,
  `imagen` text NOT NULL,
  `thumb` text NOT NULL,
  `ciudad` tinytext NOT NULL,
  `fecha` varchar(70) NOT NULL,
  `lugar` varchar(70) NOT NULL,
  `publishdate` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Eventos a publicar';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mailfolder`
--

CREATE TABLE `mailfolder` (
  `id` int(11) NOT NULL,
  `namefolder` varchar(60) NOT NULL,
  `description` tinytext NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `mailfolder`
--

INSERT INTO `mailfolder` (`id`, `namefolder`, `description`, `status`) VALUES
(1, 'Inbox', 'The main folder', 1),
(2, 'Archived', 'Archived folder', 1),
(3, 'Sent', 'Sent folder', 1),
(4, 'Deleted', 'Deleted folder', 1),
(5, 'Spam', 'Spam folder', 1),
(6, 'Starred', 'Starred folder', 1),
(7, 'Drafts', 'The drafts folder', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `_from` text NOT NULL,
  `_to` text NOT NULL,
  `_subject` varchar(255) NOT NULL,
  `_mensaje` longtext NOT NULL,
  `_cc` text NOT NULL,
  `_bcc` text NOT NULL,
  `fecha` datetime NOT NULL,
  `folder` int(11) NOT NULL,
  `estatus` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `_from`, `_to`, `_subject`, `_mensaje`, `_cc`, `_bcc`, `fecha`, `folder`, `estatus`) VALUES
(1, 'ana24@mail.org', 'admin@mail.org', 'Lorem ipsum dolor sit amet', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\n			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\n			consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\n			cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\n			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '', '2016-09-08 00:00:00', 2, 1),
(4, 'juan45@mail.org', 'Lore@gmail.org', 'Lorem ipsum dolor ', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '', '2016-09-06 06:29:44', 2, 1),
(5, 'migul@mail.org', 'Lore@gmail.org', 'Lorem ipsum dolor ', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '', '2016-09-06 06:29:44', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajesdata`
--

CREATE TABLE `mensajesdata` (
  `id` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `_key` varchar(200) COLLATE utf8_bin NOT NULL,
  `_value` varchar(600) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='mensajesdata';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `relations`
--

CREATE TABLE `relations` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tablename` tinytext NOT NULL,
  `id_row` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `action` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `relations`
--

INSERT INTO `relations` (`id`, `id_user`, `tablename`, `id_row`, `date`, `action`) VALUES
(11, 18, 'video', 2, '2017-03-06 00:09:51', 'crear');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscriptores`
--

CREATE TABLE `suscriptores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(75) NOT NULL,
  `email` varchar(125) NOT NULL,
  `fecha` datetime NOT NULL,
  `codigo` varchar(75) NOT NULL,
  `estado` set('Verificado','No verificado') NOT NULL DEFAULT 'No verificado',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL DEFAULT '1234',
  `email` varchar(255) NOT NULL,
  `lastseen` datetime NOT NULL,
  `usergroup` int(11) NOT NULL DEFAULT '3',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `lastseen`, `usergroup`, `status`) VALUES
(18, 'gerber', '1245', 'gerber@gmail.com', '2016-09-03 03:22:31', 2, 1),
(19, 'yduran', '1234', 'yduran@gmail.com', '2017-03-05 17:12:06', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `userdatapermisions`
--

CREATE TABLE `userdatapermisions` (
  `id` int(11) NOT NULL,
  `id_usergroup` int(11) NOT NULL,
  `permision` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usergroup`
--

CREATE TABLE `usergroup` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `level` int(11) NOT NULL,
  `description` tinytext NOT NULL,
  `createdate` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usergroup`
--

INSERT INTO `usergroup` (`id`, `name`, `level`, `description`, `createdate`, `status`) VALUES
(1, 'root', 1, 'All permisions allowed', '2016-08-27 09:22:22', 1),
(2, 'Administrador', 2, 'All configurations allowed', '2016-08-27 09:22:22', 1),
(3, 'Estandar', 3, 'Not delete permisions allowed', '2016-08-27 08:32:49', 1),
(7, 'Publisher', 4, 'Only Insert and Update permisions allowed', '2016-08-28 07:35:50', 1),
(8, 'Editor', 5, 'Only insert permisions allowed', '2016-08-29 03:21:39', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video`
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `duration` varchar(50) NOT NULL,
  `youtubeid` varchar(6000) NOT NULL,
  `preview` varchar(2000) NOT NULL,
  `payinfo` text NOT NULL,
  `fecha` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video-categoria`
--

CREATE TABLE `video-categoria` (
  `id` int(11) NOT NULL,
  `id_video` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `video-categoria`
--

INSERT INTO `video-categoria` (`id`, `id_video`, `id_categoria`) VALUES
(1, 0, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `album_items`
--
ALTER TABLE `album_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_album` (`id_album`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `contactsdata`
--
ALTER TABLE `contactsdata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Indices de la tabla `datauserstorage`
--
ALTER TABLE `datauserstorage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`id_user`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mailfolder`
--
ALTER TABLE `mailfolder`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `namefolder` (`namefolder`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder` (`folder`) USING BTREE;

--
-- Indices de la tabla `mensajesdata`
--
ALTER TABLE `mensajesdata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mensaje` (`id_mensaje`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`id_user`);

--
-- Indices de la tabla `relations`
--
ALTER TABLE `relations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `suscriptores`
--
ALTER TABLE `suscriptores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `userdatapermisions`
--
ALTER TABLE `userdatapermisions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usergroup`
--
ALTER TABLE `usergroup`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `video-categoria`
--
ALTER TABLE `video-categoria`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `album`
--
ALTER TABLE `album`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `album_items`
--
ALTER TABLE `album_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `contactsdata`
--
ALTER TABLE `contactsdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `datauserstorage`
--
ALTER TABLE `datauserstorage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `mailfolder`
--
ALTER TABLE `mailfolder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `mensajesdata`
--
ALTER TABLE `mensajesdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `relations`
--
ALTER TABLE `relations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `suscriptores`
--
ALTER TABLE `suscriptores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `userdatapermisions`
--
ALTER TABLE `userdatapermisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `usergroup`
--
ALTER TABLE `usergroup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `video-categoria`
--
ALTER TABLE `video-categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
