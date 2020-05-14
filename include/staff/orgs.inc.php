<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

$qs = array();

$select = 'SELECT org.*
            ,COALESCE(team.name,
                    IF(staff.staff_id, CONCAT_WS(" ", staff.firstname, staff.lastname), NULL)
                    ) as account_manager ';
$from = 'FROM '.ORGANIZATION_TABLE.' org '
       .'LEFT JOIN '.STAFF_TABLE.' staff ON (
           LEFT(org.manager, 1) = "s" AND staff.staff_id = SUBSTR(org.manager, 2)) '
       .'LEFT JOIN '.TEAM_TABLE.' team ON (
           LEFT(org.manager, 1) = "t" AND team.team_id = SUBSTR(org.manager, 2)) ';

$where = ' WHERE 1 ';

if ($_REQUEST['query']) {

    $from .=' LEFT JOIN '.FORM_ENTRY_TABLE.' entry
                ON (entry.object_type=\'O\' AND entry.object_id = org.id)
              LEFT JOIN '.FORM_ANSWER_TABLE.' value
                ON (value.entry_id=entry.id) ';

    $search = db_input(strtolower($_REQUEST['query']), false);
    $where .= ' AND (
                    org.name LIKE \'%'.$search.'%\' OR value.value LIKE \'%'.$search.'%\'
                )';

    $qs += array('query' => $_REQUEST['query']);
}

$sortOptions = array('name' => 'org.name',
                     'users' => 'users',
                     'create' => 'org.created',
                     'update' => 'org.updated');
$orderWays = array('DESC'=>'DESC','ASC'=>'ASC');
$sort= ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';
//Sorting options...
if ($sort && $sortOptions[$sort])
    $order_column =$sortOptions[$sort];

$order_column = $order_column ?: 'org.name';

if ($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])])
    $order = $orderWays[strtoupper($_REQUEST['order'])];

$order=$order ?: 'ASC';
if ($order_column && strpos($order_column,','))
    $order_column = str_replace(','," $order,",$order_column);

$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$total=db_count('SELECT count(DISTINCT org.id) '.$from.' '.$where);
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,PAGE_LIMIT);
$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('orgs.php', $qs);
$qstr.='&amp;order='.($order=='DESC' ? 'ASC' : 'DESC');

$select .= ', count(DISTINCT user.id) as users ';

$from .= ' LEFT JOIN '.USER_TABLE.' user ON (user.org_id = org.id) ';


$query="$select $from $where GROUP BY org.id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;
$qhash = md5($query);
$_SESSION['orgs_qs_'.$qhash] = $query;
?>
<div id="basic_search">
    <div style="min-height:25px;">
        <form action="orgs.php" method="get">
            <?php csrf_token(); ?>
			

            <div class="attached input">
            <input type="hidden" name="a" value="search">
            <input type="search" class="basic-search" id="basic-org-search" name="query" autofocus size="30" value="<?php echo Format::htmlchars($_REQUEST['query']); ?>" autocomplete="off" autocorrect="off" autocapitalize="off">
				<button type="submit" class="attached button">
					<svg id="search-icon" viewBox="0 0 24 24">
						<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.43,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.43C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,14C11.11,14 12.5,13.15 13.32,11.88C12.5,10.75 11.11,10 9.5,10C7.89,10 6.5,10.75 5.68,11.88C6.5,13.15 7.89,14 9.5,14M9.5,5A1.75,1.75 0 0,0 7.75,6.75A1.75,1.75 0 0,0 9.5,8.5A1.75,1.75 0 0,0 11.25,6.75A1.75,1.75 0 0,0 9.5,5Z" />
					</svg>
                </button>
            </div>			
			
			
			
			
			
        </form>
    </div>
