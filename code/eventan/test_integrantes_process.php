<?php
echo "<h1>Resultados de Test Integrantes</h1>";

echo "<h2>Todos los datos POST:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Arrays específicos:</h2>";

echo "<h3>gen_integVenta:</h3>";
if (isset($_POST['gen_integVenta']) && is_array($_POST['gen_integVenta'])) {
    echo "Encontrado! Cantidad: " . count($_POST['gen_integVenta']) . "<br>";
    print_r($_POST['gen_integVenta']);
} else {
    echo "❌ NO ENCONTRADO o no es array<br>";
}

echo "<h3>rango_integVenta:</h3>";
if (isset($_POST['rango_integVenta']) && is_array($_POST['rango_integVenta'])) {
    echo "Encontrado! Cantidad: " . count($_POST['rango_integVenta']) . "<br>";
    print_r($_POST['rango_integVenta']);
} else {
    echo "❌ NO ENCONTRADO o no es array<br>";
}

echo "<h3>cant_integVenta (array):</h3>";
if (isset($_POST['cant_integVenta']) && is_array($_POST['cant_integVenta'])) {
    echo "Encontrado! Cantidad: " . count($_POST['cant_integVenta']) . "<br>";
    print_r($_POST['cant_integVenta']);
} else {
    echo "❌ NO ENCONTRADO o no es array<br>";
}

echo "<br><a href='test_integrantes.php'>Volver al formulario</a>";
?>
