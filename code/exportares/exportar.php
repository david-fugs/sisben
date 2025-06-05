<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();  // Asegúrate de salir del script después de redirigir
}
date_default_timezone_set("America/Bogota");
include("../../conexion.php");
$id_usu     = $_SESSION['id_usu'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];
header("Content-Type: text/html;charset=utf-8");

$sql_users = "SELECT id_usu, nombre FROM usuarios WHERE tipo_usu = 3 ORDER BY nombre";
$encuestadores = mysqli_query($mysqli, $sql_users);

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
                    <i class="fa-solid fa-address-card me-2"></i> EXPORTAR ENCUESTAS REALIZADAS VENTANILLA
                </h1>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Buscar por Rango de Fechas</h5>
                <form action="exportarAll.php" method="get" class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <input name="fecha_inicio" type="date" class="form-control" placeholder="Fecha inicio" required>
                    </div>
                    <div class="col-md-3">
                        <input name="fecha_fin" type="date" class="form-control" placeholder="Fecha fin" required>
                    </div>
                    <div class="col-md-4">
                        <select name="id_usu" class="form-select" >
                            <option value="">-- Selecciona Encuestador --</option>
                            <?php foreach ($encuestadores as $enc): ?>
                                <option value="<?= $enc['id_usu'] ?>"><?= htmlspecialchars($enc['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-search me-1"></i> Exportar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <center>
        <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
    </center>

    </section>

</body>

</html>