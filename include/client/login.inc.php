<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['luser']?:$_GET['e']);
$passwd=Format::input($_POST['lpasswd']?:$_GET['t']);

$content = Page::lookupByType('banner-client');

if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getName(), $content->getBody()));
} else {
    $title = __('Sign In');
    $body = __('To better serve you, we encourage our clients to register for an account and verify the email address we have on record.');
}

?>
<h1><?php echo Format::display($title); ?></h1>
<p><?php echo Format::display($body); ?></p>


<form action="login.php" method="post" id="client-login-form">
    <?php csrf_token(); ?>
<div id="login-content">
	<div id="client-login">
		<h3>Login</h3>
		<div id="client-login-inner">
			<strong><?php echo Format::htmlchars($errors['login']); ?></strong>
			<input id="username" placeholder="<?php echo __('Email or Username'); ?>" type="text" name="luser" size="30" value="<?php echo $email; ?>" class="nowarn"><br>
			<input id="passwd" placeholder="<?php echo __('Password'); ?>" type="password" name="lpasswd" size="30" value="<?php echo $passwd; ?>" class="nowarn"><br>
			<input id="sign-in" type="submit" value="<?php echo __('Sign In'); ?>"><br></form>
			
			<?php if ($suggest_pwreset) { ?>
			<a style="padding-top:4px;display:inline-block;" href="pwreset.php"><?php echo __('Forgot My Password'); ?></a>
			<?php } ?>	
		</div>
	</div>
	<div id="client-options">
		<?php
		$ext_bks = array();
		foreach (UserAuthenticationBackend::allRegistered() as $bk)
			if ($bk instanceof ExternalAuthentication)
				$ext_bks[] = $bk;

		if (count($ext_bks)) {
			foreach ($ext_bks as $bk) { ?>
		<div class="external-auth">
			<?php $bk->renderExternalLink(); ?>
		</div><?php
			}
		}
		if ($cfg && $cfg->isClientRegistrationEnabled()) {
		if (count($ext_bks)) echo '<hr style="width:auto"/>'; ?>
		<div style="margin-bottom: 15px">
		</div>
		<?php } ?>
		<div style="margin-bottom: 15px">
			<?php
			if ($cfg->getClientRegistrationMode() != 'disabled'
				|| !$cfg->isClientLoginRequired()) {
				
			} ?>
		</div>		
		
    </div>
</div>
<div class="clear"></div>
			