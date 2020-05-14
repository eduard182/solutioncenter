<?php
//Note that ticket obj is initiated in tickets.php.
if(!defined('OSTSCPINC') || !$thisstaff || !is_object($ticket) || !$ticket->getId()) die('Invalid path');
 
//Make sure the staff is allowed to access the page.
if(!@$thisstaff->isStaff() || !$ticket->checkStaffPerm($thisstaff)) die('Access Denied');
 
//Re-use the post info on error...savekeyboards.org (Why keyboard? -> some people care about objects than users!!)
$info=($_POST && $errors)?Format::input($_POST):array();
 
//Get the goodies.
$dept  = $ticket->getDept();  //Dept
$role  = $thisstaff->getRole($dept);
$staff = $ticket->getStaff(); //Assigned or closed by..
$user  = $ticket->getOwner(); //Ticket User (EndUser)
$team  = $ticket->getTeam();  //Assigned team.
$sla   = $ticket->getSLA();
$lock  = $ticket->getLock();  //Ticket lock obj
$mylock = ($lock && $lock->getStaffId() == $thisstaff->getId()) ? $lock : null;
$id    = $ticket->getId();    //Ticket ID.
 
//Useful warnings and errors the user might want to know!
if ($ticket->isClosed() && !$ticket->isReopenable())
    $warn = sprintf(
            __('Current ticket status (%s) does not allow the end user to reply.'),
            $ticket->getStatus());
elseif ($ticket->isAssigned()
        && (($staff && $staff->getId()!=$thisstaff->getId())
            || ($team && !$team->hasMember($thisstaff))
        ))
    $warn.= sprintf('&nbsp;&nbsp;<span class="Icon assignedTicket">%s</span>',
            sprintf(__('Ticket is assigned to %s'),
                implode('/', $ticket->getAssignees())
                ));
 
if (!$errors['err']) {
 
    if ($lock && $lock->getStaffId()!=$thisstaff->getId())
        $errors['err'] = sprintf(__('This ticket is currently locked by %s'),
                $lock->getStaffName());
    elseif (($emailBanned=Banlist::isBanned($ticket->getEmail())))
        $errors['err'] = __('Email is in banlist! Must be removed before any reply/response');
    elseif (!Validator::is_valid_email($ticket->getEmail()))
        $errors['err'] = __('EndUser email address is not valid! Consider updating it before responding');
}
 
$unbannable=($emailBanned) ? BanList::includes($ticket->getEmail()) : false;
 
if($ticket->isOverdue())
    $warn.='&nbsp;&nbsp;<span class="Icon overdueTicket">'.__('Marked overdue!').'</span>';
 
?>
<div>
    <div class="sticky bar">
       <div class="content">
<div id="ticket-view-buttons-row">

<!-- Ticket View Buttons -->
	<div id="button-more">

					<a id="ticket-more" data-dropdown="#action-dropdown-more">
						<div id="more" class="action-button more gray" data-dropdown="#action-dropdown-more">
							<div id="button-more-inner">
								<div class="button-icon miclass">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 58 58" style="enable-background:new 0 0 58 58;width: 27px;
padding: 5px 0px 4px 5px;" xml:space="preserve">
<g>
	<path d="M54.319,37.839C54.762,35.918,55,33.96,55,32c0-9.095-4.631-17.377-12.389-22.153c-0.473-0.29-1.087-0.143-1.376,0.327
		c-0.29,0.471-0.143,1.086,0.327,1.376C48.724,15.96,53,23.604,53,32c0,1.726-0.2,3.451-0.573,5.147C51.966,37.051,51.489,37,51,37
		c-3.86,0-7,3.141-7,7s3.14,7,7,7s7-3.141,7-7C58,41.341,56.509,39.024,54.319,37.839z M51,49c-2.757,0-5-2.243-5-5s2.243-5,5-5
		s5,2.243,5,5S53.757,49,51,49z"/>
	<path d="M38.171,54.182C35.256,55.388,32.171,56,29,56c-6.385,0-12.527-2.575-17.017-7.092C13.229,47.643,14,45.911,14,44
		c0-3.859-3.14-7-7-7s-7,3.141-7,7s3.14,7,7,7c1.226,0,2.378-0.319,3.381-0.875C15.26,55.136,21.994,58,29,58
		c3.435,0,6.778-0.663,9.936-1.971c0.51-0.211,0.753-0.796,0.542-1.307C39.267,54.213,38.681,53.971,38.171,54.182z M2,44
		c0-2.757,2.243-5,5-5s5,2.243,5,5s-2.243,5-5,5S2,46.757,2,44z"/>
	<path d="M4,31.213c0.024,0.002,0.048,0.003,0.071,0.003c0.521,0,0.959-0.402,0.997-0.93c0.712-10.089,7.586-18.52,17.22-21.314
		C23.142,11.874,25.825,14,29,14c3.86,0,7-3.141,7-7s-3.14-7-7-7c-3.851,0-6.985,3.127-6.999,6.975
		C11.42,9.922,3.851,19.12,3.073,30.146C3.034,30.696,3.449,31.175,4,31.213z M29,2c2.757,0,5,2.243,5,5s-2.243,5-5,5s-5-2.243-5-5
		S26.243,2,29,2z"/>

