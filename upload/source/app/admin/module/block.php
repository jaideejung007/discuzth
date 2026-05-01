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
$operation = in_array($operation, ['jscall', 'list', 'perm']) ? $operation : 'list';

shownav('portal', 'block');
loadcache('blockclass');

if($operation == 'perm') {
	require_once childfile('block/perm');
} else {
	require_once childfile('block/list');
}

