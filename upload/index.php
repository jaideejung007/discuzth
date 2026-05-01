<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!empty($_GET['app'])) {
	define('MITFRAME_APP', $_app = $_GET['app']);
} else {
	$_app = 'index';
}

if(!preg_match('/^\w+$/', $_app)) {
	exit('Access Denied');
}

$f = './source/app/'.$_app.'/'.$_app.'.php';
if(!is_file($f)) {
	$_GET['module'] = !empty($_GET['module']) ? $_GET['module'] : $_app;
	$_GET['app'] = 'plugin';
	$_GET['id'] = $_app.':'.$_GET['module'];
	$f = './source/app/plugin/plugin.php';
}

require $f;