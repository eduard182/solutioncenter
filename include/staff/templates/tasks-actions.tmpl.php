<?php
// Tasks' mass actions based on logged in agent

$actions = array();

if ($agent->hasPerm(Task::PERM_CLOSE, false)) {

    if (isset($options['status'])) {
        $status = $options['status'];
    ?>
        <span class="action-button gray" data-dropdown="#action-dropdown-tasks-status">
			<div class="button-icon">
				<svg viewBox="0 0 24 24">
					<path d="M14,10H2V12H14V10M14,6H2V8H14V6M2,16H10V14H2V16M21.5,11.5L23,13L16,20L11.5,15.5L13,14L16,17L21.5,11.5Z" />
				</svg>
			</div>
			<div class="button-text">
				<?php echo __('Change Status'); ?>
			</div>
			<div id="button-more-caret">
				<div class="caret">
					<i class="material-icons more">expand_more</i>
				</div>
			</div>	
        </span>
        <div id="action-dropdown-tasks-status"
            class="action-dropdown anchor-right">
            <ul>
                <?php
                //if (!$status || !strcasecmp($status, 'closed')) { ?>
                <!-- <li>
                    <a class="no-pjax tasks-action"
                        href="#tasks/mass/reopen"><i
                        class="icon-fixed-width icon-undo"></i> <?php
                        //echo __('Reopen');?> </a>
                </li> -->
                <?php
                /*}
                if (!$status || !strcasecmp($status, 'open')) {*/
                ?>
                <!-- <li>
                    <a class="no-pjax tasks-action"
                        href="#tasks/mass/close"><i
                        class="icon-fixed-width icon-ok-circle"></i> <?php
                        //echo __('Close');?> </a>
                </li> -->

                <?php                
                //} 

                foreach (Task::getTaskStatus() as $key => $value) {
                            
                    echo '<li> <a class="no-pjax tasks-action" href="#tasks/mass/status'.$value["id"].'"><i class="icon-fixed-width icon-ok-circle"></i>'.__($value["name"]).'</a></li>';
                
                }



                ?>
            </ul>
        </div>
<?php
    } else {

        /*$actions += array(
                'reopen' => array(
                    'icon' => 'icon-undo',
                    'action' => __('Reopen')
                ));

        $actions += array(
                'close' => array(
                    'icon' => 'icon-ok-circle',
                    'action' => __('Close')
                ));*/

        foreach (Task::getTaskStatus() as $key => $value) {
                            
            $actions += array(
                'status'.$value["id"] => array(
                                        'icon' => 'icon-ok-circle',
                                        'action' => __($value["name"])
                ));        
        }

        
    }
}

if ($agent->hasPerm(Task::PERM_ASSIGN, false)) {
    $actions += array(
            'assign' => array(
                'icon' => 'icon-user',
                'action' => __('Assign')
            ));
}

if ($agent->hasPerm(Task::PERM_TRANSFER, false)) {
    $actions += array(
            'transfer' => array(
                'icon' => 'icon-share',
                'redirect' => 'tickets.php',
                'action' => __('Transfer')
            ));
}

if ($agent->hasPerm(Task::PERM_DELETE, false)) {
    $actions += array(
            'delete' => array(
                'class' => 'danger',
                'icon' => 'icon-trash',
                'action' => __('Delete')
            ));
}
if ($actions) {
    $more = $options['morelabel'] ?: __('More');
    ?>
    <span class="action-button gray" data-dropdown="#action-dropdown-moreoptions">
		<div class="button-icon">
			<svg viewBox="0 0 24 24">
				<path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
			</svg>
		</div>
		<div class="button-text">
			<?php echo $more; ?>
		</div>
		<div id="button-more-caret">
			<div class="caret">
				<i class="material-icons more">expand_more</i>
			</div>
		</div>		
    </span>
    <div id="action-dropdown-moreoptions" class="action-dropdown anchor-right">
        <ul>
    <?php foreach ($actions as $a => $action) { ?>
            <li <?php
                if ($action['class'])
                    echo sprintf("class='%s'", $action['class']); ?> >
                <a class="no-pjax tasks-action"
                    <?php
                    if ($action['dialog'])
                        echo sprintf("data-dialog-config='%s'", $action['dialog']);
                    if ($action['redirect'])
                        echo sprintf("data-redirect='%s'", $action['redirect']);
                    ?>
                    href="<?php
                    echo sprintf('#tasks/mass/%s', $a); ?>"
                    ><i class="icon-fixed-width <?php
                    echo $action['icon'] ?: 'icon-tag'; ?>"></i> <?php
                    echo $action['action']; ?></a>
            </li>
        <?php
        } ?>
        </ul>
    </div>
 <?php
 } ?>
<script type="text/javascript">
$(function() {
    $(document).off('.tasks-actions');
    $(document).on('click.tasks-actions', 'a.tasks-action', function(e) {
        e.preventDefault();
        var count = checkbox_checker($('form#tasks'), 1);
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
});
</script>
