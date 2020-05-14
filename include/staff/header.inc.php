<?php
header("Content-Type: text/html; charset=UTF-8");
if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html<?php
if (($lang = Internationalization::getCurrentLanguage())
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . Internationalization::rfc1766($lang) . '"';
}
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="x-pjax-version" content="<?php echo GIT_VERSION; ?>">
    <title><?php echo ($ost && ($title=$ost->getPageTitle()))?$title:'osTicket :: '.__('Staff Control Panel'); ?></title>
    <!--[if IE]>
    <style type="text/css">
        .tip_shadow { display:block !important; }
    </style>
    <![endif]-->
    
    <!-- <script src="//cdn.jsdelivr.net/jquery/2.1.4/jquery.min.js"></script> COMENTADO POR HDANDREA 02-03-18-->
    <script src="<?php echo ROOT_PATH ?>js/jquery_2_1_4.js"></script>
    
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/thread.css?231f11e" media="all"/>
    <link rel="stylesheet" href="./css/scp.css?231f11e" media="all"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?231f11e" media="screen"/>
    <link rel="stylesheet" href="./css/typeahead.css?231f11e" media="screen"/>
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?231f11e"
         rel="stylesheet" media="screen" />
     <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?231f11e"/>
     <script src="sweetalert2.min.js"></script>
<link rel="stylesheet" href="sweetalert2.min.css">
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php //echo ROOT_PATH; ?>css/font-awesome-ie7.min.css?231f11e"/>
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="./css/dropdown.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/loadingbar.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="./css/translatable.css?231f11e"/>
    
    <!-- agregado por hdandrea 06-03-18 Implementación jquery Data Table en el dashboard-->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/jquery.dataTables.min.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/buttons.dataTables.min.css"/>
    <!-- ------------------------------ -->

    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }
    ?>
	
	<!-- OSTA -->
	<?php include ROOT_DIR . 'awesome/inc/staff-head.html'; ?>
	<!-- /OSTA -->	
