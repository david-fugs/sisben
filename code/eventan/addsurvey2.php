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
    $fecha_nacimiento        = $_POST['fecha_nacimiento'] ?? '';
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

    // Verificar si ya existe una encuesta para este documento
    $verificar_sql = "SELECT COUNT(*) as total FROM encventanilla WHERE doc_encVenta = '$doc_encVenta' AND estado_encVenta = 1";
    $verificar_resultado = $mysqli->query($verificar_sql);
    $encuesta_existente = $verificar_resultado->fetch_assoc();
    
    // Si ya existe una encuesta, agregar comentario en observaciones
    if ($encuesta_existente['total'] > 0) {
        $obs_encVenta .= " [REGISTRO ADICIONAL - Ya existe encuesta previa para este documento]";
    }// Inserción en encVentanilla
    $sql = "INSERT INTO encventanilla (fec_reg_encVenta, doc_encVenta, nom_encVenta, dir_encVenta, zona_encVenta, id_com, id_bar, otro_bar_ver_encVenta, tram_solic_encVenta, integra_encVenta, num_ficha_encVenta, obs_encVenta, estado_encVenta, fecha_alta_encVenta, fecha_edit_encVenta, tipo_documento, fecha_expedicion, departamento_expedicion, ciudad_expedicion, fecha_nacimiento, id_usu, sisben_nocturno, estado_ficha) 
        VALUES ('$fec_reg_encVenta', '$doc_encVenta', '$nom_encVenta', '$dir_encVenta', '$zona_encVenta', '$id_com', '$id_bar', '$otro_bar_ver_encVenta', '$tram_solic_encVenta', '$integra_encVenta', '$num_ficha_encVenta', '$obs_encVenta', '$estado_encVenta', '$fecha_alta_encVenta', '$fecha_edit_encVenta', '$tipo_documento', '$fecha_expedicion', '$departamento_expedicion', '$ciudad_expedicion', '$fecha_nacimiento', '$id_usu', '$sisben_nocturno', 1)";

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

// Determinar el mensaje de éxito según si es una encuesta adicional
$mensaje_exito = "SE GUARDÓ DE FORMA EXITOSA EL REGISTRO";
$icono_exito = "fas fa-check-circle";

if ($encuesta_existente['total'] > 1) { // Ahora hay más de una encuesta para este documento
    $mensaje_exito = "SE GUARDÓ DE FORMA EXITOSA EL REGISTRO ADICIONAL";
    $icono_exito = "fas fa-plus-circle";
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
                .success-container {
                    background: linear-gradient(135deg, #28a745, #20c997);
                    border-radius: 15px;
                    color: white;
                    padding: 2rem;
                    margin: 2rem auto;
                    box-shadow: 0 8px 30px rgba(40, 167, 69, 0.3);
                    max-width: 600px;
                }
            </style>
        </head>
        <body style='background-color: #f8f9fa;'>
            <center>
                <div class='container mt-5'>
                    <img src='../../img/sisben.png' width=250 height=155 class='responsive mb-4'>
                    <div class='success-container'>
                        <h3><b><i class='{$icono_exito}'></i> {$mensaje_exito}</b></h3>
                        " . ($encuesta_existente['total'] > 1 ? 
                            "<p class='mt-3'><small><i class='fas fa-info-circle'></i> Se ha creado un registro adicional para el documento {$doc_encVenta}</small></p>" : 
                            "") . "
                    </div>
                    <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                </div>
            </center>
        </body>
    </html>
";
