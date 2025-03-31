<?php
    include("../../conexion.php");

if (isset($_POST['doc_encVenta'])) {
    $doc_encVenta = $_POST['doc_encVenta'];

    // 1️⃣ Verificar si ya tiene una encuesta en `encventanilla`
    $sql_encuesta = "SELECT * FROM encventanilla WHERE doc_encVenta = ?";
    $stmt = $mysqli->prepare($sql_encuesta);
    $stmt->bind_param("s", $doc_encVenta);
    $stmt->execute();
    $resultado_encuesta = $stmt->get_result();

    if ($resultado_encuesta->num_rows > 0) {
        echo json_encode(["status" => "existe_encuesta"]);
        exit;
    }

    // 2️⃣ Si no tiene encuesta, buscar en `informacion`
    $sql_info = "SELECT * FROM informacion WHERE doc_info = ?";
    $stmt = $mysqli->prepare($sql_info);
    $stmt->bind_param("s", $doc_encVenta);
    $stmt->execute();
    $resultado_info = $stmt->get_result();

    if ($resultado_info->num_rows > 0) {
        $datos = $resultado_info->fetch_assoc();
        echo json_encode(["status" => "existe_info", "data" => $datos]);
        exit;
    }

    // 3️⃣ Si no existe en ninguna tabla
    echo json_encode(["status" => "no_existe"]);
    exit;
}
?>
