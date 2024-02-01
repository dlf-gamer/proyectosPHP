-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-02-2024 a las 03:46:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crud`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `dni` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `celular` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `fecha_insert` datetime DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `fecha_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `dni`, `nombre`, `apellido`, `correo`, `celular`, `password`, `foto`, `estado`, `token`, `codigo`, `fecha_insert`, `fecha_update`, `fecha_login`) VALUES
(147, 1117232752, 'Daniel', 'Lopez Forero', 'dbecerra698@gmail.com', '3232435243', '$2y$10$3/9Za8JdaYJ5zMsytkI0/.IKcTll58iC4qud02TO9GhzuJzFA6Cbm', 'image/foto_por_defecto.png', '0', NULL, NULL, '2024-01-31 21:01:01', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
