<?php
defined('OSTSCPINC') or die('Invalid path');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="refresh" content="7200" />
    <title>Solution Center :: <?php echo __('Agent Login'); ?></title>
    <link rel="stylesheet" href="css/login.css" type="text/css" />
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?231f11e"/>
    <meta name="robots" content="noindex" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />    
	<script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-1.11.2.min.js?231f11e"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("input:not(.dp):visible:enabled:first").focus();
         });
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>awesome/css/awesome-login.css" media="all"/>		
</head>
<body id="loginBody">