</svg>
								</div>
								<div class="button-text estado">
									<?php echo __('More');?>
								</div>
							</div>								
							<div id="button-more-caret">
								<div class="caret">
									<i class="material-icons more">expand_more</i>
								</div>
							</div>
						</div>
					</a>	
	</div>	
	<div id="button-status">
					<?php
					if ($thisstaff->hasPerm(Email::PERM_BANLIST)
							|| $role->hasPerm(TicketModel::PERM_EDIT)
							|| ($dept && $dept->isManager($thisstaff))) { ?>	
					<?php
					}
					// Status change options
					echo TicketStatus::status_options();
					if ($role->hasPerm(TicketModel::PERM_EDIT)) { ?>  
	</div>	
					<div id="action-dropdown-more" class="action-dropdown anchor-right">
					  <ul>
						<?php
						 if ($role->hasPerm(TicketModel::PERM_EDIT)) { ?>
							<li><a class="change-user" href="#tickets/<?php
							echo $ticket->getId(); ?>/change-user"><i class="material-icons">account_box</i><?php
							echo __('Change Owner'); ?></a></li>
						<?php
						 }
		 
						 if($ticket->isOpen() && ($dept && $dept->isManager($thisstaff))) {
		 
							if($ticket->isAssigned()) { ?>
								<li><a  class="confirm-action" id="ticket-release" href="#release"><i class="material-icons">account_box</i><?php
									echo __('Release (unassign) Ticket'); ?></a></li>
							<?php
							}
		 
							if(!$ticket->isOverdue()) { ?>
								<li><a class="confirm-action" id="ticket-overdue" href="#overdue"><i class="icon-bell"></i> <?php
									echo __('Mark as Overdue'); ?></a></li>
							<?php
							}
		 
							if($ticket->isAnswered()) { ?>
							<li><a class="confirm-action" id="ticket-unanswered" href="#unanswered"><i class="icon-circle-arrow-left"></i> <?php
									echo __('Mark as Unanswered'); ?></a></li>
							<?php
							} else { ?>
							<li><a class="confirm-action" id="ticket-answered" href="#answered"><i class="icon-circle-arrow-right"></i> <?php
									echo __('Mark as Answered'); ?></a></li>
							<?php
							}
						} ?>
						<li><a href="#ajax.php/tickets/<?php echo $ticket->getId();
							?>/forms/manage" onclick="javascript:
							$.dialog($(this).attr('href').substr(1), 201);
							return false"
							><i class="icon-paste"></i> <?php echo __('Manage Forms'); ?></a></li>
		 
						<?php           if ($thisstaff->hasPerm(Email::PERM_BANLIST)) {
							 if(!$emailBanned) {?>
								<li><a class="confirm-action" id="ticket-banemail"
									href="#banemail"><i class="icon-ban-circle"></i> <?php echo sprintf(
										Format::htmlchars(__('Ban Email <%s>')),
										$ticket->getEmail()); ?></a></li>
						<?php
							 } elseif($unbannable) { ?>
								<li><a  class="confirm-action" id="ticket-banemail"
									href="#unbanemail"><i class="icon-undo"></i> <?php echo sprintf(
										Format::htmlchars(__('Unban Email <%s>')),
										$ticket->getEmail()); ?></a></li>
							<?php
							 }
						  }
						  if ($role->hasPerm(TicketModel::PERM_DELETE)) {
							 ?>
							<li class="danger"><a class="ticket-action" href="#tickets/<?php
							echo $ticket->getId(); ?>/status/delete"
							data-redirect="tickets.php"><i class="icon-trash"></i> <?php
							echo __('Delete Ticket'); ?></a></li>
						<?php
						 }
						?>
					  </ul>
					</div>		
	<div id="button-edit">

					
					<a id="ticket-edit" href="tickets.php?id=<?php echo $ticket->getId(); ?>&a=edit">
						<div id="status" class="action-button edit gray">
							<div id="button-inner">
								<div class="button-icon miclass">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 55.25 55.25" style="enable-background:new 0 0 55.25 55.25;width: 27px;padding: 5px 0px 4px 5px;" xml:space="preserve">
<path d="M52.618,2.631c-3.51-3.508-9.219-3.508-12.729,0L3.827,38.693C3.81,38.71,3.8,38.731,3.785,38.749
	c-0.021,0.024-0.039,0.05-0.058,0.076c-0.053,0.074-0.094,0.153-0.125,0.239c-0.009,0.026-0.022,0.049-0.029,0.075
	c-0.003,0.01-0.009,0.02-0.012,0.03l-3.535,14.85c-0.016,0.067-0.02,0.135-0.022,0.202C0.004,54.234,0,54.246,0,54.259
	c0.001,0.114,0.026,0.225,0.065,0.332c0.009,0.025,0.019,0.047,0.03,0.071c0.049,0.107,0.11,0.21,0.196,0.296
	c0.095,0.095,0.207,0.168,0.328,0.218c0.121,0.05,0.25,0.075,0.379,0.075c0.077,0,0.155-0.009,0.231-0.027l14.85-3.535
	c0.027-0.006,0.051-0.021,0.077-0.03c0.034-0.011,0.066-0.024,0.099-0.039c0.072-0.033,0.139-0.074,0.201-0.123
	c0.024-0.019,0.049-0.033,0.072-0.054c0.008-0.008,0.018-0.012,0.026-0.02l36.063-36.063C56.127,11.85,56.127,6.14,52.618,2.631z
	 M51.204,4.045c2.488,2.489,2.7,6.397,0.65,9.137l-9.787-9.787C44.808,1.345,48.716,1.557,51.204,4.045z M46.254,18.895l-9.9-9.9
	l1.414-1.414l9.9,9.9L46.254,18.895z M4.961,50.288c-0.391-0.391-1.023-0.391-1.414,0L2.79,51.045l2.554-10.728l4.422-0.491
	l-0.569,5.122c-0.004,0.038,0.01,0.073,0.01,0.11c0,0.038-0.014,0.072-0.01,0.11c0.004,0.033,0.021,0.06,0.028,0.092
	c0.012,0.058,0.029,0.111,0.05,0.165c0.026,0.065,0.057,0.124,0.095,0.181c0.031,0.046,0.062,0.087,0.1,0.127
	c0.048,0.051,0.1,0.094,0.157,0.134c0.045,0.031,0.088,0.06,0.138,0.084C9.831,45.982,9.9,46,9.972,46.017
	c0.038,0.009,0.069,0.03,0.108,0.035c0.036,0.004,0.072,0.006,0.109,0.006c0,0,0.001,0,0.001,0c0,0,0.001,0,0.001,0h0.001
	c0,0,0.001,0,0.001,0c0.036,0,0.073-0.002,0.109-0.006l5.122-0.569l-0.491,4.422L4.204,52.459l0.757-0.757
	C5.351,51.312,5.351,50.679,4.961,50.288z M17.511,44.809L39.889,22.43c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0
	L16.097,43.395l-4.773,0.53l0.53-4.773l22.38-22.378c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0L10.44,37.738
	l-3.183,0.354L34.94,10.409l9.9,9.9L17.157,47.992L17.511,44.809z M49.082,16.067l-9.9-9.9l1.415-1.415l9.9,9.9L49.082,16.067z"/>


</svg>
								</div>
								<div class="button-text  estado">
									<?php echo __('Edit'); ?>
								</div>
							</div>	
						</div>
					</a>  
					<?php
					} ?>	
	</div>
	<div id="button-assign">
					<?php
					// Assign
					if ($role->hasPerm(TicketModel::PERM_ASSIGN)) {?>
					<a id="ticket-assign" data-redirect="tickets.php" href="#tickets/<?php echo $ticket->getId(); ?>/assign">
						<div id="status" class="action-button change-status" data-dropdown="#action-dropdown-assign">
							<div id="button-more-inner">						
								<div class="button-icon miclass">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;width: 27px;padding: 5px 0px 4px 5px;" xml:space="preserve">
<g>
	<path d="M42,10c-2.206,0-4,1.794-4,4v1.267L6.955,25.051C6.731,23.335,5.276,22,3.5,22C1.57,22,0,23.57,0,25.5v9
		C0,36.43,1.57,38,3.5,38c1.776,0,3.231-1.335,3.455-3.051l2.134,0.672c-0.316,0.346-0.569,0.758-0.723,1.232
		c-0.578,1.783,0.402,3.705,2.187,4.283l11.415,3.698h0.001c0.347,0.112,0.698,0.166,1.044,0.166c1.435,0,2.772-0.917,3.238-2.354
		c0.171-0.53,0.191-1.068,0.107-1.584L38,44.733V46c0,2.206,1.794,4,4,4s4-1.794,4-4V14C46,11.794,44.206,10,42,10z M24.348,42.031
		c-0.238,0.735-1.03,1.14-1.764,0.901l-11.415-3.698c-0.735-0.238-1.14-1.029-0.901-1.764c0.239-0.734,1.029-1.137,1.763-0.9
		l11.417,3.698C24.182,40.506,24.585,41.297,24.348,42.031z M5,34.5C5,35.327,4.327,36,3.5,36S2,35.327,2,34.5v-9
		C2,24.673,2.673,24,3.5,24S5,24.673,5,25.5v0.167v8.666V34.5z M7,32.866v-5.732l31-9.771v25.273L7,32.866z M44,46
		c0,1.103-0.897,2-2,2s-2-0.897-2-2v-0.637V14.637V14c0-1.103,0.897-2,2-2s2,0.897,2,2V46z"/>
	<path d="M55,29h-1c-0.553,0-1,0.447-1,1s0.447,1,1,1h1c0.553,0,1-0.447,1-1S55.553,29,55,29z"/>
	<path d="M50,31h1c0.553,0,1-0.447,1-1s-0.447-1-1-1h-1c-0.553,0-1,0.447-1,1S49.447,31,50,31z"/>
	<path d="M59,29h-1c-0.553,0-1,0.447-1,1s0.447,1,1,1h1c0.553,0,1-0.447,1-1S59.553,29,59,29z"/>
	<path d="M52.828,16.536c0.256,0,0.512-0.098,0.707-0.293l0.707-0.707c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0
		l-0.707,0.707c-0.391,0.391-0.391,1.023,0,1.414C52.316,16.438,52.572,16.536,52.828,16.536z"/>
	<path d="M55.657,13.707c0.256,0,0.512-0.098,0.707-0.293l0.707-0.707c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0
		L54.95,12c-0.391,0.391-0.391,1.023,0,1.414C55.146,13.609,55.401,13.707,55.657,13.707z"/>
	<path d="M50,19.364c0.256,0,0.512-0.098,0.707-0.293l0.707-0.707c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0
		l-0.707,0.707c-0.391,0.391-0.391,1.023,0,1.414C49.488,19.267,49.744,19.364,50,19.364z"/>
	<path d="M50.632,23.1l-0.948,0.316c-0.524,0.175-0.807,0.741-0.632,1.265c0.14,0.419,0.529,0.684,0.948,0.684
		c0.104,0,0.212-0.017,0.316-0.052l0.948-0.316c0.524-0.175,0.807-0.741,0.632-1.265S51.155,22.924,50.632,23.1z"/>
	<path d="M53.795,24.1c0.104,0,0.212-0.017,0.316-0.052l0.948-0.316c0.524-0.175,0.807-0.741,0.632-1.265s-0.74-0.808-1.265-0.632
		l-0.948,0.316c-0.524,0.175-0.807,0.741-0.632,1.265C52.986,23.835,53.376,24.1,53.795,24.1z"/>
	<path d="M56.641,22.149c0.14,0.42,0.53,0.685,0.949,0.685c0.104,0,0.211-0.017,0.315-0.051l0.948-0.315
		c0.524-0.175,0.808-0.741,0.634-1.265c-0.175-0.524-0.742-0.806-1.265-0.634l-0.948,0.315C56.75,21.06,56.467,21.626,56.641,22.149
		z"/>
	<path d="M56.364,46.586c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414l0.707,0.707C55.853,48.902,56.108,49,56.364,49
		s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414L56.364,46.586z"/>
	<path d="M53.535,43.757c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414l0.707,0.707c0.195,0.195,0.451,0.293,0.707,0.293
		s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414L53.535,43.757z"/>
	<path d="M50.707,40.929c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414L50,43.05c0.195,0.195,0.451,0.293,0.707,0.293
		s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414L50.707,40.929z"/>
	<path d="M53.479,37.849l0.948,0.316c0.104,0.035,0.212,0.052,0.316,0.052c0.419,0,0.809-0.265,0.948-0.684
		c0.175-0.523-0.107-1.09-0.632-1.265l-0.948-0.316c-0.522-0.177-1.09,0.108-1.265,0.632S52.954,37.674,53.479,37.849z"/>
	<path d="M51.265,35.004l-0.948-0.316c-0.523-0.177-1.09,0.108-1.265,0.632s0.107,1.09,0.632,1.265l0.948,0.316
		c0.104,0.035,0.212,0.052,0.316,0.052c0.419,0,0.809-0.265,0.948-0.684C52.071,35.745,51.789,35.179,51.265,35.004z"/>
	<path d="M58.854,37.532l-0.948-0.315c-0.522-0.173-1.091,0.109-1.265,0.634c-0.174,0.523,0.109,1.09,0.634,1.265l0.948,0.315
		c0.104,0.034,0.211,0.051,0.315,0.051c0.419,0,0.81-0.265,0.949-0.685C59.661,38.273,59.378,37.707,58.854,37.532z"/>

</svg>


								</div>
								<div class="button-text  estado">
									<?php echo $ticket->isAssigned() ? __('Assign') :  __('Reassign'); ?>
								</div>
							</div>
							        
						</div>
					</a>          
					<div id="action-dropdown-assign" class="action-dropdown anchor-right">
					  <ul>
						 <li><a class="no-pjax ticket-action"
							data-redirect="tickets.php"
							href="#tickets/<?php echo $ticket->getId(); ?>/assign/<?php echo $thisstaff->getId(); ?>"><i
							class="icon-chevron-sign-down"></i> <?php echo __('Claim'); ?></a>
						 <li><a class="no-pjax ticket-action"
							data-redirect="tickets.php"
							href="#tickets/<?php echo $ticket->getId(); ?>/assign/agents"><i
							class="icon-user"></i> <?php echo __('Agent'); ?></a>
						 <li><a class="no-pjax ticket-action"
							data-redirect="tickets.php"
							href="#tickets/<?php echo $ticket->getId(); ?>/assign/teams"><i
							class="icon-group"></i> <?php echo __('Team'); ?></a>
					  </ul>
					</div>
					<?php
					} ?>	
	</div>
	<div id="button-transfer">
					<?php
					// Transfer
					if ($role->hasPerm(TicketModel::PERM_TRANSFER)) {?>
					<a class="ticket-action action-button" id="ticket-transfer" data-redirect="tickets.php" href="#tickets/<?php echo $ticket->getId(); ?>/transfer">
						<div id="status" class="action-button ticket-action gray">
							<div id="button-inner">						
								<div class="button-icon  miclass">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 49.7 49.7" style="enable-background:new 0 0 49.7 49.7;width: 27px;padding: 5px 0px 4px 5px;" xml:space="preserve">
<g>
	<path d="M27,13.85h9v8.964l13.7-9.964L36,2.886v8.964h-9c-7.168,0-13,5.832-13,13c0,6.065-4.935,11-11,11H1c-0.553,0-1,0.447-1,1
		s0.447,1,1,1h2c7.168,0,13-5.832,13-13C16,18.785,20.935,13.85,27,13.85z M38,6.814l8.3,6.036L38,18.886V6.814z"/>
	<path d="M1,13.85h2c2.713,0,5.318,0.994,7.336,2.799c0.191,0.171,0.43,0.255,0.667,0.255c0.274,0,0.548-0.112,0.745-0.333
		c0.368-0.412,0.333-1.044-0.078-1.412C9.285,13.025,6.206,11.85,3,11.85H1c-0.553,0-1,0.447-1,1S0.447,13.85,1,13.85z"/>
	<path d="M36,35.85h-9c-2.685,0-5.27-0.976-7.278-2.748c-0.411-0.365-1.044-0.327-1.411,0.089c-0.365,0.414-0.326,1.046,0.089,1.411
		c2.374,2.095,5.429,3.248,8.601,3.248h9v8.964l13.7-9.964L36,26.886V35.85z M38,30.814l8.3,6.036L38,42.886V30.814z"/>

</svg>
								</div>
								<div class="button-text  estado">
									<?php echo __('Transfer'); ?>
								</div>
							</div>
						</div>
					</a>
					<?php
					} ?>	
	</div>
	<div id="button-print">
					<a id="ticket-print" href="tickets.php?id=<?php echo $ticket->getId(); ?>&a=print">
						<div id="ticket-print-button" class="action-button change-status gray" data-dropdown="#action-dropdown-print">
							<div id="button-inner">						
								<div class="button-icon">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 59 59" style="enable-background:new 0 0 59 59;width: 27px;padding: 5px 0px 4px 5px;" xml:space="preserve">
