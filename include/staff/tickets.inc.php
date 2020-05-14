<?php
$search = SavedSearch::create();
$tickets = TicketModel::objects();
$clear_button = false;
$view_all_tickets = $date_header = $date_col = false;

// Make sure the cdata materialized view is available
TicketForm::ensureDynamicDataView();

// Figure out REFRESH url — which might not be accurate after posting a
// response
list($path,) = explode('?', $_SERVER['REQUEST_URI'], 2);
$args = array();
parse_str($_SERVER['QUERY_STRING'], $args);

// Remove commands from query
unset($args['id']);
if ($args['a'] !== 'search') unset($args['a']);

$refresh_url = $path . '?' . http_build_query($args);

$sort_options = array(
    'priority,updated' =>   __('Priority + Most Recently Updated'),
    'updated' =>            __('Most Recently Updated'),
    'priority,created' =>   __('Priority + Most Recently Created'),
    'due' =>                __('Due Date'),
    'priority,due' =>       __('Priority + Due Date'),
    'number' =>             __('Ticket Number'),
    'answered' =>           __('Most Recently Answered'),
    'closed' =>             __('Most Recently Closed'),
    'hot' =>                __('Longest Thread'),
    'relevance' =>          __('Relevance'),
);
$use_subquery = true;

// Figure out the queue we're viewing
$queue_key = sprintf('::Q:%s', ObjectModel::OBJECT_TYPE_TICKET);
$queue_name = $_SESSION[$queue_key] ?: '';

switch ($queue_name) {
case 'closed':
    $status='closed';
    $results_type=__('Closed Tickets');
    $showassigned=true; //closed by.
    $queue_sort_options = array('closed', 'priority,due', 'due',
        'priority,updated', 'priority,created', 'answered', 'number', 'hot');
    break;
case 'overdue':
    $status='open';
    $results_type=__('Overdue Tickets');
    $tickets->filter(array('isoverdue'=>1));
    $queue_sort_options = array('priority,due', 'due', 'priority,updated',
        'updated', 'answered', 'priority,created', 'number', 'hot');
    break;
case 'assigned':
    $status='open';
    $staffId=$thisstaff->getId();
    $results_type=__('My Tickets');
    $tickets->filter(Q::any(array(
        'staff_id'=>$thisstaff->getId(),
        Q::all(array('staff_id' => 0, 'team_id__gt' => 0)),
    )));
    $queue_sort_options = array('updated', 'priority,updated',
        'priority,created', 'priority,due', 'due', 'answered', 'number',
        'hot');
    break;
case 'answered':
    $status='open';
    $showanswered=true;
    $results_type=__('Answered Tickets');
    $tickets->filter(array('isanswered'=>1));
    $queue_sort_options = array('answered', 'priority,updated', 'updated',
        'priority,created', 'priority,due', 'due', 'number', 'hot');
    break;
default:
case 'search':
    $queue_sort_options = array('priority,updated', 'priority,created',
        'priority,due', 'due', 'updated', 'answered',
        'closed', 'number', 'hot');
    // Consider basic search
    if ($_REQUEST['query']) {
        $results_type=__('Search Results');
        // Use an index if possible
        if ($_REQUEST['search-type'] == 'typeahead' && Validator::is_email($_REQUEST['query'])) {
            $tickets = $tickets->filter(array(
                'user__emails__address' => $_REQUEST['query'],
            ));
        }
        else {
            $basic_search = Q::any(array(
                'number__startswith' => $_REQUEST['query'],
                'user__name__contains' => $_REQUEST['query'],
                'user__emails__address__contains' => $_REQUEST['query'],
                'user__org__name__contains' => $_REQUEST['query'],
            ));
            $tickets->filter($basic_search);
            if (!$_REQUEST['search-type']) {
                // [Search] click, consider keywords too. This is a
                // relatively ugly hack. SearchBackend::find() add in a
                // constraint for the search. We need to pop that off and
                // include it as an OR with the above constraints
                $keywords = TicketModel::objects();
                $keywords->extra(array('select' => array('ticket_id' => 'Z1.ticket_id')));
                $keywords = $ost->searcher->find($_REQUEST['query'], $keywords);
                $tickets->values('ticket_id')->annotate(array('__relevance__' => new SqlCode(0.5)));
                $keywords->aggregated = true; // Hack to prevent select ticket.*
                $tickets->union($keywords)->order_by(new SqlCode('__relevance__'), QuerySet::DESC);
            }
        }
        // Clear sticky search queue
        unset($_SESSION[$queue_key]);
        break;
    } elseif (isset($_SESSION['advsearch'])) {
        $form = $search->getFormFromSession('advsearch');
        $tickets = $search->mangleQuerySet($tickets, $form);
        $view_all_tickets = $thisstaff->hasPerm(SearchBackend::PERM_EVERYTHING);
        $results_type=__('Búsqueda Avanzada')
            . '<a class="action-button" style="font-size: 15px;" href="?clear_filter"><i style="top:0" class="icon-ban-circle"></i> <em>' . __('clear') . '</em></a>';
        foreach ($form->getFields() as $sf) {
            if ($sf->get('name') == 'keywords' && $sf->getClean()) {
                $has_relevance = true;
                break;
            }
        }
        break;
    }
    // Apply user filter
    elseif (isset($_GET['uid']) && ($user = User::lookup($_GET['uid']))) {
        $tickets->filter(array('user__id'=>$_GET['uid']));
        $results_type = sprintf('%s — %s', __('Search Results'),
            $user->getName());
        // Don't apply normal open ticket
        break;
    }
    elseif (isset($_GET['orgid']) && ($org = Organization::lookup($_GET['orgid']))) {
        $tickets->filter(array('user__org_id'=>$_GET['orgid']));
        $results_type = sprintf('%s — %s', __('Search Results'),
            $org->getName());
        // Don't apply normal open ticket
        break;
    }
    // Fall-through and show open tickets
case 'open':
    $status='open';
    $results_type=__('Open Tickets');
    if (!$cfg->showAnsweredTickets())
        $tickets->filter(array('isanswered'=>0));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;

case 'received':
    $status='open';
    $results_type=__('Received Tickets');
    $tickets->filter(array('status_id'=>6));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;

case 'analysis':
    $status='open';
    $results_type=__('Analysis Tickets');
    $tickets->filter(array('status_id'=>1));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;

case 'realization':
    $status='open';
    $results_type=__('Realization Tickets');
    $tickets->filter(array('status_id'=>2));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;

case 'testing':
    $status='open';
    $results_type=__('Testing Tickets');
    $tickets->filter(array('status_id'=>3));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;

case 'approved':
    
    $results_type=__('Approved Tickets');
    $tickets->filter(array('status_id'=>7));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;

}

