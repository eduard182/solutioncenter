<?php

$signin_url = ROOT_PATH . "login.php"
    . ($thisclient ? "?e=".urlencode($thisclient->getEmail()) : "");
$signout_url = ROOT_PATH . "logout.php?auth=".$ost->getLinkToken();

header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html<?php
if (($lang = Internationalization::getCurrentLanguage())
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
    $langs = Internationalization::rfc1766($langs);
    echo ' lang="' . $lang . '"';
    header("Content-Language: ".implode(', ', $langs));
}
?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Format::htmlchars($title); ?></title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	
	<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css?231f11e" media="screen"/>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/theme.css?231f11e" media="screen"/>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/print.css?231f11e" media="print"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>scp/css/typeahead.css?231f11e"
         media="screen" />
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?231f11e"
        rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css?231f11e" media="screen"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?231f11e" media="screen"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?231f11e"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?231f11e"/>
    
    
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/jquery-confirm.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/dataTables.checkboxes.css">
    
    
    <!--
    <script src="//cdn.jsdelivr.net/jquery/2.1.4/jquery.min.js"></script>  comentado por hdandrea 29-03-18-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery_2_1_4.js"></script>
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.10.3.custom.min.js?231f11e"></script>
    <script src="<?php echo ROOT_PATH; ?>js/osticket.js?231f11e"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js?231f11e"></script>
    <script src="<?php echo ROOT_PATH; ?>scp/js/bootstrap-typeahead.js?231f11e"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js?231f11e"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js?231f11e"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js?231f11e"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js?231f11e"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/fabric.min.js?231f11e"></script>
    
    
    <!-- agregado por hdandrea 21-03-18 Implementación jquery Data Table  inicio -->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/jquery.dataTables.min.css"/> 
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/buttons.dataTables.min.css"/>
    <!-- ---------------------------------------------------------------   fin  -->
    
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-confirm.min.js"></script>
    

<style>
	
/* ==========================================================================
 TABLES
========================================================================== */

