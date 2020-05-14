<?php
if (!defined('OSTADMININC') || !$thisstaff->isAdmin())
    die('Access Denied');

$qs = array();
$sortOptions=array(
    'name' => 'name',
    'type' => 'ispublic',
    'members'=> 'members_count',
    'email'=> 'email__name',
    'manager'=>'manager__lastname'
    );

$orderWays = array('DESC'=>'DESC', 'ASC'=>'ASC');
$sort = ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';
if ($sort && $sortOptions[$sort]) {
    $order_column = $sortOptions[$sort];
}

$order_column = $order_column ? $order_column : 'name';

if ($_REQUEST['order'] && isset($orderWays[strtoupper($_REQUEST['order'])])) {
    $order = $orderWays[strtoupper($_REQUEST['order'])];
} else {
    $order = 'ASC';
}

if ($order_column && strpos($order_column,',')) {
    $order_column=str_replace(','," $order,",$order_column);
}
$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
$count = Dept::objects()->count();
$pageNav = new Pagenate($count, $page, PAGE_LIMIT);
$qstr = '&amp;'. Http::build_query($qs);
$qstr .= '&amp;order='.($order=='DESC' ? 'ASC' : 'DESC');
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('departments.php', $qs);
$showing = $pageNav->showing().' '._N('department', 'departments', $count);
?>
<form action="departments.php" method="POST" name="depts">
<div class="sticky bar">
    <div class="content">
        <div class="pull-left">
            <h2><?php echo __('Departments');?></h2>
        </div>
        <div class="pull-right flush-right">
            <a href="departments.php?a=add" class="green button action-button"><i class="icon-plus-sign"></i> <?php echo __('Add New Department');?></a>

			<a id="ticket-more" data-dropdown="#action-dropdown-more">
				<div class="action-button change-status gray">
					<div class="button-icon">
						<svg viewBox="0 0 24 24">
							<path  d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
						</svg>
					</div>
					<div class="button-text">
						<?php echo __('More');?>
					</div>
					<div id="button-more-caret">
						<div class="caret">
							<i class="material-icons more">expand_more</i>
						</div>
					</div>        
				</div>
			</a>				

            <div id="action-dropdown-more" class="action-dropdown anchor-right">
                <ul id="actions">
                    <li class="danger"><a class="confirm" data-name="delete" href="departments.php?a=delete">
                        <i class="icon-trash icon-fixed-width"></i>
                        <?php echo __('Delete'); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="action" name="a" value="" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100">
    <thead>
        <tr>
            <th width="4%">&nbsp;</th>
            <th width="28%"><a <?php echo $name_sort; ?> href="departments.php?<?php echo $qstr; ?>&sort=name"><?php echo __('Name');?></a></th>
            <th width="8%"><a  <?php echo $type_sort; ?> href="departments.php?<?php echo $qstr; ?>&sort=type"><?php echo __('Type');?></a></th>
            <th width="8%"><a  <?php echo $users_sort; ?>href="departments.php?<?php echo $qstr; ?>&sort=users"><?php echo __('Agents');?></a></th>
            <th width="30%"><a  <?php echo $email_sort; ?> href="departments.php?<?php echo $qstr; ?>&sort=email"><?php echo __('Email Address');?></a></th>
            <th width="22%"><a  <?php echo $manager_sort; ?> href="departments.php?<?php echo $qstr; ?>&sort=manager"><?php echo __('Manager');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $ids= ($errors && is_array($_POST['ids'])) ? $_POST['ids'] : null;
        if ($count) {
            $depts = Dept::objects()
                ->annotate(array(
                        'members_count' => SqlAggregate::COUNT('members', true),
                ))
                ->order_by(sprintf('%s%s',
                            strcasecmp($order, 'DESC') ? '' : '-',
                            $order_column))
                ->limit($pageNav->getLimit())
                ->offset($pageNav->getStart());
            $defaultId=$cfg->getDefaultDeptId();
            $defaultEmailId = $cfg->getDefaultEmailId();
            $defaultEmailAddress = (string) $cfg->getDefaultEmail();
            foreach ($depts as $dept) {
                $id = $dept->getId();
                $sel=false;
                if($ids && in_array($dept->getId(), $ids))
                    $sel=true;

                if ($dept->email) {
                    $email = (string) $dept->email;
                    $emailId = $dept->email->getId();
                } else {
                    $emailId = $defaultEmailId;
                    $email = $defaultEmailAddress;
                }

                $default= ($defaultId == $dept->getId()) ?' <small>'.__('(Default)').'</small>' : '';
                ?>
            <tr id="<?php echo $id; ?>">
                <td align="center">
                  <input type="checkbox" class="ckb" name="ids[]"
                  value="<?php echo $id; ?>"
                  <?php echo $sel? 'checked="checked"' : ''; ?>
                  <?php echo $default? 'disabled="disabled"' : ''; ?> >
                </td>
                <td><a href="departments.php?id=<?php echo $id; ?>"><?php
                echo Dept::getNameById($id); ?></a>&nbsp;<?php echo $default; ?></td>
                <td><?php echo $dept->isPublic() ? __('Public') :'<b>'.__('Private').'</b>'; ?></td>
                <td>&nbsp;&nbsp;
                    <b>
                    <?php if ($dept->members_count) { ?>
                        <a href="staff.php?did=<?php echo $id; ?>"><?php echo $dept->members_count; ?></a>
                    <?php }else{ ?> 0
                    <?php } ?>
                    </b>
                </td>
                <td><span class="ltr"><a href="emails.php?id=<?php echo $emailId; ?>"><?php
                    echo Format::htmlchars($email); ?></a></span></td>
                <td><a href="staff.php?id=<?php echo $dept->manager_id; ?>"><?php
                    echo $dept->manager_id ? $dept->manager : ''; ?>&nbsp;</a></td>
            </tr>
            <?php
            } //end of foreach.
        } ?>
    <tfoot>
     <tr>
        <td colspan="6">
            <?php
            if ($count) { ?>
            <?php echo __('Select');?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No departments found!');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if ($count):
    echo '<div class="page-links">&nbsp;'.__('Page').':'.$pageNav->getPageLinks().'&nbsp;</div>';
?>
<?php
endif;
?>
</form>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="make_public-confirm">
        <?php echo sprintf(__('Are you sure you want to make %s <b>public</b>?'),
            _N('selected department', 'selected departments', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="make_private-confirm">
        <?php echo sprintf(__('Are you sure you want to make %s <b>private</b> (internal)?'),
            _N('selected department', 'selected departments', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected department', 'selected departments', 2));?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered.'); ?>
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

