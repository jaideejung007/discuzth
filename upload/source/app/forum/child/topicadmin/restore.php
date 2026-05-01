<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($_G['adminid'] != '1') {
	showmessage('no_privilege_restore');
}
$archiveid = intval($_GET['archiveid']);
if(!submitcheck('modsubmit')) {
	include template('forum/topicadmin_action');
} else {
	if(!in_array($archiveid, $threadtableids)) {
		$archiveid = 0;
	}
	table_forum_thread::t()->insert_thread_copy_by_tid($_G['tid'], $archiveid, 0);
	table_forum_thread::t()->delete_by_tid($_G['tid'], false, $archiveid);

	$threadcount = table_forum_thread::t()->count_by_fid($_G['fid'], $archiveid);
	if($threadcount) {
		table_forum_forum_threadtable::t()->update_threadtable($_G['fid'], $archiveid, ['threads' => $threadcount]);
	} else {
		table_forum_forum_threadtable::t()->delete_threadtable($_G['fid'], $archiveid);
	}
	if(!table_forum_forum_threadtable::t()->count_by_fid($_G['fid'])) {
		table_forum_forum::t()->update($_G['fid'], ['archive' => 0]);
	}
	$modaction = 'RST';
	$reason = checkreasonpm();
	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modaction' => $modaction,
		'modlog' => $thread
	];
}

