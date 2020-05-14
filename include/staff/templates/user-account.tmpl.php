<?php
$account = $user->getAccount();
$access = (isset($info['_target']) && $info['_target'] == 'access');

if (!$info['title'])
    $info['title'] = Format::htmlchars($user->getName());
?>
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<a class="close" href="#"><i class="material-icons">highlight_off</i></a>
<div class="clear"></div>
<hr/>
<?php
if ($info['error']) {
    echo sprintf('<div id="msg_error"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path></svg></div><div id="alert-text">%s</div></div>', $info['error']);
} elseif ($info['msg']) {
    echo sprintf('<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path></svg></div><div id="alert-text">%s</div></div>', $info['msg']);
} ?>
<form method="post" class="user" action="#users/<?php echo $user->getId(); ?>/manage" >
<ul class="tabs" id="user-account-tabs">
    <li <?php echo !$access? 'class="active"' : ''; ?>><a href="#user-account"
        ><i class="material-icons">account_box</i>&nbsp;<?php echo __('User Information'); ?></a></li>
    <li <?php echo $access? 'class="active"' : ''; ?>><a href="#user-access"
        ><i class="icon-fixed-width icon-lock faded"></i>&nbsp;<?php echo __('Manage Access'); ?></a></li>
</ul>


 <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
<div id="user-account-tabs_container">
 <div class="tab_content"  id="user-account" style="display:<?php echo $access? 'none' : 'block'; ?>; margin:5px;">
    <form method="post" class="user" action="#users/<?php echo $user->getId(); ?>/manage" >
        <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
        <table width="100%">
        <tbody>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('User Information'); ?></strong></em>
                </th>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Name'); ?>
                </td>
                <td> <?php echo Format::htmlchars($user->getName()); ?> </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Email'); ?>
                </td>
                <td> <?php echo $user->getEmail(); ?> </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Organization'); ?>
                </td>
                <td>
                    <input type="text" size="35" name="org" value="<?php echo $info['org']; ?>">
                    &nbsp;<span class="error">&nbsp;<?php echo $errors['org']; ?></span>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <th colspan="2"><em><strong><?php echo __('User Preferences'); ?></strong></em></th>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Time Zone');?>
                </td>
                <td>
                    <?php
                    $TZ_NAME = 'timezone';
                    $TZ_TIMEZONE = $info['timezone'];
                    include STAFFINC_DIR.'templates/timezone.tmpl.php'; ?>
                    <div class="error"><?php echo $errors['timezone']; ?></div>
                </td>
            </tr>
        </tbody>
        </table>
 </div>
 <div class="tab_content"  id="user-access" style="display:<?php echo $access? 'block' : 'none'; ?>; margin:5px;">
        <table width="100%">
        <tbody>
            <tr>
                <th colspan="2"><em><strong><?php echo __('Account Access'); ?></strong></em></th>
            </tr>
            <tr>
                <td width="180"><?php echo __('Status'); ?></td>
                <td id="user-status"> <?php echo $user->getAccountStatus(); ?> </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Username'); ?>
                </td>
                <td>
                    <input type="text" size="35" name="username" value="<?php echo $info['username']; ?>">
                    <i class="help-tip icon-question-sign" data-title="<?php
                        echo __("Login via email"); ?>"
                    data-content="<?php echo sprintf('%s: %s',
                        __('Users can always sign in with their email address'),
                        $user->getEmail()); ?>"></i>
                    <div class="error"><?php echo $errors['username']; ?></div>
                </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('New Password'); ?>
                </td>
                <td>
                    <input type="password" size="35" name="passwd1" value="<?php echo $info['passwd1']; ?>">
                    &nbsp;<span class="error">&nbsp;<?php echo
                    $errors['passwd1']; ?></span>
                </td>
            </tr>
            <tr>
                <td width="180">
                   <?php echo __('Confirm Password'); ?>
                </td>
                <td>
                    <input type="password" size="35" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                    &nbsp;<span class="error">&nbsp;<?php echo $errors['passwd2']; ?></span>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <th colspan="2">&nbsp;</th>
            </tr>		
            <tr>
                <th colspan="2"><em><strong><?php echo __('Account Flags'); ?></strong></em></th>
            </tr>
            <tr>
                <td colspan="2">
                <?php
                  echo sprintf('<div><input type="checkbox" name="locked-flag" %s
                       value="1"> %s</div>',
                       $account->isLocked() ?  'checked="checked"' : '',
                       __('Administratively Locked')
                       );
                  ?>
                   <div><input type="checkbox" name="pwreset-flag" value="1" <?php
                    echo $account->isPasswdResetForced() ?
                    'checked="checked"' : ''; ?>> <?php echo __('Password Reset Required'); ?></div>
                   <div><input type="checkbox" name="forbid-pwchange-flag" value="1" <?php
                    echo !$account->isPasswdResetEnabled() ?
                    'checked="checked"' : ''; ?>> <?php echo __('User Cannot Change Password'); ?></div>
                </td>
            </tr>
        </tbody>
        </table>
   </div>
   </div>
   <hr>
   <p class="full-width">
        <span class="buttons pull-left">
            <input type="reset" value="<?php echo __('Reset'); ?>">
            <input type="button" name="cancel" class="close" value="<?php echo __('Cancel'); ?>">
        </span>
        <span class="buttons pull-right">
            <input type="submit"
                value="<?php echo __('Save Changes'); ?>">
        </span>
    </p>
</form>
<div class="clear"></div>
<script type="text/javascript">
$(function() {
    $(document).on('click', 'input#sendemail', function(e) {
        if ($(this).prop('checked'))
            $('tbody#password').hide();
        else
            $('tbody#password').show();
    });
});
</script>
