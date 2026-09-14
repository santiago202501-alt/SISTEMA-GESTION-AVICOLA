-- MIGRACIÓN SIGA AVÍCOLA -> Spring Boot
-- Ejecutar sobre una copia de la BD siga_avicola.
-- Este script agrega clientes, permisos y aislamiento por cliente sin borrar los datos existentes.

CREATE TABLE IF NOT EXISTS clientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  nit VARCHAR(40) NULL,
  telefono VARCHAR(30) NULL,
  email VARCHAR(120) NULL,
  direccion VARCHAR(200) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS permisos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL UNIQUE,
  descripcion VARCHAR(255) NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rol_permisos (
  rol_id INT UNSIGNED NOT NULL,
  permiso_id INT UNSIGNED NOT NULL,
  PRIMARY KEY(rol_id,permiso_id),
  CONSTRAINT fk_rp_rol FOREIGN KEY(rol_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_perm FOREIGN KEY(permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ajustar roles al modelo solicitado.
UPDATE roles SET nombre='administrador', descripcion='Acceso total al sistema y administración de clientes, usuarios, roles y permisos' WHERE id=1;
UPDATE roles SET nombre='ojeador', descripcion='Consulta de información sin modificar los datos' WHERE id=2;
INSERT INTO roles(nombre,descripcion)
SELECT 'cliente','Gestiona y consulta únicamente los datos de su propia empresa' WHERE NOT EXISTS(SELECT 1 FROM roles WHERE LOWER(nombre)='cliente');

SET @cliente_demo := (SELECT id FROM clientes ORDER BY id LIMIT 1);
INSERT INTO clientes(nombre,activo) SELECT 'Cliente Demo SIGA',1 WHERE @cliente_demo IS NULL;
SET @cliente_demo := (SELECT id FROM clientes ORDER BY id LIMIT 1);

-- Agregar cliente_id solo si aún no existe.
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE usuarios ADD COLUMN cliente_id INT UNSIGNED NULL AFTER rol_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='usuarios' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE galpones ADD COLUMN cliente_id INT UNSIGNED NULL AFTER responsable_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='galpones' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE registros_agua ADD COLUMN cliente_id INT UNSIGNED NULL AFTER usuario_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='registros_agua' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE registros_alimento ADD COLUMN cliente_id INT UNSIGNED NULL AFTER usuario_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='registros_alimento' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE registros_amoniaco ADD COLUMN cliente_id INT UNSIGNED NULL AFTER usuario_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='registros_amoniaco' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE mortalidad ADD COLUMN cliente_id INT UNSIGNED NULL AFTER usuario_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='mortalidad' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE alertas ADD COLUMN cliente_id INT UNSIGNED NULL AFTER galpon_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='alertas' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE inventario ADD COLUMN cliente_id INT UNSIGNED NULL AFTER id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='inventario' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE movimientos_inventario ADD COLUMN cliente_id INT UNSIGNED NULL AFTER inventario_id','SELECT 1') FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name='movimientos_inventario' AND column_name='cliente_id'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

UPDATE usuarios SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE galpones SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE registros_agua SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE registros_alimento SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE registros_amoniaco SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE mortalidad SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE alertas SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE inventario SET cliente_id=COALESCE(cliente_id,@cliente_demo);
UPDATE movimientos_inventario SET cliente_id=COALESCE(cliente_id,@cliente_demo);

-- Hacer obligatoria la pertenencia luego de poblarla.
ALTER TABLE usuarios MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE galpones MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE registros_agua MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE registros_alimento MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE registros_amoniaco MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE mortalidad MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE alertas MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE inventario MODIFY cliente_id INT UNSIGNED NOT NULL;
ALTER TABLE movimientos_inventario MODIFY cliente_id INT UNSIGNED NOT NULL;

-- Permisos
INSERT INTO permisos(nombre,descripcion) VALUES
('dashboard.ver','Ver el panel principal'),
('clientes.ver','Consultar clientes'),('clientes.crear','Crear clientes'),('clientes.editar','Editar clientes'),('clientes.eliminar','Eliminar clientes'),
('usuarios.ver','Consultar usuarios'),('usuarios.crear','Crear usuarios'),('usuarios.editar','Editar usuarios'),('usuarios.eliminar','Eliminar usuarios'),
('roles.ver','Consultar roles'),('roles.crear','Crear roles'),('roles.editar','Editar roles'),('roles.eliminar','Eliminar roles'),('permisos.asignar','Asignar permisos a roles'),
('galpones.ver','Consultar galpones'),('galpones.crear','Crear galpones'),('galpones.editar','Editar galpones'),('galpones.eliminar','Eliminar galpones'),
('agua.ver','Consultar registros de agua'),('agua.crear','Registrar agua'),('agua.editar','Editar agua'),('agua.eliminar','Eliminar agua'),
('alimento.ver','Consultar registros de alimento'),('alimento.crear','Registrar alimento'),('alimento.editar','Editar alimento'),('alimento.eliminar','Eliminar alimento'),
('amoniaco.ver','Consultar registros de amoniaco'),('amoniaco.crear','Registrar amoniaco'),('amoniaco.editar','Editar amoniaco'),('amoniaco.eliminar','Eliminar amoniaco'),
('mortalidad.ver','Consultar mortalidad'),('mortalidad.crear','Registrar mortalidad'),('mortalidad.editar','Editar mortalidad'),('mortalidad.eliminar','Eliminar mortalidad'),
('inventario.ver','Consultar inventario'),('inventario.crear','Crear inventario'),('inventario.editar','Editar inventario'),('inventario.eliminar','Eliminar inventario'),
('alertas.ver','Consultar alertas'),('alertas.gestionar','Gestionar alertas'),
('reportes.ver','Consultar reportes'),('reportes.generar','Generar reportes')
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- Admin recibe todo.
INSERT IGNORE INTO rol_permisos(rol_id,permiso_id) SELECT 1,id FROM permisos;
-- Ojeador: solo lectura y reportes.
INSERT IGNORE INTO rol_permisos(rol_id,permiso_id)
SELECT 2,p.id FROM permisos p WHERE p.nombre LIKE '%.ver' OR p.nombre='reportes.generar';
-- Cliente: dashboard + lectura/alta/edición de operación; sin administración global.
SET @cliente_rol := (SELECT id FROM roles WHERE LOWER(nombre)='cliente' LIMIT 1);
INSERT IGNORE INTO rol_permisos(rol_id,permiso_id)
SELECT @cliente_rol,p.id FROM permisos p WHERE p.nombre IN ('dashboard.ver','galpones.ver','galpones.crear','galpones.editar',
'agua.ver','agua.crear','agua.editar','alimento.ver','alimento.crear','alimento.editar','amoniaco.ver','amoniaco.crear','amoniaco.editar',
'mortalidad.ver','mortalidad.crear','mortalidad.editar','inventario.ver','inventario.crear','inventario.editar','alertas.ver','reportes.ver','reportes.generar');

-- Actualizar el usuario demo operador al rol ojeador.
UPDATE usuarios SET rol_id=2 WHERE rol_id=2;

-- FKs, si no existen.
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE usuarios ADD CONSTRAINT fk_usuarios_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='usuarios' AND constraint_name='fk_usuarios_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE galpones ADD CONSTRAINT fk_galpones_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='galpones' AND constraint_name='fk_galpones_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE registros_agua ADD CONSTRAINT fk_agua_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='registros_agua' AND constraint_name='fk_agua_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE registros_alimento ADD CONSTRAINT fk_alimento_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='registros_alimento' AND constraint_name='fk_alimento_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE registros_amoniaco ADD CONSTRAINT fk_amoniaco_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='registros_amoniaco' AND constraint_name='fk_amoniaco_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE mortalidad ADD CONSTRAINT fk_mortalidad_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='mortalidad' AND constraint_name='fk_mortalidad_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE alertas ADD CONSTRAINT fk_alertas_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='alertas' AND constraint_name='fk_alertas_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE inventario ADD CONSTRAINT fk_inventario_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='inventario' AND constraint_name='fk_inventario_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql := (SELECT IF(COUNT(*)=0,'ALTER TABLE movimientos_inventario ADD CONSTRAINT fk_mov_cliente FOREIGN KEY(cliente_id) REFERENCES clientes(id)','SELECT 1') FROM information_schema.table_constraints WHERE constraint_schema=DATABASE() AND table_name='movimientos_inventario' AND constraint_name='fk_mov_cliente'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
