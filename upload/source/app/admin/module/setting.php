<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once childfile('setting/function');

cpheader();

$setting = table_common_setting::t()->fetch_all_setting(null);

if(!$isfounder) {
	unset($setting['ftp']);
}

$extbutton = '';
$operation = $operation ? $operation : 'basic';

$file = childfile('setting/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

if(!submitcheck('settingsubmit')) {

	require_once $file;

} else {

	$settingnew = $_GET['settingnew'];

	require_once $file;

	require_once childfile('setting/updatecache');

	cpmsg('setting_update_succeed', 'action='.(!empty($action) ? $action : 'setting').'&operation='.$operation.(!empty($_GET['anchor']) ? '&anchor='.$_GET['anchor'] : '').(!empty($from) ? '&from='.$from : ''), 'succeed');
}


