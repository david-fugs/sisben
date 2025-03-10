<?php
    session_start();
    
    if(!isset($_SESSION['id_usu'])){
        header("Location: index.php");
    }
    
    $usuario      = $_SESSION['usuario'];
    $nombre       = $_SESSION['nombre'];
    $tipo_usu     = $_SESSION['tipo_usu'];

?>

<!DOCTYPE html>
<!-- Coding by CodingNepal || www.codingnepalweb.com -->
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <title>BD SISBEN</title>
    <link rel="stylesheet" href="menu/style.css" />
  </head>
  <body>
    <!-- navbar -->
    <nav class="navbar">
      <div class="logo_item">
        <i class="bx bx-menu" id="sidebarOpen"></i>
        <img src="img/avatar_sisben.png" alt=""></i>SISTEMA DE INFORMACIÓN
      </div>

      <!--<div class="search_bar">
        <input type="text" placeholder="Buscar..." />
      </div>-->

      <div class="navbar_content">
        <i class="bi bi-grid"></i>
        <i class="fa-solid fa-sun" id="darkLight"></i><!--<i class='bx bx-sun' id="darkLight"></i>-->
        <a href="logout.php"> <i class="fa-solid fa-door-open"></i></a>
        <img src="img/avatar_alcaldia.jpg" alt="" class="profile" />
      </div>
    </nav>

    <!--************************INICIA MENÚ ADMINISTRADOR************************-->

    <?php if($tipo_usu == 1) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-user-pen"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>

              <span class="navlink">Usuarios</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/users/showusers.php" class="nav_link sublink">Permisos</a>
              <a href="code/users/register.php" class="nav_link sublink">Crear Nuevo</a>
            </ul>
          </li>

          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <span class="navlink">Consultar</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/users/showEncCampo.php" class="nav_link sublink">En Campo</a>
              <a href="code/users/showEncVentanilla.php" class="nav_link sublink">Ventanilla</a>
              <!--<a href="code/users/addsurvey.php" class="nav_link sublink">Nueva</a>
              <a href="code/users//addsurvey.php" class="nav_link sublink">Movimientos</a>
              <a href="code/users/addsurvey.php" class="nav_link sublink">Información</a>
              <a href="code/users/addsurvey.php" class="nav_link sublink">Portal Ciudadano</a>-->
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-chart-pie"></i>
              </span>
              <span class="navlink">Informes</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/report/report1.php" class="nav_link sublink">Campo</a>
              <a href="code/report/report7.php" class="nav_link sublink">Ventanilla</a>
              <a href="code/report/report13.php" class="nav_link sublink">Información</a>
              <a href="code/report/report17.php" class="nav_link sublink">Movimiento</a>
            </ul>
          </li>


          <!-- duplicate this li tag if you want to add or remove  navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </span>
              <span class="navlink">Mi Cuenta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="reset-password.php" class="nav_link sublink">Cambiar Contraseña</a>
            </ul>
          </li>

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
          </div>
        </div>
      </div>
    </nav>
    <?php } ?>


<!--************************MENÚ ENCUESTAS DE CAMPO************************-->
    <?php if($tipo_usu == 2) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-house-user"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>

              <span class="navlink">Salidas Campo</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/ecampo/addsurvey1.php" class="nav_link sublink">Digitación Encuesta</a>
            </ul>
          </li>
          <!-- end -->

          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <span class="navlink">Editar Encuesta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/ecampo/showsurvey.php" class="nav_link sublink">Modificar</a>
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-chart-pie"></i>
              </span>
              <span class="navlink">Descargue</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/ecampo/report/report1.php" class="nav_link sublink">Informes</a>
            </ul>
          </li>
          <!-- duplicate this li tag if you want to add or remove  navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </span>
              <span class="navlink">Mi Cuenta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="reset-password.php" class="nav_link sublink">Cambiar Contraseña</a>
              <!--<a href="#" class="nav_link sublink">Nav Sub Link</a>
              <a href="#" class="nav_link sublink">Nav Sub Link</a>
              <a href="#" class="nav_link sublink">Nav Sub Link</a>-->
            </ul>
          </li>

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
          </div>
        </div>
      </div>
    </nav>
    <?php } ?>

<!--************************MENÚ VENTANILLA - NUEVA - MOVIMIENTOS************************-->

    <?php if($tipo_usu == 3) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-building-circle-check"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>

              <span class="navlink">Ventanilla</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/eventan/addsurvey1.php" class="nav_link sublink">Nueva Encuesta</a>
              <a href="code/emovim/addsurvey1.php" class="nav_link sublink">Movimientos</a>
              <a href="code/einfo/addsurvey1.php" class="nav_link sublink">Información</a>
            </ul>
          </li>
          <!-- end -->

          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <span class="navlink">Editar Encuesta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/eventan/showsurvey.php" class="nav_link sublink">Encue. Nueva</a>
              <a href="code/emovim/showsurvey.php" class="nav_link sublink">Movimientos</a>
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Información</a>
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-chart-pie"></i>
              </span>
              <span class="navlink">Descargue</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/eventan/report/report1.php" class="nav_link sublink">Informes</a>
            </ul>
          </li>
          <!-- duplicate this li tag if you want to add or remove  navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </span>
              <span class="navlink">Mi Cuenta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="reset-password.php" class="nav_link sublink">Cambiar Contraseña</a>
              <!--<a href="#" class="nav_link sublink">Nav Sub Link</a>
              <a href="#" class="nav_link sublink">Nav Sub Link</a>
              <a href="#" class="nav_link sublink">Nav Sub Link</a>-->
            </ul>
          </li>

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
          </div>
        </div>
      </div>
    </nav>
    <?php } ?>

