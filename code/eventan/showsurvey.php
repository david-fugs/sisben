<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
	header("Location: ../../index.php");
	exit();
}

$id_usu 	= $_SESSION['id_usu'];
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
	<title>BD SISBEN - Encuestas Realizadas</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
	<style>
		body {
			background: #ffffff;
			min-height: 100vh;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.main-container {
			background: rgba(255, 255, 255, 1);
			border-radius: 20px;
			box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
			margin: 20px auto;
			padding: 30px;
			border: 1px solid #e9ecef;
		}

		.header-section {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border-radius: 15px;
			padding: 25px;
			margin-bottom: 30px;
			color: white;
			text-align: center;
		}

		.header-section h1 {
			margin: 0;
			font-size: 2.5rem;
			font-weight: 700;
			text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
		}

		.search-card {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			border: none;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
			margin-bottom: 30px;
		}

		.search-card .card-body {
			padding: 25px;
		}

		.search-card .card-title {
			color: white;
			font-weight: 600;
			font-size: 1.3rem;
			margin-bottom: 20px;
		}

		.form-control {
			border-radius: 10px;
			border: 2px solid transparent;
			padding: 12px 15px;
			font-size: 1rem;
			transition: all 0.3s ease;
		}

		.form-control:focus {
			border-color: #667eea;
			box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
		}

		.btn-search {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			border-radius: 10px;
			padding: 12px 25px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 1px;
			transition: all 0.3s ease;
		}

		.btn-search:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
		}

		.table-container {
			background: white;
			border-radius: 15px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			overflow: hidden;
		}

		.table {
			margin-bottom: 0;
		}

		.table thead th {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			border: none;
			padding: 20px 15px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 1px;
			font-size: 0.9rem;
		}

		.table tbody tr {
			transition: all 0.3s ease;
		}

		.table tbody tr:hover {
			background-color: rgba(102, 126, 234, 0.1);
			transform: scale(1.01);
		}

		.table tbody td {
			padding: 15px;
			vertical-align: middle;
			border-bottom: 1px solid rgba(0, 0, 0, 0.1);
		}

		.btn-action {
			border-radius: 8px;
			padding: 8px 15px;
			font-weight: 600;
			text-decoration: none;
			transition: all 0.3s ease;
			display: inline-flex;
			align-items: center;
			gap: 5px;
		}

		.btn-edit {
			background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
			color: white;
			border: none;
		}

		.btn-edit:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(79, 172, 254, 0.4);
			color: white;
		}

		.btn-delete {
			background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
			color: white;
			border: none;
		}

		.btn-delete:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 20px rgba(250, 112, 154, 0.4);
			color: white;
		}

		.estado-badge {
			padding: 8px 15px;
			border-radius: 20px;
			font-weight: 600;
			font-size: 0.85rem;
			text-transform: uppercase;
			letter-spacing: 1px;
		}

		.estado-activa {
			background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
			color: white;
		}

		.estado-retirada {
			background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
			color: white;
		}

		.ficha-retirada {
			background: linear-gradient(135deg, rgba(250, 112, 154, 0.1) 0%, rgba(254, 225, 64, 0.1) 100%);
		}

		.back-button {
			position: fixed;
			bottom: 30px;
			left: 50%;
			transform: translateX(-50%);
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			border-radius: 50px;
			padding: 15px 25px;
			color: white;
			text-decoration: none;
			font-weight: 600;
			transition: all 0.3s ease;
			box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
		}

		.back-button:hover {
			transform: translateX(-50%) translateY(-5px);
			box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
			color: white;
		}

		.responsive {
			max-width: 100%;
			height: auto;
		}

		.pagination-container {
			margin-top: 30px;
			text-align: center;
		}
	</style>
</head>

