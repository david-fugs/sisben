<?php
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
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
            .responsive {
                max-width: 100%;
                height: auto;
            }
            .puntero{
                cursor: pointer;
            }
            .ocultar {
                display: none;
            }
            .encuestador-container {
                max-height: 200px; /* Altura máxima del contenedor */
                overflow-y: auto; /* Habilita el desplazamiento vertical si el contenido supera la altura máxima */
            }
            .smaller-input {
                width: 200px; /* Ajusta el ancho según sea necesario */
            }
            
            .formulario-dinamico {
                 margin-bottom: 10px; /* Ajusta el margen inferior según sea necesario */
            }
        </style>
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <script>
            $(document).ready(function () 
                {
                    // Función para agregar integrantes dinámicamente.
                    $("#agregar").click(function ()
                    {
                        var inputCantidad = $("#cant_integCampo");
                        var cantidadValor = inputCantidad.val();

                        // Validar que se haya ingresado una cantidad válida
                        if (!cantidadValor || cantidadValor <= 0) 
                        {
                            alert("Por favor, ingresa una cantidad válida de integrantes.");
                            return;
                        }

                        // Eliminar los integrantes existentes
                        $("#integrantes-container").empty();

                        // Crear los campos para la cantidad especificada de integrantes
                        for (var i = 0; i < cantidadValor; i++) 
                        {
                            var integranteDiv = $("<div>").addClass("formulario-dinamico");

                            // Agregar campo de cantidad individual
                            var cantidadInput = $("<input>")
                                .attr("type", "number")
                                .attr("name", "cant_integCampo[]")
                                .addClass("form-control smaller-input")
                                .attr("placeholder", "Cantidad");

                            // Agregar campo de género
                            var generoSelect = $("<select>")
                                .attr("name", "gen_integCampo[]")
                                .addClass("form-control smaller-input")
                                .addClass("form-control")
                                .append('<option value="">Género</option>')
                                .append('<option value="F">F</option>')
                                .append('<option value="M">M</option>')
                                .append('<option value="O">Otro</option>'); // Agregamos "Otro" para opciones no binarias

                            // Agregar campo de rango de edad
                            var rangoEdadSelect = $("<select>")
                                .attr("name", "rango_integCampo[]")
                                .addClass("form-control smaller-input")
                                .addClass("form-control")
                                .append('<option value="">Rango de edad</option>')
                                .append('<option value="0 - 6">0 - 6</option>')
                                .append('<option value="7 - 12">7 - 12</option>')
                                .append('<option value="13 - 17">13 - 17</option>')
                                .append('<option value="18 - 28">18 - 28</option>')
                                .append('<option value="29 - 45">29 - 45</option>')
                                .append('<option value="46 - 64">46 - 64</option>')
                                .append('<option value="Mayor o igual a 65">Mayor o igual a 65</option>');

                            var eliminarBtn = $("<button>")
                                .attr("type", "button")
                                .addClass("btn btn-danger")
                                .text("Eliminar")
                                .click(function () {
                                    $(this).closest(".formulario-dinamico").remove();
                                });

                            integranteDiv.append(cantidadInput);
                            integranteDiv.append(generoSelect);
                            integranteDiv.append(rangoEdadSelect);
                            integranteDiv.append(eliminarBtn);

                            // Agregar una línea horizontal para separar los integrantes
                            //integranteDiv.append($("<hr>"));

                            $("#integrantes-container").append(integranteDiv);
                        }
                    });
                });
        </script>
    </head>
    <body >
       	<?php
            include("../../conexion.php");
            date_default_timezone_set("America/Bogota");
            
            $id_encCampo  = $_GET['id_encCampo'];
    	    if(isset($_GET['id_encCampo']))
    	    { 
                $sql = mysqli_query($mysqli, "SELECT * FROM encCampo WHERE id_encCampo = '$id_encCampo'");
    	        $row = mysqli_fetch_array($sql);
            }

        ?>

       	<div class="container">
            <center>
                <img src='../../img/sisben.png' width=300 height=185 class="responsive">
            </center>
            
            <form id="form_contacto" action='addencCampoFamily1.php' method="POST" enctype="multipart/form-data">

                <div class="container pt-5">
                    <h1><b><i class="fa-solid fa-people-group"></i> ADICIONAR PERSONAS AL GRUPO FAMILIAR</b></h1>

                    <hr style="border: 2px solid #16087B; border-radius: 2px;">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label for="doc_encCampo">No. DOCUMENTO:</label>
                                <input type='text' name='doc_encCampo' class='form-control'  value='<?php echo $row['doc_encCampo']; ?>' readonly/>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="nom_encCampo">NOMBRE DEL USUARIO:</label>
                                <input type='text' name='nom_encCampo' class='form-control'  value='<?php echo $row['nom_encCampo']; ?>' readonly/>
                            </div>
                            <div class="col-12 col-sm-3">
                                <input type='number' name='id_encCampo' id="id_encCampo" class='form-control' value='<?php echo $row['id_encCampo']; ?>' readonly hidden/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="cant_integCampo">* CANTIDAD:</label>
                                <input type="number" id="cant_integCampo" name="cant_integCampo" class="form-control" required style="text-transform: uppercase;" />
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

                agregar.addEventListener('click', e =>{
                    e.preventDefault();
                    let clonado = document.querySelector('.clonar');
                    let clon = clonado.cloneNode(true);

                    contenido.appendChild(clon).classList.remove('clonar');

                    let remover_ocutar = contenido.lastChild.childNodes[1].querySelectorAll('span');
                    remover_ocutar[0].classList.remove('ocultar');
                });

                contenido.addEventListener('click', e =>{
                    e.preventDefault();
                    if(e.target.classList.contains('puntero')){
                        let contenedor  = e.target.parentNode.parentNode;
                    
                        contenedor.parentNode.removeChild(contenedor);
                    }
                });

                boton_enviar.addEventListener('click', e => {
                    e.preventDefault();

                    const formulario = document.querySelector('#form_contacto');
                    const form = new FormData(formulario);

                    const peticion = {
                        body:form,
                        method:'POST'
                    };

                    fetch('php/inserta-contacto.php',peticion)
                        .then(res => res.json())
                        .then(res => {
                            if (res['respuesta']) {
                                alert(res['mensaje']);
                                formulario.reset();
                            }else{
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