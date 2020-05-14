<?php
global $cfg;

if (!$info['title'])
    $info['title'] = sprintf(__('Register: %s'), Format::htmlchars($user->getName()));

if (!$_POST) {
    $info['sendemail'] = true; // send email confirmation.
}

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
<div id="msg_notice"><div id="alert-icon"><svg viewBox="0 0 24 24"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" /></svg></div><div id="alert-text"><?php echo sprintf(__('Complete the form below to create a user account for <b>%s</b>.'), Format::htmlchars($user->getName()->getOriginal())); ?></div></div>
<div id="user-registration" style="display:block; margin:5px;">
    <form method="post" class="user"
        action="#users/<?php echo $user->getId(); ?>/register">
        <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
        <table id="user-registration-table" width="100%">
        <tbody>
            <tr>
                <th colspan="2">
                    <em><strong><?php echo __('User Account Login'); ?></strong></em>
                </th>
            </tr>
            <tr>
                <td><?php echo __('Authentication Sources'); ?></td>
                <td>
            <select name="backend" id="backend-selection" onchange="javascript:
                if (this.value != '' && this.value != 'client') {
                    $('#activation').hide();
                    $('#password').hide();
                }
                else {
                    $('#activation').show();
                    if ($('#sendemail').is(':checked'))
                        $('#password').hide();
                    else
                        $('#password').show();
                }
                ">
                <option value="">&mdash; <?php echo __('Use any available backend'); ?> &mdash;</option>
            <?php foreach (UserAuthenticationBackend::allRegistered() as $ab) {
                if (!$ab->supportsInteractiveAuthentication()) continue; ?>
                <option value="<?php echo $ab::$id; ?>" <?php
                    if ($info['backend'] == $ab::$id)
                        echo 'selected="selected"'; ?>><?php
                    echo $ab->getName(); ?></option>
            <?php } ?>
            </select>
                </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Username'); ?>
                </td>
                <td>
                    <input type="text" size="35" name="username" value="<?php echo $info['username'] ?: $user->getEmail(); ?>">
                    &nbsp;<span class="error">&nbsp;<?php echo $errors['username']; ?></span>
                </td>
            </tr>
        </tbody>
        <tbody id="activation">
            <tr>
                <td width="180">
                    <?php echo __('Status'); ?>
                </td>
                <td>
				


				
					<div id="sendemail">			
                  <input type="checkbox" id="sendemail" name="sendemail" value="1"
                    <?php echo $info['sendemail'] ? 'checked="checked"' :
                    ''; ?> ><?php echo sprintf(__(
                    'Send account activation email to %s.'), $user->getEmail()); ?>
							</div>			
					
                </td>
            </tr>
        </tbody>
        <tbody id="password"
            style="<?php echo $info['sendemail'] ? 'display:none;' : ''; ?>"
            >
            <tr>
                <td width="180">
                    <?php echo __('Temporary Password'); ?>
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
            <tr>
                <td>
                    <?php echo __('Password Change'); ?>
                </td>
                <td colspan=2>
                    <input type="checkbox" name="pwreset-flag" value="1" <?php
                        echo $info['pwreset-flag'] ?  'checked="checked"' : ''; ?>>
                        <?php echo __('Require password change on login'); ?>
                    <br/>
                    <input type="checkbox" name="forbid-pwreset-flag" value="1" <?php
                        echo $info['forbid-pwreset-flag'] ?  'checked="checked"' : ''; ?>>
                        <?php echo __('User cannot change password'); ?>
                </td>
            </tr>
        </tbody>
        <tbody id="timezone">
            <tr>
                <th colspan="2"><em><strong><?php echo
                    __('User Preferences'); ?></strong></em></th>
            </tr>
                <td><?php echo __('Time Zone'); ?></td>
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
        <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="reset" value="<?php echo __('Reset'); ?>">
                <input type="button" name="cancel" class="close" value="<?php echo __('Cancel'); ?>">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?php echo __('Create Account'); ?>">
            </span>
         </p>
    </form>
</div>
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
