<?php
/**
 * Script para verificar movimientos disponibles para editar
 */
include("../../conexion.php");

echo "<h2>📋 Movimientos Disponibles para Prueba</h2>";

$sql = "SELECT id_movimiento, doc_encVenta, nom_encVenta, integra_encVenta, tipo_movimiento 
        FROM movimientos 
        WHERE estado_ficha = 1 
        ORDER BY id_movimiento DESC 
        LIMIT 10";

$resultado = mysqli_query($mysqli, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>ID</th><th>Documento</th><th>Nombre</th><th>Integrantes</th><th>Tipo</th><th>Acciones</th>";
    echo "</tr>";
    
    while ($movimiento = mysqli_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . $movimiento['id_movimiento'] . "</td>";
        echo "<td>" . $movimiento['doc_encVenta'] . "</td>";
        echo "<td>" . $movimiento['nom_encVenta'] . "</td>";
        echo "<td>" . $movimiento['integra_encVenta'] . "</td>";
        echo "<td>" . $movimiento['tipo_movimiento'] . "</td>";
        echo "<td>";
        echo "<a href='editMovimiento.php?id_movimiento=" . $movimiento['id_movimiento'] . "' target='_blank'>✏️ Editar</a> | ";
        echo "<a href='debug_integrantes.php?id_movimiento=" . $movimiento['id_movimiento'] . "' target='_blank'>🔍 Debug</a>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><p style='color: green;'>✅ Usa los enlaces de 'Editar' para probar el sistema corregido.</p>";
    echo "<p style='color: blue;'>🔍 Usa los enlaces de 'Debug' para verificar los datos de integrantes.</p>";
} else {
    echo "<p style='color: red;'>❌ No se encontraron movimientos para probar.</p>";
}

mysqli_close($mysqli);
?>