<g>
	<path d="M14,7c0-3.859-3.141-7-7-7S0,3.141,0,7v45h0.003C0.003,52.02,0,52.04,0,52.06C0,55.887,3.113,59,6.94,59H59V7H14z M2,7
		c0-2.421,1.731-4.444,4.02-4.901C6.072,2.603,6.483,3,7,3s0.928-0.397,0.98-0.901C10.269,2.556,12,4.579,12,7v40.105
		C10.729,45.807,8.957,45,7,45c-0.243,0-0.482,0.013-0.719,0.037c-0.106,0.011-0.21,0.032-0.315,0.048
		c-0.128,0.019-0.257,0.034-0.383,0.06c-0.138,0.028-0.271,0.068-0.405,0.104C5.09,45.273,5,45.291,4.913,45.318
		c-0.167,0.053-0.329,0.116-0.491,0.181c-0.049,0.02-0.101,0.035-0.15,0.056c-0.199,0.084-0.392,0.18-0.582,0.282
		c-0.008,0.004-0.017,0.008-0.025,0.012C3.049,46.184,2.487,46.608,2,47.105V7z M57,57H6.94C4.216,57,2,54.784,2,52
		c0-2.757,2.243-5,5-5s5,2.243,5,5h2V9h43V57z"/>
	<path d="M46.017,21.001c0.176,0.123,0.377,0.183,0.576,0.183c0.314,0,0.623-0.147,0.818-0.423c0.318-0.451,0.211-1.075-0.24-1.395
		l-1.634-1.153c-0.453-0.317-1.075-0.21-1.395,0.24c-0.318,0.451-0.211,1.075,0.24,1.395L46.017,21.001z"/>
	<path d="M50.92,24.461c0.175,0.123,0.376,0.183,0.575,0.183c0.314,0,0.623-0.147,0.818-0.424c0.318-0.451,0.21-1.075-0.241-1.394
		l-1.635-1.153c-0.451-0.317-1.074-0.21-1.394,0.241c-0.318,0.451-0.21,1.075,0.241,1.394L50.92,24.461z"/>
	<path d="M37,41c0,0.553,0.447,1,1,1h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2C37.447,40,37,40.447,37,41z"/>
	<path d="M51,27.383v2c0,0.553,0.447,1,1,1s1-0.447,1-1v-2c0-0.553-0.447-1-1-1S51,26.83,51,27.383z"/>
	<path d="M41.115,17.541c0.176,0.123,0.377,0.183,0.576,0.183c0.314,0,0.623-0.147,0.818-0.423c0.318-0.451,0.211-1.075-0.24-1.395
		l-1.634-1.153c-0.453-0.317-1.075-0.21-1.395,0.24c-0.318,0.451-0.211,1.075,0.24,1.395L41.115,17.541z"/>
	<path d="M43,41c0,0.553,0.447,1,1,1h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2C43.447,40,43,40.447,43,41z"/>
	<path d="M29,41c0-0.553-0.447-1-1-1h-2c-0.553,0-1,0.447-1,1s0.447,1,1,1h2C28.553,42,29,41.553,29,41z"/>
	<path d="M52,36.383c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v2C51,35.936,51.447,36.383,52,36.383z"/>
	<path d="M36.213,14.081c0.176,0.123,0.377,0.183,0.576,0.183c0.314,0,0.623-0.147,0.818-0.423c0.318-0.451,0.211-1.075-0.24-1.395
		l-1.634-1.153c-0.453-0.316-1.075-0.209-1.395,0.24c-0.318,0.451-0.211,1.075,0.24,1.395L36.213,14.081z"/>
	<path d="M51,40h-1c-0.553,0-1,0.447-1,1s0.447,1,1,1h3v-2.617c0-0.553-0.447-1-1-1s-1,0.447-1,1V40z"/>
	<path d="M20,42h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.447-1,1S19.447,42,20,42z"/>
	<path d="M18,28c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v2C17,27.553,17.447,28,18,28z"/>
	<path d="M20.452,23.27c0.199,0,0.4-0.06,0.576-0.183l1.634-1.153c0.451-0.319,0.559-0.943,0.24-1.395
		c-0.318-0.45-0.941-0.558-1.395-0.24l-1.634,1.153c-0.451,0.319-0.559,0.943-0.24,1.395C19.829,23.122,20.138,23.27,20.452,23.27z"
		/>
	<path d="M25.354,19.81c0.199,0,0.4-0.06,0.576-0.183l1.634-1.153c0.451-0.319,0.559-0.943,0.24-1.395
		c-0.319-0.449-0.941-0.557-1.395-0.24l-1.634,1.153c-0.451,0.319-0.559,0.943-0.24,1.395C24.73,19.662,25.039,19.81,25.354,19.81z"
		/>
	<path d="M18,34c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v2C17,33.553,17.447,34,18,34z"/>
	<path d="M30.256,16.35c0.199,0,0.4-0.06,0.576-0.183l1.634-1.153c0.451-0.319,0.559-0.943,0.24-1.395
		c-0.319-0.449-0.94-0.559-1.395-0.24l-1.634,1.153c-0.451,0.319-0.559,0.943-0.24,1.395C29.633,16.202,29.941,16.35,30.256,16.35z"
		/>
	<path d="M31,41c0,0.553,0.447,1,1,1h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2C31.447,40,31,40.447,31,41z"/>
	<path d="M18,40c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v2C17,39.553,17.447,40,18,40z"/>
	<path d="M41,54h13v-8H41V54z M43,48h9v4h-9V48z"/>
	<path d="M22,30v2c0,0.553,0.447,1,1,1s1-0.447,1-1v-2c0-0.553-0.447-1-1-1S22,29.447,22,30z"/>
	<path d="M31,27c-0.553,0-1,0.447-1,1v2c0,0.553,0.447,1,1,1s1-0.447,1-1v-2C32,27.447,31.553,27,31,27z"/>
	<path d="M25,29h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.447-1,1S24.447,29,25,29z"/>
	<path d="M26,36c0-0.553-0.447-1-1-1h-2c-0.553,0-1,0.447-1,1s0.447,1,1,1h2C25.553,37,26,36.553,26,36z"/>
	<path d="M29,37h2c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v1h-1c-0.553,0-1,0.447-1,1S28.447,37,29,37z"/>
	<path d="M40,28c0,0.553,0.447,1,1,1h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2C40.447,27,40,27.447,40,28z"/>
	<path d="M46,28v2c0,0.553,0.447,1,1,1s1-0.447,1-1v-2c0-0.553-0.447-1-1-1S46,27.447,46,28z"/>
	<path d="M47,37c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v1h-1c-0.553,0-1,0.447-1,1s0.447,1,1,1H47z"/>
	<path d="M39,33c0.553,0,1-0.447,1-1v-2c0-0.553-0.447-1-1-1s-1,0.447-1,1v2C38,32.553,38.447,33,39,33z"/>
	<path d="M39,37h2c0.553,0,1-0.447,1-1s-0.447-1-1-1h-2c-0.553,0-1,0.447-1,1S38.447,37,39,37z"/>
	<path d="M38,46h-5c-0.553,0-1,0.447-1,1s0.447,1,1,1h5c0.553,0,1-0.447,1-1S38.553,46,38,46z"/>
	<path d="M38,49h-7c-0.553,0-1,0.447-1,1s0.447,1,1,1h7c0.553,0,1-0.447,1-1S38.553,49,38,49z"/>
	<path d="M38,52H28c-0.553,0-1,0.447-1,1s0.447,1,1,1h10c0.553,0,1-0.447,1-1S38.553,52,38,52z"/>
	<circle cx="4" cy="5" r="1"/>
	<circle cx="10" cy="5" r="1"/>
	<circle cx="7" cy="8" r="1"/>
	<circle cx="4" cy="11" r="1"/>
	<circle cx="10" cy="11" r="1"/>
	<circle cx="4" cy="17" r="1"/>
	<circle cx="10" cy="17" r="1"/>
	<circle cx="7" cy="14" r="1"/>
	<circle cx="7" cy="20" r="1"/>
	<circle cx="4" cy="23" r="1"/>
	<circle cx="10" cy="23" r="1"/>
	<circle cx="4" cy="29" r="1"/>
	<circle cx="10" cy="29" r="1"/>
	<circle cx="7" cy="26" r="1"/>
	<circle cx="7" cy="32" r="1"/>
	<circle cx="4" cy="35" r="1"/>
	<circle cx="10" cy="35" r="1"/>
	<circle cx="4" cy="41" r="1"/>
	<circle cx="10" cy="41" r="1"/>
	<circle cx="7" cy="38" r="1"/>
	<circle cx="7" cy="44" r="1"/>

