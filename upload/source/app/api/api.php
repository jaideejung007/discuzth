<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('MITFRAME_APP')) {
	exit('Access Denied');
}

const IN_API = true;

$modarray = ['js' => 'javascript/javascript', 'ad' => 'javascript/advertisement'];

$mod = !empty($_GET['mod']) ? $_GET['mod'] : '';
if(empty($mod) || !in_array($mod, ['js', 'ad'])) {
	exit('Access Denied');
}

require_once './api/'.$modarray[$mod].'.php';

function loadcore() {
	global $_G;
	require_once './source/class/class_core.php';

	$discuz = C::app();
	$discuz->init_cron = false;
	$discuz->init_session = false;
	$discuz->init();
}

