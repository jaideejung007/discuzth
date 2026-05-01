<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!in_array($method, $interfaces)) {
	exit;
}
if(str_starts_with($method, 'plugin_')) {
	$pluginid = substr($method, 7);
	include_once DISCUZ_PLUGIN($pluginid).'/account.class.php';
	$c = 'account_'.$pluginid;
	if(method_exists($c, 'admincp')) {
		(new $c)->admincp();
	}
	exit;
} else {
	require_once childfile('account/interface/'.$method);
	exit;
}
	