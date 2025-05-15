<?php

session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();  // Asegúrate de salir del script después de redirigir
}

$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

include("../../conexion.php");
date_default_timezone_set("America/Bogota");
header("Content-Type: text/html;charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura de datos enviados por POST
    $fec_reg_info        = $_POST['fec_reg_info'];
    $doc_info            = $_POST['doc_info'];
    $nom_info            = mb_strtoupper($_POST['nom_info']);
    $gen_integVenta      = $_POST['gen_integVenta'];
    $tipo_documento          = $_POST['tipo_documento'];
    $departamento_expedicion = $_POST['departamento_expedicion'];
    $ciudad_expedicion       = $_POST['ciudad_expedicion'];
    $fecha_expedicion       = $_POST['fecha_expedicion'];
    $rango_integVenta = isset($_POST['rango_integVenta']) ? $_POST['rango_integVenta'] : '';
    $victima = isset($_POST['victima']) ? $_POST['victima'] : '';
    $condicionDiscapacidad = isset($_POST['condicionDiscapacidad']) ? $_POST['condicionDiscapacidad'] : '';
    $tipoDiscapacidad = isset($_POST['tipoDiscapacidad']) ? $_POST['tipoDiscapacidad'] : '';
    $mujerGestante = isset($_POST['mujerGestante']) ? $_POST['mujerGestante'] : '';
    $cabezaFamilia = isset($_POST['cabezaFamilia']) ? $_POST['cabezaFamilia'] : '';
    $orientacionSexual = isset($_POST['orientacionSexual']) ? $_POST['orientacionSexual'] : '';
    $experienciaMigratoria = isset($_POST['experienciaMigratoria']) ? $_POST['experienciaMigratoria'] : '';
    $grupoEtnico = isset($_POST['grupoEtnico']) ? $_POST['grupoEtnico'] : '';
    $seguridadSalud = isset($_POST['seguridadSalud']) ? $_POST['seguridadSalud'] : '';
    $nivelEducativo = isset($_POST['nivelEducativo']) ? $_POST['nivelEducativo'] : '';
    $condicionOcupacion = isset($_POST['condicionOcupacion']) ? $_POST['condicionOcupacion'] : '';
    $fecha_alta_encInfo    = date('Y-m-d h:i:s');
    $fecha_edit_encInfo    = '0000-00-00 00:00:00';
    $tipo_solic_encInfo    = $_POST['tipo_solic_encInfo'];
    $obs1_encInfo         = mb_strtoupper($_POST['obs1_encInfo']);
    $obs2_encInfo         = mb_strtoupper($_POST['obs2_encInfo']);
    $fecha_alta_encVenta    = date('Y-m-d h:i:s');
    $fecha_edit_encVenta    = '0000-00-00 00:00:00';
    $id_usu                 = $_SESSION['id_usu'];



    $sql = "INSERT INTO informacion (
        fecha_reg_info,
        doc_info,
        nom_info,
        gen_integVenta,
        tipo_documento,
        departamento_expedicion,
        ciudad_expedicion,
        fecha_expedicion,
        
        rango_integVenta,
        victima,
        condicionDiscapacidad,
        tipoDiscapacidad,
        mujerGestante,
        cabezaFamilia,
        orientacionSexual,
        experienciaMigratoria,
        grupoEtnico,
        seguridadSalud,
        nivelEducativo,
        condicionOcupacion,
        fecha_alta_info,
        fecha_edit_info,
        tipo_solic_encInfo,
        observacion,
        info_adicional,
        id_usu
    ) VALUES (
        '$fec_reg_info',
        '$doc_info',
        '$nom_info',
        '$gen_integVenta',
        '$tipo_documento',
        '$departamento_expedicion',
        '$ciudad_expedicion',
        '$fecha_expedicion',
        '$rango_integVenta',
        '$victima',
        '$condicionDiscapacidad',
        '$tipoDiscapacidad',
        '$mujerGestante',
        '$cabezaFamilia',
        '$orientacionSexual',
        '$experienciaMigratoria',
        '$grupoEtnico',
        '$seguridadSalud',
        '$nivelEducativo',
        '$condicionOcupacion',
        '$fecha_alta_encInfo',
        '$fecha_edit_encInfo',
        '$tipo_solic_encInfo',
        '$obs1_encInfo',
        '$obs2_encInfo',
         $id_usu
    )";

    // Ejec
    if (!$mysqli->query($sql)) {
        echo "Error en la consulta: " . $mysqli->error; // Muestra el error de MySQL
    } else {
        echo "Registro insertado correctamente.";

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
}
