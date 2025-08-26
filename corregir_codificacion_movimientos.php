<?php
echo "=== SCRIPT PARA CORREGIR CODIFICACIÓN EN TABLA MOVIMIENTOS ===\n";

include("conexion.php");

// Verificar datos antes de la corrección
echo "\n1. DATOS ANTES DE LA CORRECCIÓN:\n";
$result = $mysqli->query("SELECT DISTINCT tipo_movimiento, COUNT(*) as cantidad FROM movimientos GROUP BY tipo_movimiento");
while ($row = $result->fetch_assoc()) {
    echo "   '{$row['tipo_movimiento']}' -> {$row['cantidad']} registros\n";
}

// Preguntar confirmación
echo "\n¿Desea proceder con la corrección? (S/N): ";
$handle = fopen("php://stdin", "r");
$confirmation = strtoupper(trim(fgets($handle)));
fclose($handle);

if ($confirmation !== 'S') {
    echo "Operación cancelada.\n";
    exit;
}

echo "\n2. APLICANDO CORRECCIONES...\n";

// Corregir "modificaciÃ³n datos persona" -> "modificacion datos persona"
$update1 = $mysqli->query("UPDATE movimientos SET tipo_movimiento = 'modificacion datos persona' WHERE tipo_movimiento = 'modificaciÃ³n datos persona'");
if ($update1) {
    echo "   ✓ Corregidos registros de 'modificaciÃ³n datos persona'\n";
    echo "     Registros afectados: " . $mysqli->affected_rows . "\n";
} else {
    echo "   ✗ Error al corregir 'modificaciÃ³n datos persona': " . $mysqli->error . "\n";
}

// Corregir "RetiroÂ personas" -> "Retiro personas"
$update2 = $mysqli->query("UPDATE movimientos SET tipo_movimiento = 'Retiro personas' WHERE tipo_movimiento = 'RetiroÂ personas'");
if ($update2) {
    echo "   ✓ Corregidos registros de 'RetiroÂ personas'\n";
    echo "     Registros afectados: " . $mysqli->affected_rows . "\n";
} else {
    echo "   ✗ Error al corregir 'RetiroÂ personas': " . $mysqli->error . "\n";
}

// Verificar datos después de la corrección
echo "\n3. DATOS DESPUÉS DE LA CORRECCIÓN:\n";
$result2 = $mysqli->query("SELECT DISTINCT tipo_movimiento, COUNT(*) as cantidad FROM movimientos GROUP BY tipo_movimiento ORDER BY tipo_movimiento");
while ($row = $result2->fetch_assoc()) {
    echo "   '{$row['tipo_movimiento']}' -> {$row['cantidad']} registros\n";
}

echo "\n4. RESUMEN:\n";
echo "   - Se han corregido los problemas de codificación UTF-8\n";
echo "   - Los tipos de movimiento ahora se mostrarán correctamente\n";
echo "   - La búsqueda y filtros funcionarán adecuadamente\n";

$mysqli->close();

echo "\n✓ Corrección completada exitosamente.\n";
?>