.table tr.header {
	border-bottom: inherit;
}
.table tr:not(:last-child):not(.header) {
	border-bottom: inherit;
}
table.list thead th:first-child {
	border-left: 1px solid #dcdcdc!important;
	border-radius: 6px 0 0 0;
}
table.list thead th:last-child {
	border-right: 1px solid #dcdcdc!important;
	border-radius: 0 6px 0 0;
}
table.list tbody tr td:first-child {
	border-left: 1px solid #dcdcdc!important;
}
table.list tbody tr td:last-child {
	border-right: 1px solid #dcdcdc!important;
}
table.list {
	clear: both;
	background: none;
	margin: 8px 0 10px 0;
	border-bottom: 0px solid #F8F8F8;
	font-family: verdana, arial, helvetica, sans-serif;
	border-radius: 3px 3px 3px 3px;
	width: 100%;
}
table.list thead th {
    background-color: #fafafa;
    text-align: left;
    vertical-align: top;
    padding: 5px 5px 5px 5px;
    border: 1px solid #DCDCDC;
    border-right: 0px;
    border-left: 0px;
    padding-right: 0px;
    padding-top: 6px;
    white-space: nowrap;
    color: #184E81;
    height: 20px;
    font-family: roboto;
    font-size: 13px;
    line-height: 20px;
}
table.list thead th a {
	padding-right: 0px;
	padding-top: 6px;
	display: block;
	white-space: nowrap;
	color: #184E81;
	height: 20px;
	font-family: roboto;
	font-size: 13px;
	line-height: 20px;
}
table.list tbody td {
	color: #686868;
	background: #fff;
	padding: 8px 4px 8px 0px;
	padding-left: 2px;
	vertical-align: middle;
    border: initial;	
}
th.head-priority,
th.head-priority a {
	padding: 0px;
	margin: 0px;
	background: #000;
	width: 0px!important;
	margin: -6px -8px;
}
th.head-priority {
	border-radius: 6px 0 0 0;
	border-left: 1px solid #DCDCDC!important;
}
th.head-priority svg {
    margin: 0 0px 4px -11px;
	width: 15px;
	fill: currentColor;
	padding: 0 0 0 4px;	
	position: relative;
    top: 1px;	
}
th.head-checkbox {
	padding: 5px 0 0 9px!important;
}
th.head-checkbox svg {
	width: 17px;
	position: relative;
	top: 2px;
	fill: #184E81;
	position: relative;
	top: 2px;	
}
td.table-date {
	font-size: 11px;
	margin-right: 0px!important;
    padding: 4px 1px 4px 3px!important;
}
td.table-client,
td.table-description,
td.table-status {
	padding: 0 0 0 8px!important;
}
.table-description span.pull-right.faded-more {
	padding: 0 8px 0 0;
}
th.user-list-th-checkbox {
	width: 13px;
	border-radius: 6px 0 0 0;
	border-left: 1px solid #DCDCDC!important;
}
td.checkbox.nohover {
	padding: 8px 0px 8px 8px;
}
th.user-list-th-checkbox i.material-icons {
	font-size: 19px;
	color: #184E81;
	padding: 0 0 0px 2px!important;
	position: relative;
	top: 2px;
	left: -1px;
}
td.table-id a {
	font-size: 11px;
	text-align: left;
	float: left;
	padding: 3px 0 0 7px;
	color: #696969;
}
table.list thead th a {
	background: url(../../awesome/img/asc_desc.png) 100% 50% no-repeat;
}
table.list thead th a.asc {
	background: url(../../awesome/img/asc.png) 100% 50% no-repeat #dff0fd;
	height: 22px;
}
table.list thead th a.desc {
	background: url(../../awesome/img/desc.png) 100% 50% no-repeat #dff0fd;
	height: 22px;
}
th.head-assigned-to a {
    background: none!important;
}
th.user-list-th-name {
	width: 340px;
}
th.user-list-th-tickets {
	width: 100px;
}
th.user-list-th-status {
	width: 100px;
}
th.user-list-th-created {
	width: 100px;
}
th.user-list-th-updated {
	border-right: 1px solid #DCDCDC!important;
	border-radius: 0 6px 0 0;
	width: 174px;
}
table.list tbody tr:nth-child(2n+1):hover td {
	background: #EEE8D1;
}
table.list tbody tr:hover td,
table.list tbody tr.highlight td {
	background: #EEE8D1;
}
td#user-list-td-checkbox {
	border-left: 1px solid #DCDCDC;
	padding: 0 0px 0 6px;
}
td#user-list-td-updated {
	border-right: 1px solid #DCDCDC;
}
td.Emergency {
	background: url(../../awesome/img/priority-pattern-overlay.png) #ed1423!important;
}
td.High {
	background: url(../../awesome/img/priority-pattern-overlay.png) #0083e4!important
}
td.Normal {
	background: url(../../awesome/img/priority-pattern-overlay.png) #79b600!important;
}
td.Low {
	background: url(../../awesome/img/priority-pattern-overlay.png) #e2a110!important;
}
td.priority {
	margin: 0px!important;
	padding: 0px!important;
	width: 1px;
	overflow: hidden;
	border-left: 1px solid #DCDCDC!important;
}
td.priority a {
	padding: 0;
	border-collapse: collapse;
	display: block;
	text-decoration: none;
	font-size: 1px;
	margin: 0px!important;
	padding: 17px 0;
}
.table-date .nowrap {
    width: 130px;
}
.due-date {
    float: left;
}
.due-time {
    float: left;
    margin: 0 0 0 8px;
}


