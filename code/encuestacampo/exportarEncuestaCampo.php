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
    logError("Iniciando exportarEncuestaCampo.php");
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
    // HOJA 1: ENCUESTAS DE CAMPO (datos de encuestacampo)
    // ===============================================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('ENCUESTAS DE CAMPO');
    logError("Hoja 1 - ENCUESTAS DE CAMPO creada");
    
    // Condiciones WHERE para encuestas
    $condiciones = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        // Convertir fechas para incluir todo el día
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones[] = "ec.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
        logError("Filtro de fechas aplicado: $fecha_inicio_completa a $fecha_fin_completa");
    } else {
        logError("Sin filtro de fechas - exportando todos los registros");
    }
    
    // Solo agregar filtro de usuario si no es "todos"
    $id_usu = null; // Inicializar la variable
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

    // Consulta para encuestas de campo
    $sql_encuestas = "
    SELECT ec.*, b.nombre_bar AS barrio_nombre, d.nombre_departamento AS departamento_nombre, 
           c.nombre_com AS comuna_nombre, m.nombre_municipio as ciudad_nombre,
           u.nombre AS nombre_usuario
    FROM encuestacampo ec
    LEFT JOIN barrios b ON ec.id_bar = b.id_bar
    LEFT JOIN departamentos d ON ec.departamento_expedicion = d.cod_departamento
    LEFT JOIN comunas c ON ec.id_com = c.id_com
    LEFT JOIN municipios m ON ec.ciudad_expedicion = m.cod_municipio
    LEFT JOIN usuarios u ON ec.id_usu = u.id_usu
    $where_encuestas
    ORDER BY ec.fecha_alta_encVenta DESC
    ";
    $res_encuestas = mysqli_query($mysqli, $sql_encuestas);
    if ($res_encuestas === false) {
        logError("Error en la consulta de encuestas: " . mysqli_error($mysqli));
        echo "Error en la consulta de encuestas: " . mysqli_error($mysqli);
        exit;
    }
    
    $num_rows = mysqli_num_rows($res_encuestas);
    logError("Consulta de encuestas ejecutada correctamente. Filas encontradas: " . $num_rows);
    logError("SQL ejecutado: " . $sql_encuestas);

    // Configuración de estilos
    $boldFontStyle = ['bold' => true];
    $styleHeader = [
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '333333']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffd880']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];

    // Aplicar estilos a la hoja 1
    $sheet1->getStyle('A1:AJ1')->applyFromArray($styleHeader);

    // Encabezados para ENCUESTAS DE CAMPO
    $sheet1->setCellValue('A1', 'FECHA ENCUESTA');
    $sheet1->setCellValue('B1', 'DOCUMENTO');
    $sheet1->setCellValue('C1', 'TIPO DOCUMENTO');
    $sheet1->setCellValue('D1', 'FECHA EXPEDICION');
    $sheet1->setCellValue('E1', 'DEPARTAMENTO EXPEDICION');
    $sheet1->setCellValue('F1', 'CIUDAD EXPEDICION');
    $sheet1->setCellValue('G1', 'NOMBRE');
    $sheet1->setCellValue('H1', 'FECHA NACIMIENTO');
    $sheet1->setCellValue('I1', 'DIRECCION');
    $sheet1->setCellValue('J1', 'ZONA');
    $sheet1->setCellValue('K1', 'COMUNA');
    $sheet1->setCellValue('L1', 'BARRIO');
    $sheet1->setCellValue('M1', 'QUE OTRO BARRIO');
    $sheet1->setCellValue('N1', 'TRAMITE SOLICITADO');
    $sheet1->setCellValue('O1', 'CANTIDAD INTEGRANTES');
    $sheet1->setCellValue('P1', 'NUMERO FICHA');
    $sheet1->setCellValue('Q1', 'SISBEN NOCTURNO');
    $sheet1->setCellValue('R1', 'OBSERVACIONES');

    // Siempre agregar encabezados de caracterización para mantener consistencia
    $sheet1->setCellValue('S1', 'GÉNERO');
    $sheet1->setCellValue('T1', 'RANGO EDAD');
    $sheet1->setCellValue('U1', 'VICTIMA');
    $sheet1->setCellValue('V1', 'CONDICION DISCAPACIDAD');
    $sheet1->setCellValue('W1', 'TIPO DISCAPACIDAD');
    $sheet1->setCellValue('X1', 'MUJER GESTANTE');
    $sheet1->setCellValue('Y1', 'CABEZA FAMILIA');
    $sheet1->setCellValue('Z1', 'ORIENTACION SEXUAL');
    $sheet1->setCellValue('AA1', 'EXPERIENCIA MIGRATORIA');
    $sheet1->setCellValue('AB1', 'GRUPO ETNICO');
    $sheet1->setCellValue('AC1', 'SEGURIDAD SALUD');
    $sheet1->setCellValue('AD1', 'NIVEL EDUCATIVO');
    $sheet1->setCellValue('AE1', 'CONDICION OCUPACION');
    $sheet1->setCellValue('AF1', 'ASESOR');
    $sheet1->setCellValue('AG1', 'EDAD');
    
    // NUEVOS CAMPOS ESPECÍFICOS DE ENCUESTA DE CAMPO
    $sheet1->setCellValue('AH1', 'NUMERO DE VISITA');
    $sheet1->setCellValue('AI1', 'TIPO DE PROCESO');
    $sheet1->setCellValue('AJ1', 'ESTADO DE LA FICHA');

    // Si el filtro es TODOS, usamos datos de integrantes, sino dejamos vacío
    $isTodos = (!isset($_GET['id_usu']) || $_GET['id_usu'] == '' || $_GET['id_usu'] == 'todos');

    // Ajustar ancho de columnas para ENCUESTAS DE CAMPO
    foreach ([
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R',
        'S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ'
    ] as $col) {
        $sheet1->getColumnDimension($col)->setWidth(20);
    }
    // Ajustar ancho específico para columnas de caracterización
    foreach(['S','T','U','V','W','X','Y','Z','AA','AB','AC','AD'] as $col) {
        $sheet1->getColumnDimension($col)->setWidth(30);
    }
    // Ajustar ancho específico para ASESOR y nuevos campos
    $sheet1->getColumnDimension('AF')->setWidth(25);
    $sheet1->getColumnDimension('AH')->setWidth(18);
    $sheet1->getColumnDimension('AI')->setWidth(25);
    $sheet1->getColumnDimension('AJ')->setWidth(25);
    $sheet1->getDefaultRowDimension()->setRowHeight(25);

    // --- FUNCIÓN PARA LIMPIAR TEXTO DE TIPO DISCAPACIDAD ---
    if (!function_exists('limpiarTexto')) {
        function limpiarTexto($texto) {
            // Normaliza a UTF-8 si es necesario (opcional, solo si hay problemas de codificación)
            // $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
            // Reemplaza casos conocidos
            $texto = str_replace(['FÃ­sica', 'Física', 'FISICA', 'FÍSICA'], 'Fisica', $texto);
            // Elimina tildes
            $texto = strtr($texto, 'áéíóúÁÉÍÓÚ', 'aeiouAEIOU');
            // Elimina otros caracteres raros
            $texto = preg_replace('/[^A-Za-z0-9 ]/', '', $texto);
            return $texto;
        }
    }

    // Escribir datos de ENCUESTAS DE CAMPO
    $rowIndex1 = 2;
    $registros_escritos = 0;
    while ($row = mysqli_fetch_array($res_encuestas, MYSQLI_ASSOC)) {
        $registros_escritos++;
        logError("Procesando registro " . $registros_escritos . " - ID: " . $row['id_encCampo'] . " - Documento: " . $row['doc_encVenta']);
        
        $sheet1->setCellValue('A' . $rowIndex1, $row['fecha_alta_encVenta']);
        $sheet1->setCellValue('B' . $rowIndex1, $row['doc_encVenta']);
        $sheet1->setCellValue('C' . $rowIndex1, $row['tipo_documento']);
        $sheet1->setCellValue('D' . $rowIndex1, $row['fecha_expedicion']);
        $sheet1->setCellValue('E' . $rowIndex1, $row['departamento_nombre']);
        $sheet1->setCellValue('F' . $rowIndex1, $row['ciudad_nombre']);
        $sheet1->setCellValue('G' . $rowIndex1, $row['nom_encVenta']);
        $sheet1->setCellValue('H' . $rowIndex1, $row['fecha_nacimiento']);
        $sheet1->setCellValue('I' . $rowIndex1, $row['dir_encVenta']);
        $sheet1->setCellValue('J' . $rowIndex1, $row['zona_encVenta']);
        $sheet1->setCellValue('K' . $rowIndex1, $row['comuna_nombre']);
        $sheet1->setCellValue('L' . $rowIndex1, $row['barrio_nombre']);
        $sheet1->setCellValue('M' . $rowIndex1, $row['otro_bar_ver_encVenta']);
        $sheet1->setCellValue('N' . $rowIndex1, $row['tram_solic_encVenta']);
        $sheet1->setCellValue('O' . $rowIndex1, $row['integra_encVenta']);
        $sheet1->setCellValue('P' . $rowIndex1, $row['num_ficha_encVenta']);
        $sheet1->setCellValue('Q' . $rowIndex1, $row['sisben_nocturno']);
        $sheet1->setCellValue('R' . $rowIndex1, $row['obs_encVenta']);
        
        // Si el filtro es TODOS, buscar y agregar el primer integrante
        $integ = null;
        if ($isTodos) {
            $sql_integ = "SELECT * FROM integcampo WHERE id_encuesta = " . $row['id_encCampo'] . " ORDER BY id_integCampo ASC LIMIT 1";
            $res_integ = mysqli_query($mysqli, $sql_integ);
            $integ = mysqli_fetch_array($res_integ, MYSQLI_ASSOC);
        }

        if ($integ) {
            // Limpiar tipo discapacidad si existe
            $tipoDiscapacidad = isset($integ['tipoDiscapacidad']) ? limpiarTexto($integ['tipoDiscapacidad']) : '';
            
            $sheet1->setCellValue('S' . $rowIndex1, $integ['gen_integVenta']);
            $sheet1->setCellValue('T' . $rowIndex1, $integ['rango_integVenta']);
            $sheet1->setCellValue('U' . $rowIndex1, $integ['victima']);
            $sheet1->setCellValue('V' . $rowIndex1, $integ['condicionDiscapacidad']);
            $sheet1->setCellValue('W' . $rowIndex1, $tipoDiscapacidad);
            $sheet1->setCellValue('X' . $rowIndex1, $integ['mujerGestante']);
            $sheet1->setCellValue('Y' . $rowIndex1, $integ['cabezaFamilia']);
            $sheet1->setCellValue('Z' . $rowIndex1, $integ['orientacionSexual']);
            $sheet1->setCellValue('AA' . $rowIndex1, $integ['experienciaMigratoria']);
            $sheet1->setCellValue('AB' . $rowIndex1, $integ['grupoEtnico']);
            $sheet1->setCellValue('AC' . $rowIndex1, $integ['seguridadSalud']);
            $sheet1->setCellValue('AD' . $rowIndex1, $integ['nivelEducativo']);
            $sheet1->setCellValue('AE' . $rowIndex1, $integ['condicionOcupacion']);
            $sheet1->setCellValue('AF' . $rowIndex1, $row['nombre_usuario']);
            $sheet1->setCellValue('AG' . $rowIndex1, ''); // No hay campo edad específico en integcampo
        } else {
            // Dejar campos vacíos si no hay integrantes o si no es filtro "todos"
            for ($col = 19; $col <= 33; $col++) { // Columnas S a AG
                $sheet1->setCellValue(Coordinate::stringFromColumnIndex($col) . $rowIndex1, '');
            }
            $sheet1->setCellValue('AF' . $rowIndex1, $row['nombre_usuario']); // Siempre mostrar asesor
        }
        
        // NUEVOS CAMPOS ESPECÍFICOS DE ENCUESTA DE CAMPO
        $sheet1->setCellValue('AH' . $rowIndex1, $row['num_visita']);
        $sheet1->setCellValue('AI' . $rowIndex1, $row['tipo_proceso']);
        $sheet1->setCellValue('AJ' . $rowIndex1, $row['estado_ficha']);

        $rowIndex1++;
    }
    
    logError("Total de registros escritos en hoja 1: " . $registros_escritos);
    
    // Si no hay datos, agregar una fila indicando esto
    if ($registros_escritos == 0) {
        $sheet1->setCellValue('A2', 'No se encontraron registros en el rango de fechas especificado');
        $sheet1->mergeCells('A2:AJ2');
        logError("No se encontraron registros para exportar");
    }

    // ===============================================
    // HOJA 2: INTEGRANTES DE ENCUESTAS DE CAMPO
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('INTEGRANTES CAMPO');
    logError("Hoja 2 - INTEGRANTES CAMPO creada");

    // Aplicar estilos a la hoja 2
    $sheet2->getStyle('A1:V1')->applyFromArray($styleHeader);

    // Encabezados para INTEGRANTES
    $sheet2->setCellValue('A1', 'ID ENCUESTA');
    $sheet2->setCellValue('B1', 'DOCUMENTO TITULAR');
    $sheet2->setCellValue('C1', 'NOMBRE TITULAR');
    $sheet2->setCellValue('D1', 'NUMERO FICHA');
    $sheet2->setCellValue('E1', 'GÉNERO');
    $sheet2->setCellValue('F1', 'ORIENTACION SEXUAL');
    $sheet2->setCellValue('G1', 'RANGO EDAD');
    $sheet2->setCellValue('H1', 'GRUPO ETNICO');
    $sheet2->setCellValue('I1', 'VICTIMA');
    $sheet2->setCellValue('J1', 'CONDICION DISCAPACIDAD');
    $sheet2->setCellValue('K1', 'TIPO DISCAPACIDAD');
    $sheet2->setCellValue('L1', 'MUJER GESTANTE');
    $sheet2->setCellValue('M1', 'CABEZA FAMILIA');
    $sheet2->setCellValue('N1', 'EXPERIENCIA MIGRATORIA');
    $sheet2->setCellValue('O1', 'SEGURIDAD SALUD');
    $sheet2->setCellValue('P1', 'NIVEL EDUCATIVO');
    $sheet2->setCellValue('Q1', 'CONDICION OCUPACION');
    $sheet2->setCellValue('R1', 'ASESOR');
    $sheet2->setCellValue('S1', 'FECHA REGISTRO');
    $sheet2->setCellValue('T1', 'NUMERO DE VISITA');
    $sheet2->setCellValue('U1', 'TIPO DE PROCESO');
    $sheet2->setCellValue('V1', 'ESTADO DE LA FICHA');

    // Ajustar ancho de columnas para INTEGRANTES
    foreach (['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V'] as $col) {
        $sheet2->getColumnDimension($col)->setWidth(20);
    }
    $sheet2->getDefaultRowDimension()->setRowHeight(25);

    // Consulta para integrantes de encuestas de campo
    $where_integrantes = $where_encuestas;
    // Cambiar alias en la consulta de integrantes
    if (!empty($where_integrantes)) {
        $where_integrantes = str_replace('ec.', 'ec.', $where_integrantes);
    }
    
    $sql_integrantes = "
    SELECT ic.*, ec.doc_encVenta, ec.nom_encVenta, ec.num_ficha_encVenta, 
           ec.num_visita, ec.tipo_proceso, ec.estado_ficha, ec.fecha_alta_encVenta,
           u.nombre AS nombre_usuario
    FROM integcampo ic
    INNER JOIN encuestacampo ec ON ic.id_encuesta = ec.id_encCampo
    LEFT JOIN usuarios u ON ec.id_usu = u.id_usu
    $where_integrantes
    ORDER BY ec.fecha_alta_encVenta DESC, ic.id_integCampo ASC
    ";

    $res_integrantes = mysqli_query($mysqli, $sql_integrantes);
    if ($res_integrantes === false) {
        echo "Error en la consulta de integrantes: " . mysqli_error($mysqli);
        exit;
    }
    logError("Consulta de integrantes ejecutada correctamente");

    // Escribir datos de INTEGRANTES
    $rowIndex2 = 2;
    while ($row = mysqli_fetch_array($res_integrantes, MYSQLI_ASSOC)) {
        $tipoDiscapacidad = isset($row['tipoDiscapacidad']) ? limpiarTexto($row['tipoDiscapacidad']) : '';
        
        $sheet2->setCellValue('A' . $rowIndex2, $row['id_encuesta']);
        $sheet2->setCellValue('B' . $rowIndex2, $row['doc_encVenta']);
        $sheet2->setCellValue('C' . $rowIndex2, $row['nom_encVenta']);
        $sheet2->setCellValue('D' . $rowIndex2, $row['num_ficha_encVenta']);
        $sheet2->setCellValue('E' . $rowIndex2, $row['gen_integVenta']);
        $sheet2->setCellValue('F' . $rowIndex2, $row['orientacionSexual']);
        $sheet2->setCellValue('G' . $rowIndex2, $row['rango_integVenta']);
        $sheet2->setCellValue('H' . $rowIndex2, $row['grupoEtnico']);
        $sheet2->setCellValue('I' . $rowIndex2, $row['victima']);
        $sheet2->setCellValue('J' . $rowIndex2, $row['condicionDiscapacidad']);
        $sheet2->setCellValue('K' . $rowIndex2, $tipoDiscapacidad);
        $sheet2->setCellValue('L' . $rowIndex2, $row['mujerGestante']);
        $sheet2->setCellValue('M' . $rowIndex2, $row['cabezaFamilia']);
        $sheet2->setCellValue('N' . $rowIndex2, $row['experienciaMigratoria']);
        $sheet2->setCellValue('O' . $rowIndex2, $row['seguridadSalud']);
        $sheet2->setCellValue('P' . $rowIndex2, $row['nivelEducativo']);
        $sheet2->setCellValue('Q' . $rowIndex2, $row['condicionOcupacion']);
        $sheet2->setCellValue('R' . $rowIndex2, $row['nombre_usuario']);
        $sheet2->setCellValue('S' . $rowIndex2, $row['fecha_alta_encVenta']);
        $sheet2->setCellValue('T' . $rowIndex2, $row['num_visita']);
        $sheet2->setCellValue('U' . $rowIndex2, $row['tipo_proceso']);
        $sheet2->setCellValue('V' . $rowIndex2, $row['estado_ficha']);

        $rowIndex2++;
    }

    // Crear el archivo Excel
    $writer = new Xlsx($spreadsheet);
    
    // Configurar headers para descarga
    $filename = 'encuestas_campo_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Limpiar cualquier output anterior
    ob_clean();
    
    logError("Archivo Excel generado correctamente: $filename");
    
    // Guardar y enviar el archivo
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    logError("Error en exportarEncuestaCampo.php: " . $e->getMessage());
    echo "Error: " . $e->getMessage();
    exit;
}
?>
