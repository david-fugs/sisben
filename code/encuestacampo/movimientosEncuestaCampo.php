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

// Obtener departamentos
include('../../conexion.php');

// Establecer charset UTF-8 para manejar tildes y ñ
mysqli_set_charset($mysqli, "utf8");

$query_departamentos = "SELECT cod_departamento, nombre_departamento FROM departamentos ORDER BY nombre_departamento";
$result_departamentos = mysqli_query($mysqli, $query_departamentos);
$departamentos = [];
while ($row = mysqli_fetch_assoc($result_departamentos)) {
    $departamentos[] = $row;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BD SISBEN - Movimientos Encuesta Campo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../barrios.js"></script>
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            padding: 2rem;
            margin: 2rem 0;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #007bff;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.75rem;
            transition: all 0.3s ease;
            min-height: 48px;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #a71e2a 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .header-info {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .integrantes-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            border-left: 4px solid #28a745;
        }

        .formulario-dinamico {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .formulario-dinamico:hover {
            border-color: #007bff;
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.1);
        }

        .integrante-header {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            margin: -1.5rem -1.5rem 1rem -1.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px 10px 0 0;
            font-weight: 600;
        }

        .form-row-custom {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group-dinamico {
            display: flex;
            flex-direction: column;
        }

        .form-group-dinamico label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .smaller-input {
            font-size: 0.9rem;
        }

        .tipo-discapacidad {
            transition: all 0.3s ease;
        }

        .eliminar-integrante {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #dc3545;
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select2-container .select2-selection--single {
            height: 48px !important;
            border-radius: 8px !important;
            border: 1px solid #ced4da !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 46px !important;
            padding-left: 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
        }

        .agregar-integrantes-section {
            background: #e7f3ff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px dashed #007bff;
        }

        .alert-ficha-retirada {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            border: 2px solid #dc3545;
            border-radius: 10px;
            color: #721c24;
            padding: 1rem;
            font-weight: 600;
        }

        .readonly-integrante {
            background-color: #f8f9fa !important;
            border: 2px solid #28a745 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento_expedicion');
            const ciudadSelect = document.getElementById('ciudad_expedicion');

            window.ciudadSeleccionada = null;

            window.cargarMunicipios = function(departamento, ciudadPreseleccionada = null) {
                ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

                if (departamento === '' || !departamento) {
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

                        // Seleccionar la ciudad preseleccionada
                        if (ciudadPreseleccionada) {
                            ciudadSelect.value = ciudadPreseleccionada;
                            window.ciudadSeleccionada = null;
                        } else if (window.ciudadSeleccionada) {
                            ciudadSelect.value = window.ciudadSeleccionada;
                            window.ciudadSeleccionada = null;
                        }
                    }
                };

                xhr.send('departamento=' + departamento);
            }

            departamentoSelect.addEventListener('change', function() {
                cargarMunicipios(this.value);
            });

            // Cargar municipios si hay un departamento preseleccionado
            if (departamentoSelect.value) {
                cargarMunicipios(departamentoSelect.value);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            function actualizarTotal() {
                var total = 0;
                $("input[name='cant_integVenta[]']").each(function() {
                    var valor = parseInt($(this).val()) || 0;
                    total += valor;
                });
                $("#total_integrantes").val(total);
                $("#integra_encVenta").val(total);
            }

            $("#agregar").click(function() {
                var inputCantidad = $("#cant_integVenta");
                var cantidad = parseInt(inputCantidad.val()) || 0;

                if (cantidad <= 0 || cantidad > 20) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad inválida',
                        text: 'Por favor ingrese una cantidad entre 1 y 20',
                        confirmButtonColor: '#007bff'
                    });
                    return;
                }

                for (var i = 0; i < cantidad; i++) {
                    var integranteDiv = $("<div>").addClass("formulario-dinamico");

                    var fieldsContainer = $("<div>").addClass("form-row-custom");

                    var cantidadInput = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "cant_integVenta[]")
                        .val(1);

                    var generoGroup = $("<div>").addClass("form-group-dinamico");
                    generoGroup.append($("<label>").text("Identidad de Género"));
                    var generoSelect = $("<select>")
                        .attr("name", "gen_integVenta[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Identidad Genero</option>')
                        .append('<option value="F">F</option>')
                        .append('<option value="M">M</option>')
                        .append('<option value="O">Otro</option>');
                    generoGroup.append(generoSelect);
                    fieldsContainer.append(generoGroup);

                    var rangoEdadGroup = $("<div>").addClass("form-group-dinamico");
                    rangoEdadGroup.append($("<label>").text("Rango de Edad"));
                    var rangoEdadSelect = $("<select>")
                        .attr("name", "rango_integVenta[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Rango Edad</option>')
                        .append('<option value="0 - 6">0 - 5</option>')
                        .append('<option value="6 - 12">6 - 12</option>')
                        .append('<option value="13 - 17">13 - 17</option>')
                        .append('<option value="18 - 28">18 - 28</option>')
                        .append('<option value="29 - 45">29 - 45</option>')
                        .append('<option value="46 - 64">46 - 64</option>')
                        .append('<option value="Mayor o igual a 65">Mayor o igual a 65</option>');
                    rangoEdadGroup.append(rangoEdadSelect);
                    fieldsContainer.append(rangoEdadGroup);

                    var orientacionGroup = $("<div>").addClass("form-group-dinamico");
                    orientacionGroup.append($("<label>").text("Orientación Sexual"));
                    var OrientacionSexual = $("<select>")
                        .attr("name", "orientacionSexual[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Orientacion Sexual</option>')
                        .append('<option value="Asexual">Asexual</option>')
                        .append('<option value="Bisexual">Bisexual</option>')
                        .append('<option value="Heterosexual">Heterosexual</option>')
                        .append('<option value="Homosexual">Homosexual</option>')
                        .append('<option value="Otro">Otro</option>');
                    orientacionGroup.append(OrientacionSexual);
                    fieldsContainer.append(orientacionGroup);

                    var discapacidadGroup = $("<div>").addClass("form-group-dinamico");
                    discapacidadGroup.append($("<label>").text("Condición de Discapacidad"));
                    var condicionDiscapacidad = $("<select>")
                        .attr("name", "condicionDiscapacidad[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Condicion Discapacidad</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    discapacidadGroup.append(condicionDiscapacidad);
                    fieldsContainer.append(discapacidadGroup);

                    var tipoDiscapacidadGroup = $("<div>").addClass("form-group-dinamico");
                    tipoDiscapacidadGroup.append($("<label>").text("Tipo de Discapacidad"));
                    var discapacidadSelect = $("<select>")
                        .attr("name", "tipoDiscapacidad[]")
                        .addClass("form-control smaller-input tipo-discapacidad")
                        .append('<option value="">Tipo Discapacidad</option>')
                        .append('<option value="Auditiva">Auditiva</option>')
                        .append('<option value="Física">Física</option>')
                        .append('<option value="Intelectual">Intelectual</option>')
                        .append('<option value="Múltiple">Múltiple</option>')
                        .append('<option value="Psicosocial">Psicosocial</option>')
                        .append('<option value="Sordoceguera">Sordoceguera</option>')
                        .append('<option value="Visual">Visual</option>')
                        .hide();
                    tipoDiscapacidadGroup.append(discapacidadSelect);
                    fieldsContainer.append(tipoDiscapacidadGroup);

                    var grupoEtnicoGroup = $("<div>").addClass("form-group-dinamico");
                    grupoEtnicoGroup.append($("<label>").text("Grupo Étnico"));
                    var GrupoEtnico = $("<select>")
                        .attr("name", "grupoEtnico[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Grupo Etnico</option>')
                        .append('<option value="Indigena">Indigena</option>')
                        .append('<option value="Negro(a) / Mulato(a) / Afrocolombiano(a)">Negro(a) / Mulato(a) / Afrocolombiano(a)</option>')
                        .append('<option value="Raizal">Raizal</option>')
                        .append('<option value="Palenquero de San Basilio">Palenquero de San Basilio</option>')
                        .append('<option value="Mestizo">Mestizo</option>')
                        .append('<option value="Gitano (rom)">Gitano (rom)</option>')
                        .append('<option value="Ninguno">Ninguno</option>');
                    grupoEtnicoGroup.append(GrupoEtnico);
                    fieldsContainer.append(grupoEtnicoGroup);

                    var victimaGroup = $("<div>").addClass("form-group-dinamico");
                    victimaGroup.append($("<label>").text("¿Es víctima?"));
                    var victima = $("<select>")
                        .attr("name", "victima[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Victima</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    victimaGroup.append(victima);
                    fieldsContainer.append(victimaGroup);

                    var mujerGestanteGroup = $("<div>").addClass("form-group-dinamico");
                    mujerGestanteGroup.append($("<label>").text("¿Mujer gestante?"));
                    var mujerGestante = $("<select>")
                        .attr("name", "mujerGestante[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Mujer Gestante</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    mujerGestanteGroup.append(mujerGestante);
                    fieldsContainer.append(mujerGestanteGroup);

                    var cabezaFamiliaGroup = $("<div>").addClass("form-group-dinamico");
                    cabezaFamiliaGroup.append($("<label>").text("¿Cabeza de familia?"));
                    var cabezaFamilia = $("<select>")
                        .attr("name", "cabezaFamilia[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Hombre / mujer Cabeza de Familia</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    cabezaFamiliaGroup.append(cabezaFamilia);
                    fieldsContainer.append(cabezaFamiliaGroup);

                    var experienciaMigratoriaGroup = $("<div>").addClass("form-group-dinamico");
                    experienciaMigratoriaGroup.append($("<label>").text("¿Tiene experiencia migratoria?"));
                    var experienciaMigratoria = $("<select>")
                        .attr("name", "experienciaMigratoria[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Experiencia Migratoria</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    experienciaMigratoriaGroup.append(experienciaMigratoria);
                    fieldsContainer.append(experienciaMigratoriaGroup);

                    var seguridadSaludGroup = $("<div>").addClass("form-group-dinamico");
                    seguridadSaludGroup.append($("<label>").text("Seguridad en salud"));
                    var seguridadSalud = $("<select>")
                        .attr("name", "seguridadSalud[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Seguridad Salud</option>')
                        .append('<option value="Regimen Contributivo">Regimen Contributivo</option>')
                        .append('<option value="Regimen Subsidiado">Regimen Subsidiado</option>')
                        .append('<option value="Poblacion Vinculada">Poblacion Vinculada</option>')
                        .append('<option value="Ninguno">Ninguno</option>');
                    seguridadSaludGroup.append(seguridadSalud);
                    fieldsContainer.append(seguridadSaludGroup);

                    var nivelEducativoGroup = $("<div>").addClass("form-group-dinamico");
                    nivelEducativoGroup.append($("<label>").text("Nivel educativo"));
                    var nivelEducativo = $("<select>")
                        .attr("name", "nivelEducativo[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Nivel Educativo</option>')
                        .append('<option value="Ninguno">Ninguno</option>')
                        .append('<option value="Preescolar">Preescolar</option>')
                        .append('<option value="Primaria">Primaria</option>')
                        .append('<option value="Secundaria">Secundaria</option>')
                        .append('<option value="Media Academica o Clasica">Media Academica o Clasica</option>')
                        .append('<option value="Media Tecnica">Media Tecnica</option>')
                        .append('<option value="Normalista">Normalista</option>')
                        .append('<option value="Universitario">Universitario</option>')
                        .append('<option value="Tecnica Profesional">Tecnica Profesional</option>')
                        .append('<option value="Tecnologica">Tecnologica</option>')
                        .append('<option value="Profesional">Profesional</option>')
                        .append('<option value="Especializacion">Especializacion</option>');
                    nivelEducativoGroup.append(nivelEducativo);
                    fieldsContainer.append(nivelEducativoGroup);

                    var condicionOcupacionGroup = $("<div>").addClass("form-group-dinamico");
                    condicionOcupacionGroup.append($("<label>").text("Condición de ocupación"));
                    var condicionOcupacion = $("<select>")
                        .attr("name", "condicionOcupacion[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Condicion Ocupacion</option>')
                        .append('<option value="Ama de casa">Ama de casa</option>')
                        .append('<option value="Buscando Empleo">Buscando Empleo</option>')
                        .append('<option value="Desempleado(a)">Desempleado(a)</option>')
                        .append('<option value="Empleado(a)">Empleado(a)</option>')
                        .append('<option value="Independiente">Independiente</option>')
                        .append('<option value="Estudiante">Estudiante</option>')
                        .append('<option value="Pensionado">Pensionado</option>')
                        .append('<option value="Ninguno">Ninguno</option>');
                    condicionOcupacionGroup.append(condicionOcupacion);
                    fieldsContainer.append(condicionOcupacionGroup);

                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger eliminar-integrante")
                        .text("×")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            actualizarTotal();
                        });

                    condicionDiscapacidad.on("change", function() {
                        var currentDiscapacidadSelect = $(this).closest('.formulario-dinamico').find('.tipo-discapacidad');
                        if ($(this).val() === "Si") {
                            currentDiscapacidadSelect.show();
                        } else {
                            currentDiscapacidadSelect.hide();
                        }
                    });

                    integranteDiv.append(cantidadInput);
                    integranteDiv.append(fieldsContainer);
                    integranteDiv.append(eliminarBtn);

                    $("#integrantes-container").append(integranteDiv);
                }

                actualizarTotal();
                inputCantidad.val("");
            });

            window.actualizarTotal = actualizarTotal;
        });
    </script>
</head>

<body>
    <div class="container-fluid">
        <div class="text-center mb-4">
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </div>

        <div class="container">
            <div class="main-container">
                <div class="header-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1"><i class="fas fa-exchange-alt me-2"></i>Movimientos de Encuesta Campo</h4>
                            <p class="mb-0">Control y seguimiento de movimientos de encuestas de campo</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <p class="mb-0">Usuario: <strong><?php echo $nombre; ?></strong></p>
                            <small>Fecha: <?php echo date('d/m/Y H:i'); ?></small>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarFormulario() {
                        $("#nom_encCampo, #dir_encCampo, #fecha_expedicion, #num_ficha_encCampo, #otro_bar_ver_encCampo, #obs_encCampo").val("");
                        $("#tipo_documento, #departamento_expedicion, #zona_encCampo, #selectEF").val("");
                        $("#ciudad_expedicion").empty().append('<option value="">Seleccione un municipio</option>').prop('disabled', true);
                        $("#id_comunas").empty().append('<option value="">Seleccione comuna</option>').prop('disabled', true);
                        $("#id_barrios").val(null).trigger('change');
                        $("#fec_reg_encCampo").val("<?php echo date('Y-m-d'); ?>");
                        $("#fecha_nacimiento").val("");
                        $("#integrantes-container").empty();
                        $("#total_integrantes, #cant_integVenta").val("");
                        $("#otro_barrio_container").hide();
                        $("#btnEnviar").prop("disabled", false);
                    }

                    $(document).ready(function() {
                        $("#doc_encCampo").on("blur", function() {
                            let documento = $(this).val();
                            let mensajeContainer = $("#mensajeDocumentoContainer");

                            if (documento !== "") {
                                limpiarFormulario();

                                $.ajax({
                                    url: "verificar_encuesta_campo.php",
                                    type: "POST",
                                    data: {
                                        doc_encCampo: documento
                                    },
                                    dataType: "json",
                                    beforeSend: function() {
                                        mensajeContainer.removeClass("alert-danger alert-success alert-warning").addClass("alert d-none").html("");
                                    },
                                    success: function(response) {
                                        console.log("✅ Respuesta del servidor:", response);

                                        if (response.status === "ficha_retirada") {
                                            var cleanMessage = response.message.replace(/No se pueden realizar movimientos\.?/i, '').trim();
                                            if (cleanMessage === '') {
                                                cleanMessage = '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA.';
                                            }
                                            mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger").html(cleanMessage);
                                            $("#btnEnviar").prop("disabled", true);
                                        }

                                        if (response.status === "existe" || response.status === "ficha_retirada") {
                                            $("#nom_encCampo").val(response.data.nom_encCampo || "");
                                            $("#tipo_documento").val(response.data.tipo_documento || "");
                                            $("#fecha_expedicion").val(response.data.fecha_expedicion || "");
                                            $("#fecha_nacimiento").val(response.data.fecha_nacimiento || "");
                                            $("#dir_encCampo").val(response.data.dir_encCampo || "");
                                            $("#zona_encCampo").val(response.data.zona_encCampo || "");
                                            $("#num_ficha_encCampo").val(response.data.num_ficha_encCampo || "");
                                            $("#obs_encCampo").val(response.data.obs_encCampo || "");

                                            // Cargar departamento y municipio
                                            if (response.data.departamento_expedicion) {
                                                $("#departamento_expedicion").val(response.data.departamento_expedicion);
                                                
                                                // Cargar municipios del departamento con el municipio preseleccionado
                                                if (typeof window.cargarMunicipios === 'function') {
                                                    window.cargarMunicipios(response.data.departamento_expedicion, response.data.ciudad_expedicion);
                                                }
                                            }

                                            if (response.data.id_bar) {
                                                var newOption = new Option(response.data.nombre_barrio, response.data.id_bar, true, true);
                                                $('#id_barrios').append(newOption).trigger('change');

                                                setTimeout(function() {
                                                    if (response.data.id_com) {
                                                        $('#id_comunas').load('../comunaGet.php?id_barrio=' + response.data.id_bar, function() {
                                                            $('#id_comunas').val(response.data.id_com);
                                                            $('#id_comunas').prop('disabled', false);
                                                        });
                                                    }
                                                }, 300);
                                            }

                                            if (response.integrantes && response.integrantes.length > 0) {
                                                response.integrantes.forEach(function(integ, index) {
                                                    crearIntegranteReadOnly(integ, index + 1);
                                                });
                                                actualizarTotal();
                                            }
                                        } else if (response.status === "no_existe") {
                                            mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                                .html("⚠️ El documento no está registrado. Puede crear un nuevo movimiento.");
                                            $("#btnEnviar").prop("disabled", false);
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger")
                                            .html("❌ Error en la consulta. Intente nuevamente.");
                                        $("#btnEnviar").prop("disabled", false);
                                    }
                                });
                            } else {
                                limpiarFormulario();
                                mensajeContainer.addClass("d-none").html("");
                                $("#btnEnviar").prop("disabled", false);
                            }
                        });
                    });

                    function crearIntegranteReadOnly(integ, numero) {
                        var integranteDiv = $("<div>").addClass("formulario-dinamico readonly-integrante")
                            .css({
                                'background': '#f8f9fa',
                                'border': '2px solid #28a745',
                                'border-radius': '10px',
                                'padding': '1.5rem',
                                'margin-bottom': '1.5rem',
                                'position': 'relative'
                            });

                        var header = $("<div>").addClass("integrante-header")
                            .html('<i class="fas fa-user-check text-success"></i> Integrante ' + numero + ' (Precargado)')
                            .css({
                                'color': '#28a745',
                                'font-weight': 'bold',
                                'margin-bottom': '1.5rem',
                                'font-size': '1.1rem'
                            });

                        var fieldsContainer = $("<div>").addClass("row");

                        var cantidadInput = $("<input>")
                            .attr("type", "hidden")
                            .attr("name", "cant_integVenta[]")
                            .val(1);

                        function createReadOnlyField(label, value, colSize = "col-md-4") {
                            var group = $("<div>").addClass("form-group mb-3 " + colSize);
                            group.append($("<label>").text(label).css({
                                'font-weight': '600',
                                'color': '#495057',
                                'margin-bottom': '0.5rem'
                            }));
                            var input = $("<input>")
                                .addClass("form-control form-control-lg")
                                .attr("readonly", true)
                                .val(value || 'No especificado')
                                .css({
                                    'background-color': '#ffffff',
                                    'border': '2px solid #28a745',
                                    'color': '#495057',
                                    'font-weight': '500',
                                    'font-size': '0.95rem'
                                });
                            group.append(input);
                            return group;
                        }

                        var fila1 = $("<div>").addClass("row");
                        fila1.append(createReadOnlyField("Identidad de Género", integ.gen_integCampo, "col-md-4"));
                        fila1.append(createReadOnlyField("Rango de Edad", integ.rango_integCampo, "col-md-4"));
                        fila1.append(createReadOnlyField("Orientación Sexual", integ.orientacionSexual, "col-md-4"));

                        var fila2 = $("<div>").addClass("row");
                        fila2.append(createReadOnlyField("Condición Discapacidad", integ.condicionDiscapacidad, "col-md-4"));
                        if (integ.condicionDiscapacidad === 'Si') {
                            fila2.append(createReadOnlyField("Tipo Discapacidad", integ.tipoDiscapacidad, "col-md-4"));
                            fila2.append(createReadOnlyField("Grupo Étnico", integ.grupoEtnico, "col-md-4"));
                        } else {
                            fila2.append(createReadOnlyField("Grupo Étnico", integ.grupoEtnico, "col-md-8"));
                        }

                        var fila3 = $("<div>").addClass("row");
                        fila3.append(createReadOnlyField("¿Es víctima?", integ.victima, "col-md-3"));
                        fila3.append(createReadOnlyField("¿Mujer gestante?", integ.mujerGestante, "col-md-3"));
                        fila3.append(createReadOnlyField("¿Cabeza de familia?", integ.cabezaFamilia, "col-md-3"));
                        fila3.append(createReadOnlyField("Experiencia migratoria", integ.experienciaMigratoria, "col-md-3"));

                        var fila4 = $("<div>").addClass("row");
                        fila4.append(createReadOnlyField("Seguridad en salud", integ.seguridadSalud, "col-md-6"));
                        fila4.append(createReadOnlyField("Nivel educativo", integ.nivelEducativo, "col-md-6"));

                        var fila5 = $("<div>").addClass("row");
                        fila5.append(createReadOnlyField("Condición de ocupación", integ.condicionOcupacion, "col-md-12"));

                        fieldsContainer.append(fila1, fila2, fila3, fila4, fila5);

                        var badge = $("<div>")
                            .addClass("badge badge-success")
                            .css({
                                'position': 'absolute',
                                'top': '15px',
                                'right': '15px',
                                'font-size': '0.9rem',
                                'padding': '0.5rem 1rem',
                                'border-radius': '20px'
                            })
                            .text("SOLO LECTURA");

                        integranteDiv.append(cantidadInput, header, badge, fieldsContainer);
                        $("#integrantes-container").append(integranteDiv);
                    }
                </script>

                <form id="form_contacto" action='updateEncuestaCampo_movimientos.php' method="POST" enctype="multipart/form-data">

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-user me-2"></i>Datos del Titular</h5>

                        <div id="mensajeDocumentoContainer" class="alert d-none mb-3"></div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="doc_encCampo" class="form-label">Documento <span class="text-danger">*</span></label>
                                    <input type='number' name='doc_encCampo' class='form-control' id="doc_encCampo" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fec_reg_encCampo" class="form-label">Fecha Registro <span class="text-danger">*</span></label>
                                    <input type="date" name="fec_reg_encCampo" class="form-control" id="fec_reg_encCampo"
                                        value="<?php echo date('Y-m-d'); ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo_documento" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                    <select name="tipo_documento" class="form-control" id="tipo_documento" required>
                                        <option value="">Seleccione tipo</option>
                                        <option value="cedula">Cédula</option>
                                        <option value="tarjeta identidad">Tarjeta Identidad</option>
                                        <option value="cedula_extranjeria">Cédula Extranjería</option>
                                        <option value="DNI">DNI</option>
                                        <option value="PASAPORTE">Pasaporte</option>
                                        <option value="SALVO CONDUCTO PARA REFUGIADOS">Salvo Conducto</option>
                                        <option value="PERMISO PERMANENCIA">Permiso Permanencia</option>
                                        <option value="PERMISO DE PROTECCION TEMPORAL">PPT</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-id-card me-2"></i>Información del Documento</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departamento_expedicion" class="form-label">
                                        Departamento Expedición <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="departamento_expedicion" id="departamento_expedicion" required>
                                        <option value="">Seleccione departamento</option>
                                        <?php
                                        foreach ($departamentos as $departamento) {
                                            echo "<option value='{$departamento['cod_departamento']}'>{$departamento['nombre_departamento']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ciudad_expedicion" class="form-label">Municipio Expedición <span class="text-danger">*</span></label>
                                    <select id="ciudad_expedicion" name="ciudad_expedicion" class="form-control" disabled required>
                                        <option value="">Seleccione municipio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_expedicion" class="form-label">Fecha Expedición <span class="text-danger">*</span></label>
                                    <input type='date' name='fecha_expedicion' id="fecha_expedicion" class='form-control' required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type='date' name='fecha_nacimiento' id="fecha_nacimiento" class='form-control' />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-home me-2"></i>Información Personal y Residencia</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nom_encCampo" class="form-label">Nombres Completos <span class="text-danger">*</span></label>
                                    <input type='text' name='nom_encCampo' id="nom_encCampo" class='form-control' required style="text-transform:uppercase;" />
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dir_encCampo" class="form-label">Dirección <span class="text-danger">*</span></label>
                                    <input type='text' name='dir_encCampo' id="dir_encCampo" class='form-control' required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_barrios" class="form-label">Barrio o Vereda <span class="text-danger">*</span></label>
                                    <select id="id_barrios" class="form-control" name="id_bar" required>
                                        <option value="">Seleccione barrio o vereda</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_comunas" class="form-label">Comuna o Corregimiento <span class="text-danger">*</span></label>
                                    <select id="id_comunas" class="form-control" name="id_com" disabled required>
                                        <option value="">Seleccione comuna</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="otro_barrio_container" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="otro_bar_ver_encCampo" class="form-label">Especifique Barrio, Vereda o Invasión</label>
                                    <input type="text" id="otro_bar_ver_encCampo" name="otro_bar_ver_encCampo" class="form-control" placeholder="Ingrese el barrio">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-exchange-alt me-2"></i>Información del Movimiento</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zona_encCampo">* ZONA:</label>
                                    <select id="zona_encCampo" class="form-control" name="zona_encCampo" required>
                                        <option value="">SELECCIONE LA ZONA</option>
                                        <option value="URBANA">URBANA</option>
                                        <option value="RURAL">RURAL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movimientos">* MOVIMIENTOS:</label>
                                    <select class="form-control" name="movimientos" id="selectEF" required>
                                        <option value="">Seleccione movimiento</option>
                                        <option value="inclusion">Inclusión</option>
                                        <option value="Inconformidad por clasificacion">Inconformidad por clasificación</option>
                                        <option value="modificacion datos persona">Modificación datos persona</option>
                                        <option value="Retiro ficha">Retiro ficha</option>
                                        <option value="Retiro personas">Retiro personas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="num_ficha_encCampo">* No. FICHA o RADICADO:</label>
                                    <input type='text' id="num_ficha_encCampo" name='num_ficha_encCampo' class='form-control' required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="integrantes-section">
                        <h5 class="section-title"><i class="fas fa-users me-2"></i>Gestión de Integrantes</h5>

                        <div class="agregar-integrantes-section">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="integra_encCampo">INTEGRANTES:</label>
                                        <input type='number' id='total_integrantes' name='integra_encCampo' class='form-control' value="" readonly />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cant_integVenta">CANTIDAD A AGREGAR:</label>
                                        <input type="number" id="cant_integVenta" name="cant_integVenta" class="form-control" min="1" max="20" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" id="agregar">
                                        <i class="fas fa-plus me-2"></i>Agregar Integrantes
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="integrantes-container"></div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-comment me-2"></i>Observaciones</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="obs_encCampo" class="form-label">Observaciones y/o Comentarios Adicionales</label>
                                    <textarea class="form-control" id="obs_encCampo" rows="4" name="obs_encCampo"
                                        style="text-transform:uppercase;" placeholder="Ingrese observaciones adicionales..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg me-3" id="btnEnviar">
                            <i class="fas fa-save me-2"></i>Guardar Movimiento
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg" onclick="history.back();">
                            <i class="fas fa-arrow-left me-2"></i>Regresar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
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
                minimumInputLength: 0,
                width: '100%'
            });

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
                        }
                    });

                    let selectedText = $("#id_barrios option:selected").text().trim();
                    if (selectedText.toUpperCase() === "OTRO") {
                        $('#otro_barrio_container').show();
                    } else {
                        $('#otro_barrio_container').hide();
                        $('#otro_bar_ver_encCampo').val('');
                    }
                } else {
                    $('#id_comunas').html('<option value="" disabled>Seleccione comuna</option>');
                    $('#id_comunas').prop('disabled', true);
                }
            });
        });
    </script>

</body>

</html>