a.Icon.Ticket,
a.Icon.emailTicket,
a.Icon.phoneTicket,
a.Icon.webTicket {
	background: none;
	font-weight: normal;
	font-family: verdana;
}
table.list a.userPreview {
	font-size: 15px;
}
a.preview {
	font-size: 15px;
}
td#user-list-td-name,
td#user-list-td-tickets,
td#user-list-td-status,
td#user-list-td-created,
td#user-list-td-updated {
	padding-left: 10px;
}
table.list tfoot td {
    background-color: #F8F8F8;
    color: #999;
    text-align: left;
    vertical-align: top;
    padding: 9px;
    border: 1px solid #DCDCDC;
    border-radius: 0 0 6px 6px;
    font-size: 13px;
}
div#table-foot-options {
	width: 98%;
	margin: 0 auto;
}
div#page-count {
    margin: 5px 0 0 4px;
}
td[width="160"] {
    margin: 8px 0 0 0;
}
	
	
table.dashboard-stats {
    border-bottom: 1px solid #ddd;
}

table.dashboard-stats tbody:first-child th {
    border-bottom: 1px solid #ddd;
    padding: 0 4px 8px;
}

table.dashboard-stats tbody:nth-child(2) td {
    padding: 5px 8px;
    border-right: 1px solid #ccc;
}


table.dashboard-stats tbody:nth-child(2) tr:nth-child(odd) {
    background-color: #f0faff;
}



.text-overflow {
  display: block;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.jconfirm .jconfirm-box {
    background: white;
    border-radius: 4px;
    position: relative;
    outline: 0;
    padding: 15px 15px 0;
    overflow: hidden;
    margin-left: 35%;
    margin-right: 35%;
}

.notice {
    color: #3a87ad;
}
	
table.dataTable,
table.dataTable th,
table.dataTable td {
  -webkit-box-sizing: content-box;
  -moz-box-sizing: content-box;
  box-sizing: content-box;
}

ul.tabs {
    padding:4px 0 0 20px;
    margin:0;
    text-align:left;
    height:29px;
    border-bottom:1px solid #aaa;
    background:#eef3f8;
    position: relative;
    box-shadow: inset 0 -5px 10px -9px rgba(0,0,0,0.3);
}

#response_options ul.tabs {
}

ul.tabs li {
    margin:0;
    padding:0;
    display:inline-block;
    list-style:none;
    text-align:center;
    min-width:130px;
    font-weight:bold;
    height:28px;
    line-height:20px;
    color:#444;
    display:inline-block;
    outline:none;
    position:relative;
    bottom:1px;
    background:#fbfbfb;
    background-color: rgba(251, 251, 251, 0.5);
    border:1px solid #ccc;
    border:1px solid rgba(204, 204, 204, 0.5);
    border-bottom:none;
    position: relative;
    bottom: 1px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    font-size: 95%;
}
ul.tabs li.active {
    color:#184E81;
    background-color:#f9f9f9;
    border:1px solid #aaa;
    border-bottom:none;
    text-align: center;
    border-top:2px solid #81a9d7;
    bottom: 0;
    box-shadow: 4px -1px 6px -3px rgba(0,0,0,0.2);
}



ul.tabs li:not(.active) {
    box-shadow: inset 0 -5px 10px -9px rgba(0,0,0,0.2);
}
ul.tabs.clean li.active {
    background-color: white;
}

ul.tabs li a {
    font-weight: 400;
    line-height: 20px;
    color: #444;
    color: rgba(0,0,0,0.6);
    display: block;
    outline: none;
    padding: 5px 10px;
}
ul.tabs li a:hover {
    text-decoration: none;
}

ul.tabs li.active a {
    font-weight: bold;
    color: #222;
    color: rgba(0,0,0,0.8);
}

ul.tabs li.empty {
    padding: 5px;
    border: none !important;
}

ul.tabs.vertical {
    display: inline-block;
    height: auto;
    border-bottom: initial;
    border-right: 1px solid #aaa;
    padding-left: 0;
    padding-bottom: 40px;
    padding-top: 10px;
    background: transparent;
    box-shadow: inset -5px 0 10px -9px rgba(0,0,0,0.3);
}
ul.tabs.vertical.left {
    float: left;
    margin-right: 9px;
}

