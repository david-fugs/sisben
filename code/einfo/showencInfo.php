<?php
session_start();

// Activar reporte de errores para debugging (comentar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    //traer todos los departamentos
    $sql = "SELECT * FROM departamentos ORDER BY nombre_departamento ASC";
    $resultado = mysqli_query($mysqli, $sql);
    $departamentos = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $departamentos[] = $row;
    }
    $time = time();
    $id_informacion  = $_GET['id_informacion'];
    $row = array(); // Inicializar array vacío por defecto
    
    if (isset($_GET['id_informacion'])) {
        $sql = mysqli_query($mysqli, "SELECT * FROM informacion WHERE id_informacion = '$id_informacion'");
        if ($sql && mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_array($sql);
        } else {
            // Si no se encuentra el registro, mostrar mensaje de error
            echo "<div class='alert alert-warning'>No se encontró información para el ID proporcionado.</div>";
        }
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
                        <label for="departamento_expedicion">* DEPARTAMENTO EXPEDICION:</label>
                        <select class="form-control" name="departamento_expedicion" id="departamento_expedicion">
                            <option value="">Seleccione un departamento</option>
                            <?php
                            foreach ($departamentos as $departamento) {
                                $selected = ($departamento['cod_departamento'] == $row['departamento_expedicion']) ? "selected" : "";
                                echo "<option value='{$departamento['cod_departamento']}' $selected>{$departamento['nombre_departamento']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="ciudad_expedicion">* MUNICIPIO EXPEDICION:</label>
                        <select id="ciudad_expedicion" name="ciudad_expedicion" class="form-control">
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="fecha_expedicion">* FECHA EXPEDICIÓN:</label>
                        <input type='date' name='fecha_expedicion' class='form-control' value="<?php echo isset($row['fecha_expedicion']) ? $row['fecha_expedicion'] : ''; ?>" />
                    </div>

                    <div class="form-group col-md-3">
                        <label for="nom_info">* NOMBRES COMPLETOS:</label>
                        <input type='text' name='nom_info' class='form-control' style="text-transform:uppercase;" value="<?php echo isset($row['nom_info']) ? $row['nom_info'] : ''; ?>" />
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_nacimiento">FECHA DE NACIMIENTO:</label>
                        <input type='date' name='fecha_nacimiento' id='fecha_nacimiento' class='form-control' value="<?php echo isset($row['fecha_nacimiento']) ? $row['fecha_nacimiento'] : ''; ?>" />
                    </div>
                </div>



                <div class="form-group" id="tipoDiscapacidadContainer">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="condicionDiscapacidad">* CONDICION DISCAPACIDAD:</label>
                            <select name="condicionDiscapacidad" class="form-control" id="condicionDiscapacidad">
                                <option value=""></option>
                                <option value="Si" <?php echo (isset($row['condicionDiscapacidad']) && $row['condicionDiscapacidad'] == 'Si') ? 'selected' : ''; ?>>Si</option>
                                <option value="No" <?php echo (isset($row['condicionDiscapacidad']) && $row['condicionDiscapacidad'] == 'No') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="tipoDiscapacidad">* TIPO DISCAPACIDAD:</label>
                            <select class="form-control" name="tipoDiscapacidad" id="tipoDiscapacidad">
                                <option value=""></option>
                                <option value="Auditiva" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'Auditiva') ? 'selected' : ''; ?>>Auditiva</option>
                                <option value="Fisica" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'Fisica') ? 'selected' : ''; ?>>Fisica</option>
                                <option value="Intelectual" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'Intelectual') ? 'selected' : ''; ?>>Intelectual</option>
                                <option value="Multiple" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'Multiple') ? 'selected' : ''; ?>>Multiple</option>
                                <option value="Psicosocial" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'Psicosocial') ? 'selected' : ''; ?>>Psicosocial</option>
                                <option value="SordoCeguera" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'SordoCeguera') ? 'selected' : ''; ?>>SordoCeguera</option>
                                <option value="Visual" <?php echo (isset($row['tipoDiscapacidad']) && $row['tipoDiscapacidad'] == 'Visual') ? 'selected' : ''; ?>>Visual</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="rango_integVenta">* RANGO DE EDAD:</label>
                            <select name="rango_integVenta" class="form-control" id="rango_integVenta">
                                <option value=""></option>
                                <option value="0 - 6" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == '0 - 6') ? 'selected' : ''; ?>>0 - 5</option>
                                <option value="7 - 12" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == '7 - 12') ? 'selected' : ''; ?>>6 - 12</option>
                                <option value="13 - 17" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == '13 - 17') ? 'selected' : ''; ?>>13 - 17</option>
                                <option value="18 - 28" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == '18 - 28') ? 'selected' : ''; ?>>18 - 28</option>
                                <option value="29-45" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == '29-45') ? 'selected' : ''; ?>>29-45</option>
                                <option value="46-64" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == '46-64') ? 'selected' : ''; ?>>46-64</option>
                                <option value="Mayor o igual a 65" <?php echo (isset($row['rango_integVenta']) && $row['rango_integVenta'] == 'Mayor o igual a 65') ? 'selected' : ''; ?>>Mayor o igual a 65</option>
                            </select>

                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="mujerGestante">* MUJER GESTANTE/LACTANTE:</label>
                                <select name="mujerGestante" class="form-control" id="mujerGestante">
                                    <option value=""></option>
                                    <option value="Si" <?php echo (isset($row['mujerGestante']) && $row['mujerGestante'] == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (isset($row['mujerGestante']) && $row['mujerGestante'] == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cabezaFamilia">* HOMBRE/MUJER CABEZA FAMILIA:</label>
                                <select name="cabezaFamilia" class="form-control" id="cabezaFamilia">
                                    <option value=""></option>
                                    <option value="Si" <?php echo (isset($row['cabezaFamilia']) && $row['cabezaFamilia'] == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (isset($row['cabezaFamilia']) && $row['cabezaFamilia'] == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="orientacionSexual">* ORIENTACION SEXUAL:</label>
                                <select name="orientacionSexual" class="form-control" id="orientacionSexual">
                                    <option value=""></option>
                                    <option value="Asexual" <?php echo (isset($row['orientacionSexual']) && $row['orientacionSexual'] == 'Asexual') ? 'selected' : ''; ?>>Asexual</option>
                                    <option value="Bisexual" <?php echo (isset($row['orientacionSexual']) && $row['orientacionSexual'] == 'Bisexual') ? 'selected' : ''; ?>>Bisexual</option>
                                    <option value="Homosexual" <?php echo (isset($row['orientacionSexual']) && $row['orientacionSexual'] == 'Homosexual') ? 'selected' : ''; ?>>Homosexual</option>
                                    <option value="Heterosexual" <?php echo (isset($row['orientacionSexual']) && $row['orientacionSexual'] == 'Heterosexual') ? 'selected' : ''; ?>>Heterosexual</option>
                                    <option value="Otro" <?php echo (isset($row['orientacionSexual']) && $row['orientacionSexual'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="experienciaMigratoria">* EXPERIENCIA MIGRATORIA</label>
                                <select name="experienciaMigratoria" class="form-control" id="experienciaMigratoria">
                                    <option value=""></option>
                                    <option value="Si" <?php echo (isset($row['experienciaMigratoria']) && $row['experienciaMigratoria'] == 'Si') ? 'selected' : ''; ?>>Si</option>
                                    <option value="No" <?php echo (isset($row['experienciaMigratoria']) && $row['experienciaMigratoria'] == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="grupoEtnico">* GRUPO ETNICO:</label>
                                <select name="grupoEtnico" class="form-control" id="grupoEtnico">
                                    <option value=""></option>
                                    <option value="Indigena" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'Indigena') ? 'selected' : ''; ?>>Indigena</option>
                                    <option value="ROM (Gitano)" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'ROM (Gitano)') ? 'selected' : ''; ?>>ROM (Gitano)</option>
                                    <option value="Raizal" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'Raizal') ? 'selected' : ''; ?>>Raizal</option>
                                    <option value="Palanquero de San Basilio" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'Palanquero de San Basilio') ? 'selected' : ''; ?>>Palanquero de San Basilio</option>
                                    <option value="Negro(a), Mulato(a), Afrocolobiano(a)" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'Negro(a), Mulato(a), Afrocolobiano(a)') ? 'selected' : ''; ?>>Negro(a), Mulato(a), Afrocolobiano(a)</option>
                                    <option value="Mestizo" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'Mestizo') ? 'selected' : ''; ?>>Mestizo</option>
                                    <option value="Ninguno" <?php echo (isset($row['grupoEtnico']) && $row['grupoEtnico'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="seguridadSalud">* TIPO SEGURIDAD SALUD:</label>
                                <select name="seguridadSalud" class="form-control" id="seguridadSalud">
                                    <option value=""></option>
                                    <option value="Regimen Contributivo" <?php echo (isset($row['seguridadSalud']) && $row['seguridadSalud'] == 'Regimen Contributivo') ? 'selected' : ''; ?>>Regimen Contributivo</option>
                                    <option value="Regimen Subsidiado" <?php echo (isset($row['seguridadSalud']) && $row['seguridadSalud'] == 'Regimen Subsidiado') ? 'selected' : ''; ?>>Regimen Subsidiado</option>
                                    <option value="Poblacion Vinculada" <?php echo (isset($row['seguridadSalud']) && $row['seguridadSalud'] == 'Poblacion Vinculada') ? 'selected' : ''; ?>>Poblacion Vinculada</option>
                                    <option value="Ninguno" <?php echo (isset($row['seguridadSalud']) && $row['seguridadSalud'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="nivelEducativo">* NIVEL EDUCATIVO</label>
                                <select name="nivelEducativo" class="form-control" id="nivelEducativo">
                                    <option value=""></option>
                                    <option value="Preescolar" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Preescolar') ? 'selected' : ''; ?>>Preescolar</option>
                                    <option value="Basica Primaria" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Basica Primaria') ? 'selected' : ''; ?>>Basica Primaria</option>
                                    <option value="Basica Secundaria" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Basica Secundaria') ? 'selected' : ''; ?>>Basica Secundaria</option>
                                    <option value="Media Academica o clasica" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Media Academica o clasica') ? 'selected' : ''; ?>>Media Academica o clasica</option>
                                    <option value="Media Tecnica" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Media Tecnica') ? 'selected' : ''; ?>>Media Tecnica</option>
                                    <option value="Normalista" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Normalista') ? 'selected' : ''; ?>>Normalista</option>
                                    <option value="Universitario" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Universitario') ? 'selected' : ''; ?>>Universitario</option>
                                    <option value="Tecnico profesional" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Tecnico profesional') ? 'selected' : ''; ?>>Tecnico profesional</option>
                                    <option value="Tecnologo" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Tecnologo') ? 'selected' : ''; ?>>Tecnologo</option>
                                    <option value="Profesional" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Profesional') ? 'selected' : ''; ?>>Profesional</option>
                                    <option value="Especializacion" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Especializacion') ? 'selected' : ''; ?>>Especializacion</option>
                                    <option value="Maestria" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Maestria') ? 'selected' : ''; ?>>Maestria</option>
                                    <option value="Doctorado" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Doctorado') ? 'selected' : ''; ?>>Doctorado</option>
                                    <option value="Ninguno" <?php echo (isset($row['nivelEducativo']) && $row['nivelEducativo'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="condicionOcupacion">* CONDICION OCUPACION:</label>
                                <select name="condicionOcupacion" class="form-control" id="condicionOcupacion">
                                    <option value=""></option>
                                    <option value="Ama de Casa" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Ama de Casa') ? 'selected' : ''; ?>>Ama de Casa</option>
                                    <option value="Buscando Empleo" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Buscando Empleo') ? 'selected' : ''; ?>>Buscando Empleo</option>
                                    <option value="Desempleado(a)" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Desempleado(a)') ? 'selected' : ''; ?>>Desempleado(a)</option>
                                    <option value="Empleado(a)" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Empleado(a)') ? 'selected' : ''; ?>>Empleado(a)</option>
                                    <option value="Estudiante" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Estudiante') ? 'selected' : ''; ?>>Estudiante</option>
                                    <option value="Independiente" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Independiente') ? 'selected' : ''; ?>>Independiente</option>
                                    <option value="Pensionado(a)" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Pensionado(a)') ? 'selected' : ''; ?>>Pensionado(a)</option>
                                    <option value="Ninguno" <?php echo (isset($row['condicionOcupacion']) && $row['condicionOcupacion'] == 'Ninguno') ? 'selected' : ''; ?>>Ninguno</option>
                                </select>
                            </div>
                        </div>
                    </div>







                    <div class="form-group">
                        <div class="row">


                            <div class="form-group col-md-4">
                                <label for="obs1_encInfo">* TIPO INFORMACION BRINDADA:</label>
                                <select class="form-control" name="observacion" id="obs2_encInfo">
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
                        </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const departamentoSelect = document.getElementById('departamento_expedicion');
        const ciudadSelect = document.getElementById('ciudad_expedicion');

        // Guardamos la ciudad que se debe seleccionar (si existe globalmente)
        let ciudadSeleccionada = null;

        // Función para cargar municipios
        function cargarMunicipios(departamento) {
            ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad</option>';

            if (departamento === '') {
                ciudadSelect.disabled = true;
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../obtener_municipios.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const municipios = JSON.parse(xhr.responseText);
                    municipios.forEach(function(municipio) {
                        const option = document.createElement('option');
                        option.value = municipio.cod_municipio;
                        option.textContent = municipio.nombre_municipio;
                        ciudadSelect.appendChild(option);
                    });

                    ciudadSelect.disabled = false;

                    // ✅ Si ya teníamos una ciudad guardada, la seleccionamos
                    if (ciudadSeleccionada) {
                        ciudadSelect.value = ciudadSeleccionada;
                        ciudadSeleccionada = null; // Limpiamos
                    }
                } else {
                    alert('Error al cargar municipios');
                }
            };

            xhr.send('cod_departamento=' + departamento);
        }

        // Evento change para el departamento
        departamentoSelect.addEventListener('change', function() {
            console.log('Departamento seleccionado:', this.value);
            cargarMunicipios(this.value);
        });

        // ✅ Exponemos una función global para seleccionar ciudad desde AJAX
        window.setCiudadSeleccionada = function(ciudad) {
            ciudadSeleccionada = ciudad;
        };
    });

    $(document).ready(function() {
        // Asegúrate de que también tienes el valor de ciudad_expedicion disponible
        const codDepartamento = <?= json_encode($row['departamento_expedicion']) ?>;
        const codCiudad = <?= json_encode($row['ciudad_expedicion']) ?>;

        $.ajax({
            url: '../obtener_municipios.php',
            type: 'POST',
            data: {
                cod_departamento: codDepartamento
            },
            dataType: 'json',
            success: function(municipios) {
                let ciudadSelect = $("#ciudad_expedicion");
                ciudadSelect.empty().append('<option value="">Seleccione un municipio</option>');

                $.each(municipios, function(index, municipio) {
                    ciudadSelect.append(
                        $('<option>', {
                            value: municipio.cod_municipio,
                            text: municipio.nombre_municipio,
                            selected: municipio.cod_municipio == codCiudad // No hace falta parseInt si ambos son string
                        })
                    );
                });

                ciudadSelect.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error("Error al obtener municipios:", error);
            }
        });
    });

    // Control para habilitar/deshabilitar tipoDiscapacidad según condicionDiscapacidad
    $(document).ready(function() {
        function actualizarTipoDiscapacidad() {
            var cond = $('#condicionDiscapacidad').val();
            var tipo = $('#tipoDiscapacidad');

            if (cond === 'No' || cond === '') {
                // Limpiar y deshabilitar para que no pueda modificarse
                tipo.val('');
                tipo.prop('disabled', true);
            } else {
                // Habilitar para permitir selección
                tipo.prop('disabled', false);
            }
        }

        // Inicializar al cargar la página (respeta valor cargado desde PHP)
        actualizarTipoDiscapacidad();

        // Manejar cambios del select
        $('#condicionDiscapacidad').on('change', function() {
            actualizarTipoDiscapacidad();
        });
    });
</script>

</html>