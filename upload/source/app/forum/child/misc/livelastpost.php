<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$fid = dintval($_GET['fid']);
$forum = table_forum_forumfield::t()->fetch($fid);
$livetid = $forum['livetid'];
$postlist = [];
if($livetid) {
	$thread = table_forum_thread::t()->fetch_thread($livetid);
	$postlist['count'] = $thread['replies'];
	$postarr = table_forum_post::t()->fetch_all_by_tid('tid:'.$livetid, $livetid, true, 'DESC', 20);
	ksort($postarr);
	foreach($postarr as $post) {
		if($post['first'] == 1 || getstatus($post['status'], 1)) {
			continue;
		}
		$contentarr = [
			'authorid' => !$post['anonymous'] ? $post['authorid'] : '',
			'author' => !$post['anonymous'] ? $post['author'] : lang('forum/misc', 'anonymous'),
			'message' => str_replace("\r\n", '<br>', messagecutstr($post['message'])),
			'dateline' => dgmdate($post['dateline'], 'u'),
			'avatar' => !$post['anonymous'] ? avatar($post['authorid'], 'small') : '',
		];
		$postlist['list'][$post['pid']] = $contentarr;
	}
}

showmessage('', '', $postlist);
exit;
	