<?php
session_start();
date_default_timezone_set("America/Bogota");
header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BD SISBEN</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/popper.min.j"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <link href="fontawesome/css/all.css" rel="stylesheet"> <!--load all styles -->
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

    <center>
        <img src="img/sisben.png" width=500 height=309 class="responsive">
    </center>

    <?php
    require('conexion.php');
    // Si el formulario se envió, inserta los valores en la base de datos.
    if (isset($_REQUEST['usuario'])) {
        $usuario 			= stripslashes($_REQUEST['usuario']);
        $usuario 			= mysqli_real_escape_string($mysqli, $usuario);
        $password 			= stripslashes($_REQUEST['password']);
        $password 			= mysqli_real_escape_string($mysqli, $password);
        $nombre 			= stripslashes($_REQUEST['nombre']);
        $nombre 			= mysqli_real_escape_string($mysqli, $nombre);
        $tipo_usu 			= 10;
        $doc_enc 			= stripslashes($_REQUEST['doc_enc']);
        $doc_enc 			= mysqli_real_escape_string($mysqli, $doc_enc);
        $dir_enc 			= stripslashes($_REQUEST['dir_enc']);
        $dir_enc 			= mysqli_real_escape_string($mysqli, $dir_enc);
        $tel_enc 			= stripslashes($_REQUEST['tel_enc']);
        $tel_enc 			= mysqli_real_escape_string($mysqli, $tel_enc);
        $email_enc 			= stripslashes($_REQUEST['email_enc']);
        $email_enc 			= mysqli_real_escape_string($mysqli, $email_enc);
        $estado_enc 		= 1;
        $fecha_alta_enc    	= date('Y-m-d h:i:s');
        $fecha_edit_enc    	= '0000-00-00 00:00:00';

        $query_usuarios = "INSERT INTO `usuarios` (usuario, password, tipo_usu, nombre) VALUES ('$usuario', '" . sha1($password) . "', '$tipo_usu', '$nombre')";
        $result_usuarios = mysqli_query($mysqli, $query_usuarios);

        if ($result_usuarios) {
            // Obtiene el ID de usuario generado automáticamente en la última inserción
            $id_usu = mysqli_insert_id($mysqli);

            $query_encuestadores = "INSERT INTO `encuestadores` (id_usu, doc_enc, nom_enc, dir_enc, tel_enc, email_enc, estado_enc, fecha_alta_enc, fecha_edit_enc) VALUES ('$id_usu', '$doc_enc', '$nombre', '$dir_enc', '$tel_enc', '$email_enc', '$estado_enc', '$fecha_alta_enc', '$fecha_edit_enc')";
            $result_encuestadores = mysqli_query($mysqli, $query_encuestadores);

            if ($result_encuestadores) {
                echo "<center><p style='border-radius: 20px;box-shadow: 10px 10px 5px #c68615; font-size: 23px; font-weight: bold;'>REGISTRO CREADO SATISFACTORIAMENTE<br><br></p></center>
                    <div class='form' align='center'><h3>Regresar para iniciar la sesión... <br/><br/><center><a href='index.php'>Regresar</a></center></h3></div>";
            }
        }
    } else {
    ?>

    <div class="container">
        <h1><b><i class="fas fa-users"></i> REGISTRO DE UN NUEVO USUARIO</b></h1>
        <p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
        <form action='' method="POST">

            <hr style="border: 4px solid #24E924; border-radius: 5px;">

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-2">
                        <label for="doc_enc ">* DOCUMENTO No.</label>
                        <input type='text' name='doc_enc' class='form-control' id="doc_enc" required autofocus />
                    </div>
                    <div class="col-12 col-sm-4">
						<label for="nombre">* NOMBRES COMPLETOS:</label>
						<input type='text' name='nombre' class='form-control' id="nombre" required style="text-transform:uppercase;" />
					</div>
					<div class="col-12 col-sm-4">
						<label for="dir_enc">DIRECCIÓN:</label>
						<input type='text' name='dir_enc' class='form-control' id="dir_enc" style="text-transform:uppercase;" />
					</div>
					<div class="col-12 col-sm-2">
						<label for="tel_enc">TELEFONO:</label>
						<input type='number' name='tel_enc' class='form-control' id="tel_enc" />
					</div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label for="email_enc">EMAIL:</label>
                        <input type='email' name='email_enc' id="email_enc" class='form-control' />
                    </div>
                    <div class="col-12 col-sm-3">
						<label for="usuario">* USUARIO:</label>
						<input type='text' name='usuario' id="usuario" class='form-control' required />
						<label for="usuario">(minúsculas, sin espacios, ni caracteres especiales)</label>
					</div>
					<div class="col-12 col-sm-3">
						<label for="password">* PASSWORD:</label>
						<input type='password' name='password' id="password" class='form-control' required />
						<label for="password"> (no tiene restricción)</label>
					</div>
                </div>
            </div>

            <hr style="border: 4px solid #24E924; border-radius: 5px;">
            <button type="submit" class="btn btn-primary">
                <span class="spinner-border spinner-border-sm"></span>
                REGISTRAR USUARIO
            </button>
            <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='img/atras.png' width=27 height=27> REGRESAR
            </button>
        </form>
    </div>

</body>
</html>

<?php } ?>
