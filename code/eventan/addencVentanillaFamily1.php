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
        $id_encVenta        = $_POST['id_encVenta'];
        $doc_encVenta       = $_POST['doc_encVenta'];
        $nom_encVenta       = $_POST['nom_encVenta'];
        $id_usu             = $_SESSION['id_usu'];
        
        //$id_encCampo = $mysqli->insert_id;
   
        // Verificar si se ha enviado el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            // Obtener los arreglos de integrantes
            $cant_integVenta        = $_POST['cant_integVenta'] ?? array();
            $gen_integVenta         = $_POST['gen_integVenta'] ?? array();
            $rango_integVenta       = $_POST['rango_integVenta'] ?? array();

            // Otras variables
            $id_usu                 = $_SESSION['id_usu'];
            $estado_integVenta      = 1;
            $fecha_alta_integVenta  = date('Y-m-d h:i:s');
            $fecha_edit_integVenta  = '0000-00-00 00:00:00';

            // Mapeo de descripción del rango de integrantes a valor numérico
            $rango_edad_map = array(
                "0 - 6" => 1,
                "7 - 12" => 2,
                "13 - 17" => 3,
                "18 - 28" => 4,
                "29 - 45" => 5,
                "46 - 64" => 6,
                "Mayor o igual a 65" => 7
            );

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

            foreach ($gen_integVenta as $key => $genero) 
            {
                // Verificar que los valores estén definidos y no sean null
                if (isset($cant_integVenta[$key]) && isset($rango_integVenta[$key])) 
                {
                    // Obtener los valores individuales para el integrante actual
                    $cantidad           = $cant_integVenta[$key];
                    $rango_descripcion  = $rango_integVenta[$key];

          
                    // Obtener el valor numérico del rango a partir del mapeo
                    $rango_valor = isset($rango_edad_map[$rango_descripcion]) ? $rango_edad_map[$rango_descripcion] : 'Valor_predeterminado';

                    // Crear la consulta de inserción para el integrante actual
                    $sql = "INSERT INTO integVentanilla (cant_integVenta, gen_integVenta, rango_integVenta, estado_integVenta, fecha_alta_integVenta, fecha_edit_integVenta, id_usu, id_encVenta)
                    VALUES ('$cantidad', '$genero', '$rango_valor', '$estado_integVenta', '$fecha_alta_integVenta', '$fecha_edit_integVenta', '$id_usu', '$id_encVenta')";

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
