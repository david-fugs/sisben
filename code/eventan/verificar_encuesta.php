<?php
/**
 * VERIFICAR ENCUESTA - VERSIÓN INDEPENDIENTE
 * 
 * Este archivo ahora consulta ÚNICAMENTE la tabla movimientos independiente.
 * No depende de encventanilla para obtener los datos.
 * 
 * Funciona con la nueva estructura después de la migración de independencia.
 */

header('Content-Type: application/json');
include("../../conexion.php");

if ($_POST['doc_encVenta']) {
    $documento = mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']);

    // 1) Intentar obtener datos desde encventanilla (legacy) — esto permite detectar registros antiguos primero
    $sql_legacy = "SELECT encventanilla.*, 
                   CASE 
                       WHEN encventanilla.estado_ficha = 0 THEN 'RETIRADA'
                       ELSE 'ACTIVA'
                   END as estado_ficha_texto
                   FROM encventanilla 
                   WHERE encventanilla.doc_encVenta = '$documento' 
                   LIMIT 1";
    $resultado_legacy = mysqli_query($mysqli, $sql_legacy);
    $legacy_data = null;
    $legacy_integrantes = [];

    if ($resultado_legacy && mysqli_num_rows($resultado_legacy) > 0) {
        $legacy_data = mysqli_fetch_assoc($resultado_legacy);
        // Integrantes legacy
        $sql_integrantes = "SELECT * FROM integventanilla WHERE id_encVenta = '" . $legacy_data['id_encVenta'] . "'";
        $resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);
        while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
            $legacy_integrantes[] = $integrante;
        }
    }

    // 2) Consultar movimientos independiente (si existen) — preferir estos datos si están disponibles
    $sql_movimientos = "SELECT m.*, 
                        CASE 
                            WHEN m.estado_ficha = 0 THEN 'RETIRADA'
                            ELSE 'ACTIVA'
                        END as estado_ficha_texto,
                        m.fecha_alta_movimiento as fecha_alta_encVenta,
                        m.fecha_edit_movimiento as fecha_edit_encVenta
                        FROM movimientos m 
                        WHERE m.doc_encVenta = '$documento' 
                        ORDER BY m.fecha_movimiento DESC 
                        LIMIT 1";
    $resultado_movimientos = mysqli_query($mysqli, $sql_movimientos);

    if ($resultado_movimientos && mysqli_num_rows($resultado_movimientos) > 0) {
        // Datos desde movimientos independientes -> prioridad
        $data = mysqli_fetch_assoc($resultado_movimientos);

        // Obtener integrantes desde tabla independiente primero
        $integrantes = [];
        $sql_integrantes_indep = "SELECT * FROM integmovimientos_independiente 
                                 WHERE doc_encVenta = '$documento' 
                                 AND estado_integMovIndep = 1
                                 ORDER BY fecha_alta_integMovIndep DESC";
        $resultado_integrantes_indep = mysqli_query($mysqli, $sql_integrantes_indep);
        if ($resultado_integrantes_indep && mysqli_num_rows($resultado_integrantes_indep) > 0) {
            while ($integrante = mysqli_fetch_assoc($resultado_integrantes_indep)) {
                $integrantes[] = $integrante;
            }
        } else {
            // Fallback a integrantes legacy si no existen en la tabla independiente
            $integrantes = $legacy_integrantes;
        }

        // Normalizar campos esperados por el cliente
        $normalized = [
            'doc_encVenta' => $data['doc_encVenta'] ?? '',
            'nom_encVenta' => $data['nom_encVenta'] ?? $data['nom_encMovIndep'] ?? '',
            'dir_encVenta' => $data['dir_encVenta'] ?? $data['dir_encMovIndep'] ?? '',
            'id_bar' => $data['id_bar'] ?? $data['id_bar_mov'] ?? '',
            'id_com' => $data['id_com'] ?? $data['id_com_mov'] ?? '',
            'departamento_expedicion' => $data['departamento_expedicion'] ?? '',
            'ciudad_expedicion' => $data['ciudad_expedicion'] ?? '',
            'tipo_documento' => $data['tipo_documento'] ?? '',
            'fecha_expedicion' => $data['fecha_expedicion'] ?? '',
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'fecha_alta_encVenta' => $data['fecha_alta_encVenta'] ?? $data['fecha_alta_movimiento'] ?? '',
            'sisben_nocturno' => $data['sisben_nocturno'] ?? '',
            'obs_encVenta' => $data['obs_encVenta'] ?? $data['observacion'] ?? '',
            'otro_bar_ver_encVenta' => $data['otro_bar_ver_encVenta'] ?? '',
            'num_ficha_encVenta' => $data['num_ficha_encVenta'] ?? $data['num_ficha_encMovIndep'] ?? ''
        ];

        // Verificar estado y devolver respuesta con datos normalizados
        if (($data['estado_ficha'] ?? 1) == 0) {
            echo json_encode([
                'status' => 'ficha_retirada',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.',
                'origen' => 'movimientos_independiente'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'origen' => 'movimientos_independiente'
            ]);
        }

    } elseif ($legacy_data) {
        // No hay movimientos; usar datos legacy de encventanilla
        $data = $legacy_data;
        $integrantes = $legacy_integrantes;

        // Normalizar legacy
        $normalized = [
            'doc_encVenta' => $data['doc_encVenta'] ?? '',
            'nom_encVenta' => $data['nom_encVenta'] ?? '',
            'dir_encVenta' => $data['dir_encVenta'] ?? '',
            'id_bar' => $data['id_bar'] ?? '',
            'id_com' => $data['id_com'] ?? '',
            'departamento_expedicion' => $data['departamento_expedicion'] ?? '',
            'ciudad_expedicion' => $data['ciudad_expedicion'] ?? '',
            'tipo_documento' => $data['tipo_documento'] ?? '',
            'fecha_expedicion' => $data['fecha_expedicion'] ?? '',
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'fecha_alta_encVenta' => $data['fecha_alta_encVenta'] ?? $data['fecha_alta_encVenta'] ?? '',
            'sisben_nocturno' => $data['sisben_nocturno'] ?? '',
            'obs_encVenta' => $data['obs_encVenta'] ?? '',
            'otro_bar_ver_encVenta' => $data['otro_bar_ver_encVenta'] ?? '',
            'num_ficha_encVenta' => $data['num_ficha_encVenta'] ?? ''
        ];

        if (($data['estado_ficha'] ?? 1) == 0) {
            echo json_encode([
                'status' => 'ficha_retirada',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.',
                'origen' => 'encventanilla_legacy'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'origen' => 'encventanilla_legacy'
            ]);
        }

    } else {
        echo json_encode([
            'status' => 'no_existe',
            'message' => 'El documento no está registrado en la base de datos.'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se proporcionó documento para consultar.'
    ]);
}
?>