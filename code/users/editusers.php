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
    $tipo_usu    = $_SESSION['tipo_usu'];
    
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
                ordenarSelect('selectUsuario');
                ordenarSelect('selectIE');
            });
        </script>
    </head>
    <body >
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

       	<div class="container">
            <center>
                <img src='../../img/sisben.png' width=300 height=185 class="responsive">
            </center>

            <h1><b><i class="fa-solid fa-user-pen"></i> ACTUALIZAR PERMISO DE USUARIO</b></h1>
            <p><i><b><font size=3 color=#c68615>* Datos obligatorios</i></b></font></p>
            
            <form action='editusers1.php' method="POST">
                
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-2">
                            <label for="id_usu">ID</label>
                            <input type='number' name='id_usu' class='form-control' id="id_usu" value='<?php echo $row['id_usu']; ?>' readonly />
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="usuario">* USUARIO:</label>
                            <input type='text' name='usuario' id="usuario" class='form-control' value='<?php echo utf8_encode($row['usuario']); ?>' required style="text-transform:lowercase;" />
                            <label>(minúsculas, sin espacios, ni caracteres especiales)</label>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="nombre">* NOMBRE REGISTRADO:</label>
                            <input type='text' name='nombre' id="nombre" class='form-control' value='<?php echo utf8_encode($row['nombre']); ?>' required style="text-transform:uppercase;" />
                            <label>(nombre de la persona que se registra)</label>
                        </div>
                        <div class="col-12 col-sm-3">
                            <label for="tipo_usu">* TIPO DE ACCESO:</label>
                            <select class="form-control" name="tipo_usu" required id="selctUsuario" />
                                <option value="">SELECCIONE:</option>
                                <option value=1 <?php if($row['tipo_usu']==1){echo 'selected';} ?>>ADMINISTRADOR</option>
                                <option value=3 <?php if($row['tipo_usu']==3){echo 'selected';} ?>>ENCUESTADOR</option>
                                <option value=4 <?php if($row['tipo_usu']==4){echo 'selected';} ?>>ENCUESTADOR Y REVISION</option>
                                <option value=10 <?php if($row['tipo_usu']==10){echo 'selected';} ?>>SIN ACCESO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">

                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR PERMISOS DE USUARIO 
                </button>
                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
                </button>
            </form>
        </div>
        </body>
</html>