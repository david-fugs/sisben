<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usu'])) {
    echo json_encode(['status' => 'no_auth']);
    exit;
}

include('../../conexion.php');
$mysqli->set_charset('utf8');

$documento = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $documento = isset($_GET['documento']) ? mysqli_real_escape_string($mysqli, $_GET['documento']) : '';
} else {
    $documento = isset($_POST['documento']) ? mysqli_real_escape_string($mysqli, $_POST['documento']) : '';
}

if (empty($documento)) {
    echo json_encode(['status' => 'empty']);
    exit;
}

// Buscar la última encuesta con ese documento
$sql = "SELECT * FROM encuestacampo WHERE doc_encVenta = '$documento' ORDER BY fecha_alta_encVenta DESC LIMIT 1";
$res = mysqli_query($mysqli, $sql);
if (!$res) {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($mysqli)]);
    exit;
}

$row = mysqli_fetch_assoc($res);
if (!$row) {
    echo json_encode(['status' => 'not_found']);
    exit;
}

// Buscar todos los integrantes asociados (no solo el primero)
$integrantes = [];
$sql_integ = "SELECT * FROM integcampo WHERE id_encuesta = " . intval($row['id_encCampo']) . " ORDER BY id_integCampo ASC";
$res_integ = mysqli_query($mysqli, $sql_integ);
if ($res_integ) {
    while ($integ = mysqli_fetch_assoc($res_integ)) {
        $integrantes[] = $integ;
    }
}

// Preparar datos de salida mapeando los campos que se usan en el formulario
// Intent: además de devolver los códigos, intentamos devolver los nombres para
// que el frontend pueda setear selects aunque no exista la option (id)
$data = [
    'id_encCampo' => $row['id_encCampo'],
    'doc_encVenta' => $row['doc_encVenta'],
    'fec_reg_encVenta' => $row['fec_reg_encVenta'],
    'tipo_documento' => $row['tipo_documento'],
    'departamento_expedicion' => $row['departamento_expedicion'],
    'ciudad_expedicion' => $row['ciudad_expedicion'],
    'fecha_expedicion' => $row['fecha_expedicion'],
    'nom_encVenta' => $row['nom_encVenta'],
    'fecha_nacimiento' => $row['fecha_nacimiento'],
    'dir_encVenta' => $row['dir_encVenta'],
    'id_bar' => $row['id_bar'],
    'id_com' => $row['id_com'],
    'otro_bar_ver_encVenta' => $row['otro_bar_ver_encVenta'],
    'zona_encVenta' => $row['zona_encVenta'],
    'tram_solic_encVenta' => $row['tram_solic_encVenta'],
    'num_ficha_encVenta' => $row['num_ficha_encVenta'],
    'num_visita' => $row['num_visita'],
    'estado_ficha' => $row['estado_ficha'],
    'tipo_proceso' => $row['tipo_proceso'],
    'integra_encVenta' => $row['integra_encVenta'],
    'sisben_nocturno' => $row['sisben_nocturno'],
    'obs_encVenta' => $row['obs_encVenta'],
    'fecha_alta_encVenta' => $row['fecha_alta_encVenta'],
    'id_usu' => $row['id_usu']
];

// Intent: resolver nombres según los valores almacenados
// 1) resolver nombre de municipio si ciudad_expedicion es un código
if (!empty($row['ciudad_expedicion'])) {
    $codigo = $mysqli->real_escape_string($row['ciudad_expedicion']);
    $q = "SELECT nombre_municipio FROM municipios WHERE cod_municipio = '$codigo' LIMIT 1";
    $r = mysqli_query($mysqli, $q);
    if ($r && ($mr = mysqli_fetch_assoc($r))) {
        $data['ciudad_expedicion_nombre'] = $mr['nombre_municipio'];
    } else {
        // si no encontramos por código, assumimos que el campo ya contiene el nombre
        $data['ciudad_expedicion_nombre'] = $row['ciudad_expedicion'];
    }
} else {
    $data['ciudad_expedicion_nombre'] = '';
}

// 2) resolver barrio por id_bar (nombre)
if (!empty($row['id_bar'])) {
    $idbar = intval($row['id_bar']);
    $q = "SELECT nombre_bar FROM barrios WHERE id_bar = $idbar LIMIT 1";
    $r = mysqli_query($mysqli, $q);
    if ($r && ($br = mysqli_fetch_assoc($r))) {
        $data['id_bar_nombre'] = $br['nombre_bar'];
    } else {
        $data['id_bar_nombre'] = '';
    }
} else {
    $data['id_bar_nombre'] = '';
}

// 3) resolver comuna/corregimiento por id_com (nombre)
if (!empty($row['id_com'])) {
    $idcom = intval($row['id_com']);
    $q = "SELECT nombre_com FROM comunas WHERE id_com = $idcom LIMIT 1";
    $r = mysqli_query($mysqli, $q);
    if ($r && ($cr = mysqli_fetch_assoc($r))) {
        $data['id_com_nombre'] = $cr['nombre_com'];
    } else {
        $data['id_com_nombre'] = '';
    }
} else {
    $data['id_com_nombre'] = '';
}

if (!empty($integrantes)) {
    $data['integrantes'] = $integrantes;
    // Mantener compatibilidad con el primer integrante
    $data['integ'] = $integrantes[0];
}

echo json_encode(['status' => 'found', 'data' => $data]);
exit;