<body>
	<div class="container-fluid">
		<div class="main-container">
			<!-- Header Section -->
			<div class="header-section">
				<div class="row align-items-center">
					<div class="col-md-3">
						<img src='../../img/sisben.png' class="img-fluid responsive" alt="Logo Sisben" style="max-width: 200px;">
					</div>
					<div class="col-md-9">
						<h1>
							<i class="fas fa-clipboard-list me-3"></i>
							ENCUESTAS REALIZADAS
						</h1>
						<p class="mb-0 mt-2" style="font-size: 1.1rem; opacity: 0.9;">
							Gestión y seguimiento de encuestas del sistema SISBEN
						</p>
					</div>
				</div>
			</div>

			<!-- Search Form -->
			<div class="search-card card">
				<div class="card-body">
					<h5 class="card-title">
						<i class="fas fa-search me-2"></i>
						Buscar Encuestas
					</h5>
					<form action="showsurvey.php" method="get" class="row g-3 align-items-center">
						<div class="col-md-4">
							<div class="form-floating">
								<input name="doc_encVenta" type="text" class="form-control" id="documento" placeholder="Documento" value="<?php echo isset($_GET['doc_encVenta']) ? htmlspecialchars($_GET['doc_encVenta']) : ''; ?>">
								<label for="documento">
									<i class="fas fa-id-card me-2"></i>Documento
								</label>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-floating">
								<input name="num_ficha_encVenta" type="text" class="form-control" id="ficha" placeholder="Número de ficha" value="<?php echo isset($_GET['num_ficha_encVenta']) ? htmlspecialchars($_GET['num_ficha_encVenta']) : ''; ?>">
								<label for="ficha">
									<i class="fas fa-file-alt me-2"></i>Número de Ficha
								</label>
							</div>
						</div>
						<div class="col-md-3">
							<button type="submit" class="btn btn-search btn-primary w-100">
								<i class="fas fa-search me-2"></i>
								Buscar
							</button>
						</div>
					</form>
				</div>
			</div>			<?php			date_default_timezone_set("America/Bogota");
			include("../../conexion.php");
			require_once("../../zebra.php");

			@$doc_encVenta = ($_GET['doc_encVenta']);
			@$num_ficha_encVenta = ($_GET['num_ficha_encVenta']);			$query = "SELECT COUNT(*) as total FROM encventanilla 
						WHERE (encventanilla.doc_encVenta LIKE '%$doc_encVenta%') 
						AND (encventanilla.num_ficha_encVenta LIKE '%$num_ficha_encVenta%')";

			if ($tipo_usu != '1') {
				$query .= " AND encventanilla.id_usu = $id_usu";
			}

			$query .= " ORDER BY encventanilla.fec_reg_encVenta ASC";

			$res = $mysqli->query($query);
			
			// Obtener el número de registros desde la consulta COUNT
			$row_count = $res->fetch_assoc();
			$num_registros = $row_count['total'];
			$resul_x_pagina = 200;

			echo "<div class='table-container'>";
			echo "<div class='table-responsive'>";
			echo "<table class='table table-hover align-middle'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th><i class='fas fa-hashtag me-2'></i>No.</th>";
			echo "<th><i class='fas fa-calendar me-2'></i>F. REA.</th>";
			echo "<th><i class='fas fa-id-card me-2'></i>DOC. USU.</th>";
			echo "<th><i class='fas fa-user me-2'></i>NOMBRE</th>";
			echo "<th><i class='fas fa-file-alt me-2'></i>FICHA</th>";
			echo "<th><i class='fas fa-info-circle me-2'></i>ESTADO</th>";
			echo "<th><i class='fas fa-edit me-2'></i>EDITAR</th>";
			echo "<th><i class='fas fa-trash me-2'></i>ELIMINAR</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			$paginacion = new Zebra_Pagination();
			$paginacion->records($num_registros);
			$paginacion->records_per_page($resul_x_pagina);

			$consulta = "SELECT encventanilla.*, 
						CASE 
							WHEN encventanilla.estado_ficha = 0 THEN 'FICHA RETIRADA'
							ELSE 'ACTIVA'
						END as estado_ficha_texto
						FROM encventanilla 
						WHERE (encventanilla.doc_encVenta LIKE '%" . $doc_encVenta . "%') 
						AND (encventanilla.num_ficha_encVenta LIKE '%" . $num_ficha_encVenta . "%')";

			if ($tipo_usu != '1') {
				$consulta .= " AND encventanilla.id_usu = $id_usu";
			}			$consulta .= " ORDER BY encventanilla.fec_reg_encVenta ASC 
						LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
					$result = $mysqli->query($consulta);
			
			$i = 1;
			
			while ($row = mysqli_fetch_array($result)) {
				$claseFilaRetirada = ($row['estado_ficha'] == 0) ? 'ficha-retirada' : '';
				$estadoClase = ($row['estado_ficha'] == 0) ? 'estado-retirada' : 'estado-activa';
				
				echo '<tr class="' . $claseFilaRetirada . '">';
				echo '<td><strong>' . $i++ . '</strong></td>';
				echo '<td>' . $row['fecha_alta_encVenta'] . '</td>';
				echo '<td><strong>' . $row['doc_encVenta'] . '</strong></td>';
				echo '<td>' . $row['nom_encVenta'] . '</td>';
				echo '<td><span class="badge bg-info">' . $row['num_ficha_encVenta'] . '</span></td>';
				echo '<td><span class="estado-badge ' . $estadoClase . '">' . $row['estado_ficha_texto'] . '</span></td>';
				echo '<td>';
				echo '<a href="editEncuesta.php?id_encVenta=' . $row['id_encVenta'] . '" class="btn-action btn-edit" title="Editar Encuesta">';
				echo '<i class="fas fa-edit"></i> Editar';
				echo '</a>';
				echo '</td>';
				echo '<td>';
				echo '<a href="eliminarVentanilla.php?id_encVenta=' . $row['id_encVenta'] . '" class="btn-action btn-delete" onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?\')" title="Eliminar Registro">';
				echo '<i class="fas fa-trash"></i> Eliminar';
				echo '</a>';
				echo '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table>';
			echo '</div>';
			echo '</div>';

			echo '<div class="pagination-container">';
			$paginacion->render();
			echo '</div>';
			?>
		</div>
	</div>

	<!-- Back Button -->
	<a href="../../access.php" class="back-button">
		<i class="fas fa-arrow-left me-2"></i>
		Regresar
	</a>

</body>
</html>