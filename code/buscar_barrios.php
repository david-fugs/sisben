<?php
require_once '../conexion.php'; // tu conexión a la BD

$term = $_GET['q'] ?? '';

$stmt = $mysqli->prepare("SELECT id_bar, nombre_bar,zona_bar FROM barrios WHERE nombre_bar LIKE CONCAT('%', ?, '%') LIMIT 20");
$stmt->bind_param("s", $term);
$stmt->execute();
$result = $stmt->get_result();

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
