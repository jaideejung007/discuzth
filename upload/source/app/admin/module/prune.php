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
$fromumanage = $_GET['fromumanage'] ? 1 : 0;

require_once libfile('function/misc');
loadcache('forums');

if(!submitcheck('prunesubmit')) {
	require_once childfile('prune/form');
} else {
	require_once childfile('prune/submit');
}

if(submitcheck('searchsubmit', 1)) {
	require_once childfile('prune/search');
}

