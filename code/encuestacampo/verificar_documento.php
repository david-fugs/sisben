<?php
// Configurar manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en producción
ini_set('log_errors', 1);

// Establecer cabecera JSON
header('Content-Type: application/json; charset=utf-8');

try {
    include("../../conexion.php");

    // Verificar que la conexión se estableció correctamente
    if (!isset($mysqli) || mysqli_connect_errno()) {
        throw new Exception("Error de conexión: " . mysqli_connect_error());
    }

    // Establecer charset UTF-8
    mysqli_set_charset($mysqli, "utf8");

    if (isset($_POST['doc_encVenta']) || isset($_GET['doc_encVenta'])) {
        $doc_encVenta = isset($_POST['doc_encVenta']) ? 
            mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']) : 
            mysqli_real_escape_string($mysqli, $_GET['doc_encVenta']);

        // Validar que el documento no esté vacío
        if (empty($doc_encVenta)) {
            echo json_encode(["status" => "empty", "message" => "Documento vacío"]);
            exit;
        }

        // 1️⃣ Verificar si ya tiene una encuesta en `encuestacampo`
        $sql_encuesta = "SELECT * FROM encuestacampo WHERE doc_encVenta = '$doc_encVenta' ORDER BY fecha_alta_encVenta DESC LIMIT 1";
        $resultado_encuesta = mysqli_query($mysqli, $sql_encuesta);

        // Verificar si hubo error en la consulta
        if ($resultado_encuesta === false) {
            throw new Exception("Error en consulta encuestacampo: " . mysqli_error($mysqli));
        }

        if (mysqli_num_rows($resultado_encuesta) > 0) {
            $datos = mysqli_fetch_assoc($resultado_encuesta);
            
            // Buscar integrantes asociados
            $integrantes = [];
            $sql_integ = "SELECT * FROM integcampo WHERE documento = '" . $doc_encVenta . "' ORDER BY id_integCampo ASC";
            $res_integ = mysqli_query($mysqli, $sql_integ);
            if ($res_integ) {
                while ($integ = mysqli_fetch_assoc($res_integ)) {
                    $integrantes[] = $integ;
                }
            } else {
                // Log error si falla la consulta de integrantes
                @error_log("Error consultando integrantes: " . mysqli_error($mysqli));
            }
            
            if (!empty($integrantes)) {
                $datos['integrantes'] = $integrantes;
                $datos['integ'] = $integrantes[0]; // Compatibilidad
            }
            
            echo json_encode(["status" => "existe_encuesta", "data" => $datos]);
            exit;
        }

        // 2️⃣ Si no tiene encuesta, buscar en `informacion`
        $sql_info = "SELECT * FROM informacion WHERE doc_info = '$doc_encVenta'";
        $resultado_info = mysqli_query($mysqli, $sql_info);

        // Verificar si hubo error en la consulta
        if ($resultado_info === false) {
            throw new Exception("Error en consulta informacion: " . mysqli_error($mysqli));
        }

        if (mysqli_num_rows($resultado_info) > 0) {
            $datos = mysqli_fetch_assoc($resultado_info);
            echo json_encode(["status" => "existe_info", "data" => $datos]);
            exit;
        }

        // 3️⃣ Si no existe en ninguna tabla
        echo json_encode(["status" => "no_existe"]);
        exit;
    } else {
        // No se recibió el parámetro doc_encVenta
        echo json_encode([
            "status" => "error", 
            "message" => "Parámetro doc_encVenta no recibido"
        ]);
        exit;
    }

} catch (Exception $e) {
    // Log del error para debugging
    @error_log("Error en verificar_documento.php: " . $e->getMessage() . " - " . date('Y-m-d H:i:s'));
    
    // Respuesta JSON de error
    echo json_encode([
        "status" => "error",
        "message" => "Error en el servidor",
        "debug" => $e->getMessage() // Incluir detalles solo en desarrollo, comentar en producción
    ]);
    exit;
}

// Cerrar conexión
if (isset($mysqli)) {
    mysqli_close($mysqli);
}
?>
