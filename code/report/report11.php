<?php
	header("Content-Type: text/html;charset=utf-8");
	header("Content-Type:application/vnd.ms-excel; charset=utf-8");
	//header('Content-type:application/xls'; charset=utf-8");
	header('Content-Disposition: attachment; filename=enc_ventanilla_fecha_familia.xls');
	date_default_timezone_set('America/Bogota');

	require_once('conexion.php');
	$conn=new Conexion();
	$link = $conn->conectarse();
    
    $id_usu 	= $_POST['id_usu'];
	$de			= $_POST['de'];
	$hasta 		= $_POST['hasta'];

	$query = "SELECT 
    ev.fec_reg_encVenta,
    ev.doc_encVenta,
    ev.nom_encVenta,
    ev.dir_encVenta,
    ev.zona_encVenta,
    c.nombre_com,
    ev.otro_bar_ver_encVenta,
    g.nombre_correg,
    veredas.nombre_vere,
    ev.tram_solic_encVenta,
    ev.integra_encVenta,
    ev.num_ficha_encVenta,
    ev.obs_encVenta,
    encuestadores.nom_enc,
    CASE iv.rango_integVenta
        WHEN 1 THEN 'Entre 0 - 6'
        WHEN 2 THEN 'Entre 7 - 12'
        WHEN 3 THEN 'Entre 13 - 17'
        WHEN 4 THEN 'Entre 18 - 28'
        WHEN 5 THEN 'Entre 29 - 45'
        WHEN 6 THEN 'Entre 46 - 64'
        WHEN 7 THEN 'Mayor o igual a 65'
        ELSE 'Desconocido'
    END AS rango_integVenta,
    iv.cant_integVenta,
    iv.gen_integVenta
FROM 
    encVentanilla ev
LEFT JOIN
    integVentanilla iv ON ev.id_encVenta = iv.id_encVenta 
LEFT JOIN 
    comunas c ON ev.id_com = c.id_com 
LEFT JOIN 
    barrios ON ev.id_bar = barrios.id_bar 
LEFT JOIN 
    corregimientos g ON ev.id_correg = g.id_correg 
LEFT JOIN 
    veredas ON ev.id_vere = veredas.id_vere 
LEFT JOIN 
    usuarios ON ev.id_usu = usuarios.id_usu 
LEFT JOIN 
    encuestadores ON usuarios.id_usu = encuestadores.id_usu 
WHERE 
    ev.fec_reg_encVenta >= '$de' AND ev.fec_reg_encVenta <= '$hasta' 
ORDER BY 
    ev.fec_reg_encVenta ASC, ev.nom_encVenta ASC, rango_integVenta ASC, iv.gen_integVenta ASC;

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
				<td style="text-align: center;"><?php echo $row['rango_integVenta']; ?></td>
				
			</tr>
            <?php
        }
        ?>
    </table>