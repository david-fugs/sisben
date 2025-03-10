<?php
     
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
        header("Location: ../../index.php");
        exit();  // Asegúrate de salir del script después de redirigir
    }

    $usuario    = $_SESSION['usuario'];
    $nombre     = $_SESSION['nombre'];
    $tipo_usu   = $_SESSION['tipo_usu'];
    
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");
    header("Content-Type: text/html;charset=utf-8");

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // Captura de datos enviados por POST
        $fec_rea_encInfo        = $_POST['fec_rea_encInfo'];
        $doc_encInfo            = $_POST['doc_encInfo'];
        $nom_encInfo            = mb_strtoupper($_POST['nom_encInfo']);
        $tipo_solic_encInfo     = $_POST['tipo_solic_encInfo'];
        $obs1_encInfo           = $_POST['obs1_encInfo'];
        $obs2_encInfo           = mb_strtoupper($_POST['obs2_encInfo']);
        $estado_encInfo        = 1;
        $fecha_alta_encInfo    = date('Y-m-d h:i:s');
        $fecha_edit_encInfo    = '0000-00-00 00:00:00';
        $id_usu                 = $_SESSION['id_usu'];

        $sql = "INSERT INTO encInfo (fec_rea_encInfo, doc_encInfo, nom_encInfo,  tipo_solic_encInfo, obs1_encInfo, obs2_encInfo, estado_encInfo, fecha_alta_encInfo, fecha_edit_encInfo, id_usu) 
        VALUES ('$fec_rea_encInfo', '$doc_encInfo', '$nom_encInfo', '$tipo_solic_encInfo', '$obs1_encInfo', '$obs2_encInfo', '$estado_encInfo', '$fecha_alta_encInfo', '$fecha_edit_encInfo', '$id_usu')";
        
        $resultado = $mysqli->query($sql);
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
?>