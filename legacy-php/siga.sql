-- ============================================================
-- SIGA - Sistema Integral de Gestión Avícola
-- Script SQL completo - Base de datos
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS `siga_avicola`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `siga_avicola`;

-- -----------------------------------------------------------
-- Tabla: roles
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(50)  NOT NULL,
  `descripcion` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'administrador', 'Acceso total al sistema'),
(2, 'operario',      'Registro y consulta de datos de galpón');

-- -----------------------------------------------------------
-- Tabla: usuarios
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_completo` VARCHAR(150) NOT NULL,
  `documento`       VARCHAR(30)  NOT NULL UNIQUE,
  `email`           VARCHAR(120) NOT NULL UNIQUE,
  `telefono`        VARCHAR(20),
  `password_hash`   VARCHAR(255) NOT NULL,
  `rol_id`          INT UNSIGNED NOT NULL DEFAULT 2,
  `activo`          TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contraseñas de prueba: Admin123! (ambos usuarios)
INSERT INTO `usuarios` (`nombre_completo`,`documento`,`email`,`telefono`,`password_hash`,`rol_id`) VALUES
('Administrador SIGA', '1000000001', 'admin@siga.com', '3001234567',
 '$2y$12$92bRMlJM.7LMv1Y6bJRRz.wMQlPJMq1hRAy3BpYBPisPgN2/oJ4oC', 1),
('Carlos Operario',    '1000000002', 'operario@siga.com','3109876543',
 '$2y$12$92bRMlJM.7LMv1Y6bJRRz.wMQlPJMq1hRAy3BpYBPisPgN2/oJ4oC', 2);

-- -----------------------------------------------------------
-- Tabla: galpones
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `galpones` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo`              VARCHAR(20)  NOT NULL UNIQUE,
  `nombre`              VARCHAR(100) NOT NULL,
  `ubicacion`           VARCHAR(200),
  `capacidad_maxima`    INT UNSIGNED NOT NULL DEFAULT 0,
  `cantidad_actual`     INT UNSIGNED NOT NULL DEFAULT 0,
  `estado`              ENUM('activo','inactivo','mantenimiento') NOT NULL DEFAULT 'activo',
  `responsable_id`      INT UNSIGNED,
  `created_at`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_galpones_responsable` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `galpones` (`codigo`,`nombre`,`ubicacion`,`capacidad_maxima`,`cantidad_actual`,`estado`,`responsable_id`) VALUES
('G-001','Galpón Norte',   'Sector Norte - Lote 1', 5000, 4800, 'activo', 2),
('G-002','Galpón Sur',     'Sector Sur - Lote 2',   4500, 4200, 'activo', 2),
('G-003','Galpón Central', 'Área Central',           6000, 5500, 'activo', 2),
('G-004','Galpón Este',    'Sector Este - Lote 4',  3000,    0, 'mantenimiento', NULL);

-- -----------------------------------------------------------
-- Tabla: registros_agua
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `registros_agua` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `galpon_id`   INT UNSIGNED NOT NULL,
  `fecha`       DATE         NOT NULL,
  `litros`      DECIMAL(10,2) NOT NULL DEFAULT 0,
  `usuario_id`  INT UNSIGNED NOT NULL,
  `observacion` TEXT,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_agua_galpon`   FOREIGN KEY (`galpon_id`)  REFERENCES `galpones`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agua_usuario`  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabla: registros_alimento
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `registros_alimento` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `galpon_id`   INT UNSIGNED NOT NULL,
  `fecha`       DATE         NOT NULL,
  `kilogramos`  DECIMAL(10,2) NOT NULL DEFAULT 0,
  `usuario_id`  INT UNSIGNED NOT NULL,
  `observacion` TEXT,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_alimento_galpon`  FOREIGN KEY (`galpon_id`)  REFERENCES `galpones`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_alimento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabla: registros_amoniaco
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `registros_amoniaco` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `galpon_id`   INT UNSIGNED NOT NULL,
  `fecha`       DATE         NOT NULL,
  `nivel_ppm`   DECIMAL(8,2) NOT NULL DEFAULT 0,
  `usuario_id`  INT UNSIGNED NOT NULL,
  `observacion` TEXT,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_amoniaco_galpon`  FOREIGN KEY (`galpon_id`)  REFERENCES `galpones`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_amoniaco_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabla: mortalidad
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mortalidad` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `galpon_id`   INT UNSIGNED NOT NULL,
  `fecha`       DATE         NOT NULL,
  `cantidad`    INT UNSIGNED NOT NULL DEFAULT 0,
  `causa`       VARCHAR(200),
  `usuario_id`  INT UNSIGNED NOT NULL,
  `observacion` TEXT,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mortalidad_galpon`  FOREIGN KEY (`galpon_id`)  REFERENCES `galpones`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mortalidad_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabla: alertas
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `alertas` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `galpon_id`   INT UNSIGNED NOT NULL,
  `tipo`        ENUM('amoniaco','agua','alimento','sobrepoblacion','mortalidad') NOT NULL,
  `mensaje`     TEXT NOT NULL,
  `nivel`       ENUM('info','advertencia','critica') NOT NULL DEFAULT 'advertencia',
  `leida`       TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_alertas_galpon` FOREIGN KEY (`galpon_id`) REFERENCES `galpones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Tabla: inventario
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventario` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`             ENUM('alimento','medicamento','insumo') NOT NULL,
  `nombre`           VARCHAR(150) NOT NULL,
  `descripcion`      TEXT,
  `unidad_medida`    VARCHAR(30)  NOT NULL DEFAULT 'kg',
  `stock_actual`     DECIMAL(12,2) NOT NULL DEFAULT 0,
  `stock_minimo`     DECIMAL(12,2) NOT NULL DEFAULT 0,
  `fecha_vencimiento` DATE,
  `activo`           TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `inventario` (`tipo`,`nombre`,`descripcion`,`unidad_medida`,`stock_actual`,`stock_minimo`,`fecha_vencimiento`) VALUES
('alimento',    'Concentrado Iniciador',    'Alimento para pollos fase inicial',  'kg', 5000, 500,  NULL),
('alimento',    'Concentrado Engorde',      'Alimento para pollos fase engorde',  'kg', 8000, 800,  NULL),
('medicamento', 'Vitamina E + Selenio',     'Suplemento vitamínico',              'frascos', 50, 5, '2026-06-30'),
('medicamento', 'Antibiótico Enrofloxacina','Tratamiento infecciones',            'frascos', 20, 3, '2025-12-31'),
('insumo',      'Viruta de madera',         'Cama para galpón',                   'bultos', 200, 20, NULL),
('insumo',      'Cal viva',                 'Desinfectante y neutralizante',      'kg', 300, 30, NULL);

-- -----------------------------------------------------------
-- Tabla: movimientos_inventario
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `movimientos_inventario` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inventario_id` INT UNSIGNED NOT NULL,
  `tipo_movimiento` ENUM('entrada','salida') NOT NULL,
  `cantidad`      DECIMAL(12,2) NOT NULL,
  `motivo`        VARCHAR(200),
  `usuario_id`    INT UNSIGNED NOT NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mov_inventario` FOREIGN KEY (`inventario_id`) REFERENCES `inventario`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mov_usuario`    FOREIGN KEY (`usuario_id`)    REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Datos de prueba: registros de los últimos 30 días
