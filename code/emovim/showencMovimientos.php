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
                    ordenarSelect('tram_solic_encMovim');
                    ordenarSelect('id_com');
                    ordenarSelect('id_correg');
                });
        </script>
    </head>
    <body >
       	<?php
            include("../../conexion.php");
            date_default_timezone_set("America/Bogota");
            $time = time();
    	    $id_encMovim  = $_GET['id_encMovim'];
    	    if(isset($_GET['id_encMovim']))
    	    { 
                $sql = mysqli_query($mysqli, "SELECT * FROM encMovimientos WHERE id_encMovim = '$id_encMovim'");
    	        $row = mysqli_fetch_array($sql);
            }

        ?>

       	<div class="container">
            <center>
                <img src='../../img/sisben.png' width=300 height=185 class="responsive">
            </center>

            <h1><b><i class="fas fa-users"></i> VERIFICAR Y ACTUALIZAR INFORMACIÓN DEL ENCUESTADO</b></h1>
            <p><i><b><font size=3 color=#c68615>* Datos obligatorios</i></b></font></p>
            
            <form action='editencMovimientos.php' enctype="multipart/form-data" method="POST">
                
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <input type='number' name='id_encMovim' id="id_encMovim" class='form-control' value='<?php echo $row['id_encMovim']; ?>' readonly hidden/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <label for="fec_reg_encMovim">* F. REALIZADA:</label>
                            <input type='date' name='fec_reg_encMovim' class='form-control' value='<?php echo $row['fec_reg_encMovim']; ?>'required/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="doc_encMovim">DOCUMENTO:</label>
                            <input type='text' name='doc_encMovim' class='form-control'  value='<?php echo $row['doc_encMovim']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-3">
                        <label for="tipo_doc">* TIPO DE DOCUMENTO:</label>
                        <select name="tipo_documento" class="form-control" id="">
                            <option value="" <?php if ($row['tipo_documento'] == "") echo 'selected'; ?>>SELECCIONE :</option>
                            <option value="cedula" <?php if ($row['tipo_documento'] == "cedula") echo 'selected'; ?>>CEDULA</option>
                            <option value="ppt" <?php if ($row['tipo_documento'] == "ppt") echo 'selected'; ?>>PPT</option>
                            <option value="cedula_extranjeria" <?php if ($row['tipo_documento'] == "cedula_extranjeria") echo 'selected'; ?>>CEDULA EXTRANJERIA</option>
                            <option value="otro" <?php if ($row['tipo_documento'] == "otro") echo 'selected'; ?>>OTRO</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="ciudad_expedicion">* CIUDAD EXPEDICION:</label>
                        <input type='text' name='ciudad_expedicion' value='<?php echo $row['ciudad_expedicion'] ?? "" ?>' class='form-control' required style="text-transform:uppercase;" />
                    </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">
                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICION:</label>
                        <input type='date' name='fecha_expedicion'  value='<?php echo $row['fecha_expedicion'] ?? "" ?>'class='form-control' required style="text-transform:uppercase;" />
                    </div>

                        <div class="col-12 col-sm-4">
                            <label for="nom_encMovim">* NOMBRE DEL USUARIO:</label>
                            <input type='text' name='nom_encMovim' class='form-control'  value='<?php echo $row['nom_encMovim']; ?>' style="text-transform:uppercase;" required/>
                        </div>
                        <div class="col-12 col-sm-5">
                            <label for="dir_encMovim">* DIRECCIÓN</label>
                            <input type='text' name='dir_encMovim' class='form-control' value='<?php echo $row['dir_encMovim']; ?>' required/>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="row">
                        
                        <div class="col-12 col-sm-2">
                            <label for="zona_encMovim">* ZONA:</label>
                            <select class="form-control" name="zona_encMovim" required >
                                <option value="">SELECCIONE:</option>   
                                <option value="URBANA" <?php if($row['zona_encMovim']=='URBANA'){echo 'selected';} ?>>URBANA</option>
                                <option value="RURAL" <?php if($row['zona_encMovim']=='RURAL'){echo 'selected';} ?>>RURAL</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="id_com">* COMUNA:</label>
                            <select name='id_com' class='form-control' id="id_com" required />
                                <option value=''></option>
                                <?php
                                    header('Content-Type: text/html;charset=utf-8');
                                    $consulta='SELECT * FROM comunas';
                                    $res = mysqli_query($mysqli,$consulta);
                                    $num_reg = mysqli_num_rows($res);
                                    while($row1 = $res->fetch_array())
                                    {
                                    ?>
                                        <option value='<?php echo $row1['id_com']; ?>'<?php if($row['id_com']==$row1['id_com']){echo 'selected';} ?>>
                                            <?php echo $row1['nombre_com']; ?>
                                        </option>
                                    <?php
                                    }
                                ?>    
                            </select>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="id_bar">* BARRIO:</label>
                            <select name='id_bar' class='form-control' id="id_bar" required />
                                <option value=''></option>
                                <?php
                                    header('Content-Type: text/html;charset=utf-8');
                                    $consulta='SELECT * FROM barrios';
                                    $res = mysqli_query($mysqli,$consulta);
                                    $num_reg = mysqli_num_rows($res);
                                    while($row2 = $res->fetch_array())
                                    {
                                    ?>
                                        <option value='<?php echo $row2['id_bar']; ?>'<?php if($row['id_bar']==$row2['id_bar']){echo 'selected';} ?>>
                                            <?php echo $row2['nombre_bar']; ?>
                                        </option>
                                    <?php
                                    }
                                ?>    
                            </select>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="id_correg">CORREGIMIENTO</label>
                            <select name='id_correg' class='form-control' id="id_correg" required />
                                <option value=''></option>
                                <?php
                                    header('Content-Type: text/html;charset=utf-8');
                                    $consulta='SELECT * FROM corregimientos';
                                    $res = mysqli_query($mysqli,$consulta);
                                    $num_reg = mysqli_num_rows($res);
                                    while($row4 = $res->fetch_array())
                                    {
                                    ?>
                                        <option value='<?php echo $row4['id_correg']; ?>'<?php if($row4['id_correg']==$row4['id_correg']){echo 'selected';} ?>>
                                            <?php echo $row4['nombre_correg']; ?>
                                        </option>
                                    <?php
                                    }
                                ?>    
                            </select>
                        </div>
                    </div>
                </div>
                <script>
                    $("#id_bar").select2({
                        tags: true
                    });
                </script>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <label for="id_vere">VEREDA</label>
                            <select name='id_vere' class='form-control' id='id_vere' required />
                                <option value=''></option>
                                <?php
                                    header('Content-Type: text/html;charset=utf-8');
                                    $consulta='SELECT * FROM veredas';
                                    $res = mysqli_query($mysqli,$consulta);
                                    $num_reg = mysqli_num_rows($res);
                                    while($row5 = $res->fetch_array())
                                    {
                                    ?>
                                        <option value='<?php echo $row5['id_vere']; ?>'<?php if($row5['id_vere']==$row5['id_vere']){echo 'selected';} ?>>
                                            <?php echo $row5['nombre_vere']; ?>
                                        </option>
                                    <?php
                                    }
                                ?>    
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="otro_bar_ver_encMovim">OTRO BARRIO:</label>
                            <input type='text' id='otro_bar_ver_encMovim' name='otro_bar_ver_encMovim' class='form-control' value='<?php echo $row['otro_bar_ver_encMovim']; ?>' style="text-transform:uppercase;" />
                        </div>
                        <div class="col-12 col-sm-5">
                            <label for="tram_solic_encMovim">* TRÁMITE SOLICITADO:</label>
                            <select class="form-control" name="tram_solic_encMovim" required id="tram_solic_encMovim">
                                <option value="">SELECCIONE:</option>   
                                <option value="MODIFICACION DATOS PERSONA" <?php if($row['tram_solic_encMovim']=='MODIFICACION DATOS PERSONA'){echo 'selected';} ?>>MODIFICACION DATOS PERSONA</option>
                                <option value="INCLUSION" <?php if($row['tram_solic_encMovim']=='INCLUSION'){echo 'selected';} ?>>INCLUSION</option>
                                <option value="RETIRO PERSONAS" <?php if($row['tram_solic_encMovim']=='RETIRO PERSONAS'){echo 'selected';} ?>>RETIRO PERSONAS</option>
                                <option value="RETIRO FICHA" <?php if($row['tram_solic_encMovim']=='RETIRO FICHA'){echo 'selected';} ?>>RETIRO FICHA</option>
                                <option value="INCONFORMIDAD POR CLASIFICACION" <?php if($row['tram_solic_encMovim']=='INCONFORMIDAD POR CLASIFICACION'){echo 'selected';} ?>>INCONFORMIDAD POR CLASIFICACION</option>
                                <option value="RETIRO POR INCONFORMIDAD" <?php if($row['tram_solic_encMovim']=='RETIRO POR INCONFORMIDAD'){echo 'selected';} ?>>RETIRO POR INCONFORMIDAD</option>
                                <option value="RETIRO DE PERSONA POR INCONFORMIDAD" <?php if($row['tram_solic_encMovim']=='RETIRO DE PERSONA POR INCONFORMIDAD'){echo 'selected';} ?>>RETIRO DE PERSONA POR INCONFORMIDAD</option>
                            </select>
                        </div>
                    </div>
                </div>
                <script>
                    $("#id_vere").select2({
                        tags: true
                    });
                </script>

                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="integra_encMovim">* INTEGRANTES:</label>
                            <input type='number' id='integra_encMovim' name='integra_encMovim' class='form-control' value='<?php echo $row['integra_encMovim']; ?>' required readonly />
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="num_ficha_encMovim">* No. FICHA:</label>
                            <input type='number' name='num_ficha_encMovim' class='form-control'  value='<?php echo $row['num_ficha_encMovim']; ?>' required/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <label for="obs_encMovim">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="obs_encMovim" style="text-transform:uppercase;" /><?php echo $row['obs_encMovim']; ?></textarea>
                        </div>
                    </div>
                </div>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">

                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR INFORMACIÓN MOVIMIENTOS
                </button>
                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
                </button>
            </form>
        </div>
    </body>
        <script src = "js/jquery-3.1.1.js"></script>
        <script type = "text/javascript">
            $(document).ready(function(){
                $('#id_com').on('change', function(){
                        if($('#id_com').val() == ""){
                            $('#id_bar').empty();
                            $('<option value = "">SELECCIONE EL BARRIO:</option>').appendTo('#id_bar');
                            $('#id_bar').attr('disabled', 'disabled');
                        }else{
                            $('#id_bar').removeAttr('disabled', 'disabled');
                            $('#id_bar').load('barriosGet.php?id_com=' + $('#id_com').val());
                        }
                });
            });
        </script>

        <script type = "text/javascript">
            $(document).ready(function(){
                $('#id_correg').on('change', function(){
                        if($('#id_correg').val() == ""){
                            $('#id_vere').empty();
                            $('<option value = "">SELECCIONE UNA VEREDA:</option>').appendTo('#id_vere');
                            $('#id_vere').attr('disabled', 'disabled');
                        }else{
                            $('#id_vere').removeAttr('disabled', 'disabled');
                            $('#id_vere').load('veredasGet.php?id_correg=' + $('#id_correg').val());
                        }
                });
            });
        </script>
</html>