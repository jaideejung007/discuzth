<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowsplitthread']) {
	showmessage('no_privilege_splitthread');
}

$thread = table_forum_thread::t()->fetch_thread($_G['tid']);
$posttableid = $thread['posttableid'];
if(!submitcheck('modsubmit')) {

	require_once libfile('function/discuzcode');

	$replies = $thread['replies'];
	if($replies <= 0) {
		showmessage('admin_split_invalid');
	}

	$postlist = [];
	foreach(table_forum_post::t()->fetch_all_by_tid('tid:'.$_G['tableid'], $_G['tid'], 'ASC') as $post) {
		$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], sprintf('%00b', $post['htmlon']), $_G['forum']['allowsmilies'], $_G['forum']['allowbbcode'], $_G['forum']['allowimgcode'], $_G['forum']['allowhtml']);
		$postlist[] = $post;
	}
	include template('forum/topicadmin_action');

} else {

	if(!trim($_GET['subject'])) {
		showmessage('admin_split_subject_invalid');
	} elseif(!($nos = explode(',', $_GET['split']))) {
		showmessage('admin_split_new_invalid');
	}

	sort($nos);
	foreach(table_forum_post::t()->fetch_all_by_tid_position($thread['posttableid'], $_G['tid'], $nos) as $post) {
		$pids[] = $post['pid'];
	}
	$pids = is_array($pids) ? $pids : [$pids];
	if(!($pids = implode(',', $pids))) {
		showmessage('admin_split_new_invalid');
	}

	$modaction = 'SPL';

	$reason = checkreasonpm();

	$subject = dhtmlspecialchars($_GET['subject']);

	$newtid = table_forum_thread::t()->insert(['fid' => $_G['fid'], 'posttableid' => $posttableid, 'subject' => $subject], true);

	table_forum_post::t()->update_post('tid:'.$_G['tid'], explode(',', $pids), ['tid' => $newtid]);
	updateattachtid('pid', (array)explode(',', $pids), $_G['tid'], $newtid);

	$splitauthors = [];
	foreach(table_forum_post::t()->fetch_all_visiblepost_by_tid_groupby_authorid('tid:'.$_G['tid'], $newtid) as $splitauthor) {
		$splitauthor['subject'] = $subject;
		$splitauthors[] = $splitauthor;
	}

	table_forum_post::t()->update_post('tid:'.$_G['tid'], $splitauthors[0]['pid'], ['first' => 1, 'subject' => $subject], true);

	$query = table_forum_post::t()->fetch_all_by_tid('tid:'.$_G['tid'], $_G['tid'], false, 'ASC', 0, 1);
	foreach($query as $row) {
		$fpost = $row;
	}
	table_forum_thread::t()->update($_G['tid'], ['author' => $fpost['author'], 'authorid' => $fpost['authorid'], 'dateline' => $fpost['dateline'], 'moderated' => 1]);
	table_forum_post::t()->update_post('tid:'.$_G['post'], $fpost['pid'], ['first' => 1, 'subject' => $thread['subject']]);

	$query = table_forum_post::t()->fetch_all_by_tid('tid:'.$_G['tid'], $newtid, false, 'ASC', 0, 1);
	foreach($query as $row) {
		$fpost = $row;
	}
	$maxposition = 1;
	foreach(table_forum_post::t()->fetch_all_by_tid('tid:'.$_G['tid'], $_G['tid'], false, 'ASC') as $row) {
		if($row['position'] != $maxposition) {
			table_forum_post::t()->update_post('tid:'.$_G['tid'], $row['pid'], ['position' => $maxposition]);
		}
		$maxposition++;
	}
	table_forum_thread::t()->update($_G['tid'], ['maxposition' => $maxposition]);
	$maxposition = 1;
	foreach(table_forum_post::t()->fetch_all_by_tid('tid:'.$_G['tid'], $newtid, false, 'ASC') as $row) {
		if($row['position'] != $maxposition) {
			table_forum_post::t()->update_post('tid:'.$_G['tid'], $row['pid'], ['position' => $maxposition]);
		}
		$maxposition++;
	}
	table_forum_thread::t()->update($newtid, ['author' => $fpost['author'], 'authorid' => $fpost['authorid'], 'dateline' => $fpost['dateline'], 'rate' => intval(abs($fpost['rate']) ? ($fpost['rate'] / abs($fpost['rate'])) : 0), 'maxposition' => $maxposition]);
	updatethreadcount($_G['tid']);
	updatethreadcount($newtid);
	updateforumcount($_G['fid']);

	$_G['forum']['threadcaches'] && deletethreadcaches($thread['tid']);

	$modpostsnum++;
	$resultarray = [
		'redirect' => "forum.php?mod=forumdisplay&fid={$_G['fid']}",
		'reasonpm' => ($sendreasonpm ? ['data' => $splitauthors, 'var' => 'thread', 'item' => 'reason_moderate', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modtids' => $thread['tid'].','.$newtid,
		'modlog' => [$thread, ['tid' => $newtid, 'subject' => $subject]]
	];

}

