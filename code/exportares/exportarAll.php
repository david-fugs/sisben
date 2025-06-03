<?php

if (isset($_GET['id_usu']) && $_GET['id_usu'] != '') {
    $id = $_GET['id_usu'];
    $fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
    $fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
    header("Location: exportarEncuestador.php?id_usu=$id&fecha_inicio=$fechaInicio&fecha_fin=$fechaFin");
    exit;
}


require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

session_start();
include("../../conexion.php");
date_default_timezone_set("America/Bogota");
$mysqli->set_charset('utf8');

// Crear una nueva instancia de Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

$condiciones = [];

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$where = '';
if (count($condiciones) > 0) {
    $where = 'WHERE ' . implode(' AND ', $condiciones);
}

// Consulta 1: Totales generales
$sql_totales = "
SELECT
    COUNT(*) AS total_registros,
    COUNT(CASE WHEN ev.tram_solic_encVenta = 'CAMBIO DIRECCION' THEN 1 END) AS cambio_direccion,
    COUNT(CASE WHEN ev.tram_solic_encVenta = 'ENCUESTA NUEVA' THEN 1 END) AS encuesta_nueva,
    COUNT(CASE WHEN ev.tram_solic_encVenta = 'DESCENTRALIZADO' THEN 1 END) AS descentralizado,
    COUNT(CASE WHEN ev.tram_solic_encVenta = 'ENCUESTA NUEVA POR VERIFICACION' THEN 1 END) AS encuesta_verificacion,
    COUNT(CASE WHEN ev.tram_solic_encVenta = 'FAVORES' THEN 1 END) AS favores,
    COUNT(CASE WHEN ev.tram_solic_encVenta = 'INCONFORMIDAD' THEN 1 END) AS inconformidad,
    COUNT(CASE WHEN ev.sisben_nocturno = 'SI' THEN 1 END) AS sisben_nocturno_si,
    COUNT(CASE WHEN ev.sisben_nocturno = 'NO' THEN 1 END) AS sisben_nocturno_no,
    COUNT(CASE WHEN ev.zona_encVenta = 'URBANA' THEN 1 END) AS zona_urbana,
    COUNT(CASE WHEN ev.zona_encVenta = 'RURAL' THEN 1 END) AS zona_rural,
    SUM(ev.integra_encVenta) AS total_integrantes
FROM encventanilla ev
$where
";
$res = mysqli_query($mysqli, $sql_totales);
// Verificar si la consulta se ejecutó correctamente
if ($res === false) {
    // Mostrar un mensaje de error si la consulta falla
    echo "Error en la consulta: " . mysqli_error($mysqli);
    exit;
}
// Consulta 2: Conteo por nombre de barrio
$sql_por_barrio = "
SELECT b.nombre_bar, COUNT(*) AS total_por_barrio
FROM encventanilla ev
LEFT JOIN barrios b ON ev.id_bar = b.id_bar
$where
GROUP BY b.nombre_bar
ORDER BY total_por_barrio DESC
";
// Ejecutar la consulta
$res_barrio = mysqli_query($mysqli, $sql_por_barrio);
// Verificar si la consulta se ejecutó correctamente
if ($res_barrio === false) {
    // Mostrar un mensaje de error si la consulta falla
    echo "Error en la consulta: " . mysqli_error($mysqli);
    exit;
}

