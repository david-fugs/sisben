<?php
session_start();

// Verifica si hay una sesión activa
if (!isset($_SESSION['id_usu']) || !isset($_SESSION['tipo_usu'])) {
    header("Location: ../../index.php");
    exit();
}

// Obtén el tipo de usuario de la sesión
$tipo_usu_sesion = $_SESSION['tipo_usu'];

// Recupera el id_usu del formulario
$id_usu_formulario = $_GET['id_usu'];

// Incluye la conexión a la base de datos
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nueva_password = mysqli_real_escape_string($mysqli, $_POST['nuevopassword']);
    $confirmar_password = mysqli_real_escape_string($mysqli, $_POST['confirmapassword']);

    // Verifica que las contraseñas coincidan
    if ($nueva_password === $confirmar_password) {
        // Utiliza SHA1 para cifrar la nueva contraseña
        $password_encrypt = sha1($nueva_password);

        // Actualiza la contraseña en la base de datos
        $sql = "UPDATE usuarios SET password='$password_encrypt' WHERE id_usu='$id_usu_formulario'";
        $result = $mysqli->query($sql);

        // Después de ejecutar la consulta SQL
        if ($result) {
            // Establece una variable de sesión con un mensaje de éxito
            $_SESSION['mensaje_exito'] = "Contraseña actualizada correctamente.";

            // Redirige a showusers.php
            header("Location: showusers.php");
            exit();
        } else {
            echo "Error al actualizar la contraseña: " . $mysqli->error;
        }
    } else {
        echo "<script>
                alert('Las contraseñas no coinciden, por favor verifique.');
                window.location = 'reset-password.php?id_usu=$id_usu_formulario';
              </script>";
    }
}
?>