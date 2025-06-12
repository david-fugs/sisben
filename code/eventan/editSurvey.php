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

include("../../conexion.php");
// Verificar que se haya recibido un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No se especificó ningún ID de encuesta para editar'); window.location.href='showsurvey.php';</script>";
    exit();
}

// Sanear y convertir el ID a entero para evitar inyección SQL
$id_encVenta = (int)$_GET['id'];

// Para debug, mostrar el ID recibido
// echo "ID recibido: $id_encVenta"; // Puedes descomentar esta línea para verificar el ID

date_default_timezone_set("America/Bogota");
$mysqli->set_charset('utf8');

// Obtener datos de la encuesta
$query = "SELECT * FROM encventanilla WHERE id_encVenta = ?";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    echo "<script>alert('Error al preparar la consulta: " . $mysqli->error . "'); window.location.href='showsurvey.php';</script>";
    exit();
}

$stmt->bind_param("i", $id_encVenta);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "<script>alert('Error al ejecutar la consulta: " . $mysqli->error . "'); window.location.href='showsurvey.php';</script>";
    exit();
}

$encuesta = $result->fetch_assoc();

if (!$encuesta) {
    echo "<script>alert('No se encontró la encuesta solicitada'); window.location.href='showsurvey.php';</script>";
    exit();
}

// Obtener datos de integrantes (integencventanilla)
// Primero verificamos si la tabla existe
$check_table = $mysqli->query("SHOW TABLES LIKE 'integventanilla'");
if ($check_table->num_rows == 0) {
    echo "<script>alert('La tabla integventanilla no existe en la base de datos');</script>";
    $integrantes = [];
} else {
    $query_integrantes = "SELECT * FROM integventanilla WHERE id_encVenta = ?";
    $stmt_integrantes = $mysqli->prepare($query_integrantes);
    
    if (!$stmt_integrantes) {
        echo "<script>alert('Error al preparar la consulta de integrantes: " . $mysqli->error . "');</script>";
        $integrantes = [];
    } else {
        $stmt_integrantes->bind_param("i", $id_encVenta);
        $stmt_integrantes->execute();
        $result_integrantes = $stmt_integrantes->get_result();
        
        if (!$result_integrantes) {
            echo "<script>alert('Error al ejecutar la consulta de integrantes: " . $mysqli->error . "');</script>";
            $integrantes = [];
        } else {
            $integrantes = [];
            while ($row = $result_integrantes->fetch_assoc()) {
                $integrantes[] = $row;
            }
        }
    }
}

//traer todos los departamentos
$sql = "SELECT * FROM departamentos ORDER BY nombre_departamento ASC";
$resultado = mysqli_query($mysqli, $sql);
$departamentos = [];
while ($row = mysqli_fetch_assoc($resultado)) {
    $departamentos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editar Encuesta</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../barrios.js"> </script>
    <script src="integrantesEncuesta.js" ></script>
    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
            padding: 6px 12px;
            font-size: 16px;
            line-height: 30px;
        }

        /* Ajusta la flecha del desplegable */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
        }

        #integrantes-container {
            display: flex;
            flex-direction: column;
            /* Apila los elementos verticalmente */
            gap: 15px;
            width: 100%;
        }

        .formulario-dinamico {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group-dinamico {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .form-group-dinamico label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .smaller-input {
            width: 100%;
        }

        .btn-danger {
            grid-column: 1 / -1;
            /* Hace que el botón ocupe todo el ancho */
            justify-self: start;
            /* Alinea el botón a la izquierda */
            margin-top: 10px;
        }

        /* Estilo para selectores largos */
        select.form-control {
            min-width: 100%;
            max-width: 100%;
        }

        .responsive {
            max-width: 100%;
            height: auto;
        }

        .smaller-input {
            width: 200px;
            /* Ajusta el ancho según sea necesario */
        }

        .formulario-dinamico {
            margin-bottom: 10px;
            /* Ajusta el margen inferior según sea necesario */
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Editar Encuesta</h1>
        <form action="updateSurvey.php" method="POST">
            <input type="hidden" name="id_encVenta" value="<?php echo $data['id_encVenta']; ?>">

            <div class="mb-3">
                <label for="doc_encVenta" class="form-label">Documento</label>
                <input type="text" class="form-control" id="doc_encVenta" name="doc_encVenta" value="<?php echo $data['doc_encVenta']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="nom_encVenta" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nom_encVenta" name="nom_encVenta" value="<?php echo $data['nom_encVenta']; ?>" required>
            </div>

            <!-- Add more fields as needed -->

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="showsurvey.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script>
        // Add JavaScript for dynamic functionality if needed
    </script>
</body>

</html>
