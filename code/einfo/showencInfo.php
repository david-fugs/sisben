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
    <link href="../../fontawesome/css/all.css" rel="stylesheet">
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <!-- Using Select2 from a CDN-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://kit.fontawesome.com/fed2435e21.js"></script>

    <style>
        .responsive {
            max-width: 100%;
            height: auto;
        }
    </style>

    <script>
        // Función para ordenar un select
        function ordenarSelect(id_componente) {
            var selectToSort = $('#' + id_componente);
            var optionActual = selectToSort.val();
            selectToSort.html(selectToSort.children('option').sort(function(a, b) {
                return a.text === b.text ? 0 : a.text < b.text ? -1 : 1;
            })).val(optionActual);
        }

        $(document).ready(function() {
            // Llamadas a la función de ordenar para distintos selects
            ordenarSelect('tipo_solic_encInfo');
            ordenarSelect('obs1_encInfo');
        });
    </script>
</head>

<body>
    <?php
    include("../../conexion.php");
    date_default_timezone_set("America/Bogota");
    $time = time();
    $id_informacion  = $_GET['id_informacion'];
    if (isset($_GET['id_informacion'])) {
        $sql = mysqli_query($mysqli, "SELECT * FROM informacion WHERE id_informacion = '$id_informacion'");
        $row = mysqli_fetch_array($sql);
    }

    ?>

    <div class="container">
        <center>
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </center>

        <h1><b><i class="fa-solid fa-circle-info"></i> VERIFICAR y/o MODIFICAR INFORMACION</b></h1>
        <p><i><b>
                    <font size=3 color=#c68615>* Datos obligatorios</i></b></font>
        </p>

        <form action='editencInfo.php' enctype="multipart/form-data" method="POST">

            <hr style="border: 2px solid #16087B; border-radius: 2px;">
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <input type='number' name='id_informacion' id="id_informacion" class='form-control' value='<?php echo $row['id_informacion']; ?>' readonly hidden />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="fecha_reg_info">* FECHA REGISTRO:</label>
                        <input type='date' name='fecha_reg_info' class='form-control' id="fecha_reg_info" required autofocus value="<?php echo isset($row['fecha_reg_info']) ? $row['fecha_reg_info'] : ''; ?>" />
                    </div>

                    <div class="form-group col-md-3">
                        <label for="doc_info">* DOCUMENTO:</label>
                        <input type='number' name='doc_info' class='form-control' id="doc_info" required value="<?php echo isset($row['doc_info']) ? $row['doc_info'] : ''; ?>" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="tipo_doc">* TIPO DE DOCUMENTO:</label>
                        <select name="tipo_documento" class="form-control" id="tipo_documento">
                            <option value="">SELECCIONE:</option>
                            <option value="cedula" <?php echo isset($row['tipo_documento']) && $row['tipo_documento'] == "cedula" ? 'selected' : ''; ?>>CÉDULA</option>
                            <option value="ppt" <?php echo isset($row['tipo_documento']) && $row['tipo_documento'] == "ppt" ? 'selected' : ''; ?>>PPT</option>
                            <option value="cedula_extranjeria" <?php echo isset($row['tipo_documento']) && $row['tipo_documento'] == "cedula_extranjeria" ? 'selected' : ''; ?>>CÉDULA EXTRANJERÍA</option>
                            <option value="otro" <?php echo isset($row['tipo_documento']) && $row['tipo_documento'] == "otro" ? 'selected' : ''; ?>>OTRO</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="ciudad_expedicion">* CIUDAD EXPEDICIÓN:</label>
                        <input type='text' name='ciudad_expedicion' class='form-control' required style="text-transform:uppercase;" value="<?php echo isset($row['ciudad_expedicion']) ? $row['ciudad_expedicion'] : ''; ?>" />
                    </div>

                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICIÓN:</label>
                        <input type='date' name='fecha_expedicion' class='form-control' required value="<?php echo isset($row['fecha_expedicion']) ? $row['fecha_expedicion'] : ''; ?>" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="nom_info">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_info' class='form-control' required style="text-transform:uppercase;" value="<?php echo isset($row['nom_info']) ? $row['nom_info'] : ''; ?>" />
                    </div>

                    <div class="form-group col-md-3">
                        <label for="tipo_solic_encInfo">* TIPO SOLICITUD:</label>
                        <select class="form-control" name="tipo_solic_encInfo" id="tipo_solic_encInfo" required>
                            <option value=""></option>
                            <option value="INFORMACION" <?php echo isset($row['tipo_solic_encInfo']) && $row['tipo_solic_encInfo'] == "INFORMACION" ? 'selected' : ''; ?>>INFORMACIÓN</option>
                            <option value="ATENCION" <?php echo isset($row['tipo_solic_encInfo']) && $row['tipo_solic_encInfo'] == "ATENCION" ? 'selected' : ''; ?>>ATENCIÓN</option>
                        </select>
                    </div>

                    <div class="form-group col-md-5">
                        <label for="obs1_encInfo">* OBSERVACIÓN:</label>
                        <select class="form-control" name="obs2_encInfo" id="obs2_encInfo" required>
                            <option value=""></option>
                            <option value="ACTUALIZACION" <?php echo isset($row['observacion']) && $row['observacion'] == "ACTUALIZACION" ? 'selected' : ''; ?>>ACTUALIZACIÓN</option>
                            <option value="CLASIFICACION" <?php echo isset($row['observacion']) && $row['observacion'] == "CLASIFICACION" ? 'selected' : ''; ?>>CLASIFICACIÓN</option>
                            <option value="DIRECCION" <?php echo isset($row['observacion']) && $row['observacion'] == "DIRECCION" ? 'selected' : ''; ?>>DIRECCIÓN</option>
                            <option value="DOCUMENTO" <?php echo isset($row['observacion']) && $row['observacion'] == "DOCUMENTO" ? 'selected' : ''; ?>>DOCUMENTO</option>
                            <option value="INCLUSION" <?php echo isset($row['observacion']) && $row['observacion'] == "INCLUSION" ? 'selected' : ''; ?>>INCLUSIÓN</option>
                            <option value="PENDIENTE" <?php echo isset($row['observacion']) && $row['observacion'] == "PENDIENTE" ? 'selected' : ''; ?>>PENDIENTE</option>
                            <option value="VERIFICACION" <?php echo isset($row['observacion']) && $row['observacion'] == "VERIFICACION" ? 'selected' : ''; ?>>VERIFICACIÓN</option>
                            <option value="VISITA" <?php echo isset($row['observacion']) && $row['observacion'] == "VISITA" ? 'selected' : ''; ?>>VISITA</option>
                            <option value="CALIDAD DE LA ENCUESTA" <?php echo isset($row['observacion']) && $row['observacion'] == "CALIDAD DE LA ENCUESTA" ? 'selected' : ''; ?>>CALIDAD DE LA ENCUESTA</option>
                            <option value="ATENCION" <?php echo isset($row['observacion']) && $row['observacion'] == "ATENCION" ? 'selected' : ''; ?>>ATENCIÓN</option>
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="info_adicional">INFORMACIÓN ADICIONAL:</label>
                        <textarea class="form-control" id="info_adicional" rows="2" name="info_adicional" style="text-transform:uppercase;"><?php echo isset($row['info_adicional']) ? $row['info_adicional'] : ''; ?></textarea>
                    </div>
                </div>

                <hr style="border: 2px solid #16087B; border-radius: 2px;">

                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR INFORMACIÓN
                </button>
                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
                </button>
        </form>
    </div>
    <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
</body>

</html>