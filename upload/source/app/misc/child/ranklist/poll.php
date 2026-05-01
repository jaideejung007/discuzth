<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$polllist = '';
$orderby = in_array($_GET['orderby'], ['thisweek', 'thismonth', 'today', 'all']) ? $_GET['orderby'] : '';
$navname = $_G['setting']['navs'][8]['navname'];
switch($_GET['view']) {
	case 'heats':
		$gettype = 'heat';
		break;
	case 'sharetimes':
		$gettype = 'share';
		break;
	case 'favtimes':
		$gettype = 'favorite';
		break;
	default:
		$_GET['view'] = 'heats';
}
$view = $_GET['view'];

$polllist = getranklistdata($type, $view, $orderby);
$lastupdate = $_G['lastupdate'];
$nextupdate = $_G['nextupdate'];

$navtitle = lang('ranklist/navtitle', 'ranklist_title_poll_'.$gettype).' - '.$navname;
$metakeywords = lang('ranklist/navtitle', 'ranklist_title_poll_'.$gettype);
$metadescription = lang('ranklist/navtitle', 'ranklist_title_poll_'.$gettype);

include template('diy:ranklist/poll');

