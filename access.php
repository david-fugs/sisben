<?php
session_start();
if (!isset($_SESSION['id_usu'])) {
  header("Location: index.php");
}

// Configurar zona horaria de Colombia desde el inicio
date_default_timezone_set("America/Bogota");

$usuario      = $_SESSION['usuario'];
$nombre       = $_SESSION['nombre'];
$tipo_usu     = $_SESSION['tipo_usu'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Boxicons CSS -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
  <title>BD SISBEN - Dashboard</title>
  <link rel="stylesheet" href="menu/style.css" />

  <style>
    .main-content {
      margin-left: 260px;
      padding: 20px;
      background: #f8f9fa;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }

    .sidebar.close~.main-content {
      margin-left: 78px;
    }

    .dashboard-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .welcome-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      border-left: 4px solid #667eea;
    }

    .stats-card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: none;
      height: 100%;
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: white;
      margin-bottom: 15px;
    }

    .stats-number {
      font-size: 2rem;
      font-weight: 700;
      color: #2c3e50;
      margin: 10px 0;
    }

    .stats-label {
      color: #6c757d;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .quick-actions {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
    }

    .action-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 10px;
      padding: 15px 20px;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      margin-bottom: 10px;
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
      color: white;
    }

    .action-btn i {
      margin-right: 10px;
      font-size: 18px;
    }

    .section-title {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }

    .section-title i {
      margin-right: 10px;
      color: #667eea;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 15px;
      }

      .sidebar.close~.main-content {
        margin-left: 0;
      }
    }
  </style>
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

  <?php if ($tipo_usu == 1) { ?>
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
              <a href="code/eventan/showsurvey.php" class="nav_link sublink">Ventanilla</a>
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Informacion</a>
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Movimientos</a>

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
              <a href="code/exportares/exportar.php" class="nav_link sublink">Ver Informes</a>
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
              <i class='bx bx-log-in'></i>
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
  <?php if ($tipo_usu == 2) { ?>
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
              <a href="code/exportares/exportar.php" class="nav_link sublink">Informes</a>
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
              <i class='bx bx-log-in'></i>
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

  <?php if ($tipo_usu == 3) { ?>
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
              <a href="code/eventan/movimientosEncuesta.php" class="nav_link sublink">Movimientos</a>
              <a href="code/einfo/addsurvey1.php" class="nav_link sublink">Información</a>
              <a href="code/eventan/showsurvey.php" class="nav_link sublink">Lista Encuesta</a>
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
              <!-- <a href="code/emovim/showsurvey.php" class="nav_link sublink">Movimientos</a> -->
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Información</a>
              <a href="code/eventan/showMovimientos.php" class="nav_link sublink">Movimientos</a>
              <a href="code/eventan/showsurvey.php" class="nav_link sublink">Encuesta Nueva</a>

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
              <a href="code/exportares/exportar.php" class="nav_link sublink">Informes</a>

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
              <i class='bx bx-log-in'></i>
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

  <?php if ($tipo_usu == 4) { ?>
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
              <!-- <a href="code/emovim/addsurvey1.php" class="nav_link sublink">Movimientos</a> -->
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
              <!-- <a href="code/emovim/showsurvey.php" class="nav_link sublink">Movimientos</a> -->
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Información</a>
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
              <a href="code/eventan/showsurvey.php" class="nav_link sublink">Ventanilla</a>
              <a href="code/einfo/showsurvey.php" class="nav_link sublink">Informacion</a>

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
              <a href="code/exportares/exportar.php" class="nav_link sublink">Informes</a>

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
              <i class='bx bx-log-in'></i>
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

  <?php if ($tipo_usu == 5) { ?>
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
              <span class="navlink">Informesss</span>
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
              <i class='bx bx-log-in'></i>
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

  <?php if ($tipo_usu == 6) { ?>
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
              <span class="navlink">Informesxx</span>
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
              <i class='bx bx-log-in'></i>
            </div>
            <div class="bottom collapse_sidebar">
              <span> Collapse</span>
              <i class='bx bx-log-out'></i>
            </div>
          </div>
      </div>
    </nav> <?php } ?>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1 class="mb-2">
            <i class="fas fa-tachometer-alt me-3"></i>
            Bienvenido al Sistema SISBEN
          </h1>
          <p class="mb-0 opacity-75">
            Hola <strong><?php echo $nombre; ?></strong>, estás conectado como:
            <span class="badge bg-light text-dark ms-2">
              <?php
              switch ($tipo_usu) {
                case 1:
                  echo "Administrador";
                  break;
                case 2:
                  echo "Encuestas de Campo";
                  break;
                case 3:
                  echo "Ventanilla";
                  break;
                case 4:
                  echo "Encuestas (Campo y Ventanilla)";
                  break;
                case 5:
                  echo "Supervisor Campo";
                  break;
                case 6:
                  echo "Supervisor Ventanilla";
                  break;
                default:
                  echo "Usuario";
                  break;
              }
              ?>
            </span>
          </p>
        </div>
        <div class="col-md-4 text-end">
          <p class="mb-0">
            <i class="fas fa-calendar-alt me-2"></i>
            <?php echo date('d/m/Y H:i'); ?> (COT)
          </p>
        </div>
      </div>
    </div> <?php
            // Incluir conexión para obtener estadísticas
            include("conexion.php");

            // Obtener estadísticas básicas con manejo de errores
            $total_encuestas = 0;
            $total_movimientos = 0;
            $total_informacion = 0;
            $encuestas_hoy = 0;

            // Total de encuestas (tabla: encventanilla)
            try {
              $result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM encventanilla");
              if ($result && $row = mysqli_fetch_assoc($result)) {
                $total_encuestas = $row['total'];
              }
            } catch (Exception $e) {
              $total_encuestas = 0;
            }

            // Total de información (tabla: informacion)
            try {
              $result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM informacion");
              if ($result && $row = mysqli_fetch_assoc($result)) {
                $total_informacion = $row['total'];
              }
            } catch (Exception $e) {
              $total_informacion = 0;
            }

            // Total de movimientos (tabla: movimientos)
            try {
              $result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM movimientos");
              if ($result && $row = mysqli_fetch_assoc($result)) {
                $total_movimientos = $row['total'];
              }
            } catch (Exception $e) {
              $total_movimientos = 0;
            }

            // Encuestas de hoy - buscar la columna de fecha correcta
            $encuestas_hoy = 0;

            // Primero obtener información sobre las columnas de la tabla
            $columnas_query = "SHOW COLUMNS FROM encventanilla";
            $columnas_result = @mysqli_query($mysqli, $columnas_query);

            if ($columnas_result) {
              $columnas_fecha = [];
              while ($columna = mysqli_fetch_assoc($columnas_result)) {
                $nombre_col = strtolower($columna['Field']);
                // Buscar columnas que contengan 'fecha', 'fec', 'date' o 'time'
                if (
                  strpos($nombre_col, 'fecha') !== false ||
                  strpos($nombre_col, 'fec') !== false ||
                  strpos($nombre_col, 'date') !== false ||
                  strpos($nombre_col, 'time') !== false
                ) {
                  $columnas_fecha[] = $columna['Field'];
                }
              }

              // Probar cada columna de fecha encontrada
              foreach ($columnas_fecha as $columna_fecha) {
                $query_fecha = "SELECT COUNT(*) as total FROM encventanilla WHERE DATE($columna_fecha) = CURDATE()";
                $result_fecha = @mysqli_query($mysqli, $query_fecha);

                if ($result_fecha && $row_fecha = mysqli_fetch_assoc($result_fecha)) {
                  $encuestas_hoy = $row_fecha['total'];
                  break; // Si funciona, salir del bucle
                }
              }
            }
            ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <div class="stats-number"><?php echo number_format($total_encuestas); ?></div>
          <div class="stats-label">Total Encuestas</div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="stats-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-info-circle"></i>
          </div>
          <div class="stats-number"><?php echo number_format($total_informacion); ?></div>
          <div class="stats-label">Registros de Información</div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
          <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="fas fa-exchange-alt"></i>
          </div>
          <div class="stats-number"><?php echo number_format($total_movimientos); ?></div>
          <div class="stats-label">Total Movimientos</div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Quick Actions -->
      <div class="col-lg-4 mb-4">
        <div class="quick-actions">
          <h3 class="section-title">
            <i class="fas fa-bolt"></i>
            Acciones Rápidas
          </h3>

          <?php if ($tipo_usu == 1 || $tipo_usu == 3 || $tipo_usu == 4) { ?>
            <a href="code/eventan/addsurvey1.php" class="action-btn">
              <i class="fas fa-plus-circle"></i>
              Nueva Encuesta
            </a>
          <?php } ?>

          <?php if ($tipo_usu == 1 || $tipo_usu == 3 || $tipo_usu == 4) { ?>
            <a href="code/einfo/addsurvey1.php" class="action-btn">
              <i class="fas fa-info-circle"></i>
              Registrar Información
            </a>
          <?php } ?>

          <?php if ($tipo_usu == 1 || $tipo_usu == 3) { ?>
            <a href="code/eventan/movimientosEncuesta.php" class="action-btn">
              <i class="fas fa-exchange-alt"></i>
              Gestionar Movimientos
            </a>
          <?php } ?>

          <?php if ($tipo_usu == 2) { ?>
            <a href="code/ecampo/addsurvey1.php" class="action-btn">
              <i class="fas fa-clipboard-check"></i>
              Digitación Encuesta Campo
            </a>
          <?php } ?>

          <a href="code/exportares/exportar.php" class="action-btn">
            <i class="fas fa-download"></i>
            Generar Reportes
          </a>
        </div>
      </div> <!-- System Information -->
      <div class="col-lg-8 mb-4">
        <div class="welcome-card">
          <h3 class="section-title">
            <i class="fas fa-info-circle"></i>
            Resumen del Sistema
          </h3>
          <div class="row">
            <div class="col-md-6">
              <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 40px; height: 40px; font-size: 16px;">
                  <i class="fas fa-server"></i>
                </div>
                <div>
                  <strong>Estado del Sistema</strong><br>
                  <span class="text-success">Operativo</span>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); width: 40px; height: 40px; font-size: 16px;">
                  <i class="fas fa-database"></i>
                </div>
                <div>
                  <strong>Base de Datos</strong><br>
                  <span class="text-success">Conectada</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); width: 40px; height: 40px; font-size: 16px;">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                  <strong>Seguridad</strong><br>
                  <span class="text-success">Activa</span>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); width: 40px; height: 40px; font-size: 16px;">
                  <i class="fas fa-clock"></i>
                </div>
                <div>
                  <strong>Última Actualización</strong><br>
                  <span class="text-primary"><?php echo date('H:i'); ?> COT</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- Recent Activity -->
    <div class="welcome-card">
      <h3 class="section-title">
        <i class="fas fa-chart-line"></i>
        Estadísticas Rápidas
      </h3>
      <div class="row text-center">
        <div class="col-md-3">
          <div class="p-3">
            <i class="fas fa-users fa-2x text-primary mb-2"></i>
            <h5><?php echo number_format($total_encuestas + $total_informacion); ?></h5>
            <small class="text-muted">Total Registros</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3">
            <i class="fas fa-chart-pie fa-2x text-success mb-2"></i>
            <h5><?php echo number_format($total_movimientos); ?></h5>
            <small class="text-muted">Movimientos</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3">
            <i class="fas fa-calendar-day fa-2x text-warning mb-2"></i>
            <h5><?php echo number_format($encuestas_hoy); ?></h5>
            <small class="text-muted">Registros Hoy</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="p-3">
            <i class="fas fa-clock fa-2x text-info mb-2"></i>
            <h5><?php echo date('H:i'); ?></h5>
            <small class="text-muted">Hora Colombia (COT)</small>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- JavaScript -->
  <script src="menu/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>