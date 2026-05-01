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

$operations[$operation] = true;

$navs = [
	['nav_qrcodelogin_list', 'qrcodelogin&operation=list', $operations['list']],
	isfounder() ? ['nav_qrcodelogin_setting', 'qrcodelogin&operation=setting', $operations['setting']] : null,
];

showsubmenu('nav_qrcodelogin', $navs);

$file = childfile('qrcodelogin/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function requestLoginApi($action, $param = []) {
	global $_G;
	$param['siteurl'] = $_G['siteurl'];
	$param['siteuniqueid'] = $_G['setting']['siteuniqueid'];
	return \admin\class_qrcodelogin::admin($action, $param);
}