<?php

class ReportModel {

    const PERM_AGENTS = 'stats.agents';

    static protected $perms = array(
            self::PERM_AGENTS => array(
                'title' =>
                /* @trans */ 'Stats',
                'desc'  =>
                /* @trans */ 'Ability to view stats of other agents in allowed departments',
                'primary' => true,
            ));

    static function getPermissions() {
        return self::$perms;
    }
}

RolePermission::register(/* @trans */ 'Miscellaneous', ReportModel::getPermissions());

class OverviewReport {
    var $start;
    var $end;

    function __construct($start, $end='now') {
        $this->start = $start;
        $this->end = $end;
    }

    function getDateRange() {
        global $cfg;

        $start = $this->start ?: 'last month';
        $stop = $this->end ?: 'now';

        $start = strtotime($start);

        if (substr($stop, 0, 1) == '+')
            $stop = strftime('%Y-%m-%d ', $start) . $stop;

        $start = 'FROM_UNIXTIME('.$start.')';
        $stop = 'FROM_UNIXTIME('.strtotime($stop).')';

        return array($start, $stop);
    }

    function getPlotData() {
        list($start, $stop) = $this->getDateRange();

        # Fetch all types of events over the timeframe
        $res = db_query('SELECT DISTINCT(state) FROM '.THREAD_EVENT_TABLE
            .' WHERE timestamp BETWEEN '.$start.' AND '.$stop
            .' AND state IN ("created", "closed", "reopened", "assigned", "overdue", "transferred")'
            .' ORDER BY 1');
        $events = array();
        while ($row = db_fetch_row($res)) $events[] = $row[0];

        # TODO: Handle user => db timezone offset
        # XXX: Implement annulled column from the %ticket_event table
        $res = db_query('SELECT state, DATE_FORMAT(timestamp, \'%Y-%m-%d\'), '
                .'COUNT(DISTINCT T.id)'
            .' FROM '.THREAD_EVENT_TABLE. ' E '
            .' JOIN '.THREAD_TABLE. ' T
                ON (T.id = E.thread_id AND T.object_type = "T") '
            .' WHERE E.timestamp BETWEEN '.$start.' AND '.$stop
            .' AND NOT annulled'
            .' AND E.state IN ("created", "closed", "reopened", "assigned", "overdue", "transferred")'
            .' GROUP BY E.state, DATE_FORMAT(E.timestamp, \'%Y-%m-%d\')'
            .' ORDER BY 2, 1');
        # Initialize array of plot values
        $plots = array();
        foreach ($events as $e) { $plots[$e] = array(); }

        $time = null; $times = array();
        # Iterate over result set, adding zeros for missing ticket events
        $slots = array();
        while ($row = db_fetch_row($res)) {
            $row_time = strtotime($row[1]);
            if ($time != $row_time) {
                # New time (and not the first), figure out which events did
                # not have any tickets associated for this time slot
                if ($time !== null) {
                    # Not the first record -- add zeros all the arrays that
                    # did not have at least one entry for the timeframe
                    foreach (array_diff($events, $slots) as $slot)
                        $plots[$slot][] = 0;
                }
                $slots = array();
                $times[] = $time = $row_time;
            }
            # Keep track of states for this timeframe
            $slots[] = $row[0];
            $plots[$row[0]][] = (int)$row[2];
        }
        foreach (array_diff($events, $slots) as $slot)
            $plots[$slot][] = 0;

        return array("times" => $times, "plots" => $plots, "events" => $events);
    }

    function enumTabularGroups() {
        return array("dept"=>__("Department"), "topic"=>__("Topics"),
            # XXX: This will be relative to permissions based on the
            # logged-in-staff. For basic staff, this will be 'My Stats'
           // "staff"=>__("Agent")); comentado por hdandrea 26-02-18 **************
           
           
           //*** inicio ******** agregado por hdandrea 26-02-18 *******************************
            "staff"=>__("Agent"),"opened"=>__("Opened"),"closed"=>__("Aprobadas y Cerradas"));
          //**** fin **************************************************************************
    }

