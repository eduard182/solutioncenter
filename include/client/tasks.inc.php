<?php


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
            <h2>
                <a
                id="reload-task"
                href="tasks.php?id=<?php echo $task->getId(); ?>"
                href="tasks.php?id=<?php echo $task->getId(); ?>"
                ><?php
                echo sprintf(__('Tarea #%s'), $task->getNumber()); ?><i class="refresh icon-refresh"></i>				
				</a>
                   <?php if ($task) { ?> – <small><span class="ltr"><?php echo $task->getTitle(); ?></span></small>
                <?php } ?>
            </h2>
            <h2><a id="reload-ticket" href="tickets.php?id=<?php echo $ticket_id; ?>">
                OC #<?php echo $ticket_OC?><i class="refresh icon-refresh"></i>
                </a>
                 – <small><span class="ltr"><?php echo $ticket_name; ?></span></small>
            </h2>
            
            
        </div>
        
    </div>
   </div>
</div>

<?php
if (!$ticket) { ?>
    <table class="ticket_info" cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
            <td width="50%">
                <table border="0" cellspacing="" cellpadding="4" width="100%">
                    <tr>
                        <th width="100"><?php echo __('Status');?></th>
                        <td><?php echo $task->getStatus(); ?></td>
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
                            
                            /*echo sprintf('<span><a class="collaborators preview"
                                    href="#thread/%d/collaborators"><span
                                    id="t%d-collaborators">%s</span></a></span>',
                                    $task->getThreadId(),
                                    $task->getThreadId(),
                                    $collaborators);*/
                           ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>
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
<?php
} ?>
<div class="clear"></div>
<div id="task_thread_container">
    <div id="task_thread_content" class="tab_content">
     <?php
     $task->getThread()->render(array('M', 'R'),
             array(
                 'mode' => Thread::MODE_CLIENT,
                 'container' => 'taskThread'
                 )
             );
     ?>
   </div>
</div>
<div class="clear"></div>


