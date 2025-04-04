<?php
     
    session_start();
    
    if(!isset($_SESSION['id_usu']))
    {
        header("Location: ../../index.php");
        exit();  // Asegúrate de salir del script después de redirigir
    }

    $usuario    = $_SESSION['usuario'];
    $nombre     = $_SESSION['nombre'];
    $tipo_usu   = $_SESSION['tipo_usu'];
    
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");
    header("Content-Type: text/html;charset=utf-8");

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // Captura de datos enviados por POST
        $fec_pre_encCampo       = $_POST['fec_pre_encCampo'];
        $fec_rea_encCampo       = $_POST['fec_rea_encCampo'];
        $doc_encCampo           = $_POST['doc_encCampo'];
        $nom_encCampo           = mb_strtoupper($_POST['nom_encCampo']);
        $dir_encCampo           = mb_strtoupper($_POST['dir_encCampo']);
        $zona_encCampo          = $_POST['zona_encCampo'];
        $id_com                 = $_POST['id_com'];
        $id_bar                 = $_POST['id_bar'];
        $id_correg              = $_POST['id_correg'];
        $id_vere                = $_POST['id_vere'];
        $otro_bar_ver_encCampo  = mb_strtoupper($_POST['otro_bar_ver_encCampo']);
        $est_fic_encCampo       = $_POST['est_fic_encCampo'];
        $integra_encCampo       = $_POST['integra_encCampo'];
        $num_ficha_encCampo     = $_POST['num_ficha_encCampo'];
        $proc_encCampo          = $_POST['proc_encCampo'];
        $obs_encCampo           = mb_strtoupper($_POST['obs_encCampo']);
        $estado_encCampo        = 1;
        $fecha_alta_encCampo    = date('Y-m-d h:i:s');
        $fecha_edit_encCampo    = '0000-00-00 00:00:00';
        $id_usu                 = $_SESSION['id_usu'];

        $sql = "INSERT INTO encCampo (fec_pre_encCampo, fec_rea_encCampo, doc_encCampo, nom_encCampo,  dir_encCampo, zona_encCampo, id_com, id_bar, id_correg, id_vere, otro_bar_ver_encCampo, est_fic_encCampo, integra_encCampo, num_ficha_encCampo, proc_encCampo, obs_encCampo, estado_encCampo, fecha_alta_encCampo, fecha_edit_encCampo, id_usu) 
        VALUES ('$fec_pre_encCampo', '$fec_rea_encCampo', '$doc_encCampo', '$nom_encCampo', '$dir_encCampo', '$zona_encCampo', '$id_com', '$id_bar', '$id_correg', '$id_vere', '$otro_bar_ver_encCampo', '$est_fic_encCampo', '$integra_encCampo', '$num_ficha_encCampo', '$proc_encCampo', '$obs_encCampo', '$estado_encCampo', '$fecha_alta_encCampo', '$fecha_edit_encCampo', '$id_usu')";
        
        $resultado = $mysqli->query($sql);

        $id_encCampo = $mysqli->insert_id;
        
        // Verificar si se ha enviado el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            // Obtener los arreglos de integrantes
            $cant_integCampo        = $_POST['cant_integCampo'] ?? array();
            $gen_integCampo         = $_POST['gen_integCampo'] ?? array();
            $rango_integCampo       = $_POST['rango_integCampo'] ?? array();

            // Otras variables
            $id_usu                 = $_SESSION['id_usu'];
            $estado_integCampo      = 1;
            $fecha_alta_integCampo  = date('Y-m-d h:i:s');
            $fecha_edit_integCampo  = '0000-00-00 00:00:00';

            // Mapeo de descripción del rango de integrantes a valor numérico
            $rango_edad_map = array(
                "0 - 6"                 => 1,
                "7 - 12"                => 2,
                "13 - 17"               => 3,
                "18 - 28"               => 4,
                "29 - 45"               => 5,
                "46 - 64"               => 6,
                "Mayor o igual a 65"    => 7
            );

            foreach ($gen_integCampo as $key => $genero) 
            {
                // Verificar que los valores estén definidos y no sean null
                if (isset($cant_integCampo[$key]) && isset($rango_integCampo[$key])) 
                {
                    // Obtener los valores individuales para el integrante actual
                    $cantidad           = $cant_integCampo[$key];
                    $rango_descripcion  = $rango_integCampo[$key];

          
                    // Obtener el valor numérico del rango a partir del mapeo
                    $rango_valor = isset($rango_edad_map[$rango_descripcion]) ? $rango_edad_map[$rango_descripcion] : 'Valor_predeterminado';

                    // Crear la consulta de inserción para el integrante actual
                    $sql = "INSERT INTO integCampo (cant_integCampo, gen_integCampo, rango_integCampo, estado_integCampo, fecha_alta_integCampo, fecha_edit_integCampo, id_usu, id_encCampo)
                    VALUES ('$cantidad', '$genero', '$rango_valor', '$estado_integCampo', '$fecha_alta_integCampo', '$fecha_edit_integCampo', '$id_usu', '$id_encCampo')";

                    // Ejecutar la consulta
                    if ($mysqli->query($sql) === TRUE) 
                    {
                        // Éxito al insertar el integrante
                        //echo "El integrante $key se insertó correctamente.<br>";
                    } else 
                    {
                        echo "Error al insertar el integrante $key: " . $mysqli->error . "<br>";
                    }
                }
            }
        }
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
            <title>BD SISBEN</title>
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
                    <h3><b><i class='fas fa-check-circle'></i> SE GUARDÓ DE FORMA EXITOSA EL REGISTRO</b></h3><br />
                    <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                </div>
            </center>
        </body>
    </html>
";
?>