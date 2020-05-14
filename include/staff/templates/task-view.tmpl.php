<?php
if (!defined('OSTSCPINC')
    || !$thisstaff || !$task
    || !($role = $thisstaff->getRole($task->getDeptId())))
    die('Invalid path');

global $cfg;

$iscloseable = $task->isCloseable();
$canClose = ($role->hasPerm(TaskModel::PERM_CLOSE) && $iscloseable === true);
$actions = array();
$actions += array(
        'print' => array(
            'href' => sprintf('tasks.php?id=%d&a=print', $task->getId()),
            'class' => 'no-pjax',
            'icon' => 'icon-print',
            'label' => __('Print')
        ));

if ($role->hasPerm(Task::PERM_EDIT)) {
    $actions += array(
            'edit' => array(
                'href' => sprintf('#tasks/%d/edit', $task->getId()),
                'icon' => 'icon-edit',
                'dialog' => '{"size":"large"}',
                'label' => __('Edit')
            ));
}

if ($role->hasPerm(Task::PERM_ASSIGN)) {
    $actions += array(
            'assign' => array(
                'href' => sprintf('#tasks/%d/assign', $task->getId()),
                'icon' => 'icon-user',
                'label' => $task->isAssigned() ? __('Reassign') : __('Assign'),
                'redirect' => 'tasks.php'
            ));
}

if ($role->hasPerm(Task::PERM_TRANSFER)) {
    $actions += array(
            'transfer' => array(
                'href' => sprintf('#tasks/%d/transfer', $task->getId()),
                'icon' => 'icon-share',
                'label' => __('Transfer'),
                'redirect' => 'tasks.php'
            ));
}


if ($role->hasPerm(Task::PERM_DELETE)) {
    $actions += array(
            'delete' => array(
                'href' => sprintf('#tasks/%d/delete', $task->getId()),
                'icon' => 'icon-trash',
                'class' => 'red button task-action',
                'label' => __('Delete'),
                'redirect' => 'tasks.php'
            ));
}

$info=($_POST && $errors)?Format::input($_POST):array();

$id = $task->getId();
$dept = $task->getDept();
$thread = $task->getThread();
/// AGREGADO POR FRANCISCO COLMENAREZ --------------------------
$ticket_id=$task->getTicketId();
list($ticket_OC,$ticket_name)=$task->getOcTicket($ticket_id);
/// ------------------------------------------------------------
if ($task->isOverdue())
    $warn.='&nbsp;&nbsp;<span class="Icon overdueTicket">'.__('Marked overdue!').'</span>';

?>

