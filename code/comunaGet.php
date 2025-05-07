<?php
include("../conexion.php");
$id_barrio = isset($_GET['id_barrio']) ? intval($_GET['id_barrio']) : 0;
$comunas = $mysqli->prepare("
    SELECT c.nombre_com, c.id_com 
    FROM barrios as b 
    JOIN comunas as c ON b.id_com = c.id_com 
    WHERE b.id_bar = ?
") or die(mysqli_error($mysqli));
$comunas->bind_param("i", $id_barrio);
echo '<option value = "">SELECCIONE COMUNA O VEREDA: </option>';
if ($comunas->execute()) {
	$a_result = $comunas->get_result();
}
while ($row = $a_result->fetch_array()) {
	echo '<option value = "' . $row['id_com'] . '">' . $row['nombre_com'] . '</option>';
}
