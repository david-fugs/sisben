<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
	header("Location: ../../index.php");
	exit();
}

header("Content-Type: text/html;charset=utf-8");
include("../../conexion.php");

// CRUD: Crear
if (isset($_POST['crear_comuna'])) {
    $nombre_com = $mysqli->real_escape_string($_POST['nombre_com']);
    $mysqli->query("INSERT INTO comunas (nombre_com) VALUES ('$nombre_com')");
    header("Location: comunas.php");
    exit();
}
// CRUD: Editar
if (isset($_POST['editar_comuna'])) {
    $id_com = intval($_POST['id_com']);
    $nombre_com = $mysqli->real_escape_string($_POST['nombre_com']);
    $mysqli->query("UPDATE comunas SET nombre_com='$nombre_com' WHERE id_com=$id_com");
    header("Location: comunas.php");
    exit();
}
// CRUD: Eliminar
if (isset($_GET['eliminar'])) {
    $id_com = intval($_GET['eliminar']);
    $mysqli->query("DELETE FROM comunas WHERE id_com=$id_com");
    header("Location: comunas.php");
    exit();
}
// Buscador
$where = [];
if (!empty($_GET['nombre_com'])) {
    $nombre_com = $mysqli->real_escape_string($_GET['nombre_com']);
    $where[] = "nombre_com LIKE '%$nombre_com%'";
}
$query = "SELECT * FROM comunas";
if ($where) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY nombre_com ASC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Comunas</title>
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
                <i class="fa-solid fa-circle-info me-2"></i> GESTIÓN DE COMUNAS
            </h1>
        </div>
    </div>
</div>
<div class="container my-4">
    <div class="search-form">
        <h5 class="mb-3"><i class="fas fa-search me-2"></i>Buscar Comuna</h5>
        <form action="comunas.php" method="get">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nombre Comuna</label>
                    <input name="nombre_com" type="text" class="form-control" placeholder="Nombre de la comuna" value="<?php echo isset($_GET['nombre_com']) ? $_GET['nombre_com'] : ''; ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="w-100">
                        <button type="submit" class="btn btn-custom w-100 mb-2">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="comunas.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="container mb-4">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#crearComunaModal">
        <i class="fas fa-plus"></i> Nueva Comuna
    </button>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Comuna</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_com']; ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_com']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editarComunaModal<?php echo $row['id_com']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="comunas.php?eliminar=<?php echo $row['id_com']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar esta comuna?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <!-- Modal Editar -->
                <div class="modal fade" id="editarComunaModal<?php echo $row['id_com']; ?>" tabindex="-1" aria-labelledby="editarComunaLabel<?php echo $row['id_com']; ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post" action="comunas.php">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editarComunaLabel<?php echo $row['id_com']; ?>">Editar Comuna</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_com" value="<?php echo $row['id_com']; ?>">
                          <div class="mb-3">
                            <label class="form-label">Nombre Comuna</label>
                            <input type="text" name="nombre_com" class="form-control" value="<?php echo htmlspecialchars($row['nombre_com']); ?>" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                          <button type="submit" name="editar_comuna" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Crear -->
<div class="modal fade" id="crearComunaModal" tabindex="-1" aria-labelledby="crearComunaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="comunas.php">
        <div class="modal-header">
          <h5 class="modal-title" id="crearComunaLabel">Nueva Comuna</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre Comuna</label>
            <input type="text" name="nombre_com" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="crear_comuna" class="btn btn-success">Crear</button>
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
