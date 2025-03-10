<?php
	
	include('../../fpdf/fpdf.php');

	include("../../conexion.php");
	$id_encCampo=$_GET['id_encCampo'];
	
	// Obtiene la fecha actual 
		
	class PDF extends FPDF
	{
		
		// Se heredan todas las funciones para hacer el Encabezado de la página
		function Header()
		{
			// Logo
			//$this->Image('images/amco.png',10,8,50);
			// Arial bold 15
			$this->SetFont('Arial','B',13);
			// Movernos a la derecha (espacio)
			$this->Cell(80);
			// Título (PROPIEDAD DESPUÉS DEL TITULO ES CONTORNO, SALTO DE LÍNEA, ALINEACIÓN)
			$this->Cell(70,5,'ENCUESTA EN CAMPO',0,1,'R');
			//$this->Cell(175	,5,'HISTORIAL VEHICULO',0,1,'R');
			//$this->Cell(195,8,'http://www.amco.gov.co/parqueautomotor',0,0,'R');
			// Salto de línea
			$this->Ln(25);
		}
		
		// Se crea la función FOOTER "Pie de página"
		function Footer()
		{
			date_default_timezone_set("America/Bogota");
			// Posición: a 1,5 cm del final
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','B',7);
			// Número de página
			$this->Cell(0,10,'FECHA Y HORA DE IMPRESION: '.$fecha=date("Y-m-d H:i:s"),0,0,'C');
			$this->Ln(3);
			$this->SetFont('Arial','I',7);
			$this->Cell(0,10,'Numero total de paginas '.$this->PageNo().'/{nb}',0,0,'C');	
		}

		function VariasLineas($cadena, $cantidad)
		{
			$this->Cell(100,0,'','B');
				while (!(strlen($cadena)==''))
				{
				    $subcadena = substr($cadena, 0, $cantidad);
				    $this->Ln();
				    $this->Cell(100,5,$subcadena,'LR',0,'L');
				    $cadena= substr($cadena,$cantidad);
				}
			$this->Cell(100,0,'','T');
		}  

	}

	$consulta = "SELECT * FROM encCampo WHERE id_encCampo='$id_encCampo'";
	$res = mysqli_query($mysqli,$consulta);
	$num_reg = mysqli_num_rows($res);
	$pdf = new PDF('L','mm','Letter');
	$pdf->AliasNbPages();
	$pdf->Addpage();
	if ($num_reg>0) 	{  
		//Limpiar (eliminar) y deshabilitar los búferes de salida.
		//ob_end_clean();
		
		$pdf->SetAuthor('Ing. Eumir Pulido de la Pava');
		$pdf->SetCreator('Ing. Eumir Pulido de la Pava');
		$pdf->SetTitle('SISBEN v1.0');

		/*$pdf->SetXY(10,20);
		$pdf->SetFillColor(254,255,254);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(140,6,utf8_decode('HISTORIAL DEL VEHÍCULO CON PLACAS No: '),1,0,'L',TRUE);
		$pdf->SetXY(125,40);
		$pdf->Cell(25,6,utf8_decode($placa),0,0,'L');*/

		$pdf->SetFont('Arial','B',12);
		$pdf->SetXY(10,20);
		$pdf->SetFillColor(232,232,232);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,6,'FECHA PRE.',1,0,'C',TRUE);
		$pdf->SetX(50);
		$pdf->Cell(30,6,'FECHA REA.',1,0,'C',TRUE);
		$pdf->SetX(80);
		$pdf->Cell(30,6,'DOCUMENTO',1,0,'C',TRUE);
		$pdf->SetX(110);
		$pdf->Cell(40,6,'ENCUESTADO',1,0,'C',TRUE);
		$pdf->SetX(150);
		$pdf->Cell(30,6,'FECHA RESO',1,0,'C',TRUE);
		$pdf->SetX(170);
		$pdf->Cell(80,6,'OBSERVACIONES',1,0,'C',TRUE);
		$pdf->SetX(250);
		$pdf->Cell(20,6,'FEC SIS',1,0,'C',TRUE);
					
		$i=1;
		$k=1;
		/*while($i<=$num_reg)
		{
			$f= mysqli_fetch_array($res);
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY(10,61);
			$pdf->Cell(40,6,utf8_decode($f['det_his']),1,0,'C');
			$pdf->SetX(50);
			$pdf->Cell(50,6,utf8_decode($f['con_his']),1,0,'C');
			$pdf->SetX(100);
			$pdf->Cell(20,6,$f['aut_his'],1,0,'C');
			$pdf->SetX(120);
			$pdf->Cell(20,6,$f['res_his'],1,0,'C');
			$pdf->SetX(140);
			$pdf->Cell(30,6,$f['fec_res_his'],1,0,'C');
			$pdf->SetX(170);
			$pdf->SetFont('Arial','',6);
			$pdf->Cell(80,6,$f['obs_his'],1,0,'C');
			$pdf->SetX(250);
			$pdf->Cell(20,6,$f['fec_his'],1,1,'C');
			$i++;

			if ($k==24)
			{
				$pdf->AliasNbPages();
				$pdf->Addpage();

				$pdf->SetFont('Arial','B',12);
				$pdf->SetXY(10,55);
				$pdf->SetFillColor(232,232,232);
				$pdf->SetFont('Arial','B',11);
				$pdf->Cell(40,6,utf8_decode('DESVINCULACIÓN'),1,0,'C',TRUE);
				$pdf->SetX(50);
				$pdf->Cell(50,6,'CONCEPTO',1,0,'C',TRUE);
				$pdf->SetX(100);
				$pdf->Cell(20,6,utf8_decode('AUTORIZ'),1,0,'C',TRUE);
				$pdf->SetX(120);
				$pdf->Cell(20,6,utf8_decode('RESOL'),1,0,'C',TRUE);
				$pdf->SetX(140);
				$pdf->Cell(30,6,'FECHA RESO',1,0,'C',TRUE);
				$pdf->SetX(170);
				$pdf->Cell(80,6,'OBSERVACIONES',1,0,'C',TRUE);
				$pdf->SetX(250);
				$pdf->Cell(20,6,'FEC SIS',1,0,'C',TRUE);
				$k=0;
			}
		$k=$k+1;
		
		}*/
		
	}

	else {

		$pdf->SetFillColor(232,232,232);
		$pdf->SetFont('Arial','B',13);
		$pdf->SetX(10);
		//$pdf->Cell(260,8,'NO HAY INFRACCIONES RELACIONADAS A ESTE NUMERO DE PLACA: '.$placa,1,1,'C',1);
		$pdf->Ln(25);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,10,'LA ANTERIOR INFORMACION SE BRINDA SIN PERJUICIO DE LAS ACTUALIZACIONES QUE',0,1,'C');
		$pdf->Cell(0,10,'SE REALIZAN CONSTANTEMENTE EN LAS BASES DE DATOS DE LA ENTIDAD',0,0,'C');

	}

	$pdf->Output();
?>