<?php
// Test script to verify the ASESOR columns are working
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../conexion.php");

echo "<h2>Testing ASESOR Column Implementation</h2>";

// Test ENCUESTAS query
echo "<h3>Testing ENCUESTAS Query:</h3>";
$sql_encuestas = "
SELECT ev.id_encVenta, ev.nom_encVenta, ev.doc_encVenta, ev.fecha_alta_encVenta, 
       u.nombre AS nombre_usuario, ev.id_usu
FROM encventanilla ev
LEFT JOIN usuarios u ON ev.id_usu = u.id_usu
LIMIT 5
";
$res_encuestas = mysqli_query($mysqli, $sql_encuestas);
if ($res_encuestas) {
    echo "Query successful! Results:<br>";
    while ($row = mysqli_fetch_assoc($res_encuestas)) {
        echo "ID: {$row['id_encVenta']}, Nombre: {$row['nom_encVenta']}, Usuario: {$row['nombre_usuario']}<br>";
    }
} else {
    echo "Error in ENCUESTAS query: " . mysqli_error($mysqli);
}

echo "<hr>";

// Test INFORMACION query
echo "<h3>Testing INFORMACION Query:</h3>";
$sql_informacion = "
SELECT i.id_info, i.nom_info, i.doc_info, i.fecha_alta_info, 
       u.nombre AS nombre_usuario, i.id_usu
FROM informacion i
LEFT JOIN usuarios u ON i.id_usu = u.id_usu
LIMIT 5
";
$res_informacion = mysqli_query($mysqli, $sql_informacion);
if ($res_informacion) {
    echo "Query successful! Results:<br>";
    while ($row = mysqli_fetch_assoc($res_informacion)) {
        echo "ID: {$row['id_info']}, Nombre: {$row['nom_info']}, Usuario: {$row['nombre_usuario']}<br>";
    }
} else {
    echo "Error in INFORMACION query: " . mysqli_error($mysqli);
}

echo "<hr>";

// Test MOVIMIENTOS query (for comparison)
echo "<h3>Testing MOVIMIENTOS Query (for comparison):</h3>";
$sql_movimientos = "
SELECT m.id_movimiento, m.doc_encVenta, m.fecha_movimiento, 
       u.nombre AS nombre_usuario, m.id_usu
FROM movimientos m
LEFT JOIN usuarios u ON m.id_usu = u.id_usu
LIMIT 5
";
$res_movimientos = mysqli_query($mysqli, $sql_movimientos);
if ($res_movimientos) {
    echo "Query successful! Results:<br>";
    while ($row = mysqli_fetch_assoc($res_movimientos)) {
        echo "ID: {$row['id_movimiento']}, Doc: {$row['doc_encVenta']}, Usuario: {$row['nombre_usuario']}<br>";
    }
} else {
    echo "Error in MOVIMIENTOS query: " . mysqli_error($mysqli);
}

echo "<p><strong>Test completed!</strong></p>";
?>
