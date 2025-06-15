<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();
}

$id_usu     = $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

// Obtener ID de la encuesta a editar
$id_encuesta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_encuesta == 0) {
    header("Location: showEncuestas.php");
    exit();
}

include("../../conexion.php");

// Obtener datos de la encuesta
$sql_encuesta = "SELECT ev.*, u.nombre as nombre_usuario 
                 FROM encventanilla ev 
                 LEFT JOIN usuarios u ON ev.id_usu = u.id_usu 
                 WHERE ev.id_encVenta = ?";

$stmt = $mysqli->prepare($sql_encuesta);
$stmt->bind_param("i", $id_encuesta);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    header("Location: showEncuestas.php");
    exit();
}

$encuesta = $resultado->fetch_assoc();

// Obtener integrantes de la encuesta
$sql_integrantes = "SELECT * FROM integventanilla 
                    WHERE id_encVenta = ? 
                    ORDER BY fecha_alta_integVenta DESC";
$stmt_integrantes = $mysqli->prepare($sql_integrantes);
$stmt_integrantes->bind_param("i", $id_encuesta);
$stmt_integrantes->execute();
$resultado_integrantes = $stmt_integrantes->get_result();
$integrantes = [];

// Mapeo inverso: números a texto para mostrar en el formulario
$rango_edad_texto = [
    1 => "0 - 6",
    2 => "7 - 12", 
    3 => "13 - 17",
    4 => "18 - 28",
    5 => "29 - 45",
    6 => "46 - 64",
    7 => "Mayor o igual a 65"
];

while ($integrante = $resultado_integrantes->fetch_assoc()) {
    // Convertir el rango numérico a texto para el formulario
    if (isset($integrante['rango_integVenta']) && is_numeric($integrante['rango_integVenta'])) {
        $integrante['rango_integVenta_texto'] = $rango_edad_texto[$integrante['rango_integVenta']] ?? '';
    } else {
        $integrante['rango_integVenta_texto'] = '';
    }
    $integrantes[] = $integrante;
}

// Verificar permisos: solo el usuario que creó la encuesta o admin puede editarla
if ($tipo_usu != 1 && $encuesta['id_usu'] != $id_usu) {
    header("Location: showEncuestas.php");
    exit();
}

