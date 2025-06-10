<?php
// Verificar estructuras de tablas para migración completa
include("../../conexion.php");
header("Content-Type: text/html;charset=utf-8");

echo "<h2>🔍 Verificación de Estructuras de Tablas</h2>";

// Función para mostrar estructura de tabla
function mostrarEstructura($mysqli, $tabla) {
    echo "<h3>📋 Estructura de la tabla <strong>$tabla</strong></h3>";
    
    $desc_query = "DESCRIBE $tabla";
    $result_desc = $mysqli->query($desc_query);
    
    if ($result_desc) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px; width: 100%;'>";
        echo "<tr style='background-color: #f8f9fa;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($field = $result_desc->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>" . $field['Field'] . "</strong></td>";
            echo "<td>" . $field['Type'] . "</td>";
            echo "<td>" . $field['Null'] . "</td>";
            echo "<td>" . $field['Key'] . "</td>";
            echo "<td>" . ($field['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . ($field['Extra'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Error al describir tabla $tabla: " . $mysqli->error . "</p>";
    }
}

// Verificar estructura de encventanilla
mostrarEstructura($mysqli, 'encventanilla');

// Verificar estructura actual de movimientos
mostrarEstructura($mysqli, 'movimientos');

// Verificar si existe tabla movimientos_completo
$check_table = "SHOW TABLES LIKE 'movimientos_completo'";
$result_check = $mysqli->query($check_table);

if ($result_check && $result_check->num_rows > 0) {
    echo "<h3>✅ Tabla movimientos_completo YA EXISTE</h3>";
    mostrarEstructura($mysqli, 'movimientos_completo');
} else {
    echo "<h3>⚠️ Tabla movimientos_completo NO EXISTE</h3>";
    echo "<p>Necesita ser creada para la migración completa.</p>";
}

// Mostrar algunos registros de muestra
echo "<h3>📊 Datos de Muestra</h3>";

echo "<h4>Encventanilla (5 registros):</h4>";
$sample_enc = "SELECT id_encVenta, doc_encVenta, nom_encVenta, tram_solic_encVenta, fecha_alta_encVenta FROM encventanilla ORDER BY id_encVenta DESC LIMIT 5";
$result_sample = $mysqli->query($sample_enc);

if ($result_sample && $result_sample->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px;'>";
    echo "<tr style='background-color: #f8f9fa;'><th>ID</th><th>Documento</th><th>Nombre</th><th>Trámite</th><th>Fecha Alta</th></tr>";
    
    while ($row = $result_sample->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_encVenta'] . "</td>";
        echo "<td>" . $row['doc_encVenta'] . "</td>";
        echo "<td>" . substr($row['nom_encVenta'], 0, 30) . "</td>";
        echo "<td>" . $row['tram_solic_encVenta'] . "</td>";
        echo "<td>" . $row['fecha_alta_encVenta'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h4>Movimientos actuales (5 registros):</h4>";
$sample_mov = "SELECT * FROM movimientos ORDER BY id_movimiento DESC LIMIT 5";
$result_mov = $mysqli->query($sample_mov);

if ($result_mov && $result_mov->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px; width: 100%;'>";
    echo "<tr style='background-color: #f8f9fa;'>";
    
    // Obtener nombres de columnas
    $fields = $result_mov->fetch_fields();
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    
    // Reiniciar el resultado
    $result_mov->data_seek(0);
    
    while ($row = $result_mov->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . (is_null($value) ? 'NULL' : substr($value, 0, 30)) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay registros en movimientos o error: " . $mysqli->error . "</p>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verificación de Estructuras</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px 20px; margin: 10px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div style="margin-top: 30px;">
        <h3>🚀 Acciones Disponibles</h3>
        <a href="ejecutar_migracion_completa.php" class="btn">Ejecutar Migración Completa</a>
        <a href="test_nueva_estructura.php" class="btn">Probar Nueva Estructura</a>
        <a href="../eventan/movimientosEncuesta.php" class="btn">Ver Movimientos Encuesta</a>
    </div>
</body>
</html>
