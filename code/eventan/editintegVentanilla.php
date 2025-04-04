<?php
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
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
    $id_integVenta = $_POST['id_integVenta'];
    $campos = [
        'cant_integVenta', 'gen_integVenta', 'rango_integVenta', 'condicionDiscapacidad',
        'grupoEtnico', 'orientacionSexual', 'tipoDiscapacidad', 'victima',
        'mujerGestante', 'cabezaFamilia', 'experienciaMigratoria', 'seguridadSalud',
        'nivelEducativo', 'condicionOcupacion'
    ];

    $datos_nuevos = [];
    foreach ($campos as $campo) {
        $datos_nuevos[$campo] = isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
    }

    // Obtener datos actuales y el id_encVenta
    $actual = [];
    $id_encVenta = null;
    $query_actual = "SELECT * FROM integventanilla WHERE id_integVenta = '$id_integVenta'";
    $res_actual = mysqli_query($mysqli, $query_actual);
    if ($res_actual && mysqli_num_rows($res_actual) > 0) {
        $actual = mysqli_fetch_assoc($res_actual);
        $id_encVenta = $actual['id_encVenta']; // Aquí obtenemos el id_encVenta relacionado
    }

    if (!$id_encVenta) {
        echo "❌ Error: No se encontró el id_encVenta asociado.<br>";
        exit;
    }

    // Verificar si hubo cambios
    $hubo_cambio = false;
    foreach ($campos as $campo) {
        $valor_actual = isset($actual[$campo]) ? trim($actual[$campo]) : '';
        $valor_nuevo = $datos_nuevos[$campo];
        if ($valor_actual !== $valor_nuevo) {
            $hubo_cambio = true;
            break;
        }
    }

    if ($hubo_cambio) {
        echo "✅ Se detectaron cambios.<br>";

        $estado_integVenta     = 1;
        $fecha_edit_integVenta = date('Y-m-d H:i:s');
        $id_usu                = $_SESSION['id_usu'];

        // Realizar UPDATE
        $update = "UPDATE integventanilla SET ";
        foreach ($campos as $campo) {
            $update .= "$campo = '" . $datos_nuevos[$campo] . "', ";
        }
        $update .= "estado_integVenta = '$estado_integVenta', 
                    fecha_edit_integVenta = '$fecha_edit_integVenta', 
                    id_usu = '$id_usu'
                    WHERE id_integVenta = '$id_integVenta'";

        if (mysqli_query($mysqli, $update)) {
            echo "✅ Datos actualizados correctamente en 'integventanilla'.<br>";

            // Obtener el doc_encVenta desde la tabla encventanilla
            $doc = '';
            $query_doc = "SELECT doc_encVenta FROM encventanilla WHERE id_encVenta = '$id_encVenta'";
            $res_doc = mysqli_query($mysqli, $query_doc);
            if ($res_doc && mysqli_num_rows($res_doc) > 0) {
                $row_doc = mysqli_fetch_assoc($res_doc);
                $doc = $row_doc['doc_encVenta'];
            }

            // Actualizar o insertar en movimientos
            $check = "SELECT * FROM movimientos WHERE id_encuesta = '$id_encVenta' AND id_usu = '$id_usu'";
            $res_check = mysqli_query($mysqli, $check);

            if (mysqli_num_rows($res_check) > 0) {
                $update_mov = "UPDATE movimientos 
                               SET cantidad_encuesta = cantidad_encuesta + 1 
                               WHERE id_encuesta = '$id_encVenta' AND id_usu = '$id_usu'";
                if (mysqli_query($mysqli, $update_mov)) {
                    echo "✅ Movimiento actualizado correctamente.<br>";
                } else {
                    echo "❌ Error al actualizar el movimiento: " . mysqli_error($mysqli) . "<br>";
                }
            } else {
                $insert_mov = "INSERT INTO movimientos (id_encuesta, doc_encVenta, id_usu, cantidad_encuesta) 
                               VALUES ('$id_encVenta', '$doc', '$id_usu', 1)";
                if (mysqli_query($mysqli, $insert_mov)) {
                    echo "✅ Movimiento insertado correctamente.<br>";
                } else {
                    echo "❌ Error al insertar el movimiento: " . mysqli_error($mysqli) . "<br>";
                }
            }
        } else {
            echo "❌ Error al actualizar integventanilla: " . mysqli_error($mysqli) . "<br>";
        }
    } else {
        echo "ℹ️ No se detectaron cambios, no se realizó ninguna actualización.<br>";
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