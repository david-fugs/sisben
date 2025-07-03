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
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>BD SISBEN</title>
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">
        <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
        <style>
            .responsive {
                max-width: 100%;
                height: auto;
            }

            .selector-for-some-widget {
                box-sizing: content-box;
            }
        </style>
    </head>
    <body>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>

        <center>
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </center>

        <section class="principal">
            <div align="center">
                <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em"><b><i class="fa-solid fa-house-chimney-window"></i> INTEGRANTES</b></h1>
            </div>

   	<?php
        include("../../conexion.php");
        date_default_timezone_set("America/Bogota");
        $time = time();
	    $id_encMovim  = $_GET['id_encMovim'];
	    if(isset($_GET['id_encMovim']))
	    { 
            $query = "SELECT * FROM integMovimientos INNER JOIN encMovimientos ON integMovimientos.id_encMovim=encMovimientos.id_encMovim WHERE estado_integMovim=1 AND integMovimientos.id_encMovim = '$id_encMovim'";
            $res = $mysqli->query($query);
            $num_registros = mysqli_num_rows($res);

            echo "<section class='content'>
            <div class='card-body'>
                <div class='table-responsive'>
                    <table class='table table-bordered table-striped'>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>CANTIDAD</th>
                                <th>GÉNERO</th>
                                <th>RANGO EDAD</th>
                                <th>GRUPO ÉTNICO</th>
                                <th>ORIENTACION SEXUAL</th>
                                <th>CONDICIÓN DISCAPACIDAD</th>
                                <th>EDIT</th>
                                <th>ELIMINAR</th>
                            </tr>
                        </thead>
                        <tbody>";

            $consulta = "SELECT * FROM integMovimientos INNER JOIN encMovimientos ON integMovimientos.id_encMovim=encMovimientos.id_encMovim WHERE estado_integMovim=1 AND integMovimientos.id_encMovim = '$id_encMovim'";
            $result = $mysqli->query($consulta);
            $i = 1;
            while($row = mysqli_fetch_array($result))
            {
                echo '
                        <tr>
                            <td data-label="No.">'.$i++.'</td>
                            <td data-label="CANTIDAD">'.$row['cant_integMovim'].'</td>
                            <td data-label="GÉNERO">
                                <select class="form-control" name="gen_integMovim" disabled >
                                    <option value="">SELECCIONE:</option>   
                                    <option value="M" '; if($row['gen_integMovim']=='M'){echo 'selected';} echo '>MASCULINO</option>
                                    <option value="F" '; if($row['gen_integMovim']=='F'){echo 'selected';} echo '>FEMENINO</option>
                                    <option value="O" '; if($row['gen_integMovim']=='O'){echo 'selected';} echo '>OTRO</option>
                                </select>
                            </td>
                            <td data-label="RANGO EDAD">
                                <select class="form-control" name="rango_integMovim" disabled >
                                    <option value="">SELECCIONE:</option>   
                                    <option value=1 '; if($row['rango_integMovim']==1){echo 'selected';} echo '>0 - 5</option>
                                    <option value=2 '; if($row['rango_integMovim']==2){echo 'selected';} echo '>6 - 12</option>
                                    <option value=3 '; if($row['rango_integMovim']==3){echo 'selected';} echo '>13 - 17</option>
                                    <option value=4 '; if($row['rango_integMovim']==4){echo 'selected';} echo '>18 - 28</option>
                                    <option value=5 '; if($row['rango_integMovim']==5){echo 'selected';} echo '>29 - 45</option>
                                    <option value=6 '; if($row['rango_integMovim']==6){echo 'selected';} echo '>46 - 64</option>
                                    <option value=7 '; if($row['rango_integMovim']==7){echo 'selected';} echo '>Mayor o igual a 65</option>
                                </select>
                            </td>
                                                           <td data-label="GRUPO ETNICO">
                                <select class="form-control" name="grupoEtnico" disabled >
                                    <option value="">SELECCIONE:</option>   
                                    <option value="Indigena" '; if($row['grupoEtnico']=="Indigena"){echo 'selected';} echo '>Indigena</option>
                                    <option value="Negra / Afrocolombiana" '; if($row['grupoEtnico']=="Negra / Afrocolombiana"){echo 'selected';} echo '>Negra / Afrocolombiana</option>
                                    <option value="Raizal" '; if($row['grupoEtnico']=="Raizal"){echo 'selected';} echo '>Raizal</option>
                                    <option value="Palenquero" '; if($row['grupoEtnico']=="Palenquero"){echo 'selected';} echo '>Palenquero</option>
                                    <option value="Gitano (rom)" '; if($row['grupoEtnico']=="Gitano (rom)"){echo 'selected';} echo '>Gitano (rom)</option>
                                </select>
                            </td>
                            <td data-label="ORIENTACION SEXUAL">
                                    <select class="form-control" name="orientacionSexual" disabled>
                                        <option value="">SELECCIONE:</option>   
                                        <option value="Heterosexual" ' . ($row['orientacionSexual'] == "Heterosexual" ? 'selected' : '') . '>Heterosexual</option>
                                        <option value="Homosexual" ' . ($row['orientacionSexual'] == "Homosexual" ? 'selected' : '') . '>Homosexual</option>
                                        <option value="Otro" ' . ($row['orientacionSexual'] == "Otro" ? 'selected' : '') . '>Otro</option>
                                    </select>
                                </td>
                        <td data-label="CONDICIÓN DISCAPACIDAD">
                                <select class="form-control" name="condicionDiscapacidad" disabled>
                                    <option value="">SELECCIONE:</option>   
                                    <option value="Si" ' . ($row['condicionDiscapacidad'] == "Si" ? 'selected' : '') . '>SÍ</option>
                                    <option value="No" ' . ($row['condicionDiscapacidad'] == "No" ? 'selected' : '') . '>NO</option>
                                </select>
                            </td>
                            <td data-label="EDIT"><a href="showencMovimientosFamily1.php?id_integMovim='.$row['id_integMovim'].'" ><img src="../../img/editar.png" width=28 height=28></a></td>
                            <td data-label="ELIMINAR"><a href="deleteencMovimientos.php?id_integMovim='.$row['id_integMovim'].'" ><img src="../../img/eliminar.png" width=28 height=28></a></td>
                        </tr>';
            }
 
                echo '</table>
                        </div>
                ';
        }
    ?>
            <center>
            <br/><a href="showsurvey.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
            </center>
        </<section>
    </body>
</html>