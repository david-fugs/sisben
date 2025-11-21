<?php
// Test para verificar la estructura de datos de integrantes
include("../../conexion.php");

mysqli_set_charset($mysqli, "utf8");

$doc_test = "123";

echo "<h2>Test de consulta de integrantes</h2>";
echo "<h3>Documento: $doc_test</h3>";

// Consultar integrantes
$sql_integ = "SELECT * FROM integcampo WHERE documento = '$doc_test' ORDER BY id_integCampo ASC";
$res_integ = mysqli_query($mysqli, $sql_integ);

if (!$res_integ) {
    echo "<pre>Error en consulta: " . mysqli_error($mysqli) . "</pre>";
} else {
    $count = mysqli_num_rows($res_integ);
    echo "<p><strong>Integrantes encontrados: $count</strong></p>";
    
    if ($count > 0) {
        echo "<pre>";
        while ($integ = mysqli_fetch_assoc($res_integ)) {
            print_r($integ);
            echo "\n\n---\n\n";
        }
        echo "</pre>";
        
        // Reset y mostrar JSON
        mysqli_data_seek($res_integ, 0);
        $integrantes = [];
        while ($integ = mysqli_fetch_assoc($res_integ)) {
            $integrantes[] = $integ;
        }
        
        echo "<h3>JSON:</h3>";
        echo "<pre>" . json_encode($integrantes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    } else {
        echo "<p>No se encontraron integrantes</p>";
    }
}

// Mostrar estructura de la tabla
echo "<h3>Estructura de la tabla integcampo:</h3>";
$sql_estructura = "DESCRIBE integcampo";
$res_estructura = mysqli_query($mysqli, $sql_estructura);

if ($res_estructura) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($campo = mysqli_fetch_assoc($res_estructura)) {
        echo "<tr>";
        echo "<td>" . $campo['Field'] . "</td>";
        echo "<td>" . $campo['Type'] . "</td>";
        echo "<td>" . $campo['Null'] . "</td>";
        echo "<td>" . $campo['Key'] . "</td>";
        echo "<td>" . $campo['Default'] . "</td>";
        echo "<td>" . $campo['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

mysqli_close($mysqli);
?>
