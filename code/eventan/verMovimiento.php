<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu     = $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");

// Verificar que se proporcione el ID del movimiento
if (!isset($_GET['id_movimiento']) || empty($_GET['id_movimiento'])) {
    header("Location: showMovimientos.php");
    exit();
}

$id_movimiento = $_GET['id_movimiento'];

// Conectar a la base de datos y obtener datos del movimiento
include("../../conexion.php");
date_default_timezone_set("America/Bogota");

// Verificar que el movimiento existe y que el usuario tiene permisos
$sql_movimiento = "SELECT m.*, u.nombre AS nombre_usuario, 
                   CASE 
                       WHEN m.estado_ficha = 0 THEN 'FICHA RETIRADA'
                       ELSE 'ACTIVA'
                   END as estado_ficha_texto
                   FROM movimientos m 
                   LEFT JOIN usuarios u ON m.id_usu = u.id_usu
                   WHERE m.id_movimiento = '$id_movimiento'";

// Si no es administrador, solo puede ver sus propios movimientos
if ($tipo_usu != '1') {
    $sql_movimiento .= " AND m.id_usu = '$id_usu'";
}

$resultado_movimiento = mysqli_query($mysqli, $sql_movimiento);

if (!$resultado_movimiento || mysqli_num_rows($resultado_movimiento) == 0) {
    echo "<script>alert('Movimiento no encontrado o sin permisos'); window.location.href='showMovimientos.php';</script>";
    exit();
}

$movimiento = mysqli_fetch_assoc($resultado_movimiento);

// Obtener integrantes si existen
$sql_integrantes = "SELECT * FROM integmovimientos_independiente 
                    WHERE doc_encVenta = '{$movimiento['doc_encVenta']}' 
                    AND estado_integVenta = 1
                    ORDER BY fecha_alta_integVenta DESC";
$resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);
$integrantes = [];
if ($resultado_integrantes) {
    while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
        $integrantes[] = $integrante;
    }
}

// Obtener historial de movimientos para este documento
$sql_historial = "SELECT m.*, u.nombre AS nombre_usuario
                  FROM movimientos m 
                  LEFT JOIN usuarios u ON m.id_usu = u.id_usu
                  WHERE m.doc_encVenta = '{$movimiento['doc_encVenta']}'
                  ORDER BY m.fecha_movimiento DESC";
