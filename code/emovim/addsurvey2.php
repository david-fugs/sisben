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
    $fecha_alta_encMovim    = date('Y-m-d h:i:s');
    $fecha_edit_encMovim    = '0000-00-00 00:00:00';
    $id_usu                 = $_SESSION['id_usu'];

    $sql = "INSERT INTO encMovimientos (fec_reg_encMovim, doc_encMovim, nom_encMovim,  dir_encMovim, zona_encMovim, id_com, id_bar, id_correg, id_vere, otro_bar_ver_encMovim, tram_solic_encMovim, integra_encMovim, num_ficha_encMovim, obs_encMovim, estado_encMovim, fecha_alta_encMovim, fecha_edit_encMovim,tipo_documento, ciudad_expedicion, fecha_expedicion, id_usu) 
        VALUES ('$fec_reg_encMovim', '$doc_encMovim', '$nom_encMovim', '$dir_encMovim', '$zona_encMovim', '$id_com', '$id_bar', '$id_correg', '$id_vere', '$otro_bar_ver_encMovim', '$tram_solic_encMovim', '$integra_encMovim', '$num_ficha_encMovim', '$obs_encMovim', '$estado_encMovim', '$fecha_alta_encMovim', '$fecha_edit_encMovim','$tipo_documento','$ciudad_expedicion','$fecha_expedicion', '$id_usu')";

    $resultado = $mysqli->query($sql);

    $id_encMovim = $mysqli->insert_id;

    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los arreglos de integrantes
        $cant_integMovim        = $_POST['cant_integMovim'] ?? array();
        $gen_integMovim         = $_POST['gen_integMovim'] ?? array();
        $rango_integMovim       = $_POST['rango_integMovim'] ?? array();
        $condicionDispacapacidad = $_POST['condicionDispacapacidad'] ?? array();
        $grupoEtnico            = $_POST['grupoEtnico'] ?? array();
        $orientacionSexual      = $_POST['orientacionSexual'] ?? array();

        // Otras variables
        $id_usu                 = $_SESSION['id_usu'];
        $estado_integMovim      = 1;
        $fecha_alta_integMovim  = date('Y-m-d h:i:s');
        $fecha_edit_integMovim  = '0000-00-00 00:00:00';

        // Mapeo de descripción del rango de integrantes a valor numérico
        $rango_edad_map = array(
            "0 - 6"                 => 1,
            "7 - 12"                => 2,
            "13 - 17"               => 3,
            "18 - 28"               => 4,
            "29 - 45"               => 5,
            "46 - 64"               => 6,
            "Mayor o igual a 65"    => 7
        );

        foreach ($gen_integMovim as $key => $genero) {
            // Verificar que los valores estén definidos y no sean null
            if (isset($cant_integMovim[$key]) && isset($rango_integMovim[$key])) {
                // Obtener los valores individuales para el integrante actual
                $cantidad           = $cant_integMovim[$key];
                $rango_descripcion  = $rango_integMovim[$key];
                $discapacidad           = $condicionDispacapacidad[$key];
                $grupo                  = $grupoEtnico[$key];
                $orientacion            = $orientacionSexual[$key];


                // Obtener el valor numérico del rango a partir del mapeo
                $rango_valor = isset($rango_edad_map[$rango_descripcion]) ? $rango_edad_map[$rango_descripcion] : 'Valor_predeterminado';

                // Crear la consulta de inserción para el integrante actual
                $sql = "INSERT INTO integMovimientos (cant_integMovim, gen_integMovim, rango_integMovim,condicionDiscapacidad, grupoEtnico, orientacionSexual, estado_integMovim, fecha_alta_integMovim, fecha_edit_integMovim, id_usu, id_encMovim) 
                    VALUES ('$cantidad', '$genero', '$rango_valor', '$discapacidad' , '$grupo' , '$orientacion' ,'$estado_integMovim', '$fecha_alta_integMovim', '$fecha_edit_integMovim', '$id_usu', '$id_encMovim')";

                // Ejecutar la consulta
                if ($mysqli->query($sql) === TRUE) {
                    // Éxito al insertar el integrante
                    //echo "El integrante $key se insertó correctamente.<br>";
                } else {
                    echo "Error al insertar el integrante $key: " . $mysqli->error . "<br>";
                }
            }
        }
    }
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
