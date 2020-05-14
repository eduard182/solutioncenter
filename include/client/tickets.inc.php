<?php
if(!defined('OSTCLIENTINC') || !is_object($thisclient) || !$thisclient->isValid()) die('Access Denied');
?>
<div class="search well">
<div class="flush-left">
<form action="tickets.php" method="get" id="ticketSearchForm">
<div  style="background-position:left"><a style="color:#666666; font-size: 15px;" href="<?php echo ROOT_PATH; ?>open.php"><?php echo __('Open a New Ticket'); ?></a></div>

  <div class="pull-right">
    <?php echo __('Help Topic'); ?>
    <select name="topic_id" class="nowarn" onchange="javascript: this.form.submit(); ">
      <option value="">&mdash; <?php echo __('All Help Topics');?> &mdash;</option>
      <?php foreach (Topic::getHelpTopics(true) as $id=>$name) {
        $count = $thisclient->getNumTopicTickets($id);
        if ($count == 0)
            continue;
?>
      <option value="<?php echo $id; ?>"i
            <?php if ($settings['topic_id'] == $id) echo 'selected="selected"'; ?>
            ><?php echo sprintf('%s (%d)', Format::htmlchars($name),
                $thisclient->getNumTopicTickets($id)); ?></option>
      <?php } ?>
    </select>
    <input type="hidden" name="a"  value="search" />
    <input type="search" name="keywords" size="30" value="<?php echo Format::htmlchars($settings['keywords']); ?>" />
    <input name="submit" type="submit" value="<?php echo __('Search');?>" />
    </div>
</form>
<div class="clear"></div>
</div>

<?php if ($settings['keywords'] || $settings['topic_id'] || $_REQUEST['sort']) { ?>
<div style="margin-top:10px"><strong><a href="?clear" style="color:#777"><i class="material-icons">highlight_off</i> <?php echo __('Clear all filters and sort'); ?></a></strong></div>
<?php } ?>

</div>


        <div style="float:left;margin:1px 0 2px 0;">

        </div>
        
        <div style="float:right;margin:1px 0 2px 0;">
        <a class="tickets" href="<?php echo Format::htmlchars($_SERVER['REQUEST_URI']); ?>">
            Actualizar
            <svg viewBox="0 0 24 24">
                <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
            </svg>
        </a>
        </div>
        <div class="clear"></div>


        <?php
// *************** agregado por hdandre 23-03-18


if($_REQUEST['a'] == "search" && $_REQUEST['keywords'] == "" && $_REQUEST['topic_id'] == ""){
	
	$extra_sql = "";
	
}elseif($_REQUEST['a'] == "search" && $_REQUEST['keywords'] == "" && $_REQUEST['topic_id'] != ""){
	
	$extra_sql = " AND ost_ticket.topic_id = ".$_REQUEST['topic_id'];
	
}elseif($_REQUEST['a'] == "search" && $_REQUEST['keywords'] != "" && $_REQUEST['topic_id'] == ""){
	
	$extra_sql = " AND ( ost_ticket.`number` LIKE '%".$_REQUEST['keywords']."%' OR  ost_ticket_status.`name` LIKE '%".$_REQUEST['keywords']."%' OR ost_ticket__cdata.`subject` LIKE '%".$_REQUEST['keywords']."%' OR ost_staff.firstname LIKE '%".$_REQUEST['keywords']."%' OR ost_staff.lastname LIKE '%".$_REQUEST['keywords']."%')";
	
}elseif($_REQUEST['a'] == "search" && $_REQUEST['keywords'] != "" && $_REQUEST['topic_id'] != ""){
	
	$extra_sql = " AND ost_ticket.topic_id = ".$_REQUEST['topic_id']." AND ( ost_ticket.`number` LIKE '%".$_REQUEST['keywords']."%' OR  ost_ticket_status.`name` LIKE '%".$_REQUEST['keywords']."%' OR ost_ticket__cdata.`subject` LIKE '%".$_REQUEST['keywords']."%' OR ost_staff.firstname LIKE '%".$_REQUEST['keywords']."%' OR ost_staff.lastname LIKE '%".$_REQUEST['keywords']."%')";
	
}else{
	
	$extra_sql = "";
	
}


