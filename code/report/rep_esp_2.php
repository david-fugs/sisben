<?php
header("Content-Type: text/html;charset=utf-8");
header("Content-Type:application/vnd.ms-excel; charset=utf-8");
header('Content-Disposition: attachment; filename=ENC_CAMPO_FECHA_DATOS_AGRUPADOS.xls');
date_default_timezone_set('America/Bogota');

require_once('conexion.php');
$conn = new Conexion();
$link = $conn->conectarse();

$id_usu  = $_POST['id_usu'];
$de      = $_POST['de'];
$hasta   = $_POST['hasta'];
$query = "
    SELECT 
        ev.id_encVenta, 
        ev.fec_reg_encVenta, 
        ev.doc_encVenta, 
        ev.nom_encVenta, 
        ev.dir_encVenta, 
        ev.zona_encVenta, 
        com.nombre_com, 
        bar.nombre_bar, 
        cor.nombre_correg, 
        ver.nombre_vere,
        ev.otro_bar_ver_encVenta, 
        ev.tram_solic_encVenta,
        ev.integra_encVenta, 
        ev.num_ficha_encVenta, 
        ev.obs_encVenta, 
        enc.nom_enc,
        GROUP_CONCAT(
            DISTINCT CONCAT(
                CASE
                    WHEN iv.rango_integVenta = 1 THEN 'Entre 0 - 6'
                    WHEN iv.rango_integVenta = 2 THEN 'Entre 7 - 12'
                    WHEN iv.rango_integVenta = 3 THEN 'Entre 13 - 17'
                    WHEN iv.rango_integVenta = 4 THEN 'Entre 18 - 28'
                    WHEN iv.rango_integVenta = 5 THEN 'Entre 29 - 45'
                    WHEN iv.rango_integVenta = 6 THEN 'Entre 46 - 64'
                    WHEN iv.rango_integVenta = 7 THEN 'Mayor o igual a 65'
                    ELSE ''
                END, '|',
                iv.gen_integVenta, '|',
                iv.cant_integVenta
            ) SEPARATOR ', '
        ) AS integrantes_info
    FROM 
        encVentanilla ev
    LEFT JOIN comunas com ON ev.id_com = com.id_com 
    LEFT JOIN barrios bar ON ev.id_bar = bar.id_bar 
    LEFT JOIN corregimientos cor ON ev.id_correg = cor.id_correg 
    LEFT JOIN veredas ver ON ev.id_vere = ver.id_vere 
    LEFT JOIN usuarios usu ON ev.id_usu = usu.id_usu 
    LEFT JOIN encuestadores enc ON usu.id_usu = enc.id_usu 
    LEFT JOIN integVentanilla iv ON iv.id_encVenta = ev.id_encVenta  
    WHERE 
        ev.fec_reg_encVenta >= '$de' AND ev.fec_reg_encVenta <= '$hasta'
    GROUP BY 
        ev.id_encVenta 
    ORDER BY 
        ev.fec_reg_encVenta ASC, 
        ev.nom_encVenta ASC
";

$result = mysqli_query($link, $query);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($link));
}
?>
<table border="1">
    <tr>
        <th style="background-color:#706E6E;">FECHA DE REALIZACION</th>
        <th style="background-color:#706E6E;">DOCUMENTO</th>
        <th style="background-color:#706E6E;">NOMBRES</th>
        <th style="background-color:#706E6E;">DIRECCION</th>
        <th style="background-color:#706E6E;">ZONA</th>
        <th style="background-color:#706E6E;">COMUNA</th>
        <th style="background-color:#706E6E;">BARRIO</th>
        <th style="background-color:#706E6E;">CORREGIMIENTO</th>
        <th style="background-color:#706E6E;">VEREDA</th>
        <th style="background-color:#706E6E;">OTRO BARRIO y/o VEREDA</th>
        <th style="background-color:#706E6E;">TRAMITE</th>
        <th style="background-color:#706E6E;">INTEGRANTES</th>
        <th style="background-color:#706E6E;">No. FICHA</th>
        <th style="background-color:#706E6E;">OBSERVACIONES</th>
        <th style="background-color:#706E6E;">ENCUESTADOR</th>
        <th style="background-color:#706E6E;">DATOS AGRUPADOS</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <tr>
            <td style="text-align: center;"><?php echo $row['fec_reg_encVenta']; ?></td>
            <td style="text-align: center;"><?php echo $row['doc_encVenta']; ?></td>
            <td><?php echo utf8_decode($row['nom_encVenta']); ?></td>
            <td><?php echo utf8_decode($row['dir_encVenta']); ?></td>
            <td style="text-align: center;"><?php echo $row['zona_encVenta']; ?></td>
            <td style="text-align: center;"><?php echo utf8_decode($row['nombre_com']); ?></td>
            <td style="text-align: center;"><?php echo utf8_decode($row['nombre_bar']); ?></td>
            <td style="text-align: center;"><?php echo utf8_decode($row['nombre_correg']); ?></td>
            <td style="text-align: center;"><?php echo utf8_decode($row['nombre_vere']); ?></td>
            <td style="text-align: center;"><?php echo utf8_decode($row['otro_bar_ver_encVenta']); ?></td>
            <td style="text-align: center;"><?php echo utf8_decode($row['tram_solic_encVenta']); ?></td>
            <td style="text-align: center;"><?php echo $row['integra_encVenta']; ?></td>
            <td style="text-align: center;"><?php echo $row['num_ficha_encVenta'] . ' .'; ?></td>
            <td><?php echo utf8_decode($row['obs_encVenta']); ?></td>
            <td><?php echo utf8_decode($row['nom_enc']); ?></td>
            <td style="text-align: center;"><?php echo $row['integrantes_info']; ?></td>
        </tr>
    <?php
    }
    ?>
</table>