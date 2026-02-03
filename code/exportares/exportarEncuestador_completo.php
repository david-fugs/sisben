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
    logError("Hoja 1 - ENCUESTAS creada");    // Condiciones WHERE para encuestas
    $condiciones = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        // Convertir fechas para incluir todo el día
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones[] = "ev.fecha_alta_encVenta BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
    }
      // Solo agregar filtro de usuario si no es "todos"
    $id_usu = null; // Inicializar la variable
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
        $id_usu = $_GET['id_usu'];
        $condiciones[] = "ev.id_usu = '$id_usu'";
        logError("Filtrando por usuario específico: {$id_usu}");
    } else {
        logError("Exportando TODOS los encuestadores en el rango de fechas");
    }

    $where_encuestas = '';
    if (count($condiciones) > 0) {
        $where_encuestas = 'WHERE ' . implode(' AND ', $condiciones);
    }

    // Consulta para encuestas
    $sql_encuestas = "
    SELECT ev.*, b.nombre_bar AS barrio_nombre, d.nombre_departamento AS departamento_nombre, 
           c.nombre_com AS comuna_nombre, m.nombre_municipio as ciudad_nombre,
           u.nombre AS nombre_usuario
    FROM encventanilla ev
    LEFT JOIN barrios b ON ev.id_bar = b.id_bar
    LEFT JOIN departamentos d ON ev.departamento_expedicion = d.cod_departamento
    LEFT JOIN comunas c ON ev.id_com = c.id_com
    LEFT JOIN municipios m ON ev.ciudad_expedicion = m.cod_municipio
    LEFT JOIN usuarios u ON ev.id_usu = u.id_usu
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
    $sheet1->getStyle('A1:AE1')->applyFromArray($styleHeader);

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
    $sheet1->setCellValue('M1', 'TRAMITE SOLICITADO');
    $sheet1->setCellValue('N1', 'CANTIDAD INTEGRANTES');
    $sheet1->setCellValue('O1', 'NUMERO FICHA');
    $sheet1->setCellValue('P1', 'SISBEN NOCTURNO');
    $sheet1->setCellValue('Q1', 'OBSERVACIONES');

    // Siempre agregar encabezados de caracterización para mantener consistencia
    $sheet1->setCellValue('R1', 'GÉNERO');
    $sheet1->setCellValue('S1', 'RANGO EDAD');
    $sheet1->setCellValue('T1', 'VICTIMA');
    $sheet1->setCellValue('U1', 'CONDICION DISCAPACIDAD');
    $sheet1->setCellValue('V1', 'TIPO DISCAPACIDAD');
    $sheet1->setCellValue('W1', 'MUJER GESTANTE');
    $sheet1->setCellValue('X1', 'CABEZA FAMILIA');
    $sheet1->setCellValue('Y1', 'ORIENTACION SEXUAL');
    $sheet1->setCellValue('Z1', 'EXPERIENCIA MIGRATORIA');
    $sheet1->setCellValue('AA1', 'GRUPO ETNICO');
    $sheet1->setCellValue('AB1', 'SEGURIDAD SALUD');
    $sheet1->setCellValue('AC1', 'NIVEL EDUCATIVO');
    $sheet1->setCellValue('AD1', 'CONDICION OCUPACION');
    $sheet1->setCellValue('AE1', 'ASESOR');

    // Si el filtro es TODOS, usamos datos de integrantes, sino dejamos vacío
    $isTodos = (!isset($_GET['id_usu']) || $_GET['id_usu'] == '' || $_GET['id_usu'] == 'todos');

    // Ajustar ancho de columnas para ENCUESTAS
    foreach ([
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R',
        'S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'
    ] as $col) {
        $sheet1->getColumnDimension($col)->setWidth(20);
    }
    // Ajustar ancho específico para columnas de caracterización
    foreach(['R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD'] as $col) {
        $sheet1->getColumnDimension($col)->setWidth(30);
    }
    // Ajustar ancho específico para ASESOR
    $sheet1->getColumnDimension('AE')->setWidth(25);
    $sheet1->getDefaultRowDimension()->setRowHeight(25);

    // --- FUNCION PARA LIMPIAR TEXTO DE TIPO DISCAPACIDAD ---
