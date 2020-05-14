<?php
if (!isset($info['title']))
    $info['title'] = Format::htmlchars($user->getName());

if ($info['title']) { ?>
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<hr>
<?php
} else {
    echo '<div class="clear"></div>';
}
if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} ?>
<div id="user-profile" style="display:<?php echo $forms ? 'none' : 'block'; ?>;margin:5px;">
    <div class="avatar pull-left" style="margin: 0 10px;">
    <?php echo $user->getAvatar(); ?>
    </div>
    <?php
    if ($ticket) { ?>
    <a class="action-button pull-right change-user" style="overflow:inherit"
        href="#tickets/<?php echo $ticket->getId(); ?>/change-user" ><i class="material-icons">account_box</i>
        <?php echo __('Change User'); ?></a>
    <?php
    } ?>
    <div><b><?php
    echo Format::htmlchars($user->getName()->getOriginal()); ?></b></div>
    <div class="faded">&lt;<?php echo $user->getEmail(); ?>&gt;</div>
    <?php
    if (($org=$user->getOrganization())) { ?>
    <div style="margin-top: 7px;"><?php echo $org->getName(); ?></div>
    <?php
    } ?>

<div class="clear"></div>
<ul class="tabs" id="user_tabs" style="margin-top:5px">
    <li class="active"><a href="#info-tab"
        ><i class="icon-info-sign"></i>&nbsp;<?php echo __('User'); ?></a></li>
<?php if ($org) { ?>
    <li><a href="#org-tab"
        ><i class="icon-fixed-width icon-building"></i>&nbsp;<?php echo __('Organization'); ?></a></li>
<?php }
    $ext_id = "U".$user->getId();
    $notes = QuickNote::forUser($user, $org)->all(); ?>
    <li><a href="#notes-tab"
        ><i class="icon-fixed-width icon-pushpin"></i>&nbsp;<?php echo __('Notes'); ?></a></li>
</ul>

<div id="user_tabs_container">
<div class="tab_content" id="info-tab">
<div class="floating-options">
<?php if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
    <a href="<?php echo $info['useredit'] ?: '#'; ?>" id="edituser" class="action" title="<?php echo __('Edit'); ?>"><i class="icon-edit"></i></a>
<?php }
      if ($thisstaff->hasPerm(User::PERM_DIRECTORY)) { ?>
    <a href="users.php?id=<?php echo $user->getId(); ?>" title="<?php
        echo __('Manage User'); ?>" class="action"><i class="icon-share"></i></a>
<?php } ?>
</div>
    <table class="custom-info" width="100%">
<?php foreach ($user->getDynamicData() as $entry) {
?>
    <tr><th colspan="2"><strong><?php
         echo $entry->getTitle(); ?></strong></td></tr>
<?php foreach ($entry->getAnswers() as $a) { ?>
    <tr><td style="width:30%;"><?php echo Format::htmlchars($a->getField()->get('label'));
         ?></td>
    <td><?php echo $a->display(); ?></td>
    </tr>
<?php }
}
?>
    </table>
</div>

<?php if ($org) { ?>
<div class="hidden tab_content" id="org-tab">
<?php if ($thisstaff->hasPerm(User::PERM_DIRECTORY)) { ?>
<div class="floating-options">
    <a href="orgs.php?id=<?php echo $org->getId(); ?>" title="<?php
    echo __('Manage Organization'); ?>" class="action"><i class="icon-share"></i></a>
</div>
<?php } ?>
    <table class="custom-info" width="100%">
<?php foreach ($org->getDynamicData() as $entry) {
?>
    <tr><th colspan="2"><strong><?php
         echo $entry->getTitle(); ?></strong></td></tr>
<?php foreach ($entry->getAnswers() as $a) { ?>
    <tr><td style="width:30%"><?php echo Format::htmlchars($a->getField()->get('label'));
         ?></td>
    <td><?php echo $a->display(); ?></td>
    </tr>
<?php }
}
?>
    </table>
</div>
<?php } # endif ($org) ?>

<div class="hidden tab_content" id="notes-tab">
<?php $show_options = true;
foreach ($notes as $note)
    include STAFFINC_DIR . 'templates/note.tmpl.php';
?>
<div id="new-note-box">
<div class="quicknote no-options" id="new-note"
    data-url="users/<?php echo $user->getId(); ?>/note">
<div class="body">
    <a href="#"><i class="icon-plus icon-large"></i> &nbsp;
    <?php echo __('Click to create a new note'); ?></a>
</div>
</div>
</div>
</div>
</div>

</div>
<div id="user-form" style="display:<?php echo $forms ? 'block' : 'none'; ?>;">
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
$action = $info['action'] ? $info['action'] : ('#users/'.$user->getId());
if ($ticket && $ticket->getOwnerId() == $user->getId())
    $action = '#tickets/'.$ticket->getId().'/user';
?>
<form method="post" class="user" action="<?php echo $action; ?>">
    <input type="hidden" name="uid" value="<?php echo $user->getId(); ?>" />
    <table width="100%">
    <?php
        if (!$forms) $forms = $user->getForms();
        foreach ($forms as $form)
            $form->render();
    ?>
    </table>
    <hr>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="<?php
    echo ($ticket && $user) ? 'cancel' : 'close' ?>"  value="<?php echo __('Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit" value="<?php echo __('Update User'); ?>">
        </span>
     </p>
</form>
</div>
<div class="clear"></div>
<script type="text/javascript">
$(function() {
    $('a#edituser').click( function(e) {
        e.preventDefault();
        if ($(this).attr('href').length > 1) {
            var url = 'ajax.php/'+$(this).attr('href').substr(1);
            $.dialog(url, [201, 204], function (xhr) {
                window.location.href = window.location.href;
            }, {
                onshow: function() { $('#user-search').focus(); }
            });
        } else {
            $('div#user-profile').hide();
            $('div#user-form').fadeIn();
        }

        return false;
     });

    $(document).on('click', 'form.user input.cancel', function (e) {
        e.preventDefault();
        $('div#user-form').hide();
        $('div#user-profile').fadeIn();
        return false;
     });
});
</script>
