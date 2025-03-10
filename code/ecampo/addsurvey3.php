<?php
    
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
        header("Location: ../../index.php");
        exit();  // Asegúrate de salir del script después de redirigir
    }

    $nombre = $_SESSION['nombre'];
    $tipo_usuario = $_SESSION['tipo_usuario'];
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
        </style>

        <script>
            function ordenarSelect(id_componente)
              {
                var selectToSort = jQuery('#' + id_componente);
                var optionActual = selectToSort.val();
                selectToSort.html(selectToSort.children('option').sort(function (a, b) {
                  return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
                })).val(optionActual);
              }
              $(document).ready(function () {
                ordenarSelect('selectEF');
              });
        </script>
        <script>
            function ordenarSelect(id_componente)
              {
                var selectToSort = jQuery('#' + id_componente);
                var optionActual = selectToSort.val();
                selectToSort.html(selectToSort.children('option').sort(function (a, b) {
                  return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
                })).val(optionActual);
              }
              $(document).ready(function () {
                ordenarSelect('selectPC');
              });
        </script>
        
          <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
         
    
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const zonaSelect = document.getElementById('zona_encCampo');
        const comunaSelect = document.getElementById('id_com');
        const barrioSelect = document.getElementById('id_bar');
        const corregimientoInput = document.getElementsByName('corre_encCampo')[0];
        const veredaInput = document.getElementsByName('vere_encCampo')[0];

        // Función para ajustar los campos requeridos según la zona seleccionada
        function ajustarRequerimientos() {
            if (zonaSelect.value === 'URBANA') {
                comunaSelect.required = true;
                barrioSelect.required = true;
                corregimientoInput.required = false;
                veredaInput.required = false;
            } else if (zonaSelect.value === 'RURAL') {
                comunaSelect.required = false;
                barrioSelect.required = false;
                corregimientoInput.required = true;
                veredaInput.required = true;
            }
        }

        // Manejador de eventos para la selección de ZONA
        zonaSelect.addEventListener('change', function() {
            ajustarRequerimientos();
        });

        // Llama a la función de ajuste de requerimientos al cargar la página
        zonaSelect.dispatchEvent(new Event('change'));
    });
</script>


<script>
$(document).ready(function() {
    // Función para agregar integrantes dinámicamente.
    $("#agregar").click(function() {
        var inputCantidad = $("#cant_integCampo");
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
                .attr("name", "cant_integCampo[]")
                .addClass("form-control")
                .attr("placeholder", "Cantidad");

            // Agregar campo de género
            var generoSelect = $("<select>")
                .attr("name", "gen_integCampo[]")
                .addClass("form-control")
                .append('<option value="">Seleccione género</option>')
                .append('<option value="F">F</option>')
                .append('<option value="M">M</option>')
                .append('<option value="O">Otro</option>'); // Agregamos "Otro" para opciones no binarias

            // Agregar campo de rango de edad
            var rangoEdadSelect = $("<select>")
                .attr("name", "rango_integCampo[]")
                .addClass("form-control")
                .append('<option value="">Seleccione rango de edad</option>')
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
                .click(function() {
                    $(this).closest(".formulario-dinamico").remove();
                });

            integranteDiv.append(cantidadInput);
            integranteDiv.append(generoSelect);
            integranteDiv.append(rangoEdadSelect);
            integranteDiv.append(eliminarBtn);

            // Agregar una línea horizontal para separar los integrantes
            integranteDiv.append($("<hr>"));

            $("#integrantes-container").append(integranteDiv);
        }
    });
});

</script>

        
   </head>
   

    <body>
        <style>
            .puntero{
                cursor: pointer;
            }
            .ocultar{
                display: none;
            }

            .encuestador-container {
    max-height: 200px; /* Altura máxima del contenedor */
    overflow-y: auto; /* Habilita el desplazamiento vertical si el contenido supera la altura máxima */
}

        </style>
        
        <center>
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </center>
        <br />
<?php

    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
    require_once("../../zebra.php");
?>
    

<form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">

    <div class="container pt-5">
        <h1><b><i class="fa-solid fa-address-card"></i> REGISTRO ENCUESTAS NUEVAS</b></h1>
        <p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
        
        <div class="form-group">
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="fec_pre_encCampo">* FECHA PREREGISTRO:</label>
                    <input type='date' name='fec_pre_encCampo' class='form-control' id="fec_pre_encCampo" required autofocus />
                </div>
                <div class="form-group col-md-2">
                    <label for="fec_rea_encCampo">* FECHA REALIZADA:</label>
                    <input type='date' name='fec_rea_encCampo' id="fec_rea_encCampo" class='form-control' required />
                </div>
                <div class="form-group col-md-4">
                    <label for="nom_encCampo">* NOMBRES COMPLETOS:</label>
                    <input type='text' name='nom_encCampo' class='form-control' required style="text-transform:uppercase;" />
                </div>
                <div class="form-group col-md-3">
                    <label for="doc_encCampo">* DOCUMENTO:</label>
                    <input type='number' name='doc_encCampo' class='form-control' id="doc_encCampo" required />
                </div>
            </div>
        </div>

        
