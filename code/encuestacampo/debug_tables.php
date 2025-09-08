<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu = $_SESSION['id_usu'];
$tipo_usu = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");

include('../../conexion.php');

// Establecer charset UTF-8 para manejar tildes y ñ
mysqli_set_charset($mysqli, "utf8");

// Verificar si existe la tabla encuestacampo
$check_table = "SHOW TABLES LIKE 'encuestacampo'";
$result = mysqli_query($mysqli, $check_table);

if (mysqli_num_rows($result) == 0) {
    echo "<h3>Error: La tabla 'encuestacampo' no existe en la base de datos.</h3>";
    echo "<p>Por favor, asegúrese de que la tabla esté creada antes de continuar.</p>";
    exit();
}

// Verificar estructura de la tabla
$check_columns = "DESCRIBE encuestacampo";
$result = mysqli_query($mysqli, $check_columns);

echo "<h3>Estructura de la tabla encuestacampo:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";

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

// Verificar si existe la tabla integcampo
$check_table2 = "SHOW TABLES LIKE 'integcampo'";
$result2 = mysqli_query($mysqli, $check_table2);

if (mysqli_num_rows($result2) == 0) {
    echo "<h3>Error: La tabla 'integcampo' no existe en la base de datos.</h3>";
    echo "<p>Por favor, asegúrese de que la tabla esté creada antes de continuar.</p>";
} else {
    echo "<h3>✓ La tabla 'integcampo' existe.</h3>";
}

?>

<p><a href="showsurvey.php">Volver al listado</a></p>
