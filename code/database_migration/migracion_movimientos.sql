-- =====================================================
-- SCRIPT DE MIGRACIÓN TABLA MOVIMIENTOS
-- =====================================================
-- IMPORTANTE: HACER BACKUP COMPLETO ANTES DE EJECUTAR
-- =====================================================

-- 1. Crear nueva tabla movimientos con estructura individual
CREATE TABLE movimientos_nuevo (
    id_movimiento int(11) NOT NULL AUTO_INCREMENT,
    doc_encVenta varchar(20) NOT NULL,
    tipo_movimiento varchar(100) NOT NULL,
    fecha_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion text,
    id_usu int(11) NOT NULL,
    id_encuesta int(11) DEFAULT NULL,
    id_informacion int(11) DEFAULT NULL,
    PRIMARY KEY (id_movimiento),
    KEY idx_doc_encVenta (doc_encVenta),
    KEY idx_fecha_movimiento (fecha_movimiento),
    KEY idx_tipo_movimiento (tipo_movimiento),
    KEY idx_id_usu (id_usu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Verificar que la tabla se creó correctamente
-- DESCRIBE movimientos_nuevo;

-- 3. Opcional: Migrar datos existentes (descomenta si quieres migrar datos históricos)
/*
-- Migrar registros de inclusion
INSERT INTO movimientos_nuevo (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta)
SELECT 
    doc_encVenta, 
    'inclusion' as tipo_movimiento,
    NOW() as fecha_movimiento,
    COALESCE(observacion, '') as observacion,
    id_usu,
    id_encuesta
FROM movimientos 
WHERE inclusion > 0;

-- Migrar registros de inconformidad clasificacion
INSERT INTO movimientos_nuevo (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta)
SELECT 
    doc_encVenta, 
    'Inconformidad por clasificacion' as tipo_movimiento,
    NOW() as fecha_movimiento,
    COALESCE(observacion, '') as observacion,
    id_usu,
    id_encuesta
FROM movimientos 
WHERE inconfor_clasificacion > 0;

-- Migrar registros de datos persona
INSERT INTO movimientos_nuevo (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta)
SELECT 
    doc_encVenta, 
    'modificación datos persona' as tipo_movimiento,
    NOW() as fecha_movimiento,
    COALESCE(observacion, '') as observacion,
    id_usu,
    id_encuesta
FROM movimientos 
WHERE datos_persona > 0;

-- Migrar registros de retiro ficha
INSERT INTO movimientos_nuevo (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta)
SELECT 
    doc_encVenta, 
    'Retiro ficha' as tipo_movimiento,
    NOW() as fecha_movimiento,
    COALESCE(observacion, '') as observacion,
    id_usu,
    id_encuesta
FROM movimientos 
WHERE retiro_ficha > 0;

-- Migrar registros de retiro personas
INSERT INTO movimientos_nuevo (doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu, id_encuesta)
SELECT 
    doc_encVenta, 
    'Retiro personas' as tipo_movimiento,
    NOW() as fecha_movimiento,
    COALESCE(observacion, '') as observacion,
    id_usu,
    id_encuesta
FROM movimientos 
WHERE retiro_personas > 0;
*/

-- 4. Una vez verificada la migración, renombrar tablas
-- IMPORTANTE: Ejecutar estos comandos solo después de verificar que todo funciona
/*
RENAME TABLE movimientos TO movimientos_old;
RENAME TABLE movimientos_nuevo TO movimientos;
*/

-- 5. Verificar la nueva estructura
-- SELECT * FROM movimientos LIMIT 10;

-- 6. Opcional: Agregar foreign keys (descomenta si las necesitas)
/*
ALTER TABLE movimientos ADD FOREIGN KEY (id_usu) REFERENCES usuarios(id_usu);
ALTER TABLE movimientos ADD FOREIGN KEY (id_encuesta) REFERENCES encventanilla(id_encVenta);
ALTER TABLE movimientos ADD FOREIGN KEY (id_informacion) REFERENCES informacion(id_informacion);
*/
