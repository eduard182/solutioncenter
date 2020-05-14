<?php
$info=($_POST && $errors)?Format::input($_POST):@Format::htmlchars($org->getInfo());

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
<ul class="tabs" id="orgprofile">
    <li class="active"><a href="#profile"
        ><i class="icon-edit"></i>&nbsp;<?php echo __('Fields'); ?></a></li>
    <li><a href="#contact-settings"
        ><i class="icon-fixed-width icon-cogs faded"></i>&nbsp;<?php
        echo __('Settings'); ?></a></li>
</ul>
<form method="post" class="org" action="<?php echo $action; ?>">
<div id="orgprofile_container">
<div class="tab_content" id="profile" style="margin:5px;">
<?php
$action = $info['action'] ? $info['action'] : ('#orgs/'.$org->getId());
if ($ticket && $ticket->getOwnerId() == $user->getId())
    $action = '#tickets/'.$ticket->getId().'/user';
?>
    <input type="hidden" name="id" value="<?php echo $org->getId(); ?>" />
    <table width="100%">
    <?php
        if (!$forms) $forms = $org->getForms();
        foreach ($forms as $form)
            $form->render();
    ?>
    </table>
</div>

<div class="hidden tab_content" id="contact-settings" style="margin:5px;">
    <table style="width:100%">
        <tbody>
            <tr>
                <td width="180">
                    <?php echo __('Account Manager'); ?>
                </td>
                <td>
                    <select name="manager">
                        <option value="0" selected="selected">&mdash; <?php
                            echo __('None'); ?> &mdash;</option><?php
                        if ($users=Staff::getAvailableStaffMembers()) { ?>
                            <optgroup label="<?php
                                echo sprintf(__('Agents (%d)'), count($users)); ?>">
<?php                       foreach($users as $id => $name) {
                                $k = "s$id";
                                echo sprintf('<option value="%s" %s>%s</option>',
                                    $k,(($info['manager']==$k)?'selected="selected"':''),$name);
                            }
                            echo '</optgroup>';
                        }

                        if ($teams=Team::getActiveTeams()) { ?>
                            <optgroup label="<?php echo sprintf(__('Teams (%d)'), count($teams)); ?>">
<?php                       foreach($teams as $id => $name) {
                                $k="t$id";
                                echo sprintf('<option value="%s" %s>%s</option>',
                                    $k,(($info['manager']==$k)?'selected="selected"':''),$name);
                            }
                            echo '</optgroup>';
                        } ?>
                    </select>
                    <br/><span class="error"><?php echo $errors['manager']; ?></span>
                </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Auto-Assignment'); ?>
                </td>
                <td>
                    <input type="checkbox" name="assign-am-flag" value="1" <?php echo $info['assign-am-flag']?'checked="checked"':''; ?>>
                    <?php echo __(
                    'Assign tickets from this organization to the <em>Account Manager</em>'); ?>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Primary Contacts'); ?>
                </td>
                <td>
                    <select name="contacts[]" id="primary_contacts" multiple="multiple"
                        data-placeholder="<?php echo __('Select Contacts'); ?>">
                        <option value=""></option>
<?php               foreach ($org->allMembers() as $u) { ?>
                        <option value="<?php echo $u->id; ?>" <?php
                            if ($u->isPrimaryContact())
                            echo 'selected="selected"'; ?>><?php echo $u->getName(); ?></option>
<?php               } ?>
                    </select>
                    <br/><span class="error"><?php echo $errors['contacts']; ?></span>
                </td>
            <tr>
                <th colspan="2">
                    <?php echo __('Automated Collaboration'); ?>
                </th>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Primary Contacts'); ?>
                </td>
                <td>
                    <input type="checkbox" name="collab-pc-flag" value="1" <?php echo $info['collab-pc-flag']?'checked="checked"':''; ?>>
                    <?php echo __('Add to all tickets from this organization'); ?>
                </td>
            </tr>
            <tr>
                <td width="180">
                    <?php echo __('Organization Members'); ?>
                </td>
                <td>
                    <input type="checkbox" name="collab-all-flag" value="1" <?php echo $info['collab-all-flag']?'checked="checked"':''; ?>>
                    <?php echo __('Add to all tickets from this organization'); ?>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <?php echo __('Main Domain'); ?>
                </th>
            </tr>
            <tr>
                <td style="width:180px">
                    <?php echo __('Auto Add Members From'); ?>
                </td>
                <td>
                    <input type="text" size="40" maxlength="60" name="domain"
                        value="<?php echo $info['domain']; ?>" />
                    <br/><span class="error"><?php echo $errors['domain']; ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</div>
<div class="clear"></div>

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
    $("#primary_contacts").select2({width: '300px'});
});
</script>
