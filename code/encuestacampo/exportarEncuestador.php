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
    logError("Iniciando exportarEncuestador.php para encuesta campo");
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
    $id_usu_param = isset($_GET['id_usu']) ? $_GET['id_usu'] : '';
    logError("Parámetros recibidos - fecha_inicio: {$fecha_inicio}, fecha_fin: {$fecha_fin}, id_usu: {$id_usu_param}");

    // ===============================================
    // HOJA 1: ENCUESTAS CAMPO
    // ===============================================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('ENCUESTAS CAMPO');
    logError("Hoja 1 - ENCUESTAS CAMPO creada");

    // Condiciones WHERE para encuestas
    $condiciones = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones[] = "ec.fecha_alta_encCampo BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
        logError("Filtro de fechas aplicado: $fecha_inicio_completa a $fecha_fin_completa");
    } else {
        logError("Sin filtro de fechas - exportando todos los registros");
    }
    
    // Filtro de usuario
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
        $id_usu = $_GET['id_usu'];
        $condiciones[] = "ec.id_usu = '$id_usu'";
        logError("Filtrando por usuario específico: {$id_usu}");
    } else {
        logError("Exportando TODOS los encuestadores en el rango de fechas");
    }

    $where_encuestas = '';
    if (count($condiciones) > 0) {
        $where_encuestas = 'WHERE ' . implode(' AND ', $condiciones);
    }
    
    logError("WHERE construido: " . $where_encuestas);

    // Consulta para encuestas campo
    $sql_encuestas = "
    SELECT ec.*, 
           b.nombre_bar AS barrio_nombre, 
           c.nombre_com AS comuna_nombre,
           u.nombre AS nombre_usuario,
           CASE 
               WHEN ec.zona_encCampo = 'U' THEN 'URBANA'
               WHEN ec.zona_encCampo = 'R' THEN 'RURAL'
               ELSE ec.zona_encCampo
           END as zona_texto
    FROM encCampo ec
    LEFT JOIN barrios b ON ec.id_bar = b.id_bar
    LEFT JOIN comunas c ON ec.id_com = c.id_com
    LEFT JOIN usuarios u ON ec.id_usu = u.id_usu
    $where_encuestas
    ORDER BY ec.fecha_alta_encCampo DESC
    ";
    $res_encuestas = mysqli_query($mysqli, $sql_encuestas);
    if ($res_encuestas === false) {
        logError("Error en la consulta de encuestas: " . mysqli_error($mysqli));
        echo "Error en la consulta de encuestas: " . mysqli_error($mysqli);
        exit;
    }
    
    $num_rows = mysqli_num_rows($res_encuestas);
    logError("Consulta de encuestas ejecutada correctamente. Filas encontradas: " . $num_rows);

    // Configuración de estilos
    $styleHeader = [
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffd880']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    // Aplicar estilos a la hoja 1
    $sheet1->getStyle('A1:T1')->applyFromArray($styleHeader);

    // Encabezados para ENCUESTAS
    $sheet1->setCellValue('A1', 'ID ENCUESTA');
    $sheet1->setCellValue('B1', 'FECHA PRESENTACION');
    $sheet1->setCellValue('C1', 'FECHA REALIZACION');
    $sheet1->setCellValue('D1', 'DOCUMENTO');
    $sheet1->setCellValue('E1', 'NOMBRE');
    $sheet1->setCellValue('F1', 'DIRECCION');
    $sheet1->setCellValue('G1', 'ZONA');
    $sheet1->setCellValue('H1', 'COMUNA');
    $sheet1->setCellValue('I1', 'BARRIO');
    $sheet1->setCellValue('J1', 'CORREGIMIENTO');
    $sheet1->setCellValue('K1', 'VEREDA');
    $sheet1->setCellValue('L1', 'OTRO BARRIO/VEREDA');
    $sheet1->setCellValue('M1', 'ESTADO FICHA');
    $sheet1->setCellValue('N1', 'CANTIDAD INTEGRANTES');
    $sheet1->setCellValue('O1', 'NUMERO FICHA');
    $sheet1->setCellValue('P1', 'PROCEDENCIA');
    $sheet1->setCellValue('Q1', 'OBSERVACIONES');
    $sheet1->setCellValue('R1', 'ESTADO');
    $sheet1->setCellValue('S1', 'USUARIO REGISTRO');
    $sheet1->setCellValue('T1', 'FECHA REGISTRO');

    // Ajustar el ancho de las columnas
    $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    foreach ($columns as $col) {
        $sheet1->getColumnDimension($col)->setWidth(20);
    }

    // Llenar datos de encuestas
    $row = 2;
    while ($encuesta = mysqli_fetch_assoc($res_encuestas)) {
        $sheet1->setCellValue('A' . $row, $encuesta['id_encCampo']);
        $sheet1->setCellValue('B' . $row, $encuesta['fec_pre_encCampo']);
        $sheet1->setCellValue('C' . $row, $encuesta['fec_rea_encCampo']);
        $sheet1->setCellValue('D' . $row, $encuesta['doc_encCampo']);
        $sheet1->setCellValue('E' . $row, $encuesta['nom_encCampo']);
        $sheet1->setCellValue('F' . $row, $encuesta['dir_encCampo']);
        $sheet1->setCellValue('G' . $row, $encuesta['zona_texto']);
        $sheet1->setCellValue('H' . $row, $encuesta['comuna_nombre'] ?? '');
        $sheet1->setCellValue('I' . $row, $encuesta['barrio_nombre'] ?? '');
        $sheet1->setCellValue('J' . $row, $encuesta['id_correg'] ?? '');
        $sheet1->setCellValue('K' . $row, $encuesta['id_vere'] ?? '');
        $sheet1->setCellValue('L' . $row, $encuesta['otro_bar_ver_encCampo'] ?? '');
        $sheet1->setCellValue('M' . $row, $encuesta['est_fic_encCampo'] ?? '');
        $sheet1->setCellValue('N' . $row, $encuesta['integra_encCampo'] ?? '');
        $sheet1->setCellValue('O' . $row, $encuesta['num_ficha_encCampo']);
        $sheet1->setCellValue('P' . $row, $encuesta['proc_encCampo'] ?? '');
        $sheet1->setCellValue('Q' . $row, $encuesta['obs_encCampo'] ?? '');
        $sheet1->setCellValue('R' . $row, $encuesta['estado_encCampo'] == 1 ? 'ACTIVO' : 'INACTIVO');
        $sheet1->setCellValue('S' . $row, $encuesta['nombre_usuario']);
        $sheet1->setCellValue('T' . $row, $encuesta['fecha_alta_encCampo']);
        $row++;
    }

    logError("Datos de encuestas llenados en el Excel. Total filas: " . ($row - 2));

    // ===============================================
    // HOJA 2: MOVIMIENTOS ENCUESTA CAMPO
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('MOVIMIENTOS CAMPO');
    logError("Hoja 2 - MOVIMIENTOS CAMPO creada");
    
    // Condiciones WHERE para movimientos (usar las mismas condiciones)
    $where_movimientos = $where_encuestas;
    if (!empty($where_encuestas)) {
        $where_movimientos = str_replace('ec.', 'm.', $where_encuestas);
        $where_movimientos = str_replace('fecha_alta_encCampo', 'fecha_movimiento', $where_movimientos);
    }
    
    logError("WHERE movimientos construido: " . $where_movimientos);

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
    
    $num_rows_mov = mysqli_num_rows($res_movimientos);
    logError("Consulta de movimientos ejecutada correctamente. Filas encontradas: " . $num_rows_mov);
    
    // Aplicar estilos a la hoja 2
    $sheet2->getStyle('A1:U1')->applyFromArray($styleHeader);

    // Encabezados para MOVIMIENTOS
    $sheet2->setCellValue('A1', 'ID MOVIMIENTO');
    $sheet2->setCellValue('B1', 'FECHA MOVIMIENTO');
    $sheet2->setCellValue('C1', 'TIPO MOVIMIENTO');
    $sheet2->setCellValue('D1', 'ESTADO FICHA');
    $sheet2->setCellValue('E1', 'DOCUMENTO');
    $sheet2->setCellValue('F1', 'TIPO DOCUMENTO');
    $sheet2->setCellValue('G1', 'NOMBRE');
    $sheet2->setCellValue('H1', 'FECHA NACIMIENTO');
    $sheet2->setCellValue('I1', 'FECHA EXPEDICION');
    $sheet2->setCellValue('J1', 'DEPARTAMENTO EXPEDICION');
    $sheet2->setCellValue('K1', 'CIUDAD EXPEDICION');
    $sheet2->setCellValue('L1', 'DIRECCION');
    $sheet2->setCellValue('M1', 'ZONA');
    $sheet2->setCellValue('N1', 'COMUNA');
    $sheet2->setCellValue('O1', 'BARRIO');
    $sheet2->setCellValue('P1', 'OTRO BARRIO');
    $sheet2->setCellValue('Q1', 'CANTIDAD INTEGRANTES');
    $sheet2->setCellValue('R1', 'NUMERO FICHA');
    $sheet2->setCellValue('S1', 'OBSERVACIONES');
    $sheet2->setCellValue('T1', 'USUARIO REGISTRO');
    $sheet2->setCellValue('U1', 'FECHA REGISTRO');

    // Ajustar ancho de columnas
    $columns2 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
    foreach ($columns2 as $col) {
        $sheet2->getColumnDimension($col)->setWidth(20);
    }

    // Llenar datos de movimientos
    $row2 = 2;
    while ($movimiento = mysqli_fetch_assoc($res_movimientos)) {
        $sheet2->setCellValue('A' . $row2, $movimiento['id_movimiento']);
        $sheet2->setCellValue('B' . $row2, $movimiento['fecha_movimiento']);
        $sheet2->setCellValue('C' . $row2, strtoupper($movimiento['tipo_movimiento']));
        $sheet2->setCellValue('D' . $row2, $movimiento['estado_ficha_texto']);
        $sheet2->setCellValue('E' . $row2, $movimiento['doc_encCampo']);
        $sheet2->setCellValue('F' . $row2, strtoupper($movimiento['tipo_documento']));
        $sheet2->setCellValue('G' . $row2, $movimiento['nom_encCampo']);
        $sheet2->setCellValue('H' . $row2, $movimiento['fecha_nacimiento']);
        $sheet2->setCellValue('I' . $row2, $movimiento['fecha_expedicion']);
        $sheet2->setCellValue('J' . $row2, $movimiento['departamento_nombre'] ?? '');
        $sheet2->setCellValue('K' . $row2, $movimiento['ciudad_nombre'] ?? '');
        $sheet2->setCellValue('L' . $row2, $movimiento['dir_encCampo']);
        $sheet2->setCellValue('M' . $row2, $movimiento['zona_encCampo']);
        $sheet2->setCellValue('N' . $row2, $movimiento['comuna_nombre'] ?? '');
        $sheet2->setCellValue('O' . $row2, $movimiento['barrio_nombre'] ?? '');
        $sheet2->setCellValue('P' . $row2, $movimiento['otro_bar_ver_encCampo'] ?? '');
        $sheet2->setCellValue('Q' . $row2, $movimiento['integra_encCampo'] ?? '');
        $sheet2->setCellValue('R' . $row2, $movimiento['num_ficha_encCampo']);
        $sheet2->setCellValue('S' . $row2, $movimiento['obs_encCampo']);
        $sheet2->setCellValue('T' . $row2, $movimiento['nombre_usuario']);
        $sheet2->setCellValue('U' . $row2, $movimiento['fec_reg_encCampo'] ?? $movimiento['fecha_alta_movimiento'] ?? '');
        $row2++;
    }

    logError("Datos de movimientos llenados en el Excel. Total filas: " . ($row2 - 2));

    // ===============================================
    // HOJA 3: INTEGRANTES
    // ===============================================
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('INTEGRANTES');
    logError("Hoja 3 - INTEGRANTES creada");
    
    // Consulta para integrantes relacionados con las encuestas campo
    // Solo traer integrantes de las encuestas que están en el rango de fechas y usuario filtrado
    $sql_integrantes = "
    SELECT ic.*, 
           ec.doc_encCampo,
           ec.nom_encCampo,
           ec.num_ficha_encCampo,
           ec.fecha_alta_encCampo,
           u.nombre AS nombre_usuario,
           CASE 
               WHEN ic.rango_integCampo = '1' THEN '0 - 6'
               WHEN ic.rango_integCampo = '2' THEN '7 - 12'
               WHEN ic.rango_integCampo = '3' THEN '13 - 17'
               WHEN ic.rango_integCampo = '4' THEN '18 - 28'
               WHEN ic.rango_integCampo = '5' THEN '29 - 45'
               WHEN ic.rango_integCampo = '6' THEN '46 - 64'
               WHEN ic.rango_integCampo = '7' THEN 'Mayor o igual a 65'
               ELSE ic.rango_integCampo
           END as rango_texto
    FROM integCampo ic
    INNER JOIN encCampo ec ON ic.id_encCampo = ec.id_encCampo
    LEFT JOIN usuarios u ON ic.id_usu = u.id_usu
    $where_encuestas
    ORDER BY ec.fecha_alta_encCampo DESC, ic.id_integCampo ASC
    ";
    $res_integrantes = mysqli_query($mysqli, $sql_integrantes);
    if ($res_integrantes === false) {
        logError("Error en la consulta de integrantes: " . mysqli_error($mysqli));
        echo "Error en la consulta de integrantes: " . mysqli_error($mysqli);
        exit;
    }
    
    $num_rows_integ = mysqli_num_rows($res_integrantes);
    logError("Consulta de integrantes ejecutada correctamente. Filas encontradas: " . $num_rows_integ);
    
    // Aplicar estilos a la hoja 3
    $sheet3->getStyle('A1:K1')->applyFromArray($styleHeader);

    // Encabezados para INTEGRANTES
    $sheet3->setCellValue('A1', 'ID INTEGRANTE');
    $sheet3->setCellValue('B1', 'DOCUMENTO TITULAR');
    $sheet3->setCellValue('C1', 'NOMBRE TITULAR');
    $sheet3->setCellValue('D1', 'NUMERO FICHA');
    $sheet3->setCellValue('E1', 'FECHA ENCUESTA');
    $sheet3->setCellValue('F1', 'CANTIDAD');
    $sheet3->setCellValue('G1', 'GENERO');
    $sheet3->setCellValue('H1', 'RANGO EDAD');
    $sheet3->setCellValue('I1', 'ESTADO');
    $sheet3->setCellValue('J1', 'USUARIO REGISTRO');
    $sheet3->setCellValue('K1', 'FECHA REGISTRO');

    // Ajustar ancho de columnas
    $columns3 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
    foreach ($columns3 as $col) {
        $sheet3->getColumnDimension($col)->setWidth(20);
    }

    // Llenar datos de integrantes
    $row3 = 2;
    while ($integrante = mysqli_fetch_assoc($res_integrantes)) {
        $sheet3->setCellValue('A' . $row3, $integrante['id_integCampo']);
        $sheet3->setCellValue('B' . $row3, $integrante['doc_encCampo']);
        $sheet3->setCellValue('C' . $row3, $integrante['nom_encCampo']);
        $sheet3->setCellValue('D' . $row3, $integrante['num_ficha_encCampo']);
        $sheet3->setCellValue('E' . $row3, $integrante['fecha_alta_encCampo']);
        $sheet3->setCellValue('F' . $row3, $integrante['cant_integCampo']);
        $sheet3->setCellValue('G' . $row3, $integrante['gen_integCampo']);
        $sheet3->setCellValue('H' . $row3, $integrante['rango_texto']);
        $sheet3->setCellValue('I' . $row3, $integrante['estado_integCampo'] == 1 ? 'ACTIVO' : 'INACTIVO');
        $sheet3->setCellValue('J' . $row3, $integrante['nombre_usuario']);
        $sheet3->setCellValue('K' . $row3, $integrante['fecha_alta_integCampo']);
        $row3++;
    }

    logError("Datos de integrantes llenados en el Excel. Total filas: " . ($row3 - 2));

    // Generar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $filename = 'Encuestas_Campo_' . date('Ymd_His') . '.xlsx';
    
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
