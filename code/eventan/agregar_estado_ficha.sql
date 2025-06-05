-- Agregar campo estado_ficha a la tabla encventanilla
-- 1 = ACTIVA, 0 = RETIRADA

ALTER TABLE encventanilla 
ADD COLUMN estado_ficha TINYINT DEFAULT 1 COMMENT '1=ACTIVA, 0=RETIRADA';

-- Actualizar registros existentes según la tabla movimientos
UPDATE encventanilla e
LEFT JOIN movimientos m ON e.doc_encVenta = m.doc_encVenta
SET e.estado_ficha = CASE 
    WHEN m.retiro_ficha > 0 THEN 0
    ELSE 1
END;
