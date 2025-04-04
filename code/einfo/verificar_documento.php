<?php
include("../../conexion.php");

if (isset($_POST['doc_info'])) {
    $doc_info = mysqli_real_escape_string($mysqli, $_POST['doc_info']);

    $sql = "SELECT * FROM informacion WHERE doc_info = '$doc_info'";
    $resultado = mysqli_query($mysqli, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        echo json_encode(["status" => "existe"]);
    } else {
        echo json_encode(["status" => "no_existe"]);
    }
}
?>
