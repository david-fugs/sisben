<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu = $_SESSION['id_usu'];
$tipo_usu = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");

include('../../conexion.php');

// Establecer charset UTF-8 para manejar tildes y ñ
mysqli_set_charset($mysqli, "utf8");

// Obtener ID de la encuesta a eliminar
$id_encuesta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_encuesta == 0) {
    echo "<script>alert('ID de encuesta inválido'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

// Verificar permisos y obtener datos de la encuesta (usar id_encCampo)
$query_encuesta = "SELECT doc_encVenta, nom_encVenta FROM encuestacampo WHERE id_encCampo = $id_encuesta";
if ($tipo_usu != '1') {
    $query_encuesta .= " AND id_usu = $id_usu";
}

$result_encuesta = mysqli_query($mysqli, $query_encuesta);
$encuesta = mysqli_fetch_assoc($result_encuesta);

if (!$encuesta) {
    echo "<script>alert('Encuesta no encontrada o sin permisos para eliminar'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

// Si se confirma la eliminación
if (isset($_POST['confirmar_eliminacion'])) {
    // Iniciar transacción
    mysqli_autocommit($mysqli, false);
    
    try {
        // Eliminar integrantes primero (por la relación de clave foránea)
        $sql_delete_integrantes = "DELETE FROM integcampo WHERE id_encuesta = $id_encuesta";
        if (!mysqli_query($mysqli, $sql_delete_integrantes)) {
            throw new Exception("Error al eliminar integrantes: " . mysqli_error($mysqli));
        }
        
    // Eliminar la encuesta
    $sql_delete_encuesta = "DELETE FROM encuestacampo WHERE id_encCampo = $id_encuesta";
        if ($tipo_usu != '1') {
            $sql_delete_encuesta .= " AND id_usu = $id_usu";
        }
        
        if (!mysqli_query($mysqli, $sql_delete_encuesta)) {
            throw new Exception("Error al eliminar encuesta: " . mysqli_error($mysqli));
        }
        
        // Confirmar transacción
        mysqli_commit($mysqli);
        
        echo "<script>
            alert('Encuesta eliminada exitosamente');
            window.location.href = 'showsurvey.php';
        </script>";
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($mysqli);
        echo "<script>
            alert('Error al eliminar la encuesta: " . $e->getMessage() . "');
            window.location.href = 'showsurvey.php';
        </script>";
    }
    
    mysqli_autocommit($mysqli, true);
    mysqli_close($mysqli);
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BD SISBEN - Eliminar Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .delete-icon {
            background: #dc3545;
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        .btn-confirm {
            background: #dc3545;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .btn-confirm:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }

        .info-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="delete-container">
        <div class="delete-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h2 class="text-danger mb-3">¿Confirmar Eliminación?</h2>
        
        <p class="text-muted mb-4">
            Esta acción no se puede deshacer. Se eliminará permanentemente la encuesta y todos sus integrantes asociados.
        </p>

        <div class="info-card">
            <h5>Datos de la Encuesta:</h5>
            <p><strong>Documento:</strong> <?php echo htmlspecialchars($encuesta['doc_encVenta']); ?></p>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($encuesta['nom_encVenta']); ?></p>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <form method="post" style="display: inline;">
                <input type="hidden" name="confirmar_eliminacion" value="1">
                <button type="submit" class="btn-confirm" onclick="return confirm('¿Está absolutamente seguro de eliminar esta encuesta?')">
                    <i class="fas fa-trash me-2"></i>
                    Sí, Eliminar
                </button>
            </form>
            
            <a href="showsurvey.php" class="btn-cancel">
                <i class="fas fa-times me-2"></i>
                Cancelar
            </a>
        </div>

        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Esta acción eliminará la encuesta y todos los integrantes asociados de forma permanente.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
