<?php
// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_debug.log');

// Función para logging personalizado
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents(__DIR__ . '/debug.log', $logMessage, FILE_APPEND | LOCK_EX);
}

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

try {
    logError("Iniciando exportarMovimientosCampo.php");
    logError("Autoload cargado correctamente");

    session_start();
    logError("Sesión iniciada");
    
    include("../../conexion.php");
    logError("Conexión incluida");
    
    // Verificar conexión a la base de datos
    if (!$mysqli) {
        logError("Error: No se pudo conectar a la base de datos");
        throw new Exception("Error de conexión a la base de datos");
    }
    logError("Conexión a BD verificada");
    
    date_default_timezone_set("America/Bogota");
    $mysqli->set_charset('utf8');

    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();

    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
    $tipo_movimiento = isset($_GET['tipo_movimiento']) ? $_GET['tipo_movimiento'] : '';
    $id_usu_param = isset($_GET['id_usu']) ? $_GET['id_usu'] : '';
    logError("Parámetros recibidos - fecha_inicio: {$fecha_inicio}, fecha_fin: {$fecha_fin}, tipo_movimiento: {$tipo_movimiento}, id_usu: {$id_usu_param}");

    // ===============================================
    // HOJA 1: MOVIMIENTOS ENCUESTA CAMPO
    // ===============================================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('MOVIMIENTOS CAMPO');
    logError("Hoja 1 - MOVIMIENTOS CAMPO creada");
    
    // Condiciones WHERE para movimientos
    $condiciones = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones[] = "m.fecha_movimiento BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
        logError("Filtro de fechas aplicado: $fecha_inicio_completa a $fecha_fin_completa");
    } else {
        logError("Sin filtro de fechas - exportando todos los registros");
    }
    
    // Filtro de tipo de movimiento
    if (!empty($tipo_movimiento) && $tipo_movimiento != 'todos') {
        $condiciones[] = "m.tipo_movimiento = '$tipo_movimiento'";
        logError("Filtrando por tipo de movimiento: {$tipo_movimiento}");
    }
    
    // Filtro de usuario
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
        $id_usu = $_GET['id_usu'];
        $condiciones[] = "m.id_usu = '$id_usu'";
        logError("Filtrando por usuario específico: {$id_usu}");
    } else {
        logError("Exportando TODOS los encuestadores en el rango de fechas");
    }

    $where_movimientos = '';
    if (count($condiciones) > 0) {
        $where_movimientos = 'WHERE ' . implode(' AND ', $condiciones);
    }
    
    logError("WHERE construido: " . $where_movimientos);

    // Consulta para movimientos
    $sql_movimientos = "
    SELECT m.*, 
           b.nombre_bar AS barrio_nombre, 
           c.nombre_com AS comuna_nombre,
           d.nombre_departamento AS departamento_nombre, 
           mu.nombre_municipio as ciudad_nombre,
           u.nombre AS nombre_usuario,
           CASE 
               WHEN m.estado_ficha = '0' THEN 'FICHA RETIRADA'
               WHEN m.estado_ficha = '1' THEN 'ACTIVA'
           END as estado_ficha_texto
    FROM movimientos_encuesta_campo m
    LEFT JOIN barrios b ON m.id_bar = b.id_bar
    LEFT JOIN comunas c ON m.id_com = c.id_com
    LEFT JOIN departamentos d ON m.departamento_expedicion = d.cod_departamento
    LEFT JOIN municipios mu ON m.ciudad_expedicion = mu.cod_municipio
    LEFT JOIN usuarios u ON m.id_usu = u.id_usu
    $where_movimientos
    ORDER BY m.fecha_movimiento DESC
    ";
    $res_movimientos = mysqli_query($mysqli, $sql_movimientos);
    if ($res_movimientos === false) {
        logError("Error en la consulta de movimientos: " . mysqli_error($mysqli));
        echo "Error en la consulta de movimientos: " . mysqli_error($mysqli);
        exit;
    }
    
    $num_rows = mysqli_num_rows($res_movimientos);
    logError("Consulta de movimientos ejecutada correctamente. Filas encontradas: " . $num_rows);
    logError("SQL ejecutado: " . $sql_movimientos);

    // Configuración de estilos
    $styleHeader = [
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffd880']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    // Aplicar estilos a la hoja 1
    $sheet1->getStyle('A1:X1')->applyFromArray($styleHeader);

    // Encabezados para MOVIMIENTOS
    $sheet1->setCellValue('A1', 'ID MOVIMIENTO');
    $sheet1->setCellValue('B1', 'FECHA MOVIMIENTO');
    $sheet1->setCellValue('C1', 'TIPO MOVIMIENTO');
    $sheet1->setCellValue('D1', 'ESTADO FICHA');
    $sheet1->setCellValue('E1', 'DOCUMENTO');
    $sheet1->setCellValue('F1', 'TIPO DOCUMENTO');
    $sheet1->setCellValue('G1', 'NOMBRE');
    $sheet1->setCellValue('H1', 'FECHA NACIMIENTO');
    $sheet1->setCellValue('I1', 'FECHA EXPEDICION');
    $sheet1->setCellValue('J1', 'DEPARTAMENTO EXPEDICION');
    $sheet1->setCellValue('K1', 'CIUDAD EXPEDICION');
    $sheet1->setCellValue('L1', 'DIRECCION');
    $sheet1->setCellValue('M1', 'ZONA');
    $sheet1->setCellValue('N1', 'COMUNA');
    $sheet1->setCellValue('O1', 'BARRIO');
    $sheet1->setCellValue('P1', 'QUE OTRO BARRIO');
    $sheet1->setCellValue('Q1', 'CANTIDAD INTEGRANTES');
    $sheet1->setCellValue('R1', 'NUMERO FICHA');
    $sheet1->setCellValue('S1', 'SISBEN NOCTURNO');
    $sheet1->setCellValue('T1', 'OBSERVACIONES');
    $sheet1->setCellValue('U1', 'USUARIO REGISTRO');
    $sheet1->setCellValue('V1', 'FECHA REGISTRO');

    // Ajustar el ancho de las columnas
    $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];
    foreach ($columns as $col) {
        $sheet1->getColumnDimension($col)->setWidth(20);
    }

    // Llenar datos de movimientos
    $row = 2;
    while ($movimiento = mysqli_fetch_assoc($res_movimientos)) {
        $sheet1->setCellValue('A' . $row, $movimiento['id_movimiento']);
        $sheet1->setCellValue('B' . $row, $movimiento['fecha_movimiento']);
        $sheet1->setCellValue('C' . $row, strtoupper($movimiento['tipo_movimiento']));
        $sheet1->setCellValue('D' . $row, $movimiento['estado_ficha_texto']);
        $sheet1->setCellValue('E' . $row, $movimiento['doc_encCampo']);
        $sheet1->setCellValue('F' . $row, strtoupper($movimiento['tipo_documento']));
        $sheet1->setCellValue('G' . $row, $movimiento['nom_encCampo']);
        $sheet1->setCellValue('H' . $row, $movimiento['fecha_nacimiento']);
        $sheet1->setCellValue('I' . $row, $movimiento['fecha_expedicion']);
        $sheet1->setCellValue('J' . $row, $movimiento['departamento_nombre'] ?? '');
        $sheet1->setCellValue('K' . $row, $movimiento['ciudad_nombre'] ?? '');
        $sheet1->setCellValue('L' . $row, $movimiento['dir_encCampo']);
        $sheet1->setCellValue('M' . $row, $movimiento['zona_encCampo']);
        $sheet1->setCellValue('N' . $row, $movimiento['comuna_nombre'] ?? '');
        $sheet1->setCellValue('O' . $row, $movimiento['barrio_nombre'] ?? '');
        $sheet1->setCellValue('P' . $row, $movimiento['que_otro_bar_encCampo']);
        $sheet1->setCellValue('Q' . $row, $movimiento['cant_integrantes_encCampo']);
        $sheet1->setCellValue('R' . $row, $movimiento['num_ficha_encCampo']);
        $sheet1->setCellValue('S' . $row, $movimiento['sisben_nocturno']);
        $sheet1->setCellValue('T' . $row, $movimiento['obs_encCampo']);
        $sheet1->setCellValue('U' . $row, $movimiento['nombre_usuario']);
        $sheet1->setCellValue('V' . $row, $movimiento['fecha_registro']);
        $row++;
    }

    logError("Datos de movimientos llenados en el Excel. Total filas: " . ($row - 2));

    // ===============================================
    // HOJA 2: ESTADÍSTICAS DE MOVIMIENTOS
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('ESTADISTICAS');
    logError("Hoja 2 - ESTADISTICAS creada");
    
    // Aplicar estilos a la hoja 2
    $sheet2->getStyle('A1:B1')->applyFromArray($styleHeader);
    $sheet2->getColumnDimension('A')->setWidth(40);
    $sheet2->getColumnDimension('B')->setWidth(15);

    // Consultas para estadísticas
    $sql_stats = "
    SELECT 
        COUNT(*) as total_movimientos,
        COUNT(CASE WHEN tipo_movimiento = 'inclusion' THEN 1 END) as total_inclusion,
        COUNT(CASE WHEN tipo_movimiento = 'Inconformidad por clasificacion' THEN 1 END) as total_inconformidad,
        COUNT(CASE WHEN tipo_movimiento = 'modificacion datos persona' THEN 1 END) as total_modificacion,
        COUNT(CASE WHEN tipo_movimiento = 'Retiro ficha' THEN 1 END) as total_retiro_ficha,
        COUNT(CASE WHEN tipo_movimiento = 'Retiro personas' THEN 1 END) as total_retiro_personas,
        COUNT(CASE WHEN estado_ficha = '1' THEN 1 END) as total_activas,
        COUNT(CASE WHEN estado_ficha = '0' THEN 1 END) as total_retiradas,
        SUM(cant_integrantes_encCampo) as total_integrantes
    FROM movimientos_encuesta_campo m
    $where_movimientos
    ";
    
    $res_stats = mysqli_query($mysqli, $sql_stats);
    $stats = mysqli_fetch_assoc($res_stats);
    
    // Encabezados de estadísticas
    $sheet2->setCellValue('A1', 'CONCEPTO');
    $sheet2->setCellValue('B1', 'CANTIDAD');
    
    // Datos de estadísticas
    $sheet2->setCellValue('A2', 'TOTAL MOVIMIENTOS');
    $sheet2->setCellValue('B2', $stats['total_movimientos']);
    $sheet2->setCellValue('A3', 'TOTAL INCLUSIONES');
    $sheet2->setCellValue('B3', $stats['total_inclusion']);
    $sheet2->setCellValue('A4', 'TOTAL INCONFORMIDADES');
    $sheet2->setCellValue('B4', $stats['total_inconformidad']);
    $sheet2->setCellValue('A5', 'TOTAL MODIFICACIONES');
    $sheet2->setCellValue('B5', $stats['total_modificacion']);
    $sheet2->setCellValue('A6', 'TOTAL RETIROS DE FICHA');
    $sheet2->setCellValue('B6', $stats['total_retiro_ficha']);
    $sheet2->setCellValue('A7', 'TOTAL RETIROS DE PERSONAS');
    $sheet2->setCellValue('B7', $stats['total_retiro_personas']);
    $sheet2->setCellValue('A8', '');
    $sheet2->setCellValue('A9', 'FICHAS ACTIVAS');
    $sheet2->setCellValue('B9', $stats['total_activas']);
    $sheet2->setCellValue('A10', 'FICHAS RETIRADAS');
    $sheet2->setCellValue('B10', $stats['total_retiradas']);
    $sheet2->setCellValue('A11', '');
    $sheet2->setCellValue('A12', 'TOTAL INTEGRANTES');
    $sheet2->setCellValue('B12', $stats['total_integrantes'] ?? 0);

    logError("Estadísticas generadas correctamente");

    // Generar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $filename = 'Movimientos_Encuesta_Campo_' . date('Ymd_His') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    logError("Archivo Excel generado y enviado correctamente: " . $filename);

} catch (Exception $e) {
    logError("ERROR: " . $e->getMessage());
    logError("Trace: " . $e->getTraceAsString());
    echo "Error: " . $e->getMessage();
}
?>
