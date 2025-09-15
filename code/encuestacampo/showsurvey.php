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
	<title>BD SISBEN - Encuestas de Campo</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
	<style>
		body {
			background: #f8f9fa;
			min-height: 100vh;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.main-container {
			background: rgba(255, 255, 255, 1);
			border-radius: 10px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			margin: 20px auto;
			padding: 30px;
			border: 1px solid #e9ecef;
		}

		.header-section {
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			border-radius: 10px;
			padding: 25px;
			margin-bottom: 30px;
			color: #fff; /* texto blanco para buena legibilidad sobre el azul */
			text-align: center;
		}

		.search-card {
			background: white;
			border: 1px solid #dee2e6;
			border-radius: 10px;
			margin-bottom: 30px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		}

		.btn-search {
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			color: #fff;
			border: none;
			border-radius: 8px;
			padding: 12px 20px;
			font-weight: 600;
			transition: all 0.3s ease;
		}

		.btn-search:hover {
			transform: translateY(-1px);
			filter: brightness(0.9);
		}

		.table-container {
			background: white;
			border-radius: 10px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			overflow: hidden;
		}

		.table {
			margin-bottom: 0;
		}

		.table thead th {
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			color: #fff;
			border: none;
			padding: 15px 10px;
			font-weight: 600;
			text-transform: uppercase;
			font-size: 0.85rem;
		}

		.table tbody tr {
			transition: all 0.2s ease;
		}

			.table tbody tr:hover {
				background-color: rgba(0, 123, 255, 0.06);
			}

		.table tbody td {
			padding: 12px 10px;
			vertical-align: middle;
			border-bottom: 1px solid rgba(0, 0, 0, 0.1);
		}

		.btn-action {
			border-radius: 6px;
			padding: 6px 12px;
			font-weight: 600;
			text-decoration: none;
			transition: all 0.2s ease;
			display: inline-flex;
			align-items: center;
			gap: 5px;
			font-size: 0.85rem;
		}

		.btn-edit {
			background: #28a745;
			color: white;
			border: none;
		}

		.btn-edit:hover {
			background: #218838;
			color: white;
		}

		.btn-delete {
			background: #dc3545;
			color: white;
			border: none;
		}

		.btn-delete:hover {
			background: #c82333;
			color: white;
		}

		.estado-badge {
			padding: 6px 12px;
			border-radius: 15px;
			font-weight: 600;
			font-size: 0.75rem;
			text-transform: uppercase;
		}

		.estado-activa {
			background: #d4edda;
			color: #155724;
		}

		.estado-retirada {
			background: #f8d7da;
			color: #721c24;
		}

		.back-button {
			position: fixed;
			bottom: 30px;
			left: 50%;
			transform: translateX(-50%);
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			border: none;
			border-radius: 25px;
			padding: 12px 20px;
			color: #fff;
			text-decoration: none;
			font-weight: 600;
			transition: all 0.3s ease;
			box-shadow: 0 4px 12px rgba(0, 91, 187, 0.35);
		}

		.back-button:hover {
			transform: translateX(-50%) translateY(-3px);
			color: #fff;
			filter: brightness(0.9);
		}

		/* Override Bootstrap primary buttons used on the page (Export) */
		.btn-primary{
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
			border-color: transparent !important;
			color: #fff !important;
		}

		.btn-primary:hover{
			filter: brightness(0.9);
			color: #fff !important;
		}

		.responsive {
			max-width: 100%;
			height: auto;
		}

		.pagination-container {
			margin-top: 30px;
			text-align: center;
		}

		.small-col {
			width: 8%;
		}

		.medium-col {
			width: 15%;
		}

		.large-col {
			width: 20%;
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
						<img src='../../img/sisben.png' class="img-fluid responsive" alt="Logo Sisben" style="max-width: 180px;">
					</div>
					<div class="col-md-9">
						<h1>
							<i class="fas fa-clipboard-list me-3"></i>
							ENCUESTAS DE CAMPO
						</h1>
						<p class="mb-0 mt-2" style="font-size: 1.1rem; opacity: 0.9;">
							Gestión y seguimiento de encuestas de campo del sistema SISBEN
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
						<div class="col-md-3">
							<div class="form-floating">
								<input name="doc_encVenta" type="text" class="form-control" id="documento" placeholder="Documento" value="<?php echo isset($_GET['doc_encVenta']) ? htmlspecialchars($_GET['doc_encVenta']) : ''; ?>">
								<label for="documento">
									<i class="fas fa-id-card me-2"></i>Documento
								</label>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-floating">
								<input name="num_ficha_encVenta" type="text" class="form-control" id="ficha" placeholder="Número de ficha" value="<?php echo isset($_GET['num_ficha_encVenta']) ? htmlspecialchars($_GET['num_ficha_encVenta']) : ''; ?>">
								<label for="ficha">
									<i class="fas fa-file-alt me-2"></i>Número de Ficha
								</label>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-floating">
								<input name="nom_encVenta" type="text" class="form-control" id="nombre" placeholder="Nombre" value="<?php echo isset($_GET['nom_encVenta']) ? htmlspecialchars($_GET['nom_encVenta']) : ''; ?>">
								<label for="nombre">
									<i class="fas fa-user me-2"></i>Nombre
								</label>
							</div>
						</div>
						<div class="col-md-3">
							<button type="submit" class="btn btn-search w-100">
								<i class="fas fa-search me-2"></i>
								Buscar
							</button>
						</div>
					</form>
				</div>
			</div>

			<!-- Botones de acción -->
			<div class="mb-4 text-center">
				<a href="encuesta_campo.php" class="btn btn-success me-3">
					<i class="fas fa-plus me-2"></i>
					Nueva Encuesta de Campo
				</a>
				<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
					<i class="fas fa-download me-2"></i>
					Exportar a Excel
				</button>
			</div>

			<?php
			date_default_timezone_set("America/Bogota");
			include("../../conexion.php");
			require_once("../../zebra.php");

			// Establecer charset UTF-8 para manejar tildes y ñ
			mysqli_set_charset($mysqli, "utf8");

			@$doc_encVenta = ($_GET['doc_encVenta']);
			@$num_ficha_encVenta = ($_GET['num_ficha_encVenta']);
			@$nom_encVenta = ($_GET['nom_encVenta']);

			$query = "SELECT COUNT(*) as total FROM encuestacampo 
						WHERE (encuestacampo.doc_encVenta LIKE '%$doc_encVenta%') 
						AND (encuestacampo.num_ficha_encVenta LIKE '%$num_ficha_encVenta%')
						AND (encuestacampo.nom_encVenta LIKE '%$nom_encVenta%')";

			if ($tipo_usu != '1') {
				$query .= " AND encuestacampo.id_usu = $id_usu";
			}

			$query .= " ORDER BY encuestacampo.fecha_alta_encVenta DESC";

			$res = $mysqli->query($query);
			
			// Validar que la consulta fue exitosa
			if (!$res) {
				echo "<div class='alert alert-danger'>Error en la consulta: " . mysqli_error($mysqli) . "</div>";
				exit();
			}
			
			// Obtener el número de registros desde la consulta COUNT
			$row_count = $res->fetch_assoc();
			$num_registros = $row_count['total'];
			$resul_x_pagina = 50;

			echo "<div class='table-container'>";
			echo "<div class='table-responsive'>";
			echo "<table class='table table-hover align-middle'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th class='small-col'><i class='fas fa-hashtag me-2'></i>No.</th>";
			echo "<th class='medium-col'><i class='fas fa-calendar me-2'></i>F. REG.</th>";
			echo "<th class='medium-col'><i class='fas fa-id-card me-2'></i>DOCUMENTO</th>";
			echo "<th class='large-col'><i class='fas fa-user me-2'></i>NOMBRE</th>";
			echo "<th class='medium-col'><i class='fas fa-file-alt me-2'></i>FICHA</th>";
			echo "<th class='medium-col'><i class='fas fa-info-circle me-2'></i>ESTADO</th>";
			echo "<th class='small-col'><i class='fas fa-edit me-2'></i>EDITAR</th>";
			echo "<th class='small-col'><i class='fas fa-trash me-2'></i>ELIMINAR</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			$paginacion = new Zebra_Pagination();
			$paginacion->records($num_registros);
			$paginacion->records_per_page($resul_x_pagina);

			$consulta = "SELECT *, 
						CASE 
							WHEN estado_ficha = '0' THEN 'FICHA RETIRADA'
							WHEN estado_ficha = '1' THEN 'ACTIVA'
							ELSE estado_ficha
						END as estado_ficha_texto
						FROM encuestacampo 
						WHERE (doc_encVenta LIKE '%" . $doc_encVenta . "%') 
						AND (num_ficha_encVenta LIKE '%" . $num_ficha_encVenta . "%')
						AND (nom_encVenta LIKE '%" . $nom_encVenta . "%')";

			if ($tipo_usu != '1') {
				$consulta .= " AND id_usu = $id_usu";
			}

			$consulta .= " ORDER BY fecha_alta_encVenta DESC 
						LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
			
			$result = $mysqli->query($consulta);
			
			// Validar que la consulta fue exitosa
			if (!$result) {
				echo "<tr><td colspan='8' class='text-center text-danger'>Error en la consulta: " . mysqli_error($mysqli) . "</td></tr>";
			} else {
				$i = 1;
				
				while ($row = mysqli_fetch_array($result)) {
					$estadoClase = ($row['estado_ficha'] == '0') ? 'estado-retirada' : 'estado-activa';
				
				echo '<tr>';
				echo '<td><strong>' . $i++ . '</strong></td>';
				echo '<td>' . date('d/m/Y', strtotime($row['fecha_alta_encVenta'])) . '</td>';
				echo '<td><strong>' . $row['doc_encVenta'] . '</strong></td>';
				echo '<td>' . $row['nom_encVenta'] . '</td>';
				echo '<td><span class="badge bg-info">' . $row['num_ficha_encVenta'] . '</span></td>';
				echo '<td><span class="estado-badge ' . $estadoClase . '">' . $row['estado_ficha_texto'] . '</span></td>';
				echo '<td>';
				echo '<a href="editEncuesta.php?id=' . $row['id_encCampo'] . '" class="btn-action btn-edit" title="Editar Encuesta">';
				echo '<i class="fas fa-edit"></i>';
				echo '</a>';
				echo '</td>';
				echo '<td>';
				echo '<a href="deleteEncuesta.php?id=' . $row['id_encCampo'] . '" class="btn-action btn-delete" onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?\')" title="Eliminar Registro">';
				echo '<i class="fas fa-trash"></i>';
				echo '</a>';
				echo '</td>';
				echo '</tr>';
				}
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

	<!-- Modal para Exportar -->
	<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white">
					<h5 class="modal-title" id="exportModalLabel">
						<i class="fas fa-download me-2"></i>
						Exportar Encuestas de Campo
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="exportForm" action="exportarEncuestaCampo.php" method="GET" target="_blank">
					<div class="modal-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="fecha_inicio" class="form-label">
									<i class="fas fa-calendar-alt me-2"></i>
									Fecha Inicio
								</label>
								<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
								<small class="text-muted">Dejar vacío para exportar todos los registros</small>
							</div>
							<div class="col-md-6">
								<label for="fecha_fin" class="form-label">
									<i class="fas fa-calendar-alt me-2"></i>
									Fecha Fin
								</label>
								<input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
								<small class="text-muted">Dejar vacío para exportar todos los registros</small>
							</div>
						</div>
						<div class="mb-3">
							<label for="id_usu" class="form-label">
								<i class="fas fa-user me-2"></i>
								Usuario
							</label>
							<select class="form-control" id="id_usu" name="id_usu">
								<?php
								// Obtener usuarios de la tabla usuarios
								$sql_users = "SELECT id_usu, nombre, tipo_usu FROM usuarios ORDER BY nombre";
								$result_users = mysqli_query($mysqli, $sql_users);
								
								// Determinar qué opciones mostrar según el tipo de usuario
								$mostrar_todos = ($tipo_usu != 3); // Solo mostrar "Todos" si NO es tipo_usu 3
								$solo_usuario_actual = ($tipo_usu == 3); // Si es tipo_usu 3, solo mostrar el usuario actual
								
								if ($mostrar_todos) {
									echo '<option value="todos">Todos los usuarios</option>';
								}
								
								while ($user = mysqli_fetch_assoc($result_users)) {
									if ($solo_usuario_actual && $user['id_usu'] != $id_usu) {
										continue; // Skip otros usuarios si es tipo_usu 3
									}
									$selected = ($user['id_usu'] == $id_usu) ? 'selected' : '';
									echo '<option value="' . $user['id_usu'] . '" ' . $selected . '>' . $user['nombre'] . '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
							<i class="fas fa-times me-2"></i>
							Cancelar
						</button>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-download me-2"></i>
							Exportar
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Back Button -->
	<a href="../../access.php" class="back-button">
		<i class="fas fa-arrow-left me-2"></i>
		Regresar
	</a>

	<script>
		// Establecer fecha por defecto que incluya los registros actuales
		document.addEventListener('DOMContentLoaded', function() {
			const fechaFin = document.getElementById('fecha_fin');
			const fechaInicio = document.getElementById('fecha_inicio');
			
			// Establecer rango del mes actual para incluir los registros
			const hoy = new Date();
			const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
			
			fechaFin.value = hoy.toISOString().split('T')[0];
			fechaInicio.value = primerDiaMes.toISOString().split('T')[0];
		});
	</script>

</body>
</html>