ul.tabs.vertical li {
    border:1px solid #ccc;
    border:1px solid rgba(204, 204, 204, 0.5);
    border-right: 0px;
    min-width: 0;
    display: block;
    border-top-right-radius: 0;
    border-bottom-left-radius: 5px;
    right: 0;
    height: auto;
}
ul.tabs.vertical li:not(.active) {
    box-shadow: inset -5px 0 10px -9px rgba(0,0,0,0.3);
}

ul.tabs.vertical li + li {
    margin-top: 5px;
}

ul.tabs.vertical li.active {
    border: 1px solid #aaa;
    border-left: 2px solid #81a9d7;
    border-right: 0px;
    right: -1px;
    box-shadow: -1px 4px 6px -3px rgba(0,0,0,0.3);
}

ul.tabs.vertical.left li {
    text-align: right;
}

ul.tabs.vertical li a {
    padding: 5px;
}

ul.tabs.alt {
  background-color:initial;
  border-bottom:2px solid #ccc;
  border-bottom-color: rgba(0,0,0,0.1);
  box-shadow:none;
}

ul.tabs.alt li {
  width:auto;
  border:none;
  min-width:0;
  box-shadow:none;
  bottom: 1px;
  height: auto;
}

ul.tabs.alt li.active {
  border:none;
  box-shadow:none;
  background-color: transparent;
  border-bottom:2px solid #81a9d7;
}

.tab_content:not(.left) {
    padding: 12px 0 0 0;
}
</style>
    
    
    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }

    // Offer alternate links for search engines
    // @see https://support.google.com/webmasters/answer/189077?hl=en
    if (($all_langs = Internationalization::getConfiguredSystemLanguages())
        && (count($all_langs) > 1)
    ) {
        $langs = Internationalization::rfc1766(array_keys($all_langs));
        $qs = array();
        parse_str($_SERVER['QUERY_STRING'], $qs);
        foreach ($langs as $L) {
            $qs['lang'] = $L; ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>?<?php
            echo http_build_query($qs); ?>" hreflang="<?php echo $L; ?>" />
<?php
        } ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
            hreflang="x-default";
<?php
    }
    ?>

<!-- OSTA -->
<?php include ROOT_DIR . 'awesome/inc/client-head.html'; ?>
<!-- /OSTA -->	

<!-- agregado por hdandrea 29-03-18 --------------------------------------------------------- -->

<link rel="shortcut icon" type="image/png" href="<?php echo ROOT_PATH; ?>favicon-32x32.png"/>

<!-- ---------------------------------------------------------------------------------------- -->

