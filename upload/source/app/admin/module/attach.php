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

$searchsubmit = $_GET['searchsubmit'];

if(!submitcheck('deletesubmit')) {
	require_once childfile('attach/form');
	if(submitcheck('searchsubmit')) {
		require_once childfile('attach/search');
	}
} else {
	require_once childfile('attach/submit');
}

