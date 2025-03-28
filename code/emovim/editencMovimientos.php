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
    	    if(isset($_POST['btn-update']))
            {
                $id_encMovim            = $_POST['id_encMovim'];
                $fec_reg_encMovim       = $_POST['fec_reg_encMovim'];
                $doc_encMovim           = $_POST['doc_encMovim'];
                $tipo_documento          = $_POST['tipo_documento'];
                $ciudad_expedicion       = $_POST['ciudad_expedicion'];
                $fecha_expedicion       = $_POST['fecha_expedicion'];

                $nom_encMovim           = mb_strtoupper($_POST['nom_encMovim']);
                $dir_encMovim           = mb_strtoupper($_POST['dir_encMovim']);
                $zona_encMovim          = $_POST['zona_encMovim'];
                $id_com                 = $_POST['id_com'];
                $id_bar                 = $_POST['id_bar'];
                $id_correg              = $_POST['id_correg'];
                $id_vere                = $_POST['id_vere'];
                $otro_bar_ver_encMovim  = mb_strtoupper($_POST['otro_bar_ver_encMovim']);
                $tram_solic_encMovim    = $_POST['tram_solic_encMovim'];
                $integra_encMovim       = $_POST['integra_encMovim'];
                $num_ficha_encMovim     = $_POST['num_ficha_encMovim'];
                $obs_encMovim           = mb_strtoupper($_POST['obs_encMovim']);
                $estado_encMovim        = 1;
                $fecha_edit_encMovim    = date('Y-m-d h:i:s');
                $id_usu                 = $_SESSION['id_usu'];
               
               $update = "UPDATE encMovimientos SET fec_reg_encMovim='".$fec_reg_encMovim."', doc_encMovim='".$doc_encMovim."', nom_encMovim='".$nom_encMovim."', dir_encMovim='".$dir_encMovim."', zona_encMovim='".$zona_encMovim."', id_com='".$id_com."', id_bar='".$id_bar."', id_correg='".$id_correg."', id_vere='".$id_vere."', otro_bar_ver_encMovim='".$otro_bar_ver_encMovim."', tram_solic_encMovim='".$tram_solic_encMovim."', integra_encMovim='".$integra_encMovim."', num_ficha_encMovim='".$num_ficha_encMovim."', obs_encMovim='".$obs_encMovim."', estado_encMovim='".$estado_encMovim."', fecha_edit_encMovim='".$fecha_edit_encMovim."', tipo_documento='".$tipo_documento."',ciudad_expedicion='".$ciudad_expedicion."',fecha_expedicion='".$fecha_expedicion."', id_usu='".$id_usu."' WHERE id_encMovim='".$id_encMovim."'";

                $up = mysqli_query($mysqli, $update);

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