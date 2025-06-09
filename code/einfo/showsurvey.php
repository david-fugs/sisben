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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	<style>
		.hover-bg:hover {
			background-color: #f5f5f5;
			cursor: pointer;
		}

		.responsive {
			max-width: 100%;
			height: auto;
		}

		.selector-for-some-widget {
			box-sizing: content-box;
		}

		/* Estilos para registros destacados */
		.registro-multiple {
			background-color: #e8f4f8 !important;
			border-left: 4px solid #17a2b8 !important;
		}

		.badge-info {
			background-color: #17a2b8;
			color: white;
			padding: 4px 8px;
			border-radius: 12px;
			font-size: 0.8em;
			font-weight: bold;
		}

		.badge-success {
			background-color: #28a745;
			color: white;
			padding: 4px 8px;
			border-radius: 12px;
			font-size: 0.8em;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>

	<div class="container my-5">
		<div class="row align-items-center">
			<div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
				<img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 300px;">
			</div>
			<div class="col-md-8">
				<h1 class="text-primary fw-bold">
					<i class="fa-solid fa-circle-info me-2"></i> INFORMACIÓN DIGITADA
				</h1>
			</div>
		</div>
	</div>

	<div class="container my-4">
		<div class="card shadow-sm">
			<div class="card-body">
				<h5 class="card-title mb-3">Buscar registros</h5>
				<form action="showsurvey.php" method="get" class="row g-3 align-items-center">
					<div class="col-md-4">
						<input name="doc_info" type="text" class="form-control" placeholder="Ingrese el Documento" value="<?php echo isset($_GET['doc_info']) ? $_GET['doc_info'] : ''; ?>">
					</div>
					<div class="col-md-5">
						<input name="nom_info" type="text" class="form-control" placeholder="Nombre del usuario" value="<?php echo isset($_GET['nom_info']) ? $_GET['nom_info'] : ''; ?>">
					</div>
					<div class="col-md-3">
						<button type="submit" class="btn btn-success w-100">
							<i class="fa fa-search me-1"></i> Buscar
						</button>
					</div>
				</form>
			</div>
		</div>
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
			$resul_x_pagina = 200;			echo "<section class='content'>
			<div class='container-fluid mt-3'>
				<div class='table-responsive'>
					<table class='table table-bordered table-striped table-hover align-middle text-center'>
						<thead class='table-dark'>
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
				// Añadir clase especial para registros múltiples si es necesario
				$claseEspecial = '';
				
				echo '
				<tr class="' . $claseEspecial . '">
					<td>' . $i++ . '</td>
					<td>' . $row['fecha_reg_info'] . '</td>
					<td><span class="badge-info">' . $row['doc_info'] . '</span></td>
					<td>' . $row['nom_info'] . '</td>
					<td><span class="badge-success">' . strtoupper($row['tipo_documento']) . '</span></td>
					<td>' . $row['observacion'] . '</td>
					<td>' . $row['tipo_solic_encInfo'] . '</td>
					<td>' . $row['nombre'] . '</td>
					<td>
						<a href="showencInfo.php?id_informacion=' . $row['id_informacion'] . '" class="btn btn-sm btn-outline-primary">
							<i class="fa fa-search"></i>
						</a>
					</td>
					<td>
						<a href="eliminarInformaciones.php?id_informacion=' . $row['id_informacion'] . '" 
						   onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?\')" 
						   class="btn btn-sm btn-outline-danger">
							<i class="fa fa-trash"></i>
						</a>
					</td>
				</tr>';
			}			echo '</tbody>
					</table>
				</div>
			</div>';

			$paginacion->render();

			?>
			<div class="container my-4">
				<div class="share-container text-center">
					<!-- Go to www.addthis.com/dashboard to customize your tools -->
					<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
					<!-- Go to www.addthis.com/dashboard to customize your tools -->
					<div class="addthis_sharing_toolbox"></div>
				</div>
				
				<div class="text-center mt-4">
					<a href="../../access.php" class="btn btn-secondary btn-lg">
						<i class="fa fa-arrow-left me-2"></i> Regresar
					</a>
				</div>
			</div>

		</section>	<script src="js/app.js"></script>
	<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>

</body>

</html>