<div class="has_bottom_border" class="sticky bar stop">
    <div class="sticky bar">
       <div class="content">
        <div class="pull-left flush-left">
            <?php
            if ($ticket) { ?>
                <strong>
                <a id="all-ticket-tasks" href="#tasks"> Tareas (<?php echo $ticket->getNumTasks(); ?>)</a>
                &nbsp;/&nbsp;
                <?php echo $task->getTitle(); ?>
                &nbsp;&mdash;&nbsp;
                <a
                    id="reload-task" class="preview"
                    <?php
                    echo ' class="preview" ';
                    echo sprintf('data-preview="#tasks/%d/preview" ', $task->getId());
                    echo sprintf('href="#tickets/%s/tasks/%d/view" ', $ticket->getId(), $task->getId());
                    ?>
                ><?php
                echo sprintf('#%s', $task->getNumber()); 
                
                ?></a>
                </strong>
            <?php
            } else { ?>
               <h2>
                <a
                id="reload-task"
                href="tasks.php?id=<?php echo $task->getId(); ?>"
                href="tasks.php?id=<?php echo $task->getId(); ?>"
                ><?php
                echo sprintf(__('Tarea #%s'), $task->getNumber()); ?> 
					<svg viewBox="0 0 24 24">
						<path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
					</svg>				
				</a>
                   <?php if ($task) { ?> – <small><span class="ltr"><?php echo $task->getTitle(); ?></span></small>
                <?php } ?>
            </h2>
            <h2><a id="reload-ticket" href="tickets.php?id=<?php echo $ticket_id; ?>">
                OC #<?php echo $ticket_OC?>
                <svg viewBox="0 0 24 24">
                      <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
                </svg>
                </a>
                 – <small><span class="ltr"><?php echo $ticket_name; ?></span></small>
            </h2>
            <?php
            } ?>
        </div>
        <div class="flush-right">
            <?php
            if ($ticket) { ?>
            <a  id="task-view"
                target="_blank"
                class=" blue action-button"
                href="tasks.php?id=<?php
                 echo $task->getId(); ?>"><i class="icon-share"></i> <?php
                            echo __('Ver Tarea'); ?></a>
            <span
                class=" blue action-button"
                data-dropdown="#action-dropdown-task-options">
                <i class="icon-caret-down pull-right"></i>
                <a class="task-action"
                    href="#task-options"><i
                    class="icon-reorder"></i> <?php
                    echo __('Actions'); ?></a>
            </span>
            <div id="action-dropdown-task-options"
                class="action-dropdown anchor-right">
                <ul>
            <?php foreach ($actions as $a => $action) { ?>
                    <li>
                        <a class="no-pjax <?php
                            echo $action['class'] ?: 'task-action'; ?>"
                            <?php
                            if ($action['dialog'])
                                echo sprintf("data-dialog-config='%s'", $action['dialog']);
                            if ($action['redirect'])
                                echo sprintf("data-redirect='%s'", $action['redirect']);
                            ?>
                            href="<?php echo $action['href']; ?>"
                            <?php
                            if (isset($action['href']) &&
                                    $action['href'][0] != '#') {
                                echo 'target="blank"';
                            } ?>
                            ><i class="<?php
                            echo $action['icon'] ?: 'icon-tag'; ?>"></i> <?php
                            echo  $action['label']; ?></a>
                    </li>
                <?php
                } ?>
                </ul>
            </div>
            <?php
           } else {
                ///// MODIFICADO Y POR FCOLMENAREZ --------------------------------------------------

                /*foreach ($actions as $action) {?>
                <a class="blue action-button <?php
                    echo $action['class'] ?: 'task-action'; ?>"
                    <?php
                    if ($action['dialog'])
                        echo sprintf("data-dialog-config='%s'", $action['dialog']);
                    if ($action['redirect'])
                        echo sprintf("data-redirect='%s'", $action['redirect']);
                    ?>
                    href="<?php echo $action['href']; ?>"><i
                    class="<?php
                    echo $action['icon'] ?: 'icon-tag'; ?>"></i> <?php
                    echo $action['label'];
                ?></a>
           <?php
                }*/
                
                ?>
                <span
                class=" blue action-button"
                data-dropdown="#action-dropdown-task-options">
                <i class="icon-caret-down pull-right"></i>
                <a class="task-action"
                    href="#task-options"><i
                    class="icon-reorder"></i> <?php
                    echo __('Actions'); ?></a>
            </span>
            <div id="action-dropdown-task-options"
                class="action-dropdown anchor-right">
                <ul>
            <?php foreach ($actions as $a => $action) { ?>
                    <li>
                        <a class="no-pjax <?php
                            echo $action['class'] ?: 'task-action'; ?>"
                            <?php
                            if ($action['dialog'])
                                echo sprintf("data-dialog-config='%s'", $action['dialog']);
                            if ($action['redirect'])
                                echo sprintf("data-redirect='%s'", $action['redirect']);
                            ?>
                            href="<?php echo $action['href']; ?>"
                            <?php
                            if (isset($action['href']) &&
                                    $action['href'][0] != '#') {
                                echo 'target="blank"';
                            } ?>
                            ><i class="<?php
                            echo $action['icon'] ?: 'icon-tag'; ?>"></i> <?php
                            echo  $action['label']; ?></a>
                    </li>
                <?php
                } ?>
                </ul>
            </div>

            <?php
                
            ///// FIN MODIFICADO Y POR FCOLMENAREZ --------------------------------------------------
           } ?>


        </div>
    </div>
   </div>
</div>

