<?php
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
        header("Location: ../../index.php");
        exit();  // Asegúrate de salir del script después de redirigir
    }

    $id_usu    = $_SESSION['id_usu'];
    $usuario    = $_SESSION['usuario'];
    $nombre     = $_SESSION['nombre'];
    $tipo_usu   = $_SESSION['tipo_usu'];
    
    header("Content-Type: text/html;charset=utf-8");
   include("../../conexion.php");

   if(isset($_GET['id_encCampo']))
   {
      $id_encCampo = $_GET['id_encCampo'];

      // Realizar la consulta para eliminar el registro con el ID proporcionado
      $eliminar_query = "DELETE FROM encCampo WHERE id_encCampo = ?";
      $eliminar_stmt = $mysqli->prepare($eliminar_query);
      $eliminar_stmt->bind_param("i", $id_encCampo);

      if($eliminar_stmt->execute())
      {
         // Redireccionar a la página principal después de la eliminación
         header("Location: showsurvey.php");
         exit();
      }
      else
      {
         echo "Error al intentar eliminar el registro.";
      }
   }
   else
   {
      echo "ID de registro no proporcionado.";
   }
?>