<?php
include("conexion.php");

echo "=== VERIFICACIÓN DE MUNICIPIOS DE RISARALDA ===\n\n";

// Buscar municipios de Risaralda
echo "Municipios del departamento de Risaralda (código 66):\n";
$sql = "SELECT * FROM municipios WHERE cod_municipio LIKE '66%' ORDER BY cod_municipio";
$result = mysqli_query($mysqli, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   ID=" . $row['id_municipio'] . ", COD=" . $row['cod_municipio'] . ", NOMBRE=" . $row['nombre_municipio'] . "\n";
    }
} else {
    echo "   ❌ NO SE ENCONTRARON municipios con código que inicie con 66\n";
}

// Verificar si existe específicamente 6601
echo "\n¿Qué municipios tienen código cercano a 6601?\n";
$sql = "SELECT * FROM municipios WHERE cod_municipio BETWEEN 6600 AND 6610 ORDER BY cod_municipio";
$result = mysqli_query($mysqli, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   ID=" . $row['id_municipio'] . ", COD=" . $row['cod_municipio'] . ", NOMBRE=" . $row['nombre_municipio'] . "\n";
    }
} else {
    echo "   ❌ NO SE ENCONTRARON municipios con código entre 6600 y 6610\n";
}

// Verificar algunos registros específicos que tienen este problema
echo "\n📋 Últimos registros con departamento 66 y ciudad 6601:\n";
$sql = "SELECT doc_encVenta, nom_encVenta, departamento_expedicion, ciudad_expedicion, fecha_alta_encVenta 
        FROM encventanilla 
        WHERE departamento_expedicion = 66 AND ciudad_expedicion = 6601 
        ORDER BY fecha_alta_encVenta DESC 
        LIMIT 5";
$result = mysqli_query($mysqli, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   DOC=" . $row['doc_encVenta'] . ", NOMBRE=" . $row['nom_encVenta'] . ", FECHA=" . $row['fecha_alta_encVenta'] . "\n";
    }
} else {
    echo "   ✅ No hay registros con departamento 66 y ciudad 6601\n";
}

echo "\n=== FIN VERIFICACIÓN ===\n";
?>
