<?php

session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: index.php");
    exit();  // Asegúrate de salir del script después de redirigir
}

$id_usu     = $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BD SISBEN - Registro de Información</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <!-- jQuery -->
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --info-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }        body {
            background: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin: 20px auto;
            padding: 30px;
        }        .section-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .section-card.info-basica {
            background: white;
            border-left: 4px solid #667eea;
        }

        .section-card.info-basica .section-title {
            background: var(--primary-gradient);
            color: white;
            padding: 20px;
            margin: -25px -25px 25px -25px;
            border-radius: 15px 15px 0 0;
        }

        .section-card.info-basica .section-title i {
            color: white;
            background: none;
            -webkit-background-clip: unset;
            background-clip: unset;
            -webkit-text-fill-color: unset;
        }

        .section-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }

        .section-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
        }

        .section-title i {
            margin-right: 10px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.4rem;
        }

        .form-label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            height: auto;
            min-height: 45px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            background: #6c757d;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 250px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }        .page-title {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .required-note {
            background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            border-left: 4px solid #e17055;
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .spinner-border-sm {
            display: none;
        }

        /* Select2 customization */
        .select2-container--default .select2-selection--single {
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            height: 45px !important;
            padding: 8px 12px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #667eea !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 0 !important;
            color: #495057 !important;
        }

        .select2-dropdown {
            border: 2px solid #667eea !important;
            border-radius: 10px !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .section-card {
                padding: 20px;
            }
        }
    </style><script>        $(document).ready(function() {
            console.log('🚀 Documento cargado, inicializando eventos...');
            $('#btn_ingresar').prop('disabled', false); // Habilitado por defecto
            console.log('✅ Botón habilitado');

            $('#doc_info').on('blur', function() {
                let documento = $(this).val();
                let mensajeDiv = $('#mensaje_documento');
                
                console.log('📄 Verificando documento:', documento);

                if (documento !== '') {
                    console.log('🔍 Realizando búsqueda AJAX...');
                    $.ajax({
                        url: 'verificar_documento.php',
                        method: 'POST',
                        data: {
                            doc_info: documento
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('✅ Respuesta recibida:', response);
                            if (response.status === 'existe') {
                                // Precargar todos los datos del registro más reciente
                                mensajeDiv.html('<div class="alert alert-info">📋 Documento encontrado. Datos precargados del registro más reciente.</div>');
                                console.log('📋 Precargando datos...');
                                
                                // Precargar campos básicos
                                $('#nom_info').val(response.data.nom_info);
                                $('#tipo_documento').val(response.data.tipo_documento);
                                $('#departamento_expedicion').val(response.data.departamento_expedicion);
                                $('#fecha_expedicion').val(response.data.fecha_expedicion);
                                
                                // Precargar campos demográficos
                                $('#gen_integVenta').val(response.data.gen_integVenta);
                                $('#rango_integVenta').val(response.data.rango_integVenta);
                                $('#victima').val(response.data.victima);
                                $('#condicionDiscapacidad').val(response.data.condicionDiscapacidad);
                                $('#tipoDiscapacidad').val(response.data.tipoDiscapacidad);
                                $('#mujerGestante').val(response.data.mujerGestante);
                                $('#cabezaFamilia').val(response.data.cabezaFamilia);
                                $('#orientacionSexual').val(response.data.orientacionSexual);
                                $('#experienciaMigratoria').val(response.data.experienciaMigratoria);
                                $('#grupoEtnico').val(response.data.grupoEtnico);
                                $('#seguridadSalud').val(response.data.seguridadSalud);
                                $('#nivelEducativo').val(response.data.nivelEducativo);
                                $('#condicionOcupacion').val(response.data.condicionOcupacion);
                                $('#obs1_encInfo').val(response.data.observacion);
                                $('#obs2_encInfo').val(response.data.info_adicional);
                                
                                // Manejar discapacidad
                                if (response.data.condicionDiscapacidad === 'Si') {
                                    $('#tipoDiscapacidadContainer').show();
                                } else {
                                    $('#tipoDiscapacidadContainer').hide();
                                }
                                
                                // Cargar municipio si hay departamento
                                if (response.data.departamento_expedicion && response.data.ciudad_expedicion) {
                                    cargarMunicipios(response.data.departamento_expedicion, response.data.ciudad_expedicion);
                                }
                                  } else if (response.status === 'no_existe') {
                                mensajeDiv.html('<div class="alert alert-success">✅ Documento nuevo, puede ingresar la información.</div>');
                                console.log('✅ Documento nuevo, limpiando formulario...');
                                // Limpiar campos pero mantener la fecha actual
                                limpiarFormulario();
                            } else {
                                console.log('⚠️ Respuesta inesperada:', response);
                                mensajeDiv.html('<div class="alert alert-warning">⚠️ Ocurrió un error inesperado.</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error AJAX:', error);
                            console.error('📊 Status:', status);
                            console.error('📝 Response:', xhr.responseText);
                            mensajeDiv.html('<div class="alert alert-warning">❌ Error en la conexión. Revise la consola para más detalles.</div>');
                        }
                    });
                } else {
                    mensajeDiv.html('');
                    limpiarFormulario();
                }
            });
            
            // Función para cargar municipios
            function cargarMunicipios(departamento, ciudadSeleccionada = null) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../obtener_municipios.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const municipios = JSON.parse(xhr.responseText);
                        const ciudadSelect = document.getElementById('ciudad_expedicion');
                        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';
                        
                        municipios.forEach(function(municipio) {
                            const option = document.createElement('option');
                            option.value = municipio.cod_municipio;
                            option.textContent = municipio.nombre_municipio;
                            if (ciudadSeleccionada && municipio.cod_municipio === ciudadSeleccionada) {
                                option.selected = true;
                            }
                            ciudadSelect.appendChild(option);
                        });
                        ciudadSelect.disabled = false;
                    }
                };
                
                xhr.send('cod_departamento=' + departamento);
            }
              // Función para limpiar formulario (excepto fecha y documento)
            function limpiarFormulario() {
                $('#nom_info, #tipo_documento, #departamento_expedicion, #fecha_expedicion').val('');
                $('#gen_integVenta, #rango_integVenta, #victima, #condicionDiscapacidad').val('');
                $('#tipoDiscapacidad, #mujerGestante, #cabezaFamilia, #orientacionSexual').val('');
                $('#experienciaMigratoria, #grupoEtnico, #seguridadSalud, #nivelEducativo').val('');
                $('#condicionOcupacion, #obs1_encInfo, #obs2_encInfo').val('');
                $('#ciudad_expedicion').html('<option value="">Seleccione una ciudad</option>').prop('disabled', true);
                $('#tipoDiscapacidadContainer').hide();
            }
        });

        // Función para ordenar un select
        function ordenarSelect(id_componente) {
            var selectToSort = $('#' + id_componente);
            var optionActual = selectToSort.val();
            selectToSort.html(selectToSort.children('option').sort(function(a, b) {
                return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
            })).val(optionActual);
        }

        $(document).ready(function() {
            // Llamadas a la función de ordenar para distintos selects
            ordenarSelect('tipo_solic_encInfo');
            ordenarSelect('obs1_encInfo');
        });
    </script>
