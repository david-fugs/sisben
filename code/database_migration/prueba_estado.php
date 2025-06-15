<?php
/**
 * PRUEBA RÁPIDA DE INDEPENDENCIA
 * Verifica si el sistema ya está independizado
 */

include("../../conexion.php");

try {
    echo "=== VERIFICANDO ESTADO DE INDEPENDENCIA ===\n\n";
    
    // 1. Verificar estructura de movimientos
    echo "1. Verificando estructura de tabla movimientos...\n";
    $sql_columns = "SHOW COLUMNS FROM movimientos LIKE 'nom_encVenta'";
    $result = $mysqli->query($sql_columns);
    
    if ($result->num_rows > 0) {
        echo "   ✅ Columna nom_encVenta existe\n";
        $estructura_ok = true;
    } else {
        echo "   ❌ Columna nom_encVenta NO existe\n";
        $estructura_ok = false;
    }
    
    // 2. Verificar tabla integrantes independiente
    echo "\n2. Verificando tabla integmovimientos_independiente...\n";
    $sql_table = "SHOW TABLES LIKE 'integmovimientos_independiente'";
    $result = $mysqli->query($sql_table);
    
    if ($result->num_rows > 0) {
        echo "   ✅ Tabla integmovimientos_independiente existe\n";
        $tabla_ok = true;
    } else {
        echo "   ❌ Tabla integmovimientos_independiente NO existe\n";
        $tabla_ok = false;
    }
    
    // 3. Contar datos
    echo "\n3. Verificando datos...\n";
    
    $sql_count_enc = "SELECT COUNT(*) as total FROM encventanilla";
    $result_enc = $mysqli->query($sql_count_enc);
    $count_enc = $result_enc->fetch_assoc()['total'];
    echo "   Registros en encventanilla: $count_enc\n";
    
    $sql_count_mov = "SELECT COUNT(*) as total FROM movimientos";
    $result_mov = $mysqli->query($sql_count_mov);
    $count_mov = $result_mov->fetch_assoc()['total'];
    echo "   Registros en movimientos: $count_mov\n";
    
    // Verificar datos independientes si la estructura está OK
    if ($estructura_ok) {
        $sql_independent = "SELECT COUNT(*) as total FROM movimientos WHERE nom_encVenta IS NOT NULL";
        $result_ind = $mysqli->query($sql_independent);
        $count_ind = $result_ind->fetch_assoc()['total'];
        echo "   Registros independientes: $count_ind\n";
        $datos_ok = ($count_ind > 0);
    } else {
        $datos_ok = false;
    }
    
    // 4. Resultado final
    echo "\n=== RESULTADO ===\n";
    
    if ($estructura_ok && $tabla_ok && $datos_ok) {
        echo "✅ SISTEMA YA INDEPENDIZADO - Todo funcionando correctamente\n";
        echo "No se necesita ejecutar migración.\n";
    } else {
        echo "⚠️  MIGRACIÓN REQUERIDA\n";
        echo "Estructura OK: " . ($estructura_ok ? "SÍ" : "NO") . "\n";
        echo "Tabla independiente: " . ($tabla_ok ? "SÍ" : "NO") . "\n";
        echo "Datos migrados: " . ($datos_ok ? "SÍ" : "NO") . "\n";
        echo "\nSe debe ejecutar: migracion_independencia_completa.php\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