header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BD SISBEN - Editar Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../barrios.js"></script>    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

        .form-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .btn-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        /* Estilos para integrantes */
        .formulario-dinamico {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 15px;
        }

        .form-group-dinamico {
            display: flex;
            flex-direction: column;
            margin-bottom: 0;
        }

        .form-group-dinamico label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .smaller-input {
            width: 100%;
        }

        .btn-danger {
            grid-column: 1 / -1;
            justify-self: center;
            width: 200px;
        }

        .alert-info-integrantes {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5">
        <!-- Header -->
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 150px;">
                </div>
                <div class="col-md-10">
                    <h1 class="mb-1">
                        <i class="fas fa-edit me-2"></i>EDITAR ENCUESTA DE VENTANILLA
                    </h1>
                    <p class="mb-0">Documento: <strong><?php echo $encuesta['doc_encVenta']; ?></strong> - <?php echo $encuesta['nom_encVenta']; ?></p>
                    <small>Creada el: <?php echo date('d/m/Y H:i', strtotime($encuesta['fecha_alta_encVenta'])); ?> por <?php echo $encuesta['nombre_usuario']; ?></small>
                </div>
            </div>
        </div>

        <form action="updateEncuesta.php" method="POST" id="formEditarEncuesta">
            <input type="hidden" name="id_encVenta" value="<?php echo $encuesta['id_encVenta']; ?>">

            <!-- Datos Básicos -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user me-2"></i>Datos Básicos de la Persona
                </h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="doc_encVenta" class="form-label">Documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="doc_encVenta" name="doc_encVenta" 
                               value="<?php echo $encuesta['doc_encVenta']; ?>" required readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="tipo_documento" name="tipo_documento">
                            <option value="">Seleccione...</option>
                            <option value="CC" <?php echo ($encuesta['tipo_documento'] == 'CC') ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                            <option value="TI" <?php echo ($encuesta['tipo_documento'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad</option>
                            <option value="CE" <?php echo ($encuesta['tipo_documento'] == 'CE') ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                            <option value="RC" <?php echo ($encuesta['tipo_documento'] == 'RC') ? 'selected' : ''; ?>>Registro Civil</option>
                            <option value="PA" <?php echo ($encuesta['tipo_documento'] == 'PA') ? 'selected' : ''; ?>>Pasaporte</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_expedicion" class="form-label">Fecha de Expedición</label>
                        <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" 
                               value="<?php echo $encuesta['fecha_expedicion']; ?>">
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label for="nom_encVenta" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nom_encVenta" name="nom_encVenta" 
                               value="<?php echo $encuesta['nom_encVenta']; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="departamento_expedicion" class="form-label">Departamento Expedición</label>
                        <select class="form-select" id="departamento_expedicion" name="departamento_expedicion">
                            <option value="">Seleccione...</option>
                            <?php
                            $sql_dep = "SELECT * FROM departamentos ORDER BY nombre_departamento";
                            $res_dep = $mysqli->query($sql_dep);
                            while ($dep = $res_dep->fetch_assoc()) {
                                $selected = ($encuesta['departamento_expedicion'] == $dep['cod_departamento']) ? 'selected' : '';
                                echo "<option value='{$dep['cod_departamento']}' {$selected}>{$dep['nombre_departamento']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="ciudad_expedicion" class="form-label">Ciudad Expedición</label>
                        <select class="form-select" id="ciudad_expedicion" name="ciudad_expedicion">
                            <option value="">Seleccione...</option>
                            <!-- Se cargará dinámicamente -->
                        </select>
                    </div>
                </div>
            </div>

            <!-- Datos de Ubicación -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-map-marker-alt me-2"></i>Datos de Ubicación
                </h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="dir_encVenta" class="form-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dir_encVenta" name="dir_encVenta" 
                               value="<?php echo $encuesta['dir_encVenta']; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="zona_encVenta" class="form-label">Zona <span class="text-danger">*</span></label>
                        <select class="form-select" id="zona_encVenta" name="zona_encVenta" required>
                            <option value="">Seleccione...</option>
                            <option value="Urbana" <?php echo ($encuesta['zona_encVenta'] == 'Urbana') ? 'selected' : ''; ?>>Urbana</option>
                            <option value="Rural" <?php echo ($encuesta['zona_encVenta'] == 'Rural') ? 'selected' : ''; ?>>Rural</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="id_com" class="form-label">Comuna</label>
                        <select class="form-select" id="id_com" name="id_com">
                            <option value="">Seleccione...</option>
                            <?php
                            $sql_com = "SELECT * FROM comunas ORDER BY nombre_com";
                            $res_com = $mysqli->query($sql_com);
                            while ($com = $res_com->fetch_assoc()) {
                                $selected = ($encuesta['id_com'] == $com['id_com']) ? 'selected' : '';
                                echo "<option value='{$com['id_com']}' {$selected}>{$com['nombre_com']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label for="id_bar" class="form-label">Barrio</label>
                        <select class="form-select" id="id_bar" name="id_bar">
                            <option value="">Seleccione...</option>
                            <?php
                            $sql_bar = "SELECT * FROM barrios ORDER BY nombre_bar";
                            $res_bar = $mysqli->query($sql_bar);
                            while ($bar = $res_bar->fetch_assoc()) {
                                $selected = ($encuesta['id_bar'] == $bar['id_bar']) ? 'selected' : '';
                                echo "<option value='{$bar['id_bar']}' {$selected}>{$bar['nombre_bar']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="otro_bar_ver_encVenta" class="form-label">Otro Barrio/Vereda</label>
                        <input type="text" class="form-control" id="otro_bar_ver_encVenta" name="otro_bar_ver_encVenta" 
                               value="<?php echo $encuesta['otro_bar_ver_encVenta']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="tram_solic_encVenta" class="form-label">Trámite Solicitado <span class="text-danger">*</span></label>
                        <select class="form-select" id="tram_solic_encVenta" name="tram_solic_encVenta" required>
                            <option value="">Seleccione...</option>
                            <option value="Inclusión" <?php echo ($encuesta['tram_solic_encVenta'] == 'Inclusión') ? 'selected' : ''; ?>>Inclusión</option>
                            <option value="Modificación" <?php echo ($encuesta['tram_solic_encVenta'] == 'Modificación') ? 'selected' : ''; ?>>Modificación</option>
                            <option value="Retiro" <?php echo ($encuesta['tram_solic_encVenta'] == 'Retiro') ? 'selected' : ''; ?>>Retiro</option>
                            <option value="Actualización" <?php echo ($encuesta['tram_solic_encVenta'] == 'Actualización') ? 'selected' : ''; ?>>Actualización</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Datos del Hogar -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-home me-2"></i>Datos del Hogar
                </h3>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="integra_encVenta" class="form-label">Cantidad de Integrantes <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="integra_encVenta" name="integra_encVenta" 
                               value="<?php echo $encuesta['integra_encVenta']; ?>" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label for="num_ficha_encVenta" class="form-label">Número de Ficha</label>
                        <input type="text" class="form-control" id="num_ficha_encVenta" name="num_ficha_encVenta" 
                               value="<?php echo $encuesta['num_ficha_encVenta']; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="sisben_nocturno" class="form-label">SISBEN Nocturno</label>
                        <select class="form-select" id="sisben_nocturno" name="sisben_nocturno">
                            <option value="">Seleccione...</option>
                            <option value="Si" <?php echo ($encuesta['sisben_nocturno'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                            <option value="No" <?php echo ($encuesta['sisben_nocturno'] == 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_alta_encVenta" class="form-label">Fecha de Registro</label>
                        <input type="date" class="form-control" id="fecha_alta_encVenta" name="fecha_alta_encVenta" 
                               value="<?php echo date('Y-m-d', strtotime($encuesta['fecha_alta_encVenta'])); ?>" readonly>
                    </div>
                </div>
            </div>            <!-- Observaciones -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-comment-alt me-2"></i>Observaciones
                </h3>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="obs_encVenta" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="obs_encVenta" name="obs_encVenta" rows="3" 
                                  placeholder="Ingrese observaciones adicionales..."><?php echo $encuesta['obs_encVenta']; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Gestión de Integrantes -->
            <div class="form-section">
                <div class="alert alert-info-integrantes">
                    <h6><i class="fas fa-users me-2"></i>Gestión de Integrantes del Grupo Familiar</h6>
                    <p class="mb-0">Aquí puede agregar, editar o eliminar integrantes del grupo familiar asociado a esta encuesta.</p>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="cant_nuevos_integrantes">Agregar Integrantes:</label>
                            <input type="number" id="cant_nuevos_integrantes" class="form-control" min="1" max="10" placeholder="Cantidad a agregar" />
                        </div>
                        <div class="form-group col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="agregar_integrantes">
                                <i class="fas fa-plus"></i> Agregar Integrantes
                            </button>
                        </div>
                    </div>
                </div>

                <div id="integrantes-container">
                    <?php if (!empty($integrantes)): ?>
                        <?php foreach ($integrantes as $index => $integrante): ?>
                        <div class="formulario-dinamico" data-integrante-id="<?php echo $integrante['id_integVenta']; ?>">
                            <input type="hidden" name="id_integrante[]" value="<?php echo $integrante['id_integVenta']; ?>" />
                            <!-- Campo cantidad oculto, siempre valor 1 -->
                            <input type="hidden" name="cant_integVenta[]" value="1" />
                            
                            <div class="form-group-dinamico">
                                <label>* Género</label>
                                <select name="gen_integVenta[]" class="form-control smaller-input" required>
                                    <option value="">Seleccione</option>
                                    <option value="M" <?php echo (($integrante['gen_integVenta'] ?? '') == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="F" <?php echo (($integrante['gen_integVenta'] ?? '') == 'F') ? 'selected' : ''; ?>>Femenino</option>
                                    <option value="O" <?php echo (($integrante['gen_integVenta'] ?? '') == 'O') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>

                            <div class="form-group-dinamico">
                                <label>* Rango Edad</label>                                <select name="rango_integVenta[]" class="form-control smaller-input" required>
                                    <option value="">Seleccione</option>
                                    <option value="0 - 6" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == '0 - 6') ? 'selected' : ''; ?>>0 - 6</option>
                                    <option value="7 - 12" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == '7 - 12') ? 'selected' : ''; ?>>7 - 12</option>
                                    <option value="13 - 17" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == '13 - 17') ? 'selected' : ''; ?>>13 - 17</option>
                                    <option value="18 - 28" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == '18 - 28') ? 'selected' : ''; ?>>18 - 28</option>
                                    <option value="29 - 45" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == '29 - 45') ? 'selected' : ''; ?>>29 - 45</option>
                                    <option value="46 - 64" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == '46 - 64') ? 'selected' : ''; ?>>46 - 64</option>
                                    <option value="Mayor o igual a 65" <?php echo (($integrante['rango_integVenta_texto'] ?? '') == 'Mayor o igual a 65') ? 'selected' : ''; ?>>Mayor o igual a 65</option>
                                </select>
                            </div>

                            <div class="form-group-dinamico">
                                <label>Orientación Sexual</label>
                                <select name="orientacionSexual[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Asexual" <?php echo (($integrante['orientacionSexual'] ?? '') == 'Asexual') ? 'selected' : ''; ?>>Asexual</option>
                                    <option value="Bisexual" <?php echo (($integrante['orientacionSexual'] ?? '') == 'Bisexual') ? 'selected' : ''; ?>>Bisexual</option>
                                    <option value="Heterosexual" <?php echo (($integrante['orientacionSexual'] ?? '') == 'Heterosexual') ? 'selected' : ''; ?>>Heterosexual</option>
                                    <option value="Homosexual" <?php echo (($integrante['orientacionSexual'] ?? '') == 'Homosexual') ? 'selected' : ''; ?>>Homosexual</option>
                                    <option value="Otro" <?php echo (($integrante['orientacionSexual'] ?? '') == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                            
                            <div class="form-group-dinamico">
                                <label>Condición Discapacidad</label>
                                <select name="condicionDiscapacidad[]" class="form-control smaller-input condicion-discapacidad">
                                    <option value="">Seleccione</option>
                                    <option value="Si" <?php echo (($integrante['condicionDiscapacidad'] ?? '') == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (($integrante['condicionDiscapacidad'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <div class="form-group-dinamico">
                                <label>Tipo Discapacidad</label>
                                <select name="tipoDiscapacidad[]" class="form-control smaller-input tipo-discapacidad" style="display: <?php echo (($integrante['condicionDiscapacidad'] ?? '') == 'Si') ? 'block' : 'none'; ?>;">
                                    <option value="">Seleccione</option>
                                    <option value="Auditiva" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Auditiva') ? 'selected' : ''; ?>>Auditiva</option>
                                    <option value="Física" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Física') ? 'selected' : ''; ?>>Física</option>
                                    <option value="Intelectual" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Intelectual') ? 'selected' : ''; ?>>Intelectual</option>
                                    <option value="Múltiple" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Múltiple') ? 'selected' : ''; ?>>Múltiple</option>
                                    <option value="Psicosocial" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Psicosocial') ? 'selected' : ''; ?>>Psicosocial</option>
                                    <option value="Sordoceguera" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Sordoceguera') ? 'selected' : ''; ?>>Sordoceguera</option>
                                    <option value="Visual" <?php echo (($integrante['tipoDiscapacidad'] ?? '') == 'Visual') ? 'selected' : ''; ?>>Visual</option>
                                </select>
                            </div>
                            
                            <div class="form-group-dinamico">
                                <label>Grupo Étnico</label>
                                <select name="grupoEtnico[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Indigena" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Indigena') ? 'selected' : ''; ?>>Indígena</option>
                                    <option value="Negro(a) / Mulato(a) / Afrocolombiano(a)" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Negro(a) / Mulato(a) / Afrocolombiano(a)') ? 'selected' : ''; ?>>Negro(a) / Mulato(a) / Afrocolombiano(a)</option>
                                    <option value="Raizal" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Raizal') ? 'selected' : ''; ?>>Raizal</option>
                                    <option value="Palenquero de San Basilio" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Palenquero de San Basilio') ? 'selected' : ''; ?>>Palenquero de San Basilio</option>
                                    <option value="Mestizo" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Mestizo') ? 'selected' : ''; ?>>Mestizo</option>
                                    <option value="Gitano (rom)" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Gitano (rom)') ? 'selected' : ''; ?>>Gitano (rom)</option>
                                    <option value="Ninguno" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                            
                            <div class="form-group-dinamico">
                                <label>Víctima</label>
                                <select name="victima[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Si" <?php echo (($integrante['victima'] ?? '') == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (($integrante['victima'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            
                            <div class="form-group-dinamico">
                                <label>Mujer Gestante</label>
                                <select name="mujerGestante[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Si" <?php echo (($integrante['mujerGestante'] ?? '') == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (($integrante['mujerGestante'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            
                            <div class="form-group-dinamico">
                                <label>Cabeza Familia</label>
                                <select name="cabezaFamilia[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Si" <?php echo (($integrante['cabezaFamilia'] ?? '') == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (($integrante['cabezaFamilia'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <div class="form-group-dinamico">
                                <label>Experiencia Migratoria</label>
                                <select name="experienciaMigratoria[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Si" <?php echo (($integrante['experienciaMigratoria'] ?? '') == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (($integrante['experienciaMigratoria'] ?? '') == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <div class="form-group-dinamico">
                                <label>Seguridad Salud</label>
                                <select name="seguridadSalud[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Regimen Contributivo" <?php echo (($integrante['seguridadSalud'] ?? '') == 'Regimen Contributivo') ? 'selected' : ''; ?>>Regimen Contributivo</option>
                                    <option value="Regimen Subsidiado" <?php echo (($integrante['seguridadSalud'] ?? '') == 'Regimen Subsidiado') ? 'selected' : ''; ?>>Regimen Subsidiado</option>
                                    <option value="Poblacion Vinculada" <?php echo (($integrante['seguridadSalud'] ?? '') == 'Poblacion Vinculada') ? 'selected' : ''; ?>>Poblacion Vinculada</option>
                                </select>
                            </div>
                            
                            <div class="form-group-dinamico">
                                <label>Nivel Educativo</label>
                                <select name="nivelEducativo[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Ninguno" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                    <option value="Preescolar" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Preescolar') ? 'selected' : ''; ?>>Preescolar</option>
                                    <option value="Primaria" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Primaria') ? 'selected' : ''; ?>>Primaria</option>
                                    <option value="Secundaria" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Secundaria') ? 'selected' : ''; ?>>Secundaria</option>
                                    <option value="Media Academica o Clasica" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Media Academica o Clasica') ? 'selected' : ''; ?>>Media Académica o Clásica</option>
                                    <option value="Media Tecnica" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Media Tecnica') ? 'selected' : ''; ?>>Media Técnica</option>
                                    <option value="Normalista" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Normalista') ? 'selected' : ''; ?>>Normalista</option>
                                    <option value="Tecnica Profesional" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Tecnica Profesional') ? 'selected' : ''; ?>>Técnica Profesional</option>
                                    <option value="Tecnologica" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Tecnologica') ? 'selected' : ''; ?>>Tecnológica</option>
                                    <option value="Universitario" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Universitario') ? 'selected' : ''; ?>>Universitario</option>
                                    <option value="Profesional" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Profesional') ? 'selected' : ''; ?>>Profesional</option>
                                    <option value="Especializacion" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Especializacion') ? 'selected' : ''; ?>>Especialización</option>
                                </select>
                            </div>

                            <div class="form-group-dinamico">
                                <label>Condición Ocupación</label>
                                <select name="condicionOcupacion[]" class="form-control smaller-input">
                                    <option value="">Seleccione</option>
                                    <option value="Ama de casa" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Ama de casa') ? 'selected' : ''; ?>>Ama de casa</option>
                                    <option value="Buscando Empleo" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Buscando Empleo') ? 'selected' : ''; ?>>Buscando Empleo</option>
                                    <option value="Desempleado(a)" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Desempleado(a)') ? 'selected' : ''; ?>>Desempleado(a)</option>
                                    <option value="Empleado(a)" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Empleado(a)') ? 'selected' : ''; ?>>Empleado(a)</option>
                                    <option value="Independiente" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Independiente') ? 'selected' : ''; ?>>Independiente</option>
                                    <option value="Estudiante" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                                    <option value="Pensionado" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Pensionado') ? 'selected' : ''; ?>>Pensionado</option>
                                    <option value="Ninguno" <?php echo (($integrante['condicionOcupacion'] ?? '') == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                            
                            <button type="button" class="btn btn-danger eliminar-integrante">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-section">
                <div class="row g-3">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-custom w-100">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="showEncuestas.php" class="btn btn-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="viewEncuesta.php?id=<?php echo $encuesta['id_encVenta']; ?>" class="btn btn-info w-100">
                            <i class="fas fa-eye me-2"></i>Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Cargar municipios del departamento seleccionado al cargar la página
            const departamentoSeleccionado = $('#departamento_expedicion').val();
            const ciudadSeleccionada = '<?php echo $encuesta['ciudad_expedicion']; ?>';
            
            if (departamentoSeleccionado) {
                cargarMunicipios(departamentoSeleccionado, ciudadSeleccionada);
            }

            // Evento change para cargar municipios cuando se selecciona un departamento
            $('#departamento_expedicion').change(function() {
                const departamento = $(this).val();
                if (departamento) {
                    cargarMunicipios(departamento);
                } else {
                    $('#ciudad_expedicion').empty().append('<option value="">Seleccione...</option>').prop('disabled', true);
                }
            });

            function cargarMunicipios(codDepartamento, ciudadSeleccionada = '') {
                $.ajax({
                    url: '../obtener_municipios.php',
                    type: 'POST',
                    data: { cod_departamento: codDepartamento },
                    dataType: 'json',
                    success: function(municipios) {
                        let ciudadSelect = $('#ciudad_expedicion');
                        ciudadSelect.empty().append('<option value="">Seleccione...</option>');
                        
                        $.each(municipios, function(index, municipio) {
                            const selected = (municipio.cod_municipio === ciudadSeleccionada) ? 'selected' : '';
                            ciudadSelect.append(
                                $('<option>', {
                                    value: municipio.cod_municipio,
                                    text: municipio.nombre_municipio,
                                    selected: selected
                                })
                            );
                        });
                        
                        ciudadSelect.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al obtener municipios:", error);
                        alert("Error al cargar los municipios. Por favor, intente nuevamente.");
                    }
                });            }

            // Validación del formulario
            $('#formEditarEncuesta').submit(function(e) {
                let errores = [];
                
                if (!$('#doc_encVenta').val().trim()) {
                    errores.push('El documento es obligatorio');
                }
                
                if (!$('#nom_encVenta').val().trim()) {
                    errores.push('El nombre es obligatorio');
                }
                
                if (!$('#dir_encVenta').val().trim()) {
                    errores.push('La dirección es obligatoria');
                }
                
                if (!$('#zona_encVenta').val()) {
                    errores.push('La zona es obligatoria');
                }
                
                if (!$('#tram_solic_encVenta').val()) {
                    errores.push('El trámite solicitado es obligatorio');
                }
                
                if (!$('#integra_encVenta').val() || $('#integra_encVenta').val() < 1) {
                    errores.push('La cantidad de integrantes debe ser mayor a 0');
                }
                
                if (errores.length > 0) {
                    e.preventDefault();
                    alert('Por favor corrija los siguientes errores:\n\n• ' + errores.join('\n• '));
                    return false;
                }
                
                return confirm('¿Está seguro de que desea guardar los cambios realizados a esta encuesta?');
            });

            // Función para crear nuevo integrante
            function crearNuevoIntegrante() {
                return `
                <div class="formulario-dinamico">
                    <input type="hidden" name="id_integrante[]" value="nuevo" />
                    <!-- Campo cantidad oculto, siempre valor 1 -->
                    <input type="hidden" name="cant_integVenta[]" value="1" />
                    
                    <div class="form-group-dinamico">
                        <label>* Género</label>
                        <select name="gen_integVenta[]" class="form-control smaller-input" required>
                            <option value="">Seleccione</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>* Rango Edad</label>
                        <select name="rango_integVenta[]" class="form-control smaller-input" required>
                            <option value="">Seleccione</option>
                            <option value="0 - 6">0 - 6</option>
                            <option value="7 - 12">7 - 12</option>
                            <option value="13 - 17">13 - 17</option>
                            <option value="18 - 28">18 - 28</option>
                            <option value="29 - 45">29 - 45</option>
                            <option value="46 - 64">46 - 64</option>
                            <option value="Mayor o igual a 65">Mayor o igual a 65</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Orientación Sexual</label>
                        <select name="orientacionSexual[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Asexual">Asexual</option>
                            <option value="Bisexual">Bisexual</option>
                            <option value="Heterosexual">Heterosexual</option>
                            <option value="Homosexual">Homosexual</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Condición Discapacidad</label>
                        <select name="condicionDiscapacidad[]" class="form-control smaller-input condicion-discapacidad">
                            <option value="">Seleccione</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Tipo Discapacidad</label>
                        <select name="tipoDiscapacidad[]" class="form-control smaller-input tipo-discapacidad" style="display: none;">
                            <option value="">Seleccione</option>
                            <option value="Auditiva">Auditiva</option>
                            <option value="Física">Física</option>
                            <option value="Intelectual">Intelectual</option>
                            <option value="Múltiple">Múltiple</option>
                            <option value="Psicosocial">Psicosocial</option>
                            <option value="Sordoceguera">Sordoceguera</option>
                            <option value="Visual">Visual</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Grupo Étnico</label>
                        <select name="grupoEtnico[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Indigena">Indígena</option>
                            <option value="Negro(a) / Mulato(a) / Afrocolombiano(a)">Negro(a) / Mulato(a) / Afrocolombiano(a)</option>
                            <option value="Raizal">Raizal</option>
                            <option value="Palenquero de San Basilio">Palenquero de San Basilio</option>
                            <option value="Mestizo">Mestizo</option>
                            <option value="Gitano (rom)">Gitano (rom)</option>
                            <option value="Ninguno">Ninguno</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Víctima</label>
                        <select name="victima[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Mujer Gestante</label>
                        <select name="mujerGestante[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Cabeza Familia</label>
                        <select name="cabezaFamilia[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group-dinamico">
                        <label>Experiencia Migratoria</label>
                        <select name="experienciaMigratoria[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group-dinamico">
                        <label>Seguridad Salud</label>
                        <select name="seguridadSalud[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Regimen Contributivo">Regimen Contributivo</option>
                            <option value="Regimen Subsidiado">Regimen Subsidiado</option>
                            <option value="Poblacion Vinculada">Poblacion Vinculada</option>
                        </select>
                    </div>
                    
                    <div class="form-group-dinamico">
                        <label>Nivel Educativo</label>
                        <select name="nivelEducativo[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Ninguno">Ninguno</option>
                            <option value="Preescolar">Preescolar</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                            <option value="Media Academica o Clasica">Media Académica o Clásica</option>
                            <option value="Media Tecnica">Media Técnica</option>
                            <option value="Normalista">Normalista</option>
                            <option value="Tecnica Profesional">Técnica Profesional</option>
                            <option value="Tecnologica">Tecnológica</option>
                            <option value="Universitario">Universitario</option>
                            <option value="Profesional">Profesional</option>
                            <option value="Especializacion">Especialización</option>
                        </select>
                    </div>

                    <div class="form-group-dinamico">
                        <label>Condición Ocupación</label>
                        <select name="condicionOcupacion[]" class="form-control smaller-input">
                            <option value="">Seleccione</option>
                            <option value="Ama de casa">Ama de casa</option>
                            <option value="Buscando Empleo">Buscando Empleo</option>
                            <option value="Desempleado(a)">Desempleado(a)</option>
                            <option value="Empleado(a)">Empleado(a)</option>
                            <option value="Independiente">Independiente</option>
                            <option value="Estudiante">Estudiante</option>
                            <option value="Pensionado">Pensionado</option>
                            <option value="Ninguno">Ninguno</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn btn-danger eliminar-integrante">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
                `;
            }

            // Evento para agregar nuevos integrantes
            $('#agregar_integrantes').click(function() {
                const cantidad = parseInt($('#cant_nuevos_integrantes').val());
                
                if (!cantidad || cantidad < 1 || cantidad > 10) {
                    alert('Por favor ingrese una cantidad válida (1-10)');
                    return;
                }
                
                for (let i = 0; i < cantidad; i++) {
                    $('#integrantes-container').append(crearNuevoIntegrante());
                }
                
                // Limpiar el campo
                $('#cant_nuevos_integrantes').val('');
                
                // Reactivar eventos para nuevos elementos
                activarEventosIntegrantes();
            });

            // Función para activar eventos en integrantes
            function activarEventosIntegrantes() {
                // Evento para eliminar integrante
                $(document).off('click', '.eliminar-integrante').on('click', '.eliminar-integrante', function() {
                    if (confirm('¿Está seguro de que desea eliminar este integrante?')) {
                        $(this).closest('.formulario-dinamico').remove();
                    }
                });

                // Evento para mostrar/ocultar tipo discapacidad
                $(document).off('change', '.condicion-discapacidad').on('change', '.condicion-discapacidad', function() {
                    const tipoDiscapacidadSelect = $(this).closest('.formulario-dinamico').find('.tipo-discapacidad');
                    if ($(this).val() === 'Si') {
                        tipoDiscapacidadSelect.show();
                    } else {
                        tipoDiscapacidadSelect.hide();
                        tipoDiscapacidadSelect.val('');
                    }
                });
            }

            // Activar eventos al cargar la página
            activarEventosIntegrantes();
        });
    </script>
</body>
</html>
