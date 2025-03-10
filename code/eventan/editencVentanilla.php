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
                $id_encVenta            = $_POST['id_encVenta'];
                $fec_reg_encVenta       = $_POST['fec_reg_encVenta'];
                $doc_encVenta           = $_POST['doc_encVenta'];
                $nom_encVenta           = mb_strtoupper($_POST['nom_encVenta']);
                $dir_encVenta           = mb_strtoupper($_POST['dir_encVenta']);
                $zona_encVenta          = $_POST['zona_encVenta'];
                $id_com                 = $_POST['id_com'];
                $id_bar                 = $_POST['id_bar'];
                $id_correg              = $_POST['id_correg'];
                $id_vere                = $_POST['id_vere'];
                $otro_bar_ver_encVenta  = mb_strtoupper($_POST['otro_bar_ver_encVenta']);
                $tram_solic_encVenta    = $_POST['tram_solic_encVenta'];
                $integra_encVenta       = $_POST['integra_encVenta'];
                $num_ficha_encVenta     = $_POST['num_ficha_encVenta'];
                $obs_encVenta           = mb_strtoupper($_POST['obs_encVenta']);
                $estado_encVenta        = 1;
                $fecha_edit_encVenta    = date('Y-m-d h:i:s');
                $id_usu                 = $_SESSION['id_usu'];
               
               $update = "UPDATE encVentanilla SET fec_reg_encVenta='".$fec_reg_encVenta."', doc_encVenta='".$doc_encVenta."', nom_encVenta='".$nom_encVenta."', dir_encVenta='".$dir_encVenta."', zona_encVenta='".$zona_encVenta."', id_com='".$id_com."', id_bar='".$id_bar."', id_correg='".$id_correg."', id_vere='".$id_vere."', otro_bar_ver_encVenta='".$otro_bar_ver_encVenta."', tram_solic_encVenta='".$tram_solic_encVenta."', integra_encVenta='".$integra_encVenta."', num_ficha_encVenta='".$num_ficha_encVenta."', obs_encVenta='".$obs_encVenta."', estado_encVenta='".$estado_encVenta."', fecha_edit_encVenta='".$fecha_edit_encVenta."', id_usu='".$id_usu."' WHERE id_encVenta='".$id_encVenta."'";

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