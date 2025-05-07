<?php
header('Content-Type: application/json');
require '../conexion.php';

$response = [];

if (isset($_POST['cod_departamento'])) {
    $codDepartamento = $mysqli->real_escape_string($_POST['cod_departamento']);

    $query = "SELECT cod_municipio, nombre_municipio 
              FROM municipios 
              WHERE cod_departamento = '$codDepartamento' 
              ORDER BY nombre_municipio ASC";

    $resultado = $mysqli->query($query);

    if ($resultado) {
        $municipios = [];

        while ($fila = $resultado->fetch_assoc()) {
            $municipios[] = $fila;
        }

        echo json_encode($municipios);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al ejecutar la consulta.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibió el código del departamento.']);
}
