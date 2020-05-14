<?php

require($_SERVER['DOCUMENT_ROOT'].'include/fpdf/fpdf.php'); //url productivo
//require('C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/fpdf.php');//url pc local




@$datos = json_decode(file_get_contents('php://input'), true);


//$fstart = @$datos[0]['fstart'];
//$fend = @$datos[0]['fend'];

//$header = @$datos[1]['header'];
//$body = @$datos[1]['body'];

$header = @$datos[0]['header'];
$body = @$datos[0]['body'];

$registros = count($body);

$string = "";




$time_fechas = array();
$str_fechas = array();


for ($f = 0; $f <= $registros-1; $f++){
	
	
	$explode1 = explode("/",$body[$f][1]);
	
	$fecha = $explode1[2]."-".$explode1[1]."-".$explode1[0];
	
	 array_push($time_fechas, strtotime($fecha));
	 
	 array_push($str_fechas, $fecha);
	 
}

//print_r($time_fechas);
//print_r($str_fechas);


$maximo = max($time_fechas);
$minima = min($time_fechas);

$key_max = array_search($maximo, $time_fechas);

$key_min = array_search($minima, $time_fechas);

$fecha_ini = "".$str_fechas[$key_min];
$fecha_fin = "".$str_fechas[$key_max];


	$explode2 = explode("-",$fecha_ini);
	$fecha_ini = $explode2[2]."/".$explode2[1]."/".$explode2[0];
	
	$explode3 = explode("-",$fecha_fin);
	$fecha_fin = $explode3[2]."/".$explode3[1]."/".$explode3[0];

$string = $fecha_ini." - ".$fecha_fin;


//print_r($string);
//exit();


define('STRING_PERIODO', $string);



class PDF extends FPDF{
	
	// Cabecera de página
	function Header()
	{

		 $this->SetXY(10,10);
		// $this->Cell(40,16,'',1,0,'C');
		// Logo
		$this->Image($_SERVER['DOCUMENT_ROOT']."include/fpdf/LOGO-MT-COLOR-FULL.png",10,12,80); //url productivo
		$this->Image($_SERVER['DOCUMENT_ROOT']."include/fpdf/LOGO-MT-METODO-CROWD.png",245,12,40); // url productivo
		
		//$this->Image($_SERVER['DOCUMENT_ROOT']."/upload/include/fpdf/LOGO-MT-COLOR-FULL.png",10,12,80); // url local
		//$this->Image($_SERVER['DOCUMENT_ROOT']."/upload/include/fpdf/LOGO-MT-METODO-CROWD.png",245,12,40);  // url local


		
		// Arial bold 15
		$this->SetFont('Arial','B',12);
		// Título
		//$this->SetXY(50,10);
		$this->Text(130, 20,utf8_decode('Reporte Actividad Crowd'));
		
		$this->SetFont('Arial','',10);
		$this->Text(130, 27,utf8_decode('Período: '.STRING_PERIODO));
		//$this->Cell(235,16,utf8_decode('Reporte de Gestión de Ordenes de Crowdsourcing'),0,0,'C');
		
		
		
		
		
		
		// Salto de línea
		$this->Ln(20);
	}
	
	// Pie de página
	function Footer()
	{
		$this->Line(10, 195, 285, 195);
		
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'www.mtsolutioncenter.com | Crowdsourcing for enjoy the life',0,0,'C');
		$this->Text(95,205,utf8_decode('Transformando el empleo en un jugar resolviendo acitividades'));

		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Número de página
		$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'R');
	}
	
	
}//fin de la clase PDF




$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');


$pdf->SetFont('Times','',12);
$pdf->SetFillColor(0,153, 204);
$pdf->SetTextColor(255);
$pdf->SetLineWidth(.3);


$pdf->Cell(20,7,$header[0],1,0,'C',true);
$pdf->Cell(20,7,$header[1],1,0,'C',true);
$pdf->Cell(20,7,$header[2],1,0,'C',true);
$pdf->Cell(60,7,$header[3],1,0,'C',true);
$pdf->Cell(30,7,$header[4],1,0,'C',true);
$pdf->Cell(30,7,$header[5],1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[6]),1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[7]),1,0,'C',true);
$pdf->Cell(15,7,$header[8],1,0,'C',true);
$pdf->Cell(20,7,$header[9],1,0,'C',true);
$pdf->Cell(25,7,$header[10],1,0,'C',true);
$pdf->Ln();

// Restauración de colores y fuentes
$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetFont('');

$pdf->SetFont('Times','',8);

$s = 0;


for ($i = 0; $i <= ($registros-1); $i++){
	$long_str = strlen($body[$i][3]);
		
		$pdf->Cell(20,7,$body[$i][0],1,0,'C',true);
		$pdf->Cell(20,7,$body[$i][1],1,0,'C',true);
		$pdf->Cell(20,7,$body[$i][2],1,0,'C',true);
		$pdf->Cell(60,7,utf8_decode($body[$i][3]),1,0,'L',true);
		$pdf->Cell(30,7,utf8_decode($body[$i][4]),1,0,'L',true);
		$pdf->Cell(30,7,$body[$i][5],1,0,'L',true);
		$pdf->Cell(20,7,$body[$i][6],1,0,'R',true);
		
		$pdf->Cell(20,7,$body[$i][7],1,0,'R',true);
		$pdf->Cell(15,7,$body[$i][8],1,0,'R',true);
		$pdf->Cell(20,7,$body[$i][9],1,0,'R',true);
		$pdf->Cell(25,7,$body[$i][10],1,0,'R',true);
		$pdf->Ln();
		
		$s = $s + ($body[$i][10]);
	
}


$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,7,'',1,0,'R',true);
$pdf->Cell(55,7,'Total horas crowdsourcing',1,0,'C',true);
$pdf->Cell(25,7,''.$s.'',1,0,'C',true);

$pdf->Output();


?>
