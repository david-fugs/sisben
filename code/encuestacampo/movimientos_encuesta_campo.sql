-- ============================================================================
-- TABLA: movimientos_encuesta_campo
-- Descripción: Control de movimientos y modificaciones de encuestas de campo
-- Fecha: 2025-11-11
-- ============================================================================

CREATE TABLE IF NOT EXISTS movimientos_encuesta_campo (
    id_movimiento INT(11) NOT NULL AUTO_INCREMENT,
    
    -- Datos del titular
    doc_encCampo VARCHAR(20) NOT NULL,
    nom_encCampo VARCHAR(255) NOT NULL,
    tipo_documento VARCHAR(50) NOT NULL,
    fecha_expedicion DATE NOT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    departamento_expedicion VARCHAR(10) NOT NULL,
    ciudad_expedicion VARCHAR(10) NOT NULL,
    
    -- Dirección y ubicación
    dir_encCampo VARCHAR(255) NOT NULL,
    zona_encCampo VARCHAR(50) NOT NULL,
    id_com INT(11) NOT NULL,
    id_bar INT(11) NOT NULL,
    otro_bar_ver_encCampo VARCHAR(255) DEFAULT NULL,
    
    -- Datos de la encuesta
    num_ficha_encCampo VARCHAR(50) NOT NULL,
    integra_encCampo INT(11) DEFAULT 0,
    fec_reg_encCampo DATE NOT NULL,
    
    -- Tipo de movimiento
    tipo_movimiento VARCHAR(100) NOT NULL COMMENT 'inclusion, Inconformidad por clasificacion, modificacion datos persona, Retiro ficha, Retiro personas',
    
    -- Observaciones y control
    obs_encCampo TEXT DEFAULT NULL,
    estado_ficha TINYINT(1) DEFAULT 1 COMMENT '1 = activa, 0 = retirada',
    
    -- Auditoría
    fecha_movimiento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_alta_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_edit_movimiento DATETIME DEFAULT NULL,
    id_usu INT(11) NOT NULL,
    
    -- Llaves
    PRIMARY KEY (id_movimiento),
    KEY idx_doc_encCampo (doc_encCampo),
    KEY idx_fecha_movimiento (fecha_movimiento),
    KEY idx_tipo_movimiento (tipo_movimiento),
    KEY idx_estado_ficha (estado_ficha),
    KEY idx_id_usu (id_usu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Movimientos y control de encuestas de campo';

-- ============================================================================
-- TABLA: integ_movimientos_encuesta_campo
-- Descripción: Integrantes asociados a cada movimiento de encuesta de campo
-- ============================================================================

CREATE TABLE IF NOT EXISTS integ_movimientos_encuesta_campo (
    id_integrante INT(11) NOT NULL AUTO_INCREMENT,
    doc_encCampo VARCHAR(20) NOT NULL,
    id_movimiento INT(11) NOT NULL,
    
    -- Datos del integrante
    gen_integCampo VARCHAR(50) NOT NULL,
    rango_integCampo VARCHAR(100) NOT NULL,
    orientacionSexual VARCHAR(100) DEFAULT NULL,
    condicionDiscapacidad VARCHAR(50) DEFAULT NULL,
    tipoDiscapacidad VARCHAR(100) DEFAULT NULL,
    grupoEtnico VARCHAR(100) DEFAULT NULL,
    victima VARCHAR(10) DEFAULT NULL,
    mujerGestante VARCHAR(10) DEFAULT NULL,
    cabezaFamilia VARCHAR(10) DEFAULT NULL,
    experienciaMigratoria VARCHAR(10) DEFAULT NULL,
    seguridadSalud VARCHAR(100) DEFAULT NULL,
    nivelEducativo VARCHAR(100) DEFAULT NULL,
    condicionOcupacion VARCHAR(100) DEFAULT NULL,
    
    -- Auditoría
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Llaves
    PRIMARY KEY (id_integrante),
    KEY idx_doc_encCampo (doc_encCampo),
    KEY idx_id_movimiento (id_movimiento),
    FOREIGN KEY (id_movimiento) REFERENCES movimientos_encuesta_campo(id_movimiento) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Integrantes de movimientos de encuesta campo';

-- ============================================================================
-- INDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================================================

-- Índice compuesto para búsquedas por documento y fecha
CREATE INDEX idx_doc_fecha ON movimientos_encuesta_campo(doc_encCampo, fecha_movimiento DESC);

-- Índice para búsquedas por usuario y fecha
CREATE INDEX idx_usuario_fecha ON movimientos_encuesta_campo(id_usu, fecha_movimiento DESC);

-- ============================================================================
-- COMENTARIOS Y DOCUMENTACIÓN
-- ============================================================================

/*
TIPOS DE MOVIMIENTO VÁLIDOS:
- inclusion: Nueva inclusión en el sistema
- Inconformidad por clasificacion: Revisión de clasificación
- modificacion datos persona: Actualización de información personal
- Retiro ficha: Retiro completo de la ficha (estado_ficha = 0)
- Retiro personas: Retiro de personas específicas del grupo

ESTADO DE FICHA:
- 1: Ficha activa (normal)
- 0: Ficha retirada (no se permiten más movimientos excepto reactivación)

FLUJO DE TRABAJO:
1. Usuario ingresa documento
2. Sistema verifica si existe en encuesta_campo o en movimientos
3. Si existe, carga los datos más recientes
4. Usuario completa/modifica información y selecciona tipo de movimiento
5. Sistema guarda nuevo registro en movimientos_encuesta_campo
6. Sistema guarda integrantes asociados
7. Mantiene historial completo de todos los movimientos
*/
