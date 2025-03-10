<?php
header("Content-Type: text/html;charset=utf-8");
header("Content-Type:application/vnd.ms-excel; charset=utf-8");
header('Content-Disposition: attachment; filename=ENC_MOVIMIENTOS_FECHA_DATOS_AGRUPADOS.xls');
date_default_timezone_set('America/Bogota');

require_once('conexion.php');
$conn = new Conexion();
$link = $conn->conectarse();

$id_usu  = $_POST['id_usu'];
$de      = $_POST['de'];
$hasta   = $_POST['hasta'];

$query = "SELECT 
            em.id_encMovim, 
            em.fec_reg_encMovim, 
            em.doc_encMovim, 
            em.nom_encMovim, 
            em.dir_encMovim, 
            em.zona_encMovim, 
            com.nombre_com, 
            bar.nombre_bar, 
            cor.nombre_correg, 
            ver.nombre_vere,
            em.otro_bar_ver_encMovim, 
            em.tram_solic_encMovim,
            em.integra_encMovim, 
            em.num_ficha_encMovim, 
            em.obs_encMovim, 
            enc.nom_enc,
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    CASE
                        WHEN im.rango_integMovim = 1 THEN 'Entre 0 - 6'
                        WHEN im.rango_integMovim = 2 THEN 'Entre 7 - 12'
                        WHEN im.rango_integMovim = 3 THEN 'Entre 13 - 17'
                        WHEN im.rango_integMovim = 4 THEN 'Entre 18 - 28'
                        WHEN im.rango_integMovim = 5 THEN 'Entre 29 - 45'
                        WHEN im.rango_integMovim = 6 THEN 'Entre 46 - 64'
                        WHEN im.rango_integMovim = 7 THEN 'Mayor o igual a 65'
                        ELSE ''
                    END, '|',
                    im.gen_integMovim, '|',
                    im.cant_integMovim
                ) SEPARATOR ', '
            ) AS integrantes_info
        FROM 
            encMovimientos em
        LEFT JOIN 
            comunas com ON em.id_com = com.id_com 
        LEFT JOIN 
            barrios bar ON em.id_bar = bar.id_bar 
        LEFT JOIN 
            corregimientos cor ON em.id_correg = cor.id_correg 
        LEFT JOIN 
            veredas ver ON em.id_vere = ver.id_vere 
        LEFT JOIN 
            usuarios usu ON em.id_usu = usu.id_usu 
        LEFT JOIN 
            encuestadores enc ON usu.id_usu = enc.id_usu 
        LEFT JOIN 
            integMovimientos im ON im.id_encMovim = em.id_encMovim  
        WHERE 
            em.fec_reg_encMovim >= ? AND em.fec_reg_encMovim <= ?
        GROUP BY 
            em.id_encMovim 
        ORDER BY 
            em.fec_reg_encMovim ASC, 
            em.nom_encMovim ASC";

        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "ss", $de, $hasta);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
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
                <td style="text-align: center;"><?php echo $row['fec_reg_encMovim']; ?></td>
                <td style="text-align: center;"><?php echo $row['doc_encMovim']; ?></td>
                <td><?php echo utf8_decode($row['nom_encMovim']); ?></td>
                <td><?php echo utf8_decode($row['dir_encMovim']); ?></td>
                <td style="text-align: center;"><?php echo $row['zona_encMovim']; ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_com']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_bar']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_correg']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_vere']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['otro_bar_ver_encMovim']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['tram_solic_encMovim']); ?></td>
                <td style="text-align: center;"><?php echo $row['integra_encMovim']; ?></td>
                <td style="text-align: center;"><?php echo $row['num_ficha_encMovim'].' .'; ?></td>
                <td><?php echo utf8_decode($row['obs_encMovim']); ?></td>
                <td><?php echo utf8_decode($row['nom_enc']); ?></td>
                <td style="text-align: center;"><?php echo $row['integrantes_info']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>