-- -----------------------------------------------------------
-- Agua
INSERT INTO `registros_agua` (`galpon_id`,`fecha`,`litros`,`usuario_id`) VALUES
(1, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 1200, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 1050, 2),
(3, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 1380, 2),
(1, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 1180, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 1020, 2),
(3, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 1350, 2),
(1, DATE_SUB(CURDATE(),INTERVAL 3 DAY), 1250, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 3 DAY), 980,  2),
(3, DATE_SUB(CURDATE(),INTERVAL 3 DAY), 1400, 2);

-- Alimento
INSERT INTO `registros_alimento` (`galpon_id`,`fecha`,`kilogramos`,`usuario_id`) VALUES
(1, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 480, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 420, 2),
(3, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 550, 2),
(1, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 460, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 410, 2),
(3, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 530, 2);

-- Amoníaco
INSERT INTO `registros_amoniaco` (`galpon_id`,`fecha`,`nivel_ppm`,`usuario_id`) VALUES
(1, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 18.5, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 26.0, 2),  -- ALERTA: >25 ppm
(3, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 14.0, 2),
(1, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 17.0, 2),
(2, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 22.0, 2),
(3, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 13.5, 2);

-- Mortalidad
INSERT INTO `mortalidad` (`galpon_id`,`fecha`,`cantidad`,`causa`,`usuario_id`) VALUES
(1, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 3, 'Natural', 2),
(2, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 5, 'Calor',   2),
(3, DATE_SUB(CURDATE(),INTERVAL 1 DAY), 2, 'Natural', 2),
(1, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 4, 'Natural', 2),
(2, DATE_SUB(CURDATE(),INTERVAL 2 DAY), 6, 'Estrés',  2);

SET FOREIGN_KEY_CHECKS=1;
