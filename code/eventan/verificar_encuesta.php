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
    
    // 🚀 BUSCAR EN MOVIMIENTOS INDEPENDIENTE (única fuente de datos)
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
    
    if (mysqli_num_rows($resultado_movimientos) > 0) {
        // ✅ DATOS ENCONTRADOS EN MOVIMIENTOS INDEPENDIENTE
        $data = mysqli_fetch_assoc($resultado_movimientos);
        
        // Consultar integrantes desde tabla independiente (si existe)
        $integrantes = [];
          // Primero intentar desde integmovimientos_independiente
        $sql_integrantes_indep = "SELECT * FROM integmovimientos_independiente 
                                 WHERE doc_encVenta = '$documento' 
                                 AND estado_integMovIndep = 1
                                 ORDER BY fecha_alta_integMovIndep DESC";
        $resultado_integrantes_indep = mysqli_query($mysqli, $sql_integrantes_indep);
        
        if ($resultado_integrantes_indep && mysqli_num_rows($resultado_integrantes_indep) > 0) {
            // Usar integrantes de tabla independiente
            while ($integrante = mysqli_fetch_assoc($resultado_integrantes_indep)) {
                $integrantes[] = $integrante;
            }
        } else {
            // FALLBACK: Consultar integrantes legacy (compatibilidad temporal)
            $sql_integrantes_legacy = "SELECT iv.* FROM integventanilla iv
                                      INNER JOIN encventanilla ev ON iv.id_encVenta = ev.id_encVenta
                                      WHERE ev.doc_encVenta = '$documento'
                                      ORDER BY iv.fecha_alta_integVenta DESC";
            $resultado_integrantes_legacy = mysqli_query($mysqli, $sql_integrantes_legacy);
            
            if ($resultado_integrantes_legacy) {
                while ($integrante = mysqli_fetch_assoc($resultado_integrantes_legacy)) {
                    $integrantes[] = $integrante;
                }
            }
        }
        
        // Verificar estado de ficha
        if ($data['estado_ficha'] == 0) {
            echo json_encode([
                'status' => 'ficha_retirada',
                'data' => $data,
                'integrantes' => $integrantes,
                'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.',
                'origen' => 'movimientos_independiente'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $data,
                'integrantes' => $integrantes,
                'origen' => 'movimientos_independiente'
            ]);
        }
    } else {
        // 📋 FALLBACK: Buscar en encventanilla solo como última opción (datos legacy)
        $sql_legacy = "SELECT encventanilla.*, 
                       CASE 
                           WHEN encventanilla.estado_ficha = 0 THEN 'RETIRADA'
                           ELSE 'ACTIVA'
                       END as estado_ficha_texto
                       FROM encventanilla 
                       WHERE encventanilla.doc_encVenta = '$documento'";
        $resultado_legacy = mysqli_query($mysqli, $sql_legacy);
        
        if (mysqli_num_rows($resultado_legacy) > 0) {
            $data = mysqli_fetch_assoc($resultado_legacy);
            
            // Consultar los integrantes legacy
            $sql_integrantes = "SELECT * FROM integventanilla WHERE id_encVenta = '" . $data['id_encVenta'] . "'";
            $resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);
            
            $integrantes = [];
            while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
                $integrantes[] = $integrante;
            }
            
            // Verificar si la ficha está retirada
            if ($data['estado_ficha'] == 0) {
                echo json_encode([
                    'status' => 'ficha_retirada',
                    'data' => $data,
                    'integrantes' => $integrantes,
                    'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.',
                    'origen' => 'encventanilla_legacy'
                ]);
            } else {
                echo json_encode([
                    'status' => 'existe',
                    'data' => $data,
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
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se proporcionó documento para consultar.'
    ]);
}
?>