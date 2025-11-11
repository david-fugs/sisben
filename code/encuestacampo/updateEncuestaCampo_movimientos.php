<?php
/**
 * UPDATE ENCUESTA CAMPO - SISTEMA DE MOVIMIENTOS
 * 
 * Funciona de manera independiente guardando todos los movimientos en:
 * - movimientos_encuesta_campo (datos principales)
 * - integ_movimientos_encuesta_campo (integrantes)
 * 
 * Mantiene historial completo de cambios
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
        $fec_reg_encCampo        = $_POST['fec_reg_encCampo'] ?? date('Y-m-d');
        $doc_encCampo            = mysqli_real_escape_string($mysqli, $_POST['doc_encCampo']);
        $tipo_documento          = mysqli_real_escape_string($mysqli, $_POST['tipo_documento']);
        $fecha_expedicion        = mysqli_real_escape_string($mysqli, $_POST['fecha_expedicion']);
        $fecha_nacimiento        = isset($_POST['fecha_nacimiento']) && $_POST['fecha_nacimiento'] != '' ? mysqli_real_escape_string($mysqli, $_POST['fecha_nacimiento']) : NULL;
        $departamento_expedicion = mysqli_real_escape_string($mysqli, $_POST['departamento_expedicion']);
        $ciudad_expedicion       = mysqli_real_escape_string($mysqli, $_POST['ciudad_expedicion']);
        $nom_encCampo           = mb_strtoupper(mysqli_real_escape_string($mysqli, $_POST['nom_encCampo']));
        $dir_encCampo           = mb_strtoupper(mysqli_real_escape_string($mysqli, $_POST['dir_encCampo']));
        $zona_encCampo          = mysqli_real_escape_string($mysqli, $_POST['zona_encCampo']);
        $id_com                 = mysqli_real_escape_string($mysqli, $_POST['id_com']);
        $id_bar                 = mysqli_real_escape_string($mysqli, $_POST['id_bar']);
        $otro_bar_ver_encCampo  = mb_strtoupper(mysqli_real_escape_string($mysqli, $_POST['otro_bar_ver_encCampo'] ?? ''));
        $movimientos            = mysqli_real_escape_string($mysqli, $_POST['movimientos']); // Tipo de movimiento
        $integra_encCampo       = intval($_POST['integra_encCampo'] ?? 0);
        $num_ficha_encCampo     = mysqli_real_escape_string($mysqli, $_POST['num_ficha_encCampo']);
        $obs_encCampo           = mb_strtoupper(mysqli_real_escape_string($mysqli, $_POST['obs_encCampo'] ?? ''));
        $fecha_movimiento       = date('Y-m-d H:i:s');
        $id_usu                 = $_SESSION['id_usu'];

        // Determinar el estado de la ficha según el tipo de movimiento
        $estado_ficha = ($movimientos == "Retiro ficha") ? 0 : 1; // 0 = retirada, 1 = activa

        // VERIFICAR SI YA EXISTE UN REGISTRO PARA ESTE DOCUMENTO EN MOVIMIENTOS
        $sql_check = "SELECT * FROM movimientos_encuesta_campo 
                     WHERE doc_encCampo = '$doc_encCampo' 
                     ORDER BY fecha_movimiento DESC LIMIT 1";
        $result_check = $mysqli->query($sql_check);
        
        $registro_existe = ($result_check && $result_check->num_rows > 0);
        $id_movimiento_nuevo = null;
        
        if ($registro_existe) {
            $registro_anterior = $result_check->fetch_assoc();
            
            // ACTUALIZAR REGISTRO EXISTENTE CON NUEVA INFORMACIÓN
            $sql_update = "UPDATE movimientos_encuesta_campo SET 
                nom_encCampo = '$nom_encCampo',
                fec_reg_encCampo = '$fec_reg_encCampo',
                tipo_documento = '$tipo_documento',
                fecha_nacimiento = " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . ",
                departamento_expedicion = '$departamento_expedicion',
                ciudad_expedicion = '$ciudad_expedicion',
                fecha_expedicion = '$fecha_expedicion',
                dir_encCampo = '$dir_encCampo',
                zona_encCampo = '$zona_encCampo',
                id_com = '$id_com',
                id_bar = '$id_bar',
                otro_bar_ver_encCampo = '$otro_bar_ver_encCampo',
                integra_encCampo = '$integra_encCampo',
                num_ficha_encCampo = '$num_ficha_encCampo',
                estado_ficha = '$estado_ficha',
                fecha_edit_movimiento = '$fecha_movimiento'
                WHERE doc_encCampo = '$doc_encCampo' 
                ORDER BY fecha_movimiento DESC LIMIT 1";

            if (!$mysqli->query($sql_update)) {
                throw new Exception("Error al actualizar el registro existente: " . $mysqli->error);
            }
            
            // Crear NUEVO REGISTRO de movimiento para mantener historial
            $sql_new_movement = "INSERT INTO movimientos_encuesta_campo (
                doc_encCampo, nom_encCampo, tipo_documento,
                fecha_expedicion, fecha_nacimiento,
                departamento_expedicion, ciudad_expedicion,
                dir_encCampo, zona_encCampo, id_com, id_bar, otro_bar_ver_encCampo,
                num_ficha_encCampo, integra_encCampo, fec_reg_encCampo,
                tipo_movimiento, obs_encCampo, estado_ficha,
                fecha_movimiento, fecha_alta_movimiento, id_usu
            ) VALUES (
                '$doc_encCampo', '$nom_encCampo', '$tipo_documento',
                '$fecha_expedicion', " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . ",
                '$departamento_expedicion', '$ciudad_expedicion',
                '$dir_encCampo', '$zona_encCampo', '$id_com', '$id_bar', '$otro_bar_ver_encCampo',
                '$num_ficha_encCampo', '$integra_encCampo', '$fec_reg_encCampo',
                '$movimientos', '$obs_encCampo', '$estado_ficha',
                '$fecha_movimiento', '$fecha_movimiento', '$id_usu'
            )";

            if ($mysqli->query($sql_new_movement)) {
                $id_movimiento_nuevo = $mysqli->insert_id;
            } else {
                throw new Exception("Error al crear el nuevo movimiento: " . $mysqli->error);
            }
            
        } else {
            // CREAR PRIMER REGISTRO PARA ESTE DOCUMENTO
            $sql_insert = "INSERT INTO movimientos_encuesta_campo (
                doc_encCampo, nom_encCampo, tipo_documento,
                fecha_expedicion, fecha_nacimiento,
                departamento_expedicion, ciudad_expedicion,
                dir_encCampo, zona_encCampo, id_com, id_bar, otro_bar_ver_encCampo,
                num_ficha_encCampo, integra_encCampo, fec_reg_encCampo,
                tipo_movimiento, obs_encCampo, estado_ficha,
                fecha_movimiento, fecha_alta_movimiento, id_usu
            ) VALUES (
                '$doc_encCampo', '$nom_encCampo', '$tipo_documento',
                '$fecha_expedicion', " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . ",
                '$departamento_expedicion', '$ciudad_expedicion',
                '$dir_encCampo', '$zona_encCampo', '$id_com', '$id_bar', '$otro_bar_ver_encCampo',
                '$num_ficha_encCampo', '$integra_encCampo', '$fec_reg_encCampo',
                '$movimientos', '$obs_encCampo', '$estado_ficha',
                '$fecha_movimiento', '$fecha_movimiento', '$id_usu'
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
            $sql_delete_integrantes = "DELETE FROM integ_movimientos_encuesta_campo WHERE doc_encCampo = '$doc_encCampo'";
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
            
            // Insertar cada integrante en la tabla
            foreach ($gen_integVenta as $key => $genero) {
                if (!empty($genero)) {
                    $rango_descripcion  = $mysqli->real_escape_string($rango_integVenta[$key] ?? '');
                    $orientacion        = $mysqli->real_escape_string($orientacionSexual[$key] ?? '');
                    $discapacidad       = $mysqli->real_escape_string($condicionDiscapacidad[$key] ?? '');
                    $tipo_disc          = $mysqli->real_escape_string($tipoDiscapacidad[$key] ?? '');
                    $grupo              = $mysqli->real_escape_string($grupoEtnico[$key] ?? '');
                    $es_victima         = $mysqli->real_escape_string($victima[$key] ?? '');
                    $es_gestante        = $mysqli->real_escape_string($mujerGestante[$key] ?? '');
                    $es_cabeza_familia  = $mysqli->real_escape_string($cabezaFamilia[$key] ?? '');
                    $experiencia_migr   = $mysqli->real_escape_string($experienciaMigratoria[$key] ?? '');
                    $seguridad_social   = $mysqli->real_escape_string($seguridadSalud[$key] ?? '');
                    $educacion          = $mysqli->real_escape_string($nivelEducativo[$key] ?? '');
                    $ocupacion          = $mysqli->real_escape_string($condicionOcupacion[$key] ?? '');
                    $genero_escaped     = $mysqli->real_escape_string($genero);

                    $sql_integrante = "INSERT INTO integ_movimientos_encuesta_campo (
                        doc_encCampo, id_movimiento,
                        gen_integCampo, rango_integCampo, orientacionSexual, 
                        condicionDiscapacidad, tipoDiscapacidad, grupoEtnico,
                        victima, mujerGestante, cabezaFamilia, experienciaMigratoria,
                        seguridadSalud, nivelEducativo, condicionOcupacion,
                        fecha_registro
                    ) VALUES (
                        '$doc_encCampo', '$id_movimiento_nuevo',
                        '$genero_escaped', '$rango_descripcion', '$orientacion',
                        '$discapacidad', '$tipo_disc', '$grupo',
                        '$es_victima', '$es_gestante', '$es_cabeza_familia', '$experiencia_migr',
                        '$seguridad_social', '$educacion', '$ocupacion',
                        '$fecha_movimiento'
                    )";
                    
                    if ($mysqli->query($sql_integrante)) {
                        $total_integrantes++;
                    } else {
                        throw new Exception("Error al insertar integrante $key: " . $mysqli->error);
                    }
                }
            }

            // Actualizar el total de integrantes en el registro principal
            $sql_update_total = "UPDATE movimientos_encuesta_campo 
                                SET integra_encCampo = '$total_integrantes' 
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
                <title>BD SISBEN - Movimiento Registrado</title>
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
                            <h3><i class='fas fa-check-circle'></i> MOVIMIENTO DE ENCUESTA CAMPO REGISTRADO</h3>
                            <p><strong>Sistema de Control de Movimientos - Encuestas de Campo</strong></p>
                        </div>
                        
                        <div class='info-box'>
                            <h5><i class='fas fa-info-circle'></i> Detalles del Movimiento</h5>
                            <p><strong>Documento:</strong> $doc_encCampo</p>
                            <p><strong>Nombre:</strong> $nom_encCampo</p>
                            <p><strong>Tipo de Movimiento:</strong> $movimientos</p>
                            <p><strong>Fecha y Hora:</strong> $fecha_movimiento</p>
                            <p><strong>Estado de Ficha:</strong> " . ($estado_ficha == 1 ? 'ACTIVA' : 'RETIRADA') . "</p>
                            <p><strong>ID Movimiento:</strong> #$id_movimiento_nuevo</p>
                            <p><strong>Número de Ficha:</strong> $num_ficha_encCampo</p>
                        </div>
                        
                        <div class='info-box'>
                            <h5><i class='fas fa-users'></i> Información de Integrantes</h5>
                            <p><strong>Total de Integrantes:</strong> " . ($total_integrantes ?? 0) . "</p>
                            <p><em>Los integrantes se registraron correctamente en el sistema de movimientos.</em></p>
                        </div>
                        
                        <div style='margin-top: 30px;'>
                            <a href='movimientosEncuestaCampo.php' class='btn btn-primary'>
                                <i class='fas fa-plus'></i> Nuevo Movimiento
                            </a>
                            <a href='showMovimientosCampo.php' class='btn btn-info'>
                                <i class='fas fa-list'></i> Ver Movimientos Campo
                            </a>
                            <a href='../../access.php' class='btn btn-success'>
                                <i class='fas fa-home'></i> Menú Principal
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
                <link href='../../fontawesome/css/all.css' rel='stylesheet'>
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
                            <a href='movimientosEncuestaCampo.php' class='btn btn-primary'>
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
    header("Location: movimientosEncuestaCampo.php");
    exit();
}

mysqli_close($mysqli);
?>
