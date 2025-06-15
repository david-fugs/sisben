<?php
/**
 * Script para verificar la estructura actual de la tabla integmovimientos_independiente
 */
include("../../conexion.php");

echo "<h2>🔍 Estructura Actual de la Tabla integmovimientos_independiente</h2>";

// Verificar estructura de tabla
$sql_estructura = "DESCRIBE integmovimientos_independiente";
$resultado_estructura = mysqli_query($mysqli, $sql_estructura);

if ($resultado_estructura) {
    echo "<h3>Campos Disponibles:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th>";
    echo "</tr>";
    
    while ($campo = mysqli_fetch_assoc($resultado_estructura)) {
        echo "<tr>";
        echo "<td><strong>" . $campo['Field'] . "</strong></td>";
        echo "<td>" . $campo['Type'] . "</td>";
        echo "<td>" . $campo['Null'] . "</td>";
        echo "<td>" . $campo['Key'] . "</td>";
        echo "<td>" . ($campo['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $campo['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Mostrar algunos datos de ejemplo
    echo "<h3>Ejemplo de Datos (últimos 3 registros):</h3>";
    $sql_ejemplo = "SELECT * FROM integmovimientos_independiente ORDER BY fecha_alta_integMovIndep DESC LIMIT 3";
    $resultado_ejemplo = mysqli_query($mysqli, $sql_ejemplo);
    
    if ($resultado_ejemplo && mysqli_num_rows($resultado_ejemplo) > 0) {
        echo "<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size: 12px;'>";
        
        // Headers
        $first_row = mysqli_fetch_assoc($resultado_ejemplo);
        mysqli_data_seek($resultado_ejemplo, 0); // Reset pointer
        
        echo "<tr style='background-color: #f0f0f0;'>";
        foreach (array_keys($first_row) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        // Data
        while ($row = mysqli_fetch_assoc($resultado_ejemplo)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . ($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay datos de ejemplo en la tabla.</p>";
    }
    
} else {
    echo "<p style='color:red'>❌ Error al verificar estructura: " . mysqli_error($mysqli) . "</p>";
}

mysqli_close($mysqli);
?>
