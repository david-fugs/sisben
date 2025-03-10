<?php
    header("Content-Type: text/html;charset=utf-8");
    header("Content-Type:application/vnd.ms-excel; charset=utf-8");
    //header('Content-type:application/xls'; charset=utf-8");
    header('Content-Disposition: attachment; filename=informe_general_enc_campo.xls');
    date_default_timezone_set('America/Bogota');

    require_once('conexion.php');
    $conn=new Conexion();
    $link = $conn->conectarse();
    
    $id_usu     = $_POST['id_usu'];
   
    $query = "SELECT encCampo.id_encCampo,encCampo.fec_pre_encCampo, encCampo.fec_rea_encCampo, encCampo.doc_encCampo, encCampo.nom_encCampo, encCampo.dir_encCampo, encCampo.zona_encCampo, comunas.nombre_com, barrios.nombre_bar, corregimientos.nombre_correg, veredas.nombre_vere, encCampo.num_ficha_encCampo,encCampo.est_fic_encCampo, encCampo.proc_encCampo, encCampo.obs_encCampo, encuestadores.nom_enc   
            FROM encCampo 
            LEFT JOIN comunas ON encCampo.id_com=comunas.id_com 
            LEFT JOIN barrios ON encCampo.id_bar=barrios.id_bar 
            LEFT JOIN corregimientos ON encCampo.id_correg=corregimientos.id_correg 
            LEFT JOIN veredas ON encCampo.id_vere=veredas.id_vere 
            LEFT JOIN usuarios ON encCampo.id_usu=usuarios.id_usu 
            LEFT JOIN encuestadores ON usuarios.id_usu=encuestadores.id_usu
            WHERE usuarios.id_usu='$id_usu'
            ORDER BY encCampo.fec_rea_encCampo AND usuarios.id_usu='$id_usu' ASC";

    $result = mysqli_query($link, $query);
?>

    <table border="1">
        <tr>
            <th style="background-color:#706E6E;">FECHA PRE-REGISTRO</th>
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
            <th style="background-color:#706E6E;">ESTADO</th>
            <th style="background-color:#706E6E;">INTEGRANTES</th>
            <th style="background-color:#706E6E;">No. FICHA</th>
            <th style="background-color:#706E6E;">PROCESO EN CAMPO</th>
            <th style="background-color:#706E6E;">OBSERVACIONES</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $row['fec_pre_encCampo']; ?></td>
                <td style="text-align: center;"><?php echo $row['fec_rea_encCampo']; ?></td>
                <td style="text-align: center;"><?php echo $row['doc_encCampo']; ?></td>
                <td><?php echo utf8_decode($row['nom_encCampo']); ?></td>
                <td><?php echo utf8_decode($row['dir_encCampo']); ?></td>
                <td style="text-align: center;"><?php echo $row['zona_encCampo']; ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_com']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_bar']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_correg']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['nombre_vere']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['otro_bar_ver_encCampo']); ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['est_fic_encCampo']); ?></td>
                <td style="text-align: center;"><?php echo $row['integra_encCampo']; ?></td>
                <td style="text-align: center;"><?php echo $row['num_ficha_encCampo'].' .'; ?></td>
                <td style="text-align: center;"><?php echo utf8_decode($row['proc_encCampo']); ?></td>
                <td><?php echo utf8_decode($row['obs_encCampo']); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>