</head>

<body>

    <div class="container-fluid">
        <h1 class="page-title">
            <i class="fas fa-user-plus"></i> Sistema de Información SISBEN
        </h1>
        
        <div class="container main-container">
            <div class="logo-container">
                <img src='../../img/sisben.png' alt="SISBEN Logo" class="img-fluid">
            </div>

            <?php
            include("../../conexion.php");
            date_default_timezone_set("America/Bogota");

            //traer todos los departamentos
            $sql = "SELECT * FROM departamentos ORDER BY nombre_departamento ASC";
            $resultado = mysqli_query($mysqli, $sql);
            $departamentos = [];
            while ($row = mysqli_fetch_assoc($resultado)) {
                $departamentos[] = $row;
            }
            $id_usu  = $_GET['id_usu'];
            if (isset($_GET['id_usu'])) {
                $sql = mysqli_query($mysqli, "SELECT * FROM usuarios WHERE id_usu = '$id_usu'");
                $row = mysqli_fetch_array($sql);
            }
            ?>

            <form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">
                  <!-- Información Básica -->
                <div class="section-card info-basica">
                    <h2 class="section-title">
                        <i class="fas fa-user-circle"></i>
                        Información Básica del Ciudadano
                    </h2>
                    
                    <div class="required-note">
                        <i class="fas fa-exclamation-circle text-warning me-2"></i>
                        <strong>Nota:</strong> Los campos marcados con (*) son obligatorios
                    </div>

                    <div id="mensaje_documento" class="mb-3"></div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="fec_reg_info" class="form-label">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>
                                * Fecha de Registro
                            </label>
                            <input type='date' name='fec_reg_info' value="<?php echo date('Y-m-d'); ?>" class='form-control' id="fec_reg_info" required autofocus />
                        </div>
                        <div class="col-md-3">
                            <label for="doc_info" class="form-label">
                                <i class="fas fa-id-card text-primary me-1"></i>
                                * Número de Documento
                            </label>
                            <input type='number' name='doc_info' class='form-control' id="doc_info" required placeholder="Ingrese número de documento" />
                        </div>
                        <div class="col-md-6">
                            <label for="tipo_documento" class="form-label">
                                <i class="fas fa-file-alt text-primary me-1"></i>
                                * Tipo de Documento
                            </label>
                            <select name="tipo_documento" class="form-select" id="tipo_documento" required>
                                <option value="">Seleccione tipo de documento</option>
                                <option value="cedula">Cédula de Ciudadanía</option>
                                <option value="ppt">PPT</option>
                                <option value="cedula_extranjeria">Cédula de Extranjería</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <label for="departamento_expedicion" class="form-label">
                                <i class="fas fa-map-marked-alt text-primary me-1"></i>
                                * Departamento de Expedición
                            </label>
                            <select class="form-select" name="departamento_expedicion" id="departamento_expedicion" required>
                                <option value="">Seleccione un departamento</option>
                                <?php
                                foreach ($departamentos as $departamento) {
                                    echo "<option value='{$departamento['cod_departamento']}'>{$departamento['nombre_departamento']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="ciudad_expedicion" class="form-label">
                                <i class="fas fa-city text-primary me-1"></i>
                                * Municipio de Expedición
                            </label>
                            <select id="ciudad_expedicion" name="ciudad_expedicion" class="form-select" disabled required>
                                <option value="">Seleccione una ciudad</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_expedicion" class="form-label">
                                <i class="fas fa-calendar-check text-primary me-1"></i>
                                * Fecha de Expedición
                            </label>
                            <input type='date' name='fecha_expedicion' id='fecha_expedicion' class='form-control' required />
                        </div>
                        <div class="col-md-3">
                            <label for="nom_info" class="form-label">
                                <i class="fas fa-user text-primary me-1"></i>
                                * Nombres Completos
                            </label>
                            <input type='text' name='nom_info' id='nom_info' class='form-control' required style="text-transform:uppercase;" placeholder="Nombres completos" />
                        </div>
                    </div>
                </div>                <!-- Información Demográfica -->
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-users"></i>
                        Información Demográfica
                    </h2>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="gen_integVenta" class="form-label">
                                <i class="fas fa-venus-mars text-primary me-1"></i>
                                * Identidad de Género
                            </label>
                            <select name="gen_integVenta" class="form-select" id="gen_integVenta" required>
                                <option value="">Seleccione una opción</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="OTRO">Otro</option>
                            </select> 
                        </div>
                        <div class="col-md-3">
                            <label for="rango_integVenta" class="form-label">
                                <i class="fas fa-birthday-cake text-primary me-1"></i>
                                * Rango de Edad
                            </label>
                            <select name="rango_integVenta" class="form-select" id="rango_integVenta" required>
                                <option value="">Seleccione rango</option>
                                <option value="0 - 6">0 - 6 años</option>
                                <option value="7 - 12">7 - 12 años</option>
                                <option value="13 - 17">13 - 17 años</option>
                                <option value="18 - 28">18 - 28 años</option>
                                <option value="29 - 45">29 - 45 años</option>
                                <option value="46 - 64">46 - 64 años</option>
                                <option value="Mayor o igual a 65">Mayor o igual a 65 años</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="victima" class="form-label">
                                <i class="fas fa-shield-alt text-primary me-1"></i>
                                * Víctima del Conflicto
                            </label>
                            <select name="victima" class="form-select" id="victima" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="condicionDiscapacidad" class="form-label">
                                <i class="fas fa-wheelchair text-primary me-1"></i>
                                * Condición de Discapacidad
                            </label>
                            <select name="condicionDiscapacidad" class="form-select" id="condicionDiscapacidad" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2" id="tipoDiscapacidadContainer" style="display: none;">
                        <div class="col-md-4">
                            <label for="tipoDiscapacidad" class="form-label">
                                <i class="fas fa-list-alt text-primary me-1"></i>
                                * Tipo de Discapacidad
                            </label>
                            <select class="form-select" name="tipoDiscapacidad" id="tipoDiscapacidad">
                                <option value="">Seleccione tipo</option>
                                <option value="Auditiva">Auditiva</option>
                                <option value="Fisica">Física</option>
                                <option value="Intelectual">Intelectual</option>
                                <option value="Multiple">Múltiple</option>
                                <option value="Psicosocial">Psicosocial</option>
                                <option value="SordoCeguera">Sordoceguera</option>
                                <option value="Visual">Visual</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Información Social -->
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-home"></i>
                        Información Social y Familiar
                    </h2>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="mujerGestante" class="form-label">
                                <i class="fas fa-baby text-primary me-1"></i>
                                * Mujer Gestante/Lactante
                            </label>
                            <select name="mujerGestante" class="form-select" id="mujerGestante" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="cabezaFamilia" class="form-label">
                                <i class="fas fa-user-tie text-primary me-1"></i>
                                * Cabeza de Familia
                            </label>
                            <select name="cabezaFamilia" class="form-select" id="cabezaFamilia" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="orientacionSexual" class="form-label">
                                <i class="fas fa-heart text-primary me-1"></i>
                                * Orientación Sexual
                            </label>
                            <select name="orientacionSexual" class="form-select" id="orientacionSexual" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Asexual">Asexual</option>
                                <option value="Bisexual">Bisexual</option>
                                <option value="Homosexual">Homosexual</option>
                                <option value="Heterosexual">Heterosexual</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label for="experienciaMigratoria" class="form-label">
                                <i class="fas fa-globe-americas text-primary me-1"></i>
                                * Experiencia Migratoria
                            </label>
                            <select name="experienciaMigratoria" class="form-select" id="experienciaMigratoria" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="grupoEtnico" class="form-label">
                                <i class="fas fa-users-cog text-primary me-1"></i>
                                * Grupo Étnico
                            </label>
                            <select name="grupoEtnico" class="form-select" id="grupoEtnico" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Indigena">Indígena</option>
                                <option value="ROM (Gitano)">ROM (Gitano)</option>
                                <option value="Raizal">Raizal</option>
                                <option value="Palanquero de San Basilio">Palanquero de San Basilio</option>
                                <option value="Negro(a), Mulato(a), Afrocolobiano(a)">Negro(a), Mulato(a), Afrocolombiano(a)</option>
                                <option value="Mestizo">Mestizo</option>
                                <option value="Ninguno">Ninguno</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="seguridadSalud" class="form-label">
                                <i class="fas fa-medical-kit text-primary me-1"></i>
                                * Tipo de Seguridad en Salud
                            </label>
                            <select name="seguridadSalud" class="form-select" id="seguridadSalud" required>
                                <option value="">Seleccione una opción</option>
                                <option value="Regimen Contributivo">Régimen Contributivo</option>
                                <option value="Regimen Subsidiado">Régimen Subsidiado</option>
                                <option value="Poblacion Vinculada">Población Vinculada</option>
                                <option value="Ninguno">Ninguno</option>
                            </select>
                        </div>
                    </div>
                </div>                <!-- Información Educativa y Laboral -->
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Información Educativa y Laboral
                    </h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nivelEducativo" class="form-label">
                                <i class="fas fa-book text-primary me-1"></i>
                                * Nivel Educativo
                            </label>
                            <select name="nivelEducativo" class="form-select" id="nivelEducativo" required>
                                <option value="">Seleccione nivel educativo</option>
                                <option value="Preescolar">Preescolar</option>
                                <option value="Basica Primaria">Básica Primaria</option>
                                <option value="Basica Secundaria">Básica Secundaria</option>
                                <option value="Media Academica o clasica">Media Académica o Clásica</option>
                                <option value="Media Tecnica">Media Técnica</option>
                                <option value="Normalista">Normalista</option>
                                <option value="Universitario">Universitario</option>
                                <option value="Tecnico profesional">Técnico Profesional</option>
                                <option value="Tecnologo">Tecnólogo</option>
                                <option value="Profesional">Profesional</option>
                                <option value="Especializacion">Especialización</option>
                                <option value="Maestria">Maestría</option>
                                <option value="Doctorado">Doctorado</option>
                                <option value="Ninguno">Ninguno</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="condicionOcupacion" class="form-label">
                                <i class="fas fa-briefcase text-primary me-1"></i>
                                * Condición de Ocupación
                            </label>
                            <select name="condicionOcupacion" class="form-select" id="condicionOcupacion" required>
                                <option value="">Seleccione condición</option>
                                <option value="Ama de Casa">Ama de Casa</option>
                                <option value="Buscando Empleo">Buscando Empleo</option>
                                <option value="Desempleado(a)">Desempleado(a)</option>
                                <option value="Empleado(a)">Empleado(a)</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Independiente">Independiente</option>
                                <option value="Pensionado(a)">Pensionado(a)</option>
                                <option value="Ninguno">Ninguno</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="section-card">
                    <h2 class="section-title">
                        <i class="fas fa-clipboard-list"></i>
                        Información del Servicio
                    </h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="obs1_encInfo" class="form-label">
                                <i class="fas fa-info-circle text-primary me-1"></i>
                                * Tipo de Información Brindada
                            </label>
                            <select class="form-select" name="obs1_encInfo" id="obs1_encInfo" required>
                                <option value="">Seleccione tipo de información</option>
                                <option value="ACTUALIZACION">Actualización</option>
                                <option value="CLASIFICACION">Clasificación</option>
                                <option value="DIRECCION">Dirección</option>
                                <option value="DOCUMENTO">Documento</option>
                                <option value="INCLUSION">Inclusión</option>
                                <option value="PENDIENTE">Pendiente</option>
                                <option value="VERIFICACION">Verificación</option>
                                <option value="VISITA">Visita</option>
                                <option value="CALIDAD DE LA ENCUESTA">Calidad de la Encuesta</option>
                                <option value="ATENCION">Atención</option>
                            </select>
                        </div>
                        <div class="col-md-6" style="display: none;">
                            <label for="tipo_solic_encInfo" class="form-label">UD:</label>
                            <select class="form-select" name="tipo_solic_encInfo" id="tipo_solic_encInfo">
                                <option value="ATENCION">ATENCION</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label for="obs2_encInfo" class="form-label">
                                <i class="fas fa-edit text-primary me-1"></i>
                                Información Adicional
                            </label>
                            <textarea class="form-control" id="obs2_encInfo" rows="4" name="obs2_encInfo" 
                                      style="text-transform:uppercase;" 
                                      placeholder="Ingrese información adicional o comentarios relevantes..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="section-card">
                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <button type="submit" class="btn btn-primary btn-lg" id="btn_ingresar">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            <i class="fas fa-save me-2"></i>
                            Ingresar Información
                        </button>

                        <button type="button" class="btn btn-outline-secondary btn-lg" onclick="history.back();">
                            <i class="fas fa-arrow-left me-2"></i>
                            Regresar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</body>
<script>
    document.getElementById("condicionDiscapacidad").addEventListener("change", function() {
        var tipoDiscapacidadContainer = document.getElementById("tipoDiscapacidadContainer");
        if (this.value === "Si") {
            tipoDiscapacidadContainer.style.display = "block";
        } else {
            tipoDiscapacidadContainer.style.display = "none";
            document.getElementById("tipoDiscapacidad").value = ""; // Reiniciar selección
        }
    });    document.addEventListener('DOMContentLoaded', function() {
        const departamentoSelect = document.getElementById('departamento_expedicion');
        const ciudadSelect = document.getElementById('ciudad_expedicion');

        departamentoSelect.addEventListener('change', function() {
            console.log('Departamento seleccionado:', this.value);
            const departamento = this.value;

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
                        ciudadSelect.appendChild(option);
                    });
                    ciudadSelect.disabled = false;
                } else {
                    console.error('Error al cargar municipios');
                }
            };

            xhr.send('cod_departamento=' + departamento);
        });
    });
</script>

</html>