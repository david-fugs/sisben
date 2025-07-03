<?php

if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
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
    // Convertir fechas para incluir todo el día
    $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
    $fecha_fin_completa = $fecha_fin . ' 23:59:59';
    $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
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
// Consulta 2: Conteo de rangos de edad por barrio
$sql_por_barrio = "
SELECT 
    b.nombre_bar,
    c.nombre_com,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '1' THEN 1 END) AS masculino_0_6,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '1' THEN 1 END) AS femenino_0_6,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '2' THEN 1 END) AS masculino_7_12,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '2' THEN 1 END) AS femenino_7_12,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '3' THEN 1 END) AS masculino_13_17,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '3' THEN 1 END) AS femenino_13_17,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '4' THEN 1 END) AS masculino_18_28,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '4' THEN 1 END) AS femenino_18_28,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '5' THEN 1 END) AS masculino_39_45,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '5' THEN 1 END) AS femenino_39_45,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '6' THEN 1 END) AS masculino_46_64,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '6' THEN 1 END) AS femenino_46_64,
    COUNT(CASE WHEN iv.gen_integVenta = 'M' AND iv.rango_integVenta = '7' THEN 1 END) AS masculino_mayor_65,
    COUNT(CASE WHEN iv.gen_integVenta = 'F' AND iv.rango_integVenta = '7' THEN 1 END) AS femenino_mayor_65,
    COUNT(*) AS total_por_barrio
FROM integventanilla iv
JOIN encventanilla ev ON iv.id_encVenta = ev.id_encVenta
LEFT JOIN barrios b ON ev.id_bar = b.id_bar
LEFT JOIN comunas c ON b.id_com = c.id_com
" . (!empty($fecha_inicio) && !empty($fecha_fin) ? "WHERE ev.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'" : "") . "
GROUP BY b.nombre_bar, b.id_bar, c.nombre_com
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
FROM integventanilla iv
" . (!empty($fecha_inicio) && !empty($fecha_fin) ? "WHERE iv.fecha_alta_integVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'" : "") . "
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

$sheet->getStyle('A4:P4')->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffd880', // Cambia 'CCE5FF' al color deseado en formato RGB
        ],
    ],
]);
$sheet->getStyle('A9:S9')->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffd880', // Cambia 'CCE5FF' al color deseado en formato RGB
        ],
    ],
]);

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(25); // Barrio
$sheet->getColumnDimension('B')->setWidth(20); // Comuna
$sheet->getColumnDimension('C')->setWidth(12); // M 0-6
$sheet->getColumnDimension('D')->setWidth(12); // M 7-12
$sheet->getColumnDimension('E')->setWidth(12); // M 13-17
$sheet->getColumnDimension('F')->setWidth(12); // M 18-28
$sheet->getColumnDimension('G')->setWidth(12); // M 39-45
$sheet->getColumnDimension('H')->setWidth(12); // M 46-64
$sheet->getColumnDimension('I')->setWidth(12); // M +65
$sheet->getColumnDimension('J')->setWidth(15); // TOTAL M
$sheet->getColumnDimension('K')->setWidth(12); // F 0-6
$sheet->getColumnDimension('L')->setWidth(12); // F 7-12
$sheet->getColumnDimension('M')->setWidth(12); // F 13-17
$sheet->getColumnDimension('N')->setWidth(12); // F 18-28
$sheet->getColumnDimension('O')->setWidth(12); // F 39-45
$sheet->getColumnDimension('P')->setWidth(12); // F 46-64
$sheet->getColumnDimension('Q')->setWidth(12); // F +65
$sheet->getColumnDimension('R')->setWidth(15); // TOTAL F
$sheet->getColumnDimension('S')->setWidth(15); // TOTAL
$sheet->getDefaultRowDimension()->setRowHeight(25);

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
$sheet->getStyle('A4:P4')->applyFromArray(['font' => $styleHeader, 'fill' => $styleHeader, 'alignment' => $styleHeader]);
$sheet->getStyle('A9:S9')->applyFromArray(['font' => $styleHeader, 'fill' => $styleHeader, 'alignment' => $styleHeader]);

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


// Título de totales generales de integrantes en la fila 4
$tituloRow = 4;
$sheet->mergeCells("A$tituloRow:P$tituloRow");
$sheet->setCellValue("A$tituloRow", "TOTALES GENERALES DE INTEGRANTES");
$sheet->getStyle("A$tituloRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A$tituloRow")->getFont()->setBold(true)->setSize(14);

$rowIntegranteIndex = 5; // Empezamos en la fila 5
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

