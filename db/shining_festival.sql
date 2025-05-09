-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-05-2025 a las 21:11:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `shining_festival`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `jurado_id` int(11) NOT NULL,
  `pronunciacion` tinyint(4) DEFAULT NULL CHECK (`pronunciacion` between 1 and 10),
  `fluidez` tinyint(4) DEFAULT NULL CHECK (`fluidez` between 1 and 10),
  `vocabulario` tinyint(4) DEFAULT NULL CHECK (`vocabulario` between 1 and 10),
  `afinacion` tinyint(4) DEFAULT NULL CHECK (`afinacion` between 1 and 10),
  `proyeccion_vocal` tinyint(4) DEFAULT NULL CHECK (`proyeccion_vocal` between 1 and 10),
  `interpretacion` tinyint(4) DEFAULT NULL CHECK (`interpretacion` between 1 and 10),
  `story_time` tinyint(4) DEFAULT NULL CHECK (`story_time` between 1 and 10),
  `diseno_escenico` tinyint(4) DEFAULT NULL CHECK (`diseno_escenico` between 1 and 10),
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` enum('Kids','Teens','Seniors') NOT NULL,
  `modalidad` enum('Solista','Grupo') NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','jurado') NOT NULL,
  `area` enum('musica','ingles') DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `clave` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `contrasena`, `rol`, `area`, `creado_en`, `clave`) VALUES
(1, 'Rector General', '126719dbb4b1af350b07b192cb95e6e9f368630cd16952ef632ae3b3a735dfa5', 'admin', NULL, '2025-04-30 14:02:38', 'admin01'),
(2, 'Jurado Inglés 1', '71ad6d5695dc8858bc15bca86a77ecc26d425cc320105d418f8f9a5bd2f8109d', 'jurado', 'ingles', '2025-04-30 14:02:38', 'jurado_en_01'),
(3, 'Jurado Inglés 2', '7e137f87f56ba056ae0d245491ab423b886800e92b69ec1c88604d064317a208', 'jurado', 'ingles', '2025-04-30 14:02:38', 'jurado_en_02'),
(4, 'Jurado Música', '2168697dc70840ee5e958777ea1e9e16b840ec7d82d1827b4445ad8f4360729a', 'jurado', 'musica', '2025-04-30 14:02:38', 'jurado_mu_01');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participante_id` (`participante_id`,`jurado_id`),
  ADD KEY `jurado_id` (`jurado_id`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`jurado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