</svg>

								</div>
								<div class="button-text estado">
									<?php echo __('Print'); ?>
								</div>
							</div>
						</div>
					</a> 
         <div id="action-dropdown-print" class="action-dropdown anchor-right">
              <ul>
                 <li><a class="no-pjax" target="_blank" href="tickets.php?id=<?php echo $ticket->getId(); ?>&a=print&notes=0"><i
                 class="icon-file-alt"></i> <?php echo __('Ticket Thread'); ?></a>
                 <li><a class="no-pjax" target="_blank" href="tickets.php?id=<?php echo $ticket->getId(); ?>&a=print&notes=1"><i
                 class="icon-file-text-alt"></i> <?php echo __('Thread + Internal Notes'); ?></a>
              </ul>
         </div>					
	</div>
	
</div>
<!-- End Ticket View Buttons -->

<div class="clear"></div>	   

			<div id="ticket-user-count-title">
				<div class="ticket-view-user">        
					<a class="ticket-view-user-profile" href="#tickets/<?php echo $ticket->getId(); ?>/user"
						onclick="javascript:
							$.userLookup('ajax.php/tickets/<?php echo $ticket->getId(); ?>/user',
									function (user) {
										$('#user-'+user.id+'-name').text(user.name);
										$('#user-'+user.id+'-email').text(user.email);
										$('#user-'+user.id+'-phone').text(user.phone);
										$('select#emailreply option[value=1]').text(user.name+' <'+user.email+'>');
									});
							return false;
							"><i class="material-icons ticket-view-account-icon">account_box</i> <span id="user-<?php echo $ticket->getOwnerId(); ?>-name"
							><?php echo Format::htmlchars($ticket->getName());
						?></span>
					</a>
				</div> 
				<?php
				if ($user) { ?>
				<div class="desktop-ticket-view-ticket-count">
					<a  id="ticket-view-number" href="tickets.php?<?php echo Http::build_query(array(
						'status'=>'open', 'a'=>'search', 'uid'=> $user->getId()
						)); ?>" title="<?php echo __('Related Tickets'); ?>"
						data-dropdown="#action-dropdown-stats">
						<?php echo $user->getNumTickets(); ?>
					</a>
				</div>
				<div id="action-dropdown-stats" class="action-dropdown anchor-right">
					<ul>
						<?php
						if(($open=$user->getNumOpenTickets()))
							echo sprintf('<li><a href="tickets.php?a=search&status=open&uid=%s"><i class="icon-folder-open-alt icon-fixed-width"></i> %s</a></li>',
									$user->getId(), sprintf(_N('%d Open Ticket', '%d Open Tickets', $open), $open));
	 
						if(($closed=$user->getNumClosedTickets()))
							echo sprintf('<li><a href="tickets.php?a=search&status=closed&uid=%d"><i
									class="icon-folder-close-alt icon-fixed-width"></i> %s</a></li>',
									$user->getId(), sprintf(_N('%d Closed Ticket', '%d Closed Tickets', $closed), $closed));
						?>
						<li><a href="tickets.php?a=search&uid=<?php echo $ticket->getOwnerId(); ?>">
									<svg viewBox="0 0 24 24">
										<path d="M5,13H19V11H5M3,17H17V15H3M7,7V9H21V7"></path>
									</svg>
									<?php echo __('All Tickets'); ?></a>
						</li>
			<?php   if ($thisstaff->hasPerm(User::PERM_DIRECTORY)) { ?>
						<li><a href="users.php?id=<?php echo
						$user->getId(); ?>">
									<svg viewBox="0 0 24 24">
										<path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"></path>
									</svg> <?php echo __('Manage User'); ?></a>
						</li>
			<?php   } ?>
					</ul>
				</div>
			<?php                   } # end if ($user) ?>
				<div class="ticket-view-title">
					<h3><?php echo Format::htmlchars($ticket->getSubject()); ?></h3>
				</div> 
				<div class="pull-right"><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 57 57" style="enable-background:new 0 0 57 57;width: 27px;padding: 5px 0px 4px 5px;" xml:space="preserve">