if (!function_exists('limpiarTexto')) {
    function limpiarTexto($texto) {
        // Normaliza a UTF-8 si es necesario (opcional, solo si hay problemas de codificación)
        // $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
        // Reemplaza casos conocidos
        $texto = str_replace(['FÃ­sica', 'Física', 'FISICA', 'FÍSICA'], 'Fisica', $texto);
        $texto = str_replace('MOULTIPLE', 'MULTIPLE', $texto);
        // Elimina tildes
        $texto = strtr($texto, 'áéíóúÁÉÍÓÚ', 'aeiouAEIOU');
        // Elimina otros caracteres raros
        $texto = preg_replace('/[^A-Za-z0-9 ]/', '', $texto);
        return $texto;
    }
}

    // Escribir datos de ENCUESTAS
    $rowIndex1 = 2;
    while ($row = mysqli_fetch_array($res_encuestas, MYSQLI_ASSOC)) {
        $sheet1->setCellValue('A' . $rowIndex1, $row['fecha_alta_encVenta']);
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
        $sheet1->setCellValue('M' . $rowIndex1, $row['tram_solic_encVenta']);
        $sheet1->setCellValue('N' . $rowIndex1, $row['integra_encVenta']);
        $sheet1->setCellValue('O' . $rowIndex1, $row['num_ficha_encVenta']);
        $sheet1->setCellValue('P' . $rowIndex1, $row['sisben_nocturno']);
        $sheet1->setCellValue('Q' . $rowIndex1, $row['obs_encVenta']);
        
        // Si el filtro es TODOS, buscar y agregar el primer integrante
        if ($isTodos) {
            $id_encVenta = $row['id_encVenta'];
            $sql_integrante = "SELECT * FROM integventanilla WHERE id_encVenta = '$id_encVenta' ORDER BY id_integVenta ASC LIMIT 1";
            $res_integrante = mysqli_query($mysqli, $sql_integrante);
            $integ = mysqli_fetch_assoc($res_integrante);
            // Mapeo de rango edad
            $rangoEdadMap = [
                1 => "0 - 6",
                2 => "7 - 12", 
                3 => "13 - 17",
                4 => "18 - 28",
                5 => "29 - 45",
                6 => "46 - 64",
                7 => "Mayor o igual a 65"
            ];
            if ($integ) {
                $sheet1->setCellValue('R' . $rowIndex1, $integ['gen_integVenta'] ?? '');
                $sheet1->setCellValue('S' . $rowIndex1, isset($rangoEdadMap[$integ['rango_integVenta'] ?? null]) ? $rangoEdadMap[$integ['rango_integVenta']] : '');
                $sheet1->setCellValue('T' . $rowIndex1, $integ['victima'] ?? '');
                $sheet1->setCellValue('U' . $rowIndex1, $integ['condicionDiscapacidad'] ?? '');
                // Aquí aplicamos la limpieza a tipoDiscapacidad
                $tipoDiscapacidadLimpio = isset($integ['tipoDiscapacidad']) ? limpiarTexto($integ['tipoDiscapacidad']) : '';
                $sheet1->setCellValue('V' . $rowIndex1, $tipoDiscapacidadLimpio);
                $sheet1->setCellValue('W' . $rowIndex1, $integ['mujerGestante'] ?? '');
                $sheet1->setCellValue('X' . $rowIndex1, $integ['cabezaFamilia'] ?? '');
                $sheet1->setCellValue('Y' . $rowIndex1, $integ['orientacionSexual'] ?? '');
                $sheet1->setCellValue('Z' . $rowIndex1, $integ['experienciaMigratoria'] ?? '');
                $sheet1->setCellValue('AA' . $rowIndex1, $integ['grupoEtnico'] ?? '');
                $sheet1->setCellValue('AB' . $rowIndex1, $integ['seguridadSalud'] ?? '');
                $sheet1->setCellValue('AC' . $rowIndex1, $integ['nivelEducativo'] ?? '');
                $sheet1->setCellValue('AD' . $rowIndex1, $integ['condicionOcupacion'] ?? '');
            } else {
                // Si no hay integrante, dejar vacío
                $sheet1->setCellValue('R' . $rowIndex1, '');
                $sheet1->setCellValue('S' . $rowIndex1, '');
                $sheet1->setCellValue('T' . $rowIndex1, '');
                $sheet1->setCellValue('U' . $rowIndex1, '');
                $sheet1->setCellValue('V' . $rowIndex1, '');
                $sheet1->setCellValue('W' . $rowIndex1, '');
                $sheet1->setCellValue('X' . $rowIndex1, '');
                $sheet1->setCellValue('Y' . $rowIndex1, '');
                $sheet1->setCellValue('Z' . $rowIndex1, '');
                $sheet1->setCellValue('AA' . $rowIndex1, '');
                $sheet1->setCellValue('AB' . $rowIndex1, '');
                $sheet1->setCellValue('AC' . $rowIndex1, '');
                $sheet1->setCellValue('AD' . $rowIndex1, '');
            }
        } else {
            // Si no es TODOS, dejar columnas de caracterización vacías
            $sheet1->setCellValue('R' . $rowIndex1, '');
            $sheet1->setCellValue('S' . $rowIndex1, '');
            $sheet1->setCellValue('T' . $rowIndex1, '');
            $sheet1->setCellValue('U' . $rowIndex1, '');
            $sheet1->setCellValue('V' . $rowIndex1, '');
            $sheet1->setCellValue('W' . $rowIndex1, '');
            $sheet1->setCellValue('X' . $rowIndex1, '');
            $sheet1->setCellValue('Y' . $rowIndex1, '');
            $sheet1->setCellValue('Z' . $rowIndex1, '');
            $sheet1->setCellValue('AA' . $rowIndex1, '');
            $sheet1->setCellValue('AB' . $rowIndex1, '');
            $sheet1->setCellValue('AC' . $rowIndex1, '');
            $sheet1->setCellValue('AD' . $rowIndex1, '');
        }
        
        // ASESOR siempre al final
        $sheet1->setCellValue('AE' . $rowIndex1, $row['nombre_usuario'] ?? '');
        $rowIndex1++;
    }
    logError("Datos de encuestas escritos en la hoja 1");

    // ===============================================
    // HOJA 2: INFORMACIÓN (datos de informacion)
    // ===============================================
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('INFORMACION');
    logError("Hoja 2 - INFORMACIÓN creada");    // Condiciones WHERE para información
    $condiciones_info = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        // Convertir fechas para incluir todo el día
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones_info[] = "i.fecha_alta_info BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
    }
    
    // Solo agregar filtro de usuario si no es "todos"
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
        $condiciones_info[] = "i.id_usu = '$id_usu'";
    }

    $where_info = '';
    if (count($condiciones_info) > 0) {
        $where_info = 'WHERE ' . implode(' AND ', $condiciones_info);
    }

    // Consulta para información
    $sql_informacion = "
    SELECT i.*, d.nombre_departamento AS departamento_nombre, m.nombre_municipio as ciudad_nombre,
           u.nombre AS nombre_usuario
    FROM informacion i
    LEFT JOIN departamentos d ON i.departamento_expedicion = d.cod_departamento
    LEFT JOIN municipios m ON i.ciudad_expedicion = m.cod_municipio
    LEFT JOIN usuarios u ON i.id_usu = u.id_usu
    $where_info
    ";
    $res_informacion = mysqli_query($mysqli, $sql_informacion);
    if ($res_informacion === false) {
        echo "Error en la consulta de información: " . mysqli_error($mysqli);
        exit;
    }    logError("Consulta de información ejecutada correctamente");

    // Aplicar estilos a la hoja 2 - INFORMACIÓN (mismo orden que ENCUESTAS)
    $sheet2->getStyle('A1:AE1')->applyFromArray($styleHeader);

    // Encabezados para INFORMACIÓN (mismo orden que ENCUESTAS)
    $sheet2->setCellValue('A1', 'FECHA REGISTRO');
    $sheet2->setCellValue('B1', 'DOCUMENTO');
    $sheet2->setCellValue('C1', 'TIPO DOCUMENTO');
    $sheet2->setCellValue('D1', 'FECHA EXPEDICION');
    $sheet2->setCellValue('E1', 'DEPARTAMENTO EXPEDICION');
    $sheet2->setCellValue('F1', 'CIUDAD EXPEDICION');
    $sheet2->setCellValue('G1', 'NOMBRE');
    $sheet2->setCellValue('H1', 'DIRECCION');
    $sheet2->setCellValue('I1', 'ZONA');
    $sheet2->setCellValue('J1', 'COMUNA');
    $sheet2->setCellValue('K1', 'BARRIO');
    $sheet2->setCellValue('L1', 'QUE OTRO BARRIO');
    $sheet2->setCellValue('M1', 'TRAMITE SOLICITADO');
    $sheet2->setCellValue('N1', 'CANTIDAD INTEGRANTES');
    $sheet2->setCellValue('O1', 'NUMERO FICHA');
    $sheet2->setCellValue('P1', 'SISBEN NOCTURNO');
    $sheet2->setCellValue('Q1', 'OBSERVACIONES');
    $sheet2->setCellValue('R1', 'GÉNERO');
    $sheet2->setCellValue('S1', 'RANGO EDAD');
    $sheet2->setCellValue('T1', 'VICTIMA');
    $sheet2->setCellValue('U1', 'CONDICION DISCAPACIDAD');
    $sheet2->setCellValue('V1', 'TIPO DISCAPACIDAD');
    $sheet2->setCellValue('W1', 'MUJER GESTANTE');
    $sheet2->setCellValue('X1', 'CABEZA FAMILIA');
    $sheet2->setCellValue('Y1', 'ORIENTACION SEXUAL');
    $sheet2->setCellValue('Z1', 'EXPERIENCIA MIGRATORIA');
    $sheet2->setCellValue('AA1', 'GRUPO ETNICO');
    $sheet2->setCellValue('AB1', 'SEGURIDAD SALUD');
    $sheet2->setCellValue('AC1', 'NIVEL EDUCATIVO');
    $sheet2->setCellValue('AD1', 'CONDICION OCUPACION');
    $sheet2->setCellValue('AE1', 'ASESOR');

    // Ajustar ancho de columnas para INFORMACIÓN (mismo que ENCUESTAS)
    foreach ([
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R',
        'S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'
    ] as $col) {
        $sheet2->getColumnDimension($col)->setWidth(20);
    }
    // Ajustar ancho específico para columnas de caracterización
    foreach(['R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD'] as $col) {
        $sheet2->getColumnDimension($col)->setWidth(30);
    }
    // Ajustar ancho específico para ASESOR
    $sheet2->getColumnDimension('AE')->setWidth(25);
    $sheet2->getDefaultRowDimension()->setRowHeight(25);

    // Escribir datos de INFORMACIÓN (mismo orden que ENCUESTAS)
    $rowIndex2 = 2;
    while ($row = mysqli_fetch_array($res_informacion, MYSQLI_ASSOC)) {
        $sheet2->setCellValue('A' . $rowIndex2, $row['fecha_alta_info']);
        $sheet2->setCellValue('B' . $rowIndex2, $row['doc_info']);
        $sheet2->setCellValue('C' . $rowIndex2, $row['tipo_documento']);
        $sheet2->setCellValue('D' . $rowIndex2, $row['fecha_expedicion']);
        $sheet2->setCellValue('E' . $rowIndex2, $row['departamento_nombre']);
        $sheet2->setCellValue('F' . $rowIndex2, $row['ciudad_nombre']);
        $sheet2->setCellValue('G' . $rowIndex2, $row['nom_info']);
        $sheet2->setCellValue('H' . $rowIndex2, ''); // DIRECCION - no existe en informacion
        $sheet2->setCellValue('I' . $rowIndex2, ''); // ZONA - no existe en informacion
        $sheet2->setCellValue('J' . $rowIndex2, ''); // COMUNA - no existe en informacion
        $sheet2->setCellValue('K' . $rowIndex2, ''); // BARRIO - no existe en informacion
        $sheet2->setCellValue('L' . $rowIndex2, ''); // QUE OTRO BARRIO - no existe en informacion
        $sheet2->setCellValue('M' . $rowIndex2, $row['tipo_solic_encInfo'] ?? ''); // TRAMITE SOLICITADO
        $sheet2->setCellValue('N' . $rowIndex2, ''); // CANTIDAD INTEGRANTES - no existe en informacion
        $sheet2->setCellValue('O' . $rowIndex2, ''); // NUMERO FICHA - no existe en informacion
        $sheet2->setCellValue('P' . $rowIndex2, ''); // SISBEN NOCTURNO - no existe en informacion
        $sheet2->setCellValue('Q' . $rowIndex2, $row['observacion'] ?? ''); // OBSERVACIONES
        
        // Mapeo de rango edad (mismo que ENCUESTAS)
        $rangoEdadMap = [
            1 => "0 - 6",
            2 => "7 - 12", 
            3 => "13 - 17",
            4 => "18 - 28",
            5 => "29 - 45",
            6 => "46 - 64",
            7 => "Mayor o igual a 65"
        ];
        
        // Datos de caracterización (que sí existen en información)
        $sheet2->setCellValue('R' . $rowIndex2, $row['gen_integVenta']);
        $sheet2->setCellValue('S' . $rowIndex2, isset($rangoEdadMap[$row['rango_integVenta'] ?? null]) ? $rangoEdadMap[$row['rango_integVenta']] : '');
        $sheet2->setCellValue('T' . $rowIndex2, $row['victima']);
        $sheet2->setCellValue('U' . $rowIndex2, $row['condicionDiscapacidad']);
        $sheet2->setCellValue('V' . $rowIndex2, $row['tipoDiscapacidad']);
        $sheet2->setCellValue('W' . $rowIndex2, $row['mujerGestante']);
        $sheet2->setCellValue('X' . $rowIndex2, $row['cabezaFamilia']);
        $sheet2->setCellValue('Y' . $rowIndex2, $row['orientacionSexual']);
        $sheet2->setCellValue('Z' . $rowIndex2, $row['experienciaMigratoria']);
        $sheet2->setCellValue('AA' . $rowIndex2, $row['grupoEtnico']);
        $sheet2->setCellValue('AB' . $rowIndex2, $row['seguridadSalud']);
        $sheet2->setCellValue('AC' . $rowIndex2, $row['nivelEducativo']);
        $sheet2->setCellValue('AD' . $rowIndex2, $row['condicionOcupacion']);
        
        // ASESOR siempre al final
        $sheet2->setCellValue('AE' . $rowIndex2, $row['nombre_usuario'] ?? '');
        $rowIndex2++;
    }
    logError("Datos de información escritos en la hoja 2");    // ===============================================
    // HOJA 3: MOVIMIENTOS (datos de movimientos)
    // ===============================================
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('MOVIMIENTOS');
    logError("Hoja 3 - MOVIMIENTOS creada");

    // Condiciones WHERE para movimientos
    $condiciones_mov = [];
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        // Convertir fechas para incluir todo el día
        $fecha_inicio_completa = $fecha_inicio . ' 00:00:00';
        $fecha_fin_completa = $fecha_fin . ' 23:59:59';
        $condiciones_mov[] = "m.fecha_movimiento BETWEEN '$fecha_inicio_completa' AND '$fecha_fin_completa'";
    }    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
        $user_id = $_GET['id_usu'];
        $condiciones_mov[] = "m.id_usu = '$user_id'";
    }

    $where_mov = '';
    if (count($condiciones_mov) > 0) {
        $where_mov = 'WHERE ' . implode(' AND ', $condiciones_mov);
    }    // Consulta completa para movimientos con todas las columnas y nombres descriptivos
    $sql_movimientos = "
    SELECT m.*, u.nombre AS nombre_usuario, 
           COALESCE(ev.nom_encVenta, 'N/A') as nombre_persona_encuesta,
           COALESCE(ev.dir_encVenta, 'N/A') as direccion_encuesta,
           COALESCE(d.nombre_departamento, 'N/A') as departamento_nombre,
           COALESCE(mu.nombre_municipio, 'N/A') as ciudad_nombre,
           COALESCE(c.nombre_com, 'N/A') as comuna_nombre,
           COALESCE(b.nombre_bar, 'N/A') as barrio_nombre
    FROM movimientos m
    LEFT JOIN usuarios u ON m.id_usu = u.id_usu
    LEFT JOIN encventanilla ev ON m.doc_encVenta = ev.doc_encVenta
    LEFT JOIN departamentos d ON m.departamento_expedicion = d.cod_departamento
    LEFT JOIN municipios mu ON m.ciudad_expedicion = mu.cod_municipio
    LEFT JOIN comunas c ON m.id_com = c.id_com
    LEFT JOIN barrios b ON m.id_bar = b.id_bar
    $where_mov
    ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC
    ";
    $res_movimientos = mysqli_query($mysqli, $sql_movimientos);
    if ($res_movimientos === false) {
        echo "Error en la consulta de movimientos: " . mysqli_error($mysqli);
        exit;
    }
    logError("Consulta de movimientos ejecutada correctamente");    // Aplicar estilos a la hoja 3 - MOVIMIENTOS con todas las columnas
    $sheet3->getStyle('A1:AD1')->applyFromArray($styleHeader);

    // Encabezados para MOVIMIENTOS (mismo orden que ENCUESTAS pero sin FECHA ALTA)
    $sheet3->setCellValue('A1', 'DOCUMENTO');
    $sheet3->setCellValue('B1', 'TIPO DOCUMENTO');
    $sheet3->setCellValue('C1', 'FECHA EXPEDICION');
    $sheet3->setCellValue('D1', 'DEPARTAMENTO EXPEDICION');
    $sheet3->setCellValue('E1', 'CIUDAD EXPEDICION');
    $sheet3->setCellValue('F1', 'NOMBRE');
    $sheet3->setCellValue('G1', 'DIRECCION');
    $sheet3->setCellValue('H1', 'ZONA');
    $sheet3->setCellValue('I1', 'COMUNA');
    $sheet3->setCellValue('J1', 'BARRIO');
    $sheet3->setCellValue('K1', 'QUE OTRO BARRIO');
    $sheet3->setCellValue('L1', 'TIPO MOVIMIENTO');
    $sheet3->setCellValue('M1', 'CANTIDAD INTEGRANTES');
    $sheet3->setCellValue('N1', 'NUMERO FICHA');
    $sheet3->setCellValue('O1', 'SISBEN NOCTURNO');
    $sheet3->setCellValue('P1', 'OBSERVACIONES');
    $sheet3->setCellValue('Q1', 'GÉNERO');
    $sheet3->setCellValue('R1', 'RANGO EDAD');
    $sheet3->setCellValue('S1', 'VICTIMA');
    $sheet3->setCellValue('T1', 'CONDICION DISCAPACIDAD');
    $sheet3->setCellValue('U1', 'TIPO DISCAPACIDAD');
    $sheet3->setCellValue('V1', 'MUJER GESTANTE');
    $sheet3->setCellValue('W1', 'CABEZA FAMILIA');
    $sheet3->setCellValue('X1', 'ORIENTACION SEXUAL');
    $sheet3->setCellValue('Y1', 'EXPERIENCIA MIGRATORIA');
    $sheet3->setCellValue('Z1', 'GRUPO ETNICO');
    $sheet3->setCellValue('AA1', 'SEGURIDAD SALUD');
    $sheet3->setCellValue('AB1', 'NIVEL EDUCATIVO');
    $sheet3->setCellValue('AC1', 'CONDICION OCUPACION');
    $sheet3->setCellValue('AD1', 'ASESOR');

    // Ajustar ancho de columnas para MOVIMIENTOS (mismo que ENCUESTAS pero sin FECHA ALTA)
    foreach ([
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R',
        'S','T','U','V','W','X','Y','Z','AA','AB','AC','AD'
    ] as $col) {
        $sheet3->getColumnDimension($col)->setWidth(20);
    }
    // Ajustar ancho específico para columnas de caracterización
    foreach(['Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC'] as $col) {
        $sheet3->getColumnDimension($col)->setWidth(30);
    }
    // Ajustar ancho específico para ASESOR
    $sheet3->getColumnDimension('AD')->setWidth(25);
    $sheet3->getDefaultRowDimension()->setRowHeight(25);// Escribir datos de MOVIMIENTOS (basado en integmovimientos_independiente)
    $rowIndex3 = 2;
    while ($row = mysqli_fetch_array($res_movimientos, MYSQLI_ASSOC)) {
        // Buscar el primer registro de integmovimientos_independiente para este movimiento
        $id_movimiento = $row['id_movimiento'];
        $sql_integ_mov = "SELECT * FROM integmovimientos_independiente WHERE id_movimiento = '$id_movimiento' ORDER BY id_integmov_indep ASC LIMIT 1";
        $res_integ_mov = mysqli_query($mysqli, $sql_integ_mov);
        $integ_mov = mysqli_fetch_assoc($res_integ_mov);
        
        // Si hay datos en integmovimientos_independiente, usar esos; sino usar los del movimiento base
        if ($integ_mov) {
            $sheet3->setCellValue('A' . $rowIndex3, $integ_mov['doc_encVenta'] ?? $row['doc_encVenta']);
            $sheet3->setCellValue('B' . $rowIndex3, $row['tipo_documento'] ?? '');
            $sheet3->setCellValue('C' . $rowIndex3, $row['fecha_expedicion'] ?? '');
            $sheet3->setCellValue('D' . $rowIndex3, $row['departamento_nombre'] ?? '');
            $sheet3->setCellValue('E' . $rowIndex3, $row['ciudad_nombre'] ?? '');
            $sheet3->setCellValue('F' . $rowIndex3, $row['nom_encVenta'] ?? '');
            $sheet3->setCellValue('G' . $rowIndex3, $row['dir_encVenta'] ?? '');
            $sheet3->setCellValue('H' . $rowIndex3, $row['zona_encVenta'] ?? '');
            $sheet3->setCellValue('I' . $rowIndex3, $row['comuna_nombre'] ?? '');
            $sheet3->setCellValue('J' . $rowIndex3, $row['barrio_nombre'] ?? '');
            $sheet3->setCellValue('K' . $rowIndex3, $row['otro_bar_ver_encVenta'] ?? '');
            $sheet3->setCellValue('L' . $rowIndex3, $row['tipo_movimiento'] ?? '');
            $sheet3->setCellValue('M' . $rowIndex3, $integ_mov['cant_integMovIndep'] ?? $row['integra_encVenta']);
            $sheet3->setCellValue('N' . $rowIndex3, $row['num_ficha_encVenta'] ?? '');
            $sheet3->setCellValue('O' . $rowIndex3, $row['sisben_nocturno'] ?? '');
            $sheet3->setCellValue('P' . $rowIndex3, $row['observacion'] ?? '');
            
            // Datos de caracterización del primer integrante
            $sheet3->setCellValue('Q' . $rowIndex3, $integ_mov['gen_integMovIndep'] ?? '');
            $sheet3->setCellValue('R' . $rowIndex3, $integ_mov['rango_integMovIndep'] ?? '');
            $sheet3->setCellValue('S' . $rowIndex3, $integ_mov['victima'] ?? '');
            $sheet3->setCellValue('T' . $rowIndex3, $integ_mov['condicionDiscapacidad'] ?? '');
            // Aplicar limpieza a tipoDiscapacidad
            $tipoDiscapacidadLimpio = isset($integ_mov['tipoDiscapacidad']) ? limpiarTexto($integ_mov['tipoDiscapacidad']) : '';
            $sheet3->setCellValue('U' . $rowIndex3, $tipoDiscapacidadLimpio);
            $sheet3->setCellValue('V' . $rowIndex3, $integ_mov['mujerGestante'] ?? '');
            $sheet3->setCellValue('W' . $rowIndex3, $integ_mov['cabezaFamilia'] ?? '');
            $sheet3->setCellValue('X' . $rowIndex3, $integ_mov['orientacionSexual'] ?? '');
            $sheet3->setCellValue('Y' . $rowIndex3, $integ_mov['experienciaMigratoria'] ?? '');
            $sheet3->setCellValue('Z' . $rowIndex3, $integ_mov['grupoEtnico'] ?? '');
            $sheet3->setCellValue('AA' . $rowIndex3, $integ_mov['seguridadSalud'] ?? '');
            $sheet3->setCellValue('AB' . $rowIndex3, $integ_mov['nivelEducativo'] ?? '');
            $sheet3->setCellValue('AC' . $rowIndex3, $integ_mov['condicionOcupacion'] ?? '');
        } else {
            // Si no hay datos en integmovimientos_independiente, usar datos del movimiento base
            $sheet3->setCellValue('A' . $rowIndex3, $row['doc_encVenta'] ?? '');
            $sheet3->setCellValue('B' . $rowIndex3, $row['tipo_documento'] ?? '');
            $sheet3->setCellValue('C' . $rowIndex3, $row['fecha_expedicion'] ?? '');
            $sheet3->setCellValue('D' . $rowIndex3, $row['departamento_nombre'] ?? '');
            $sheet3->setCellValue('E' . $rowIndex3, $row['ciudad_nombre'] ?? '');
            $sheet3->setCellValue('F' . $rowIndex3, $row['nom_encVenta'] ?? '');
            $sheet3->setCellValue('G' . $rowIndex3, $row['dir_encVenta'] ?? '');
            $sheet3->setCellValue('H' . $rowIndex3, $row['zona_encVenta'] ?? '');
            $sheet3->setCellValue('I' . $rowIndex3, $row['comuna_nombre'] ?? '');
            $sheet3->setCellValue('J' . $rowIndex3, $row['barrio_nombre'] ?? '');
            $sheet3->setCellValue('K' . $rowIndex3, $row['otro_bar_ver_encVenta'] ?? '');
            $sheet3->setCellValue('L' . $rowIndex3, $row['tipo_movimiento'] ?? '');
            $sheet3->setCellValue('M' . $rowIndex3, $row['integra_encVenta'] ?? '');
            $sheet3->setCellValue('N' . $rowIndex3, $row['num_ficha_encVenta'] ?? '');
            $sheet3->setCellValue('O' . $rowIndex3, $row['sisben_nocturno'] ?? '');
            $sheet3->setCellValue('P' . $rowIndex3, $row['observacion'] ?? '');
            
            // Dejar vacías las columnas de caracterización si no hay datos
            $sheet3->setCellValue('Q' . $rowIndex3, '');
            $sheet3->setCellValue('R' . $rowIndex3, '');
            $sheet3->setCellValue('S' . $rowIndex3, '');
            $sheet3->setCellValue('T' . $rowIndex3, '');
            $sheet3->setCellValue('U' . $rowIndex3, '');
            $sheet3->setCellValue('V' . $rowIndex3, '');
            $sheet3->setCellValue('W' . $rowIndex3, '');
            $sheet3->setCellValue('X' . $rowIndex3, '');
            $sheet3->setCellValue('Y' . $rowIndex3, '');
            $sheet3->setCellValue('Z' . $rowIndex3, '');
            $sheet3->setCellValue('AA' . $rowIndex3, '');
            $sheet3->setCellValue('AB' . $rowIndex3, '');
            $sheet3->setCellValue('AC' . $rowIndex3, '');
        }
        
        // ASESOR siempre al final
        $sheet3->setCellValue('AD' . $rowIndex3, $row['nombre_usuario'] ?? '');
        $rowIndex3++;
    }logError("Datos de movimientos escritos en la hoja 3");

    // Nombre del archivo
    $nombreArchivo = 'Reporte_Completo_Sisben_' . $fecha_inicio . '_' . $fecha_fin . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    logError("Archivo Excel generado: {$nombreArchivo}");    // Headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
    header('Cache-Control: max-age=0');

    // Enviar archivo
    $writer->save('php://output');
    logError("Archivo Excel enviado para descarga");
    
} catch (Exception $e) {
    logError("Excepción capturada: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    
    // Mostrar error detallado para debugging
    echo "<h1>Error en el Exportador</h1>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . " (línea " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // También mostrar el log
    $debug_log = __DIR__ . '/debug.log';
    if (file_exists($debug_log)) {
        echo "<h3>Log de Debug:</h3>";
        echo "<pre>" . htmlspecialchars(file_get_contents($debug_log)) . "</pre>";
    }
    
    exit;
}
?>
