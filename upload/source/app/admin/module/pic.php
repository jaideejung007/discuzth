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
$albumid = $_GET['albumid'];
$users = $_GET['users'];
$picid = $_GET['picid'];
$postip = $_GET['postip'];
$hot1 = $_GET['hot1'];
$hot2 = $_GET['hot2'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchsubmit = $_GET['searchsubmit'];
$picids = $_GET['picids'];
$title = $_GET['title'];
$orderby = $_GET['orderby'];
$ordersc = $_GET['ordersc'];

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

$muticondition = '';
$muticondition .= $albumid ? '&albumid='.$albumid : '';
$muticondition .= $users ? '&users='.$users : '';
$muticondition .= $picid ? '&picid='.$picid : '';
$muticondition .= $postip ? '&postip='.$postip : '';
$muticondition .= $hot1 ? '&hot1='.$hot1 : '';
$muticondition .= $hot2 ? '&hot2='.$hot2 : '';
$muticondition .= $starttime ? '&starttime='.$starttime : '';
$muticondition .= $endtime ? '&endtime='.$endtime : '';
$muticondition .= $title ? '&title='.$title : '';
$muticondition .= $orderby ? '&orderby='.$orderby : '';
$muticondition .= $ordersc ? '&ordersc='.$ordersc : '';
$muticondition .= $fromumanage ? '&fromumanage='.$fromumanage : '';
$muticondition .= $searchsubmit ? '&searchsubmit='.$searchsubmit : '';
$muticondition .= $_GET['search'] ? '&search='.$_GET['search'] : '';
$muticondition .= $detail ? '&detail='.$detail : '';

cpheader();

if(!submitcheck('picsubmit')) {
	require_once childfile('pic/form');
} else {
	require_once childfile('pic/submit');
}

if(submitcheck('searchsubmit', 1) || $newlist) {
	require_once childfile('pic/search');
}