<path d="M57,12.002H41.741C42.556,10.837,43,9.453,43,7.995c0-1.875-0.726-3.633-2.043-4.95c-2.729-2.729-7.17-2.729-9.899,0
	l-2.829,2.829l-2.828-2.829c-2.729-2.729-7.17-2.729-9.899,0c-1.317,1.317-2.043,3.075-2.043,4.95c0,1.458,0.444,2.842,1.259,4.007
	H0v14h5v30h48v-30h4V12.002z M32.472,4.459c1.95-1.949,5.122-1.949,7.071,0C40.482,5.399,41,6.654,41,7.995
	c0,1.34-0.518,2.596-1.457,3.535l-0.472,0.472H24.929l4.714-4.714l0,0L32.472,4.459z M16.916,11.53
	c-0.939-0.939-1.457-2.195-1.457-3.535c0-1.341,0.518-2.596,1.457-3.536c1.95-1.949,5.122-1.949,7.071,0l2.828,2.829l-3.535,3.535
	c-0.207,0.207-0.397,0.441-0.581,0.689c-0.054,0.073-0.107,0.152-0.159,0.229c-0.06,0.088-0.123,0.167-0.18,0.26h-4.972
	L16.916,11.53z M2,24.002v-10h14.559h4.733h2.255H28v10H5H2z M28,26.002v12H7v-12H28z M7,40.002h21v14H7V40.002z M30,54.002v-14h21
	v14H30z M51,38.002H30v-12h21V38.002z M55,24.002h-2H30v-10h9.899H55V24.002z"/>

</svg><h3 style="display: inline;
font-size: 17px;
padding-left: 10px;
font-weight: lighter;
text-transform: capitalize;
background: #454d66;
padding-right: 10px;
border-radius: 8px;
color: #fff;
margin-left: 6px;"><?php echo ($S = $ticket->getStatus()) ? $S->getLocalName() : ''; ?></h3></div>
			</div>			
			<div class="clear"></div>
		</div>
	</div>
</div>


<?php
$tcount = $ticket->getThreadEntries($types)->count();
?>
<ul  class="tabs clean threads tabla1" id="ticket_tabs" >
	<li class="active  tabla2" style="min-width: 120px"><a href="#propuesta">Propuesta</a></li>
    <li class="tabw tabla2" style="min-width: 120px"><a href="#ticket_thread"><?php echo sprintf(__('Comentarios (%d)'), $tcount); ?></a></li>
    <li class="tabw tabla2" style="min-width: 120px"><a id="ticket-tasks-tab" href="#tasks"
            data-url="<?php echo sprintf('#tickets/%d/tasks', $ticket->getId()); ?>">
        
        <?php
        echo __('Tareas');
        if ($ticket->getNumTasks())
            echo sprintf('&nbsp;(%d)', $ticket->getNumTasks());
        ?></a></li>
</ul>

<div id="ticket_tabs_container">
	
<div id="propuesta" class="tab_content">
	
<div class="responsive-div ticket_info ticket-view">
	<div id="ticket-view-one">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="120"><?php echo __('Status');?></th>
                    <td><?php echo ($S = $ticket->getStatus()) ? $S->getLocalName() : ''; ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Priority');?></th>
                    <td><?php echo $ticket->getPriority(); ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Department');?></th>
                    <td><?php echo Format::htmlchars($ticket->getDeptName()); ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Create Date');?></th>
                    <td><?php echo Format::datetime($ticket->getCreateDate()); ?></td>
                </tr>
                <?php
                if($ticket->isOpen()) { ?>
                <tr>
                    <th width="120"><?php echo __('Assigned To');?></th>
                    <td>
                        <?php
                        if($ticket->isAssigned())
                            echo Format::htmlchars(implode('/', $ticket->getAssignees()));
                        else
                            echo '<span class="ticket-unassigned">&mdash; '.__('Unassigned').' &mdash;</span>';
                        ?>
                    </td>
                </tr>
                <?php
                } else { ?>
                <tr>
                    <th width="120"><?php echo __('Closed By');?></th>
                    <td>
                        <?php
                        if(($staff = $ticket->getStaff()))
                            echo Format::htmlchars($staff->getName());
                        else
                            echo '<span class="faded">&mdash; '.__('Unknown').' &mdash;</span>';
                        ?>
                    </td>
                </tr>
                <?php
                } ?>
                <tr>
                    <th><?php echo __('SLA Plan');?></th>
                    <td><?php echo $sla?Format::htmlchars($sla->getName()):'<span class="faded">&mdash; '.__('None').' &mdash;</span>'; ?></td>
                </tr>
                <?php
                if($ticket->isOpen()){ ?>
                <tr>
                    <th><?php echo __('Due Date');?></th>
                    <td><?php echo Format::datetime($ticket->getEstDueDate()); ?></td>
                </tr>
                <?php
                }else { ?>
                <tr>
                    <th><?php echo __('Close Date');?></th>
                    <td><?php echo Format::datetime($ticket->getCloseDate()); ?></td>
                </tr>
                <?php
                }
                ?>
            </table>			
	</div>			
	<div id="ticket-view-two">	
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="120"><?php echo __('Ticket'); ?></th>
                    <td>					
						<div class="ticket-view-number">
							 <a href="tickets.php?id=<?php echo $ticket->getId(); ?>"
							 title="<?php echo __('Reload'); ?>">
							 <?php echo sprintf(__('#%s'), $ticket->getNumber()); ?> 
								<svg viewBox="0 0 24 24">
									<path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
								</svg>             
							 </a>
						</div>  					
					</td>
                </tr>
                <tr>
                    <th><?php echo __('Email'); ?></th>
                    <td>
                        <span id="user-<?php echo $ticket->getOwnerId(); ?>-email"><?php echo $ticket->getEmail(); ?></span>
                    </td>
                </tr>
				<?php   if ($user->getOrgId()) { ?>
                <tr>
                    <th><?php echo __('Organization'); ?></th>
                    <td><i class="icon-building"></i>
                    <?php echo Format::htmlchars($user->getOrganization()->getName()); ?>
                        <a href="tickets.php?<?php echo Http::build_query(array(
                            'status'=>'open', 'a'=>'search', 'orgid'=> $user->getOrgId()
                        )); ?>" title="<?php echo __('Related Tickets'); ?>"
                        data-dropdown="#action-dropdown-org-stats">
                        (<b><?php echo $user->getNumOrganizationTickets(); ?></b>)
                        </a>
                            <div id="action-dropdown-org-stats" class="action-dropdown anchor-right">
                                <ul>
				<?php   if ($open = $user->getNumOpenOrganizationTickets()) { ?>
                                    <li><a href="tickets.php?<?php echo Http::build_query(array(
                                        'a' => 'search', 'status' => 'open', 'orgid' => $user->getOrgId()
                                    )); ?>"><i class="icon-folder-open-alt icon-fixed-width"></i>
                                    <?php echo sprintf(_N('%d Open Ticket', '%d Open Tickets', $open), $open); ?>
                                    </a></li>
				<?php   }
						if ($closed = $user->getNumClosedOrganizationTickets()) { ?>
                                    <li><a href="tickets.php?<?php echo Http::build_query(array(
                                        'a' => 'search', 'status' => 'closed', 'orgid' => $user->getOrgId()
                                    )); ?>"><i class="icon-folder-close-alt icon-fixed-width"></i>
                                    <?php echo sprintf(_N('%d Closed Ticket', '%d Closed Tickets', $closed), $closed); ?>
                                    </a></li>
                                    <li><a href="tickets.php?<?php echo Http::build_query(array(
                                        'a' => 'search', 'orgid' => $user->getOrgId()
                                    )); ?>"><svg viewBox="0 0 24 24"><path d="M5,13H19V11H5M3,17H17V15H3M7,7V9H21V7"></path></svg> <?php echo __('All Tickets'); ?></a></li>
				<?php   }
						if ($thisstaff->hasPerm(User::PERM_DIRECTORY)) { ?>
													<li><a href="orgs.php?id=<?php echo $user->getOrgId(); ?>"><i
                                        class="icon-building icon-fixed-width"></i> <?php
                                        echo __('Manage Organization'); ?></a></li>
				<?php   } ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
				<?php   } # end if (user->org) ?>
                <tr>
                    <th><?php echo __('Source'); ?></th>
                    <td><?php
                        echo Format::htmlchars($ticket->getSource());
 
                        if($ticket->getIP())
                            echo '&nbsp;&nbsp; <span class="faded">('.$ticket->getIP().')</span>';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th width="120"><?php echo __('Help Topic');?></th>
                    <td><?php echo Format::htmlchars($ticket->getHelpTopic()); ?></td>
                </tr>
                <tr>
                    <th nowrap><?php echo __('Last Message');?></th>
                    <td><?php echo Format::datetime($ticket->getLastMsgDate()); ?></td>
                </tr>
                <tr>
                    <th nowrap><?php echo __('Last Response');?></th>
                    <td><?php echo Format::datetime($ticket->getLastRespDate()); ?></td>
                </tr>
            </table>
 	</div>
