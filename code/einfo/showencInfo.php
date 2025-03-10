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
    	<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BD SISBEN</title>
        <link href="../../css/bootstrap.min.css" rel="stylesheet">
        <link href="../../fontawesome/css/all.css" rel="stylesheet">
        <script type="text/javascript" src="../../js/jquery.min.js"></script>
        <!-- Using Select2 from a CDN-->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://kit.fontawesome.com/fed2435e21.js" ></script>

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

                $(document).ready(function () {
                    // Llamadas a la función de ordenar para distintos selects
                    ordenarSelect('tipo_solic_encInfo');
                    ordenarSelect('obs1_encInfo');
                });
        </script>
    </head>
    <body >
       	<?php
            include("../../conexion.php");
            date_default_timezone_set("America/Bogota");
            $time = time();
    	    $id_encInfo  = $_GET['id_encInfo'];
    	    if(isset($_GET['id_encInfo']))
    	    { 
                $sql = mysqli_query($mysqli, "SELECT * FROM encInfo WHERE id_encInfo = '$id_encInfo'");
    	        $row = mysqli_fetch_array($sql);
            }

        ?>

       	<div class="container">
            <center>
                <img src='../../img/sisben.png' width=300 height=185 class="responsive">
            </center>

            <h1><b><i class="fa-solid fa-circle-info"></i> VERIFICAR y/o MODIFICAR INFORMACION</b></h1>
            <p><i><b><font size=3 color=#c68615>* Datos obligatorios</i></b></font></p>
            
            <form action='editencInfo.php' enctype="multipart/form-data" method="POST">
                
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <input type='number' name='id_encInfo' id="id_encInfo" class='form-control' value='<?php echo $row['id_encInfo']; ?>' readonly hidden/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <label for="fec_rea_encInfo">* F. REALIZADA:</label>
                            <input type='date' name='fec_rea_encInfo' class='form-control' value='<?php echo $row['fec_rea_encInfo']; ?>'required/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="doc_encInfo">* DOCUMENTO:</label>
                            <input type='text' name='doc_encInfo' class='form-control'  value='<?php echo $row['doc_encInfo']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="nom_encInfo">* NOMBRE DEL USUARIO:</label>
                            <input type='text' name='nom_encInfo' class='form-control'  value='<?php echo $row['nom_encInfo']; ?>' style="text-transform:uppercase;" required/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="tipo_solic_encInfo">* TIPO SOLICITUD:</label>
                            <select class="form-control" name="tipo_solic_encInfo" required id="tipo_solic_encInfo">
                                <option value=""></option>   
                                <option value="INFORMACION" <?php if($row['tipo_solic_encInfo']=='INFORMACION'){echo 'selected';} ?>>INFORMACION</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-5">
                            <label for="obs1_encInfo">* OBSERVACION:</label>
                            <select class="form-control" name="obs1_encInfo" required id="obs1_encInfo">
                                <option value=""></option>   
                                <option value="ACTUALIZACION" <?php if($row['obs1_encInfo']=='ACTUALIZACION'){echo 'selected';} ?>>ACTUALIZACION</option>
                                <option value="CLASIFICACION" <?php if($row['obs1_encInfo']=='CLASIFICACION'){echo 'selected';} ?>>CLASIFICACION</option>
                                <option value="DIRECCION" <?php if($row['obs1_encInfo']=='DIRECCION'){echo 'selected';} ?>>DIRECCION</option>
                                <option value="DOCUMENTO" <?php if($row['obs1_encInfo']=='DOCUMENTO'){echo 'selected';} ?>>DOCUMENTO</option>
                                <option value="INCLUSION" <?php if($row['obs1_encInfo']=='INCLUSION'){echo 'selected';} ?>>INCLUSION</option>
                                <option value="PENDIENTE" <?php if($row['obs1_encInfo']=='PENDIENTE'){echo 'selected';} ?>>PENDIENTE</option>
                                <option value="VERIFICACION" <?php if($row['obs1_encInfo']=='VERIFICACION'){echo 'selected';} ?>>VERIFICACION</option>
                                <option value="VISITA" <?php if($row['obs1_encInfo']=='VISITA'){echo 'selected';} ?>>VISITA</option>
                                <option value="CALIDAD DE LA ENCUESTA" <?php if($row['obs1_encInfo']=='CALIDAD DE LA ENCUESTA'){echo 'selected';} ?>>CALIDAD DE LA ENCUESTA</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <label for="obs2_encInfo">INFORMACION y/o COMENTARIOS ADICIONALES:</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="obs2_encInfo" style="text-transform:uppercase;" /><?php echo $row['obs2_encInfo']; ?></textarea>
                        </div>
                    </div>
                </div>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">

                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR INFORMACIÓN
                </button>
                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
                </button>
            </form>
        </div>
        <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
    </body>
</html>