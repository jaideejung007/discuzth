<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

define('IN_ARCHIVER', 1);

chdir('../');

$querystring = $_SERVER['QUERY_STRING'];

if(!empty($_GET['action']) && !empty($_GET['value'])) {
	$querystring = $_GET['action'].'-'.$_GET['value'];
}

if(substr($querystring, 0, 3) == 'fid') {
	$_GET['mod'] = 'forumdisplay';
	$_GET['fid'] = intval(substr($querystring, 4));
} elseif(substr($querystring, 0, 3) == 'tid') {
	$_GET['mod'] = 'viewthread';
	$_GET['tid'] = intval(substr($querystring, 4));
}

$_GET['app'] = 'forum';

require 'index.php';

?>