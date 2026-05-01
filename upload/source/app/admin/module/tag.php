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
$operation = $operation == 'admin' ? $operation : 'admin';
$current = [$operation => 1];
shownav('global', 'tag');
showsubmenu('tag', [
	['search', 'tag&operation=admin', $current['admin']],
]);

if($operation == 'admin') {
	$tagarray = [];
	if(submitcheck('submit') && !empty($_GET['tagidarray']) && is_array($_GET['tagidarray']) && !empty($_GET['operate_type'])) {
		require_once childfile('tag/submit');
	}
	if(!submitcheck('searchsubmit', 1)) {
		require_once childfile('tag/search');
	} else {
		require_once childfile('tag/form');
	}
}

