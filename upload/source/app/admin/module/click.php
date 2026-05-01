<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$operation = $operation ? $operation : '';
cpheader();

if(empty($operation)) {
	$idtype = in_array($_GET['idtype'], ['blogid', 'picid', 'aid']) ? trim($_GET['idtype']) : 'blogid';
	if(!submitcheck('clicksubmit')) {
		require_once childfile('click/form');
	} else {
		require_once childfile('click/submit');
	}
}
