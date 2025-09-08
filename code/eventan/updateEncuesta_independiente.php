<?php
/**
 * UPDATE ENCUESTA - VERSIÓN INDEPENDIENTE
 * 
 * Este archivo ahora funciona completamente independiente de la tabla encventanilla.
 * Todos los datos se almacenan y gestionan únicamente en la tabla movimientos.
 * 
 * Características:
 * - No depende de encventanilla para funcionar
 * - Almacena toda la información en movimientos
 * - Guarda integrantes en tabla independiente
 * - Mantiene historial completo de movimientos
 */

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
        $fec_reg_encVenta        = $_POST['fec_reg_encVenta'] ?? date('Y-m-d');
        $doc_encVenta            = $_POST['doc_encVenta'];
        $tipo_documento          = $_POST['tipo_documento'];
        $fecha_expedicion        = $_POST['fecha_expedicion'];
    $fecha_nacimiento        = $_POST['fecha_nacimiento'] ?? NULL;
        $departamento_expedicion = $_POST['departamento_expedicion'];
        $ciudad_expedicion       = $_POST['ciudad_expedicion'];
        $nom_encVenta           = mb_strtoupper($_POST['nom_encVenta']);
        $dir_encVenta           = mb_strtoupper($_POST['dir_encVenta']);
        $zona_encVenta          = $_POST['zona_encVenta'];
        $id_com                 = $_POST['id_com'];
        $id_bar                 = $_POST['id_bar'];
        $otro_bar_ver_encVenta  = mb_strtoupper($_POST['otro_bar_ver_encVenta']);
        $movimientos            = $_POST['movimientos']; // Tipo de movimiento
        $integra_encVenta       = $_POST['integra_encVenta'] ?? 0;
        $sisben_nocturno        = $_POST['sisben_nocturno'];
        $num_ficha_encVenta     = $_POST['num_ficha_encVenta'];
        $obs_encVenta           = mb_strtoupper($_POST['obs_encVenta']);
        $fecha_movimiento       = date('Y-m-d H:i:s');
        $id_usu                 = $_SESSION['id_usu'];

        // Determinar el estado de la ficha según el tipo de movimiento
        $estado_ficha = ($movimientos == "Retiro ficha") ? 0 : 1; // 0 = retirada, 1 = activa

        // VERIFICAR SI YA EXISTE UN REGISTRO PARA ESTE DOCUMENTO
        $sql_check = "SELECT * FROM movimientos WHERE doc_encVenta = '$doc_encVenta' ORDER BY fecha_movimiento DESC LIMIT 1";
        $result_check = $mysqli->query($sql_check);
        
        $registro_existe = ($result_check->num_rows > 0);
        $id_movimiento_nuevo = null;
        
        if ($registro_existe) {
            $registro_anterior = $result_check->fetch_assoc();
            
            // ACTUALIZAR REGISTRO EXISTENTE CON NUEVA INFORMACIÓN
            $sql_update = "UPDATE movimientos SET 
                nom_encVenta = '$nom_encVenta',
                fec_reg_encVenta = '$fec_reg_encVenta',
                tipo_documento = '$tipo_documento',
                fecha_nacimiento = " . ($fecha_nacimiento ? "'" . $mysqli->real_escape_string($fecha_nacimiento) . "'" : "NULL") . ",
                departamento_expedicion = '$departamento_expedicion',
                ciudad_expedicion = '$ciudad_expedicion',
                fecha_expedicion = '$fecha_expedicion',
                dir_encVenta = '$dir_encVenta',
                zona_encVenta = '$zona_encVenta',
                id_com = '$id_com',
                id_bar = '$id_bar',
                otro_bar_ver_encVenta = '$otro_bar_ver_encVenta',
                integra_encVenta = '$integra_encVenta',
                num_ficha_encVenta = '$num_ficha_encVenta',
                sisben_nocturno = '$sisben_nocturno',
                estado_ficha = '$estado_ficha',
                fecha_edit_movimiento = '$fecha_movimiento'
                WHERE doc_encVenta = '$doc_encVenta' 
                ORDER BY fecha_movimiento DESC LIMIT 1";

            if (!$mysqli->query($sql_update)) {
                throw new Exception("Error al actualizar el registro existente: " . $mysqli->error);
            }
            
            // Crear NUEVO REGISTRO de movimiento para mantener historial
            $sql_new_movement = "INSERT INTO movimientos (
                doc_encVenta, nom_encVenta, fec_reg_encVenta, tipo_documento,
                fecha_nacimiento,
                departamento_expedicion, ciudad_expedicion, fecha_expedicion,
                dir_encVenta, zona_encVenta, id_com, id_bar, otro_bar_ver_encVenta,
                integra_encVenta, num_ficha_encVenta, sisben_nocturno, estado_ficha,
                tipo_movimiento, fecha_movimiento, observacion, id_usu,
                fecha_alta_movimiento, fecha_edit_movimiento
            ) VALUES (
                '$doc_encVenta', '$nom_encVenta', '$fec_reg_encVenta', '$tipo_documento',
                " . ($fecha_nacimiento ? "'" . $mysqli->real_escape_string($fecha_nacimiento) . "'" : "NULL") . ",
                '$departamento_expedicion', '$ciudad_expedicion', '$fecha_expedicion',
                '$dir_encVenta', '$zona_encVenta', '$id_com', '$id_bar', '$otro_bar_ver_encVenta',
                '$integra_encVenta', '$num_ficha_encVenta', '$sisben_nocturno', '$estado_ficha',
                '$movimientos', '$fecha_movimiento', '$obs_encVenta', '$id_usu',
                '$fecha_movimiento', NULL
            )";

            if ($mysqli->query($sql_new_movement)) {
                $id_movimiento_nuevo = $mysqli->insert_id;
            } else {
                throw new Exception("Error al crear el nuevo movimiento: " . $mysqli->error);
            }
            
        } else {
            // CREAR PRIMER REGISTRO PARA ESTE DOCUMENTO
            $sql_insert = "INSERT INTO movimientos (
                doc_encVenta, nom_encVenta, fec_reg_encVenta, tipo_documento,
                fecha_nacimiento,
                departamento_expedicion, ciudad_expedicion, fecha_expedicion,
                dir_encVenta, zona_encVenta, id_com, id_bar, otro_bar_ver_encVenta,
                integra_encVenta, num_ficha_encVenta, sisben_nocturno, estado_ficha,
                tipo_movimiento, fecha_movimiento, observacion, id_usu,
                fecha_alta_movimiento, fecha_edit_movimiento
            ) VALUES (
                '$doc_encVenta', '$nom_encVenta', '$fec_reg_encVenta', '$tipo_documento',
                " . ($fecha_nacimiento ? "'" . $mysqli->real_escape_string($fecha_nacimiento) . "'" : "NULL") . ",
                '$departamento_expedicion', '$ciudad_expedicion', '$fecha_expedicion',
                '$dir_encVenta', '$zona_encVenta', '$id_com', '$id_bar', '$otro_bar_ver_encVenta',
                '$integra_encVenta', '$num_ficha_encVenta', '$sisben_nocturno', '$estado_ficha',
                '$movimientos', '$fecha_movimiento', '$obs_encVenta', '$id_usu',
                '$fecha_movimiento', NULL
            )";

            if ($mysqli->query($sql_insert)) {
                $id_movimiento_nuevo = $mysqli->insert_id;
            } else {
                throw new Exception("Error al crear el registro: " . $mysqli->error);
            }
        }

        // MANEJO DE INTEGRANTES - Solo si no es "Retiro ficha"
        if ($movimientos != "Retiro ficha" && isset($_POST['gen_integVenta']) && is_array($_POST['gen_integVenta'])) {
            
            // Limpiar integrantes existentes para este documento
            $sql_delete_integrantes = "DELETE FROM integmovimientos_independiente WHERE doc_encVenta = '$doc_encVenta'";
            $mysqli->query($sql_delete_integrantes);
            
            // Obtener datos de integrantes
            $gen_integVenta         = $_POST['gen_integVenta'] ?? array();
            $rango_integVenta       = $_POST['rango_integVenta'] ?? array();
            $orientacionSexual      = $_POST['orientacionSexual'] ?? array();
            $condicionDiscapacidad  = $_POST['condicionDiscapacidad'] ?? array();
            $tipoDiscapacidad       = $_POST['tipoDiscapacidad'] ?? array();
            $grupoEtnico            = $_POST['grupoEtnico'] ?? array();
            $victima                = $_POST['victima'] ?? array();
            $mujerGestante          = $_POST['mujerGestante'] ?? array();
            $cabezaFamilia          = $_POST['cabezaFamilia'] ?? array();
            $experienciaMigratoria  = $_POST['experienciaMigratoria'] ?? array();
            $seguridadSalud         = $_POST['seguridadSalud'] ?? array();
            $nivelEducativo         = $_POST['nivelEducativo'] ?? array();
            $condicionOcupacion     = $_POST['condicionOcupacion'] ?? array();
            
            $total_integrantes = 0;
            
            // Insertar cada integrante en la tabla independiente
            foreach ($gen_integVenta as $key => $genero) {
                if (!empty($genero)) {
                    $cantidad           = 1; // Siempre 1 por integrante
                    $rango_descripcion  = $rango_integVenta[$key] ?? '';
                    $orientacion        = $orientacionSexual[$key] ?? '';
                    $discapacidad       = $condicionDiscapacidad[$key] ?? '';
                    $tipo_disc          = $tipoDiscapacidad[$key] ?? '';
                    $grupo              = $grupoEtnico[$key] ?? '';
                    $es_victima         = $victima[$key] ?? '';
                    $es_gestante        = $mujerGestante[$key] ?? '';
                    $es_cabeza_familia  = $cabezaFamilia[$key] ?? '';
                    $experiencia_migr   = $experienciaMigratoria[$key] ?? '';
                    $seguridad_social   = $seguridadSalud[$key] ?? '';
                    $educacion          = $nivelEducativo[$key] ?? '';
                    $ocupacion          = $condicionOcupacion[$key] ?? '';

                    // Escapar strings
                    $genero = $mysqli->real_escape_string($genero);
                    $rango_descripcion = $mysqli->real_escape_string($rango_descripcion);
                    $orientacion = $mysqli->real_escape_string($orientacion);
                    $discapacidad = $mysqli->real_escape_string($discapacidad);
                    $tipo_disc = $mysqli->real_escape_string($tipo_disc);
                    $grupo = $mysqli->real_escape_string($grupo);
                    $es_victima = $mysqli->real_escape_string($es_victima);
                    $es_gestante = $mysqli->real_escape_string($es_gestante);
                    $es_cabeza_familia = $mysqli->real_escape_string($es_cabeza_familia);
                    $experiencia_migr = $mysqli->real_escape_string($experiencia_migr);
                    $seguridad_social = $mysqli->real_escape_string($seguridad_social);
                    $educacion = $mysqli->real_escape_string($educacion);
                    $ocupacion = $mysqli->real_escape_string($ocupacion);

                    $sql_integrante = "INSERT INTO integmovimientos_independiente (
                        id_movimiento, doc_encVenta, cant_integMovIndep, gen_integMovIndep, rango_integMovIndep,
                        orientacionSexual, condicionDiscapacidad, tipoDiscapacidad, grupoEtnico,
                        victima, mujerGestante, cabezaFamilia, experienciaMigratoria,
                        seguridadSalud, nivelEducativo, condicionOcupacion,
                        estado_integMovIndep, fecha_alta_integMovIndep, id_usu
                    ) VALUES (
                        '$id_movimiento_nuevo', '$doc_encVenta', '$cantidad', '$genero', '$rango_descripcion',
                        '$orientacion', '$discapacidad', '$tipo_disc', '$grupo',
                        '$es_victima', '$es_gestante', '$es_cabeza_familia', '$experiencia_migr',
                        '$seguridad_social', '$educacion', '$ocupacion',
                        1, '$fecha_movimiento', '$id_usu'
                    )";
                    
                    if ($mysqli->query($sql_integrante)) {
                        $total_integrantes++;
                    } else {
                        throw new Exception("Error al insertar integrante $key: " . $mysqli->error);
                    }
                }
            }

            // Actualizar el total de integrantes en el registro principal
            $sql_update_total = "UPDATE movimientos SET integra_encVenta = '$total_integrantes' 
                                WHERE id_movimiento = '$id_movimiento_nuevo'";
            $mysqli->query($sql_update_total);
        }

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
                <title>BD SISBEN - Éxito</title>
                <style>
                    .responsive {
                        max-width: 100%;
                        height: auto;
                    }
                    .success-container {
                        background: linear-gradient(135deg, #4CAF50, #45a049);
                        color: white;
                        padding: 20px;
                        border-radius: 10px;
                        margin: 20px 0;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                    }
                    .info-box {
                        background: #f8f9fa;
                        border: 1px solid #dee2e6;
                        border-radius: 5px;
                        padding: 15px;
                        margin: 10px 0;
                        color: #333;
                    }
                </style>
            </head>
            <body>
                <center>
                    <img src='../../img/sisben.png' width=300 height=185 class='responsive'>
                    <div class='container'>
                        <div class='success-container'>
                            <h3><i class='fas fa-check-circle'></i> MOVIMIENTO REGISTRADO EXITOSAMENTE</h3>
                            <p><strong>Sistema Independiente SISBEN</strong></p>
                        </div>
                        
                        <div class='info-box'>
                            <h5><i class='fas fa-info-circle'></i> Detalles del Movimiento</h5>
                            <p><strong>Documento:</strong> $doc_encVenta</p>
                            <p><strong>Nombre:</strong> $nom_encVenta</p>
                            <p><strong>Tipo de Movimiento:</strong> $movimientos</p>
                            <p><strong>Fecha y Hora:</strong> $fecha_movimiento</p>
                            <p><strong>Estado de Ficha:</strong> " . ($estado_ficha == 1 ? 'ACTIVA' : 'RETIRADA') . "</p>
                            <p><strong>ID Movimiento:</strong> #$id_movimiento_nuevo</p>
                        </div>
                        
                        <div class='info-box'>
                            <h5><i class='fas fa-users'></i> Información de Integrantes</h5>
                            <p><strong>Total de Integrantes:</strong> " . ($total_integrantes ?? 0) . "</p>
                            <p><em>Los integrantes se almacenaron en el sistema independiente.</em></p>
                        </div>
                        
                        <div style='margin-top: 30px;'>
                            <a href='movimientosEncuesta.php' class='btn btn-primary'>
                                <i class='fas fa-plus'></i> Nuevo Movimiento
                            </a>
                            <a href='../../access.php' class='btn btn-success'>
                                <i class='fas fa-home'></i> Ir al Menú Principal
                            </a>
                        </div>
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
                <style>
                    .responsive {
                        max-width: 100%;
                        height: auto;
                    }
                    .error-container {
                        background: linear-gradient(135deg, #f44336, #d32f2f);
                        color: white;
                        padding: 20px;
                        border-radius: 10px;
                        margin: 20px 0;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                    }
                </style>
            </head>
            <body>
                <center>
                    <img src='../../img/sisben.png' width=300 height=185 class='responsive'>
                    <div class='container'>
                        <div class='error-container'>
                            <h3><i class='fas fa-times-circle'></i> ERROR AL PROCESAR EL MOVIMIENTO</h3>
                            <p><strong>Error:</strong> " . $e->getMessage() . "</p>
                            <p><em>Los cambios fueron revertidos. El sistema está en estado seguro.</em></p>
                        </div>
                        <div style='margin-top: 30px;'>
                            <a href='movimientosEncuesta.php' class='btn btn-primary'>
                                <i class='fas fa-arrow-left'></i> Volver al Formulario
                            </a>
                            <a href='../../access.php' class='btn btn-secondary'>
                                <i class='fas fa-home'></i> Ir al Menú Principal
                            </a>
                        </div>
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
