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

// Incluir conexión al principio para usar en el formulario
date_default_timezone_set("America/Bogota");
include("../../conexion.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BD SISBEN - Movimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>

    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

        .badge-movimiento {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85em;
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
        }        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }        .btn-outline-secondary {
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
        }        .card-stats {
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
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="row align-items-center mb-4">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 300px;">
            </div>
            <div class="col-md-8">
                <h1 class="text-primary fw-bold">
                    <i class="fas fa-list-alt me-2"></i> MOVIMIENTOS REGISTRADOS
                </h1>
                <p class="text-muted">Sistema independiente de gestión de movimientos</p>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="search-form">
            <h5 class="mb-3"><i class="fas fa-search me-2"></i>Buscar Movimientos</h5>            <form action="showMovimientos.php" method="get">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Documento</label>
                        <input name="doc_encVenta" type="text" class="form-control" placeholder="Número de documento" value="<?php echo isset($_GET['doc_encVenta']) ? $_GET['doc_encVenta'] : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre</label>
                        <input name="nom_encVenta" type="text" class="form-control" placeholder="Nombre del usuario" value="<?php echo isset($_GET['nom_encVenta']) ? $_GET['nom_encVenta'] : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de Movimiento</label>
                        <select name="tipo_movimiento" class="form-control">
                            <option value="">Todos</option>
                            <option value="inclusion" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'inclusion') ? 'selected' : ''; ?>>Inclusión</option>
                            <option value="Inconformidad por clasificacion" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'Inconformidad por clasificacion') ? 'selected' : ''; ?>>Inconformidad</option>
                            <option value="modificacion datos persona" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'modificacion datos persona') ? 'selected' : ''; ?>>Modificación datos</option>
                            <option value="Retiro ficha" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'Retiro ficha') ? 'selected' : ''; ?>>Retiro ficha</option>
                            <option value="Retiro personas" <?php echo (isset($_GET['tipo_movimiento']) && $_GET['tipo_movimiento'] == 'Retiro personas') ? 'selected' : ''; ?>>Retiro personas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Elegir asesor</label>
                        <select name="asesor" class="form-control">
                            <?php
                            if ($tipo_usu == 3) {
                                // Si es tipo_usu 3, solo mostrar su propio nombre
                                echo '<option value="' . $id_usu . '" selected>' . $nombre . '</option>';
                            } else {
                                // Para otros tipos, mostrar todos los asesores
                                echo '<option value="">Todos los asesores</option>';
                                $query_asesores = "SELECT DISTINCT u.id_usu, u.nombre 
                                                  FROM usuarios u 
                                                  INNER JOIN movimientos m ON u.id_usu = m.id_usu 
                                                  ORDER BY u.nombre";
                                $result_asesores = $mysqli->query($query_asesores);
                                while ($asesor = $result_asesores->fetch_assoc()) {
                                    $selected = (isset($_GET['asesor']) && $_GET['asesor'] == $asesor['id_usu']) ? 'selected' : '';
                                    echo '<option value="' . $asesor['id_usu'] . '" ' . $selected . '>' . $asesor['nombre'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-custom me-2">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="showMovimientos.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>    <?php
    require_once("../../zebra.php");    // Construir filtros dinámicos
    $where = [];
    
    // Excluir movimientos de tipo "encuesta nueva"
    $where[] = "m.tipo_movimiento != 'encuesta nueva'";
    
    // Filtro por usuario según tipo
    if ($tipo_usu == 3) {
        // Si es tipo_usu 3, solo mostrar sus propios registros
        $where[] = "m.id_usu = '$id_usu'";
    } else {
        // Para otros tipos, aplicar filtro de asesor si se selecciona
        if (!empty($_GET['asesor'])) {
            $asesor_id = $mysqli->real_escape_string($_GET['asesor']);
            $where[] = "m.id_usu = '$asesor_id'";
        }
    }

    // Filtros de búsqueda
    if (!empty($_GET['doc_encVenta'])) {
        $doc_encVenta = $mysqli->real_escape_string($_GET['doc_encVenta']);
        $where[] = "m.doc_encVenta LIKE '%$doc_encVenta%'";
    }

    if (!empty($_GET['nom_encVenta'])) {
        $nom_encVenta = $mysqli->real_escape_string($_GET['nom_encVenta']);
        $where[] = "m.nom_encVenta LIKE '%$nom_encVenta%'";
    }

    if (!empty($_GET['tipo_movimiento'])) {
        $tipo_movimiento = $mysqli->real_escape_string($_GET['tipo_movimiento']);
        $where[] = "m.tipo_movimiento = '$tipo_movimiento'";
    }

    // Construir consulta base
    $base_query = "SELECT m.*, u.nombre AS nombre_usuario,
                   CASE 
                       WHEN m.estado_ficha = 0 THEN 'FICHA RETIRADA'
                       ELSE 'ACTIVA'
                   END as estado_ficha_texto
                   FROM movimientos m 
                   LEFT JOIN usuarios u ON m.id_usu = u.id_usu
                   WHERE " . implode(" AND ", $where);

    // Contar registros totales
    $count_query = str_replace("SELECT m.*, u.nombre AS nombre_usuario,", "SELECT COUNT(*) as total,", $base_query);
    $count_query = preg_replace("/CASE.*?END as estado_ficha_texto/s", "1", $count_query);
      $count_result = $mysqli->query($count_query);
    $num_registros = $count_result->fetch_assoc()['total'];
    $resul_x_pagina = 25;

    // Mostrar estadísticas
    $stats_query = str_replace("SELECT m.*, u.nombre AS nombre_usuario,", "SELECT COUNT(*) as total, m.tipo_movimiento,", $base_query);
    $stats_query = preg_replace("/CASE.*?END as estado_ficha_texto/s", "1", $stats_query);
    $stats_query .= " GROUP BY m.tipo_movimiento";
    
    $stats_result = $mysqli->query($stats_query);
    $stats = [];
    while ($row = $stats_result->fetch_assoc()) {
        $stats[$row['tipo_movimiento']] = $row['total'];
    }
    ?>

    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card-stats p-3">
                    <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Estadísticas de Movimientos</h6>
                    <div class="row text-center">
                        <div class="col">
                            <strong><?php echo $stats['inclusion'] ?? 0; ?></strong><br>
                            <small>Inclusiones</small>
                        </div>
                        <div class="col">
                            <strong><?php echo $stats['Inconformidad por clasificacion'] ?? 0; ?></strong><br>
                            <small>Inconformidades</small>
                        </div>
                        <div class="col">
                            <strong><?php echo $stats['modificacion datos persona'] ?? 0; ?></strong><br>
                            <small>Modificaciones</small>
                        </div>
                        <div class="col">
                            <strong><?php echo $stats['Retiro ficha'] ?? 0; ?></strong><br>
                            <small>Retiros Ficha</small>
                        </div>
                        <div class="col">
                            <strong><?php echo $stats['Retiro personas'] ?? 0; ?></strong><br>
                            <small>Retiros Personas</small>
                        </div>
                        <div class="col">
                            <strong><?php echo $num_registros; ?></strong><br>
                            <small>Total</small>
                        </div>                    </div>
                </div>
            </div>
        </div>

        <?php
        // Configurar paginación
        $paginacion = new Zebra_Pagination();
        $paginacion->records($num_registros);
        $paginacion->records_per_page($resul_x_pagina);
        ?>

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
                        <thead class="table-dark">                            <tr>
                                <th>No.</th>
                                <th>Fecha Movimiento</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Tipo Movimiento</th>
                                <th>No. Ficha</th>
                                <th>Estado</th>
                                <th>Asesor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>                        <tbody>

                        <?php
                        // Consulta final con paginación
                        $final_query = $base_query . " ORDER BY m.fecha_movimiento DESC 
                                      LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
                        
                        $result = $mysqli->query($final_query);
                        
                        // Calcular números de registro para mostrar
                        $registro_inicio = (($paginacion->get_page() - 1) * $resul_x_pagina) + 1;
                        $registro_actual = $registro_inicio;
                        
                        while ($row = mysqli_fetch_array($result)) {
                            // Determinar clase del badge según el tipo de movimiento
                            $badge_class = 'badge-default';
                            switch (strtolower($row['tipo_movimiento'])) {
                                case 'inclusion':
                                    $badge_class = 'badge-inclusion';
                                    break;
                                case 'inconformidad por clasificacion':
                                    $badge_class = 'badge-inconformidad';
                                    break;
                                case 'modificacion datos persona':
                                    $badge_class = 'badge-modificacion';
                                    break;
                                case 'retiro ficha':
                                    $badge_class = 'badge-retiro-ficha';
                                    break;
                                case 'retiro personas':
                                    $badge_class = 'badge-retiro-personas';
                                    break;
                            }

                            $estado_class = ($row['estado_ficha'] == 0) ? 'estado-retirada' : 'estado-activa';                            echo '
                            <tr>
                                <td>' . $registro_actual++ . '</td>
                                <td>' . date('d/m/Y H:i', strtotime($row['fecha_movimiento'])) . '</td>
                                <td><span class="badge bg-info">' . $row['doc_encVenta'] . '</span></td>
                                <td>' . $row['nom_encVenta'] . '</td>
                                <td><span class="badge-movimiento ' . $badge_class . '">' . $row['tipo_movimiento'] . '</span></td>
                                <td>' . $row['num_ficha_encVenta'] . '</td>
                                <td><span class="' . $estado_class . '">' . $row['estado_ficha_texto'] . '</span></td>
                                <td>' . $row['nombre_usuario'] . '</td>
                                <td>
                                    <a href="editMovimiento.php?id_movimiento=' . $row['id_movimiento'] . '" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="verMovimiento.php?id_movimiento=' . $row['id_movimiento'] . '" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>';
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <?php $paginacion->render(); ?>
        </div>

        <div class="text-center mt-4">
            <a href="../../access.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i> Regresar al Menú Principal
            </a>
            <a href="movimientosEncuesta.php" class="btn btn-success btn-lg ms-2">
                <i class="fas fa-plus me-2"></i> Nuevo Movimiento
            </a>
        </div>
    </div>

    <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
</body>

</html>