// Open queues _except_ assigned should respect showAssignedTickets()
// settings
if ($status != 'closed' && $queue_name != 'assigned') {
    $hideassigned = ($cfg && !$cfg->showAssignedTickets()) && !$thisstaff->showAssignedTickets();
    $showassigned = !$hideassigned;
    if ($queue_name == 'open' && $hideassigned)
        $tickets->filter(array('staff_id'=>0, 'team_id'=>0));
}


// Apply primary ticket status
if ($status)
    $tickets->filter(array('status__state'=>$status));


// Impose visibility constraints
// ------------------------------------------------------------
if (!$view_all_tickets) {
    // -- Open and assigned to me
    $assigned = Q::any(array(
        'staff_id' => $thisstaff->getId(),
    ));
    // -- Open and assigned to a team of mine
    if ($teams = array_filter($thisstaff->getTeams()))
        $assigned->add(array('team_id__in' => $teams));

    $visibility = Q::any(new Q(array('status__state'=>'open', $assigned)));

    // -- Routed to a department of mine
    if (!$thisstaff->showAssignedOnly() && ($depts=$thisstaff->getDepts()))
        $visibility->add(array('dept_id__in' => $depts));

    $tickets->filter(Q::any($visibility));
}

// TODO :: Apply requested quick filter

// Apply requested pagination
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$count = $tickets->count();
$pageNav = new Pagenate($count, $page, PAGE_LIMIT);
$pageNav->setURL('tickets.php', $args);
$tickets = $pageNav->paginate($tickets);



/********<< INICIO>>  Ajuste realizado por HDANDREA 05-02-18 AC000000065 – Agregar el campo estado de la OC en la vista princ ***************/

