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
    </head>
    <body >
       	<?php
            include("../../conexion.php");
            date_default_timezone_set("America/Bogota");
            $time = time();
    	    $id_integVenta  = $_GET['id_integVenta'];
    	    if(isset($_GET['id_integVenta']))
    	    { 
                $sql = mysqli_query($mysqli, "SELECT * FROM integVentanilla WHERE id_integVenta = '$id_integVenta'");
    	        $row = mysqli_fetch_array($sql);
            }

        ?>

       	<div class="container">
            <center>
                <img src='../../img/sisben.png' width=300 height=185 class="responsive">
            </center>

            <h1><b><i class="fas fa-users"></i> VERIFICAR Y ACTUALIZAR INFORMACIÓN DE LOS INTEGRANTES DEL GRUPO FAMILIAR</b></h1>
            <p><i><b><font size=3 color=#c68615>* Datos obligatorios</i></b></font></p>
            
            <form action='editintegVentanilla.php' enctype="multipart/form-data" method="POST">
                
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <input type='number' name='id_integVenta' id="id_integVenta" class='form-control' value='<?php echo $row['id_integVenta']; ?>' readonly hidden/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-2">
                            <label for="cant_integVenta">* CANTIDAD:</label>
                            <input type='number' name='cant_integVenta' class='form-control'  value='<?php echo $row['cant_integVenta']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-2">
                            <label for="gen_integVenta">* IDENTIDAD GENERO</label>
                            <select class="form-control" name="gen_integVenta" required >
                                <option value=""></option>   
                                <option value="M" <?php if($row['gen_integVenta']=='M'){echo 'selected';} ?>>M</option>
                                <option value="F" <?php if($row['gen_integVenta']=='F'){echo 'selected';} ?>>F</option>
                                <option value="O" <?php if($row['gen_integVenta']=='O'){echo 'selected';} ?>>OTRO</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="rango_integVenta">* EDAD:</label>
                            <select class="form-control" name="rango_integVenta" required >
                                <option value="">SELECCIONE:</option>   
                                <option value=1 <?php if($row['rango_integVenta']==1){echo 'selected';} ?>>0 - 6</option>
                                <option value=2 <?php if($row['rango_integVenta']==2){echo 'selected';} ?>>7 - 12</option>
                                <option value=3 <?php if($row['rango_integVenta']==3){echo 'selected';} ?>>13 - 17</option>
                                <option value=4 <?php if($row['rango_integVenta']==4){echo 'selected';} ?>>18 - 28</option>
                                <option value=5 <?php if($row['rango_integVenta']==5){echo 'selected';} ?>>29 - 45</option>
                                <option value=6 <?php if($row['rango_integVenta']==6){echo 'selected';} ?>>46 - 64</option>
                                <option value=7 <?php if($row['rango_integVenta']==7){echo 'selected';} ?>>Mayor o igual a 65</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-2">
                            <label for="id_encVenta">ID:</label>
                            <input type='number' name='id_encVenta' id="id_encVenta" class='form-control' value='<?php echo $row['id_encVenta']; ?>' readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-4 mt-3">
                            <label for="grupoEtnico">* GRUPO ETNICO:</label>
                            <select class="form-control" name="grupoEtnico" required >
                                <option value="">SELECCIONE:</option>   
                                <option value="Indigena" <?php if($row['grupoEtnico']=="Indigena"){echo 'selected';} ?>>Indigena</option>
                                <option value="Negra / Afrocolombiana" <?php if($row['grupoEtnico']=="Negra / Afrocolombiana"){echo 'selected';} ?>>Negra / Afrocolombiana</option>
                                <option value="Raizal" <?php if($row['grupoEtnico']=="Raizal"){echo 'selected';} ?>>Raizal</option>
                                <option value="Palenquero" <?php if($row['grupoEtnico']=="Palenquero"){echo 'selected';} ?>>Palenquero</option>
                                <option value="Gitano (rom)" <?php if($row['grupoEtnico']=="Gitano (rom)"){echo 'selected';} ?>>Gitano (rom)</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-4 mt-3">
                            <label for="orientacionSexual">* ORIENTACION SEXUAL:</label>
                            <select class="form-control" name="orientacionSexual" required >
                                <option value="">SELECCIONE:</option>   
                                <option value="Heterosexual" <?php if($row['orientacionSexual']=="Heterosexual"){echo 'selected';} ?>>Heterosexual</option>
                                <option value="Homosexual" <?php if($row['orientacionSexual']=="Homosexual"){echo 'selected';} ?>>Homosexual</option>
                                <option value="Otro" <?php if($row['orientacionSexual']=="Otro"){echo 'selected';} ?>>Otro</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-4 mt-3">
                            <label for="condicionDiscapacidad">* CONDICIÓN DISCAPACIDAD:</label>
                            <select class="form-control" name="condicionDiscapacidad" required >
                                <option value="">SELECCIONE:</option>   
                                <option value="Si" <?php if($row['condicionDiscapacidad']=="Si"){echo 'selected';} ?>>SÍ</option>
                                <option value="No" <?php if($row['condicionDiscapacidad']=="No"){echo 'selected';} ?>>NO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">

                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR INFORMACIÓN FAMILIAR
                </button>
                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
                </button>
            </form>
        </div>
    </body>
</html>