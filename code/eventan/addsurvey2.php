<?php

session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();  // Asegúrate de salir del script después de redirigir
}

$usuario    = $_SESSION['id_usu'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

include("../../conexion.php");
date_default_timezone_set("America/Bogota");
header("Content-Type: text/html;charset=utf-8");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura de datos enviados por POST
    $fec_reg_encVenta        = $_POST['fec_reg_encVenta'] ?? '';
    $doc_encVenta            = $_POST['doc_encVenta'];
    $tipo_documento          = $_POST['tipo_documento'];
    $fecha_expedicion        = $_POST['fecha_expedicion'];
    $departamento_expedicion = $_POST['departamento_expedicion'];
    $ciudad_expedicion       = $_POST['ciudad_expedicion'];
    $nom_encVenta           = mb_strtoupper($_POST['nom_encVenta']);
    $dir_encVenta           = mb_strtoupper($_POST['dir_encVenta']);
    $zona_encVenta          = $_POST['zona_encVenta'];
    $id_com                 = $_POST['id_com'];
    $id_bar                 = $_POST['id_bar'];
    $otro_bar_ver_encVenta  = mb_strtoupper($_POST['otro_bar_ver_encVenta']);
    $tram_solic_encVenta    = $_POST['tram_solic_encVenta'];
    $integra_encVenta       = $_POST['integra_encVenta'];
    $sisben_nocturno         = $_POST['sisben_nocturno'];
    $num_ficha_encVenta     = $_POST['num_ficha_encVenta'];
    $obs_encVenta           = mb_strtoupper($_POST['obs_encVenta']);
    $estado_encVenta        = 1;
    $fecha_alta_encVenta    = date('Y-m-d h:i:s');
    $fecha_edit_encVenta    = '0000-00-00 00:00:00';
    $id_usu                 = $_SESSION['id_usu'];

    // Inserción en encVentanilla
    $sql = "INSERT INTO encventanilla (fec_reg_encVenta, doc_encVenta, nom_encVenta, dir_encVenta, zona_encVenta, id_com, id_bar, otro_bar_ver_encVenta, tram_solic_encVenta, integra_encVenta, num_ficha_encVenta, obs_encVenta, estado_encVenta, fecha_alta_encVenta, fecha_edit_encVenta, tipo_documento, fecha_expedicion,departamento_expedicion,ciudad_expedicion,id_usu ,sisben_nocturno) 
        VALUES ('$fec_reg_encVenta', '$doc_encVenta', '$nom_encVenta', '$dir_encVenta', '$zona_encVenta', '$id_com', '$id_bar', '$otro_bar_ver_encVenta', '$tram_solic_encVenta', '$integra_encVenta', '$num_ficha_encVenta', '$obs_encVenta', '$estado_encVenta', '$fecha_alta_encVenta', '$fecha_edit_encVenta','$tipo_documento','$fecha_expedicion', '$departamento_expedicion','$ciudad_expedicion', '$id_usu', '$sisben_nocturno')";

    $resultado = $mysqli->query($sql);

    $id_encVenta = $mysqli->insert_id; // Obtener el ID insertado

    // Procesamiento de integrantes
    $cant_integVenta        = $_POST['cant_integVenta'] ?? array();
    $gen_integVenta         = $_POST['gen_integVenta'] ?? array();
    $rango_integVenta       = $_POST['rango_integVenta'] ?? array();
    $condicionDiscapacidad  = $_POST['condicionDiscapacidad'] ?? array();
    $grupoEtnico            = $_POST['grupoEtnico'] ?? array();
    $orientacionSexual      = $_POST['orientacionSexual'] ?? array();
    $nivelEducativo         = $_POST['nivelEducativo'] ?? array();
    $tipoDiscapacidad       = $_POST['tipoDiscapacidad'] ?? array();
    $victima             = $_POST['victima'] ?? array();
    $mujerGestante          = $_POST['mujerGestante'] ?? array();
    $cabezaFamilia           = $_POST['cabezaFamilia'] ?? array();
    $experienciaMigratoria  = $_POST['experienciaMigratoria'] ?? array();
    $seguridadSalud         = $_POST['seguridadSalud'] ?? array();
    $condicionOcupacion     = $_POST['condicionOcupacion'] ?? array();
    $estado_integVenta      = 1;
    $fecha_alta_integVenta  = date('Y-m-d h:i:s');
    $fecha_edit_integVenta  = '0000-00-00 00:00:00';
    $rango_edad_map = array(
        "0 - 6"                 => 1,
        "7 - 12"                => 2,
        "13 - 17"               => 3,
        "18 - 28"               => 4,
        "29 - 45"               => 5,
        "46 - 64"               => 6,
        "Mayor o igual a 65"    => 7
    );
  
    foreach ($gen_integVenta as $key => $genero) {
        
      
            
            $cantidad           = $cant_integVenta[$key];
            $rango_descripcion  = $rango_integVenta[$key];
            $rango_valor        = $rango_edad_map[$rango_descripcion] ?? 'NULL';
            $discapacidad       = $condicionDiscapacidad[$key];
            $grupo              = $grupoEtnico[$key];
            $orientacion        = $orientacionSexual[$key];
            $educacion          = $nivelEducativo[$key];
            $tipo_discapacidad  = $tipoDiscapacidad[$key];
            $es_victima         = $victima[$key];
            $es_gestante        = $mujerGestante[$key];
            $es_cabeza_familia  = $cabezaFamilia[$key];
            $experiencia_migr   = $experienciaMigratoria[$key];
            $seguridad_social   = $seguridadSalud[$key];
            $cond_ocupacion     = $condicionOcupacion[$key];

            // Inserción en la base de datos
            $sql = "INSERT INTO integventanilla 
                        (cant_integVenta, gen_integVenta, rango_integVenta, condicionDiscapacidad, grupoEtnico, 
                         orientacionSexual, nivelEducativo, tipoDiscapacidad, victima, 
                         mujerGestante, cabezaFamilia, experienciaMigratoria, seguridadSalud, condicionOcupacion, 
                         estado_integVenta, fecha_alta_integVenta, fecha_edit_integVenta, id_usu, id_encVenta) 
                    VALUES ('$cantidad', '$genero', '$rango_valor', '$discapacidad', '$grupo', '$orientacion', 
                            '$educacion', '$tipo_discapacidad', '$es_victima', '$es_gestante', 
                            '$es_cabeza_familia', '$experiencia_migr', '$seguridad_social', '$cond_ocupacion', 
                            '$estado_integVenta', '$fecha_alta_integVenta', '$fecha_edit_integVenta', '$id_usu', '$id_encVenta')";
               
            if ($mysqli->query($sql) === TRUE) {
                // Éxito al insertar el integrante
            } else {
                echo "Error al insertar el integrante $key: " . $mysqli->error . "<br>";
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
