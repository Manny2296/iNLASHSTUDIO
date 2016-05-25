-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2016 a las 01:48:53
-- Versión del servidor: 10.1.9-MariaDB
-- Versión de PHP: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inlash_bd`
--

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`inlifes1`@`localhost` FUNCTION `estado_medida` (`p_id_medida` INT, `p_id_usuario` INT, `p_fecha_act` DATE) RETURNS VARCHAR(1) CHARSET latin1 Begin
   Declare v_valor_ant Float;
   Declare v_valor_act Float;
   Declare v_fecha_ant Date;
   Declare Exit Handler For Not Found Return 'I';
	
   Select max(fecha)
     Into v_fecha_ant
     From spa_ficha_antro fian
    Where fian.id_medida  = p_id_medida
      And fian.id_usuario = p_id_usuario
      And fian.fecha      < p_fecha_act;

   If (v_fecha_ant Is Null) Then
      return ('I');
   End If;

   Select valor
     Into v_valor_ant
     From spa_ficha_antro fian
    Where fian.id_medida  = p_id_medida
      And fian.id_usuario = p_id_usuario
      And fian.fecha      = v_fecha_ant;

   Select valor
     Into v_valor_act
     From spa_ficha_antro fian
    Where fian.id_medida  = p_id_medida
      And fian.id_usuario = p_id_usuario
      And fian.fecha      = p_fecha_act;   

   If (v_valor_ant > v_valor_act) Then
      Return('B');
   ElseIf (v_valor_ant < v_valor_act) Then
      Return ('S');
   Else
      Return ('I');
   End If;
End$$

CREATE DEFINER=`inlifes1`@`localhost` FUNCTION `sesiones_disp` (`p_id_servicio` INT, `p_id_usuario` INT, `p_fecha` DATE) RETURNS FLOAT Begin
   Declare v_cont INT(10);
   Declare v_cantidad FLOAT;
   Declare v_sesiones FLOAT;
   Declare v_fecha DATE;
   Declare Exit Handler For Not Found Return 0;

   Select count(9)
     Into v_cont
     From conf_servicios
    Where id_servicio = p_id_servicio
      And prepagado   = 'S';

   If (v_cont = 0) Then
      Return (10);
   End If;

   Select count(9)
     Into v_cont
     From spa_servicios_x_usuario
    Where id_servicio  = p_id_servicio
      And fecha       <= p_fecha
      And id_usuario   = p_id_usuario
      And caducidad   >= curdate();

   If (v_cont = 0) Then
      Return (0);
   End If;

   Select IfNull(Sum(cantidad), 0)
     Into v_cantidad
     From spa_servicios_x_usuario
    Where id_usuario  = p_id_usuario
      And id_servicio = p_id_servicio
      And ( caducidad  >= curdate() Or
            caducidad Is Null );

   If (v_cantidad = 0) Then
      Return (v_cantidad);
   End If;

   Select Min(fecha)
     Into v_fecha
     From spa_servicios_x_usuario
    Where id_servicio  = p_id_servicio
      And fecha       <= p_fecha
      And id_usuario   = p_id_usuario
      And caducidad   >= curdate();

   Select count(9)
     Into v_sesiones
     From spa_programacion
    Where id_servicio  = p_id_servicio
      And id_usuario   = p_id_usuario
      And fecha       >= v_fecha;

  Return (v_cantidad - v_sesiones);
End$$

CREATE DEFINER=`inlifes1`@`localhost` FUNCTION `tiene_tabla_medidas` (`p_id_medida` INT) RETURNS VARCHAR(1) CHARSET latin1 Begin
   Declare v_cont INT(10);
   Declare Exit Handler For Not Found Return 'N';

   Select count(9)
     Into v_cont
     From conf_tabla_medidas
    Where id_medida = p_id_medida;

   If (v_cont = 0) Then
      Return ('N');
   Else
      Return ('S');
   End If;
End$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_eps`
--