<?php
if (!$ticket) { ?>
    <div class="responsive-div ticket_info ticket-view">
    <table class="ticket_info" cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
            <td width="50%">
                <table border="0" cellspacing="" cellpadding="4" width="100%">
                    <tr>
                        <th width="100"><?php echo __('Status');?></th>
                        <td><?php echo $task->getTaskStatusName(); ?></td>
                    </tr>

                    <tr>
                        <th><?php echo __('Created');?></th>
                        <td><?php echo Format::datetime($task->getCreateDate()); ?></td>
                    </tr>
                    <?php
                    if($task->isOpen()){ ?>
                    <tr>
                        <th><?php echo __('Due Date');?></th>
                        <td><?php echo $task->duedate ?
                        Format::datetime($task->duedate) : '<span
                        class="faded">&mdash; '.__('None').' &mdash;</span>'; ?></td>
                    </tr>
                    <?php
                    }else { ?>
                    <tr>
                        <th><?php echo __('Completed');?></th>
                        <td><?php echo Format::datetime($task->getCloseDate()); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
            </td>
            <td width="50%" style="vertical-align:top">
                <table cellspacing="0" cellpadding="4" width="100%" border="0">

                    <tr>
                        <th><?php echo __('Department');?></th>
                        <td><?php echo Format::htmlchars($task->dept->getName()); ?></td>
                    </tr>
                    <?php
                    if ($task->isOpen()) { ?>
                    <tr>
                        <th width="100"><?php echo __('Assigned To');?></th>
                        <td>
                            <?php
                            if ($assigned=$task->getAssigned())
                                echo Format::htmlchars($assigned);
                            else
                                echo '<span class="ticket-unassigned">&mdash; '.__('Unassigned').' &mdash;</span>';
                            ?>
                        </td>
                    </tr>
                    <?php
                    } else { ?>
                    <tr>
                        <th width="100"><?php echo __('Closed By');?></th>
                        <td>
                            <?php
                            if (($staff = $task->getStaff()))
                                echo Format::htmlchars($staff->getName());
                            else
                                echo '<span class="faded">&mdash; '.__('Unknown').' &mdash;</span>';
                            ?>
                        </td>
                    </tr>
                    <?php
                    } ?>
                    <tr>
                        <th><?php echo __('Collaborators');?></th>
                        <td>
                            <?php
                            $collaborators = __('Add Participants');
                            if ($task->getThread()->getNumCollaborators())
                                $collaborators = sprintf(__('Participants (%d)'),
                                        $task->getThread()->getNumCollaborators());

                            echo sprintf('<span><a class="collaborators preview"
                                    href="#thread/%d/collaborators"><span
                                    id="t%d-collaborators">%s</span></a></span>',
                                    $task->getThreadId(),
                                    $task->getThreadId(),
                                    $collaborators);
                           ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
    <br>
    <br>
    <div class="responsive-div ticket_info ticket-view">
    <table class="ticket_info" cellspacing="0" cellpadding="0" width="100%" border="0">
    <?php
    $idx = 0;
    foreach (DynamicFormEntry::forObject($task->getId(),
                ObjectModel::OBJECT_TYPE_TASK) as $form) {
        $answers = $form->getAnswers()->exclude(Q::any(array(
            'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED
        )));
        if (!$answers || count($answers) == 0)
            continue;

        ?>
            <tr>
            <td colspan="2">
                <table cellspacing="0" cellpadding="4" width="100%" border="0">
                <?php foreach($answers as $a) {
                    if (!($v = $a->display())) continue; ?>
                    <tr>
                        <th width="150"><?php
                            echo $a->getField()->get('label');
                        ?></th>
                        <td><?php
                            echo $v;
                        ?></td>
                    </tr>
                    <?php
                } ?>
                </table>
            </td>
            </tr>
        <?php
        $idx++;
    } ?>
    </table>
    </div>
<?php
} ?>
<div class="clear"></div>
<div id="task_thread_container">
    <div id="task_thread_content" class="tab_content">
     <?php
     $task->getThread()->render(array('M', 'R', 'N'),
             array(
                 'mode' => Thread::MODE_STAFF,
                 'container' => 'taskThread'
                 )
             );
     ?>
   </div>
</div>
<div class="clear"></div>
<?php if($errors['err']) { ?>
    <div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text"><?php echo $errors['err']; ?></div></div>
<?php }elseif($_REQUEST["msg"] || $msg) { ?>
    <div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text"><?php echo $_REQUEST["msg"]?$_REQUEST["msg"]:$msg; ?></div></div>
<?php }elseif($warn) { ?>
    <div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text"><?php echo $warn; ?></div></div>
<?php }

if ($ticket)
    $action = sprintf('#tickets/%d/tasks/%d',
            $ticket->getId(), $task->getId());
else
    $action = 'tasks.php?id='.$task->getId();

?>
<div id="task_response_options" class="<?php echo $ticket ? 'ticket_task_actions' : ''; ?> sticky bar stop actions">
    <ul class="tabs">
        <?php
        if ($role->hasPerm(TaskModel::PERM_REPLY)) { ?>
        <li class="active"><a href="#task_reply"><?php echo __('Post Update');?></a></li>
        <li><a href="#task_note"><?php echo __('Post Internal Note');?></a></li>
        <?php
        }?>
    </ul>
    <?php
    if ($role->hasPerm(TaskModel::PERM_REPLY)) { ?>
    <form id="task_reply" class="tab_content spellcheck"
        action="<?php echo $action; ?>"
        name="task_reply" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $task->getId(); ?>">
        <input type="hidden" name="a" value="postreply">
        <input type="hidden" name="lockCode" value="<?php echo ($mylock) ? $mylock->getCode() : ''; ?>">
        <span class="error"></span>
        <table style="width:100%" border="0" cellspacing="0" cellpadding="3">
            <tbody id="collab_sec" style="display:table-row-group">
             <tr>
                <td>
                    <input type='checkbox' value='1' name="emailcollab" id="emailcollab"
                        <?php echo ((!$info['emailcollab'] && !$errors) || isset($info['emailcollab']))?'checked="checked"':''; ?>
                        style="display:<?php echo $thread->getNumCollaborators() ? 'inline-block': 'none'; ?>;"
                        >
                    <?php
                    $recipients = __('Add Participants');
                    if ($thread->getNumCollaborators())
                        $recipients = sprintf(__('Recipients (%d of %d)'),
                                $thread->getNumActiveCollaborators(),
                                $thread->getNumCollaborators());

                    echo sprintf('<span><a class="collaborators preview"
                            href="#thread/%d/collaborators"><span id="t%d-recipients">%s</span></a></span>',
                            $thread->getId(),
                            $thread->getId(),
                            $recipients);
                   ?>
                </td>
             </tr>
            </tbody>
            <tbody id="update_sec">
            <tr>
                <td>
                    <div class="error"><?php echo $errors['response']; ?></div>
                    <input type="hidden" name="draft_id" value=""/>
                    <textarea name="response" id="task-response" cols="50"
                        data-signature-field="signature" data-dept-id="<?php echo $dept->getId(); ?>"
                        data-signature="<?php
                            echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                        placeholder="<?php echo __( 'Start writing your update here.'); ?>"
                        rows="9" wrap="soft"
                        class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft draft-delete" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('task.response', $task->getId(), $info['task.response']);
    echo $attrs; ?>><?php echo $draft ?: $info['task.response'];
                    ?></textarea>
                <div id="task_response_form_attachments" class="attachments">
                <?php
                    if ($reply_attachments_form)
                        print $reply_attachments_form->getField('attachments')->render();
                ?>
                </div>
               </td>
            </tr>
            <tr>
                <td>
                    <div><?php echo __('Status');?>
                        <span class="faded"> - </span>
                        <select id="task_status" name="task:status">
                            <!-- <option value="open" <?php //echo $task->isOpen() ? 'selected="selected"': ''; ?>> <?php //echo _('Open'); ?></option>
                            <?php 
                            //if ($task->isClosed() || $canClose) { ?>
                            <option value="closed" <?php //echo $task->isClosed() ? 'selected="selected"': ''; ?>> <?php //echo _('Closed'); ?></option>
                            <?php //} ?> -->
                            <?php 
                                
                                foreach ($task->getTaskStatus() as $key => $value) {
                                    $selected="";
                                    if($task->getStatusId()==$value["id"]) $selected='selected="selected"';

                                    echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"].'</option>';
                                }

                            ?>

                        </select>
                        &nbsp;<span class='error'><?php echo $errors['task:status']; ?></span>
                    </div>
                </td>
            </tr>
        </table>
       <p  style="text-align:center;">
           <!-- <input class="save pending" type="submit" value="<?php echo __('Post Update');?>"> -->
           
           <!-- <<INICIO>>  Ajuste realizado por HDANDREA 05-02-18 AC000000064  – sensibilidad botón publicar respuesta y nota inter ********* -->
           
           <input id="save_task_reply" class="save pending" type="button" value="<?php echo __('Post Update');?>" style="background: #d62705!important;float: right;">
           
           <!-- <<FIN>> ****************************************************************** -->
           
           <input type="reset" value="<?php echo __('Reset');?>">
       </p>
    </form>
    <?php
    } ?>
    <form id="task_note"
        action="<?php echo $action; ?>"
        class="tab_content spellcheck <?php
            echo $role->hasPerm(TaskModel::PERM_REPLY) ? 'hidden' : ''; ?>"
        name="task_note"
        method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $task->getId(); ?>">
        <input type="hidden" name="a" value="postnote">
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
            <tr>
                <td>
                    <div><span class='error'><?php echo $errors['note']; ?></span></div>
                    <textarea name="note" id="task-note" cols="80"
                        placeholder="<?php echo __('Internal Note details'); ?>"
                        rows="9" wrap="soft" data-draft-namespace="task.note"
                        data-draft-object-id="<?php echo $task->getId(); ?>"
                        class="richtext ifhtml draft draft-delete"><?php
                        echo $info['note'];
                        ?></textarea>
                    <div class="attachments">
                    <?php
                        if ($note_attachments_form)
                            print $note_attachments_form->getField('attachments')->render();
                    ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><?php echo __('Status');?>
                        <span class="faded"> - </span>
                        <select id="task_status"  name="task:status">
                            <!-- <option value="open" <?php //echo $task->isOpen() ? 'selected="selected"': ''; ?>> <?php //echo _('Open'); ?></option>
                            <?php //if ($task->isClosed() || $canClose) {?>
                            <option value="closed" <?php //echo $task->isClosed() ?'selected="selected"': ''; ?>> <?php //echo _('Closed'); ?></option>
                            <?php //} ?> -->

                            <?php 
                                
                                foreach ($task->getTaskStatus() as $key => $value) {
                                    $selected="";
                                    if($task->getStatusId()==$value["id"]) $selected='selected="selected"';

                                    echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["name"].'</option>';
                                }

                            ?>

                        </select>
                        &nbsp;<span class='error'><?php echo
                        $errors['task:status']; ?></span>
                    </div>
                </td>
            </tr>
        </table>
       <p  style="text-align:center;">
           <!-- <input class="save pending" type="submit" value="<?php echo __('Post Note');?>"> -->
           <!-- <<INICIO>>  Ajuste realizado por HDANDREA 05-02-18 AC000000064  – sensibilidad botón publicar respuesta y nota inter ********* -->
           
           <input id="save_task_note" class="save pending" type="button" value="<?php echo __('Post Note');?>" style="background: #d62705!important;float: right;">
           
           <!-- <<FIN>> ****************************************************************** -->
           <input type="reset" value="<?php echo __('Reset');?>">
       </p>
    </form>
 </div>
<?php
echo $reply_attachments_form->getMedia();
?>

<script type="text/javascript">
$(function() {
    $(document).off('.tasks-content');
    $(document).on('click.tasks-content', '#all-ticket-tasks', function(e) {
        e.preventDefault();
        $('div#task_content').hide().empty();
        $('div#tasks_content').show();
        return false;
     });

    $(document).off('.task-action');
    $(document).on('click.task-action', 'a.task-action', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'
        +$(this).attr('href').substr(1)
        +'?_uid='+new Date().getTime();
        var $options = $(this).data('dialogConfig');
        var $redirect = $(this).data('redirect');
        $.dialog(url, [201], function (xhr) {
            if (!!$redirect)
                window.location.href = $redirect;
            else
                $.pjax.reload('#pjax-container');
        }, $options);

        return false;
    });

    $(document).off('.tf');
    $(document).on('submit.tf', '.ticket_task_actions form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $container = $('div#task_content');
        $.ajax({
            type:  $form.attr('method'),
            url: 'ajax.php/'+$form.attr('action').substr(1),
            data: $form.serialize(),
            cache: false,
            success: function(resp, status, xhr) {
                $container.html(resp);
                
                
                var jsonData={  "id":<?php echo $task->getId();?>,
                                "a": "poststatus"};
                $.ajax({
                    url: "tasks.php",
                    method: "POST",
                    data: jsonData,
                    dataType: "json",
                    success: function(id) {
                        document.task_reply.task_status.value=id;
                        document.task_note.task_status.value=id;
                    }
                });

                $('#msg_notice, #msg_error',$container)
                .delay(5000)
                .slideUp();
            }
        })
        .done(function() { })
        .fail(function() { });
     });
     
     
     
   <!-- <<INICIO>>   Ajuste realizado por HDANDREA 05-02-18  AC000000064  – sensibilidad botón publicar respuesta y nota inter********** -->
     
     
	$("#save_task_reply" ).mouseover(function() {
		$('#save_task_reply').attr('style','float: right;');
	}).mouseout(function() {
		$('#save_task_reply').attr('style','background: #d62705!important;float: right;');
	});
     
	$("#save_task_note" ).mouseover(function() {
		$('#save_task_note').attr('style','float: right;');
	}).mouseout(function() {
		$('#save_task_note').attr('style','background: #d62705!important;float: right;');
	});
	
	
     $("#save_task_reply").on('click', function (event) {  
		   
           event.preventDefault();
           var el = $(this);
           el.prop('disabled', true);
           setTimeout(function(){
			   el.prop('disabled', false); 
			   
			}, 3000);
           
           $("#task_reply").submit();
     });
	
     
     $("#save_task_note").on('click', function (event) {  
		   
           event.preventDefault();
           var el = $(this);
           el.prop('disabled', true);
           setTimeout(function(){
			   el.prop('disabled', false); 
			   
			}, 3000);
           
           $("#task_note").submit();
     });
     
     
     
     
     
   <!-- <<FIN>>  ****************************************************** -->
     
     
});
</script>
