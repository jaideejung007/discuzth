<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$aid = intval($_GET['aid']);
$imgurl = $_GET['imgurl'];
require_once libfile('function/post');
if($_G['forum'] && ($aid || $imgurl)) {
	if($imgurl) {
		$tid = intval($_GET['tid']);
		$pid = intval($_GET['pid']);
	} else {
		$threadimage = table_forum_attachment_n::t()->fetch_attachment('aid:'.$aid, $aid);
		$tid = $threadimage['tid'];
		$pid = $threadimage['pid'];
	}

	if($tid && $pid) {
		$thread = get_thread_by_tid($tid);
	} else {
		$thread = [];
	}
	if(empty($thread) || (!$_G['forum']['ismoderator'] && $_G['uid'] != $thread['authorid'])) {
		if($_GET['newthread']) {
			showmessage('set_cover_faild', '', [], ['msgtype' => 3]);
		} else {
			showmessage('set_cover_faild', '', [], ['closetime' => 3]);
		}
	}
	if(setthreadcover($pid, $tid, $aid, 0, $imgurl)) {
		if(empty($imgurl)) {
			table_forum_threadimage::t()->delete_by_tid($threadimage['tid']);
			table_forum_threadimage::t()->insert([
				'tid' => $threadimage['tid'],
				'attachment' => $threadimage['attachment'],
				'remote' => $threadimage['remote'],
			]);
		}
		if($_GET['newthread']) {
			showmessage('set_cover_succeed', '', [], ['msgtype' => 3]);
		} else {
			showmessage('set_cover_succeed', '', [], ['alert' => 'right', 'closetime' => 1]);
		}
	}
}
if($_GET['newthread']) {
	showmessage('set_cover_faild', '', [], ['msgtype' => 3]);
} else {
	showmessage('set_cover_faild', '', [], ['closetime' => 3]);
}
	