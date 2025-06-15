<?php
/**
 * POST-MIGRACIÓN: Verificación y Pruebas Completas
 * 
 * Este script debe ejecutarse DESPUÉS de la migración para confirmar
 * que el sistema está funcionando correctamente en producción.
 */

include("../../conexion.php");
header("Content-Type: text/html;charset=utf-8");

echo "<h1>🔬 Verificación Post-Migración - Sistema Integrantes Independientes</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

$errores = [];
$advertencias = [];
$exitos = [];

// 1. VERIFICAR ESTRUCTURA DE TABLA
echo "<h2>1. 🏗️ Verificación de Estructura de Tabla</h2>";

$sql_estructura = "DESCRIBE integmovimientos_independiente";
$resultado = mysqli_query($mysqli, $sql_estructura);

if ($resultado) {
    $campos_esperados = [
        'id_integmov_indep' => 'int(11)',
        'doc_encVenta' => 'varchar(20)',
        'cant_integMovIndep' => 'int(11)',
        'gen_integMovIndep' => 'varchar(10)',
        'rango_integMovIndep' => 'varchar(50)',
        'orientacionSexual' => 'varchar(100)',
        'condicionDiscapacidad' => 'varchar(10)',
        'grupoEtnico' => 'varchar(100)',
        'estado_integMovIndep' => 'int(11)'
    ];
    
    $campos_encontrados = [];
    while ($campo = mysqli_fetch_assoc($resultado)) {
        $campos_encontrados[$campo['Field']] = $campo['Type'];
    }
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr style='background: #f8f9fa;'><th>Campo</th><th>Esperado</th><th>Encontrado</th><th>Estado</th></tr>";
    
    foreach ($campos_esperados as $campo => $tipo_esperado) {
        $encontrado = isset($campos_encontrados[$campo]);
        $tipo_actual = $campos_encontrados[$campo] ?? 'No encontrado';
        
        echo "<tr>";
        echo "<td><strong>$campo</strong></td>";
        echo "<td>$tipo_esperado</td>";
        echo "<td>$tipo_actual</td>";
        
        if ($encontrado) {
            echo "<td style='color: green;'>✅ OK</td>";
            $exitos[] = "Campo $campo existe";
        } else {
            echo "<td style='color: red;'>❌ FALTA</td>";
            $errores[] = "Campo $campo no encontrado";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    $errores[] = "No se pudo verificar estructura de tabla: " . mysqli_error($mysqli);
}

// 2. VERIFICAR ÍNDICES
echo "<h2>2. 📇 Verificación de Índices</h2>";

$sql_indices = "SHOW INDEX FROM integmovimientos_independiente";
$resultado_indices = mysqli_query($mysqli, $sql_indices);

if ($resultado_indices) {
    $indices_encontrados = [];
    while ($indice = mysqli_fetch_assoc($resultado_indices)) {
        $indices_encontrados[] = $indice['Key_name'];
    }
    
    $indices_esperados = ['PRIMARY', 'idx_doc_encVenta', 'idx_estado'];
    
    echo "<ul>";
    foreach ($indices_esperados as $indice) {
        if (in_array($indice, $indices_encontrados)) {
            echo "<li style='color: green;'>✅ Índice $indice: OK</li>";
            $exitos[] = "Índice $indice presente";
        } else {
            echo "<li style='color: orange;'>⚠️ Índice $indice: No encontrado (opcional)</li>";
            $advertencias[] = "Índice $indice no encontrado";
        }
    }
    echo "</ul>";
}

// 3. PROBAR OPERACIONES CRUD
echo "<h2>3. 🔧 Pruebas de Operaciones CRUD</h2>";

$test_doc = 'POST_MIGRACION_TEST_' . time();

// Probar INSERT
echo "<h3>3.1 Prueba de Inserción</h3>";
$sql_insert = "INSERT INTO integmovimientos_independiente 
               (doc_encVenta, cant_integMovIndep, gen_integMovIndep, rango_integMovIndep, 
                orientacionSexual, condicionDiscapacidad, grupoEtnico, estado_integMovIndep, id_usu) 
               VALUES ('$test_doc', 1, 'M', '2', 'Heterosexual', 'No', 'Mestizo', 1, 1)";

if (mysqli_query($mysqli, $sql_insert)) {
    $test_id = mysqli_insert_id($mysqli);
    echo "<p style='color: green;'>✅ Inserción exitosa - ID: $test_id</p>";
    $exitos[] = "Inserción de integrante exitosa";
    
    // Probar SELECT
    echo "<h3>3.2 Prueba de Consulta</h3>";
    $sql_select = "SELECT * FROM integmovimientos_independiente WHERE id_integmov_indep = $test_id";
    $resultado_select = mysqli_query($mysqli, $sql_select);
    
    if ($resultado_select && mysqli_num_rows($resultado_select) > 0) {
        echo "<p style='color: green;'>✅ Consulta exitosa</p>";
        $exitos[] = "Consulta de integrante exitosa";
        
        // Probar UPDATE
        echo "<h3>3.3 Prueba de Actualización</h3>";
        $sql_update = "UPDATE integmovimientos_independiente 
                       SET gen_integMovIndep = 'F', rango_integMovIndep = '3' 
                       WHERE id_integmov_indep = $test_id";
        
        if (mysqli_query($mysqli, $sql_update)) {
            echo "<p style='color: green;'>✅ Actualización exitosa</p>";
            $exitos[] = "Actualización de integrante exitosa";
        } else {
            $errores[] = "Error en actualización: " . mysqli_error($mysqli);
        }
        
        // Probar DELETE
        echo "<h3>3.4 Prueba de Eliminación (Lógica)</h3>";
        $sql_delete = "UPDATE integmovimientos_independiente 
                       SET estado_integMovIndep = 0 
                       WHERE id_integmov_indep = $test_id";
        
        if (mysqli_query($mysqli, $sql_delete)) {
            echo "<p style='color: green;'>✅ Eliminación lógica exitosa</p>";
            $exitos[] = "Eliminación lógica de integrante exitosa";
        } else {
            $errores[] = "Error en eliminación lógica: " . mysqli_error($mysqli);
        }
        
        // Limpiar datos de prueba
        $sql_cleanup = "DELETE FROM integmovimientos_independiente WHERE id_integmov_indep = $test_id";
        mysqli_query($mysqli, $sql_cleanup);
        
    } else {
        $errores[] = "Error en consulta de datos insertados";
    }
} else {
    $errores[] = "Error en inserción: " . mysqli_error($mysqli);
}

// 4. VERIFICAR ARCHIVOS DE SISTEMA
echo "<h2>4. 📁 Verificación de Archivos de Sistema</h2>";

$archivos_sistema = [
    'editMovimiento.php' => 'Editor principal de movimientos',
    'updateMovimiento.php' => 'Procesador CRUD de integrantes',
    'eliminarIntegrante.php' => 'Endpoint AJAX para eliminación',
    '../../conexion.php' => 'Archivo de conexión a BD'
];

echo "<ul>";
foreach ($archivos_sistema as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "<li style='color: green;'>✅ $archivo - $descripcion</li>";
        $exitos[] = "Archivo $archivo presente";
    } else {
        echo "<li style='color: red;'>❌ $archivo - $descripcion</li>";
        $errores[] = "Archivo $archivo no encontrado";
    }
}
echo "</ul>";

// 5. VERIFICAR COMPATIBILIDAD CON MOVIMIENTOS
echo "<h2>5. 🔗 Verificación de Compatibilidad con Movimientos</h2>";

$sql_movimientos = "SELECT COUNT(*) as total FROM movimientos LIMIT 1";
$resultado_movimientos = mysqli_query($mysqli, $sql_movimientos);

if ($resultado_movimientos) {
    echo "<p style='color: green;'>✅ Tabla movimientos accesible</p>";
    $exitos[] = "Compatibilidad con tabla movimientos confirmada";
    
    // Verificar relación
    $sql_relacion = "SELECT COUNT(*) as total 
                     FROM integmovimientos_independiente i 
                     LEFT JOIN movimientos m ON i.doc_encVenta = m.doc_encVenta 
                     WHERE i.estado_integMovIndep = 1 
                     LIMIT 5";
    
    $resultado_relacion = mysqli_query($mysqli, $sql_relacion);
    if ($resultado_relacion) {
        echo "<p style='color: green;'>✅ Relación entre tablas funcional</p>";
        $exitos[] = "Relación entre integrantes y movimientos funcional";
    }
} else {
    $advertencias[] = "No se pudo verificar compatibilidad con movimientos";
}

// 6. ESTADÍSTICAS DEL SISTEMA
echo "<h2>6. 📊 Estadísticas del Sistema</h2>";

$estadisticas = [
    "SELECT COUNT(*) as total FROM integmovimientos_independiente" => "Total integrantes en tabla",
    "SELECT COUNT(*) as total FROM integmovimientos_independiente WHERE estado_integMovIndep = 1" => "Integrantes activos",
    "SELECT COUNT(DISTINCT doc_encVenta) as total FROM integmovimientos_independiente WHERE estado_integMovIndep = 1" => "Documentos con integrantes activos",
    "SELECT COUNT(*) as total FROM movimientos" => "Total movimientos"
];

echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
echo "<tr style='background: #f8f9fa;'><th>Métrica</th><th>Valor</th></tr>";

foreach ($estadisticas as $sql => $descripcion) {
    $resultado_stat = mysqli_query($mysqli, $sql);
    if ($resultado_stat) {
        $valor = mysqli_fetch_assoc($resultado_stat)['total'];
        echo "<tr><td>$descripcion</td><td><strong>$valor</strong></td></tr>";
    } else {
        echo "<tr><td>$descripcion</td><td style='color: red;'>Error</td></tr>";
    }
}
echo "</table>";

// RESUMEN FINAL
echo "<h2>📋 Resumen de Verificación</h2>";

echo "<div style='display: flex; gap: 20px;'>";

// Éxitos
if (!empty($exitos)) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; flex: 1;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Verificaciones Exitosas (" . count($exitos) . ")</h3>";
    echo "<ul style='color: #155724; margin: 0;'>";
    foreach ($exitos as $exito) {
        echo "<li>$exito</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Advertencias
if (!empty($advertencias)) {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; flex: 1;'>";
    echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Advertencias (" . count($advertencias) . ")</h3>";
    echo "<ul style='color: #856404; margin: 0;'>";
    foreach ($advertencias as $advertencia) {
        echo "<li>$advertencia</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Errores
if (!empty($errores)) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; flex: 1;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Errores (" . count($errores) . ")</h3>";
    echo "<ul style='color: #721c24; margin: 0;'>";
    foreach ($errores as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

echo "</div>";

// Conclusión
echo "<h2>🎯 Conclusión</h2>";

if (empty($errores)) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #155724;'>🎉 ¡MIGRACIÓN EXITOSA!</h3>";
    echo "<p style='color: #155724;'><strong>El sistema de integrantes independientes está funcionando correctamente en producción.</strong></p>";
    echo "<p style='color: #155724;'>Se pueden comenzar a usar las nuevas funcionalidades.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24;'>⚠️ Se encontraron errores</h3>";
    echo "<p style='color: #721c24;'>Revisar y corregir los errores antes de usar el sistema en producción.</p>";
    echo "</div>";
}

echo "<h3>📝 Próximos Pasos:</h3>";
echo "<ol>";
echo "<li><strong>Si todo está OK:</strong> Comunicar a usuarios que el sistema está listo</li>";
echo "<li><strong>Capacitación:</strong> Entrenar usuarios en nuevas funcionalidades</li>";
echo "<li><strong>Monitoreo:</strong> Supervisar rendimiento y logs durante primeros días</li>";
echo "<li><strong>Backup programado:</strong> Asegurar que los backups incluyan la nueva tabla</li>";
echo "</ol>";

mysqli_close($mysqli);
?>
