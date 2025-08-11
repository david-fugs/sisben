<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

header("Content-Type: text/html;charset=utf-8");
include("../../conexion.php");

// Obtener comunas para el select
$comunas = $mysqli->query("SELECT * FROM comunas ORDER BY nombre_com ASC");

// CRUD: Crear
if (isset($_POST['crear_barrio'])) {
    $nombre_bar = $mysqli->real_escape_string($_POST['nombre_bar']);
    $id_com = intval($_POST['id_com']);
    $zona_bar = $mysqli->real_escape_string($_POST['zona_bar']);
    $mysqli->query("INSERT INTO barrios (nombre_bar, id_com, zona_bar) VALUES ('$nombre_bar', $id_com, '$zona_bar')");
    header("Location: barrios.php");
    exit();
}
// CRUD: Editar
if (isset($_POST['editar_barrio'])) {
    $id_bar = intval($_POST['id_bar']);
    $nombre_bar = $mysqli->real_escape_string($_POST['nombre_bar']);
    $id_com = intval($_POST['id_com']);
    $zona_bar = $mysqli->real_escape_string($_POST['zona_bar']);
    $mysqli->query("UPDATE barrios SET nombre_bar='$nombre_bar', id_com=$id_com, zona_bar='$zona_bar' WHERE id_bar=$id_bar");
    header("Location: barrios.php");
    exit();
}
// CRUD: Eliminar
if (isset($_GET['eliminar'])) {
    $id_bar = intval($_GET['eliminar']);
    $mysqli->query("DELETE FROM barrios WHERE id_bar=$id_bar");
    header("Location: barrios.php");
    exit();
}

// Zebra_Pagination para paginación
require_once("../../zebra.php");

// Buscador
$where = [];
if (!empty($_GET['nombre_bar'])) {
    $nombre_bar = $mysqli->real_escape_string($_GET['nombre_bar']);
    $where[] = "barrios.nombre_bar LIKE '%$nombre_bar%'";
}
if (!empty($_GET['id_com'])) {
    $id_com = intval($_GET['id_com']);
    $where[] = "barrios.id_com = $id_com";
}

// Contar total de registros para paginación
$count_query = "SELECT COUNT(*) as total FROM barrios JOIN comunas ON barrios.id_com = comunas.id_com";
if ($where) {
    $count_query .= " WHERE " . implode(" AND ", $where);
}
$count_result = $mysqli->query($count_query);
$total_registros = $count_result->fetch_assoc()['total'];

// Zebra_Pagination
$resul_x_pagina = 25;
$paginacion = new Zebra_Pagination();
$paginacion->records($total_registros);
$paginacion->records_per_page($resul_x_pagina);

