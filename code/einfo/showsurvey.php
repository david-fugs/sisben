<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
	header("Location: ../../index.php");
	exit();  // Asegúrate de salir del script después de redirigir
}

$id_usu 	= $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>BD SISBEN</title>
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">

	<style>
		.responsive {
			max-width: 100%;
			height: auto;
		}

		.selector-for-some-widget {
			box-sizing: content-box;
		}
	</style>
</head>

<body>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>

	<center>
		<img src='../../img/sisben.png' width=300 height=185 class="responsive">
	</center>

	<section class="principal">

		<div style="border-radius: 9px 9px 9px 9px; -moz-border-radius: 9px 9px 9px 9px; -webkit-border-radius: 9px 9px 9px 9px; border: 4px solid #FFFFFF;" align="center">

			<div align="center">
				<h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em"><b><i class="fa-solid fa-circle-info"></i> INFORMACIÓN DIGITADA</b></h1>
			</div>

			<div style="border-radius: 9px 9px 9px 9px; -moz-border-radius: 9px 9px 9px 9px; -webkit-border-radius: 9px 9px 9px 9px; border: 1px solid #efd47d; width: 500px; height: 30px; background:#FAFAFA; display:table-cell; vertical-align:middle;">

				<label for="buscar">Datos de búsqueda</label>

				<form action="showsurvey.php" method="get">
					<input name="doc_info" type="text" placeholder="Ingrese el Documento" size="20" value="<?php echo isset($_GET['doc_info']) ? $_GET['doc_info'] : ''; ?>">
					<input name="nom_info" type="text" placeholder="Nombre del usuario" size="30" value="<?php echo isset($_GET['nom_info']) ? $_GET['nom_info'] : ''; ?>">
					<input value="Buscar" type="submit">
				</form>

			</div>

			<?php
			date_default_timezone_set("America/Bogota");
			include("../../conexion.php");
			require_once("../../zebra.php");

			$where = [];

			if ($tipo_usu != '1') {
				$where[] = "informacion.id_usu = '$id_usu'";
			}

			// Si se envió el documento, agrégalo al filtro
			if (!empty($_GET['doc_info'])) {
				$doc_info = $mysqli->real_escape_string($_GET['doc_info']);
				$where[] = "informacion.doc_info = '$doc_info'";
			}

			// Si se envió el nombre, agrégalo al filtro
			if (!empty($_GET['nom_info'])) {
				$nom_info = $mysqli->real_escape_string($_GET['nom_info']);
				$where[] = "informacion.nom_info LIKE '%$nom_info%'";
			}

			// Construir la consulta con los filtros dinámicos
			$query = "SELECT informacion.*, usuarios.nombre 
					  FROM informacion 
					  JOIN usuarios ON informacion.id_usu = usuarios.id_usu";

			if (!empty($where)) {
				$query .= " WHERE " . implode(" AND ", $where);
			}

			$result = $mysqli->query($query);
			$res = $mysqli->query($query);
			$num_registros = mysqli_num_rows($res);
			$resul_x_pagina = 200;

			echo "<section class='content'>
			<div class='card-body'>
        		<div class='table-responsive'>
		        	<table>
		            	<thead>
		                	<tr>
								<th>No.</th>
								<th>FECHA REGISTRO</th>
								<th>DOCUMENTO</th>
								<th>NOMBRE</th>
								<th>TIPO DOC.</th>
				        		<th>OBSERVACION</th>
								<th>TIPO SOLICITUD</th>
								<th>ENCUESTADOR</th>
				        		<th>EDITAR</th>
				        		<th>ELIMINAR REG.</th>
				    		</tr>
				  		</thead>
            			<tbody>";

			$paginacion = new Zebra_Pagination();
			$paginacion->records($num_registros);
			$paginacion->records_per_page($resul_x_pagina);

			$consulta = "SELECT informacion.*, usuarios.nombre 
			FROM informacion 
			JOIN usuarios ON informacion.id_usu = usuarios.id_usu";

			if (!empty($where)) {
				$consulta .= " WHERE " . implode(" AND ", $where);
			}

			$result = $mysqli->query($consulta);


			$i = 1;
			while ($row = mysqli_fetch_array($result)) {
				echo '
				<tr>
					<td data-label="No.">' . $i++ . '</td>
					<td data-label="F. REA.">' . $row['fecha_reg_info'] . '</td>
					<td data-label="DOC. USU">' . $row['doc_info'] . '</td>
					<td data-label="NOMBRE">' . $row['nom_info'] . '</td>
					<td data-label="TIPO">' . strtoupper($row['tipo_documento']) . ' </td>
					<td data-label="OBSERVACION">' . $row['observacion'] . '</td>
					<td data-label="tipo solic">' . $row['tipo_solic_encInfo'] . '</td>
					<td data-label="ENCUESTADOR">' . $row['nombre'] . '</td>
					<td data-label="EDITAR"><a href="showencInfo.php?id_informacion=' . $row['id_informacion'] . '" ><img src="../../img/search.png" width=28 height=28></a></td>
					<td data-label="ELIMINAR"><a href="eliminarInformaciones.php?id_informacion=' . $row['id_informacion'] . '" onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?\')"><img src="../../img/eliminar.png" width=28 height=28></a></td>
				</tr>';
			}

			echo '</table>
		</div>

		';

			$paginacion->render();

			?>
			<div class="share-container">
				<!-- Go to www.addthis.com/dashboard to customize your tools -->
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
				<!-- Go to www.addthis.com/dashboard to customize your tools -->
				<div class="addthis_sharing_toolbox"></div>
			</div>
			<center>
				<br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
			</center>

		</div>
		</div>
	</section>
	<script src="js/app.js"></script>
	<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>

</body>

</html>