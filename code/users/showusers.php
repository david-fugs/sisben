<?php
    
    session_start();

    // Verifica si hay un mensaje de éxito en la sesión
	if (isset($_SESSION['mensaje_exito'])) {
	    // Muestra el mensaje de éxito y luego elimínalo de la sesión
	    echo "<script>alert('{$_SESSION['mensaje_exito']}');</script>";
	    unset($_SESSION['mensaje_exito']);
	}
    
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
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>BD SISBEN</title>
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">

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

			<div style="border-radius: 9px 9px 9px 9px; -moz-border-radius: 9px 9px 9px 9px; -webkit-border-radius: 9px 9px 9px 9px; border: 4px solid #FFFFFF;" align="center">

				<div align="center">
					<h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em"><b><i class="fa-solid fa-address-card"></i> GESTIÓN Y ADMINISTRACIÓN DE USUARIOS</b></h1>
				</div>

    			<div style="border-radius: 9px 9px 9px 9px; -moz-border-radius: 9px 9px 9px 9px; -webkit-border-radius: 9px 9px 9px 9px; border: 1px solid #efd47d; width: 500px; height: 30px; background:#FAFAFA; display:table-cell; vertical-align:middle;">

					<label for="buscar">Datos de búsqueda</label>

	    			<form action="showusers.php" method="get">
	    				<input name="usuario" type="text"  placeholder="Documento" size=20>
	    				<input name="nombre" type="text"  placeholder="Nombre del usuario" size=30>
						<input value="Buscar" type="submit">
					</form>
					
	    		</div>

<?php

	date_default_timezone_set("America/Bogota");
	include("../../conexion.php");
	require_once("../../zebra.php");

	@$usuario = ($_GET['usuario']);
	@$nombre = ($_GET['nombre']);

	$query = "SELECT * FROM `usuarios` WHERE (usuario LIKE '%".$usuario."%') AND (nombre LIKE '%".$nombre."%') AND id_usu != $id_usu ORDER BY usuarios.id_usu ASC";
	$res = $mysqli->query($query);
	$num_registros = mysqli_num_rows($res);
	$resul_x_pagina = 30;

	echo "<section class='content'>
			<div class='card-body'>
        		<div class='table-responsive'>
		        	<table>
		            	<thead>
		                	<tr>
								<th>No.</th>
								<th>USUARIO</th>
								<th>NOMBRE</th>
								<th>TIPO USUARIO</th>
				        		<th>EDITAR</th>
				        		<th>CAMBIAR CLAVE</th>
    							<th>ELIMINAR</th>
				    		</tr>
				  		</thead>
            			<tbody>";

	$paginacion = new Zebra_Pagination();
	$paginacion->records($num_registros);
	$paginacion->records_per_page($resul_x_pagina);

	$consulta = "SELECT * FROM `usuarios` WHERE (usuario LIKE '%".$usuario."%') AND (nombre LIKE '%".$nombre."%') AND id_usu != $id_usu ORDER BY usuarios.id_usu ASC LIMIT " .(($paginacion->get_page() - 1) * $resul_x_pagina). "," .$resul_x_pagina;
	$result = $mysqli->query($consulta);

	$i = 1;
	while($row = mysqli_fetch_array($result))
	{

		echo '
				<tr>
					<td data-label="No.">'.$i++.'</td>
					<td data-label="USUARIO">'.$row['usuario'].'</td>
					<td data-label="NOMBRE">'.utf8_encode($row['nombre']).'</td>
					<td data-label="TIPO USUARIO">
						<select class="form-control" name="tipo_usu" disabled >
                        	<option value="">SELECCIONE:</option>   
                            <option value=1 '; if($row['tipo_usu']==1){echo 'selected';} echo '>ADMINISTRADOR</option>
                            <option value=2 '; if($row['tipo_usu']==2){echo 'selected';} echo '>ENCUESTA CAMPO</option>
                            <option value=3 '; if($row['tipo_usu']==3){echo 'selected';} echo '>ENCUESTA VENTANILLA</option>
                            <option value=4 '; if($row['tipo_usu']==4){echo 'selected';} echo '>APOYO</option>
                            <option value=5 '; if($row['tipo_usu']==5){echo 'selected';} echo '>SUPERVISIÓN CAMPO</option>
                            <option value=6 '; if($row['tipo_usu']==6){echo 'selected';} echo '>SUPERVISIÓN VENTANILLA</option>
                            <option value=10 '; if($row['tipo_usu']==10){echo 'selected';} echo '>SIN ACCESO</option>
                        </select>
					</td>
					<td data-label="EDITAR"><a href="editusers.php?id_usu='.$row['id_usu'].'"><img src="../../img/editar.png" width=28 height=28></a></td>
					<td data-label="CAMBIAR CLAVE"><a href="reset-password.php?id_usu='.$row['id_usu'].'"><img src="../../img/change_password.png" width=28 height=28></a></td>
					<td data-label="ELIMINAR"><a href="delete_user.php?id_usu='.$row['id_usu'].'" onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE USUARIO?\')"><img src="../../img/delete_user.png" width=28 height=28></a></td>
				</tr>';
	}
 
	echo '</table>
		</div>

		';

	$paginacion->render();

?>
			<div class="share-container">
	            <!-- Go to www.addthis.com/dashboard to customize your tools -->
	            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
	            <!-- Go to www.addthis.com/dashboard to customize your tools -->
	            <div class="addthis_sharing_toolbox"></div>
	        </div>
			<center>
			<br/><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
			</center>

			</div>
		</div>
		</section>
		<script src="js/app.js"></script>
		<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>

	</body>
</html>