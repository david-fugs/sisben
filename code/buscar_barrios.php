<?php
require_once '../conexion.php';

header('Content-Type: application/json; charset=utf-8');

$mysqli->set_charset("utf8");

$term = $_GET['q'] ?? '';
$term = $mysqli->real_escape_string($term);
if ($term == '') {
    $term = $_GET['id'] ?? '';
    $query = "SELECT id_bar, nombre_bar, zona_bar FROM barrios WHERE id_bar = $term LIMIT 20";
    $result = $mysqli->query($query);
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = [
            "id" => $row['id_bar'],
            "text" => $row['nombre_bar'],
            "zona" => $row['zona_bar'] ?? ''
        ];
    }
}
else {
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
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
