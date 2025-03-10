<?php
// Verifica si el ID del usuario está presente en la URL
if (!isset($_GET['id_usu']) || empty($_GET['id_usu'])) {
    // Redirige a alguna página de manejo de errores o a la página principal
    header("Location: ../../index.php");
    exit();
}

// Recupera el ID del usuario de la URL
$id_usu = $_GET['id_usu'];

session_start();
if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
    exit();  // Asegúrate de salir del script después de redirigir
}
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
    <script type="text/javascript" src="../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<center>
    <img src='../../img/alcaldia.png' width="231" height="185" class="responsive">
    <img src="../../img/sisben.png" width="300" height="185" class="responsive">
</center>
<br />
<div class="container">
    <h1><b><i class="fas fa-key"></i> ACTUALIZACIÓN DE LA CONTRASEÑA</b></h1>
    <p><i><b><font size=3 color=#c68615>*Datos obligatorios</i></b></font></p>
    <form action='reset-password1.php?id_usu=<?php echo $id_usu; ?>' method="POST" onsubmit="return validarContraseñas();">

        <div class="form-group">
            <div class="row">
                <div class="col-12 col-sm-4">
                    <label for="nuevopassword">* NUEVA CONTRASEÑA:</label>
                    <input type='password' name='nuevopassword' class='form-control' id="nuevopassword" required autofocus />
                </div>
                <div class="col-12 col-sm-4">
                    <label for="confirmapassword">* CONFIRMAR CONTRASEÑA:</label>
                    <input type='password' name='confirmapassword' id="confirmapassword" class='form-control' required />
                </div>
                <div class="col-2">
                    <label for="id_usu">ID</label>
                    <?php
                        // Asegúrate de que $id_usu esté definida y tenga un valor antes de incluir el campo oculto
                        if (isset($id_usu)) {
                            echo "<input type='number' name='id_usu' value='$id_usu' class='form-control' readonly/>";
                        } else {
                            echo "<span class='text-danger'>Error: ID de usuario no definido.</span>";
                        }
                    ?>
                </div>
            </div>
            <div id="mensaje-error" style="color: red;"></div>
        </div>

        <button type="submit" class="btn btn-outline-warning">
            <span class="spinner-border spinner-border-sm"></span>
            ACTUALIZAR CONTRASEÑA
        </button>
        <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
        </button>
    </form>

    <script>
        function validarContraseñas() {
            var nuevaContraseña = document.getElementById("nuevopassword").value;
            var confirmarContraseña = document.getElementById("confirmapassword").value;

            if (nuevaContraseña !== confirmarContraseña) {
                document.getElementById("mensaje-error").innerHTML = "Las contraseñas no coinciden.";
                return false;
            } else {
                document.getElementById("mensaje-error").innerHTML = "";
                return true;
            }
        }
    </script>
</div>

</body>
</html>