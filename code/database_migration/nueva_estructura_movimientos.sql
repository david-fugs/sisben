-- Script SQL para modificar la tabla movimientos
-- IMPORTANTE: Hacer backup de la base de datos antes de ejecutar

-- 1. Crear tabla temporal con nueva estructura
CREATE TABLE movimientos_nuevo (
    id_movimiento int(11) NOT NULL AUTO_INCREMENT,
    doc_encVenta varchar(20) NOT NULL,
    tipo_movimiento varchar(100) NOT NULL, -- 'inclusion', 'inconfor_clasificacion', etc.
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

-- 2. Migrar datos existentes de la tabla antigua (opcional, según sea necesario)
-- Esta parte depende de cómo quieras manejar los datos históricos

-- 3. Una vez verificada la nueva estructura, renombrar tablas
-- RENAME TABLE movimientos TO movimientos_old;
-- RENAME TABLE movimientos_nuevo TO movimientos;

-- 4. Agregar foreign keys si es necesario
-- ALTER TABLE movimientos ADD FOREIGN KEY (id_usu) REFERENCES usuarios(id_usu);
-- ALTER TABLE movimientos ADD FOREIGN KEY (id_encuesta) REFERENCES encventanilla(id_encVenta);
-- ALTER TABLE movimientos ADD FOREIGN KEY (id_informacion) REFERENCES informacion(id_informacion);
