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
    	    $id_integMovim  = $_GET['id_integMovim'];
    	    if(isset($_GET['id_integMovim']))
    	    { 
                $sql = mysqli_query($mysqli, "SELECT * FROM integMovimientos WHERE id_integMovim = '$id_integMovim'");
    	        $row = mysqli_fetch_array($sql);
            }

        ?>

       	<div class="container">
            <center>
                <img src='../../img/sisben.png' width=300 height=185 class="responsive">
            </center>

            <h1><b><i class="fas fa-users"></i> VERIFICAR Y ACTUALIZAR INFORMACIÓN DE LOS INTEGRANTES DEL GRUPO FAMILIAR</b></h1>
            <p><i><b><font size=3 color=#c68615>* Datos obligatorios</i></b></font></p>
            
            <form action='editintegMovimientos.php' enctype="multipart/form-data" method="POST">
                
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <input type='number' name='id_integMovim' id="id_integMovim" class='form-control' value='<?php echo $row['id_integMovim']; ?>' readonly hidden/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-2">
                            <label for="cant_integMovim">* CANTIDAD:</label>
                            <input type='number' name='cant_integMovim' class='form-control'  value='<?php echo $row['cant_integMovim']; ?>' required/>
                        </div>
                        <div class="col-12 col-sm-2">
                            <label for="gen_integMovim">* GENERO</label>
                            <select class="form-control" name="gen_integMovim" required >
                                <option value=""></option>   
                                <option value="M" <?php if($row['gen_integMovim']=='M'){echo 'selected';} ?>>M</option>
                                <option value="F" <?php if($row['gen_integMovim']=='F'){echo 'selected';} ?>>F</option>
                                <option value="O" <?php if($row['gen_integMovim']=='O'){echo 'selected';} ?>>OTRO</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="rango_integMovim">* EDAD:</label>
                            <select class="form-control" name="rango_integMovim" required >
                                <option value="">SELECCIONE:</option>   
                                <option value=1 <?php if($row['rango_integMovim']==1){echo 'selected';} ?>>0 - 6</option>
                                <option value=2 <?php if($row['rango_integMovim']==2){echo 'selected';} ?>>7 - 12</option>
                                <option value=3 <?php if($row['rango_integMovim']==3){echo 'selected';} ?>>13 - 17</option>
                                <option value=4 <?php if($row['rango_integMovim']==4){echo 'selected';} ?>>18 - 28</option>
                                <option value=5 <?php if($row['rango_integMovim']==5){echo 'selected';} ?>>29 - 45</option>
                                <option value=6 <?php if($row['rango_integMovim']==6){echo 'selected';} ?>>46 - 64</option>
                                <option value=7 <?php if($row['rango_integMovim']==7){echo 'selected';} ?>>Mayor o igual a 65</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-2">
                            <label for="id_encMovim">ID:</label>
                            <input type='number' name='id_encMovim' id="id_encMovim" class='form-control' value='<?php echo $row['id_encMovim']; ?>' readonly/>
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