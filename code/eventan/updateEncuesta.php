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
        $id_usu                 = $_SESSION['id_usu'];        // Obtener el ID de la encuesta existente
        $sql_get_id = "SELECT id_encVenta FROM encventanilla WHERE doc_encVenta = '$doc_encVenta'";
        $result_id = $mysqli->query($sql_get_id);
        
        if ($result_id->num_rows == 0) {
            throw new Exception("No se encontró la encuesta para actualizar");
        }
        
        $row_id = $result_id->fetch_assoc();
        $id_encVenta = $row_id['id_encVenta'];

        // Determinar el estado de la ficha según el tipo de movimiento
        $estado_ficha = ($movimientos == "Retiro ficha") ? 0 : 1; // 0 = retirada, 1 = activa

        // Actualizar la tabla encventanilla
        $sql_update = "UPDATE encventanilla SET 
            fec_reg_encVenta = '$fec_reg_encVenta',
            nom_encVenta = '$nom_encVenta',
            dir_encVenta = '$dir_encVenta',
            zona_encVenta = '$zona_encVenta',
            id_com = '$id_com',
            id_bar = '$id_bar',
            otro_bar_ver_encVenta = '$otro_bar_ver_encVenta',
            tram_solic_encVenta = '$movimientos',
            integra_encVenta = '$integra_encVenta',
            num_ficha_encVenta = '$num_ficha_encVenta',
            obs_encVenta = '$obs_encVenta',
            fecha_edit_encVenta = '$fecha_edit_encVenta',
            tipo_documento = '$tipo_documento',
            fecha_expedicion = '$fecha_expedicion',
            departamento_expedicion = '$departamento_expedicion',
            ciudad_expedicion = '$ciudad_expedicion',
            sisben_nocturno = '$sisben_nocturno',
            estado_ficha = '$estado_ficha'
            WHERE doc_encVenta = '$doc_encVenta'";        if (!$mysqli->query($sql_update)) {
            throw new Exception("Error al actualizar la encuesta: " . $mysqli->error);
        }

        // MANEJO INTELIGENTE DE INTEGRANTES
        // Solo procesar integrantes si no es "Retiro ficha" (para ficha retirada no se modifican integrantes)
        if ($movimientos != "Retiro ficha") {
            // Obtener IDs de integrantes existentes para comparar
            $sql_existing = "SELECT id_integVenta FROM integventanilla WHERE id_encVenta = '$id_encVenta'";
            $result_existing = $mysqli->query($sql_existing);
            $existing_ids = [];
            while ($row = $result_existing->fetch_assoc()) {
                $existing_ids[] = $row['id_integVenta'];
            }

            // Eliminar SOLO los integrantes existentes (mantendremos los nuevos por separado)
            if (!empty($existing_ids)) {
                $ids_to_delete = implode(',', $existing_ids);
                $sql_delete_integrantes = "DELETE FROM integventanilla WHERE id_integVenta IN ($ids_to_delete)";
                if (!$mysqli->query($sql_delete_integrantes)) {
                    throw new Exception("Error al eliminar integrantes existentes: " . $mysqli->error);
                }
            }

            // Insertar todos los integrantes del formulario (tanto existentes modificados como nuevos)
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
                $cond_ocupacion = $mysqli->real_escape_string($cond_ocupacion);

                $sql_integrante = "INSERT INTO integventanilla 
                    (cant_integVenta, gen_integVenta, rango_integVenta, condicionDiscapacidad, grupoEtnico, 
                     orientacionSexual, nivelEducativo, tipoDiscapacidad, victima, 
                     mujerGestante, cabezaFamilia, experienciaMigratoria, seguridadSalud, condicionOcupacion, 
                     estado_integVenta, fecha_alta_integVenta, fecha_edit_integVenta, id_usu, id_encVenta)
                    VALUES ('$cantidad', '$genero', '$rango_valor', '$discapacidad', '$grupo', '$orientacion', 
                            '$educacion', '$tipo_discapacidad', '$es_victima', '$es_gestante', 
                            '$es_cabeza_familia', '$experiencia_migr', '$seguridad_social', '$cond_ocupacion', 
                            '$estado_integVenta', '$fecha_alta_integVenta', '$fecha_edit_integVenta', '$id_usu', '$id_encVenta')";
                
                if (!$mysqli->query($sql_integrante)) {
                    throw new Exception("Error al insertar integrante $key: " . $mysqli->error);
                }
            }
        }

        // Registrar movimiento en la tabla movimientos
        $fecha_movimiento = date('Y-m-d H:i:s');
          // Determinar qué campo incrementar según el tipo de movimiento
        $campo_incrementar = "";
        switch($movimientos) {
            case "inclusion":
                $campo_incrementar = "inclusion";
                break;
            case "Inconformidad por clasificacion":
                $campo_incrementar = "inconfor_clasificacion";
                break;
            case "modificación datos persona":
                $campo_incrementar = "datos_persona";
                break;
            case "Retiro ficha":
                $campo_incrementar = "retiro_ficha";
                break;
            case "Retiro personas":
                $campo_incrementar = "retiro_personas";
                break;
            default:
                $campo_incrementar = "inclusion"; // Por defecto
        }

        // Verificar si ya existe un registro para este documento
        $sql_check_movimiento = "SELECT id_movimiento FROM movimientos WHERE doc_encVenta = '$doc_encVenta'";
        $result_check = $mysqli->query($sql_check_movimiento);

        if ($result_check->num_rows > 0) {
            // Si existe, actualizar incrementando el campo correspondiente
            $sql_movimiento = "UPDATE movimientos SET 
                $campo_incrementar = $campo_incrementar + 1,
                cantidad_encuesta = cantidad_encuesta + 1,
                observacion = '$obs_encVenta',
                id_usu = '$id_usu'
                WHERE doc_encVenta = '$doc_encVenta'";        } else {
            // Si no existe, crear nuevo registro - omitiendo id_informacion si no puede ser NULL
            $sql_movimiento = "INSERT INTO movimientos 
                (inclusion, inconfor_clasificacion, datos_persona, retiro_ficha, retiro_personas, 
                 retiro_personas_inconformidad, cantidad_informacion, cantidad_encuesta, 
                 id_encuesta, doc_encVenta, observacion, id_usu)
                VALUES (
                    " . ($campo_incrementar == 'inclusion' ? 1 : 0) . ",
                    " . ($campo_incrementar == 'inconfor_clasificacion' ? 1 : 0) . ",
                    " . ($campo_incrementar == 'datos_persona' ? 1 : 0) . ",
                    " . ($campo_incrementar == 'retiro_ficha' ? 1 : 0) . ",
                    " . ($campo_incrementar == 'retiro_personas' ? 1 : 0) . ",
                    0, 0, 1, '$id_encVenta', '$doc_encVenta', '$obs_encVenta', '$id_usu'
                )";
        }

        if (!$mysqli->query($sql_movimiento)) {
            throw new Exception("Error al registrar movimiento: " . $mysqli->error);
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