    function getTabularData($group='dept') {
        global $thisstaff;

        list($start, $stop) = $this->getDateRange();
        $times = Ticket::objects()
            ->constrain(array(
                'thread__entries' => array(
                    'thread__entries__type' => 'R'
                ),
            ))
            ->aggregate(array(
                'ServiceTime' => SqlAggregate::AVG(SqlFunction::DATEDIFF(
                    new SqlField('closed'), new SqlField('created')
                )),
                'ResponseTime' => SqlAggregate::AVG(SqlFunction::DATEDIFF(
                    new SqlField('thread__entries__created'), new SqlField('thread__entries__parent__created')
                )),
            ));

        $stats = Ticket::objects()
            ->constrain(array(
                'thread__events' => array(
                    'thread__events__annulled' => 0,
                    'thread__events__timestamp__range' => array($start, $stop),
                ),
            ))
            ->aggregate(array(
                'Opened' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(new Q(array('thread__events__state' => 'created')), 1)
                ),
                'Assigned' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(new Q(array('thread__events__state' => 'assigned')), 1)
                ),
                'Overdue' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(new Q(array('thread__events__state' => 'overdue')), 1)
                ),
                'Closed' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(new Q(array('thread__events__state' => 'closed')), 1)
                ),
                'Reopened' => SqlAggregate::COUNT(
                    SqlCase::N()
                        ->when(new Q(array('thread__events__state' => 'reopened')), 1)
                ),
            ));
        
        $pestanna = '';

        switch ($group) {
        case 'dept':
            $headers = array(__('Department'));
            $header = function($row) { return Dept::getLocalNameById($row['dept_id'], $row['dept__name']); };
            $pk = 'dept_id';
            $stats = $stats
                ->filter(array('dept_id__in' => $thisstaff->getDepts()))
                ->values('dept__id', 'dept__name');
            $times = $times
                ->filter(array('dept_id__in' => $thisstaff->getDepts()))
                ->values('dept__id');
            break;
        case 'topic':
            $headers = array(__('Help Topic'));
            $header = function($row) { return Topic::getLocalNameById($row['topic_id'], $row['topic__topic']); };
            $pk = 'topic_id';
            $stats = $stats
                ->values('topic_id', 'topic__topic')
                ->filter(array('topic_id__gt' => 0));
            $times = $times
                ->values('topic_id')
                ->filter(array('topic_id__gt' => 0));
            break;
        case 'staff':
            $headers = array(__('Agent'));
            $header = function($row) { return new AgentsName(array(
                'first' => $row['staff__firstname'], 'last' => $row['staff__lastname'])); };
            $pk = 'staff_id';
            $stats = $stats->values('staff_id', 'staff__firstname', 'staff__lastname');
            $times = $times->values('staff_id');
            $depts = $thisstaff->getManagedDepartments();
            if ($thisstaff->hasPerm(ReportModel::PERM_AGENTS))
                $depts = array_merge($depts, $thisstaff->getDepts());
            $Q = Q::any(array(
                'staff_id' => $thisstaff->getId(),
            ));
            if ($depts)
                $Q->add(array('dept_id__in' => $depts));
            $stats = $stats->filter(array('staff_id__gt'=>0))->filter($Q);
            $times = $times->filter(array('staff_id__gt'=>0))->filter($Q);
            break;
        
        //**** inicio ******** agregado por hdandrea 26-02-18 *********************************************************    
        case 'opened':
            $headers = array('OC '.__('Opened'));
            $header = function($row) { return new AgentsName(array('first' => $row['staff__firstname'], 'last' => $row['staff__lastname'])); };
            $pestanna = 'O';
            break;
            
        case 'closed':
            $headers = array(__('Closed'));
            $header = function($row) { return new AgentsName(array('first' => $row['staff__firstname'], 'last' => $row['staff__lastname'])); };
            $pestanna = 'C';
            break;
        //*** fin ********************************************************************************************************
        default:
            # XXX: Die if $group not in $groups
        }

        $timings = array();
        foreach ($times as $T) {
            $timings[$T[$pk]] = $T;
        }

        $rows = array();
        foreach ($stats as $R) {
            $T = $timings[$R[$pk]];
            $rows[] = array($header($R), $R['Opened'], $R['Assigned'],
                $R['Overdue'], $R['Closed'], $R['Reopened'],
                number_format($T['ServiceTime'], 1),
                number_format($T['ResponseTime'], 1));
        }
        
        
        //inicio ************  agregado por hdandrea 26-08-18 ****************************************
        
        
        
			$inicio = @$_POST['start'];
			
			$periodo = @$_POST['period'];
			
			$extra_sql = "";
			
			//print_r($_POST['start']."/".$_POST['period']);
			
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
				
				$str_inicio_mes_anterior = "1-".$mes_anterior."-".$anno_anterior;
				
				$inicio_mes_anterior = date("Y-m-d G:i:s",strtotime($str_inicio_mes_anterior));
				
				$extra_sql = 'AND ost_ticket.`created` >= "'.$inicio_mes_anterior.'" ORDER BY ost_ticket.created ASC';
				
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
				
				$inicio_mes_anterior = date("Y-m-d G:i:s",strtotime($str_inicio_mes_anterior));
				
				$extra_sql = 'AND ost_ticket.`created` >= "'.$inicio_mes_anterior.'" ORDER BY ost_ticket.created ASC';
				
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
				
				$inicio_mes_anterior = date("Y-m-d G:i:s",strtotime($str_inicio_mes_anterior));
				
				$mod_date = strtotime($date.$periodo);
				$fechasql = date("Y-m-d G:i:s",$mod_date);
				
				$extra_sql = 'AND ost_ticket.`created` >= "'.$inicio_mes_anterior.'" AND ost_ticket.`created` <= "'.$fechasql.'" ORDER BY ost_ticket.created DESC';
				
			}elseif(!empty($inicio) && $periodo == 'now'){
				
				$inicio = date("Y-m-d G:i:s",strtotime($inicio));
				
				$extra_sql = 'AND ost_ticket.`created` >= "'.$inicio.'" ORDER BY ost_ticket.created ASC';
				
			}else{//busqueda por rango
				
				$date = date("d-m-Y");
				$inicio = date("Y-m-d G:i:s",strtotime($inicio));
				
				$mod_date = strtotime($date.$periodo);
				$fechasql = date("Y-m-d G:i:s",$mod_date);
				
				$extra_sql = 'AND ost_ticket.`created` >= "'.$inicio.'" AND ost_ticket.`created` <= "'.$fechasql.'" ORDER BY ost_ticket.created DESC';
			}
			
        if($pestanna == 'C'){
			
			/*$consulta = db_query('SELECT ost_ticket.`ticket_id`, ost_ticket.`number` AS "orden de compra",  DATE_FORMAT(ost_ticket.`created`, "%d/%m/%Y") AS "Fecha", ost_ticket_status.`name` AS "Estatus",
									ost_ticket__cdata.`subject` AS "Asunto", ost_user.`name` AS "De", CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS crowdsourcer, FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing", FLOOR(ost_ticket__cdata.`totales`) AS "Total Horas"
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									WHERE (ost_ticket_status.`id`= 7 OR ost_ticket_status.`id`= 8) '.$extra_sql.';');*/
									
		/*		$consulta = db_query('SELECT 
									ost_ticket.`ticket_id`, ost_ticket.`number` AS "orden de compra", 
									DATE_FORMAT(ost_ticket.`lastupdate`, "%d/%m/%Y") AS "Fecha", 
									ost_ticket_status.`name` AS "Estatus",				
									ost_ticket__cdata.`subject` AS "Asunto", ost_user.`name` AS "De", 
									CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS crowdsourcer, 
									FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", 
									FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",									
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing", 
									FLOOR(ost_ticket__cdata.`totales`) AS "Total Horas"			
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`			
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 		
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									WHERE (ost_ticket_status.`id`= 7 OR ost_ticket_status.`id`= 8) '.$extra_sql.';');	*/
									
				$consulta = db_query('SELECT 
									ost_ticket.`ticket_id`, ost_ticket.`number` AS "orden de compra", 
									DATE_FORMAT(ost_ticket.`lastupdate`, "%d/%m/%Y") AS "Fecha", 
									ost_ticket_status.`name` AS "Estatus",				
									ost_ticket__cdata.`subject` AS "Asunto", ost_user.`name` AS "De", 
									(SELECT data FROM ost_thread_event WHERE ost_thread_event.thread_id = ost_thread.id AND ost_thread_event.state = "assigned" order by id desc limit 1) AS crowdsourcer, 
									FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", 
									FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",									
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing", 
									FLOOR(ost_ticket__cdata.`totales`) AS "Total Horas",
									CASE WHEN FLOOR(ost_ticket__cdata.`totales`)>0
                                    THEN
                                    DATE_FORMAT(ost_ticket.est_duedate, "%d/%m/%Y") 
                                    ELSE "En Estimacion" end AS "Fecha de Entrega"			
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`			
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 		
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									
									left join ost_thread on ost_ticket.ticket_id = ost_thread.object_id
									
									WHERE (ost_ticket_status.`id`= 7 OR ost_ticket_status.`id`= 8) '.$extra_sql.';');					
		    
												
			$registros = array();
			
			;
			
			while($fila = db_fetch_row($consulta)) {
				
				
				
				//if($fila[6] == null){
					
				//	$fila[6] = 'NO ASIGNADO';
					
				//}else{
				
				if($fila[6] != null){
					
					$obj = json_decode($fila[6]);
					
					$fila[6] = (@$obj->staff[1]);
					
				};
				
				//print_r($fila);
				
				$return[] = $fila;
			}
			
			return array("columns" => array_merge(array(__('Orden'),__('Fecha'),__('Estatus'),__('Actividad'),__('De'),__('Crowdsolver'),__('Análisis'),__('Realización'),__('Testing'),__('Total Horas'),__('Fecha Ent.'))),
						 "data" => $return);
		
		}elseif($pestanna == 'O'){
			
			$consulta = db_query('SELECT ost_ticket.`ticket_id`, ost_ticket.`number` AS "orden de compra", DATE_FORMAT(ost_ticket.`created`, "%d/%m/%Y") AS "Fecha", ost_ticket_status.`name` AS "Estatus",
									ost_ticket__cdata.`subject` AS "Asunto", ost_user.`name` AS "De", CONCAT(ost_staff.firstname," ",ost_staff.lastname) AS crowdsourcer, FLOOR(ost_ticket__cdata.`analisises`) AS "analisis", FLOOR(ost_ticket__cdata.`realizaciones`) AS "realizacion",
									FLOOR(ost_ticket__cdata.`testinges`) AS "testing", FLOOR(ost_ticket__cdata.`totales`) AS "Total Horas",
									CASE WHEN FLOOR(ost_ticket__cdata.`totales`)>0
                                    THEN
                                    DATE_FORMAT(ost_ticket.est_duedate, "%d/%m/%Y") 
                                    ELSE "En Estimacion" end AS "Fecha de Entrega"
									FROM ost_ticket LEFT JOIN ost_ticket_status ON ost_ticket.`status_id`= ost_ticket_status.`id`
									LEFT JOIN ost_ticket__cdata ON ost_ticket.`ticket_id` = ost_ticket__cdata.`ticket_id`
									LEFT JOIN ost_user ON ost_ticket.`user_id`=ost_user.`id` 
									LEFT JOIN ost_staff ON ost_ticket.staff_id = ost_staff.staff_id 
									WHERE (ost_ticket_status.`id`= 1 OR ost_ticket_status.`id`= 2 OR ost_ticket_status.`id`= 3 OR ost_ticket_status.`id`= 6 ) '.$extra_sql.';');
												
			$registros = array();
			
			;
			
			while($fila = db_fetch_row($consulta)) {
				$return[] = $fila;
			}
			
			return array("columns" => array_merge(array(__('Orden'),__('Fecha'),__('Estatus'),__('Actividad'),__('De'),__('Crowdsolver'),__('Análisis'),__('Realización'),__('Testing'),__('Total Horas'),__('Fecha Ent.'))),
						 "data" => $return);
		}else{
			
			return array("columns" => array_merge($headers,
							array(__('Opened'),__('Assigned'),__('Overdue'),__('Closed'),__('Reopened'),
								  __('Service Time'),__('Response Time'))),
						 "data" => $rows);
        }
        //****** fin **************************************************************************************************************
        
        /*  comentado por hdandrea 26-02-18
			return array("columns" => array_merge($headers,
							array(__('Opened'),__('Assigned'),__('Overdue'),__('Closed'),__('Reopened'),
								  __('Service Time'),__('Response Time'))),
						 "data" => $rows);*/
        
    }
}
