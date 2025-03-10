<?php
    
    session_start();
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
    }

    $nombre = $_SESSION['nombre'];
    $tipo_usuario = $_SESSION['tipo_usuario'];
    header("Content-Type: text/html;charset=utf-8");

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>BD SISBEN</title>
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <script type="text/javascript" src="../../js/jquery.min.js"></script>
        <script type="text/javascript" src="../../js/popper.min.j"></script>
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
        <link href="../../fontawesome/css/all.css" rel="stylesheet"> <!--load all styles -->
       	<!-- CSS -->
       	<script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
		<style>
        	.responsive {
           		max-width: 100%;
            	height: auto;
        	}
    	</style>
    	<!--SCRIPT PARA VALIDAR SI EL REGISTRO YA ESTÁ EN LA BD-->
    	<script type="text/javascript">
    		$(document).ready(function()
    		{  
        		$('#doc_enc').on('blur', function()
        		{
            		$('#result-doc_enc').html('<img src="../../img/loader.gif" />').fadeOut(1000);
             		var doc_enc = $(this).val();   
            		var dataString = 'doc_enc='+doc_enc;

            		$.ajax(
            		{
		                type: "POST",
		                url: "chkparent.php",
		                data: dataString,
		                success: function(data)
		                {
		                	$('#result-doc_enc').fadeIn(1000).html(data);
            			}
            		});
        		});
        	});    
  		</script>

   </head>
    <body>
    	
		<center>
	    	<img src='../../img/sisben.png' width=300 height=185 class="responsive">
		</center>
		<br />
<?php

	date_default_timezone_set("America/Bogota");
	include("../../conexion.php");
	require_once("../../zebra.php");

?>

		<div class="container">
			<h1><b><i class='fas fa-user-shield'></i> REGISTRO CONTRATISTAS</b></h1>
			<p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
			<form action='addencuestadores2.php' method="POST">
				<div class="row">
					<div class="col">
						<div id="result-doc_enc"></div>
					</div>  
				</div>

				<div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-3">
							<label for="doc_enc">* DOCUMENTO:</label>
							<input type='number' name='doc_enc' class='form-control' id="doc_enc" required autofocus />
						</div>
						<div class="col-12 col-sm-4">
							<label for="nom_enc">* NOMBRES COMPLETOS:</label>
							<input type='text' name='nom_enc' id="nom_enc" class='form-control' required style="text-transform:uppercase;" />
						</div>
						<div class="col-12 col-sm-5">
	                        <label for="dir_enc">DIRECCIÓN:</label>
	                        <input type='text' name='dir_enc' class='form-control'  style="text-transform:uppercase;" />
	                    </div>
					</div>
				</div>

				<div class="form-group">
	                <div class="row">
	                    <div class="col-12 col-sm-4">
	                        <label for="tel_enc">TELÉFONO CONTACTO:</label>
	                        <input type='text' name='tel_enc' class='form-control' />
	                    </div>
	                    <div class="col-12 col-sm-8">
	                        <label for="email_enc">EMAIL (CORREO ELECTRÓNICO):</label>
	                        <input type='email' name='email_enc' class='form-control' style="text-transform:lowercase;" />
	                    </div>
	                </div>
            	</div>

		        <div class="form-group">
		            <div class="row">
		                <div class="col-12">
		                    <label for="obs_enc">OBSERVACIONES y/o COMENTARIOS ADICIONALES:</label>
		                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" name="obs_enc" style="text-transform:uppercase;" /></textarea>
		                </div>
		            </div>
		        </div>

				<button type="submit" class="btn btn-outline-warning">
					<span class="spinner-border spinner-border-sm"></span>
					INGRESAR REGISTRO
				</button>
				<button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
				</button>
				<button type="reset" class="btn btn-outline-success" role='link' onclick="location='addencuestadores.php';"><img src='../../img/search.png' width=27 height=27> CONSULTAR
				</button>
			</form>
		</div>
	</body>
</html>