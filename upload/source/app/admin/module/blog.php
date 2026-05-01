<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
include_once libfile('function/portalcp');

cpheader();

$detail = $_GET['detail'];
$uid = $_GET['uid'];
$blogid = $_GET['blogid'];
$users = $_GET['users'];
$keywords = $_GET['keywords'];
$lengthlimit = $_GET['lengthlimit'];
$viewnum1 = $_GET['viewnum1'];
$viewnum2 = $_GET['viewnum2'];
$replynum1 = $_GET['replynum1'];
$replynum2 = $_GET['replynum2'];
$hot1 = $_GET['hot1'];
$hot2 = $_GET['hot2'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchsubmit = $_GET['searchsubmit'];
$blogids = $_GET['blogids'];
$friend = $_GET['friend'];
$ip = $_GET['ip'];
$orderby = $_GET['orderby'];
$ordersc = $_GET['ordersc'];

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

$muticondition = '';
$muticondition .= $uid ? '&uid='.$uid : '';
$muticondition .= $blogid ? '&blogid='.$blogid : '';
$muticondition .= $users ? '&users='.$users : '';
$muticondition .= $keywords ? '&keywords='.$keywords : '';
$muticondition .= $lengthlimit ? '&lengthlimit='.$lengthlimit : '';
$muticondition .= $viewnum1 ? '&viewnum1='.$viewnum1 : '';
$muticondition .= $viewnum2 ? '&viewnum2='.$viewnum2 : '';
$muticondition .= $replynum1 ? '&replynum1='.$replynum1 : '';
$muticondition .= $replynum2 ? '&replynum2='.$replynum2 : '';
$muticondition .= $hot1 ? '&hot1='.$hot1 : '';
$muticondition .= $hot2 ? '&hot2='.$hot2 : '';
$muticondition .= $starttime ? '&starttime='.$starttime : '';
$muticondition .= $endtime ? '&endtime='.$endtime : '';
$muticondition .= $friend ? '&friend='.$friend : '';
$muticondition .= $ip ? '&ip='.$ip : '';
$muticondition .= $orderby ? '&orderby='.$orderby : '';
$muticondition .= $ordersc ? '&ordersc='.$ordersc : '';
$muticondition .= $fromumanage ? '&fromumanage='.$fromumanage : '';
$muticondition .= $searchsubmit ? '&searchsubmit='.$searchsubmit : '';
$muticondition .= $_GET['search'] ? '&search='.$_GET['search'] : '';
$muticondition .= $detail ? '&detail='.$detail : '';

if(!submitcheck('blogsubmit')) {
	require_once childfile('blog/form');
} else {
	require_once childfile('blog/submit');
}

if(submitcheck('searchsubmit', 1) || $newlist) {
	require_once childfile('blog/search');
}

