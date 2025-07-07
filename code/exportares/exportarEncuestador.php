<?php
// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_debug.log');

// Función para logging personalizado
function logError($message)
{
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents(__DIR__ . '/debug.log', $logMessage, FILE_APPEND | LOCK_EX);
}

// Función para normalizar texto UTF-8
function normalizeUtf8($text)
{
    if (empty($text)) {
        return '';
    }

    // Asegurar que el texto esté en UTF-8
    if (!mb_check_encoding($text, 'UTF-8')) {
        $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
    }

    // Corregir el problema específico de "RetiroÂ personas"
    $text = str_replace('Â', '', $text);

    // Limpiar caracteres de control
    $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);

    // Limpiar espacios múltiples
    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
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
    $mysqli->set_charset('utf8mb4');

    // Crear una nueva instancia de Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Configurar propiedades del documento para UTF-8
    $spreadsheet->getProperties()
        ->setCreator("Sistema SISBEN")
        ->setTitle("Reporte Completo SISBEN")
        ->setSubject("Reporte de Encuestas, Información y Movimientos")
        ->setDescription("Reporte generado automáticamente");

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

    // Consulta para encuestas con tipo_movimiento desde la tabla movimientos
    $sql_encuestas = "
    SELECT ev.*, b.nombre_bar AS barrio_nombre, d.nombre_departamento AS departamento_nombre, 
           c.nombre_com AS comuna_nombre, m.nombre_municipio as ciudad_nombre,
           COALESCE(mov.tipo_movimiento, ev.tram_solic_encVenta) as tipo_movimiento_final
    FROM encventanilla ev
    LEFT JOIN barrios b ON ev.id_bar = b.id_bar
    LEFT JOIN departamentos d ON ev.departamento_expedicion = d.cod_departamento
    LEFT JOIN comunas c ON ev.id_com = c.id_com
    LEFT JOIN municipios m ON ev.ciudad_expedicion = m.cod_municipio
    LEFT JOIN movimientos mov ON ev.doc_encVenta = mov.doc_encVenta
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
    $sheet1->setCellValue('M1', 'TIPO MOVIMIENTO');
    $sheet1->setCellValue('N1', 'CANTIDAD INTEGRANTES');
    $sheet1->setCellValue('O1', 'NUMERO FICHA');
    $sheet1->setCellValue('P1', 'SISBEN NOCTURNO');
    $sheet1->setCellValue('Q1', 'OBSERVACIONES');

    // Si el filtro es TODOS, agregamos encabezados de primer integrante (ajustados)
    $isTodos = (!isset($_GET['id_usu']) || $_GET['id_usu'] == '' || $_GET['id_usu'] == 'todos');
    if ($isTodos) {
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
        // Forzar ancho de columnas de los campos de integrante
        foreach (['R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF'] as $col) {
            $sheet1->getColumnDimension($col)->setWidth(30);
        }
    } else {
        $sheet1->setCellValue('R1', 'ASESOR');
    }

    // Ajustar ancho de columnas para ENCUESTAS
    foreach (
        [
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            'AA',
            'AB',
            'AC',
            'AD'
        ] as $col
    ) {
        $sheet1->getColumnDimension($col)->setWidth(20);
    }
    // Ajustar ancho específico para ASESOR
    if ($isTodos) {
        foreach (['R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF'] as $col) {
            $sheet1->getColumnDimension($col)->setWidth(30);
        }
    }
    $sheet1->getDefaultRowDimension()->setRowHeight(25);

    // --- FUNCION PARA LIMPIAR TEXTO DE TIPO DISCAPACIDAD ---
    if (!function_exists('limpiarTexto')) {
        function limpiarTexto($texto)
        {
            if (empty($texto)) {
                return '';
            }

            // Normalizar UTF-8 primero
            $texto = normalizeUtf8($texto);

            // Reemplaza casos conocidos problemáticos
            $texto = str_replace(['FÃ­sica', 'Física', 'FISICA', 'FÍSICA'], 'Fisica', $texto);
            $texto = str_replace(['Â', 'Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã±'], ['', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], $texto);

            // Mantener tildes y ñ, solo eliminar caracteres especiales problemáticos
            $texto = preg_replace('/[^\p{L}\p{N}\s]/u', '', $texto);

            // Limpiar espacios múltiples
            $texto = preg_replace('/\s+/', ' ', $texto);

            return trim($texto);
        }
    }

    // Escribir datos de ENCUESTAS
    $rowIndex1 = 2;
    while ($row = mysqli_fetch_array($res_encuestas, MYSQLI_ASSOC)) {
        $sheet1->setCellValue('A' . $rowIndex1, $row['fecha_alta_encVenta']);
        $sheet1->setCellValue('B' . $rowIndex1, $row['doc_encVenta']);
        $sheet1->setCellValue('C' . $rowIndex1, $row['tipo_documento']);
        $sheet1->setCellValue('D' . $rowIndex1, $row['fecha_expedicion']);
        $sheet1->setCellValue('E' . $rowIndex1, normalizeUtf8($row['departamento_nombre']));
        $sheet1->setCellValue('F' . $rowIndex1, normalizeUtf8($row['ciudad_nombre']));
        $sheet1->setCellValue('G' . $rowIndex1, normalizeUtf8($row['nom_encVenta']));
        $sheet1->setCellValue('H' . $rowIndex1, normalizeUtf8($row['dir_encVenta']));
        $sheet1->setCellValue('I' . $rowIndex1, normalizeUtf8($row['zona_encVenta']));
        $sheet1->setCellValue('J' . $rowIndex1, normalizeUtf8($row['comuna_nombre']));
        $sheet1->setCellValue('K' . $rowIndex1, normalizeUtf8($row['barrio_nombre']));
        $sheet1->setCellValue('L' . $rowIndex1, normalizeUtf8($row['otro_bar_ver_encVenta']));
        $sheet1->setCellValue('M' . $rowIndex1, normalizeUtf8($row['tipo_movimiento_final']));
        $sheet1->setCellValue('N' . $rowIndex1, $row['integra_encVenta']);
        $sheet1->setCellValue('O' . $rowIndex1, $row['num_ficha_encVenta']);
        $sheet1->setCellValue('P' . $rowIndex1, $row['sisben_nocturno']);
        $sheet1->setCellValue('Q' . $rowIndex1, normalizeUtf8($row['obs_encVenta']));

        // Si el filtro es TODOS, buscar y agregar el primer integrante (ajustado)
        if ($isTodos) {
            $id_encVenta = $row['id_encVenta'];
            $sql_integrante = "SELECT * FROM integventanilla WHERE id_encVenta = '$id_encVenta' ORDER BY id_integVenta ASC LIMIT 1";
            $res_integrante = mysqli_query($mysqli, $sql_integrante);
            $integ = mysqli_fetch_assoc($res_integrante);
            // Mapeo de rango edad
            $rangoEdadMap = [
                1 => '0 - 6',
                2 => '7 - 12',
                3 => '13 - 17',
                4 => '18 - 28',
                5 => '29 - 45',
                6 => '46 - 64',
                7 => 'Mayor o igual a 65',
            ];
            if ($integ) {
                $sheet1->setCellValue('R' . $rowIndex1, normalizeUtf8($integ['gen_integVenta'] ?? ''));
                $sheet1->setCellValue('S' . $rowIndex1, isset($rangoEdadMap[$integ['rango_integVenta'] ?? null]) ? $rangoEdadMap[$integ['rango_integVenta']] : '');
                $sheet1->setCellValue('T' . $rowIndex1, normalizeUtf8($integ['victima'] ?? ''));
                $sheet1->setCellValue('U' . $rowIndex1, normalizeUtf8($integ['condicionDiscapacidad'] ?? ''));
                // Aquí aplicamos la limpieza a tipoDiscapacidad
                $tipoDiscapacidadLimpio = isset($integ['tipoDiscapacidad']) ? limpiarTexto($integ['tipoDiscapacidad']) : '';
                $sheet1->setCellValue('V' . $rowIndex1, $tipoDiscapacidadLimpio);
                $sheet1->setCellValue('W' . $rowIndex1, normalizeUtf8($integ['mujerGestante'] ?? ''));
                $sheet1->setCellValue('X' . $rowIndex1, normalizeUtf8($integ['cabezaFamilia'] ?? ''));
                $sheet1->setCellValue('Y' . $rowIndex1, normalizeUtf8($integ['orientacionSexual'] ?? ''));
                $sheet1->setCellValue('Z' . $rowIndex1, normalizeUtf8($integ['experienciaMigratoria'] ?? ''));
                $sheet1->setCellValue('AA' . $rowIndex1, normalizeUtf8($integ['grupoEtnico'] ?? ''));
                $sheet1->setCellValue('AB' . $rowIndex1, normalizeUtf8($integ['seguridadSalud'] ?? ''));
                $sheet1->setCellValue('AC' . $rowIndex1, normalizeUtf8($integ['nivelEducativo'] ?? ''));
                $sheet1->setCellValue('AD' . $rowIndex1, normalizeUtf8($integ['condicionOcupacion'] ?? ''));
                $sheet1->setCellValue('AE' . $rowIndex1, normalizeUtf8($integ['tipo_solic_encInfo'] ?? ''));
                $sheet1->setCellValue('AF' . $rowIndex1, normalizeUtf8($integ['observacion'] ?? ''));
            }
            // ASESOR siempre al final cuando es TODOS
            $sheet1->setCellValue('AE' . $rowIndex1, $row['nombre_usuario'] ?? '');
        } else {
            // Si no es TODOS, ASESOR va en columna R
            $sheet1->setCellValue('R' . $rowIndex1, $row['nombre_usuario'] ?? '');
        }
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
    }
    logError("Consulta de información ejecutada correctamente");

    // Aplicar estilos a la hoja 2 - CORREGIR RANGO HASTA X1
    $sheet2->getStyle('A1:X1')->applyFromArray($styleHeader);

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
    $sheet2->setCellValue('X1', 'ASESOR');

    // Ajustar ancho de columnas para INFORMACIÓN
    foreach (range('A', 'W') as $col) {
        $sheet2->getColumnDimension($col)->setWidth(20);
    }
    // Ajustar ancho específico para ASESOR
    $sheet2->getColumnDimension('X')->setWidth(25);
    $sheet2->getDefaultRowDimension()->setRowHeight(25);

    // Escribir datos de INFORMACIÓN
    $rowIndex2 = 2;
    while ($row = mysqli_fetch_array($res_informacion, MYSQLI_ASSOC)) {
        $sheet2->setCellValue('A' . $rowIndex2, $row['fecha_alta_info']);
        $sheet2->setCellValue('B' . $rowIndex2, $row['doc_info']);
        $sheet2->setCellValue('C' . $rowIndex2, normalizeUtf8($row['nom_info']));
        $sheet2->setCellValue('D' . $rowIndex2, normalizeUtf8($row['gen_integVenta']));
        $sheet2->setCellValue('E' . $rowIndex2, $row['tipo_documento']);
        $sheet2->setCellValue('F' . $rowIndex2, normalizeUtf8($row['departamento_nombre']));
        $sheet2->setCellValue('G' . $rowIndex2, normalizeUtf8($row['ciudad_nombre']));
        $sheet2->setCellValue('H' . $rowIndex2, $row['fecha_expedicion']);
        $sheet2->setCellValue('I' . $rowIndex2, $row['rango_integVenta']);
        $sheet2->setCellValue('J' . $rowIndex2, normalizeUtf8($row['victima']));
        $sheet2->setCellValue('K' . $rowIndex2, normalizeUtf8($row['condicionDiscapacidad']));
        $sheet2->setCellValue('L' . $rowIndex2, normalizeUtf8($row['tipoDiscapacidad']));
        $sheet2->setCellValue('M' . $rowIndex2, normalizeUtf8($row['mujerGestante']));
        $sheet2->setCellValue('N' . $rowIndex2, normalizeUtf8($row['cabezaFamilia']));
        $sheet2->setCellValue('O' . $rowIndex2, normalizeUtf8($row['orientacionSexual']));
        $sheet2->setCellValue('P' . $rowIndex2, normalizeUtf8($row['experienciaMigratoria']));
        $sheet2->setCellValue('Q' . $rowIndex2, normalizeUtf8($row['grupoEtnico']));
        $sheet2->setCellValue('R' . $rowIndex2, normalizeUtf8($row['seguridadSalud']));
        $sheet2->setCellValue('S' . $rowIndex2, normalizeUtf8($row['nivelEducativo']));
        $sheet2->setCellValue('T' . $rowIndex2, normalizeUtf8($row['condicionOcupacion']));
        $sheet2->setCellValue('U' . $rowIndex2, normalizeUtf8($row['tipo_solic_encInfo']));
        $sheet2->setCellValue('V' . $rowIndex2, normalizeUtf8($row['observacion']));
        $sheet2->setCellValue('W' . $rowIndex2, normalizeUtf8($row['info_adicional']));
        $sheet2->setCellValue('X' . $rowIndex2, $row['nombre_usuario'] ?? '');
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
    }
    if (isset($_GET['id_usu']) && $_GET['id_usu'] != '' && $_GET['id_usu'] != 'todos') {
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
    logError("Consulta de movimientos ejecutada correctamente");    // Aplicar estilos a la hoja 3 - MOVIMIENTOS SIMPLIFICADA
    $sheet3->getStyle('A1:S1')->applyFromArray($styleHeader);

    // Encabezados para MOVIMIENTOS (sin ID, sin TRAMITE SOLICITADO y campos eliminados)
    $sheet3->setCellValue('A1', 'FECHA MOVIMIENTO');
    $sheet3->setCellValue('B1', 'DOCUMENTO ENCUESTA');
    $sheet3->setCellValue('C1', 'NOMBRE PERSONA');
    $sheet3->setCellValue('D1', 'TIPO DOCUMENTO');
    $sheet3->setCellValue('E1', 'DEPARTAMENTO EXPEDICION');
    $sheet3->setCellValue('F1', 'CIUDAD EXPEDICION');
    $sheet3->setCellValue('G1', 'FECHA EXPEDICION');
    $sheet3->setCellValue('H1', 'DIRECCION');
    $sheet3->setCellValue('I1', 'ZONA');
    $sheet3->setCellValue('J1', 'COMUNA');
    $sheet3->setCellValue('K1', 'BARRIO');
    $sheet3->setCellValue('L1', 'OTRO BARRIO');
    $sheet3->setCellValue('M1', 'CANTIDAD INTEGRANTES');
    $sheet3->setCellValue('N1', 'NUMERO FICHA');
    $sheet3->setCellValue('O1', 'SISBEN NOCTURNO');
    $sheet3->setCellValue('P1', 'TIPO MOVIMIENTO');
    $sheet3->setCellValue('Q1', 'FECHA ALTA');
    $sheet3->setCellValue('R1', 'OBSERVACION');
    $sheet3->setCellValue('S1', 'ASESOR'); // Ajustar ancho de columnas para MOVIMIENTOS SIMPLIFICADA
    $sheet3->getColumnDimension('A')->setWidth(20); // FECHA MOVIMIENTO
    $sheet3->getColumnDimension('B')->setWidth(20); // DOCUMENTO
    $sheet3->getColumnDimension('C')->setWidth(35); // NOMBRE
    $sheet3->getColumnDimension('D')->setWidth(20); // TIPO DOCUMENTO
    $sheet3->getColumnDimension('E')->setWidth(25); // DEPARTAMENTO
    $sheet3->getColumnDimension('F')->setWidth(25); // CIUDAD
    $sheet3->getColumnDimension('G')->setWidth(20); // FECHA EXPEDICION
    $sheet3->getColumnDimension('H')->setWidth(35); // DIRECCION
    $sheet3->getColumnDimension('I')->setWidth(15); // ZONA
    $sheet3->getColumnDimension('J')->setWidth(20); // COMUNA
    $sheet3->getColumnDimension('K')->setWidth(25); // BARRIO
    $sheet3->getColumnDimension('L')->setWidth(25); // OTRO BARRIO
    $sheet3->getColumnDimension('M')->setWidth(15); // CANTIDAD
    $sheet3->getColumnDimension('N')->setWidth(15); // NUMERO FICHA
    $sheet3->getColumnDimension('O')->setWidth(15); // SISBEN NOCTURNO    $sheet3->getColumnDimension('P')->setWidth(40); // TIPO MOVIMIENTO - MÁS ANCHO
    $sheet3->getColumnDimension('R')->setWidth(25); //TIPO MOVI
    $sheet3->getColumnDimension('Q')->setWidth(20); // FECHA ALTA
    $sheet3->getColumnDimension('R')->setWidth(40); // OBSERVACION
    $sheet3->getColumnDimension('S')->setWidth(25); // ASESOR
    $sheet3->getDefaultRowDimension()->setRowHeight(25); // Escribir datos de MOVIMIENTOS simplificados
    $rowIndex3 = 2;
    while ($row = mysqli_fetch_array($res_movimientos, MYSQLI_ASSOC)) {
        $sheet3->setCellValue('A' . $rowIndex3, $row['fecha_movimiento'] ?? '');
        $sheet3->setCellValue('B' . $rowIndex3, $row['doc_encVenta'] ?? '');
        $sheet3->setCellValue('C' . $rowIndex3, normalizeUtf8($row['nom_encVenta'] ?? ''));
        $sheet3->setCellValue('D' . $rowIndex3, $row['tipo_documento'] ?? '');
        $sheet3->setCellValue('E' . $rowIndex3, normalizeUtf8($row['departamento_nombre'] ?? ''));
        $sheet3->setCellValue('F' . $rowIndex3, normalizeUtf8($row['ciudad_nombre'] ?? ''));
        $sheet3->setCellValue('G' . $rowIndex3, $row['fecha_expedicion'] ?? '');
        $sheet3->setCellValue('H' . $rowIndex3, normalizeUtf8($row['dir_encVenta'] ?? ''));
        $sheet3->setCellValue('I' . $rowIndex3, normalizeUtf8($row['zona_encVenta'] ?? ''));
        $sheet3->setCellValue('J' . $rowIndex3, normalizeUtf8($row['comuna_nombre'] ?? ''));
        $sheet3->setCellValue('K' . $rowIndex3, normalizeUtf8($row['barrio_nombre'] ?? ''));
        $sheet3->setCellValue('L' . $rowIndex3, normalizeUtf8($row['otro_bar_ver_encVenta'] ?? ''));
        $sheet3->setCellValue('M' . $rowIndex3, $row['integra_encVenta'] ?? '');
        $sheet3->setCellValue('N' . $rowIndex3, $row['num_ficha_encVenta'] ?? '');
        $sheet3->setCellValue('O' . $rowIndex3, $row['sisben_nocturno'] ?? '');
        $sheet3->setCellValue('P' . $rowIndex3, normalizeUtf8($row['tipo_movimiento'] ?? ''));
        $sheet3->setCellValue('Q' . $rowIndex3, $row['fecha_alta_movimiento'] ?? '');
        $sheet3->setCellValue('R' . $rowIndex3, normalizeUtf8($row['observacion'] ?? ''));
        $sheet3->setCellValue('S' . $rowIndex3, normalizeUtf8($row['nombre_usuario'] ?? ''));
        $rowIndex3++;
    }
    logError("Datos de movimientos escritos en la hoja 3");

    // Nombre del archivo
    $nombreArchivo = 'Reporte_Completo_Sisben_' . $fecha_inicio . '_' . $fecha_fin . '.xlsx';

    // Crear writer con configuración UTF-8
    $writer = new Xlsx($spreadsheet);

    // Configurar el writer para mantener UTF-8
    $writer->setPreCalculateFormulas(false);

    logError("Archivo Excel generado: {$nombreArchivo}");

    // Headers para descarga con codificación UTF-8
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
    header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');
    header('Expires: 0');

    // Limpiar buffer de salida para evitar corrupción
    if (ob_get_level()) {
        ob_end_clean();
    }

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
