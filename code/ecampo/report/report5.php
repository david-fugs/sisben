<?php
	header("Content-Type: text/html;charset=utf-8");
	header("Content-Type:application/vnd.ms-excel; charset=utf-8");
	//header('Content-type:application/xls'; charset=utf-8");
	header('Content-Disposition: attachment; filename=enc_general_familia.xls');
	date_default_timezone_set('America/Bogota');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();
    
    $id_usu 	= $_POST['id_usu'];
	$de			= $_POST['de'];
	$hasta 		= $_POST['hasta'];

	$query = "SELECT 
            encCampo.id_encCampo, 
            encCampo.fec_pre_encCampo, 
            encCampo.fec_rea_encCampo, 
            encCampo.doc_encCampo, 
            encCampo.nom_encCampo, 
            encCampo.dir_encCampo, 
            encCampo.zona_encCampo, 
            comunas.nombre_com, 
            barrios.nombre_bar, 
            corregimientos.nombre_correg, 
            veredas.nombre_vere, 
            encCampo.num_ficha_encCampo,
            encCampo.est_fic_encCampo, 
            encCampo.proc_encCampo, 
            encCampo.obs_encCampo, 
            encuestadores.nom_enc,
            GROUP_CONCAT(CONCAT(integCampo.rango_integCampo, '|', integCampo.gen_integCampo, '|', integCampo.cant_integCampo) SEPARATOR ', ') AS integrantes_info,
            GROUP_CONCAT(integCampo.cant_integCampo SEPARATOR ', ') AS cant_integCampo,
            GROUP_CONCAT(integCampo.gen_integCampo SEPARATOR ', ') AS gen_integCampo,
            GROUP_CONCAT(CASE
                WHEN integCampo.rango_integCampo = 1 THEN 'Entre 0 - 6'
                WHEN integCampo.rango_integCampo = 2 THEN 'Entre 7 - 12'
                WHEN integCampo.rango_integCampo = 3 THEN 'Entre 13 - 17'
                WHEN integCampo.rango_integCampo = 4 THEN 'Entre 18 - 28'
                WHEN integCampo.rango_integCampo = 5 THEN 'Entre 29 - 45'
                WHEN integCampo.rango_integCampo = 6 THEN 'Entre 46 - 64'
                WHEN integCampo.rango_integCampo = 7 THEN 'Mayor o igual a 65'
                ELSE ''
            END SEPARATOR ', ') AS rango_descripcion
            FROM encCampo 
            LEFT JOIN comunas ON encCampo.id_com=comunas.id_com 
            LEFT JOIN barrios ON encCampo.id_bar=barrios.id_bar 
            LEFT JOIN corregimientos ON encCampo.id_correg=corregimientos.id_correg 
            LEFT JOIN veredas ON encCampo.id_vere=veredas.id_vere 
            LEFT JOIN usuarios ON encCampo.id_usu=usuarios.id_usu 
            LEFT JOIN encuestadores ON usuarios.id_usu=encuestadores.id_usu 
            LEFT JOIN integCampo ON integCampo.id_encCampo=encCampo.id_encCampo  
            WHERE usuarios.id_usu='$id_usu' GROUP BY encCampo.id_encCampo ORDER BY encCampo.fec_rea_encCampo ASC, encCampo.nom_encCampo ASC";

	$result=mysqli_query($link, $query);
	
?>
 
	<table border="1">
		<tr style="background-color:#706E6E;">
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
			<th style="background-color:#706E6E;">No. FICHA</th>
			<th style="background-color:#706E6E;">ESTADO</th>
			<th style="background-color:#706E6E;">INTEGRANTES</th>
			<th style="background-color:#706E6E;">PROCESO EN CAMPO</th>
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
				<td style="text-align: center;"><?php echo $row['num_ficha_encCampo'].' .'; ?></td>
				<td style="text-align: center;"><?php echo utf8_decode($row['est_fic_encCampo']); ?></td>
				<td style="text-align: center;"><?php echo $row['integra_encCampo']; ?></td>
				<td style="text-align: center;"><?php echo utf8_decode($row['proc_encCampo']); ?></td>
				<td><?php echo utf8_decode($row['obs_encCampo']); ?></td>
				<td><?php echo utf8_decode($row['nom_enc']); ?></td>
				<td style="text-align: center;"><?php echo $row['cant_integCampo']; ?></td>
				<td style="text-align: center;"><?php echo $row['gen_integCampo']; ?></td>
				<td style="text-align: center;"><?php echo $row['rango_descripcion']; ?></td>
				
			</tr>
            <?php
        }
        ?>
    </table>