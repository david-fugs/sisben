<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu = $_SESSION['id_usu'];
$tipo_usu = $_SESSION['tipo_usu'];

include("../../conexion.php");
date_default_timezone_set("America/Bogota");
header("Content-Type: text/html;charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_movimiento'])) {
    try {
        // Iniciar transacción
        $mysqli->autocommit(FALSE);

        $id_movimiento = $_POST['id_movimiento'];

        // Verificar que el movimiento existe y que el usuario tiene permisos
        $sql_verificar = "SELECT * FROM movimientos WHERE id_movimiento = '$id_movimiento'";
        if ($tipo_usu != '1') {
            $sql_verificar .= " AND id_usu = '$id_usu'";
        }

        $resultado_verificar = mysqli_query($mysqli, $sql_verificar);
        if (!$resultado_verificar || mysqli_num_rows($resultado_verificar) == 0) {
            throw new Exception("Movimiento no encontrado o sin permisos para editarlo.");
        }

        // Captura de datos enviados por POST
        $fec_reg_encVenta        = $_POST['fec_reg_encVenta'] ?? date('Y-m-d');
        $doc_encVenta            = $_POST['doc_encVenta'];
        $tipo_documento          = $_POST['tipo_documento'];
        $fecha_expedicion        = $_POST['fecha_expedicion'];
        $departamento_expedicion = $_POST['departamento_expedicion'];
        $ciudad_expedicion       = $_POST['ciudad_expedicion'];
        $nom_encVenta           = mb_strtoupper($_POST['nom_encVenta'], 'UTF-8');
        $dir_encVenta           = mb_strtoupper($_POST['dir_encVenta'], 'UTF-8');
        $zona_encVenta          = $_POST['zona_encVenta'];
        $id_com                 = $_POST['id_com'];
        $id_bar                 = $_POST['id_bar'];
        $otro_bar_ver_encVenta  = mb_strtoupper($_POST['otro_bar_ver_encVenta'] ?? '', 'UTF-8');
        $movimientos            = $_POST['movimientos']; // Tipo de movimiento
        $integra_encVenta       = $_POST['integra_encVenta'] ?? 0;
        $sisben_nocturno        = $_POST['sisben_nocturno'];
        $num_ficha_encVenta     = $_POST['num_ficha_encVenta'];
        $obs_encVenta           = mb_strtoupper($_POST['obs_encVenta'] ?? '', 'UTF-8');
        $fecha_edit_movimiento  = date('Y-m-d H:i:s');
        $fecha_nacimiento       = $_POST['fecha_nacimiento'] ?? null;

        // Determinar el estado de la ficha según el tipo de movimiento
        $estado_ficha = ($movimientos == "Retiro ficha") ? 0 : 1; // 0 = retirada, 1 = activa

        // Actualizar el movimiento
        $sql_update = "UPDATE movimientos SET 
            fec_reg_encVenta = '$fec_reg_encVenta',
            tipo_documento = '$tipo_documento',
            departamento_expedicion = '$departamento_expedicion',
            ciudad_expedicion = '$ciudad_expedicion',
            fecha_expedicion = '$fecha_expedicion',
            nom_encVenta = '$nom_encVenta',
            dir_encVenta = '$dir_encVenta',
            zona_encVenta = '$zona_encVenta',
            id_com = '$id_com',
            id_bar = '$id_bar',
            otro_bar_ver_encVenta = '$otro_bar_ver_encVenta',
            integra_encVenta = '$integra_encVenta',
            num_ficha_encVenta = '$num_ficha_encVenta',
            sisben_nocturno = '$sisben_nocturno',
            estado_ficha = '$estado_ficha',
            tipo_movimiento = '$movimientos',
            observacion = '$obs_encVenta',
            fecha_edit_movimiento = '$fecha_edit_movimiento',
            fecha_nacimiento = " . ($fecha_nacimiento ? "'$fecha_nacimiento'" : "NULL") . "
            WHERE id_movimiento = '$id_movimiento'";
        if (!$mysqli->query($sql_update)) {
            throw new Exception("Error al actualizar el movimiento: " . $mysqli->error);
        }

        // Procesar actualización de integrantes
        if (isset($_POST['id_integrante']) && is_array($_POST['id_integrante'])) {
            $ids_integrantes = $_POST['id_integrante'];
            $cant_integMovIndep = $_POST['cant_integMovIndep'] ?? [];
            $gen_integMovIndep = $_POST['gen_integMovIndep'] ?? [];
            $rango_integMovIndep = $_POST['rango_integMovIndep'] ?? [];
            $orientacionSexual = $_POST['orientacionSexual'] ?? [];
            $condicionDiscapacidad = $_POST['condicionDiscapacidad'] ?? [];
            $tipoDiscapacidad = $_POST['tipoDiscapacidad'] ?? [];
            $grupoEtnico = $_POST['grupoEtnico'] ?? [];
            $victima = $_POST['victima'] ?? [];
            $mujerGestante = $_POST['mujerGestante'] ?? [];
            $cabezaFamilia = $_POST['cabezaFamilia'] ?? [];
            $nivelEducativo = $_POST['nivelEducativo'] ?? [];

            // Mapeo de rangos de edad a valores numéricos
            $rango_edad_map = [
                "0 - 6" => 1,
                "7 - 12" => 2,
                "13 - 17" => 3,
                "18 - 28" => 4,
                "29 - 45" => 5,
                "46 - 64" => 6,
                "Mayor o igual a 65" => 7
            ];            foreach ($ids_integrantes as $index => $id_integrante) {
                // Cantidad siempre es 1 por integrante
                $cantidad = 1;
                $genero = $gen_integMovIndep[$index] ?? '';
                $rango_desc = $rango_integMovIndep[$index] ?? '';
                $rango_num = $rango_edad_map[$rango_desc] ?? 1;
                $orientacion = $orientacionSexual[$index] ?? '';
                $discapacidad = $condicionDiscapacidad[$index] ?? '';
                $tipo_disc = $tipoDiscapacidad[$index] ?? '';
                $grupo = $grupoEtnico[$index] ?? '';
                $es_victima = $victima[$index] ?? '';
                $gestante = $mujerGestante[$index] ?? '';
                $cabeza = $cabezaFamilia[$index] ?? '';
                $educacion = $nivelEducativo[$index] ?? '';

                if ($id_integrante === 'nuevo') {
                    // Insertar nuevo integrante
                    $sql_insert_integrante = "INSERT INTO integmovimientos_independiente 
                        (cant_integMovIndep, gen_integMovIndep, rango_integMovIndep, orientacionSexual, 
                         condicionDiscapacidad, tipoDiscapacidad, grupoEtnico, victima, mujerGestante, 
                         cabezaFamilia, nivelEducativo, doc_encVenta, estado_integMovIndep, 
                         fecha_alta_integMovIndep, fecha_edit_integMovIndep, id_usu) 
                        VALUES 
                        ('$cantidad', '$genero', '$rango_num', '$orientacion', '$discapacidad', 
                         '$tipo_disc', '$grupo', '$es_victima', '$gestante', '$cabeza', '$educacion', 
                         '$doc_encVenta', 1, NOW(), '0000-00-00 00:00:00', '$id_usu')";

                    if (!$mysqli->query($sql_insert_integrante)) {
                        throw new Exception("Error al insertar nuevo integrante: " . $mysqli->error);
                    }
                } else {
                    // Actualizar integrante existente
                    $sql_update_integrante = "UPDATE integmovimientos_independiente SET 
                        cant_integMovIndep = '$cantidad',
                        gen_integMovIndep = '$genero',
                        rango_integMovIndep = '$rango_num',
                        orientacionSexual = '$orientacion',
                        condicionDiscapacidad = '$discapacidad',
                        tipoDiscapacidad = '$tipo_disc',
                        grupoEtnico = '$grupo',
                        victima = '$es_victima',
                        mujerGestante = '$gestante',
                        cabezaFamilia = '$cabeza',
                        nivelEducativo = '$educacion',
                        fecha_edit_integMovIndep = NOW()
                        WHERE 	
id_integmov_indep = '$id_integrante' AND doc_encVenta = '$doc_encVenta'";

                    if (!$mysqli->query($sql_update_integrante)) {
                        throw new Exception("Error al actualizar integrante: " . $mysqli->error);
                    }
                }
            }            // Actualizar el contador de integrantes en el movimiento (cada integrante cuenta como 1)
            $total_integrantes = count($ids_integrantes);
            $sql_update_contador = "UPDATE movimientos SET integra_encVenta = '$total_integrantes' WHERE id_movimiento = '$id_movimiento'";
            if (!$mysqli->query($sql_update_contador)) {
                throw new Exception("Error al actualizar contador de integrantes: " . $mysqli->error);
            }
        }

        // Confirmar transacción
        $mysqli->commit();
        $mysqli->autocommit(TRUE);

        // Página de éxito
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
                <title>BD SISBEN - Actualización Exitosa</title>
                <style>
                    .responsive {
                        max-width: 100%;
                        height: auto;
                    }
                    .success-container {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        border-radius: 15px;
                        padding: 40px;
                        text-align: center;
                        margin: 50px auto;
                        max-width: 600px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                    }
                    .success-icon {
                        font-size: 4rem;
                        margin-bottom: 20px;
                        color: #28a745;
                    }
                    .btn-custom {
                        background: rgba(255,255,255,0.2);
                        border: 2px solid white;
                        color: white;
                        padding: 12px 30px;
                        border-radius: 25px;
                        text-decoration: none;
                        margin: 10px;
                        display: inline-block;
                        transition: all 0.3s ease;
                    }
                    .btn-custom:hover {
                        background: white;
                        color: #667eea;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <center>
                    <img src='../../img/sisben.png' width=300 height=185 class='responsive'>
                    <div class='success-container'>
                        <div class='success-icon'>
                            <i class='fas fa-check-circle'></i>
                        </div>
                        <h2><b>¡MOVIMIENTO ACTUALIZADO EXITOSAMENTE!</b></h2>
                        <hr style='border-color: rgba(255,255,255,0.3);'>
                        <p><strong>Documento:</strong> $doc_encVenta</p>
                        <p><strong>Nombre:</strong> $nom_encVenta</p>
                        <p><strong>Tipo de Movimiento:</strong> $movimientos</p>
                        <p><strong>Fecha de Actualización:</strong> " . date('d/m/Y H:i:s') . "</p>
                        <div class='mt-4'>
                            <a href='showMovimientos.php' class='btn-custom'>
                                <i class='fas fa-list me-2'></i>Ver Todos los Movimientos
                            </a>
                            <a href='editMovimiento.php?id_movimiento=$id_movimiento' class='btn-custom'>
                                <i class='fas fa-edit me-2'></i>Editar Nuevamente
                            </a>
                            <a href='../../access.php' class='btn-custom'>
                                <i class='fas fa-home me-2'></i>Menú Principal
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
                        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                        color: white;
                        border-radius: 15px;
                        padding: 40px;
                        text-align: center;
                        margin: 50px auto;
                        max-width: 600px;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                    }
                    .error-icon {
                        font-size: 4rem;
                        margin-bottom: 20px;
                    }
                    .btn-custom {
                        background: rgba(255,255,255,0.2);
                        border: 2px solid white;
                        color: white;
                        padding: 12px 30px;
                        border-radius: 25px;
                        text-decoration: none;
                        margin: 10px;
                        display: inline-block;
                        transition: all 0.3s ease;
                    }
                    .btn-custom:hover {
                        background: white;
                        color: #dc3545;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <center>
                    <img src='../../img/sisben.png' width=300 height=185 class='responsive'>
                    <div class='error-container'>
                        <div class='error-icon'>
                            <i class='fas fa-times-circle'></i>
                        </div>
                        <h2><b>ERROR AL ACTUALIZAR EL MOVIMIENTO</b></h2>
                        <hr style='border-color: rgba(255,255,255,0.3);'>
                        <p class='alert alert-light text-danger'><strong>Error:</strong> " . $e->getMessage() . "</p>
                        <div class='mt-4'>
                            <a href='showMovimientos.php' class='btn-custom'>
                                <i class='fas fa-arrow-left me-2'></i>Volver a la Lista
                            </a>
                            <a href='../../access.php' class='btn-custom'>
                                <i class='fas fa-home me-2'></i>Menú Principal
                            </a>
                        </div>
                    </div>
                </center>
            </body>
        </html>";
    }
} else {
    // Si no se envió por POST o falta el ID, redirigir
    header("Location: showMovimientos.php");
    exit();
}
