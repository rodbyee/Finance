-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-06-2026 a las 04:34:24
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
-- Base de datos: `admon`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fecha`
--

CREATE TABLE `fecha` (
  `id_date` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `week_date` varchar(100) DEFAULT NULL,
  `datest` date DEFAULT NULL,
  `datend` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `move`
--

CREATE TABLE `move` (
  `id_mov` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_date` int(11) DEFAULT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `cant` decimal(10,2) NOT NULL,
  `descrip` varchar(100) DEFAULT NULL,
  `datet` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `move`
--

INSERT INTO `move` (`id_mov`, `id_user`, `id_date`, `tipo`, `cant`, `descrip`, `datet`) VALUES
(1, 1, NULL, 'ingreso', 700.00, 'Semana 1 pago', '2026-05-22'),
(2, 1, NULL, 'egreso', 200.00, 'Parte de mama', '2026-05-22'),
(3, 1, NULL, 'egreso', 1604.00, 'Pago celular', '2026-06-13'),
(4, 1, NULL, 'ingreso', 2100.00, 'Pago semana 2 VTT', '2026-05-30'),
(5, 1, NULL, 'ingreso', 1900.00, 'Semana 3 pago VTT', '2026-06-06'),
(6, 1, NULL, 'ingreso', 1900.00, 'Pago Semana 4 vtt', '2026-06-13'),
(7, 1, NULL, 'egreso', 1250.00, 'Salida ceramia aniversario', '2026-05-30'),
(8, 1, NULL, 'egreso', 450.00, 'Salida con May tacos', '2026-06-13'),
(9, 1, NULL, 'egreso', 200.00, 'pago mama', '2026-05-30'),
(10, 1, NULL, 'egreso', 200.00, 'Pago Mama', '2026-06-06'),
(11, 1, NULL, 'egreso', 200.00, 'Pago mama', '2026-06-13'),
(12, 1, NULL, 'egreso', 300.00, 'Cine en familia', '2026-05-23'),
(13, 1, NULL, 'egreso', 100.00, 'Playeras mi mujer y yo cars', '2026-06-08'),
(14, 1, NULL, 'egreso', 96.00, 'Gastos en general', '2026-06-13'),
(15, 1, NULL, 'ingreso', 85.00, 'Gastos en general', '2026-06-15'),
(16, 1, NULL, 'egreso', 170.00, 'Gastos en general', '2026-06-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id_pay` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name_p` varchar(100) NOT NULL,
  `descrip_p` varchar(100) DEFAULT NULL,
  `montototal` decimal(10,2) NOT NULL,
  `fecha_limite` date NOT NULL,
  `estado` enum('pendiente','pagado') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `payments`
--

INSERT INTO `payments` (`id_pay`, `id_user`, `name_p`, `descrip_p`, `montototal`, `fecha_limite`, `estado`) VALUES
(1, 1, 'Tarjeta de credito', 'Tarjeta Nu', 1972.00, '2026-06-23', 'pendiente'),
(2, 1, 'Pago Laptop', '', 500.00, '2026-06-15', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `name_user` varchar(100) NOT NULL,
  `password_user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id_user`, `name_user`, `password_user`) VALUES
(1, 'Rodrigo Aguilar', '$2y$10$SLM./9uy.AtM7gzFp3byj.KLl0YVlzqeTMXnwyWhpxuS7UeIvMoxi');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `fecha`
--
ALTER TABLE `fecha`
  ADD PRIMARY KEY (`id_date`),
  ADD KEY `fk_userr` (`id_user`);

--
-- Indices de la tabla `move`
--
ALTER TABLE `move`
  ADD PRIMARY KEY (`id_mov`),
  ADD KEY `fk_usermove` (`id_user`),
  ADD KEY `fk_fecha` (`id_date`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_pay`),
  ADD KEY `fk_userpaym` (`id_user`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `fecha`
--
ALTER TABLE `fecha`
  MODIFY `id_date` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `move`
--
ALTER TABLE `move`
  MODIFY `id_mov` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `id_pay` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `fecha`
--
ALTER TABLE `fecha`
  ADD CONSTRAINT `fk_userr` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Filtros para la tabla `move`
--
ALTER TABLE `move`
  ADD CONSTRAINT `fk_fecha` FOREIGN KEY (`id_date`) REFERENCES `fecha` (`id_date`),
  ADD CONSTRAINT `fk_usermove` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_userpaym` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
