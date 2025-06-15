<?php
echo "<h1>Datos recibidos por POST:</h1>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Arrays específicos:</h2>";
if (isset($_POST['gen_integVenta'])) {
    echo "<h3>gen_integVenta:</h3>";
    print_r($_POST['gen_integVenta']);
} else {
    echo "<h3>gen_integVenta: NO RECIBIDO</h3>";
}

if (isset($_POST['rango_integVenta'])) {
    echo "<h3>rango_integVenta:</h3>";
    print_r($_POST['rango_integVenta']);
} else {
    echo "<h3>rango_integVenta: NO RECIBIDO</h3>";
}
?>
