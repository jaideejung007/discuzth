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

$operation = !empty($_GET['operation']) ? 'import' : '';

showsubmenu('district', [
	['list', 'district', !$operation],
	['import', 'district&operation=import', $operation == 'import'],
]);

$values = [intval($_GET['countryid']), intval($_GET['pid']), intval($_GET['cid']), intval($_GET['did'])];
$elems = [$_GET['country'], $_GET['province'], $_GET['city'], $_GET['district']];
$level = 0;
$upids = [0];
$theid = 0;
for($i = 0; $i < 4; $i++) {
	if(!empty($values[$i])) {
		$theid = intval($values[$i]);
		$upids[] = $theid;
		$level++;
	} else {
		for($j = $i; $j < 4; $j++) {
			$values[$j] = '';
		}
		break;
	}
}

if(submitcheck('editsubmit')) {
	require_once childfile('district/submit');
} elseif($operation == 'import') {
	require_once childfile('district/import');
} else {
	require_once childfile('district/form');
}

