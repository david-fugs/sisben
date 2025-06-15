<?php
/**
 * SCRIPT DE CORRECCIÓN: Arreglar tipos de movimiento con problemas de codificación UTF-8
 * 
 * Este script corrige el problema donde "modificación datos persona" se guardó como 
 * "modificaciÃ³n datos persona" debido a problemas de codificación UTF-8.
 */

include('../../conexion.php');

echo "=== CORRECCIÓN DE TIPOS DE MOVIMIENTO ===\n";

// Verificar cuántos registros tienen el problema
$sql_verificar = "SELECT COUNT(*) as total FROM movimientos WHERE tipo_movimiento LIKE '%modificaci%' AND tipo_movimiento LIKE '%Ã³%'";
$resultado = mysqli_query($mysqli, $sql_verificar);
$row = mysqli_fetch_assoc($resultado);
$total_problemas = $row['total'];

echo "Registros con problema de codificación encontrados: $total_problemas\n";

if ($total_problemas > 0) {
    echo "Iniciando corrección...\n";
    
    // Corregir los registros
    $sql_corregir = "UPDATE movimientos 
                     SET tipo_movimiento = 'modificacion datos persona' 
                     WHERE tipo_movimiento LIKE '%modificaci%' AND tipo_movimiento LIKE '%Ã³%'";
    
    if (mysqli_query($mysqli, $sql_corregir)) {
        $registros_corregidos = mysqli_affected_rows($mysqli);
        echo "✅ Corrección exitosa! $registros_corregidos registros corregidos.\n";
    } else {
        echo "❌ Error en la corrección: " . mysqli_error($mysqli) . "\n";
    }
} else {
    echo "✅ No se encontraron registros con problemas de codificación.\n";
}

// Verificar el estado final
echo "\n=== VERIFICACIÓN FINAL ===\n";
$sql_final = "SELECT DISTINCT tipo_movimiento, COUNT(*) as cantidad FROM movimientos GROUP BY tipo_movimiento ORDER BY tipo_movimiento";
$resultado_final = mysqli_query($mysqli, $sql_final);

while($row = mysqli_fetch_assoc($resultado_final)) {
    echo "'" . $row['tipo_movimiento'] . "' - " . $row['cantidad'] . " registros\n";
}

echo "\n✅ Corrección completada.\n";
?>
