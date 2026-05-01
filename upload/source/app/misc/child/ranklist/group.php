<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['groupstatus']) {
	showmessage('ranklist_this_status_off');
}

$groupsrank = '';
$view = 'threads';
$navname = $_G['setting']['navs'][8]['navname'];
switch($_GET['view']) {
	case 'posts':
		$gettype = 'post';
		break;
	case 'today':
		$gettype = 'post_24';
		break;
	case 'threads':
		$gettype = 'thread';
		break;
	case 'credit':
		$gettype = 'credit';
		break;
	case 'member':
		$gettype = 'member';
		break;
	default:
		$_GET['view'] = 'credit';
}
$view = $_GET['view'];
$groupsrank = getranklistdata($type, $view);
$lastupdate = $_G['lastupdate'];
$nextupdate = $_G['nextupdate'];

$navtitle = lang('ranklist/navtitle', 'ranklist_title_group_'.$gettype).' - '.$navname;
$metakeywords = lang('ranklist/navtitle', 'ranklist_title_group_'.$gettype);
$metadescription = lang('ranklist/navtitle', 'ranklist_title_group_'.$gettype);

include template('diy:ranklist/group');

