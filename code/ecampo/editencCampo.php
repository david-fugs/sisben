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
                $id_encCampo            = $_POST['id_encCampo'];
                $fec_pre_encCampo       = $_POST['fec_pre_encCampo'];
                $fec_rea_encCampo       = $_POST['fec_rea_encCampo'];
                $doc_encCampo           = $_POST['doc_encCampo'];
                $nom_encCampo           = mb_strtoupper($_POST['nom_encCampo']);
                $dir_encCampo           = mb_strtoupper($_POST['dir_encCampo']);
                $zona_encCampo          = $_POST['zona_encCampo'];
                $id_com                 = $_POST['id_com'];
                $id_bar                 = $_POST['id_bar'];
                $id_correg              = $_POST['id_correg'];
                $id_vere                = $_POST['id_vere'];
                $otro_bar_ver_encCampo  = mb_strtoupper($_POST['otro_bar_ver_encCampo']);
                $est_fic_encCampo       = $_POST['est_fic_encCampo'];
                $integra_encCampo       = $_POST['integra_encCampo'];
                $num_ficha_encCampo     = $_POST['num_ficha_encCampo'];
                $proc_encCampo          = $_POST['proc_encCampo'];
                $obs_encCampo           = mb_strtoupper($_POST['obs_encCampo']);
                $estado_encCampo        = 1;
                $fecha_edit_encCampo    = date('Y-m-d h:i:s');
                $id_usu                 = $_SESSION['id_usu'];
               
               $update = "UPDATE encCampo SET fec_pre_encCampo='".$fec_pre_encCampo."', fec_rea_encCampo='".$fec_rea_encCampo."', doc_encCampo='".$doc_encCampo."', nom_encCampo='".$nom_encCampo."', dir_encCampo='".$dir_encCampo."', zona_encCampo='".$zona_encCampo."', id_com='".$id_com."', id_bar='".$id_bar."', id_correg='".$id_correg."', id_vere='".$id_vere."', otro_bar_ver_encCampo='".$otro_bar_ver_encCampo."', est_fic_encCampo='".$est_fic_encCampo."', integra_encCampo='".$integra_encCampo."', num_ficha_encCampo='".$num_ficha_encCampo."', proc_encCampo='".$proc_encCampo."', obs_encCampo='".$obs_encCampo."', estado_encCampo='".$estado_encCampo."', fecha_edit_encCampo='".$fecha_edit_encCampo."', id_usu='".$id_usu."' WHERE id_encCampo='".$id_encCampo."'";

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