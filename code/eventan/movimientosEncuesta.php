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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BD SISBEN - Movimientos de Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../barrios.js"></script>
    <script src="integrantesEncuesta.js"></script>
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

        /* Estilos para integrantes readonly */
        .integrante-readonly {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .integrante-readonly::before {
            content: "Integrante Precargado";
            position: absolute;
            top: -10px;
            left: 15px;
            background-color: #007bff;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .readonly-field {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            color: #495057 !important;
            cursor: not-allowed !important;
        }

        .readonly-field:focus {
            background-color: #f8f9fa !important;
            border-color: #e9ecef !important;
            box-shadow: none !important;
        }

        .form-group-dinamico {
            margin-bottom: 1rem;
        }

        .form-group-dinamico label {
            display: block;
            margin-bottom: 0.3rem;
            font-weight: 500;
            color: #495057;
            font-size: 0.9rem;
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

        .alert-ficha-retirada {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            border: 2px solid #dc3545;
            border-radius: 10px;
            color: #721c24;
            padding: 1rem;
            font-weight: 600;
        }

        .alert-ficha-retirada .fa-exclamation-triangle {
            color: #dc3545;
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .puntero {
            cursor: pointer;
        }

        .ocultar {
            display: none;
        }

        .encuestador-container {
            max-height: 200px;
            overflow-y: auto;
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

        // Función global para actualizar totales
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

        // Función para crear integrantes readonly (precargados)
        function crearIntegranteReadOnly(integrante) {
            var integranteDiv = $("<div>").addClass("formulario-dinamico integrante-readonly");
            
            // Mapeo de rangos de edad (número a texto)
            var rangoEdadMap = {
                1: '0 - 6',
                2: '7 - 12', 
                3: '13 - 17',
                4: '18 - 28',
                5: '29 - 45',
                6: '46 - 64',
                7: 'Mayor o igual a 65'
            };

            // 🔧 MAPEO UNIVERSAL - Determinar los valores correctos según la tabla de origen
            var generoValue = integrante.gen_integVenta || integrante.gen_integMovIndep || '';
            var rangoValue = integrante.rango_integVenta || integrante.rango_integMovIndep || '';
            var cantidadValue = integrante.cant_integVenta || integrante.cant_integMovIndep || 1;

            // Mapear el rango de edad de número a texto
            var rangoTexto = rangoEdadMap[rangoValue] || rangoValue;

            function createReadOnlyField(name, label, value) {
                var group = $("<div>").addClass("form-group-dinamico");
                var labelEl = $("<label>").text(label);
                var input = $("<input>")
                    .attr("type", "text")
                    .attr("name", name)
                    .addClass("form-control smaller-input readonly-field")
                    .val(value || "No especificado")
                    .prop("readonly", true);
                group.append(labelEl, input);
                return group;
            }

            // Crear campos de solo lectura con los datos del integrante
            var campos = [
                createReadOnlyField("cant_integVenta[]", "Cantidad", cantidadValue),
                createReadOnlyField("gen_integVenta[]", "Identidad de Género", generoValue === 'F' ? 'Femenino' : generoValue === 'M' ? 'Masculino' : generoValue === 'O' ? 'Otro' : generoValue),
                createReadOnlyField("rango_integVenta[]", "Rango de Edad", rangoTexto),
                createReadOnlyField("orientacionSexual[]", "Orientación Sexual", integrante.orientacionSexual),
                createReadOnlyField("condicionDiscapacidad[]", "Condición Discapacidad", integrante.condicionDiscapacidad),
                createReadOnlyField("tipoDiscapacidad[]", "Tipo Discapacidad", integrante.tipoDiscapacidad),
                createReadOnlyField("grupoEtnico[]", "Grupo Étnico", integrante.grupoEtnico),
                createReadOnlyField("victima[]", "¿Es víctima?", integrante.victima),
                createReadOnlyField("mujerGestante[]", "¿Mujer gestante?", integrante.mujerGestante),
                createReadOnlyField("cabezaFamilia[]", "¿Cabeza de familia?", integrante.cabezaFamilia),
                createReadOnlyField("experienciaMigratoria[]", "Experiencia Migratoria", integrante.experienciaMigratoria),
                createReadOnlyField("seguridadSalud[]", "Seguridad en Salud", integrante.seguridadSalud),
                createReadOnlyField("nivelEducativo[]", "Nivel Educativo", integrante.nivelEducativo),
                createReadOnlyField("condicionOcupacion[]", "Condición Ocupación", integrante.condicionOcupacion)
            ];

            // Agregar campos al contenedor
            campos.forEach(function(campo) {
                integranteDiv.append(campo);
            });

            // Agregar nota informativa
            var notaDiv = $("<div>").addClass("alert alert-info mt-2")
                .html("<i class='fas fa-info-circle'></i> <strong>Integrante precargado:</strong> Este integrante ya existe en el sistema. Los datos se muestran como referencia y no serán modificados.");

            integranteDiv.append(notaDiv);
            return integranteDiv;
        }

        $(document).ready(function() {

            // Precargar automáticamente 1 integrante al cargar la página
            setTimeout(function() {
                if ($("#integrantes-container .formulario-dinamico").length === 0) {
                    $("#agregar").trigger('click');
                }
            }, 500);

            $("#agregar").click(function() {
                var inputCantidad = $("#cant_integVenta");
                var cantidadValor = parseInt(inputCantidad.val());

                if (!cantidadValor || cantidadValor <= 0) {
                    alert("Por favor, ingresa una cantidad válida de integrantes.");
                    return;
                }

                for (var i = 0; i < cantidadValor; i++) {
                    var integranteDiv = $("<div>").addClass("formulario-dinamico");

                    var cantidadInput = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "cant_integVenta[]")
                        .addClass("form-control smaller-input")
                        .val(1) // Por defecto 1 para que se cuente automáticamente
                        .on("input", actualizarTotal)
                        .attr("placeholder", "Cantidad")
                        .attr("readonly", true) // Hacer el campo de solo lectura;

                    var generoSelect = $("<select>")
                        .attr("name", "gen_integVenta[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Identidad Genero</option>')
                        .append('<option value="F">F</option>')
                        .append('<option value="M">M</option>')
                        .append('<option value="O">Otro</option>');

                    var OrientacionSexual = $("<select>")
                        .attr("name", "orientacionSexual[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Orientiacion Sexual</option>')
                        .append('<option value="Asexual">Asexual</option>')
                        .append('<option value="Bisexual">Bisexual</option>')
                        .append('<option value="Heterosexual">Heterosexual</option>')
                        .append('<option value="Homosexual">Homosexual</option>')
                        .append('<option value="Otro">Otro</option>');

                    var rangoEdadSelect = $("<select>")
                        .attr("name", "rango_integVenta[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Rango de edad</option>')
                        .append('<option value="0 - 6">0 - 5</option>')
                        .append('<option value="7 - 12">6 - 12</option>')
                        .append('<option value="13 - 17">13 - 17</option>')
                        .append('<option value="18 - 28">18 - 28</option>')
                        .append('<option value="29 - 45">29 - 45</option>')
                        .append('<option value="46 - 64">46 - 64</option>')
                        .append('<option value="Mayor o igual a 65">Mayor o igual a 65</option>');

                    var condicionDiscapacidad = $("<select>")
                        .attr("name", "condicionDiscapacidad[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Condicion Discapacidad</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var discapacidadSelect = $("<select>")
                        .attr("name", "tipoDiscapacidad[]")
                        .addClass("form-control smaller-input tipo-discapacidad")
                        .append('<option value="">Tipo de Discapacidad</option>')
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
                        .append('<option value="">Victima</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var mujerGestante = $("<select>")
                        .attr("name", "mujerGestante[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Mujer Gestante</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var cabezaFamilia = $("<select>")
                        .attr("name", "cabezaFamilia[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Hombre / mujer  Cabeza de Familia</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var experienciaMigratoria = $("<select>")
                        .attr("name", "experienciaMigratoria[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Experiencia Migratoria</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var seguridadSalud = $("<select>")
                        .attr("name", "seguridadSalud[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Seguridad Salud</option>')
                        .append('<option value="Regimen Contributivo">Regimen Contributivo</option>')
                        .append('<option value="Regimen Subsidiado">Regimen Subsidiado</option>')
                        .append('<option value="Poblacion Vinculada">Poblacion Vinculada</option>')
                        .append('<option value="Ninguno">Ninguno</option>');

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

                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger")
                        .text("Eliminar")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            // Actualizar la cantidad de integrantes en el campo
                            var cantidadActual = parseInt($("#cant_integVenta").val()) || 0;
                            if (cantidadActual > 0) {
                                $("#cant_integVenta").val(cantidadActual - 1);
                            }
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
    <div class="container-fluid">
        <div class="text-center mb-4">
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </div>

        <div class="container">
            <div class="main-container">
                <div class="header-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1"><i class="fas fa-exchange-alt me-2"></i>Movimientos de Encuesta</h4>
                            <p class="mb-0">Registrar movimientos y gestión de encuestas en el sistema SISBEN</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <p class="mb-0">Usuario: <strong><?php echo $nombre; ?></strong></p>
                            <small>Fecha: <?php echo date('d/m/Y H:i'); ?></small>
                        </div>
                    </div>
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
                ?> <script>
                    // Función para limpiar todos los campos del formulario
                    function limpiarFormulario() {
                        // Limpiar campos de texto
                        $("#nom_encVenta, #dir_encVenta, #fecha_expedicion, #num_ficha_encVenta, #otro_bar_ver_encVenta, #obs_encVenta").val("");

                        // Limpiar selects
                        $("#tipo_documento, #departamento_expedicion, #zona_encVenta, #selectEF, #nocturno").val("");
                        $("#ciudad_expedicion").empty().append('<option value="">Seleccione un municipio</option>').prop('disabled', true);
                        $("#id_comunas").empty().append('<option value="">Seleccione comuna</option>').prop('disabled', true);

                        // Limpiar barrios (Select2)
                        $("#id_barrios").val(null).trigger('change');

                        // Limpiar fecha (mantener fecha actual)
                        $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");
                        // Limpiar fecha de nacimiento
                        $("#fecha_nacimiento").val("");

                        // Limpiar integrantes
                        $("#integrantes-container").empty();
                        $("#total_integrantes").val("");
                        
                        // Restablecer cantidad a 1 y agregar 1 integrante automáticamente
                        $("#cant_integVenta").val("1");
                        setTimeout(function() {
                            if ($("#integrantes-container .formulario-dinamico").length === 0) {
                                $("#agregar").trigger('click');
                            }
                        }, 100);

                        // Ocultar campo otro barrio
                        $("#otro_barrio_container").hide();

                        // Habilitar botón enviar
                        $("#btnEnviar").prop("disabled", false);
                    }

                    $(document).ready(function() {
                        $("#doc_encVenta").on("blur", function() {
                            let documento = $(this).val();
                            let mensajeContainer = $("#mensajeDocumentoContainer");

                            // Primero limpiar el formulario cuando cambie el documento
                            if (documento !== "") {
                                // Limpiar formulario antes de hacer la consulta
                                limpiarFormulario();

                                $.ajax({
                                    url: "verificar_encuesta.php",
                                    type: "POST",
                                    data: {
                                        doc_encVenta: documento
                                    },
                                    dataType: "json",
                                    beforeSend: function() {
                                        mensajeContainer.removeClass("alert-danger alert-success alert-warning").addClass("alert d-none").html("");
                                    },
                                    success: function(response) {
                                        console.log("✅ Respuesta del servidor:", response);
                                        console.log("🔍 Integrantes recibidos:", response.integrantes);

                                        if (response.status === "ficha_retirada") {
                                            // Mostrar advertencia de ficha retirada (eliminando la frase restrictiva)
                                            var cleanMessage = response.message.replace(/No se pueden realizar movimientos\.?/i, '').trim();
                                            if (cleanMessage === '') {
                                                cleanMessage = '⚠️ ADVERTENCIA: Esta persona tiene la ficha RETIRADA.';
                                            }
                                            mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger").html(cleanMessage);

                                            // Cargar los datos pero deshabilitar el botón de enviar
                                            // Permitir crear un nuevo movimiento incluso si la ficha está retirada
                                            $("#btnEnviar").prop("disabled", false);

                                            // Continuar cargando los datos normalmente
                                            $("#dir_encVenta").val(response.data.dir_encVenta);
                                            $("#zona_encVenta").val(response.data.zona_encVenta);
                                            $("#num_ficha_encVenta").val(response.data.num_ficha_encVenta);
                                            // NO cargar movimientos para ficha retirada
                                            $("#nocturno").val(response.data.sisben_nocturno);
                                            $("#obs_encVenta").val(response.data.obs_encVenta);
                                            $("#otro_bar_ver_encVenta").val(response.data.otro_bar_ver_encVenta);

                                            // Resto del código de carga...
                                            if (response.data.departamento_expedicion) {
                                                $("#departamento_expedicion").val(response.data.departamento_expedicion);

                                                // Cargar municipios después de seleccionar departamento
                                                window.setCiudadSeleccionada(response.data.ciudad_expedicion);

                                                const event = new Event('change');
                                                document.getElementById('departamento_expedicion').dispatchEvent(event);
                                            }
                                            $("#tipo_documento").val(response.data.tipo_documento);
                                            $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                            $("#fecha_nacimiento").val(response.data.fecha_nacimiento || '');
                                            $("#nom_encVenta").val(response.data.nom_encVenta);
                                            $("#fec_reg_encVenta").val(response.data.fecha_alta_encVenta.split(' ')[0]); // Solo la fecha, sin la hora

                                            // Cargar barrio y comuna
                                            if (response.data.id_bar) {
                                                // Crear option y agregarlo al select
                                                const newOption = new Option("Cargando...", response.data.id_bar, true, true);
                                                $("#id_barrios").append(newOption).trigger('change');
                                            }

                                            // Limpiar integrantes existentes
                                            $("#integrantes-container").empty(); // Cargar integrantes (solo lectura para ficha retirada)
                                            if (response.integrantes && response.integrantes.length > 0) {
                                                response.integrantes.forEach(function(integrante) {
                                                    var integranteDiv = $("<div>").addClass("formulario-dinamico");

                                                    // 🔧 MAPEO UNIVERSAL - Determinar los valores correctos según la tabla de origen
                                                    var generoValue = integrante.gen_integVenta || integrante.gen_integMovIndep || '';
                                                    var rangoValue = integrante.rango_integVenta || integrante.rango_integMovIndep || '';
                                                    var cantidadValue = integrante.cant_integVenta || integrante.cant_integMovIndep || 1;

                                                    // Crear función auxiliar para elementos de solo lectura
                                                    function createReadOnlyFormGroup(name, label, value) {
                                                        return $("<div>").addClass("form-group-dinamico")
                                                            .append($("<label>").text(label))
                                                            .append($("<input>")
                                                                .attr("type", "text")
                                                                .attr("name", name)
                                                                .addClass("form-control smaller-input")
                                                                .val(value || "No especificado")
                                                                .prop("readonly", true)
                                                                .css("background-color", "#f8f9fa")
                                                            );
                                                    }

                                                    // Crear todos los campos como solo lectura
                                                    var campos = [
                                                        createReadOnlyFormGroup("cant_integVenta[]", "Cantidad", cantidadValue),
                                                        createReadOnlyFormGroup("gen_integVenta[]", "Género", generoValue),
                                                        createReadOnlyFormGroup("rango_integVenta[]", "Rango edad", rangoValue),
                                                        createReadOnlyFormGroup("orientacionSexual[]", "Orientación sexual", integrante.orientacionSexual),
                                                        createReadOnlyFormGroup("condicionDiscapacidad[]", "Condición discapacidad", integrante.condicionDiscapacidad),
                                                        createReadOnlyFormGroup("tipoDiscapacidad[]", "Tipo discapacidad", integrante.tipoDiscapacidad),
                                                        createReadOnlyFormGroup("grupoEtnico[]", "Grupo étnico", integrante.grupoEtnico),
                                                        createReadOnlyFormGroup("victima[]", "Víctima", integrante.victima),
                                                        createReadOnlyFormGroup("mujerGestante[]", "Mujer gestante", integrante.mujerGestante),
                                                        createReadOnlyFormGroup("cabezaFamilia[]", "Cabeza familia", integrante.cabezaFamilia),
                                                        createReadOnlyFormGroup("experienciaMigratoria[]", "Exp. migratoria", integrante.experienciaMigratoria),
                                                        createReadOnlyFormGroup("seguridadSalud[]", "Seguridad salud", integrante.seguridadSalud),
                                                        createReadOnlyFormGroup("nivelEducativo[]", "Nivel educativo", integrante.nivelEducativo),
                                                        createReadOnlyFormGroup("condicionOcupacion[]", "Condición ocupación", integrante.condicionOcupacion)
                                                    ];

                                                    // Agregar nota de solo lectura
                                                    var notaDiv = $("<div>").addClass("alert alert-warning mt-2")
                                                        .html("<small><strong>Nota:</strong> Ficha retirada - Solo lectura</small>");

                                                    integranteDiv.append(campos);
                                                    integranteDiv.append(notaDiv);
                                                    $("#integrantes-container").append(integranteDiv);
                                                });

                                                // Actualizar el total
                                                actualizarTotal();
                                            }
                                        } else if (response.status === "existe") {
                                            mensajeContainer.removeClass("d-none alert-danger alert-warning").addClass("alert alert-success")
                                                .html("✔ Encuesta encontrada.");
                                            $("#btnEnviar").prop("disabled", false);

                                            // Llenar todos los campos principales
                                            $("#fec_reg_encVenta").val(response.data.fecha_alta_encVenta.split(' ')[0]); // Solo la fecha, sin la hora
                                            $("#nom_encVenta").val(response.data.nom_encVenta);
                                            $("#tipo_documento").val(response.data.tipo_documento);
                                            $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                                $("#fecha_nacimiento").val(response.data.fecha_nacimiento || '');
                                            $("#dir_encVenta").val(response.data.dir_encVenta);
                                            $("#zona_encVenta").val(response.data.zona_encVenta);
                                            $("#num_ficha_encVenta").val(response.data.num_ficha_encVenta);
                                            // $("#selectEF").val(response.data.tram_solic_encVenta); // No cargar automáticamente, permitir selección manual
                                            $("#nocturno").val(response.data.sisben_nocturno);
                                            $("#obs_encVenta").val(response.data.obs_encVenta);
                                            $("#otro_bar_ver_encVenta").val(response.data.otro_bar_ver_encVenta);

                                            // Cargar departamento y después municipio
                                            $("#departamento_expedicion").val(response.data.departamento_expedicion);
                                            
                                            // Cargar municipio usando AJAX
                                            if (response.data.departamento_expedicion) {
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
                                                    error: function() {
                                                        console.error("Error al cargar municipios");
                                                    }
                                                });
                                            }

                                            // Cargar barrio y comuna
                                            if (response.data.id_bar) {
                                                // Buscar el barrio específico y cargarlo
                                                $.ajax({
                                                    type: 'GET',
                                                    url: '../buscar_barrios.php',
                                                    data: {
                                                        q: '',
                                                        id: response.data.id_bar
                                                    },
                                                    dataType: 'json',
                                                    success: function(data) {
                                                        if (data.length > 0) {
                                                            let barrio = data[0];
                                                            
                                                            // Limpiar select y agregar la opción
                                                            $('#id_barrios').empty();
                                                            let option = new Option(barrio.text, barrio.id, true, true);
                                                            $('#id_barrios').append(option).trigger('change');

                                                            // Cargar comuna después de cargar el barrio
                                                            setTimeout(function() {
                                                                $.ajax({
                                                                    url: '../comunaGet.php',
                                                                    type: 'GET',
                                                                    data: {
                                                                        id_barrio: barrio.id
                                                                    },
                                                                    success: function(comunasHtml) {
                                                                        $('#id_comunas').html(comunasHtml);
                                                                        $('#id_comunas').prop('disabled', false);
                                                                        // Seleccionar la comuna específica
                                                                        $('#id_comunas').val(response.data.id_com);
                                                                    },
                                                                    error: function() {
                                                                        console.error("Error al cargar comunas");
                                                                    }
                                                                });
                                                            }, 500); // Esperar 500ms para que se complete la carga del barrio
                                                        }
                                                    },
                                                    error: function() {
                                                        console.error("Error al cargar barrio");
                                                    }
                                                });
                                            }

                                            // Limpiar integrantes existentes
                                            $("#integrantes-container").empty();

                                            // Cargar integrantes
                                            if (response.integrantes && response.integrantes.length > 0) {
                                                response.integrantes.forEach(function(integrante) {
                                                    var integranteReadOnly = crearIntegranteReadOnly(integrante);
                                                    $("#integrantes-container").append(integranteReadOnly);
                                                });

                                                // Actualizar el total
                                                actualizarTotal();
                                            }
                                        } else if (response.status === "no_existe") {
                                            mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                                .html("⚠️ El documento no está registrado en la base de encuestas. Puede crear un nuevo movimiento.");
                                            $("#btnEnviar").prop("disabled", false);
                                            // Limpiar integrantes y agregar 1 nuevo
                                            $("#integrantes-container").empty();
                                            $("#cant_integVenta").val("1");
                                            setTimeout(function() {
                                                if ($("#integrantes-container .formulario-dinamico").length === 0) {
                                                    $("#agregar").trigger('click');
                                                }
                                            }, 100);
                                        } else {
                                            mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                                .html("⚠️ El documento no está registrado. Puede crear un nuevo movimiento.");
                                            $("#btnEnviar").prop("disabled", false);
                                            // Limpiar integrantes y agregar 1 nuevo
                                            $("#integrantes-container").empty();
                                            $("#cant_integVenta").val("1");
                                            setTimeout(function() {
                                                if ($("#integrantes-container .formulario-dinamico").length === 0) {
                                                    $("#agregar").trigger('click');
                                                }
                                            }, 100);
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger")
                                            .html("❌ Error en la consulta. Intente nuevamente.");
                                        $("#btnEnviar").prop("disabled", false);
                                    }
                                });
                            } else {
                                // Si el campo está vacío, limpiar todo
                                limpiarFormulario();
                                mensajeContainer.addClass("d-none").html("");
                                $("#btnEnviar").prop("disabled", false);
                            }
                        });
                    });
                </script>
                <form id="form_contacto" action='updateEncuesta_independiente.php' method="POST" enctype="multipart/form-data">

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-user me-2"></i>Datos del Titular</h5>

                        <div id="mensajeDocumentoContainer" class="alert d-none mb-3"></div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="doc_encVenta" class="form-label">Documento <span class="text-danger">*</span></label>
                                <input type='number' name='doc_encVenta' class='form-control' id="doc_encVenta" required />
                                <small id="mensajeDocumento" class="text-danger"></small>
                            </div>
                            <div class="col-md-4">
                                <label for="fec_reg_encVenta" class="form-label">Fecha Registro <span class="text-danger">*</span></label>
                                <input type="date" name="fec_reg_encVenta" class="form-control" id="fec_reg_encVenta"
                                    value="<?php echo date('Y-m-d'); ?>" readonly />
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_documento" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                                <select name="tipo_documento" class="form-select" id="tipo_documento" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="cedula">Cédula</option>
                                    <option value="ppt">PPT</option>
                                    <option value="cedula_extranjeria">Cédula Extranjería</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-id-card me-2"></i>Información del Documento</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="departamento_expedicion" class="form-label">
                                    Departamento Expedición <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="departamento_expedicion" id="departamento_expedicion" required>
                                    <option value="">Seleccione departamento</option>
                                    <?php
                                    foreach ($departamentos as $departamento) {
                                        echo "<option value='{$departamento['cod_departamento']}'>{$departamento['nombre_departamento']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="ciudad_expedicion" class="form-label">Municipio Expedición <span class="text-danger">*</span></label>
                                <select id="ciudad_expedicion" name="ciudad_expedicion" class="form-select" disabled required>
                                    <option value="">Seleccione municipio</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_expedicion" class="form-label">Fecha Expedición <span class="text-danger">*</span></label>
                                <input type='date' name='fecha_expedicion' id="fecha_expedicion" class='form-control' required />
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type='date' name='fecha_nacimiento' id="fecha_nacimiento" class='form-control' />
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5 class="section-title"><i class="fas fa-home me-2"></i>Información Personal y Residencia</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label for="nom_encVenta" class="form-label">Nombres Completos <span class="text-danger">*</span></label>
                                <input type='text' name='nom_encVenta' id="nom_encVenta" class='form-control' required style="text-transform:uppercase;" />
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="dir_encVenta" class="form-label">Dirección <span class="text-danger">*</span></label>
                                <input type='text' name='dir_encVenta' id="dir_encVenta" class='form-control' required />
                            </div>
                            <div class="col-md-4">
                                <label for="id_barrios" class="form-label">Barrio o Vereda <span class="text-danger">*</span></label>
                                <select id="id_barrios" class="form-select" name="id_bar" required>
                                    <option value="">Seleccione barrio o vereda</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="id_comunas" class="form-label">Comuna o Corregimiento <span class="text-danger">*</span></label>
                                <select id="id_comunas" class="form-select" name="id_com" disabled required>
                                    <option value="">Seleccione comuna</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3" id="otro_barrio_container" style="display: none;">
                            <div class="col-md-6">
                                <label for="otro_bar_ver_encVenta" class="form-label">Especifique Barrio, Vereda o Invasión</label>
                                <input type="text" id="otro_bar_ver_encVenta" name="otro_bar_ver_encVenta" class="form-control" placeholder="Ingrese el barrio">
                            </div>
                        </div>
                    </div>

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
                                <label for="movimientos">* MOVIMIENTOS:</label>
                                <select class="form-control" name="movimientos" id="selectEF">
                                    <option value=""></option>
                                    <option value="inclusion">Inclusion</option>
                                    <option value="Inconformidad por clasificacion">Inconformidad por clasificación</option>
                                    <option value="modificacion datos persona">Modificación datos persona</option>
                                    <option value="Retiro ficha">Retiro ficha</option>
                                    <option value="Retiro personas"> Retiro personas</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="num_ficha_encVenta">* No. FICHA o RADICADO:</label>
                                <input type='number' id="num_ficha_encVenta" name='num_ficha_encVenta' class='form-control' required />
                            </div>
                        </div>
                    </div>

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
                        </div>
                    </div>
                    <div class="integrantes-section">
                        <h5 class="section-title"><i class="fas fa-users me-2"></i>Gestión de Integrantes</h5>

                        <div class="agregar-integrantes-section">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="cant_integVenta" class="form-label">Cantidad a Agregar</label>
                                    <input type="number" id="cant_integVenta" name="cant_integVenta" class="form-control" min="1" max="20" value="1" />
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
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="obs_encVenta" class="form-label">Observaciones y/o Comentarios Adicionales</label>
                                <textarea class="form-control" id="obs_encVenta" rows="4" name="obs_encVenta"
                                    style="text-transform:uppercase;" placeholder="Ingrese observaciones adicionales..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg me-3" id="btnEnviar">
                            <i class="fas fa-save me-2"></i>Actualizar Encuesta
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg" onclick="history.back();">
                            <i class="fas fa-arrow-left me-2"></i>Regresar
                        </button>
                    </div>
            </div>
        </div>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Establecer el valor del campo id_correg como vacío
                if (document.getElementById('id_correg')) {
                    document.getElementById('id_correg').value = '';
                }
            });
        </script>

        <script>
            let agregar = document.getElementById('agregar');
            let contenido = document.getElementById('contenedor');
            let boton_enviar = document.querySelector('#enviar_contacto')
            agregar.addEventListener('click', e => {
                e.preventDefault();
                let clonado = document.querySelector('.clonar');
                if (clonado) {
                    let clon = clonado.cloneNode(true);
                    contenido.appendChild(clon).classList.remove('clonar');
                    let remover_ocutar = contenido.lastChild.childNodes[1].querySelectorAll('span');
                    if (remover_ocutar[0]) {
                        remover_ocutar[0].classList.remove('ocultar');
                    }
                }
            });

            contenido.addEventListener('click', e => {
                e.preventDefault();
                if (e.target.classList.contains('puntero')) {
                    let contenedor = e.target.parentNode.parentNode;
                    contenedor.parentNode.removeChild(contenedor);
                }
            });
        </script>

        <script>
            // Inicializar Select2 para barrios
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
                    minimumInputLength: 0, // Cambiar a 0 para permitir búsquedas vacías
                    width: '100%'
                });

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
                            }
                        });

                        // Verificar si se seleccionó "Otro" para mostrar el campo de texto
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

                // Solo aplicar Select2 a los campos que realmente lo necesitan
                // NO aplicar Select2 a departamentos - usar select nativo normal

                // Select2 solo para municipios (que se cargan dinámicamente y lo necesitan)
                $('#ciudad_expedicion').select2({
                    placeholder: 'Seleccione municipio',
                    width: '100%',
                    allowClear: false
                });
            });
        </script>

</body>

</html>