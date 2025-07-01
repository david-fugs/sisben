<?php

session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
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
    <title>BD SISBEN</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../barrios.js"> </script>
    <script src="integrantesEncuesta.js" ></script><style>
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

        /* Select nativo simple sin complicaciones */
        select.form-select {
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
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

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #d39e00 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
            color: #212529;
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

        /* Estilos para el dropdown de Select2 */
        .select2-dropdown {
            border-radius: 8px !important;
            border: 1px solid #ced4da !important;
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.15) !important;
        }

        .select2-container--default .select2-results__option {
            padding: 8px 12px !important;
            font-size: 0.9rem;
        }

        .select2-container--default .select2-results__option--highlighted {
            background-color: #007bff !important;
            color: white !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 6px !important;
            border: 1px solid #ced4da !important;
            padding: 8px 12px !important;
        }

        /* Asegurar que el dropdown aparezca correctamente */
        .select2-container--open .select2-dropdown--below {
            border-top: none !important;
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
        }

        /* Limitar altura del dropdown */
        .select2-container--default .select2-results>.select2-results__options {
            max-height: 200px !important;
            overflow-y: auto !important;
        }

        #integrantes-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .agregar-integrantes-section {
            background: #e7f3ff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px dashed #007bff;
        }

        /* Estilos para validación de campos requeridos */
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Animación para campos con error */
        .is-invalid {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 20%, 40%, 60%, 80% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento_expedicion');
            const ciudadSelect = document.getElementById('ciudad_expedicion');

            // Guardamos la ciudad que se debe seleccionar (si existe globalmente)
            let ciudadSeleccionada = null;

            // Función para cargar municipios
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
                            ciudadSelect.appendChild(option);
                        });

                        ciudadSelect.disabled = false;

                        // ✅ Si ya teníamos una ciudad guardada, la seleccionamos
                        if (ciudadSeleccionada) {
                            ciudadSelect.value = ciudadSeleccionada;
                            ciudadSeleccionada = null; // Limpiamos
                        }
                    } else {
                        alert('Error al cargar municipios');
                    }
                };

                xhr.send('cod_departamento=' + departamento);
            }

            // Evento change para el departamento
            departamentoSelect.addEventListener('change', function() {
                //console.log('Departamento seleccionado:', this.value);
                cargarMunicipios(this.value);
            });

            // ✅ Exponemos una función global para seleccionar ciudad desde AJAX
            window.setCiudadSeleccionada = function(ciudad) {
                ciudadSeleccionada = ciudad;
            };
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
            ordenarSelect('selectEF');
            ordenarSelect('selectPC');
            ordenarSelect('selectEncuestador');
            ordenarSelect('id_com');
            ordenarSelect('id_correg');
        });

        $(document).ready(function() {
            function actualizarTotal() {
                let total = 0;
                $("input[name='cant_integVenta[]']").each(function() {
                    let valor = parseInt($(this).val()) || 0;
                    total += valor;
                });
                $("#total_integrantes").val(total);

                // Actualizar también el campo cant_integVenta con la cantidad total
                $("#cant_integVenta").val($("input[name='cant_integVenta[]']").length);
            }

            $("#agregar").click(function() {
                var inputCantidad = $("#cant_integVenta");
                var cantidadValor = parseInt(inputCantidad.val());

                if (!cantidadValor || cantidadValor <= 0) {
                    alert("Por favor, ingresa una cantidad válida de integrantes.");
                    return;
                }                for (var i = 0; i < cantidadValor; i++) {
                    var integranteDiv = $("<div>").addClass("formulario-dinamico");
                    
                    // Agregar header del integrante
                    var integranteHeader = $("<div>").addClass("integrante-header").text("Integrante " + (i + 1));
                    integranteDiv.append(integranteHeader);
                    
                    // Crear contenedor para los campos en grid
                    var fieldsContainer = $("<div>").addClass("form-row-custom");

                    var cantidadInput = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "cant_integVenta[]")
                        .addClass("form-control smaller-input")
                        .val(1) // Por defecto 1 para que se cuente automáticamente
                        .on("input", actualizarTotal)
                        .attr("placeholder", "Cantidad")
                        .attr("readonly", true); // Hacer el campo de solo lectura

                    // Crear grupos de campos con labels
                    var generoGroup = $("<div>").addClass("form-group-dinamico");                    generoGroup.append($("<label>").text("Identidad de Género"));                    var generoSelect = $("<select>")
                        .attr("name", "gen_integVenta[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Identidad Genero</option>')
                        .append('<option value="F">F</option>')
                        .append('<option value="M">M</option>')
                        .append('<option value="O">Otro</option>');
                    generoGroup.append(generoSelect);
                    fieldsContainer.append(generoGroup);

                    var orientacionGroup = $("<div>").addClass("form-group-dinamico");
                    orientacionGroup.append($("<label>").text("Orientación Sexual"));                    var OrientacionSexual = $("<select>")
                        .attr("name", "orientacionSexual[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Orientacion Sexual</option>')
                        .append('<option value="Asexual">Asexual</option>')
                        .append('<option value="Bisexual">Bisexual</option>')
                        .append('<option value="Heterosexual">Heterosexual</option>')
                        .append('<option value="Homosexual">Homosexual</option>')
                        .append('<option value="Otro">Otro</option>');
                    orientacionGroup.append(OrientacionSexual);
                    fieldsContainer.append(orientacionGroup);

                    var rangoEdadGroup = $("<div>").addClass("form-group-dinamico");
                    rangoEdadGroup.append($("<label>").text("Rango de Edad"));                    var rangoEdadSelect = $("<select>")
                        .attr("name", "rango_integVenta[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Rango Edad</option>')
                        .append('<option value="0 - 6">0 - 6</option>')
                        .append('<option value="7 - 12">7 - 12</option>')
                        .append('<option value="13 - 17">13 - 17</option>')
                        .append('<option value="18 - 28">18 - 28</option>')
                        .append('<option value="29 - 45">29 - 45</option>')
                        .append('<option value="46 - 64">46 - 64</option>')
                        .append('<option value="Mayor o igual a 65">Mayor o igual a 65</option>');
                    rangoEdadGroup.append(rangoEdadSelect);
                    fieldsContainer.append(rangoEdadGroup);

                    var discapacidadGroup = $("<div>").addClass("form-group-dinamico");
                    discapacidadGroup.append($("<label>").text("Condición de Discapacidad"));                    var condicionDiscapacidad = $("<select>")
                        .attr("name", "condicionDiscapacidad[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Condicion Discapacidad</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    discapacidadGroup.append(condicionDiscapacidad);
                    fieldsContainer.append(discapacidadGroup);

                    var tipoDiscapacidadGroup = $("<div>").addClass("form-group-dinamico");
                    tipoDiscapacidadGroup.append($("<label>").text("Tipo de Discapacidad"));                    var discapacidadSelect = $("<select>")
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
                        .hide(); // Ocultar por defecto

                    var GrupoEtnico = $("<select>")
                        .attr("name", "grupoEtnico[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Grupo Etnico</option>')
                        .append('<option value="Indigena">Indigena</option>')
                        .append('<option value="Negro(a) / Mulato(a) / Afrocolombiano(a)">Negro(a) / Mulato(a) / Afrocolombiano(a)</option>')
                        .append('<option value="Raizal">Raizal</option>')
                        .append('<option value="Palenquero de San Basilio">Palenquero de San Basilio</option>')
                        .append('<option value="Mestizo">Mestizo</option>')
                        .append('<option value="Gitano (rom)">Gitano (rom)</option>')
                        .append('<option value="Ninguno">Ninguno</option>');

                    var victima = $("<select>")
                        .attr("name", "victima[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Victima</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');                    var mujerGestante = $("<select>")
                        .attr("name", "mujerGestante[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Mujer Gestante</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                        
                    var cabezaFamilia = $("<select>")
                        .attr("name", "cabezaFamilia[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Cabeza de Familia</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var experienciaMigratoria = $("<select>")
                        .attr("name", "experienciaMigratoria[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Experiencia Migratoria</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var seguridadSalud = $("<select>")
                        .attr("name", "seguridadSalud[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Seguridad Salud</option>')
                        .append('<option value="Regimen Contributivo">Regimen Contributivo</option>')
                        .append('<option value="Regimen Subsidiado">Regimen Subsidiado</option>')
                        .append('<option value="Poblacion Vinculada">Poblacion Vinculada</option>')
                        .append('<option value="Ninguno">Ninguno</option>');

                    var nivelEducativo = $("<select>")
                        .attr("name", "nivelEducativo[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
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
                        .append('<option value="Especializacion">Especializacion</option>')

                    var condicionOcupacion = $("<select>")
                        .attr("name", "condicionOcupacion[]")
                        .addClass("form-control smaller-input")
                        .attr("required", true)
                        .append('<option value="">Condicion Ocupacion</option>')
                        .append('<option value="Ama de casa">Ama de casa</option>')
                        .append('<option value="Buscando Empleo">Buscando Empleo</option>')
                        .append('<option value="Desempleado(a)">Desempleado(a)</option>')
                        .append('<option value="Empleado(a)">Empleado(a)</option>')
                        .append('<option value="Independiente">Independiente</option>')
                        .append('<option value="Estudiante">Estudiante</option>')
                        .append('<option value="Pensionado">Pensionado</option>')
                        .append('<option value="Ninguno">Ninguno</option>')

                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger")
                        .text("Eliminar")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            actualizarTotal(); // Esta llamada ahora actualizará ambos campos
                        });

                    // Agregar evento para mostrar/ocultar el select de discapacidad
                    condicionDiscapacidad.on("change", function() {
                        // Encuentra el select de discapacidad específico para este contenedor
                        var currentDiscapacidadSelect = $(this).closest('.formulario-dinamico').find('.tipo-discapacidad');
                        if ($(this).val() === "Si") {
                            currentDiscapacidadSelect.show();
                        } else {
                            currentDiscapacidadSelect.hide();
                        }
                    });

                    integranteDiv.append(cantidadInput, generoSelect, rangoEdadSelect, OrientacionSexual, condicionDiscapacidad, discapacidadSelect, GrupoEtnico, victima, mujerGestante, cabezaFamilia, experienciaMigratoria, seguridadSalud, nivelEducativo, condicionOcupacion, eliminarBtn);
                    $("#integrantes-container").append(integranteDiv);
                }

                actualizarTotal();
            });
        });
    </script>
</head>

<body>
    <style>
        .puntero {
            cursor: pointer;
        }

        .ocultar {
            display: none;
        }

        .encuestador-container {
            max-height: 200px;
            /* Altura máxima del contenedor */
            overflow-y: auto;
            /* Habilita el desplazamiento vertical si el contenido supera la altura máxima */
        }
    </style>

    <center>
        <img src='../../img/sisben.png' width=300 height=185 class="responsive">
    </center>
    <br />

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
    ?>
    <script>
        $(document).ready(function() {
            $("#doc_encVenta").on("blur", function() {
                let documento = $(this).val();
                let mensajeContainer = $("#mensajeDocumentoContainer");

                if (documento !== "") {
                    $.ajax({
                        url: "verificar_documento.php",
                        type: "POST",
                        data: {
                            doc_encVenta: documento
                        },
                        dataType: "json",
                        beforeSend: function() {
                           // console.log("⏳ Consultando en la base de datos...");
                            mensajeContainer.removeClass("alert-danger alert-success alert-warning").addClass("alert d-none").html("");
                        },
                        success: function(response) {
                            console.log("✅ Respuesta del servidor:", response);                            if (response.status === "existe_encuesta") {
                                mensajeContainer.removeClass("d-none alert-danger alert-warning").addClass("alert alert-warning")
                                    .html('<i class="fas fa-exclamation-triangle"></i> <strong>Encuesta Ya Realizada</strong><br>' +
                                          'Esta encuesta ya fue registrada el ' + new Date(response.data.fecha_alta_encVenta).toLocaleDateString() + '. ' +
                                          'Puede ver los datos existentes, crear una nueva entrada, o continuar con el formulario actual.<br>' +
                                          '<button type="button" class="btn btn-info btn-sm mt-2 me-2" onclick="verEncuestaExistente()">Ver Encuesta Existente</button>' +
                                          '<button type="button" class="btn btn-success btn-sm mt-2 me-2" onclick="permitirNuevaEncuesta()">Crear Nueva Encuesta</button>' +
                                          '<button type="button" class="btn btn-primary btn-sm mt-2" onclick="continuarConFormulario()">Continuar de Todas Formas</button>');
                                
                                // Guardar los datos existentes para referencia
                                window.encuestaExistente = response.data;
                                
                                // No deshabilitar el botón de envío, solo mostrar la alerta
                                // $("#btnEnviar").prop("disabled", true);
                                
                                // Llenar los campos con los datos de la encuesta existente para visualización
                                llenarDatosExistentes(response.data);
                                //traer el municipio
                                $.ajax({
                                    url: '../obtener_municipios.php',
                                    type: 'POST',
                                    data: {
                                        cod_departamento: response.data.departamento_expedicion
                                    },
                                    dataType: 'json',
                                    success: function(municipios) {
                                        let ciudadSelect = $("#ciudad_expedicion");
                                        ciudadSelect.empty().append('<option value="">Seleccione un municipio</option>');
                                        $.each(municipios, function(index, municipio) {
                                            ciudadSelect.append(
                                                $('<option>', {
                                                    value: municipio.cod_municipio,
                                                    text: municipio.nombre_municipio,
                                                    selected: municipio.cod_municipio === response.data.ciudad_expedicion
                                                })
                                            );
                                        });
                                        ciudadSelect.prop('disabled', false);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error al obtener municipios:", error);
                                    }
                                });

                                $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                $("#dir_encVenta").val(response.data.dir_encVenta);
                                $("#zona_encVenta").val(response.data.zona_encVenta);
                                $("#num_ficha_encVenta").val(response.data.num_ficha_encVenta);
                                //barrio y comuna
                                // Setear el barrio seleccionado en Select2 (Select2 requiere cargarlo manualmente)
                                $.ajax({
                                    type: 'GET',
                                    url: '../buscar_barrios.php',
                                    data: {
                                        q: '', // esto puede quedar vacío si buscar_barrios.php acepta ID directo
                                        id: response.data.id_bar // puedes necesitar modificar tu script PHP para permitir búsquedas por ID
                                    },
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data.length > 0) {
                                            let barrio = data[0];
                                            let option = new Option(barrio.text, barrio.id, true, true);
                                            $('#id_barrios').append(option).trigger('change');

                                            // Una vez cargado el barrio, obtenemos su comuna
                                            $.ajax({
                                                url: '../comunaGet.php',
                                                type: 'GET',
                                                data: {
                                                    id_barrio: barrio.id
                                                },
                                                success: function(comunasHtml) {
                                                    $('#id_comunas').html(comunasHtml);
                                                    $('#id_comunas').prop('disabled', false);
                                                    $('#id_comunas').val(response.data.id_com);
                                                }
                                            });
                                        }
                                    }
                                });                            } else if (response.status === "existe_info") {
                                mensajeContainer.removeClass("d-none alert-danger alert-warning").addClass("alert alert-success")
                                    .html("✔ Documento encontrado en Información.");
                                $("#btnEnviar").prop("disabled", false);
                                // Para información, mantener la fecha actual ya que no tiene fecha_alta_encVenta
                                $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");
                                $("#nom_encVenta").val(response.data.nom_info);
                                $("#tipo_documento").val(response.data.tipo_documento);
                                $("#departamento_expedicion").val(response.data.departamento_expedicion).trigger('change');
                                $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                $("#obs1_encInfo").val(response.data.observacion);
                                $("#obs2_encInfo").val(response.data.info_adicional);

                                //aqui intentamos rellenar el municipio despues de tener el vlaor de departamento
                                $.ajax({
                                    url: '../obtener_municipios.php',
                                    type: 'POST',
                                    data: {
                                        cod_departamento: response.data.departamento_expedicion
                                    },
                                    dataType: 'json',
                                    success: function(municipios) {
                                        let ciudadSelect = $("#ciudad_expedicion");
                                        ciudadSelect.empty().append('<option value="">Seleccione un municipio</option>');
                                        $.each(municipios, function(index, municipio) {
                                            ciudadSelect.append(
                                                $('<option>', {
                                                    value: municipio.cod_municipio,
                                                    text: municipio.nombre_municipio,
                                                    selected: municipio.cod_municipio === response.data.ciudad_expedicion
                                                })
                                            );
                                        });

                                        ciudadSelect.prop('disabled', false);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error al obtener municipios:", error);
                                    }
                                });


                                // Generar automáticamente UN integrante con los datos de response.data
                                var integrantesActuales = $("input[name='cant_integVenta[]']").length;
                                var integrantesAAgregar = 1 - integrantesActuales; // Solo agregar si no hay ninguno
                                // Establecer la cantidad de integrantes (1 en este caso)

                                if (integrantesAAgregar > 0) {
                                    $("#cant_integVenta").val(integrantesAAgregar);
                                }

                                // Crear el formulario para el integrante principal
                                var integranteDiv = $("<div>").addClass("formulario-dinamico");
                                // Función auxiliar para crear grupos de formulario con label
                                function createFormGroup(name, labelText, inputElement) {
                                    var group = $("<div>").addClass("form-group-dinamico");
                                    var label = $("<label>").attr("for", name).text(labelText);
                                    group.append(label, inputElement);
                                    return group;
                                }
                                var cantidadInput = $("<input>")
                                    .attr("type", "hidden")
                                    .attr("name", "cant_integVenta[]")
                                    .addClass("form-control smaller-input")
                                    .val(1)
                                    .attr("readonly", true);

                                var generoSelect =
                                    createFormGroup(
                                        "gen_integVenta[]",
                                        "Identidad de Género",
                                        $("<select>")
                                        .attr("name", "gen_integVenta[]")
                                        .addClass("form-control smaller-input")                                        .append('<option value="">Identidad Genero</option>')
                                        .append('<option value="F"' + (response.data.gen_integVenta === 'F' ? ' selected' : '') + '>F</option>')
                                        .append('<option value="M"' + (response.data.gen_integVenta === 'M' ? ' selected' : '') + '>M</option>')
                                        .append('<option value="O"' + (response.data.gen_integVenta === 'O' ? ' selected' : '') + '>Otro</option>')
                                    );

                                var rangoEdadSelect =
                                    createFormGroup(
                                        "rango_integVenta[]",
                                        "Rango de edad",                                        $("<select>")
                                        .attr("name", "rango_integVenta[]")
                                        .addClass("form-control smaller-input")
                                        .append('<option value="">Rango Edad</option>')
                                        .append('<option value="0 - 6"' + (response.data.rango_integVenta === '0 - 6' ? ' selected' : '') + '>0 - 6</option>')
                                        .append('<option value="7 - 12"' + (response.data.rango_integVenta === '7 - 12' ? ' selected' : '') + '>7 - 12</option>')
                                        .append('<option value="13 - 17"' + (response.data.rango_integVenta === '13 - 17' ? ' selected' : '') + '>13 - 17</option>')
                                        .append('<option value="18 - 28"' + (response.data.rango_integVenta === '18 - 28' ? ' selected' : '') + '>18 - 28</option>')
                                        .append('<option value="29 - 45"' + (response.data.rango_integVenta === '29 - 45' ? ' selected' : '') + '>29 - 45</option>')
                                        .append('<option value="46 - 64"' + (response.data.rango_integVenta === '46 - 64' ? ' selected' : '') + '>46 - 64</option>')
                                        .append('<option value="Mayor o igual a 65"' + (response.data.rango_integVenta === 'Mayor o igual a 65' ? ' selected' : '') + '>Mayor o igual a 65</option>')
                                    );

                                var OrientacionSexual =
                                    createFormGroup(
                                        "orientacionSexual[]",
                                        "Orientación Sexual",
                                        $("<select>")
                                        .attr("name", "orientacionSexual[]")                                        .addClass("form-control smaller-input")
                                        .append('<option value="">Orientacion Sexual</option>')
                                        .append('<option value="Asexual"' + (response.data.orientacionSexual === 'Asexual' ? ' selected' : '') + '>Asexual</option>')
                                        .append('<option value="Bisexual"' + (response.data.orientacionSexual === 'Bisexual' ? ' selected' : '') + '>Bisexual</option>')
                                        .append('<option value="Heterosexual"' + (response.data.orientacionSexual === 'Heterosexual' ? ' selected' : '') + '>Heterosexual</option>')
                                        .append('<option value="Homosexual"' + (response.data.orientacionSexual === 'Homosexual' ? ' selected' : '') + '>Homosexual</option>')
                                        .append('<option value="Otro"' + (response.data.orientacionSexual === 'Otro' ? ' selected' : '') + '>Otro</option>')
                                    );

                                var condicionDiscapacidad = createFormGroup(
                                    "condicionDiscapacidad[]",
                                    "Condición de Discapacidad",
                                    $("<select>")
                                    .attr("name", "condicionDiscapacidad[]")
                                    .attr("id", "condicionDiscapacidad")                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Condicion Discapacidad</option>')
                                    .append('<option value="Si"' + (response.data.condicionDiscapacidad === 'Si' ? ' selected' : '') + '>Si</option>')
                                    .append('<option value="No"' + (response.data.condicionDiscapacidad === 'No' ? ' selected' : '') + '>No</option>')
                                );

                                var discapacidadSelect = createFormGroup(
                                    "tipoDiscapacidad[]",
                                    "Tipo de Discapacidad",
                                    $("<select>")
                                    .attr("name", "tipoDiscapacidad[]")
                                    .attr("id", "tipoDiscapacidad")                                    .addClass("form-control smaller-input tipo-discapacidad")
                                    .append('<option value="">Tipo Discapacidad</option>')
                                    .append('<option value="Auditiva"' + (response.data.tipoDiscapacidad === 'Auditiva' ? ' selected' : '') + '>Auditiva</option>')
                                    .append('<option value="Física"' + (response.data.tipoDiscapacidad === 'Física' ? ' selected' : '') + '>Física</option>')
                                    .append('<option value="Intelectual"' + (response.data.tipoDiscapacidad === 'Intelectual' ? ' selected' : '') + '>Intelectual</option>')
                                    .append('<option value="Múltiple"' + (response.data.tipoDiscapacidad === 'Múltiple' ? ' selected' : '') + '>Múltiple</option>')
                                    .append('<option value="Psicosocial"' + (response.data.tipoDiscapacidad === 'Psicosocial' ? ' selected' : '') + '>Psicosocial</option>')
                                    .append('<option value="Sordoceguera"' + (response.data.tipoDiscapacidad === 'Sordoceguera' ? ' selected' : '') + '>Sordoceguera</option>')
                                    .append('<option value="Visual"' + (response.data.tipoDiscapacidad === 'Visual' ? ' selected' : '') + '>Visual</option>')
                                );

                                discapacidadSelect.attr("id", "grupoDiscapacidad");

                                // Crear los demás campos con los datos de response.data
                                var GrupoEtnico = createFormGroup(
                                    "grupoEtnico[]",
                                    "Grupo Étnico",
                                    $("<select>")
                                    .attr("name", "grupoEtnico[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Indigena"' + (response.data.grupoEtnico === 'Indigena' ? ' selected' : '') + '>Indígena</option>')
                                    .append('<option value="Negro(a) / Mulato(a) / Afrocolombiano(a)"' + (response.data.grupoEtnico === 'Negro(a) / Mulato(a) / Afrocolombiano(a)' ? ' selected' : '') + '>Negro(a) / Mulato(a) / Afrocolombiano(a)</option>')
                                    .append('<option value="Raizal"' + (response.data.grupoEtnico === 'Raizal' ? ' selected' : '') + '>Raizal</option>')
                                    .append('<option value="Palenquero de San Basilio"' + (response.data.grupoEtnico === 'Palenquero de San Basilio' ? ' selected' : '') + '>Palenquero de San Basilio</option>')
                                    .append('<option value="Mestizo"' + (response.data.grupoEtnico === 'Mestizo' ? ' selected' : '') + '>Mestizo</option>')
                                    .append('<option value="Gitano (rom)"' + (response.data.grupoEtnico === 'Gitano (rom)' ? ' selected' : '') + '>Gitano (rom)</option>')
                                    .append('<option value="Ninguno"' + (response.data.grupoEtnico === 'Ninguno' ? ' selected' : '') + '>Ninguno</option>')
                                );

                                var victima = createFormGroup(
                                    "victima[]",
                                    "¿Es víctima?",
                                    $("<select>")
                                    .attr("name", "victima[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Si"' + (response.data.victima === 'Si' ? ' selected' : '') + '>Sí</option>')
                                    .append('<option value="No"' + (response.data.victima === 'No' ? ' selected' : '') + '>No</option>')
                                );

                                var mujerGestante = createFormGroup(
                                    "mujerGestante[]",
                                    "¿Mujer gestante?",
                                    $("<select>")
                                    .attr("name", "mujerGestante[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Si"' + (response.data.mujerGestante === 'Si' ? ' selected' : '') + '>Sí</option>')
                                    .append('<option value="No"' + (response.data.mujerGestante === 'No' ? ' selected' : '') + '>No</option>')
                                );

                                var cabezaFamilia = createFormGroup(
                                    "cabezaFamilia[]",
                                    "¿Cabeza de familia?",
                                    $("<select>")
                                    .attr("name", "cabezaFamilia[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Si"' + (response.data.cabezaFamilia === 'Si' ? ' selected' : '') + '>Sí</option>')
                                    .append('<option value="No"' + (response.data.cabezaFamilia === 'No' ? ' selected' : '') + '>No</option>')
                                );

                                var experienciaMigratoria = createFormGroup(
                                    "experienciaMigratoria[]",
                                    "¿Tiene experiencia migratoria?",
                                    $("<select>")
                                    .attr("name", "experienciaMigratoria[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Si"' + (response.data.experienciaMigratoria === 'Si' ? ' selected' : '') + '>Sí</option>')
                                    .append('<option value="No"' + (response.data.experienciaMigratoria === 'No' ? ' selected' : '') + '>No</option>')
                                );

                                var seguridadSalud = createFormGroup(
                                    "seguridadSalud[]",
                                    "Seguridad en salud",
                                    $("<select>")
                                    .attr("name", "seguridadSalud[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Regimen Contributivo"' + (response.data.seguridadSalud === 'Regimen Contributivo' ? ' selected' : '') + '>Régimen Contributivo</option>')
                                    .append('<option value="Regimen Subsidiado"' + (response.data.seguridadSalud === 'Regimen Subsidiado' ? ' selected' : '') + '>Régimen Subsidiado</option>')
                                    .append('<option value="Poblacion Vinculada"' + (response.data.seguridadSalud === 'Poblacion Vinculada' ? ' selected' : '') + '>Población Vinculada</option>')
                                );

                                var nivelEducativo = createFormGroup(
                                    "nivelEducativo[]",
                                    "Nivel educativo",
                                    $("<select>")
                                    .attr("name", "nivelEducativo[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Ninguno"' + (response.data.nivelEducativo === 'Ninguno' ? ' selected' : '') + '>Ninguno</option>')
                                    .append('<option value="Preescolar"' + (response.data.nivelEducativo === 'Preescolar' ? ' selected' : '') + '>Preescolar</option>')
                                    .append('<option value="Primaria"' + (response.data.nivelEducativo === 'Primaria' ? ' selected' : '') + '>Primaria</option>')
                                    .append('<option value="Secundaria"' + (response.data.nivelEducativo === 'Secundaria' ? ' selected' : '') + '>Secundaria</option>')
                                    .append('<option value="Media Academica o Clasica"' + (response.data.nivelEducativo === 'Media Academica o Clasica' ? ' selected' : '') + '>Media Académica o Clásica</option>')
                                    .append('<option value="Media Tecnica"' + (response.data.nivelEducativo === 'Media Tecnica' ? ' selected' : '') + '>Media Técnica</option>')
                                    .append('<option value="Normalista"' + (response.data.nivelEducativo === 'Normalista' ? ' selected' : '') + '>Normalista</option>')
                                    .append('<option value="Universitario"' + (response.data.nivelEducativo === 'Universitario' ? ' selected' : '') + '>Universitario</option>')
                                    .append('<option value="Tecnica Profesional"' + (response.data.nivelEducativo === 'Tecnica Profesional' ? ' selected' : '') + '>Técnica Profesional</option>')
                                    .append('<option value="Tecnologica"' + (response.data.nivelEducativo === 'Tecnologica' ? ' selected' : '') + '>Tecnológica</option>')
                                    .append('<option value="Profesional"' + (response.data.nivelEducativo === 'Profesional' ? ' selected' : '') + '>Profesional</option>')
                                    .append('<option value="Especializacion"' + (response.data.nivelEducativo === 'Especializacion' ? ' selected' : '') + '>Especialización</option>')
                                );

                                var condicionOcupacion = createFormGroup(
                                    "condicionOcupacion[]",
                                    "Condición de ocupación",
                                    $("<select>")
                                    .attr("name", "condicionOcupacion[]")
                                    .addClass("form-control smaller-input")
                                    .append('<option value="">Seleccione...</option>')
                                    .append('<option value="Ama de casa"' + (response.data.condicionOcupacion === 'Ama de Casa' ? ' selected' : '') + '>Ama de casa</option>')
                                    .append('<option value="Buscando Empleo"' + (response.data.condicionOcupacion === 'Buscando Empleo' ? ' selected' : '') + '>Buscando Empleo</option>')
                                    .append('<option value="Desempleado(a)"' + (response.data.condicionOcupacion === 'Desempleado(a)' ? ' selected' : '') + '>Desempleado(a)</option>')
                                    .append('<option value="Empleado(a)"' + (response.data.condicionOcupacion === 'Empleado(a)' ? ' selected' : '') + '>Empleado(a)</option>')
                                    .append('<option value="Independiente"' + (response.data.condicionOcupacion === 'Independiente' ? ' selected' : '') + '>Independiente</option>')
                                    .append('<option value="Estudiante"' + (response.data.condicionOcupacion === 'Estudiante' ? ' selected' : '') + '>Estudiante</option>')
                                    .append('<option value="Pensionado(a)"' + (response.data.condicionOcupacion === 'Pensionado(a)' ? ' selected' : '') + '>Pensionado(a)</option>')
                                    .append('<option value="Ninguno"' + (response.data.condicionOcupacion === 'Ninguno' ? ' selected' : '') + '>Ninguno</option>')
                                );
                                var eliminarBtn = $("<button>")
                                    .attr("type", "button")
                                    .addClass("btn btn-danger")
                                    .text("Eliminar")
                                    .click(function() {
                                        $(this).closest(".formulario-dinamico").remove();
                                        actualizarTotal();
                                    });

                                integranteDiv.append(
                                    cantidadInput,
                                    generoSelect,
                                    rangoEdadSelect,
                                    OrientacionSexual,
                                    condicionDiscapacidad,
                                    discapacidadSelect,
                                    GrupoEtnico,
                                    victima,
                                    mujerGestante,
                                    cabezaFamilia,
                                    experienciaMigratoria,
                                    seguridadSalud,
                                    nivelEducativo,
                                    condicionOcupacion,
                                    eliminarBtn
                                );


                                // Agregar el integrante al contenedor
                                $("#integrantes-container").append(integranteDiv);
                                if (response.data.condicionDiscapacidad === 'Si') {
                                    $("#grupoDiscapacidad").show();
                                } else {
                                    $("#grupoDiscapacidad").hide();
                                    $("#tipoDiscapacidad").val("");
                                }

                                // 5. Listener para cuando cambie el valor
                                $("#condicionDiscapacidad").on("change", function() {
                                    const valor = $(this).val();
                                    if (valor === "Si") {
                                        $("#grupoDiscapacidad").show();
                                    } else {
                                        $("#grupoDiscapacidad").hide();
                                        $("#tipoDiscapacidad").val("");
                                    }
                                });
                                // Actualizar el total
                                actualizarTotal();                            } else if (response.status === "no_existe") {
                                mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                    .html("⚠️ El documento no está registrado en ninguna base de datos.");
                                $("#btnEnviar").prop("disabled", false);
                                
                                // Mantener la fecha actual cuando el documento no existe
                                $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");
                            } else {
                                mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                    .html("⚠️ El documento no está registrado.");
                                $("#btnEnviar").prop("disabled", false);

                                // Mantener la fecha actual y vaciar solo los demás campos
                                $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");
                                $("#nom_encVenta, #tipo_documento, #ciudad_expedicion, #fecha_expedicion, #obs1_encInfo, #obs2_encInfo").val("");
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                           // console.log("❌ Error AJAX:", textStatus, errorThrown);
                            mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger")
                                .html("❌ Error en la consulta. Intente nuevamente.");
                            $("#btnEnviar").prop("disabled", false);
                        }
                    });                } else {
                    // Si el campo está vacío, limpiar todo excepto la fecha
                    mensajeContainer.addClass("d-none").html("");
                    $("#btnEnviar").prop("disabled", false);
                    $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");
                    $("#nom_encVenta, #tipo_documento, #ciudad_expedicion, #fecha_expedicion, #obs1_encInfo, #obs2_encInfo").val("");
                }
            });
        });
    </script>    <form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">
        <div class="container">
            <div class="main-container">
                <div class="header-info">
                    <h1><b><i class="fa-solid fa-building"></i> REGISTRO ENCUESTAS NUEVA VENTANILLA</b></h1>
                    <p><i><b>*Datos obligatorios</b></i></p>
                </div>
                
                <div id="mensajeDocumentoContainer" class="alert d-none"></div> <!-- Mensaje arriba -->
                
                <div class="form-section">
                    <h5 class="section-title">Información Personal</h5>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="doc_encVenta">* DOCUMENTO:</label>
                        <input type='number' name='doc_encVenta' class='form-control' id="doc_encVenta" required />
                        <small id="mensajeDocumento" class="text-danger"></small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fec_reg_encVenta">* FECHA REGISTRO:</label>
                        <input type="date" name="fec_reg_encVenta" class="form-control" id="fec_reg_encVenta"
                            value="<?php echo date('Y-m-d'); ?>"  autofocus disabled />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="tipo_doc">* TIPO DE DOCUMENTO:</label>
                        <select name="tipo_documento" class="form-control" id="tipo_documento">
                            <option value="">SELECCIONE:</option>
                            <option value="cedula">CEDULA</option>
                            <option value="ppt">PPT</option>
                            <option value="cedula_extranjeria">CEDULA EXTRANJERIA</option>
                            <option value="otro">otro</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="departamento_expedicion">* DEPARTAMENTO EXPEDICION:</label>
                        <select class="form-control" name="departamento_expedicion" id="departamento_expedicion">
                            <option value="">Seleccione un departamento</option>
                            <?php
                            foreach ($departamentos as $departamento) {
                                echo "<option value='{$departamento['cod_departamento']}'>{$departamento['nombre_departamento']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="ciudad_expedicion">* MUNICIPIO EXPEDICION:</label>
                        <select id="ciudad_expedicion" name="ciudad_expedicion" class="form-control" disabled required>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICION:</label>
                        <input type='date' name='fecha_expedicion' id="fecha_expedicion" class='form-control' required style="text-transform:uppercase;" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="nom_encVenta">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_encVenta' id="nom_encVenta" class='form-control' required style="text-transform:uppercase;" />
                    </div>
                </div>                </div>
            </div>
            
            <div class="form-section">
                <h5 class="section-title">Información de Ubicación</h5>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="dir_encVenta">* DIRECCIÓN:</label>
                        <input type='text' name='dir_encVenta' id="dir_encVenta" class='form-control' />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_barrios">* BARRIO O VEREDA:</label>
                        <select id="id_barrios" class="form-control" name="id_bar" style="width: 100%;min-height: 55px; "></select>
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
                    <div class="form-group col-md-4" id="otro_barrio_container" style="display: none;">
                        <label for="otro_bar_ver_encVenta">ESPECIFIQUE BARRIO, VEREDA O INVASIÓN:</label>
                        <input type="text" id="otro_bar_ver_encVenta" name="otro_bar_ver_encVenta" class="form-control" placeholder="Ingrese el barrio">
                    </div>
                </div>
            </div>
                </div>

            <div class="form-section">
                <h5 class="section-title">Trámite y Ficha</h5>


            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="zona_encVenta">* ZONA:</label>
                        <select id="zona_encVenta" class="form-control" name="zona_encVenta">
                            <option value="">* SELECCIONE LA ZONA:</option>
                            <option value="URBANA">URBANA</option>
                            <option value="RURAL">RURAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tram_solic_encVenta">* TRÁMITE SOLICITADO:</label>
                        <select class="form-control" name="tram_solic_encVenta" id="selectEF">
                            <option value=""></option>
                            <option value="ENCUESTA NUEVA">ENCUESTA NUEVA</option>
                            <option value="ENCUESTA NUEVA POR VERIFICACION">ENCUESTA NUEVA POR VERIFICACION</option>
                            <option value="CAMBIO DIRECCION">CAMBIO DIRECCION</option>
                            <option value="INCONFORMIDAD">INCONFORMIDAD</option>
                            <option value=" DESCENTRALIZADO"> DESCENTRALIZADO</option>
                            <option value="FAVORES">FAVORES</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="num_ficha_encVenta">* No. FICHA o RADICADO:</label>
                        <input type='number' id="num_ficha_encVenta" name='num_ficha_encVenta' class='form-control' required />
                    </div>                </div>
            </div>
            </div>

            <script>
                $("#id_bar").select2({
                    tags: true
                });
                $("#id_vere").select2({
                    tags: true
                });
            </script>

            <div class="integrantes-section">
                <h5 class="section-title">Información de Integrantes</h5>
                <div class="agregar-integrantes-section">
                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="integra_encVenta">INTEGRANTES:</label>
                                <input type='number' id='total_integrantes' name='integra_encVenta' class='form-control' value="" readonly />
                            </div>
                            <div class="form-group col-md-4">
                                <label for="sisben_nocturno">* SISBEN NOCTURNO:</label>
                                <select class="form-control" name="sisben_nocturno" id="nocturno">
                                    <option value=""></option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="cant_integVenta">CANTIDAD:</label>
                                <input type="number" id="cant_integVenta" name="cant_integVenta" class="form-control" />
                            </div>
                            <div class="form-group col-md-2 d-flex flex-column align-items-start">
                                <label for=""></label>
                                <button type="button" class="btn btn-primary mt-auto" id="agregar">Agregar +</button>
                            </div>
                        </div>
                    </div>
                </div>                <div id="integrantes-container"></div>
            </div>

            <div class="form-section">
                <h5 class="section-title">Observaciones</h5>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="obs_encVenta">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs_encVenta" style="text-transform:uppercase;"></textarea>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success me-3" id="btnEnviar">
                        <span class="spinner-border spinner-border-sm"></span>
                        INGRESAR ENCUESTA
                    </button>

                    <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'>
                        <img src='../../img/atras.png' width=27 height=27> REGRESAR
                    </button>
                </div>
            </div>
            </div>
        </div>
    </form>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Establecer el valor del campo id_correg como vacío
            document.getElementById('id_correg').value = '';
        });
    </script>

    <script>
        let agregar = document.getElementById('agregar');
        let contenido = document.getElementById('contenedor');
        let boton_enviar = document.querySelector('#enviar_contacto')
        agregar.addEventListener('click', e => {
            e.preventDefault();
            let clonado = document.querySelector('.clonar');
            let clon = clonado.cloneNode(true);

            contenido.appendChild(clon).classList.remove('clonar');

            let remover_ocutar = contenido.lastChild.childNodes[1].querySelectorAll('span');
            remover_ocutar[0].classList.remove('ocultar');
        });

        contenido.addEventListener('click', e => {
            e.preventDefault();
            if (e.target.classList.contains('puntero')) {
                let contenedor = e.target.parentNode.parentNode;

                contenedor.parentNode.removeChild(contenedor);
            }
        });
        boton_enviar.addEventListener('click', e => {
            e.preventDefault();
            const formulario = document.querySelector('#form_contacto');
            const form = new FormData(formulario);
            const peticion = {
                body: form,
                method: 'POST'
            };
            fetch('php/inserta-contacto.php', peticion)
                .then(res => res.json())
                .then(res => {
                    if (res['respuesta']) {
                        alert(res['mensaje']);
                        formulario.reset();
                    } else {
                        alert(res['mensaje']);
                    }
                });
        });
    </script>

</body>
<script type="text/javascript">    $(document).ready(function() {
        // Inicializar el botón de envío como habilitado por defecto
        $("#btnEnviar").prop("disabled", false);
        
        //  Agregar LOGS para ver qué tiene id_bar al cambiar
        $('#id_bar').on('change', function() {
            $('#id_bar option:selected').each(function() {
              //  console.log("Valor:", $(this).val(), "Texto:", $(this).text());
            });

            let selectedText = $("#id_bar option:selected").text().trim();
            if (selectedText.toUpperCase() === "OTRO") {
                $('#otro_barrio_container').show();
            } else {
                $('#otro_barrio_container').hide();
                $('#otro_barrio').val('');
            }
        });
    });

    $(document).ready(function() {
        $('#id_correg').on('change', function() {
            if ($('#id_correg').val() == "") {
                $('#id_vere').empty();
                $('<option value = "">SELECCIONE UNA VEREDA:</option>').appendTo('#id_vere');
                $('#id_vere').attr('disabled', 'disabled');
            } else {
                $('#id_vere').removeAttr('disabled', 'disabled');
                $('#id_vere').load('../ecampo/veredasGet.php?id_correg=' + $('#id_correg').val());
            }
        });
    });    var cargarDocumentoCheckbox = document.getElementById("cargarDocumento");
    var campoArchivo = document.getElementById("campoArchivo");

    cargarDocumentoCheckbox.addEventListener("change", function() {
        campoArchivo.style.display = cargarDocumentoCheckbox.checked ? "block" : "none";
    });    // Funciones para manejar encuesta existente
    function verEncuestaExistente() {
        if (window.encuestaExistente) {
            // Obtener detalles completos de la encuesta
            let docEncuesta = $('#doc_encVenta').val();
            
            $.ajax({
                url: 'consultar_encuesta_detalle.php',
                type: 'POST',
                data: { doc_encVenta: docEncuesta },
                dataType: 'json',
                beforeSend: function() {
                    // Mostrar loading
                    $('body').append('<div id="loadingModal" class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Cargando detalles...</div></div></div></div>');
                },
                success: function(response) {
                    $('#loadingModal').remove();
                    
                    if (response.status === 'success') {
                        let encuesta = response.encuesta;
                        let integrantes = response.integrantes;
                        
                        // Generar tabla de integrantes
                        let tablaIntegrantes = '';
                        if (integrantes.length > 0) {
                            tablaIntegrantes = `
                                <div class="mt-3">
                                    <h6 class="text-primary"><i class="fas fa-users"></i> Integrantes Registrados (${integrantes.length})</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Cantidad</th>
                                                    <th>Género</th>
                                                    <th>Rango Edad</th>
                                                    <th>Discapacidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;
                            
                            integrantes.forEach(function(integrante) {
                                tablaIntegrantes += `
                                    <tr>
                                        <td>${integrante.cant_integVenta}</td>
                                        <td>${integrante.gen_integVenta}</td>
                                        <td>${integrante.rango_descripcion}</td>
                                        <td>${integrante.condicionDiscapacidad}</td>
                                    </tr>
                                `;
                            });
                            
                            tablaIntegrantes += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `;
                        }
                        
                        let modalContent = `
                            <div class="modal fade" id="modalEncuestaExistente" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="modalLabel">
                                                <i class="fas fa-file-alt"></i> Encuesta Existente - Documento: ${encuesta.doc_encVenta}
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary"><i class="fas fa-user"></i> Información Personal</h6>
                                                    <table class="table table-sm">
                                                        <tr><th width="40%">Documento:</th><td>${encuesta.doc_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>Nombre:</th><td>${encuesta.nom_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>Dirección:</th><td>${encuesta.dir_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>Zona:</th><td>${encuesta.zona_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>Departamento:</th><td>${encuesta.departamento_expedicion || 'N/A'}</td></tr>
                                                        <tr><th>Ciudad:</th><td>${encuesta.ciudad_expedicion || 'N/A'}</td></tr>
                                                        <tr><th>Fecha Expedición:</th><td>${encuesta.fecha_expedicion || 'N/A'}</td></tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-primary"><i class="fas fa-calendar"></i> Detalles de Registro</h6>
                                                    <table class="table table-sm">
                                                        <tr><th width="40%">Fecha Registro:</th><td>${new Date(encuesta.fecha_alta_encVenta).toLocaleDateString('es-ES')}</td></tr>
                                                        <tr><th>Total Integrantes:</th><td>${encuesta.integra_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>No. Ficha:</th><td>${encuesta.num_ficha_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>Trámite:</th><td>${encuesta.tram_solic_encVenta || 'N/A'}</td></tr>
                                                        <tr><th>Comuna:</th><td>${encuesta.comuna || 'N/A'}</td></tr>
                                                        <tr><th>Barrio:</th><td>${encuesta.barrio || 'N/A'}</td></tr>
                                                        <tr><th>SISBEN Nocturno:</th><td>${encuesta.sisben_nocturno || 'No'}</td></tr>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            ${encuesta.obs_encVenta ? `
                                                <div class="mt-3">
                                                    <h6 class="text-primary"><i class="fas fa-comment"></i> Observaciones</h6>
                                                    <div class="alert alert-light">${encuesta.obs_encVenta}</div>
                                                </div>
                                            ` : ''}
                                            
                                            ${tablaIntegrantes}
                                        </div>                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                <i class="fas fa-times"></i> Cerrar
                                            </button>
                                            <button type="button" class="btn btn-primary" onclick="continuarConFormulario(); $('#modalEncuestaExistente').modal('hide');">
                                                <i class="fas fa-arrow-right"></i> Continuar de Todas Formas
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="permitirNuevaEncuesta(); $('#modalEncuestaExistente').modal('hide');">
                                                <i class="fas fa-plus"></i> Crear Nueva Encuesta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remover modal existente si existe
                        $('#modalEncuestaExistente').remove();
                        
                        // Agregar el modal al body
                        $('body').append(modalContent);
                        
                        // Mostrar el modal
                        $('#modalEncuestaExistente').modal('show');
                    } else {
                        $('#loadingModal').remove();
                        alert('No se pudieron cargar los detalles de la encuesta.');
                    }
                },
                error: function() {
                    $('#loadingModal').remove();
                    alert('Error al consultar los detalles de la encuesta.');
                }
            });
        }
    }    function permitirNuevaEncuesta() {
        // Confirmar con el usuario de manera más clara
        if (confirm('🔄 CREAR NUEVA ENCUESTA\n\n' +
                   'Esto limpiará todos los campos del formulario y le permitirá ' +
                   'crear una nueva encuesta desde cero.\n\n' +
                   '¿Desea continuar?')) {
            // Limpiar los campos del formulario
            $('#formulario')[0].reset();
            
            // Limpiar selects específicos
            $('#departamento_expedicion').val('').trigger('change');
            $('#ciudad_expedicion').empty().append('<option value="">Seleccione un municipio</option>').prop('disabled', true);
            $('#id_barrios').val(null).trigger('change');
            $('#id_comunas').empty().append('<option value="">Seleccione una comuna</option>').prop('disabled', true);
            
            // Restablecer la fecha actual
            $('#fec_reg_encVenta').val("<?php echo date('Y-m-d'); ?>");
            
            // Habilitar el botón de envío
            $("#btnEnviar").prop("disabled", false);
            
            // Mostrar mensaje de confirmación
            let mensajeContainer = $("#mensajeConsulta");
            mensajeContainer.removeClass('d-none alert-warning alert-danger').addClass('alert alert-success')
                .html('<i class="fas fa-check-circle"></i> <strong>Formulario Limpio</strong><br>' +
                      'Puede proceder a llenar el formulario para crear una nueva encuesta.');
            
            // Limpiar la sección de integrantes
            $('#integrantesContainer').empty();
            $('#integra_encVenta').val('');
            
            // Limpiar la variable global
            delete window.encuestaExistente;
            
            // Mostrar mensaje de confirmación
            $('.alert').removeClass('d-none alert-warning alert-danger').addClass('alert alert-info')
                .html('<i class="fas fa-info-circle"></i> <strong>Nuevo Registro</strong><br>Puede proceder a llenar el formulario para crear una nueva encuesta.');
        }    }

    function continuarConFormulario() {
        // Mostrar una confirmación más clara al usuario
        if (confirm('⚠️ CONFIRMACIÓN REQUERIDA\n\n' +
                   'Ya existe una encuesta registrada para este documento. ' +
                   'Si continúa, se creará un registro adicional en la base de datos.\n\n' +
                   '¿Está seguro que desea continuar y guardar esta nueva encuesta?')) {
            
            // Cambiar el mensaje a información
            let mensajeContainer = $("#mensajeConsulta");
            mensajeContainer.removeClass("alert-warning alert-danger").addClass("alert-info")
                .html('<i class="fas fa-info-circle"></i> <strong>Continuando con Nueva Encuesta</strong><br>' +
                      'Se guardará una nueva encuesta aunque ya exista una registrada anteriormente. ' +
                      'Puede proceder a completar y enviar el formulario.');
            
            // Asegurar que el botón de envío esté habilitado
            $("#btnEnviar").prop("disabled", false);
            
            // Opcional: scroll hacia arriba para que vea el mensaje
            $('html, body').animate({
                scrollTop: mensajeContainer.offset().top - 100
            }, 500);
        }
    }

    function llenarDatosExistentes(data) {
        // Esta función ya existe en el código, se mantiene como está
        // Solo agregamos algunos campos que podrían estar faltando
        if (data.obs_encVenta) {
            $("#obs_encVenta").val(data.obs_encVenta);
        }
        if (data.tram_solic_encVenta) {
            $("#tram_solic_encVenta").val(data.tram_solic_encVenta);
        }
        if (data.sisben_nocturno) {
            $("#sisben_nocturno").val(data.sisben_nocturno);
        }
    }

    // Función para validar los integrantes antes del envío
    function validarIntegrantes() {
        var integrantesContainer = $("#integrantes-container");
        var formulariosDinamicos = integrantesContainer.find(".formulario-dinamico");
        
        if (formulariosDinamicos.length === 0) {
            alert("Debe agregar al menos un integrante antes de enviar el formulario.");
            return false;
        }
        
        var errores = [];
        var integranteNumero = 1;
        
        formulariosDinamicos.each(function(index) {
            var formulario = $(this);
            var camposRequeridos = formulario.find("select[required]");
            
            camposRequeridos.each(function() {
                var campo = $(this);
                var valor = campo.val();
                var nombre = campo.attr("name");
                
                if (!valor || valor === "") {
                    // Obtener el label del campo para un mensaje más amigable
                    var label = campo.closest('.form-group-dinamico').find('label').text() || nombre;
                    errores.push("Integrante " + integranteNumero + ": " + label + " es requerido");
                    
                    // Agregar clase de error visual
                    campo.addClass("is-invalid");
                } else {
                    // Remover clase de error si el campo está completo
                    campo.removeClass("is-invalid");
                }
            });
            
            integranteNumero++;
        });
        
        if (errores.length > 0) {
            var mensajeError = "Por favor complete los siguientes campos:\n\n" + errores.join("\n");
            alert(mensajeError);
            
            // Scroll al primer campo con error
            var primerCampoError = $(".is-invalid").first();
            if (primerCampoError.length > 0) {
                $('html, body').animate({
                    scrollTop: primerCampoError.offset().top - 100
                }, 500);
            }
            
            return false;
        }
        
        return true;
    }

    // Agregar evento de validación al formulario
    $(document).ready(function() {
        $("#form_contacto").on("submit", function(e) {
            if (!validarIntegrantes()) {
                e.preventDefault();
                return false;
            }
        });
        
        // Remover clase de error cuando el usuario selecciona un valor
        $(document).on("change", "select.is-invalid", function() {
            if ($(this).val() !== "") {
                $(this).removeClass("is-invalid");
            }
        });
    });
</script>

</html>