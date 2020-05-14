<?php

$org_id = $_GET["id"];

$extra_sql = "";

if($_REQUEST['start_op'] != "" && $_REQUEST['end_op'] != ""){
	
	
	
	$d1 =  strtotime($_REQUEST['start_op']);
	$f1 =  date('Y-m-d H:i:s',$d1);
	
	$d2 =  strtotime($_REQUEST['end_op']);
	$f2 =  date('Y-m-d H:i:s',$d2);
	
	//echo $f1."****".$f2;

	if( $f1 <= $f2){
		$rango = '	AND (DATE_FORMAT(ost_ticket.est_duedate, "%Y-%m-%d") BETWEEN "'.$_REQUEST['start_op'].'" AND "'.$_REQUEST['end_op'].'")';
	}else{
		$rango = "";
	}
}else{
	$rango = "";
}

//print_r($rango);

if(!empty($org_id)){
	
$consulta = db_query('SELECT ost_ticket.`ticket_id`, ost_ticket.`number` AS "orden de compra", DATE_FORMAT(ost_ticket.`created`, "%d/%m/%Y") AS "Fecha", ost_ticket_status.`name` AS "Estatus",
									ost_ticket__cdata.`subject` AS "Asunto", ost_user.`name` AS "De", CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS crowdsourcer, FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing", FLOOR(ost_ticket__cdata.`totales`) AS "Total Horas"
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									WHERE (ost_ticket_status.`id`= 1 OR ost_ticket_status.`id`= 2 OR ost_ticket_status.`id`= 3 OR ost_ticket_status.`id`= 6 ) AND `ost_user`.org_id ='.$org_id.' '.$extra_sql.' '.$rango.';');
												
												
												
												
}

?>

<div>
<form action="users.php" method="POST" name='tickets' style="padding-top:10px;">
<?php csrf_token(); ?>
<input type="hidden" name="a" value="mass_process" >
<input type="hidden" name="do" id="action" value="" >
<table id="table_org_orders_1" class="list" border="0" cellspacing="1" cellpadding="0" >
<thead>
<tr>
	<th>Orden</th>
	<th>Fecha</th>
	<th>Estatus</th>
	<th>Asunto</th>
	<th>De</th>
	<th>Crowdsolver</th>
	<th>Análisis</th>
	<th>Realización</th>
	<th>Testing</th>
	<th>Total Horas</th>
</tr>
</thead>
<tbody>
	<?php
	if(!empty($consulta)){
		
		while($fila = db_fetch_row($consulta)) {
			echo '<tr id="'.$fila[0].'" style="font-size: 10px;"><td align="center" nowrap><a class="webTicket preview" style="font-size: 10px;" title="Vista previa de la Orden de Crowdsourcing" href="tickets.php?id='.$fila[0].'" data-preview="#tickets/'.$fila[0].'/preview">'.$fila[1].'</a></td><td align="center">'.$fila[2].'</td><td>'.$fila[3].'</td><td>'.$fila[4].'</td><td>'.$fila[5].'</td><td>'.$fila[6].'</td><td align="center">'.$fila[7].'</td><td align="center">'.$fila[8].'</td><td align="center">'.$fila[9].'</td><td align="center">'.$fila[10].'</td></tr>';
		}
	}
	?>
</tbody>
</table>
</form>
</div>


 </br>

</br>
<form action="orgs.php" method="get">
<input type="hidden" name="id" value="<?php echo $org_id; ?>">
<div id="basic_search" style="background-color: #f9f9f9;border: 1px solid #DCDCDC;">
    <div style="min-height:25px;">
			
			<label>
                Fecha desde <input type="text" class="dp input-medium search-query" name="start_op">
            </label>
            <label>
                Fecha hasta <input type="text" class="dp input-medium search-query" name="end_op">
            </label>
            <button class="green button action-button muted" type="submit">Actualizar</button>
    </div>
</div>
</form>
