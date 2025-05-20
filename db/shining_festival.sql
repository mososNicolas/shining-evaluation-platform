-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-05-2025 a las 18:40:06
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
-- Estructura de tabla para la tabla `calificaciones_ingles`
--

CREATE TABLE `calificaciones_ingles` (
  `id` int(11) NOT NULL,
  `jurado_id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `pronunciacion` int(11) NOT NULL COMMENT '0-10',
  `fluidez` int(11) NOT NULL COMMENT '0-10',
  `vocabulario` int(11) NOT NULL COMMENT '0-10',
  `creatividad_ingles` int(11) NOT NULL COMMENT '0-10',
  `story_time` int(11) DEFAULT NULL COMMENT '0-10',
  `diseno_escenico` int(11) DEFAULT NULL COMMENT '0-10',
  `comentarios_ingles` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones_ingles`
--

INSERT INTO `calificaciones_ingles` (`id`, `jurado_id`, `participante_id`, `pronunciacion`, `fluidez`, `vocabulario`, `creatividad_ingles`, `story_time`, `diseno_escenico`, `comentarios_ingles`, `creado_en`) VALUES
(6, 2, 1, 10, 9, 9, 0, 7, 8, '', '2025-05-12 16:28:31'),
(7, 3, 1, 7, 6, 8, 0, 10, 9, '', '2025-05-12 16:34:39'),
(8, 2, 3, 9, 8, 9, 0, 8, 9, '', '2025-05-12 16:48:47'),
(9, 3, 3, 8, 7, 8, 0, 7, 7, '', '2025-05-12 16:50:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_musica`
--

CREATE TABLE `calificaciones_musica` (
  `id` int(11) NOT NULL,
  `jurado_id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `afinacion` int(11) NOT NULL COMMENT '0-10',
  `ritmo` int(11) NOT NULL COMMENT '0-10',
  `proyeccion_vocal` int(11) NOT NULL COMMENT '0-10',
  `interpretacion` int(11) NOT NULL COMMENT '0-10',
  `creatividad_musica` int(11) NOT NULL COMMENT '0-10',
  `story_time` int(11) DEFAULT NULL COMMENT '0-10',
  `diseno_escenico` int(11) DEFAULT NULL COMMENT '0-10',
  `comentarios_musica` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones_musica`
--

INSERT INTO `calificaciones_musica` (`id`, `jurado_id`, `participante_id`, `afinacion`, `ritmo`, `proyeccion_vocal`, `interpretacion`, `creatividad_musica`, `story_time`, `diseno_escenico`, `comentarios_musica`, `creado_en`) VALUES
(5, 4, 1, 9, 10, 8, 5, 0, 9, 8, '', '2025-05-12 16:42:00'),
(6, 4, 3, 8, 7, 9, 5, 0, 7, 8, '', '2025-05-12 16:53:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` enum('kids','teens','seniors') NOT NULL,
  `modalidad` enum('solistas','grupos') NOT NULL,
  `colegio` varchar(100) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `participantes`
--

INSERT INTO `participantes` (`id`, `nombre`, `categoria`, `modalidad`, `colegio`, `creado_en`) VALUES
(1, 'jaime', 'kids', 'solistas', 'terranova', '2025-05-05 04:09:03'),
(2, 'alejandro', 'teens', 'grupos', 'terranova', '2025-05-09 12:31:34'),
(3, 'nicolas', 'kids', 'solistas', 'buga', '2025-05-09 13:25:15'),
(4, 'Andres ', 'kids', 'solistas', 'yumbo', '2025-05-09 13:25:26');

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
(1, 'Rector', '$2y$10$rcu6FPnoAuQXSPTfLFokYelyqxCXEollQGajMg8Bo5/VCIoGvXsby', 'admin', NULL, '2025-05-05 03:03:37', 'rector2025'),
(2, 'Jurado Inglés 1', '$2y$10$lPJzMhHgg7TLqvfEszi5P.h6vAMFbGrW2InjlRFVDarol4NlVNcaG', 'jurado', 'ingles', '2025-05-05 03:03:37', 'ingles2025'),
(3, 'Jurado Inglés 2', '$2y$10$CUfrxZFsDiJ2lG/0M5Ky4eguMEHqanBZQO0kYKCt.Fhgk8wbIlUBW', 'jurado', 'ingles', '2025-05-05 03:03:37', 'ingles2026'),
(4, 'Jurado Música 1', '$2y$10$WJA.iuGpIZ25hIWJG5FqOOCBX31aXtGMao3f5FlSxcpWiKEuRRebe', 'jurado', 'musica', '2025-05-05 03:03:37', 'musica2025');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones_ingles`
--
ALTER TABLE `calificaciones_ingles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jurado_id` (`jurado_id`),
  ADD KEY `participante_id` (`participante_id`);

--
-- Indices de la tabla `calificaciones_musica`
--
ALTER TABLE `calificaciones_musica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jurado_id` (`jurado_id`),
  ADD KEY `participante_id` (`participante_id`);

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
-- AUTO_INCREMENT de la tabla `calificaciones_ingles`
--
ALTER TABLE `calificaciones_ingles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `calificaciones_musica`
--
ALTER TABLE `calificaciones_musica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones_ingles`
--
ALTER TABLE `calificaciones_ingles`
  ADD CONSTRAINT `calificaciones_ingles_ibfk_1` FOREIGN KEY (`jurado_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `calificaciones_ingles_ibfk_2` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`);

--
-- Filtros para la tabla `calificaciones_musica`
--
ALTER TABLE `calificaciones_musica`
  ADD CONSTRAINT `calificaciones_musica_ibfk_1` FOREIGN KEY (`jurado_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `calificaciones_musica_ibfk_2` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
