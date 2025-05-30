<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();  // Asegúrate de salir del script después de redirigir
}

$id_usu     = $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BD SISBEN</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>

    <?php
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");
    $mysqli->set_charset('utf8');
    if (isset($_POST['btn-update'])) {
        $id_integVenta          = $_POST['id_integVenta'];
        $cant_integVenta        = $_POST['cant_integVenta'];
        $gen_integVenta         = $_POST['gen_integVenta'];
        $rango_integVenta       = $_POST['rango_integVenta'];
        $estado_integVenta      = 0;
        $fecha_edit_integVenta  = date('Y-m-d h:i:s');
        $id_usu                 = $_SESSION['id_usu'];
        $movimiento            = $_POST['movimiento'] ?? '';

        $select_encVenta = mysqli_query($mysqli, "SELECT * FROM integventanilla WHERE id_integVenta = '$id_integVenta'");
        $row_encVenta = mysqli_fetch_array($select_encVenta);
        $id_encVenta = $row_encVenta['id_encVenta'];

        $update = "UPDATE integventanilla SET cant_integVenta='" . $cant_integVenta . "', gen_integVenta='" . $gen_integVenta . "', rango_integVenta='" . $rango_integVenta . "', estado_integVenta='" . $estado_integVenta . "', fecha_edit_integVenta='" . $fecha_edit_integVenta . "', id_usu='" . $id_usu . "' WHERE id_integVenta='" . $id_integVenta . "'";

        $up = mysqli_query($mysqli, $update);

        if ($up) {

            // 1. Obtener doc_encVenta desde encventanilla
            $queryDoc = "SELECT doc_encVenta FROM encventanilla WHERE id_encVenta = '$id_encVenta'";
            $resultadoDoc = $mysqli->query($queryDoc);

            if ($resultadoDoc && $rowDoc = $resultadoDoc->fetch_assoc()) {
                $doc_enc = $rowDoc['doc_encVenta'] ?? '';

                // 2. Obtener el movimiento que llega por POST
                $movimiento = isset($_POST['movimiento']) ? $_POST['movimiento'] : '';

                // Validar que el nombre de la columna sea seguro
                $columnas_validas = [
                    "retiro_personas",
                    "retiro_personas_inconformidad",
                    // Agrega aquí todas las columnas válidas
                ];

                $columna = strtolower($movimiento); // convertimos a minúscula para comparación
                if (in_array($columna, $columnas_validas)) {

                    // 3. Verificar si ya existe en movimientos
                    $queryExiste = "SELECT $columna FROM movimientos WHERE id_encuesta = '$id_encVenta' AND id_usu = '$id_usu'";
                    $resultadoExiste = $mysqli->query($queryExiste);

                    $exito = false;

                    if ($resultadoExiste && $resultadoExiste->num_rows > 0) {
                        // Ya existe, actualizar
                        $row = $resultadoExiste->fetch_assoc();
                        $nuevaCantidad = $row[$columna] + 1;

                        $update = "UPDATE movimientos SET $columna = '$nuevaCantidad' WHERE id_encuesta = '$id_encVenta' AND id_usu = '$id_usu'";
                        $exito = $mysqli->query($update);
                    } else {
                        // No existe, insertar con la columna dinámica
                        $insert = "INSERT INTO movimientos (id_encuesta, doc_encVenta, $columna, id_usu) 
                           VALUES ('$id_encVenta', '$doc_enc', 1, '$id_usu')";
                        $exito = $mysqli->query($insert);
                    }

                    // Mostrar confirmación si hubo éxito
                    if ($exito) {
                        echo "
                <!DOCTYPE html>
                <html lang='es'>
                <head>
                    <meta charset='utf-8' />
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                    <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet'>
                    <link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet'>
                    <link rel='stylesheet' href='../../css/bootstrap.min.css'>
                    <link href='../../fontawesome/css/all.css' rel='stylesheet'>
                    <title>BD SISBEN</title>
                    <style>
                        .responsive {
                            max-width: 100%;
                            height: auto;
                        }
                    </style>
                </head>
                <body>
                    <center>
                        <img src='../../img/sisben.png' width=300 height=185 class='responsive'>
                        <div class='container'>
                            <br />
                            <h3><b><i class='fas fa-check-circle'></i> SE GUARDÓ DE FORMA EXITOSA EL REGISTRO</b></h3><br />
                            <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                        </div>   
                    </center>
                </body>
                </html>
                ";
                    }
                } else {
                    echo "❌ Movimiento inválido.";
                }
            }
        } else {
            echo "❌ Error al actualizar el registro: " . mysqli_error($mysqli);
        }
    }
    ?>
</body>

</html>