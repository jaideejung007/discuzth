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
if($tid) {
	$thread = table_forum_thread::t()->fetch_thread($tid);
	if($thread && !getstatus($thread['status'], 2)) {
		$list = table_forum_post::t()->fetch_all_by_tid('tid:'.$tid, $tid, true, 'DESC', 0, 10, null, 0);
		loadcache('smilies');
		foreach($list as $pid => $post) {
			if($post['first']) {
				unset($list[$pid]);
			} else {
				$post['message'] = preg_replace($_G['cache']['smilies']['searcharray'], '', $post['message']);
				$post['message'] = preg_replace('/\{\:soso_((e\d+)|(_\d+_\d))\:\}/', '', $post['message']);
				$list[$pid]['message'] = cutstr(preg_replace('/\[.+?\]/is', '', dhtmlspecialchars($post['message'])), 300);
			}
		}
		krsort($list);
	}
}
list($seccodecheck, $secqaacheck) = seccheck('post', 'reply');
include template('forum/ajax_quickreply');
	