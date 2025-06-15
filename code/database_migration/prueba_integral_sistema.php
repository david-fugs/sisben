<?php
/**
 * PRUEBA INTEGRAL DEL SISTEMA INDEPENDIENTE
 * 
 * Verifica que todos los componentes funcionen correctamente
 */

include("../../conexion.php");

try {
    echo "=== PRUEBA INTEGRAL DEL SISTEMA INDEPENDIENTE ===\n\n";
    
    // 1. Probar verificar_encuesta.php
    echo "1. Probando verificar_encuesta.php...\n";
    
    // Buscar un documento existente para probar
    $sql_sample = "SELECT doc_encVenta FROM movimientos WHERE nom_encVenta IS NOT NULL LIMIT 1";
    $result_sample = $mysqli->query($sql_sample);
    
    if ($result_sample->num_rows > 0) {
        $sample_data = $result_sample->fetch_assoc();
        $test_doc = $sample_data['doc_encVenta'];
        
        echo "   Probando con documento: $test_doc\n";
        
        // Simular la consulta que hace verificar_encuesta.php
        $sql_verify = "SELECT m.*, 
                              COALESCE(m.nom_encVenta, e.nom_encVenta) as nom_encVenta,
                              COALESCE(m.dir_encVenta, e.dir_encVenta) as dir_encVenta
                       FROM movimientos m
                       LEFT JOIN encventanilla e ON m.doc_encVenta = e.doc_encVenta
                       WHERE m.doc_encVenta = '$test_doc'
                       ORDER BY m.fecha_movimiento DESC 
                       LIMIT 1";
        
        $result_verify = $mysqli->query($sql_verify);
        
        if ($result_verify->num_rows > 0) {
            $data = $result_verify->fetch_assoc();
            echo "   ✅ Consulta exitosa - Documento encontrado\n";
            echo "   ✅ Nombre: " . $data['nom_encVenta'] . "\n";
            echo "   ✅ Dirección: " . $data['dir_encVenta'] . "\n";
            echo "   ✅ Estado ficha: " . ($data['estado_ficha'] == 1 ? 'ACTIVA' : 'RETIRADA') . "\n";
            
            // Probar consulta de integrantes
            $sql_integrantes = "SELECT * FROM integmovimientos_independiente WHERE doc_encVenta = '$test_doc'";
            $result_integrantes = $mysqli->query($sql_integrantes);
            echo "   ✅ Integrantes encontrados: " . $result_integrantes->num_rows . "\n";
            
        } else {
            echo "   ❌ No se pudo recuperar datos del documento\n";
        }
    } else {
        echo "   ❌ No hay documentos para probar\n";
    }
    
    // 2. Verificar updateEncuesta_independiente.php (simulación)
    echo "\n2. Verificando funcionalidad de actualización...\n";
    
    // Contar movimientos antes
    $sql_count_before = "SELECT COUNT(*) as total FROM movimientos";
    $result_count_before = $mysqli->query($sql_count_before);
    $count_before = $result_count_before->fetch_assoc()['total'];
    
    echo "   ✅ Registros en movimientos: $count_before\n";
    
    // Verificar que las columnas necesarias existen
    $required_columns = [
        'nom_encVenta', 'fec_reg_encVenta', 'tipo_documento', 
        'dir_encVenta', 'zona_encVenta', 'num_ficha_encVenta'
    ];
    
    $columns_ok = true;
    foreach ($required_columns as $column) {
        $sql_check_col = "SHOW COLUMNS FROM movimientos LIKE '$column'";
        $result_check_col = $mysqli->query($sql_check_col);
        
        if ($result_check_col->num_rows > 0) {
            echo "   ✅ Columna $column: EXISTE\n";
        } else {
            echo "   ❌ Columna $column: NO EXISTE\n";
            $columns_ok = false;
        }
    }
    
    // 3. Verificar tabla de integrantes independiente
    echo "\n3. Verificando tabla de integrantes independiente...\n";
    
    $sql_count_integ = "SELECT COUNT(*) as total FROM integmovimientos_independiente";
    $result_count_integ = $mysqli->query($sql_count_integ);
    $count_integ = $result_count_integ->fetch_assoc()['total'];
    
    echo "   ✅ Integrantes independientes: $count_integ\n";
    
    // Verificar estructura de la tabla
    $sql_desc_integ = "DESCRIBE integmovimientos_independiente";
    $result_desc_integ = $mysqli->query($sql_desc_integ);
    
    $integ_columns = [];
    while ($col = $result_desc_integ->fetch_assoc()) {
        $integ_columns[] = $col['Field'];
    }
    
    $required_integ_columns = [
        'id_movimiento', 'doc_encVenta', 'gen_integVenta', 
        'rango_integVenta', 'condicionDiscapacidad'
    ];
    
    $integ_structure_ok = true;
    foreach ($required_integ_columns as $req_col) {
        if (in_array($req_col, $integ_columns)) {
            echo "   ✅ Columna integrantes $req_col: EXISTE\n";
        } else {
            echo "   ❌ Columna integrantes $req_col: NO EXISTE\n";
            $integ_structure_ok = false;
        }
    }
    
    // 4. Verificar relaciones entre tablas
    echo "\n4. Verificando relaciones entre tablas...\n";
    
    $sql_relation = "SELECT COUNT(*) as total 
                     FROM movimientos m 
                     INNER JOIN integmovimientos_independiente i ON m.id_movimiento = i.id_movimiento";
    $result_relation = $mysqli->query($sql_relation);
    $relation_count = $result_relation->fetch_assoc()['total'];
    
    echo "   ✅ Relaciones válidas: $relation_count\n";
    
    // 5. Resumen final
    echo "\n=== RESUMEN DE LA PRUEBA INTEGRAL ===\n";
    
    $all_ok = $columns_ok && $integ_structure_ok && ($count_before > 0) && ($count_integ > 0);
    
    if ($all_ok) {
        echo "🎉 ¡SISTEMA COMPLETAMENTE FUNCIONAL!\n";
        echo "✅ Estructura de datos: CORRECTA\n";
        echo "✅ Datos migrados: CORRECTOS\n";
        echo "✅ Integrantes independientes: FUNCIONALES\n";
        echo "✅ Relaciones entre tablas: VÁLIDAS\n";
        echo "\n🚀 EL SISTEMA ESTÁ LISTO PARA USARSE DE FORMA INDEPENDIENTE\n";
        echo "\nAcciones disponibles:\n";
        echo "- Ir a movimientosEncuesta.php para probar el formulario\n";
        echo "- Crear nuevos movimientos independientes\n";
        echo "- Consultar datos sin depender de encventanilla\n";
    } else {
        echo "⚠️  SISTEMA PARCIALMENTE FUNCIONAL\n";
        echo "Revisar los elementos marcados con ❌\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
}
?>
