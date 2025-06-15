<?php
session_start();

// Crear una sesión temporal para pruebas
$_SESSION['id_usu'] = 1;
$_SESSION['usuario'] = 'test_user';
$_SESSION['nombre'] = 'Usuario Test';
$_SESSION['tipo_usu'] = 'admin';

echo "Sesión creada. <a href='addsurvey1.php'>Ir al formulario</a>";
?>
