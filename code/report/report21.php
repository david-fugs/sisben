<?php
	header("Content-Type: text/html;charset=utf-8");
	header("Content-Type:application/vnd.ms-excel; charset=utf-8");
	//header('Content-type:application/xls'; charset=utf-8");
	header('Content-Disposition: attachment; filename=enc_movimientos_fecha_familia.xls');
	date_default_timezone_set('America/Bogota');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();
    
    $id_usu 	= $_POST['id_usu'];
	$de			= $_POST['de'];
	$hasta 		= $_POST['hasta'];

	$query = "SELECT 
                em.fec_reg_encMovim,
                em.doc_encMovim,
                em.nom_encMovim,
                em.dir_encMovim,
                em.zona_encMovim,
                c.nombre_com,
                em.otro_bar_ver_encMovim,
                g.nombre_correg,
                veredas.nombre_vere,
                em.tram_solic_encMovim,
                em.integra_encMovim,
                em.num_ficha_encMovim,
                em.obs_encMovim,
                encuestadores.nom_enc,
                CASE im.rango_integMovim
                    WHEN 1 THEN 'Entre 0 - 6'
                    WHEN 2 THEN 'Entre 7 - 12'
                    WHEN 3 THEN 'Entre 13 - 17'
                    WHEN 4 THEN 'Entre 18 - 28'
                    WHEN 5 THEN 'Entre 29 - 45'
                    WHEN 6 THEN 'Entre 46 - 64'
                    WHEN 7 THEN 'Mayor o igual a 65'
                    ELSE 'Desconocido'
                END AS rango_integMovim,
                im.cant_integMovim,
                im.gen_integMovim
            FROM 
                encMovimientos em
            LEFT JOIN
                integMovimientos im ON em.id_encMovim = im.id_encMovim 
            LEFT JOIN 
                comunas c ON em.id_com = c.id_com 
            LEFT JOIN 
                barrios ON em.id_bar = barrios.id_bar 
            LEFT JOIN 
                corregimientos g ON em.id_correg = g.id_correg 
            LEFT JOIN 
                veredas ON em.id_vere = veredas.id_vere 
            LEFT JOIN 
                usuarios ON em.id_usu = usuarios.id_usu 
            LEFT JOIN 
                encuestadores ON usuarios.id_usu = encuestadores.id_usu 
            WHERE 
                em.fec_reg_encMovim >= '$de' AND em.fec_reg_encMovim <= '$hasta' 
            ORDER BY 
                em.fec_reg_encMovim ASC, em.nom_encMovim ASC, rango_integMovim ASC, im.gen_integMovim ASC;
        ";

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
            // Agrega mensajes de depuración después de ejecutar la consulta
            while ($row = mysqli_fetch_assoc($result)) 
            {
                //var_dump($row);  // Imprimir datos para depuración
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
				<td style="text-align: center;"><?php echo $row['rango_integMovim']; ?></td>
				
			</tr>
            <?php
        }
        ?>
    </table>