$sql_integrantes = "
SELECT COUNT(*) AS total_integrantes ,
COUNT(CASE WHEN gen_integVenta = 'M' THEN 1 END) AS total_masculino,
COUNT(CASE WHEN gen_integVenta = 'F' THEN 1 END) AS total_femenino,
COUNT(CASE WHEN rango_integVenta = '1' THEN 1 END) AS total_0_6,
COUNT(CASE WHEN rango_integVenta = '2' THEN 1 END) AS total_7_12,
COUNT(CASE WHEN rango_integVenta = '3' THEN 1 END) AS total_13_17,
COUNT(CASE WHEN rango_integVenta = '4' THEN 1 END) AS total_18_24,
COUNT(CASE WHEN rango_integVenta = '5' THEN 1 END) AS total_25_45,
COUNT(CASE WHEN rango_integVenta = '6' THEN 1 END) AS total_46_64,
COUNT(CASE WHEN rango_integVenta = '7' THEN 1 END) AS total_mayor_65,
COUNT(CASE WHEN orientacionSexual = 'Heterosexual' THEN 1 END) AS total_heterosexual,
COUNT(CASE WHEN orientacionSexual = 'Homosexual' THEN 1 END) AS total_homosexual,
COUNT(CASE WHEN orientacionSexual = 'Bisexual' THEN 1 END) AS total_bisexual,
COUNT(CASE WHEN orientacionSexual = 'Asexual' THEN 1 END) AS total_asexual,
COUNT(CASE WHEN orientacionSexual = 'Otro' THEN 1 END) AS total_otro_orientacion,
COUNT(CASE WHEN condicionDiscapacidad = 'Si' THEN 1 END) AS total_condicion_discapacidad,
COUNT(CASE WHEN tipoDiscapacidad = 'Visual' THEN 1 END) AS total_visual,
COUNT(CASE WHEN tipoDiscapacidad = 'Auditiva' THEN 1 END) AS total_auditiva,
COUNT(CASE WHEN tipoDiscapacidad = 'Física' OR tipoDiscapacidad = 'FÃ­sica' THEN 1 END) AS total_fisica,
COUNT(CASE WHEN tipoDiscapacidad = 'Intelectual' THEN 1 END) AS total_intelectual,
COUNT(CASE WHEN tipoDiscapacidad = 'Psicosocial' THEN 1 END) AS total_psicosocial,
COUNT(CASE WHEN tipoDiscapacidad = 'Múltiple' THEN 1 END) AS total_multiple,
COUNT(CASE WHEN tipoDiscapacidad = 'Sordoceguera' THEN 1 END) AS total_no_aplica,
COUNT(CASE WHEN grupoEtnico = 'Negro(a) / Mulato(a) / Afrocolombiano(a)' THEN 1 END) AS total_afrocolombiano,
COUNT(CASE WHEN grupoEtnico = 'Indigena' THEN 1 END) AS total_indigena,
COUNT(CASE WHEN grupoEtnico = 'Raizal' THEN 1 END) AS total_raizal,
COUNT(CASE WHEN grupoEtnico = 'Palanquero de San Basilio' THEN 1 END) AS total_palanquero,
COUNT(CASE WHEN grupoEtnico = 'Gitano (rom)' THEN 1 END) AS total_gitanorom,
COUNT(CASE WHEN grupoEtnico = 'Ninguno' THEN 1 END) AS total_ninguno,
COUNT(CASE WHEN grupoEtnico = 'Mestizo' THEN 1 END) AS total_mestizo
FROM integventanilla
WHERE fecha_alta_integVenta BETWEEN '$fecha_inicio' AND '$fecha_fin'
";
// Ejecutar la consulta
$res_integrantes = mysqli_query($mysqli, $sql_integrantes);
// Verificar si la consulta se ejecutó correctamente
if ($res_integrantes === false) {
    // Mostrar un mensaje de error si la consulta falla
    echo "Error en la consulta: " . mysqli_error($mysqli);
    exit;
}

// Aplicar color de fondo a las celdas A1 a 
$sheet->getStyle('A1:L1')->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffd880', // Cambia 'CCE5FF' al color deseado en formato RGB
        ],
    ],
]);

$sheet->getStyle('A4:L4')->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffd880', // Cambia 'CCE5FF' al color deseado en formato RGB
        ],
    ],
]);
$sheet->getStyle('A9:L9')->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffd880', // Cambia 'CCE5FF' al color deseado en formato RGB
        ],
    ],
]);

// Aplicar formato en negrita a las celdas con títulos
$boldFontStyle = [
    'bold' => true,
];
$sheet->getStyle('A2:L2')->applyFromArray(['font' => $boldFontStyle]);
$sheet->getStyle('A5:K5')->applyFromArray(['font' => $boldFontStyle]);

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
$sheet->getStyle('A1:L1')->applyFromArray(['font' => $styleHeader, 'fill' => $styleHeader, 'alignment' => $styleHeader]);
$sheet->getStyle('A4:K4')->applyFromArray(['font' => $styleHeader, 'fill' => $styleHeader, 'alignment' => $styleHeader]);
$sheet->getStyle('A9:K9')->applyFromArray(['font' => $styleHeader, 'fill' => $styleHeader, 'alignment' => $styleHeader]);

// // Definir los encabezados de columna

$sheet->setCellValue('A1', 'TOTAL REGISTROS');
$sheet->setCellValue('B1', 'CAMBIO DIRECCION');
$sheet->setCellValue('C1', 'ENCUESTA NUEVA');
$sheet->setCellValue('D1', 'DESCENTRALIZADAS');
$sheet->setCellValue('E1', 'ENCUESTA VERIFICACION');
$sheet->setCellValue('F1', 'FAVORES');
$sheet->setCellValue('G1', 'INCONFORMIDAD');
$sheet->setCellValue('H1', 'SISBEN NOCTURNO SI');
$sheet->setCellValue('I1', 'SISBEN NOCTURNO NO');
$sheet->setCellValue('J1', 'ZONA URBANA');
$sheet->setCellValue('K1', 'ZONA RURAL');
$sheet->setCellValue('L1', 'TOTAL INTEGRANTES');



