<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

include("../../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['doc_encVenta'])) {
    $doc_encVenta = $_POST['doc_encVenta'];
    
    // Consultar la encuesta principal
    $sql_encuesta = "SELECT e.*, 
                            d.nombre_departamento,
                            m.nombre_municipio,
                            c.nombre_com,
                            b.nombre_bar
                     FROM encventanilla e
                     LEFT JOIN departamentos d ON e.departamento_expedicion = d.cod_departamento
                     LEFT JOIN municipios m ON e.ciudad_expedicion = m.cod_municipio
                     LEFT JOIN comunas c ON e.id_com = c.id_com
                     LEFT JOIN barrios b ON e.id_bar = b.id_bar
                     WHERE e.doc_encVenta = ? AND e.estado_encVenta = 1
                     ORDER BY e.fecha_alta_encVenta DESC
                     LIMIT 1";
    
    $stmt = $mysqli->prepare($sql_encuesta);
    $stmt->bind_param("s", $doc_encVenta);
    $stmt->execute();
    $resultado_encuesta = $stmt->get_result();
    
    if ($resultado_encuesta->num_rows > 0) {
        $encuesta = $resultado_encuesta->fetch_assoc();
        
        // Consultar los integrantes de esta encuesta
        $sql_integrantes = "SELECT i.*,
                                   CASE i.rango_integVenta
                                       WHEN 1 THEN '0 - 6'
                                       WHEN 2 THEN '7 - 12'
                                       WHEN 3 THEN '13 - 17'
                                       WHEN 4 THEN '18 - 28'
                                       WHEN 5 THEN '29 - 45'
                                       WHEN 6 THEN '46 - 64'
                                       WHEN 7 THEN 'Mayor o igual a 65'
                                       ELSE 'No especificado'
                                   END as rango_descripcion
                            FROM integventanilla i
                            WHERE i.id_encVenta = ? AND i.estado_integVenta = 1";
        
        $stmt_integrantes = $mysqli->prepare($sql_integrantes);
        $stmt_integrantes->bind_param("i", $encuesta['id_encVenta']);
        $stmt_integrantes->execute();
        $resultado_integrantes = $stmt_integrantes->get_result();
        
        $integrantes = [];
        while ($integrante = $resultado_integrantes->fetch_assoc()) {
            $integrantes[] = $integrante;
        }
        
        // Formatear la respuesta
        $respuesta = [
            'status' => 'success',
            'encuesta' => [
                'id_encVenta' => $encuesta['id_encVenta'],
                'doc_encVenta' => $encuesta['doc_encVenta'],
                'nom_encVenta' => $encuesta['nom_encVenta'],
                'dir_encVenta' => $encuesta['dir_encVenta'],
                'zona_encVenta' => $encuesta['zona_encVenta'],
                'departamento_expedicion' => $encuesta['nombre_departamento'],
                'ciudad_expedicion' => $encuesta['nombre_municipio'],
                'fecha_expedicion' => $encuesta['fecha_expedicion'],
                'comuna' => $encuesta['nombre_com'],
                'barrio' => $encuesta['nombre_bar'],
                'tram_solic_encVenta' => $encuesta['tram_solic_encVenta'],
                'integra_encVenta' => $encuesta['integra_encVenta'],
                'num_ficha_encVenta' => $encuesta['num_ficha_encVenta'],
                'obs_encVenta' => $encuesta['obs_encVenta'],
                'fecha_alta_encVenta' => $encuesta['fecha_alta_encVenta'],
                'sisben_nocturno' => $encuesta['sisben_nocturno'] ? 'Sí' : 'No'
            ],
            'integrantes' => $integrantes,
            'total_integrantes' => count($integrantes)
        ];
        
        echo json_encode($respuesta);
    } else {
        echo json_encode(['status' => 'not_found', 'message' => 'Encuesta no encontrada']);
    }
    
    $stmt->close();
    if (isset($stmt_integrantes)) {
        $stmt_integrantes->close();
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetros inválidos']);
}

$mysqli->close();
?>