$sortOptions = array('date' => 'lastupdate',
                     'name' => 'user__name',
                     'subj' => 'cdata__subject',
                     'status' => 'status',
                     'assignee' => 'staff__firstname',
                     'id'=>'number');
                     
$orderWays = array('DESC'=>'-','ASC'=>'');
$sort= ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';

//Sorting options...
if ($sort && $sortOptions[$sort])
    $order_column =$sortOptions[$sort];

$order_column = $order_column ?: 'name';

if ($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])])
    $order = $orderWays[strtoupper($_REQUEST['order'])];

if ($order_column && strpos($order_column,','))
    $order_column = str_replace(','," $order,",$order_column);
    
    
$x=$sort.'_sort';
$$x=' class="'.($order == '' ? 'asc' : 'desc').'" ';


$qs = array();
$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('tickets.php', $qs);
$qstr.='&amp;order='.($order=='-' ? 'ASC' : 'DESC');


if(!empty($_REQUEST['order']) && !empty($_REQUEST['sort'])){
	
	$_SESSION[':Q:tickets'] = clone $tickets;
	$tickets->values('lock__staff_id', 'staff_id', 'isoverdue', 'team_id', 'ticket_id', 'number', 'cdata__subject', 'user__default_email__address', 'source', 'cdata__priority__priority_color', 'cdata__priority__priority_desc', 'status_id', 'status__name', 'status__state', 'dept_id', 'dept__name', 'user__name', 'lastupdate', 'isanswered', 'staff__firstname', 'staff__lastname', 'team__name','est_duedate');
	$tickets->order_by($order . $order_column);
	
}

/********<< FIN >> *******************************************/









// Rewrite $tickets to use a nested query, which will include the LIMIT part
// in order to speed the result
//
// ATM, advanced search with keywords doesn't support the subquery approach


if ($use_subquery) {
    $orig_tickets = clone $tickets;
    $tickets2 = TicketModel::objects();
    $tickets2->values = $tickets->values;
    $tickets2->filter(array('ticket_id__in' => $tickets->values_flat('ticket_id')));

    // Transfer the order_by from the original tickets
    $tickets2->order_by($orig_tickets->getSortFields());
    $tickets = $tickets2;
}

// Apply requested sorting
$queue_sort_key = sprintf(':Q%s:%s:sort', ObjectModel::OBJECT_TYPE_TICKET, $queue_name);


// If relevance is available, use it as the default
if ($has_relevance) {
    array_unshift($queue_sort_options, 'relevance');
}
elseif ($_SESSION[$queue_sort_key][0] == 'relevance') {
    unset($_SESSION[$queue_sort_key]);
}

if (isset($_GET['sort'])) {
    $_SESSION[$queue_sort_key] = array($_GET['sort'], $_GET['dir']);
}
elseif (!isset($_SESSION[$queue_sort_key])) {
    $_SESSION[$queue_sort_key] = array($queue_sort_options[0], 0);
}

list($sort_cols, $sort_dir) = $_SESSION[$queue_sort_key];
$orm_dir = $sort_dir ? QuerySet::ASC : QuerySet::DESC;
$orm_dir_r = $sort_dir ? QuerySet::DESC : QuerySet::ASC;

