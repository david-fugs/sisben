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

// Obtener ID de la encuesta a ver
$id_encuesta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_encuesta == 0) {
    header("Location: showEncuestas.php");
    exit();
}

include("../../conexion.php");

// Obtener datos de la encuesta con joins
$sql_encuesta = "SELECT ev.*, 
                        u.nombre as nombre_usuario,
                        d.nombre_departamento,
                        m.nombre_municipio,
                        c.nombre_com,
                        b.nombre_bar
                 FROM encventanilla ev 
                 LEFT JOIN usuarios u ON ev.id_usu = u.id_usu 
                 LEFT JOIN departamentos d ON ev.departamento_expedicion = d.cod_departamento
                 LEFT JOIN municipios m ON ev.ciudad_expedicion = m.cod_municipio
                 LEFT JOIN comunas c ON ev.id_com = c.id_com
                 LEFT JOIN barrios b ON ev.id_bar = b.id_bar
                 WHERE ev.id_encVenta = ?";

$stmt = $mysqli->prepare($sql_encuesta);
$stmt->bind_param("i", $id_encuesta);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: showEncuestas.php");
    exit();
}

$encuesta = $resultado->fetch_assoc();

header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BD SISBEN - Detalles de Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>

    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

        .info-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .info-row {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 1.1em;
        }

        .badge-tramite {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .badge-inclusion { background-color: #28a745; color: white; }
        .badge-modificacion { background-color: #17a2b8; color: white; }
        .badge-retiro { background-color: #dc3545; color: white; }
        .badge-actualizacion { background-color: #fd7e14; color: white; }
        .badge-default { background-color: #6c757d; color: white; }

        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5">
        <!-- Header -->
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 150px;">
                </div>
                <div class="col-md-10">
                    <h1 class="mb-1">
                        <i class="fas fa-eye me-2"></i>DETALLES DE ENCUESTA DE VENTANILLA
                    </h1>
                    <p class="mb-0">Documento: <strong><?php echo $encuesta['doc_encVenta']; ?></strong> - <?php echo $encuesta['nom_encVenta']; ?></p>
                    <small>Creada el: <?php echo date('d/m/Y H:i', strtotime($encuesta['fecha_alta_encVenta'])); ?> por <?php echo $encuesta['nombre_usuario']; ?></small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Datos Básicos -->
            <div class="col-md-6">
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-user me-2"></i>Datos Básicos
                    </h3>
                    <div class="info-row">
                        <div class="info-label">Documento</div>
                        <div class="info-value"><?php echo $encuesta['doc_encVenta'] ?: 'No especificado'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tipo de Documento</div>
                        <div class="info-value"><?php echo $encuesta['tipo_documento'] ?: 'No especificado'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nombre Completo</div>
                        <div class="info-value"><?php echo $encuesta['nom_encVenta'] ?: 'No especificado'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fecha de Expedición</div>
                        <div class="info-value">
                            <?php echo $encuesta['fecha_expedicion'] ? date('d/m/Y', strtotime($encuesta['fecha_expedicion'])) : 'No especificada'; ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Departamento de Expedición</div>
                        <div class="info-value"><?php echo $encuesta['nombre_departamento'] ?: 'No especificado'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Ciudad de Expedición</div>
                        <div class="info-value"><?php echo $encuesta['nombre_municipio'] ?: 'No especificada'; ?></div>
                    </div>
                </div>
            </div>

            <!-- Datos de Ubicación -->
            <div class="col-md-6">
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                    </h3>
                    <div class="info-row">
                        <div class="info-label">Dirección</div>
                        <div class="info-value"><?php echo $encuesta['dir_encVenta'] ?: 'No especificada'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Zona</div>
                        <div class="info-value">
                            <span class="badge <?php echo ($encuesta['zona_encVenta'] == 'Urbana') ? 'bg-primary' : 'bg-success'; ?>">
                                <?php echo $encuesta['zona_encVenta'] ?: 'No especificada'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Comuna</div>
                        <div class="info-value"><?php echo $encuesta['nombre_com'] ?: 'No especificada'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Barrio</div>
                        <div class="info-value"><?php echo $encuesta['nombre_bar'] ?: 'No especificado'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Otro Barrio/Vereda</div>
                        <div class="info-value"><?php echo $encuesta['otro_bar_ver_encVenta'] ?: 'No especificado'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Datos del Trámite -->
            <div class="col-md-6">
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-file-alt me-2"></i>Datos del Trámite
                    </h3>
                    <div class="info-row">
                        <div class="info-label">Trámite Solicitado</div>
                        <div class="info-value">
                            <?php 
                            $tramite_class = 'badge-default';
                            switch ($encuesta['tram_solic_encVenta']) {
                                case 'Inclusión':
                                    $tramite_class = 'badge-inclusion';
                                    break;
                                case 'Modificación':
                                    $tramite_class = 'badge-modificacion';
                                    break;
                                case 'Retiro':
                                    $tramite_class = 'badge-retiro';
                                    break;
                                case 'Actualización':
                                    $tramite_class = 'badge-actualizacion';
                                    break;
                            }
                            ?>
                            <span class="badge badge-tramite <?php echo $tramite_class; ?>">
                                <?php echo $encuesta['tram_solic_encVenta'] ?: 'No especificado'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Cantidad de Integrantes</div>
                        <div class="info-value">
                            <span class="badge bg-info fs-6"><?php echo $encuesta['integra_encVenta'] ?: '0'; ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Número de Ficha</div>
                        <div class="info-value"><?php echo $encuesta['num_ficha_encVenta'] ?: 'No especificado'; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">SISBEN Nocturno</div>
                        <div class="info-value">
                            <?php if ($encuesta['sisben_nocturno']): ?>
                                <span class="badge <?php echo ($encuesta['sisben_nocturno'] == 'Si') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $encuesta['sisben_nocturno']; ?>
                                </span>
                            <?php else: ?>
                                No especificado
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del Sistema -->
            <div class="col-md-6">
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-cog me-2"></i>Información del Sistema
                    </h3>
                    <div class="info-row">
                        <div class="info-label">ID de Encuesta</div>
                        <div class="info-value"><?php echo $encuesta['id_encVenta']; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fecha de Registro</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($encuesta['fecha_alta_encVenta'])); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Usuario que Registró</div>
                        <div class="info-value"><?php echo $encuesta['nombre_usuario']; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Observaciones</div>
                        <div class="info-value">
                            <?php if ($encuesta['obs_encVenta']): ?>
                                <div class="border p-2 rounded bg-light">
                                    <?php echo nl2br(htmlspecialchars($encuesta['obs_encVenta'])); ?>
                                </div>
                            <?php else: ?>
                                Sin observaciones
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="info-section">
            <div class="row g-3">
                <?php if ($tipo_usu == 1 || $encuesta['id_usu'] == $id_usu): ?>
                <div class="col-md-3">
                    <a href="editEncuesta.php?id=<?php echo $encuesta['id_encVenta']; ?>" class="btn btn-custom w-100">
                        <i class="fas fa-edit me-2"></i>Editar Encuesta
                    </a>
                </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <a href="showEncuestas.php" class="btn btn-secondary w-100">
                        <i class="fas fa-list me-2"></i>Volver al Listado
                    </a>
                </div>
                <div class="col-md-3">
                    <button onclick="window.print()" class="btn btn-info w-100">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="addsurvey1.php" class="btn btn-success w-100">
                        <i class="fas fa-plus me-2"></i>Nueva Encuesta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración para impresión
        window.addEventListener('beforeprint', function() {
            document.body.style.background = 'white';
        });

        window.addEventListener('afterprint', function() {
            document.body.style.background = '';
        });
    </script>
</body>
</html>
