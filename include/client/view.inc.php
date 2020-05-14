<?php
if(!defined('OSTCLIENTINC') || !$thisclient || !$ticket || !$ticket->checkUserAccess($thisclient)) die('Acceso Denegado!');

$info=($_POST && $errors)?Format::htmlchars($_POST):array();

$dept = $ticket->getDept();

if ($ticket->isClosed() && !$ticket->isReopenable())
    $warn = __('This ticket is marked as closed and cannot be reopened.');

//Making sure we don't leak out internal dept names
if(!$dept || !$dept->isPublic())
    $dept = $cfg->getDefaultDept();

if ($thisclient && $thisclient->isGuest()
    && $cfg->isClientRegistrationEnabled()) { ?>

<div id="msg_info">
    <i class="icon-compass icon-2x pull-left"></i>
    <strong><?php echo __('Looking for your other tickets?'); ?></strong><br />
    <a href="<?php echo ROOT_PATH; ?>login.php?e=<?php
        echo urlencode($thisclient->getEmail());
    ?>" style="text-decoration:underline"><?php echo __('Sign In'); ?></a>
    <?php echo sprintf(__('or %s register for an account %s for the best experience on our help desk.'),
        '<a href="account.php?do=create" style="text-decoration:underline">','</a>'); ?>
    </div>

<?php } ?>

<table width="800" cellpadding="1" cellspacing="0" border="0" id="ticketInfo">
    <tr>
        <td colspan="2" width="100%">
            <h1>
                <a href="tickets.php?id=<?php echo $ticket->getId(); ?>" title="<?php echo __('Reload'); ?>"><i class="refresh icon-refresh"></i></a>
                <b><?php echo Format::htmlchars($ticket->getSubject()); ?></b>
                <small>#<?php echo $ticket->getNumber(); ?></small>
				<div class="pull-right">
					<a class="action-button" href="tickets.php?a=print&id=<?php
						echo $ticket->getId(); ?>">
                        <i class="icon-print"></i>
				<!-- <svg viewBox="0 0 28 28">
					<path d="M18,3H6V7H18M19,12A1,1 0 0,1 18,11A1,1 0 0,1 19,10A1,1 0 0,1 20,11A1,1 0 0,1 19,12M16,19H8V14H16M19,8H5A3,3 0 0,0 2,11V17H6V21H18V17H22V11A3,3 0 0,0 19,8Z" />
				</svg> -->		
		<?php echo __('Print'); ?></a>
