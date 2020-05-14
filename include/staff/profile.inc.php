<?php
if(!defined('OSTSTAFFINC') || !$staff || !$thisstaff) die('Access Denied');
?>

<form action="profile.php" method="post" id="save" autocomplete="off">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="update">
 <input type="hidden" name="id" value="<?php echo $staff->getId(); ?>">
<h2><?php echo __('My Account Profile');?></h2>
  <ul class="clean tabs">
    <li class="active"><a href="#account"><i class="material-icons">account_box</i><?php echo __('Account'); ?></a></li>
    <li><a href="#preferences"><?php echo __('Preferences'); ?></a></li>
    <li><a href="#signature"><?php echo __('Signature'); ?></a></li>
  </ul>

  <div class="tab_content" id="account">
    <table class="table two-column" width="100%" border="0" cellspacing="0" cellpadding="0">
      <tbody>
        <tr><td colspan="2"><div>
        <div class="avatar pull-left" style="margin: 10px 15px; width: 100px; height: 100px;">
<?php       $avatar = $staff->getAvatar();
            echo $avatar;
if ($avatar->isChangeable()) { ?>
          <div style="text-align: center">
            <a class="button no-pjax"
                href="#ajax.php/staff/<?php echo $staff->getId(); ?>/avatar/change"
                onclick="javascript:
    event.preventDefault();
    var $a = $(this),
        form = $a.closest('form');
    $.ajax({
      url: $a.attr('href').substr(1),
      dataType: 'json',
      success: function(json) {
        if (!json || !json.code)
          return;
        var code = form.find('[name=avatar_code]');
        if (!code.length)
          code = form.append($('<input>').attr({type: 'hidden', name: 'avatar_code'}));
        code.val(json.code).trigger('change');
        $a.closest('.avatar').find('img').replaceWith($(json.img));
      }
    });
    return false;"><svg style="width:18px;height:18px;padding:0 2px 0 0;" viewBox="0 0 24 24"><path fill="#fff" d="M16,9C18.33,9 23,10.17 23,12.5V15H17V12.5C17,11 16.19,9.89 15.04,9.05L16,9M8,9C10.33,9 15,10.17 15,12.5V15H1V12.5C1,10.17 5.67,9 8,9M8,7A3,3 0 0,1 5,4A3,3 0 0,1 8,1A3,3 0 0,1 11,4A3,3 0 0,1 8,7M16,7A3,3 0 0,1 13,4A3,3 0 0,1 16,1A3,3 0 0,1 19,4A3,3 0 0,1 16,7M9,16.75V19H15V16.75L18.25,20L15,23.25V21H9V23.25L5.75,20L9,16.75Z" /></svg><?php echo __('Change'); ?></a>
	
          </div>
<?php
} ?>
        </div>
        <table class="table two-column" border="0" cellspacing="2" cellpadding="0" style="width:760px">
        <tr>
          <td class="required"><?php echo __('Name'); ?></td>
          <td>
            <input type="text" size="20" maxlength="64" style="width: 145px" name="firstname"
              autofocus value="<?php echo Format::htmlchars($staff->firstname); ?>"
              placeholder="<?php echo __("First Name"); ?>" />
            <input type="text" size="20" maxlength="64" style="width: 145px" name="lastname"
              value="<?php echo Format::htmlchars($staff->lastname); ?>"
              placeholder="<?php echo __("Last Name"); ?>" />
            <div class="error"><?php echo $errors['firstname']; ?></div>
            <div class="error"><?php echo $errors['lastname']; ?></div>
          </td>
        </tr>
        <tr>
          <td class="required"><?php echo __('Email Address'); ?></td>
          <td>
            <input type="email" size="40" maxlength="64" style="width: 300px" name="email"
              value="<?php echo Format::htmlchars($staff->email); ?>"
              placeholder="<?php echo __('e.g. me@mycompany.com'); ?>" />
            <div class="error"><?php echo $errors['email']; ?></div>
          </td>
        </tr>
        <tr>
          <td><?php echo __('Phone Number');?></td>
          <td>
            <input type="tel" size="18" name="phone" class="auto phone"
              value="<?php echo Format::htmlchars($staff->phone); ?>" />
            <?php echo __('Ext');?>
            <input type="text" size="5" name="phone_ext"
              value="<?php echo Format::htmlchars($staff->phone_ext); ?>">
            <div class="error"><?php echo $errors['phone']; ?></div>
            <div class="error"><?php echo $errors['phone_ext']; ?></div>
          </td>
        </tr>
        <tr>
          <td><?php echo __('Mobile Number');?></td>
          <td>
            <input type="tel" size="18" name="mobile" class="auto phone"
              value="<?php echo Format::htmlchars($staff->mobile); ?>" />
            <div class="error"><?php echo $errors['mobile']; ?></div>
          </td>
        </tr>
        </table></div></td></tr>
      </tbody>
      <!-- ================================================ -->
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?php echo __('Authentication'); ?>
          </th>
        </tr>
        <?php if ($bk = $staff->getAuthBackend()) { ?>
        <tr>
          <td><?php echo __("Backend"); ?></td>
          <td><?php echo $bk->getName(); ?></td>
        </tr>
        <?php } ?>
        <tr>
          <td class="required"><?php echo __('Username'); ?></td>
          <td>
            <input type="text" size="40" style="width:300px"
              class="staff-username typeahead"
              name="username" disabled value="<?php echo Format::htmlchars($staff->username); ?>" /><font class="error-asterisk">*</font>
