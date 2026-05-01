<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

[$app, $filename] = explode(':', $_GET['operation']);

$f = appfile('admin/'.$filename, $app);

if(!$f || !file_exists($f)) {
	exit('Access Denied');
}

define('MITFRAME_APP_ADMIN', $app);
require_once $f;