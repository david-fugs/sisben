<?php
// Habilitar reporte de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer header JSON
header('Content-Type: application/json');

include("../../conexion.php");

// Log para debug
error_log("verificar_documento.php: Script iniciado");

if (isset($_POST['doc_info'])) {
    $doc_info = mysqli_real_escape_string($mysqli, $_POST['doc_info']);
    
    error_log("verificar_documento.php: Buscando documento: " . $doc_info);

    $sql = "SELECT * FROM informacion WHERE doc_info = '$doc_info' ORDER BY fecha_alta_info DESC LIMIT 1";
    $resultado = mysqli_query($mysqli, $sql);
    
    if (!$resultado) {
        error_log("verificar_documento.php: Error en consulta: " . mysqli_error($mysqli));
        echo json_encode(["status" => "error", "message" => "Error en consulta: " . mysqli_error($mysqli)]);
        exit;
    }

    if (mysqli_num_rows($resultado) > 0) {
        $datos = mysqli_fetch_assoc($resultado);
        error_log("verificar_documento.php: Documento encontrado: " . $datos['nom_info']);
        echo json_encode(["status" => "existe", "data" => $datos]);
    } else {
        error_log("verificar_documento.php: Documento no encontrado");
        echo json_encode(["status" => "no_existe"]);
    }
} else {
    error_log("verificar_documento.php: No se recibió doc_info");
    echo json_encode(["status" => "error", "message" => "No se recibió el parámetro doc_info"]);
}
?>