</head>
<body>
<div id="container">
    <?php
    if($ost->getError())
        echo sprintf('<div id="error_bar">%s</div>', $ost->getError());
    elseif($ost->getWarning())
        echo sprintf('<div id="warning_bar">%s</div>', $ost->getWarning());
    elseif($ost->getNotice())
        echo sprintf('<div id="notice_bar">%s</div>', $ost->getNotice());
    ?>
    <div id="header">	
		<div id="header-desktop">		
			<p id="info"><?php echo sprintf(__('Bienvenido, %s'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?>
			   <?php
				if($thisstaff->isAdmin() && !defined('ADMINPAGE')) { ?>
				&nbsp;&nbsp;  <a href="admin.php" class="no-pjax"><?php echo __('Admin Panel'); ?></a>
				<?php }else{ ?>
				&nbsp;&nbsp;  <a href="index.php" class="no-pjax"><?php echo __('Agent Panel'); ?></a>
				<?php } ?>
				&nbsp;&nbsp;  <a href="profile.php"><?php echo __('Profile'); ?></a>
				&nbsp;&nbsp;  <a href="logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax"><?php echo __('Log Out'); ?></a>
			</p>
			<div id="header-logo" class="no-pjax">
				<?php include ROOT_DIR . 'awesome/inc/awesome-logo.php'; ?>
			</div>
		</div>
		<div id="header-mobile">
			<div id="left-logo">
				<a id="header-logo-link" href="<?php echo ROOT_PATH; ?>scp/">
	<img src="header1.png" alt="mtsolutioncenter" style="width: 8rem;">
	<span class="header-logo-text"><?php echo __('  '); ?></span>
</a>			
			</div>
			<div id="right-buttons">
				<a class="mobile-nav-new-ticket" href="<?php echo ROOT_PATH; ?>scp/">
					<svg style="width:34px;height:34px; padding: 18px;float:right;margin-right:-14px;" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="a" gradientTransform="matrix(1 0 0 -1 0 -10886)" gradientUnits="userSpaceOnUse" x1="0" x2="512" y1="-11142" y2="-11142"><stop offset="0" stop-color="#00f1ff"/><stop offset=".231" stop-color="#00d8ff"/><stop offset=".5138" stop-color="#00c0ff"/><stop offset=".7773" stop-color="#00b2ff"/><stop offset="1" stop-color="#00adff"/></linearGradient>

<path d="m512 256c0 141.386719-114.613281 256-256 256s-256-114.613281-256-256 114.613281-256 256-256 256 114.613281 256 256zm0 0" fill="url(#a)"/><path d="m444.558594 222.78125-157.101563-158.136719c-.199219-.203125-.40625-.398437-.621093-.589843-17.125-15.375-43.003907-15.433594-60.191407-.125-.214843.1875-.421875.382812-.621093.585937l-158.535157 158.21875c-5.863281 5.851563-5.875 15.351563-.019531 21.214844 5.851562 5.863281 15.347656 5.871093 21.210938.019531l12.097656-12.074219v130.371094c0 33.210937 27.023437 60.230469 60.234375 60.230469h54.277343c8.285157 0 15-6.714844 15-15v-122.957032h52.929688v122.957032c0 8.285156 6.714844 15 15 15h52.769531c33.210938 0 60.234375-27.019532 60.234375-60.230469 0-8.285156-6.71875-15-15-15-8.285156 0-15 6.714844-15 15 0 16.667969-13.566406 30.230469-30.234375 30.230469h-37.769531v-122.957032c0-8.285156-6.714844-15-15-15h-82.929688c-8.28125 0-15 6.714844-15 15v122.957032h-39.277343c-16.671875 0-30.234375-13.5625-30.234375-30.230469v-159.824219c0-.15625-.015625-.308594-.019532-.464844l116.101563-115.875c5.675781-4.824218 14.007813-4.804687 19.667969.042969l114.695312 115.449219v85.285156c0 8.285156 6.714844 15 15 15 8.28125 0 15-6.714844 15-15v-55.085937l12.050782 12.132812c2.933593 2.949219 6.785156 4.425781 10.640624 4.425781 3.824219 0 7.648438-1.449218 10.574219-4.355468 5.875-5.839844 5.90625-15.339844.070313-21.214844zm0 0" fill="#fff"/></svg>
				</a>	
				<a class="mobile-nav-new-ticket" href="<?php echo ROOT_PATH; ?>scp/users.php">
					<svg style="width:34px;height:34px; padding: 18px;float:right;margin-right: -15px;" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="a" gradientTransform="matrix(1 0 0 -1 0 -19430)" gradientUnits="userSpaceOnUse" x1="0" x2="512" y1="-19686" y2="-19686"><stop offset="0" stop-color="#00f1ff"/><stop offset=".231" stop-color="#00d8ff"/><stop offset=".5138" stop-color="#00c0ff"/><stop offset=".7773" stop-color="#00b2ff"/><stop offset="1" stop-color="#00adff"/></linearGradient><path d="m512 256c0 141.386719-114.613281 256-256 256s-256-114.613281-256-256 114.613281-256 256-256 256 114.613281 256 256zm0 0" fill="url(#a)"/><path d="m432.082031 356.167969c-1.6875-8.109375-9.632812-13.3125-17.742187-11.628907-8.109375 1.6875-13.316406 9.632813-11.625 17.742188.808594 3.898438-.15625 7.894531-2.652344 10.964844-1.441406 1.773437-4.660156 4.75-9.996094 4.75h-268.132812c-5.335938 0-8.550782-2.976563-9.996094-4.75-2.496094-3.066406-3.460938-7.066406-2.648438-10.964844 14.132813-67.894531 74.046876-117.476562 143.214844-119.101562 1.160156.042968 2.324219.074218 3.496094.074218 1.101562 0 2.195312-.027344 3.285156-.066406 48.820313 1.066406 93.96875 25.730469 121.285156 66.46875 4.613282 6.878906 13.929688 8.71875 20.8125 4.105469 6.878907-4.613281 8.71875-13.933594 4.101563-20.8125-22.230469-33.160157-54.316406-57.4375-90.941406-70.03125 21.960937-17.34375 36.085937-44.199219 36.085937-74.292969 0-52.179688-42.449218-94.628906-94.628906-94.628906s-94.628906 42.449218-94.628906 94.628906c0 30.109375 14.140625 56.980469 36.121094 74.320312-20.148438 6.933594-39.058594 17.410157-55.679688 31.082032-31.613281 26.003906-53.597656 62.277344-61.894531 102.140625-2.660157 12.777343.527343 25.898437 8.742187 36.007812 8.179688 10.054688 20.308594 15.820313 33.273438 15.820313h268.132812c12.964844 0 25.09375-5.765625 33.269532-15.820313 8.21875-10.105469 11.40625-23.230469 8.746093-36.007812zm-240.710937-207.542969c0-35.636719 28.992187-64.628906 64.628906-64.628906s64.628906 28.992187 64.628906 64.628906c0 34.699219-27.492187 63.085938-61.832031 64.558594-.933594-.015625-1.863281-.042969-2.796875-.042969-1.050781 0-2.101562.015625-3.148438.03125-34.179687-1.648437-61.480468-29.96875-61.480468-64.546875zm0 0" fill="#fff"/></svg>
				</a>
				<a class="mobile-nav-new-ticket" href="<?php echo ROOT_PATH; ?>scp/tickets.php?a=open">
					<svg style="width:34px;height:34px; padding: 18px; display: block; margin-right:54px;" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="a" gradientTransform="matrix(1 0 0 -1 0 -16582)" gradientUnits="userSpaceOnUse" x1="0" x2="512" y1="-16838" y2="-16838"><stop offset="0" stop-color="#00f1ff"/><stop offset=".231" stop-color="#00d8ff"/><stop offset=".5138" stop-color="#00c0ff"/><stop offset=".7773" stop-color="#00b2ff"/><stop offset="1" stop-color="#00adff"/></linearGradient><path d="m512 256c0 141.386719-114.613281 256-256 256s-256-114.613281-256-256 114.613281-256 256-256 256 114.613281 256 256zm0 0" fill="url(#a)"/><path d="m441 240.980469h-170v-170c0-8.28125-6.714844-15-15-15s-15 6.71875-15 15v170h-170c-8.285156 0-15 6.71875-15 15 0 8.285156 6.714844 15 15 15h170v170c0 8.285156 6.714844 15 15 15s15-6.714844 15-15v-170h170c8.285156 0 15-6.714844 15-15 0-8.28125-6.714844-15-15-15zm0 0" fill="#fff"/></svg>
				</a>
			</div>	
			<div id="right-menu" href="#right-menu">
				<button href="#right-menu" class="c-hamburger c-hamburger--htx" style="">
					<span>toggle menu</span>
				</button>
				<script>
				$(document).ready(function() {
				  "use strict";
					var toggles = document.querySelectorAll(".c-hamburger");
					for (var i = toggles.length - 1; i >= 0; i--) {
					  var toggle = toggles[i];
					  toggleHandler(toggle);
					};
					function toggleHandler(toggle) {
					  toggle.addEventListener( "click", function(e) {
						e.preventDefault();
						(this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
					  });
					  toggle.addEventListener( "touchstart", function(e) {
						e.preventDefault();
						(this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
					  });	  
					}
				   $('.c-hamburger').sidr({
						name: 'sidr-right',
						side: 'right',
						body: '#content',
						displace: false
					});	
				});
				</script>
			</div>
		</div>
		<div id="sidr-right" class="sidr right">
			<?php include ROOT_DIR . 'awesome/inc/staff-mobile-menu.html'; ?>		
		</div>			

    </div><!-- END Header -->
	
    <div id="pjax-container" class="<?php if ($_POST) echo 'no-pjax'; ?>">
<?php } else {
    header('X-PJAX-Version: ' . GIT_VERSION);
    if ($pjax = $ost->getExtraPjax()) { ?>
    <script type="text/javascript">
    <?php foreach (array_filter($pjax) as $s) echo $s.";"; ?>
    </script>
    <?php }
    foreach ($ost->getExtraHeaders() as $h) {
        if (strpos($h, '<script ') !== false)
            echo $h;
    } ?>
    <title><?php echo ($ost && ($title=$ost->getPageTitle()))?$title:'osTicket :: '.__('Staff Control Panel'); ?></title><?php
} # endif X_PJAX ?>
    <ul id="nav">
		<?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>
    </ul>

    <div style="text-align: center ;background: white;">
    <ul id="sub_nav">
		<?php include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>
    </ul>
	</div>

	
    <div id="hidden-layer">
		&nbsp; <!-- this creates a hidden layer below the subnav to accomodate the overflow navigation links on admin pages -->
	</div>	
    <div id="content">
        <?php if($errors['err']) { ?>
		<div id="msg_error">
			<div id="alert-icon">
				<svg viewBox="0 0 24 24">
					<path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"></path>
				</svg>
			</div>
			<div id="alert-text">
				<?php echo $errors['err']; ?>		
			</div>		
		</div>
        <?php }elseif($msg) { ?>
		<div id="msg_notice">
			<div id="alert-icon">
				<svg viewBox="0 0 24 24">
					<path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16.5,8L11,13.5L7.5,10L8.91,8.59L11,10.67L15.09,6.59L16.5,8Z" /></path>
				</svg>
			</div>
			<div id="alert-text">
				<?php echo $msg; ?>
			</div>		
		</div>	
        <?php }elseif($warn) { ?>
		<div id="msg_warning">
			<div id="alert-icon">
				<svg viewBox="0 0 24 24">
					<path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z" />
				</svg>
			</div>
			<div id="alert-text">
				<?php echo $warn; ?>
			</div>		
		</div>
        <?php } ?>
