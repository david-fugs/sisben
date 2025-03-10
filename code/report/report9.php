<?php
    header("Content-Type: text/html;charset=utf-8");
    header("Content-Type:application/vnd.ms-excel; charset=utf-8");
    //header('Content-type:application/xls'; charset=utf-8");
    header('Content-Disposition: attachment; filename=informe_enc_ventanilla_fecha.xls');
    date_default_timezone_set('America/Bogota');

    require_once('conexion.php');
    $conn=new Conexion();
    $link = $conn->conectarse();
    
    $id_usu     = $_POST['id_usu'];
    $de         = $_POST['de'];
    $hasta      = $_POST['hasta'];

    $query = "SELECT encVentanilla.id_encVenta,encVentanilla.fec_reg_encVenta, encVentanilla.doc_encVenta, encVentanilla.nom_encVenta, encVentanilla.dir_encVenta, encVentanilla.zona_encVenta, comunas.nombre_com, barrios.nombre_bar, corregimientos.nombre_correg, veredas.nombre_vere, encVentanilla.otro_bar_ver_encVenta, encVentanilla.tram_solic_encVenta, encVentanilla.integra_encVenta, encVentanilla.num_ficha_encVenta, encVentanilla.obs_encVenta, encuestadores.nom_enc  
            FROM encVentanilla 
            LEFT JOIN comunas ON encVentanilla.id_com=comunas.id_com 
            LEFT JOIN barrios ON encVentanilla.id_bar=barrios.id_bar 
            LEFT JOIN corregimientos ON encVentanilla.id_correg=corregimientos.id_correg 
            LEFT JOIN veredas ON encVentanilla.id_vere=veredas.id_vere 
            LEFT JOIN usuarios ON encVentanilla.id_usu=usuarios.id_usu 
            LEFT JOIN encuestadores ON usuarios.id_usu=encuestadores.id_usu 
            WHERE encVentanilla.fec_reg_encVenta>='$de' AND encVentanilla.fec_reg_encVenta<='$hasta' ORDER BY encVentanilla.fec_reg_encVenta ASC";

    $result = mysqli_query($link, $query);
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
                <td style="text-align: center;"><?php echo $row['num_ficha_encVenta'].' .'; ?></td>
                <td><?php echo utf8_decode($row['obs_encVenta']); ?></td>
                <td><?php echo utf8_decode($row['nom_enc']); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>