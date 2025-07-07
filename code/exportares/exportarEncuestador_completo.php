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
    logError("Iniciando exportarEncuestador.php");
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
    logError("Parámetros recibidos - fecha_inicio: {$fecha_inicio}, fecha_fin: {$fecha_fin}");

    // ===============================================
    // HOJA 1: ENCUESTAS (datos de encventanilla)
    // ===============================================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('ENCUESTAS');
    logError("Hoja 1 - ENCUESTAS creada");

    // Condiciones WHERE para encuestas
    $condiciones = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '') {
        $id_usu = $_GET['id_usu'];
        $condiciones[] = "ev.id_usu = '$id_usu'";
    }

    $where_encuestas = '';
    if (count($condiciones) > 0) {
        $where_encuestas = 'WHERE ' . implode(' AND ', $condiciones);
    }

    // Consulta para encuestas con tipo_movimiento desde la tabla movimientos
    $sql_encuestas = "
    SELECT ev.*, b.nombre_bar AS barrio_nombre, d.nombre_departamento AS departamento_nombre, 
           c.nombre_com AS comuna_nombre, m.nombre_municipio as ciudad_nombre,
           COALESCE(mov.tipo_movimiento, ev.tram_solic_encVenta) as tipo_movimiento_final
    FROM encventanilla ev
    LEFT JOIN barrios b ON ev.id_bar = b.id_bar
    LEFT JOIN departamentos d ON ev.departamento_expedicion = d.id_departamento
    LEFT JOIN comunas c ON ev.id_com = c.id_com
    LEFT JOIN municipios m ON ev.ciudad_expedicion = m.id_municipio
    LEFT JOIN movimientos mov ON ev.doc_encVenta = mov.doc_encVenta
    $where_encuestas
    ";
    $res_encuestas = mysqli_query($mysqli, $sql_encuestas);
    if ($res_encuestas === false) {
        echo "Error en la consulta de encuestas: " . mysqli_error($mysqli);
        exit;
    }
    logError("Consulta de encuestas ejecutada correctamente");

    // Configuración de estilos
    $boldFontStyle = ['bold' => true];
    $styleHeader = [
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffd880']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    // Aplicar estilos a la hoja 1
    $sheet1->getStyle('A1:Q1')->applyFromArray($styleHeader);

    // Encabezados para ENCUESTAS
    $sheet1->setCellValue('A1', 'FECHA ENCUESTA');
    $sheet1->setCellValue('B1', 'DOCUMENTO');
    $sheet1->setCellValue('C1', 'TIPO DOCUMENTO');
    $sheet1->setCellValue('D1', 'FECHA EXPEDICION');
    $sheet1->setCellValue('E1', 'DEPARTAMENTO EXPEDICION');
    $sheet1->setCellValue('F1', 'CIUDAD EXPEDICION');
    $sheet1->setCellValue('G1', 'NOMBRE');
    $sheet1->setCellValue('H1', 'DIRECCION');
    $sheet1->setCellValue('I1', 'ZONA');
    $sheet1->setCellValue('J1', 'COMUNA');
    $sheet1->setCellValue('K1', 'BARRIO');
    $sheet1->setCellValue('L1', 'QUE OTRO BARRIO');
    $sheet1->setCellValue('M1', 'TIPO MOVIMIENTO');
    $sheet1->setCellValue('N1', 'CANTIDAD INTEGRANTES');
    $sheet1->setCellValue('O1', 'NUMERO FICHA');
    $sheet1->setCellValue('P1', 'SISBEN NOCTURNO');
    $sheet1->setCellValue('Q1', 'OBSERVACIONES');

    // Ajustar ancho de columnas para ENCUESTAS
    foreach(range('A','Q') as $col) {
        $sheet1->getColumnDimension($col)->setWidth(20);
    }
    $sheet1->getDefaultRowDimension()->setRowHeight(25);

    // Escribir datos de ENCUESTAS
    $rowIndex1 = 2;
    while ($row = mysqli_fetch_array($res_encuestas, MYSQLI_ASSOC)) {
        $sheet1->setCellValue('A' . $rowIndex1, $row['fec_reg_encVenta']);
        $sheet1->setCellValue('B' . $rowIndex1, $row['doc_encVenta']);
        $sheet1->setCellValue('C' . $rowIndex1, $row['tipo_documento']);
        $sheet1->setCellValue('D' . $rowIndex1, $row['fecha_expedicion']);
        $sheet1->setCellValue('E' . $rowIndex1, $row['departamento_nombre']);
        $sheet1->setCellValue('F' . $rowIndex1, $row['ciudad_nombre']);
        $sheet1->setCellValue('G' . $rowIndex1, $row['nom_encVenta']);
        $sheet1->setCellValue('H' . $rowIndex1, $row['dir_encVenta']);
        $sheet1->setCellValue('I' . $rowIndex1, $row['zona_encVenta']);
        $sheet1->setCellValue('J' . $rowIndex1, $row['comuna_nombre']);
        $sheet1->setCellValue('K' . $rowIndex1, $row['barrio_nombre']);
        $sheet1->setCellValue('L' . $rowIndex1, $row['otro_bar_ver_encVenta']);
        $sheet1->setCellValue('M' . $rowIndex1, $row['tipo_movimiento_final']);
        $sheet1->setCellValue('N' . $rowIndex1, $row['integra_encVenta']);
        $sheet1->setCellValue('O' . $rowIndex1, $row['num_ficha_encVenta']);
        $sheet1->setCellValue('P' . $rowIndex1, $row['sisben_nocturno']);
        $sheet1->setCellValue('Q' . $rowIndex1, $row['obs_encVenta']);
        $rowIndex1++;
    }
    logError("Datos de encuestas escritos en la hoja 1");

    // ===============================================
    // HOJA 2: INFORMACIÓN (datos de informacion)
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('INFORMACION');
    logError("Hoja 2 - INFORMACIÓN creada");

    // Condiciones WHERE para información
    $condiciones_info = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $condiciones_info[] = "i.fecha_alta_info BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '') {
        $condiciones_info[] = "i.id_usu = '$id_usu'";
    }

    $where_info = '';
    if (count($condiciones_info) > 0) {
        $where_info = 'WHERE ' . implode(' AND ', $condiciones_info);
    }

    // Consulta para información
    $sql_informacion = "
    SELECT i.*, d.nombre_departamento AS departamento_nombre, m.nombre_municipio as ciudad_nombre
    FROM informacion i
    LEFT JOIN departamentos d ON i.departamento_expedicion = d.id_departamento
    LEFT JOIN municipios m ON i.ciudad_expedicion = m.id_municipio
    $where_info
    ";
    $res_informacion = mysqli_query($mysqli, $sql_informacion);
    if ($res_informacion === false) {
        echo "Error en la consulta de información: " . mysqli_error($mysqli);
        exit;
    }    logError("Consulta de información ejecutada correctamente");    // Aplicar estilos a la hoja 2 - FORZAR HASTA COLUMNA W
    $sheet2->getStyle('A1:W1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffd880']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ]);
    logError("Estilo aplicado a la hoja 2 desde A1 hasta W1");

    // Encabezados para INFORMACIÓN (basados en los campos de addsurvey2.php)
    $sheet2->setCellValue('A1', 'FECHA REGISTRO');
    $sheet2->setCellValue('B1', 'DOCUMENTO');
    $sheet2->setCellValue('C1', 'NOMBRE');
    $sheet2->setCellValue('D1', 'GENERO');
    $sheet2->setCellValue('E1', 'TIPO DOCUMENTO');
    $sheet2->setCellValue('F1', 'DEPARTAMENTO EXPEDICION');
    $sheet2->setCellValue('G1', 'CIUDAD EXPEDICION');
    $sheet2->setCellValue('H1', 'FECHA EXPEDICION');
    $sheet2->setCellValue('I1', 'RANGO EDAD');
    $sheet2->setCellValue('J1', 'VICTIMA');
    $sheet2->setCellValue('K1', 'CONDICION DISCAPACIDAD');
    $sheet2->setCellValue('L1', 'TIPO DISCAPACIDAD');
    $sheet2->setCellValue('M1', 'MUJER GESTANTE');
    $sheet2->setCellValue('N1', 'CABEZA FAMILIA');
    $sheet2->setCellValue('O1', 'ORIENTACION SEXUAL');
    $sheet2->setCellValue('P1', 'EXPERIENCIA MIGRATORIA');
    $sheet2->setCellValue('Q1', 'GRUPO ETNICO');
    $sheet2->setCellValue('R1', 'SEGURIDAD SALUD');
    $sheet2->setCellValue('S1', 'NIVEL EDUCATIVO');
    $sheet2->setCellValue('T1', 'CONDICION OCUPACION');
    $sheet2->setCellValue('U1', 'TIPO SOLICITUD');
    $sheet2->setCellValue('V1', 'OBSERVACION');
    $sheet2->setCellValue('W1', 'INFO ADICIONAL');

    // Ajustar ancho de columnas para INFORMACIÓN
    foreach(range('A','W') as $col) {
        $sheet2->getColumnDimension($col)->setWidth(20);
    }
    $sheet2->getDefaultRowDimension()->setRowHeight(25);

    // Escribir datos de INFORMACIÓN
    $rowIndex2 = 2;
    while ($row = mysqli_fetch_array($res_informacion, MYSQLI_ASSOC)) {
        $sheet2->setCellValue('A' . $rowIndex2, $row['fecha_reg_info']);
        $sheet2->setCellValue('B' . $rowIndex2, $row['doc_info']);
        $sheet2->setCellValue('C' . $rowIndex2, $row['nom_info']);
        $sheet2->setCellValue('D' . $rowIndex2, $row['gen_integVenta']);
        $sheet2->setCellValue('E' . $rowIndex2, $row['tipo_documento']);
        $sheet2->setCellValue('F' . $rowIndex2, $row['departamento_nombre']);
        $sheet2->setCellValue('G' . $rowIndex2, $row['ciudad_nombre']);
        $sheet2->setCellValue('H' . $rowIndex2, $row['fecha_expedicion']);
        $sheet2->setCellValue('I' . $rowIndex2, $row['rango_integVenta']);
        $sheet2->setCellValue('J' . $rowIndex2, $row['victima']);
        $sheet2->setCellValue('K' . $rowIndex2, $row['condicionDiscapacidad']);
        $sheet2->setCellValue('L' . $rowIndex2, $row['tipoDiscapacidad']);
        $sheet2->setCellValue('M' . $rowIndex2, $row['mujerGestante']);
        $sheet2->setCellValue('N' . $rowIndex2, $row['cabezaFamilia']);
        $sheet2->setCellValue('O' . $rowIndex2, $row['orientacionSexual']);
        $sheet2->setCellValue('P' . $rowIndex2, $row['experienciaMigratoria']);
        $sheet2->setCellValue('Q' . $rowIndex2, $row['grupoEtnico']);
        $sheet2->setCellValue('R' . $rowIndex2, $row['seguridadSalud']);
        $sheet2->setCellValue('S' . $rowIndex2, $row['nivelEducativo']);
        $sheet2->setCellValue('T' . $rowIndex2, $row['condicionOcupacion']);
        $sheet2->setCellValue('U' . $rowIndex2, $row['tipo_solic_encInfo']);
        $sheet2->setCellValue('V' . $rowIndex2, $row['observacion']);
        $sheet2->setCellValue('W' . $rowIndex2, $row['info_adicional']);
        $rowIndex2++;
    }
    logError("Datos de información escritos en la hoja 2");

    // Nombre del archivo
    $nombreArchivo = 'Encuestas_e_Informacion_' . $fecha_inicio . '_' . $fecha_fin . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    logError("Archivo Excel generado: {$nombreArchivo}");

    // Headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
    header('Cache-Control: max-age=0');

    // Enviar archivo
    $writer->save('php://output');
    logError("Archivo Excel enviado para descarga");
    
} catch (Exception $e) {
    logError("Excepción capturada: " . $e->getMessage());
    echo "Ocurrió un error al generar el archivo. Por favor, inténtelo de nuevo más tarde.";
    exit;
}
?>
