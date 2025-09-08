<?php
include("../../conexion.php");

// Establecer charset UTF-8
mysqli_set_charset($mysqli, "utf8");

echo "<h3>Verificando datos en la tabla encuestacampo</h3>";

// Consulta básica para verificar registros
$sql_count = "SELECT COUNT(*) as total FROM encuestacampo";
$result_count = mysqli_query($mysqli, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);

echo "<p>Total de registros en encuestacampo: " . $row_count['total'] . "</p>";

// Mostrar algunos registros recientes
$sql_sample = "SELECT id_encCampo, doc_encVenta, nom_encVenta, fecha_alta_encVenta, id_usu 
               FROM encuestacampo 
               ORDER BY fecha_alta_encVenta DESC 
               LIMIT 10";
$result_sample = mysqli_query($mysqli, $sql_sample);

echo "<h4>Últimos 10 registros:</h4>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Documento</th><th>Nombre</th><th>Fecha</th><th>ID Usuario</th></tr>";

while ($row = mysqli_fetch_assoc($result_sample)) {
    echo "<tr>";
    echo "<td>" . $row['id_encCampo'] . "</td>";
    echo "<td>" . $row['doc_encVenta'] . "</td>";
    echo "<td>" . $row['nom_encVenta'] . "</td>";
    echo "<td>" . $row['fecha_alta_encVenta'] . "</td>";
    echo "<td>" . $row['id_usu'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Verificar registros en el rango especificado
$fecha_inicio = '2025-09-01 00:00:00';
$fecha_fin = '2025-09-09 23:59:59';

$sql_range = "SELECT COUNT(*) as total 
              FROM encuestacampo 
              WHERE fecha_alta_encVenta BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_range = mysqli_query($mysqli, $sql_range);
$row_range = mysqli_fetch_assoc($result_range);

echo "<h4>Registros en el rango 01/09/2025 - 09/09/2025:</h4>";
echo "<p>Total: " . $row_range['total'] . "</p>";

// Mostrar registros del rango
if ($row_range['total'] > 0) {
    $sql_range_detail = "SELECT id_encCampo, doc_encVenta, nom_encVenta, fecha_alta_encVenta, id_usu 
                         FROM encuestacampo 
                         WHERE fecha_alta_encVenta BETWEEN '$fecha_inicio' AND '$fecha_fin'
                         ORDER BY fecha_alta_encVenta DESC";
    $result_range_detail = mysqli_query($mysqli, $sql_range_detail);
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Documento</th><th>Nombre</th><th>Fecha</th><th>ID Usuario</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result_range_detail)) {
        echo "<tr>";
        echo "<td>" . $row['id_encCampo'] . "</td>";
        echo "<td>" . $row['doc_encVenta'] . "</td>";
        echo "<td>" . $row['nom_encVenta'] . "</td>";
        echo "<td>" . $row['fecha_alta_encVenta'] . "</td>";
        echo "<td>" . $row['id_usu'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
