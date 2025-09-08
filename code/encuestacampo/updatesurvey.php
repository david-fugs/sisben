<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu = $_SESSION['id_usu'];
$tipo_usu = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");

include('../../conexion.php');

// Establecer charset UTF-8 para manejar tildes y ñ
mysqli_set_charset($mysqli, "utf8");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Iniciar transacción
    mysqli_autocommit($mysqli, false);
    
    try {
    $id_encuesta = intval($_POST['id_encuesta']);
        
    // Verificar permisos (usar id_encCampo)
    $check_query = "SELECT id_encCampo FROM encuestacampo WHERE id_encCampo = $id_encuesta";
        if ($tipo_usu != '1') {
            $check_query .= " AND id_usu = $id_usu";
        }
        $check_result = mysqli_query($mysqli, $check_query);
        
        if (mysqli_num_rows($check_result) == 0) {
            throw new Exception("No tiene permisos para editar esta encuesta");
        }
        
        // Datos principales de la encuesta
        $doc_encVenta = mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']);
        $fec_reg_encVenta = mysqli_real_escape_string($mysqli, $_POST['fec_reg_encVenta']);
        $tipo_documento = mysqli_real_escape_string($mysqli, $_POST['tipo_documento']);
        $departamento_expedicion = mysqli_real_escape_string($mysqli, $_POST['departamento_expedicion']);
        $ciudad_expedicion = mysqli_real_escape_string($mysqli, $_POST['ciudad_expedicion']);
        $fecha_expedicion = mysqli_real_escape_string($mysqli, $_POST['fecha_expedicion']);
        $nom_encVenta = mysqli_real_escape_string($mysqli, $_POST['nom_encVenta']);
        $fecha_nacimiento = mysqli_real_escape_string($mysqli, $_POST['fecha_nacimiento'] ?? '');
        $dir_encVenta = mysqli_real_escape_string($mysqli, $_POST['dir_encVenta']);
        $id_bar = mysqli_real_escape_string($mysqli, $_POST['id_bar']);
        $id_com = mysqli_real_escape_string($mysqli, $_POST['id_com'] ?? '');
        $otro_bar_ver_encVenta = mysqli_real_escape_string($mysqli, $_POST['otro_bar_ver_encVenta'] ?? '');
        $zona_encVenta = mysqli_real_escape_string($mysqli, $_POST['zona_encVenta']);
        $tram_solic_encVenta = mysqli_real_escape_string($mysqli, $_POST['tram_solic_encVenta'] ?? 'Encuesta de Campo');
        $num_ficha_encVenta = mysqli_real_escape_string($mysqli, $_POST['num_ficha_encVenta']);
        $num_visita = mysqli_real_escape_string($mysqli, $_POST['num_visita'] ?? '1');
        $estado_ficha = mysqli_real_escape_string($mysqli, $_POST['estado_ficha'] ?? '1');
        $tipo_proceso = mysqli_real_escape_string($mysqli, $_POST['tipo_proceso'] ?? 'Encuesta Campo');
        $integra_encVenta = mysqli_real_escape_string($mysqli, $_POST['integra_encVenta']);
        $sisben_nocturno = mysqli_real_escape_string($mysqli, $_POST['sisben_nocturno']);
        $obs_encVenta = mysqli_real_escape_string($mysqli, $_POST['obs_encVenta'] ?? '');
        
        // Actualizar encuesta principal
    $sql_update_encuesta = "UPDATE encuestacampo SET 
            doc_encVenta = '$doc_encVenta',
            fec_reg_encVenta = '$fec_reg_encVenta',
            tipo_documento = '$tipo_documento',
            departamento_expedicion = '$departamento_expedicion',
            ciudad_expedicion = '$ciudad_expedicion',
            fecha_expedicion = '$fecha_expedicion',
            nom_encVenta = '$nom_encVenta',
            fecha_nacimiento = '$fecha_nacimiento',
            dir_encVenta = '$dir_encVenta',
            id_bar = '$id_bar',
            id_com = '$id_com',
            otro_bar_ver_encVenta = '$otro_bar_ver_encVenta',
            zona_encVenta = '$zona_encVenta',
            tram_solic_encVenta = '$tram_solic_encVenta',
            num_ficha_encVenta = '$num_ficha_encVenta',
            num_visita = '$num_visita',
            estado_ficha = '$estado_ficha',
            tipo_proceso = '$tipo_proceso',
            integra_encVenta = '$integra_encVenta',
            sisben_nocturno = '$sisben_nocturno',
            obs_encVenta = '$obs_encVenta'
            WHERE id_encCampo = $id_encuesta";

        if (!mysqli_query($mysqli, $sql_update_encuesta)) {
            throw new Exception("Error al actualizar encuesta: " . mysqli_error($mysqli));
        }

        // Actualizar integrantes
        if (isset($_POST['integrante_id']) && is_array($_POST['integrante_id'])) {
            $integrante_ids = $_POST['integrante_id'];
            $gen_integVenta = $_POST['gen_integVenta'] ?? [];
            $orientacionSexual = $_POST['orientacionSexual'] ?? [];
            $rango_integVenta = $_POST['rango_integVenta'] ?? [];
            $condicionDiscapacidad = $_POST['condicionDiscapacidad'] ?? [];
            $tipoDiscapacidad = $_POST['tipoDiscapacidad'] ?? [];
            $grupoEtnico = $_POST['grupoEtnico'] ?? [];
            $victima = $_POST['victima'] ?? [];
            $mujerGestante = $_POST['mujerGestante'] ?? [];
            $cabezaFamilia = $_POST['cabezaFamilia'] ?? [];
            $experienciaMigratoria = $_POST['experienciaMigratoria'] ?? [];
            $seguridadSalud = $_POST['seguridadSalud'] ?? [];
            $nivelEducativo = $_POST['nivelEducativo'] ?? [];
            $condicionOcupacion = $_POST['condicionOcupacion'] ?? [];

            for ($i = 0; $i < count($integrante_ids); $i++) {
                $integrante_id = intval($integrante_ids[$i]);
                $gen = mysqli_real_escape_string($mysqli, $gen_integVenta[$i] ?? '');
                $orient = mysqli_real_escape_string($mysqli, $orientacionSexual[$i] ?? '');
                $rango = mysqli_real_escape_string($mysqli, $rango_integVenta[$i] ?? '');
                $cond_disc = mysqli_real_escape_string($mysqli, $condicionDiscapacidad[$i] ?? '');
                $tipo_disc = mysqli_real_escape_string($mysqli, $tipoDiscapacidad[$i] ?? '');
                $grupo = mysqli_real_escape_string($mysqli, $grupoEtnico[$i] ?? '');
                $vict = mysqli_real_escape_string($mysqli, $victima[$i] ?? '');
                $mujer = mysqli_real_escape_string($mysqli, $mujerGestante[$i] ?? '');
                $cabeza = mysqli_real_escape_string($mysqli, $cabezaFamilia[$i] ?? '');
                $exp = mysqli_real_escape_string($mysqli, $experienciaMigratoria[$i] ?? '');
                $seg = mysqli_real_escape_string($mysqli, $seguridadSalud[$i] ?? '');
                $niv = mysqli_real_escape_string($mysqli, $nivelEducativo[$i] ?? '');
                $ocup = mysqli_real_escape_string($mysqli, $condicionOcupacion[$i] ?? '');

                // Si es un integrante existente, actualizar
                if ($integrante_id > 0) {
                    $sql_update_integrante = "UPDATE integcampo SET 
                        gen_integVenta = '$gen',
                        orientacionSexual = '$orient',
                        rango_integVenta = '$rango',
                        condicionDiscapacidad = '$cond_disc',
                        tipoDiscapacidad = '$tipo_disc',
                        grupoEtnico = '$grupo',
                        victima = '$vict',
                        mujerGestante = '$mujer',
                        cabezaFamilia = '$cabeza',
                        experienciaMigratoria = '$exp',
                        seguridadSalud = '$seg',
                        nivelEducativo = '$niv',
                        condicionOcupacion = '$ocup'
                        WHERE id_integCampo = $integrante_id AND id_encuesta = $id_encuesta";

                    if (!mysqli_query($mysqli, $sql_update_integrante)) {
                        throw new Exception("Error al actualizar integrante: " . mysqli_error($mysqli));
                    }
                } else {
                    // Si es un integrante nuevo, insertar
                    $sql_insert_integrante = "INSERT INTO integcampo (
                        id_encuesta,
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
                    ) VALUES ($id_encuesta, 1, '$gen', '$orient', '$rango', '$cond_disc', '$tipo_disc', '$grupo', '$vict', '$mujer', '$cabeza', '$exp', '$seg', '$niv', '$ocup', NOW())";

                    if (!mysqli_query($mysqli, $sql_insert_integrante)) {
                        throw new Exception("Error al insertar nuevo integrante: " . mysqli_error($mysqli));
                    }
                }
            }
        }
        
        // Confirmar transacción
        mysqli_commit($mysqli);
        
        echo "<script>
            alert('Encuesta actualizada exitosamente');
            window.location.href = 'showsurvey.php';
        </script>";
        
    } catch (Exception $e) {
        // Revertir transacción
        mysqli_rollback($mysqli);
        echo "<script>
            alert('Error al actualizar la encuesta: " . $e->getMessage() . "');
            window.history.back();
        </script>";
    }
    
    mysqli_autocommit($mysqli, true);
    mysqli_close($mysqli);
} else {
    header("Location: showsurvey.php");
    exit();
}
?>
