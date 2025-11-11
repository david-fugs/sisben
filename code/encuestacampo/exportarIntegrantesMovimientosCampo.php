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
    logError("Iniciando exportarIntegrantesMovimientosCampo.php");
    
    session_start();
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");
    $mysqli->set_charset('utf8');
    
    // Verificar conexión a la base de datos
    if (!$mysqli) {
        logError("Error: No se pudo conectar a la base de datos");
        throw new Exception("Error de conexión a la base de datos");
    }
    
    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();

    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
    $tipo_movimiento = isset($_GET['tipo_movimiento']) ? $_GET['tipo_movimiento'] : '';
    $id_usu_param = isset($_GET['id_usu']) ? $_GET['id_usu'] : '';
    logError("Parámetros recibidos - fecha_inicio: {$fecha_inicio}, fecha_fin: {$fecha_fin}, tipo_movimiento: {$tipo_movimiento}, id_usu: {$id_usu_param}");

    // ===============================================
    // HOJA 1: TOTALES GENERALES DE INTEGRANTES
    // ===============================================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('TOTALES INTEGRANTES');
    logError("Hoja 1 - TOTALES INTEGRANTES creada");
    
    // Condiciones WHERE para filtros
    $condiciones = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones[] = "m.fecha_movimiento BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
        logError("Filtro de fechas aplicado: $fecha_inicio_completa a $fecha_fin_completa");
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
    }

    $where_clause = '';
    if (count($condiciones) > 0) {
        $where_clause = 'WHERE ' . implode(' AND ', $condiciones);
    }
    
    // Consulta para totales de integrantes de movimientos
    $sql_integrantes = "
    SELECT COUNT(*) AS total_integrantes,
    COUNT(CASE WHEN ic.gen_integCampo = 'M' THEN 1 END) AS total_masculino,
    COUNT(CASE WHEN ic.gen_integCampo = 'F' THEN 1 END) AS total_femenino,
    COUNT(CASE WHEN ic.gen_integCampo = 'O' THEN 1 END) AS total_otro_genero,
    COUNT(CASE WHEN ic.rango_integCampo = '0 - 6' OR ic.rango_integCampo = '0 - 5' THEN 1 END) AS total_0_5,
    COUNT(CASE WHEN ic.rango_integCampo = '6 - 12' THEN 1 END) AS total_6_12,
    COUNT(CASE WHEN ic.rango_integCampo = '13 - 17' THEN 1 END) AS total_13_17,
    COUNT(CASE WHEN ic.rango_integCampo = '18 - 28' THEN 1 END) AS total_18_28,
    COUNT(CASE WHEN ic.rango_integCampo = '29 - 45' THEN 1 END) AS total_29_45,
    COUNT(CASE WHEN ic.rango_integCampo = '46 - 64' THEN 1 END) AS total_46_64,
    COUNT(CASE WHEN ic.rango_integCampo = 'Mayor o igual a 65' THEN 1 END) AS total_mayor_65,
    COUNT(CASE WHEN ic.orientacionSexual = 'Heterosexual' THEN 1 END) AS total_heterosexual,
    COUNT(CASE WHEN ic.orientacionSexual = 'Homosexual' THEN 1 END) AS total_homosexual,
    COUNT(CASE WHEN ic.orientacionSexual = 'Bisexual' THEN 1 END) AS total_bisexual,
    COUNT(CASE WHEN ic.orientacionSexual = 'Asexual' THEN 1 END) AS total_asexual,
    COUNT(CASE WHEN ic.orientacionSexual = 'Otro' THEN 1 END) AS total_otro_orientacion,
    COUNT(CASE WHEN ic.condicionDiscapacidad = 'Si' THEN 1 END) AS total_con_discapacidad,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Visual' THEN 1 END) AS total_visual,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Auditiva' THEN 1 END) AS total_auditiva,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Física' THEN 1 END) AS total_fisica,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Intelectual' THEN 1 END) AS total_intelectual,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Psicosocial' THEN 1 END) AS total_psicosocial,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Múltiple' THEN 1 END) AS total_multiple,
    COUNT(CASE WHEN ic.tipoDiscapacidad = 'Sordoceguera' THEN 1 END) AS total_sordoceguera,
    COUNT(CASE WHEN ic.grupoEtnico = 'Negro(a) / Mulato(a) / Afrocolombiano(a)' THEN 1 END) AS total_afrocolombiano,
    COUNT(CASE WHEN ic.grupoEtnico = 'Indigena' THEN 1 END) AS total_indigena,
    COUNT(CASE WHEN ic.grupoEtnico = 'Raizal' THEN 1 END) AS total_raizal,
    COUNT(CASE WHEN ic.grupoEtnico = 'Palenquero de San Basilio' THEN 1 END) AS total_palenquero,
    COUNT(CASE WHEN ic.grupoEtnico = 'Gitano (rom)' THEN 1 END) AS total_gitanorom,
    COUNT(CASE WHEN ic.grupoEtnico = 'Mestizo' THEN 1 END) AS total_mestizo,
    COUNT(CASE WHEN ic.grupoEtnico = 'Ninguno' THEN 1 END) AS total_ninguno_etnico,
    COUNT(CASE WHEN ic.victima = 'Si' THEN 1 END) AS total_victimas,
    COUNT(CASE WHEN ic.mujerGestante = 'Si' THEN 1 END) AS total_mujeres_gestantes,
    COUNT(CASE WHEN ic.cabezaFamilia = 'Si' THEN 1 END) AS total_cabezas_familia,
    COUNT(CASE WHEN ic.experienciaMigratoria = 'Si' THEN 1 END) AS total_experiencia_migratoria,
    COUNT(CASE WHEN ic.seguridadSalud = 'Regimen Contributivo' THEN 1 END) AS total_contributivo,
    COUNT(CASE WHEN ic.seguridadSalud = 'Regimen Subsidiado' THEN 1 END) AS total_subsidiado,
    COUNT(CASE WHEN ic.seguridadSalud = 'Poblacion Vinculada' THEN 1 END) AS total_vinculada,
    COUNT(CASE WHEN ic.seguridadSalud = 'Ninguno' THEN 1 END) AS total_sin_seguridad
    FROM integ_movimientos_encuesta_campo ic
    JOIN movimientos_encuesta_campo m ON ic.id_movimiento = m.id_movimiento
    $where_clause
    ";
    
    $res_integrantes = mysqli_query($mysqli, $sql_integrantes);
    if ($res_integrantes === false) {
        logError("Error en la consulta de integrantes: " . mysqli_error($mysqli));
        throw new Exception("Error en la consulta de integrantes: " . mysqli_error($mysqli));
    }
    
    $totales = mysqli_fetch_assoc($res_integrantes);
    logError("Consulta de totales ejecutada correctamente. Total integrantes: " . $totales['total_integrantes']);

    // Configuración de estilos
    $styleHeader = [
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffd880']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    // Aplicar estilos a encabezados
    $sheet1->getStyle('A1:B1')->applyFromArray($styleHeader);
    $sheet1->getStyle('A4:B4')->applyFromArray($styleHeader);
    $sheet1->getStyle('A9:B9')->applyFromArray($styleHeader);
    $sheet1->getStyle('A14:B14')->applyFromArray($styleHeader);
    $sheet1->getStyle('A23:B23')->applyFromArray($styleHeader);
    $sheet1->getStyle('A34:B34')->applyFromArray($styleHeader);
    $sheet1->getStyle('A41:B41')->applyFromArray($styleHeader);

    // Ajustar el ancho de las columnas
    $sheet1->getColumnDimension('A')->setWidth(40);
    $sheet1->getColumnDimension('B')->setWidth(15);

    // ===== SECCIÓN 1: TOTALES GENERALES =====
    $sheet1->setCellValue('A1', 'TOTALES GENERALES');
    $sheet1->setCellValue('B1', 'CANTIDAD');
    $sheet1->setCellValue('A2', 'Total Integrantes');
    $sheet1->setCellValue('B2', $totales['total_integrantes']);

    // ===== SECCIÓN 2: POR GÉNERO =====
    $sheet1->setCellValue('A4', 'POR GÉNERO');
    $sheet1->setCellValue('B4', 'CANTIDAD');
    $sheet1->setCellValue('A5', 'Masculino');
    $sheet1->setCellValue('B5', $totales['total_masculino']);
    $sheet1->setCellValue('A6', 'Femenino');
    $sheet1->setCellValue('B6', $totales['total_femenino']);
    $sheet1->setCellValue('A7', 'Otro');
    $sheet1->setCellValue('B7', $totales['total_otro_genero']);

    // ===== SECCIÓN 3: POR RANGO DE EDAD =====
    $sheet1->setCellValue('A9', 'POR RANGO DE EDAD');
    $sheet1->setCellValue('B9', 'CANTIDAD');
    $sheet1->setCellValue('A10', '0 - 5 años');
    $sheet1->setCellValue('B10', $totales['total_0_5']);
    $sheet1->setCellValue('A11', '6 - 12 años');
    $sheet1->setCellValue('B11', $totales['total_6_12']);
    $sheet1->setCellValue('A12', '13 - 17 años');
    $sheet1->setCellValue('B12', $totales['total_13_17']);
    $sheet1->setCellValue('A13', '18 - 28 años');
    $sheet1->setCellValue('B13', $totales['total_18_28']);
    $sheet1->setCellValue('A14', '29 - 45 años');
    $sheet1->setCellValue('B14', $totales['total_29_45']);
    $sheet1->setCellValue('A15', '46 - 64 años');
    $sheet1->setCellValue('B15', $totales['total_46_64']);
    $sheet1->setCellValue('A16', 'Mayor o igual a 65 años');
    $sheet1->setCellValue('B16', $totales['total_mayor_65']);

    // ===== SECCIÓN 4: POR ORIENTACIÓN SEXUAL =====
    $sheet1->setCellValue('A18', 'POR ORIENTACIÓN SEXUAL');
    $sheet1->setCellValue('B18', 'CANTIDAD');
    $sheet1->getStyle('A18:B18')->applyFromArray($styleHeader);
    $sheet1->setCellValue('A19', 'Heterosexual');
    $sheet1->setCellValue('B19', $totales['total_heterosexual']);
    $sheet1->setCellValue('A20', 'Homosexual');
    $sheet1->setCellValue('B20', $totales['total_homosexual']);
    $sheet1->setCellValue('A21', 'Bisexual');
    $sheet1->setCellValue('B21', $totales['total_bisexual']);
    $sheet1->setCellValue('A22', 'Asexual');
    $sheet1->setCellValue('B22', $totales['total_asexual']);
    $sheet1->setCellValue('A23', 'Otro');
    $sheet1->setCellValue('B23', $totales['total_otro_orientacion']);

    // ===== SECCIÓN 5: DISCAPACIDAD =====
    $sheet1->setCellValue('A25', 'DISCAPACIDAD');
    $sheet1->setCellValue('B25', 'CANTIDAD');
    $sheet1->getStyle('A25:B25')->applyFromArray($styleHeader);
    $sheet1->setCellValue('A26', 'Con Discapacidad');
    $sheet1->setCellValue('B26', $totales['total_con_discapacidad']);
    $sheet1->setCellValue('A27', 'Visual');
    $sheet1->setCellValue('B27', $totales['total_visual']);
    $sheet1->setCellValue('A28', 'Auditiva');
    $sheet1->setCellValue('B28', $totales['total_auditiva']);
    $sheet1->setCellValue('A29', 'Física');
    $sheet1->setCellValue('B29', $totales['total_fisica']);
    $sheet1->setCellValue('A30', 'Intelectual');
    $sheet1->setCellValue('B30', $totales['total_intelectual']);
    $sheet1->setCellValue('A31', 'Psicosocial');
    $sheet1->setCellValue('B31', $totales['total_psicosocial']);
    $sheet1->setCellValue('A32', 'Múltiple');
    $sheet1->setCellValue('B32', $totales['total_multiple']);
    $sheet1->setCellValue('A33', 'Sordoceguera');
    $sheet1->setCellValue('B33', $totales['total_sordoceguera']);

    // ===== SECCIÓN 6: GRUPO ÉTNICO =====
    $sheet1->setCellValue('A35', 'GRUPO ÉTNICO');
    $sheet1->setCellValue('B35', 'CANTIDAD');
    $sheet1->getStyle('A35:B35')->applyFromArray($styleHeader);
    $sheet1->setCellValue('A36', 'Afrocolombiano');
    $sheet1->setCellValue('B36', $totales['total_afrocolombiano']);
    $sheet1->setCellValue('A37', 'Indígena');
    $sheet1->setCellValue('B37', $totales['total_indigena']);
    $sheet1->setCellValue('A38', 'Raizal');
    $sheet1->setCellValue('B38', $totales['total_raizal']);
    $sheet1->setCellValue('A39', 'Palenquero de San Basilio');
    $sheet1->setCellValue('B39', $totales['total_palenquero']);
    $sheet1->setCellValue('A40', 'Gitano (ROM)');
    $sheet1->setCellValue('B40', $totales['total_gitanorom']);
    $sheet1->setCellValue('A41', 'Mestizo');
    $sheet1->setCellValue('B41', $totales['total_mestizo']);
    $sheet1->setCellValue('A42', 'Ninguno');
    $sheet1->setCellValue('B42', $totales['total_ninguno_etnico']);

    // ===== SECCIÓN 7: CARACTERÍSTICAS ESPECIALES =====
    $sheet1->setCellValue('A44', 'CARACTERÍSTICAS ESPECIALES');
    $sheet1->setCellValue('B44', 'CANTIDAD');
    $sheet1->getStyle('A44:B44')->applyFromArray($styleHeader);
    $sheet1->setCellValue('A45', 'Víctimas');
    $sheet1->setCellValue('B45', $totales['total_victimas']);
    $sheet1->setCellValue('A46', 'Mujeres Gestantes');
    $sheet1->setCellValue('B46', $totales['total_mujeres_gestantes']);
    $sheet1->setCellValue('A47', 'Cabezas de Familia');
    $sheet1->setCellValue('B47', $totales['total_cabezas_familia']);
    $sheet1->setCellValue('A48', 'Experiencia Migratoria');
    $sheet1->setCellValue('B48', $totales['total_experiencia_migratoria']);

    // ===== SECCIÓN 8: SEGURIDAD EN SALUD =====
    $sheet1->setCellValue('A50', 'SEGURIDAD EN SALUD');
    $sheet1->setCellValue('B50', 'CANTIDAD');
    $sheet1->getStyle('A50:B50')->applyFromArray($styleHeader);
    $sheet1->setCellValue('A51', 'Régimen Contributivo');
    $sheet1->setCellValue('B51', $totales['total_contributivo']);
    $sheet1->setCellValue('A52', 'Régimen Subsidiado');
    $sheet1->setCellValue('B52', $totales['total_subsidiado']);
    $sheet1->setCellValue('A53', 'Población Vinculada');
    $sheet1->setCellValue('B53', $totales['total_vinculada']);
    $sheet1->setCellValue('A54', 'Sin Seguridad Social');
    $sheet1->setCellValue('B54', $totales['total_sin_seguridad']);

    logError("Hoja de totales completada");

    // ===============================================
    // HOJA 2: DETALLE DE INTEGRANTES
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('DETALLE INTEGRANTES');
    logError("Hoja 2 - DETALLE INTEGRANTES creada");
    
    // Consulta para detalle de integrantes
    $sql_detalle = "
    SELECT ic.*, 
           m.doc_encCampo, 
           m.nom_encCampo,
           m.num_ficha_encCampo,
           m.tipo_movimiento,
           m.fecha_movimiento,
           u.nombre AS nombre_usuario
    FROM integ_movimientos_encuesta_campo ic
    JOIN movimientos_encuesta_campo m ON ic.id_movimiento = m.id_movimiento
    LEFT JOIN usuarios u ON m.id_usu = u.id_usu
    $where_clause
    ORDER BY m.fecha_movimiento DESC, ic.fecha_registro ASC
    ";
    
    $res_detalle = mysqli_query($mysqli, $sql_detalle);
    if ($res_detalle === false) {
        logError("Error en la consulta de detalle: " . mysqli_error($mysqli));
        throw new Exception("Error en la consulta de detalle: " . mysqli_error($mysqli));
    }
    
    logError("Consulta de detalle ejecutada. Filas: " . mysqli_num_rows($res_detalle));

    // Aplicar estilos a la hoja 2
    $sheet2->getStyle('A1:R1')->applyFromArray($styleHeader);

    // Encabezados para detalle
    $sheet2->setCellValue('A1', 'ID MOVIMIENTO');
    $sheet2->setCellValue('B1', 'FECHA MOVIMIENTO');
    $sheet2->setCellValue('C1', 'TIPO MOVIMIENTO');
    $sheet2->setCellValue('D1', 'DOCUMENTO TITULAR');
    $sheet2->setCellValue('E1', 'NOMBRE TITULAR');
    $sheet2->setCellValue('F1', 'NUMERO FICHA');
    $sheet2->setCellValue('G1', 'GÉNERO');
    $sheet2->setCellValue('H1', 'RANGO EDAD');
    $sheet2->setCellValue('I1', 'ORIENTACIÓN SEXUAL');
    $sheet2->setCellValue('J1', 'DISCAPACIDAD');
    $sheet2->setCellValue('K1', 'TIPO DISCAPACIDAD');
    $sheet2->setCellValue('L1', 'GRUPO ÉTNICO');
    $sheet2->setCellValue('M1', 'VÍCTIMA');
    $sheet2->setCellValue('N1', 'MUJER GESTANTE');
    $sheet2->setCellValue('O1', 'CABEZA FAMILIA');
    $sheet2->setCellValue('P1', 'EXPERIENCIA MIGRATORIA');
    $sheet2->setCellValue('Q1', 'SEGURIDAD SALUD');
    $sheet2->setCellValue('R1', 'NIVEL EDUCATIVO');
    $sheet2->setCellValue('S1', 'OCUPACIÓN');
    $sheet2->setCellValue('T1', 'USUARIO REGISTRO');

    // Ajustar anchos
    for ($col = 'A'; $col <= 'T'; $col++) {
        $sheet2->getColumnDimension($col)->setWidth(20);
    }

    // Llenar datos
    $row = 2;
    while ($integ = mysqli_fetch_assoc($res_detalle)) {
        $sheet2->setCellValue('A' . $row, $integ['id_movimiento']);
        $sheet2->setCellValue('B' . $row, $integ['fecha_movimiento']);
        $sheet2->setCellValue('C' . $row, strtoupper($integ['tipo_movimiento']));
        $sheet2->setCellValue('D' . $row, $integ['doc_encCampo']);
        $sheet2->setCellValue('E' . $row, $integ['nom_encCampo']);
        $sheet2->setCellValue('F' . $row, $integ['num_ficha_encCampo']);
        $sheet2->setCellValue('G' . $row, $integ['gen_integCampo']);
        $sheet2->setCellValue('H' . $row, $integ['rango_integCampo']);
        $sheet2->setCellValue('I' . $row, $integ['orientacionSexual']);
        $sheet2->setCellValue('J' . $row, $integ['condicionDiscapacidad']);
        $sheet2->setCellValue('K' . $row, $integ['tipoDiscapacidad']);
        $sheet2->setCellValue('L' . $row, $integ['grupoEtnico']);
        $sheet2->setCellValue('M' . $row, $integ['victima']);
        $sheet2->setCellValue('N' . $row, $integ['mujerGestante']);
        $sheet2->setCellValue('O' . $row, $integ['cabezaFamilia']);
        $sheet2->setCellValue('P' . $row, $integ['experienciaMigratoria']);
        $sheet2->setCellValue('Q' . $row, $integ['seguridadSalud']);
        $sheet2->setCellValue('R' . $row, $integ['nivelEducativo']);
        $sheet2->setCellValue('S' . $row, $integ['condicionOcupacion']);
        $sheet2->setCellValue('T' . $row, $integ['nombre_usuario']);
        $row++;
    }

    logError("Detalle de integrantes llenado. Total filas: " . ($row - 2));

    // Generar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $filename = 'Integrantes_Movimientos_Campo_' . date('Ymd_His') . '.xlsx';
    
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
