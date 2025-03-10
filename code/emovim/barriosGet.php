<?php
	include("../../conexion.php");
	$id_com=intval($_REQUEST['id_com']);
	$barrios = $mysqli->prepare("SELECT * FROM barrios WHERE id_com = '$id_com'") or die(mysqli_error());
		echo '<option value = "">SELECCIONE EL BARRIO: </option>';
	if($barrios->execute()){
		$a_result = $barrios->get_result();
	}
		while($row = $a_result->fetch_array()){
			echo '<option value = "'.$row['id_bar'].'">'.$row['nombre_bar'].'</option>';
		}
?>