</div>	
<br>
<div class="clear"></div>

<?php
/*foreach (DynamicFormEntry::forTicket($ticket->getId()) as $form) {
    // Skip core fields shown earlier in the ticket view
    // TODO: Rewrite getAnswers() so that one could write
    //       ->getAnswers()->filter(not(array('field__name__in'=>
    //           array('email', ...))));
    $answers = $form->getAnswers()->exclude(Q::any(array(
        'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
        'field__name__in' => array('subject', 'priority')
    )));
    $displayed = array();
    foreach($answers as $a) {
        if (!($v = $a->display()))
            continue;
        $displayed[] = array($a->getLocal('label'), $v);
    }
    if (count($displayed) == 0)
        continue;*/
    ?>
    <!-- <table class="ticket_info custom-data" cellspacing="0" cellpadding="0" width="100%" border="0">
    <thead>
        <th colspan="2"><?php //echo Format::htmlchars($form->getTitle()); ?></th>
    </thead>
    <tbody> -->
<?php
    /*foreach ($displayed as $stuff) {
        list($label, $v) = $stuff;*/
?>
        <!-- <tr>
            <td width="20%"> --><?php
		//echo Format::htmlchars($label);
            ?><!-- </th>
            <td> --><?php
		//echo $v;
            ?><!-- </td>
        </tr> -->
<?php //} ?>
    <!-- </tbody>
    </table> -->
<?php //} ?>

<!-- /// Cambio Aplicado por FCOLMENAREZ   -->
<?php 	/*$staff_id=$_SESSION["_auth"]["staff"]["id"];
		$aux_octask=$ticket->getOCTaskResumen($ticket->getId(),$staff_id);

	if (count($aux_octask)>0){
		$tabla.='<div class="responsive-div ticket_info ticket-view">';
		$tabla.='<table class="ticket_info custom-data" cellspacing="0" cellpadding="0" width="100%" border="0">';
		$tabla.='<tr><th colspan="8"><B>Resumen de la Orden de Servicio</B></th></tr>';
		$tabla.='<tr><th><B>An&aacute;lisis</B></th>
					 <th><B>Realizaci&oacute;n</B></th>
					 <th><B>Testing</B></th>
					 <th><B>Total Estimaci&oacute;n</B></th>
					 <th><B>Gesti&oacute;n de An&aacute;lisis</B></th>
					 <th><B>Gesti&oacute;n de Realizaci&oacute;n</B></th>
					 <th><B>Gesti&oacute;n de Testing</B></th>
					 <th><B>Total Gesti&oacute;n</B></th>
				 </tr>';
		
		$tabla.='<tr style="text-align:center;">
					<td>'.$aux_octask["analisis"].'</td>
					<td>'.$aux_octask["realizaciones"].'</td>
					<td>'.$aux_octask["testing"].'</td>
					<td>'.$aux_octask["estimacion"].'</td>
					<td>'.$aux_octask["holanalisis"].'</td>
					<td>'.$aux_octask["holrealizacion"].'</td>
					<td>'.$aux_octask["holtesting"].'</td>
					<td>'.$aux_octask["totalholgura"].'</td>
				</tr>';
		

		$tabla.='</table></div>';
		echo $tabla;
	}/// FIN DEL IF ------------------------------------
*/
	$staff_id=$_SESSION["_auth"]["staff"]["id"];
	$aux_octask=$ticket->getOCResumen($ticket->getId(),$staff_id);
	if (count($aux_octask)>0){

		$tabla='<div class="responsive-div ticket_info ticket-view">';
		$tabla.='<table class="ticket_info custom-data" cellspacing="0" cellpadding="0" width="100%" border="0">';
		$tabla.='<tr><th colspan="8"><B>'.$aux_octask[0]["title"].'</B></th></tr>';
		
		foreach ($aux_octask as $value) {
			$tabla.='<tr><td width="20%"><B>'.$value["label"].'<B></td><td>'.$value["value"].'</td></tr>';			
		}
		$tabla.='</table></div>';
		echo $tabla;	

	}/// FIN DEL IF ------------------------------------------

?>
<!-- /// FIN DEL CAMBIO-->

<div class="clear"></div>
	
	
</div><!-- end tab_content propuesta --->



<div id="ticket_thread" class="tab_content" style="display: none;"> 
	
<?php
    // Render ticket thread 
    
    //print_r($ticket->getThreadId());
    
 
    
    $ticket->getThread()->render(
            array('M', 'R', 'N'),
            array(
                'html-id' => 'ticketThread',
                'mode' => Thread::MODE_STAFF)
            );
?>

<div class="clear"></div>
<?php if($errors['err']) { ?>
    <div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text"><?php echo $errors['err']; ?></div></div>
<?php }elseif($msg) { ?>
    <div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text"><?php echo $msg; ?></div></div>
<?php }elseif($warn) { ?>
    <div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text"><?php echo $warn; ?></div></div>
<?php } ?>

</div><!-- end tab_content ticket_thread -->



	
</div> <!-- fin ticket_tabs_container -->


<br>
<div class="clear"></div>





