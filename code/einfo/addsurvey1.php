<?php
    
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
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
        <script src="https://kit.fontawesome.com/fed2435e21.js" ></script>
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
            function ordenarSelect(id_componente) 
            {
                var selectToSort = $('#' + id_componente);
                var optionActual = selectToSort.val();
                selectToSort.html(selectToSort.children('option').sort(function (a, b) {
                    return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
                })).val(optionActual);
            }

            $(document).ready(function () 
            {
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
            if(isset($_GET['id_usu']))
            { 
                $sql = mysqli_query($mysqli, "SELECT * FROM usuarios WHERE id_usu = '$id_usu'");
                $row = mysqli_fetch_array($sql);
            }
        ?>
    

        <form id="form_contacto" action='addsurvey2.php' method="POST" enctype="multipart/form-data">

            <div class="container pt-2">
                <h1><b><i class="fa-solid fa-circle-info"></i> INFORMACION</b></h1>
                <p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
                
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="fec_rea_encInfo">* FECHA REALIZADA:</label>
                            <input type='date' name='fec_rea_encInfo' class='form-control' id="fec_rea_encInfo" required autofocus />
                        </div>
                        <div class="form-group col-md-3">
                            <label for="doc_encInfo">* DOCUMENTO:</label>
                            <input type='number' name='doc_encInfo' class='form-control' id="doc_encInfo" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nom_encInfo">* NOMBRES COMPLETOS:</label>
                            <input type='text' name='nom_encInfo' class='form-control' required style="text-transform:uppercase;" />
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

                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27>     REGRESAR
                </button>
            </div>
        </form>
    
    </body>
    
</html>