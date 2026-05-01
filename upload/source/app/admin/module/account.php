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

shownav('global', 'account');

$method = $_GET['method'] ?? '';
$menus = [];

$interfaces = account_base::getInterfaces();
if(in_array($method, $interfaces)) {
	$menus[] = [account_base::getName($method), 'account&method='.$method, true];
}

if($method) {
	showchildmenu([['account', 'account']], account_base::getName($method));
	require_once childfile('account/interface');
}

showsubmenu('account', $menus);

require_once childfile('account/list');