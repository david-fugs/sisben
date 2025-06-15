<?php
include("../../conexion.php");

echo "=== TABLAS RELACIONADAS CON INTEGRANTES ===\n";

// Buscar todas las tablas que contengan 'integ'
$sql = "SHOW TABLES LIKE '%integ%'";
$result = $mysqli->query($sql);

echo "Tablas encontradas:\n";
while ($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

echo "\n=== VERIFICANDO TABLA INTEGRANTES VENTANILLA ===\n";

// Intentar diferentes nombres posibles
$posibles_nombres = [
    'integencVentanilla',
    'integenc_ventanilla', 
    'integrantes_ventanilla',
    'integVentanilla',
    'integrantesVentanilla'
];

foreach ($posibles_nombres as $nombre) {
    $sql_check = "SHOW TABLES LIKE '$nombre'";
    $result_check = $mysqli->query($sql_check);
    
    if ($result_check->num_rows > 0) {
        echo "✅ Encontrada: $nombre\n";
        
        // Mostrar estructura
        $sql_desc = "DESCRIBE $nombre";
        $result_desc = $mysqli->query($sql_desc);
        echo "   Columnas:\n";
        while ($col = $result_desc->fetch_assoc()) {
            echo "   - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
        
        // Contar registros
        $sql_count = "SELECT COUNT(*) as total FROM $nombre";
        $result_count = $mysqli->query($sql_count);
        $count = $result_count->fetch_assoc()['total'];
        echo "   Registros: $count\n\n";
        
    } else {
        echo "❌ No encontrada: $nombre\n";
    }
}
?>
