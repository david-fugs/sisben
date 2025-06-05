<?php
echo "<h2>Test de Logging</h2>";

// Función para logging personalizado
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents(__DIR__ . '/debug.log', $logMessage, FILE_APPEND | LOCK_EX);
}

// Activar error log
ini_set('error_log', __DIR__ . '/error_debug.log');
ini_set('log_errors', 1);

echo "<p>Creando logs de prueba...</p>";

// Crear un log normal
logError("Test de debug log - funcionando correctamente");

// Forzar un error PHP para crear error_debug.log
@include('archivo_que_no_existe.php');
trigger_error("Test de error log - error generado intencionalmente", E_USER_WARNING);

echo "<p>Logs creados. <a href='ver_logs.php'>Ver logs aquí</a></p>";
?>
