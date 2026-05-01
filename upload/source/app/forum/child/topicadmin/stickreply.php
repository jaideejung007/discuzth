<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowstickreply'] && !$specialperm) {
	showmessage('no_privilege_stickreply');
}

$topiclist = $_GET['topiclist'];
$modpostsnum = count($topiclist);
if(empty($topiclist)) {
	showmessage('admin_stickreply_invalid');
} elseif(!$_G['tid']) {
	showmessage('admin_nopermission', NULL);
}
$sticktopiclist = $posts = [];
$authorids = [];
foreach($topiclist as $pid) {
	$post = table_forum_post::t()->fetch_post('tid:'.$_G['tid'], $pid, false);
	$authorids[] = ['authorid' => $post['authorid']];
	$sticktopiclist[$pid] = $post['position'];
}

if(!submitcheck('modsubmit')) {

	$stickpid = '';
	foreach($sticktopiclist as $id => $postnum) {
		$stickpid .= '<input type="hidden" name="topiclist[]" value="'.dintval($id).'" />';
	}

	include template('forum/topicadmin_action');

} else {

	if($_GET['stickreply']) {
		foreach($sticktopiclist as $pid => $postnum) {
			$post = table_forum_post::t()->fetch_all_by_pid('tid:'.$_G['tid'], $pid, false);
			if($post[$pid]['tid'] != $_G['tid']) {
				continue;
			}
			table_forum_poststick::t()->insert([
				'tid' => $_G['tid'],
				'pid' => $pid,
				'position' => $postnum,
				'dateline' => $_G['timestamp'],
			], false, true);
		}
	} else {
		foreach($sticktopiclist as $pid => $postnum) {
			table_forum_poststick::t()->delete_stick($_G['tid'], $pid);
		}
	}

	$sticknum = table_forum_poststick::t()->count_by_tid($_G['tid']);
	$stickreply = intval($_GET['stickreply']);

	if($sticknum == 0 || $stickreply == 1) {
		table_forum_thread::t()->update($_G['tid'], ['moderated' => 1, 'stickreply' => $stickreply]);
	}

	$modaction = $_GET['stickreply'] ? 'SRE' : 'USR';
	$reason = checkreasonpm();

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => $authorids, 'var' => 'post', 'notictype' => 'post', 'item' => $_GET['stickreply'] ? 'reason_stickreply' : 'reason_stickdeletereply'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modlog' => $thread
	];

}

