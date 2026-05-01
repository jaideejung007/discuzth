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

$operation = $operation ? $operation : 'list';

if($operation == 'list'){

	$detail = $_GET['detail'];
	$users = $_GET['users'];
	$userip = $_GET['userip'];
	$keywords = $_GET['keywords'];
	$lengthlimit = $_GET['lengthlimit'];
	$starttime = $_GET['starttime'];
	$endtime = $_GET['endtime'];
	$searchsubmit = $_GET['searchsubmit'];
	$doids = $_GET['doids'];

	$fromumanage = $_GET['fromumanage'] ? 1 : 0;

	if(!submitcheck('doingsubmit')) {
		require_once childfile('doing/form');
	} else {
		require_once childfile('doing/submit');
	}

	if(submitcheck('searchsubmit', 1) || $newlist) {
		require_once childfile('doing/search');
	}

}else{
	$file = childfile('doing/'.$operation);
	if(!file_exists($file)) {
		cpmsg('undefined_action');
	}
	require_once $file;
}