-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 27-02-2013 a las 20:39:07
-- Versión del servidor: 5.5.25
-- Versión de PHP: 5.3.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `kasparo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` longtext NOT NULL,
  `path` longtext NOT NULL,
  `slider1` tinyint(4) NOT NULL DEFAULT '0',
  `slider2` tinyint(4) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `slider3` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `images`
--

INSERT INTO `images` (`id`, `name`, `path`, `slider1`, `slider2`, `description`, `slider3`) VALUES
(3, 'Prueba', '/public/images/3c8ad41ef58ce2e1596bba8e41986894.png', 1, 0, 'Todo', 1),
(4, 'Prueba', '/public/images/97a8c123c12f56d627253849e75a4b81.png', 1, 0, 'adfadf', 0),
(5, 'adsfa', '/public/images/769c8d555c12b8a9a0a222bb5319f886.png', 1, 0, 'asdf', 0);
