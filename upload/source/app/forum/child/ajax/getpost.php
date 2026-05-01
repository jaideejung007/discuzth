<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$tid = intval($_GET['tid']);
$fid = intval($_GET['fid']);
$pid = intval($_GET['pid']);
$thread = table_forum_thread::t()->fetch_thread($tid);
$post = table_forum_post::t()->fetch_post($thread['posttableid'], $pid);
if($_G['uid'] != $post['authorid']) {
	showmessage('quickclear_noperm');
}
include template('forum/ajax_followpost');
	