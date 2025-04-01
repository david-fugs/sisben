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
        .responsive {
            max-width: 100%;
            height: auto;
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
            ordenarSelect('tipo_solic_encInfo');
            ordenarSelect('obs1_encInfo');
        });
    </script>
</head>

<body>

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
            <h1><b><i class="fa-solid fa-circle-info"></i> INFORMACION</b></h1>
            <p><i><b>
                        <font size=3 color=#c68615>*Datos obligatorios</i></b></font>
            </p>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="fec_reg_info">* FECHA REGISTRO:</label>
                        <input type='date' name='fec_reg_info' class='form-control' id="fec_reg_info" required autofocus />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="doc_info">* DOCUMENTO:</label>
                        <input type='number' name='doc_info' class='form-control' id="doc_info" required />
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
                        <label for="nom_info">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_info' class='form-control' required style="text-transform:uppercase;" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="gen_integVenta">* IDENTIDAD DE GENERO:</label>
                        <select name="gen_integVenta" class="form-control" id="gen_integVenta" required>
                            <option value=""></option>
                            <option value="M">MASCULINO</option>
                            <option value="F">FEMENINO</option>
                            <option value="OTRO">OTRO</option>
                        </select> <!-- Asegurar el cierre correcto aquí -->
                    </div>
                    <div class="form-group col-md-3">
                        <label for="rango_integVenta">* RANGO DE EDAD:</label>
                        <select name="rango_integVenta" class="form-control" id="rango_integVenta" required>
                            <option value=""></option>
                            <option value="0 - 6">0 - 6</option>
                            <option value="7 - 12">7 - 12</option>
                            <option value="13 - 17">13 - 17</option>
                            <option value="18 - 28">18 - 28</option>
                            <option value="29-45">29-45</option>
                            <option value="46-64">46-64</option>
                            <option value="Mayor o igual a 65">Mayor o igual a 65</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="victima">* VICTIMA:</label>
                        <select name="victima" class="form-control" id="victima" required>
                            <option value=""></option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="condicionDiscapacidad">* CONDICION DISCAPACIDAD:</label>
                        <select name="condicionDiscapacidad" class="form-control" id="condicionDiscapacidad" required>
                            <option value=""></option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group" id="tipoDiscapacidadContainer" style="display: none;">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="tipoDiscapacidad">* TPO DISCAPACIDAD:</label>
                        <select class="form-control" name="tipoDiscapacidad" id="tipoDiscapacidad" required>
                            <option value=""></option>
                            <option value="Auditiva">Auditiva</option>
                            <option value="Fisica">Fisica</option>
                            <option value="Intelectual">Intelectual</option>
                            <option value="Multiple">Multiple</option>
                            <option value="Psicosocial">Psicosocial</option>
                            <option value="SordoCeguera">SordoCeguera</option>
                            <option value="Visual">Visual</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="mujerGestante">* MUJER GESTANTE/LACTANTE:</label>
                        <select name="mujerGestante" class="form-control" id="mujerGestante" required>
                            <option value=""></option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select> <!-- Asegurar el cierre correcto aquí -->
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cabeza_familia">* HOMBRE/MUJER CABEZA FAMILIA:</label>
                        <select name="cabeza_familia" class="form-control" id="cabeza_familia" required>
                            <option value=""></option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="orientacionSexual">* ORIENTACION SEXUAL:</label>
                        <select name="orientacionSexual" class="form-control" id="orientacionSexual" required>
                            <option value=""></option>
                            <option value="Asexual">Asexual</option>
                            <option value="Bisexual">Bisexual</option>
                            <option value="Homosexual">Homosexual</option>
                            <option value="Heterosexual">Heterosexual</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="experienciaMigratoria">* EXPERIENCIA MIGRATORIA</label>
                        <select name="experienciaMigratoria" class="form-control" id="experienciaMigratoria" required>
                            <option value=""></option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select> <!-- Asegurar el cierre correcto aquí -->
                    </div>
                    <div class="form-group col-md-4">
                        <label for="grupoEtnico">* GRUPO ETNICO:</label>
                        <select name="grupoEtnico" class="form-control" id="grupoEtnico" required>
                            <option value=""></option>
                            <option value="Indigena">Indigena</option>
                            <option value="ROM (Gitano)">ROM (Gitano)</option>
                            <option value="Raizal">Raizal</option>
                            <option value="Palanquero de San Basilio">Palanquero de San Basilio</option>
                            <option value="Negro(a), Mulato(a), Afrocolobiano(a)">Negro(a), Mulato(a), Afrocolobiano(a)</option>
                            <option value="Mestizo"> Mestizo</option>
                            <option value="Ninguno"> Ninguno</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="seguridadSalud">* TIPO SEGURIDAD SALUD:</label>
                        <select name="seguridadSalud" class="form-control" id="seguridadSalud" required>
                            <option value=""></option>
                            <option value="Regimen Contributivo">Regimen Contributivo</option>
                            <option value="Regimen Subsidiado">Regimen Subsidiado</option>
                            <option value="Poblacion Vinculada">Poblacion Vinculada</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="tipo_solic_encInfo">* TIPO SOLICITUD:</label>
                        <select class="form-control" name="tipo_solic_encInfo" id="tipo_solic_encInfo" required>
                            <option value=""></option>
                            <option value="INFORMACION">INFORMACION</option>
                            <option value="ATENCION">ATENCION</option>
                        </select>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="obs1_encInfo">* OBSERVACION:</label>
                        <select class="form-control" name="obs1_encInfo" id="obs1_encInfo" required>
                            <option value=""></option>
                            <option value="ACTUALIZACION">ACTUALIZACION</option>
                            <option value="CLASIFICACION">CLASIFICACION</option>
                            <option value="DIRECCION">DIRECCION</option>
                            <option value="DOCUMENTO">DOCUMENTO</option>
                            <option value="INCLUSION">INCLUSION</option>
                            <option value="PENDIENTE">PENDIENTE</option>
                            <option value="VERIFICACION">VERIFICACION</option>
                            <option value="VISITA">VISITA</option>
                            <option value="CALIDAD DE LA ENCUESTA">CALIDAD DE LA ENCUESTA</option>
                            <option value="ATENCION">ATENCION</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="obs2_encInfo">INFORMACION ADICIONAL:</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs2_encInfo" style="text-transform:uppercase;"></textarea>
                </div>
            </div>





            <button type="submit" class="btn btn-success">
                <span class="spinner-border spinner-border-sm"></span>
                INGRESAR INFORMACION
            </button>

            <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
            </button>
        </div>
    </form>

</body>
<script>
    document.getElementById("condicionDiscapacidad").addEventListener("change", function() {
        var tipoDiscapacidadContainer = document.getElementById("tipoDiscapacidadContainer");
        if (this.value === "Si") {
            tipoDiscapacidadContainer.style.display = "block";
        } else {
            tipoDiscapacidadContainer.style.display = "none";
            document.getElementById("tipo_discapacidad").value = ""; // Reiniciar selección
        }
    });
</script>

</html>