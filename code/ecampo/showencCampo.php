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
                    ordenarSelect('id_com');
                    ordenarSelect('id_bar');
                    ordenarSelect('id_vere');
                    ordenarSelect('id_correg');
                    ordenarSelect('est_fic_encCampo');
                    ordenarSelect('proc_encCampo');
                });
        </script>
    </head>
    <body >
       	<?php
            include("../../conexion.php");
            date_default_timezone_set("America/Bogota");
            $time = time();
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

            <h1><b><i class="fas fa-users"></i> VERIFICAR Y ACTUALIZAR INFORMACIÓN DEL ENCUESTADO</b></h1>
            <p><i><b><font size=3 color=#c68615>* Datos obligatorios</i></b></font></p>
            
            <form action='editencCampo.php' enctype="multipart/form-data" method="POST">
                
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <input type='number' name='id_encCampo' id="id_encCampo" class='form-control' value='<?php echo $row['id_encCampo']; ?>' readonly hidden/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <label for="fec_pre_encCampo">* FECHA PREREGISTRO:</label>
                            <input type='date' name='fec_pre_encCampo' class='form-control' value='<?php echo $row['fec_pre_encCampo']; ?>'required/>
                        </div>
                        <div class="col-12 col-sm-2">
                            <label for="fec_rea_encCampo">* FECHA REALIZADA:</label>
                            <input type='date' name='fec_rea_encCampo' class='form-control' value='<?php echo $row['fec_rea_encCampo']; ?>'required/>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="doc_encCampo">DOCUMENTO:</label>
                            <input type='text' name='doc_encCampo' class='form-control'  value='<?php echo $row['doc_encCampo']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="nom_encCampo">* NOMBRE DEL USUARIO:</label>
                            <input type='text' name='nom_encCampo' class='form-control'  value='<?php echo $row['nom_encCampo']; ?>' style="text-transform:uppercase;" required/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <label for="dir_encCampo">* DIRECCIÓN</label>
                            <input type='text' name='dir_encCampo' class='form-control' value='<?php echo $row['dir_encCampo']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-2">
                            <label for="zona_encCampo">* ZONA:</label>
                            <select class="form-control" name="zona_encCampo" required >
                                <option value="">SELECCIONE:</option>   
                                <option value="URBANA" <?php if($row['zona_encCampo']=='URBANA'){echo 'selected';} ?>>URBANA</option>
                                <option value="RURAL" <?php if($row['zona_encCampo']=='RURAL'){echo 'selected';} ?>>RURAL</option>
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
                        <div class="form-group col-md-3">
                            <label for="otro_bar_ver_encCampo">OTRO BARRIO:</label>
                            <input type='text' id='otro_bar_ver_encCampo' name='otro_bar_ver_encCampo' class='form-control' value='<?php echo $row['otro_bar_ver_encCampo']; ?>' />
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="est_fic_encCampo">* ESTADO:</label>
                            <select class="form-control" name="est_fic_encCampo" required id="est_fic_encCampo">
                                <option value="">SELECCIONE:</option>   
                                <option value="VALIDADA" <?php if($row['est_fic_encCampo']=='VALIDADA'){echo 'selected';} ?>>VALIDADA</option>
                                <option value="PRIMERA VISITA" <?php if($row['est_fic_encCampo']=='PRIMERA VISITA'){echo 'selected';} ?>>PRIMERA VISITA</option>
                                <option value="SEGUNDA VISITA" <?php if($row['est_fic_encCampo']=='SEGUNDA VISITA'){echo 'selected';} ?>>SEGUNDA VISITA</option>
                                <option value="DIRECCIÓN ERRADA" <?php if($row['est_fic_encCampo']=='DIRECCIÓN ERRADA'){echo 'selected';} ?>>DIRECCIÓN ERRADA</option>
                                <option value="YA NO VIVE" <?php if($row['est_fic_encCampo']=='YA NO VIVE'){echo 'selected';} ?>>YA NO VIVE</option>
                                <option value="RECHAZADA EN LA VIVIENDA" <?php if($row['est_fic_encCampo']=='RECHAZADA EN LA VIVIENDA'){echo 'selected';} ?>>RECHAZADA EN LA VIVIENDA</option>
                                <option value="DIRECCIÓN INCOMPLETA" <?php if($row['est_fic_encCampo']=='DIRECCIÓN INCOMPLETA'){echo 'selected';} ?>>DIRECCIÓN INCOMPLETA</option>
                                <option value="INFORMANTE NO IDÓNEO" <?php if($row['est_fic_encCampo']=='INFORMANTE NO IDÓNEO'){echo 'selected';} ?>>INFORMANTE NO IDÓNEO</option>
                                <option value="FALLECIDO" <?php if($row['est_fic_encCampo']=='FALLECIDO'){echo 'selected';} ?>>FALLECIDO</option>
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
                            <label for="integra_encCampo">* INTEGRANTES:</label>
                            <input type='number' id='integra_encCampo' name='integra_encCampo' class='form-control' value='<?php echo $row['integra_encCampo']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="num_ficha_encCampo">* No. FICHA:</label>
                            <input type='number' name='num_ficha_encCampo' class='form-control'  value='<?php echo $row['num_ficha_encCampo']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="proc_encCampo">* PROCESO CAMPO:</label>
                            <select class="form-control" name="proc_encCampo" id="proc_encCampo" required>
                                <option value=""></option>   
                                <option value="PORTAL CIUDADANO" <?php if($row['proc_encCampo']=='PORTAL CIUDADANO'){echo 'selected';} ?>>PORTAL CIUDADANO</option>
                                <option value="DESCENTRALIZADO" <?php if($row['proc_encCampo']=='DESCENTRALIZADO'){echo 'selected';} ?>>DESCENTRALIZADO</option>
                                <option value="PRIORIDAD" <?php if($row['proc_encCampo']=='PRIORIDAD'){echo 'selected';} ?>>PRIORIDAD</option>
                                <option value="ENCUESTA POR VERIFICACIÓN" <?php if($row['proc_encCampo']=='ENCUESTA POR VERIFICACIÓN'){echo 'selected';} ?>>ENCUESTA POR VERIFICACIÓN</option>
                                <option value="ENCUESTA SAT" <?php if($row['proc_encCampo']=='ENCUESTA SAT'){echo 'selected';} ?>>ENCUESTA SAT</option>
                                <option value="VIVE DIGITAL LA BELLA" <?php if($row['proc_encCampo']=='VIVE DIGITAL LA BELLA'){echo 'selected';} ?>>VIVE DIGITAL LA BELLA</option>
                                <option value="VIVE DIGITAL SAN FERNANDO" <?php if($row['proc_encCampo']=='VIVE DIGITAL SAN FERNANDO'){echo 'selected';} ?>>VIVE DIGITAL SAN FERNANDO</option>
                                <option value="VIVE DIGITAL EL DORADO" <?php if($row['proc_encCampo']=='VIVE DIGITAL EL DORADO'){echo 'selected';} ?>>VIVE DIGITAL EL DORADO</option>
                                <option value="VIVE DIGITAL EL REMANSO" <?php if($row['proc_encCampo']=='VIVE DIGITAL EL REMANSO'){echo 'selected';} ?>>VIVE DIGITAL EL REMANSO</option>
                                <option value="VIVE DIGITAL UTP" <?php if($row['proc_encCampo']=='VIVE DIGITAL UTP'){echo 'selected';} ?>>VIVE DIGITAL UTP</option>
                                <option value="LC PROYECTOS Y CONSTRUCCIONES S.A.S." <?php if($row['proc_encCampo']=='LC PROYECTOS Y CONSTRUCCIONES S.A.S.'){echo 'selected';} ?>>LC PROYECTOS Y CONSTRUCCIONES S.A.S.</option>
                                <option value="IARCO S.A." <?php if($row['proc_encCampo']=='IARCO S.A.'){echo 'selected';} ?>>IARCO S.A.</option>
                                <option value="LC PROYECTOS Y CONSTRUCCIONES S.A.S." <?php if($row['proc_encCampo']=='LC PROYECTOS Y CONSTRUCCIONES S.A.S.'){echo 'selected';} ?>>LC PROYECTOS Y CONSTRUCCIONES S.A.S.</option>
                                <option value="BASA CONSTRUCCIONES S.A.S." <?php if($row['proc_encCampo']=='BASA CONSTRUCCIONES S.A.S.'){echo 'selected';} ?>>BASA CONSTRUCCIONES S.A.S.</option>
                                <option value="CONSTRUCCIONES CFC & ASOCIADOS S.A." <?php if($row['proc_encCampo']=='CONSTRUCCIONES CFC & ASOCIADOS S.A.'){echo 'selected';} ?>>CONSTRUCCIONES CFC & ASOCIADOS S.A.</option>
                                <option value="ASUL S.A.S." <?php if($row['proc_encCampo']=='ASUL S.A.S.'){echo 'selected';} ?>>ASUL S.A.S.</option>
                                 <option value="CONSTRUCTORA Y COMERCIALIZADORA CAMU S.A.S." <?php if($row['proc_encCampo']=='CONSTRUCTORA Y COMERCIALIZADORA CAMU S.A.S.'){echo 'selected';} ?>>CONSTRUCTORA Y COMERCIALIZADORA CAMU S.A.S.</option>
                                <option value="LATERIZIO S.A.S." <?php if($row['proc_encCampo']=='LATERIZIO S.A.S.'){echo 'selected';} ?>>LATERIZIO S.A.S.</option>
                                 <option value="PROYECTOS URBANOS 3L S.A.S." <?php if($row['proc_encCampo']=='PROYECTOS URBANOS 3L S.A.S.'){echo 'selected';} ?>>PROYECTOS URBANOS 3L S.A.S.</option>
                                <option value="CONSTRUCTORA PALO DE AGUA S.A." <?php if($row['proc_encCampo']=='CONSTRUCTORA PALO DE AGUA S.A.'){echo 'selected';} ?>>CONSTRUCTORA PALO DE AGUA S.A.</option>
                                 <option value="CENTRO SUR S.A." <?php if($row['proc_encCampo']=='CENTRO SUR S.A.'){echo 'selected';} ?>>CENTRO SUR S.A.</option>
                                <option value="FORTAL CONSTRUCCIONES" <?php if($row['proc_encCampo']=='FORTAL CONSTRUCCIONES'){echo 'selected';} ?>>FORTAL CONSTRUCCIONES</option>
                                <option value="A&G INVERSIONES" <?php if($row['proc_encCampo']=='A&G INVERSIONES'){echo 'selected';} ?>>A&G INVERSIONES</option>
                                <option value="ASESORIA PRIVADA" <?php if($row['proc_encCampo']=='ASESORIA PRIVADA'){echo 'selected';} ?>>ASESORIA PRIVADA</option>
                                <option value="MI CASA YA" <?php if($row['proc_encCampo']=='MI CASA YA'){echo 'selected';} ?>>MI CASA YA</option>
                                <option value="CONTRUCTORA RUBAU" <?php if($row['proc_encCampo']=='CONTRUCTORA RUBAU'){echo 'selected';} ?>>CONTRUCTORA RUBAU</option>
                                <option value="SORIANO" <?php if($row['proc_encCampo']=='SORIANO'){echo 'selected';} ?>>SORIANO</option>
                                <option value="SEMILLAS DEL OTUN CENTRO SUR" <?php if($row['proc_encCampo']=='SEMILLAS DEL OTUN CENTRO SUR'){echo 'selected';} ?>>SEMILLAS DEL OTUN CENTRO SUR</option>
                                <option value="ASDELOGY" <?php if($row['proc_encCampo']=='ASDELOGY'){echo 'selected';} ?>>ASDELOGY</option>
                                <option value="PORTAL CIUDADANO MI CASA YA" <?php if($row['proc_encCampo']=='PORTAL CIUDADANO MI CASA YA'){echo 'selected';} ?>>PORTAL CIUDADANO MI CASA YA</option>
                                <option value="ENCUESTA NUEVA POR VERIFICACION MI CASA YA" <?php if($row['proc_encCampo']=='ENCUESTA NUEVA POR VERIFICACION MI CASA YA'){echo 'selected';} ?>>ENCUESTA NUEVA POR VERIFICACION MI CASA YA</option>
                                <option value="FALLECIDO" <?php if($row['proc_encCampo']=='FALLECIDO'){echo 'selected';} ?>>FALLECIDO</option>
                                <option value="N/A" <?php if($row['proc_encCampo']=='N/A'){echo 'selected';} ?>>N/A</option>               
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <label for="obs_encCampo">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="obs_encCampo" style="text-transform:uppercase;" /><?php echo $row['obs_encCampo']; ?></textarea>
                        </div>
                    </div>
                </div>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">

                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR INFORMACIÓN ENCUESTA
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