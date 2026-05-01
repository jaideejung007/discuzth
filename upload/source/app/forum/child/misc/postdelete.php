<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $_GET['pid']);
if($post['authorid'] != $_G['uid']) {
	showmessage('postdelete_only_yourself');
}
if(submitcheck('postdeletesubmit')) {
	$url_forward = 'forum.php?mod=viewthread&tid=' .$post['tid'];
	require_once libfile('function/delete');

	if($post['first']) {
		deletethread([$post['tid']], true, true);
		updateforumcount($post['fid']);

		deletepost([$post['tid']], 'tid', true);
		updatethreadcount($post['tid']);
		$url_forward = 'forum.php?mod=forumdisplay&fid=' .$post['fid'];
	} else {
		deletepost([$post['pid']], 'pid', true);
		updatethreadcount($post['tid']);
	}

	if(!empty($_G['inajax'])) {
		showmessage('postdelete_succeed', $url_forward, [], ['location' => true]);
	} else {
		showmessage('postdelete_succeed', $url_forward);
	}
} else {
	include template('forum/postdelete');
}
	