<?php

require($_SERVER['DOCUMENT_ROOT'].'include/fpdf/fpdf.php'); //url productivo
//require('C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/fpdf.php');//url pc local




@$datos = json_decode(file_get_contents('php://input'), true);

$inicio = @$datos[0]['fstart'];
$periodo = @$datos[0]['fselected'];
$header = @$datos[1]['header'];
$body = @$datos[1]['body'];
$registros = count($body);

$string = "";



if(empty($inicio) && empty($periodo)){
	
	$date = date("d-m-Y");
	$fx = explode("-",$date);
	
	$mes = intval($fx[1]);
	$anno = intval($fx[2]);
	
	if($mes == 1){
		$mes_anterior = 12;
		$anno_anterior = $anno - 1;
	}else{
		$mes_anterior = $mes - 1;
		$anno_anterior =  $anno;
	}
	
	$str_inicio_mes_anterior = "01-".$mes_anterior."-".$anno_anterior;
	$inicio_mes_anterior = date("d-m-Y",strtotime($str_inicio_mes_anterior));
	
	$string = $inicio_mes_anterior." / ".$date;
	
	
}elseif(empty($inicio) && $periodo == 'now'){
	
	$date = date("d-m-Y");
	$fx = explode("-",$date);
	
	$mes = intval($fx[1]);
	$anno = intval($fx[2]);
	
	if($mes == 1){
		$mes_anterior = 12;
		$anno_anterior = $anno - 1;
	}else{
		$mes_anterior = $mes - 1;
		$anno_anterior =  $anno;
	}
	
	$str_inicio_mes_anterior = "1-".$mes_anterior."-".$anno_anterior;
	$inicio_mes_anterior = date("d-m-Y",strtotime($str_inicio_mes_anterior));
	
	$string =  $inicio_mes_anterior." / ".$date;
	

}elseif(empty($inicio) && $periodo != 'now'){
	
	$date = date("d-m-Y");
	$fx = explode("-",$date);
	
	$mes = intval($fx[1]);
	$anno = intval($fx[2]);
	
	if($mes == 1){
		$mes_anterior = 12;
		$anno_anterior = $anno - 1;
	}else{
		$mes_anterior = $mes - 1;
		$anno_anterior =  $anno;
	}
	
	$str_inicio_mes_anterior = "1-".$mes_anterior."-".$anno_anterior;
	
	$inicio_mes_anterior = date("d-m-Y",strtotime($str_inicio_mes_anterior));
	
	$mod_date = strtotime($date.$periodo);

	$fechasql = date("d-m-Y",$mod_date);
	
	$string =  $inicio_mes_anterior." / ".$fechasql;
	
	
	
}elseif(!empty($inicio) && $periodo == 'now'){
	
	$date = date("d-m-Y");
	$inicio = date("d-m-Y",strtotime($inicio));
	
	$string =  $inicio." / ".$date;
	
}else{//busqueda por rango
	
	$date = date("d-m-Y");
	
	$inicio = date("d-m-Y",strtotime($inicio));
	
	$mod_date = strtotime($date.$periodo);
	
	$fechasql = date("d-m-Y",$mod_date);
	
	
	$string = $inicio." / ".$fechasql;
}

define('STRING_PERIODO', $string);



class PDF extends FPDF{
	
	// Cabecera de página
	function Header()
	{

		 $this->SetXY(10,10);
		// $this->Cell(40,16,'',1,0,'C');
		// Logo
		//$this->Image($_SERVER['DOCUMENT_ROOT']."include/fpdf/LOGO-MT-COLOR-FULL.png",10,12,80); //url productivo
		//$this->Image($_SERVER['DOCUMENT_ROOT']."include/fpdf/LOGO-MT-METODO-CROWD.png",245,12,40); // url productivo

		//$this->Image("C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/LOGO-MT-COLOR-FULL.png",10,12,80); // url local
		//$this->Image("C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/LOGO-MT-METODO-CROWD.png",245,12,40); //url local


		
		// Arial bold 15
		$this->SetFont('Arial','B',12);
		// Título
		//$this->SetXY(50,10);
		$this->Text(130, 20,utf8_decode('Reporte Actividad Crowd'));
		
		$this->SetFont('Arial','',10);
		$this->Text(128, 27,utf8_decode('Período: '.STRING_PERIODO));
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
$pdf->Cell(15,7,utf8_decode($header[8]),1,0,'C',true);
$pdf->Cell(20,7,$header[9],1,0,'C',true);
$pdf->Cell(20,7,$header[10],1,0,'C',true);
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
		$pdf->Cell(20,7,$body[$i][10],1,0,'R',true);
		$pdf->Ln();
		
		$s = $s + ($body[$i][10]);
	
}


$pdf->SetFont('Arial','B',10);
$pdf->Cell(200,7,'',1,0,'R',true);
$pdf->Cell(55,7,'Total horas crowdsourcing',1,0,'C',true);
$pdf->Cell(20,7,''.$s.'',1,0,'C',true);

$pdf->Output();


?>
