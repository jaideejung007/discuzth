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
	$operation = 'headernav';
}

$navs = ['headernav', 'topnav', 'footernav', 'mynav', 'mnav', 'mfindnav'];
$navdata = [];
foreach($navs as $nav) {
	$navdata[] = ['nav_nav_'.$nav, 'nav&operation='.$nav, $nav == $operation];
}

$file = childfile('nav/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