//titulo de rangos de edad por barrio
// Fila donde se escribirá el título 
$tituloRow = 9;
// Unir celdas de A a S para abarcar todas las columnas necesarias
$sheet->mergeCells("A$tituloRow:S$tituloRow");
// Escribir el título
$sheet->setCellValue("A$tituloRow", "RANGOS DE EDAD POR BARRIO Y GÉNERO");
// Centrar el texto
$sheet->getStyle("A$tituloRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
// (Opcional) Negrita y tamaño de fuente
$sheet->getStyle("A$tituloRow")->getFont()->setBold(true)->setSize(14);

// Crear encabezados para los rangos de edad
$rowBarrioIndex = 10; // Directamente en la fila 10

// Establecer encabezados de columnas para los rangos
$sheet->setCellValue('A' . $rowBarrioIndex, 'BARRIO');
$sheet->setCellValue('B' . $rowBarrioIndex, 'COMUNA');
$sheet->setCellValue('C' . $rowBarrioIndex, 'M 0-6');
$sheet->setCellValue('D' . $rowBarrioIndex, 'M 7-12');
$sheet->setCellValue('E' . $rowBarrioIndex, 'M 13-17');
$sheet->setCellValue('F' . $rowBarrioIndex, 'M 18-28');
$sheet->setCellValue('G' . $rowBarrioIndex, 'M 39-45');
$sheet->setCellValue('H' . $rowBarrioIndex, 'M 46-64');
$sheet->setCellValue('I' . $rowBarrioIndex, 'M +65');
$sheet->setCellValue('J' . $rowBarrioIndex, 'TOTAL M');
$sheet->setCellValue('K' . $rowBarrioIndex, 'F 0-6');
$sheet->setCellValue('L' . $rowBarrioIndex, 'F 7-12');
$sheet->setCellValue('M' . $rowBarrioIndex, 'F 13-17');
$sheet->setCellValue('N' . $rowBarrioIndex, 'F 18-28');
$sheet->setCellValue('O' . $rowBarrioIndex, 'F 39-45');
$sheet->setCellValue('P' . $rowBarrioIndex, 'F 46-64');
$sheet->setCellValue('Q' . $rowBarrioIndex, 'F +65');
$sheet->setCellValue('R' . $rowBarrioIndex, 'TOTAL F');
$sheet->setCellValue('S' . $rowBarrioIndex, 'TOTAL');

// Aplicar estilo a los encabezados
$sheet->getStyle("A$rowBarrioIndex:S$rowBarrioIndex")->applyFromArray(['font' => $boldFontStyle]);
$sheet->getStyle("A$rowBarrioIndex:S$rowBarrioIndex")->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffd880',
        ],
    ],
]);

$rowBarrioIndex++; // Mover a la siguiente fila para empezar con los datos

// Variables para calcular totales generales
$totales = [
    'masculino_0_6' => 0, 'masculino_7_12' => 0, 'masculino_13_17' => 0, 'masculino_18_28' => 0,
    'masculino_39_45' => 0, 'masculino_46_64' => 0, 'masculino_mayor_65' => 0,
    'femenino_0_6' => 0, 'femenino_7_12' => 0, 'femenino_13_17' => 0, 'femenino_18_28' => 0,
    'femenino_39_45' => 0, 'femenino_46_64' => 0, 'femenino_mayor_65' => 0,
    'total_masculino' => 0, 'total_femenino' => 0, 'total_general' => 0
];


