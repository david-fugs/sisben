<?php
	header("Content-Type: text/html;charset=utf-8");
	header("Content-Type:application/vnd.ms-excel; charset=utf-8");
	//header('Content-type:application/xls'; charset=utf-8");
	header('Content-Disposition: attachment; filename=enc_fecha_familia.xls');
	date_default_timezone_set('America/Bogota');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();
    
    $id_usu 	= $_POST['id_usu'];
	$de			= $_POST['de'];
	$hasta 		= $_POST['hasta'];

	$query = "SELECT
                ec.fec_pre_encCampo, 
                ec.fec_rea_encCampo,
                ec.doc_encCampo,
                ec.nom_encCampo,
                ec.dir_encCampo,
                ec.zona_encCampo,
                c.nombre_com,
                ec.otro_bar_ver_encCampo,
                g.nombre_correg,
                veredas.nombre_vere,
                ec.proc_encCampo,
                ec.integra_encCampo,
                ec.num_ficha_encCampo,
                ec.est_fic_encCampo,
                ec.obs_encCampo,
                encuestadores.nom_enc,
                CASE ic.rango_integCampo
                    WHEN 1 THEN 'Entre 0 - 6'
                    WHEN 2 THEN 'Entre 7 - 12'
                    WHEN 3 THEN 'Entre 13 - 17'
                    WHEN 4 THEN 'Entre 18 - 28'
                    WHEN 5 THEN 'Entre 29 - 45'
                    WHEN 6 THEN 'Entre 46 - 64'
                    WHEN 7 THEN 'Mayor o igual a 65'
                    ELSE 'Desconocido'
                END AS rango_integCampo,
                ic.cant_integCampo,
                ic.gen_integCampo
            FROM 
                encCampo ec
            LEFT JOIN
                integCampo ic ON ec.id_encCampo = ic.id_encCampo 
            LEFT JOIN 
                comunas c ON ec.id_com = c.id_com 
            LEFT JOIN 
                barrios ON ec.id_bar = barrios.id_bar 
            LEFT JOIN 
                corregimientos g ON ec.id_correg = g.id_correg 
            LEFT JOIN 
                veredas ON ec.id_vere = veredas.id_vere 
            LEFT JOIN 
                usuarios ON ec.id_usu = usuarios.id_usu 
            LEFT JOIN 
                encuestadores ON usuarios.id_usu = encuestadores.id_usu 
            WHERE 
                ec.fec_rea_encCampo >= '$de' AND ec.fec_rea_encCampo <= '$hasta' 
            ORDER BY 
                ec.fec_rea_encCampo ASC, ec.nom_encCampo ASC, rango_integCampo ASC, ic.gen_integCampo ASC;
        ";

	$result=mysqli_query($link, $query);
	
?>
 
	<table border="1">
		<tr style="background-color:#706E6E;">
            <th style="background-color:#706E6E;">FECHA DE PREREGISTRO</th>
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
            <th style="background-color:#706E6E;">PROCESO</th>
            <th style="background-color:#706E6E;">INTEGRANTES</th>
            <th style="background-color:#706E6E;">No. FICHA</th>
            <th style="background-color:#706E6E;">ESTADO</th>
            <th style="background-color:#706E6E;">OBSERVACIONES</th>
            <th style="background-color:#706E6E;">ENCUESTADOR</th>
			<th style="background-color:#706E6E;">CANTIDAD</th>
			<th style="background-color:#706E6E;">GENERO</th>
			<th style="background-color:#706E6E;">RANGO EDAD</th>
		</tr>
	<?php
            // Agrega mensajes de depuración después de ejecutar la consulta
            while ($row = mysqli_fetch_assoc($result)) 
            {
                //var_dump($row);  // Imprimir datos para depuración
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
                <td style="text-align: center;"><?php echo utf8_decode($row['proc_encCampo']); ?></td>
                <td style="text-align: center;"><?php echo $row['integra_encCampo']; ?></td>
                <td style="text-align: center;"><?php echo $row['num_ficha_encCampo'].' .'; ?></td>
                <td style="text-align: center;"><?php echo $row['est_fic_encCampo'].' .'; ?></td>
                <td><?php echo utf8_decode($row['obs_encCampo']); ?></td>
                <td><?php echo utf8_decode($row['nom_enc']); ?></td>
				<td style="text-align: center;"><?php echo $row['cant_integCampo']; ?></td>
				<td style="text-align: center;"><?php echo $row['gen_integCampo']; ?></td>
				<td style="text-align: center;"><?php echo $row['rango_integCampo']; ?></td>
				
			</tr>
            <?php
        }
        ?>
    </table>