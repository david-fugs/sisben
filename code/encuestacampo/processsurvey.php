<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu = $_SESSION['id_usu'];
// Log POST for debugging (do not print to output to avoid header issues)
@file_put_contents(__DIR__ . '/debug_post.log', date('Y-m-d H:i:s') . " POST: " . print_r($_POST, true) . PHP_EOL, FILE_APPEND);
header("Content-Type: text/html;charset=utf-8");

include('../../conexion.php');

// Establecer charset UTF-8 para manejar tildes y ñ
mysqli_set_charset($mysqli, "utf8");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Iniciar transacción
    mysqli_autocommit($mysqli, false);

    try {
        // Datos principales de la encuesta
        $doc_encVenta = mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']);
        $tipo_documento = mysqli_real_escape_string($mysqli, $_POST['tipo_documento']);
        $nom_encVenta = mysqli_real_escape_string($mysqli, $_POST['nom_encVenta']);
        $fecha_nacimiento = mysqli_real_escape_string($mysqli, $_POST['fecha_nacimiento'] ?? '');
        $dir_encVenta = mysqli_real_escape_string($mysqli, $_POST['dir_encVenta']);
        $id_bar = mysqli_real_escape_string($mysqli, $_POST['id_bar']);
        $id_com = mysqli_real_escape_string($mysqli, $_POST['id_com'] ?? '');
        $otro_bar_ver_encVenta = mysqli_real_escape_string($mysqli, $_POST['otro_bar_ver_encVenta'] ?? '');
        $zona_encVenta = mysqli_real_escape_string($mysqli, $_POST['zona_encVenta']);
        $tram_solic_encVenta = mysqli_real_escape_string($mysqli, $_POST['tram_solic_encVenta']);
        $num_ficha_encVenta = mysqli_real_escape_string($mysqli, $_POST['num_ficha_encVenta']);
        $num_visita = mysqli_real_escape_string($mysqli, $_POST['num_visita'] ?? '');
        $estado_ficha = mysqli_real_escape_string($mysqli, $_POST['estado_ficha'] ?? '');
        $integra_encVenta = mysqli_real_escape_string($mysqli, $_POST['integra_encVenta']);
        $obs_encVenta = mysqli_real_escape_string($mysqli, $_POST['obs_encVenta'] ?? '');
        $fecha_preregistro = mysqli_real_escape_string($mysqli, $_POST['fecha_preregistro'] ?? '');

        // Insertar en la tabla principal encuestacampo usando consulta plana
        $sql_encuesta = "INSERT INTO encuestacampo (
            doc_encVenta, 
            fec_reg_encVenta,
            tipo_documento,
            nom_encVenta,
            fecha_nacimiento,
            dir_encVenta,
            id_bar,
            id_com,
            otro_bar_ver_encVenta,
            zona_encVenta,
            tram_solic_encVenta,
            num_ficha_encVenta,
            num_visita,
            estado_ficha,
            integra_encVenta,
            obs_encVenta,
            fecha_alta_encVenta,
            fecha_preregistro,
            id_usu
        ) VALUES ('" . $doc_encVenta . "', NOW(), '" . $tipo_documento . "', '" . $nom_encVenta . "', '" . $fecha_nacimiento . "', '" . $dir_encVenta . "', '" . $id_bar . "', '" . $id_com . "', '" . $otro_bar_ver_encVenta . "', '" . $zona_encVenta . "', '" . $tram_solic_encVenta . "', '" . $num_ficha_encVenta . "', '" . $num_visita . "', '" . $estado_ficha . "', '" . $integra_encVenta . "', '" . $obs_encVenta . "', NOW(), '" . $fecha_preregistro . "', " . intval($id_usu) . ")";

        if (!mysqli_query($mysqli, $sql_encuesta)) {
            $mysql_error = mysqli_error($mysqli);
            $mysql_errno = mysqli_errno($mysqli);
            
            // PERMITIR múltiples encuestas con el mismo documento
            // Código 1062 es para entrada duplicada - pero ahora esto es válido
            // Si hay restricción UNIQUE en doc_encVenta, debe eliminarse de la BD
            // Por ahora lanzamos un error descriptivo si el problema persiste
            
            throw new Exception("Error al insertar encuesta: " . $mysql_error . " - SQL: " . $sql_encuesta);
        }

        $id_encuesta = mysqli_insert_id($mysqli);

        // Insertar integrantes (consultas planas)
        // Solo insertar integrantes NUEVOS (marcados como data-es-nuevo="true" en el frontend)
        // Los integrantes precargados NO deben insertarse de nuevo
        if (isset($_POST['gen_integVenta']) && is_array($_POST['gen_integVenta'])) {
            $gen_integVenta = $_POST['gen_integVenta'];
            $orientacionSexual = $_POST['orientacionSexual'];
            $rango_integVenta = $_POST['rango_integVenta'];
            $mysqlidicionDiscapacidad = $_POST['condicionDiscapacidad'];
            $tipoDiscapacidad = $_POST['tipoDiscapacidad'];
            $grupoEtnico = $_POST['grupoEtnico'];
            $victima = $_POST['victima'];
            $mujerGestante = $_POST['mujerGestante'];
            $cabezaFamilia = $_POST['cabezaFamilia'];
            $experienciaMigratoria = $_POST['experienciaMigratoria'];
            $seguridadSalud = $_POST['seguridadSalud'];
            $nivelEducativo = $_POST['nivelEducativo'];
            $mysqlidicionOcupacion = $_POST['condicionOcupacion'];
            
            // Recibir array de marcadores de integrantes nuevos vs precargados
            $es_nuevo = isset($_POST['es_nuevo']) ? $_POST['es_nuevo'] : array();

            for ($i = 0; $i < count($gen_integVenta); $i++) {
                // Solo insertar si es un integrante nuevo (no precargado)
                // Si no viene el array es_nuevo, asumir que todos son nuevos (compatibilidad)
                $es_integrante_nuevo = empty($es_nuevo) || (isset($es_nuevo[$i]) && $es_nuevo[$i] === 'true');
                
                if (!$es_integrante_nuevo) {
                    // Saltar este integrante porque es precargado
                    continue;
                }
                
                $gen = mysqli_real_escape_string($mysqli, $gen_integVenta[$i]);
                $rango = mysqli_real_escape_string($mysqli, $rango_integVenta[$i] ?? '');
                
                // Solo insertar si tiene género Y rango de edad (campos mínimos obligatorios)
                if (empty($gen) || empty($rango)) {
                    // Saltar este integrante porque está incompleto
                    continue;
                }
                
                $orient = mysqli_real_escape_string($mysqli, $orientacionSexual[$i] ?? '');
                $cond_disc = mysqli_real_escape_string($mysqli, $mysqlidicionDiscapacidad[$i] ?? '');
                $tipo_disc = mysqli_real_escape_string($mysqli, $tipoDiscapacidad[$i] ?? '');
                $grupo = mysqli_real_escape_string($mysqli, $grupoEtnico[$i] ?? '');
                $vict = mysqli_real_escape_string($mysqli, $victima[$i] ?? '');
                $mujer = mysqli_real_escape_string($mysqli, $mujerGestante[$i] ?? '');
                $cabeza = mysqli_real_escape_string($mysqli, $cabezaFamilia[$i] ?? '');
                $exp = mysqli_real_escape_string($mysqli, $experienciaMigratoria[$i] ?? '');
                $seg = mysqli_real_escape_string($mysqli, $seguridadSalud[$i] ?? '');
                $niv = mysqli_real_escape_string($mysqli, $nivelEducativo[$i] ?? '');
                $ocup = mysqli_real_escape_string($mysqli, $mysqlidicionOcupacion[$i] ?? '');

                $sql_integrante = "INSERT INTO integcampo (
                    id_encuesta,
                    documento,
                    cant_integVenta,
                    gen_integVenta,
                    orientacionSexual,
                    rango_integVenta,
                    condicionDiscapacidad,
                    tipoDiscapacidad,
                    grupoEtnico,
                    victima,
                    mujerGestante,
                    cabezaFamilia,
                    experienciaMigratoria,
                    seguridadSalud,
                    nivelEducativo,
                    condicionOcupacion,
                    fecha_alta_integCampo
                ) VALUES (" . intval($id_encuesta) . ",'" . $doc_encVenta . "', 1, '" . $gen . "', '" . $orient . "', '" . $rango . "', '" . $cond_disc . "', '" . $tipo_disc . "', '" . $grupo . "', '" . $vict . "', '" . $mujer . "', '" . $cabeza . "', '" . $exp . "', '" . $seg . "', '" . $niv . "', '" . $ocup . "', NOW())";

                if (!mysqli_query($mysqli, $sql_integrante)) {
                    throw new Exception("Error al insertar integrante: " . mysqli_error($mysqli) . " - SQL: " . $sql_integrante);
                }
            }
        }

        // Confirmar transacción
        mysqli_commit($mysqli);

        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Encuesta de campo registrada exitosamente',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = 'showsurvey.php';
            });
        </script>
        </body>
        </html>";
        exit();
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($mysqli);
        // Log error to file for debugging
        @file_put_contents(__DIR__ . '/debug_post.log', date('Y-m-d H:i:s') . " ERROR: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        
        // Mostrar error genérico (ya no bloqueamos por documento duplicado)
        $errorMessage = $e->getMessage();
        
        // Mostrar mensaje genérico con SweetAlert
        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error al Registrar',
                text: 'Ocurrió un error al registrar la encuesta. Por favor, intente nuevamente.',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                window.location.href = 'encuesta_campo.php';
            });
        </script>
        </body>
        </html>";
        
        // Log error to console (visible in browser console for debugging)
        echo "<script>console.error('Error details: " . addslashes(htmlspecialchars($e->getMessage())) . "');</script>";
        exit();
    }

    mysqli_autocommit($mysqli, true);
    mysqli_close($mysqli);
}
