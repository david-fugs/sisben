<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

include("../../conexion.php");
header("Content-Type: application/json;charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_integrante'])) {
    $id_integrante = $_POST['id_integrante'];
    $id_usu = $_SESSION['id_usu'];
    $tipo_usu = $_SESSION['tipo_usu'];

    try {
        // Verificar que el integrante existe y obtener información del movimiento        
        $sql_verificar = "SELECT im.*, m.id_usu as propietario_movimiento 
                                  FROM integmovimientos_independiente im
                          INNER JOIN movimientos m ON im.doc_encVenta = m.doc_encVenta
                          WHERE im.id_integmov_indep = '$id_integrante' AND im.estado_integMovIndep = 1";

        $resultado = mysqli_query($mysqli, $sql_verificar);

        if (!$resultado || mysqli_num_rows($resultado) == 0) {
            throw new Exception("Integrante no encontrado");
        }

        $integrante = mysqli_fetch_assoc($resultado);

        // Verificar permisos (solo el dueño del movimiento o administrador puede eliminar)
        if ($tipo_usu != '1' && $integrante['propietario_movimiento'] != $id_usu) {
            throw new Exception("Sin permisos para eliminar este integrante");
        }

        // Marcar como eliminado (cambiar estado a 0)        
        $sql_eliminar = "UPDATE integmovimientos_independiente 
                         SET estado_integMovIndep = 0, fecha_edit_integMovIndep = NOW() 
                         WHERE id_integmov_indep = '$id_integrante'";

        if (!mysqli_query($mysqli, $sql_eliminar)) {
            throw new Exception("Error al eliminar integrante: " . mysqli_error($mysqli));
        }

        // Contar integrantes activos restantes
        $sql_contar = "SELECT COUNT(*) as total FROM integmovimientos_independiente 
                       WHERE doc_encVenta = '{$integrante['doc_encVenta']}' AND estado_integMovIndep = 1";
        $resultado_contar = mysqli_query($mysqli, $sql_contar);
        $total_integrantes = mysqli_fetch_assoc($resultado_contar)['total'];

        // Actualizar contador en la tabla movimientos
        $sql_actualizar_contador = "UPDATE movimientos 
                                    SET integra_encVenta = '$total_integrantes' 
                                    WHERE doc_encVenta = '{$integrante['doc_encVenta']}'";
        mysqli_query($mysqli, $sql_actualizar_contador);

        echo json_encode([
            'success' => true,
            'message' => 'Integrante eliminado exitosamente',
            'total_integrantes' => $total_integrantes
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos'
    ]);
}
