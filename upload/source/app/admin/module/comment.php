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
$idtype = $_GET['idtype'];
$id = $_GET['id'];
$author = $_GET['author'];
$authorid = $_GET['authorid'];
$uid = $_GET['uid'];
$message = $_GET['message'];
$ip = $_GET['ip'];
$users = $_GET['users'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchsubmit = $_GET['searchsubmit'];
$cids = $_GET['cids'];
$page = max(1, $_GET['page']);

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

cpheader();
if(empty($operation)) {
	require_once childfile('comment/search');
}

require_once childfile('comment/article_topic');
