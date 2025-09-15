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
    logError("Iniciando exportarIntegrantesCampo.php");
    
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
    $id_usu_param = isset($_GET['id_usu']) ? $_GET['id_usu'] : '';
    logError("Parámetros recibidos - fecha_inicio: {$fecha_inicio}, fecha_fin: {$fecha_fin}, id_usu: {$id_usu_param}");

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
        $condiciones[] = "ec.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
        logError("Filtro de fechas aplicado: $fecha_inicio_completa a $fecha_fin_completa");
    }
    
    // Filtro de usuario
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
        $id_usu = $_GET['id_usu'];
        $condiciones[] = "ec.id_usu = '$id_usu'";
        logError("Filtrando por usuario específico: {$id_usu}");
    }

    $where_clause = '';
    if (count($condiciones) > 0) {
        $where_clause = 'WHERE ' . implode(' AND ', $condiciones);
    }
    
    // Consulta para totales de integrantes
    $sql_integrantes = "
    SELECT COUNT(*) AS total_integrantes,
    COUNT(CASE WHEN ic.gen_integVenta = 'M' THEN 1 END) AS total_masculino,
    COUNT(CASE WHEN ic.gen_integVenta = 'F' THEN 1 END) AS total_femenino,
    COUNT(CASE WHEN ic.gen_integVenta = 'O' THEN 1 END) AS total_otro_genero,
    COUNT(CASE WHEN ic.rango_integVenta = '0 - 6' OR ic.rango_integVenta = '0 - 5' THEN 1 END) AS total_0_5,
    COUNT(CASE WHEN ic.rango_integVenta = '6 - 12' THEN 1 END) AS total_6_12,
    COUNT(CASE WHEN ic.rango_integVenta = '13 - 17' THEN 1 END) AS total_13_17,
    COUNT(CASE WHEN ic.rango_integVenta = '18 - 28' THEN 1 END) AS total_18_28,
    COUNT(CASE WHEN ic.rango_integVenta = '29 - 45' THEN 1 END) AS total_29_45,
    COUNT(CASE WHEN ic.rango_integVenta = '46 - 64' THEN 1 END) AS total_46_64,
    COUNT(CASE WHEN ic.rango_integVenta = 'Mayor o igual a 65' THEN 1 END) AS total_mayor_65,
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
    FROM integcampo ic
    JOIN encuestacampo ec ON ic.id_encuesta = ec.id_encCampo
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
    $sheet1->getStyle('A19:B19')->applyFromArray($styleHeader);
    $sheet1->getStyle('A28:B28')->applyFromArray($styleHeader);
    $sheet1->getStyle('A35:B35')->applyFromArray($styleHeader);

    // Ajustar el ancho de las columnas
    $sheet1->getColumnDimension('A')->setWidth(40);
    $sheet1->getColumnDimension('B')->setWidth(15);

    // ===== SECCIÓN 1: TOTALES GENERALES =====
    $sheet1->setCellValue('A1', 'TOTALES GENERALES');
    $sheet1->setCellValue('B1', 'CANTIDAD');
    
    $sheet1->setCellValue('A2', 'Total Integrantes');
    $sheet1->setCellValue('B2', $totales['total_integrantes']);
    $sheet1->setCellValue('A3', 'Total Masculino');
    $sheet1->setCellValue('B3', $totales['total_masculino']);
    
    // ===== SECCIÓN 2: GÉNERO =====
    $sheet1->setCellValue('A4', 'POR GÉNERO');
    $sheet1->setCellValue('B4', 'CANTIDAD');
    
    $sheet1->setCellValue('A5', 'Masculino');
    $sheet1->setCellValue('B5', $totales['total_masculino']);
    $sheet1->setCellValue('A6', 'Femenino');
    $sheet1->setCellValue('B6', $totales['total_femenino']);
    $sheet1->setCellValue('A7', 'Otro');
    $sheet1->setCellValue('B7', $totales['total_otro_genero']);
    
    // ===== SECCIÓN 3: RANGOS DE EDAD =====
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
    
    // ===== SECCIÓN 4: ORIENTACIÓN SEXUAL =====
    $sheet1->setCellValue('A14', 'POR ORIENTACIÓN SEXUAL');
    $sheet1->setCellValue('B14', 'CANTIDAD');
    
    $sheet1->setCellValue('A15', 'Heterosexual');
    $sheet1->setCellValue('B15', $totales['total_heterosexual']);
    $sheet1->setCellValue('A16', 'Homosexual');
    $sheet1->setCellValue('B16', $totales['total_homosexual']);
    $sheet1->setCellValue('A17', 'Bisexual');
    $sheet1->setCellValue('B17', $totales['total_bisexual']);
    $sheet1->setCellValue('A18', 'Asexual');
    $sheet1->setCellValue('B18', $totales['total_asexual']);
    
    // ===== SECCIÓN 5: DISCAPACIDAD =====
    $sheet1->setCellValue('A19', 'CONDICIÓN DE DISCAPACIDAD');
    $sheet1->setCellValue('B19', 'CANTIDAD');
    
    $sheet1->setCellValue('A20', 'Con Discapacidad');
    $sheet1->setCellValue('B20', $totales['total_con_discapacidad']);
    $sheet1->setCellValue('A21', 'Visual');
    $sheet1->setCellValue('B21', $totales['total_visual']);
    $sheet1->setCellValue('A22', 'Auditiva');
    $sheet1->setCellValue('B22', $totales['total_auditiva']);
    $sheet1->setCellValue('A23', 'Física');
    $sheet1->setCellValue('B23', $totales['total_fisica']);
    $sheet1->setCellValue('A24', 'Intelectual');
    $sheet1->setCellValue('B24', $totales['total_intelectual']);
    $sheet1->setCellValue('A25', 'Psicosocial');
    $sheet1->setCellValue('B25', $totales['total_psicosocial']);
    $sheet1->setCellValue('A26', 'Múltiple');
    $sheet1->setCellValue('B26', $totales['total_multiple']);
    $sheet1->setCellValue('A27', 'Sordoceguera');
    $sheet1->setCellValue('B27', $totales['total_sordoceguera']);
    
    // ===== SECCIÓN 6: GRUPO ÉTNICO =====
    $sheet1->setCellValue('A28', 'POR GRUPO ÉTNICO');
    $sheet1->setCellValue('B28', 'CANTIDAD');
    
    $sheet1->setCellValue('A29', 'Afrocolombiano');
    $sheet1->setCellValue('B29', $totales['total_afrocolombiano']);
    $sheet1->setCellValue('A30', 'Indígena');
    $sheet1->setCellValue('B30', $totales['total_indigena']);
    $sheet1->setCellValue('A31', 'Raizal');
    $sheet1->setCellValue('B31', $totales['total_raizal']);
    $sheet1->setCellValue('A32', 'Palenquero');
    $sheet1->setCellValue('B32', $totales['total_palenquero']);
    $sheet1->setCellValue('A33', 'Gitano (ROM)');
    $sheet1->setCellValue('B33', $totales['total_gitanorom']);
    $sheet1->setCellValue('A34', 'Mestizo');
    $sheet1->setCellValue('B34', $totales['total_mestizo']);
    
    // ===== SECCIÓN 7: CARACTERÍSTICAS ESPECIALES =====
    $sheet1->setCellValue('A35', 'CARACTERÍSTICAS ESPECIALES');
    $sheet1->setCellValue('B35', 'CANTIDAD');
    
    $sheet1->setCellValue('A36', 'Víctimas');
    $sheet1->setCellValue('B36', $totales['total_victimas']);
    $sheet1->setCellValue('A37', 'Mujeres Gestantes');
    $sheet1->setCellValue('B37', $totales['total_mujeres_gestantes']);
    $sheet1->setCellValue('A38', 'Cabezas de Familia');
    $sheet1->setCellValue('B38', $totales['total_cabezas_familia']);
    $sheet1->setCellValue('A39', 'Con Experiencia Migratoria');
    $sheet1->setCellValue('B39', $totales['total_experiencia_migratoria']);

    // ===============================================
    // HOJA 2: DETALLE POR BARRIO
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('DETALLE POR BARRIO');
    logError("Hoja 2 - DETALLE POR BARRIO creada");
    
    // Consulta de integrantes por barrio
    $sql_por_barrio = "
    SELECT 
        b.nombre_bar,
        c.nombre_com,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND (ic.rango_integVenta = '0 - 6' OR ic.rango_integVenta = '0 - 5') THEN 1 END) AS masculino_0_5,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND (ic.rango_integVenta = '0 - 6' OR ic.rango_integVenta = '0 - 5') THEN 1 END) AS femenino_0_5,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND ic.rango_integVenta = '6 - 12' THEN 1 END) AS masculino_6_12,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND ic.rango_integVenta = '6 - 12' THEN 1 END) AS femenino_6_12,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND ic.rango_integVenta = '13 - 17' THEN 1 END) AS masculino_13_17,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND ic.rango_integVenta = '13 - 17' THEN 1 END) AS femenino_13_17,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND ic.rango_integVenta = '18 - 28' THEN 1 END) AS masculino_18_28,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND ic.rango_integVenta = '18 - 28' THEN 1 END) AS femenino_18_28,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND ic.rango_integVenta = '29 - 45' THEN 1 END) AS masculino_29_45,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND ic.rango_integVenta = '29 - 45' THEN 1 END) AS femenino_29_45,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND ic.rango_integVenta = '46 - 64' THEN 1 END) AS masculino_46_64,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND ic.rango_integVenta = '46 - 64' THEN 1 END) AS femenino_46_64,
        COUNT(CASE WHEN ic.gen_integVenta = 'M' AND ic.rango_integVenta = 'Mayor o igual a 65' THEN 1 END) AS masculino_mayor_65,
        COUNT(CASE WHEN ic.gen_integVenta = 'F' AND ic.rango_integVenta = 'Mayor o igual a 65' THEN 1 END) AS femenino_mayor_65,
        COUNT(*) AS total_por_barrio
    FROM integcampo ic
    JOIN encuestacampo ec ON ic.id_encuesta = ec.id_encCampo
    LEFT JOIN barrios b ON ec.id_bar = b.id_bar
    LEFT JOIN comunas c ON ec.id_com = c.id_com
    $where_clause
    GROUP BY b.nombre_bar, ec.id_bar, c.nombre_com
    ORDER BY total_por_barrio DESC
    ";
    
    $res_barrio = mysqli_query($mysqli, $sql_por_barrio);
    if ($res_barrio === false) {
        logError("Error en la consulta por barrio: " . mysqli_error($mysqli));
        throw new Exception("Error en la consulta por barrio: " . mysqli_error($mysqli));
    }
    
    // Configurar encabezados para la hoja de detalle por barrio
    $sheet2->getStyle('A1:Q1')->applyFromArray($styleHeader);
    
    // Ajustar el ancho de las columnas
    $sheet2->getColumnDimension('A')->setWidth(25); // Barrio
    $sheet2->getColumnDimension('B')->setWidth(20); // Comuna
    for ($col = 'C'; $col <= 'Q'; $col++) {
        $sheet2->getColumnDimension($col)->setWidth(12);
    }
    
    // Encabezados
    $sheet2->setCellValue('A1', 'BARRIO');
    $sheet2->setCellValue('B1', 'COMUNA');
    $sheet2->setCellValue('C1', 'M 0-5');
    $sheet2->setCellValue('D1', 'F 0-5');
    $sheet2->setCellValue('E1', 'M 6-12');
    $sheet2->setCellValue('F1', 'F 6-12');
    $sheet2->setCellValue('G1', 'M 13-17');
    $sheet2->setCellValue('H1', 'F 13-17');
    $sheet2->setCellValue('I1', 'M 18-28');
    $sheet2->setCellValue('J1', 'F 18-28');
    $sheet2->setCellValue('K1', 'M 29-45');
    $sheet2->setCellValue('L1', 'F 29-45');
    $sheet2->setCellValue('M1', 'M 46-64');
    $sheet2->setCellValue('N1', 'F 46-64');
    $sheet2->setCellValue('O1', 'M +65');
    $sheet2->setCellValue('P1', 'F +65');
    $sheet2->setCellValue('Q1', 'TOTAL');
    
    // Llenar datos por barrio
    $rowIndex2 = 2;
    while ($row_barrio = mysqli_fetch_assoc($res_barrio)) {
        $sheet2->setCellValue('A' . $rowIndex2, $row_barrio['nombre_bar'] ?: 'Sin Barrio');
        $sheet2->setCellValue('B' . $rowIndex2, $row_barrio['nombre_com'] ?: 'Sin Comuna');
        $sheet2->setCellValue('C' . $rowIndex2, $row_barrio['masculino_0_5']);
        $sheet2->setCellValue('D' . $rowIndex2, $row_barrio['femenino_0_5']);
        $sheet2->setCellValue('E' . $rowIndex2, $row_barrio['masculino_6_12']);
        $sheet2->setCellValue('F' . $rowIndex2, $row_barrio['femenino_6_12']);
        $sheet2->setCellValue('G' . $rowIndex2, $row_barrio['masculino_13_17']);
        $sheet2->setCellValue('H' . $rowIndex2, $row_barrio['femenino_13_17']);
        $sheet2->setCellValue('I' . $rowIndex2, $row_barrio['masculino_18_28']);
        $sheet2->setCellValue('J' . $rowIndex2, $row_barrio['femenino_18_28']);
        $sheet2->setCellValue('K' . $rowIndex2, $row_barrio['masculino_29_45']);
        $sheet2->setCellValue('L' . $rowIndex2, $row_barrio['femenino_29_45']);
        $sheet2->setCellValue('M' . $rowIndex2, $row_barrio['masculino_46_64']);
        $sheet2->setCellValue('N' . $rowIndex2, $row_barrio['femenino_46_64']);
        $sheet2->setCellValue('O' . $rowIndex2, $row_barrio['masculino_mayor_65']);
        $sheet2->setCellValue('P' . $rowIndex2, $row_barrio['femenino_mayor_65']);
        $sheet2->setCellValue('Q' . $rowIndex2, $row_barrio['total_por_barrio']);
        $rowIndex2++;
    }

    // ===============================================
    // HOJA 3: LISTADO COMPLETO DE INTEGRANTES
    // ===============================================
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('LISTADO INTEGRANTES');
    logError("Hoja 3 - LISTADO INTEGRANTES creada");
    
    // Consulta para listado completo de integrantes
    $sql_listado = "
    SELECT 
        ec.doc_encVenta,
        ec.nom_encVenta,
        ec.fecha_alta_encVenta,
        b.nombre_bar,
        c.nombre_com,
        ic.gen_integVenta,
        ic.rango_integVenta,
        ic.orientacionSexual,
        ic.condicionDiscapacidad,
        ic.tipoDiscapacidad,
        ic.grupoEtnico,
        ic.victima,
        ic.mujerGestante,
        ic.cabezaFamilia,
        ic.experienciaMigratoria,
        ic.seguridadSalud,
        ic.nivelEducativo,
        ic.condicionOcupacion,
        u.nombre AS nombre_usuario
    FROM integcampo ic
    JOIN encuestacampo ec ON ic.id_encuesta = ec.id_encCampo
    LEFT JOIN barrios b ON ec.id_bar = b.id_bar
    LEFT JOIN comunas c ON ec.id_com = c.id_com
    LEFT JOIN usuarios u ON ec.id_usu = u.id_usu
    $where_clause
    ORDER BY ec.fecha_alta_encVenta DESC, ec.doc_encVenta
    ";
    
    $res_listado = mysqli_query($mysqli, $sql_listado);
    if ($res_listado === false) {
        logError("Error en la consulta de listado: " . mysqli_error($mysqli));
        throw new Exception("Error en la consulta de listado: " . mysqli_error($mysqli));
    }
    
    // Configurar encabezados para la hoja de listado
    $sheet3->getStyle('A1:S1')->applyFromArray($styleHeader);
    
    // Ajustar el ancho de las columnas
    $sheet3->getColumnDimension('A')->setWidth(15); // Documento
    $sheet3->getColumnDimension('B')->setWidth(30); // Nombre
    $sheet3->getColumnDimension('C')->setWidth(15); // Fecha
    $sheet3->getColumnDimension('D')->setWidth(25); // Barrio
    $sheet3->getColumnDimension('E')->setWidth(20); // Comuna
    for ($col = 'F'; $col <= 'S'; $col++) {
        $sheet3->getColumnDimension($col)->setWidth(15);
    }
    
    // Encabezados
    $sheet3->setCellValue('A1', 'DOCUMENTO');
    $sheet3->setCellValue('B1', 'NOMBRE TITULAR');
    $sheet3->setCellValue('C1', 'FECHA ENCUESTA');
    $sheet3->setCellValue('D1', 'BARRIO');
    $sheet3->setCellValue('E1', 'COMUNA');
    $sheet3->setCellValue('F1', 'GÉNERO');
    $sheet3->setCellValue('G1', 'RANGO EDAD');
    $sheet3->setCellValue('H1', 'ORIENTACIÓN SEXUAL');
    $sheet3->setCellValue('I1', 'CON DISCAPACIDAD');
    $sheet3->setCellValue('J1', 'TIPO DISCAPACIDAD');
    $sheet3->setCellValue('K1', 'GRUPO ÉTNICO');
    $sheet3->setCellValue('L1', 'VÍCTIMA');
    $sheet3->setCellValue('M1', 'MUJER GESTANTE');
    $sheet3->setCellValue('N1', 'CABEZA FAMILIA');
    $sheet3->setCellValue('O1', 'EXP. MIGRATORIA');
    $sheet3->setCellValue('P1', 'SEGURIDAD SALUD');
    $sheet3->setCellValue('Q1', 'NIVEL EDUCATIVO');
    $sheet3->setCellValue('R1', 'CONDICIÓN OCUPACIÓN');
    $sheet3->setCellValue('S1', 'ENCUESTADOR');
    
    // Llenar datos del listado
    $rowIndex3 = 2;
    while ($row_listado = mysqli_fetch_assoc($res_listado)) {
        $sheet3->setCellValue('A' . $rowIndex3, $row_listado['doc_encVenta']);
        $sheet3->setCellValue('B' . $rowIndex3, $row_listado['nom_encVenta']);
        $sheet3->setCellValue('C' . $rowIndex3, date('d/m/Y', strtotime($row_listado['fecha_alta_encVenta'])));
        $sheet3->setCellValue('D' . $rowIndex3, $row_listado['nombre_bar'] ?: 'Sin Barrio');
        $sheet3->setCellValue('E' . $rowIndex3, $row_listado['nombre_com'] ?: 'Sin Comuna');
        $sheet3->setCellValue('F' . $rowIndex3, $row_listado['gen_integVenta']);
        $sheet3->setCellValue('G' . $rowIndex3, $row_listado['rango_integVenta']);
        $sheet3->setCellValue('H' . $rowIndex3, $row_listado['orientacionSexual']);
        $sheet3->setCellValue('I' . $rowIndex3, $row_listado['condicionDiscapacidad']);
        $sheet3->setCellValue('J' . $rowIndex3, $row_listado['tipoDiscapacidad']);
        $sheet3->setCellValue('K' . $rowIndex3, $row_listado['grupoEtnico']);
        $sheet3->setCellValue('L' . $rowIndex3, $row_listado['victima']);
        $sheet3->setCellValue('M' . $rowIndex3, $row_listado['mujerGestante']);
        $sheet3->setCellValue('N' . $rowIndex3, $row_listado['cabezaFamilia']);
        $sheet3->setCellValue('O' . $rowIndex3, $row_listado['experienciaMigratoria']);
        $sheet3->setCellValue('P' . $rowIndex3, $row_listado['seguridadSalud']);
        $sheet3->setCellValue('Q' . $rowIndex3, $row_listado['nivelEducativo']);
        $sheet3->setCellValue('R' . $rowIndex3, $row_listado['condicionOcupacion']);
        $sheet3->setCellValue('S' . $rowIndex3, $row_listado['nombre_usuario']);
        $rowIndex3++;
    }

    // Configurar la primera hoja como activa
    $spreadsheet->setActiveSheetIndex(0);
    
    // Configurar cabeceras para descarga
    $filename = 'Integrantes_Campo_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Crear el escritor y generar el archivo
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
    logError("Exportación completada exitosamente");

} catch (Exception $e) {
    logError("Error en exportación: " . $e->getMessage());
    echo "Error en la exportación: " . $e->getMessage();
}

mysqli_close($mysqli);
?>