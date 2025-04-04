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
    session_start(); // Por si no está iniciado
    $id_encVenta = $_POST['id_encVenta'];
    $id_usu = $_SESSION['id_usu'];

    // 1. Lista de campos a verificar
    $campos = [
        'fec_reg_encVenta', 'doc_encVenta', 'tipo_documento', 'ciudad_expedicion', 'fecha_expedicion',
        'nom_encVenta', 'dir_encVenta', 'zona_encVenta', 'id_com', 'id_bar', 'id_correg', 'id_vere',
        'otro_bar_ver_encVenta', 'tram_solic_encVenta', 'integra_encVenta', 'num_ficha_encVenta', 'obs_encVenta'
    ];

    // 2. Obtener datos actuales
    $query = "SELECT * FROM encVentanilla WHERE id_encVenta = '$id_encVenta'";
    $result = mysqli_query($mysqli, $query);
    $actual = mysqli_fetch_assoc($result);

    // 3. Obtener nuevos datos del formulario
    $datos_nuevos = [];
    foreach ($campos as $campo) {
        $valor = isset($_POST[$campo]) ? $_POST[$campo] : '';
        $datos_nuevos[$campo] = in_array($campo, ['nom_encVenta', 'dir_encVenta', 'otro_bar_ver_encVenta', 'obs_encVenta']) ? mb_strtoupper($valor) : $valor;
    }

    // 4. Verificar si hay algún cambio
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
        // 5. Ejecutar UPDATE
        $fecha_edit_encVenta = date('Y-m-d H:i:s');
        $estado_encVenta = 1;

        $update = "UPDATE encVentanilla SET ";
        foreach ($campos as $campo) {
            $valor = $mysqli->real_escape_string($datos_nuevos[$campo]);
            $update .= "$campo = '$valor', ";
        }
        $update .= "estado_encVenta = '$estado_encVenta', fecha_edit_encVenta = '$fecha_edit_encVenta', id_usu = '$id_usu' ";
        $update .= "WHERE id_encVenta = '$id_encVenta'";

        $up = mysqli_query($mysqli, $update);

        if ($up) {
            // 6. Insertar o actualizar en movimientos
            $doc_encVenta = $datos_nuevos['doc_encVenta'];

            // Verificamos si ya hay registro para ese id_encVenta
            $check = "SELECT * FROM movimientos WHERE id_encuesta = '$id_encVenta' AND id_usu = '$id_usu'";
            $res_check = mysqli_query($mysqli, $check);

            if (mysqli_num_rows($res_check) > 0) {
                // Ya existe → actualizar cantidad_encuesta
                $update_mov = "UPDATE movimientos SET cantidad_encuesta = cantidad_encuesta + 1 WHERE id_encuesta = '$id_encVenta' AND id_usu = '$id_usu'";
                mysqli_query($mysqli, $update_mov);
            } else {
                // No existe → insertar nuevo
                $insert_mov = "INSERT INTO movimientos (id_encuesta, doc_encVenta, id_usu, cantidad_encuesta) 
                               VALUES ('$id_encVenta', '$doc_encVenta', '$id_usu', 1)";
                mysqli_query($mysqli, $insert_mov);
            }

            echo "✅ Registro actualizado correctamente y movimiento registrado.";
        } else {
            echo "❌ Error al actualizar: " . mysqli_error($mysqli);
        }
    } else {
        echo "⚠️ No se realizaron cambios.";
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