<?php
if (!$info['title'])
    $info['title'] = Format::htmlchars($org->getName());
?>
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/>
<?php
if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} ?>
<div id="org-profile" style="display:<?php echo $forms ? 'none' : 'block'; ?>;margin:5px;">
    <i class="icon-group icon-4x pull-left icon-border"></i>
    <?php
    if ($user) { ?>
    <a class="action-button pull-right user-action" style="overflow:inherit"
        href="#users/<?php echo $user->getId(); ?>/org/<?php echo $org->getId(); ?>" ><i class="material-icons">account_box</i>
        <?php echo __('Change'); ?></a>
    <a class="action-button pull-right" href="orgs.php?id=<?php echo $org->getId(); ?>"><i class="icon-share"></i>
        <?php echo __('Manage'); ?></a>
    <?php
    } ?>
    <div><b><a href="#" id="editorg"><i class="icon-edit"></i>&nbsp;<?php
    echo Format::htmlchars($org->getName()); ?></a></b></div>
    <table style="margin-top: 1em;">
<?php foreach ($org->getDynamicData() as $entry) {
?>
    <tr><td colspan="2" style=""><strong><?php
         echo $entry->getTitle(); ?></strong></td></tr>
<?php foreach ($entry->getAnswers() as $a) { ?>
    <tr style="vertical-align:top"><td style="width:30%;"><?php echo Format::htmlchars($a->getField()->get('label'));
         ?></td>
    <td style=""><?php echo $a->display(); ?></td>
    </tr>
<?php }
}
?>
    </table>
    <div class="clear"></div>
    <hr>
    <div class="faded">Last updated <b><?php echo Format::datetime($org->getUpdateDate()); ?> </b></div>
</div>
<div id="org-form" style="display:<?php echo $forms ? 'block' : 'none'; ?>;">
<div id="msg_notice">
    <div id="alert-icon">
        <svg viewBox="0 0 24 24">
            <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path>
        </svg>
    </div>
    <div id="alert-text">
        <?php echo __('Please note that updates will be reflected system-wide.'); ?>
    </div>		
</div>
<?php
$action = $info['action'] ? $info['action'] : ('#orgs/'.$org->getId());
if ($ticket && $ticket->getOwnerId() == $user->getId())
    $action = '#tickets/'.$ticket->getId().'/user';
?>
<form method="post" class="org" action="<?php echo $action; ?>">
    <input type="hidden" name="id" value="<?php echo $org->getId(); ?>" />
    <table width="100%">
    <?php
        if (!$forms) $forms = $org->getForms();
        foreach ($forms as $form)
            $form->render();
    ?>
    </table>
    <hr>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="<?php
            echo $account ? 'cancel' : 'close'; ?>"  value="<?php echo __('Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" value="<?php echo __('Update Organization'); ?>">
        </span>
     </p>
</form>
</div>
<div class="clear"></div>
<script type="text/javascript">
$(function() {
    $('a#editorg').click( function(e) {
        e.preventDefault();
        $('div#org-profile').hide();
        $('div#org-form').fadeIn();
        return false;
     });

    $(document).on('click', 'form.org input.cancel', function (e) {
        e.preventDefault();
        $('div#org-form').hide();
        $('div#org-profile').fadeIn();
        return false;
     });
});
</script>
