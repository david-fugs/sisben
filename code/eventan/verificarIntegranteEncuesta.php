<?php
include("../../conexion.php");

if (isset($_POST['documento'])) {
    $doc_encVenta = mysqli_real_escape_string($mysqli, $_POST['documento']);
    
    $sql_integrantes = "SELECT integventanilla.* 
        FROM integventanilla
        JOIN encventanilla ON integventanilla.id_encVenta = encventanilla.id_encVenta
        WHERE doc_encVenta = '$doc_encVenta'
        ORDER BY encventanilla.fecha_alta_encVenta DESC";
    
    $resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);

    if (mysqli_num_rows($resultado_integrantes) > 0) {
        $datos = [];
        while ($fila = mysqli_fetch_assoc($resultado_integrantes)) {
            $datos[] = $fila;
        }
        echo json_encode(["status" => "existe_integrante", "data" => $datos]);
        exit;
    } else {
        echo json_encode(["status" => "no_existe"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "No se recibió el documento."]);
    exit;
}
