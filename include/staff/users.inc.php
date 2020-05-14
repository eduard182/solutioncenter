<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

$qs = array();

$users = User::objects()
    ->annotate(array('ticket_count'=>SqlAggregate::COUNT('tickets')));

if ($_REQUEST['query']) {
    $search = $_REQUEST['query'];
    $users->filter(Q::any(array(
        'emails__address__contains' => $search,
        'name__contains' => $search,
        'org__name__contains' => $search,
        // TODO: Add search for cdata
    )));
    $qs += array('query' => $_REQUEST['query']);
}

$sortOptions = array('name' => 'name',
                     'email' => 'emails__address',
                     'status' => 'account__status',
                     'create' => 'created',
                     'update' => 'updated');
$orderWays = array('DESC'=>'-','ASC'=>'');
$sort= ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';
//Sorting options...
if ($sort && $sortOptions[$sort])
    $order_column =$sortOptions[$sort];

$order_column = $order_column ?: 'name';

if ($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])])
    $order = $orderWays[strtoupper($_REQUEST['order'])];

if ($order_column && strpos($order_column,','))
    $order_column = str_replace(','," $order,",$order_column);

$x=$sort.'_sort';
$$x=' class="'.($order == '' ? 'asc' : 'desc').'" ';

$total = $users->count();
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,PAGE_LIMIT);
$pageNav->paginate($users);

$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('users.php', $qs);
$qstr.='&amp;order='.($order=='-' ? 'ASC' : 'DESC');

//echo $query;
$_SESSION[':Q:users'] = clone $users;

$users->values('id', 'name', 'default_email__address', 'account__id',
    'account__status', 'created', 'updated');
$users->order_by($order . $order_column);
?>
<div id="basic_search">
    <div style="min-height:25px;">
        <form action="users.php" method="get">
            <?php csrf_token(); ?>
            <input type="hidden" name="a" value="search">
            <div class="attached input">
                <input type="search" class="basic-search" id="basic-user-search" name="query"
                         size="30" value="<?php echo Format::htmlchars($_REQUEST['query']); ?>"
                        autocomplete="off" autocorrect="off" autocapitalize="off">
            <!-- <td>&nbsp;&nbsp;<a href="" id="advanced-user-search">[advanced]</a></td> -->
				<button type="submit" class="attached button">
					<svg id="search-icon" viewBox="0 0 24 24">
						<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.43,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.43C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,14C11.11,14 12.5,13.15 13.32,11.88C12.5,10.75 11.11,10 9.5,10C7.89,10 6.5,10.75 5.68,11.88C6.5,13.15 7.89,14 9.5,14M9.5,5A1.75,1.75 0 0,0 7.75,6.75A1.75,1.75 0 0,0 9.5,8.5A1.75,1.75 0 0,0 11.25,6.75A1.75,1.75 0 0,0 9.5,5Z" />
					</svg>
                </button>
            </div>
        </form>
    </div>
 </div>
<form id="users-list" action="users.php" method="POST" name="staff" >

<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?php echo __('User Directory'); ?></h2>
            </div>
            <div class="pull-right page-top">
                <?php if ($thisstaff->hasPerm(User::PERM_CREATE)) { ?>
				

<a id="tickets-action" class="popup-dialog users-import" href="#users/add">
	<div class="action-button popup-dialog add-user gray">
		<div class="button-icon">
			<svg viewBox="0 0 24 24">
				<path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"></path>
			</svg>
		</div>
		<div class="button-text user-lookup">
			<?php echo __('Add User'); ?>
		</div>
		<div class="button-spacing">
			&nbsp;
		</div>
	</div>
</a>
				
				
<a id="tickets-action" class=" popup-dialog users-import" href="#users/import">
<div class="action-button popup-dialog users-import gray" href="#users/import">
    <div class="button-icon">
		<svg viewBox="0 0 24 24">
			<path d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z" />
		</svg>
    </div>
    <div class="button-text user-lookup">
        <?php echo __('Import'); ?>
    </div>
    <div class="button-spacing">
    	&nbsp;
    </div>
</div>
</a>				
<?php } ?>


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
                        <?php if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
                        <li><a href="#add-to-org" class="users-action">
                            <i class="icon-group icon-fixed-width"></i>
                            <?php echo __('Add to Organization'); ?></a></li>
                        <?php
                            }
                        if ('disabled' != $cfg->getClientRegistrationMode()) { ?>
                        <li><a class="users-action" href="#reset">
                            <i class="icon-envelope icon-fixed-width"></i>
                            <?php echo __('Send Password Reset Email'); ?></a></li>
                        <?php if ($thisstaff->hasPerm(User::PERM_MANAGE)) { ?>
                        <li><a class="users-action" href="#register">
                            <i class="icon-smile icon-fixed-width"></i>
                            <?php echo __('Register'); ?></a></li>
                        <li><a class="users-action" href="#lock">
                            <i class="icon-lock icon-fixed-width"></i>
                            <?php echo __('Lock'); ?></a></li>
                        <li><a class="users-action" href="#unlock">
                            <i class="icon-unlock icon-fixed-width"></i>
                            <?php echo __('Unlock'); ?></a></li>
                        <?php }
                        if ($thisstaff->hasPerm(User::PERM_DELETE)) { ?>
                        <li class="danger"><a class="users-action" href="#delete">
                            <i class="icon-trash icon-fixed-width"></i>
                            <?php echo __('Delete'); ?></a></li>
                        <?php }
                        } # end of registration-enabled? ?>
                    </ul>
                </div>	
