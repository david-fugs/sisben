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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

		<style>
        	.responsive {
           		max-width: 100%;
            	height: auto;
        	}

        	.selector-for-some-widget {
  				box-sizing: content-box;
			}

			.hover-bg:hover {
				background-color: #f5f5f5;
				cursor: pointer;
			}

			.user-status-admin {
				background-color: #dc3545;
				color: white;
				padding: 2px 8px;
				border-radius: 12px;
				font-size: 0.8em;
				font-weight: bold;
			}

			.user-status-campo {
				background-color: #28a745;
				color: white;
				padding: 2px 8px;
				border-radius: 12px;
				font-size: 0.8em;
				font-weight: bold;
			}

			.user-status-ventanilla {
				background-color: #007bff;
				color: white;
				padding: 2px 8px;
				border-radius: 12px;
				font-size: 0.8em;
				font-weight: bold;
			}

			.user-status-apoyo {
				background-color: #ffc107;
				color: black;
				padding: 2px 8px;
				border-radius: 12px;
				font-size: 0.8em;
				font-weight: bold;
			}

			.user-status-supervision {
				background-color: #6f42c1;
				color: white;
				padding: 2px 8px;
				border-radius: 12px;
				font-size: 0.8em;
				font-weight: bold;
			}

			.user-status-sin-acceso {
				background-color: #6c757d;
				color: white;
				padding: 2px 8px;
				border-radius: 12px;
				font-size: 0.8em;
				font-weight: bold;
			}
    	</style>
    </head>
    <body>    	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>

		<div class="container my-5">
			<div class="row align-items-center">
				<div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
					<img src='../../img/sisben.png' class="img-fluid" alt="Logo Sisben" style="max-width: 300px;">
				</div>				<div class="col-md-8">
					<h1 class="text-primary fw-bold">
						<i class="fas fa-users-cog me-2"></i> GESTIÓN Y ADMINISTRACIÓN DE USUARIOS
					</h1>
				</div>
			</div>
		</div>

		<div class="container my-4">
			<div class="card shadow-sm">
				<div class="card-body">
					<h5 class="card-title mb-3">Buscar usuarios</h5>
					<form action="showusers.php" method="get" class="row g-3 align-items-center">
						<div class="col-md-4">
							<input name="usuario" type="text" class="form-control" placeholder="Documento">
						</div>
						<div class="col-md-5">
							<input name="nombre" type="text" class="form-control" placeholder="Nombre del usuario">
						</div>						<div class="col-md-3">
							<button type="submit" class="btn btn-success w-100">
								<i class="fas fa-search me-1"></i> Buscar
							</button>
						</div>
					</form>
				</div>
			</div>
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
			<div class='container-fluid mt-3'>
				<div class='table-responsive'>
					<table class='table table-bordered table-striped table-hover align-middle text-center'>
						<thead class='table-dark'>
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
		// Determinar el estilo del tipo de usuario
		$tipoUsuarioTexto = '';
		$tipoUsuarioClase = '';
		
		switch($row['tipo_usu']) {
			case 1:
				$tipoUsuarioTexto = 'ADMINISTRADOR';
				$tipoUsuarioClase = 'user-status-admin';
				break;
			case 2:
				$tipoUsuarioTexto = 'ENCUESTA CAMPO';
				$tipoUsuarioClase = 'user-status-campo';
				break;
			case 3:
				$tipoUsuarioTexto = 'ENCUESTA VENTANILLA';
				$tipoUsuarioClase = 'user-status-ventanilla';
				break;
			case 4:
				$tipoUsuarioTexto = 'APOYO';
				$tipoUsuarioClase = 'user-status-apoyo';
				break;
			case 5:
				$tipoUsuarioTexto = 'SUPERVISIÓN CAMPO';
				$tipoUsuarioClase = 'user-status-supervision';
				break;
			case 6:
				$tipoUsuarioTexto = 'SUPERVISIÓN VENTANILLA';
				$tipoUsuarioClase = 'user-status-supervision';
				break;
			case 10:
				$tipoUsuarioTexto = 'SIN ACCESO';
				$tipoUsuarioClase = 'user-status-sin-acceso';
				break;
			default:
				$tipoUsuarioTexto = 'NO DEFINIDO';
				$tipoUsuarioClase = 'user-status-sin-acceso';
		}
		echo '
				<tr class="hover-bg">
					<td>' . $i++ . '</td>
					<td>' . $row['usuario'] . '</td>
					<td>' . utf8_encode($row['nombre']) . '</td>
					<td><span class="' . $tipoUsuarioClase . '">' . $tipoUsuarioTexto . '</span></td>
					<td>
						<a href="editusers.php?id_usu=' . $row['id_usu'] . '" class="btn btn-outline-primary btn-sm" title="Editar usuario">
							<i class="fas fa-edit"></i>
						</a>
					</td>
					<td>
						<a href="reset-password.php?id_usu=' . $row['id_usu'] . '" class="btn btn-outline-warning btn-sm" title="Cambiar contraseña">
							<i class="fas fa-key"></i>
						</a>
					</td>
					<td>
						<a href="delete_user.php?id_usu=' . $row['id_usu'] . '" onclick="return confirm(\'¿ESTÁS SEGURO DE ELIMINAR ESTE USUARIO?\')" class="btn btn-outline-danger btn-sm" title="Eliminar usuario">
							<i class="fas fa-trash"></i>
						</a>
					</td>
				</tr>';
	}
 	echo '</table>
		</div>
	</div>
</section>';

	$paginacion->render();

?>
			<div class="share-container">
	            <!-- Go to www.addthis.com/dashboard to customize your tools -->
	            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ecc1a47193e29e4" async="async"></script>
	            <!-- Go to www.addthis.com/dashboard to customize your tools -->
	            <div class="addthis_sharing_toolbox"></div>
	        </div>			<div class="text-center my-4">
				<a href="../../access.php" class="btn btn-secondary">
					<i class="fas fa-arrow-left me-2"></i> Regresar
				</a>
			</div>
		<script src="js/app.js"></script>
		<script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>

	</body>
</html>