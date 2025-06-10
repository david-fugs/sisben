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
   $id_usu_formulario = $_GET['id_usu'];   // Incluye la conexión a la base de datos
   include("../../conexion.php");
   if(isset($_GET['id_usu'])) {
      $id_usu = $_GET['id_usu'];
      
  
      // Realizar la consulta para eliminar el registro con el ID proporcionado
      $eliminar_query = "DELETE FROM usuarios WHERE id_usu = $id_usu";
  
      if(mysqli_query($mysqli, $eliminar_query)) {
          // Redireccionar a la página principal después de la eliminación
          header("Location: showusers.php");
          exit();
      } else {
          echo "Error al intentar eliminar el registro.";
      }
  } else {
      echo "ID de registro no proporcionado.";
  }
  
?>