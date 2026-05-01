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
$status = $_GET['op'] == 'ignore' ? 0 : 1;
if(!empty($tid)) {
	$thread = table_forum_thread::t()->fetch_by_tid_displayorder($tid, 0);
	if($thread['authorid'] == $_G['uid']) {
		$thread['status'] = setstatus(6, $status, $thread['status']);
		table_forum_thread::t()->update($tid, ['status' => $thread['status']], true);
		showmessage('replynotice_success_'.$status);
	}
}
showmessage('replynotice_error', 'forum.php?mod=viewthread&tid='.$tid);
	