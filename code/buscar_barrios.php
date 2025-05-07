<?php
require_once '../conexion.php'; // tu conexión a la BD

$term = $_GET['q'] ?? '';
$term = $mysqli->real_escape_string($term); // para evitar inyección SQL
$query = "SELECT id_bar, nombre_bar, zona_bar FROM barrios WHERE nombre_bar LIKE '%$term%' LIMIT 20";
$result = $mysqli->query($query);


$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        "id" => $row['id_bar'],
        "text" => $row['nombre_bar'],
        "zona" => $row['zona_bar'] ?? ''
    ];
}

echo json_encode($results);
?>
