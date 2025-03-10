<?php
    header("Content-Type: text/html;charset=utf-8");
    header("Content-Type:application/vnd.ms-excel; charset=utf-8");
    //header('Content-type:application/xls'; charset=utf-8");
    header('Content-Disposition: attachment; filename=reporte_informacion.xls');
    date_default_timezone_set('America/Bogota');

    require_once('conexion.php');
    $conn=new Conexion();
    $link = $conn->conectarse();
    
    $id_usu     = $_POST['id_usu'];
    $de         = $_POST['de'];
    $hasta      = $_POST['hasta'];

    $query = "SELECT encInfo.id_encInfo, encInfo.fec_rea_encInfo, encInfo.doc_encInfo, encInfo.nom_encInfo, encInfo.tipo_solic_encInfo, encInfo.obs1_encInfo, encInfo.obs2_encInfo, encuestadores.nom_enc  
            FROM encInfo 
            LEFT JOIN usuarios ON encInfo.id_usu=usuarios.id_usu 
            LEFT JOIN encuestadores ON usuarios.id_usu=encuestadores.id_usu 
            WHERE encInfo.fec_rea_encInfo>='$de' AND encInfo.fec_rea_encInfo<='$hasta' AND encInfo.id_usu='$id_usu' ORDER BY encInfo.fec_rea_encInfo ASC";

    $result = mysqli_query($link, $query);
?>

    <table border="1">
        <tr>
            <th style="background-color:#706E6E;">FECHA DE REALIZACION</th>
            <th style="background-color:#706E6E;">DOCUMENTO</th>
            <th style="background-color:#706E6E;">NOMBRES</th>
            <th style="background-color:#706E6E;">TIPO SOLICITUD</th>
            <th style="background-color:#706E6E;">OBSERVACIONES</th>
            <th style="background-color:#706E6E;">COMENTARIOS</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $row['fec_rea_encInfo']; ?></td>
                <td style="text-align: center;"><?php echo $row['doc_encInfo']; ?></td>
                <td><?php echo $row['nom_encInfo']; ?></td>
                <td style="text-align: center;"><?php echo $row['tipo_solic_encInfo']; ?></td>
                <td><?php echo $row['obs1_encInfo']; ?></td>
                <td><?php echo $row['obs2_encInfo']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>