<form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="cant_integCampo">*CANTIDAD:</label>
                    <input type="number" id="cant_integCampo" name="cant_integCampo" class="form-control" required style="text-transform: uppercase;" />
                </div>
                <!--<div class="form-group col-md-3">-->
                <div class="form-group col-md-3 d-flex flex-column align-items-start">
                    <label for=""></label>
                    <button type="button" class="btn btn-primary mt-auto" id="agregar">Agregar +</button>
                </div>
            </div>
        </div>

        <div id="integrantes-container"></div>
</form>

        <div id="contenedor"></div>

        <div class="form-group">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="dir_encCampo">* DIRECCIÓN:</label>
                    <input type='text' name='dir_encCampo' class='form-control' required />
                </div>
                <div class="form-group col-md-2">
                    <label for="zona_encCampo">* ZONA:</label>
                     <select id="zona_encCampo" class="form-control" name="zona_encCampo" required>
                        <option value="URBANA">URBANA</option>   
                        <option value="RURAL">RURAL</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                   <label for="id_com">* COMUNA:</label>
                    <select id="id_com"  class="form-control" name="id_com" required>
                        <option value = ""></option>
                        <?php
                        $sql = $mysqli->prepare("SELECT * FROM comunas");
                        if($sql->execute()){
                        $g_result = $sql->get_result();
                        }
                        while($row = $g_result->fetch_array()){
                        ?>
                        <option value = "<?php echo $row['id_com']?>"><?php echo $row['nombre_com']?></option>
                        <?php
                        }
                        $mysqli->close();   
                        ?>
                    </select>
                </div>
            </div>
        </div>
 
        <div class="form-group">
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="id_bar">* BARRIO:</label>
                    <select  id="id_bar" name="id_bar"  class="form-control" disabled="disabled" required>
                        <option value = "">* SELECCIONE EL BARRIO:</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="corre_encCampo">CORREGIMIENTO:</label>
                    <input type='text' name='corre_encCampo' class='form-control' />
                </div>
                <div class="form-group col-md-4">
                    <label for="vere_encCampo">VEREDA:</label>
                    <input type='text' name='vere_encCampo' class='form-control' />
                </div>
            </div>
        </div>
         
       <div class="form-group">
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="num_ficha_encCampo">* No. FICHA o RADICADO:</label>
                    <input type='number' name='num_ficha_encCampo' class='form-control'  required />
                </div>
                <div class="form-group col-md-3">
                    <label for="est_fic_encCampo">* ESTADO FICHA:</label>
                    <select class="form-control" name="est_fic_encCampo" id="selectEF" required>
                        <option value=""></option>   
                        <option value="VALIDADA">VALIDADA</option>   
                        <option value="PRIMERA VISITA">PRIMERA VISITA</option>
                        <option value="SEGUNDA VISITA">SEGUNDA VISITA</option>
                        <option value="DIRECCIÓN ERRADA">DIRECCIÓN ERRADA</option>
                        <option value="YA NO VIVE">YA NO VIVE</option>
                        <option value="RECHAZADA EN LA VIVIENDA">RECHAZADA EN LA VIVIENDA</option>
                        <option value="DIRECCIÓN INCOMPLETA">DIRECCIÓN INCOMPLETA</option>
                        <option value="INFORMANTE NO IDÓNEO">INFORMANTE NO IDÓNEO</option>
                        <option value="FALLECIDO">FALLECIDO</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="doc_enc">* ENCUESTADOR:</label>
                    <select name='doc_enc' class='form-control'>
                        <option value=""></option>
                        <?php
                            $mysqli = new mysqli("localhost", "aprendad_sisben", "~CY]&J9u#wxa", "aprendad_sisben");

                            // Verificar la conexión
                            if ($mysqli->connect_error) {
                                die("Error en la conexión: " . $mysqli->connect_error);
                            }

                            // Consulta SQL para obtener  encuestadores
                            $sql = $mysqli->prepare("SELECT * FROM  encuestadores");
                            if ($sql->execute()) {
                                $result = $sql->get_result();
                            }

                            // Generamos opciones a partir de los datos de la base de datos
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['doc_enc'] . '">' . $row['nom_enc'] . '</option>';
                            }

                            // Cerrar la conexión a la base de datos
                            $mysqli->close();
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="proc_encCampo">* PROCESO CAMPO:</label>
                    <select class="form-control" name="proc_encCampo" id="selectPC" required>
                        <option value=""></option>  
                        <option value="PORTAL CIUDADANO">PORTAL CIUDADANO</option>
                        <option value="DESCENTRALIZADO">DESCENTRALIZADO</option>
                        <option value="PRIORIDAD">PRIORIDAD</option>
                        <option value="ENCUESTA POR VERIFICACIÓN">ENCUESTA POR VERIFICACIÓN</option>
                        <option value="ENCUESTA SAT">ENCUESTA SAT</option>
                        <option value="VIVE DIGITAL LA BELLA">VIVE DIGITAL LA BELLA</option>
                        <option value="VIVE DIGITAL SAN FERNANDO">VIVE DIGITAL SAN FERNANDO</option>
                        <option value="VIVE DIGITAL EL DORADO">VIVE DIGITAL EL DORADO</option>
                        <option value="VIVE DIGITAL EL REMANSO">VIVE DIGITAL EL REMANSO</option>
                        <option value="VIVE DIGITAL UTP">VIVE DIGITAL UTP</option>
                        <option value="LC PROYECTOS Y CONSTRUCCIONES S.A.S.">LC PROYECTOS Y CONSTRUCCIONES S.A.S.</option>
                        <option value="IARCO S.A.">IARCO S.A.</option>
                        <option value="LC PROYECTOS Y CONSTRUCCIONES S.A.S.">LC PROYECTOS Y CONSTRUCCIONES S.A.S.</option>
                        <option value="BASA CONSTRUCCIONES S.A.S.">BASA CONSTRUCCIONES S.A.S.</option>
                        <option value="CONSTRUCCIONES CFC & ASOCIADOS S.A.">CONSTRUCCIONES CFC & ASOCIADOS S.A.</option>
                        <option value="ASUL S.A.S.">ASUL S.A.S.</option>
                        <option value="CONSTRUCTORA Y COMERCIALIZADORA CAMU S.A.S.">CONSTRUCTORA Y COMERCIALIZADORA CAMU S.A.S.</option>
                        <option value="LATERIZIO S.A.S.">LATERIZIO S.A.S.</option>
                        <option value="PROYECTOS URBANOS 3L S.A.S.">PROYECTOS URBANOS 3L S.A.S.</option>
                        <option value="CONSTRUCTORA PALO DE AGUA S.A.">CONSTRUCTORA PALO DE AGUA S.A.</option>
                        <option value="CENTRO SUR S.A.">CENTRO SUR S.A.</option>
                        <option value="FORTAL CONSTRUCCIONES">FORTAL CONSTRUCCIONES</option>
                        <option value="A&G INVERSIONES">A&G INVERSIONES</option>
                        <option value="ASESORIA PRIVADA">ASESORIA PRIVADA</option>
                        <option value="MI CASA YA">MI CASA YA</option>
                        <option value="CONTRUCTORA RUBAU">CONTRUCTORA RUBAU</option>
                        <option value="SORIANO">SORIANO</option>
                        <option value="SEMILLAS DEL OTUN CENTRO SUR">SEMILLAS DEL OTUN CENTRO SUR</option>
                        <option value="ASDELOGY">ASDELOGY</option>
                        <option value="PORTAL CIUDADANO MI CASA YA">PORTAL CIUDADANO MI CASA YA</option>
                        <option value="ENCUESTA NUEVA POR VERIFICACION MI CASA YA">ENCUESTA NUEVA POR VERIFICACION MI CASA YA</option>
                        <option value="SALUD TOTAL">SALUD TOTAL</option>
                        <option value="FALLECIDO">FALLECIDO</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="obs_encCampo">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs_encCampo" style="text-transform:uppercase;"></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">
            <span class="spinner-border spinner-border-sm"></span>
            INGRESAR ENCUESTA
        </button>

        <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27>     REGRESAR
        </button>

        <button type="reset" class="btn btn-secondary" role='link' onclick="location='addencuestadores.php';"><img src='../../img/search.png' width=27 height=27>
            CONSULTAR
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

</body>
    <script src = "js/jquery-3.1.1.js"></script>
    <script type = "text/javascript">
        $(document).ready(function(){
            $('#id_com').on('change', function(){
                    if($('#id_com').val() == ""){
                        $('#id_bar').empty();
                        $('<option value = "">Seleccione un barrio</option>').appendTo('#id_bar');
                        $('#id_bar').attr('disabled', 'disabled');
                    }else{
                        $('#id_bar').removeAttr('disabled', 'disabled');
                        $('#id_bar').load('barriosGet.php?id_com=' + $('#id_com').val());
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