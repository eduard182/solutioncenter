<?php

if (!$info['title'])
    $info['title'] = sprintf(__('Delete %s'), Format::htmlchars($org->getName()));

$info['error'] = __('Deleted organization CANNOT be recovered');

?>
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr/>
<?php

if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['warn']) {
    echo sprintf('<div id="msg_warning"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text">%s</div></div>', $info['warn']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} ?>

<div id="org-profile" style="margin:5px;">
    <i class="icon-group icon-4x pull-left icon-border"></i>
    <div><b> <?php echo Format::htmlchars($org->getName()); ?></b></div>
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
    <?php
    if (($users=$org->users->count())) { ?>
    <hr>
    <div>&nbsp;<strong><?php echo sprintf(__(
            '%s assigned to this organization will be orphaned.'),
            sprintf(_N('One user', '%d users', $users), $users)); ?></strong></div>
    <?php
    } ?>
    <hr>
    <form method="delete" class="org"
        action="#orgs/<?php echo $org->getId(); ?>/delete">
        <input type="hidden" name="id" value="<?php echo $org->getId(); ?>" />
        <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="close"
                value="<?php echo __('No, Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" value="<?php echo __('Yes, Delete'); ?>">
        </span>
        </p>
    </form>
</div>
<div class="clear"></div>