switch ($sort_cols) {
case 'number':
    $tickets->extra(array(
        'order_by'=>array(
            array(SqlExpression::times(new SqlField('number'), 1), $orm_dir)
        )
    ));
    break;

case 'priority,created':
    $tickets->order_by(($sort_dir ? '-' : '') . 'cdata__priority__priority_urgency');
    // Fall through to columns for `created`
case 'created':
    $date_header = __('Date Created');
    $date_col = 'created';
    $tickets->values('created');
    $tickets->order_by($sort_dir ? 'created' : '-created');
    break;

case 'priority,due':
    $tickets->order_by('cdata__priority__priority_urgency', $orm_dir_r);
    // Fall through to add in due date filter
case 'due':
    $date_header = __('Due Date');
    $date_col = 'est_duedate';
    $tickets->values('est_duedate');
    $tickets->order_by(SqlFunction::COALESCE(new SqlField('est_duedate'), 'zzz'), $orm_dir_r);
    break;

case 'closed':
    $date_header = __('Date Closed');
    $date_col = 'closed';
    $tickets->values('closed');
    $tickets->order_by('closed', $orm_dir);
    break;

case 'answered':
    $date_header = __('Last Response');
    $date_col = 'thread__lastresponse';
    $date_fallback = '<em class="faded">'.__('unanswered').'</em>';
    $tickets->order_by('thread__lastresponse', $orm_dir);
    $tickets->values('thread__lastresponse');
    break;

case 'hot':
    $tickets->order_by('thread_count', $orm_dir);
    $tickets->annotate(array(
        'thread_count' => SqlAggregate::COUNT('thread__entries'),
    ));
    break;

case 'relevance':
    $tickets->order_by(new SqlCode('__relevance__'), $orm_dir);
    break;

default:
case 'priority,updated':
    $tickets->order_by('cdata__priority__priority_urgency', $orm_dir_r);
    // Fall through for columns defined for `updated`
case 'updated':
    $date_header = __('Last Updated');
    $date_col = 'lastupdate';
    $tickets->order_by('lastupdate', $orm_dir);
    break;
}

// Save the query to the session for exporting
$_SESSION[':Q:tickets'] = $tickets;

TicketForm::ensureDynamicDataView();

// Select pertinent columns
// ------------------------------------------------------------
$tickets->values('lock__staff_id', 'staff_id', 'isoverdue', 'team_id', 'ticket_id', 'number', 'cdata__subject', 'user__default_email__address', 'source', 'cdata__priority__priority_color', 'cdata__priority__priority_desc', 'status_id', 'status__name', 'status__state', 'dept_id', 'dept__name', 'user__name', 'lastupdate', 'isanswered', 'staff__firstname', 'staff__lastname', 'team__name','est_duedate');
// Add in annotations
$tickets->annotate(array(
    'collab_count' => TicketThread::objects()
        ->filter(array('ticket__ticket_id' => new SqlField('ticket_id', 1)))
        ->aggregate(array('count' => SqlAggregate::COUNT('collaborators__id'))),
    'attachment_count' => TicketThread::objects()
        ->filter(array('ticket__ticket_id' => new SqlField('ticket_id', 1)))
        ->filter(array('entries__attachments__inline' => 0))
        ->aggregate(array('count' => SqlAggregate::COUNT('entries__attachments__id'))),
    'thread_count' => TicketThread::objects()
        ->filter(array('ticket__ticket_id' => new SqlField('ticket_id', 1)))
        ->exclude(array('entries__flags__hasbit' => ThreadEntry::FLAG_HIDDEN))
        ->aggregate(array('count' => SqlAggregate::COUNT('entries__id'))),
));
 
?>

<!-- SEARCH FORM START -->
<div id='basic_search'>
  <div class="pull-right" style="height:25px">
    <span class="valign-helper"></span>
    <?php
    require STAFFINC_DIR.'templates/queue-sort.tmpl.php';
    ?>
  </div>
    <form action="tickets.php" method="get" onsubmit="javascript:
  $.pjax({
    url:$(this).attr('action') + '?' + $(this).serialize(),
    container:'#pjax-container',
    timeout: 2000
  });
return false;">
    <input type="hidden" name="a" value="search">
    <input type="hidden" name="search-type" value=""/>
    <div class="attached input">
		<input type="text" class="basic-search" data-url="ajax.php/tickets/lookup" name="query"
			autofocus size="30" value="<?php echo Format::htmlchars($_REQUEST['query'], true); ?>"
			autocomplete="off" autocorrect="off" autocapitalize="off">		
		<button type="submit" class="attached button">
			<svg viewBox="0 0 24 24">
				<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
			</svg>
		</button>
    </div>
	<a href="#" onclick="javascript:$.dialog('ajax.php/tickets/search', 201);">
		<div class="action-button advanced-search gray-light2">
			<div class="button-icon">
				<svg viewBox="0 0 24 24">
					<path d="M9,2A7,7 0 0,1 16,9C16,10.57 15.5,12 14.61,13.19L15.41,14H16L22,20L20,22L14,16V15.41L13.19,14.61C12,15.5 10.57,16 9,16A7,7 0 0,1 2,9A7,7 0 0,1 9,2M8,5V8H5V10H8V13H10V10H13V8H10V5H8Z" />
				</svg>
			</div>
			<div class="button-text advanced-search">
				Búsqueda Avanzada <!-- Advanced Search -->
			</div>
			<div class="button-spacing">
				&nbsp;
			</div>
		</div>
	</a>
    <i class="help-tip icon-question-sign" href="#advanced"></i>
    </form>
