-- Script SQL para crear nueva estructura de movimientos COMPLETA
-- IMPORTANTE: Hacer backup de la base de datos antes de ejecutar

-- 1. Crear tabla temporal con nueva estructura COMPLETA (independiente de encventanilla)
CREATE TABLE movimientos_completo (
    id_movimiento int(11) NOT NULL AUTO_INCREMENT,
    
    -- Campos básicos de movimiento
    doc_encVenta varchar(20) NOT NULL,
    tipo_movimiento varchar(100) NOT NULL,
    fecha_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion text,
    id_usu int(11) NOT NULL,
    
    -- Todos los campos de encventanilla para independizar completamente
    fec_reg_encVenta date DEFAULT NULL,
    nom_encVenta varchar(100) DEFAULT NULL,
    dir_encVenta varchar(200) DEFAULT NULL,
    zona_encVenta varchar(50) DEFAULT NULL,
    id_com int(11) DEFAULT NULL,
    id_bar int(11) DEFAULT NULL,
    otro_bar_ver_encVenta varchar(100) DEFAULT NULL,
    tram_solic_encVenta varchar(100) DEFAULT NULL,
    integra_encVenta int(11) DEFAULT NULL,
    num_ficha_encVenta varchar(50) DEFAULT NULL,
    obs_encVenta text,
    tipo_documento varchar(20) DEFAULT NULL,
    fecha_expedicion date DEFAULT NULL,
    departamento_expedicion varchar(10) DEFAULT NULL,
    ciudad_expedicion varchar(10) DEFAULT NULL,
    sisben_nocturno varchar(10) DEFAULT NULL,
    estado_ficha int(1) DEFAULT 1,
    
    -- Campos de control
    fecha_alta_movimiento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_edit_movimiento datetime DEFAULT NULL,
    
    -- Relaciones opcionales
    id_encuesta int(11) DEFAULT NULL,
    id_informacion int(11) DEFAULT NULL,
    
    PRIMARY KEY (id_movimiento),
    KEY idx_doc_encVenta (doc_encVenta),
    KEY idx_fecha_movimiento (fecha_movimiento),
    KEY idx_tipo_movimiento (tipo_movimiento),
    KEY idx_id_usu (id_usu),
    KEY idx_fec_reg_encVenta (fec_reg_encVenta),
    KEY idx_num_ficha_encVenta (num_ficha_encVenta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Migrar datos de encventanilla a movimientos (crear un movimiento inicial por cada encuesta)
INSERT INTO movimientos_completo 
(doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu,
 fec_reg_encVenta, nom_encVenta, dir_encVenta, zona_encVenta, id_com, id_bar,
 otro_bar_ver_encVenta, tram_solic_encVenta, integra_encVenta, num_ficha_encVenta,
 obs_encVenta, tipo_documento, fecha_expedicion, departamento_expedicion,
 ciudad_expedicion, sisben_nocturno, estado_ficha, fecha_alta_movimiento,
 fecha_edit_movimiento, id_encuesta)
SELECT 
    doc_encVenta,
    COALESCE(tram_solic_encVenta, 'ENCUESTA INICIAL') as tipo_movimiento,
    COALESCE(fecha_alta_encVenta, NOW()) as fecha_movimiento,
    obs_encVenta as observacion,
    id_usu,
    fec_reg_encVenta,
    nom_encVenta,
    dir_encVenta,
    zona_encVenta,
    id_com,
    id_bar,
    otro_bar_ver_encVenta,
    tram_solic_encVenta,
    integra_encVenta,
    num_ficha_encVenta,
    obs_encVenta,
    tipo_documento,
    fecha_expedicion,
    departamento_expedicion,
    ciudad_expedicion,
    sisben_nocturno,
    COALESCE(estado_ficha, 1),
    COALESCE(fecha_alta_encVenta, NOW()),
    fecha_edit_encVenta,
    id_encVenta
FROM encventanilla;

-- 3. Una vez verificada la nueva estructura, renombrar tablas
-- RENAME TABLE movimientos TO movimientos_old;
-- RENAME TABLE movimientos_completo TO movimientos;

-- 4. Agregar foreign keys si es necesario
-- ALTER TABLE movimientos ADD FOREIGN KEY (id_usu) REFERENCES usuarios(id_usu);
-- ALTER TABLE movimientos ADD FOREIGN KEY (id_com) REFERENCES comunas(id_com);
-- ALTER TABLE movimientos ADD FOREIGN KEY (id_bar) REFERENCES barrios(id_bar);
