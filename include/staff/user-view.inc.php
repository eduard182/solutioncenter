<?php
if(!defined('OSTSCPINC') || !$thisstaff || !is_object($user)) die('Invalid path');

$account = $user->getAccount();
$org = $user->getOrganization();

?>

<div id="page-top">
	<div id="page-top-title" class="user-view-user-name">
		 <h2>
			 <a href="users.php?id=<?php echo $user->getId(); ?>"
			 title="Reload"><?php echo Format::htmlchars($user->getFullName()); ?> 
				<svg viewBox="0 0 24 24">
					<path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z" />
				</svg>			 
			 </a>
		 </h2>
	</div>
	<div id="page-top-buttons" class="user-view-buttons">
	<?php if (($account && $account->isConfirmed())
		|| $thisstaff->hasPerm(User::PERM_EDIT)) { ?>
				
	<a id="user-more" class="action-button user-more gray" data-dropdown="#action-dropdown-more">
		<div class="">
			<div class="button-icon">
				<svg viewBox="0 0 24 24">
					<path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z" />
				</svg>
			</div>
			<div class="button-text">
				<?php echo __('More'); ?>
			</div>
			<div id="button-more-caret">
				<div class="caret">
					<i class="material-icons more">expand_more</i>
				</div>
			</div>	
		</div>	
	</a>		
				
	<?php }
		if ($thisstaff->hasPerm(User::PERM_DELETE)) { ?>
				
				<a id="user-delete" class="user-action user-delete" href="#users/<?php echo $user->getId(); ?>/delete">
					<div class="action-button user-delete gray hover-red">
						<div class="button-icon">
							<svg viewBox="0 0 24 24">
								<path d="M12,4A4,4 0 0,1 16,8C16,9.95 14.6,11.58 12.75,11.93L8.07,7.25C8.42,5.4 10.05,4 12,4M12.28,14L18.28,20L20,21.72L18.73,23L15.73,20H4V18C4,16.16 6.5,14.61 9.87,14.14L2.78,7.05L4.05,5.78L12.28,14M20,18V19.18L15.14,14.32C18,14.93 20,16.35 20,18Z" />
							</svg>
						</div>
						<div class="button-text user-lookup">
							<?php echo __('Delete User'); ?></a>
						</div>
						<div class="button-spacing">
							&nbsp;
						</div>
					</div>
				</a>
				
	<?php } ?>
	<?php if ($thisstaff->hasPerm(User::PERM_MANAGE)) { ?>
				<?php
				if ($account) { ?>
				
				<a id="user-manage" class="user-action user-delete" href="#users/<?php echo $user->getId(); ?>/manage">
					<div class="action-button user-delete gray">
						<div class="button-icon">
							<svg viewBox="0 0 24 24">
								<path d="M12,19.2C9.5,19.2 7.29,17.92 6,16C6.03,14 10,12.9 12,12.9C14,12.9 17.97,14 18,16C16.71,17.92 14.5,19.2 12,19.2M12,5A3,3 0 0,1 15,8A3,3 0 0,1 12,11A3,3 0 0,1 9,8A3,3 0 0,1 12,5M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2Z" />
							</svg>					</div>
						<div class="button-text user-lookup">
							<?php echo __('Manage Account'); ?></a>
						</div>
						<div class="button-spacing">
							&nbsp;
						</div>
					</div>
				</a>			
				
				<?php
				} else { ?>

				<a id="user-register" class="user-action user-register" href="#users/<?php echo $user->getId(); ?>/register">
					<div class="action-button user-register blue2">
						<div class="button-icon">
							<svg viewBox="0 0 24 24">
								<path d="M11,10V12H10V14H8V12H5.83C5.42,13.17 4.31,14 3,14A3,3 0 0,1 0,11A3,3 0 0,1 3,8C4.31,8 5.42,8.83 5.83,10H11M3,10A1,1 0 0,0 2,11A1,1 0 0,0 3,12A1,1 0 0,0 4,11A1,1 0 0,0 3,10M16,14C18.67,14 24,15.34 24,18V20H8V18C8,15.34 13.33,14 16,14M16,12A4,4 0 0,1 12,8A4,4 0 0,1 16,4A4,4 0 0,1 20,8A4,4 0 0,1 16,12Z" />
							</svg>
						</div>
						<div class="button-text user-lookup">
							<?php echo __('Register'); ?></a>
						</div>
						<div class="button-spacing">
							&nbsp;
						</div>
					</div>
				</a>				
				<?php
				} ?>
	<?php } ?>
           <div id="action-dropdown-more" class="action-dropdown anchor-right">
              <ul>
                <?php
                if ($account) {
                    if (!$account->isConfirmed()) {
                        ?>
                    <li><a class="confirm-action" href="#confirmlink"><i
                        class="icon-envelope"></i>
                        <?php echo __('Send Activation Email'); ?></a></li>
                    <?php
                    } else { ?>
                    <li><a class="confirm-action" href="#pwreset"><i
                        class="icon-envelope"></i>
                        <?php echo __('Send Password Reset Email'); ?></a></li>
                    <?php
                    } ?>
<?php if ($thisstaff->hasPerm(User::PERM_MANAGE)) { ?>
                    <li><a class="user-action"
                        href="#users/<?php echo $user->getId(); ?>/manage/access"><i
                        class="icon-lock"></i>
                        <?php echo __('Manage Account Access'); ?></a></li>
                <?php
}
                } ?>
