<?php
header('Content-Type: application/json');
include("../../conexion.php");

if ($_POST['doc_encVenta']) {
    $documento = mysqli_real_escape_string($mysqli, $_POST['doc_encVenta']);
      // Consultar en la tabla encventanilla 
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
                'message' => '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA. No se pueden realizar movimientos.'
            ]);
        } else {
            echo json_encode([
                'status' => 'existe',
                'data' => $data,
                'integrantes' => $integrantes
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'no_existe',
            'message' => 'El documento no está registrado en la base de encuestas.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se proporcionó documento para consultar.'
    ]);
}
?>