<!--***************MENÚ ENCUESTAS (INCLUYE CAMPO Y VENTANILLA)**************-->

    <?php if($tipo_usu == 4) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-house-user"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>

              <span class="navlink">Salidas Campo</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/ecampo/addsurvey1.php" class="nav_link sublink">Digitación Encuesta</a>
            </ul>
          </li>
          
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-building-circle-check"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>

              <span class="navlink">Ventanilla</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">

              <a href="code/eventan/addsurvey1.php" class="nav_link sublink">Nueva Encuesta</a>
              <a href="code/emovim/addsurvey1.php" class="nav_link sublink">Movimientos</a>
              <a href="code/einfo/addsurvey1.php" class="nav_link sublink">Información</a>
            </ul>
          </li>
          <!-- end -->

          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <span class="navlink">Realizadas</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/ecampo/showsurvey.php" class="nav_link sublink">Campo</a>
              <a href="code/eventan/showsurvey.php" class="nav_link sublink">Nuevas</a>
              <a href="code/emovim/showsurvey.php" class="nav_link sublink">Movimientos</a>
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Información</a>
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-chart-pie"></i>
              </span>
              <span class="navlink">Informes</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/ecampo/report/report1.php" class="nav_link sublink">Campo</a>
              <a href="code/eventan/report/report1.php" class="nav_link sublink">Ventanilla</a>
            </ul>
          </li>
          <!-- duplicate this li tag if you want to add or remove  navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </span>
              <span class="navlink">Mi Cuenta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="reset-password.php" class="nav_link sublink">Cambiar Contraseña</a>
              <!--<a href="#" class="nav_link sublink">Nav Sub Link</a>
              <a href="#" class="nav_link sublink">Nav Sub Link</a>
              <a href="#" class="nav_link sublink">Nav Sub Link</a>-->
            </ul>
          </li>

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
          </div>
        </div>
      </div>
    </nav>
    <?php } ?>

<!--***************MENÚ SUPERVISOR CAMPO**************-->

    <?php if($tipo_usu == 5) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
          
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <span class="navlink">Supervisión</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/supervisor/campo/showEncCampo.php" class="nav_link sublink">Campo</a>
              <!--<a href="code/users/addsurvey.php" class="nav_link sublink">Nueva</a>
              <a href="code/users//addsurvey.php" class="nav_link sublink">Movimientos</a>
              <a href="code/users/addsurvey.php" class="nav_link sublink">Información</a>
              <a href="code/users/addsurvey.php" class="nav_link sublink">Portal Ciudadano</a>-->
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-chart-pie"></i>
              </span>
              <span class="navlink">Informes</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/report/report1.php" class="nav_link sublink">Campo</a>
            </ul>
          </li>


          <!-- duplicate this li tag if you want to add or remove  navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </span>
              <span class="navlink">Mi Cuenta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="reset-password.php" class="nav_link sublink">Cambiar Contraseña</a>
            </ul>
          </li>

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
          </div>
        </div>
      </div>
    </nav>
    <?php } ?>

<!--***************MENÚ SUPERVISOR VENTANILLA**************-->

    <?php if($tipo_usu == 6) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
          
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
              <span class="navlink">Supervisión</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/supervisor/ventan/showEncVentanilla.php" class="nav_link sublink">Ventanilla</a>
              <!--<a href="code/users/addsurvey.php" class="nav_link sublink">Nueva</a>
              <a href="code/users//addsurvey.php" class="nav_link sublink">Movimientos</a>
              <a href="code/users/addsurvey.php" class="nav_link sublink">Información</a>
              <a href="code/users/addsurvey.php" class="nav_link sublink">Portal Ciudadano</a>-->
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-chart-pie"></i>
              </span>
              <span class="navlink">Informes</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/report/report7.php" class="nav_link sublink">Ventanilla</a>
            </ul>
          </li>


          <!-- duplicate this li tag if you want to add or remove  navlink with submenu -->
          <!-- start -->
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </span>
              <span class="navlink">Mi Cuenta</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="reset-password.php" class="nav_link sublink">Cambiar Contraseña</a>
            </ul>
          </li>

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bx-log-in' ></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bx-log-out'></i>
          </div>
        </div>
      </div>
    </nav>
    <?php } ?>


    <!-- JavaScript -->
    <script src="menu/script.js"></script>
  </body>
</html>