</a>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
$showing = $search ? __('Search Results').': ' : '';
if($users->exists(true))
    $showing .= $pageNav->showing();
else
    $showing .= __('No users found!');
?>
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="action" name="a" value="" >
 <input type="hidden" id="selected-count" name="count" value="" >
 <input type="hidden" id="org_id" name="org_id" value="" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100">
    <thead>
        <tr>
            <th class="user-list-th-checkbox"><i class="material-icons md-light md-84">check</i></th>
			<th class="user-list-th-name"><a <?php echo $name_sort; ?> href="users.php?<?php
	            echo $qstr; ?>&sort=name"><?php echo __('Name'); ?></a></th>
	        <th class="user-list-th-tickets"><a  <?php echo $status_sort; ?> href="users.php?<?php
	            echo $qstr; ?>&sort=status"><?php echo __('Tickets'); ?></a></th>					
            <th class="user-list-th-status"><a  <?php echo $status_sort; ?> href="users.php?<?php
                echo $qstr; ?>&sort=status"><?php echo __('Status'); ?></a></th>
			<th class="user-list-th-created"><a <?php echo $create_sort; ?> href="users.php?<?php
                echo $qstr; ?>&sort=create"><?php echo __('Created'); ?></a></th>
			<th class="user-list-th-updated"><a <?php echo $update_sort; ?> href="users.php?<?php
                echo $qstr; ?>&sort=update"><?php echo __('Updated'); ?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        foreach ($users as $U) {
                // Default to email address mailbox if no name specified
                if (!$U['name'])
                    list($name) = explode('@', $U['default_email__address']);
                else
                    $name = new UsersName($U['name']);

                // Account status
                if ($U['account__id'])
                    $status = new UserAccountStatus($U['account__status']);
                else
                    $status = __('Guest');

                $sel=false;
                if($ids && in_array($U['id'], $ids))
                    $sel=true;
                ?>
               <tr id="<?php echo $U['id']; ?>">
			   
				<td id="user-list-td-checkbox" nowrap="">
                    <input type="checkbox" id="checkboxG4-<?php echo $U['id']; ?>" value="<?php echo $U['id']; ?>" class="ckb mass nowarn css-checkbox"/>
					<label for="checkboxG4-<?php echo $U['id']; ?>" class="css-label"></label>
                </td>					
                <td>&nbsp;
                    <a class="preview"
                        href="users.php?id=<?php echo $U['id']; ?>"
                        data-preview="#users/<?php echo $U['id']; ?>/preview"><?php
                        echo Format::htmlchars($name); ?></a>
                    &nbsp;

                </td>
				<td id="user-list-td-tickets">
                    <?php
                    if ($U['ticket_count'])
                         echo sprintf('<i class="icon-fixed-width icon-file-text-alt"></i>
                             <small>x %d</small>', $U['ticket_count']);
                    ?>
				</td>				
                <td id="user-list-td-status"><?php echo $status; ?></td>
                <td id="user-list-td-created"><?php echo Format::date($U['created']); ?></td>
                <td id="user-list-td-updated"><?php echo Format::datetime($U['updated']); ?>&nbsp;</td>
               </tr>
<?php   } //end of foreach. ?>
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
if ($total) {
    echo sprintf('<div id="page-count">&nbsp;'.__('Page').': %s &nbsp; <a class="no-pjax"
            href="users.php?a=export&qh=%s">'.__('Export').'</a></div>',
            $pageNav->getPageLinks(),
            $qhash);
}
?>
</form>

<script type="text/javascript">
$(function() {
    $('input#basic-user-search').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/users/local?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            window.location.href = 'users.php?id='+obj.id;
        },
        property: "/bin/true"
    });

    $(document).on('click', 'a.popup-dialog', function(e) {
        e.preventDefault();
        $.userLookup('ajax.php/' + $(this).attr('href').substr(1), function (user) {
            var url = window.location.href;
            if (user && user.id)
                url = 'users.php?id='+user.id;
            $.pjax({url: url, container: '#pjax-container'})
            return false;
         });

        return false;
    });
    var goBaby = function(action, confirmed) {
        var ids = [],
            $form = $('form#users-list');
        $(':checkbox.mass:checked', $form).each(function() {
            ids.push($(this).val());
        });
        if (ids.length) {
          var submit = function(data) {
            $form.find('#action').val(action);
            $.each(ids, function() { $form.append($('<input type="hidden" name="ids[]">').val(this)); });
            if (data)
              $.each(data, function() { $form.append($('<input type="hidden">').attr('name', this.name).val(this.value)); });
            $form.find('#selected-count').val(ids.length);
            $form.submit();
          };
          var options = {};
          if (action === 'delete') {
              options['deletetickets']
                =  __('Also delete all associated tickets and attachments');
          }
          else if (action === 'add-to-org') {
            $.dialog('ajax.php/orgs/lookup/form', 201, function(xhr, json) {
              var $form = $('form#users-list');
              try {
                  var json = $.parseJSON(json),
                      org_id = $form.find('#org_id');
                  if (json.id) {
                      org_id.val(json.id);
                      goBaby('setorg', true);
                  }
              }
              catch (e) { }
            });
            return;
          }
          if (!confirmed)
              $.confirm(__('You sure?'), undefined, options).then(submit);
          else
              submit();
        }
        else {
            $.sysAlert(__('Oops'),
                __('You need to select at least one item'));
        }
    };
    $(document).on('click', 'a.users-action', function(e) {
        e.preventDefault();
        goBaby($(this).attr('href').substr(1));
        return false;
    });
});
</script>

