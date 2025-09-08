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

header("Content-Type: text/html;charset=utf-8");

// Verificar que se proporcione el ID del movimiento
if (!isset($_GET['id_movimiento']) || empty($_GET['id_movimiento'])) {
    header("Location: showMovimientos.php");
    exit();
}

$id_movimiento = $_GET['id_movimiento'];

// Conectar a la base de datos y obtener datos del movimiento
include("../../conexion.php");
date_default_timezone_set("America/Bogota");

// Verificar que el movimiento existe y que el usuario tiene permisos
$sql_movimiento = "SELECT m.*, u.nombre AS nombre_usuario
                   FROM movimientos m 
                   LEFT JOIN usuarios u ON m.id_usu = u.id_usu
                   WHERE m.id_movimiento = '$id_movimiento'";

// Si no es administrador, solo puede ver sus propios movimientos
if ($tipo_usu != '1') {
    $sql_movimiento .= " AND m.id_usu = '$id_usu'";
}

$resultado_movimiento = mysqli_query($mysqli, $sql_movimiento);

if (!$resultado_movimiento || mysqli_num_rows($resultado_movimiento) == 0) {
    echo "<script>alert('Movimiento no encontrado o sin permisos'); window.location.href='showMovimientos.php';</script>";
    exit();
}

$movimiento = mysqli_fetch_assoc($resultado_movimiento);

// Obtener integrantes si existen
$sql_integrantes = "SELECT * FROM integmovimientos_independiente 
                    WHERE doc_encVenta = '{$movimiento['doc_encVenta']}' 
                    AND estado_integMovIndep = 1
                    ORDER BY fecha_alta_integMovIndep DESC";
$resultado_integrantes = mysqli_query($mysqli, $sql_integrantes);
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

if ($resultado_integrantes) {
    while ($integrante = mysqli_fetch_assoc($resultado_integrantes)) {
        
        // El valor ya viene como texto desde la BD, no necesita conversión
        if (isset($integrante['rango_integMovIndep']) && !empty($integrante['rango_integMovIndep'])) {
            $integrante['rango_integMovIndep_texto'] = $integrante['rango_integMovIndep'];
        } else {
            $integrante['rango_integMovIndep_texto'] = '';
        }
        $integrantes[] = $integrante;
    }
}

