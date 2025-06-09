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
    <title>BD SISBEN - Movimientos Encuesta</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../barrios.js"> </script>
    <script src="integrantesEncuesta.js"></script>
    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
            padding: 6px 12px;
            font-size: 16px;
            line-height: 30px;
        }

        /* Ajusta la flecha del desplegable */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px !important;
        }

        #integrantes-container {
            display: flex;
            flex-direction: column;
            /* Apila los elementos verticalmente */
            gap: 15px;
            width: 100%;
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
            /* Hace que el botón ocupe todo el ancho */
            justify-self: start;
            /* Alinea el botón a la izquierda */
            margin-top: 10px;
        }

        /* Estilo para selectores largos */
        select.form-control {
            min-width: 100%;
            max-width: 100%;
        }

        .responsive {
            max-width: 100%;
            height: auto;
        }

        .smaller-input {
            width: 200px;
            /* Ajusta el ancho según sea necesario */
        }        .formulario-dinamico {
            margin-bottom: 10px;
            /* Ajusta el margen inferior según sea necesario */
        }

        /* Estilos para alertas de ficha retirada */
        .alert-ficha-retirada {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-left: 5px solid #dc3545;
            font-weight: bold;
        }

        .alert-ficha-retirada .fa-exclamation-triangle {
            color: #dc3545;
            margin-right: 10px;
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
                        .append('<option value="0 - 6">0 - 6</option>')
                        .append('<option value="7 - 12">7 - 12</option>')
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
                        .append('<option value="Poblacion Vinculada">Poblacion Vinculada</option>');

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
                        url: "verificar_encuesta.php",
                        type: "POST",
                        data: {
                            doc_encVenta: documento
                        },
                        dataType: "json",
                        beforeSend: function() {
                            mensajeContainer.removeClass("alert-danger alert-success alert-warning").addClass("alert d-none").html("");
                        },                        success: function(response) {
                            console.log("✅ Respuesta del servidor:", response);

                            if (response.status === "ficha_retirada") {
                                // Mostrar advertencia de ficha retirada
                                mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger")
                                    .html(response.message);
                                
                                // Cargar los datos pero deshabilitar el botón de enviar
                                $("#btnEnviar").prop("disabled", true);
                                
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
                                }                                $("#tipo_documento").val(response.data.tipo_documento);
                                $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                $("#nom_encVenta").val(response.data.nom_encVenta);
                                $("#fec_reg_encVenta").val(response.data.fecha_alta_encVenta.split(' ')[0]); // Solo la fecha, sin la hora

                                // Cargar barrio y comuna
                                if (response.data.id_bar) {
                                    // Crear option y agregarlo al select
                                    const newOption = new Option("Cargando...", response.data.id_bar, true, true);
                                    $("#id_barrios").append(newOption).trigger('change');
                                }

                                // Limpiar integrantes existentes
                                $("#integrantes-container").empty();                                // Cargar integrantes (solo lectura para ficha retirada)
                                if (response.integrantes && response.integrantes.length > 0) {
                                    response.integrantes.forEach(function(integrante) {
                                        var integranteDiv = $("<div>").addClass("formulario-dinamico");

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
                                            createReadOnlyFormGroup("cant_integVenta[]", "Cantidad", integrante.cant_integVenta),
                                            createReadOnlyFormGroup("gen_integVenta[]", "Género", integrante.gen_integVenta),
                                            createReadOnlyFormGroup("rango_integVenta[]", "Rango edad", integrante.rango_integVenta),
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
                                }                            } else if (response.status === "existe") {
                                mensajeContainer.removeClass("d-none alert-danger alert-warning").addClass("alert alert-success")
                                    .html("✔ Encuesta encontrada.");
                                $("#btnEnviar").prop("disabled", false);

                                // Llenar todos los campos principales
                                $("#fec_reg_encVenta").val(response.data.fecha_alta_encVenta.split(' ')[0]); // Solo la fecha, sin la hora
                                $("#nom_encVenta").val(response.data.nom_encVenta);
                                $("#tipo_documento").val(response.data.tipo_documento);
                                $("#departamento_expedicion").val(response.data.departamento_expedicion);
                                $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                $("#dir_encVenta").val(response.data.dir_encVenta);
                                $("#zona_encVenta").val(response.data.zona_encVenta);
                                $("#num_ficha_encVenta").val(response.data.num_ficha_encVenta);
                                // $("#selectEF").val(response.data.tram_solic_encVenta); // No cargar automáticamente, permitir selección manual
                                $("#nocturno").val(response.data.sisben_nocturno);
                                $("#obs_encVenta").val(response.data.obs_encVenta);
                                $("#otro_bar_ver_encVenta").val(response.data.otro_bar_ver_encVenta);

                                // Cargar municipio
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
                                    }
                                });

                                // Cargar barrio y comuna
                                if (response.data.id_bar) {
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
                                                let option = new Option(barrio.text, barrio.id, true, true);
                                                $('#id_barrios').append(option).trigger('change');

                                                // Cargar comuna
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
                                    });
                                }

                                // Limpiar integrantes existentes
                                $("#integrantes-container").empty();

                                // Cargar integrantes
                                if (response.integrantes && response.integrantes.length > 0) {
                                    response.integrantes.forEach(function(integrante) {
                                        var integranteDiv = $("<div>").addClass("formulario-dinamico");

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

                                        var generoSelect = createFormGroup(
                                            "gen_integVenta[]",
                                            "Identidad de Género",
                                            $("<select>")
                                            .attr("name", "gen_integVenta[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="F"' + (integrante.gen_integVenta === 'F' ? ' selected' : '') + '>Femenino</option>')
                                            .append('<option value="M"' + (integrante.gen_integVenta === 'M' ? ' selected' : '') + '>Masculino</option>')
                                            .append('<option value="O"' + (integrante.gen_integVenta === 'O' ? ' selected' : '') + '>Otro</option>')
                                        );

                                        var rangoEdadSelect = createFormGroup(
                                            "rango_integVenta[]",
                                            "Rango de edad",
                                            $("<select>")
                                            .attr("name", "rango_integVenta[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="0 - 6"' + (integrante.rango_integVenta === '0 - 6' ? ' selected' : '') + '>0 - 6</option>')
                                            .append('<option value="7 - 12"' + (integrante.rango_integVenta === '7 - 12' ? ' selected' : '') + '>7 - 12</option>')
                                            .append('<option value="13 - 17"' + (integrante.rango_integVenta === '13 - 17' ? ' selected' : '') + '>13 - 17</option>')
                                            .append('<option value="18 - 28"' + (integrante.rango_integVenta === '18 - 28' ? ' selected' : '') + '>18 - 28</option>')
                                            .append('<option value="29 - 45"' + (integrante.rango_integVenta === '29 - 45' ? ' selected' : '') + '>29 - 45</option>')
                                            .append('<option value="46 - 64"' + (integrante.rango_integVenta === '46 - 64' ? ' selected' : '') + '>46 - 64</option>')
                                            .append('<option value="Mayor o igual a 65"' + (integrante.rango_integVenta === 'Mayor o igual a 65' ? ' selected' : '') + '>Mayor o igual a 65</option>')
                                        );

                                        var OrientacionSexual = createFormGroup(
                                            "orientacionSexual[]",
                                            "Orientación Sexual",
                                            $("<select>")
                                            .attr("name", "orientacionSexual[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Asexual"' + (integrante.orientacionSexual === 'Asexual' ? ' selected' : '') + '>Asexual</option>')
                                            .append('<option value="Bisexual"' + (integrante.orientacionSexual === 'Bisexual' ? ' selected' : '') + '>Bisexual</option>')
                                            .append('<option value="Heterosexual"' + (integrante.orientacionSexual === 'Heterosexual' ? ' selected' : '') + '>Heterosexual</option>')
                                            .append('<option value="Homosexual"' + (integrante.orientacionSexual === 'Homosexual' ? ' selected' : '') + '>Homosexual</option>')
                                            .append('<option value="Otro"' + (integrante.orientacionSexual === 'Otro' ? ' selected' : '') + '>Otro</option>')
                                        );

                                        var condicionDiscapacidad = createFormGroup(
                                            "condicionDiscapacidad[]",
                                            "Condición de Discapacidad",
                                            $("<select>")
                                            .attr("name", "condicionDiscapacidad[]")
                                            .addClass("form-control smaller-input condicion-discapacidad")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Si"' + (integrante.condicionDiscapacidad === 'Si' ? ' selected' : '') + '>Sí</option>')
                                            .append('<option value="No"' + (integrante.condicionDiscapacidad === 'No' ? ' selected' : '') + '>No</option>')
                                        );

                                        var discapacidadSelect = createFormGroup(
                                            "tipoDiscapacidad[]",
                                            "Tipo de Discapacidad",
                                            $("<select>")
                                            .attr("name", "tipoDiscapacidad[]")
                                            .addClass("form-control smaller-input tipo-discapacidad")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Auditiva"' + (integrante.tipoDiscapacidad === 'Auditiva' ? ' selected' : '') + '>Auditiva</option>')
                                            .append('<option value="Física"' + (integrante.tipoDiscapacidad === 'Física' ? ' selected' : '') + '>Física</option>')
                                            .append('<option value="Intelectual"' + (integrante.tipoDiscapacidad === 'Intelectual' ? ' selected' : '') + '>Intelectual</option>')
                                            .append('<option value="Múltiple"' + (integrante.tipoDiscapacidad === 'Múltiple' ? ' selected' : '') + '>Múltiple</option>')
                                            .append('<option value="Psicosocial"' + (integrante.tipoDiscapacidad === 'Psicosocial' ? ' selected' : '') + '>Psicosocial</option>')
                                            .append('<option value="Sordoceguera"' + (integrante.tipoDiscapacidad === 'Sordoceguera' ? ' selected' : '') + '>Sordoceguera</option>')
                                            .append('<option value="Visual"' + (integrante.tipoDiscapacidad === 'Visual' ? ' selected' : '') + '>Visual</option>')
                                        );

                                        // Mostrar/ocultar tipo de discapacidad según la condición
                                        if (integrante.condicionDiscapacidad !== 'Si') {
                                            discapacidadSelect.hide();
                                        }

                                        var GrupoEtnico = createFormGroup(
                                            "grupoEtnico[]",
                                            "Grupo Étnico",
                                            $("<select>")
                                            .attr("name", "grupoEtnico[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Indigena"' + (integrante.grupoEtnico === 'Indigena' ? ' selected' : '') + '>Indígena</option>')
                                            .append('<option value="Negro(a) / Mulato(a) / Afrocolombiano(a)"' + (integrante.grupoEtnico === 'Negro(a) / Mulato(a) / Afrocolombiano(a)' ? ' selected' : '') + '>Negro(a) / Mulato(a) / Afrocolombiano(a)</option>')
                                            .append('<option value="Raizal"' + (integrante.grupoEtnico === 'Raizal' ? ' selected' : '') + '>Raizal</option>')
                                            .append('<option value="Palenquero de San Basilio"' + (integrante.grupoEtnico === 'Palenquero de San Basilio' ? ' selected' : '') + '>Palenquero de San Basilio</option>')
                                            .append('<option value="Mestizo"' + (integrante.grupoEtnico === 'Mestizo' ? ' selected' : '') + '>Mestizo</option>')
                                            .append('<option value="Gitano (rom)"' + (integrante.grupoEtnico === 'Gitano (rom)' ? ' selected' : '') + '>Gitano (rom)</option>')
                                            .append('<option value="Ninguno"' + (integrante.grupoEtnico === 'Ninguno' ? ' selected' : '') + '>Ninguno</option>')
                                        );

                                        var victima = createFormGroup(
                                            "victima[]",
                                            "¿Es víctima?",
                                            $("<select>")
                                            .attr("name", "victima[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Si"' + (integrante.victima === 'Si' ? ' selected' : '') + '>Sí</option>')
                                            .append('<option value="No"' + (integrante.victima === 'No' ? ' selected' : '') + '>No</option>')
                                        );

                                        var mujerGestante = createFormGroup(
                                            "mujerGestante[]",
                                            "¿Mujer gestante?",
                                            $("<select>")
                                            .attr("name", "mujerGestante[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Si"' + (integrante.mujerGestante === 'Si' ? ' selected' : '') + '>Sí</option>')
                                            .append('<option value="No"' + (integrante.mujerGestante === 'No' ? ' selected' : '') + '>No</option>')
                                        );

                                        var cabezaFamilia = createFormGroup(
                                            "cabezaFamilia[]",
                                            "¿Cabeza de familia?",
                                            $("<select>")
                                            .attr("name", "cabezaFamilia[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Si"' + (integrante.cabezaFamilia === 'Si' ? ' selected' : '') + '>Sí</option>')
                                            .append('<option value="No"' + (integrante.cabezaFamilia === 'No' ? ' selected' : '') + '>No</option>')
                                        );

                                        var experienciaMigratoria = createFormGroup(
                                            "experienciaMigratoria[]",
                                            "¿Tiene experiencia migratoria?",
                                            $("<select>")
                                            .attr("name", "experienciaMigratoria[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Si"' + (integrante.experienciaMigratoria === 'Si' ? ' selected' : '') + '>Sí</option>')
                                            .append('<option value="No"' + (integrante.experienciaMigratoria === 'No' ? ' selected' : '') + '>No</option>')
                                        );

                                        var seguridadSalud = createFormGroup(
                                            "seguridadSalud[]",
                                            "Seguridad en salud",
                                            $("<select>")
                                            .attr("name", "seguridadSalud[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Regimen Contributivo"' + (integrante.seguridadSalud === 'Regimen Contributivo' ? ' selected' : '') + '>Régimen Contributivo</option>')
                                            .append('<option value="Regimen Subsidiado"' + (integrante.seguridadSalud === 'Regimen Subsidiado' ? ' selected' : '') + '>Régimen Subsidiado</option>')
                                            .append('<option value="Poblacion Vinculada"' + (integrante.seguridadSalud === 'Poblacion Vinculada' ? ' selected' : '') + '>Población Vinculada</option>')
                                        );

                                        var nivelEducativo = createFormGroup(
                                            "nivelEducativo[]",
                                            "Nivel educativo",
                                            $("<select>")
                                            .attr("name", "nivelEducativo[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Ninguno"' + (integrante.nivelEducativo === 'Ninguno' ? ' selected' : '') + '>Ninguno</option>')
                                            .append('<option value="Preescolar"' + (integrante.nivelEducativo === 'Preescolar' ? ' selected' : '') + '>Preescolar</option>')
                                            .append('<option value="Primaria"' + (integrante.nivelEducativo === 'Primaria' ? ' selected' : '') + '>Primaria</option>')
                                            .append('<option value="Secundaria"' + (integrante.nivelEducativo === 'Secundaria' ? ' selected' : '') + '>Secundaria</option>')
                                            .append('<option value="Media Academica o Clasica"' + (integrante.nivelEducativo === 'Media Academica o Clasica' ? ' selected' : '') + '>Media Académica o Clásica</option>')
                                            .append('<option value="Media Tecnica"' + (integrante.nivelEducativo === 'Media Tecnica' ? ' selected' : '') + '>Media Técnica</option>')
                                            .append('<option value="Normalista"' + (integrante.nivelEducativo === 'Normalista' ? ' selected' : '') + '>Normalista</option>')
                                            .append('<option value="Universitario"' + (integrante.nivelEducativo === 'Universitario' ? ' selected' : '') + '>Universitario</option>')
                                            .append('<option value="Tecnica Profesional"' + (integrante.nivelEducativo === 'Tecnica Profesional' ? ' selected' : '') + '>Técnica Profesional</option>')
                                            .append('<option value="Tecnologica"' + (integrante.nivelEducativo === 'Tecnologica' ? ' selected' : '') + '>Tecnológica</option>')
                                            .append('<option value="Profesional"' + (integrante.nivelEducativo === 'Profesional' ? ' selected' : '') + '>Profesional</option>')
                                            .append('<option value="Especializacion"' + (integrante.nivelEducativo === 'Especializacion' ? ' selected' : '') + '>Especialización</option>')
                                        );

                                        var condicionOcupacion = createFormGroup(
                                            "condicionOcupacion[]",
                                            "Condición de ocupación",
                                            $("<select>")
                                            .attr("name", "condicionOcupacion[]")
                                            .addClass("form-control smaller-input")
                                            .append('<option value="">Seleccione...</option>')
                                            .append('<option value="Ama de casa"' + (integrante.condicionOcupacion === 'Ama de casa' ? ' selected' : '') + '>Ama de casa</option>')
                                            .append('<option value="Buscando Empleo"' + (integrante.condicionOcupacion === 'Buscando Empleo' ? ' selected' : '') + '>Buscando Empleo</option>')
                                            .append('<option value="Desempleado(a)"' + (integrante.condicionOcupacion === 'Desempleado(a)' ? ' selected' : '') + '>Desempleado(a)</option>')
                                            .append('<option value="Empleado(a)"' + (integrante.condicionOcupacion === 'Empleado(a)' ? ' selected' : '') + '>Empleado(a)</option>')
                                            .append('<option value="Independiente"' + (integrante.condicionOcupacion === 'Independiente' ? ' selected' : '') + '>Independiente</option>')
                                            .append('<option value="Estudiante"' + (integrante.condicionOcupacion === 'Estudiante' ? ' selected' : '') + '>Estudiante</option>')
                                            .append('<option value="Pensionado"' + (integrante.condicionOcupacion === 'Pensionado' ? ' selected' : '') + '>Pensionado</option>')
                                            .append('<option value="Ninguno"' + (integrante.condicionOcupacion === 'Ninguno' ? ' selected' : '') + '>Ninguno</option>')
                                        );
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
                                                actualizarTotal();
                                            });

                                        // Agregar evento para mostrar/ocultar el select de discapacidad
                                        condicionDiscapacidad.find('select').on("change", function() {
                                            var currentDiscapacidadSelect = $(this).closest('.formulario-dinamico').find('.tipo-discapacidad');
                                            if ($(this).val() === "Si") {
                                                currentDiscapacidadSelect.show();
                                            } else {
                                                currentDiscapacidadSelect.hide();
                                            }
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

                                        $("#integrantes-container").append(integranteDiv);
                                    });

                                    // Actualizar el total
                                    actualizarTotal();
                                }

                            } else if (response.status === "no_existe") {
                                mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                    .html("⚠️ El documento no está registrado en la base de encuestas.");
                                $("#btnEnviar").prop("disabled", false);
                            } else {
                                mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                    .html("⚠️ El documento no está registrado.");
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
                    // Si el campo está vacío, limpiar todo
                    mensajeContainer.addClass("d-none").html("");
                    $("#btnEnviar").prop("disabled", false);
                    // Limpiar campos y contenedor de integrantes
                    $("#integrantes-container").empty();
                }
            });
        });
    </script>

    <form id="form_contacto" action='updateEncuesta.php' method="POST" enctype="multipart/form-data">

        <div class="container pt-2">
            <h1><b><i class="fa-solid fa-building"></i> MOVIMIENTOS ENCUESTA VENTANILLA</b></h1>
            <p><i><b>
                        <font size=3 color=#c68615>*Datos obligatorios</i></b></font>
            </p>
            <div id="mensajeDocumentoContainer" class="alert d-none"></div> <!-- Mensaje arriba -->
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
                            value="<?php echo date('Y-m-d'); ?>" readonly />
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
                </div>
            </div>
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
                            <option value="modificación datos persona">Modificación datos persona </option>
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

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="cant_integVenta">CANTIDAD:</label>
                        <input type="number" id="cant_integVenta" name="cant_integVenta" class="form-control" />
                    </div>
                    <div class="form-group col-md-3 d-flex flex-column align-items-start">
                        <label for=""></label>
                        <button type="button" class="btn btn-primary mt-auto" id="agregar">Agregar +</button>
                    </div>
                </div>
            </div>

            <div id="integrantes-container"></div>

            <div id="contenedor"></div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="obs_encVenta">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs_encVenta" style="text-transform:uppercase;"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-success" id="btnEnviar">
                <span class="spinner-border spinner-border-sm"></span>
                ACTUALIZAR ENCUESTA
            </button>

            <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
            </button>
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
                minimumInputLength: 1,
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
        });
    </script>

</body>

</html>