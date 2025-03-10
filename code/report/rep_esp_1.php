<?php
header("Content-Type: text/html;charset=utf-8");
header("Content-Type:application/vnd.ms-excel; charset=utf-8");
header('Content-Disposition: attachment; filename=ENC_CAMPO_FECHA_DATOS_AGRUPADOS.xls');
date_default_timezone_set('America/Bogota');

require_once('conexion.php');
$conn = new Conexion();
$link = $conn->conectarse();

$id_usu  = $_POST['id_usu'];
$de      = $_POST['de'];
$hasta   = $_POST['hasta'];

$query = "SELECT 
            ec.id_encCampo, 
            ec.fec_pre_encCampo, 
            ec.fec_rea_encCampo, 
            ec.doc_encCampo, 
            ec.nom_encCampo, 
            ec.dir_encCampo, 
            ec.zona_encCampo, 
            com.nombre_com, 
            bar.nombre_bar, 
            cor.nombre_correg, 
            ver.nombre_vere, 
            ec.num_ficha_encCampo,
            ec.est_fic_encCampo, 
            ec.proc_encCampo, 
            ec.obs_encCampo, 
            enc.nom_enc,
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    CASE
                        WHEN ic.rango_integCampo = 1 THEN 'Entre 0 - 6'
                        WHEN ic.rango_integCampo = 2 THEN 'Entre 7 - 12'
                        WHEN ic.rango_integCampo = 3 THEN 'Entre 13 - 17'
                        WHEN ic.rango_integCampo = 4 THEN 'Entre 18 - 28'
                        WHEN ic.rango_integCampo = 5 THEN 'Entre 29 - 45'
                        WHEN ic.rango_integCampo = 6 THEN 'Entre 46 - 64'
                        WHEN ic.rango_integCampo = 7 THEN 'Mayor o igual a 65'
                        ELSE ''
                    END, '|',
                    ic.gen_integCampo, '|',
                    ic.cant_integCampo
                ) SEPARATOR ', '
            ) AS integrantes_info
        FROM 
            encCampo ec
        LEFT JOIN 
            comunas com ON ec.id_com = com.id_com 
        LEFT JOIN 
            barrios bar ON ec.id_bar = bar.id_bar 
        LEFT JOIN 
            corregimientos cor ON ec.id_correg = cor.id_correg 
        LEFT JOIN 
            veredas ver ON ec.id_vere = ver.id_vere 
        LEFT JOIN 
            usuarios usu ON ec.id_usu = usu.id_usu 
        LEFT JOIN 
            encuestadores enc ON usu.id_usu = enc.id_usu 
        LEFT JOIN 
            integCampo ic ON ic.id_encCampo = ec.id_encCampo  
        WHERE 
            ec.fec_rea_encCampo >= '$de' AND ec.fec_rea_encCampo <= '$hasta'              
        GROUP BY 
            ec.id_encCampo 
        ORDER BY 
            ec.fec_rea_encCampo ASC, 
            ec.nom_encCampo ASC";

		$stmt = mysqli_prepare($link, $query);
		mysqli_stmt_bind_param($stmt, "ss", $de, $hasta);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
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
			<th style="background-color:#706E6E;">No. FICHA</th>
			<th style="background-color:#706E6E;">ESTADO</th>
			<th style="background-color:#706E6E;">INTEGRANTES</th>
			<th style="background-color:#706E6E;">PROCESO EN CAMPO</th>
			<th style="background-color:#706E6E;">OBSERVACIONES</th>
			<th style="background-color:#706E6E;">ENCUESTADOR</th>
			<th style="background-color:#706E6E;">DATOS AGRUPADOS</th>
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
				<td style="text-align: center;"><?php echo $row['integrantes_info']; ?></td>

			</tr>
            <?php
        }
        ?>
    </table>