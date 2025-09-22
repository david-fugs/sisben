<?php
/*
 * Archivo de configuración para debugging en producción
 * Incluir este archivo en verificar_documento.php si necesitas más debugging
 */

// Configuración de errores PHP para debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../debug_errors.log');

// Función para logging personalizado
function debug_log($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";
    
    if ($data !== null) {
        $log_message .= " - Data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    error_log($log_message . PHP_EOL, 3, __DIR__ . '/../../debug_verificar_documento.log');
}

// Función para verificar conexión a base de datos
function test_database_connection() {
    try {
        $test_mysqli = new mysqli("localhost", "root", "", "softepuc_sisben");
        
        if ($test_mysqli->connect_error) {
            debug_log("Error de conexión DB", $test_mysqli->connect_error);
            return false;
        }
        
        $test_mysqli->set_charset("utf8");
        debug_log("Conexión DB exitosa");
        $test_mysqli->close();
        return true;
        
    } catch (Exception $e) {
        debug_log("Excepción en conexión DB", $e->getMessage());
        return false;
    }
}

// Función para verificar si las tablas existen
function verify_tables_exist($mysqli) {
    $tables = ['encventanilla', 'integventanilla', 'informacion'];
    $results = [];
    
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = mysqli_query($mysqli, $sql);
        $exists = mysqli_num_rows($result) > 0;
        $results[$table] = $exists;
        
        if (!$exists) {
            debug_log("Tabla no existe", $table);
        }
    }
    
    return $results;
}

// Función para verificar estructura de tablas críticas
function verify_table_structure($mysqli, $document) {
    debug_log("Verificando estructura para documento", $document);
    
    // Verificar estructura de encventanilla
    $sql = "DESCRIBE encventanilla";
    $result = mysqli_query($mysqli, $sql);
    if ($result) {
        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }
        debug_log("Columnas en encventanilla", $columns);
    } else {
        debug_log("Error al describir encventanilla", mysqli_error($mysqli));
    }
    
    // Verificar estructura de informacion
    $sql = "DESCRIBE informacion";
    $result = mysqli_query($mysqli, $sql);
    if ($result) {
        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }
        debug_log("Columnas en informacion", $columns);
    } else {
        debug_log("Error al describir informacion", mysqli_error($mysqli));
    }
}
?>