<div class="sticky bar stop actions" id="response_options">
    <ul class="tabs">
        <?php
        if ($role->hasPerm(TicketModel::PERM_REPLY)) { ?>
        <li class="active"><a href="#reply"><?php echo __('Post Reply');?></a></li>
        <?php
        } ?>
        <li><a href="#note"><?php echo __('Post Internal Note');?></a></li>
    </ul>
    <?php
    if ($role->hasPerm(TicketModel::PERM_REPLY)) { ?>
    <form id="reply" class="tab_content spellcheck exclusive"
        data-lock-object-id="ticket/<?php echo $ticket->getId(); ?>"
        data-lock-id="<?php echo $mylock ? $mylock->getId() : ''; ?>"
        action="tickets.php?id=<?php
        echo $ticket->getId(); ?>#reply" name="reply" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
        <input type="hidden" name="msgId" value="<?php echo $msgId; ?>">
        <input type="hidden" name="a" value="reply">
        <input type="hidden" name="lockCode" value="<?php echo $mylock ? $mylock->getCode() : ''; ?>">
        <span class="error"></span>
        <table id="ticket-view-reply" style="width:100%" border="0" cellspacing="0" cellpadding="0">
           <tbody id="to_sec">
            <tr>
                <td width="120">
                    <label><strong><?php echo __('To'); ?></strong></label>
                </td>
                <td>
                    <?php
                    # XXX: Add user-to-name and user-to-email HTML ID#s
                    $to =sprintf('%s &lt;%s&gt;',
                            Format::htmlchars($ticket->getName()),
                            $ticket->getReplyToEmail());
                    $emailReply = (!isset($info['emailreply']) || $info['emailreply']);
                    ?>
                    <select id="emailreply" name="emailreply">
                        <option value="1" <?php echo $emailReply ?  'selected="selected"' : ''; ?>><?php echo $to; ?></option>
                        <option value="0" <?php echo !$emailReply ? 'selected="selected"' : ''; ?>
                        >&mdash; <?php echo __('Do Not Email Reply'); ?> &mdash;</option>
                    </select>
                </td>
            </tr>
            </tbody>
            <?php
            if(1) { //Make CC optional feature? NO, for now.
                ?>
           
            <tbody id="cc_sec"
                style="display:<?php echo $emailReply?  'table-row-group':'none'; ?>;">
             <tr>
                <td width="120">
                    <label><strong><?php echo __('Collaborators'); ?></strong></label>
                </td>
                <td>
                    <input type='checkbox' value='1' name="emailcollab" id="emailcollab"
                        <?php echo ((!$info['emailcollab'] && !$errors) || isset($info['emailcollab']))?'checked="checked"':''; ?>
                        style="display:<?php echo $ticket->getThread()->getNumCollaborators() ? 'inline-block': 'none'; ?>;"
                        >
                    <?php
                    $recipients = __('Add Recipients');
                    if ($ticket->getThread()->getNumCollaborators())
                        $recipients = sprintf(__('Recipients (%d of %d)'),
                                $ticket->getThread()->getNumActiveCollaborators(),
                                $ticket->getThread()->getNumCollaborators());
 
                    echo sprintf('<span><a class="collaborators preview"
                            href="#thread/%d/collaborators"><span id="t%d-recipients">%s</span></a></span>',
                            $ticket->getThreadId(),
                            $ticket->getThreadId(),
                            $recipients);
                   ?>
                </td>
             </tr>
            </tbody>
          
            
            <?php
            } ?>
            <tbody id="resp_sec">
            <?php
            if($errors['response']) {?>
            <tr><td width="120">&nbsp;</td><td class="error"><?php echo $errors['response']; ?>&nbsp;</td></tr>
            <?php
            }?>
            <tr>
                <td width="120" style="vertical-align:top">
                    <label><strong><?php echo __('Response');?></strong></label>
                </td>
                <td>
					<?php if ($cfg->isCannedResponseEnabled()) { ?>
                    <select id="cannedResp" name="cannedResp">
                        <option value="0" selected="selected"><?php echo __('Select a canned response');?></option>
                        <option value='original'><?php echo __('Original Message'); ?></option>
                        <option value='lastmessage'><?php echo __('Last Message'); ?></option>
                        <?php
                        if(($cannedResponses=Canned::responsesByDeptId($ticket->getDeptId()))) {
                            echo '<option value="0" disabled="disabled">
                                ------------- '.__('Premade Replies').' ------------- </option>';
                            foreach($cannedResponses as $id =>$title)
                                echo sprintf('<option value="%d">%s</option>',$id,$title);
                        }
                        ?>
                    </select>
                    <br>
					<?php } # endif (canned-resonse-enabled)
                    $signature = '';
                    switch ($thisstaff->getDefaultSignatureType()) {
                    case 'dept':
                        if ($dept && $dept->canAppendSignature())
                           $signature = $dept->getSignature();
                       break;
                    case 'mine':
                        $signature = $thisstaff->getSignature();
                        break;
                    } ?>
                    <input type="hidden" name="draft_id" value=""/>
                    <textarea name="response" id="response" cols="50"
                        data-signature-field="signature" data-dept-id="<?php echo $dept->getId(); ?>"
                        data-signature="<?php
                            echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                        placeholder="<?php echo __(
                        'Start writing your response here. Use canned responses from the drop-down above'
                        ); ?>"
                        rows="9" wrap="soft"
                        class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft draft-delete" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.response', $ticket->getId(), $info['response']);
    echo $attrs; ?>><?php echo $_POST ? $info['response'] : $draft;
                    ?></textarea>
                <div id="reply_form_attachments" class="attachments">
                <?php
                    print $response_form->getField('attachments')->render();
                ?>
                </div>
                </td>
            </tr>
            <tr>
                <td width="120" id="ticket-view-signature-td">
                    <label for="signature" class="left"><?php echo __('Signature');?></label>
                </td>
                <td>
                    <?php
                    $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                    <label id="ticket-view-signature-options"><input type="radio" name="signature" value="none" checked="checked"> <?php echo __('None');?></label>
                    <?php
                    if($thisstaff->getSignature()) {?>
                    <label><input type="radio" name="signature" value="mine" 
                        <?php echo ($info['signature']=='mine')?'checked="checked"':''; ?>> <?php echo __('My Signature');?></label>
                    <?php
                    } ?>
                    <?php
                    if($dept && $dept->canAppendSignature()) { ?>
                    <label><input type="radio" name="signature" value="dept"
                        <?php echo ($info['signature']=='dept')?'checked="checked"':''; ?>>
                        <?php echo sprintf(__('Department Signature (%s)'), Format::htmlchars($dept->getName())); ?></label>
                    <?php
                    } ?>
                </td>
            </tr>
            <tr>
                <td width="120" style="vertical-align: top; padding: 0px;">
                    <label><strong><?php echo __('Ticket Status');?></strong></label>
                </td>
                <td>
                    <?php
                    $outstanding = false;
                    if ($role->hasPerm(TicketModel::PERM_CLOSE)
                            && is_string($warning=$ticket->isCloseable())) {
                        $outstanding =  true;
                        echo sprintf('<div class="warning-banner">%s</div>', $warning);
                    } ?>
                    <select name="reply_status_id">
                    <?php
                    $statusId = $info['reply_status_id'] ?: $ticket->getStatusId();
                    $states = array('open');
                    if ($role->hasPerm(TicketModel::PERM_CLOSE) && !$outstanding)
                        $states = array_merge($states, array('closed'));
 
                    foreach (TicketStatusList::getStatuses(
                                array('states' => $states)) as $s) {
                        if (!$s->isEnabled()) continue;
                        $selected = ($statusId == $s->getId());
                        echo sprintf('<option value="%d" %s>%s%s</option>',
                                $s->getId(),
                                $selected
                                 ? 'selected="selected"' : '',
                                __($s->getName()),
                                $selected
                                ? (' ('.__('current').')') : ''
                                );
                    }
                    ?>
                    </select>
                </td>
            </tr>
         </tbody>
        </table>
        <p  style="text-align:center;">
            <!-- <input class="save pending" type="submit" value="<?php echo __('Post Reply');?>"> -->
            
           <!-- <<INICIO>>  Ajuste realizado por HDANDREA 05-02-18 AC000000064  – sensibilidad botón publicar respuesta y nota inter ********* -->
           
           <input id="save_reply" class="save pending" type="button" value="<?php echo __('Post Reply');?>" style="background: #d62705!important;float: right;">
           
           <!-- <<FIN>> ****************************************************************** -->
            
            <input class="" type="reset" value="<?php echo __('Reset');?>">
        </p>
    </form>
    <?php
    } ?>
    <form id="note" class="hidden tab_content spellcheck exclusive"
        data-lock-object-id="ticket/<?php echo $ticket->getId(); ?>"
        data-lock-id="<?php echo $mylock ? $mylock->getId() : ''; ?>"
        action="tickets.php?id=<?php echo $ticket->getId(); ?>#note"
        name="note" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
        <input type="hidden" name="locktime" value="<?php echo $cfg->getLockTime() * 60; ?>">
        <input type="hidden" name="a" value="postnote">
        <input type="hidden" name="lockCode" value="<?php echo $mylock ? $mylock->getCode() : ''; ?>">
        <table id="ticket-view-note" width="100%" border="0" cellspacing="0" cellpadding="0">
            <?php
            if($errors['postnote']) {?>
            <tr>
                <td width="120">&nbsp;</td>
                <td class="error"><?php echo $errors['postnote']; ?></td>
            </tr>
            <?php
            } ?>
            <tr>
                <td width="120" style="vertical-align:top">
                    <label><strong><?php echo __('Internal Note'); ?></strong><span class='error'>&nbsp;*</span></label>
                </td>
                <td>
                    <div>
                        <div class="faded" style="padding-left:0.15em"><?php
                        echo __('Note title - summary of the note (optional)'); ?></div>
                        <input type="text" name="title" id="title" size="60" value="<?php echo $info['title']; ?>" >
                        <span class="error">&nbsp;<?php echo $errors['title']; ?></span>
                    </div>
                    <div class="error"><?php echo $errors['note']; ?></div>
                    <textarea name="note" id="internal_note" cols="80"
                        placeholder="<?php echo __('Note details'); ?>"
                        rows="9" wrap="soft"
                        class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft draft-delete" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.note', $ticket->getId(), $info['note']);
    echo $attrs; ?>><?php echo $_POST ? $info['note'] : $draft;
                        ?></textarea>
                <div class="attachments">
                <?php
                    print $note_form->getField('attachments')->render();
                ?>
                </div>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label><?php echo __('Ticket Status');?></label>
                </td>
                <td>
                    <div class="faded"></div>
                    <select name="note_status_id">
                        <?php
                        $statusId = $info['note_status_id'] ?: $ticket->getStatusId();
                        $states = array('open');
                        if ($ticket->isCloseable() === true
                                && $role->hasPerm(TicketModel::PERM_CLOSE))
                            $states = array_merge($states, array('closed'));
                        foreach (TicketStatusList::getStatuses(
                                    array('states' => $states)) as $s) {
                            if (!$s->isEnabled()) continue;
                            $selected = $statusId == $s->getId();
                            echo sprintf('<option value="%d" %s>%s%s</option>',
                                    $s->getId(),
                                    $selected ? 'selected="selected"' : '',
                                    __($s->getName()),
                                    $selected ? (' ('.__('current').')') : ''
                                    );
                        }
                        ?>
                    </select>
                    &nbsp;<span class='error'>*&nbsp;<?php echo $errors['note_status_id']; ?></span>
                </td>
            </tr>
        </table>
 
       <p style="text-align:center;margin-top:20px;">
           <!-- <input class="save pending" type="submit" value="<?php echo __('Post Note');?>">  comentado por HDANDREA -->
           
           <!-- <<INICIO>>  Ajuste realizado por HDANDREA 05-02-18 AC000000064  – sensibilidad botón publicar respuesta y nota inter ********* -->
           
           <input id="save_note" class="save pending" type="button" value="<?php echo __('Post Note');?>" style="background: #d62705!important;float: right;">
           
           <!-- <<FIN>> ****************************************************************** -->
           <input class="" type="reset" value="<?php echo __('Reset');?>">
       </p>
   </form>
 </div>
 </div>
