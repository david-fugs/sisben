<?php
include('conexion.php');

echo "<h1>ESTRUCTURA ACTUAL DE LA TABLA MOVIMIENTOS</h1>";

// Verificar estructura
echo "<h2>Columnas:</h2>";
$result = mysqli_query($mysqli, 'DESCRIBE movimientos');
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($mysqli);
}

// Mostrar datos de ejemplo
echo "<h2>Ejemplo de datos (1 registro):</h2>";
$sample = mysqli_query($mysqli, 'SELECT * FROM movimientos ORDER BY id_movimiento DESC LIMIT 1');
if ($sample && mysqli_num_rows($sample) > 0) {
    $row = mysqli_fetch_assoc($sample);
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Valor</th></tr>";
    foreach ($row as $campo => $valor) {
        echo "<tr><td><strong>$campo</strong></td><td>" . ($valor ?? 'NULL') . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No hay datos de ejemplo";
}

// Contar registros
$count_result = mysqli_query($mysqli, 'SELECT COUNT(*) as total FROM movimientos');
if ($count_result) {
    $count = mysqli_fetch_assoc($count_result)['total'];
    echo "<h2>Total de registros: $count</h2>";
}

mysqli_close($mysqli);
?>
