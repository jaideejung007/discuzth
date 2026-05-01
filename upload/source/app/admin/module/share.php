<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$detail = $_GET['detail'];
$uid = $_GET['uid'];
$users = $_GET['users'];
$sid = $_GET['sid'];
$type = $_GET['type'];
$hot1 = $_GET['hot1'];
$hot2 = $_GET['hot2'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchsubmit = $_GET['searchsubmit'];
$sids = $_GET['sids'];

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

cpheader();

if(!submitcheck('sharesubmit')) {
	require_once childfile('share/form');
} else {
	require_once childfile('share/submit');
}

if(submitcheck('searchsubmit', 1) || $newlist) {
	require_once childfile('share/search');
}