<?php if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
                <li><a href="#ajax.php/users/<?php echo $user->getId();
                    ?>/forms/manage" onclick="javascript:
                    $.dialog($(this).attr('href').substr(1), 201);
                    return false"
                    ><i class="icon-paste"></i>
                    <?php echo __('Manage Forms'); ?></a></li>
<?php } ?>

              </ul>
            </div>
			
			
	</div>
</div>
<div style="clear: both;"></div>


<div class="avatar pull-left user-view-avatar">
    <?php echo $user->getAvatar(); ?>
</div>


<div class="responsive-div ticket_info user-view">
	<div id="one">
		<table border="0" cellspacing="" cellpadding="4">
			<tr>
				<th width="10"><?php echo __('Name'); ?></th>
				<td>
	<?php
	if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
				<b><a href="#users/<?php echo $user->getId();
				?>/edit" class="user-action link">
					<svg viewBox="0 0 24 24">
						<path d="M6,17C6,15 10,13.9 12,13.9C14,13.9 18,15 18,17V18H6M15,9A3,3 0 0,1 12,12A3,3 0 0,1 9,9A3,3 0 0,1 12,6A3,3 0 0,1 15,9M3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3H5C3.89,3 3,3.9 3,5Z" />
					</svg>
	<?php }
				echo Format::htmlchars($user->getName()->getOriginal());
	if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
					</a></b>
	<?php } ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Email'); ?></th>
				<td>
					<span id="user-<?php echo $user->getId(); ?>-email"><?php echo $user->getEmail(); ?></span>
				</td>
			</tr>
			<tr>
				<th><?php echo __('Organization'); ?></th>
				<td>
					<span id="user-<?php echo $user->getId(); ?>-org">
					<?php
						if ($org)
							echo sprintf('<a href="#users/%d/org" class="user-action">%s</a>',
									$user->getId(), $org->getName());
						elseif ($thisstaff->hasPerm(User::PERM_EDIT)) {
							echo sprintf(
								'<a href="#users/%d/org" class="user-action">+&nbsp;%s</a>',
								$user->getId(),
								__('Add Organization'));
						}
					?>
					</span>
				</td>
			</tr>
		</table>
	</div>			
	<div id="two">	
		<table border="0" cellspacing="" cellpadding="4">
			<tr>
				<th width="100"><?php echo __('Status'); ?></th>
				<td> <span id="user-<?php echo $user->getId();
				?>-status"><?php echo $user->getAccountStatus(); ?></span></td>
			</tr>
			<tr>
				<th><?php echo __('Created'); ?></th>
				<td><?php echo Format::datetime($user->getCreateDate()); ?></td>
			</tr>
			<tr>
				<th><?php echo __('Updated'); ?></th>
				<td><?php echo Format::datetime($user->getUpdateDate()); ?></td>
			</tr>
		</table>
	</div>
</div>	

<br>
<div class="clear"></div>
<ul class="clean tabs" id="user-view-tabs">
    <li class="active"><a href="#tickets"><i
    class="icon-list-alt"></i>&nbsp;<?php echo __('User Tickets'); ?></a></li>
    <li><a href="#notes"><i
    class="icon-pushpin"></i>&nbsp;<?php echo __('Notes'); ?></a></li>
</ul>
<div id="user-view-tabs_container">
    <div id="tickets" class="tab_content">
    <?php
    include STAFFINC_DIR . 'templates/tickets.tmpl.php';
    ?>
    </div>

    <div class="hidden tab_content" id="notes">
    <?php
    $notes = QuickNote::forUser($user);
    $create_note_url = 'users/'.$user->getId().'/note';
    include STAFFINC_DIR . 'templates/notes.tmpl.php';
    ?>
    </div>
</div>
<div class="hidden dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm'); ?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="banemail-confirm">
        <?php echo sprintf(__('Are you sure you want to <b>ban</b> %s?'), $user->getEmail()); ?>
        <br><br>
        <?php echo __('New tickets from the email address will be auto-rejected.'); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="confirmlink-confirm">
        <?php echo sprintf(__(
        'Are you sure you want to send an <b>Account Activation Link</b> to <em> %s </em>?'),
        $user->getEmail()); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="pwreset-confirm">
        <?php echo sprintf(__(
        'Are you sure you want to send a <b>Password Reset Link</b> to <em> %s </em>?'),
        $user->getEmail()); ?>
    </p>
    <div><?php echo __('&nbsp;'); ?></div>
    <form action="users.php?id=<?php echo $user->getId(); ?>" method="post" id="confirm-form" name="confirm-form">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo $user->getId(); ?>">
        <input type="hidden" name="a" value="process">
        <input type="hidden" name="do" id="action" value="">
        <hr style="margin-top:1em"/>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="button" value="<?php echo __('Cancel'); ?>" class="close">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?php echo __('OK'); ?>">
            </span>
         </p>
    </form>
    <div class="clear"></div>
</div>

<script type="text/javascript">
$(function() {
    $(document).on('click', 'a.user-action', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.dialog(url, [201, 204], function (xhr) {
            if (xhr.status == 204)
                window.location.href = 'users.php';
            else
                window.location.href = window.location.href;
            return false;
         }, {
            onshow: function() { $('#user-search').focus(); }
         });
        return false;
    });
});
</script>
