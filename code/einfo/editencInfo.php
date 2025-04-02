<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    header("Location: ../../index.php");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BD SISBEN</title>
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>

    <?php
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");
    $mysqli->set_charset('utf8');
    if (isset($_POST['btn-update'])) {
        $id_informacion             = $_POST['id_informacion'];
        $fecha_reg_info        = $_POST['fecha_reg_info'];
        $doc_info            = $_POST['doc_info'];
        $nom_info            = mb_strtoupper($_POST['nom_info']);
        $tipo_documento          = $_POST['tipo_documento'];
        $ciudad_expedicion       = $_POST['ciudad_expedicion'];
        $fecha_expedicion       = $_POST['fecha_expedicion'];
        $rango_integVenta = isset($_POST['rango_integVenta']) ? $_POST['rango_integVenta'] : '';
        $victima = isset($_POST['victima']) ? $_POST['victima'] : '';
        $condicionDiscapacidad = isset($_POST['condicionDiscapacidad']) ? $_POST['condicionDiscapacidad'] : '';
        $tipoDiscapacidad = isset($_POST['tipoDiscapacidad']) ? $_POST['tipoDiscapacidad'] : '';
        $mujerGestante = isset($_POST['mujerGestante']) ? $_POST['mujerGestante'] : '';
        $cabezaFamilia = isset($_POST['cabezaFamilia']) ? $_POST['cabezaFamilia'] : '';
        $orientacionSexual = isset($_POST['orientacionSexual']) ? $_POST['orientacionSexual'] : '';
        $experienciaMigratoria = isset($_POST['experienciaMigratoria']) ? $_POST['experienciaMigratoria'] : '';
        $grupoEtnico = isset($_POST['grupoEtnico']) ? $_POST['grupoEtnico'] : '';
        $seguridadSalud = isset($_POST['seguridadSalud']) ? $_POST['seguridadSalud'] : '';
        $nivelEducativo = isset($_POST['nivelEducativo']) ? $_POST['nivelEducativo'] : '';
        $condicionOcupacion = isset($_POST['condicionOcupacion']) ? $_POST['condicionOcupacion'] : '';
    
        $tipo_solic_encInfo     = $_POST['tipo_solic_encInfo'];
        $info_adicional           = $_POST['info_adicional'];
        $obs2_encInfo           = $_POST['obs2_encInfo'];
        $estado_encInfo         = 1;
        $fecha_edit_info     = date('Y-m-d');
        $id_usu                 = $_SESSION['id_usu'];

        $update =  "UPDATE informacion SET 
                    fecha_reg_info = '$fecha_reg_info',
                    doc_info = '$doc_info',
                    nom_info = '$nom_info',
                    tipo_documento = '$tipo_documento',
                    ciudad_expedicion = '$ciudad_expedicion',
                    fecha_expedicion = '$fecha_expedicion',
                    rango_integVenta = '$rango_integVenta',
                    victima = '$victima',
                    condicionDiscapacidad = '$condicionDiscapacidad',
                    tipoDiscapacidad = '$tipoDiscapacidad',
                    mujerGestante = '$mujerGestante',
                    cabezaFamilia = '$cabezaFamilia',
                    orientacionSexual = '$orientacionSexual',
                    experienciaMigratoria = '$experienciaMigratoria',
                    grupoEtnico = '$grupoEtnico',
                    seguridadSalud = '$seguridadSalud',
                    nivelEducativo = '$nivelEducativo',
                    condicionOcupacion = '$condicionOcupacion',
                    fecha_edit_info = '$fecha_edit_info',
                    tipo_solic_encInfo = '$tipo_solic_encInfo',
                    info_adicional = '$info_adicional',
                    observacion = '$obs2_encInfo'
                WHERE id_informacion='$id_informacion'";

        
        $up = mysqli_query($mysqli, $update);
        if (!$up) {
            echo "Error en la actualización: " . mysqli_error($mysqli);
        } else {
            echo "Registro actualizado correctamente.";
        }
        echo "
                    <!DOCTYPE html>
                        <html lang='es'>
                            <head>
                                <meta charset='utf-8' />
                                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                                <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet'>
                                <link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet'>
                                <link rel='stylesheet' href='../../css/bootstrap.min.css'>
                                <link href='../../fontawesome/css/all.css' rel='stylesheet'>
                                <title>FICHA</title>
                                <style>
                                    .responsive {
                                        max-width: 100%;
                                        height: auto;
                                    }
                                </style>
                            </head>
                            <body>
                                <center>
                                   <img src='../../img/sisben.png' width=300 height=185 class='responsive'>
                                <div class='container'>
                                    <br />
                                    <h3><b><i class='fas fa-users'></i> SE ACTUALIZÓ DE FORMA EXITOSA EL REGISTRO</b></h3><br />
                                    <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                                </div>
                                </center>
                            </body>
                        </html>
                    ";
    }
    ?>
</body>

</html>