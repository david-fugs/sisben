<?php
// Script de verificación de sistema
echo "<h2>Verificación del Sistema</h2>";

// 1. Verificar PHP y extensiones
echo "<h3>PHP y Extensiones:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . " segundos<br>";

// Verificar extensiones requeridas
$required_extensions = ['zip', 'xml', 'gd', 'mbstring', 'mysqli'];
echo "<h4>Extensiones requeridas:</h4>";
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✓ Instalada" : "✗ NO instalada";
    echo "$ext: $status<br>";
}

// 2. Verificar Composer y vendor
echo "<h3>Composer y Vendor:</h3>";
$vendor_path = '../../vendor/autoload.php';
if (file_exists($vendor_path)) {
    echo "✓ Autoload encontrado en: $vendor_path<br>";
    try {
        require $vendor_path;
        echo "✓ Autoload cargado correctamente<br>";
        
        // Verificar PhpSpreadsheet
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            echo "✓ PhpSpreadsheet disponible<br>";
        } else {
            echo "✗ PhpSpreadsheet NO disponible<br>";
        }
    } catch (Exception $e) {
        echo "✗ Error al cargar autoload: " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ Autoload NO encontrado en: $vendor_path<br>";
}

// 3. Verificar conexión a base de datos
echo "<h3>Base de Datos:</h3>";
if (file_exists('../../conexion.php')) {
    echo "✓ Archivo de conexión encontrado<br>";
    try {
        include('../../conexion.php');
        if (isset($mysqli) && $mysqli instanceof mysqli) {
            if ($mysqli->connect_error) {
                echo "✗ Error de conexión: " . $mysqli->connect_error . "<br>";
            } else {
                echo "✓ Conexión a base de datos exitosa<br>";
                echo "Base de datos: " . $mysqli->get_server_info() . "<br>";
            }
        } else {
            echo "✗ Variable \$mysqli no está definida o no es válida<br>";
        }
    } catch (Exception $e) {
        echo "✗ Error al incluir conexión: " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ Archivo de conexión NO encontrado<br>";
}

// 4. Verificar permisos de escritura
echo "<h3>Permisos de Escritura:</h3>";
$test_file = __DIR__ . '/test_write.txt';
if (is_writable(__DIR__)) {
    echo "✓ Directorio escribible<br>";
    if (file_put_contents($test_file, 'test')) {
        echo "✓ Puede crear archivos<br>";
        unlink($test_file);
    } else {
        echo "✗ No puede crear archivos<br>";
    }
} else {
    echo "✗ Directorio NO escribible<br>";
}

// 5. Verificar parámetros GET
echo "<h3>Parámetros GET recibidos:</h3>";
if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        echo "$key: $value<br>";
    }
} else {
    echo "No se recibieron parámetros GET<br>";
}

// 6. Información del servidor
echo "<h3>Información del Servidor:</h3>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No disponible') . "<br>";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'No disponible') . "<br>";
echo "Current Directory: " . getcwd() . "<br>";

echo "<h3>Variables de Entorno PHP:</h3>";
echo "Error Reporting: " . error_reporting() . "<br>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";
echo "Log Errors: " . ini_get('log_errors') . "<br>";
echo "Error Log: " . ini_get('error_log') . "<br>";

?>
