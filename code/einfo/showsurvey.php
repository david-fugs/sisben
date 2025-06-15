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

		/* Estilos modernos */
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

		/* Estilos para registros destacados */
		.registro-multiple {
			background-color: #e8f4f8 !important;
			border-left: 4px solid #17a2b8 !important;
		}

		.badge-documento {
			background-color: #17a2b8;
			color: white;
			padding: 6px 12px;
			border-radius: 15px;
			font-size: 0.85em;
			font-weight: bold;
		}

		.badge-tipo {
			background-color: #28a745;
			color: white;
			padding: 6px 12px;
			border-radius: 15px;
			font-size: 0.85em;
			font-weight: bold;
		}

		.table-hover tbody tr:hover {
			background-color: #f8f9fa;
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
		<div class="search-form">
			<h5 class="mb-3"><i class="fas fa-search me-2"></i>Buscar Información Registrada</h5>
			<form action="showsurvey.php" method="get">
				<div class="row g-3">
					<div class="col-md-4">
						<label class="form-label">Documento</label>
						<input name="doc_info" type="text" class="form-control" placeholder="Número de documento" value="<?php echo isset($_GET['doc_info']) ? $_GET['doc_info'] : ''; ?>">
					</div>
					<div class="col-md-5">
						<label class="form-label">Nombre</label>
						<input name="nom_info" type="text" class="form-control" placeholder="Nombre del usuario" value="<?php echo isset($_GET['nom_info']) ? $_GET['nom_info'] : ''; ?>">
					</div>
					<div class="col-md-3 d-flex align-items-end">
						<div class="w-100">
							<button type="submit" class="btn btn-custom w-100 mb-2">
								<i class="fas fa-search me-1"></i> Buscar
							</button>
							<a href="showsurvey.php" class="btn btn-outline-secondary w-100">
								<i class="fas fa-times me-1"></i> Limpiar
							</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

			<?php
			date_default_timezone_set("America/Bogota");
			include("../../conexion.php");
			require_once("../../zebra.php");			$where = [];

			// Filtro para mostrar solo registros de 2025 en adelante
			$where[] = "YEAR(informacion.fecha_reg_info) >= 2025";

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
			}			$result = $mysqli->query($query);
			$res = $mysqli->query($query);
			$num_registros = mysqli_num_rows($res);
			$resul_x_pagina = 25;

			// Configurar paginación
			$paginacion = new Zebra_Pagination();
			$paginacion->records($num_registros);
			$paginacion->records_per_page($resul_x_pagina);			// Obtener estadísticas
			$stats_query = "SELECT 
							COUNT(*) as total_registros,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Actualización' THEN 1 END) as actualizacion,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Atención' THEN 1 END) as atencion,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Calidad de la Encuesta' THEN 1 END) as calidad_encuesta,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Clasificación' THEN 1 END) as clasificacion,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Dirección' THEN 1 END) as direccion,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Documento' THEN 1 END) as documento,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Inclusión' THEN 1 END) as inclusion,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Pendiente' THEN 1 END) as pendiente,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Verificación' THEN 1 END) as verificacion,
							COUNT(CASE WHEN tipo_solic_encInfo = 'Visita' THEN 1 END) as visita
							FROM informacion";
			
			if (!empty($where)) {
				$stats_query .= " WHERE " . implode(" AND ", $where);
			}
			
			$stats_result = $mysqli->query($stats_query);
			$stats = $stats_result->fetch_assoc();
			?>

			<div class="container">
				<div class="row mb-4">
					<div class="col-md-12">						<div class="card-stats p-3">
							<h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Estadísticas por Tipo de Información</h6>
							<div class="row text-center mb-3">
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['actualizacion'] ?? 0; ?></strong><br>
									<small>Actualización</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['atencion'] ?? 0; ?></strong><br>
									<small>Atención</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['calidad_encuesta'] ?? 0; ?></strong><br>
									<small>Calidad Encuesta</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['clasificacion'] ?? 0; ?></strong><br>
									<small>Clasificación</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['direccion'] ?? 0; ?></strong><br>
									<small>Dirección</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['documento'] ?? 0; ?></strong><br>
									<small>Documento</small>
								</div>
							</div>
							<div class="row text-center">
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['inclusion'] ?? 0; ?></strong><br>
									<small>Inclusión</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['pendiente'] ?? 0; ?></strong><br>
									<small>Pendiente</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['verificacion'] ?? 0; ?></strong><br>
									<small>Verificación</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $stats['visita'] ?? 0; ?></strong><br>
									<small>Visita</small>
								</div>
								<div class="col-6 col-md-2">
									<strong><?php echo $num_registros; ?></strong><br>
									<small>Total</small>
								</div>
								<div class="col-6 col-md-2">
									<!-- Espacio para equilibrar la fila -->
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card shadow-sm">
					<div class="card-body">
						<?php
						// Información de paginación
						$registro_inicio_pagina = (($paginacion->get_page() - 1) * $resul_x_pagina) + 1;
						$registro_fin_pagina = min($paginacion->get_page() * $resul_x_pagina, $num_registros);
						?>
						<div class="info-pagination">
							<strong>Mostrando <?php echo $registro_inicio_pagina; ?> - <?php echo $registro_fin_pagina; ?> de <?php echo $num_registros; ?> registros</strong>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-hover align-middle text-center">
								<thead class="table-dark">
									<tr>
										<th>No.</th>
										<th>Fecha Registro</th>
										<th>Documento</th>
										<th>Nombre</th>
										<th>Tipo Doc.</th>
										<th>Observación</th>
										<th>Tipo Solicitud</th>
										<th>Encuestador</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>

			<?php
			// Consulta final con paginación
			$consulta_paginada = "SELECT informacion.*, usuarios.nombre 
								  FROM informacion 
								  JOIN usuarios ON informacion.id_usu = usuarios.id_usu";

			if (!empty($where)) {
				$consulta_paginada .= " WHERE " . implode(" AND ", $where);
			}

			$consulta_paginada .= " ORDER BY informacion.fecha_reg_info DESC 
								   LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;

			$result_paginado = $mysqli->query($consulta_paginada);
			
			// Calcular números de registro para mostrar
			$registro_inicio = (($paginacion->get_page() - 1) * $resul_x_pagina) + 1;
			$registro_actual = $registro_inicio;			while ($row = mysqli_fetch_array($result_paginado)) {
				// Añadir clase especial para registros múltiples si es necesario
				$claseEspecial = '';
				
				echo '
				<tr class="' . $claseEspecial . '">
					<td>' . $registro_actual++ . '</td>
					<td>' . date('d/m/Y H:i', strtotime($row['fecha_reg_info'])) . '</td>
					<td><span class="badge-documento">' . $row['doc_info'] . '</span></td>
					<td>' . $row['nom_info'] . '</td>
					<td><span class="badge-tipo">' . strtoupper($row['tipo_documento']) . '</span></td>
					<td>' . (strlen($row['observacion']) > 30 ? substr($row['observacion'], 0, 30) . '...' : $row['observacion']) . '</td>
					<td>' . $row['tipo_solic_encInfo'] . '</td>
					<td>' . $row['nombre'] . '</td>
					<td>
						<a href="showencInfo.php?id_informacion=' . $row['id_informacion'] . '" class="btn btn-sm btn-outline-primary me-1" title="Ver detalles">
							<i class="fas fa-eye"></i>
						</a>
						<a href="eliminarInformaciones.php?id_informacion=' . $row['id_informacion'] . '" 
						   onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE REGISTRO?\')" 
						   class="btn btn-sm btn-outline-danger" title="Eliminar">
							<i class="fas fa-trash"></i>
						</a>
					</td>
				</tr>';
			}

			echo '
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="mt-4">
					<?php $paginacion->render(); ?>
				</div>';			?>
			
			<div class="text-center mt-4">
				<a href="../../access.php" class="btn btn-secondary btn-lg">
					<i class="fas fa-arrow-left me-2"></i> Regresar al Menú Principal
				</a>
			</div>
		</div>	<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
	<script src="https://kit.fontawesome.com/fed2435e21.js"></script>

</body>

</html>