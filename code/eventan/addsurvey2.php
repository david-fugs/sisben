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
    $fecha_alta_encVenta    = date('Y-m-d h:i:s');
    $fecha_edit_encVenta    = '0000-00-00 00:00:00';
    $id_usu                 = $_SESSION['id_usu'];

    $sql = "INSERT INTO encVentanilla (fec_reg_encVenta, doc_encVenta, nom_encVenta,  dir_encVenta, zona_encVenta, id_com, id_bar, id_correg, id_vere, otro_bar_ver_encVenta, tram_solic_encVenta, integra_encVenta, num_ficha_encVenta, obs_encVenta, estado_encVenta, fecha_alta_encVenta, fecha_edit_encVenta, id_usu) 
        VALUES ('$fec_reg_encVenta', '$doc_encVenta', '$nom_encVenta', '$dir_encVenta', '$zona_encVenta', '$id_com', '$id_bar', '$id_correg', '$id_vere', '$otro_bar_ver_encVenta', '$tram_solic_encVenta', '$integra_encVenta', '$num_ficha_encVenta', '$obs_encVenta', '$estado_encVenta', '$fecha_alta_encVenta', '$fecha_edit_encVenta', '$id_usu')";

    $resultado = $mysqli->query($sql);

    $id_encVenta = $mysqli->insert_id;

    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los arreglos de integrantes
        $cant_integVenta        = $_POST['cant_integVenta'] ?? array();
        $gen_integVenta         = $_POST['gen_integVenta'] ?? array();
        $rango_integVenta       = $_POST['rango_integVenta'] ?? array();
        $condicionDispacapacidad = $_POST['condicionDispacapacidad'] ?? array();
        $grupoEtnico            = $_POST['grupoEtnico'] ?? array();
        $orientacionSexual      = $_POST['orientacionSexual'] ?? array();

        // Otras variables
        $id_usu                 = $_SESSION['id_usu'];
        $estado_integVenta      = 1;
        $fecha_alta_integVenta  = date('Y-m-d h:i:s');
        $fecha_edit_integVenta  = '0000-00-00 00:00:00';

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

        foreach ($gen_integVenta as $key => $genero) {
            // Verificar que los valores estén definidos y no sean null
            if (
                isset($cant_integVenta[$key]) && isset($rango_integVenta[$key]) &&
                isset($condicionDispacapacidad[$key]) && isset($grupoEtnico[$key]) &&
                isset($orientacionSexual[$key])
            ) {

                // Obtener los valores individuales para el integrante actual
                $cantidad               = $cant_integVenta[$key];
                $rango_descripcion      = $rango_integVenta[$key];
                $rango_valor            = $rango_edad_map[$rango_descripcion] ?? 'Valor_predeterminado';
                $discapacidad           = $condicionDispacapacidad[$key];
                $grupo                  = $grupoEtnico[$key];
                $orientacion            = $orientacionSexual[$key];

                // Crear la consulta de inserción para el integrante actual
                $sql = "INSERT INTO integVentanilla 
                            (cant_integVenta, gen_integVenta, rango_integVenta, condicionDispacapacidad, grupoEtnico, orientacionSexual, estado_integVenta, fecha_alta_integVenta, fecha_edit_integVenta, id_usu, id_encVenta) 
                            VALUES ('$cantidad', '$genero', '$rango_valor', '$discapacidad', '$grupo', '$orientacion', '$estado_integVenta', '$fecha_alta_integVenta', '$fecha_edit_integVenta', '$id_usu', '$id_encVenta')";

                // Ejecutar la consulta
                if ($mysqli->query($sql) === TRUE) {
                    // Éxito al insertar el integrante
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
