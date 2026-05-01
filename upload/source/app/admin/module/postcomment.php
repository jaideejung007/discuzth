<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$detail = !empty($_GET['authorid']) ? true : $_GET['detail'];
$author = $_GET['author'];
$authorid = $_GET['authorid'];
$uid = $_GET['uid'];
$message = $_GET['message'];
$ip = $_GET['ip'];
$users = $_GET['users'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchtid = $_GET['searchtid'];
$searchpid = $_GET['searchpid'];
$searchsubmit = $_GET['searchsubmit'];
$cids = $_GET['cids'];
$page = max(1, $_GET['page']);

cpheader();

$aid = $_GET['aid'];
$subject = $_GET['subject'];

if(!submitcheck('postcommentsubmit')) {
	require_once childfile('postcomment/form');
} else {
	require_once childfile('postcomment/submit');
}

if(submitcheck('searchsubmit') || $newlist) {
	require_once childfile('postcomment/search');
}

