<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu = $_SESSION['id_usu'];
$usuario = $_SESSION['usuario'];
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");

include('../../conexion.php');

// Establecer charset UTF-8 para manejar tildes y ñ
mysqli_set_charset($mysqli, "utf8");

// Obtener ID de la encuesta a editar
$id_encuesta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_encuesta == 0) {
    echo "<script>alert('ID de encuesta inválido'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

// Obtener datos de la encuesta
$query_encuesta = "SELECT * FROM encuestacampo WHERE id_encCampo = $id_encuesta";
if ($tipo_usu != '1') {
    $query_encuesta .= " AND id_usu = $id_usu";
}

$result_encuesta = mysqli_query($mysqli, $query_encuesta);

// Validar que la consulta fue exitosa
if (!$result_encuesta) {
    echo "<script>alert('Error en la consulta: " . mysqli_error($mysqli) . "'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

$encuesta = mysqli_fetch_assoc($result_encuesta);

if (!$encuesta) {
    echo "<script>alert('Encuesta no encontrada o sin permisos'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

// Obtener integrantes de la encuesta (usar id_encuesta como FK)
$query_integrantes = "SELECT * FROM integcampo WHERE documento = " . $encuesta['doc_encVenta'] . " ORDER BY id_integCampo";
$result_integrantes = mysqli_query($mysqli, $query_integrantes);

// Validar que la consulta fue exitosa
if (!$result_integrantes) {
    echo "<script>alert('Error al obtener integrantes: " . mysqli_error($mysqli) . "'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

$integrantes = [];
while ($row = mysqli_fetch_assoc($result_integrantes)) {
    $integrantes[] = $row;
}

// Obtener departamentos
$query_departamentos = "SELECT cod_departamento, nombre_departamento FROM departamentos ORDER BY nombre_departamento";
$result_departamentos = mysqli_query($mysqli, $query_departamentos);

// Validar que la consulta fue exitosa
if (!$result_departamentos) {
    echo "<script>alert('Error al obtener departamentos: " . mysqli_error($mysqli) . "'); window.location.href = 'showsurvey.php';</script>";
    exit();
}

$departamentos = [];
while ($row = mysqli_fetch_assoc($result_departamentos)) {
    $departamentos[] = $row;
}

// Formatear fechas para inputs type=date (YYYY-MM-DD)
$fec_reg_encVenta = '';
$fecha_expedicion_val = '';
$fecha_nacimiento_val = '';
if (!empty($encuesta)) {
    // fec_reg_encVenta: prefer first 10 chars if already YYYY-MM-DD or datetime; else try strtotime
    if (!empty($encuesta['fec_reg_encVenta'])) {
        $raw = $encuesta['fec_reg_encVenta'];
        if (is_string($raw) && strlen($raw) >= 10 && preg_match('/\d{4}-\d{2}-\d{2}/', substr($raw,0,10))) {
            $fec_reg_encVenta = substr($raw,0,10);
        } else {
            $ts = strtotime($raw);
            if ($ts !== false) $fec_reg_encVenta = date('Y-m-d', $ts);
            else $fec_reg_encVenta = '';
        }
    }

    // fecha_expedicion
    if (!empty($encuesta['fecha_expedicion'])) {
        $raw2 = $encuesta['fecha_expedicion'];
        if (is_string($raw2) && strlen($raw2) >= 10 && preg_match('/\d{4}-\d{2}-\d{2}/', substr($raw2,0,10))) {
            $fecha_expedicion_val = substr($raw2,0,10);
        } else {
            $ts2 = strtotime($raw2);
            if ($ts2 !== false) $fecha_expedicion_val = date('Y-m-d', $ts2);
            else $fecha_expedicion_val = '';
        }
    }

    // fecha_nacimiento
    if (!empty($encuesta['fecha_nacimiento'])) {
        $raw3 = $encuesta['fecha_nacimiento'];
        if (is_string($raw3) && strlen($raw3) >= 10 && preg_match('/\d{4}-\d{2}-\d{2}/', substr($raw3,0,10))) {
            $fecha_nacimiento_val = substr($raw3,0,10);
        } else {
            $ts3 = strtotime($raw3);
            if ($ts3 !== false) $fecha_nacimiento_val = date('Y-m-d', $ts3);
            else $fecha_nacimiento_val = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BD SISBEN - Editar Encuesta Campo</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../barrios.js"></script>
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px 0;
        }

        .section-header {
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .section-title {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .smaller-input {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .formulario-dinamico {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            position: relative;
        }

        .eliminar-integrante {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            padding: 0;
            font-size: 16px;
        }

        .back-button {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 10px;
        }

        .back-button:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>

<body style="background-color: #f8f9fa;">
    <div class="container-fluid">
        <div class="form-container">
            <div class="section-header">
                <h2><i class="fas fa-edit me-2"></i>EDITAR ENCUESTA DE CAMPO</h2>
                <p class="mb-0">Sistema SISBEN - Actualización de datos</p>
            </div>

            <form action="updatesurvey.php" method="post" id="form_contacto" enctype="multipart/form-data">
                <input type="hidden" name="id_encuesta" value="<?php echo $id_encuesta; ?>">

                <!-- Sección Información Personal -->
                <div class="form-section">
                    <h5 class="section-title">Información Personal</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="doc_encVenta" class="form-label">Documento de Identidad *</label>
                            <input type="text" class="form-control" id="doc_encVenta" name="doc_encVenta" 
                                   value="<?php echo $encuesta['doc_encVenta']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fec_reg_encVenta" class="form-label">Fecha de Registro *</label>
                <input type="date" class="form-control" id="fec_reg_encVenta" name="fec_reg_encVenta" 
                    value="<?php echo $fec_reg_encVenta; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo_documento" class="form-label">Tipo de Documento *</label>
                            <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                <option value="">Seleccione</option>
                                <option value="cedula" <?php echo ($encuesta['tipo_documento'] == 'cedula') ? 'selected' : ''; ?>>CEDULA</option>
                                <option value="ppt" <?php echo ($encuesta['tipo_documento'] == 'ppt') ? 'selected' : ''; ?>>PPT</option>
                                <option value="cedula_extranjeria" <?php echo ($encuesta['tipo_documento'] == 'cedula_extranjeria') ? 'selected' : ''; ?>>CEDULA EXTRANJERIA</option>
                                <option value="otro" <?php echo ($encuesta['tipo_documento'] == 'otro') ? 'selected' : ''; ?>>OTRO</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="departamento_expedicion" class="form-label">Departamento Expedición *</label>
                            <select class="form-control" id="departamento_expedicion" name="departamento_expedicion" required>
                                <option value="">Seleccione departamento</option>
                                <?php foreach ($departamentos as $dept): ?>
                                    <option value="<?php echo $dept['cod_departamento']; ?>" 
                                            <?php echo ($encuesta['departamento_expedicion'] == $dept['cod_departamento']) ? 'selected' : ''; ?>>
                                        <?php echo $dept['nombre_departamento']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="ciudad_expedicion" class="form-label">Ciudad Expedición *</label>
                            <select class="form-control" id="ciudad_expedicion" name="ciudad_expedicion" required>
                                <option value="">Seleccione ciudad</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fecha_expedicion" class="form-label">Fecha de Expedición *</label>
                <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" 
                    value="<?php echo $fecha_expedicion_val; ?>" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" 
                    value="<?php echo $fecha_nacimiento_val; ?>" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="edad_calculada" class="form-label">Edad</label>
                            <input type="text" class="form-control" id="edad_calculada" readonly style="background-color: #e9ecef; font-weight: bold;" placeholder="Calculada">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nom_encVenta" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nom_encVenta" name="nom_encVenta" 
                                   value="<?php echo $encuesta['nom_encVenta']; ?>" required style="text-transform:uppercase;">
                        </div>
                    </div>
                </div>

                <!-- Sección Fotografía del Encuestado -->
                <div class="form-section">
                    <h5 class="section-title">Fotografía del Encuestado</h5>
                    <?php
                    $ruta_foto = isset($encuesta['foto_encuestado']) && !empty($encuesta['foto_encuestado']) ? '../../' . $encuesta['foto_encuestado'] : '';
                    $tiene_foto = !empty($ruta_foto) && file_exists($ruta_foto);
                    ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-camera"></i> Foto del Encuestado
                            </label>
                            <div class="mb-3">
                                <label for="foto_camara" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-camera"></i> Tomar Foto con Cámara
                                </label>
                                <input type="file" name="foto_encuestado" id="foto_camara" class="d-none" accept="image/*" capture="environment">
                            </div>
                            <div>
                                <label for="foto_galeria" class="btn btn-success btn-block">
                                    <i class="fas fa-images"></i> Seleccionar de Galería
                                </label>
                                <input type="file" id="foto_galeria" class="d-none" accept="image/*">
                            </div>
                            <small class="form-text text-muted mt-2">
                                Elija tomar una foto nueva o seleccionar una existente
                            </small>
                            <?php if ($tiene_foto): ?>
                                <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($encuesta['foto_encuestado']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vista Actual:</label>
                            <div id="preview_foto" style="border: 2px dashed #007bff; border-radius: 8px; min-height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; position: relative;">
                                <?php if ($tiene_foto): ?>
                                    <div style="text-align: center; width: 100%;">
                                        <img src="<?php echo $ruta_foto; ?>" style="max-width: 100%; max-height: 300px; border-radius: 8px;" alt="Foto encuestado" id="foto_actual_img">
                                        <div class="mt-2">
                                            <a href="<?php echo $ruta_foto; ?>" download class="btn btn-sm btn-info" title="Descargar foto">
                                                <i class="fas fa-download"></i> Descargar
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" id="eliminar_foto" title="Eliminar foto">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                            <input type="hidden" name="eliminar_foto_flag" id="eliminar_foto_flag" value="0">
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #6c757d;">
                                        <i class="fas fa-image fa-3x"></i><br>
                                        Sin foto registrada
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección Información de Ubicación -->
                <div class="form-section">
                    <h5 class="section-title">Información de Ubicación</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="dir_encVenta" class="form-label">Dirección *</label>
                            <input type="text" class="form-control" id="dir_encVenta" name="dir_encVenta" 
                                   value="<?php echo $encuesta['dir_encVenta']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="zona_encVenta" class="form-label">Zona *</label>
                            <select class="form-control" id="zona_encVenta" name="zona_encVenta" required>
                                <option value="">Seleccione</option>
                                <option value="URBANA" <?php echo ($encuesta['zona_encVenta'] == 'URBANA') ? 'selected' : ''; ?>>URBANA</option>
                                <option value="RURAL" <?php echo ($encuesta['zona_encVenta'] == 'RURAL') ? 'selected' : ''; ?>>RURAL</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="id_barrios" class="form-label">Barrio/Vereda *</label>
                            <select class="form-control" id="id_barrios" name="id_bar" required>
                                <option value="">Seleccione</option>
                                        <?php if (!empty($encuesta['id_bar'])): ?>
                                            <?php
                                            // Obtener el nombre y zona del barrio seleccionado (columnas según buscar_barrios.php)
                                            $query_barrio = "SELECT nombre_bar, zona_bar FROM barrios WHERE id_bar = " . intval($encuesta['id_bar']);
                                            $result_barrio = mysqli_query($mysqli, $query_barrio);
                                            if ($result_barrio && $row_barrio = mysqli_fetch_assoc($result_barrio)) {
                                                $barrio_nombre = htmlspecialchars($row_barrio['nombre_bar'], ENT_QUOTES, 'UTF-8');
                                                $barrio_zona = htmlspecialchars($row_barrio['zona_bar'] ?? '', ENT_QUOTES, 'UTF-8');
                                            ?>
                                                <option value="<?php echo intval($encuesta['id_bar']); ?>" selected data-zona="<?php echo $barrio_zona; ?>">
                                                    <?php echo $barrio_nombre; ?>
                                                </option>
                                            <?php } ?>
                                        <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_comunas" class="form-label">Comuna/Corregimiento</label>
                            <select class="form-control" id="id_comunas" name="id_com">
                                <option value="">Seleccione comuna</option>
                                <?php if (!empty($encuesta['id_com'])): ?>
                                    <option value="<?php echo $encuesta['id_com']; ?>" selected>
                                        <?php 
                                        // Obtener el nombre de la comuna seleccionada
                                        $query_comuna = "SELECT nombre_com FROM comunas WHERE id_com = " . $encuesta['id_com'];
                                        $result_comuna = mysqli_query($mysqli, $query_comuna);
                                        if ($result_comuna && $row_comuna = mysqli_fetch_assoc($result_comuna)) {
                                            echo $row_comuna['nombre'];
                                        }
                                        ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="otro_barrio_container" style="<?php echo ($encuesta['id_bar'] == '1897') ? 'display:block;' : 'display:none;'; ?>">
                            <label for="otro_bar_ver_encVenta" class="form-label">Especifique Barrio, Vereda o Invasión</label>
                            <input type="text" class="form-control" id="otro_bar_ver_encVenta" name="otro_bar_ver_encVenta" 
                                   value="<?php echo $encuesta['otro_bar_ver_encVenta']; ?>" placeholder="Ingrese el barrio">
                        </div>
                    </div>
                </div>

                <!-- Sección Trámite y Ficha -->
                <div class="form-section">
                    <h5 class="section-title">Trámite y Ficha</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="tram_solic_encVenta" class="form-label">Trámite Solicitado</label>
                            <select class="form-control" id="tram_solic_encVenta" name="tram_solic_encVenta">
                                <option value="">Seleccione</option>
                                <option value="ENCUESTA NUEVA" <?php echo ($encuesta['tram_solic_encVenta'] == 'ENCUESTA NUEVA') ? 'selected' : ''; ?>>ENCUESTA NUEVA</option>
                                <option value="ENCUESTA NUEVA POR VERIFICACION" <?php echo ($encuesta['tram_solic_encVenta'] == 'ENCUESTA NUEVA POR VERIFICACION') ? 'selected' : ''; ?>>ENCUESTA NUEVA POR VERIFICACION</option>
                                <option value="CAMBIO DIRECCION" <?php echo ($encuesta['tram_solic_encVenta'] == 'CAMBIO DIRECCION') ? 'selected' : ''; ?>>CAMBIO DIRECCION</option>
                                <option value="INCONFORMIDAD" <?php echo ($encuesta['tram_solic_encVenta'] == 'INCONFORMIDAD') ? 'selected' : ''; ?>>INCONFORMIDAD</option>
                                <option value="DESCENTRALIZADO" <?php echo ($encuesta['tram_solic_encVenta'] == 'DESCENTRALIZADO') ? 'selected' : ''; ?>>DESCENTRALIZADO</option>
                                <option value="FAVORES" <?php echo ($encuesta['tram_solic_encVenta'] == 'FAVORES') ? 'selected' : ''; ?>>FAVORES</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="num_ficha_encVenta" class="form-label">Número de Ficha *</label>
                            <input type="text" class="form-control" id="num_ficha_encVenta" name="num_ficha_encVenta" 
                                   value="<?php echo $encuesta['num_ficha_encVenta']; ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="num_visita" class="form-label">Número de Visita</label>
                            <input type="number" class="form-control" id="num_visita" name="num_visita" 
                                   value="<?php echo $encuesta['num_visita']; ?>" min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipo_proceso" class="form-label">Tipo de Proceso</label>
                            <select class="form-control" id="tipo_proceso" name="tipo_proceso">
                                <option value="">Seleccione</option>
                                <option value="Descentralizado" <?php echo ($encuesta['tipo_proceso'] == 'Descentralizado') ? 'selected' : ''; ?>>Descentralizado</option>
                                <option value="Encuesta nueva" <?php echo ($encuesta['tipo_proceso'] == 'Encuesta nueva') ? 'selected' : ''; ?>>Encuesta nueva</option>
                                <option value="Encuesta por verificacion" <?php echo ($encuesta['tipo_proceso'] == 'Encuesta por verificacion') ? 'selected' : ''; ?>>Encuesta por verificacion</option>
                                <option value="Favor" <?php echo ($encuesta['tipo_proceso'] == 'Favor') ? 'selected' : ''; ?>>Favor</option>
                                <option value="Inconformidad" <?php echo ($encuesta['tipo_proceso'] == 'Inconformidad') ? 'selected' : ''; ?>>Inconformidad</option>
                                <option value="Portal ciudadano" <?php echo ($encuesta['tipo_proceso'] == 'Portal ciudadano') ? 'selected' : ''; ?>>Portal ciudadano</option>
                                <option value="Prioridad" <?php echo ($encuesta['tipo_proceso'] == 'Prioridad') ? 'selected' : ''; ?>>Prioridad</option>
                                <option value="Verificacion" <?php echo ($encuesta['tipo_proceso'] == 'Verificacion') ? 'selected' : ''; ?>>Verificacion</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="estado_ficha" class="form-label">Estado de la Ficha</label>
                            <select class="form-control" id="estado_ficha" name="estado_ficha">
                                <option value="">Seleccione estado</option>
                                <option value="Direccion errada" <?php echo ($encuesta['estado_ficha'] == 'Direccion errada') ? 'selected' : ''; ?>>Direccion errada</option>
                                <option value="Direccion incompleta" <?php echo ($encuesta['estado_ficha'] == 'Direccion incompleta') ? 'selected' : ''; ?>>Direccion incompleta</option>
                                <option value="Fallecido" <?php echo ($encuesta['estado_ficha'] == 'Fallecido') ? 'selected' : ''; ?>>Fallecido</option>
                                <option value="Fuera de ruta" <?php echo ($encuesta['estado_ficha'] == 'Fuera de ruta') ? 'selected' : ''; ?>>Fuera de ruta</option>
                                <option value="Informante no idoneo" <?php echo ($encuesta['estado_ficha'] == 'Informante no idoneo') ? 'selected' : ''; ?>>Informante no idoneo</option>
                                <option value="Rechazo a la vivienda" <?php echo ($encuesta['estado_ficha'] == 'Rechazo a la vivienda') ? 'selected' : ''; ?>>Rechazo a la vivienda</option>
                                <option value="Validada" <?php echo ($encuesta['estado_ficha'] == 'Validada') ? 'selected' : ''; ?>>Validada</option>
                                <option value="Ya le realizo la encuesta" <?php echo ($encuesta['estado_ficha'] == 'Ya le realizo la encuesta') ? 'selected' : ''; ?>>Ya le realizo la encuesta</option>
                                <option value="Ya no vive en la direccion" <?php echo ($encuesta['estado_ficha'] == 'Ya no vive en la direccion') ? 'selected' : ''; ?>>Ya no vive en la direccion</option>
                                <option value="Zona insegura" <?php echo ($encuesta['estado_ficha'] == 'Zona insegura') ? 'selected' : ''; ?>>Zona insegura</option>
                                <option value="Nadie en el hogar" <?php echo ($encuesta['estado_ficha'] == 'Nadie en el hogar') ? 'selected' : ''; ?>>Nadie en el hogar</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="integra_encVenta" class="form-label">Total Integrantes *</label>
                            <input type="number" class="form-control" id="integra_encVenta" name="integra_encVenta" 
                                   value="<?php echo $encuesta['integra_encVenta']; ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sisben_nocturno" class="form-label">SISBEN Nocturno *</label>
                            <select class="form-control" id="sisben_nocturno" name="sisben_nocturno" required>
                                <option value="">Seleccione</option>
                                <option value="SI" <?php echo ($encuesta['sisben_nocturno'] == 'SI') ? 'selected' : ''; ?>>SI</option>
                                <option value="NO" <?php echo ($encuesta['sisben_nocturno'] == 'NO') ? 'selected' : ''; ?>>NO</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="obs_encVenta" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="obs_encVenta" name="obs_encVenta" rows="3" style="text-transform:uppercase;"><?php echo $encuesta['obs_encVenta']; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección de Integrantes -->
                <div class="section-header">
                    <h4><i class="fas fa-users me-2"></i>INTEGRANTES DEL HOGAR</h4>
                </div>

                <div id="integrantes-container">
                    <?php foreach ($integrantes as $index => $integrante): ?>
                        <div class="formulario-dinamico" data-integrante-id="<?php echo $integrante['id_integCampo']; ?>">
                            <input type="hidden" name="integrante_id[]" value="<?php echo $integrante['id_integCampo']; ?>">
                            
                            <button type="button" class="btn btn-danger eliminar-integrante" onclick="eliminarIntegrante(this)">×</button>
                            
                            <h5>Integrante <?php echo $index + 1; ?></h5>
                            
                            <div class="row">
                                <div class="col-md-3 form-group-dinamico">
                                    <label>Identidad de Género</label>
                                    <select name="gen_integVenta[]" class="form-control smaller-input" >
                                        <option value="">Identidad Género</option>
                                        <option value="F" <?php echo ($integrante['gen_integVenta'] == 'F') ? 'selected' : ''; ?>>F</option>
                                        <option value="M" <?php echo ($integrante['gen_integVenta'] == 'M') ? 'selected' : ''; ?>>M</option>
                                        <option value="O" <?php echo ($integrante['gen_integVenta'] == 'O') ? 'selected' : ''; ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group-dinamico">
                                    <label>Orientación Sexual</label>
                                    <select name="orientacionSexual[]" class="form-control smaller-input" >
                                        <option value="">Orientación Sexual</option>
                                        <option value="Asexual" <?php echo ($integrante['orientacionSexual'] == 'Asexual') ? 'selected' : ''; ?>>Asexual</option>
                                        <option value="Bisexual" <?php echo ($integrante['orientacionSexual'] == 'Bisexual') ? 'selected' : ''; ?>>Bisexual</option>
                                        <option value="Heterosexual" <?php echo ($integrante['orientacionSexual'] == 'Heterosexual') ? 'selected' : ''; ?>>Heterosexual</option>
                                        <option value="Homosexual" <?php echo ($integrante['orientacionSexual'] == 'Homosexual') ? 'selected' : ''; ?>>Homosexual</option>
                                        <option value="Otro" <?php echo ($integrante['orientacionSexual'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group-dinamico">
                                    <label>Rango de Edad</label>
                                    <select name="rango_integVenta[]" class="form-control smaller-input" >
                                        <option value="">Rango Edad</option>
                                        <option value="0 - 6" <?php echo ($integrante['rango_integVenta'] == '0 - 6') ? 'selected' : ''; ?>>0 - 5</option>
                                        <option value="6 - 12" <?php echo ($integrante['rango_integVenta'] == '6 - 12') ? 'selected' : ''; ?>>6 - 12</option>
                                        <option value="13 - 17" <?php echo ($integrante['rango_integVenta'] == '13 - 17') ? 'selected' : ''; ?>>13 - 17</option>
                                        <option value="18 - 28" <?php echo ($integrante['rango_integVenta'] == '18 - 28') ? 'selected' : ''; ?>>18 - 28</option>
                                        <option value="29 - 45" <?php echo ($integrante['rango_integVenta'] == '29 - 45') ? 'selected' : ''; ?>>29 - 45</option>
                                        <option value="46 - 64" <?php echo ($integrante['rango_integVenta'] == '46 - 64') ? 'selected' : ''; ?>>46 - 64</option>
                                        <option value="Mayor o igual a 65" <?php echo ($integrante['rango_integVenta'] == 'Mayor o igual a 65') ? 'selected' : ''; ?>>Mayor o igual a 65</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group-dinamico">
                                    <label>Condición de Discapacidad</label>
                                    <select name="condicionDiscapacidad[]" class="form-control smaller-input condicion-discapacidad" >
                                        <option value="">Condición Discapacidad</option>
                                        <option value="Si" <?php echo ($integrante['condicionDiscapacidad'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                                        <option value="No" <?php echo ($integrante['condicionDiscapacidad'] == 'No') ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 form-group-dinamico tipo-discapacidad" style="<?php echo ($integrante['condicionDiscapacidad'] != 'Si') ? 'display:none;' : ''; ?>">
                                    <label>Tipo de Discapacidad</label>
                                    <select name="tipoDiscapacidad[]" class="form-control smaller-input">
                                        <option value="">Tipo Discapacidad</option>
                                        <option value="Auditiva" <?php echo ($integrante['tipoDiscapacidad'] == 'Auditiva') ? 'selected' : ''; ?>>Auditiva</option>
                                        <option value="Física" <?php echo ($integrante['tipoDiscapacidad'] == 'Física') ? 'selected' : ''; ?>>Física</option>
                                        <option value="Intelectual" <?php echo ($integrante['tipoDiscapacidad'] == 'Intelectual') ? 'selected' : ''; ?>>Intelectual</option>
                                        <option value="Múltiple" <?php echo ($integrante['tipoDiscapacidad'] == 'Múltiple') ? 'selected' : ''; ?>>Múltiple</option>
                                        <option value="Psicosocial" <?php echo ($integrante['tipoDiscapacidad'] == 'Psicosocial') ? 'selected' : ''; ?>>Psicosocial</option>
                                        <option value="Sordoceguera" <?php echo ($integrante['tipoDiscapacidad'] == 'Sordoceguera') ? 'selected' : ''; ?>>Sordoceguera</option>
                                        <option value="Visual" <?php echo ($integrante['tipoDiscapacidad'] == 'Visual') ? 'selected' : ''; ?>>Visual</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group-dinamico">
                                    <label>Grupo Étnico</label>
                                    <select name="grupoEtnico[]" class="form-control smaller-input" >
                                        <option value="">Grupo Étnico</option>
                                        <option value="Indigena" <?php echo ($integrante['grupoEtnico'] == 'Indigena') ? 'selected' : ''; ?>>Indígena</option>
                                        <option value="Negro(a) / Mulato(a) / Afrocolombiano(a)" <?php echo ($integrante['grupoEtnico'] == 'Negro(a) / Mulato(a) / Afrocolombiano(a)') ? 'selected' : ''; ?>>Negro(a) / Mulato(a) / Afrocolombiano(a)</option>
                                        <option value="Raizal" <?php echo ($integrante['grupoEtnico'] == 'Raizal') ? 'selected' : ''; ?>>Raizal</option>
                                        <option value="Palenquero de San Basilio" <?php echo ($integrante['grupoEtnico'] == 'Palenquero de San Basilio') ? 'selected' : ''; ?>>Palenquero de San Basilio</option>
                                        <option value="Mestizo" <?php echo ($integrante['grupoEtnico'] == 'Mestizo') ? 'selected' : ''; ?>>Mestizo</option>
                                        <option value="Gitano (rom)" <?php echo ($integrante['grupoEtnico'] == 'Gitano (rom)') ? 'selected' : ''; ?>>Gitano (rom)</option>
                                        <option value="Ninguno" <?php echo ($integrante['grupoEtnico'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group-dinamico">
                                    <label>¿Es víctima del conflicto?</label>
                                    <select name="victima[]" class="form-control smaller-input" >
                                        <option value="">¿Es víctima?</option>
                                        <option value="Si" <?php echo ($integrante['victima'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                                        <option value="No" <?php echo ($integrante['victima'] == 'No') ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 form-group-dinamico">
                                    <label>¿Es mujer gestante?</label>
                                    <select name="mujerGestante[]" class="form-control smaller-input" >
                                        <option value="">¿Es gestante?</option>
                                        <option value="Si" <?php echo ($integrante['mujerGestante'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                                        <option value="No" <?php echo ($integrante['mujerGestante'] == 'No') ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group-dinamico">
                                    <label>¿Es cabeza de familia?</label>
                                    <select name="cabezaFamilia[]" class="form-control smaller-input" >
                                        <option value="">¿Es cabeza familia?</option>
                                        <option value="Si" <?php echo ($integrante['cabezaFamilia'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                                        <option value="No" <?php echo ($integrante['cabezaFamilia'] == 'No') ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 form-group-dinamico">
                                    <label>¿Experiencia migratoria?</label>
                                    <select name="experienciaMigratoria[]" class="form-control smaller-input" >
                                        <option value="">¿Experiencia migratoria?</option>
                                        <option value="Si" <?php echo ($integrante['experienciaMigratoria'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                                        <option value="No" <?php echo ($integrante['experienciaMigratoria'] == 'No') ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 form-group-dinamico">
                                    <label>Seguridad en Salud</label>
                                    <select name="seguridadSalud[]" class="form-control smaller-input" >
                                        <option value="">Seguridad Salud</option>
                                        <option value="Regimen Contributivo" <?php echo ($integrante['seguridadSalud'] == 'Regimen Contributivo') ? 'selected' : ''; ?>>Regimen Contributivo</option>
                                        <option value="Regimen Subsidiado" <?php echo ($integrante['seguridadSalud'] == 'Regimen Subsidiado') ? 'selected' : ''; ?>>Regimen Subsidiado</option>
                                        <option value="Poblacion Vinculada" <?php echo ($integrante['seguridadSalud'] == 'Poblacion Vinculada') ? 'selected' : ''; ?>>Poblacion Vinculada</option>
                                        <option value="Ninguno" <?php echo ($integrante['seguridadSalud'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group-dinamico">
                                    <label>Nivel Educativo</label>
                                    <select name="nivelEducativo[]" class="form-control smaller-input" >
                                        <option value="">Nivel Educativo</option>
                                        <option value="Ninguno" <?php echo ($integrante['nivelEducativo'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                        <option value="Preescolar" <?php echo ($integrante['nivelEducativo'] == 'Preescolar') ? 'selected' : ''; ?>>Preescolar</option>
                                        <option value="Primaria" <?php echo ($integrante['nivelEducativo'] == 'Primaria') ? 'selected' : ''; ?>>Primaria</option>
                                        <option value="Secundaria" <?php echo ($integrante['nivelEducativo'] == 'Secundaria') ? 'selected' : ''; ?>>Secundaria</option>
                                        <option value="Media Academica o Clasica" <?php echo ($integrante['nivelEducativo'] == 'Media Academica o Clasica') ? 'selected' : ''; ?>>Media Academica o Clasica</option>
                                        <option value="Media Tecnica" <?php echo ($integrante['nivelEducativo'] == 'Media Tecnica') ? 'selected' : ''; ?>>Media Tecnica</option>
                                        <option value="Normalista" <?php echo ($integrante['nivelEducativo'] == 'Normalista') ? 'selected' : ''; ?>>Normalista</option>
                                        <option value="Universitario" <?php echo ($integrante['nivelEducativo'] == 'Universitario') ? 'selected' : ''; ?>>Universitario</option>
                                        <option value="Tecnica Profesional" <?php echo ($integrante['nivelEducativo'] == 'Tecnica Profesional') ? 'selected' : ''; ?>>Tecnica Profesional</option>
                                        <option value="Tecnologica" <?php echo ($integrante['nivelEducativo'] == 'Tecnologica') ? 'selected' : ''; ?>>Tecnologica</option>
                                        <option value="Profesional" <?php echo ($integrante['nivelEducativo'] == 'Profesional') ? 'selected' : ''; ?>>Profesional</option>
                                        <option value="Especializacion" <?php echo ($integrante['nivelEducativo'] == 'Especializacion') ? 'selected' : ''; ?>>Especializacion</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 form-group-dinamico">
                                    <label>Condición de Ocupación</label>
                                    <select name="condicionOcupacion[]" class="form-control smaller-input" >
                                        <option value="">Condición Ocupación</option>
                                        <option value="Ama de casa" <?php echo ($integrante['condicionOcupacion'] == 'Ama de casa') ? 'selected' : ''; ?>>Ama de casa</option>
                                        <option value="Buscando Empleo" <?php echo ($integrante['condicionOcupacion'] == 'Buscando Empleo') ? 'selected' : ''; ?>>Buscando Empleo</option>
                                        <option value="Desempleado(a)" <?php echo ($integrante['condicionOcupacion'] == 'Desempleado(a)') ? 'selected' : ''; ?>>Desempleado(a)</option>
                                        <option value="Empleado(a)" <?php echo ($integrante['condicionOcupacion'] == 'Empleado(a)') ? 'selected' : ''; ?>>Empleado(a)</option>
                                        <option value="Independiente" <?php echo ($integrante['condicionOcupacion'] == 'Independiente') ? 'selected' : ''; ?>>Independiente</option>
                                        <option value="Estudiante" <?php echo ($integrante['condicionOcupacion'] == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                                        <option value="Pensionado" <?php echo ($integrante['condicionOcupacion'] == 'Pensionado') ? 'selected' : ''; ?>>Pensionado</option>
                                        <option value="Ninguno" <?php echo ($integrante['condicionOcupacion'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-4">
                    <a href="showsurvey.php" class="back-button">
                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Actualizar Encuesta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function eliminarIntegrante(button) {
            if (confirm('¿Está seguro de eliminar este integrante?')) {
                $(button).closest('.formulario-dinamico').remove();
                actualizarTotal();
            }
        }

        function actualizarTotal() {
            var total = $('#integrantes-container .formulario-dinamico').length;
            $('#integra_encVenta').val(total);
        }

        $(document).ready(function() {
            // Inicializar Select2 para barrios
            $("#id_barrios").select2({
                placeholder: "Seleccione barrio",
                minimumInputLength: 2,
                allowClear: true,
                ajax: {
                    url: "../buscar_barrios.php",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data,
                        };
                    },
                    cache: true,
                },
                // Mostrar el texto del option existente al inicializar
                templateSelection: function (data) {
                    return data.text || $(data.element).text();
                }
            });

            // Si hay un barrio precargado en el <option selected>, aplicar su zona y cargar comunas
            var preselectedBarrio = $('#id_barrios option[selected]');
            if (preselectedBarrio.length) {
                var zonaVal = preselectedBarrio.data('zona') || preselectedBarrio.attr('data-zona') || ''; 
                zonaVal = zonaVal.toString().toUpperCase();
                if (zonaVal === 'URBANO') zonaVal = 'URBANA';
                if (zonaVal === 'URBANA' || zonaVal === 'RURAL') {
                    $('#zona_encVenta').val(zonaVal);
                }

                // Forzar que Select2 muestre la opción seleccionada
                var selectedId = preselectedBarrio.val();
                if (selectedId) {
                    // trigger change so Select2 acknowledges the selected option
                    $('#id_barrios').val(selectedId).trigger('change');

                    // Cargar comunas para el barrio precargado
                    $.ajax({
                        url: "../comunaGet.php",
                        type: "GET",
                        data: { id_barrio: selectedId },
                        success: function (response) {
                            $("#id_comunas").html(response);
                            $("#id_comunas").removeAttr("disabled");
                            // Si en la encuesta hay una comuna guardada, seleccionarla
                            var comunaGuardada = '<?php echo $encuesta['id_com']; ?>';
                            if (comunaGuardada) {
                                $("#id_comunas").val(comunaGuardada);
                            }
                        },
                        error: function () {
                            console.warn('No se pudieron cargar comunas para el barrio precargado.');
                        }
                    });
                }
            }

            // Asignar zona automáticamente al seleccionar barrio
            $('#id_barrios').on('select2:select', function (e) {
                var data = e.params.data;
                let zona = data.zona?.toUpperCase() || '';
                
                if (zona === 'URBANO') zona = 'URBANA';
                if (zona === 'RURAL') zona = 'RURAL';
                
                if (zona === 'URBANA' || zona === 'RURAL') {
                    $('#zona_encVenta').val(zona);
                } else {
                    $('#zona_encVenta').val('');
                }
            });

            // Manejar cambio de barrio para mostrar/ocultar "otro barrio" y cargar comunas
            $("#id_barrios").on("change", function () {
                const selectedValue = $(this).val();

                // Mostrar/ocultar campo "otro barrio"
                if (selectedValue == "1897") {
                    $("#otro_barrio_container").show();
                } else {
                    $("#otro_barrio_container").hide();
                }

                // Cargar comunas
                let id_barrio = $(this).val();
                if (id_barrio !== "") {
                    $.ajax({
                        url: "../comunaGet.php",
                        type: "GET",
                        data: {
                            id_barrio: id_barrio,
                        },
                        success: function (response) {
                            $("#id_comunas").html(response);
                            $("#id_comunas").removeAttr("disabled");
                        },
                        error: function () {
                            alert("Error al obtener las comunas.");
                        },
                    });
                } else {
                    $("#id_comunas").html('<option value="">Seleccione comuna</option>');
                    $("#id_comunas").attr("disabled", true);
                }
            });

            // Cargar ciudades al inicializar
            var deptoSeleccionado = $('#departamento_expedicion').val();
            var ciudadSeleccionada = '<?php echo $encuesta['ciudad_expedicion']; ?>';
            
            if (deptoSeleccionado) {
                cargarCiudades(deptoSeleccionado, ciudadSeleccionada);
            }

            $('#departamento_expedicion').change(function() {
                var deptoId = $(this).val();
                cargarCiudades(deptoId, '');
            });

            function cargarCiudades(deptoId, ciudadSeleccionada) {
                if (deptoId) {
                    $.ajax({
                        url: '../obtener_municipios.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { cod_departamento: deptoId },
                        success: function(municipios) {
                            var html = '<option value="">Seleccione ciudad</option>';
                            if (Array.isArray(municipios)) {
                                municipios.forEach(function(m) {
                                    html += '<option value="' + m.cod_municipio + '">' + m.nombre_municipio + '</option>';
                                });
                            }
                            $('#ciudad_expedicion').html(html);
                            if (ciudadSeleccionada) {
                                $('#ciudad_expedicion').val(ciudadSeleccionada);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error cargar municipios:', status, error, xhr.responseText);
                            alert('Error al cargar ciudades');
                        }
                    });
                }
            }

            // Manejar condición de discapacidad
            $(document).on('change', '.condicion-discapacidad', function() {
                const valor = $(this).val();
                const tipoDiscapacidad = $(this).closest('.formulario-dinamico').find('.tipo-discapacidad');
                
                if (valor === "Si") {
                    tipoDiscapacidad.show();
                } else {
                    tipoDiscapacidad.hide();
                    tipoDiscapacidad.find('select').val('');
                }
            });

            // Validación del formulario
            $('#form_contacto').submit(function(e) {
                var totalIntegrantes = $('#integrantes-container .formulario-dinamico').length;
                if (totalIntegrantes === 0) {
                    alert('Debe tener al menos un integrante en la encuesta.');
                    e.preventDefault();
                    return false;
                }
            });

            // Actualizar total de integrantes al cargar
            actualizarTotal();

            // Calcular edad automáticamente cuando cambie la fecha de nacimiento
            function calcularEdad() {
                var fechaNac = $("#fecha_nacimiento").val();
                if (fechaNac) {
                    var hoy = new Date();
                    var nacimiento = new Date(fechaNac);
                    var edad = hoy.getFullYear() - nacimiento.getFullYear();
                    var mes = hoy.getMonth() - nacimiento.getMonth();
                    
                    // Ajustar si aún no ha cumplido años este año
                    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                        edad--;
                    }
                    
                    $("#edad_calculada").val(edad + " años");
                } else {
                    $("#edad_calculada").val("");
                }
            }

            // Calcular edad al cargar la página
            calcularEdad();

            $("#fecha_nacimiento").on("change", calcularEdad);

            // Vista previa de la foto - Función común para ambos inputs
            function mostrarVistaPrevia(file) {
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $("#preview_foto").html(
                            '<div style="text-align: center; width: 100%;">' +
                            '<img src="' + event.target.result + '" style="max-width: 100%; max-height: 300px; border-radius: 8px;">' +
                            '<div class="mt-2"><small class="text-success"><i class="fas fa-check-circle"></i> Nueva foto seleccionada (guardar para aplicar cambios)</small></div>' +
                            '</div>'
                        );
                        $("#eliminar_foto_flag").val("0");
                    };
                    reader.readAsDataURL(file);
                } else {
                    $("#preview_foto").html(
                        '<span style="color: #6c757d;"><i class="fas fa-image fa-3x"></i><br>Sin imagen seleccionada</span>'
                    );
                }
            }

            // Input de cámara
            $("#foto_camara").on("change", function(e) {
                var file = e.target.files[0];
                mostrarVistaPrevia(file);
            });

            // Input de galería - transferir archivo al input principal
            $("#foto_galeria").on("change", function(e) {
                var file = e.target.files[0];
                if (file) {
                    // Transferir el archivo al input principal que se enviará
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('foto_camara').files = dataTransfer.files;
                    
                    mostrarVistaPrevia(file);
                }
            });

            // Vista previa de la foto al seleccionar nueva (código anterior - eliminado y reemplazado por función común)
            // $("#foto_encuestado").on("change", function(e) { ... });

            // Eliminar foto
            $("#eliminar_foto").on("click", function() {
                if (confirm("¿Está seguro de eliminar la foto actual?")) {
                    $("#eliminar_foto_flag").val("1");
                    $("#preview_foto").html(
                        '<span style="color: #dc3545;"><i class="fas fa-trash fa-3x"></i><br>Foto marcada para eliminar (guardar para aplicar cambios)</span>'
                    );
                    $("#foto_camara").val("");
                    $("#foto_galeria").val("");
                }
            });
        });
    </script>
</body>
</html>