</div>

<!-- SEARCH FORM END -->
<div class="clear"></div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><a href="<?php echo $refresh_url; ?>"
                    title="<?php echo __('Refresh'); ?>"><?php echo
                    $results_type; ?></a>
					<svg style="width:34px;height:34px; margin-right:-14px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<circle style="fill:#82D2FF;" cx="256" cy="256" r="256"/>
<path style="fill:#08B7FC;" d="M512,256c0,141.385-114.615,256-256,256c0-68.055,0-468.118,0-512C397.385,0,512,114.615,512,256z"/>
<g>
    <path style="fill:#F1F1F2;" d="M120.262,295.373c-14.054-48.578-1.148-102.311,35.826-139.285
        c22.099-22.099,50.104-35.972,80.987-40.12c29.917-4.015,60.857,1.749,87.125,16.239l14.49-26.27
        c-31.859-17.573-69.364-24.568-105.609-19.702c-37.458,5.031-71.417,21.851-98.207,48.64
        C89.909,179.842,74.311,245.261,91.621,304.31l-24.838-11.059l-12.202,27.406l72.827,32.426l32.424-72.829l-27.406-12.201
        L120.262,295.373z"/>
    <path style="fill:#F1F1F2;" d="M457.419,191.343l-72.827-32.426l-32.424,72.829l27.406,12.201l12.164-27.321
        c14.054,48.578,1.148,102.311-35.826,139.285c-22.099,22.099-50.104,35.972-80.987,40.12c-29.918,4.017-60.857-1.749-87.125-16.239
        l-14.49,26.27c25.25,13.928,54.044,21.212,82.949,21.212c7.566,0,15.141-0.5,22.66-1.51c37.458-5.031,71.417-21.851,98.207-48.64
        c44.967-44.967,60.564-110.386,43.254-169.435l24.838,11.059L457.419,191.343z"/>
</g>
<g>
    <path style="fill:#E6E6E6;" d="M324.201,132.207l14.49-26.27C313.517,92.052,284.817,84.777,256,84.734v29.994
        C279.777,114.763,303.447,120.759,324.201,132.207z"/>
    <path style="fill:#E6E6E6;" d="M457.419,191.343l-72.827-32.426l-32.424,72.829l27.406,12.201l12.164-27.321
        c14.054,48.578,1.148,102.311-35.826,139.285c-22.099,22.099-50.104,35.972-80.987,40.12c-6.281,0.843-12.607,1.25-18.925,1.24
        v29.998c0.086,0,0.172,0.004,0.258,0.004c7.566,0,15.141-0.5,22.66-1.51c37.458-5.031,71.417-21.851,98.207-48.64
        c44.967-44.967,60.564-110.386,43.254-169.435l24.838,11.059L457.419,191.343z"/>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>
										
					</h2>
            </div>
            <div class="pull-right flush-right page-top">
            <?php
            if ($count) {
                Ticket::agentActions($thisstaff, array('status' => $status));
            }?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<form action="tickets.php" method="POST" name='tickets' id="tickets">