// Obtener departamentos
$sql_departamentos = "SELECT * FROM departamentos ORDER BY nombre_departamento ASC";
$resultado_departamentos = mysqli_query($mysqli, $sql_departamentos);
$departamentos = [];
while ($row = mysqli_fetch_assoc($resultado_departamentos)) {
    $departamentos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BD SISBEN - Editar Movimiento</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

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

        .alert-info-movimiento {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
        }

        .select2-container .select2-selection--single {
            height: 40px !important;
            padding: 6px 12px;
            font-size: 16px;
            line-height: 30px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento_expedicion');
            const ciudadSelect = document.getElementById('ciudad_expedicion');

            // Cargar municipios al cambiar departamento
            departamentoSelect.addEventListener('change', function() {
                const departamento = this.value;
                cargarMunicipios(departamento);
            });

            function cargarMunicipios(departamento) {
                ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

                if (departamento === '') {
                    ciudadSelect.disabled = true;
                    return;
                }

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../obtener_municipios.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const municipios = JSON.parse(xhr.responseText);
                        municipios.forEach(function(municipio) {
                            const option = document.createElement('option');
                            option.value = municipio.cod_municipio;
                            option.textContent = municipio.nombre_municipio;
                            
                            // Seleccionar el municipio actual si coincide
                            if (municipio.cod_municipio === '<?php echo $movimiento['ciudad_expedicion']; ?>') {
                                option.selected = true;
                            }
                            
                            ciudadSelect.appendChild(option);
                        });
                        ciudadSelect.disabled = false;
                    }
                };

                xhr.send('cod_departamento=' + encodeURIComponent(departamento));
            }

            // Cargar municipios al cargar la página si hay departamento seleccionado
            if (departamentoSelect.value) {
                cargarMunicipios(departamentoSelect.value);
            }
        });        $(document).ready(function() {            function actualizarTotal() {
                // Contar simplemente el número de integrantes (cada uno cuenta como 1)
                let total = $(".formulario-dinamico").length;
                $("#total_integrantes").val(total);
                $("#integra_encVenta").val(total);
            }

            // Función para crear un nuevo integrante
            function crearNuevoIntegrante() {                return `
                <div class="formulario-dinamico">
                    <input type="hidden" name="id_integrante[]" value="nuevo" />
                    <!-- Campo cantidad oculto, siempre valor 1 -->
                    <input type="hidden" name="cant_integMovIndep[]" value="1" />
                    
                    <div class="form-group-dinamico">
                        <label>* Género</label>
                        <select name="gen_integMovIndep[]" class="form-control smaller-input" required>
                            <option value="">Seleccione</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="O">Otro</option>
                        </select>
                    </div>                      <div class="form-group-dinamico">
                        <label>* Rango Edad</label>
                        <select name="rango_integMovIndep[]" class="form-control smaller-input" required>
                            <option value="">Seleccione</option>
                            <option value="0 - 6">0 - 5</option>
                            <option value="7 - 12">6 - 12</option>
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
                
                $('#cant_nuevos_integrantes').val('');
                actualizarTotal();
            });            // Evento para eliminar integrantes
            $(document).on('click', '.eliminar-integrante', function() {
                const integranteDiv = $(this).closest('.formulario-dinamico');
                const idIntegrante = integranteDiv.find("input[name='id_integrante[]']").val();
                
                if (confirm('¿Está seguro de eliminar este integrante?')) {
                    if (idIntegrante === 'nuevo') {
                        // Si es un integrante nuevo (no guardado), simplemente remover del DOM
                        integranteDiv.remove();
                        actualizarTotal();
                    } else {
                        // Si es un integrante existente, eliminar vía AJAX
                        $.ajax({
                            url: 'eliminarIntegrante.php',
                            type: 'POST',
                            data: { id_integrante: idIntegrante },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    integranteDiv.remove();
                                    actualizarTotal();
                                    alert('Integrante eliminado exitosamente');
                                } else {
                                    alert('Error al eliminar integrante: ' + response.message);
                                }
                            },
                            error: function() {
                                alert('Error de conexión al eliminar integrante');
                            }
                        });
                    }
                }
            });

            // Evento para mostrar/ocultar tipo de discapacidad
            $(document).on('change', '.condicion-discapacidad', function() {
                const tipoDiscapacidadSelect = $(this).closest('.formulario-dinamico').find('.tipo-discapacidad');
                if ($(this).val() === 'Si') {
                    tipoDiscapacidadSelect.show();
                } else {
                    tipoDiscapacidadSelect.hide();
                    tipoDiscapacidadSelect.val('');
                }
            });            // Inicializar Select2 para barrios
            $('#id_barrios').select2({
                ajax: {
                    url: '../buscar_barrios.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Seleccione un barrio',
                minimumInputLength: 1,
                width: '100%'
            });

            // Cargar barrio actual si existe
            <?php if (!empty($movimiento['id_bar'])): ?>
            $.ajax({
                type: 'GET',
                url: '../buscar_barrios.php',
                data: {
                    q: '',
                    id: <?php echo $movimiento['id_bar']; ?>
                },
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        let barrio = data[0];
                        let option = new Option(barrio.text, barrio.id, true, true);
                        $('#id_barrios').append(option).trigger('change');
                    }
                }
            });
            <?php endif; ?>

            // Evento para cargar comunas cuando se selecciona un barrio
            $('#id_barrios').on('change', function() {
                var id_barrio = $(this).val();
                if (id_barrio) {
                    $.ajax({
                        url: '../comunaGet.php',
                        type: 'GET',
                        data: {
                            id_barrio: id_barrio
                        },
                        success: function(data) {
                            $('#id_comunas').html(data);
                            $('#id_comunas').prop('disabled', false);
                            $('#id_comunas').val('<?php echo $movimiento['id_com']; ?>');
                        }
                    });

                    // Verificar si se seleccionó "Otro"
                    let selectedText = $("#id_barrios option:selected").text().trim();
                    if (selectedText.toUpperCase() === "OTRO") {
                        $('#otro_barrio_container').show();
                    } else {
                        $('#otro_barrio_container').hide();
                        $('#otro_bar_ver_encVenta').val('');
                    }
                } else {
                    $('#id_comunas').html('<option value="" disabled>Seleccione comuna</option>');
                    $('#id_comunas').prop('disabled', true);
                }
            });

            // Inicializar total de integrantes
            actualizarTotal();
        });
    </script>
</head>

<body>
    <center>
        <img src='../../img/sisben.png' width=300 height=185 class="responsive">
    </center>
    <br />

    <div class="container pt-2">
        <h1><b><i class="fas fa-edit"></i> EDITAR MOVIMIENTO</b></h1>
        <p><i><b><font size=3 color=#c68615>* Campos obligatorios</font></b></i></p>

        <div class="alert alert-info-movimiento">
            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Información del Movimiento</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>ID Movimiento:</strong> <?php echo $movimiento['id_movimiento']; ?><br>
                    <strong>Fecha Creación:</strong> <?php echo date('d/m/Y H:i', strtotime($movimiento['fecha_movimiento'])); ?><br>
                    <strong>Usuario Creador:</strong> <?php echo $movimiento['nombre_usuario']; ?>
                </div>
                <div class="col-md-6">
                    <strong>Tipo Original:</strong> <?php echo $movimiento['tipo_movimiento']; ?><br>
                    <strong>Estado Ficha:</strong> <?php echo ($movimiento['estado_ficha'] == 0) ? 'RETIRADA' : 'ACTIVA'; ?><br>
                    <strong>Integrantes:</strong> <?php echo count($integrantes); ?>
                </div>
            </div>
        </div>

        <form action='updateMovimiento.php' method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_movimiento" value="<?php echo $movimiento['id_movimiento']; ?>">
            
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="doc_encVenta">* DOCUMENTO:</label>
                        <input type='number' name='doc_encVenta' class='form-control' id="doc_encVenta" value="<?php echo $movimiento['doc_encVenta']; ?>" readonly />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fec_reg_encVenta">* FECHA REGISTRO:</label>
                        <input type="date" name="fec_reg_encVenta" class="form-control" id="fec_reg_encVenta" value="<?php echo date('Y-m-d', strtotime($movimiento['fec_reg_encVenta'])); ?>" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tipo_documento">* TIPO DE DOCUMENTO:</label>
                        <select name="tipo_documento" class="form-control" id="tipo_documento">
                            <option value="">SELECCIONE:</option>
                            <option value="cedula" <?php echo ($movimiento['tipo_documento'] == 'cedula') ? 'selected' : ''; ?>>CEDULA</option>
                            <option value="ppt" <?php echo ($movimiento['tipo_documento'] == 'ppt') ? 'selected' : ''; ?>>PPT</option>
                            <option value="cedula_extranjeria" <?php echo ($movimiento['tipo_documento'] == 'cedula_extranjeria') ? 'selected' : ''; ?>>CEDULA EXTRANJERIA</option>
                            <option value="otro" <?php echo ($movimiento['tipo_documento'] == 'otro') ? 'selected' : ''; ?>>OTRO</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="departamento_expedicion">* DEPARTAMENTO EXPEDICIÓN:</label>
                        <select class="form-control" name="departamento_expedicion" id="departamento_expedicion">
                            <option value="">Seleccione un departamento</option>
                            <?php foreach ($departamentos as $departamento): ?>
                                <option value="<?php echo $departamento['cod_departamento']; ?>" 
                                        <?php echo ($movimiento['departamento_expedicion'] == $departamento['cod_departamento']) ? 'selected' : ''; ?>>
                                    <?php echo $departamento['nombre_departamento']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="ciudad_expedicion">* MUNICIPIO EXPEDICIÓN:</label>
                        <select id="ciudad_expedicion" name="ciudad_expedicion" class="form-control" disabled required>
                            <option value="">Seleccione un municipio</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICIÓN:</label>
                        <input type='date' name='fecha_expedicion' id="fecha_expedicion" class='form-control' value="<?php echo $movimiento['fecha_expedicion']; ?>" required />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="nom_encVenta">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_encVenta' id="nom_encVenta" class='form-control' value="<?php echo $movimiento['nom_encVenta']; ?>" required style="text-transform:uppercase;" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type='date' name='fecha_nacimiento' id="fecha_nacimiento" class='form-control' value="<?php echo (!empty($movimiento['fecha_nacimiento']) ? $movimiento['fecha_nacimiento'] : ''); ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="dir_encVenta">* DIRECCIÓN:</label>
                        <input type='text' name='dir_encVenta' id="dir_encVenta" class='form-control' value="<?php echo $movimiento['dir_encVenta']; ?>" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_barrios">* BARRIO O VEREDA:</label>
                        <select id="id_barrios" class="form-control" name="id_bar" style="width: 100%;min-height: 55px;"></select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_comunas">* COMUNA O CORREGIMIENTO:</label>
                        <select id="id_comunas" class="form-control" name="id_com" disabled>
                            <option value="" disabled>Seleccione comuna</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4" id="otro_barrio_container" style="display: <?php echo (!empty($movimiento['otro_bar_ver_encVenta'])) ? 'block' : 'none'; ?>;">
                        <label for="otro_bar_ver_encVenta">ESPECIFIQUE BARRIO, VEREDA O INVASIÓN:</label>
                        <input type="text" id="otro_bar_ver_encVenta" name="otro_bar_ver_encVenta" class="form-control" value="<?php echo $movimiento['otro_bar_ver_encVenta']; ?>" placeholder="Ingrese el barrio">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="zona_encVenta">* ZONA:</label>
                        <select id="zona_encVenta" class="form-control" name="zona_encVenta">
                            <option value="">* SELECCIONE LA ZONA:</option>
                            <option value="URBANA" <?php echo ($movimiento['zona_encVenta'] == 'URBANA') ? 'selected' : ''; ?>>URBANA</option>
                            <option value="RURAL" <?php echo ($movimiento['zona_encVenta'] == 'RURAL') ? 'selected' : ''; ?>>RURAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="movimientos">* MOVIMIENTOS:</label>
                        <select class="form-control" name="movimientos" id="selectEF">
                            <option value="">SELECCIONE:</option>
                            <option value="inclusion" <?php echo ($movimiento['tipo_movimiento'] == 'inclusion') ? 'selected' : ''; ?>>Inclusión</option>
                            <option value="Inconformidad por clasificacion" <?php echo ($movimiento['tipo_movimiento'] == 'Inconformidad por clasificacion') ? 'selected' : ''; ?>>Inconformidad por clasificación</option>
                            <option value="modificacion datos persona" <?php echo ($movimiento['tipo_movimiento'] == 'modificacion datos persona') ? 'selected' : ''; ?>>Modificación datos persona</option>
                            <option value="Retiro ficha" <?php echo ($movimiento['tipo_movimiento'] == 'Retiro ficha') ? 'selected' : ''; ?>>Retiro ficha</option>
                            <option value="Retiro personas" <?php echo ($movimiento['tipo_movimiento'] == 'Retiro personas') ? 'selected' : ''; ?>>Retiro personas</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="num_ficha_encVenta">* No. FICHA o RADICADO:</label>
                        <input type='number' id="num_ficha_encVenta" name='num_ficha_encVenta' class='form-control' value="<?php echo $movimiento['num_ficha_encVenta']; ?>" required />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="integra_encVenta">INTEGRANTES:</label>
                        <input type='number' id='total_integrantes' name='integra_encVenta' class='form-control' value="<?php echo count($integrantes); ?>" readonly />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="sisben_nocturno">* SISBEN NOCTURNO:</label>
                        <select class="form-control" name="sisben_nocturno" id="nocturno">
                            <option value="">SELECCIONE:</option>
                            <option value="SI" <?php echo ($movimiento['sisben_nocturno'] == 'SI') ? 'selected' : ''; ?>>SI</option>
                            <option value="NO" <?php echo ($movimiento['sisben_nocturno'] == 'NO') ? 'selected' : ''; ?>>NO</option>
                        </select>
                    </div>
                </div>
            </div>            <!-- Sección de Gestión de Integrantes -->
            <div class="alert alert-success">
                <h6><i class="fas fa-users me-2"></i>Gestión de Integrantes del Grupo Familiar</h6>
                <p class="mb-0">Aquí puede agregar, editar o eliminar integrantes del grupo familiar asociado a este movimiento.</p>
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
            </div>            <div id="integrantes-container">
                <?php if (!empty($integrantes)): ?>
                    <?php foreach ($integrantes as $index => $integrante): ?>
                    <div class="formulario-dinamico" data-integrante-id="<?php echo $integrante['id_integmov_indep']; ?>">
                        <input type="hidden" name="id_integrante[]" value="<?php echo $integrante['id_integmov_indep']; ?>" />                        <!-- Campo cantidad oculto, siempre valor 1 -->
                        <input type="hidden" name="cant_integMovIndep[]" value="1" />
                        
                        <div class="form-group-dinamico">
                            <label>* Género</label>
                            <select name="gen_integMovIndep[]" class="form-control smaller-input" required>
                                <option value="">Seleccione</option>
                                <option value="M" <?php echo (($integrante['gen_integMovIndep'] ?? '') == 'M') ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo (($integrante['gen_integMovIndep'] ?? '') == 'F') ? 'selected' : ''; ?>>Femenino</option>
                                <option value="O" <?php echo (($integrante['gen_integMovIndep'] ?? '') == 'O') ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>                          <div class="form-group-dinamico">
                            <label>* Rango Edad</label>                            <select name="rango_integMovIndep[]" class="form-control smaller-input" required>
                                <option value="">Seleccione</option>
                                <option value="0 - 6" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '0 - 6') ? 'selected' : ''; ?>>0 - 5</option>
                                <option value="7 - 12" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '7 - 12') ? 'selected' : ''; ?>>6 - 12</option>
                                <option value="13 - 17" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '13 - 17') ? 'selected' : ''; ?>>13 - 17</option>
                                <option value="18 - 28" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '18 - 28') ? 'selected' : ''; ?>>18 - 28</option>
                                <option value="29 - 45" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '29 - 45') ? 'selected' : ''; ?>>29 - 45</option>
                                <option value="46 - 64" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == '46 - 64') ? 'selected' : ''; ?>>46 - 64</option>
                                <option value="Mayor o igual a 65" <?php echo (($integrante['rango_integMovIndep_texto'] ?? '') == 'Mayor o igual a 65') ? 'selected' : ''; ?>>Mayor o igual a 65</option>
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
                                <option value="Negro(a) / Mulato(a) / Afrocolombiano(a)" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Negro(a) / Mulato(a) / Afrocolombiano(a)') ? 'selected' : ''; ?>>Negro(a) / Mulato(a) / Afrocolombiano(a)</option>                                <option value="Raizal" <?php echo (($integrante['grupoEtnico'] ?? '') == 'Raizal') ? 'selected' : ''; ?>>Raizal</option>
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
                                <option value="">Seleccione</option>                                <option value="Si" <?php echo (($integrante['mujerGestante'] ?? '') == 'Si') ? 'selected' : ''; ?>>Si</option>
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
                            <label>Nivel Educativo</label>
                            <select name="nivelEducativo[]" class="form-control smaller-input">
                                <option value="">Seleccione</option>
                                <option value="Ninguno" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                <option value="Preescolar" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Preescolar') ? 'selected' : ''; ?>>Preescolar</option>
                                <option value="Primaria" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Primaria') ? 'selected' : ''; ?>>Primaria</option>                                <option value="Secundaria" <?php echo (($integrante['nivelEducativo'] ?? '') == 'Secundaria') ? 'selected' : ''; ?>>Secundaria</option>
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
                        
                        <button type="button" class="btn btn-danger eliminar-integrante">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="obs_encVenta">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                    <textarea class="form-control" rows="3" name="obs_encVenta" style="text-transform:uppercase;"><?php echo $movimiento['observacion']; ?></textarea>
                </div>
            </div>

            <div class="text-center mb-4">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save me-2"></i> ACTUALIZAR MOVIMIENTO
                </button>
                <a href="showMovimientos.php" class="btn btn-secondary btn-lg ms-2">
                    <i class="fas fa-arrow-left me-2"></i> CANCELAR
                </a>
            </div>
        </form>
    </div>
</body>

</html>
