<?php
require('secure.inc.php');


$function = $_POST['func'];
$orders = json_decode($_POST['orders']);


$id_usuario = $thisclient->getId();

require_once(INCLUDE_DIR.'class.ticket.php');


if($id_usuario){
	
	if($function == "getRole"){//inicio condicion 1
		
		if($id_usuario){
			
			$sql0 = db_query("SELECT ismanager FROM ost_user WHERE id = ".$id_usuario.";");
			$consulta = db_fetch_row($sql0);
			
			echo json_encode(array("role"=>$consulta[0]));
			//echo json_encode(array("role"=>1));
		}
		
	}// fin condicion 1
	

	
	
	if($function == "updateStatus" && !empty($orders)){//inicio condicion 2
		
		
			$sql0 = db_query("SELECT ismanager FROM ost_user WHERE id = ".$id_usuario.";");
			$consulta = db_fetch_row($sql0);
			
			if($consulta[0] == 0){
				
				echo '<p class="error">Disculpe. Su usuario posee los privilegios para ejecutar la transacción</p>';
				
			}else{
				
				$cant_oc = count($orders);
				$msj = "";
				
				for ($i = 0; $i <= $cant_oc -1; $i++) {
					
					$sql1 = db_query('SELECT
						ost_ticket.number,
						ost_ticket.status_id,
						CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS crowdsourcer,
						FLOOR(ost_ticket__cdata.analisises) AS "analisis", 
						FLOOR(ost_ticket__cdata.realizaciones) AS "realizacion",
						FLOOR(ost_ticket__cdata.testinges) AS "testing",
						ost_staff.email,
						ost_ticket.ticket_id
						FROM ost_ticket 
						LEFT JOIN ost_ticket__cdata ON ost_ticket.ticket_id = ost_ticket__cdata.ticket_id
						LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
						WHERE ost_ticket.number = "'.$orders[$i].'" 
						LIMIT 1;');
					
					$registro1 = db_fetch_row($sql1);
					
					
					
					$status = $registro1[1];
					$crowdsourcer = $registro1[2];
					$analisis = $registro1[3];
					$realizacion =  $registro1[4];
					$testing =  $registro1[5];
					
					$correo = $registro1[6];
					$idoc = $registro1[7];
					
					
					print_r(json_encode(array('msj'=>$registro1)));
					exit();
					
					
					if(empty($crowdsourcer)){
						
						$msj .= '<p class="error">La orden '.$orders[$i].' no tiene crowdsolver asignado.</p>';
						
					}elseif($analisis == 0 && $realizacion == 0 && $testing == 0){
						
						$msj .= '<p class="error">La orden '.$orders[$i].' debe tener horas estimadas en análisis, realización y testing para ser aprobada.</p>';
						
					}elseif($status != 6){
						
						$msj .= '<p class="error">La orden '.$orders[$i].' no posee estatus "Recibida" para ser aprobada.</p>';
						
					}else{//si cumple con todas las condiciones
						
						$sql = db_query("UPDATE ost_ticket SET status_id = 1 WHERE number='".$orders[$i]."';");
						
						if($sql == 1){
							$msj .=  '<p class="notice">La orden '.$orders[$i].' ha sido aprobada exitosamente.</p>';
							
							
							//**************** envio de correo **********************************
							
							$headers = "MIME-Version: 1.0\r\n";
							$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
							$headers .= "Content-Transfer-Encoding: 8bit\r\n";
							$headers .= "From: MT Solution Center <desarrollo@mtsolutioncenter.com>\r\n";
							
							$htmlx = '<h3><strong>Hola '.$crowdsourcer.',</strong></h3> 
										Un cliente ha aprobado la ejecuci&oacute;n Orden de Crowdsourcing&nbsp;<a href="http://solucionesdevalor.mtsolutioncenter.com/tickets.php?id='.$idoc.'">#'.$orders[$i].'</a> 
										<br /><br />
										<hr /> Para ver o responder la Orden de Servicio, por favor <a href="http://solucionesdevalor.mtsolutioncenter.com/scp">inicie secci&oacute;n</a> en el Sistema de Soporte 
										<br /><br />
										<em>Att: Administrador de la Plataforma Solution Center</em> 
										<br /><br />
										
										<br />
										<hr />';
							
							//$correo = "jorge.gonzalez@mtsolutioncenter.com";

							ini_set("SMTP", "mevtechnology.cl");
							ini_set("smtp_port", "587");

							mail($correo, utf8_encode("Notificación MT Solution Center"), $htmlx, $headers);
							sleep(5);
							
							//********************************************************************
							
						}else{
							$msj .=  '<p class="error">Ha ocurrido un error al procesar la orden '.$orders[$i].'.</p>';
						}
						
					}
					
					
				}
				
				
				print_r(json_encode(array('msj'=>$msj)));

			}
			
		
	}//fin condicion 2
	
	
	if($function == "updateStatus" && empty($orders)){
		print_r(json_encode(array('msj'=>'<p class="error">Debe seleccionar al menos una orden</p>')));

	}
	
}//si existe el usuario
?>
