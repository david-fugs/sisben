<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
   header("Location: ../../index.php");
   exit();  // Asegúrate de salir del script después de redirigir
}

$id_usu    = $_SESSION['id_usu'];
$usuario    = $_SESSION['usuario'];
$nombre     = $_SESSION['nombre'];
$tipo_usu   = $_SESSION['tipo_usu'];

header("Content-Type: text/html;charset=utf-8");
include("../../conexion.php");

if (isset($_GET['id_informacion'])) {
   $id_informacion = (int) $_GET['id_informacion']; // Convertir a entero por seguridad

   // Realizar la consulta para eliminar el registro con el ID proporcionado
   $eliminar_query = "DELETE FROM informacion WHERE id_informacion = $id_informacion";

   if ($mysqli->query($eliminar_query)) {
       // Redireccionar a la página principal después de la eliminación
       header("Location: showsurvey.php");
       exit();
   } else {
       echo "Error al intentar eliminar el registro.";
   }
} else {
   echo "ID de registro no proporcionado.";
}

