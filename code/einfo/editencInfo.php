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
        session_start(); // Asegúrate de tener session_start() si usas $_SESSION

        $id_informacion = $_POST['id_informacion'];
        $fecha_edit_info = date('Y-m-d');
        $id_usu = $_SESSION['id_usu'];
        $doc_info = $_POST['doc_info'];
        // Obtener los datos actuales de la BD
        $sql_actual = "SELECT * FROM informacion WHERE id_informacion = '$id_informacion'";
        $res_actual = mysqli_query($mysqli, $sql_actual);
        $actual = mysqli_fetch_assoc($res_actual);

        // Datos nuevos
        $campos = [
            'fecha_reg_info',
            'doc_info',
            'nom_info',
            'tipo_documento',
            'ciudad_expedicion',
            'fecha_expedicion',
            'rango_integVenta',
            'victima',
            'condicionDiscapacidad',
            'tipoDiscapacidad',
            'mujerGestante',
            'cabezaFamilia',
            'orientacionSexual',
            'experienciaMigratoria',
            'grupoEtnico',
            'seguridadSalud',
            'nivelEducativo',
            'condicionOcupacion',
            'tipo_solic_encInfo',
            'info_adicional',
            'observacion'
        ];

        $datos_nuevos = [];
        foreach ($campos as $campo) {
            $valor = isset($_POST[$campo]) ? $_POST[$campo] : '';
            $datos_nuevos[$campo] = ($campo == 'nom_info') ? mb_strtoupper($valor) : $valor;
        }

        // Verificar si hubo algún cambio
        $hubo_cambio = false;
        foreach ($campos as $campo) {
            $valor_actual = isset($actual[$campo]) ? trim($actual[$campo]) : '';
            $valor_nuevo = trim($datos_nuevos[$campo]);
            if ($valor_actual !== $valor_nuevo) {
                $hubo_cambio = true;
                break;
            }
        }

        if ($hubo_cambio) {
            // Hacer el UPDATE
            $update = "UPDATE informacion SET ";
            foreach ($datos_nuevos as $campo => $valor) {
                $update .= "$campo = '" . mysqli_real_escape_string($mysqli, $valor) . "', ";
            }
            $update .= "fecha_edit_info = '$fecha_edit_info' WHERE id_informacion='$id_informacion'";

            $up = mysqli_query($mysqli, $update);

            if (!$up) {
                echo "Error en la actualización: " . mysqli_error($mysqli);
            } else {
                // Verificar si ya existe en movimientos
                $check_mov = "SELECT * FROM movimientos WHERE id_informacion = '$id_informacion' AND id_usu = '$id_usu'";
                $res_mov = mysqli_query($mysqli, $check_mov);
                $doc_info = trim($_POST['doc_info']);

                if (mysqli_num_rows($res_mov) > 0) {
                    // Ya existe, sumar 1
                    $update_mov = "UPDATE movimientos SET cantidad_informacion = cantidad_informacion + 1 WHERE id_informacion = '$id_informacion' AND id_usu = '$id_usu'";
                    mysqli_query($mysqli, $update_mov);
                } else {
                    // No existe, insertar nuevo
                    $insert_mov = "INSERT INTO movimientos (id_informacion, doc_info, id_usu, cantidad_informacion) 
                VALUES ('$id_informacion', '$doc_info', '$id_usu', 1)";
                    mysqli_query($mysqli, $insert_mov);
                }

                echo "Registro actualizado correctamente.";
            }
        } else {
            echo "No se realizaron cambios.";
        }

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
                                <title>FICHA</title>
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
                                    <h3><b><i class='fas fa-users'></i> SE ACTUALIZÓ DE FORMA EXITOSA EL REGISTRO</b></h3><br />
                                    <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                                </div>
                                </center>
                            </body>
                        </html>
                    ";
    }
    ?>
</body>

</html>