CREATE TABLE `conf_eps` (
  `id_eps` int(10) UNSIGNED NOT NULL COMMENT 'Código de la EPS',
  `nombre` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la EPS'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Entidades prestadoras de salud (EPS)';

--
-- Volcado de datos para la tabla `conf_eps`
--

INSERT INTO `conf_eps` (`id_eps`, `nombre`) VALUES
(1, 'Anaswayuu'),
(2, 'Asmet Salud'),
(3, 'Asociación Indígena del Cauca'),
(4, 'Cafaba'),
(5, 'Cafam'),
(6, 'Cafesalud EPS'),
(7, 'Cajacopi Atlántico'),
(8, 'Cajasan'),
(9, 'Calisalud EPS'),
(10, 'Camacol'),
(11, 'Caprecom EPS'),
(12, 'Capresoca EPS'),
(13, 'Colsubsidio'),
(14, 'Comfaboy'),
(15, 'Comfaca'),
(16, 'Comfachoco'),
(17, 'Comfacor'),
(18, 'Comfacundi'),
(19, 'Comfama'),
(20, 'Comfamiliar Cartagena'),
(21, 'Comfamiliar Guajira'),
(22, 'Comfamiliar huila'),
(23, 'Comfamiliar Nariño'),
(24, 'Comfamiliar Sucre'),
(25, 'Comfanorte'),
(26, 'Comfaoriente'),
(27, 'Comfenalco Antioquia'),
(28, 'Comfenalco Santander'),
(29, 'Comfenalco Tolima'),
(30, 'Condor EPS'),
(31, 'Convida EPS'),
(32, 'Coosalud ESS'),
(33, 'Dusakawi'),
(34, 'Emdisalud ESS'),
(35, 'EPS Fuerzas Militares'),
(36, 'EPS Policia Nacional (HOCEN);'),
(37, 'Humana Vivir EPS'),
(38, 'Mallamas'),
(39, 'Manexka EPSI'),
(40, 'Nueva EPS (ISS)'),
(41, 'Pijaos salud EPSI'),
(42, 'Salud Total EPS'),
(43, 'Salud Vida EPS'),
(44, 'Saludcoop EPS'),
(45, 'Selvasalud EPS'),
(46, 'Solsalud EPS'),
(47, 'Colmédica'),
(48, 'Colsánitas'),
(49, 'Sánitas'),
(50, 'Aliansalud'),
(51, 'coomeva'),
(52, 'comeva'),
(53, 'compensar'),
(54, 'no tiene'),
(55, 'famisanar'),
(56, 'pendiente'),
(57, 'susalud'),
(58, 'salub total'),
(59, 'no'),
(60, 'CRUS BLANCA'),
(61, 'conpensar'),
(62, 'medicol');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_medidas`
--

CREATE TABLE `conf_medidas` (
  `id_medida` int(10) UNSIGNED NOT NULL COMMENT 'Código de la medida a tomar',
  `id_tpmedida` int(10) UNSIGNED NOT NULL COMMENT 'Tipo de medida (Ref. conf_tipo_medida)',
  `genero` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'T' COMMENT 'Genero asociado a la medida (T-Todos, M-masculino, F-Femenino)',
  `nombre` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la medida',
  `calculable` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si el valor es calculable basado en una fórmula matemática (S) o no (N)',
  `formula` text COLLATE latin1_spanish_ci COMMENT 'Fórmula matemática para el cálculo de la medida. Los valores requeridos de otras medidas se expresan entre [ ] usando el código ',
  `unidad` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Unidad de medida',
  `orden` int(3) UNSIGNED NOT NULL COMMENT 'Orden de presentación de la medida en el formulario'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Medidas de la ficha antropométrica de un usuario';

--
-- Volcado de datos para la tabla `conf_medidas`
--

INSERT INTO `conf_medidas` (`id_medida`, `id_tpmedida`, `genero`, `nombre`, `calculable`, `formula`, `unidad`, `orden`) VALUES
(1, 1, 'T', 'Pecho', 'N', NULL, 'cm', 1),
(2, 1, 'T', 'Brazo', 'N', NULL, 'cm', 2),
(3, 1, 'T', 'Cintura', 'N', NULL, 'cm', 3),
(4, 1, 'F', 'Cadera', 'N', NULL, 'cm', 4),
(5, 1, 'T', 'Muslo', 'N', NULL, 'cm', 5),
(6, 1, 'T', 'Pantorilla', 'N', NULL, 'cm', 6),
(7, 2, 'T', 'Triceps', 'N', NULL, 'mm', 1),
(8, 2, 'T', 'Subscapular', 'N', NULL, 'mm', 2),
(9, 2, 'T', 'Supra Iliaco', 'N', NULL, 'mm', 3),
(10, 2, 'T', 'Abdominal', 'N', NULL, 'mm', 4),
(11, 2, 'T', 'Muslo', 'N', NULL, 'mm', 5),
(12, 2, 'T', 'Pantorrilla', 'N', NULL, 'mm', 6),
(13, 1, 'T', 'Peso', 'N', NULL, 'Kg', 7),
(14, 1, 'T', 'Estatura', 'N', NULL, 'cm', 8),
(15, 3, 'M', 'Porcentaje Graso', 'S', '([7]+[8]+[9]+[10]+[11]+[12])*0.097+3.64', '%', 1),
(16, 3, 'F', 'Porcentaje Graso', 'S', '([7]+[8]+[9]+[10]+[11]+[12])*0.1429+4.56', '%', 1),
(17, 3, 'M', 'Peso Graso', 'S', '[13]*(([7]+[8]+[9]+[10]+[11]+[12])*0.097+3.64)/100', 'Kg', 2),
(18, 3, 'F', 'Peso Graso', 'S', '[13]*(([7]+[8]+[9]+[10]+[11]+[12])*0.1429+4.56)/100', 'Kg', 2),
(19, 3, 'T', 'I.M.C', 'S', '[13]/(([14]/100)*([14]/100))', 'Kg/m^2', 3),
(20, 3, 'M', 'Peso Ideal', 'S', '48.08+(([14]-152.4)/2.54)*2.720 ', 'Kg', 4),
(21, 3, 'F', 'Peso Ideal', 'S', '45.35+(([14]-152.4)/2.54)*2.267', 'Kg', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_parametros`
--

CREATE TABLE `conf_parametros` (
  `id_parametro` int(10) UNSIGNED NOT NULL COMMENT 'Código interno del parámetro',
  `id_tpparametro` int(10) UNSIGNED NOT NULL COMMENT 'Tipo de parámetro (Ref. conf_tipos_parametro)',
  `codigo` varchar(20) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Código asignado al parámetro',
  `descripcion` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Descripción de parámetro',
  `valor` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Valor asignado al parámetro'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Parámetros de la aplicación';

--
-- Volcado de datos para la tabla `conf_parametros`
--

INSERT INTO `conf_parametros` (`id_parametro`, `id_tpparametro`, `codigo`, `descripcion`, `valor`) VALUES
(1, 1, 'SKIN', 'Tema y estilos de la aplicación', 'default'),
(2, 1, 'HINI', 'Hora de apertura del Spa', '06:00'),
(3, 1, 'HFIN', 'Hora de cierre del Spa', '21:00'),
(7, 2, 'TICA', 'Tiempo máximo requerido para una cancelación (Horas)', '6'),
(8, 5, 'PEOB', 'Permitir a los clientes establecer sus propios objetivos de entrenamiento', 'N'),
(9, 5, 'PEST', 'Activar el módulo de pestañas', 'S'),
(10, 2, 'MNPE', 'Número de mantenimientos para pestañas', '4'),
(12, 2, 'FRMN', 'Frecuencia de mantenimientos (dias)', '20'),
(14, 2, 'COFA', 'Número actual de facturación', '3064');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_parametros_lista`
--

CREATE TABLE `conf_parametros_lista` (
  `id_lista` int(10) UNSIGNED NOT NULL COMMENT 'Código del item',
  `id_parametro` int(10) UNSIGNED NOT NULL COMMENT 'Parámetro asociado (Ref. conf_parametros)',
  `texto` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Texto a mostrar en lista desplegable',
  `valor` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Valor a almacenar en el parámetro'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_prepagadas`
--

CREATE TABLE `conf_prepagadas` (
  `id_prepagada` int(10) UNSIGNED NOT NULL COMMENT 'Código de la prepagada',
  `nombre` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre de la prepagada'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Entidades de salud de medicina prepagada';

--
-- Volcado de datos para la tabla `conf_prepagadas`
--

INSERT INTO `conf_prepagadas` (`id_prepagada`, `nombre`) VALUES
(1, 'Cafesalud MP'),
(2, 'Colsanitas MP'),
(3, 'Comfenalco Valle'),
(4, 'Humana MP'),
(5, 'Medisalud MP'),
(6, 'Medisanitas MP'),
(7, 'Colmédica MP'),
(8, 'Salud Colpatria'),
(9, 'Coomeva MP'),
(10, 'Servicio de Salud Inmediato MP'),
(11, 'SuSalud MP'),
(12, 'Seguros Bolívar'),
(13, 'colsanitas'),
(14, 'comeva'),
(15, 'compensar'),
(16, 'no tiene'),
(17, 'pendiente'),
(18, 'susalud'),
(19, 'ermermi'),
(20, 'Aliansalud'),
(21, 'sanitas'),
(22, 'famisanar'),
(23, 'colmedica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_sedes`
--

CREATE TABLE `conf_sedes` (
  `id_sede` int(5) NOT NULL COMMENT 'Código de la sede',
  `nombre` varchar(250) NOT NULL COMMENT 'Nombre único de la sede',
  `pais` varchar(100) NOT NULL COMMENT 'Nombre del pais de la sede',
  `ciudad` varchar(100) NOT NULL COMMENT 'Nombre de la ciudad de la sede',
  `direccion` varchar(300) NOT NULL COMMENT 'Direccion de la sede ',
  `telefono` varchar(100) NOT NULL COMMENT 'Numero de telefono de la sede',
  `domicilio` varchar(1) NOT NULL COMMENT 'Indica si la sede se dedica a hacer  solo servicio a domicilio (S) o No  (N)',
  `Num_factura` int(10) NOT NULL COMMENT 'Consecutivo de facturación  para la sede',
  `Pref_factura` varchar(10) NOT NULL COMMENT 'Prefijo de la factura (si aplica)',
  `Activa` varchar(1) NOT NULL COMMENT 'indica si la sede está activa (S) o no  (N)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `conf_sedes`
--

INSERT INTO `conf_sedes` (`id_sede`, `nombre`, `pais`, `ciudad`, `direccion`, `telefono`, `domicilio`, `Num_factura`, `Pref_factura`, `Activa`) VALUES
(1, 'sede Prueba 1 ', 'Cll94ANo 68B-41', 'Bogota', 'Cll94ANo 68B-41', '241111', 'S', 14, 'Pru1', 'S'),
(2, 'Sede prueba 2', 'Cll 9 No 7', 'Bogota', 'Cll 9 No 7', '2', 'S', 25, 'Pru2', 'S'),
(3, 'Sede prueba 3', 'Cll 85-48', 'Bogota', 'Cll 85-48', '2', 'S', 25, 'Pru3', 'S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_servicios`
--

CREATE TABLE `conf_servicios` (
  `id_servicio` int(10) UNSIGNED NOT NULL COMMENT 'Código del servicio',
  `nombre` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre del servicio',
  `descripcion` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'Descripción del servicio',
  `precio_base` float UNSIGNED NOT NULL COMMENT 'Precio del servicio antes de impuestos',
  `impuesto` float NOT NULL COMMENT 'Impuesto o gravamen para el servicio',
  `prepagado` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si el servicio debe ser prepagado por el cliente (S) o no (N)',
  `programable` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'S' COMMENT 'Indica si el servicio es programable solo por el Spa (S), por el spa y el cliente (C) o no es programable (N)',
  `ficha_antrop` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si requiere ficha antropométrica (S) o no (N)',
  `sesion_minima` int(4) UNSIGNED DEFAULT NULL COMMENT 'Indica la duración mínima de una sesión del servicio en minutos',
  `modulo_pestanas` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si el servicio está asociado al módulo de pestañas',
  `dias_vencimiento` int(3) UNSIGNED DEFAULT NULL COMMENT 'Días previos al vencimiento para notificaciones por email',
  `dias_mantenimiento` int(3) UNSIGNED DEFAULT NULL COMMENT 'Días necesarios para que el cliente programe su mantenimiento'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Servicios ofrecidos por la Compañía';

--
-- Volcado de datos para la tabla `conf_servicios`
--

INSERT INTO `conf_servicios` (`id_servicio`, `nombre`, `descripcion`, `precio_base`, `impuesto`, `prepagado`, `programable`, `ficha_antrop`, `sesion_minima`, `modulo_pestanas`, `dias_vencimiento`, `dias_mantenimiento`) VALUES
(10, 'Set nuevos', '', 1, 1, 'S', 'S', 'S', 120, 'S', 20, 15),
(11, 'Mantenimientos', '', 1, 1, 'S', 'S', 'S', 60, 'S', 15, 20),
(12, 'Depilación', '', 1, 1, 'N', 'S', 'N', 30, 'N', 20, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_servicios_x_sede`
--

CREATE TABLE `conf_servicios_x_sede` (
  `id_sede` int(5) NOT NULL COMMENT 'Código de la sede (Ref.conf_sedes)',
  `id_servicio` int(10) NOT NULL COMMENT 'Codigo de los servicios de la sede(Ref.conf_servicios)',
  `sesiones_simultaneas` int(3) NOT NULL COMMENT 'Cantidad de sesiones simultaneas que se pueden presentar'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `conf_servicios_x_sede`
--

INSERT INTO `conf_servicios_x_sede` (`id_sede`, `id_servicio`, `sesiones_simultaneas`) VALUES
(1, 10, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_tabla_medidas`
--

CREATE TABLE `conf_tabla_medidas` (
  `id_det_medida` int(10) UNSIGNED NOT NULL COMMENT 'Código del detalle de la medida',
  `id_medida` int(10) UNSIGNED NOT NULL COMMENT 'Medida asociada (Ref. conf_medidas)',
  `rango_min` float NOT NULL COMMENT 'Rango mínimo de la medida',
  `rango_max` float NOT NULL COMMENT 'Rango Máximo de la medida',
  `interpretacion` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Interpretación del rango ingresado',
  `anotaciones` text COLLATE latin1_spanish_ci COMMENT 'Anotaciones asociadas a los datos ingresados'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tablas oficiales de las medidas y su interpretación';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_tipos_parametro`
--

CREATE TABLE `conf_tipos_parametro` (
  `id_tpparametro` int(10) UNSIGNED NOT NULL COMMENT 'Código del tipo de parámetro',
  `nombre` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre descripctivo del tipo de parámetro'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de parámetro disponibles';

--
-- Volcado de datos para la tabla `conf_tipos_parametro`
--

INSERT INTO `conf_tipos_parametro` (`id_tpparametro`, `nombre`) VALUES
(1, 'Texto'),
(2, 'Número'),
(3, 'Fecha'),
(4, 'Lista'),
(5, 'S/N');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_tipo_id`
--

CREATE TABLE `conf_tipo_id` (
  `id_tipoid` int(10) UNSIGNED NOT NULL COMMENT 'Código del tipo de documento de identidad',
  `nombre` varchar(50) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre del documento de identidad',
  `abreviatura` varchar(4) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de documento de identidad';

--
-- Volcado de datos para la tabla `conf_tipo_id`
--

INSERT INTO `conf_tipo_id` (`id_tipoid`, `nombre`, `abreviatura`) VALUES
(0, 'Inlife ID', 'ILID'),
(1, 'Cédula de Ciudadanía', 'CC'),
(2, 'Cédula de Extranjería', 'CE'),
(3, 'Pasaporte', 'PA'),
(4, 'Documento Extranjero', 'FI'),
(5, 'Número Único de Identificación Personal', 'NUIP'),
(6, 'Número de Identificación Tributaria', 'NIT');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_tipo_medidas`
--

CREATE TABLE `conf_tipo_medidas` (
  `id_tpmedida` int(10) UNSIGNED NOT NULL COMMENT 'Código del tipo de medida',
  `nombre` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre del tipo de medida'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de medidas';

--
-- Volcado de datos para la tabla `conf_tipo_medidas`
--

INSERT INTO `conf_tipo_medidas` (`id_tpmedida`, `nombre`) VALUES
(1, 'Antropometría'),
(2, 'Adipometría'),
(3, 'Totales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_tipo_perfil`
--

CREATE TABLE `conf_tipo_perfil` (
  `id_perfil` int(10) UNSIGNED NOT NULL COMMENT 'Código del tipo de perfil',
  `nombre` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Descripción del tipo de perfil'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de perfil de usuarios';

--
-- Volcado de datos para la tabla `conf_tipo_perfil`
--

INSERT INTO `conf_tipo_perfil` (`id_perfil`, `nombre`) VALUES
(1, 'Super Administrador'),
(2, 'Especialista'),
(3, 'Cliente'),
(4, 'Administrador de Sede');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_tipo_pestana`
--

CREATE TABLE `conf_tipo_pestana` (
  `id_tipo_pestana` int(10) UNSIGNED NOT NULL COMMENT 'Código del tipo de pestaña usada',
  `referencia` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Referencia del tipo de pestaña'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de pestañas';

--
-- Volcado de datos para la tabla `conf_tipo_pestana`
--

INSERT INTO `conf_tipo_pestana` (`id_tipo_pestana`, `referencia`) VALUES
(36, '3D-09-C'),
(42, '3D-09-N'),
(37, '3D-10-C'),
(43, '3D-10-N'),
(38, '3D-11-C'),
(44, '3D-11-N'),
(39, '3D-12-C'),
(45, '3D-12-N'),
(40, '3D-13-C'),
(46, '3D-13-N'),
(41, '3D-14-C'),
(47, '3D-14-N'),
(1, 'C-15-09-S-C'),
(7, 'C-15-09-S-N'),
(2, 'C-15-10-S-C'),
(8, 'C-15-10-S-N'),
(3, 'C-15-11-S-C'),
(9, 'C-15-11-S-N'),
(4, 'C-15-12-S-C'),
(10, 'C-15-12-S-N'),
(5, 'C-15-13-S-C'),
(11, 'C-15-13-S-N'),
(6, 'C-15-14-S-C'),
(12, 'C-15-14-S-N'),
(13, 'C-20-09-S-C'),
(19, 'C-20-09-S-N'),
(14, 'C-20-10-S-C'),
(20, 'C-20-10-S-N'),
(15, 'C-20-11-S-C'),
(21, 'C-20-11-S-N'),
(16, 'C-20-12-S-C'),
(22, 'C-20-12-S-N'),
(17, 'C-20-13-S-C'),
(23, 'C-20-13-S-N'),
(18, 'C-20-14-S-C'),
(24, 'C-20-14-S-N'),
(25, 'D-15-09-S-C'),
(30, 'D-15-09-S-N'),
(26, 'D-15-10-S-C'),
(31, 'D-15-10-S-N'),
(32, 'D-15-11-S-N'),
(27, 'D-15-12-S-C'),
(33, 'D-15-12-S-N'),
(28, 'D-15-13-S-C'),
(34, 'D-15-13-S-N'),
(29, 'D-15-14-S-C'),
(35, 'D-15-14-S-N'),
(48, 'V-09-C'),
(54, 'V-09-N'),
(49, 'V-10-C'),
(55, 'V-10-N'),
(50, 'V-11-C'),
(56, 'V-11-N'),
(51, 'V-12-C'),
(57, 'V-12-N'),
(52, 'V-13-C'),
(58, 'V-13-N'),
(53, 'V-14-C'),
(59, 'V-14-N');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fact_detalle`
--

CREATE TABLE `fact_detalle` (
  `id_detalle` int(10) UNSIGNED NOT NULL COMMENT 'Código del detalle',
  `id_factura` int(10) UNSIGNED NOT NULL COMMENT 'Código de la factura (Ref. fact_facturacion)',
  `id_producto` int(10) UNSIGNED DEFAULT NULL COMMENT 'Código del producto (ref. fact_productos)',
  `id_servicio` int(10) UNSIGNED DEFAULT NULL COMMENT 'Código del servicio (ref. conf_servicios)',
  `cantidad` int(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Cantidad adquirida',
  `valor_unitario` float UNSIGNED NOT NULL COMMENT 'Valor unitarios del producto adquirido',
  `iva` float UNSIGNED NOT NULL COMMENT 'Valor del impuesto sobre las ventas (porcentual)',
  `descuento` float UNSIGNED NOT NULL COMMENT 'Descuento porcentual otorgado',
  `valor_descuento` float UNSIGNED NOT NULL COMMENT 'Valor del descuento',
  `total` float UNSIGNED NOT NULL COMMENT 'Total del producto o servicio adquirido'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Detalle de items facturados';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fact_facturacion`
--

CREATE TABLE `fact_facturacion` (
  `id_factura` int(10) UNSIGNED NOT NULL COMMENT 'Código interno de la factura',
  `num_factura` varchar(45) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Número de la factura (puede contener letras)',
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del cliente (Ref. segu_usuarios)',
  `fecha` datetime NOT NULL COMMENT 'fecha de la factura',
  `descuento` float UNSIGNED NOT NULL COMMENT 'Valor acumulado de descuento para la factura',
  `total` float UNSIGNED NOT NULL COMMENT 'Valor total de la factura',
  `pagado` float UNSIGNED NOT NULL COMMENT 'Valor pagado de la factura',
  `fecha_ult_pago` date DEFAULT NULL COMMENT 'Fecha del último pago realizado',
  `estado` varchar(3) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'PRC' COMMENT 'Estado de la factura - PRC (En proceso) - FAC (Facturada) - PPA (Pagada parcialmente) - OK (Pagada)',
  `cajero` int(10) UNSIGNED NOT NULL COMMENT 'Código de identificación del cajero (ref. segu_usuarios)',
  `tipo_pago` varchar(2) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'EF' COMMENT 'Medio de pago usado (EF-Efectivo, TC-Tarjeta Crédito, TD-Tarjeta Débito, CH-Cheque',
  `id_sede` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Facturación de Inlife Studio';

--
-- Volcado de datos para la tabla `fact_facturacion`
--

INSERT INTO `fact_facturacion` (`id_factura`, `num_factura`, `id_usuario`, `fecha`, `descuento`, `total`, `pagado`, `fecha_ult_pago`, `estado`, `cajero`, `tipo_pago`, `id_sede`) VALUES
(7236, NULL, 3900, '2016-05-14 14:42:00', 0, 0, 0, NULL, 'PRC', 3899, 'EF', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fact_productos`
--

CREATE TABLE `fact_productos` (
  `id_producto` int(10) UNSIGNED NOT NULL COMMENT 'Código del producto',
  `referencia` varchar(200) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Código o referencia del producto',
  `nombre` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Descripción del producto',
  `valor` float UNSIGNED NOT NULL COMMENT 'Valor unitario',
  `iva` float UNSIGNED NOT NULL COMMENT 'Porcentaje de impuesto sobre las ventas'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Productos facturables';

--
-- Volcado de datos para la tabla `fact_productos`
--

INSERT INTO `fact_productos` (`id_producto`, `referencia`, `nombre`, `valor`, `iva`) VALUES
(2, 'Ext02', 'Pestañina', 80000, 0),
(3, 'Ext 03', 'Lash Food', 200000, 0),
(4, 'Producto Extensiones 1', 'Desmaquillador', 80000, 0),
(5, 'Depilación facial hilo hindú.', 'Depilación facial hilo hindú cara completa', 50000, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noti_envio_mensajes`
--

CREATE TABLE `noti_envio_mensajes` (
  `id_usuario` int(10) NOT NULL,
  `email` varchar(250) COLLATE latin1_spanish_ci NOT NULL,
  `tipo` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `resultado` varchar(3) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Resultado de envio de notificaciones';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `segu_perfil_x_usuario`
--

CREATE TABLE `segu_perfil_x_usuario` (
  `id_perf_unico` int(10) UNSIGNED NOT NULL COMMENT 'Código único de identificación del perfil del usuario',
  `id_perfil` int(10) UNSIGNED NOT NULL COMMENT 'Código del perfil (Ref. conf_tipo_perfil)',
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del usuario (Ref. segu_usuarios)',
  `estado` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'A' COMMENT 'estado del perfil del usuario (A - Activo - I Inactivo)',
  `fecha` date DEFAULT NULL COMMENT 'Fecha de última actualización',
  `login_mod` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Login del usuario que realiza la actualización',
  `id_sede` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Perfiles de cada usuario';

--
-- Volcado de datos para la tabla `segu_perfil_x_usuario`
--

INSERT INTO `segu_perfil_x_usuario` (`id_perf_unico`, `id_perfil`, `id_usuario`, `estado`, `fecha`, `login_mod`, `id_sede`) VALUES
(1, 1, 1, 'A', '2015-06-10', 'webmaster', NULL),
(3900, 4, 3899, 'A', '2016-05-14', 'webmaster', 1),
(3901, 3, 3900, 'A', '2016-05-14', 'madmin', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `segu_programas`
--

CREATE TABLE `segu_programas` (
  `id_programa` int(10) UNSIGNED NOT NULL COMMENT 'Código del programa',
  `descripcion` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Descripción del programa',
  `archivo` varchar(128) COLLATE latin1_spanish_ci NOT NULL COMMENT 'nombre del archivo del programa',
  `id_programa_padre` int(10) UNSIGNED DEFAULT NULL COMMENT 'Código del programa padre (si aplica)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Programas del sistema';

--
-- Volcado de datos para la tabla `segu_programas`
--

INSERT INTO `segu_programas` (`id_programa`, `descripcion`, `archivo`, `id_programa_padre`) VALUES
(1, 'Encabezado de las páginas', 'layout_header.php', NULL),
(2, 'Menú lateral de las páginas', 'layout_menu_lateral.php', NULL),
(3, 'Configuración del sistema', 'sysconfig_frm.php', NULL),
(4, 'Control de actualización de parámetros', 'exec_upd_config.php', 3),
(5, 'Configuración de servicios ofrecidos', 'servicios_lst.php', NULL),
(6, 'Formulario para configuración de servicios', 'servicios_frm.php', NULL),
(7, 'Control de actualización de servicios', 'exec_upd_servicios.php', 6),
(8, 'Eliminación de servicios', 'exec_del_servicios.php', NULL),
(9, 'Listado de usuarios del sistema', 'usuarios_lst.php', NULL),
(10, 'Formulario de administración de usuarios del sistema', 'usuarios_frm.php', NULL),
(11, 'Control de actualización de usuarios', 'exec_upd_usuario.php', 10),
(12, 'Eliminación de usuarios del sistema', 'exec_del_usuario.php', NULL),
(13, 'Listado de clientes', 'clientes_lst.php', NULL),
(14, 'Formulario de creación o modificación de clientes', 'clientes_frm.php', NULL),
(15, 'Eliminación de clientes', 'exec_del_cliente.php', NULL),
(16, 'Lista de servicios prepagados por el cliente', 'cliente_servicios_lst.php', 13),
(17, 'Formato de asignación de servicios prepagados', 'cliente_servicios_frm.php', 16),
(18, 'Ficha antropométrica del cliente', 'cliente_ficha_frm.php', NULL),
(19, 'Actualización de servicios del cliente', 'exec_upd_servcliente.php', 17),
(20, 'Eliminación de servicios del cliente', 'exec_del_servcliente.php', NULL),
(21, 'Actualización de la ficha antropométrica del cliente', 'exec_upd_fichacliente.php', 18),
(22, 'Programación de sesiones', 'programacion_lst.php', NULL),
(23, 'Formato de programación de sesiones', 'programacion_frm.php', NULL),
(24, 'Formato de programación de sesiones por parte del cliente', 'programacion_cliente_frm.php', NULL),
(25, 'Àctualización de programación (administrador)', 'exec_upd_program_adm.php', 23),
(26, 'Actualización de programación (cliente)', 'exec_upd_program_clie.php', 24),
(27, 'Eliminación de programación de sesión', 'exec_del_programacion_adm.php', NULL),
(28, 'Formato de seguimiento de asistencia del cliente', 'programacion_asistencia_frm.php', NULL),
(29, 'Seguimiento de asistencia del cliente', 'exec_upd_asistencia.php', 28),
(30, 'Sitio Inlife del cliente', 'myinlife.php', NULL),
(31, 'Portlet con ficha antropometrica del cliente', 'portlet_ficha_frm.php', 30),
(32, 'Portlet con programación del cliente', 'portlet_programacion_frm.php', 30),
(33, 'Actualización de objetivos via AJAX', 'ajax_upd_objetivo.php', NULL),
(34, 'Formulario de ingreso de tratamiento de pestañas', 'cliente_pestanas_frm.php', NULL),
(35, 'Actualización de tratamiento de pestañas del cliente', 'exec_upd_pestanas.php', 34),
(36, 'Listado de productos ', 'productos_lst.php', NULL),
(37, 'Creación / modificación de productos', 'productos_frm.php', NULL),
(38, 'Control de actualización de productos', 'exec_upd_productos.php', 37),
(39, 'Eliminación de productos', 'exec_del_productos.php', NULL),
(40, 'Generación de facturas ', 'factura_frm.php', NULL),
(41, 'Actualización de estado de factura', 'ajax_upd_factura.php', NULL),
(42, 'Lista de facturas emitidas', 'facturacion_lst.php', NULL),
(43, 'Restablecimiento de la contraseña del usuario', 'exec_reset_pwd.php', NULL),
(44, 'Eliminación de registros de ficha antropométrica', 'exec_del_ficha.php', NULL),
(45, 'Eliminación de comentarios de la ficha antropométrica', 'exec_del_comentario.php', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `segu_programas_x_perfil`
--

CREATE TABLE `segu_programas_x_perfil` (
  `id_perfil` int(10) UNSIGNED NOT NULL COMMENT 'Perfil con acceso (Ref. conf_tipo_perfil)',
  `id_programa` int(10) UNSIGNED NOT NULL COMMENT 'Programa del sistema (ref. segu_programas)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Programas accesibles por cada perfil';

--
-- Volcado de datos para la tabla `segu_programas_x_perfil`
--

INSERT INTO `segu_programas_x_perfil` (`id_perfil`, `id_programa`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 25),
(1, 27),
(1, 28),
(1, 29),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(2, 1),
(2, 2),
(2, 11),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 27),
(2, 28),
(2, 29),
(2, 33),
(2, 34),
(2, 35),
(2, 40),
(2, 41),
(2, 42),
(3, 1),
(3, 2),
(3, 18),
(3, 22),
(3, 24),
(3, 26),
(3, 27),
(3, 30),
(3, 31),
(3, 32),
(3, 33),
(4, 1),
(4, 2),
(4, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 11),
(4, 12),
(4, 13),
(4, 14),
(4, 15),
(4, 16),
(4, 17),
(4, 18),
(4, 19),
(4, 20),
(4, 21),
(4, 22),
(4, 23),
(4, 24),
(4, 25),
(4, 27),
(4, 28),
(4, 29),
(4, 33),
(4, 34),
(4, 35),
(4, 36),
(4, 40),
(4, 41),
(4, 42),
(4, 43),
(4, 44),
(4, 45);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `segu_usuarios`
--

CREATE TABLE `segu_usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del usuario',
  `id_tipoid` int(10) UNSIGNED NOT NULL COMMENT 'Tipo de documento de identidad (Ref. conf_tipos_id)',
  `numero_id` varchar(45) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Número del documento de identidad',
  `nombres` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombres completos del usuario',
  `apellidos` varchar(100) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Apellidos del usuario',
  `telefono` varchar(30) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Teléfono de contacto',
  `celular` varchar(30) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Número de teléfono celular',
  `email` varchar(200) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Correo electrónico',
  `genero` varchar(1) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Genero del usuario (M- Masculino, F - Femenino)',
  `fecha_nacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento del usuario',
  `id_eps` int(10) UNSIGNED DEFAULT NULL COMMENT 'EPS del usuario (Ref. conf_eps)',
  `id_prepagada` int(10) UNSIGNED DEFAULT NULL COMMENT 'Prepagada del usuario (Ref. conf_prepagada)',
  `descripcion` text COLLATE latin1_spanish_ci COMMENT 'Descripción personal del usuario',
  `fecha_ingreso` date NOT NULL COMMENT 'Fecha de ingreso al sistema',
  `login` varchar(15) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Login del usuario',
  `pwd` varchar(32) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Contraseña del usuario',
  `notificar` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'S' COMMENT 'Enviar notificaciones por email',
  `multisede` varchar(1) COLLATE latin1_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Usuarios del sistema';

--
-- Volcado de datos para la tabla `segu_usuarios`
--

INSERT INTO `segu_usuarios` (`id_usuario`, `id_tipoid`, `numero_id`, `nombres`, `apellidos`, `telefono`, `celular`, `email`, `genero`, `fecha_nacimiento`, `id_eps`, `id_prepagada`, `descripcion`, `fecha_ingreso`, `login`, `pwd`, `notificar`, `multisede`) VALUES
(1, 0, '0', 'Webmaster', 'iNlash & Co', 'N/A', NULL, 'notificaciones@inlifestudio.com', 'F', '2012-05-24', NULL, NULL, 'Webmaster del sistema de inlife Studio', '2012-05-24', 'webmaster', 'a8698009bce6d1b8c2128eddefc25aad', 'S', NULL),
(3899, 1, '1018484513', 'Manuel', 'Admin Sed', '2136565', '3183771785', 'manuelfedss@hotmail.com', 'M', '1962-05-15', NULL, NULL, 'Nada', '2016-05-11', 'madmin', '827ccb0eea8a706c4c34a16891f84e7b', 'N', 'N'),
(3900, 1, '79280440', 'Manuel', 'Cliente Pru', '2518095', '3183445551', 'manuelfedss@hotmail.com', 'M', '1969-05-20', 53, 15, NULL, '2016-05-11', 'mcliente', 'a6041e6f9ef9a2701177a6179e71e110', 'S', 'S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_anotaciones`
--

CREATE TABLE `spa_anotaciones` (
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del usuario (Ref. segu_usuarios)',
  `fecha` date NOT NULL COMMENT 'Fecha de la anotación',
  `texto` text COLLATE latin1_spanish_ci NOT NULL COMMENT 'Texto de la anotación',
  `login_mod` varchar(15) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Login del usuario que hace la observacion'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Anotaciones de la ficha antropológica del cliente';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_bloqueo_estacion`
--

CREATE TABLE `spa_bloqueo_estacion` (
  `id_programacion` int(10) UNSIGNED NOT NULL COMMENT 'Código de la sesión programada (Ref. spa_programacion)',
  `maquina` int(3) UNSIGNED NOT NULL COMMENT 'número de estación bloquedad'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Bloqueo de estaciones para servicios especiales';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_ficha_antro`
--

CREATE TABLE `spa_ficha_antro` (
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del cliente',
  `id_medida` int(10) UNSIGNED NOT NULL COMMENT 'Código de la medida (Ref. conf_medidas)',
  `fecha` date NOT NULL COMMENT 'Fecha de toma de la muestra',
  `valor` float NOT NULL COMMENT 'Valor de la medida tomada',
  `objetivo` varchar(1) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Objetivo con las medidas (S- Subir, B - Bajar)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Ficha antropométrica del cliente';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_ficha_resp`
--

CREATE TABLE `spa_ficha_resp` (
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del cliente',
  `fecha` date NOT NULL COMMENT 'Fecha de toma de medidas',
  `entrenador` varchar(250) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Nombre del entrenador que tomó las medidas'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Responsables de cada toma de medidas al cliente';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_pestanas`
--

CREATE TABLE `spa_pestanas` (
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del cliente (Ref. segu_usuarios)',
  `id_pestana_1` int(10) UNSIGNED NOT NULL COMMENT 'Referencia de pestanas 1',
  `id_pestana_2` int(10) UNSIGNED NOT NULL COMMENT 'Referencia de pestanas 2',
  `id_pestana_3` int(10) UNSIGNED NOT NULL COMMENT 'Referencia de pestanas 3',
  `fecha_postura` date NOT NULL COMMENT 'fecha de colocación de las petañas',
  `fecha_ult_mantenimiento` date DEFAULT NULL COMMENT 'Fecha del último mantenimiento',
  `mantenimientos` int(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cantidad de mantenimientos realizados'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Seguimiento de colocación de pestañas por cliente';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_programacion`
--

CREATE TABLE `spa_programacion` (
  `id_programacion` int(10) UNSIGNED NOT NULL COMMENT 'Código de la programación',
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Código del usuario al que se le programa la sesión',
  `fecha` date NOT NULL COMMENT 'Fecha de la sesión',
  `hora_ini` varchar(5) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Hora inicial programada',
  `hora_fin` varchar(5) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Hora final programada',
  `maquina` int(3) NOT NULL,
  `login_mod` varchar(15) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Login de la persona que programa la sesión',
  `asistencia` varchar(1) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Indica si el usuario asistio o no a la sesion (S/N)',
  `sesion_especial` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si la sesión es para toma de medidas o postura de pestañas (S) o no (N)',
  `comentarios` text COLLATE latin1_spanish_ci COMMENT 'Comentarios sobre la programación de la sesión',
  `cortesia` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si la sesión es una cortesia',
  `id_sede` int(10) DEFAULT NULL,
  `id_servicio` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Programación de servicios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_resthoraria`
--

CREATE TABLE `spa_resthoraria` (
  `id_servicio` int(10) UNSIGNED NOT NULL COMMENT 'Código del servicio (Ref. conf_servicios)',
  `dia` int(1) UNSIGNED NOT NULL COMMENT 'Día de la semana (1- Lunes)',
  `hora_inicio` varchar(5) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Hora Inicial de la restricción',
  `hora_final` varchar(5) COLLATE latin1_spanish_ci NOT NULL COMMENT 'Hora final de la restricción'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Restricción horaria por servicio';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `spa_servicios_x_usuario`
--

CREATE TABLE `spa_servicios_x_usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL COMMENT 'Usuario del servicio (Ref. segu_usuarios)',
  `id_servicio` int(10) UNSIGNED NOT NULL COMMENT 'Servicio prepagado (Ref. conf_servicios)',
  `fecha` date NOT NULL COMMENT 'Fecha de contratación del servicio',
  `caducidad` date DEFAULT NULL COMMENT 'fecha de caducidad del servicio contratado',
  `cantidad` float NOT NULL COMMENT 'cantidad de sesiones / horas contratadas',
  `continuidad` float DEFAULT NULL COMMENT 'Indica el número de sesiones semanales que debe tomar el cliente para demostrar continuidad',
  `congelar` varchar(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'N' COMMENT 'Indica si el servicio está congelado o no para el cliente',
  `fecha_cambio` date DEFAULT NULL COMMENT 'Fecha de último cambio realizado'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Servicios prepagados por cada usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temp_notif_login`
--

CREATE TABLE `temp_notif_login` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(300) COLLATE latin1_spanish_ci NOT NULL,
  `login` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  `numero_id` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `email` varchar(300) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='tabla temporal de envio de logins';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `conf_eps`
--
ALTER TABLE `conf_eps`
  ADD PRIMARY KEY (`id_eps`);

--
-- Indices de la tabla `conf_medidas`
--
ALTER TABLE `conf_medidas`
  ADD PRIMARY KEY (`id_medida`),
  ADD KEY `tpme_medi_fk` (`id_tpmedida`);

--
-- Indices de la tabla `conf_parametros`
--
ALTER TABLE `conf_parametros`
  ADD PRIMARY KEY (`id_parametro`),
  ADD KEY `tppa_para_fk` (`id_tpparametro`);

--
-- Indices de la tabla `conf_parametros_lista`
--
ALTER TABLE `conf_parametros_lista`
  ADD PRIMARY KEY (`id_lista`),
  ADD KEY `para_pali_fk` (`id_parametro`);

--
-- Indices de la tabla `conf_prepagadas`
--
ALTER TABLE `conf_prepagadas`
  ADD PRIMARY KEY (`id_prepagada`);

--
-- Indices de la tabla `conf_sedes`
--
ALTER TABLE `conf_sedes`
  ADD PRIMARY KEY (`id_sede`);

--
-- Indices de la tabla `conf_servicios`
--
ALTER TABLE `conf_servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `conf_servicios_x_sede`
--
ALTER TABLE `conf_servicios_x_sede`
  ADD PRIMARY KEY (`id_sede`,`id_servicio`);

--
-- Indices de la tabla `conf_tabla_medidas`
--
ALTER TABLE `conf_tabla_medidas`
  ADD PRIMARY KEY (`id_det_medida`),
  ADD KEY `tame_medi_fk` (`id_medida`);

--
-- Indices de la tabla `conf_tipos_parametro`
--
ALTER TABLE `conf_tipos_parametro`
  ADD PRIMARY KEY (`id_tpparametro`);

--
-- Indices de la tabla `conf_tipo_id`
--
ALTER TABLE `conf_tipo_id`
  ADD PRIMARY KEY (`id_tipoid`);

--
-- Indices de la tabla `conf_tipo_medidas`
--
ALTER TABLE `conf_tipo_medidas`
  ADD PRIMARY KEY (`id_tpmedida`);

--
-- Indices de la tabla `conf_tipo_perfil`
--
ALTER TABLE `conf_tipo_perfil`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `conf_tipo_pestana`
--
ALTER TABLE `conf_tipo_pestana`
  ADD PRIMARY KEY (`id_tipo_pestana`),
  ADD UNIQUE KEY `referencia` (`referencia`);

--
-- Indices de la tabla `fact_detalle`
--
ALTER TABLE `fact_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `serv_deta_fk_ix` (`id_servicio`),
  ADD KEY `fact_deta_fk_ix` (`id_factura`),
  ADD KEY `prod_deta_fk_ix` (`id_producto`);

--
-- Indices de la tabla `fact_facturacion`
--
ALTER TABLE `fact_facturacion`
  ADD PRIMARY KEY (`id_factura`),
  ADD UNIQUE KEY `num_factura_uk` (`num_factura`),
  ADD KEY `usua_fact_fk_ix` (`id_usuario`),
  ADD KEY `usua1_fact_fk_ix` (`cajero`),
  ADD KEY `id_sede` (`id_sede`);

--
-- Indices de la tabla `fact_productos`
--
ALTER TABLE `fact_productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `referencia_uk_ix` (`referencia`);

--
-- Indices de la tabla `noti_envio_mensajes`
--
ALTER TABLE `noti_envio_mensajes`
  ADD PRIMARY KEY (`id_usuario`,`fecha`);

--
-- Indices de la tabla `segu_perfil_x_usuario`
--
ALTER TABLE `segu_perfil_x_usuario`
  ADD PRIMARY KEY (`id_perf_unico`),
  ADD KEY `tppf_pfus_fk` (`id_perfil`),
  ADD KEY `usua_pfus_fk` (`id_usuario`),
  ADD KEY `id_sede` (`id_sede`);

--
-- Indices de la tabla `segu_programas`
--
ALTER TABLE `segu_programas`
  ADD PRIMARY KEY (`id_programa`),
  ADD KEY `prog_prog_fk` (`id_programa_padre`);

--
-- Indices de la tabla `segu_programas_x_perfil`
--
ALTER TABLE `segu_programas_x_perfil`
  ADD PRIMARY KEY (`id_perfil`,`id_programa`),
  ADD KEY `tppf_prpf_fk` (`id_perfil`),
  ADD KEY `prog_prpf_fk` (`id_programa`);

--
-- Indices de la tabla `segu_usuarios`
--
ALTER TABLE `segu_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `tpid_usua_fk` (`id_tipoid`),
  ADD KEY `eps_usua_fk` (`id_eps`),
  ADD KEY `prep_usua_fk` (`id_prepagada`);

--
-- Indices de la tabla `spa_anotaciones`
--
ALTER TABLE `spa_anotaciones`
  ADD PRIMARY KEY (`id_usuario`,`fecha`),
  ADD KEY `usua_anot_fk` (`id_usuario`);

--
-- Indices de la tabla `spa_bloqueo_estacion`
--
ALTER TABLE `spa_bloqueo_estacion`
  ADD PRIMARY KEY (`id_programacion`,`maquina`),
  ADD KEY `prog_bles_fk_ix` (`id_programacion`);

--
-- Indices de la tabla `spa_ficha_antro`
--
ALTER TABLE `spa_ficha_antro`
  ADD PRIMARY KEY (`id_usuario`,`id_medida`,`fecha`),
  ADD KEY `usua_fian_fk` (`id_usuario`),
  ADD KEY `medi_fian_fk` (`id_medida`);

--
-- Indices de la tabla `spa_ficha_resp`
--
ALTER TABLE `spa_ficha_resp`
  ADD PRIMARY KEY (`id_usuario`,`fecha`);

--
-- Indices de la tabla `spa_pestanas`
--
ALTER TABLE `spa_pestanas`
  ADD PRIMARY KEY (`id_usuario`,`fecha_postura`),
  ADD KEY `tppe_pest_fk1_ix` (`id_pestana_1`),
  ADD KEY `tppe_pest_fk2_ix` (`id_pestana_2`),
  ADD KEY `tppe_pest_fk3_ix` (`id_pestana_3`),
  ADD KEY `usua_pest_fk_ix` (`id_usuario`);

--
-- Indices de la tabla `spa_programacion`
--
ALTER TABLE `spa_programacion`
  ADD PRIMARY KEY (`id_programacion`),
  ADD KEY `usua_prog_fk` (`id_usuario`),
  ADD KEY `id_sede` (`id_sede`,`id_servicio`);

--
-- Indices de la tabla `spa_resthoraria`
--
ALTER TABLE `spa_resthoraria`
  ADD PRIMARY KEY (`id_servicio`,`dia`,`hora_inicio`),
  ADD KEY `serv_reho_fk` (`id_servicio`);

--
-- Indices de la tabla `spa_servicios_x_usuario`
--
ALTER TABLE `spa_servicios_x_usuario`
  ADD PRIMARY KEY (`id_usuario`,`id_servicio`,`fecha`),
  ADD KEY `usua_svus_fk` (`id_usuario`),
  ADD KEY `serv_svus_fk` (`id_servicio`);

--
-- Indices de la tabla `temp_notif_login`
--
ALTER TABLE `temp_notif_login`
  ADD PRIMARY KEY (`login`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `conf_eps`
--
ALTER TABLE `conf_eps`
  MODIFY `id_eps` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código de la EPS', AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT de la tabla `conf_medidas`
--
ALTER TABLE `conf_medidas`
  MODIFY `id_medida` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código de la medida a tomar', AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT de la tabla `conf_parametros`
--
ALTER TABLE `conf_parametros`
  MODIFY `id_parametro` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código interno del parámetro', AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT de la tabla `conf_parametros_lista`
--
ALTER TABLE `conf_parametros_lista`
  MODIFY `id_lista` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del item';
--
-- AUTO_INCREMENT de la tabla `conf_prepagadas`
--
ALTER TABLE `conf_prepagadas`
  MODIFY `id_prepagada` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código de la prepagada', AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `conf_sedes`
--
ALTER TABLE `conf_sedes`
  MODIFY `id_sede` int(5) NOT NULL AUTO_INCREMENT COMMENT 'Código de la sede', AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `conf_servicios`
--
ALTER TABLE `conf_servicios`
  MODIFY `id_servicio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del servicio', AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT de la tabla `conf_tabla_medidas`
--
ALTER TABLE `conf_tabla_medidas`
  MODIFY `id_det_medida` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del detalle de la medida';
--
-- AUTO_INCREMENT de la tabla `conf_tipos_parametro`
--
ALTER TABLE `conf_tipos_parametro`
  MODIFY `id_tpparametro` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del tipo de parámetro', AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `conf_tipo_medidas`
--
ALTER TABLE `conf_tipo_medidas`
  MODIFY `id_tpmedida` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del tipo de medida', AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `conf_tipo_perfil`
--
ALTER TABLE `conf_tipo_perfil`
  MODIFY `id_perfil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del tipo de perfil', AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `conf_tipo_pestana`
--
ALTER TABLE `conf_tipo_pestana`
  MODIFY `id_tipo_pestana` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del tipo de pestaña usada', AUTO_INCREMENT=60;
--
-- AUTO_INCREMENT de la tabla `fact_detalle`
--
ALTER TABLE `fact_detalle`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del detalle';
--
-- AUTO_INCREMENT de la tabla `fact_facturacion`
--
ALTER TABLE `fact_facturacion`
  MODIFY `id_factura` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código interno de la factura', AUTO_INCREMENT=7237;
--
-- AUTO_INCREMENT de la tabla `fact_productos`
--
ALTER TABLE `fact_productos`
  MODIFY `id_producto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del producto', AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `segu_perfil_x_usuario`
--
ALTER TABLE `segu_perfil_x_usuario`
  MODIFY `id_perf_unico` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código único de identificación del perfil del usuario', AUTO_INCREMENT=3902;
--
-- AUTO_INCREMENT de la tabla `segu_programas`
--
ALTER TABLE `segu_programas`
  MODIFY `id_programa` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del programa', AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT de la tabla `segu_usuarios`
--
ALTER TABLE `segu_usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código del usuario', AUTO_INCREMENT=3901;
--
-- AUTO_INCREMENT de la tabla `spa_programacion`
--
ALTER TABLE `spa_programacion`
  MODIFY `id_programacion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Código de la programación';
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `conf_medidas`
--
ALTER TABLE `conf_medidas`
  ADD CONSTRAINT `tpme_medi_fk` FOREIGN KEY (`id_tpmedida`) REFERENCES `conf_tipo_medidas` (`id_tpmedida`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `conf_parametros`
--
ALTER TABLE `conf_parametros`
  ADD CONSTRAINT `tppa_para_fk` FOREIGN KEY (`id_tpparametro`) REFERENCES `conf_tipos_parametro` (`id_tpparametro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `conf_parametros_lista`
--
ALTER TABLE `conf_parametros_lista`
  ADD CONSTRAINT `para_pali_fk` FOREIGN KEY (`id_parametro`) REFERENCES `conf_parametros` (`id_parametro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `conf_tabla_medidas`
--
ALTER TABLE `conf_tabla_medidas`
  ADD CONSTRAINT `tame_medi_fk` FOREIGN KEY (`id_medida`) REFERENCES `conf_medidas` (`id_medida`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `fact_detalle`
--
ALTER TABLE `fact_detalle`
  ADD CONSTRAINT `fact_deta_fk` FOREIGN KEY (`id_factura`) REFERENCES `fact_facturacion` (`id_factura`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `prod_deta_fk` FOREIGN KEY (`id_producto`) REFERENCES `fact_productos` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `serv_deta_fk` FOREIGN KEY (`id_servicio`) REFERENCES `conf_servicios` (`id_servicio`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `fact_facturacion`
--
ALTER TABLE `fact_facturacion`
  ADD CONSTRAINT `fact_facturacion_ibfk_1` FOREIGN KEY (`id_sede`) REFERENCES `conf_sedes` (`id_sede`),
  ADD CONSTRAINT `usua1_fact_fk` FOREIGN KEY (`cajero`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `usua_fact_fk` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `segu_perfil_x_usuario`
--
ALTER TABLE `segu_perfil_x_usuario`
  ADD CONSTRAINT `segu_perfil_x_usuario_ibfk_1` FOREIGN KEY (`id_sede`) REFERENCES `conf_sedes` (`id_sede`),
  ADD CONSTRAINT `tppf_pfus_fk` FOREIGN KEY (`id_perfil`) REFERENCES `conf_tipo_perfil` (`id_perfil`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `usua_pfus_fk` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `segu_programas`
--
ALTER TABLE `segu_programas`
  ADD CONSTRAINT `prog_prog_fk` FOREIGN KEY (`id_programa_padre`) REFERENCES `segu_programas` (`id_programa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `segu_programas_x_perfil`
--
ALTER TABLE `segu_programas_x_perfil`
  ADD CONSTRAINT `prog_prpf_fk` FOREIGN KEY (`id_programa`) REFERENCES `segu_programas` (`id_programa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tppf_prpf_fk` FOREIGN KEY (`id_perfil`) REFERENCES `conf_tipo_perfil` (`id_perfil`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `segu_usuarios`
--
ALTER TABLE `segu_usuarios`
  ADD CONSTRAINT `segu_usuarios_ibfk_1` FOREIGN KEY (`id_eps`) REFERENCES `conf_eps` (`id_eps`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `segu_usuarios_ibfk_2` FOREIGN KEY (`id_prepagada`) REFERENCES `conf_prepagadas` (`id_prepagada`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tpid_usua_fk` FOREIGN KEY (`id_tipoid`) REFERENCES `conf_tipo_id` (`id_tipoid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `spa_anotaciones`
--
ALTER TABLE `spa_anotaciones`
  ADD CONSTRAINT `usua_anot_fk` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `spa_bloqueo_estacion`
--
ALTER TABLE `spa_bloqueo_estacion`
  ADD CONSTRAINT `prog_bles_fk` FOREIGN KEY (`id_programacion`) REFERENCES `spa_programacion` (`id_programacion`);

--
-- Filtros para la tabla `spa_ficha_antro`
--
ALTER TABLE `spa_ficha_antro`
  ADD CONSTRAINT `medi_fian_fk` FOREIGN KEY (`id_medida`) REFERENCES `conf_medidas` (`id_medida`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `usua_fian_fk` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `spa_pestanas`
--
ALTER TABLE `spa_pestanas`
  ADD CONSTRAINT `spa_pestanas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `spa_pestanas_ibfk_2` FOREIGN KEY (`id_pestana_1`) REFERENCES `conf_tipo_pestana` (`id_tipo_pestana`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `spa_pestanas_ibfk_3` FOREIGN KEY (`id_pestana_2`) REFERENCES `conf_tipo_pestana` (`id_tipo_pestana`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `spa_pestanas_ibfk_4` FOREIGN KEY (`id_pestana_3`) REFERENCES `conf_tipo_pestana` (`id_tipo_pestana`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `spa_programacion`
--
ALTER TABLE `spa_programacion`
  ADD CONSTRAINT `spa_programacion_ibfk_1` FOREIGN KEY (`id_sede`) REFERENCES `conf_sedes` (`id_sede`),
  ADD CONSTRAINT `spa_programacion_ibfk_2` FOREIGN KEY (`id_sede`,`id_servicio`) REFERENCES `conf_servicios_x_sede` (`id_sede`, `id_servicio`),
  ADD CONSTRAINT `usua_prog_fk` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `spa_resthoraria`
--
ALTER TABLE `spa_resthoraria`
  ADD CONSTRAINT `serv_reho_fk` FOREIGN KEY (`id_servicio`) REFERENCES `conf_servicios` (`id_servicio`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `spa_servicios_x_usuario`
--
ALTER TABLE `spa_servicios_x_usuario`
  ADD CONSTRAINT `serv_svus_fk` FOREIGN KEY (`id_servicio`) REFERENCES `conf_servicios` (`id_servicio`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `usua_svus_fk` FOREIGN KEY (`id_usuario`) REFERENCES `segu_usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
