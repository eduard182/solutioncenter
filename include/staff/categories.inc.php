<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

$qs = array();
$categories = Category::objects()
    ->annotate(array('faq_count'=>SqlAggregate::COUNT('faqs')));
$sortOptions=array('name'=>'name','type'=>'ispublic','faqs'=>'faq_count','updated'=>'updated');
$orderWays=array('DESC'=>'-','ASC'=>'');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column ?: 'name';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order ?: '';

$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$total=$categories->count();
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('categories.php', $qs);
$qstr = '&amp;order='.($order=='DESC'?'ASC':'DESC');
$pageNav->paginate($categories);

if ($total)
    $showing=$pageNav->showing().' '.__('categories');
else
    $showing=__('No FAQ categories found!');

?>

<form action="categories.php" method="POST" id="mass-actions">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?php echo __('FAQ Categories');?></h2>
            </div>
            <div class="pull-right flush-right page-top">		
			
				<a id="tickets-action" class="muted category-add" href="categories.php?a=add">
					<div class="action-button popup-dialog category-add gray">
						<div class="button-icon">
							<svg viewBox="0 0 24 24">
								<path d="M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M13,7H11V11H7V13H11V17H13V13H17V11H13V7Z"></path>
							</svg>
						</div>
						<div class="button-text user-lookup">
							<?php echo __( 'Add New Category');?>
						</div>
						<div class="button-spacing">
							&nbsp;
						</div>
					</div>
				</a>				
			
                <div class="pull-right flush-right">
					
					<a id="tickets-action" class="muted category" href="#users/add">
						<div class="action-button popup-dialog user-add gray" data-dropdown="#action-dropdown-more">
							<div class="button-icon">
								<svg viewBox="0 0 24 24">
									<path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"></path>
								</svg>
							</div>
							<div class="button-text user-lookup">
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
                                <a class="confirm" data-form-id="mass-actions" data-name="delete" href="categories.php?a=delete">
                                    <i class="icon-trash icon-fixed-width"></i>
                                    <?php echo __( 'Delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="mass_process" >
    <input type="hidden" id="action" name="a" value="" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100">
    <thead>
        <tr>
            <th width="4%"><svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path></svg></th>
            <th width="56%"><a <?php echo $name_sort; ?> href="categories.php?<?php echo $qstr; ?>&sort=name"><?php echo __('Name');?></a></th>
            <th width="10%"><a  <?php echo $type_sort; ?> href="categories.php?<?php echo $qstr; ?>&sort=type"><?php echo __('Type');?></a></th>
            <th width="10%"><a  <?php echo $faqs_sort; ?> href="categories.php?<?php echo $qstr; ?>&sort=faqs"><?php echo __('FAQs');?></a></th>
            <th width="20%" nowrap><a  <?php echo $updated_sort; ?>href="categories.php?<?php echo $qstr; ?>&sort=updated"><?php echo __('Last Updated');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        foreach ($categories as $C) {
            $sel=false;
            if ($ids && in_array($C->getId(), $ids))
                $sel=true;

            $faqs=0;
            if ($C->faq_count)
                $faqs=sprintf('<a href="faq.php?cid=%d">%d</a>',$C->getId(),$C->faq_count);
            ?>
            <tr id="<?php echo $C->getId(); ?>">
                <td align="center">
                  <input type="checkbox" name="ids[]" value="<?php echo $C->getId(); ?>" class="ckb"
                            <?php echo $sel?'checked="checked"':''; ?>>
                </td>
                <td><a class="truncate" style="width:500px" href="categories.php?id=<?php echo $C->getId(); ?>"><?php
                    echo $C->getLocalName(); ?></a></td>
                <td><?php echo $C->getVisibilityDescription(); ?></td>
                <td style="text-align:right;padding-right:25px;"><?php echo $faqs; ?></td>
                <td>&nbsp;<?php echo Format::datetime($C->updated); ?></td>
            </tr><?php
        } // end of foreach ?>
    <tfoot>
     <tr>
        <td colspan="5">
            <?php if($res && $num){ ?>
            <?php echo __('Select');?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No FAQ categories found!');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if($res && $num): //Show options..
    echo '<div class="page-links">&nbsp;'.__('Page').':'.$pageNav->getPageLinks().'&nbsp;</div>';
?>
<p class="centered" id="actions">
    <input class="button" type="submit" name="make_public" value="<?php echo __('Make Public');?>">
    <input class="button" type="submit" name="make_private" value="<?php echo __('Make Internal');?>">
    <input class="button" type="submit" name="delete" value="<?php echo __('Delete');?>" >
</p>
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
            _N('selected category', 'selected categories', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="make_private-confirm">
        <?php echo sprintf(__('Are you sure you want to make %s <b>private</b> (internal)?'),
            _N('selected category', 'selected categories', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?php echo sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected category', 'selected categories', 2));?></strong></font>
        <br><br><?php echo __('Deleted data CANNOT be recovered, including any associated FAQs.'); ?>
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
