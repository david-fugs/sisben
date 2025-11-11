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
	<title>BD SISBEN - Movimientos Encuesta Campo</title>
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
			color: #fff;
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

		.btn-view {
			background: #17a2b8;
			color: white;
			border: none;
		}

		.btn-view:hover {
			background: #138496;
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

		.tipo-movimiento-badge {
			padding: 5px 10px;
			border-radius: 12px;
			font-size: 0.75rem;
			font-weight: 600;
		}

		.tipo-inclusion {
			background: #cfe2ff;
			color: #084298;
		}

		.tipo-inconformidad {
			background: #fff3cd;
			color: #664d03;
		}

		.tipo-modificacion {
			background: #d1e7dd;
			color: #0f5132;
		}

		.tipo-retiro-ficha {
			background: #f8d7da;
			color: #842029;
		}

		.tipo-retiro-personas {
			background: #e2e3e5;
			color: #41464b;
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
			width: 5%;
		}

		.medium-col {
			width: 12%;
		}

		.large-col {
			width: 18%;
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
							<i class="fas fa-exchange-alt me-3"></i>
							MOVIMIENTOS ENCUESTA CAMPO
						</h1>
						<p class="mb-0 mt-2" style="font-size: 1.1rem; opacity: 0.9;">
							Historial y control de movimientos de encuestas de campo
						</p>
					</div>
				</div>
			</div>

			<!-- Search Form -->
			<div class="search-card card">
				<div class="card-body">
					<h5 class="card-title">
						<i class="fas fa-search me-2"></i>
						Buscar Movimientos
					</h5>
					<form action="showMovimientosCampo.php" method="get" class="row g-3 align-items-center">
						<div class="col-md-3">
							<div class="form-floating">
								<input name="doc_encCampo" type="text" class="form-control" id="documento" placeholder="Documento" value="<?php echo isset($_GET['doc_encCampo']) ? htmlspecialchars($_GET['doc_encCampo']) : ''; ?>">
								<label for="documento">
									<i class="fas fa-id-card me-2"></i>Documento
								</label>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-floating">
								<input name="num_ficha_encCampo" type="text" class="form-control" id="ficha" placeholder="Número de ficha" value="<?php echo isset($_GET['num_ficha_encCampo']) ? htmlspecialchars($_GET['num_ficha_encCampo']) : ''; ?>">
								<label for="ficha">
									<i class="fas fa-file-alt me-2"></i>Número de Ficha
								</label>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-floating">
								<select name="tipo_movimiento" class="form-control" id="tipo_movimiento">
									<option value="">Todos los movimientos</option>
									<option value="inclusion" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'inclusion') ? 'selected' : ''; ?>>Inclusión</option>
									<option value="Inconformidad por clasificacion" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'Inconformidad por clasificacion') ? 'selected' : ''; ?>>Inconformidad</option>
									<option value="modificacion datos persona" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'modificacion datos persona') ? 'selected' : ''; ?>>Modificación</option>
									<option value="Retiro ficha" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'Retiro ficha') ? 'selected' : ''; ?>>Retiro Ficha</option>
									<option value="Retiro personas" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'Retiro personas') ? 'selected' : ''; ?>>Retiro Personas</option>
								</select>
								<label for="tipo_movimiento">
									<i class="fas fa-filter me-2"></i>Tipo Movimiento
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
				<a href="movimientosEncuestaCampo.php" class="btn btn-success me-3">
					<i class="fas fa-plus me-2"></i>
					Nuevo Movimiento
				</a>
				<button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#exportModal">
					<i class="fas fa-download me-2"></i>
					Exportar Movimientos
				</button>
				<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exportIntegrantesModal">
					<i class="fas fa-users me-2"></i>
					Exportar Integrantes
				</button>
			</div>

			<?php
			date_default_timezone_set("America/Bogota");
			include("../../conexion.php");
			require_once("../../zebra.php");

			mysqli_set_charset($mysqli, "utf8");

			@$doc_encCampo = $_GET['doc_encCampo'] ?? '';
			@$num_ficha_encCampo = $_GET['num_ficha_encCampo'] ?? '';
			@$tipo_movimiento = $_GET['tipo_movimiento'] ?? '';

			$query = "SELECT COUNT(*) as total FROM movimientos_encuesta_campo 
						WHERE (doc_encCampo LIKE '%$doc_encCampo%') 
						AND (num_ficha_encCampo LIKE '%$num_ficha_encCampo%')
						AND (tipo_movimiento LIKE '%$tipo_movimiento%')";

			if ($tipo_usu != '1') {
				$query .= " AND id_usu = $id_usu";
			}

			$res = $mysqli->query($query);
			
			if (!$res) {
				echo "<div class='alert alert-danger'>Error en la consulta: " . mysqli_error($mysqli) . "</div>";
				exit();
			}
			
			$row_count = $res->fetch_assoc();
			$num_registros = $row_count['total'];
			$resul_x_pagina = 50;

			if ($num_registros == 0) {
				echo "<div class='alert alert-info'>";
				echo "<i class='fas fa-info-circle'></i> <strong>No se encontraron movimientos con los filtros aplicados.</strong>";
				echo "<br>Documento: " . (empty($doc_encCampo) ? "<em>Todos</em>" : "<strong>$doc_encCampo</strong>");
				echo " | Ficha: " . (empty($num_ficha_encCampo) ? "<em>Todas</em>" : "<strong>$num_ficha_encCampo</strong>");
				echo " | Tipo: " . (empty($tipo_movimiento) ? "<em>Todos</em>" : "<strong>$tipo_movimiento</strong>");
				if ($tipo_usu != '1') {
					echo "<br><small>Nota: Solo se muestran los movimientos creados por ti (Usuario ID: $id_usu)</small>";
				}
				echo "</div>";
			} else {
				echo "<div class='alert alert-success'>";
				echo "<i class='fas fa-check-circle'></i> <strong>$num_registros</strong> movimiento(s) encontrado(s).";
				echo "</div>";
			}

			echo "<div class='table-container'>";
			echo "<div class='table-responsive'>";
			echo "<table class='table table-hover align-middle'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th class='small-col'><i class='fas fa-hashtag me-2'></i>No.</th>";
			echo "<th class='medium-col'><i class='fas fa-calendar me-2'></i>FECHA</th>";
			echo "<th class='medium-col'><i class='fas fa-id-card me-2'></i>DOCUMENTO</th>";
			echo "<th class='large-col'><i class='fas fa-user me-2'></i>NOMBRE</th>";
			echo "<th class='medium-col'><i class='fas fa-file-alt me-2'></i>FICHA</th>";
			echo "<th class='medium-col'><i class='fas fa-exchange-alt me-2'></i>TIPO MOVIMIENTO</th>";
			echo "<th class='medium-col'><i class='fas fa-info-circle me-2'></i>ESTADO</th>";
			echo "<th class='small-col'><i class='fas fa-eye me-2'></i>VER</th>";
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
						FROM movimientos_encuesta_campo 
						WHERE (doc_encCampo LIKE '%" . $doc_encCampo . "%') 
						AND (num_ficha_encCampo LIKE '%" . $num_ficha_encCampo . "%')
						AND (tipo_movimiento LIKE '%" . $tipo_movimiento . "%')";

			if ($tipo_usu != '1') {
				$consulta .= " AND id_usu = $id_usu";
			}

			$consulta .= " ORDER BY fecha_movimiento DESC 
						LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
			
			$result = $mysqli->query($consulta);
			
			if (!$result) {
				echo "<tr><td colspan='8' class='text-center text-danger'>Error en la consulta: " . mysqli_error($mysqli) . "</td></tr>";
			} else {
				$i = 1;
				$total_rows = mysqli_num_rows($result);
				
				if ($total_rows == 0) {
					echo "<tr><td colspan='8' class='text-center text-warning'>";
					echo "<i class='fas fa-exclamation-triangle'></i> No se encontraron movimientos registrados.";
					echo "</td></tr>";
				}
				
				while ($row = mysqli_fetch_array($result)) {
					$estadoClase = ($row['estado_ficha'] == '0') ? 'estado-retirada' : 'estado-activa';
					
					// Determinar clase CSS para tipo de movimiento
					$tipoClase = '';
					switch($row['tipo_movimiento']) {
						case 'inclusion':
							$tipoClase = 'tipo-inclusion';
							break;
						case 'Inconformidad por clasificacion':
							$tipoClase = 'tipo-inconformidad';
							break;
						case 'modificacion datos persona':
							$tipoClase = 'tipo-modificacion';
							break;
						case 'Retiro ficha':
							$tipoClase = 'tipo-retiro-ficha';
							break;
						case 'Retiro personas':
							$tipoClase = 'tipo-retiro-personas';
							break;
					}
				
				echo '<tr>';
				echo '<td><strong>' . $i++ . '</strong></td>';
				echo '<td>' . date('d/m/Y H:i', strtotime($row['fecha_movimiento'])) . '</td>';
				echo '<td><strong>' . $row['doc_encCampo'] . '</strong></td>';
				echo '<td>' . $row['nom_encCampo'] . '</td>';
				echo '<td><span class="badge bg-info">' . $row['num_ficha_encCampo'] . '</span></td>';
				echo '<td><span class="tipo-movimiento-badge ' . $tipoClase . '">' . ucfirst($row['tipo_movimiento']) . '</span></td>';
				echo '<td><span class="estado-badge ' . $estadoClase . '">' . $row['estado_ficha_texto'] . '</span></td>';
				echo '<td>';
				echo '<a href="verMovimientoCampo.php?id=' . $row['id_movimiento'] . '" class="btn-action btn-view" title="Ver Detalle">';
				echo '<i class="fas fa-eye"></i>';
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

	<!-- Modal para Exportar Movimientos -->
	<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white">
					<h5 class="modal-title" id="exportModalLabel">
						<i class="fas fa-download me-2"></i>
						Exportar Movimientos Encuesta Campo
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="exportForm" action="exportarMovimientosCampo.php" method="GET" target="_blank">
					<div class="modal-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="fecha_inicio" class="form-label">
									<i class="fas fa-calendar-alt me-2"></i>
									Fecha Inicio
								</label>
								<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
								<small class="text-muted">Dejar vacío para exportar todos</small>
							</div>
							<div class="col-md-6">
								<label for="fecha_fin" class="form-label">
									<i class="fas fa-calendar-alt me-2"></i>
									Fecha Fin
								</label>
								<input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
								<small class="text-muted">Dejar vacío para exportar todos</small>
							</div>
						</div>
						<div class="mb-3">
							<label for="tipo_movimiento_export" class="form-label">
								<i class="fas fa-filter me-2"></i>
								Tipo de Movimiento
							</label>
							<select class="form-control" id="tipo_movimiento_export" name="tipo_movimiento">
								<option value="">Todos los movimientos</option>
								<option value="inclusion">Inclusión</option>
								<option value="Inconformidad por clasificacion">Inconformidad</option>
								<option value="modificacion datos persona">Modificación</option>
								<option value="Retiro ficha">Retiro Ficha</option>
								<option value="Retiro personas">Retiro Personas</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="id_usu" class="form-label">
								<i class="fas fa-user me-2"></i>
								Usuario
							</label>
							<select class="form-control" id="id_usu" name="id_usu">
								<?php
								$sql_users = "SELECT id_usu, nombre FROM usuarios WHERE tipo_usu = 2 ORDER BY nombre";
								$result_users = mysqli_query($mysqli, $sql_users);
								
								$mostrar_todos = ($tipo_usu != 3);
								$solo_usuario_actual = ($tipo_usu == 3);
								
								if ($mostrar_todos) {
									echo '<option value="todos">Todos los usuarios</option>';
								}
								
								while ($user = mysqli_fetch_assoc($result_users)) {
									if ($solo_usuario_actual && $user['id_usu'] != $id_usu) {
										continue;
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

	<!-- Modal para Exportar Integrantes -->
	<div class="modal fade" id="exportIntegrantesModal" tabindex="-1" aria-labelledby="exportIntegrantesModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-info text-white">
					<h5 class="modal-title" id="exportIntegrantesModalLabel">
						<i class="fas fa-users me-2"></i>
						Exportar Integrantes de Movimientos
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="exportIntegrantesForm" action="exportarIntegrantesMovimientosCampo.php" method="GET" target="_blank">
					<div class="modal-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="fecha_inicio_int" class="form-label">
									<i class="fas fa-calendar-alt me-2"></i>
									Fecha Inicio
								</label>
								<input type="date" class="form-control" id="fecha_inicio_int" name="fecha_inicio">
								<small class="text-muted">Dejar vacío para exportar todos</small>
							</div>
							<div class="col-md-6">
								<label for="fecha_fin_int" class="form-label">
									<i class="fas fa-calendar-alt me-2"></i>
									Fecha Fin
								</label>
								<input type="date" class="form-control" id="fecha_fin_int" name="fecha_fin">
								<small class="text-muted">Dejar vacío para exportar todos</small>
							</div>
						</div>
						<div class="mb-3">
							<label for="id_usu_int" class="form-label">
								<i class="fas fa-user me-2"></i>
								Usuario
							</label>
							<select class="form-control" id="id_usu_int" name="id_usu">
								<?php
								$sql_users_int = "SELECT id_usu, nombre FROM usuarios WHERE tipo_usu = 2 ORDER BY nombre";
								$result_users_int = mysqli_query($mysqli, $sql_users_int);
								
								if ($mostrar_todos) {
									echo '<option value="todos">Todos los usuarios</option>';
								}
								
								while ($user = mysqli_fetch_assoc($result_users_int)) {
									if ($solo_usuario_actual && $user['id_usu'] != $id_usu) {
										continue;
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
						<button type="submit" class="btn btn-info">
							<i class="fas fa-users me-2"></i>
							Exportar Integrantes
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
		document.addEventListener('DOMContentLoaded', function() {
			const fechaFin = document.getElementById('fecha_fin');
			const fechaInicio = document.getElementById('fecha_inicio');
			
			const hoy = new Date();
			const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
			
			fechaFin.value = hoy.toISOString().split('T')[0];
			fechaInicio.value = primerDiaMes.toISOString().split('T')[0];
		});
	</script>

</body>
</html>
