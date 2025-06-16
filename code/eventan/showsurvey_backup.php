<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
	header("Location: ../../index.php");
	exit();  // Asegúrate de salir del script después de redirigir
}

$id_usu 	= $_SESSION['id_usu'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];
								<th>FICHA</th>
								<th>ESTADO</th>
								<th>EDITAR</th>
								<th>ELIMINAR REG.</th>
							</tr>
						</thead>
						<tbody>";ION['tipo_usu'];
header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">	<title>BD SISBEN</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
	<style>
		.responsive {
			max-width: 100%;
			height: auto;
		}

		.hover-bg:hover {
			background-color: #f5f5f5;
			cursor: pointer;
		}

		.selector-for-some-widget {
			box-sizing: content-box;
		}

		/* Estilos para fichas retiradas */
		.ficha-retirada {
			background-color: #ffebee !important;
			background-image: linear-gradient(45deg, #ffcdd2 25%, transparent 25%, transparent 75%, #ffcdd2 75%, #ffcdd2),
							  linear-gradient(45deg, #ffcdd2 25%, transparent 25%, transparent 75%, #ffcdd2 75%, #ffcdd2);
			background-size: 20px 20px;
			background-position: 0 0, 10px 10px;
			border-left: 5px solid #f44336 !important;
		}

		.estado-ficha-retirada {
			background-color: #f44336;
			color: white;
			padding: 6px 12px;
			border-radius: 15px;
			font-size: 0.85em;
			font-weight: bold;
			text-transform: uppercase;
		}

		.estado-ficha-activa {
			background-color: #4caf50;
			color: white;
			padding: 6px 12px;
			border-radius: 15px;
			font-size: 0.85em;
			font-weight: bold;
			text-transform: uppercase;
		}

		.table-hover tbody tr:hover {
			background-color: #f8f9fa;
		}

		.search-form {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			border-radius: 15px;
			padding: 25px;
			margin-bottom: 30px;
		}

		.btn-custom {
			background: linear-gradient(45deg, #667eea, #764ba2);
			border: none;
			color: white;
			border-radius: 25px;
			padding: 10px 25px;
			transition: all 0.3s ease;
		}

		.btn-custom:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0,0,0,0.2);
			color: white;
		}

		.btn-outline-secondary {
			background-color: #6c757d;
			color: white;
			border-color: #6c757d;
			border-radius: 25px;
			padding: 10px 25px;
			transition: all 0.3s ease;
		}

		.btn-outline-secondary:hover {
			background-color: #5a6268;
			border-color: #5a6268;
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0,0,0,0.2);
		}

		.card-stats {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			border-radius: 15px;
			margin-bottom: 20px;
		}

		/* Estilos para la paginación */
		.zebra_pagination {
			margin: 20px 0;
			text-align: center;
		}

		.zebra_pagination a, 
		.zebra_pagination span {
			display: inline-block;
			padding: 8px 12px;
			margin: 0 2px;
			text-decoration: none;
			border: 1px solid #dee2e6;
			border-radius: 6px;
			color: #6c757d;
			background-color: #fff;
			transition: all 0.3s ease;
		}

		.zebra_pagination a:hover {
			background-color: #e9ecef;
			border-color: #adb5bd;
			color: #495057;
			text-decoration: none;
		}

		.zebra_pagination .current {
			background: linear-gradient(45deg, #667eea, #764ba2);
			border-color: #667eea;
			color: white !important;
			font-weight: bold;
		}

		.zebra_pagination .disabled {
			color: #6c757d;
			background-color: #fff;
			border-color: #dee2e6;
			opacity: 0.5;
			cursor: not-allowed;
		}

		.info-pagination {
			text-align: center;
			color: #6c757d;
			margin-bottom: 15px;
			font-size: 0.9em;
		}

		.btn-action {
			border-radius: 20px;
			padding: 5px 10px;
			font-size: 0.85em;
			transition: all 0.3s ease;
		}

		.btn-action:hover {
			transform: translateY(-1px);
			box-shadow: 0 3px 10px rgba(0,0,0,0.2);
		}
	</style>
</head>

<body>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>
	<div class="container my-5">
		<div class="row align-items-center mb-4">
			<div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
				<img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 300px;">
			</div>
			<div class="col-md-8">
				<h1 class="text-primary fw-bold">
					<i class="fa-solid fa-address-card me-2"></i> ENCUESTAS REALIZADAS
				</h1>
				<p class="text-muted">Sistema de gestión de encuestas de ventanilla</p>
			</div>
		</div>
	</div>
	<div class="container my-4">
		<div class="search-form">
			<h5 class="mb-3"><i class="fa fa-search me-2"></i>Buscar Encuestas</h5>
			<form action="showsurvey.php" method="get">
				<div class="row g-3">
					<div class="col-md-4">
						<label class="form-label">Documento</label>
						<input name="doc_encVenta" type="text" class="form-control" placeholder="Número de documento" value="<?php echo isset($_GET['doc_encVenta']) ? $_GET['doc_encVenta'] : ''; ?>">
					</div>
					<div class="col-md-5">
						<label class="form-label">Número de ficha</label>
						<input name="num_ficha_encVenta" type="text" class="form-control" placeholder="Número de ficha" value="<?php echo isset($_GET['num_ficha_encVenta']) ? $_GET['num_ficha_encVenta'] : ''; ?>">
					</div>
					<div class="col-md-3 d-flex align-items-end">
						<button type="submit" class="btn btn-custom w-100">
							<i class="fa fa-search me-1"></i> Buscar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>


	<?php

	date_default_timezone_set("America/Bogota");
	include("../../conexion.php");
	require_once("../../zebra.php");

	@$doc_encVenta = ($_GET['doc_encVenta']);
	@$num_ficha_encVenta = ($_GET['num_ficha_encVenta']);	$query = "SELECT COUNT(*) as total FROM encventanilla 
				WHERE (encventanilla.doc_encVenta LIKE '%$doc_encVenta%') 
				AND (encventanilla.num_ficha_encVenta LIKE '%$num_ficha_encVenta%')";

	if ($tipo_usu != '1') {
		$query .= " AND encventanilla.id_usu = $id_usu";
	}

	$query .= " ORDER BY encventanilla.fec_reg_encVenta ASC";

	$res = $mysqli->query($query);
	$num_registros = mysqli_num_rows($res);
	$resul_x_pagina = 200;

	echo "<section class='content'>
			<div class='container-fluid mt-3'>
				<div class='table-responsive'>
					<table class='table table-bordered table-striped table-hover align-middle text-center'>						<thead class='table-dark'>
							<tr>
								<th>No.</th>
								<th>F. REA.</th>
								<th>DOC. USU.</th>
								<th>NOMBRE</th>
								<th>FICHA</th>
								<th>ESTADO</th>
								<th>EDITAR</th>
								<th>ELIMINAR REG.</th>
							</tr>
						</thead>
						<tbody'>";

	$paginacion = new Zebra_Pagination();
	$paginacion->records($num_registros);
	$paginacion->records_per_page($resul_x_pagina);	$consulta = "SELECT encventanilla.*, 
				CASE 
					WHEN encventanilla.estado_ficha = 0 THEN 'FICHA RETIRADA'
					ELSE 'ACTIVA'
				END as estado_ficha_texto
				FROM encventanilla 
				WHERE (encventanilla.doc_encVenta LIKE '%" . $doc_encVenta . "%') 
				AND (encventanilla.num_ficha_encVenta LIKE '%" . $num_ficha_encVenta . "%')";

	if ($tipo_usu != '1') {
		$consulta .= " AND encventanilla.id_usu = $id_usu";
	}

	$consulta .= " ORDER BY encventanilla.fec_reg_encVenta ASC 
				LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
	$result = $mysqli->query($consulta);
	$i = 1;
	while ($row = mysqli_fetch_array($result)) {		// Determinar si la fila debe tener el estilo de ficha retirada
		$claseFilaRetirada = ($row['estado_ficha'] == 0) ? 'ficha-retirada' : '';
		$estadoFicha = ($row['estado_ficha'] == 0) ? 'estado-ficha-retirada' : 'estado-ficha-activa';
		echo '
				<tr class="' . $claseFilaRetirada . '">
		<td>' . $i++ . '</td>
		<td>' . $row['fecha_alta_encVenta'] . '</td>
		<td>' . $row['doc_encVenta'] . '</td>
		<td>' . $row['nom_encVenta'] . '</td>
		<td>' . $row['num_ficha_encVenta'] . '</td>
		<td><span class="' . $estadoFicha . '">' . $row['estado_ficha_texto'] . '</span></td>
		<td>
			<a href="editEncuesta.php?id_encVenta=' . $row['id_encVenta'] . '" class="btn btn-sm btn-primary" title="Editar Encuesta">
				<i class="fas fa-edit"></i>
			</a>
		</td>
		<td>
			<a href="eliminarVentanilla.php?id_encVenta=' . $row['id_encVenta'] . '" onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?\')">
				<img src="../../img/eliminar.png" width="28" height="28" alt="Eliminar">
			</a>
		</td>
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
	<!-- MODAL MENUS -->
	<div class="modal fade" id="modalMenus" tabindex="-1" aria-labelledby="modalMenusLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content rounded-4 shadow-sm">
				<div class="modal-header border-bottom-0">
					<h1 class="modal-title fs-5 text-dark fw-semibold" id="modalMenusLabel">Selecciona un Movimiento</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>
				<div class="modal-body">
					<div class="list-group">
						<a href="#" class="list-group-item list-group-item-action movimiento-link"
							data-movimiento="INCLUSION"
							data-custom-url="addencVentanillaFamily.php">
							INCLUSION
						</a>
						<a href="#" class="list-group-item list-group-item-action movimiento-link"
							data-movimiento="INCONFOR_CLASIFICACION">INCONFORMIDAD POR CLASIFICACIÓN</a>
						<a href="#" class="list-group-item list-group-item-action movimiento-link"
							data-movimiento="DATOS_PERSONA">MODIFICACION DATOS PERSONA</a>
						<a href="#" class="list-group-item list-group-item-action movimiento-link"
							data-movimiento="RETIRO_PERSONAS"
							data-custom-url="showencVentanillaFamily.php">
							RETIRO PERSONAS
						</a>
						<a href="#" class="list-group-item list-group-item-action movimiento-link"
							data-movimiento="RETIRO_PERSONAS_INCONFORMIDAD"
							data-custom-url="showencVentanillaFamily.php">
							RETIRO PERSONAS POR INCONFORMIDAD
						</a>
					</div>
				</div>
				<div class="modal-footer border-top-0">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>




	<script src="js/app.js"></script>
	<script src="js/redirecciones_movimientos.js"></script>
	<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>

</body>

</html>