<?php csrf_token(); ?>
 <input type="hidden" name="a" value="mass_process" >
 <input type="hidden" name="do" id="action" value="" >
 <input type="hidden" name="status" value="<?php echo Format::htmlchars($_REQUEST['status'], true);?>" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100%">
	    <thead>
	        <tr class="head">
	               
	<!-- Head Priority -->	
				<th class="head-priority" <?php echo $pri_sort;?>>
					<a <?php echo $pri_sort; ?> href="tickets.php?sort=pri&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
						title="Sort By Priority <?php echo $negorder; ?>">
														
						</a>
				</th>
	                        
	<!-- Head Checkbox -->          
	            <?php if($thisstaff->canManageTickets()) { ?>
		        <th class="head-checkbox" width="2px">
					<svg viewBox="0 0 24 24">
						<path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
					</svg>				
				</th>
	            <?php
	            } ?>  

	<!-- Head Date -->            
	            <th class="head-date">
	                <a  <?php echo $date_sort; ?> href="tickets.php?sort=date<?php echo $qstr; ?>"
	                    title="<?php echo sprintf(__('Sort by %s %s'), __('Date'), __($negorder)); ?>"><?php echo __('Fecha Entrega'); ?></a>
	            </th>  

	<!-- Head Client -->             
	            <th class="head-client">
	                <a <?php echo $name_sort; ?> href="tickets.php?sort=name<?php echo $qstr; ?>"
	                     title="<?php echo sprintf(__('Sort by %s %s'), __('Name'), __($negorder)); ?>"><?php echo __('Client');?></a>
	            </th>

	<!-- Head Description -->            
		        <th class="head-description">
	                 <a <?php echo $subj_sort; ?> href="tickets.php?sort=subj<?php echo $qstr; ?>"
	                    title="<?php echo sprintf(__('Sort by %s %s'), __('Subject'), __($negorder)); ?>"><?php echo __('Description'); ?></a>
	            </th>
	            
	<!-- Head Status -->  
	
	            <?php
	            if($search && !$status) { ?>
	                <th class="head-status">
	                    <a <?php echo $status_sort; ?> href="tickets.php?sort=status&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
	                        title="<?php echo sprintf(__('Sort by %s %s'), __('Status'), __($negorder)); ?>"><?php echo __('Status');?></a>
	            </th>

	<!-- Head Closed By --> <!-- OR -->    
	<!-- Head Assigned To --> <!-- OR -->   
	<!-- Head Department -->  
	            <?php
	            }
	            else{ 
					
					/********<< INICIO >>  Ajuste realizado por HDANDREA 05-02-18 AC000000065 – Agregar el campo estado de la OC en la vista princ ***************/
		            //################################## ENCABEZADO ESTATUS #############################
		            echo '
	                <th class="head-status">
	                    <a '.$status_sort.' href="tickets.php?sort=status'.$qstr.'" title="'.sprintf(__('Sort by %s %s'), __('Status'), __($negorder)).'">'.__('Status').'</a>
	                </th>';
	            
					/********<< FIN >> ***************/
					
					
					
				}
	            
	            
	            
	            
	            
	            if($showassigned ) {
	                //Closed by
	                if(!strcasecmp($status,'closed')) { ?>
	                    <th class="head-closed-by">
	                        <a <?php echo $staff_sort; ?> href="tickets.php?sort=staff&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
	                            title="<?php echo sprintf(__('Sort by %s %s'), __("Closing Agent's Name"), __($negorder)); ?>"><?php echo __('Closed By'); ?></a>
	            </th> 
	<!-- OR -->
	<!-- Head Assigned To -->    
	                <?php
	                } else { //assigned to ?>
	                    <th class="head-assigned-to">
	                        <a <?php echo $assignee_sort; ?> href="tickets.php?sort=assignee<?php echo $qstr; ?>"
	                            title="<?php echo sprintf(__('Sort by %s %s'), __('Assignee'), __($negorder)); ?>"><?php echo __('Assigned To'); ?></a>
	            </th>         
	<!-- Head Department -->             
	                <?php
	                }
	            } else { ?>
	                <th class="head-department">
	                    <a <?php echo $dept_sort; ?> href="tickets.php?sort=dept&order=<?php echo $negorder;?><?php echo $qstr; ?>"
	                        title="<?php echo sprintf(__('Sort by %s %s'), __('Department'), __($negorder)); ?>"><?php echo __('Dept'); ?></a>
	            </th>                
	            
	            <?php
	            } ?>

	<!-- Head ID --> 
	            <th class="head-id">
	                <a style="padding-left:7px;" <?php echo $id_sort; ?> href="tickets.php?sort=ID&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
	                    title="<?php echo sprintf(__('Sort by %s %s'), __('Ticket ID'), __($negorder)); ?>"><?php echo __('ID'); ?></a>
	            </th>   
								
	        </tr>
	     </thead>
     <tbody>
        <?php
        // Setup Subject field for display
        $subject_field = TicketForm::getInstance()->getField('subject');
        $class = "row1";
        $total=0;
        $ids=($errors && $_POST['tids'] && is_array($_POST['tids']))?$_POST['tids']:null;
        foreach ($tickets as $T) {

            $total += 1;
                $tag=$T['staff_id']?'assigned':'openticket';
                $flag=null;
                if($T['lock__staff_id'] && $T['lock__staff_id'] != $thisstaff->getId())
                    $flag='locked';
                elseif($T['isoverdue'])
                    $flag='overdue';

                $lc='';
                if ($showassigned) {
                    if ($T['staff_id'])
                        $lc = new AgentsName($T['staff__firstname'].' '.$T['staff__lastname']);
                    elseif ($T['team_id'])
                        $lc = Team::getLocalById($T['team_id'], 'name', $T['team__name']);
                }
                else {
                    $lc = Dept::getLocalById($T['dept_id'], 'name', $T['dept__name']);
                }
                $tid=$T['number'];
                $subject = $subject_field->display($subject_field->to_php($T['cdata__subject']));
                $threadcount=$T['thread_count'];
                if(!strcasecmp($T['status__state'],'open') && !$T['isanswered'] && !$T['lock__staff_id']) {
                    $tid=sprintf('%s',$tid);
                }
                ?>


	<!-- Table Priority -->
            <tr id="<?php echo $T['ticket_id']; ?>">	
			
                <td class="cursor priority <?php echo $T['cdata__priority__priority_desc']; ?>" style="cursor:pointer" nowrap >
						<a style="display:block;" class="preview cursor" href="#" onclick='return false;'
						data-preview="#tickets/<?php echo $T['ticket_id']; ?>/priority">
						<?php echo $T['cdata__priority__priority_desc']; ?>
                    </a>
                </td>			

	<!-- Table Checkbox -->  
                <?php if($thisstaff->canManageTickets()) {

                    $sel=false;
                    if($ids && in_array($T['ticket_id'], $ids))
                        $sel=true;
                    ?>
					
                <td align="center" class="checkbox nohover">
				
				
                    <input id="checkboxG4-<?php echo $T['ticket_id']; ?>" class="ckb css-checkbox" type="checkbox" name="tids[]"
                    value="<?php echo $T['ticket_id']; ?>" <?php echo $sel?'checked="checked"':''; ?>><label for="checkboxG4-<?php echo $T['ticket_id']; ?>" class="css-label"></label>
					
					
                </td>

                <?php } ?>

				
	<!-- Table Date -->
                <td class="table-date" nowrap>
					<div class="nowrap">
						<div class="due-date">
							<?php //echo Format::date($T[$date_col ?: 'lastupdate']) ?: $date_fallback; 
                                    echo Format::date($T['est_duedate']);
                            ?>
						</div>
						<div class="due-time">
							<?php //echo Format::time($T[$date_col ?: 'lastupdate']) ?: $date_fallback; 
                                    echo Format::time($T['est_duedate']);
                            ?>
						</div>		
					</div>
				</td>
				
	<!-- Table Client -->				
                <td class="table-client" nowrap><?php
                    if ($T['collab_count'])
                        echo '<span class="pull-right faded-more" data-toggle="tooltip" title="'
                            .$T['collab_count'].'"><i class="icon-group"></i></span>';
                    ?><span class="truncate" style="max-width:<?php
                        echo $T['collab_count'] ? '150px' : '170px'; ?>"><a href="tickets.php?id=<?php echo $T['ticket_id']; ?>"><?php
                    $un = new UsersName($T['user__name']);
                        echo Format::htmlchars($un);
                    ?></a></span>

	<!-- Table Description -->
                <td class="table-description"><a <?php if ($flag) { ?> title="<?php echo ucfirst($flag); ?> Ticket" <?php } ?>
                    style="max-width: <?php
                    $base = 280;
                    // Make room for the paperclip and some extra
                    if ($T['attachment_count']) $base -= 18;
                    // Assume about 8px per digit character
                    if ($threadcount > 1) $base -= 20 + ((int) log($threadcount, 10) + 1) * 8;
                    // Make room for overdue flag and friends
                    if ($flag) $base -= 20;
                    echo $base; ?>px;"
                    href="tickets.php?id=<?php echo $T['ticket_id']; ?>"><span
                    class="truncate"><?php echo $subject; ?></span></a>
<?php               if ($T['attachment_count'])
                        echo '<i class="small icon-paperclip icon-flip-horizontal" data-toggle="tooltip" title="'
                            .$T['attachment_count'].'"></i>';
                    if ($threadcount > 1) { ?>
                        <span class="pull-right faded-more"><i class="icon-comments-alt"></i>
                            <small><?php echo $threadcount; ?></small>
                        </span>
                    <?php } ?>
                </td>				
				
				
                <?php
               
                if($search && !$status){

                    $displaystatus=TicketStatus::getLocalById($T['status_id'], 'value', $T['status__name']);
                    if(!strcasecmp($T['status__state'],'open'))
                        $displaystatus="<b>$displaystatus</b>";
                    echo "<td>".$displaystatus."</td>";
                } else { ?>
              
	<!-- Table Closed By --> <!-- OR -->    
	<!-- Table Assigned To --> <!-- OR -->   
	<!-- Table Department -->                  
	<!-- Table Status -->
                <?php
                
					//***   <<INICIO>> Ajuste realizado por HDANDREA 05-02-18 AC000000065 – Agregar el campo estado de la OC en la vista princ  ***
                
                    $displaystatus=TicketStatus::getLocalById($T['status_id'], 'value', $T['status__name']);
                    
                    if(strcasecmp($T['status__state'],'open'))
                        $displaystatus="<b>$displaystatus</b>";
                    echo "<td>".$displaystatus."</td>";
                
                   //*** <<FIN> *******************************************************************************************
                
                }
                ?>
                <td class="table-status" nowrap><span class="truncate" style="max-width: 169px"><?php
                    echo Format::htmlchars($lc); ?></span></td>
					
				
	<!-- Table ID -->
                <td class="table-id" title="<?php echo $T['user__default_email__address']; ?>" nowrap>
                  <a class="Icon <?php echo strtolower($T['source']); ?>Ticket preview"
                    title="Preview Ticket"
                    href="tickets.php?id=<?php echo $T['ticket_id']; ?>"
                    data-preview="#tickets/<?php echo $T['ticket_id']; ?>/preview"
                    ><?php echo $tid; ?></a>
                </td>					
					
            </tr>
            <?php
            } //end of foreach
        if (!$total)
            $ferror=__('There are no tickets matching your criteria.');
        ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="8">
            <?php if($total && $thisstaff->canManageTickets()){ ?>
            <?php echo __('Select');?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo '<i>';
                echo $ferror?Format::htmlchars($ferror):__('Query returned 0 results.');
                echo '</i>';
            } ?>
        </td>
     </tr>
    </tfoot>
    </table>
    <?php
    if ($total>0) { //if we actually had any tickets returned.
?>      <div id="table-foot-options">
            <span class="faded pull-right"><?php echo $pageNav->showing(); ?></span>
<?php
        echo __('Page').':'.$pageNav->getPageLinks().'&nbsp;';
        echo sprintf('<a class="export-csv no-pjax" href="?%s">%s</a>',
                Http::build_query(array(
                        'a' => 'export', 'h' => $hash,
                        'status' => $_REQUEST['status'])),
                __('Export'));
        echo '&nbsp;<i class="help-tip icon-question-sign" href="#export"></i></div>';
    } ?>
    </form>
</div>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="mark_overdue-confirm">
        <?php echo __('Are you sure you want to flag the selected tickets as <font color="red"><b>overdue</b></font>?');?>
    </p>
    <div><?php echo __('&nbsp;');?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="<?php echo __('No, Cancel');?>" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!');?>" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {
    $(document).off('.tickets');
    $(document).on('click.tickets', 'a.tickets-action', function(e) {
        e.preventDefault();
        var count = checkbox_checker($('form#tickets'), 1);
        if (count) {
            var url = 'ajax.php/'
            +$(this).attr('href').substr(1)
            +'?count='+count
            +'&_uid='+new Date().getTime();
            $.dialog(url, [201], function (xhr) {
                $.pjax.reload('#pjax-container');
             });
        }
        return false;
    });
    $('[data-toggle=tooltip]').tooltip();
});
</script>
