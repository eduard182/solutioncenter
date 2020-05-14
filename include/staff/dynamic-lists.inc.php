<form action="lists.php" method="POST" name="lists">

<div class="sticky bar opaque">
    <div class="content">
        <div class="pull-left flush-left">
            <h2><?php echo __('Custom Lists'); ?></h2>
        </div>
        <div class="pull-right flush-right">
            <a href="lists.php?a=add" class="green button action-button"><i class="icon-plus-sign"></i> <?php
                    echo __('Add New Custom List'); ?></a>

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
                    <li class="danger">
                        <a class="confirm" data-name="delete" href="lists.php?a=delete">
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

<?php
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
$count = DynamicList::objects()->count();
$pageNav = new Pagenate($count, $page, PAGE_LIMIT);
$pageNav->setURL('lists.php');
$showing=$pageNav->showing().' '._N('custom list', 'custom lists', $count);

?>
<?php csrf_token(); ?>
<input type="hidden" name="do" value="mass_process" >
<input type="hidden" id="action" name="a" value="" >
<table class="list" border="0" cellspacing="0" cellpadding="0" width="100">
    <thead>
        <tr>
            <th width="4%">&nbsp;</th>
            <th width="32%"><?php echo __('List Name'); ?></th>
            <th width="32%"><?php echo __('Created') ?></th>
            <th width="32%"><?php echo __('Last Updated'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach (DynamicList::objects()->order_by('-type', 'name')
                ->limit($pageNav->getLimit())
                ->offset($pageNav->getStart()) as $list) {
            $sel = false;
            if ($ids && in_array($form->get('id'),$ids))
                $sel = true; ?>
        <tr>
            <td align="center">
                <?php
                if ($list->isDeleteable()) { ?>
                <input width="7" type="checkbox" class="ckb" name="ids[]"
                value="<?php echo $list->getId(); ?>"
                    <?php echo $sel?'checked="checked"':''; ?>>
                <?php
                } else {
                    echo '&nbsp;';
                }
                ?>
            </td>
            <td><a href="?id=<?php echo $list->getId(); ?>"><?php echo
            $list->getPluralName() ?: $list->getName(); ?></a></td>
            <td><?php echo $list->get('created'); ?></td>
            <td><?php echo $list->get('updated'); ?></td>
        </tr>
    <?php }
    ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="4">
            <?php if($count){ ?>
            <?php echo __('Select'); ?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All'); ?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None'); ?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle'); ?></a>&nbsp;&nbsp;
            <?php } else {
                echo sprintf(__('No custom lists defined yet &mdash; %s add one %s!'),
                    '<a href="lists.php?a=add">','</a>');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if ($count) //Show options..
    echo '<div class="page-links">&nbsp;'.__('Page').':'.$pageNav->getPageLinks().'&nbsp;</div>';
?>

</form>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm'); ?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(
        __('Are you sure you want to DELETE %s?'),
        _N('selected custom list', 'selected custom lists', 2)); ?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered.'); ?>
    </p>
    <div><?php echo __('&nbsp;'); ?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="No, Cancel" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="Yes, Do it!" class="confirm">
        </span>
    </p>
    <div class="clear"></div>
</div>
