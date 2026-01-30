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
    <title>BD SISBEN - Encuesta Campo</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../barrios.js"> </script>
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
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

        .primer-integrante {
            border: 2px solid #dc3545;
            background-color: #fff5f5;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .primer-integrante .integrante-header {
            color: #dc3545 !important;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .integrante-adicional {
            border: 2px solid #6c757d;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .integrante-adicional .integrante-header {
            color: #6c757d;
            font-weight: normal;
            margin-bottom: 1rem;
        }

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

        .readonly-integrante {
            opacity: 1;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
        }

        .readonly-integrante .form-control[readonly] {
            background-color: #ffffff !important;
            border-color: #28a745 !important;
            color: #495057 !important;
            font-weight: 500 !important;
        }

        .readonly-integrante .integrante-header {
            color: #28a745 !important;
            font-weight: bold !important;
            border-bottom: 2px solid #28a745;
            padding-bottom: 0.5rem;
        }

        .readonly-integrante .form-group label {
            color: #495057 !important;
            font-weight: 600 !important;
            font-size: 0.9rem;
        }

        .readonly-integrante .badge-success {
            background-color: #28a745 !important;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
    </style>

    <script>
        // departamento/ciudad expedicion handlers removed

        $(document).ready(function() {
            function actualizarTotal() {
                let total = 0;
                let totalReadOnly = 0;
                let totalEditables = 0;

                // Contar integrantes precargados (solo lectura)
                $(".readonly-integrante input[name='cant_integVenta[]']").each(function() {
                    let valor = parseInt($(this).val()) || 0;
                    totalReadOnly += valor;
                });

                // Contar integrantes editables (nuevos)
                $(".formulario-dinamico:not(.readonly-integrante) input[name='cant_integVenta[]']").each(function() {
                    let valor = parseInt($(this).val()) || 0;
                    totalEditables += valor;
                });

                total = totalReadOnly + totalEditables;
                $("#total_integrantes").val(total);

                // Actualizar solo el contador de formularios dinámicos (editables)
                $("#cant_integVenta").val($(".formulario-dinamico:not(.readonly-integrante)").length);
            }

            $("#agregar").click(function() {
                var inputCantidad = $("#cant_integVenta");
                var cantidadValor = parseInt(inputCantidad.val());

                if (!cantidadValor || cantidadValor <= 0) {
                    alert("Por favor, ingresa una cantidad válida de integrantes.");
                    return;
                }

                // Contar integrantes existentes para la numeración
                var integrantesExistentes = $("#integrantes-container .formulario-dinamico").length;

                for (var i = 0; i < cantidadValor; i++) {
                    var numeroIntegrante = integrantesExistentes + i + 1;
                    var integranteDiv = $("<div>").addClass("formulario-dinamico integrante-adicional").attr("data-es-nuevo", "true");

                    var integranteHeader = $("<div>").addClass("integrante-header").text("Integrante " + numeroIntegrante + " (Opcional)");
                    integranteDiv.append(integranteHeader);

                    var fieldsContainer = $("<div>").addClass("form-row-custom");

                    var cantidadInput = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "cant_integVenta[]")
                        .addClass("form-control smaller-input")
                        .val(1)
                        .on("input", actualizarTotal)
                        .attr("placeholder", "Cantidad")
                        .attr("readonly", true);

                    var generoGroup = $("<div>").addClass("form-group-dinamico");
                    generoGroup.append($("<label>").text("Identidad de Género"));
                    var generoSelect = $("<select>")
                        .attr("name", "gen_integVenta[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Identidad Genero</option>')
                        .append('<option value="F">F</option>')
                        .append('<option value="M">M</option>')
                        .append('<option value="O">Otro</option>');
                    generoGroup.append(generoSelect);
                    fieldsContainer.append(generoGroup);

                    /*
                    var orientacionGroup = $("<div>").addClass("form-group-dinamico");
                    orientacionGroup.append($("<label>").text("Orientación Sexual"));
                    var OrientacionSexual = $("<select>")
                        .attr("name", "orientacionSexual[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Orientacion Sexual</option>')
                        .append('<option value="Asexual">Asexual</option>')
                        .append('<option value="Bisexual">Bisexual</option>')
                        .append('<option value="Heterosexual">Heterosexual</option>')
                        .append('<option value="Homosexual">Homosexual</option>')
                        .append('<option value="Otro">Otro</option>');
                    orientacionGroup.append(OrientacionSexual);
                    fieldsContainer.append(orientacionGroup);
                    */

                    var rangoEdadGroup = $("<div>").addClass("form-group-dinamico");
                    rangoEdadGroup.append($("<label>").text("Rango de Edad"));
                    var rangoEdadSelect = $("<select>")
                        .attr("name", "rango_integVenta[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
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

                    /*
                    var discapacidadGroup = $("<div>").addClass("form-group-dinamico");
                    discapacidadGroup.append($("<label>").text("Condición de Discapacidad"));
                    var condicionDiscapacidad = $("<select>")
                        .attr("name", "condicionDiscapacidad[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Condicion Discapacidad</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    discapacidadGroup.append(condicionDiscapacidad);
                    fieldsContainer.append(discapacidadGroup);
                    */

                    /*
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
                    */

                    /*
                    var grupoEtnicoGroup = $("<div>").addClass("form-group-dinamico");
                    grupoEtnicoGroup.append($("<label>").text("Grupo Étnico"));
                    var GrupoEtnico = $("<select>")
                        .attr("name", "grupoEtnico[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
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
                    */

                    /*
                    var victimaGroup = $("<div>").addClass("form-group-dinamico");
                    victimaGroup.append($("<label>").text("¿Es víctima?"));
                    var victima = $("<select>")
                        .attr("name", "victima[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Victima</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    victimaGroup.append(victima);
                    fieldsContainer.append(victimaGroup);
                    */

                    /*
                    var mujerGestanteGroup = $("<div>").addClass("form-group-dinamico");
                    mujerGestanteGroup.append($("<label>").text("¿Mujer gestante?"));
                    var mujerGestante = $("<select>")
                        .attr("name", "mujerGestante[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Mujer Gestante</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    mujerGestanteGroup.append(mujerGestante);
                    fieldsContainer.append(mujerGestanteGroup);
                    */

                    /*
                    var cabezaFamiliaGroup = $("<div>").addClass("form-group-dinamico");
                    cabezaFamiliaGroup.append($("<label>").text("¿Cabeza de familia?"));
                    var cabezaFamilia = $("<select>")
                        .attr("name", "cabezaFamilia[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Cabeza de Familia</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    cabezaFamiliaGroup.append(cabezaFamilia);
                    fieldsContainer.append(cabezaFamiliaGroup);
                    */

                    /*
                    var experienciaMigratoriaGroup = $("<div>").addClass("form-group-dinamico");
                    experienciaMigratoriaGroup.append($("<label>").text("¿Tiene experiencia migratoria?"));
                    var experienciaMigratoria = $("<select>")
                        .attr("name", "experienciaMigratoria[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Experiencia Migratoria</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');
                    experienciaMigratoriaGroup.append(experienciaMigratoria);
                    fieldsContainer.append(experienciaMigratoriaGroup);
                    */

                    /*
                    var seguridadSaludGroup = $("<div>").addClass("form-group-dinamico");
                    seguridadSaludGroup.append($("<label>").text("Seguridad en salud"));
                    var seguridadSalud = $("<select>")
                        .attr("name", "seguridadSalud[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
                        .append('<option value="">Seguridad Salud</option>')
                        .append('<option value="Regimen Contributivo">Regimen Contributivo</option>')
                        .append('<option value="Regimen Subsidiado">Regimen Subsidiado</option>')
                        .append('<option value="Poblacion Vinculada">Poblacion Vinculada</option>')
                        .append('<option value="Ninguno">Ninguno</option>');
                    seguridadSaludGroup.append(seguridadSalud);
                    fieldsContainer.append(seguridadSaludGroup);
                    */

                    /*
                    var nivelEducativoGroup = $("<div>").addClass("form-group-dinamico");
                    nivelEducativoGroup.append($("<label>").text("Nivel educativo"));
                    var nivelEducativo = $("<select>")
                        .attr("name", "nivelEducativo[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
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
                    */

                    /*
                    var condicionOcupacionGroup = $("<div>").addClass("form-group-dinamico");
                    condicionOcupacionGroup.append($("<label>").text("Condición de ocupación"));
                    var condicionOcupacion = $("<select>")
                        .attr("name", "condicionOcupacion[]")
                        .addClass("form-control smaller-input")
                        // NO agregamos required para integrantes adicionales
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
                    */

                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger eliminar-integrante")
                        .text("×")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            actualizarTotal();
                        });

                    integranteDiv.append(cantidadInput);
                    integranteDiv.append(fieldsContainer);
                    integranteDiv.append(eliminarBtn);

                    /*
                    condicionDiscapacidad.on("change", function() {
                        const valor = $(this).val();
                        if (valor === "Si") {
                            $(this).closest('.formulario-dinamico').find('.tipo-discapacidad').show();
                        } else {
                            $(this).closest('.formulario-dinamico').find('.tipo-discapacidad').hide().val("");
                        }
                    });
                    */

                    $("#integrantes-container").append(integranteDiv);
                }

                actualizarTotal();
                inputCantidad.val("");
            });

            // Función para crear el primer integrante con campos requeridos
            function crearPrimerIntegrante(esRequerido) {
                // Si no se especifica, es requerido solo si no hay integrantes precargados
                if (typeof esRequerido === 'undefined') {
                    esRequerido = (window.integrantesPrecargados && window.integrantesPrecargados.length > 0) ? false : true;
                }

                var integranteDiv = $("<div>").addClass("formulario-dinamico primer-integrante").attr("data-es-nuevo", "true");

                var headerText = esRequerido ? "Integrante 1 (Requerido)" : "Integrante 1";
                var headerColor = esRequerido ? '#dc3545' : '#007bff';

                var integranteHeader = $("<div>").addClass("integrante-header").text(headerText).css({
                    'color': headerColor,
                    'font-weight': 'bold',
                    'margin-bottom': '1rem'
                });
                integranteDiv.append(integranteHeader);

                var fieldsContainer = $("<div>").addClass("form-row-custom");

                var cantidadInput = $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "cant_integVenta[]")
                    .addClass("form-control smaller-input")
                    .val(1)
                    .on("input", actualizarTotal)
                    .attr("placeholder", "Cantidad")
                    .attr("readonly", true);

                var generoGroup = $("<div>").addClass("form-group-dinamico");
                generoGroup.append($("<label>").text("Identidad de Género *"));
                var generoSelect = $("<select>")
                    .attr("name", "gen_integVenta[]")
                    .addClass("form-control smaller-input")
                    .attr("required", esRequerido)
                    .append('<option value="">Identidad Genero</option>')
                    .append('<option value="F">F</option>')
                    .append('<option value="M">M</option>')
                    .append('<option value="O">Otro</option>');
                generoGroup.append(generoSelect);
                fieldsContainer.append(generoGroup);

                /*
                var orientacionGroup = $("<div>").addClass("form-group-dinamico");
                orientacionGroup.append($("<label>").text("Orientación Sexual *"));
                var OrientacionSexual = $("<select>")
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
                */

                var rangoEdadGroup = $("<div>").addClass("form-group-dinamico");
                rangoEdadGroup.append($("<label>").text("Rango de Edad *"));
                var rangoEdadSelect = $("<select>")
                    .attr("name", "rango_integVenta[]")
                    .addClass("form-control smaller-input")
                    .attr("required", esRequerido)
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

                /*
                var discapacidadGroup = $("<div>").addClass("form-group-dinamico");
                discapacidadGroup.append($("<label>").text("Condición de Discapacidad *"));
                var condicionDiscapacidad = $("<select>")
                    .attr("name", "condicionDiscapacidad[]")
                    .addClass("form-control smaller-input")
                    .attr("required", true)
                    .append('<option value="">Condicion Discapacidad</option>')
                    .append('<option value="Si">Si</option>')
                    .append('<option value="No">No</option>');
                discapacidadGroup.append(condicionDiscapacidad);
                fieldsContainer.append(discapacidadGroup);
                */

                /*
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
                */

                /*
                var grupoEtnicoGroup = $("<div>").addClass("form-group-dinamico");
                grupoEtnicoGroup.append($("<label>").text("Grupo Étnico *"));
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
                grupoEtnicoGroup.append(GrupoEtnico);
                fieldsContainer.append(grupoEtnicoGroup);
                */

                /*
                var victimaGroup = $("<div>").addClass("form-group-dinamico");
                victimaGroup.append($("<label>").text("¿Es víctima? *"));
                var victima = $("<select>")
                    .attr("name", "victima[]")
                    .addClass("form-control smaller-input")
                    .attr("required", true)
                    .append('<option value="">Victima</option>')
                    .append('<option value="Si">Si</option>')
                    .append('<option value="No">No</option>');
                victimaGroup.append(victima);
                fieldsContainer.append(victimaGroup);
                */

                /*
                var mujerGestanteGroup = $("<div>").addClass("form-group-dinamico");
                mujerGestanteGroup.append($("<label>").text("¿Mujer gestante? *"));
                var mujerGestante = $("<select>")
                    .attr("name", "mujerGestante[]")
                    .addClass("form-control smaller-input")
                    .attr("required", true)
                    .append('<option value="">Mujer Gestante</option>')
                    .append('<option value="Si">Si</option>')
                    .append('<option value="No">No</option>');
                mujerGestanteGroup.append(mujerGestante);
                fieldsContainer.append(mujerGestanteGroup);
                */

                /*
                var cabezaFamiliaGroup = $("<div>").addClass("form-group-dinamico");
                cabezaFamiliaGroup.append($("<label>").text("¿Cabeza de familia? *"));
                var cabezaFamilia = $("<select>")
                    .attr("name", "cabezaFamilia[]")
                    .addClass("form-control smaller-input")
                    .attr("required", true)
                    .append('<option value="">Cabeza de Familia</option>')
                    .append('<option value="Si">Si</option>')
                    .append('<option value="No">No</option>');
                cabezaFamiliaGroup.append(cabezaFamilia);
                fieldsContainer.append(cabezaFamiliaGroup);
                */

                /*
                var experienciaMigratoriaGroup = $("<div>").addClass("form-group-dinamico");
                experienciaMigratoriaGroup.append($("<label>").text("¿Tiene experiencia migratoria? *"));
                var experienciaMigratoria = $("<select>")
                    .attr("name", "experienciaMigratoria[]")
                    .addClass("form-control smaller-input")
                    .attr("required", true)
                    .append('<option value="">Experiencia Migratoria</option>')
                    .append('<option value="Si">Si</option>')
                    .append('<option value="No">No</option>');
                experienciaMigratoriaGroup.append(experienciaMigratoria);
                fieldsContainer.append(experienciaMigratoriaGroup);
                */

                /*
                var seguridadSaludGroup = $("<div>").addClass("form-group-dinamico");
                seguridadSaludGroup.append($("<label>").text("Seguridad en salud *"));
                var seguridadSalud = $("<select>")
                    .attr("name", "seguridadSalud[]")
                    .addClass("form-control smaller-input")
                    .attr("required", true)
                    .append('<option value="">Seguridad Salud</option>')
                    .append('<option value="Regimen Contributivo">Regimen Contributivo</option>')
                    .append('<option value="Regimen Subsidiado">Regimen Subsidiado</option>')
                    .append('<option value="Poblacion Vinculada">Poblacion Vinculada</option>')
                    .append('<option value="Ninguno">Ninguno</option>');
                seguridadSaludGroup.append(seguridadSalud);
                fieldsContainer.append(seguridadSaludGroup);
                */

                /*
                var nivelEducativoGroup = $("<div>").addClass("form-group-dinamico");
                nivelEducativoGroup.append($("<label>").text("Nivel educativo *"));
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
                    .append('<option value="Especializacion">Especializacion</option>');
                nivelEducativoGroup.append(nivelEducativo);
                fieldsContainer.append(nivelEducativoGroup);
                */

                /*
                var condicionOcupacionGroup = $("<div>").addClass("form-group-dinamico");
                condicionOcupacionGroup.append($("<label>").text("Condición de ocupación *"));
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
                    .append('<option value="Ninguno">Ninguno</option>');
                condicionOcupacionGroup.append(condicionOcupacion);
                fieldsContainer.append(condicionOcupacionGroup);
                */

                integranteDiv.append(cantidadInput);
                integranteDiv.append(fieldsContainer);
                // No agregamos botón de eliminar al primer integrante

                // Agregar funcionalidad de mostrar/ocultar tipo de discapacidad
                /*
                condicionDiscapacidad.on("change", function() {
                    const valor = $(this).val();
                    if (valor === "Si") {
                        $(this).closest('.formulario-dinamico').find('.tipo-discapacidad').show();
                    } else {
                        $(this).closest('.formulario-dinamico').find('.tipo-discapacidad').hide().val("");
                    }
                });
                */

                $("#integrantes-container").append(integranteDiv);
                actualizarTotal();
            }

            // Crear el primer integrante automáticamente al cargar la página
            crearPrimerIntegrante();

            window.actualizarTotal = actualizarTotal;
        });
    </script>

    <script>
        // AJAX para consulta de documento al perder foco o al presionar Enter
        $(document).ready(function() {
            // Variable global para rastrear si el documento ya existe
            window.documentoYaExiste = false;

            // Array para almacenar IDs de integrantes precargados (no deben insertarse de nuevo)
            window.integrantesPrecargados = [];

            var buscarEncuesta = function() {
                var documento = $("#doc_encVenta").val().toString().trim();
                console.log('buscarEncuesta triggered for documento:', documento);

                // evitar búsquedas muy cortas (mínimo 3 caracteres)
                if (documento.length < 3) {
                    console.log('documento demasiado corto (<3), se omite la búsqueda');
                    window.documentoYaExiste = false;
                    return;
                }
                var mensajeContainer = $("#mensajeDocumentoContainer");

                if (!documento) {
                    // Limpiar mensajes y rehabilitar controles cuando no hay documento
                    mensajeContainer.addClass("d-none").html("");
                    limpiarIntegrantesPrecargados();
                    $("#btnEnviar").prop("disabled", false);
                    window.documentoYaExiste = false;
                    return;
                }

                $("#btnEnviar").prop("disabled", true);
                mensajeContainer.removeClass("d-none").removeClass('alert-success alert-warning alert-danger').addClass("alert alert-info").html("🔍 Consultando encuesta previa...");

                $.ajax({
                    url: 'verificar_documento.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        doc_encVenta: documento
                    },
                    success: function(response) {
                        if (response.status === 'existe_encuesta') {
                            var d = response.data;
                            // Marcar que el documento ya existe pero permitir nueva encuesta
                            window.documentoYaExiste = false;

                            mensajeContainer.removeClass('alert-info alert-warning alert-danger').addClass('alert alert-info')
                                .html('ℹ️ Este documento ya tiene una encuesta registrada. Los datos se han precargado pero puede agregar una nueva encuesta.');

                            // Prefill main fields
                            $("#nom_encVenta").val(d.nom_encVenta || "");
                            $("#tipo_documento").val(d.tipo_documento || "");

                            // departamento/ciudad expedicion and fecha_expedicion removed from form; no prefill performed
                            $("#fecha_nacimiento").val(d.fecha_nacimiento || "");
                            $("#dir_encVenta").val(d.dir_encVenta || "");

                            // Barrio/vereda (select2) - precarga correcta con nombre real
                            if (d.id_bar) {
                                var idbar = d.id_bar;

                                // Buscar el nombre real del barrio desde la base de datos
                                $.ajax({
                                    url: '../buscar_barrios.php',
                                    type: 'GET',
                                    data: {
                                        id: idbar
                                    },
                                    dataType: 'json',
                                    success: function(barrioData) {
                                        if (barrioData && barrioData.length > 0) {
                                            var barrio = barrioData[0];
                                            var labelBar = barrio.text;

                                            // Crear la opción con el nombre real
                                            if ($("#id_barrios").find("option[value='" + idbar + "']").length === 0) {
                                                var newOpt = new Option(labelBar, idbar, true, true);
                                                $("#id_barrios").append(newOpt);
                                            } else {
                                                $("#id_barrios").val(idbar);
                                            }

                                            // Disparar el evento de cambio para cargar las comunas correspondientes
                                            $("#id_barrios").trigger('change');

                                            // Configurar comuna después de que se carguen
                                            setTimeout(function() {
                                                if (d.id_com) {
                                                    $("#id_comunas").val(d.id_com);
                                                }
                                            }, 500);
                                        }
                                    },
                                    error: function() {
                                        console.log('Error al obtener nombre del barrio, usando fallback');
                                        var labelBar = d.id_bar_nombre || 'Barrio ID: ' + idbar;

                                        if ($("#id_barrios").find("option[value='" + idbar + "']").length === 0) {
                                            var newOpt = new Option(labelBar, idbar, true, true);
                                            $("#id_barrios").append(newOpt);
                                        } else {
                                            $("#id_barrios").val(idbar);
                                        }

                                        $("#id_barrios").trigger('change');
                                        setTimeout(function() {
                                            if (d.id_com) {
                                                $("#id_comunas").val(d.id_com);
                                            }
                                        }, 500);
                                    }
                                });
                            }

                            // Comuna/corregimiento - fallback si no hay barrio pero sí comuna
                            if (d.id_com && !d.id_bar) {
                                var labelCom = (d.id_com_nombre && d.id_com_nombre !== '') ? d.id_com_nombre : 'Cargado';
                                if ($("#id_comunas").find("option[value='" + d.id_com + "']").length === 0) {
                                    $("#id_comunas").append(new Option(labelCom, d.id_com, true, true));
                                } else {
                                    $("#id_comunas").val(d.id_com);
                                }
                            }

                            $("#zona_encVenta").val(d.zona_encVenta || "");
                            $("#tram_solic_encVenta").val(d.tram_solic_encVenta || "");
                            $("#num_ficha_encVenta").val(d.num_ficha_encVenta || "");
                            $("#num_visita").val(d.num_visita || "");
                            $("#estado_ficha").val(d.estado_ficha || "");
                            $("#tipo_proceso").val(d.tipo_proceso || "");
                            $("#integra_encVenta").val(d.integra_encVenta || "");
                            $("#sisben_nocturno").val(d.sisben_nocturno || "");
                            $("#obs_encVenta").val(d.obs_encVenta || "");

                            // PERMITIR nueva encuesta - habilitar formulario y botón
                            $("#form_contacto :input").prop("disabled", false);
                            $("#fec_reg_encVenta").prop("disabled", true); // Fecha registro siempre deshabilitada
                            $("#btnEnviar").prop("disabled", false);

                            // Limpiar integrantes precargados anteriores
                            limpiarIntegrantesPrecargados();

                            // Cargar integrantes precargados si existen
                            if (response.data.integrantes && response.data.integrantes.length > 0) {
                                console.log('📋 Cargando ' + response.data.integrantes.length + ' integrantes precargados');
                                window.integrantesPrecargados = response.data.integrantes;
                                response.data.integrantes.forEach(function(integ, index) {
                                    crearIntegranteReadOnly(integ, index + 1);
                                });
                                actualizarTotal();

                                // Si existe el primer integrante nuevo, actualizar su header a "NO requerido"
                                var primerIntegrante = $(".primer-integrante");
                                if (primerIntegrante.length > 0) {
                                    primerIntegrante.find(".integrante-header")
                                        .text("Integrante 1")
                                        .css('color', '#007bff');

                                    // Hacer los campos NO requeridos ya que hay precargados
                                    primerIntegrante.find("select[name='gen_integVenta[]']").attr("required", false);
                                    primerIntegrante.find("select[name='rango_integVenta[]']").attr("required", false);
                                }
                            }
                        } else if (response.status === 'existe_info') {
                            window.documentoYaExiste = false;

                            mensajeContainer.removeClass('alert-info alert-warning alert-danger').addClass('alert alert-success')
                                .html('✅ Documento encontrado en Información.');

                            var d = response.data;
                            $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");
                            $("#nom_encVenta").val(d.nom_info || "");
                            $("#tipo_documento").val(d.tipo_documento || "");
                            $("#fecha_nacimiento").val(d.fecha_nacimiento || "");
                            $("#fecha_preregistro").val("");

                            // Limpiar campos que no vienen de información
                            $("#dir_encVenta").val("");
                            $("#id_barrios").val(null).trigger('change');
                            $("#id_comunas").val("").prop("disabled", true);
                            $("#otro_bar_ver_encVenta").val("");
                            $("#otro_barrio_container").hide();
                            $("#zona_encVenta").val("");
                            $("#selectEF").val("");
                            $("#num_ficha_encVenta").val("");
                            $("#num_visita").val("");
                            $("#estado_ficha").val("");
                            $("#tipo_proceso").val("");
                            $("#total_integrantes").val("");
                            $("#exampleFormControlTextarea1").val("");

                            // Limpiar integrantes precargados ya que es información nueva
                            limpiarIntegrantesPrecargados();

                            // Habilitar formulario y botón
                            $("#form_contacto :input").prop("disabled", false);
                            $("#fec_reg_encVenta").prop("disabled", true); // Fecha registro siempre deshabilitada
                            $("#btnEnviar").prop("disabled", false);
                        } else if (response.status === 'no_existe' || response.status === 'not_found' || response.status === 'empty') {
                            window.documentoYaExiste = false;

                            mensajeContainer.removeClass('alert-info alert-success alert-danger').addClass('alert alert-warning')
                                .html('⚠️ No se encontró encuesta previa con ese documento.');
                            $("#fec_reg_encVenta").val("<?php echo date('Y-m-d'); ?>");

                            // Limpiar todos los campos del formulario exceptao el documento
                            $("#nom_encVenta").val("");
                            $("#tipo_documento").val("");
                            $("#fecha_nacimiento").val("");
                            $("#fecha_preregistro").val("");
                            $("#dir_encVenta").val("");

                            // Limpiar select2 de barrios y comunas
                            $("#id_barrios").val(null).trigger('change');
                            $("#id_comunas").val("").prop("disabled", true);
                            $("#otro_bar_ver_encVenta").val("");
                            $("#otro_barrio_container").hide();

                            // Limpiar campos de trámite
                            $("#zona_encVenta").val("");
                            $("#selectEF").val("");
                            $("#num_ficha_encVenta").val("");
                            $("#num_visita").val("");
                            $("#estado_ficha").val("");
                            $("#tipo_proceso").val("");
                            $("#total_integrantes").val("");
                            $("#exampleFormControlTextarea1").val("");

                            // Limpiar integrantes precargados y rehabilitar controles
                            limpiarIntegrantesPrecargados();

                            // Habilitar formulario y botón
                            $("#form_contacto :input").prop("disabled", false);
                            $("#fec_reg_encVenta").prop("disabled", true); // Fecha registro siempre deshabilitada
                            $("#btnEnviar").prop("disabled", false);
                        } else if (response.status === 'error') {
                            window.documentoYaExiste = false;

                            var errorMsg = '❌ Error en el servidor';
                            if (response.message) {
                                errorMsg += ': ' + response.message;
                            }
                            if (response.debug) {
                                console.error('Error debug:', response.debug);
                            }

                            mensajeContainer.removeClass('alert-info alert-success alert-warning').addClass('alert alert-danger')
                                .html(errorMsg);

                            // Limpiar todos los campos excepto documento
                            $("#nom_encVenta").val("");
                            $("#tipo_documento").val("");
                            $("#fecha_nacimiento").val("");
                            $("#fecha_preregistro").val("");
                            $("#dir_encVenta").val("");
                            $("#id_barrios").val(null).trigger('change');
                            $("#id_comunas").val("").prop("disabled", true);
                            $("#otro_bar_ver_encVenta").val("");
                            $("#otro_barrio_container").hide();
                            $("#zona_encVenta").val("");
                            $("#selectEF").val("");
                            $("#num_ficha_encVenta").val("");
                            $("#num_visita").val("");
                            $("#estado_ficha").val("");
                            $("#tipo_proceso").val("");
                            $("#total_integrantes").val("");
                            $("#exampleFormControlTextarea1").val("");

                            // Limpiar integrantes precargados y rehabilitar controles
                            limpiarIntegrantesPrecargados();

                            // Habilitar formulario y botón
                            $("#form_contacto :input").prop("disabled", false);
                            $("#fec_reg_encVenta").prop("disabled", true); // Fecha registro siempre deshabilitada
                            $("#btnEnviar").prop("disabled", false);
                        } else {
                            window.documentoYaExiste = false;

                            mensajeContainer.removeClass('alert-info alert-success alert-warning').addClass('alert alert-danger')
                                .html('❌ Error en la búsqueda de la encuesta.');

                            // Limpiar todos los campos excepto documento
                            $("#nom_encVenta").val("");
                            $("#tipo_documento").val("");
                            $("#fecha_nacimiento").val("");
                            $("#fecha_preregistro").val("");
                            $("#dir_encVenta").val("");
                            $("#id_barrios").val(null).trigger('change');
                            $("#id_comunas").val("").prop("disabled", true);
                            $("#otro_bar_ver_encVenta").val("");
                            $("#otro_barrio_container").hide();
                            $("#zona_encVenta").val("");
                            $("#selectEF").val("");
                            $("#num_ficha_encVenta").val("");
                            $("#num_visita").val("");
                            $("#estado_ficha").val("");
                            $("#tipo_proceso").val("");
                            $("#total_integrantes").val("");
                            $("#exampleFormControlTextarea1").val("");

                            // Limpiar integrantes precargados y rehabilitar controles
                            limpiarIntegrantesPrecargados();

                            // Habilitar formulario y botón
                            $("#form_contacto :input").prop("disabled", false);
                            $("#fec_reg_encVenta").prop("disabled", true); // Fecha registro siempre deshabilitada
                            $("#btnEnviar").prop("disabled", false);
                        }
                    },
                    error: function(xhr, status, error) {
                        window.documentoYaExiste = false;

                        console.error('Error en AJAX:', status, error);
                        console.error('Response:', xhr.responseText);

                        var mensajeError = '❌ Error en la consulta. Intente nuevamente.';

                        // Intentar parsear la respuesta para obtener más información
                        try {
                            var respuesta = JSON.parse(xhr.responseText);
                            if (respuesta.message) {
                                mensajeError = '❌ Error: ' + respuesta.message;
                            }
                            if (respuesta.debug) {
                                console.error('Debug info:', respuesta.debug);
                            }
                        } catch (e) {
                            // Si no se puede parsear, usar el mensaje por defecto
                            console.error('No se pudo parsear la respuesta:', e);
                        }

                        mensajeContainer.removeClass('alert-info alert-success alert-warning').addClass('alert alert-danger')
                            .html(mensajeError);

                        // Limpiar todos los campos excepto documento
                        $("#nom_encVenta").val("");
                        $("#tipo_documento").val("");
                        $("#fecha_nacimiento").val("");
                        $("#fecha_preregistro").val("");
                        $("#dir_encVenta").val("");
                        $("#id_barrios").val(null).trigger('change');
                        $("#id_comunas").val("").prop("disabled", true);
                        $("#otro_bar_ver_encVenta").val("");
                        $("#otro_barrio_container").hide();
                        $("#zona_encVenta").val("");
                        $("#selectEF").val("");
                        $("#num_ficha_encVenta").val("");
                        $("#num_visita").val("");
                        $("#estado_ficha").val("");
                        $("#tipo_proceso").val("");
                        $("#total_integrantes").val("");
                        $("#exampleFormControlTextarea1").val("");

                        // Limpiar integrantes precargados y rehabilitar controles
                        limpiarIntegrantesPrecargados();

                        // Habilitar formulario y botón
                        $("#form_contacto :input").prop("disabled", false);
                        $("#fec_reg_encVenta").prop("disabled", true); // Fecha registro siempre deshabilitada
                        $("#btnEnviar").prop("disabled", false);
                    }
                });
            };

            // Disparar búsqueda al perder foco o al cambiar el campo
            $("#doc_encVenta").on('blur change', function() {
                buscarEncuesta();
            });

            // También permitir buscar al presionar Enter (keydown para mejor compatibilidad)
            $("#doc_encVenta").on('keydown', function(e) {
                if (e.key === 'Enter' || e.which === 13) {
                    e.preventDefault();
                    buscarEncuesta();
                }
            });
        });
    </script>
