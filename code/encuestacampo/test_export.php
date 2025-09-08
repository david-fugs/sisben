<?php
// Archivo de prueba simple para verificar parámetros
echo "<h3>Test de parámetros recibidos:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

if (isset($_GET['fecha_inicio'])) {
    echo "<p>Fecha inicio: " . $_GET['fecha_inicio'] . "</p>";
}
if (isset($_GET['fecha_fin'])) {
    echo "<p>Fecha fin: " . $_GET['fecha_fin'] . "</p>";
}
if (isset($_GET['id_usu'])) {
    echo "<p>ID Usuario: " . $_GET['id_usu'] . "</p>";
}

// Conectar y probar consulta
include("../../conexion.php");
mysqli_set_charset($mysqli, "utf8");

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

$condiciones = [];
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
    $fecha_fin_completa = $fecha_fin . ' 23:59:59';
    $condiciones[] = "ec.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
}

$id_usu = null;
if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
    $id_usu = $_GET['id_usu'];
    $condiciones[] = "ec.id_usu = '$id_usu'";
}

$where_encuestas = '';
if (count($condiciones) > 0) {
    $where_encuestas = 'WHERE ' . implode(' AND ', $condiciones);
}

$sql_encuestas = "
SELECT ec.*, b.nombre_bar AS barrio_nombre, d.nombre_departamento AS departamento_nombre, 
       c.nombre_com AS comuna_nombre, m.nombre_municipio as ciudad_nombre,
       u.nombre AS nombre_usuario
FROM encuestacampo ec
LEFT JOIN barrios b ON ec.id_bar = b.id_bar
LEFT JOIN departamentos d ON ec.departamento_expedicion = d.cod_departamento
LEFT JOIN comunas c ON ec.id_com = c.id_com
LEFT JOIN municipios m ON ec.ciudad_expedicion = m.cod_municipio
LEFT JOIN usuarios u ON ec.id_usu = u.id_usu
$where_encuestas
ORDER BY ec.fecha_alta_encVenta DESC
LIMIT 5
";

echo "<h3>SQL generado:</h3>";
echo "<pre>$sql_encuestas</pre>";

$res_encuestas = mysqli_query($mysqli, $sql_encuestas);
if ($res_encuestas === false) {
    echo "<p style='color:red'>Error en la consulta: " . mysqli_error($mysqli) . "</p>";
} else {
    $num_rows = mysqli_num_rows($res_encuestas);
    echo "<p>Registros encontrados: $num_rows</p>";
    
    if ($num_rows > 0) {
        echo "<h4>Primeros registros:</h4>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Documento</th><th>Nombre</th><th>Fecha</th><th>Usuario</th></tr>";
        
        while ($row = mysqli_fetch_assoc($res_encuestas)) {
            echo "<tr>";
            echo "<td>" . $row['id_encCampo'] . "</td>";
            echo "<td>" . $row['doc_encVenta'] . "</td>";
            echo "<td>" . $row['nom_encVenta'] . "</td>";
            echo "<td>" . $row['fecha_alta_encVenta'] . "</td>";
            echo "<td>" . $row['nombre_usuario'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>
