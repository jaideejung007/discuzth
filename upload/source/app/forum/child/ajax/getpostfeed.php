<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['followstatus']) {
	showmessage('follow_status_off');
}
$tid = intval($_GET['tid']);
$pid = intval($_GET['pid']);
$flag = intval($_GET['flag']);
$feed = $thread = [];
if($tid) {
	$thread = table_forum_thread::t()->fetch_thread($tid);
	if($flag) {
		$post = table_forum_post::t()->fetch_post($thread['posttableid'], $pid);
		if($thread['tid'] != $post['tid']) {
			showmessage('quickclear_noperm');
		}
		require_once libfile('function/discuzcode');
		require_once libfile('function/followcode');
		$post['message'] = followcode($post['message'], $tid, $pid);
	} else {
		if(!isset($_G['cache']['forums'])) {
			loadcache('forums');
		}
		$feedid = intval($_GET['feedid']);
		$feed = table_forum_threadpreview::t()->fetch($tid);
		if($feedid) {
			$feed = array_merge($feed, table_home_follow_feed::t()->fetch_by_feedid($feedid));
		}
		$post['message'] = $feed['content'];
	}
}
include template('forum/ajax_followpost');
	