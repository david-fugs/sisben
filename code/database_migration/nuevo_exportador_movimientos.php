<?php
// NUEVO EXPORTADOR PARA MOVIMIENTOS CON ESTRUCTURA INDIVIDUAL
// Reemplaza la sección de movimientos en exportarEncuestador.php

// ===============================================
// HOJA 3: MOVIMIENTOS (NUEVA ESTRUCTURA INDIVIDUAL)
// ===============================================
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('MOVIMIENTOS');
logError("Hoja 3 - MOVIMIENTOS creada (nueva estructura)");

// Condiciones WHERE para movimientos
$condiciones_mov = [];
if (isset($_GET['id_usu']) && $_GET['id_usu'] != '') {
    $condiciones_mov[] = "m.id_usu = '$id_usu'";
}

// Filtros de fecha para movimientos
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
    $fecha_fin_completa = $fecha_fin . ' 23:59:59';
    $condiciones_mov[] = "m.fecha_movimiento BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
}

$where_mov = '';
if (count($condiciones_mov) > 0) {
    $where_mov = 'WHERE ' . implode(' AND ', $condiciones_mov);
}

// Consulta para movimientos (NUEVA ESTRUCTURA)
$sql_movimientos = "
SELECT 
    m.doc_encVenta,
    COALESCE(ev.nom_encVenta, i.nom_info, 'N/A') as nombre_persona,
    COALESCE(ev.dir_encVenta, i.dir_info, 'N/A') as direccion,
    m.tipo_movimiento,
    m.fecha_movimiento,
    m.observacion,
    u.nombre AS nombre_usuario,
    CASE 
        WHEN m.id_encuesta IS NOT NULL THEN 'ENCUESTA'
        WHEN m.id_informacion IS NOT NULL THEN 'INFORMACION'
        ELSE 'N/A'
    END as origen
FROM movimientos m
LEFT JOIN usuarios u ON m.id_usu = u.id_usu
LEFT JOIN encventanilla ev ON m.id_encuesta = ev.id_encVenta
LEFT JOIN informacion i ON m.id_informacion = i.id_informacion
$where_mov
ORDER BY m.fecha_movimiento DESC
";

$res_movimientos = mysqli_query($mysqli, $sql_movimientos);
if ($res_movimientos === false) {
    echo "Error en la consulta de movimientos: " . mysqli_error($mysqli);
    exit;
}
logError("Consulta de movimientos ejecutada correctamente (nueva estructura)");

// Aplicar estilos a la hoja 3
$sheet3->getStyle('A1:H1')->applyFromArray($styleHeader);

// Encabezados para MOVIMIENTOS (NUEVA ESTRUCTURA)
$sheet3->setCellValue('A1', 'DOCUMENTO');
$sheet3->setCellValue('B1', 'NOMBRE');
$sheet3->setCellValue('C1', 'DIRECCION');
$sheet3->setCellValue('D1', 'TIPO MOVIMIENTO');
$sheet3->setCellValue('E1', 'FECHA MOVIMIENTO');
$sheet3->setCellValue('F1', 'OBSERVACION');
$sheet3->setCellValue('G1', 'USUARIO');
$sheet3->setCellValue('H1', 'ORIGEN');

// Ajustar ancho de columnas para MOVIMIENTOS
$sheet3->getColumnDimension('A')->setWidth(15); // DOCUMENTO
$sheet3->getColumnDimension('B')->setWidth(35); // NOMBRE
$sheet3->getColumnDimension('C')->setWidth(35); // DIRECCION
$sheet3->getColumnDimension('D')->setWidth(25); // TIPO MOVIMIENTO
$sheet3->getColumnDimension('E')->setWidth(20); // FECHA MOVIMIENTO
$sheet3->getColumnDimension('F')->setWidth(40); // OBSERVACION
$sheet3->getColumnDimension('G')->setWidth(25); // USUARIO
$sheet3->getColumnDimension('H')->setWidth(15); // ORIGEN

$sheet3->getDefaultRowDimension()->setRowHeight(25);

// Escribir datos de MOVIMIENTOS (NUEVA ESTRUCTURA)
$rowIndex3 = 2;
while ($row = mysqli_fetch_array($res_movimientos, MYSQLI_ASSOC)) {
    $sheet3->setCellValue('A' . $rowIndex3, $row['doc_encVenta']);
    $sheet3->setCellValue('B' . $rowIndex3, $row['nombre_persona']);
    $sheet3->setCellValue('C' . $rowIndex3, $row['direccion']);
    $sheet3->setCellValue('D' . $rowIndex3, $row['tipo_movimiento']);
    $sheet3->setCellValue('E' . $rowIndex3, $row['fecha_movimiento']);
    $sheet3->setCellValue('F' . $rowIndex3, $row['observacion']);
    $sheet3->setCellValue('G' . $rowIndex3, $row['nombre_usuario']);
    $sheet3->setCellValue('H' . $rowIndex3, $row['origen']);
    $rowIndex3++;
}

logError("Datos de movimientos escritos en la hoja 3 (nueva estructura)");
?>
