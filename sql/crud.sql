-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-01-2024 a las 01:45:59
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
  `fecha_insert` datetime DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `fecha_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `dni`, `nombre`, `apellido`, `correo`, `celular`, `password`, `foto`, `fecha_insert`, `fecha_update`, `fecha_login`) VALUES
(131, 1117232752, 'DANIEL', 'LOPEZ FORERO', 'DDANIELFORERO698@GMAIL.COM', '3232435243', '$2y$10$tYfNZBNJgjDkWGY2iHVRu.Lc2l9CXXhVGdp5ZJB3zFgxaNEIVZayy', 'upload/65b8745067f6e5.90567678_20240130050016.png', NULL, NULL, '2024-01-30 19:01:49'),
(132, 1117429475, 'MARIA', 'VALDERRAMA', 'MARIAVALDERRAMA10@GMAIL.COM', '3236391738', '$2y$10$zH2QLM2lPEk.buX3Rs7HAurw8wNHyPEwu4u/uhOnvWJO3b5Ikp1pe', 'image/foto_por_defecto.png', NULL, NULL, '2024-01-30 19:01:49'),
(134, 1117392741, 'LUIS', 'GONZALES JIL', 'LUISGONZALES10@GMAIL.COM', '3239163854', '$2y$10$WksG7o5drY3PU9GNFA.wnuxOGMd0z.pofohPTtumvkvqty1s1AEbu', 'image/foto_por_defecto.png', '2024-01-30 14:01:50', NULL, '2024-01-30 19:01:23'),
(135, 1117936286, 'JULIAN', 'VALDEZ VELEZ', 'JULIANVALDEZ10@GMAIL.COM', '3239174026', '$2y$10$4c.WEW9JGKsvkOfK1kipmO8fhZ/bxBYmyEJWU7ehuGx57zR.bRrUq', 'image/foto_por_defecto.png', '2024-01-30 19:01:28', NULL, '2024-01-30 19:01:35');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
