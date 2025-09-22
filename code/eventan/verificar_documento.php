<?php
// Configurar manejo de errores y logging
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores al cliente
ini_set('log_errors', 1);

// Establecer header de contenido JSON
header('Content-Type: application/json; charset=utf-8');

try {
    include("../../conexion.php");

    // Verificar que la conexión sea exitosa
    if ($mysqli->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $mysqli->connect_error);
    }

    // Validar que se recibió el parámetro
    if (!isset($_POST['doc_encVenta']) || empty(trim($_POST['doc_encVenta']))) {
        echo json_encode([
            "status" => "error", 
            "message" => "Documento no proporcionado o vacío",
            "code" => "INVALID_INPUT"
        ]);
        exit;
    }

    $doc_encVenta = trim($_POST['doc_encVenta']);
    
    // Validar que el documento solo contenga números
    if (!preg_match('/^[0-9]+$/', $doc_encVenta)) {
        echo json_encode([
            "status" => "error", 
            "message" => "El documento debe contener solo números",
            "code" => "INVALID_FORMAT"
        ]);
        exit;
    }

    // Escapar el documento para evitar inyección SQL
    $doc_encVenta_escaped = mysqli_real_escape_string($mysqli, $doc_encVenta);

    // 1️⃣ Verificar si ya tiene una encuesta en `encventanilla`
    $sql_encuesta = "SELECT * FROM encventanilla WHERE doc_encVenta = '$doc_encVenta_escaped' ORDER BY fecha_alta_encVenta DESC LIMIT 1";
    $resultado_encuesta = mysqli_query($mysqli, $sql_encuesta);

    if (!$resultado_encuesta) {
        throw new Exception("Error en consulta encventanilla: " . mysqli_error($mysqli));
    }

    if (mysqli_num_rows($resultado_encuesta) > 0) {
        $datos = mysqli_fetch_assoc($resultado_encuesta);
        
        // Buscar integrantes asociados
        $integrantes = [];
        $sql_integ = "SELECT * FROM integventanilla WHERE id_encVenta = " . intval($datos['id_encVenta']) . " ORDER BY id_integVenta ASC";
        $res_integ = mysqli_query($mysqli, $sql_integ);
        
        if (!$res_integ) {
            // Log el error pero no fallar completamente
            error_log("Error en consulta integventanilla: " . mysqli_error($mysqli));
        } else {
            while ($integ = mysqli_fetch_assoc($res_integ)) {
                $integrantes[] = $integ;
            }
        }
        
        if (!empty($integrantes)) {
            $datos['integrantes'] = $integrantes;
            $datos['integ'] = $integrantes[0]; // Compatibilidad
        }
        
        echo json_encode([
            "status" => "existe_encuesta", 
            "data" => $datos,
            "debug" => [
                "documento_consultado" => $doc_encVenta,
                "integrantes_encontrados" => count($integrantes)
            ]
        ]);
        exit;
    }

    // 2️⃣ Si no tiene encuesta, buscar en `informacion`
    $sql_info = "SELECT * FROM informacion WHERE doc_info = '$doc_encVenta_escaped'";
    $resultado_info = mysqli_query($mysqli, $sql_info);

    if (!$resultado_info) {
        throw new Exception("Error en consulta informacion: " . mysqli_error($mysqli));
    }

    if (mysqli_num_rows($resultado_info) > 0) {
        $datos = mysqli_fetch_assoc($resultado_info);
        echo json_encode([
            "status" => "existe_info", 
            "data" => $datos,
            "debug" => [
                "documento_consultado" => $doc_encVenta
            ]
        ]);
        exit;
    }

    // 3️⃣ Si no existe en ninguna tabla
    echo json_encode([
        "status" => "no_existe",
        "debug" => [
            "documento_consultado" => $doc_encVenta
        ]
    ]);

} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error en verificar_documento.php: " . $e->getMessage());
    
    // Respuesta de error al cliente
    echo json_encode([
        "status" => "error",
        "message" => "Error interno del servidor",
        "code" => "SERVER_ERROR",
        "debug" => [
            "error_message" => $e->getMessage(),
            "timestamp" => date('Y-m-d H:i:s')
        ]
    ]);
} finally {
    // Cerrar conexión si existe
    if (isset($mysqli) && $mysqli) {
        $mysqli->close();
    }
}
?>