<?php if (!$bk || $bk->supportsPasswordChange()) { ?>
            <button type="button" id="change-pw-button" class="action-button blue" onclick="javascript:
            $.dialog('ajax.php/staff/'+<?php echo $staff->getId(); ?>+'/change-password', 201);">
				<svg viewBox="0 0 24 24">
					<path d="M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V10A2,2 0 0,1 6,8H15V6A3,3 0 0,0 12,3A3,3 0 0,0 9,6H7A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,17A2,2 0 0,0 14,15A2,2 0 0,0 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17Z" />
				</svg>            
			  <?php echo __('Change Password'); ?>
            </button>
<?php } ?>
            <i class="offset help-tip icon-question-sign" href="#username"></i>
            <div class="error"><?php echo $errors['username']; ?></div>
          </td>
        </tr>
      </tbody>
      <!-- ================================================ -->
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?php echo __('Status and Settings'); ?>
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <label class="checkbox">
            <input type="checkbox" name="show_assigned_tickets"
              <?php echo $cfg->showAssignedTickets() ? 'disabled="disabled" ' : ''; ?>
              <?php echo $staff->show_assigned_tickets ? 'checked="checked"' : ''; ?> />
              <?php echo __('Show assigned tickets on open queue.'); ?>
            <i class="help-tip icon-question-sign" href="#show_assigned_tickets"></i>
            </label>
            <label class="checkbox">
            <input type="checkbox" name="onvacation"
              <?php echo ($staff->onvacation) ? 'checked="checked"' : ''; ?> />
              <?php echo __('Vacation Mode'); ?>
            </label>
            <br/>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- =================== PREFERENCES ======================== -->

  <div class="hidden tab_content" id="preferences">
    <table class="table two-column" width="100%">
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?php echo __('Preferences'); ?>
            <div><small><?php echo __(
            "Profile preferences and settings"
          ); ?>
            </small></div>
          </th>
        </tr>
        <tr>
            <td width="180"><?php echo __('Maximum Page size');?></td>
            <td>
                <select name="max_page_size">
                    <option value="0">&mdash; <?php echo __('system default');?> &mdash;</option>
                    <?php
                    $pagelimit = $staff->max_page_size ?: $cfg->getPageSize();
                    for ($i = 5; $i <= 50; $i += 5) {
                        $sel=($pagelimit==$i)?'selected="selected"':'';
                         echo sprintf('<option value="%d" %s>'.__('show %s records').'</option>',$i,$sel,$i);
                    } ?>
                </select> <?php echo __('per page.');?>
            </td>
        </tr>
        <tr>
            <td width="180"><?php echo __('Auto Refresh Rate');?>
              <div class="faded"><?php echo __('Tickets page refresh rate in minutes.'); ?></div>
            </td>
            <td>
                <select name="auto_refresh_rate">
                  <option value="0">&mdash; <?php echo __('disable');?> &mdash;</option>
                  <?php
                  $y=1;
                   for($i=1; $i <=30; $i+=$y) {
                     $sel=($staff->auto_refresh_rate==$i)?'selected="selected"':'';
                     echo sprintf('<option value="%d" %s>%s</option>', $i, $sel,
                        sprintf(_N('Every minute', 'Every %d minutes', $i), $i));
                     if($i>9)
                        $y=2;
                   } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo __('Default Signature');?>
              <div class="faded"><?php echo __('This can be selected when replying to a ticket');?></div>
            </td>
            <td>
                <select name="default_signature_type">
                  <option value="none" selected="selected">&mdash; <?php echo __('None');?> &mdash;</option>
                  <?php
                   $options=array('mine'=>__('My Signature'),'dept'=>sprintf(__('Department Signature (%s)'),
                       __('if set' /* This is used in 'Department Signature (>if set<)' */)));
                  foreach($options as $k=>$v) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $k,($staff->default_signature_type==$k)?'selected="selected"':'',$v);
                  }
                  ?>
                </select>
                <div class="error"><?php echo $errors['default_signature_type']; ?></div>
            </td>
        </tr>
        <tr>
            <td width="180"><?php echo __('Default Paper Size');?>
              <div class="faded"><?php echo __('Paper size used when printing tickets to PDF');?></div>
            </td>
            <td>
                <select name="default_paper_size">
                  <option value="none" selected="selected">&mdash; <?php echo __('None');?> &mdash;</option>
                  <?php

                  foreach(Export::$paper_sizes as $v) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $v,($staff->default_paper_size==$v)?'selected="selected"':'',__($v));
                  }
                  ?>
                </select>
                <div class="error"><?php echo $errors['default_paper_size']; ?></div>
            </td>
        </tr>
      </tbody>
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?php echo __('Localization'); ?>
          </th>
        </tr>
        <tr>
            <td><?php echo __('Time Zone');?></td>
            <td>
                <?php
                $TZ_NAME = 'timezone';
                $TZ_TIMEZONE = $staff->timezone;
                include STAFFINC_DIR.'templates/timezone.tmpl.php'; ?>
                <div class="error"><?php echo $errors['timezone']; ?></div>
            </td>
        </tr>
