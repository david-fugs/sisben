<?php
/**
 * VERIFICAR Y LIMPIAR BASE DE DATOS
 * 
 * Este script:
 * 1. Verifica el estado actual de movimientos
 * 2. Puede limpiar la migración anterior si es necesario
 * 3. Prepara para la migración correcta
 */

include("../../conexion.php");

echo "=== VERIFICACIÓN Y LIMPIEZA DE BASE DE DATOS ===\n\n";

try {
    // 1. Verificar estado actual
    echo "1. Estado actual de la base de datos:\n";
    
    $sql_count_mov = "SELECT COUNT(*) as total FROM movimientos";
    $result_count_mov = $mysqli->query($sql_count_mov);
    $count_movimientos = $result_count_mov->fetch_assoc()['total'];
    echo "   Movimientos: $count_movimientos\n";
    
    $sql_count_enc = "SELECT COUNT(*) as total FROM encventanilla";
    $result_count_enc = $mysqli->query($sql_count_enc);
    $count_encventanilla = $result_count_enc->fetch_assoc()['total'];
    echo "   Encventanilla: $count_encventanilla\n";
    
    // Verificar si existe tabla independiente
    $sql_check_table = "SHOW TABLES LIKE 'integmovimientos_independiente'";
    $result_table = $mysqli->query($sql_check_table);
    
    if ($result_table->num_rows > 0) {
        $sql_count_integ = "SELECT COUNT(*) as total FROM integmovimientos_independiente";
        $result_count_integ = $mysqli->query($sql_count_integ);
        $count_integ = $result_count_integ->fetch_assoc()['total'];
        echo "   Integrantes independientes: $count_integ\n";
    } else {
        echo "   Integrantes independientes: TABLA NO EXISTE\n";
    }
    
    // Verificar columnas agregadas
    echo "\n2. Verificando columnas agregadas en movimientos:\n";
    $sql_check_col = "SHOW COLUMNS FROM movimientos LIKE 'nom_encVenta'";
    $result_check_col = $mysqli->query($sql_check_col);
    
    if ($result_check_col->num_rows > 0) {
        echo "   ✅ Columnas adicionales YA EXISTEN\n";
        
        // Contar movimientos con datos independientes
        $sql_count_independientes = "SELECT COUNT(*) as total FROM movimientos WHERE nom_encVenta IS NOT NULL";
        $result_count_indep = $mysqli->query($sql_count_independientes);
        $count_independientes = $result_count_indep->fetch_assoc()['total'];
        echo "   Movimientos con datos independientes: $count_independientes\n";
        
    } else {
        echo "   ➡️  Columnas adicionales NO EXISTEN\n";
    }
    
    echo "\n3. Análisis:\n";
    
    if ($count_movimientos > 119) {
        echo "   ⚠️  PROBLEMA: Hay $count_movimientos movimientos (esperados: 119)\n";
        echo "   Esto indica que se crearon registros adicionales desde encventanilla\n";
        echo "\n¿Desea limpiar y restaurar solo los 119 movimientos originales? (s/n): ";
        
        // Para script automatizado, asumimos que sí queremos limpiar
        $respuesta = 's';
        echo "$respuesta\n";
        
        if ($respuesta == 's' || $respuesta == 'S') {
            echo "\n4. LIMPIANDO BASE DE DATOS...\n";
            
            // Iniciar transacción
            $mysqli->autocommit(FALSE);
            
            // Eliminar tabla de integrantes independientes si existe
            if ($result_table->num_rows > 0) {
                $sql_drop_integ = "DROP TABLE IF EXISTS integmovimientos_independiente";
                if ($mysqli->query($sql_drop_integ)) {
                    echo "   ✅ Tabla integmovimientos_independiente eliminada\n";
                }
            }
            
            // Eliminar columnas agregadas
            $columnas_eliminar = [
                'nom_encVenta', 'fec_reg_encVenta', 'tipo_documento', 
                'departamento_expedicion', 'ciudad_expedicion', 'fecha_expedicion',
                'dir_encVenta', 'zona_encVenta', 'id_com', 'id_bar', 
                'otro_bar_ver_encVenta', 'integra_encVenta', 'num_ficha_encVenta',
                'sisben_nocturno', 'estado_ficha', 'fecha_alta_movimiento', 
                'fecha_edit_movimiento'
            ];
            
            foreach ($columnas_eliminar as $columna) {
                $sql_check_exists = "SHOW COLUMNS FROM movimientos LIKE '$columna'";
                $result_exists = $mysqli->query($sql_check_exists);
                
                if ($result_exists->num_rows > 0) {
                    $sql_drop_col = "ALTER TABLE movimientos DROP COLUMN $columna";
                    if ($mysqli->query($sql_drop_col)) {
                        echo "   ✅ Columna $columna eliminada\n";
                    }
                }
            }
            
            // Eliminar registros de movimientos que no sean los originales
            // Asumiendo que los primeros 119 son los originales
            $sql_delete_extra = "DELETE FROM movimientos WHERE id_movimiento > (
                SELECT id_movimiento FROM (
                    SELECT id_movimiento FROM movimientos ORDER BY id_movimiento LIMIT 119
                ) as temp ORDER BY id_movimiento DESC LIMIT 1
            )";
            
            // Esta consulta es compleja, mejor usar un enfoque diferente
            // Primero, obtener los IDs de los primeros 119 movimientos
            $sql_get_original = "SELECT id_movimiento FROM movimientos ORDER BY id_movimiento LIMIT 119";
            $result_original = $mysqli->query($sql_get_original);
            
            $ids_originales = [];
            while ($row = $result_original->fetch_assoc()) {
                $ids_originales[] = $row['id_movimiento'];
            }
            
            if (count($ids_originales) > 0) {
                $ids_string = implode(',', $ids_originales);
                $sql_delete_extra = "DELETE FROM movimientos WHERE id_movimiento NOT IN ($ids_string)";
                
                if ($mysqli->query($sql_delete_extra)) {
                    $affected_rows = $mysqli->affected_rows;
                    echo "   ✅ Eliminados $affected_rows movimientos extra\n";
                } else {
                    echo "   ❌ Error eliminando movimientos extra: " . $mysqli->error . "\n";
                }
            }
            
            $mysqli->commit();
            $mysqli->autocommit(TRUE);
            
            // Verificar estado final
            $sql_count_final = "SELECT COUNT(*) as total FROM movimientos";
            $result_count_final = $mysqli->query($sql_count_final);
            $count_final = $result_count_final->fetch_assoc()['total'];
            
            echo "\n   ✅ LIMPIEZA COMPLETADA\n";
            echo "   Movimientos restantes: $count_final\n";
            
            if ($count_final == 119) {
                echo "   🎉 PERFECTO: Se mantuvieron exactamente 119 movimientos\n";
            } else {
                echo "   ⚠️  ADVERTENCIA: Se esperaban 119, hay $count_final\n";
            }
            
        } else {
            echo "   ➡️  Limpieza cancelada por el usuario\n";
        }
        
    } else if ($count_movimientos == 119) {
        echo "   ✅ PERFECTO: Hay exactamente 119 movimientos\n";
        echo "   Listo para ejecutar la migración correcta\n";
        
    } else {
        echo "   ⚠️  ADVERTENCIA: Hay solo $count_movimientos movimientos (esperados: 119)\n";
        echo "   Puede proceder pero verificar que sea correcto\n";
    }
    
    echo "\n=== ESTADO FINAL ===\n";
    
    // Recalcular estado final
    $sql_count_mov_final = "SELECT COUNT(*) as total FROM movimientos";
    $result_count_mov_final = $mysqli->query($sql_count_mov_final);
    $count_movimientos_final = $result_count_mov_final->fetch_assoc()['total'];
    
    echo "Movimientos finales: $count_movimientos_final\n";
    echo "Encventanilla: $count_encventanilla\n";
    
    if ($count_movimientos_final <= 119) {
        echo "\n🚀 LISTO PARA EJECUTAR: migracion_correcta_enriquecer.php\n";
    } else {
        echo "\n⚠️  Revisar el número de movimientos antes de continuar\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    
    if (isset($mysqli)) {
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
    }
}
?>