if($_REQUEST['min'] != "" && $_REQUEST['max'] != ""){
	
	
	
	$d1 =  strtotime($_REQUEST['min']);
	$f1 =  date('Y-m-d H:i:s',$d1);
	
	$d2 =  strtotime($_REQUEST['max']);
	$f2 =  date('Y-m-d H:i:s',$d2);
	
	//echo $f1."****".$f2;

	if( $f1 <= $f2){
		$rango = '	AND (DATE_FORMAT(ost_ticket.lastupdate, "%d/%m/%Y") BETWEEN "'.$_REQUEST['min'].'" AND "'.$_REQUEST['max'].'")';
	}else{
		$rango = "";
	}
}else{
	$rango = "";
}


//verificar si es manager
$sql0 = db_query("SELECT ismanager,org_id FROM ost_user WHERE id = ".$thisclient->getId().";");
$consulta = db_fetch_row($sql0);

$manager = $consulta[0];
$organizacion = $consulta[1];

if($manager == 0){//si no es manager de la cuenta



$consulta_abiertas = db_query('SELECT 
									ost_ticket.`ticket_id`, 
									ost_ticket.`number` AS "orden de compra", 
									DATE_FORMAT(ost_ticket.`created`, "%d/%m/%Y") AS "Fecha", 
									ost_ticket_status.`name` AS "Estatus",
									ost_ticket__cdata.`subject` AS "Asunto", 
									ost_user.`name` AS "De", 
									CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS "crowdsourcer",
									CASE WHEN FLOOR(ost_ticket__cdata.`totales`)>0
                                    THEN
                                    DATE_FORMAT(ost_ticket.est_duedate, "%d/%m/%Y") 
                                    ELSE "En Estimacion" end AS "fechafin",
									FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", 
									FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing",
									FLOOR(ost_ticket__cdata.`totales`) AS "thoras"
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									WHERE (ost_ticket_status.`id`= 1 OR ost_ticket_status.`id`= 2 OR ost_ticket_status.`id`= 3 OR ost_ticket_status.`id`= 6 ) AND ost_ticket.`user_id`='.$thisclient->getId().' '.$extra_sql.' '.$rango.';');


$consulta_cerradas = db_query('SELECT 
						ost_ticket.`ticket_id`, 
 						ost_ticket.`number` AS "orden de compra",  
						DATE_FORMAT(ost_ticket.`lastupdate`, "%d/%m/%Y") AS "Fecha",
						ost_ticket_status.`name` AS "Estatus",
						ost_ticket__cdata.`subject` AS "Asunto", 
						ost_user.`name` AS "De", 
						CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS "crowdsourcer",
						CASE WHEN FLOOR(ost_ticket__cdata.`totales`)>0
						THEN
						DATE_FORMAT(ost_ticket.est_duedate, "%d/%m/%Y") 
						ELSE "En Estimacion" end as "fechafin",
						FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", 
						FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
						FLOOR(ost_ticket__cdata.`testinges`) AS "testing",
						FLOOR(ost_ticket__cdata.`totales`) AS "thoras"
						FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
						LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
						LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
						LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
						WHERE (ost_ticket_status.`id`= 7 )AND ost_ticket.`user_id`='.$thisclient->getId().' '.$extra_sql.' '.$rango.';');




}

if($manager == 1){//si es manager de la cuenta
	
	
$consulta_abiertas = db_query('SELECT 
									ost_ticket.`ticket_id`,
									ost_ticket.`number` AS "orden de compra", 
									DATE_FORMAT(ost_ticket.`created`, "%d/%m/%Y") AS "Fecha", 
									ost_ticket_status.`name` AS "Estatus",
									ost_ticket__cdata.`subject` AS "Asunto", 
									ost_user.`name` AS "De", 
									CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS "crowdsourcer",
									CASE WHEN FLOOR(ost_ticket__cdata.`totales`)>0
                                    THEN
                                    DATE_FORMAT(ost_ticket.est_duedate, "%d/%m/%Y") 
                                    ELSE "En Estimacion" end as "fechafin",
									FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", 
									FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing",
									FLOOR(ost_ticket__cdata.`totales`) AS "thoras"
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									WHERE (ost_ticket_status.`id`= 1 OR ost_ticket_status.`id`= 2 OR ost_ticket_status.`id`= 3 OR ost_ticket_status.`id`= 6 ) AND `ost_user`.org_id ='.$organizacion.' '.$extra_sql.' '.$rango.';');


$consulta_cerradas = db_query('SELECT 
						ost_ticket.`ticket_id`, 
						ost_ticket.`number` AS "orden de compra",  
                        DATE_FORMAT(ost_ticket.`lastupdate`, "%d/%m/%Y") AS "Fecha", 
                        ost_ticket_status.`name` AS "Estatus",
						ost_ticket__cdata.`subject` AS "Asunto", 
						ost_user.`name` AS "De", 
						CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS "crowdsourcer",
						CASE WHEN FLOOR(ost_ticket__cdata.`totales`)>0
						THEN
						DATE_FORMAT(ost_ticket.est_duedate, "%d/%m/%Y") 
						ELSE "En Estimacion" end as "fechafin",
						FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", 
						FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
						FLOOR(ost_ticket__cdata.`testinges`) AS "testing",
						FLOOR(ost_ticket__cdata.`totales`) AS "thoras"
						FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
						LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
						LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
						LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id
						WHERE (ost_ticket_status.`id`= 7) AND `ost_user`.org_id ='.$organizacion.' '.$extra_sql.' '.$rango.';');
	
	
}

?>

        <div class="clear"></div>
<ul class="clean tabs">
    <li id="opened" class="active"><a href="#">Ordenes de Crowdsourcing Abiertas</a></li>
    <li id="closed" class="inactive"><a href="#">Ordenes de Crowdsourcing Aprobadas</a></li>
</ul>


<div class="tab_content" id="cont_opened">
	


  
  </br>
  
  
					<?php
					echo '
					<table id="ticketTable2" class="dashboard-stats table" style="width:100%;font-size: 11px; color:#666666">
						<thead>
						<tr>
							<th></th>
							<th>Orden</th>
							<th>Creado</th>
							<th>Estatus</th>
							<th>Actividad</th>
							<th>Crowdsolver</th>
							<th>Cliente</th>
							<th>Fecha Entrega Est.</th>
							<th>Análisis</th>
							<th>Realización</th>
							<th>Testing</th>
							<th>Total H</th>
							
						</tr>
						</thead>
						<tbody>';
					while($fila1 = db_fetch_row($consulta_abiertas)) {
						//print_r($fila1);
						
						
						echo ' <tr style="font-size: 10px;"><td></td><td ><a style="color:#666666" href=?id='.$fila1[0].'>'.$fila1[1].' </a></td><td>'.$fila1[2].'</td><td>'.$fila1[3].'</td><td>'.$fila1[4].'</td><td>'.$fila1[6].'</td><td>'.$fila1[5].'</td><td style="text-align:center;">'.$fila1[7].'</td><td style="text-align:center;">'.$fila1[8].'</td><td style="text-align:center;">'.$fila1[9].'</td><td style="text-align:center;">'.$fila1[10].'</td><td style="text-align:center;">'.$fila1[11].'</td></tr>';
						
					}
					echo '</tbody>
					</table>';
					?>
					
</div>

<div class="tab_content hidden" id="cont_closed">
	 </br>
  
					<?php
					echo '
					<table id="ticketTable3" class="dashboard-stats table" style="width:100%;font-size: 11px;color:#666666">
						<thead>
						<tr>
							<th>Orden</th>
							<th>Creado</th>
							<th>Estatus</th>
							<th>Actividad</th>
							<th>Crowdsolver</th>
							<th>Cliente</th>
							<th>Fecha Entrega Est.</th>
							<th>Análisis</th>
							<th>Realización</th>
							<th>Testing</th>
							<th>Total H</th>
						</tr>
						</thead>
						<tbody>';
					while($fila2 = db_fetch_row($consulta_cerradas)) {
						//print_r($fila2);
						
						
						echo ' <tr style="font-size: 10px;"><td><a style="color:#666666" href=?id='.$fila2[0].'>'.$fila2[1].'</a></td><td>'.$fila2[2].'</td><td>'.$fila2[3].'</td><td >'.$fila2[4].'</td><td>'.$fila2[6].'</td><td>'.$fila2[5].'</td><td>'.$fila2[7].'</td><td style="text-align:center;">'.$fila2[8].'</td><td style="text-align:center;">'.$fila2[9].'</td><td style="text-align:center;">'.$fila2[10].'</td><td style="text-align:center;">'.$fila2[11].'</td></tr>';
						
						
					}
					echo '</tbody>
					</table>';
					?>
</div>


 </br>
  
<div class="search well" style="background-color:#eef3f8;border: 1px solid #aaa; color:#666666">
	<div class="flush-left">
		<form action="tickets.php" method="get" id="ticketSearchForm">
			<label>Fecha desde</label>
			<input name="min" id="min" type="text" style="width: 100px;background: #fff!important;1px solid #A5A4A4!important;" readonly>
			<label>Fecha hasta</label>
			<input name="max" id="max" type="text"  style="width: 100px;background: #fff!important;1px solid #A5A4A4!important;" readonly>
			<button class="green button action-button muted" type="submit">Actualizar</button>
		</form>
	</div>
</div>
