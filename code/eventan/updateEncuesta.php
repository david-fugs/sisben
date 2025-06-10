<?php

session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$usuario    = $_SESSION['id_usu'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

include("../../conexion.php");
date_default_timezone_set("America/Bogota");
header("Content-Type: text/html;charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Iniciar transacción
        $mysqli->autocommit(FALSE);
        
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
        $movimientos            = $_POST['movimientos']; // Nuevo campo de movimientos
        $integra_encVenta       = $_POST['integra_encVenta'];
        $sisben_nocturno        = $_POST['sisben_nocturno'];
        $num_ficha_encVenta     = $_POST['num_ficha_encVenta'];
        $obs_encVenta           = mb_strtoupper($_POST['obs_encVenta']);
        $fecha_edit_encVenta    = date('Y-m-d H:i:s');
        $id_usu                 = $_SESSION['id_usu'];        // Verificar si ya existen movimientos para este documento
        $sql_check_movimientos = "SELECT id_movimiento FROM movimientos WHERE doc_encVenta = '$doc_encVenta' ORDER BY fecha_movimiento DESC LIMIT 1";
        $result_check = $mysqli->query($sql_check_movimientos);
        
        $ultimo_movimiento_existe = ($result_check->num_rows > 0);
        
        // Determinar el estado de la ficha según el tipo de movimiento
        $estado_ficha = ($movimientos == "Retiro ficha") ? 0 : 1; // 0 = retirada, 1 = activa
        
        // CREAR NUEVO MOVIMIENTO (siempre independiente)
        $fecha_movimiento = date('Y-m-d H:i:s');
        
        $sql_insert_movimiento = "INSERT INTO movimientos (
            doc_encVenta, tipo_movimiento, fecha_movimiento, observacion, id_usu,
            fec_reg_encVenta, nom_encVenta, dir_encVenta, zona_encVenta, id_com, id_bar,
            otro_bar_ver_encVenta, tram_solic_encVenta, integra_encVenta, num_ficha_encVenta,
            obs_encVenta, tipo_documento, fecha_expedicion, departamento_expedicion,
            ciudad_expedicion, sisben_nocturno, estado_ficha, fecha_alta_movimiento
        ) VALUES (
            '$doc_encVenta', '$movimientos', '$fecha_movimiento', '$obs_encVenta', '$id_usu',
            '$fec_reg_encVenta', '$nom_encVenta', '$dir_encVenta', '$zona_encVenta', '$id_com', '$id_bar',
            '$otro_bar_ver_encVenta', '$movimientos', '$integra_encVenta', '$num_ficha_encVenta',
            '$obs_encVenta', '$tipo_documento', '$fecha_expedicion', '$departamento_expedicion',
            '$ciudad_expedicion', '$sisben_nocturno', '$estado_ficha', '$fecha_movimiento'
        )";

        if (!$mysqli->query($sql_insert_movimiento)) {
            throw new Exception("Error al crear el nuevo movimiento: " . $mysqli->error);
        }
        
        // Obtener el ID del movimiento recién creado
        $id_movimiento = $mysqli->insert_id;        // MANEJO DE INTEGRANTES VINCULADOS AL MOVIMIENTO
        // Solo procesar integrantes si no es "Retiro ficha"
        if ($movimientos != "Retiro ficha") {
            // Procesamiento de integrantes
            $cant_integVenta        = $_POST['cant_integVenta'] ?? array();
            $gen_integVenta         = $_POST['gen_integVenta'] ?? array();
            $rango_integVenta       = $_POST['rango_integVenta'] ?? array();
            $condicionDiscapacidad  = $_POST['condicionDiscapacidad'] ?? array();
            $grupoEtnico            = $_POST['grupoEtnico'] ?? array();
            $orientacionSexual      = $_POST['orientacionSexual'] ?? array();
            $nivelEducativo         = $_POST['nivelEducativo'] ?? array();
            $tipoDiscapacidad       = $_POST['tipoDiscapacidad'] ?? array();
            $victima                = $_POST['victima'] ?? array();
            $mujerGestante          = $_POST['mujerGestante'] ?? array();
            $cabezaFamilia          = $_POST['cabezaFamilia'] ?? array();
            $experienciaMigratoria  = $_POST['experienciaMigratoria'] ?? array();
            $seguridadSalud         = $_POST['seguridadSalud'] ?? array();
            $condicionOcupacion     = $_POST['condicionOcupacion'] ?? array();
            
            $estado_integVenta      = 1;
            $fecha_alta_integVenta  = date('Y-m-d H:i:s');
            $fecha_edit_integVenta  = date('Y-m-d H:i:s'); // Marcar como editado
            
            $rango_edad_map = array(
                "0 - 6"                 => 1,
                "7 - 12"                => 2,
                "13 - 17"               => 3,
                "18 - 28"               => 4,
                "29 - 45"               => 5,
                "46 - 64"               => 6,
                "Mayor o igual a 65"    => 7
            );

            // Insertar cada integrante
            foreach ($gen_integVenta as $key => $genero) {
                $cantidad           = $cant_integVenta[$key] ?? 1;
                $rango_descripcion  = $rango_integVenta[$key] ?? '';
                $rango_valor        = $rango_edad_map[$rango_descripcion] ?? 'NULL';
                $discapacidad       = $condicionDiscapacidad[$key] ?? '';
                $grupo              = $grupoEtnico[$key] ?? '';
                $orientacion        = $orientacionSexual[$key] ?? '';
                $educacion          = $nivelEducativo[$key] ?? '';
                $tipo_discapacidad  = $tipoDiscapacidad[$key] ?? '';
                $es_victima         = $victima[$key] ?? '';
                $es_gestante        = $mujerGestante[$key] ?? '';
                $es_cabeza_familia  = $cabezaFamilia[$key] ?? '';
                $experiencia_migr   = $experienciaMigratoria[$key] ?? '';
                $seguridad_social   = $seguridadSalud[$key] ?? '';
                $cond_ocupacion     = $condicionOcupacion[$key] ?? '';

                // Escape de strings para evitar inyección SQL
                $genero = $mysqli->real_escape_string($genero);
                $discapacidad = $mysqli->real_escape_string($discapacidad);
                $grupo = $mysqli->real_escape_string($grupo);
                $orientacion = $mysqli->real_escape_string($orientacion);
                $educacion = $mysqli->real_escape_string($educacion);
                $tipo_discapacidad = $mysqli->real_escape_string($tipo_discapacidad);
                $es_victima = $mysqli->real_escape_string($es_victima);
                $es_gestante = $mysqli->real_escape_string($es_gestante);
                $es_cabeza_familia = $mysqli->real_escape_string($es_cabeza_familia);
                $experiencia_migr = $mysqli->real_escape_string($experiencia_migr);
                $seguridad_social = $mysqli->real_escape_string($seguridad_social);
                $cond_ocupacion = $mysqli->real_escape_string($cond_ocupacion);                $sql_integrante = "INSERT INTO integventanilla 
                    (cant_integVenta, gen_integVenta, rango_integVenta, condicionDiscapacidad, grupoEtnico, 
                     orientacionSexual, nivelEducativo, tipoDiscapacidad, victima, 
                     mujerGestante, cabezaFamilia, experienciaMigratoria, seguridadSalud, condicionOcupacion, 
                     estado_integVenta, fecha_alta_integVenta, fecha_edit_integVenta, id_usu, id_movimiento)
                    VALUES ('$cantidad', '$genero', '$rango_valor', '$discapacidad', '$grupo', '$orientacion', 
                            '$educacion', '$tipo_discapacidad', '$es_victima', '$es_gestante', 
                            '$es_cabeza_familia', '$experiencia_migr', '$seguridad_social', '$cond_ocupacion', 
                            '$estado_integVenta', '$fecha_alta_integVenta', '$fecha_edit_integVenta', '$id_usu', '$id_movimiento')";
                
                if (!$mysqli->query($sql_integrante)) {
                    throw new Exception("Error al insertar integrante $key: " . $mysqli->error);
                }
            }        }

        // Ya no necesitamos registrar movimiento separado porque el movimiento YA se creó arriba
        // con toda la información completa

        // Confirmar transacción
        $mysqli->commit();
        $mysqli->autocommit(TRUE);

        // Mostrar página de éxito
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
                        <h3><b><i class='fas fa-check-circle'></i> SE ACTUALIZÓ DE FORMA EXITOSA LA ENCUESTA</b></h3>
                        <p><b>Movimiento registrado:</b> $movimientos</p>
                        <br />
                        <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                    </div>
                </center>
            </body>
        </html>";

    } catch (Exception $e) {
        // Rollback en caso de error
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        
        echo "
        <!DOCTYPE html>
        <html lang='es'>
            <head>
                <meta charset='utf-8' />
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                <link rel='stylesheet' href='../../css/bootstrap.min.css'>
                <title>Error - BD SISBEN</title>
            </head>
            <body>
                <center>
                    <div class='container'>
                        <br />
                        <h3><b><i class='fas fa-times-circle'></i> ERROR AL ACTUALIZAR LA ENCUESTA</b></h3>
                        <p class='text-danger'>" . $e->getMessage() . "</p>
                        <br />
                        <p align='center'><a href='movimientosEncuesta.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                    </div>
                </center>
            </body>
        </html>";
    }
} else {
    header("Location: movimientosEncuesta.php");
    exit();
}
?>
