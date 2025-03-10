<?php
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
        header("Location: ../../../index.php");
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
        <link rel="stylesheet" href="../../css/styles.css">
        <link rel="stylesheet" href="../../../css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">
        <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
        <script>
		    function ordenarSelect(id_componente)
		      {
		        var selectToSort = jQuery('#' + id_componente);
		        var optionActual = selectToSort.val();
		        selectToSort.html(selectToSort.children('option').sort(function (a, b) {
		          return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
		        })).val(optionActual);
		      }
		      $(document).ready(function () {
		        ordenarSelect('selectDigitador');
		      });
  		</script>
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
    	
		<center>
            <img src='../../../img/sisben.png' width=300 height=185 class="responsive">
        </center>

		<div class="container">
			<h1><b><i class="fas fa-edit"></i> REPORTE ENCUESTAS VENTANILLA DIGITADAS </b></h1>
			<BR />
			<form method="POST" action="report2.php">
				<div class="form-group">
					<div class="row">
	                	<div class="col-12 col-sm-3">
	                    	<label for="de">FECHA INICIAL</label>
	                    	<i class="fas fa-hand-point-down"></i>
							<input type='date' name='de' class='form-control' required/>
	               		</div>
	               		<div class="col-12 col-sm-3">
	                    	<label for="hasta">FECHA FINAL</label>
	                    	<i class="far fa-hand-point-down"></i>
							<input type='date' name='hasta' class='form-control' required/>
	               		</div>
	               		<div class="col-12 col-sm-3">
						    <input type='hidden' name='id_usu' value='<?php echo $_SESSION['id_usu']; ?>' />
						</div>
	           		</div>
	            </div>
			    
			    <button type="submit" class="btn btn-primary">
					<span class="spinner-border spinner-border-sm"></span> EXPORTAR ENCUESTAS VENTANILLA
				</button>
	  		</form>

	  		<HR style="border: 1px solid #3380ff;">

	  	</div>

	  	<div class="container">
			<h1><b><i class="fas fa-edit"></i> REPORTE MOVIMIENTOS REALIZADOS </b></h1>
			<BR />
			<form method="POST" action="report3.php">
				<div class="form-group">
					<div class="row">
	                	<div class="col-12 col-sm-3">
	                    	<label for="de">FECHA INICIAL</label>
	                    	<i class="fas fa-hand-point-down"></i>
							<input type='date' name='de' class='form-control' required/>
	               		</div>
	               		<div class="col-12 col-sm-3">
	                    	<label for="hasta">FECHA FINAL</label>
	                    	<i class="far fa-hand-point-down"></i>
							<input type='date' name='hasta' class='form-control' required/>
	               		</div>
	               		<div class="col-12 col-sm-3">
						    <input type='hidden' name='id_usu' value='<?php echo $_SESSION['id_usu']; ?>' />
						</div>
	           		</div>
	            </div>
			    
			    <button type="submit" class="btn btn-success">
					<span class="spinner-border spinner-border-sm"></span> EXPORTAR MOVIMIENTOS REALIZADOS
				</button>
	  		</form>

	  		<HR style="border: 1px solid #2BE627;">

	  	</div>

	  	<div class="container">
			<h1><b><i class="fas fa-edit"></i> REPORTE INFORMACIÓN </b></h1>
			<BR />
			<form method="POST" action="report4.php">
				<div class="form-group">
					<div class="row">
	                	<div class="col-12 col-sm-3">
	                    	<label for="de">FECHA INICIAL</label>
	                    	<i class="fas fa-hand-point-down"></i>
							<input type='date' name='de' class='form-control' required/>
	               		</div>
	               		<div class="col-12 col-sm-3">
	                    	<label for="hasta">FECHA FINAL</label>
	                    	<i class="far fa-hand-point-down"></i>
							<input type='date' name='hasta' class='form-control' required/>
	               		</div>
	               		<div class="col-12 col-sm-3">
						    <input type='hidden' name='id_usu' value='<?php echo $_SESSION['id_usu']; ?>' />
						</div>
	           		</div>
	            </div>
			    
			    <button type="submit" class="btn btn-warning">
					<span class="spinner-border spinner-border-sm"></span> EXPORTAR REPORTE INFORMACIÓN
				</button>
	  		</form>

	  		<HR style="border: 1px solid #EAF533;">

	  	</div>
		
			<center>
				<br/><a href="../../../../access.php"><img src='../../../../img/atras.png' width="72" height="72" title="Regresar" /></a>
			</center>

	</body>
</html>