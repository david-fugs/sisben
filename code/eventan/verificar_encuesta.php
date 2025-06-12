<?php
header('Content-Type: application/json');
include("../../conexion.php");

if ($_POST['doc_encVenta']) {
    $documento = mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']);
    
    // 🚀 PRIORIZAR MOVIMIENTOS: Buscar primero en movimientos (datos más recientes)
    $sql_movimientos = "SELECT m.*, 
                        CASE 
                            WHEN m.estado_ficha = 0 THEN 'RETIRADA'
                            ELSE 'ACTIVA'
                        END as estado_ficha_texto
                        FROM movimientos m 
                        WHERE m.doc_encVenta = '$documento' 
                        ORDER BY m.fecha_movimiento DESC 
                        LIMIT 1";
    $resultado_movimientos = mysqli_query($mysqli, $sql_movimientos);
    
    if (mysqli_num_rows($resultado_movimientos) > 0) {
        // ✅ DATOS ENCONTRADOS EN MOVIMIENTOS (más actuales)
        $data = mysqli_fetch_assoc($resultado_movimientos);
        
        // Consultar integrantes relacionados (buscar por documento en integventanilla)
        $sql_integrantes = "SELECT iv.* FROM integventanilla iv
                           INNER JOIN encventanilla ev ON iv.id_encVenta = ev.id_encVenta
                           WHERE ev.doc_encVenta = '$documento'
                           ORDER BY iv.fecha_alta_integVenta DESC";
        $resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);
        
        $integrantes = [];
        while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
            $integrantes[] = $integrante;
        }
        
        // Verificar estado de ficha
        if ($data['estado_ficha'] == 0) {
            echo json_encode([
                'status' => 'ficha_retirada',
                'data' => $data,
                'integrantes' => $integrantes,
                'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.',
                'origen' => 'movimientos'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $data,
                'integrantes' => $integrantes,
                'origen' => 'movimientos'
            ]);
        }
    } else {
        // 📋 FALLBACK: Si no existe en movimientos, buscar en encventanilla (datos legacy)
        $sql = "SELECT encventanilla.*, 
                CASE 
                    WHEN encventanilla.estado_ficha = 0 THEN 'RETIRADA'
                    ELSE 'ACTIVA'
                END as estado_ficha_texto
                FROM encventanilla 
                WHERE encventanilla.doc_encVenta = '$documento'";
        $resultado = mysqli_query($mysqli, $sql);
        
        if (mysqli_num_rows($resultado) > 0) {
            $data = mysqli_fetch_assoc($resultado);
            
            // Consultar los integrantes de la encuesta
            $sql_integrantes = "SELECT * FROM integventanilla WHERE id_encVenta = '" . $data['id_encVenta'] . "'";
            $resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);
            
            $integrantes = [];
            while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
                $integrantes[] = $integrante;
            }
            
            // Verificar si la ficha está retirada basándose en el campo estado_ficha
            if ($data['estado_ficha'] == 0) {
                echo json_encode([
                    'status' => 'ficha_retirada',
                    'data' => $data,
                    'integrantes' => $integrantes,
                    'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.',
                    'origen' => 'encventanilla'
                ]);
            } else {
                echo json_encode([
                    'status' => 'existe',
                    'nada'=>'nada',
                    'data' => $data,
                    'integrantes' => $integrantes,
                    'origen' => 'encventanilla'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'no_existe',
                'message' => 'El documento no está registrado en la base de encuestas.'
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