</head>

<body>
    <form id="form_contacto" action='processsurvey.php' method="POST" enctype="multipart/form-data">
        <div class="container">
            <div class="main-container">
                <div class="header-info">
                    <h1><b style="color:black"><i class="fa-solid fa-building "></i> REGISTRO ENCUESTAS DE CAMPO</b></h1>
                    <p><i><b>*Datos obligatorios</b></i></p>
                </div>

                <div id="mensajeDocumentoContainer" class="alert d-none"></div>

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
                                    value="<?php echo date('Y-m-d'); ?>" autofocus disabled />
                            </div>

                            <div class="form-group col-md-6">
                                <label for="tipo_doc">* TIPO DE DOCUMENTO:</label>
                                <select name="tipo_documento" class="form-control" id="tipo_documento">
                                    <option value="">SELECCIONE:</option>
                                    <option value="cedula">CEDULA</option>
                                    <option value="tarjeta identidad">TARJETA IDENTIDAD</option>
                                    <option value="cedula_extranjeria">CEDULA EXTRANJERIA</option>
                                    <option value="DNI">DNI</option>
                                    <option value="PASAPORTE">PASAPORTE</option>
                                    <option value="SALVO CONDUCTO PARA REFUGIADOS">SALVO CONDUCTO PARA REFUGIADOS</option>
                                    <option value="PERMISO PERMANENCIA">PERMISO PERMANENCIA</option>
                                    <option value="PERMISO DE PROTECCION TEMPORAL">PERMISO DE PROTECCION TEMPORAL</option>
                                    <option value="otro">otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="nom_encVenta">* NOMBRES COMPLETOS:</label>
                                <input type='text' name='nom_encVenta' id="nom_encVenta" class='form-control' required style="text-transform:uppercase;" />
                            </div>
                            <div class="form-group col-md-2">
                                <label for="fecha_nacimiento">FECHA NACIMIENTO:</label>
                                <input type='date' name='fecha_nacimiento' id="fecha_nacimiento" class='form-control' />
                            </div>
                            <div class="form-group col-md-2">
                                <label for="edad_calculada">EDAD:</label>
                                <input type='text' id="edad_calculada" class='form-control' readonly style="background-color: #e9ecef; font-weight: bold;" placeholder="Calculada" />
                            </div>
                            <div class="form-group col-md-3">
                                <label for="fecha_preregistro">FECHA PREREGISTRO:</label>
                                <input type='date' name='fecha_preregistro' id="fecha_preregistro" class='form-control' />
                            </div>

                        </div>
                    </div>
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
                            </div>
                            <div class="form-group col-md-4">
                                <label for="num_visita">Número de Visita</label>
                                <input type='number' id="num_visita" name='num_visita' class='form-control' min="0" />
                            </div>
                            <div class="form-group col-md-4">
                                <label for="estado_ficha">Estado de la Ficha</label>
                                <select id="estado_ficha" name="estado_ficha" class="form-control">
                                    <option value="">Seleccione estado</option>
                                    <option value="Visita fallida">Visita fallida</option>
                                    <option value="Direccion errada">Direccion errada</option>
                                    <option value="Direccion incompleta">Direccion incompleta</option>
                                    <option value="Fallecido">Fallecido</option>
                                    <option value="Fuera de ruta">Fuera de ruta</option>
                                    <option value="Informante no idoneo">Informante no idoneo</option>
                                    <option value="Rechazo a la vivienda">Rechazo a la vivienda</option>
                                    <option value="Validada">Validada</option>
                                    <option value="Ya le realizo la encuesta">Ya le realizo la encuesta</option>
                                    <option value="Ya no vive en la direccion">Ya no vive en la direccion</option>
                                    <option value="Zona insegura">Zona insegura</option>
                                    <option value="Nadie en el hogar">Nadie en el hogar</option>
                                </select>
                            </div>
                            <!-- Tipo de Proceso removed per request -->
                        </div>
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
                                <!-- SISBEN NOCTURNO removed per request -->
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
                    </div>
                    <div id="integrantes-container"></div>
                </div>

                <div class="form-section">
                    <h5 class="section-title">Observaciones</h5>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="obs_encVenta">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs_encVenta" style="text-transform:uppercase;" required></textarea>
                        </div>
                    </div>
                    <div class="form-section">
                        <h5 class="section-title">Fotografía del Encuestado</h5>
                        <div class="form-group">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>
                                        <i class="fas fa-camera"></i> Foto del Encuestado (Opcional)
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
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Vista Previa:</label>
                                    <div id="preview_foto" style="border: 2px dashed #007bff; border-radius: 8px; min-height: 150px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                        <span style="color: #6c757d;">
                                            <i class="fas fa-image fa-3x"></i><br>
                                            Sin imagen seleccionada
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success me-3" id="btnEnviar">
                            <span class="spinner-border spinner-border-sm"></span>
                            INGRESAR ENCUESTA
                        </button>

                        <a href="showsurvey.php" class="btn btn-info me-3">
                            <i class="fas fa-list me-2"></i>VER ENCUESTAS
                        </a>

                        <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'>
                            <img src='../../img/atras.png' width=27 height=27> REGRESAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#btnEnviar").prop("disabled", false);

            $('#id_bar').on('change', function() {
                $('#id_bar option:selected').each(function() {});

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
        });

        // Función para validar los integrantes antes del envío
        function validarIntegrantes() {
            var integrantesContainer = $("#integrantes-container");
            var formulariosDinamicos = integrantesContainer.find(".formulario-dinamico:not(.readonly-integrante)"); // Excluir integrantes de solo lectura
            var integrantesReadOnly = integrantesContainer.find(".readonly-integrante").length;

            // Si solo hay integrantes de solo lectura, es válido (no requiere agregar más)
            if (formulariosDinamicos.length === 0 && integrantesReadOnly > 0) {
                return true; // Válido si solo hay integrantes precargados
            }

            // Si no hay integrantes en absoluto (ni precargados ni nuevos), requerir al menos uno
            if (formulariosDinamicos.length === 0 && integrantesReadOnly === 0) {
                alert("Debe agregar al menos un integrante antes de enviar el formulario.");
                return false;
            }

            var errores = [];
            var hayAlMenosUnIntegranteCompleto = false;

            // Validar solo integrantes que estén PARCIALMENTE llenos
            formulariosDinamicos.each(function(index) {
                var integranteDiv = $(this);
                var numeroIntegrante = index + 1 + integrantesReadOnly;

                var genero = integranteDiv.find("select[name='gen_integVenta[]']");
                var rangoEdad = integranteDiv.find("select[name='rango_integVenta[]']");

                var generoVal = genero.val() || "";
                var rangoVal = rangoEdad.val() || "";

                // Verificar si el integrante tiene ALGÚN campo lleno
                var tieneCamposLlenos = false;
                integranteDiv.find("select, input[type='text'], input[type='number']").each(function() {
                    if ($(this).val() && $(this).val() !== "") {
                        tieneCamposLlenos = true;
                        return false; // break
                    }
                });

                // Si el integrante está completamente vacío, ignorarlo (no validar)
                if (!tieneCamposLlenos) {
                    return true; // continue al siguiente integrante
                }

                // Si tiene algún campo lleno, validar que tenga género Y rango de edad
                if (generoVal === "") {
                    errores.push("Integrante " + numeroIntegrante + ": Identidad de Género es requerida");
                    genero.addClass("is-invalid");
                } else {
                    genero.removeClass("is-invalid");
                }

                if (rangoVal === "") {
                    errores.push("Integrante " + numeroIntegrante + ": Rango de Edad es requerido");
                    rangoEdad.addClass("is-invalid");
                } else {
                    rangoEdad.removeClass("is-invalid");
                }

                // Si este integrante está completo (tiene género y rango), marcarlo
                if (generoVal !== "" && rangoVal !== "") {
                    hayAlMenosUnIntegranteCompleto = true;
                }
            });

            // Si hay integrantes precargados, no requerir que haya nuevos completos
            if (integrantesReadOnly === 0 && !hayAlMenosUnIntegranteCompleto && errores.length === 0) {
                alert("Debe completar al menos un integrante antes de enviar el formulario.");
                return false;
            }

            if (errores.length > 0) {
                var mensajeError = "Por favor complete los siguientes campos obligatorios:\n\n" + errores.join("\n");
                alert(mensajeError);

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

        $(document).ready(function() {
            $("#form_contacto").on("submit", function(e) {
                // Verificar si el documento ya existe
                if (window.documentoYaExiste) {
                    e.preventDefault();
                    var cedula = $("#doc_encVenta").val();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cédula Duplicada',
                        text: 'La cédula ' + cedula + ' ya existe en el sistema. No se puede registrar nuevamente.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                    return false;
                }

                if (!validarIntegrantes()) {
                    e.preventDefault();
                    return false;
                }
            });

            $(document).on("change", "select.is-invalid", function() {
                if ($(this).val() !== "") {
                    $(this).removeClass("is-invalid");
                }
            });
        });

        // Función para crear integrantes precargados como solo lectura
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

            var fieldsContainer = $("<div>").addClass("row"); // Usar row de Bootstrap

            // Input oculto para cantidad (siempre 1 por integrante)
            var cantidadInput = $("<input>")
                .attr("type", "hidden")
                .attr("name", "cant_integVenta[]")
                .val(1);

            // Función para crear campos de solo lectura más anchos
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

            // Crear campos con datos del integrante - organizados en filas más legibles
            var fila1 = $("<div>").addClass("row");
            fila1.append(createReadOnlyField("Identidad de Género", integ.gen_integVenta, "col-md-4"));
            fila1.append(createReadOnlyField("Rango de Edad", integ.rango_integVenta, "col-md-4"));
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

            // Badge indicativo mejorado
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

        // Función para limpiar integrantes precargados y rehabilitar controles
        function limpiarIntegrantesPrecargados() {
            $(".readonly-integrante").remove();

            // Resetear array de integrantes precargados
            if (typeof window.integrantesPrecargados !== 'undefined') {
                window.integrantesPrecargados = [];
            }

            // Si existe el primer integrante, actualizar su header a "Requerido"
            var primerIntegrante = $(".primer-integrante");
            if (primerIntegrante.length > 0) {
                primerIntegrante.find(".integrante-header")
                    .text("Integrante 1 (Requerido)")
                    .css('color', '#dc3545');

                // Hacer los campos requeridos nuevamente
                primerIntegrante.find("select[name='gen_integVenta[]']").attr("required", true);
                primerIntegrante.find("select[name='rango_integVenta[]']").attr("required", true);
            }

            // Recalcular el total sin los integrantes precargados
            actualizarTotal();

            $("#cant_integVenta").prop("disabled", false);
            $("#agregar").text("Agregar +")
                .removeClass("btn-secondary")
                .addClass("btn-primary");
        } // Antes de enviar el formulario, agregar marcadores de integrantes nuevos vs precargados
        $("#form_contacto").on("submit", function(e) {
            // Eliminar marcadores anteriores si existen
            $("input[name='es_nuevo[]']").remove();

            // Recorrer todos los integrantes y agregar un input hidden con el marcador
            $("#integrantes-container .formulario-dinamico").each(function(index) {
                var esNuevo = $(this).attr("data-es-nuevo") === "true" ? "true" : "false";

                // Agregar input hidden después de cada integrante
                var marcador = $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "es_nuevo[]")
                    .val(esNuevo);
                $(this).append(marcador);
            });

            // Permitir que el formulario se envíe normalmente
            return true;
        });

        // Calcular edad automáticamente cuando cambie la fecha de nacimiento
        $("#fecha_nacimiento").on("change", function() {
            var fechaNac = $(this).val();
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
        });

        // Vista previa de la foto - Función común para ambos inputs
        function mostrarVistaPrevia(file) {
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $("#preview_foto").html(
                        '<img src="' + event.target.result + '" style="max-width: 100%; max-height: 300px; border-radius: 8px;">'
                    );
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
    </script>

</body>

</html>