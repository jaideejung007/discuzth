<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$fid = intval($_GET['fid']);
$allthread = intval($_GET['allthread']);
if(empty($fid)) {
	cpmsg('counter_thread_cover_fiderror', 'action=counter', 'error');
}
$nextlink = "action=counter&current=$next&pertask=$pertask&setthreadcover=yes&fid=$fid&allthread=$allthread";
$starttime = strtotime($_GET['starttime']);
$endtime = strtotime($_GET['endtime']);
$timesql = '';
if($starttime) {
	$timesql .= " AND lastpost > $starttime";
	$nextlink .= '&starttime='.$_GET['starttime'];
}
if($endtime) {
	$timesql .= " AND lastpost < $endtime";
	$nextlink .= '&endtime='.$_GET['endtime'];
}
$processed = 0;
$foruminfo = table_forum_forum::t()->fetch_info_by_fid($fid);
if(empty($foruminfo['picstyle'])) {
	cpmsg('counter_thread_cover_fidnopicstyle', 'action=counter', 'error');
}
if($_G['setting']['forumpicstyle']) {
	$_G['setting']['forumpicstyle'] = dunserialize($_G['setting']['forumpicstyle']);
	empty($_G['setting']['forumpicstyle']['thumbwidth']) && $_G['setting']['forumpicstyle']['thumbwidth'] = 203;
	empty($_G['setting']['forumpicstyle']['thumbheight']) && $_G['setting']['forumpicstyle']['thumbheight'] = 0;
} else {
	$_G['setting']['forumpicstyle'] = ['thumbwidth' => 203, 'thumbheight' => 0];
}
require_once libfile('function/post');
$coversql = empty($allthread) ? 'AND cover=\'0\'' : '';
$cover = empty($allthread) ? 0 : null;
$_G['forum']['ismoderator'] = 1;
foreach(table_forum_thread::t()->fetch_all_by_fid_cover_lastpost($fid, $cover, $starttime, $endtime, $current, $pertask) as $thread) {
	$processed = 1;
	$pid = table_forum_post::t()->fetch_threadpost_by_tid_invisible($thread['tid'], 0);
	$pid = $pid['pid'];
	setthreadcover($pid);
}

if($processed) {
	cpmsg("{$lang['counter_thread_cover']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_thread_cover_succeed', 'action=counter', 'succeed');
}
	