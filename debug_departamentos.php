<?php
include("conexion.php");

echo "=== VERIFICACIÓN DE DEPARTAMENTOS Y MUNICIPIOS ===\n\n";

// Verificar departamento 66
echo "1. Verificando departamento 66:\n";
$sql = "SELECT * FROM departamentos WHERE id_departamento = 66 OR cod_departamento = 66";
$result = mysqli_query($mysqli, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   Encontrado: ID=" . $row['id_departamento'] . ", COD=" . $row['cod_departamento'] . ", NOMBRE=" . $row['nombre_departamento'] . "\n";
    }
} else {
    echo "   ❌ NO EXISTE departamento con ID o COD = 66\n";
}

// Verificar municipio 6601
echo "\n2. Verificando municipio 6601:\n";
$sql = "SELECT * FROM municipios WHERE id_municipio = 6601 OR cod_municipio = 6601";
$result = mysqli_query($mysqli, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   Encontrado: ID=" . $row['id_municipio'] . ", COD=" . $row['cod_municipio'] . ", NOMBRE=" . $row['nombre_municipio'] . "\n";
    }
} else {
    echo "   ❌ NO EXISTE municipio con ID o COD = 6601\n";
}

// Verificar registros en encventanilla con estos valores
echo "\n3. Contando registros en encventanilla con departamento_expedicion = 66:\n";
$sql = "SELECT COUNT(*) as total FROM encventanilla WHERE departamento_expedicion = 66";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
echo "   Total registros: " . $row['total'] . "\n";

echo "\n4. Contando registros en encventanilla con ciudad_expedicion = 6601:\n";
$sql = "SELECT COUNT(*) as total FROM encventanilla WHERE ciudad_expedicion = 6601";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
echo "   Total registros: " . $row['total'] . "\n";

// Verificar registros en informacion con estos valores
echo "\n5. Contando registros en informacion con departamento_expedicion = 66:\n";
$sql = "SELECT COUNT(*) as total FROM informacion WHERE departamento_expedicion = 66";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
echo "   Total registros: " . $row['total'] . "\n";

echo "\n6. Contando registros en informacion con ciudad_expedicion = 6601:\n";
$sql = "SELECT COUNT(*) as total FROM informacion WHERE ciudad_expedicion = 6601";
$result = mysqli_query($mysqli, $sql);
$row = mysqli_fetch_assoc($result);
echo "   Total registros: " . $row['total'] . "\n";

// Mostrar algunos departamentos válidos
echo "\n7. Algunos departamentos válidos:\n";
$sql = "SELECT * FROM departamentos LIMIT 5";
$result = mysqli_query($mysqli, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo "   ID=" . $row['id_departamento'] . ", COD=" . $row['cod_departamento'] . ", NOMBRE=" . $row['nombre_departamento'] . "\n";
}

// Mostrar algunos municipios válidos
echo "\n8. Algunos municipios válidos:\n";
$sql = "SELECT * FROM municipios LIMIT 5";
$result = mysqli_query($mysqli, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo "   ID=" . $row['id_municipio'] . ", COD=" . $row['cod_municipio'] . ", NOMBRE=" . $row['nombre_municipio'] . "\n";
}

echo "\n=== FIN VERIFICACIÓN ===\n";
?>
