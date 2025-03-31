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
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <style>
        #integrantes-container {
            display: flex;
            flex-wrap: wrap;
            /* Permite que los elementos pasen a la siguiente línea si no caben */
            gap: 10px;
        }

        .formulario-dinamico {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #ccc;
            /* Opcional: agregar un borde */
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            /* Opcional: fondo para cada elemento */
        }

        .responsive {
            max-width: 100%;
            height: auto;
        }

        .smaller-input {
            width: 200px;
            /* Ajusta el ancho según sea necesario */
        }

        .formulario-dinamico {
            margin-bottom: 10px;
            /* Ajusta el margen inferior según sea necesario */
        }
    </style>

    <script>
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
            }

            $("#agregar").click(function() {
                var inputCantidad = $("#cant_integVenta");
                var cantidadValor = parseInt(inputCantidad.val());

                if (!cantidadValor || cantidadValor <= 0) {
                    alert("Por favor, ingresa una cantidad válida de integrantes.");
                    return;
                }

                $("#integrantes-container").empty();

                for (var i = 0; i < cantidadValor; i++) {
                    var integranteDiv = $("<div>").addClass("formulario-dinamico");

                    var cantidadInput = $("<input>")
                        .attr("type", "number")
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

                    var hombreCabezaFamilia = $("<select>")
                        .attr("name", "hombreCabezaFamilia[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Hombre Cabeza de Familia</option>')
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
                        .append('<option value="Especializacion">Especializacion</option>')

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
                        .append('<option value="Ninguno">Ninguno</option>')

                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger")
                        .text("Eliminar")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            actualizarTotal();
                        });

                    // Agregar evento para mostrar/ocultar el select de discapacidad
                    condicionDiscapacidad.on("change", function() {
                        if ($(this).val() === "Si") {
                            discapacidadSelect.show();
                        } else {
                            discapacidadSelect.hide();
                        }
                    });

                    integranteDiv.append(cantidadInput, generoSelect, rangoEdadSelect, OrientacionSexual, condicionDiscapacidad, GrupoEtnico, eliminarBtn);
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
                            console.log("⏳ Consultando en la base de datos...");
                            mensajeContainer.removeClass("alert-danger alert-success alert-warning").addClass("alert d-none").html("");
                        },
                        success: function(response) {
                            console.log("✅ Respuesta del servidor:", response);

                            if (response.status === "existe_encuesta") {
                                mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger")
                                    .html("⚠️ La encuesta ya fue realizada.");
                                $("#btnEnviar").prop("disabled", true);
                            } else if (response.status === "existe_info") {
                                mensajeContainer.removeClass("d-none alert-danger alert-warning").addClass("alert alert-success")
                                    .html("✔ Documento encontrado en Información.");
                                $("#btnEnviar").prop("disabled", false);
                                // Llenar los campos con los datos de la BD
                                $("#fec_reg_encVenta").val(response.data.fecha_reg_info);
                                $("#nom_encVenta").val(response.data.nom_info);
                                $("#tipo_documento").val(response.data.tipo_documento);
                                $("#ciudad_expedicion").val(response.data.ciudad_expedicion);
                                $("#fecha_expedicion").val(response.data.fecha_expedicion);
                                $("#obs1_encInfo").val(response.data.observacion);
                                $("#obs2_encInfo").val(response.data.info_adicional);
                            } else {
                                mensajeContainer.removeClass("d-none alert-danger alert-success").addClass("alert alert-warning")
                                    .html("⚠️ El documento no está registrado.");
                                $("#btnEnviar").prop("disabled", false);

                                // Vaciar los campos si el documento no existe
                                $("#fec_reg_encVenta, #nom_encVenta, #tipo_documento, #ciudad_expedicion, #fecha_expedicion, #obs1_encInfo, #obs2_encInfo").val("");
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log("❌ Error AJAX:", textStatus, errorThrown);
                            mensajeContainer.removeClass("d-none alert-success alert-warning").addClass("alert alert-danger")
                                .html("❌ Error en la consulta. Intente nuevamente.");
                            $("#btnEnviar").prop("disabled", false);
                        }
                    });
                } else {
                    // Si el campo está vacío, limpiar todo
                    mensajeContainer.addClass("d-none").html("");
                    $("#btnEnviar").prop("disabled", false);
                    $("#fec_reg_encVenta, #nom_encVenta, #tipo_documento, #ciudad_expedicion, #fecha_expedicion, #obs1_encInfo, #obs2_encInfo").val("");
                }
            });
        });
    </script>

    <form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">

        <div class="container pt-2">
            <h1><b><i class="fa-solid fa-building"></i> REGISTRO ENCUESTAS NUEVA VENTANILLA</b></h1>
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
                        <input type='date' name='fec_reg_encVenta' class='form-control' id="fec_reg_encVenta" required autofocus />
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
                        <label for="ciudad_expedicion">* CIUDAD EXPEDICION:</label>
                        <input type='text' id="ciudad_expedicion" name='ciudad_expedicion' class='form-control' required style="text-transform:uppercase;" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICION:</label>
                        <input type='date' name='fecha_expedicion' id="fecha_expedicion" class='form-control' required style="text-transform:uppercase;" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nom_encVenta">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_encVenta' id="nom_encVenta" class='form-control' required style="text-transform:uppercase;" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="dir_encVenta">* DIRECCIÓN:</label>
                        <input type='text' name='dir_encVenta' class='form-control' required />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="zona_encVenta">* ZONA:</label>
                        <select id="zona_encVenta" class="form-control" name="zona_encVenta" required>
                            <option value="URBANA">URBANA</option>
                            <option value="RURAL">RURAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_com">* COMUNA:</label>
                        <select id="id_com" class="form-control" name="id_com" required>
                            <option value=""></option>
                            <?php
                            $sql = $mysqli->prepare("SELECT * FROM comunas");
                            if ($sql->execute()) {
                                $g_result = $sql->get_result();
                            }
                            while ($row = $g_result->fetch_array()) {
                            ?>
                                <option value="<?php echo $row['id_com'] ?>"><?php echo $row['nombre_com'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="id_correg">* CORREGIMIENTO:</label>
                        <select id="id_correg" class="form-control" name="id_correg" required>
                            <?php
                            $sql_correg = $mysqli->prepare("SELECT * FROM corregimientos");
                            if ($sql_correg->execute()) {
                                $g_result_correg = $sql_correg->get_result();
                            }
                            while ($row_correg = $g_result_correg->fetch_array()) {
                            ?>
                                <option value="<?php echo $row_correg['id_correg'] ?>"><?php echo $row_correg['nombre_correg'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_vere">VEREDA:</label>
                        <select id="id_vere" name="id_vere" class="form-control" disabled="disabled" required>
                            <option value="">* SELECCIONE LA VEREDA:</option>
                        </select>
                    </div>


                    <div class="form-group col-md-4">
                        <label for="id_bar">* BARRIO:</label>
                        <select id="id_bar" name="id_bar" class="form-control" disabled="disabled" required>
                            <option value="">* SELECCIONE EL BARRIO:</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4" id="otro_barrio_container" style="display: none;">
                        <label for="otro_bar_ver_encVenta">ESPECIFIQUE BARRIO,VEREDA O INVASION:</label>
                        <input type="text" id="otro_bar_ver_encVenta" name="otro_bar_ver_encVenta" class="form-control" placeholder="Ingrese el barrio">
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

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="tram_solic_encVenta">* TRÁMITE SOLICITADO:</label>
                        <select class="form-control" name="tram_solic_encVenta" id="selectEF" required>
                            <option value=""></option>
                            <option value="ENCUESTA NUEVA">ENCUESTA NUEVA</option>
                            <option value="ENCUESTA NUEVA POR VERIFICACION">ENCUESTA NUEVA POR VERIFICACION</option>
                            <option value="MIGRACION SISBEN 4">MIGRACION SISBEN 4</option>
                            <option value="CAMBIO DIRECCION">CAMBIO DIRECCION</option>
                            <option value="INCONFORMIDAD">INCONFORMIDAD</option>
                            <option value=" DESCENTRALIZADO"> DESCENTRALIZADO</option>
                            <option value="SOLICITUDES PENDIENTES 2024">SOLICITUDES PENDIENTES 2024</option>
                            <option value="FAVORES">FAVORES</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="num_ficha_encVenta">* No. FICHA o RADICADO:</label>
                        <input type='number' name='num_ficha_encVenta' class='form-control' required />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="integra_encVenta">INTEGRANTES:</label>
                        <input type='number' id='total_integrantes' name='integra_encVenta' class='form-control' value="" required readonly />
                    </div>
                </div>

            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="cant_integVenta">CANTIDAD:</label>
                        <input type="number" id="cant_integVenta" name="cant_integVenta" class="form-control" />
                    </div>
                    <!--<div class="form-group col-md-3">-->
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
                INGRESAR ENCUESTA
            </button>

            <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
            </button>
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
<script src="../ecampo/js/jquery-3.1.1.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#id_com').on('change', function() {
            if ($('#id_com').val() == "") {
                $('#id_bar').empty();
                $('<option value="">SELECCIONE EL BARRIO:</option>').appendTo('#id_bar');
                $('#id_bar').attr('disabled', 'disabled');
            } else {
                $('#id_bar').removeAttr('disabled');
                $('#id_bar').load('../ecampo/barriosGet.php?id_com=' + $('#id_com').val(), function() {
                    console.log("Barrios cargados:");
                    $('#id_bar option').each(function() {});
                });
            }
        });

        //  Agregar LOGS para ver qué tiene id_bar al cambiar
        $('#id_bar').on('change', function() {
            $('#id_bar option:selected').each(function() {
                console.log("Valor:", $(this).val(), "Texto:", $(this).text());
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
    });
    var cargarDocumentoCheckbox = document.getElementById("cargarDocumento");
    var campoArchivo = document.getElementById("campoArchivo");

    cargarDocumentoCheckbox.addEventListener("change", function() {
        campoArchivo.style.display = cargarDocumentoCheckbox.checked ? "block" : "none";
    });
</script>

</html>