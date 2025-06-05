<?php
echo "<h2>Logs de Debug</h2>";

$debug_log = __DIR__ . '/debug.log';
$error_log = __DIR__ . '/error_debug.log';

echo "<h3>Debug Log:</h3>";
if (file_exists($debug_log)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($debug_log)) . "</pre>";
} else {
    echo "No existe el archivo debug.log aún.";
}

echo "<hr>";

echo "<h3>Error Log:</h3>";
if (file_exists($error_log)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($error_log)) . "</pre>";
} else {
    echo "No existe el archivo error_debug.log aún.";
}

echo "<hr>";
echo "<p><a href='verificar_sistema.php'>Ver verificación del sistema</a></p>";
echo "<p><a href='exportarEncuestador.php?id_usu=8&fecha_inicio=2025-01-01&fecha_fin=2025-06-01'>Probar exportar con logging</a></p>";
?>
