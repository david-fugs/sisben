<?php
include("../../conexion.php");

echo "=== ESTRUCTURA DE TABLA integmovimientos_independiente ===\n";

$result = $mysqli->query('DESCRIBE integmovimientos_independiente');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . "\n";
}
?>
