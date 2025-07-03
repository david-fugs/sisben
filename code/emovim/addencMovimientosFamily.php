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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <style>
        #integrantes-container {
            display: flex;
            flex-wrap: wrap;
            /* Permite que los elementos pasen a la siguiente línea si no caben */
            gap: 10px;
        }

        .responsive {
            max-width: 100%;
            height: auto;
        }

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

        .smaller-input {
            width: 200px;
            /* Ajusta el ancho según sea necesario */
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
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function actualizarTotal() {
            let total = 0;
            $("input[name='cant_integVenta[]']").each(function() {
                let valor = parseInt($(this).val()) || 0;
                total += valor;
            });
            $("#total_integrantes").val(total);
        }
        $(document).ready(function() {
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

                    // Agregar campo de cantidad individual
                    var cantidadInput = $("<input>")
                        .attr("type", "number")
                        .attr("name", "cant_integVenta[]")
                        .addClass("form-control smaller-input")
                        .val(1) // Por defecto 1 para que se cuente automáticamente
                        .on("input", actualizarTotal)
                        .attr("placeholder", "Cantidad")
                        .attr("readonly", true) // Hacer el campo de solo lectura;

                    // Agregar campo de género
                    var generoSelect = $("<select>")
                        .attr("name", "gen_integMovim[]")
                        .addClass("form-control smaller-input")
                        .addClass("form-control")
                        .append('<option value="">Identidad Genero</option>')
                        .append('<option value="F">F</option>')
                        .append('<option value="M">M</option>')
                        .append('<option value="O">Otro</option>');

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
                    var condicionDiscapacidad = $("<select>")
                        .attr("name", "condicionDiscapacidad[]")
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
                    integranteDiv.append(OrientacionSexual);
                    integranteDiv.append(condicionDiscapacidad);
                    integranteDiv.append(GrupoEtnico);
                    integranteDiv.append(rangoEdadSelect);
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
    <?php
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");

    $id_encMovim  = $_GET['id_encMovim'];
    if (isset($_GET['id_encMovim'])) {
        $sql = mysqli_query($mysqli, "SELECT * FROM encMovimientos WHERE id_encMovim = '$id_encMovim'");
        $row = mysqli_fetch_array($sql);
    }

    ?>

    <div class="container">
        <center>
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </center>

        <form id="form_contacto" action='addencMovimientosFamily1.php' method="POST" enctype="multipart/form-data">

            <div class="container pt-5">
                <h1><b><i class="fa-solid fa-people-group"></i> ADICIONAR PERSONAS AL GRUPO FAMILIAR</b></h1>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <label for="doc_encMovim">No. DOCUMENTO:</label>
                            <input type='text' name='doc_encMovim' class='form-control' value='<?php echo $row['doc_encMovim']; ?>' readonly />
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="nom_encMovim">NOMBRE DEL USUARIO:</label>
                            <input type='text' name='nom_encMovim' class='form-control' value='<?php echo $row['nom_encMovim']; ?>' readonly />
                        </div>
                        <div class="col-12 col-sm-3">
                            <input type='number' name='id_encMovim' id="id_encMovim" class='form-control' value='<?php echo $row['id_encMovim']; ?>' readonly hidden />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="cant_integMovim">* CANTIDAD:</label>
                            <input type="number" id="cant_integMovim" name="cant_integMovim" class="form-control" required style="text-transform: uppercase;" />
                        </div>
                        <!--<div class="form-group col-md-3">-->
                        <div class="form-group col-md-3 d-flex flex-column align-items-start">
                            <label for=""></label>
                            <button type="button" class="btn btn-primary mt-auto" id="agregar">Agregar +</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="integrantes-container"></div>

            <div id="contenedor"></div>

            <hr style="border: 2px solid #16087B; border-radius: 2px;">

            <button type="submit" class="btn btn-success">
                <span class="spinner-border spinner-border-sm"></span>
                INGRESAR NUEVO MIEMBRO DEL GRUPO FAMILIAR
            </button>

            <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27>REGRESAR
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
    </div>
</body>

<script>
    var cargarDocumentoCheckbox = document.getElementById("cargarDocumento");
    var campoArchivo = document.getElementById("campoArchivo");

    cargarDocumentoCheckbox.addEventListener("change", function() {
        campoArchivo.style.display = cargarDocumentoCheckbox.checked ? "block" : "none";
    });
</script>

</html>