</div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?php echo __('Organizations'); ?></h2>
            </div>
            <div class="pull-right page-top">
                <?php if ($thisstaff->hasPerm(Organization::PERM_CREATE)) { ?>
				
<a id="tickets-action" class="popup-dialog add-org" href="#users/add">
	<div class="action-button popup-dialog add-user gray">
		<div class="button-icon">
			<svg viewBox="0 0 24 24">
				<path d="M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M13,7H11V11H7V13H11V17H13V13H17V11H13V7Z" />
			</svg>
		</div>
		<div class="button-text user-lookup">
			<?php echo __('Add Organization'); ?>
		</div>
		<div class="button-spacing">
			&nbsp;
		</div>
	</div>
</a>			
                <?php }
            if ($thisstaff->hasPerm(Organization::PERM_DELETE)) { ?>
			
			
				
<a id="tickets-action" href="#statuses">
	<div id="status" class="action-button users-more gray" data-dropdown="#action-dropdown-more" style="/*DELME*/">
		<div class="button-icon">
			<svg viewBox="0 0 24 24">
				<path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
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
				
				
				
                <div id="action-dropdown-more" class="action-dropdown anchor-right">
                    <ul>
                        <li class="danger"><a class="orgs-action" href="#delete">
                            <i class="icon-trash icon-fixed-width"></i>
                            <?php echo __('Delete'); ?></a></li>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$showing = $search ? __('Search Results').': ' : '';
$res = db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing .= $pageNav->showing();
else
    $showing .= __('No organizations found!');
?>
<form id="orgs-list" action="orgs.php" method="POST" name="staff" >
 <?php csrf_token(); ?>
 <input type="hidden" name="a" value="mass_process" >
 <input type="hidden" id="action" name="do" value="" >
 <input type="hidden" id="selected-count" name="count" value="" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100">
    <thead>
        <tr>
            <th class="head-checkbox" nowrap width="4%"><svg viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path></svg></th>
            <th width="45%"><a <?php echo $name_sort; ?> href="orgs.php?<?php echo $qstr; ?>&sort=name"><?php echo __('Name'); ?></a></th>
            <th width="11%"><a <?php echo $users_sort; ?> href="orgs.php?<?php echo $qstr; ?>&sort=users"><?php echo __('Users'); ?></a></th>
            <th width="20%"><a <?php echo $create_sort; ?> href="orgs.php?<?php echo $qstr; ?>&sort=create"><?php echo __('Created'); ?></a></th>
            <th width="20%"><a <?php echo $update_sort; ?> href="orgs.php?<?php echo $qstr; ?>&sort=update"><?php echo __('Last Updated'); ?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        if($res && db_num_rows($res)):
            $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
            while ($row = db_fetch_array($res)) {

                $sel=false;
                if($ids && in_array($row['id'], $ids))
                    $sel=true;
                ?>
               <tr id="<?php echo $row['id']; ?>">
                <td nowrap align="center">
                    <input type="checkbox" value="<?php echo $row['id']; ?>" class="ckb mass nowarn"/>
                </td>
                <td>&nbsp; <a href="orgs.php?id=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a> </td>
                <td>&nbsp;<?php echo $row['users']; ?></td>
                <td><?php echo Format::date($row['created']); ?></td>
                <td><?php echo Format::datetime($row['updated']); ?>&nbsp;</td>
               </tr>
            <?php
            } //end of while.
        endif; ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="7">
            <?php if ($res && $num) { ?>
            <?php echo __('Select');?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo '<i>';
                echo __('Query returned 0 results.');
                echo '</i>';
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if($res && $num): //Show options..
    echo sprintf('<div id="page-count">&nbsp;%s: %s &nbsp; <a class="no-pjax"
            href="orgs.php?a=export&qh=%s">%s</a></div>',
            __('Page'),
            $pageNav->getPageLinks(),
            $qhash,
            __('Export'));
endif;
?>
</form>

<script type="text/javascript">
$(function() {
    $('input#basic-org-search').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/orgs/search?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            window.location.href = 'orgs.php?id='+obj.id;
        },
        property: "/bin/true"
    });

    $(document).on('click', 'a.add-org', function(e) {
        e.preventDefault();
        $.orgLookup('ajax.php/orgs/add', function (org) {
            var url = 'orgs.php?id=' + org.id;
            $.pjax({url: url, container: '#pjax-container'})
         });

        return false;
     });

    var goBaby = function(action) {
        var ids = [],
            $form = $('form#orgs-list');
        $(':checkbox.mass:checked', $form).each(function() {
            ids.push($(this).val());
        });
        if (ids.length) {
          var submit = function() {
            $form.find('#action').val(action);
            $.each(ids, function() { $form.append($('<input type="hidden" name="ids[]">').val(this)); });
            $form.find('#selected-count').val(ids.length);
            $form.submit();
          };
          $.confirm(__('You sure?')).then(submit);
        }
        else if (!ids.length) {
            $.sysAlert(__('Oops'),
                __('You need to select at least one item'));
        }
    };
    $(document).on('click', 'a.orgs-action', function(e) {
        e.preventDefault();
        goBaby($(this).attr('href').substr(1));
        return false;
    });
});
</script>
