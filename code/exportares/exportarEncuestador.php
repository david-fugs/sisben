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
    $sheet = $spreadsheet->getActiveSheet();
    logError("Spreadsheet creado");

    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
    
    logError("Parámetros recibidos - fecha_inicio: {$fecha_inicio}, fecha_fin: {$fecha_fin}");

    $condiciones = [];

    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '') {
        $id_usu = $_GET['id_usu'];
        $condiciones[] = "ev.id_usu = '$id_usu'";
    }

    $where = '';
    if (count($condiciones) > 0) {
        $where = 'WHERE ' . implode(' AND ', $condiciones);
    }

    // Consulta 1: Totales generales
    $sql_totales = "
    SELECT ev.*, b.nombre_bar AS barrio_nombre, d.nombre_departamento AS departamento_nombre, c.nombre_com AS comuna_nombre, m.nombre_municipio as ciudad_nombre
    FROM encventanilla ev
    LEFT JOIN barrios b ON ev.id_bar = b.id_bar
    LEFT JOIN departamentos d ON ev.departamento_expedicion = d.id_departamento
    LEFT JOIN comunas c ON ev.id_com = c.id_com
    LEFT JOIN municipios m ON ev.ciudad_expedicion = m.id_municipio

    $where
    ";
    $res = mysqli_query($mysqli, $sql_totales);
    // Verificar si la consulta se ejecutó correctamente
    if ($res === false) {
        // Mostrar un mensaje de error si la consulta falla
        echo "Error en la consulta: " . mysqli_error($mysqli);
        exit;
    }
    logError("Consulta ejecutada correctamente");

    // Aplicar color de fondo a las celdas A1 a 
    $sheet->getStyle('A1:Q1')->applyFromArray([
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => 'ffd880', // Cambia 'CCE5FF' al color deseado en formato RGB
            ],
        ],
    ]);
    logError("Estilo de color de fondo aplicado a las celdas A1:Q1");

    // Aplicar formato en negrita a las celdas con títulos
    $boldFontStyle = [
        'bold' => true,
    ];
    $sheet->getStyle('A2:Q2')->applyFromArray(['font' => $boldFontStyle]);
    logError("Estilo de negrita aplicado a las celdas A2:Q2");

    // Establecer estilos para los encabezados
    $styleHeader = [
        'font' => [
            'bold' => true,
            'size' => 20,
            'color' => ['rgb' => '333333'], // Color de texto (negro)
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'F2F2F2'], // Color de fondo (gris claro)
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];

    // Aplicar el estilo a las celdas de encabezado
    $sheet->getStyle('A1:Q1')->applyFromArray(['font' => $styleHeader, 'fill' => $styleHeader, 'alignment' => $styleHeader]);
    logError("Estilo de encabezado aplicado a las celdas A1:Q1");

    // // Definir los encabezados de columna

    $sheet->setCellValue('A1', 'FECHA ENCUESTA');
    $sheet->setCellValue('B1', 'DOCUMENTO');
    $sheet->setCellValue('C1', 'TIPO DOCUMENTO');
    $sheet->setCellValue('D1', 'FECHA EXPEDICION');
    $sheet->setCellValue('E1', 'DEPARTAMENTO EXPEDICION');
    $sheet->setCellValue('F1', 'CIUDAD EXPEDICION');
    $sheet->setCellValue('G1', 'NOMBRE');
    $sheet->setCellValue('H1', 'DIRECCION');
    $sheet->setCellValue('I1', 'ZONA');
    $sheet->setCellValue('J1', 'COMUNA');
    $sheet->setCellValue('K1', 'BARRIO');
    $sheet->setCellValue('L1', 'QUE OTRO BARRIO');
    $sheet->setCellValue('M1', 'TRAMITE SOLICITADO');
    $sheet->setCellValue('N1', 'CANTIDAD INTEGRANTES');
    $sheet->setCellValue('O1', 'NUMERO FICHA');
    $sheet->setCellValue('P1', 'SISBEN NOCTURNO');
    $sheet->setCellValue('Q1', 'OBSERVACIONES');
    logError("Encabezados de columna definidos");

    // Ajustar el ancho de las columna

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(25);
    $sheet->getColumnDimension('H')->setWidth(25);
    $sheet->getColumnDimension('I')->setWidth(25);
    $sheet->getColumnDimension('J')->setWidth(20);
    $sheet->getColumnDimension('K')->setWidth(20);
    $sheet->getColumnDimension('L')->setWidth(25);
    $sheet->getColumnDimension('M')->setWidth(25);
    $sheet->getColumnDimension('N')->setWidth(25);
    $sheet->getColumnDimension('O')->setWidth(25);
    $sheet->getColumnDimension('P')->setWidth(25);
    $sheet->getColumnDimension('Q')->setWidth(25);
    logError("Ancho de columnas ajustado");

    $sheet->getDefaultRowDimension()->setRowHeight(25);
    $nombreEst = '';
    $rowIndex = 2;
    while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
        $sheet->setCellValue('A' . $rowIndex, $row['fec_reg_encVenta']);
        $sheet->setCellValue('B' . $rowIndex, $row['doc_encVenta']);
        $sheet->setCellValue('C' . $rowIndex, $row['tipo_documento']);
        $sheet->setCellValue('D' . $rowIndex, $row['fecha_expedicion']);
        $sheet->setCellValue('E' . $rowIndex, $row['departamento_nombre']);
        $sheet->setCellValue('F' . $rowIndex, $row['ciudad_nombre']);
        $sheet->setCellValue('G' . $rowIndex, $row['nom_encVenta']);
        $sheet->setCellValue('H' . $rowIndex, $row['dir_encVenta']);
        $sheet->setCellValue('I' . $rowIndex, $row['zona_encVenta']);
        $sheet->setCellValue('J' . $rowIndex, $row['comuna_nombre']);
        $sheet->setCellValue('K' . $rowIndex, $row['barrio_nombre']);
        $sheet->setCellValue('L' . $rowIndex, $row['otro_bar_ver_encVenta']);
        $sheet->setCellValue('M' . $rowIndex, $row['tram_solic_encVenta']);
        $sheet->setCellValue('N' . $rowIndex, $row['integra_encVenta']);
        $sheet->setCellValue('O' . $rowIndex, $row['num_ficha_encVenta']);
        $sheet->setCellValue('P' . $rowIndex, $row['sisben_nocturno']);
        $sheet->setCellValue('Q' . $rowIndex, $row['obs_encVenta']);
         $sheet->getStyle('A' . $rowIndex . ':R' . $rowIndex . '')->applyFromArray(['font' => $boldFontStyle]);
        $rowIndex++;
    }
    logError("Datos de la consulta escritos en el archivo Excel");

    // // Nombre del archivo con la fecha actual
    $nombreArchivo = 'Encuestas_' . $fecha_inicio . '_' . $fecha_fin . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    logError("Archivo Excel generado: {$nombreArchivo}");

    //Set the headers for file download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
    header('Cache-Control: max-age=0');

    // Output the generated Excel file to the browser
    $writer->save('php://output');
    logError("Archivo Excel enviado para descarga");
} catch (Exception $e) {
    logError("Excepción capturada: " . $e->getMessage());
    echo "Ocurrió un error al generar el archivo. Por favor, inténtelo de nuevo más tarde.";
    exit;
}
