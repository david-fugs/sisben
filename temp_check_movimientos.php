<?php
include('conexion.php');

echo "=== TIPOS DE MOVIMIENTO EN LA BASE DE DATOS ===\n";
$sql = "SELECT DISTINCT tipo_movimiento FROM movimientos ORDER BY tipo_movimiento";
$result = mysqli_query($mysqli, $sql);

while($row = mysqli_fetch_assoc($result)) {
    echo "'" . $row['tipo_movimiento'] . "'\n";
}

echo "\n=== ALGUNOS REGISTROS DE EJEMPLO ===\n";
$sql2 = "SELECT doc_encVenta, tipo_movimiento, fecha_movimiento FROM movimientos WHERE tipo_movimiento LIKE '%modificaci%' OR tipo_movimiento LIKE '%datos%' LIMIT 10";
$result2 = mysqli_query($mysqli, $sql2);

while($row = mysqli_fetch_assoc($result2)) {
    echo "Doc: " . $row['doc_encVenta'] . " | Tipo: '" . $row['tipo_movimiento'] . "' | Fecha: " . $row['fecha_movimiento'] . "\n";
}
?>
