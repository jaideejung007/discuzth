<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

cpheader();
$operation = in_array($operation, ['add', 'edit', 'delete']) ? $operation : 'list';

loadcache('blockclass');

shownav('portal', 'blockstyle');

if($operation == 'add' || $operation == 'edit') {
	require_once childfile('blockstyle/add_edit');
} else {
	$file = childfile('blockstyle/'.$operation);
	if(!file_exists($file)) {
		cpmsg('undefined_action');
	}
	require_once $file;
}

