<?php
header('Content-Type: application/json');
require '../conexion.php';

$response = [];

if (isset($_POST['cod_departamento'])) {
    $codDepartamento = $_POST['cod_departamento'];

    $stmt = $mysqli->prepare("SELECT cod_municipio, nombre_municipio FROM municipios WHERE cod_departamento = ? ORDER BY nombre_municipio ASC");
    $stmt->bind_param("s", $codDepartamento);

    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
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