$resultado_historial = mysqli_query($mysqli, $sql_historial);
$historial = [];
if ($resultado_historial) {
    while ($hist = mysqli_fetch_assoc($resultado_historial)) {
        $historial[] = $hist;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BD SISBEN - Ver Movimiento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

        .detail-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
        }

        .integrante-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .badge-movimiento {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-inclusion { background-color: #28a745; color: white; }
        .badge-inconformidad { background-color: #fd7e14; color: white; }
        .badge-modificacion { background-color: #17a2b8; color: white; }
        .badge-retiro-ficha { background-color: #dc3545; color: white; }
        .badge-retiro-personas { background-color: #6f42c1; color: white; }
        .badge-default { background-color: #6c757d; color: white; }

        .estado-activa { color: #28a745; font-weight: bold; }
        .estado-retirada { color: #dc3545; font-weight: bold; }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            height: 100%;
            width: 2px;
            background: #667eea;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
        }

        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 10px 25px;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <div class="text-center mb-4">
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </div>

        <!-- Información Principal del Movimiento -->
        <div class="detail-card">
            <div class="row">
                <div class="col-md-6">
                    <h3><i class="fas fa-file-alt me-2"></i>Detalles del Movimiento</h3>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <p><strong>ID Movimiento:</strong> <?php echo $movimiento['id_movimiento']; ?></p>
                    <p><strong>Documento:</strong> <?php echo $movimiento['doc_encVenta']; ?></p>
                    <p><strong>Nombre Completo:</strong> <?php echo $movimiento['nom_encVenta']; ?></p>
                    <p><strong>Tipo de Documento:</strong> <?php echo strtoupper($movimiento['tipo_documento']); ?></p>
                </div>
                <div class="col-md-6">
                    <h3><i class="fas fa-info-circle me-2"></i>Estado y Fechas</h3>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <p><strong>Fecha Movimiento:</strong> <?php echo date('d/m/Y H:i:s', strtotime($movimiento['fecha_movimiento'])); ?></p>
                    <p><strong>Fecha Registro:</strong> <?php echo date('d/m/Y', strtotime($movimiento['fec_reg_encVenta'])); ?></p>
                    <p><strong>Estado Ficha:</strong> <span class="badge bg-<?php echo ($movimiento['estado_ficha'] == 0) ? 'danger' : 'success'; ?>"><?php echo $movimiento['estado_ficha_texto']; ?></span></p>
                    <p><strong>Usuario Responsable:</strong> <?php echo $movimiento['nombre_usuario']; ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Información Personal -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="fas fa-user me-2"></i>Información Personal</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Fecha Expedición:</strong></td>
                            <td><?php echo date('d/m/Y', strtotime($movimiento['fecha_expedicion'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Departamento Expedición:</strong></td>
                            <td><?php echo $movimiento['departamento_expedicion']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ciudad Expedición:</strong></td>
                            <td><?php echo $movimiento['ciudad_expedicion']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Dirección:</strong></td>
                            <td><?php echo $movimiento['dir_encVenta']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Zona:</strong></td>
                            <td><?php echo $movimiento['zona_encVenta']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Barrio/Vereda ID:</strong></td>
                            <td><?php echo $movimiento['id_bar']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Comuna ID:</strong></td>
                            <td><?php echo $movimiento['id_com']; ?></td>
                        </tr>
                        <?php if (!empty($movimiento['otro_bar_ver_encVenta'])): ?>
                        <tr>
                            <td><strong>Otro Barrio:</strong></td>
                            <td><?php echo $movimiento['otro_bar_ver_encVenta']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Información del Movimiento -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="fas fa-clipboard-list me-2"></i>Información del Proceso</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Tipo de Movimiento:</strong></td>
                            <td>
                                <?php
                                $badge_class = 'badge-default';
                                switch (strtolower($movimiento['tipo_movimiento'])) {
                                    case 'inclusion': $badge_class = 'badge-inclusion'; break;
                                    case 'inconformidad por clasificacion': $badge_class = 'badge-inconformidad'; break;
                                    case 'modificacion datos persona': $badge_class = 'badge-modificacion'; break;
                                    case 'retiro ficha': $badge_class = 'badge-retiro-ficha'; break;
                                    case 'retiro personas': $badge_class = 'badge-retiro-personas'; break;
                                }
                                ?>
                                <span class="badge-movimiento <?php echo $badge_class; ?>"><?php echo $movimiento['tipo_movimiento']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>No. Ficha/Radicado:</strong></td>
                            <td><?php echo $movimiento['num_ficha_encVenta']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Sisben Nocturno:</strong></td>
                            <td><span class="badge bg-<?php echo ($movimiento['sisben_nocturno'] == 'SI') ? 'success' : 'secondary'; ?>"><?php echo $movimiento['sisben_nocturno']; ?></span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Integrantes:</strong></td>
                            <td><span class="badge bg-info"><?php echo count($integrantes); ?></span></td>
                        </tr>
                        <?php if (!empty($movimiento['observacion'])): ?>
                        <tr>
                            <td><strong>Observaciones:</strong></td>
                            <td><?php echo $movimiento['observacion']; ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($movimiento['fecha_edit_movimiento']) && $movimiento['fecha_edit_movimiento'] != '0000-00-00 00:00:00'): ?>
                        <tr>
                            <td><strong>Última Edición:</strong></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($movimiento['fecha_edit_movimiento'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Integrantes -->
        <?php if (!empty($integrantes)): ?>
        <div class="info-card">
            <h5><i class="fas fa-users me-2"></i>Integrantes Registrados (<?php echo count($integrantes); ?>)</h5>
            <div class="row">
                <?php foreach ($integrantes as $index => $integrante): ?>
                <div class="col-md-6 mb-3">
                    <div class="integrante-card">
                        <h6><i class="fas fa-user me-2"></i>Integrante #<?php echo $index + 1; ?></h6>
                        <div class="row">
                            <div class="col-6">
                                <small><strong>Género:</strong> <?php echo $integrante['gen_integVenta']; ?></small><br>
                                <small><strong>Edad:</strong> <?php echo $integrante['rango_integVenta']; ?></small><br>
                                <small><strong>Orientación:</strong> <?php echo $integrante['orientacionSexual']; ?></small><br>
                                <small><strong>Discapacidad:</strong> <?php echo $integrante['condicionDiscapacidad']; ?></small><br>
                                <?php if ($integrante['condicionDiscapacidad'] == 'Si' && !empty($integrante['tipoDiscapacidad'])): ?>
                                <small><strong>Tipo:</strong> <?php echo $integrante['tipoDiscapacidad']; ?></small><br>
                                <?php endif; ?>
                            </div>
                            <div class="col-6">
                                <small><strong>Grupo Étnico:</strong> <?php echo $integrante['grupoEtnico']; ?></small><br>
                                <small><strong>Víctima:</strong> <?php echo $integrante['victima']; ?></small><br>
                                <small><strong>Gestante:</strong> <?php echo $integrante['mujerGestante'] ?? 'No'; ?></small><br>
                                <small><strong>Cabeza Familia:</strong> <?php echo $integrante['cabezaFamilia'] ?? 'No'; ?></small><br>
                                <small><strong>Educación:</strong> <?php echo $integrante['nivelEducativo']; ?></small><br>
                            </div>
                        </div>
                        <?php if (!empty($integrante['seguridadSalud']) || !empty($integrante['condicionOcupacion'])): ?>
                        <hr>
                        <small><strong>Seguridad Social:</strong> <?php echo $integrante['seguridadSalud']; ?></small><br>
                        <small><strong>Ocupación:</strong> <?php echo $integrante['condicionOcupacion']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Historial de Movimientos -->
        <div class="info-card">
            <h5><i class="fas fa-history me-2"></i>Historial de Movimientos para este Documento</h5>
            <div class="timeline">
                <?php foreach ($historial as $hist): ?>
                <div class="timeline-item <?php echo ($hist['id_movimiento'] == $id_movimiento) ? 'border-primary bg-light' : ''; ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <?php if ($hist['id_movimiento'] == $id_movimiento): ?>
                                <i class="fas fa-arrow-right text-primary me-1"></i>
                                <?php endif; ?>
                                <?php echo $hist['tipo_movimiento']; ?>
                                <?php if ($hist['id_movimiento'] == $id_movimiento): ?>
                                <small class="text-primary">(Actual)</small>
                                <?php endif; ?>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i><?php echo date('d/m/Y H:i', strtotime($hist['fecha_movimiento'])); ?>
                                <i class="fas fa-user ms-2 me-1"></i><?php echo $hist['nombre_usuario']; ?>
                                <i class="fas fa-file-alt ms-2 me-1"></i>Ficha: <?php echo $hist['num_ficha_encVenta']; ?>
                            </small>
                            <?php if (!empty($hist['observacion'])): ?>
                            <p class="mb-0 mt-1"><small><strong>Obs:</strong> <?php echo $hist['observacion']; ?></small></p>
                            <?php endif; ?>
                        </div>
                        <span class="badge bg-<?php echo ($hist['estado_ficha'] == 0) ? 'danger' : 'success'; ?>">
                            <?php echo ($hist['estado_ficha'] == 0) ? 'RETIRADA' : 'ACTIVA'; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="text-center mt-4">
            <a href="editMovimiento.php?id_movimiento=<?php echo $id_movimiento; ?>" class="btn btn-custom">
                <i class="fas fa-edit me-2"></i>Editar Movimiento
            </a>
            <a href="showMovimientos.php" class="btn btn-custom">
                <i class="fas fa-list me-2"></i>Ver Todos los Movimientos
            </a>
            <a href="movimientosEncuesta.php" class="btn btn-custom">
                <i class="fas fa-plus me-2"></i>Nuevo Movimiento
            </a>
            <a href="../../access.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-home me-2"></i>Menú Principal
            </a>
        </div>
    </div>

    <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
</body>

</html>
