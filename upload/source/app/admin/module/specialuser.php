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

$operation = in_array($_GET['operation'], ['defaultuser', 'follow']) ? trim($_GET['operation']) : 'defaultuser';
$suboperation = in_array($_GET['suboperation'], ['adduser', 'specialuser']) ? trim($_GET['suboperation']) : '';
$status = ($operation == 'defaultuser') ? 1 : 0;
$op = ($status == 1) ? 'defaultuser' : 'follow';
$url = 'specialuser&operation='.$op.'&suboperation=specialuser';

if($suboperation !== 'adduser') {
	if($_GET['do'] == 'edit') {
		require_once childfile('specialuser/edit');
	} elseif(!submitcheck('usersubmit')) {
		require_once childfile('specialuser/form');
	} else {
		require_once childfile('specialuser/submit');
	}
} else {
	require_once childfile('specialuser/add');
}