// Consulta con paginación
$query = "SELECT barrios.*, comunas.nombre_com FROM barrios JOIN comunas ON barrios.id_com = comunas.id_com";
if ($where) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY barrios.nombre_bar ASC LIMIT " . (($paginacion->get_page() - 1) * $resul_x_pagina) . "," . $resul_x_pagina;
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Barrios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-form { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .btn-custom { background: linear-gradient(45deg, #667eea, #764ba2); border: none; color: white; border-radius: 25px; padding: 10px 25px; transition: all 0.3s ease; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); color: white; }
        .btn-outline-secondary { background-color: #6c757d; color: white; border-color: #6c757d; border-radius: 25px; padding: 10px 25px; transition: all 0.3s ease; }
        .btn-outline-secondary:hover { background-color: #5a6268; border-color: #5a6268; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
            <img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 300px;">
        </div>
        <div class="col-md-8">
            <h1 class="text-primary fw-bold">
                <i class="fa-solid fa-circle-info me-2"></i> GESTIÓN DE BARRIOS
            </h1>
        </div>
    </div>
</div>
<div class="container my-4">
    <div class="search-form">
        <h5 class="mb-3"><i class="fas fa-search me-2"></i>Buscar Barrio</h5>
        <form action="barrios.php" method="get">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Nombre Barrio</label>
                    <input name="nombre_bar" type="text" class="form-control" placeholder="Nombre del barrio" value="<?php echo isset($_GET['nombre_bar']) ? $_GET['nombre_bar'] : ''; ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Comuna</label>
                    <select name="id_com" class="form-select">
                        <option value="">Todas</option>
                        <?php $comunas->data_seek(0); while($com = $comunas->fetch_assoc()): ?>
                            <option value="<?php echo $com['id_com']; ?>" <?php if(isset($_GET['id_com']) && $_GET['id_com'] == $com['id_com']) echo 'selected'; ?>><?php echo htmlspecialchars($com['nombre_com']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="w-100">
                        <button type="submit" class="btn btn-custom w-100 mb-2">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="barrios.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="container mb-4">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#crearBarrioModal">
        <i class="fas fa-plus"></i> Nuevo Barrio
    </button>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Barrio</th>
                    <th>Comuna</th>
                    <th>Zona</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $registro_inicio_pagina = (($paginacion->get_page() - 1) * $resul_x_pagina) + 1;
            $registro_fin_pagina = min($paginacion->get_page() * $resul_x_pagina, $total_registros);
            while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_bar']; ?></td>
                    <td><?php echo htmlspecialchars(utf8_encode($row['nombre_bar'])); ?></td>
                    <td><?php echo htmlspecialchars(utf8_encode($row['nombre_com'])); ?></td>
                    <td><?php echo htmlspecialchars($row['zona_bar']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editarBarrioModal<?php echo $row['id_bar']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="barrios.php?eliminar=<?php echo $row['id_bar']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este barrio?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <!-- Modal Editar -->
                <div class="modal fade" id="editarBarrioModal<?php echo $row['id_bar']; ?>" tabindex="-1" aria-labelledby="editarBarrioLabel<?php echo $row['id_bar']; ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="barrios.php">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editarBarrioLabel<?php echo $row['id_bar']; ?>">Editar Barrio</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_bar" value="<?php echo $row['id_bar']; ?>">
                          <div class="mb-3">
                            <label class="form-label">Nombre Barrio</label>
                            <input type="text" name="nombre_bar" class="form-control" value="<?php echo htmlspecialchars(utf8_encode($row['nombre_bar'])); ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Comuna</label>
                            <select name="id_com" class="form-select" required>
                                <?php $comunas->data_seek(0); while($com = $comunas->fetch_assoc()): ?>
                                    <option value="<?php echo $com['id_com']; ?>" <?php if($row['id_com'] == $com['id_com']) echo 'selected'; ?>><?php echo htmlspecialchars(utf8_encode($com['nombre_com'])); ?></option>
                                <?php endwhile; ?>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Zona</label>
                            <input type="text" name="zona_bar" class="form-control" value="<?php echo htmlspecialchars($row['zona_bar']); ?>">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" name="editar_barrio" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            <?php endwhile; ?>
            </tbody>
        </table>
        <div class="info-pagination text-center mb-2">
            <strong>Mostrando <?php echo $registro_inicio_pagina; ?> - <?php echo $registro_fin_pagina; ?> de <?php echo $total_registros; ?> registros</strong>
        </div>
        <div class="zebra_pagination">
            <?php $paginacion->render(); ?>
        </div>
    </div>
</div>
<!-- Modal Crear -->
<div class="modal fade" id="crearBarrioModal" tabindex="-1" aria-labelledby="crearBarrioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="barrios.php">
        <div class="modal-header">
          <h5 class="modal-title" id="crearBarrioLabel">Nuevo Barrio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre Barrio</label>
            <input type="text" name="nombre_bar" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Comuna</label>
            <select name="id_com" class="form-select" required>
                <option value="">Seleccione una comuna</option>
                <?php $comunas->data_seek(0); while($com = $comunas->fetch_assoc()): ?>
                    <option value="<?php echo $com['id_com']; ?>"><?php echo htmlspecialchars(utf8_encode($com['nombre_com'])); ?></option>
                <?php endwhile; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Zona</label>
            <input type="text" name="zona_bar" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="crear_barrio" class="btn btn-success">Crear</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="text-center mt-4">
    <a href="../../access.php" class="btn btn-secondary btn-lg">
        <i class="fas fa-arrow-left me-2"></i> Regresar al Menú Principal
    </a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/fed2435e21.js"></script>
</body>
</html>
