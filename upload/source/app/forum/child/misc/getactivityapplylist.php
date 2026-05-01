<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$pp = $_G['setting']['activitypp'];
$page = max(1, $_G['page']);
$start = ($page - 1) * $pp;
$activity = table_forum_activity::t()->fetch($_G['tid']);
if(!$activity || $thread['special'] != 4) {
	showmessage('undefined_action');
}
$query = table_forum_activityapply::t()->fetch_all_for_thread($_G['tid'], $start, $pp);
foreach($query as $activityapplies) {
	$activityapplies['dateline'] = dgmdate($activityapplies['dateline']);
	$applylist[] = $activityapplies;
}
$multi = multi($activity['applynumber'], $pp, $page, "forum.php?mod=misc&action=getactivityapplylist&tid={$_G['tid']}&pid={$_GET['pid']}");
include template('forum/activity_applist_more');
	