$rowIndex = 2;
while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
    $sheet->setCellValue('A' . $rowIndex, $row['total_registros']);
    $sheet->setCellValue('B' . $rowIndex, $row['cambio_direccion']);
    $sheet->setCellValue('C' . $rowIndex, $row['encuesta_nueva']);
    $sheet->setCellValue('D' . $rowIndex, $row['descentralizado']);
    $sheet->setCellValue('E' . $rowIndex, $row['encuesta_verificacion']);
    $sheet->setCellValue('F' . $rowIndex, $row['favores']);
    $sheet->setCellValue('G' . $rowIndex, $row['inconformidad']);
    $sheet->setCellValue('H' . $rowIndex, $row['sisben_nocturno_si']);
    $sheet->setCellValue('I' . $rowIndex, $row['sisben_nocturno_no']);
    $sheet->setCellValue('J' . $rowIndex, $row['zona_urbana']);
    $sheet->setCellValue('K' . $rowIndex, $row['zona_rural']);
    $sheet->setCellValue('L' . $rowIndex, $row['total_integrantes']);
    $sheet->getStyle('A' . $rowIndex . ':L' . $rowIndex . '')->applyFromArray(['font' => $boldFontStyle]);
    $rowIndex++;
}


//titulo de barrios encuestas
// Fila donde se escribirá el título (una fila antes de $rowBarrioIndex)
$tituloRow = 9;
// Unir celdas de A a K
$sheet->mergeCells("A$tituloRow:K$tituloRow");
// Escribir el título
$sheet->setCellValue("A$tituloRow", "CANTIDAD DE ENCUESTAS POR BARRIO");
// Centrar el texto
$sheet->getStyle("A$tituloRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
// (Opcional) Negrita y tamaño de fuente
$sheet->getStyle("A$tituloRow")->getFont()->setBold(true)->setSize(14);
// Ajustar el ancho de las columna
$sheet->getColumnDimension('A')->setWidth(25);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(25);
$sheet->getColumnDimension('E')->setWidth(25);
$sheet->getColumnDimension('F')->setWidth(25);
$sheet->getColumnDimension('G')->setWidth(25);
$sheet->getColumnDimension('H')->setWidth(25);
$sheet->getColumnDimension('I')->setWidth(25);
$sheet->getColumnDimension('J')->setWidth(20);
$sheet->getColumnDimension('K')->setWidth(20);
$sheet->getColumnDimension('L')->setWidth(25);
$sheet->getDefaultRowDimension()->setRowHeight(25);
// Empieza a escribir desde la fila 4 en adelante

$rowBarrioIndex = $rowIndex + 7; // Dos filas debajo de los totales
$colIndex = 1; // Comienza en la columna A (índice 1)

while ($rowBarrio = mysqli_fetch_assoc($res_barrio)) {
    // Si llegamos a la columna 11 (K), reiniciamos y bajamos una fila
    if ($colIndex > 11) {
        $colIndex = 1; // volver a columna A
        $rowBarrioIndex++;
    }

    // Convertir número de columna a letra (A, B, C, ...)
    $colLetter = Coordinate::stringFromColumnIndex($colIndex);

    // Concatenar nombre + total
    $texto = $rowBarrio['nombre_bar'] . ' (' . $rowBarrio['total_por_barrio'] . ')';
    // Escribir el texto en una sola celda
    $sheet->setCellValue($colLetter . $rowBarrioIndex, $texto);
    // Aplicar estilo de negrita a la celda
    $sheet->getStyle($colLetter . $rowBarrioIndex)->applyFromArray(['font' => $boldFontStyle]);

    $colIndex++;
}

$tituloRow = 4;
// Unir celdas de A a K
$sheet->mergeCells("A$tituloRow:K$tituloRow");
// Escribir el título
$sheet->setCellValue("A$tituloRow", "CANTIDAD DE INTEGRANTES POR ENCUESTA");
// Centrar el texto
$sheet->getStyle("A$tituloRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
// (Opcional) Negrita y tamaño de fuente
$sheet->getStyle("A$tituloRow")->getFont()->setBold(true)->setSize(14);

$rowIntegranteIndex = 5; // Saltamos 2 filas debajo del bloque anterior
$colIndex = 1; // Empezamos desde la columna A

$rowIntegrante = mysqli_fetch_assoc($res_integrantes); // Solo hay una fila con todos los totales

foreach ($rowIntegrante as $nombreCampo => $valorTotal) {
    // Si llegamos a la columna 11 (K), reiniciamos columna y bajamos una fila
    if ($colIndex > 11) {
        $colIndex = 1;
        $rowIntegranteIndex++;
    }

    // Convertir índice de columna a letra (A, B, C, ...)
    $colLetter = Coordinate::stringFromColumnIndex($colIndex);

    // Dar formato: nombre legible + total
    $texto = ucwords(str_replace('_', ' ', $nombreCampo)) . ' (' . $valorTotal . ')';

    // Escribir el texto en la celda
    $sheet->setCellValue($colLetter . $rowIntegranteIndex, $texto);
    $sheet->getStyle($colLetter . $rowIntegranteIndex)->applyFromArray(['font' => $boldFontStyle]);

    $colIndex++;
}


// Nombre del archivo con la fecha actual
$nombreArchivo = 'Encuestas_' . $fecha_inicio . '_' . $fecha_fin . '.xlsx';
$writer = new Xlsx($spreadsheet);

//Set the headers for file download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

// Output the generated Excel file to the browser
$writer->save('php://output');
exit;