</div>
<div style="display:none;" class="dialog" id="print-options">
    <h3><?php echo __('Ticket Print Options');?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <form action="tickets.php?id=<?php echo $ticket->getId(); ?>"
        method="post" id="print-form" name="print-form" target="_blank">
        <?php csrf_token(); ?>
        <input type="hidden" name="a" value="print">
        <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
        <fieldset class="notes">
            <label class="fixed-size" for="notes"><?php echo __('Print Notes');?></label>
            <label class="inline checkbox">
            <input type="checkbox" id="notes" name="notes" value="1"> <?php echo __('Print <b>Internal</b> Notes/Comments');?>
            </label>
        </fieldset>
        <fieldset>
            <label class="fixed-size" for="psize"><?php echo __('Paper Size');?></label>
            <select id="psize" name="psize">
                <option value="">&mdash; <?php echo __('Select Print Paper Size');?> &mdash;</option>
                <?php
                  $psize =$_SESSION['PAPER_SIZE']?$_SESSION['PAPER_SIZE']:$thisstaff->getDefaultPaperSize();
                  foreach(Export::$paper_sizes as $v) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $v,($psize==$v)?'selected="selected"':'', __($v));
                  }
                ?>
            </select>
        </fieldset>
        <div class="clear"></div>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="reset" value="<?php echo __('Reset');?>">
                <input type="button" value="<?php echo __('Cancel');?>" class="close">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?php echo __('Print');?>">
            </span>
         </p>
    </form>
    <div class="clear"></div>
</div>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="claim-confirm">
        <?php echo __('Are you sure you want to <b>claim</b> (self assign) this ticket?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="answered-confirm">
        <?php echo __('Are you sure you want to flag the ticket as <b>answered</b>?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="unanswered-confirm">
        <?php echo __('Are you sure you want to flag the ticket as <b>unanswered</b>?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="overdue-confirm">
        <?php echo __('Are you sure you want to flag the ticket as <font color="red"><b>overdue</b></font>?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="banemail-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>ban</b> %s?'), $ticket->getEmail());?> <br><br>
        <?php echo __('New tickets from the email address will be automatically rejected.');?>
    </p>
    <p class="confirm-action" style="display:none;" id="unbanemail-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>remove</b> %s from ban list?'), $ticket->getEmail()); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="release-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>unassign</b> ticket from <b>%s</b>?'), $ticket->getAssigned()); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="changeuser-confirm">
        <span id="msg_warning" style="display:block;vertical-align:top">
        <?php echo sprintf(Format::htmlchars(__('%s <%s> will longer have access to the ticket')),
            '<b>'.Format::htmlchars($ticket->getName()).'</b>', Format::htmlchars($ticket->getEmail())); ?>
        </span>
        <?php echo sprintf(__('Are you sure you want to <b>change</b> ticket owner to %s?'),
            '<b><span id="newuser">this guy</span></b>'); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo __('Are you sure you want to DELETE this ticket?');?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered, including any associated attachments.');?>
    </p>
    <div><?php echo __('&nbsp;');?></div>
    <form action="tickets.php?id=<?php echo $ticket->getId(); ?>" method="post" id="confirm-form" name="confirm-form">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
        <input type="hidden" name="a" value="process">
        <input type="hidden" name="do" id="action" value="">
        <hr style="margin-top:1em"/>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="button" value="<?php echo __('Cancel');?>" class="close">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?php echo __('OK');?>">
            </span>
         </p>
    </form>
    <div class="clear"></div>
</div>










</div>








<script type="text/javascript">
$(function() {
	
	
	
    $(document).on('click', 'a.change-user', function(e) {
        e.preventDefault();
        var tid = <?php echo $ticket->getOwnerId(); ?>;
        var cid = <?php echo $ticket->getOwnerId(); ?>;
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.userLookup(url, function(user) {
            if(cid!=user.id
                    && $('.dialog#confirm-action #changeuser-confirm').length) {
                $('#newuser').html(user.name +' &lt;'+user.email+'&gt;');
                $('.dialog#confirm-action #action').val('changeuser');
                $('#confirm-form').append('<input type=hidden name=user_id value='+user.id+' />');
                $('#overlay').show();
                $('.dialog#confirm-action .confirm-action').hide();
                $('.dialog#confirm-action p#changeuser-confirm')
                .show()
                .parent('div').show().trigger('click');
            }
        });
    });
    
   <!-- <<INICIO>>   Ajuste realizado por HDANDREA 05-02-18  AC000000064  – sensibilidad botón publicar respuesta y nota inter********** -->


	$("#save_reply" ).mouseover(function() {
		$('#save_reply').attr('style','float: right;');
	}).mouseout(function() {
		$('#save_reply').attr('style','background: #d62705!important;float: right;');
	});
     
	$("#save_note" ).mouseover(function() {
		$('#save_note').attr('style','float: right;');
	}).mouseout(function() {
		$('#save_note').attr('style','background: #d62705!important;float: right;');
	});
     
     $("#save_note").on('click', function (event) {  
		
           event.preventDefault();
           var el = $(this);
           el.prop('disabled', true);
           setTimeout(function(){
			   el.prop('disabled', false); 
			   
			}, 3000);
          
           $("#note").submit();
          
     });
     
     $("#save_reply").on('click', function (event) {  
		
           event.preventDefault();
           var el = $(this);
           el.prop('disabled', true);
           setTimeout(function(){
			   el.prop('disabled', false); 
			   
			}, 3000);
          
           $("#reply").submit();
          
     });
     
     
   <!-- <<FIN>>  ****************************************************** -->
   
   
   
   
   
   

});
</script>
