<?php
include('conexion.php');

echo "=== ESTRUCTURA TABLA DEPARTAMENTOS ===\n";
$result = mysqli_query($mysqli, 'DESCRIBE departamentos');
while($row = mysqli_fetch_array($result)) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== ESTRUCTURA TABLA MUNICIPIOS ===\n";
$result = mysqli_query($mysqli, 'DESCRIBE municipios');
while($row = mysqli_fetch_array($result)) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== EJEMPLO DEPARTAMENTO 66 ===\n";
$result = mysqli_query($mysqli, 'SELECT * FROM departamentos WHERE codigo_departamento = 66 OR id_departamento = 66 LIMIT 1');
while($row = mysqli_fetch_array($result)) {
    print_r($row);
}

echo "\n=== EJEMPLO MUNICIPIO 6601 ===\n";
$result = mysqli_query($mysqli, 'SELECT * FROM municipios WHERE codigo_municipio = 6601 OR id_municipio = 6601 LIMIT 1');
while($row = mysqli_fetch_array($result)) {
    print_r($row);
}

echo "\n=== DATOS GUARDADOS EN ENCVENTANILLA ===\n";
$result = mysqli_query($mysqli, 'SELECT departamento_expedicion, ciudad_expedicion FROM encventanilla WHERE departamento_expedicion = 66 LIMIT 3');
echo "Registros con departamento_expedicion = 66:\n";
while($row = mysqli_fetch_array($result)) {
    echo "Depto: " . $row['departamento_expedicion'] . " - Ciudad: " . $row['ciudad_expedicion'] . "\n";
}
?>
