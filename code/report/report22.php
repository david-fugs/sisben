<?php
	header("Content-Type: text/html;charset=utf-8");
	header("Content-Type:application/vnd.ms-excel; charset=utf-8");
	//header('Content-type:application/xls'; charset=utf-8");
	header('Content-Disposition: attachment; filename=informe_encuestador_movimientos_familia.xls');
	date_default_timezone_set('America/Bogota');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();
    
    $id_usu 	= $_POST['id_usu'];
	$de			= $_POST['de'];
	$hasta 		= $_POST['hasta'];

	$query = "SELECT 
                encMovimientos.id_encMovim,
                encMovimientos.fec_reg_encMovim,
                encMovimientos.doc_encMovim,
                encMovimientos.nom_encMovim,
                encMovimientos.dir_encMovim,
                encMovimientos.zona_encMovim, 
                comunas.nombre_com,
                barrios.nombre_bar,
                corregimientos.nombre_correg,
                veredas.nombre_vere,
                encMovimientos.otro_bar_ver_encMovim,
                encMovimientos.tram_solic_encMovim,
                encMovimientos.integra_encMovim,
                encMovimientos.num_ficha_encMovim,
                encMovimientos.obs_encMovim,
                encuestadores.nom_enc,
            GROUP_CONCAT(CONCAT(integMovimientos.rango_integMovim, '|', integMovimientos.gen_integMovim, '|', integMovimientos.cant_integMovim) SEPARATOR ', ') AS integrantes_info,
            GROUP_CONCAT(integMovimientos.cant_integMovim SEPARATOR ', ') AS cant_integMovim,
            GROUP_CONCAT(integMovimientos.gen_integMovim SEPARATOR ', ') AS gen_integMovim,
            GROUP_CONCAT(CASE
                WHEN integMovimientos.rango_integMovim = 1 THEN 'Entre 0 - 6'
                WHEN integMovimientos.rango_integMovim = 2 THEN 'Entre 7 - 12'
                WHEN integMovimientos.rango_integMovim = 3 THEN 'Entre 13 - 17'
                WHEN integMovimientos.rango_integMovim = 4 THEN 'Entre 18 - 28'
                WHEN integMovimientos.rango_integMovim = 5 THEN 'Entre 29 - 45'
                WHEN integMovimientos.rango_integMovim = 6 THEN 'Entre 46 - 64'
                WHEN integMovimientos.rango_integMovim = 7 THEN 'Mayor o igual a 65'
                ELSE ''
            END SEPARATOR ', ') AS rango_descripcion
            FROM encMovimientos 
            LEFT JOIN comunas ON encMovimientos.id_com=comunas.id_com 
            LEFT JOIN barrios ON encMovimientos.id_bar=barrios.id_bar 
            LEFT JOIN corregimientos ON encMovimientos.id_correg=corregimientos.id_correg 
            LEFT JOIN veredas ON encMovimientos.id_vere=veredas.id_vere 
            LEFT JOIN usuarios ON encMovimientos.id_usu=usuarios.id_usu 
            LEFT JOIN encuestadores ON usuarios.id_usu=encuestadores.id_usu 
            LEFT JOIN integMovimientos ON integMovimientos.id_encMovim=encMovimientos.id_encMovim  
            WHERE encMovimientos.fec_reg_encMovim>='$de' AND encMovimientos.fec_reg_encMovim<='$hasta' AND usuarios.id_usu='$id_usu' GROUP BY encMovimientos.id_encMovim ORDER BY encMovimientos.fec_reg_encMovim ASC, encMovimientos.nom_encMovim ASC";

    $result=mysqli_query($link, $query);
    
?>
 
    <table border="1">
        <tr style="background-color:#706E6E;">
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
            <th style="background-color:#706E6E;">CANTIDAD</th>
            <th style="background-color:#706E6E;">GENERO</th>
            <th style="background-color:#706E6E;">RANGO EDAD</th>
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
                <td style="text-align: center;"><?php echo $row['cant_integMovim']; ?></td>
                <td style="text-align: center;"><?php echo $row['gen_integMovim']; ?></td>
                <td style="text-align: center;"><?php echo $row['rango_descripcion']; ?></td>
                
            </tr>
            <?php
        }
        ?>
    </table>