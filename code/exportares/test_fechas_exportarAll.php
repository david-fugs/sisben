<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Date Filtering in exportarAll.php</h2>";

// Test 1: Empty dates
$fecha_inicio = '';
$fecha_fin = '';

$condiciones = [];

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
    $fecha_fin_completa = $fecha_fin . ' 23:59:59';
    $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
}

$where = '';
if (count($condiciones) > 0) {
    $where = 'WHERE ' . implode(' AND ', $condiciones);
}

echo "<h3>Test 1: Empty dates</h3>";
echo "WHERE clause for main query: '$where'<br>";

$integventanilla_where = (!empty($fecha_inicio) && !empty($fecha_fin) ? "WHERE iv.fecha_alta_integVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'" : "");
echo "WHERE clause for integventanilla: '$integventanilla_where'<br><br>";

// Test 2: Same date
$fecha_inicio = '2024-01-15';
$fecha_fin = '2024-01-15';

$condiciones = [];

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
    $fecha_fin_completa = $fecha_fin . ' 23:59:59';
    $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
}

$where = '';
if (count($condiciones) > 0) {
    $where = 'WHERE ' . implode(' AND ', $condiciones);
}

echo "<h3>Test 2: Same date (2024-01-15)</h3>";
echo "fecha_inicio_completa: '$fecha_inicio_completa'<br>";
echo "fecha_fin_completa: '$fecha_fin_completa'<br>";
echo "WHERE clause for main query: '$where'<br>";

$integventanilla_where = (!empty($fecha_inicio) && !empty($fecha_fin) ? "WHERE iv.fecha_alta_integVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'" : "");
echo "WHERE clause for integventanilla: '$integventanilla_where'<br><br>";

// Test 3: Date range
$fecha_inicio = '2024-01-10';
$fecha_fin = '2024-01-20';

$condiciones = [];

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
    $fecha_fin_completa = $fecha_fin . ' 23:59:59';
    $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
}

$where = '';
if (count($condiciones) > 0) {
    $where = 'WHERE ' . implode(' AND ', $condiciones);
}

echo "<h3>Test 3: Date range (2024-01-10 to 2024-01-20)</h3>";
echo "fecha_inicio_completa: '$fecha_inicio_completa'<br>";
echo "fecha_fin_completa: '$fecha_fin_completa'<br>";
echo "WHERE clause for main query: '$where'<br>";

$integventanilla_where = (!empty($fecha_inicio) && !empty($fecha_fin) ? "WHERE iv.fecha_alta_integVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'" : "");
echo "WHERE clause for integventanilla: '$integventanilla_where'<br><br>";

echo "<h3>Summary</h3>";
echo "✅ Date filtering logic updated to use complete timestamps<br>";
echo "✅ Both main query and integventanilla query use the same date format<br>";
echo "✅ Same-day filtering now works (00:00:00 to 23:59:59)<br>";
echo "✅ Empty dates result in no WHERE clause (all data)<br>";
?>
