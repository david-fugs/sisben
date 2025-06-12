<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu     = $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

include("../../conexion.php");
date_default_timezone_set("America/Bogota");
$mysqli->set_charset('utf8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id_encVenta = $_POST['id_encVenta'];
    
    // Campos principales de la encuesta
    $fec_reg_encVenta = $_POST['fec_reg_encVenta'];
    $doc_encVenta = $_POST['doc_encVenta'];
    $tipo_documento = $_POST['tipo_documento'];
    $departamento_expedicion = $_POST['departamento_expedicion'];
    $ciudad_expedicion = $_POST['ciudad_expedicion'];
    $fecha_expedicion = $_POST['fecha_expedicion'];
    $nom_encVenta = mb_strtoupper($_POST['nom_encVenta'], 'UTF-8');
    $dir_encVenta = mb_strtoupper($_POST['dir_encVenta'], 'UTF-8');
    $zona_encVenta = $_POST['zona_encVenta'];
    $id_com = isset($_POST['id_com']) ? $_POST['id_com'] : null;
    $id_bar = isset($_POST['id_bar']) ? $_POST['id_bar'] : null;
    $tram_solic_encVenta = mb_strtoupper($_POST['tram_solic_encVenta'], 'UTF-8');
    $obs_encVenta = isset($_POST['obs_encVenta']) ? mb_strtoupper($_POST['obs_encVenta'], 'UTF-8') : '';

    // Actualizar datos de la encuesta
    $query = "UPDATE encventanilla SET 
                fec_reg_encVenta = ?,
                doc_encVenta = ?,
                tipo_documento = ?,
                departamento_expedicion = ?,
                ciudad_expedicion = ?,
                fecha_expedicion = ?,
                nom_encVenta = ?,
                dir_encVenta = ?,
                zona_encVenta = ?,
                id_com = ?,
                id_bar = ?,
                tram_solic_encVenta = ?,
                obs_encVenta = ?,
                fecha_mod = NOW(),
                usu_mod = ?
            WHERE id_encVenta = ?";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param(
        "sssssssssssssis", 
        $fec_reg_encVenta, 
        $doc_encVenta, 
        $tipo_documento, 
        $departamento_expedicion,
        $ciudad_expedicion,
        $fecha_expedicion,
        $nom_encVenta,
        $dir_encVenta,
        $zona_encVenta,
        $id_com,
        $id_bar,
        $tram_solic_encVenta,
        $obs_encVenta,
        $id_usu,
        $id_encVenta
    );
    
    $result = $stmt->execute();

    if ($result) {
        // Eliminar registros anteriores de integrantes para esta encuesta
        $delete_query = "DELETE FROM integencventanilla WHERE id_encVenta = ?";
        $delete_stmt = $mysqli->prepare($delete_query);
        $delete_stmt->bind_param("i", $id_encVenta);
        $delete_stmt->execute();

        // Procesar integrantes
        if (isset($_POST['cant_integVenta']) && is_array($_POST['cant_integVenta'])) {
            $total_integrantes = count($_POST['cant_integVenta']);
            
            for ($i = 0; $i < $total_integrantes; $i++) {
                // Obtener datos de cada integrante
                $gen_integVenta = isset($_POST['gen_integVenta'][$i]) ? $_POST['gen_integVenta'][$i] : '';
                $orientacionSexual = isset($_POST['orientacionSexual'][$i]) ? $_POST['orientacionSexual'][$i] : '';
                $rango_integVenta = isset($_POST['rango_integVenta'][$i]) ? $_POST['rango_integVenta'][$i] : '';
                $condicionDiscapacidad = isset($_POST['condicionDiscapacidad'][$i]) ? $_POST['condicionDiscapacidad'][$i] : '';
                $tipoDiscapacidad = isset($_POST['tipoDiscapacidad'][$i]) ? $_POST['tipoDiscapacidad'][$i] : '';
                $grupoEtnico = isset($_POST['grupoEtnico'][$i]) ? $_POST['grupoEtnico'][$i] : '';
                $victima = isset($_POST['victima'][$i]) ? $_POST['victima'][$i] : '';
                $mujerGestante = isset($_POST['mujerGestante'][$i]) ? $_POST['mujerGestante'][$i] : '';
                $cabezaFamilia = isset($_POST['cabezaFamilia'][$i]) ? $_POST['cabezaFamilia'][$i] : '';
                $experienciaMigratoria = isset($_POST['experienciaMigratoria'][$i]) ? $_POST['experienciaMigratoria'][$i] : '';
                $seguridadSalud = isset($_POST['seguridadSalud'][$i]) ? $_POST['seguridadSalud'][$i] : '';
                $nivelEducativo = isset($_POST['nivelEducativo'][$i]) ? $_POST['nivelEducativo'][$i] : '';
                $condicionOcupacion = isset($_POST['condicionOcupacion'][$i]) ? $_POST['condicionOcupacion'][$i] : '';

                // Insertar integrante
                $insert_query = "INSERT INTO integencventanilla (
                    id_encVenta,
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
                    fecha_alta_integencVenta,
                    usu_alta_integencVenta
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

                $insert_stmt = $mysqli->prepare($insert_query);
                $insert_stmt->bind_param(
                    "isssssssssssssi",
                    $id_encVenta,
                    $gen_integVenta,
                    $orientacionSexual,
                    $rango_integVenta,
                    $condicionDiscapacidad,
                    $tipoDiscapacidad,
                    $grupoEtnico,
                    $victima,
                    $mujerGestante,
                    $cabezaFamilia,
                    $experienciaMigratoria,
                    $seguridadSalud,
                    $nivelEducativo,
                    $condicionOcupacion,
                    $id_usu
                );
                $insert_stmt->execute();
            }
        }

        echo "<script>alert('Encuesta actualizada con éxito'); window.location.href='showsurvey.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la encuesta: " . $mysqli->error . "'); window.history.back();</script>";
    }
} else {
    header("Location: showsurvey.php");
    exit();
}
?>
