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
    <title>BD SISBEN</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            ordenarSelect('tram_solic_encMovim');
            ordenarSelect('id_com');
            ordenarSelect('id_correg');
        });

        $(document).ready(function() {
            function actualizarTotal() {
                let total = 0;
                $("input[name='cant_integMovim[]']").each(function() {
                    let valor = parseInt($(this).val()) || 0;
                    total += valor;
                });
                $("#total_integrantes").val(total);
            }
            // Función para agregar integrantes dinámicamente.
            $("#agregar").click(function() {

                var inputCantidad = $("#cant_integMovim");
                var cantidadValor = inputCantidad.val();

                // Validar que se haya ingresado una cantidad válida
                if (!cantidadValor || cantidadValor <= 0) {
                    alert("Por favor, ingresa una cantidad válida de integrantes.");
                    return;
                }

                // Eliminar los integrantes existentes
                $("#integrantes-container").empty();

                // Crear los campos para la cantidad especificada de integrantes
                for (var i = 0; i < cantidadValor; i++) {
                    var integranteDiv = $("<div>").addClass("formulario-dinamico");

                    var cantidadInput = $("<input>")
                        .attr("type", "number")
                        .attr("name", "cant_integMovim[]")
                        .addClass("form-control smaller-input")
                        .attr("placeholder", "Cantidad")
                        .val(1) // Establecer el valor inicial en 1
                        .attr("readonly", true) // Hacer el campo de solo lectura
                        .on("blur", actualizarTotal); // Llamar a actualizarTotal cuando pierde el foco


                    // Agregar campo de género
                    var generoSelect = $("<select>")
                        .attr("name", "gen_integMovim[]")
                        .addClass("form-control smaller-input")
                        .addClass("form-control")
                        .append('<option value="">Identidad Genero</option>')
                        .append('<option value="F">F</option>')
                        .append('<option value="M">M</option>')
                        .append('<option value="O">Otro</option>'); // Agregamos "Otro" para opciones no binarias

                    var OrientacionSexual = $("<select>")
                        .attr("name", "orientacionSexual[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Orientiacion Sexual</option>')
                        .append('<option value="Heterosexual">Heterosexual</option>')
                        .append('<option value="Homosexual">Homosexual</option>')
                        .append('<option value="Otro">Otro</option>');
                    // Agregar campo de rango de edad
                    var rangoEdadSelect = $("<select>")
                        .attr("name", "rango_integMovim[]")
                        .addClass("form-control smaller-input")
                        .addClass("form-control")
                        .append('<option value="">Rango de edad</option>')
                        .append('<option value="0 - 6">0 - 5</option>')
                        .append('<option value="7 - 12">6 - 12</option>')
                        .append('<option value="13 - 17">13 - 17</option>')
                        .append('<option value="18 - 28">18 - 28</option>')
                        .append('<option value="29 - 45">29 - 45</option>')
                        .append('<option value="46 - 64">46 - 64</option>')
                        .append('<option value="Mayor o igual a 65">Mayor o igual a 65</option>');
                    var condicionDispacapacidad = $("<select>")
                        .attr("name", "condicionDispacapacidad[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Condicion Discapacidad</option>')
                        .append('<option value="Si">Si</option>')
                        .append('<option value="No">No</option>');

                    var GrupoEtnico = $("<select>")
                        .attr("name", "grupoEtnico[]")
                        .addClass("form-control smaller-input")
                        .append('<option value="">Grupo Etnico</option>')
                        .append('<option value="Indigena">Indigena</option>')
                        .append('<option value="Negra / Afrocolombiana">Negra / Afrocolombiana</option>')
                        .append('<option value="Raizal">Raizal</option>')
                        .append('<option value="Palenquero">Palenquero</option>')
                        .append('<option value="Gitano (rom)">Gitano (rom)</option>');

                    var eliminarBtn = $("<button>")
                        .attr("type", "button")
                        .addClass("btn btn-danger")
                        .text("Eliminar")
                        .click(function() {
                            $(this).closest(".formulario-dinamico").remove();
                            actualizarTotal();
                        });

                    integranteDiv.append(cantidadInput);
                    integranteDiv.append(generoSelect);
                    integranteDiv.append(rangoEdadSelect);
                    integranteDiv.append(condicionDispacapacidad);
                    integranteDiv.append(GrupoEtnico);
                    integranteDiv.append(OrientacionSexual);
                    integranteDiv.append(eliminarBtn);


                    // Agregar una línea horizontal para separar los integrantes
                    //integranteDiv.append($("<hr>"));

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

    $id_usu  = $_GET['id_usu'];
    if (isset($_GET['id_usu'])) {
        $sql = mysqli_query($mysqli, "SELECT * FROM usuarios WHERE id_usu = '$id_usu'");
        $row = mysqli_fetch_array($sql);
    }
    ?>


    <form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">

        <div class="container pt-2">
            <h1><b><i class="fa-solid fa-file-pen"></i> REGISTRO MOVIMIENTOS</b></h1>
            <p><i><b>
                        <font size=3 color=#c68615>*Datos obligatorios</i></b></font>
            </p>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="fec_reg_encMovim">* FECHA REALIZADA:</label>
                        <input type='date' name='fec_reg_encMovim' class='form-control' id="fec_reg_encMovim" required autofocus />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="doc_encMovim">* DOCUMENTO:</label>
                        <input type='number' name='doc_encMovim' class='form-control' id="doc_encMovim" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tipo_doc">* TIPO DE DOCUMENTO:</label>
                        <select name="tipo_documento" class="form-control" id="">
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
                        <input type='text' name='ciudad_expedicion' class='form-control' required style="text-transform:uppercase;" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICION:</label>
                        <input type='date' name='fecha_expedicion' class='form-control' required style="text-transform:uppercase;" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nom_encMovim">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_encMovim' class='form-control' required style="text-transform:uppercase;" />
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="dir_encMovim">* DIRECCIÓN:</label>
                        <input type='text' name='dir_encMovim' class='form-control' required />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="zona_encMovim">* ZONA:</label>
                        <select id="zona_encMovim" class="form-control" name="zona_encMovim" required>
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
                        <label for="barrio_select">* BARRIO:</label>
                        <select id="barrio_select" name="id_bar" class="form-control" disabled="disabled" required>
                            <option value="">* SELECCIONE EL BARRIO:</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4" id="otro_barrio_container" style="display: none;">
                        <label for="otro_bar_ver_encVenta">ESPECIFIQUE BARRIO,VEREDA O INVASION:</label>
                        <input type="text" id="otro_bar_ver_encVenta" name="otro_bar_ver_encMovim" class="form-control" placeholder="Ingrese el barrio">
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
                        <label for="tram_solic_encMovim">* TRÁMITE SOLICITADO:</label>
                        <select class="form-control" name="tram_solic_encMovim" id="tram_solic_encMovim" required>
                            <option value=""></option>
                            <option value="MODIFICACION DATOS PERSONA">MODIFICACION DATOS PERSONA</option>
                            <option value="INCLUSION">INCLUSION</option>
                            <option value="RETIRO PERSONAS">RETIRO PERSONAS</option>
                            <option value="RETIRO FICHA">RETIRO FICHA</option>
                            <option value="INCONFORMIDAD POR CLASIFICACION">INCONFORMIDAD POR CLASIFICACION</option>
                            <option value="RETIRO POR INCONFORMIDAD">RETIRO POR INCONFORMIDAD</option>
                            <option value="RETIRO DE PERSONA POR INCONFORMIDAD">RETIRO DE PERSONA POR INCONFORMIDAD</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="num_ficha_encMovim">* No. FICHA o RADICADO:</label>
                        <input type='number' name='num_ficha_encMovim' class='form-control' required />
                    </div>
                    <div class="form-group col-md-2">
                        <label for="integra_encVenta">INTEGRANTES:</label>
                        <input type='number' id='total_integrantes' name='integra_encMovim' class='form-control' value="" required readonly />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="cant_integMovim">CANTIDAD:</label>
                        <input type="number" id="cant_integMovim" name="cant_integMovim" class="form-control" />
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
                    <label for="obs_encMovim">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs_encMovim" style="text-transform:uppercase;"></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-success">
                <span class="spinner-border spinner-border-sm"></span>
                INGRESAR ENCUESTA
            </button>

            <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
            </button>
        </div>
    </form>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

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
    $(document).on('change', '#barrio_select', function() {
        console.log("Cambio detectado en barrio_select");
        let selectedText = $("#barrio_select option:selected").text().trim().toUpperCase();
        console.log("Texto seleccionado:", selectedText);

        if (selectedText === "OTRO") {
            $('#otro_barrio_container').show();
        } else {
            $('#otro_barrio_container').hide();
            $('#otro_bar_ver_encVenta').val('');
        }
    });
    $(document).ready(function() {
        $('#id_com').on('change', function() {
            if ($('#id_com').val() == "") {
                $('#barrio_select').empty();
                $('<option value="">SELECCIONE EL BARRIO:</option>').appendTo('#barrio_select');
                $('#barrio_select').attr('disabled', 'disabled');
            } else {
                $('#barrio_select').removeAttr('disabled');
                $('#barrio_select').load('../ecampo/barriosGet.php?id_com=' + $('#id_com').val(), function() {
                    console.log($("#barrio_select").html());
                    $('#barrio_select option').each(function() {});
                });
            }
        });

        $(document).on('change', '#id_bar', function() {
            console.log("Cambio detectado en id_bar");
            let selectedText = $("#id_bar option:selected").text().trim().toUpperCase();
            console.log("Texto seleccionado:", selectedText);

            if (selectedText == "OTRO") {
                $('#otro_barrio_container').show();
            } else {
                $('#otro_barrio_container').hide();
                $('#otro_bar_ver_encVenta').val('');
            }
        });
    });
</script>

<script type="text/javascript">
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
</script>

<script>
    var cargarDocumentoCheckbox = document.getElementById("cargarDocumento");
    var campoArchivo = document.getElementById("campoArchivo");

    cargarDocumentoCheckbox.addEventListener("change", function() {
        campoArchivo.style.display = cargarDocumentoCheckbox.checked ? "block" : "none";
    });
</script>

</html>