<?php if ($ticket->hasClientEditableFields()
        // Only ticket owners can edit the ticket details (and other forms)
        && $thisclient->getId() == $ticket->getUserId()) { ?>
                <a class="action-button" href="tickets.php?a=edit&id=<?php
                     echo $ticket->getId(); ?>"><i class="icon-edit"></i> <?php echo __('Edit'); ?></a>
<?php } ?>
</div>
            </h1>
        </td>
    </tr>
    <tr>
        <td width="50%">
            <table class="infoTable" cellspacing="1" cellpadding="3" width="100%" border="0">
                <thead>
                    <tr><td class="headline" colspan="2">
                        <?php echo __('Informaci&oacute;n Basica'); ?>
                    </td></tr>
                </thead>
                <tr>
                    <th width="100"><?php echo __('Ticket Status');?></th>
                    <td><?php echo ($S = $ticket->getStatus()) ? $S->getLocalName() : ''; ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Department');?></th>
                    <td><?php echo Format::htmlchars($dept instanceof Dept ? $dept->getName() : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Create Date');?></th>
                    <td><?php echo Format::datetime($ticket->getCreateDate()); ?></td>
                </tr>
           </table>
       </td>
       <td width="50%">
           <table class="infoTable" cellspacing="1" cellpadding="3" width="100%" border="0">
                <thead>
                    <tr><td class="headline" colspan="2">
                        <?php echo __('User Information'); ?>
                    </td></tr>
                </thead>
               <tr>
                   <th width="100"><?php echo __('Name');?></th>
                   <td><?php echo mb_convert_case(Format::htmlchars($ticket->getName()), MB_CASE_TITLE); ?></td>
               </tr>
               <tr>
                   <th width="100"><?php echo __('Email');?></th>
                   <td><?php echo Format::htmlchars($ticket->getEmail()); ?></td>
               </tr>
               <tr>
                   <th><?php echo __('Phone');?></th>
                   <td><?php echo $ticket->getPhoneNumber(); ?></td>
               </tr>
            </table>
       </td>
    </tr>
    <tr>
        <td colspan="2">
<!-- Custom Data -->
<?php
foreach (DynamicFormEntry::forTicket($ticket->getId()) as $form) {
    // Skip core fields shown earlier in the ticket view
    $answers = $form->getAnswers()->exclude(Q::any(array(
        'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
        'field__name__in' => array('subject', 'priority'),
        Q::not(array('field__flags__hasbit' => DynamicFormField::FLAG_CLIENT_VIEW)),
    )));
    if (count($answers) == 0)
        continue;
    ?>
        <table class="custom-data" cellspacing="0" cellpadding="4" width="100%" border="0">
        <tr><td colspan="2" class="headline flush-left"><?php echo $form->getTitle(); ?></th></tr>
        <?php foreach($answers as $a) {
            if (!($v = $a->display())) continue; ?>
            <tr>
                <th><?php
    echo $a->getField()->get('label');
                ?></th>
                <td><?php
    echo $v;
                ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php
    $idx++;
} ?>
    </td>
</tr>
</table>
<br>


<?php
$tcount = $ticket->getThreadEntries($types)->count();
$res_taskcli=$ticket->get_taskCli($ticket->getId());
?>

<ul class="clean tabs">
    <li id="ticket_tab" class="active"><a href="#"><?php echo sprintf(__('Ticket Thread (%d)'), $tcount); ?></a></li>
    <li id="ticket_task_tab" class="inactive"><a href="#"><?php echo __('Tareas'); if ($ticket->getNumTasks()) echo sprintf('&nbsp;(%d)', $ticket->getNumTasks());?></a></li>
</ul>

<div class="tab_content" id="cont_ticket_tab">
    <?php
        $ticket->getThread()->render(array('M', 'R'), array(
            'mode' => Thread::MODE_CLIENT,
            'html-id' => 'ticketThread')
        );
    ?>
</div>

<div class="tab_content hidden" id="cont_ticket_task_tab">
    <?php 
        $tabla_task='<table id="task_cli" class="display  responsive nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th>N&uacute;mero</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>T&iacute;tulo</th>
                            <th>Departamento</th>
                            <th>Procurador</th>
                        </tr>
                        </thead>
                        <tbody>';

        while($arr = db_fetch_array($res_taskcli)) {
        
            $tabla_task.='<tr><td><a href=tasks.php?id='.$arr[id].' >'.$arr["number"].'</a></td>
                        <td>'.$arr["created"].'</td>
                        <td>'.$arr["estado"].'</td>
                        <td>'.$arr["title"].'</td>
                        <td>'.$arr["name"].'</td>
                        <td>'.$arr["procurador"].'</td>
                        </tr>';
        }
        
        $tabla_task.='</tbody></table>';
        echo $tabla_task;
        

    ?>
</div>



<div class="clear" style="padding-bottom:10px;"></div>
<?php if($errors['err']) { ?>
<div id="msg_error">
    <div id="alert-icon">
        <svg viewBox="0 0 24 24">
            <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path>
        </svg>
    </div>
    <div id="alert-text">
		<?php echo $errors['err']; ?>		
    </div>		
</div>
<?php }elseif($msg) { ?>
<div id="msg_notice">
    <div id="alert-icon">
        <svg viewBox="0 0 24 24">
            <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path>
        </svg>
    </div>
    <div id="alert-text">
        <?php echo $msg; ?>
    </div>		
</div>
<?php }elseif($warn) { ?>
<div id="msg_warning">
    <div id="alert-icon">
		<svg viewBox="0 0 24 24">
            <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" />
        </svg>
    </div>
    <div id="alert-text">
        <?php echo $warn; ?>
    </div>		
</div>
<?php }

if (!$ticket->isClosed() || $ticket->isReopenable()) { ?>
<form id="reply" action="tickets.php?id=<?php echo $ticket->getId(); ?>#reply" name="reply" method="post" enctype="multipart/form-data">
    <?php csrf_token(); ?>
    <h3><?php echo __('Post a Reply');?></h3>
    <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
    <input type="hidden" name="a" value="reply">
    <div>
        <p><em><?php
         echo __('To best assist you, we request that you be specific and detailed'); ?></em>
			<span id="msg"><em><?php echo $msg; ?> </em></span>
			<font class="error-asterisk">*</font>
			<br><font class="error"><?php echo $errors['message']; ?></font>
			<br/>
        <textarea name="message" id="message" cols="50" rows="9" wrap="soft"
            class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                ?> draft" <?php
list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.client', $ticket->getId(), $info['message']);
echo $attrs; ?>><?php echo $draft ?: $info['message'];
            ?></textarea>
    <?php
    if ($messageField->isAttachmentsEnabled()) {
        print $attachments->render(array('client'=>true));
    } ?>
    </div>
<?php if ($ticket->isClosed()) { ?>
    <div class="warning-banner">
        <?php echo __('Ticket will be reopened on message post'); ?>
    </div>
<?php } ?>
    <p style="text-align:center">
        <input type="submit" value="<?php echo __('Post Reply');?>">
        <input type="reset" value="<?php echo __('Reset');?>">
        <input type="button" value="<?php echo __('Cancel');?>" onClick="history.go(-1)">
    </p>
<div class="clear"></div>	
</form>

<?php
} ?>
<script type="text/javascript">
<?php
// Hover support for all inline images
$urls = array();
foreach (AttachmentFile::objects()->filter(array(
    'attachments__thread_entry__thread__id' => $ticket->getThreadId(),
    'attachments__inline' => true,
)) as $file) {
    $urls[strtolower($file->getKey())] = array(
        'download_url' => $file->getDownloadUrl(),
        'filename' => $file->name,
    );
} ?>
showImagesInline(<?php echo JsonDataEncoder::encode($urls); ?>);
</script>

<script>
    
$("#ticket_tab").click(function(){
    document.getElementById('ticket_tab').className = 'active';
    document.getElementById('ticket_task_tab').className = 'inactive';
    document.getElementById('cont_ticket_tab').className = 'tab_content';
    document.getElementById('cont_ticket_task_tab').className = 'tab_content hidden';
});

$("#ticket_task_tab").click(function(){
    
    document.getElementById('ticket_tab').className = 'inactive';
    document.getElementById('ticket_task_tab').className = 'active';
    document.getElementById('cont_ticket_task_tab').className = 'tab_content';
    document.getElementById('cont_ticket_tab').className = 'tab_content hidden';
});

</script>