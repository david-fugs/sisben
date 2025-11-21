-- ============================================================================
-- ELIMINAR RESTRICCIÓN UNIQUE DE doc_encVenta EN TABLA encuestacampo
-- Para permitir múltiples encuestas con el mismo documento
-- Fecha: 2025-11-21
-- ============================================================================

-- PASO 1: Verificar si existe la restricción UNIQUE
-- Ejecutar esta consulta para ver las restricciones actuales:
SHOW INDEXES FROM encuestacampo WHERE Column_name = 'doc_encVenta';

-- PASO 2: Eliminar la restricción UNIQUE si existe
-- Si la consulta anterior muestra un índice UNIQUE, ejecutar:

-- Opción A: Si el índice se llama 'doc_encVenta' (nombre típico)
ALTER TABLE encuestacampo DROP INDEX doc_encVenta;

-- Opción B: Si aparece con otro nombre en los resultados, usar ese nombre
-- ALTER TABLE encuestacampo DROP INDEX nombre_del_indice;

-- PASO 3: Verificar que se eliminó correctamente
SHOW INDEXES FROM encuestacampo WHERE Column_name = 'doc_encVenta';

-- NOTA: Después de ejecutar esto, se podrán crear múltiples encuestas
-- con el mismo número de documento

-- ============================================================================
-- Si necesitas mantener un índice para búsquedas rápidas (sin UNIQUE):
-- ============================================================================
-- CREATE INDEX idx_doc_encVenta ON encuestacampo(doc_encVenta);
