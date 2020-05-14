<?php
if (!defined('OSTADMININC') || !$thisstaff->isAdmin()) die('Access Denied');


$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
$count = Topic::objects()->count();
$pageNav = new Pagenate($count, $page, PAGE_LIMIT);
$pageNav->setURL('helptopics.php', $_qstr);
$showing = $pageNav->showing().' '._N('help topic', 'help topics', $count);

$order_by = 'sort';

?>
<form action="helptopics.php" method="POST" name="topics">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?php echo __('Help Topics');?></h2>
            </div>
            <div class="pull-right flush-right">
                <?php if ($cfg->getTopicSortMode() != 'a') { ?>
                <button class="button no-confirm" type="submit" name="sort"><i class="icon-save"></i>
                <?php echo __('Save'); ?></button>
                <?php } ?>
                <a href="helptopics.php?a=add" class="green button action-button"><i class="icon-plus-sign"></i> <?php echo __('Add New Help Topic');?></a>

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
                        <li>
                            <a class="confirm" data-name="enable" href="helptopics.php?a=enable">
                                <i class="icon-ok-sign icon-fixed-width"></i>
                                <?php echo __( 'Enable'); ?>
                            </a>
                        </li>
                        <li>
                            <a class="confirm" data-name="disable" href="helptopics.php?a=disable">
                                <i class="icon-ban-circle icon-fixed-width"></i>
                                <?php echo __( 'Disable'); ?>
                            </a>
                        </li>
                        <li class="danger">
                            <a class="confirm" data-name="delete" href="helptopics.php?a=delete">
                                <i class="icon-trash icon-fixed-width"></i>
                                <?php echo __( 'Delete'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
<input type="hidden" id="action" name="a" value="sort" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100">

    <thead>
<tr><td colspan="7">
    <div style="padding:3px" class="pull-right"><?php echo __('Sorting Mode'); ?>
    <select name="help_topic_sort_mode" onchange="javascript:
    var $form = $(this).closest('form');
    $form.find('input[name=a]').val('sort');
    $form.submit();
">
<?php foreach (OsticketConfig::allTopicSortModes() as $i=>$m)
    echo sprintf('<option value="%s"%s>%s</option>',
        $i, $i == $cfg->getTopicSortMode() ? ' selected="selected"' : '', $m); ?>
        </select>
    </div>
</td></tr>
        <tr>
            <th width="4%" style="height:20px;">&nbsp;</th>
            <th style="padding-left:4px;vertical-align:middle" width="36%"><?php echo __('Help Topic'); ?></th>
            <th style="padding-left:4px;vertical-align:middle" width="8%"><?php echo __('Status'); ?></th>
            <th style="padding-left:4px;vertical-align:middle" width="8%"><?php echo __('Type'); ?></th>
            <th style="padding-left:4px;vertical-align:middle" width="10%"><?php echo __('Priority'); ?></th>
            <th style="padding-left:4px;vertical-align:middle" width="14%"><?php echo __('Department'); ?></th>
            <th style="padding-left:4px;vertical-align:middle" width="20%" nowrap><?php echo __('Last Updated'); ?></th>
        </tr>
    </thead>
    <tbody class="<?php if ($cfg->getTopicSortMode() == 'm') echo 'sortable-rows'; ?>"
        data-sort="sort-">
    <?php
        $ids= ($errors && is_array($_POST['ids'])) ? $_POST['ids'] : null;
        if ($count) {
            $topics = Topic::objects()
                ->order_by(sprintf('%s%s',
                            strcasecmp($order, 'DESC') ? '' : '-',
                            $order_by))
                ->limit($pageNav->getLimit())
                ->offset($pageNav->getStart());

            $defaultDept = $cfg->getDefaultDept();
            $defaultPriority = $cfg->getDefaultPriority();
            $sort = 0;
            foreach($topics as $topic) {
                $id = $topic->getId();
                $sort++; // Track initial order for transition
                $sel=false;
                if ($ids && in_array($id, $ids))
                    $sel=true;

                if ($topic->dept_id) {
                    $deptId = $topic->dept_id;
                    $dept = (string) $topic->dept;
                } elseif ($defaultDept) {
                    $deptId = $defaultDept->getId();
                    $dept = (string) $defaultDept;
                } else {
                    $deptId = 0;
                    $dept = '';
                }
                $priority = $topic->priority ?: $defaultPriority;
                ?>
            <tr id="<?php echo $id; ?>">
                <td align="center">
                  <input type="hidden" name="sort-<?php echo $id; ?>" value="<?php
                        echo $topic->sort ?: $sort; ?>"/>
                  <input type="checkbox" class="ckb" name="ids[]"
                    value="<?php echo $id; ?>" <?php
                    echo $sel ? 'checked="checked"' : ''; ?>>
                </td>
                <td>
                    <?php
                    if ($cfg->getTopicSortMode() == 'm') { ?>
                        <i class="icon-sort faded"></i>
                    <?php } ?>
                    <a href="helptopics.php?id=<?php echo $id; ?>"><?php
                    echo Topic::getTopicName($id); ?></a>&nbsp;
                </td>
                <td><?php echo $topic->isactive ? __('Active') : '<b>'.__('Disabled').'</b>'; ?></td>
                <td><?php echo $topic->ispublic ? __('Public') : '<b>'.__('Private').'</b>'; ?></td>
                <td><?php echo $priority; ?></td>
                <td><a href="departments.php?id=<?php echo $deptId;
                ?>"><?php echo $dept; ?></a></td>
                <td>&nbsp;<?php echo Format::datetime($team->updated); ?></td>
            </tr>
            <?php
            } //end of foreach.
        }?>
    <tfoot>
     <tr>
        <td colspan="7">
            <?php if ($count) { ?>
            <?php echo __('Select');?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No help topics found');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if ($count): //Show options..
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
    <p class="confirm-action" style="display:none;" id="enable-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>enable</b> %s?'),
            _N('selected help topic', 'selected help topics', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="disable-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>disable</b> %s?'),
            _N('selected help topic', 'selected help topics', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected help topic', 'selected help topics', 2));?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered.');?>
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
