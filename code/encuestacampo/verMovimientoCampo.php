<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
	header("Location: ../../index.php");
	exit();
}

$id_usu = $_SESSION['id_usu'];
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usu'];

include("../../conexion.php");
mysqli_set_charset($mysqli, "utf8");

$id_movimiento = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_movimiento == 0) {
	header("Location: showMovimientosCampo.php");
	exit();
}

// Consultar el movimiento
$sql = "SELECT m.*, 
        b.nombre_bar,
        c.nombre_com,
        u.nombre as nombre_usuario
        FROM movimientos_encuesta_campo m
        LEFT JOIN barrios b ON m.id_bar = b.id_bar
        LEFT JOIN comunas c ON m.id_com = c.id_com
        LEFT JOIN usuarios u ON m.id_usu = u.id_usu
        WHERE m.id_movimiento = $id_movimiento";

$result = mysqli_query($mysqli, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
	echo "<script>alert('Movimiento no encontrado'); window.location='showMovimientosCampo.php';</script>";
	exit();
}

$mov = mysqli_fetch_assoc($result);

// Consultar integrantes
$sql_integ = "SELECT * FROM integ_movimientos_encuesta_campo WHERE id_movimiento = $id_movimiento ORDER BY fecha_registro ASC";
$result_integ = mysqli_query($mysqli, $sql_integ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>BD SISBEN - Detalle Movimiento</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://kit.fontawesome.com/fed2435e21.js"></script>
	<style>
		body {
			background: #f8f9fa;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}
		.container-main {
			background: white;
			border-radius: 15px;
			box-shadow: 0 4px 20px rgba(0,0,0,0.1);
			padding: 2rem;
			margin: 2rem auto;
			max-width: 1200px;
		}
		.header {
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			color: white;
			padding: 1.5rem;
			border-radius: 10px;
			margin-bottom: 2rem;
		}
		.info-card {
			background: #f8f9fa;
			border-left: 4px solid #007bff;
			padding: 1.5rem;
			margin-bottom: 1.5rem;
			border-radius: 8px;
		}
		.info-row {
			display: flex;
			padding: 0.75rem 0;
			border-bottom: 1px solid #e9ecef;
		}
		.info-row:last-child {
			border-bottom: none;
		}
		.info-label {
			font-weight: 600;
			color: #495057;
			width: 200px;
			flex-shrink: 0;
		}
		.info-value {
			color: #212529;
		}
		.badge-custom {
			padding: 0.5rem 1rem;
			border-radius: 20px;
			font-size: 0.9rem;
		}
		.integrante-card {
			background: white;
			border: 2px solid #e9ecef;
			border-radius: 10px;
			padding: 1rem;
			margin-bottom: 1rem;
		}
		.integrante-header {
			background: #007bff;
			color: white;
			padding: 0.5rem 1rem;
			border-radius: 8px;
			margin-bottom: 1rem;
			font-weight: 600;
		}
	</style>
</head>
<body>
	<div class="container-main">
		<div class="header">
			<h3><i class="fas fa-file-alt me-2"></i>Detalle de Movimiento #<?php echo $id_movimiento; ?></h3>
			<p class="mb-0">Sistema de Control de Movimientos - Encuesta Campo</p>
		</div>

		<!-- Información del Movimiento -->
		<div class="info-card">
			<h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información del Movimiento</h5>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-calendar me-2"></i>Fecha Movimiento:</span>
				<span class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($mov['fecha_movimiento'])); ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-exchange-alt me-2"></i>Tipo:</span>
				<span class="info-value"><strong><?php echo ucfirst($mov['tipo_movimiento']); ?></strong></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-flag me-2"></i>Estado Ficha:</span>
				<span class="info-value">
					<?php if ($mov['estado_ficha'] == 1): ?>
						<span class="badge bg-success badge-custom">ACTIVA</span>
					<?php else: ?>
						<span class="badge bg-danger badge-custom">RETIRADA</span>
					<?php endif; ?>
				</span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-user me-2"></i>Registrado por:</span>
				<span class="info-value"><?php echo $mov['nombre_usuario']; ?></span>
			</div>
		</div>

		<!-- Datos del Titular -->
		<div class="info-card">
			<h5 class="mb-3"><i class="fas fa-user-circle me-2"></i>Datos del Titular</h5>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-id-card me-2"></i>Documento:</span>
				<span class="info-value"><strong><?php echo $mov['doc_encCampo']; ?></strong></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-user me-2"></i>Nombre:</span>
				<span class="info-value"><?php echo $mov['nom_encCampo']; ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-file me-2"></i>Tipo Documento:</span>
				<span class="info-value"><?php echo strtoupper($mov['tipo_documento']); ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-birthday-cake me-2"></i>Fecha Nacimiento:</span>
				<span class="info-value"><?php echo $mov['fecha_nacimiento'] ? date('d/m/Y', strtotime($mov['fecha_nacimiento'])) : 'No registrada'; ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-calendar-check me-2"></i>Fecha Expedición:</span>
				<span class="info-value"><?php echo date('d/m/Y', strtotime($mov['fecha_expedicion'])); ?></span>
			</div>
		</div>

		<!-- Información de Ubicación -->
		<div class="info-card">
			<h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Información de Ubicación</h5>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-home me-2"></i>Dirección:</span>
				<span class="info-value"><?php echo $mov['dir_encCampo']; ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-map me-2"></i>Barrio:</span>
				<span class="info-value"><?php echo $mov['nombre_bar'] ?? 'No especificado'; ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-building me-2"></i>Comuna:</span>
				<span class="info-value"><?php echo $mov['nombre_com'] ?? 'No especificada'; ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-location-arrow me-2"></i>Zona:</span>
				<span class="info-value"><?php echo $mov['zona_encCampo']; ?></span>
			</div>
			<div class="info-row">
				<span class="info-label"><i class="fas fa-file-alt me-2"></i>No. Ficha:</span>
				<span class="info-value"><strong><?php echo $mov['num_ficha_encCampo']; ?></strong></span>
			</div>
		</div>

		<!-- Integrantes -->
		<?php if ($result_integ && mysqli_num_rows($result_integ) > 0): ?>
		<div class="info-card">
			<h5 class="mb-3"><i class="fas fa-users me-2"></i>Integrantes (<?php echo mysqli_num_rows($result_integ); ?>)</h5>
			<?php 
			$i = 1;
			while ($integ = mysqli_fetch_assoc($result_integ)): 
			?>
			<div class="integrante-card">
				<div class="integrante-header">
					Integrante #<?php echo $i++; ?>
				</div>
				<div class="row">
					<div class="col-md-4 mb-2">
						<small class="text-muted">Género:</small><br>
						<strong><?php echo $integ['gen_integCampo']; ?></strong>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Rango Edad:</small><br>
						<strong><?php echo $integ['rango_integCampo']; ?></strong>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Orientación Sexual:</small><br>
						<?php echo $integ['orientacionSexual'] ?? 'No especificado'; ?>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Discapacidad:</small><br>
						<?php echo $integ['condicionDiscapacidad'] ?? 'No'; ?>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Tipo Discapacidad:</small><br>
						<?php echo $integ['tipoDiscapacidad'] ?? 'N/A'; ?>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Grupo Étnico:</small><br>
						<?php echo $integ['grupoEtnico'] ?? 'No especificado'; ?>
					</div>
					<div class="col-md-3 mb-2">
						<small class="text-muted">Víctima:</small><br>
						<?php echo $integ['victima'] ?? 'No'; ?>
					</div>
					<div class="col-md-3 mb-2">
						<small class="text-muted">Gestante:</small><br>
						<?php echo $integ['mujerGestante'] ?? 'No'; ?>
					</div>
					<div class="col-md-3 mb-2">
						<small class="text-muted">Cabeza Familia:</small><br>
						<?php echo $integ['cabezaFamilia'] ?? 'No'; ?>
					</div>
					<div class="col-md-3 mb-2">
						<small class="text-muted">Exp. Migratoria:</small><br>
						<?php echo $integ['experienciaMigratoria'] ?? 'No'; ?>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Seguridad Salud:</small><br>
						<?php echo $integ['seguridadSalud'] ?? 'No especificado'; ?>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Nivel Educativo:</small><br>
						<?php echo $integ['nivelEducativo'] ?? 'No especificado'; ?>
					</div>
					<div class="col-md-4 mb-2">
						<small class="text-muted">Ocupación:</small><br>
						<?php echo $integ['condicionOcupacion'] ?? 'No especificado'; ?>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
		</div>
		<?php else: ?>
		<div class="alert alert-info">
			<i class="fas fa-info-circle me-2"></i>
			No hay integrantes registrados para este movimiento.
		</div>
		<?php endif; ?>

		<!-- Observaciones -->
		<?php if (!empty($mov['obs_encCampo'])): ?>
		<div class="info-card">
			<h5 class="mb-3"><i class="fas fa-comment me-2"></i>Observaciones</h5>
			<p><?php echo nl2br(htmlspecialchars($mov['obs_encCampo'])); ?></p>
		</div>
		<?php endif; ?>

		<!-- Botones -->
		<div class="text-center mt-4">
			<a href="showMovimientosCampo.php" class="btn btn-primary btn-lg">
				<i class="fas fa-arrow-left me-2"></i>Volver al Listado
			</a>
			<a href="movimientosEncuestaCampo.php" class="btn btn-success btn-lg">
				<i class="fas fa-plus me-2"></i>Nuevo Movimiento
			</a>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
