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
$albumname = $_GET['albumname'];
$albumid = $_GET['albumid'];
$uid = $_GET['uid'];
$users = $_GET['users'];
$starttime = $_GET['starttime'];
$endtime = $_GET['endtime'];
$searchsubmit = $_GET['searchsubmit'];
$albumids = $_GET['albumids'];
$friend = $_GET['friend'];
$orderby = $_GET['orderby'];
$ordersc = $_GET['ordersc'];

$fromumanage = $_GET['fromumanage'] ? 1 : 0;

$muticondition = '';
$muticondition .= $albumname ? '&albumname='.$albumname : '';
$muticondition .= $albumid ? '&albumid='.$albumid : '';
$muticondition .= $uid ? '&uid='.$uid : '';
$muticondition .= $users ? '&users='.$users : '';
$muticondition .= $starttime ? '&starttime='.$starttime : '';
$muticondition .= $endtime ? '&endtime='.$endtime : '';
$muticondition .= $friend ? '&friend='.$friend : '';
$muticondition .= $orderby ? '&orderby='.$orderby : '';
$muticondition .= $ordersc ? '&ordersc='.$ordersc : '';
$muticondition .= $fromumanage ? '&fromumanage='.$fromumanage : '';
$muticondition .= $searchsubmit ? '&searchsubmit='.$searchsubmit : '';
$muticondition .= $_GET['search'] ? '&search='.$_GET['search'] : '';
$muticondition .= $detail ? '&detail='.$detail : '';

if(!submitcheck('albumsubmit')) {
	require_once childfile('album/form');
} else {
	require_once childfile('album/submit');
}

if(submitcheck('searchsubmit', 1) || $newlist) {
	require_once childfile('album/search');
}
