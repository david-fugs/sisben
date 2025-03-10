<?php
	header("Content-Type: text/html;charset=utf-8");
	header("Content-Type:application/vnd.ms-excel; charset=utf-8");
	//header('Content-type:application/xls'; charset=utf-8");
	header('Content-Disposition: attachment; filename=informe_encuestador_ventanilla_familia.xls');
	date_default_timezone_set('America/Bogota');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();
    
    $id_usu 	= $_POST['id_usu'];
	$de			= $_POST['de'];
	$hasta 		= $_POST['hasta'];

	$query = "SELECT 
            	encVentanilla.id_encVenta,
            	encVentanilla.fec_reg_encVenta,
            	encVentanilla.doc_encVenta,
            	encVentanilla.nom_encVenta,
            	encVentanilla.dir_encVenta,
            	encVentanilla.zona_encVenta,
            	comunas.nombre_com,
            	barrios.nombre_bar,
            	corregimientos.nombre_correg,
            	veredas.nombre_vere,
            	encVentanilla.otro_bar_ver_encVenta,
            	encVentanilla.tram_solic_encVenta,
            	encVentanilla.integra_encVenta,
            	encVentanilla.num_ficha_encVenta,
            	encVentanilla.obs_encVenta,
            	encuestadores.nom_enc,
            GROUP_CONCAT(CONCAT(integVentanilla.rango_integVenta, '|', integVentanilla.gen_integVenta, '|', integVentanilla.cant_integVenta) SEPARATOR ', ') AS integrantes_info,
            GROUP_CONCAT(integVentanilla.cant_integVenta SEPARATOR ', ') AS cant_integVenta,
            GROUP_CONCAT(integVentanilla.gen_integVenta SEPARATOR ', ') AS gen_integVenta,
            GROUP_CONCAT(CASE
                WHEN integVentanilla.rango_integVenta = 1 THEN 'Entre 0 - 6'
                WHEN integVentanilla.rango_integVenta = 2 THEN 'Entre 7 - 12'
                WHEN integVentanilla.rango_integVenta = 3 THEN 'Entre 13 - 17'
                WHEN integVentanilla.rango_integVenta = 4 THEN 'Entre 18 - 28'
                WHEN integVentanilla.rango_integVenta = 5 THEN 'Entre 29 - 45'
                WHEN integVentanilla.rango_integVenta = 6 THEN 'Entre 46 - 64'
                WHEN integVentanilla.rango_integVenta = 7 THEN 'Mayor o igual a 65'
                ELSE ''
            END SEPARATOR ', ') AS rango_descripcion
            FROM encVentanilla 
            LEFT JOIN comunas ON encVentanilla.id_com=comunas.id_com 
            LEFT JOIN barrios ON encVentanilla.id_bar=barrios.id_bar 
            LEFT JOIN corregimientos ON encVentanilla.id_correg=corregimientos.id_correg 
            LEFT JOIN veredas ON encVentanilla.id_vere=veredas.id_vere 
            LEFT JOIN usuarios ON encVentanilla.id_usu=usuarios.id_usu 
            LEFT JOIN encuestadores ON usuarios.id_usu=encuestadores.id_usu 
            LEFT JOIN integVentanilla ON integVentanilla.id_encVenta=encVentanilla.id_encVenta  
            WHERE encVentanilla.fec_reg_encVenta>='$de' AND encVentanilla.fec_reg_encVenta<='$hasta' AND usuarios.id_usu='$id_usu' GROUP BY encVentanilla.id_encVenta ORDER BY encVentanilla.fec_reg_encVenta ASC, encVentanilla.nom_encVenta ASC";

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
				<td style="text-align: center;"><?php echo $row['cant_integVenta']; ?></td>
				<td style="text-align: center;"><?php echo $row['gen_integVenta']; ?></td>
				<td style="text-align: center;"><?php echo $row['rango_descripcion']; ?></td>
				
			</tr>
            <?php
        }
        ?>
    </table>