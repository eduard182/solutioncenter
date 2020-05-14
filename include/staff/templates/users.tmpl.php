<?php
$qs = array();
$select = 'SELECT user.*, email.address as email ';

$from = 'FROM '.USER_TABLE.' user '
      . 'LEFT JOIN '.USER_EMAIL_TABLE.' email ON (user.id = email.user_id) ';

$where = ' WHERE user.org_id='.db_input($org->getId());

$sortOptions = array('name' => 'user.name',
                     'email' => 'email.address',
                     'create' => 'user.created',
                     'update' => 'user.updated');
$orderWays = array('DESC'=>'DESC','ASC'=>'ASC');
$sort= ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';
//Sorting options...
if ($sort && $sortOptions[$sort])
    $order_column =$sortOptions[$sort];

$order_column = $order_column ?: 'user.name';

if ($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])])
    $order = $orderWays[strtoupper($_REQUEST['order'])];

$order=$order ?: 'ASC';
if ($order_column && strpos($order_column,','))
    $order_column = str_replace(','," $order,",$order_column);

$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$total=db_count('SELECT count(DISTINCT user.id) '.$from.' '.$where);
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,PAGE_LIMIT);
$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);

$pageNav->setURL('users.php', $qs);
//Ok..lets roll...create the actual query
$qstr .= '&amp;order='.($order=='DESC' ? 'ASC' : 'DESC');

$select .= ', count(DISTINCT ticket.ticket_id) as tickets ';

$from .= ' LEFT JOIN '.TICKET_TABLE.' ticket ON (ticket.user_id = user.id) ';


$query="$select $from $where GROUP BY user.id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;

$showing = $search ? __('Search Results').': ' : '';
$res = db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing .= $pageNav->showing();
else
    $showing .= __("This organization doesn't have any users yet");

?>
<form action="orgs.php?id=<?php echo $org->getId(); ?>" method="POST" name="users" id="users">

<div style="margin-top:5px;" class="pull-left"><b><?php echo $showing; ?></b></div>
<?php if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
<div class="pull-right flush-right" style="margin-bottom:10px;">
    <a href="#orgs/<?php echo $org->getId(); ?>/add-user" class="green button action-button add-user"><i class="icon-plus"></i> <?php echo __('Add User'); ?></a>
    <a href="#orgs/<?php echo $org->getId(); ?>/import-users" class="button action-button add-user"><i class="icon-cloud-upload icon-large"></i><?php echo __('Import'); ?></a>
    <button id="actions" class="red button action-button" type="submit" name="remove-users"><i class="icon-trash"></i> <?php echo __('Remove'); ?></button>

    <div id="manager" class="action-button popup-dialog add-user gray">
        <div class="button-icon">
            <!-- <svg viewBox="0 0 24 24">
                <path d="M15,14C12.33,14 7,15.33 7,18V20H23V18C23,15.33 17.67,14 15,14M6,10V7H4V10H1V12H4V15H6V12H9V10M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12Z"></path>
            </svg> -->
        </div>
        <div class="button-text user-lookup">Owner(+/-)</div>
        <div class="button-spacing">&nbsp;</div>
    </div>




</div>
<?php } ?>
<div class="clear"></div>
<?php
if ($num) { ?>
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="id" name="id" value="<?php echo $org->getId(); ?>" >
 <input type="hidden" id="action" name="a" value="" >
 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100">
    <thead>
        <tr>
            <th width="4%">&nbsp;</th>
            <th width="38%"><?php echo __('Name'); ?></th>
            <th width="35%"><?php echo __('Email'); ?></th>
            <th width="8%"><?php echo __('Status'); ?></th>
            <th width="15%"><?php echo __('Created'); ?></th>
            <th width="15%"><?php echo __('Manager'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        if($res && db_num_rows($res)):
            $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
            while ($row = db_fetch_array($res)) {

                $name = new UsersName($row['name']);
                $status = 'Active';
                $sel=false;
                if($ids && in_array($row['id'], $ids))
                    $sel=true;
                ?>
               <tr id="<?php echo $row['id']; ?>">
                <td align="center">
                  <input type="checkbox" class="ckb" name="ids[]"
                    value="<?php echo $row['id']; ?>" <?php echo $sel?'checked="checked"':''; ?> >
                </td>
                <td>&nbsp;
                    <a class="preview"
                        href="users.php?id=<?php echo $row['id']; ?>"
                        data-preview="#users/<?php
                        echo $row['id']; ?>/preview" ><?php
                        echo Format::htmlchars($name); ?></a>
                    &nbsp;
                    <?php
                    if ($row['tickets'])
                         echo sprintf('<i class="icon-fixed-width icon-file-text-alt"></i>
                             <small>(%d)</small>', $row['tickets']);
                    ?>
                </td>
                <td><?php echo Format::htmlchars($row['email']); ?></td>
                <td><?php echo $status; ?></td>
                <td><?php echo Format::date($row['created']); ?></td>
                <td><?php if($row['ismanager']>0) echo "Si"; else echo "No"; ?></td>
               </tr>
            <?php
            } //end of while.
        endif; ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="6">
            <?php
            if ($res && $num) {
                ?>
            <?php echo __('Select'); ?>&nbsp;
            <a id="selectAll" href="#ckb"><?php echo __('All'); ?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?php echo __('None'); ?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?php echo __('Toggle'); ?></a>&nbsp;&nbsp;
            <?php
            } else {
                echo __('No users found!');
            }
            ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if ($res && $num) { //Show options..
    echo '<div class="page-links">&nbsp;'.__('Page').':'.$pageNav->getPageLinks().'&nbsp;</div>';

    ?>

<?php
}
?>
</form>
<?php
} ?>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3 class="drag-handle"><?php echo __('Please Confirm'); ?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="remove-users-confirm">
        <?php echo sprintf(__(
        'Are you sure you want to <b>REMOVE</b> %1$s from <strong>%2$s</strong>?'),
        _N('selected user', 'selected users', 2),
        $org->getName()); ?>
    </p>
    <div><?php echo __('&nbsp;'); ?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="<?php echo __('No, Cancel'); ?>" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!'); ?>" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>

<script type="text/javascript">
$(function() {
    $(document).on('click', 'a.add-user', function(e) {
        e.preventDefault();
        $.userLookup('ajax.php/' + $(this).attr('href').substr(1), function (user) {
            if (user && user.id)
                window.location.href = 'orgs.php?id=<?php echo $org->getId(); ?>'
            else
              $.pjax({url: window.location.href, container: '#pjax-container'})
        });
        return false;
     });
    ///---------------------------------------------------------------------------------
    ///---------------------------------------------------------------------------------
    $("#manager").click(function(){
        
        var checked=$('input:checkbox:checked', $('#users')).length;
        
        if (checked==0){
            $.sysAlert( __('Alerta'),
                __("Por favor selecciona Almenos {1} Usuario!!!.").replace('{1}', 'UN (1)')
            );
        }else{
            
            $("#action").val("addmanager-users");
            $("form[name='users']").submit();

        }
    });
    ///---------------------------------------------------------------------------------
    ///---------------------------------------------------------------------------------


});
</script>

