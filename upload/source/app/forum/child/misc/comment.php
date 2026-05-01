<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['setting']['commentnumber']) {
	showmessage('postcomment_closed');
}
$thread = table_forum_thread::t()->fetch_thread($_GET['tid']);
if($thread['closed'] && !$_G['forum']['ismoderator']) {
	showmessage('thread_closed');
}
$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid']);
if($_G['group']['allowcommentitem'] && !empty($_G['uid']) && $post['authorid'] != $_G['uid']) {
	$thread = table_forum_thread::t()->fetch_thread($post['tid']);
	$itemi = $thread['special'];
	if($thread['special'] > 0) {
		if($thread['special'] == 2) {
			$thread['special'] = $post['first'] || table_forum_trade::t()->check_goods($post['pid']) ? 2 : 0;
		} elseif($thread['special'] == 127) {
			$thread['special'] = $_GET['special'];
		} else {
			$thread['special'] = $post['first'] ? $thread['special'] : 0;
		}
	}
	$_G['setting']['commentitem'] = $_G['setting']['commentitem'][$thread['special']];
	if($thread['special'] == 0) {
		loadcache('forums');
		if($_G['cache']['forums'][$post['fid']]['commentitem']) {
			$_G['setting']['commentitem'] = $_G['cache']['forums'][$post['fid']]['commentitem'];
		}
	}
	if($_G['setting']['commentitem'] && !table_forum_postcomment::t()->count_by_pid($_GET['pid'], $_G['uid'], 1)) {
		$commentitem = explode("\n", $_G['setting']['commentitem']);
	}
}
if(!$post || !($_G['setting']['commentpostself'] || $post['authorid'] != $_G['uid']) || !(($post['first'] && $_G['setting']['commentfirstpost'] && in_array($_G['group']['allowcommentpost'], [1, 3]) || (!$post['first'] && in_array($_G['group']['allowcommentpost'], [2, 3]))))) {
	showmessage('postcomment_error');
}
$extra = !empty($_GET['extra']) ? rawurlencode($_GET['extra']) : '';
list($seccodecheck, $secqaacheck) = seccheck('post', 'reply');

include template('forum/comment');
	