<?php
	include("../../conexion.php");
	$id_correg=intval($_REQUEST['id_correg']);
	$veredas = $mysqli->prepare("SELECT * FROM veredas WHERE id_correg = '$id_correg'") or die(mysqli_error());
		echo '<option value = "">SELECCIONE UNA VEREDA: </option>';
	if($veredas->execute()){
		$a_result = $veredas->get_result();
	}
		while($row = $a_result->fetch_array()){
			echo '<option value = "'.$row['id_vere'].'">'.$row['nombre_vere'].'</option>';
		}
?>