</head>
<body>
    <div id="container">
	
        <div id="header">	
		
			<div id="header-desktop">
			  <div id="header-logo">
				
				<!--  orignial logo function removed by osTicket Awesome
					<a class="pull-left" id="logo" href="<?php echo ROOT_PATH; ?>index.php"
					title="<?php echo __('Support Center'); ?>">
						<span class="valign-helper"></span>
						<img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php
						echo $ost->getConfig()->getTitle(); ?>">
					</a>
				-->	
					<!-- <a id="header-logo-link" href="<?php echo ROOT_PATH; ?>">
						<?php
								echo $ost->getConfig()->getTitle(); ?><br />
						<span class="header-logo-text">
						<?php echo __('Support Ticket System'); ?>						</span>
				</a> -->
				<?php include ROOT_DIR . 'awesome/inc/awesome-logo.php'; ?>
			  </div>			
				<div id="header-links">
				  <?php
						if ($thisclient && is_object($thisclient) && $thisclient->isValid()
							&& !$thisclient->isGuest()) {
						 echo 'Bienvenido,&nbsp;'.Format::htmlchars($thisclient->getName()).'&nbsp;';
						 ?>
				  <a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a> 
						<a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets (%d)'), $thisclient->getNumTickets()); ?></a>
						<a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
					<?php
					} elseif($nav) {
						if ($cfg->getClientRegistrationMode() == 'public') { ?>
							<?php echo __('Guest User'); ?>  <?php
						}
						if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
							<a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a><?php
						}
						elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
							<a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a>
					<?php
						}
					} ?>				
				</div>	

			</div>	
	  
			<div id="header-mobile">

				<div id="left-logo">
					<a id="header-logo-link" href="#">
                        <img src="mt.png" >
                        <!--
						<?php echo $ost->getConfig()->getTitle(); ?><br>
						<span class="header-logo-text">Ticket Tracking System</span>
					-->    
                    </a>	
                    	
				</div>
				<div id="right-buttons">
					<a class="mobile-nav-new-ticket" href="/support/scp/users.php">
						<svg style="width:24px;height:24px; padding: 18px;float:right;margin-right:1px;" viewBox="0 0 24 24">
							<path fill="#fff" d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
						</svg>
					</a>	
					<a class="mobile-nav-new-ticket" href="/support/scp/users.php">
						<svg style="width:24px;height:24px; padding: 18px;float:right;margin-right:1px;" viewBox="0 0 24 24">
							<path fill="#fff" d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" />
						</svg>
					</a>
					<a class="mobile-nav-new-ticket" href="/support/scp/users.php">
					<svg style="width:24px;height:24px; padding: 18px;float: right;margin-right: 61px;" viewBox="0 0 24 24">
						<path fill="#fff" d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
					</svg>
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
				<div class="sidr-inner">

					<ul id="nav-mobile" class="flush-left">
					<li><a href="<?php echo ROOT_PATH; ?>"><?php echo __(''); ?></a></li>
						<li><a href="<?php echo ROOT_PATH; ?>open.php"><?php echo __('Open a New Ticket'); ?></a></li>
						<li><a href="<?php echo ROOT_PATH; ?>view.php"><?php echo __('Check Ticket Status'); ?></a></li>
					<?php
						if ($thisclient && is_object($thisclient) && $thisclient->isValid()
							&& !$thisclient->isGuest()) {
						echo '<div id="welcome">Welcome,&nbsp;'.Format::htmlchars($thisclient->getName()).'</div>';
						 ?>
					<li><a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a></li>
						<li><a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets (%d)'), $thisclient->getNumTickets()); ?></a></li>
						<li><a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a></li>
				<?php
				} elseif($nav) {
					if ($cfg->getClientRegistrationMode() == 'public') { ?>
						<div id="welcome"><?php echo __('Guest User'); ?></div>  <?php
					}
					if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
						<li><a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a></li><?php
					}
					elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
						<li><a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a></li>
				<?php
					}
				} ?>
						<li id="contact-id"><a id="contact" href="#"><?php echo $ost->getConfig()->getTitle(); ?></a></li>
					</ul>

			  </div>
			</div>			
  
		</div> <!-- END Header -->
        <!--<div class="clear"></div>
        <?php
        if($nav){ ?>
        <ul id="nav" class="flush-left">
            <?php
            if($nav && ($navs=$nav->getNavLinks()) && is_array($navs)){
                foreach($navs as $name =>$nav) {
                    echo sprintf('<li><a class="%s %s" href="%s">%s</a></li>%s',$nav['active']?'active':'',$name,(ROOT_PATH.$nav['href']),$nav['desc'],"\n");
                }
            } ?>
        </ul>
        <?php
        }else{ ?>
         <hr>
        <?php
        } ?>
        <div class="clear"></div>
		<div id="internationalization-container">
			<div id="internationalization">

				<?php
				if (($all_langs = Internationalization::availableLanguages())
					&& (count($all_langs) > 1)
				) {
					foreach ($all_langs as $code=>$info) {
						list($lang, $locale) = explode('_', $code);
				?>
						<a class="flag flag-<?php echo strtolower($locale ?: $info['flag'] ?: $lang); ?>"
							href="?<?php echo urlencode($_GET['QUERY_STRING']); ?>&amp;lang=<?php echo $code;
							?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
				<?php }
					} ?>
			</div>
		</div>-->
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
