<?php

require($_SERVER['DOCUMENT_ROOT'].'include/fpdf/fpdf.php'); //url productivo
//require('C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/fpdf.php');//url pc local



@$datos = json_decode(file_get_contents('php://input'), true);


$header = @$datos[0]['header'];
$body = @$datos[0]['body'];
$registros = count($body);

$role = @$datos[1]['role'];


class PDF extends FPDF{
	
	// Cabecera de página
	function Header()
	{

		 $this->SetXY(10,10);
		// $this->Cell(40,16,'',1,0,'C');
		// Logo
		$this->Image($_SERVER['DOCUMENT_ROOT']."include/fpdf/LOGO-MT-COLOR-FULL.png",10,12,80); //url productivo
		$this->Image($_SERVER['DOCUMENT_ROOT']."include/fpdf/LOGO-MT-METODO-CROWD.png",245,12,40); // url productivo
		
		//$this->Image("C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/LOGO-MT-COLOR-FULL.png",10,12,80); // url local
		//$this->Image("C:/Program Files/Ampps/www/mtsolution/mtsccrowdsolver/upload/include/fpdf/LOGO-MT-METODO-CROWD.png",245,12,40); //url local

		
		// Arial bold 15
		$this->SetFont('Arial','B',12);
		// Título
		//$this->SetXY(50,10);
		$this->Text(130, 20,utf8_decode('Reporte Actividad Crowd'));
		
		$this->Text(130, 25,"Periodo ".date("m-Y"));
		$this->Text(163, 25,"/ ".date("m-Y"));

		
		
		
		// Salto de línea
		$this->Ln(20);
	}
	
	// Pie de página
	function Footer()
	{
		//$this->Line(10, 195, 285, 195);
		
		$this->Line(10, 195, 289, 195);
		
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


if($role == 0){


$pdf->Cell(20,7,utf8_decode($header[0]),1,0,'C',true);
$pdf->Cell(20,7,$header[1],1,0,'C',true);
$pdf->Cell(20,7,$header[2],1,0,'C',true);
$pdf->Cell(55,7,$header[3],1,0,'C',true);
$pdf->Cell(30,7,$header[4],1,0,'C',true);
$pdf->Cell(25,7,utf8_decode($header[5]),1,0,'C',true);
$pdf->Cell(35,7,utf8_decode($header[6]),1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[7]),1,0,'C',true);
$pdf->Cell(22,7,utf8_decode($header[8]),1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[9]),1,0,'C',true);
$pdf->Cell(18,7,$header[10],1,0,'C',true);


$pdf->Ln();


// Restauración de colores y fuentes

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetFont('');

$pdf->SetFont('Times','',8);

$total = 0;

for ($i = 0; $i <= ($registros-1); $i++){
	
		
		$pdf->Cell(20,7,$body[$i][0],1,0,'C',true);
		$pdf->Cell(20,7,$body[$i][1],1,0,'C',true);
		$pdf->Cell(20,7,$body[$i][2],1,0,'C',true);
		$pdf->Cell(55,7,utf8_decode($body[$i][3]),1,0,'L',true);
		$pdf->Cell(30,7,utf8_decode($body[$i][4]),1,0,'C',true);
		$pdf->Cell(25,7,utf8_decode($body[$i][5]),1,0,'C',true);
		$pdf->Cell(35,7,utf8_decode($body[$i][6]),1,0,'C',true);
		$pdf->Cell(20,7,utf8_decode($body[$i][7]),1,0,'C',true);
		$pdf->Cell(22,7,utf8_decode($body[$i][8]),1,0,'C',true);
		$pdf->Cell(20,7,utf8_decode($body[$i][9]),1,0,'C',true);
		$pdf->Cell(18,7,utf8_decode($body[$i][10]),1,0,'C',true);
		
		
		$total = $total + ($body[$i][9]);

		$pdf->Ln();
		
	
}

$pdf->SetFont('Arial','B',10);
$pdf->Cell(202,7,'',1,0,'R',true);
$pdf->Cell(65,7,'Total horas crowdsourcing',1,0,'C',true);
$pdf->Cell(18,7,''.$total.'',1,0,'C',true);



}else{//si la peticion lleva checks ########################################################
	
$pdf->Cell(20,7,utf8_decode($header[1]),1,0,'C',true);
$pdf->Cell(20,7,$header[2],1,0,'C',true);
$pdf->Cell(20,7,$header[3],1,0,'C',true);
$pdf->Cell(95,7,$header[4],1,0,'C',true);
$pdf->Cell(40,7,$header[6],1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[7]),1,0,'C',true);
$pdf->Cell(20,7,$header[5],1,0,'C',true);
$pdf->Cell(25,7,utf8_decode($header[8]),1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[9]),1,0,'C',true);
$pdf->Cell(20,7,utf8_decode($header[10]),1,0,'C',true);


$pdf->Ln();

//275 
// Restauración de colores y fuentes

$pdf->SetFillColor(255,255,255);
$pdf->SetTextColor(0);
$pdf->SetFont('');

$pdf->SetFont('Times','',8);

$total = 0;

for ($i = 0; $i <= ($registros-1); $i++){
	
		
		$pdf->Cell(20,7,$body[$i][1],1,0,'C',true);
		$pdf->Cell(20,7,$body[$i][2],1,0,'C',true);
		$pdf->Cell(20,7,$body[$i][3],1,0,'C',true);
		$pdf->Cell(50,7,utf8_decode($body[$i][4]),1,0,'L',true);
		$pdf->Cell(40,7,utf8_decode($body[$i][6]),1,0,'C',true);				
		$pdf->Cell(20,7,utf8_decode($body[$i][7]),1,0,'C',true);
		$pdf->Cell(20,7,utf8_decode($body[$i][5]),1,0,'C',true);
		$pdf->Cell(25,7,utf8_decode($body[$i][8]),1,0,'C',true);
		$pdf->Cell(20,7,utf8_decode($body[$i][9]),1,0,'C',true);
		$pdf->Cell(20,7,utf8_decode($body[$i][10]),1,0,'C',true);
		
		$total = $total + ($body[$i][10]);

		$pdf->Ln();
		
	
}
$pdf->SetFont('Arial','B',10);
$pdf->Cell(195,7,'',1,0,'R',true);
$pdf->Cell(65,7,'Total horas crowdsourcing',1,0,'C',true);
$pdf->Cell(20,7,''.$total.'',1,0,'C',true);



	
}//end else

$pdf->Output();


?>
