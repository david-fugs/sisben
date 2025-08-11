<?php
// Zebra_Pagination para paginación
require_once("../../zebra.php");

// ...existing code...

// Buscador
$where = [];
if (!empty($_GET['nombre_bar'])) {
    $nombre_bar = $mysqli->real_escape_string($_GET['nombre_bar']);
    $where[] = "barrios.nombre_bar LIKE '%$nombre_bar%'";
}
if (!empty($_GET['id_com'])) {
    $id_com = intval($_GET['id_com']);
    $where[] = "barrios.id_com = $id_com";
}

// Contar total de registros para paginación
$count_query = "SELECT COUNT(*) as total FROM barrios JOIN comunas ON barrios.id_com = comunas.id_com";
if ($where) {
    $count_query .= " WHERE " . implode(" AND ", $where);
}
$count_result = $mysqli->query($count_query);
$total_registros = $count_result->fetch_assoc()['total'];

// Zebra_Pagination
$resul_x_pagina = 25;
$paginacion = new Zebra_Pagination();
$paginacion->records($total_registros);
$paginacion->records_per_page($resul_x_pagina);

// Consulta con paginación
$query = "SELECT barrios.*, comunas.nombre_com FROM barrios JOIN comunas ON barrios.id_com = comunas.id_com";
if ($where) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY barrios.nombre_bar ASC LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
$result = $mysqli->query($query);
?>
