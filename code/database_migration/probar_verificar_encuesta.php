<?php
include("../../conexion.php");

echo "=== PRUEBA DE VERIFICAR_ENCUESTA.PHP ===\n";

// Simular una consulta POST
$_POST['doc_encVenta'] = '123'; // Usar un documento que sabemos que existe

// Capturar la salida
ob_start();
include("../eventan/verificar_encuesta.php");
$output = ob_get_contents();
ob_end_clean();

echo "Respuesta del servidor:\n";
echo $output . "\n";

// Intentar decodificar como JSON
$response = json_decode($output, true);
if ($response) {
    echo "\nDatos decodificados:\n";
    echo "Status: " . $response['status'] . "\n";
    if (isset($response['data']['nom_encVenta'])) {
        echo "Nombre: " . $response['data']['nom_encVenta'] . "\n";
    }
    if (isset($response['integrantes'])) {
        echo "Integrantes: " . count($response['integrantes']) . "\n";
    }
} else {
    echo "\nError: No se pudo decodificar JSON\n";
}
?>
