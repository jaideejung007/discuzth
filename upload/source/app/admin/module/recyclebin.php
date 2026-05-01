<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once libfile('function/post');
require_once libfile('function/discuzcode');

cpheader();

$operation = $operation ? $operation : 'list';
$file = childfile('recyclebin/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;