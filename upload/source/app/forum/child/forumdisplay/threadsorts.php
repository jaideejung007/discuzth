<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/threadsort');

$showpic = intval($_GET['showpic']);
$templatearray = $sortoptionarray = [];
foreach($_G['forum']['threadsorts']['types'] as $stid => $sortname) {
	loadcache(['threadsort_option_'.$stid, 'threadsort_template_'.$stid]);
	sortthreadsortselectoption($stid);
	$templatearray[$stid] = $_G['cache']['threadsort_template_'.$stid]['subject'];
	if(is_array($templatearray[$stid])) {
		$templatearray[$stid] = defined('IN_MOBILE') ? $templatearray[$stid][1] : $templatearray[$stid][0];
	}
	$sortoptionarray[$stid] = $_G['cache']['threadsort_option_'.$stid];
}

if(!empty($_G['forum']['threadsorts']['defaultshow']) && empty($_GET['sortid']) && empty($_GET['sortall'])) {
	$_GET['sortid'] = $_G['forum']['threadsorts']['defaultshow'];
	$_GET['filter'] = 'sortid';
	$_SERVER['QUERY_STRING'] = $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'].'&sortid='.$_GET['sortid'] : 'sortid='.$_GET['sortid'];
	$filterurladd = '&amp;filter=sort';
}

$_GET['sortid'] = $_GET['sortid'] ? $_GET['sortid'] : $_GET['searchsortid'];
if(isset($_GET['sortid']) && $_G['forum']['threadsorts']['types'][$_GET['sortid']]) {
	$searchsortoption = $sortoptionarray[$_GET['sortid']];
	$quicksearchlist = quicksearch($searchsortoption);
	$_G['forum_optionlist'] = $_G['cache']['threadsort_option_'.$_GET['sortid']];
	$forum_optionlist = getsortedoptionlist();
}
	