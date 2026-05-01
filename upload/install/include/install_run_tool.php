<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('RUN_MODE') || RUN_MODE != 'tool') {
	show_msg('method_undefined', $method, 0);
}

$allow_method = ['select', 'resetpw', 'dircheck', 'updatecache', 'restore', 'done'];

$method = getgpc('method');

if(empty($method) || !in_array($method, $allow_method)) {
	$method = 'select';
}

if(!file_exists($lockfile) ||
	!file_exists(ROOT_PATH.'./config/config_global.php') ||
	!file_exists(ROOT_PATH.'./config/config_ucenter.php')) {
	show_msg('install_locked_exists', '', 0);
}

if(str_contains(_FILE_, 'index')) {
	show_msg('install_locked_format_error', '', 0);
}

require_once ROOT_PATH.'./config/config_global.php';
require_once ROOT_PATH.'./config/config_ucenter.php';

if($method == 'done') {
	all_done();
} else {
	require_once ROOT_PATH.'./install/include/tool/tool_'.$method.'.php';
}