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
                $id_encInfo             = $_POST['id_encInfo'];
                $fec_rea_encInfo        = $_POST['fec_rea_encInfo'];
                $doc_encInfo            = $_POST['doc_encInfo'];
                $nom_encInfo            = mb_strtoupper($_POST['nom_encInfo']);
                $tipo_solic_encInfo     = $_POST['tipo_solic_encInfo'];
                $obs1_encInfo           = $_POST['obs1_encInfo'];
                $obs2_encInfo           = $_POST['obs2_encInfo'];
                $estado_encInfo         = 1;
                $fecha_edit_encInfo     = date('Y-m-d h:i:s');
                $id_usu                 = $_SESSION['id_usu'];
               
               $update = "UPDATE encInfo SET fec_rea_encInfo='".$fec_rea_encInfo."', doc_encInfo='".$doc_encInfo."', nom_encInfo='".$nom_encInfo."', tipo_solic_encInfo='".$tipo_solic_encInfo."', obs1_encInfo='".$obs1_encInfo."', obs2_encInfo='".$obs2_encInfo."', estado_encInfo='".$estado_encInfo."', fecha_edit_encInfo='".$fecha_edit_encInfo."', id_usu='".$id_usu."' WHERE id_encInfo='".$id_encInfo."'";

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