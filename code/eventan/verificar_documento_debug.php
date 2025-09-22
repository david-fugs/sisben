<?php
/*
 * Versión de debugging de verificar_documento.php
 * Úsala temporalmente en producción para diagnosticar el error 500
 * Renombra a verificar_documento.php cuando la necesites
 */

// Incluir configuración de debugging
include('debug_config.php');

// Configurar manejo de errores y logging
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores al cliente
ini_set('log_errors', 1);

// Establecer header de contenido JSON
header('Content-Type: application/json; charset=utf-8');

debug_log("=== INICIO VERIFICAR DOCUMENTO ===");
debug_log("POST recibido", $_POST);
debug_log("Server info", [
    'PHP_VERSION' => PHP_VERSION,
    'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
]);

try {
    // Verificar conexión antes de proceder
    if (!test_database_connection()) {
        throw new Exception("Fallo en test de conexión a base de datos");
    }

    include("../../conexion.php");

    // Verificar que la conexión sea exitosa
    if ($mysqli->connect_error) {
        debug_log("Error de conexión mysqli", $mysqli->connect_error);
        throw new Exception("Error de conexión a la base de datos: " . $mysqli->connect_error);
    }

    debug_log("Conexión mysqli exitosa");

    // Verificar que las tablas existen
    $table_status = verify_tables_exist($mysqli);
    debug_log("Estado de tablas", $table_status);

    // Validar que se recibió el parámetro
    if (!isset($_POST['doc_encVenta']) || empty(trim($_POST['doc_encVenta']))) {
        debug_log("Documento no proporcionado o vacío");
        echo json_encode([
            "status" => "error", 
            "message" => "Documento no proporcionado o vacío",
            "code" => "INVALID_INPUT"
        ]);
        exit;
    }

    $doc_encVenta = trim($_POST['doc_encVenta']);
    debug_log("Documento a consultar", $doc_encVenta);
    
    // Validar que el documento solo contenga números
    if (!preg_match('/^[0-9]+$/', $doc_encVenta)) {
        debug_log("Formato de documento inválido", $doc_encVenta);
        echo json_encode([
            "status" => "error", 
            "message" => "El documento debe contener solo números",
            "code" => "INVALID_FORMAT"
        ]);
        exit;
    }

    // Verificar estructura de tablas para este documento
    verify_table_structure($mysqli, $doc_encVenta);

    // Escapar el documento para evitar inyección SQL
    $doc_encVenta_escaped = mysqli_real_escape_string($mysqli, $doc_encVenta);
    debug_log("Documento escapado", $doc_encVenta_escaped);

    // 1️⃣ Verificar si ya tiene una encuesta en `encventanilla`
    $sql_encuesta = "SELECT * FROM encventanilla WHERE doc_encVenta = '$doc_encVenta_escaped' ORDER BY fecha_alta_encVenta DESC LIMIT 1";
    debug_log("SQL encuesta", $sql_encuesta);
    
    $resultado_encuesta = mysqli_query($mysqli, $sql_encuesta);

    if (!$resultado_encuesta) {
        $error = mysqli_error($mysqli);
        debug_log("Error en consulta encventanilla", $error);
        throw new Exception("Error en consulta encventanilla: " . $error);
    }

    $num_rows_encuesta = mysqli_num_rows($resultado_encuesta);
    debug_log("Filas encontradas en encventanilla", $num_rows_encuesta);

    if ($num_rows_encuesta > 0) {
        $datos = mysqli_fetch_assoc($resultado_encuesta);
        debug_log("Datos de encuesta encontrados", array_keys($datos));
        
        // Buscar integrantes asociados
        $integrantes = [];
        $sql_integ = "SELECT * FROM integventanilla WHERE id_encuesta = " . intval($datos['id_encVenta']) . " ORDER BY id_integVenta ASC";
        debug_log("SQL integrantes", $sql_integ);
        
        $res_integ = mysqli_query($mysqli, $sql_integ);
        
        if (!$res_integ) {
            $error = mysqli_error($mysqli);
            debug_log("Error en consulta integventanilla", $error);
            // Log el error pero no fallar completamente
            error_log("Error en consulta integventanilla: " . $error);
        } else {
            $num_integrantes = 0;
            while ($integ = mysqli_fetch_assoc($res_integ)) {
                $integrantes[] = $integ;
                $num_integrantes++;
            }
            debug_log("Integrantes encontrados", $num_integrantes);
        }
        
        if (!empty($integrantes)) {
            $datos['integrantes'] = $integrantes;
            $datos['integ'] = $integrantes[0]; // Compatibilidad
        }
        
        $response = [
            "status" => "existe_encuesta", 
            "data" => $datos,
            "debug" => [
                "documento_consultado" => $doc_encVenta,
                "integrantes_encontrados" => count($integrantes)
            ]
        ];
        
        debug_log("Respuesta existe_encuesta", "Preparada con " . count($integrantes) . " integrantes");
        echo json_encode($response);
        exit;
    }

    // 2️⃣ Si no tiene encuesta, buscar en `informacion`
    $sql_info = "SELECT * FROM informacion WHERE doc_info = '$doc_encVenta_escaped'";
    debug_log("SQL información", $sql_info);
    
    $resultado_info = mysqli_query($mysqli, $sql_info);

    if (!$resultado_info) {
        $error = mysqli_error($mysqli);
        debug_log("Error en consulta informacion", $error);
        throw new Exception("Error en consulta informacion: " . $error);
    }

    $num_rows_info = mysqli_num_rows($resultado_info);
    debug_log("Filas encontradas en informacion", $num_rows_info);

    if ($num_rows_info > 0) {
        $datos = mysqli_fetch_assoc($resultado_info);
        debug_log("Datos de información encontrados", array_keys($datos));
        
        $response = [
            "status" => "existe_info", 
            "data" => $datos,
            "debug" => [
                "documento_consultado" => $doc_encVenta
            ]
        ];
        
        debug_log("Respuesta existe_info", "Preparada");
        echo json_encode($response);
        exit;
    }

    // 3️⃣ Si no existe en ninguna tabla
    $response = [
        "status" => "no_existe",
        "debug" => [
            "documento_consultado" => $doc_encVenta
        ]
    ];
    
    debug_log("Respuesta no_existe", "Documento no encontrado en ninguna tabla");
    echo json_encode($response);

} catch (Exception $e) {
    // Log del error para debugging
    $error_message = $e->getMessage();
    $error_trace = $e->getTraceAsString();
    
    debug_log("EXCEPCIÓN CAPTURADA", [
        'message' => $error_message,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $error_trace
    ]);
    
    error_log("Error en verificar_documento.php: " . $error_message);
    
    // Respuesta de error al cliente
    $error_response = [
        "status" => "error",
        "message" => "Error interno del servidor",
        "code" => "SERVER_ERROR",
        "debug" => [
            "error_message" => $error_message,
            "timestamp" => date('Y-m-d H:i:s'),
            "file" => basename($e->getFile()),
            "line" => $e->getLine()
        ]
    ];
    
    echo json_encode($error_response);
    
} finally {
    debug_log("=== FIN VERIFICAR DOCUMENTO ===");
    
    // Cerrar conexión si existe
    if (isset($mysqli) && $mysqli) {
        $mysqli->close();
        debug_log("Conexión mysqli cerrada");
    }
}
?>