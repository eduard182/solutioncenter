<?php
/*************************************************************************
    tasks.php

    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('secure.inc.php');
if(!is_object($thisclient) || !$thisclient->isValid()) die('Access denied'); //Double check again.

if ($thisclient->isGuest())
    $_REQUEST['id'] = $thisclient->getTicketId();


require_once(INCLUDE_DIR.'class.task.php');
require_once(INCLUDE_DIR.'class.export.php');

$page = '';
$task = null; //clean start.
if ($_REQUEST['id']) {
    if (!($task=Task::lookup($_REQUEST['id'])))
         $errors['err'] = sprintf(__('%s: Unknown or invalid ID.'), __('task'));
    
}



$inc="tasks.inc.php";
include(CLIENTINC_DIR.'header.inc.php');
include(CLIENTINC_DIR.$inc);
include(CLIENTINC_DIR.'footer.inc.php');
?>
