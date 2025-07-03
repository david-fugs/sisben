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
    $time = time();
    $id_integVenta  = $_GET['id_integVenta'];
    if (isset($_GET['id_integVenta'])) {
        $sql = mysqli_query($mysqli, "SELECT * FROM integventanilla WHERE id_integVenta = '$id_integVenta'");
        $row = mysqli_fetch_array($sql);
    }

    ?>

    <div class="container">
        <center>
            <img src='../../img/sisben.png' width=300 height=185 class="responsive">
        </center>

        <h1><b><i class="fas fa-users"></i> VERIFICAR Y ACTUALIZAR INFORMACIÓN DE LOS INTEGRANTES DEL GRUPO FAMILIAR</b></h1>
        <p><i><b>
                    <font size=3 color=#c68615>* Datos obligatorios</i></b></font>
        </p>

        <form action='editintegVentanilla.php' enctype="multipart/form-data" method="POST">

            <hr style="border: 2px solid #16087B; border-radius: 2px;">
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-3">
                        <input type='number' name='id_integVenta' id="id_integVenta" class='form-control' value='<?php echo $row['id_integVenta']; ?>' readonly hidden />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-2">
                        <label for="cant_integVenta">* CANTIDAD:</label>
                        <input type='number' name='cant_integVenta' class='form-control' value='<?php echo $row['cant_integVenta']; ?>' required />
                    </div>
                    <div class="col-12 col-sm-2">
                        <label for="gen_integVenta">* IDENTIDAD GENERO</label>
                        <select class="form-control" name="gen_integVenta" required>
                            <option value=""></option>
                            <option value="M" <?php if ($row['gen_integVenta'] == 'M') {
                                                    echo 'selected';
                                                } ?>>M</option>
                            <option value="F" <?php if ($row['gen_integVenta'] == 'F') {
                                                    echo 'selected';
                                                } ?>>F</option>
                            <option value="O" <?php if ($row['gen_integVenta'] == 'O') {
                                                    echo 'selected';
                                                } ?>>OTRO</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-3">
                        <label for="rango_integVenta">* EDAD:</label>
                        <select class="form-control" name="rango_integVenta" required>
                            <option value="">SELECCIONE:</option>
                            <option value=1 <?php if ($row['rango_integVenta'] == 1) {
                                                echo 'selected';
                                            } ?>>0 - 5</option>
                            <option value=2 <?php if ($row['rango_integVenta'] == 2) {
                                                echo 'selected';
                                            } ?>>6 - 12</option>
                            <option value=3 <?php if ($row['rango_integVenta'] == 3) {
                                                echo 'selected';
                                            } ?>>13 - 17</option>
                            <option value=4 <?php if ($row['rango_integVenta'] == 4) {
                                                echo 'selected';
                                            } ?>>18 - 28</option>
                            <option value=5 <?php if ($row['rango_integVenta'] == 5) {
                                                echo 'selected';
                                            } ?>>29 - 45</option>
                            <option value=6 <?php if ($row['rango_integVenta'] == 6) {
                                                echo 'selected';
                                            } ?>>46 - 64</option>
                            <option value=7 <?php if ($row['rango_integVenta'] == 7) {
                                                echo 'selected';
                                            } ?>>Mayor o igual a 65</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-2">
                        <label for="id_encVenta">ID:</label>
                        <input type='number' name='id_encVenta' id="id_encVenta" class='form-control' value='<?php echo $row['id_encVenta']; ?>' readonly />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-4 mt-3">
                        <label for="grupoEtnico">* GRUPO ETNICO:</label>
                        <select class="form-control" name="grupoEtnico" required>
                            <option value="">SELECCIONE:</option>
                            <option value="Indigena" <?php if ($row['grupoEtnico'] == "Indigena") {
                                                            echo 'selected';
                                                        } ?>>Indigena</option>
                            <option value="Negra / Afrocolombiana" <?php if ($row['grupoEtnico'] == "Negra / Afrocolombiana") {
                                                                        echo 'selected';
                                                                    } ?>>Negra / Afrocolombiana</option>
                            <option value="Raizal" <?php if ($row['grupoEtnico'] == "Raizal") {
                                                        echo 'selected';
                                                    } ?>>Raizal</option>
                            <option value="Palenquero" <?php if ($row['grupoEtnico'] == "Palenquero") {
                                                            echo 'selected';
                                                        } ?>>Palenquero</option>
                            <option value="Gitano (rom)" <?php if ($row['grupoEtnico'] == "Gitano (rom)") {
                                                                echo 'selected';
                                                            } ?>>Gitano (rom)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 mt-3">
                        <label for="orientacionSexual">* ORIENTACION SEXUAL:</label>
                        <select name="orientacionSexual" class="form-control" id="orientacionSexual" required>
                            <option value=""></option>
                            <option value="Asexual" <?= ($row['orientacionSexual'] == 'Asexual') ? 'selected' : '' ?>>Asexual</option>
                            <option value="Bisexual" <?= ($row['orientacionSexual'] == 'Bisexual') ? 'selected' : '' ?>>Bisexual</option>
                            <option value="Homosexual" <?= ($row['orientacionSexual'] == 'Homosexual') ? 'selected' : '' ?>>Homosexual</option>
                            <option value="Heterosexual" <?= ($row['orientacionSexual'] == 'Heterosexual') ? 'selected' : '' ?>>Heterosexual</option>
                            <option value="Otro" <?= ($row['orientacionSexual'] == 'Otro') ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>


                    <div class="col-12 col-sm-4 mt-3">
                        <label for="condicionDiscapacidad">* CONDICIÓN DISCAPACIDAD:</label>
                        <select class="form-control" name="condicionDiscapacidad" required>
                            <option value="">SELECCIONE:</option>
                            <option value="Si" <?php if ($row['condicionDiscapacidad'] == "Si") {
                                                    echo 'selected';
                                                } ?>>SÍ</option>
                            <option value="No" <?php if ($row['condicionDiscapacidad'] == "No") {
                                                    echo 'selected';
                                                } ?>>NO</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mt-3" id="tipoDiscapacidadContainer">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="tipoDiscapacidad">* TIPO DISCAPACIDAD:</label>
                            <select class="form-control" name="tipoDiscapacidad" id="tipoDiscapacidad">
                                <option value=""></option>
                                <option value="Auditiva" <?= ($row['tipoDiscapacidad'] == 'Auditiva') ? 'selected' : '' ?>>Auditiva</option>
                                <option value="Fisica" <?= ($row['tipoDiscapacidad'] == 'Fisica') ? 'selected' : '' ?>>Fisica</option>
                                <option value="Intelectual" <?= ($row['tipoDiscapacidad'] == 'Intelectual') ? 'selected' : '' ?>>Intelectual</option>
                                <option value="Multiple" <?= ($row['tipoDiscapacidad'] == 'Multiple') ? 'selected' : '' ?>>Multiple</option>
                                <option value="Psicosocial" <?= ($row['tipoDiscapacidad'] == 'Psicosocial') ? 'selected' : '' ?>>Psicosocial</option>
                                <option value="SordoCeguera" <?= ($row['tipoDiscapacidad'] == 'SordoCeguera') ? 'selected' : '' ?>>SordoCeguera</option>
                                <option value="Visual" <?= ($row['tipoDiscapacidad'] == 'Visual') ? 'selected' : '' ?>>Visual</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="victima">* VICTIMA:</label>
                            <select class="form-control" name="victima" id="victima">
                                <option value=""></option>
                                <option value="Si" <?= ($row['victima'] == 'Si') ? 'selected' : '' ?>>Si</option>
                                <option value="No" <?= ($row['victima'] == 'No') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="experienciaMigratoria">* EXPERIENCIA MIGRATORIA:</label>
                            <select class="form-control" name="experienciaMigratoria" id="experienciaMigratoria">
                                <option value=""></option>
                                <option value="Si" <?= ($row['experienciaMigratoria'] == 'Si') ? 'selected' : '' ?>>Si</option>
                                <option value="No" <?= ($row['experienciaMigratoria'] == 'No') ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="mujerGestante">* MUJER GESTANTE/LACTANTE:</label>
                                <select name="mujerGestante" class="form-control" id="mujerGestante" required>
                                    <option value=""></option>
                                    <option value="Si" <?= ($row['mujerGestante'] == 'Si') ? 'selected' : '' ?>>Si</option>
                                    <option value="No" <?= ($row['mujerGestante'] == 'No') ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cabezaFamilia">* HOMBRE/MUJER CABEZA FAMILIA:</label>
                                <select name="cabezaFamilia" class="form-control" id="cabezaFamilia" required>
                                    <option value=""></option>
                                    <option value="Si" <?= ($row['cabezaFamilia'] == 'Si') ? 'selected' : '' ?>>Si</option>
                                    <option value="No" <?= ($row['cabezaFamilia'] == 'No') ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="seguridadSalud">* TIPO SEGURIDAD SALUD:</label>
                                <select class="form-control" name="seguridadSalud" id="seguridadSalud">
                                    <option value=""></option>
                                    <option value="Regimen Contributivo" <?= ($row['seguridadSalud'] == 'Regimen Contributivo') ? 'selected' : '' ?>>Regimen Contributivo</option>
                                    <option value="Regimen Subsidiado" <?= ($row['seguridadSalud'] == 'Regimen Subsidiado') ? 'selected' : '' ?>>Regimen Subsidiado</option>
                                    <option value="Población Vinculada" <?= ($row['seguridadSalud'] == 'Población Vinculada') ? 'selected' : '' ?>>Población Vinculada</option>
                                </select>
                            </div>
                        </div>
                    </div>



                    <div class="form-group">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="nivelEducativo">* NIVEL EDUCATIVO:</label>
                                <select name="nivelEducativo" class="form-control" id="nivelEducativo" required>
                                    <option value=""></option>
                                    <option value="Preescolar" <?= ($row['nivelEducativo'] == 'Preescolar') ? 'selected' : '' ?>>Preescolar</option>
                                    <option value="Basica Primaria" <?= ($row['nivelEducativo'] == 'Basica Primaria') ? 'selected' : '' ?>>Basica Primaria</option>
                                    <option value="Basica Secundaria" <?= ($row['nivelEducativo'] == 'Basica Secundaria') ? 'selected' : '' ?>>Basica Secundaria</option>
                                    <option value="Media Academica o clasica" <?= ($row['nivelEducativo'] == 'Media Academica o clasica') ? 'selected' : '' ?>>Media Academica o clasica</option>
                                    <option value="Media Tecnica" <?= ($row['nivelEducativo'] == 'Media Tecnica') ? 'selected' : '' ?>>Media Tecnica</option>
                                    <option value="Normalista" <?= ($row['nivelEducativo'] == 'Normalista') ? 'selected' : '' ?>>Normalista</option>
                                    <option value="Universitario" <?= ($row['nivelEducativo'] == 'Universitario') ? 'selected' : '' ?>>Universitario</option>
                                    <option value="Tecnico profesional" <?= ($row['nivelEducativo'] == 'Tecnico profesional') ? 'selected' : '' ?>>Tecnico profesional</option>
                                    <option value="Tecnologo" <?= ($row['nivelEducativo'] == 'Tecnologo') ? 'selected' : '' ?>>Tecnologo</option>
                                    <option value="Profesional" <?= ($row['nivelEducativo'] == 'Profesional') ? 'selected' : '' ?>>Profesional</option>
                                    <option value="Especializacion" <?= ($row['nivelEducativo'] == 'Especializacion') ? 'selected' : '' ?>>Especializacion</option>
                                    <option value="Maestria" <?= ($row['nivelEducativo'] == 'Maestria') ? 'selected' : '' ?>>Maestria</option>
                                    <option value="Doctorado" <?= ($row['nivelEducativo'] == 'Doctorado') ? 'selected' : '' ?>>Doctorado</option>
                                    <option value="Ninguno" <?= ($row['nivelEducativo'] == 'Ninguno') ? 'selected' : '' ?>>Ninguno</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="condicionOcupacion">* CONDICION OCUPACION:</label>
                                <select name="condicionOcupacion" class="form-control" id="condicionOcupacion" required>
                                    <option value=""></option>
                                    <option value="Ama de Casa" <?= ($row['condicionOcupacion'] == 'Ama de Casa') ? 'selected' : '' ?>>Ama de Casa</option>
                                    <option value="Buscando Empleo" <?= ($row['condicionOcupacion'] == 'Buscando Empleo') ? 'selected' : '' ?>>Buscando Empleo</option>
                                    <option value="Desempleado(a)" <?= ($row['condicionOcupacion'] == 'Desempleado(a)') ? 'selected' : '' ?>>Desempleado(a)</option>
                                    <option value="Empleado(a)" <?= ($row['condicionOcupacion'] == 'Empleado(a)') ? 'selected' : '' ?>>Empleado(a)</option>
                                    <option value="Estudiante" <?= ($row['condicionOcupacion'] == 'Estudiante') ? 'selected' : '' ?>>Estudiante</option>
                                    <option value="Independiente" <?= ($row['condicionOcupacion'] == 'Independiente') ? 'selected' : '' ?>>Independiente</option>
                                    <option value="Pensionado(a)" <?= ($row['condicionOcupacion'] == 'Pensionado(a)') ? 'selected' : '' ?>>Pensionado(a)</option>
                                    <option value="Ninguno" <?= ($row['condicionOcupacion'] == 'Ninguno') ? 'selected' : '' ?>>Ninguno</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <hr style="border: 2px solid #16087B; border-radius: 2px;">
                <button type="submit" class="btn btn-primary" name="btn-update">
                    <span class="spinner-border spinner-border-sm"></span>
                    ACTUALIZAR INFORMACIÓN FAMILIAR
                </button>
                <button type="reset" class="btn btn-outline-dark" role='link' onclick="history.back();" type='reset'><img src='../../img/atras.png' width=27 height=27> REGRESAR
                </button>
        </form>
    </div>
</body>

</html>