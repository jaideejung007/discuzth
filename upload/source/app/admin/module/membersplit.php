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


if(!$operation) {
	$operation = 'check';
}
loadcache(['membersplitdata', 'userstats']);
if(!empty($_G['cache']['membersplitstep'])) {
	cpmsg('membersplit_split_in_backstage', 'action=membersplit&operation=check', 'loadingform');
}

$file = childfile('membersplit/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}
require_once $file;

