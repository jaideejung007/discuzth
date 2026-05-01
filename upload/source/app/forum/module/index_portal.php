<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['setting']['domain']['defaultindex'] == 'forum.php?mod=forumdisplay&fid=0') {
	$uriBase = 'index.php?';
} else {
	$uriBase = 'forum.php?mod=forumdisplay&fid=0&';
}

if(empty($_G['setting']['forumportal']['navList']) || !is_array($_G['setting']['forumportal']['navList'])) {
	showmessage('forumportal_no_setting');
}

$tpp = !empty($_G['setting']['forumportal']['setting']['tpp']) ? $_G['setting']['forumportal']['setting']['tpp'] : $_G['tpp'];


$portalNavList = [];
foreach($_G['setting']['forumportal']['navList'] as $navId => $row) {
	if(!$row['allow']) {
		continue;
	}
	if(!empty($row['adminid']) && $row['adminid'] > 0 && (!$_G['adminid'] || $_G['adminid'] > $row['adminid'])) {
		continue;
	}
	$row['navid'] = $navId;
	$portalNavList[$navId] = $row;
}

if(!$portalNavList) {
	showmessage('forumportal_no_setting');
}


if(!isset($_GET['navId'])) {
	$curNavId = array_keys($portalNavList)[0];
} else {
	if(!isset($portalNavList[$_GET['navId']])) {
		showmessage('forumportal_page_not_found');
	}
	$curNavId = $_GET['navId'];
}
$setting = $_G['setting']['forumportal']['navList'][$curNavId];


require_once childfile('filter');


$_G['forum_threadcount'] = table_forum_thread::t()->count_search($filterarr, 0);
$_G['forum_threadimage'] = !empty($_G['setting']['forumportal']['setting']['image']) ? $_G['setting']['forumportal']['setting']['image'] : [];

$page = max(1, $_G['page']);
$page = $_G['setting']['threadmaxpages'] && $page > $_G['setting']['threadmaxpages'] ? 1 : $page;
$maxPage = @ceil($_G['forum_threadcount'] / $tpp);
$page = $maxPage < $page ? 1 : $page;
$start_limit = ($page - 1) * $tpp;
$nextpage = '';

$threadlist = table_forum_thread::t()->fetch_all_search($filterarr, 0, $start_limit, $tpp, $order, '');

$_G['ppp'] = $_G['forum']['threadcaches'] && !$_G['uid'] ? $_G['setting']['postperpage'] : $_G['ppp'];

$used = cells::getUsed('forum/portal/threadlist');
if(!empty($used['nextpage'])) {
	$_GET['ajaxtarget'] = 'threadlistAppend';
	$nextpage = $tpp == count($threadlist) ? $uriBase.'navId='.$curNavId.'&page='.($page + 1) : '';
} else {
	$_GET['ajaxtarget'] = 'threadlist';
	$multipage = multi($_G['forum_threadcount'], $tpp, $page, $uriBase.'navId='.$curNavId, $_G['setting']['threadmaxpages']);
}

$page = $_G['page'];

$allowleftside = 0;
$allowside = 1;

$leftside = empty($_G['cookie']['disableleftside']) && $allowleftside ? forumleftside() : [];

list($navtitle, $metadescription, $metakeywords) = get_seosetting('forum');
if(!$navtitle) {
	$navtitle = $_G['setting']['navs'][2]['navname'];
	$nobbname = false;
} else {
	$nobbname = true;
}
if(!$metadescription) {
	$metadescription = $navtitle;
}
if(!$metakeywords) {
	$metakeywords = $navtitle;
}

if($_G['inajax']) {
	include template('forum/ajax_index_portal');
} else {
	include template('diy:forum/index_portal');
}