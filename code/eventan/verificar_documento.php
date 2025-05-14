<?php
include("../../conexion.php");

if (isset($_POST['doc_encVenta'])) {
    $doc_encVenta = mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']);

    // 1️⃣ Verificar si ya tiene una encuesta en `encventanilla`
    $sql_encuesta = "SELECT * FROM encventanilla WHERE doc_encVenta = '$doc_encVenta'";
    $resultado_encuesta = mysqli_query($mysqli, $sql_encuesta);

    if (mysqli_num_rows($resultado_encuesta) > 0) {
        $datos = mysqli_fetch_assoc($resultado_encuesta);
        echo json_encode(["status" => "existe_encuesta", "data" => $datos]);
        exit;
    }

    // 2️⃣ Si no tiene encuesta, buscar en `informacion`
    $sql_info = "SELECT * FROM informacion WHERE doc_info = '$doc_encVenta'";
    $resultado_info = mysqli_query($mysqli, $sql_info);

    if (mysqli_num_rows($resultado_info) > 0) {
        $datos = mysqli_fetch_assoc($resultado_info);
        echo json_encode(["status" => "existe_info", "data" => $datos]);
        exit;
    }

    // 3️⃣ Si no existe en ninguna tabla
    echo json_encode(["status" => "no_existe"]);
    exit;
}
?>