<?php if ($cfg->getSecondaryLanguages()) { ?>
        <tr>
            <td><?php echo __('Preferred Language'); ?></td>
            <td>
        <?php
        $langs = Internationalization::getConfiguredSystemLanguages(); ?>
                <select name="lang">
                    <option value="">&mdash; <?php echo __('Use Browser Preference'); ?> &mdash;</option>
<?php foreach($langs as $l) {
    $selected = ($staff->lang == $l['code']) ? 'selected="selected"' : ''; ?>
                    <option value="<?php echo $l['code']; ?>" <?php echo $selected;
                        ?>><?php echo Internationalization::getLanguageDescription($l['code']); ?></option>
<?php } ?>
                </select>
                <span class="error">&nbsp;<?php echo $errors['lang']; ?></span>
            </td>
        </tr>
<?php } ?>
<?php if (extension_loaded('intl')) { ?>
        <tr>
            <td><?php echo __('Preferred Locale');?></td>
            <td>
                <select name="locale">
                    <option value=""><?php echo __('Use Language Preference'); ?></option>
<?php foreach (Internationalization::allLocales() as $code=>$name) { ?>
                    <option value="<?php echo $code; ?>" <?php
                        if ($code == $staff->locale)
                            echo 'selected="selected"';
                    ?>><?php echo $name; ?></option>
<?php } ?>
                </select>
            </td>
        </tr>
<?php } ?>
    </table>
  </div>

  <!-- ==================== SIGNATURES ======================== -->

  <div id="signature" class="hidden">
    <table class="table two-column" width="100%">
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?php echo __('Signature'); ?>
            <div><small><?php echo __(
            "Optional signature used on outgoing emails.")
            .' '.
            __('Signature is made available as a choice, on ticket reply.'); ?>
            </small></div>
          </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext no-bar" name="signature" cols="21"
                    rows="5" style="width: 60%;"><?php echo $staff->signature; ?></textarea>
            </td>
        </tr>
      </tbody>
    </table>
  </div>

  <p style="text-align:center;margin-top:20px;">
    <button class="button action-button right red" type="submit" name="submit" ><?php echo __('Save Changes'); ?></button>
    <button class="button action-button left gray" type="reset"  name="reset"></i>
        <?php echo __('Reset');?></button>
    <button class="button action-button left gray" type="button" name="cancel" onclick="window.history.go(-1);"><?php echo __('Cancel');?></button>
  </p>


    <div class="clear"></div>
</form>
<?php
if ($staff->change_passwd) { ?>
<script type="text/javascript">
    $(function() { $('#change-pw-button').trigger('click'); });
</script>
<?php
}