while ($rowBarrio = mysqli_fetch_assoc($res_barrio)) {
    // Calcular totales por género para ESTA fila
    $totalMasculino = $rowBarrio['masculino_0_6'] + $rowBarrio['masculino_7_12'] + $rowBarrio['masculino_13_17'] + 
                     $rowBarrio['masculino_18_28'] + $rowBarrio['masculino_39_45'] + $rowBarrio['masculino_46_64'] + 
                     $rowBarrio['masculino_mayor_65'];
    
    $totalFemenino = $rowBarrio['femenino_0_6'] + $rowBarrio['femenino_7_12'] + $rowBarrio['femenino_13_17'] + 
                    $rowBarrio['femenino_18_28'] + $rowBarrio['femenino_39_45'] + $rowBarrio['femenino_46_64'] + 
                    $rowBarrio['femenino_mayor_65'];
    
    // Acumular totales GENERALES (suma de todos los barrios)
    $totales['masculino_0_6'] += $rowBarrio['masculino_0_6'];
    $totales['masculino_7_12'] += $rowBarrio['masculino_7_12'];
    $totales['masculino_13_17'] += $rowBarrio['masculino_13_17'];
    $totales['masculino_18_28'] += $rowBarrio['masculino_18_28'];
    $totales['masculino_39_45'] += $rowBarrio['masculino_39_45'];
    $totales['masculino_46_64'] += $rowBarrio['masculino_46_64'];
    $totales['masculino_mayor_65'] += $rowBarrio['masculino_mayor_65'];
    $totales['femenino_0_6'] += $rowBarrio['femenino_0_6'];
    $totales['femenino_7_12'] += $rowBarrio['femenino_7_12'];
    $totales['femenino_13_17'] += $rowBarrio['femenino_13_17'];
    $totales['femenino_18_28'] += $rowBarrio['femenino_18_28'];
    $totales['femenino_39_45'] += $rowBarrio['femenino_39_45'];
    $totales['femenino_46_64'] += $rowBarrio['femenino_46_64'];
    $totales['femenino_mayor_65'] += $rowBarrio['femenino_mayor_65'];
    
    // IMPORTANTE: Acumular totales por género y general
    $totales['total_masculino'] += $totalMasculino;
    $totales['total_femenino'] += $totalFemenino;
    $totales['total_general'] += ($totalMasculino + $totalFemenino); // Suma calculada, no la de BD
    
    // Escribir datos de este barrio
    $sheet->setCellValue('A' . $rowBarrioIndex, $rowBarrio['nombre_bar']);
    $sheet->setCellValue('B' . $rowBarrioIndex, $rowBarrio['nombre_com']);
    // Hombres
    $sheet->setCellValue('C' . $rowBarrioIndex, $rowBarrio['masculino_0_6']);
    $sheet->setCellValue('D' . $rowBarrioIndex, $rowBarrio['masculino_7_12']);
    $sheet->setCellValue('E' . $rowBarrioIndex, $rowBarrio['masculino_13_17']);
    $sheet->setCellValue('F' . $rowBarrioIndex, $rowBarrio['masculino_18_28']);
    $sheet->setCellValue('G' . $rowBarrioIndex, $rowBarrio['masculino_39_45']);
    $sheet->setCellValue('H' . $rowBarrioIndex, $rowBarrio['masculino_46_64']);
    $sheet->setCellValue('I' . $rowBarrioIndex, $rowBarrio['masculino_mayor_65']);
    $sheet->setCellValue('J' . $rowBarrioIndex, $totalMasculino);
    // Mujeres
    $sheet->setCellValue('K' . $rowBarrioIndex, $rowBarrio['femenino_0_6']);
    $sheet->setCellValue('L' . $rowBarrioIndex, $rowBarrio['femenino_7_12']);
    $sheet->setCellValue('M' . $rowBarrioIndex, $rowBarrio['femenino_13_17']);
    $sheet->setCellValue('N' . $rowBarrioIndex, $rowBarrio['femenino_18_28']);
    $sheet->setCellValue('O' . $rowBarrioIndex, $rowBarrio['femenino_39_45']);
    $sheet->setCellValue('P' . $rowBarrioIndex, $rowBarrio['femenino_46_64']);
    $sheet->setCellValue('Q' . $rowBarrioIndex, $rowBarrio['femenino_mayor_65']);
    $sheet->setCellValue('R' . $rowBarrioIndex, $totalFemenino);
    $sheet->setCellValue('S' . $rowBarrioIndex, $totalMasculino + $totalFemenino);
    
    $rowBarrioIndex++;
}

// Agregar fila de totales generales

$sheet->setCellValue('A' . $rowBarrioIndex, 'TOTALES GENERALES');
$sheet->setCellValue('B' . $rowBarrioIndex, '');
$sheet->setCellValue('C' . $rowBarrioIndex, $totales['masculino_0_6']);
$sheet->setCellValue('D' . $rowBarrioIndex, $totales['masculino_7_12']);
$sheet->setCellValue('E' . $rowBarrioIndex, $totales['masculino_13_17']);
$sheet->setCellValue('F' . $rowBarrioIndex, $totales['masculino_18_28']);
$sheet->setCellValue('G' . $rowBarrioIndex, $totales['masculino_39_45']);
$sheet->setCellValue('H' . $rowBarrioIndex, $totales['masculino_46_64']);
$sheet->setCellValue('I' . $rowBarrioIndex, $totales['masculino_mayor_65']);
$sheet->setCellValue('J' . $rowBarrioIndex, $totales['total_masculino']);
$sheet->setCellValue('K' . $rowBarrioIndex, $totales['femenino_0_6']);
$sheet->setCellValue('L' . $rowBarrioIndex, $totales['femenino_7_12']);
$sheet->setCellValue('M' . $rowBarrioIndex, $totales['femenino_13_17']);
$sheet->setCellValue('N' . $rowBarrioIndex, $totales['femenino_18_28']);
$sheet->setCellValue('O' . $rowBarrioIndex, $totales['femenino_39_45']);
$sheet->setCellValue('P' . $rowBarrioIndex, $totales['femenino_46_64']);
$sheet->setCellValue('Q' . $rowBarrioIndex, $totales['femenino_mayor_65']);
$sheet->setCellValue('R' . $rowBarrioIndex, $totales['total_femenino']);
$sheet->setCellValue('S' . $rowBarrioIndex, $totales['total_general']);
// Aplicar estilo especial a la fila de totales
$sheet->getStyle("A$rowBarrioIndex:S$rowBarrioIndex")->applyFromArray(['font' => $boldFontStyle]);
$sheet->getStyle("A$rowBarrioIndex:S$rowBarrioIndex")->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'ffcc99', // Color diferente para los totales
        ],
    ],
]);

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
