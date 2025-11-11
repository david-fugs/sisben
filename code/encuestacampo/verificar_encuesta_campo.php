<?php
/**
 * VERIFICAR ENCUESTA CAMPO - Control de Movimientos
 * 
 * Este archivo consulta:
 * 1. Tabla encuestacampo (datos originales)
 * 2. Tabla movimientos_encuesta_campo (movimientos más recientes)
 * 
 * Prioriza datos más recientes de movimientos sobre datos originales
 */

header('Content-Type: application/json');
include("../../conexion.php");

mysqli_set_charset($mysqli, "utf8");

if (isset($_POST['doc_encCampo']) && $_POST['doc_encCampo'] != '') {
    $documento = mysqli_real_escape_string($mysqli, $_POST['doc_encCampo']);

    // 1) Consultar tabla original encuestacampo (usa sufijo _encVenta en columnas)
    $sql_encuesta = "SELECT ec.*, 
                     b.nombre_bar,
                     c.nombre_com,
                     CASE 
                         WHEN ec.estado_ficha = 0 THEN 'RETIRADA'
                         ELSE 'ACTIVA'
                     END as estado_ficha_texto
                     FROM encuestacampo ec
                     LEFT JOIN barrios b ON ec.id_bar = b.id_bar
                     LEFT JOIN comunas c ON ec.id_com = c.id_com
                     WHERE ec.doc_encVenta = '$documento' 
                     LIMIT 1";
    
    $resultado_encuesta = mysqli_query($mysqli, $sql_encuesta);
    $encuesta_data = null;
    $encuesta_integrantes = [];

    if ($resultado_encuesta && mysqli_num_rows($resultado_encuesta) > 0) {
        $encuesta_data = mysqli_fetch_assoc($resultado_encuesta);
        
        // Obtener integrantes de la encuesta original (tabla usa id_encuesta, no id_encCampo)
        $sql_integrantes_enc = "SELECT * FROM integcampo 
                               WHERE id_encuesta = '" . $encuesta_data['id_encCampo'] . "'
                               ORDER BY id_integCampo ASC";
        $resultado_integ_enc = mysqli_query($mysqli, $sql_integrantes_enc);
        
        if ($resultado_integ_enc) {
            while ($integrante = mysqli_fetch_assoc($resultado_integ_enc)) {
                $encuesta_integrantes[] = [
                    'gen_integCampo' => $integrante['gen_integVenta'] ?? '',
                    'rango_integCampo' => $integrante['rango_integVenta'] ?? '',
                    'orientacionSexual' => $integrante['orientacionSexual'] ?? '',
                    'condicionDiscapacidad' => $integrante['condicionDiscapacidad'] ?? '',
                    'tipoDiscapacidad' => $integrante['tipoDiscapacidad'] ?? '',
                    'grupoEtnico' => $integrante['grupoEtnico'] ?? '',
                    'victima' => $integrante['victima'] ?? '',
                    'mujerGestante' => $integrante['mujerGestante'] ?? '',
                    'cabezaFamilia' => $integrante['cabezaFamilia'] ?? '',
                    'experienciaMigratoria' => $integrante['experienciaMigratoria'] ?? '',
                    'seguridadSalud' => $integrante['seguridadSalud'] ?? '',
                    'nivelEducativo' => $integrante['nivelEducativo'] ?? '',
                    'condicionOcupacion' => $integrante['condicionOcupacion'] ?? ''
                ];
            }
        }
    }

    // 2) Consultar tabla movimientos_encuesta_campo (datos más recientes)
    $sql_movimientos = "SELECT m.*, 
                        b.nombre_bar,
                        c.nombre_com,
                        CASE 
                            WHEN m.estado_ficha = 0 THEN 'RETIRADA'
                            ELSE 'ACTIVA'
                        END as estado_ficha_texto
                        FROM movimientos_encuesta_campo m
                        LEFT JOIN barrios b ON m.id_bar = b.id_bar
                        LEFT JOIN comunas c ON m.id_com = c.id_com
                        WHERE m.doc_encCampo = '$documento' 
                        ORDER BY m.fecha_movimiento DESC 
                        LIMIT 1";
    
    $resultado_movimientos = mysqli_query($mysqli, $sql_movimientos);

    if ($resultado_movimientos && mysqli_num_rows($resultado_movimientos) > 0) {
        // DATOS MÁS RECIENTES DESDE MOVIMIENTOS
        $data = mysqli_fetch_assoc($resultado_movimientos);

        // Obtener integrantes de movimientos
        $integrantes = [];
        $sql_integrantes_mov = "SELECT * FROM integ_movimientos_encuesta_campo 
                               WHERE doc_encCampo = '$documento' 
                               ORDER BY fecha_registro DESC";
        $resultado_integ_mov = mysqli_query($mysqli, $sql_integrantes_mov);
        
        if ($resultado_integ_mov && mysqli_num_rows($resultado_integ_mov) > 0) {
            while ($integrante = mysqli_fetch_assoc($resultado_integ_mov)) {
                $integrantes[] = $integrante;
            }
        } else {
            // Si no hay integrantes en movimientos, usar los de la encuesta original
            $integrantes = $encuesta_integrantes;
        }

        // Normalizar campos para el frontend
        $normalized = [
            'doc_encCampo' => $data['doc_encCampo'] ?? '',
            'nom_encCampo' => $data['nom_encCampo'] ?? '',
            'tipo_documento' => $data['tipo_documento'] ?? '',
            'fecha_expedicion' => $data['fecha_expedicion'] ?? '',
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'departamento_expedicion' => $data['departamento_expedicion'] ?? '',
            'ciudad_expedicion' => $data['ciudad_expedicion'] ?? '',
            'dir_encCampo' => $data['dir_encCampo'] ?? '',
            'zona_encCampo' => $data['zona_encCampo'] ?? '',
            'id_bar' => $data['id_bar'] ?? '',
            'id_com' => $data['id_com'] ?? '',
            'nombre_barrio' => $data['nombre_bar'] ?? '',
            'nombre_comuna' => $data['nombre_com'] ?? '',
            'otro_bar_ver_encCampo' => $data['otro_bar_ver_encCampo'] ?? '',
            'num_ficha_encCampo' => $data['num_ficha_encCampo'] ?? '',
            'integra_encCampo' => $data['integra_encCampo'] ?? 0,
            'fec_reg_encCampo' => $data['fec_reg_encCampo'] ?? '',
            'obs_encCampo' => $data['obs_encCampo'] ?? '',
            'tipo_movimiento' => $data['tipo_movimiento'] ?? '',
            'fecha_movimiento' => $data['fecha_movimiento'] ?? '',
            'estado_ficha' => $data['estado_ficha'] ?? 1,
            'estado_ficha_texto' => $data['estado_ficha_texto'] ?? 'ACTIVA'
        ];

        // Verificar estado de la ficha
        if (($data['estado_ficha'] ?? 1) == 0) {
            echo json_encode([
                'status' => 'ficha_retirada',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'message' => '⚠️ ADVERTENCIA: Esta encuesta tiene la ficha RETIRADA.',
                'origen' => 'movimientos_encuesta_campo'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'message' => '✅ Documento encontrado en movimientos. Datos más recientes cargados.',
                'origen' => 'movimientos_encuesta_campo'
            ]);
        }

    } elseif ($encuesta_data) {
        // DATOS DESDE ENCUESTA ORIGINAL (no hay movimientos)
        $data = $encuesta_data;
        $integrantes = $encuesta_integrantes;

        // Normalizar campos desde encuestacampo (tabla usa sufijo _encVenta)
        // NOTA: encuestacampo NO tiene los campos fecha_expedicion, departamento_expedicion, ciudad_expedicion
        // Estos campos solo existen en movimientos_encuesta_campo
        $normalized = [
            'doc_encCampo' => $data['doc_encCampo'] ?? $data['doc_encVenta'] ?? '',
            'nom_encCampo' => $data['nom_encCampo'] ?? $data['nom_encVenta'] ?? '',
            'tipo_documento' => $data['tipo_documento'] ?? '',
            'fecha_expedicion' => '', // Campo no existe en encuestacampo, debe llenarse manualmente
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'departamento_expedicion' => '', // Campo no existe en encuestacampo, debe llenarse manualmente
            'ciudad_expedicion' => '', // Campo no existe en encuestacampo, debe llenarse manualmente
            'dir_encCampo' => $data['dir_encCampo'] ?? $data['dir_encVenta'] ?? '',
            'zona_encCampo' => $data['zona_encCampo'] ?? $data['zona_encVenta'] ?? '',
            'id_bar' => $data['id_bar'] ?? '',
            'id_com' => $data['id_com'] ?? '',
            'nombre_barrio' => $data['nombre_bar'] ?? '',
            'nombre_comuna' => $data['nombre_com'] ?? '',
            'otro_bar_ver_encCampo' => $data['otro_bar_ver_encCampo'] ?? $data['otro_bar_ver_encVenta'] ?? '',
            'num_ficha_encCampo' => $data['num_ficha_encCampo'] ?? $data['num_ficha_encVenta'] ?? '',
            'integra_encCampo' => $data['integra_encCampo'] ?? $data['integra_encVenta'] ?? 0,
            'fec_reg_encCampo' => $data['fec_reg_encCampo'] ?? $data['fec_reg_encVenta'] ?? '',
            'obs_encCampo' => $data['obs_encCampo'] ?? $data['obs_encVenta'] ?? '',
            'estado_ficha' => $data['estado_ficha'] ?? 1,
            'estado_ficha_texto' => $data['estado_ficha_texto'] ?? 'ACTIVA'
        ];

        // Verificar estado de la ficha
        if (($data['estado_ficha'] ?? 1) == 0) {
            echo json_encode([
                'status' => 'ficha_retirada',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'message' => '⚠️ ADVERTENCIA: Esta encuesta tiene la ficha RETIRADA.',
                'origen' => 'encuestacampo'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $normalized,
                'integrantes' => $integrantes,
                'message' => '✅ Documento encontrado en encuestacampo. Puede realizar movimientos.',
                'origen' => 'encuestacampo'
            ]);
        }

    } else {
        // NO EXISTE EL DOCUMENTO
        echo json_encode([
            'status' => 'no_existe',
            'message' => '⚠️ El documento no está registrado. Puede crear un nuevo movimiento.'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se proporcionó documento para consultar.'
    ]);
}

mysqli_close($mysqli);
?>
