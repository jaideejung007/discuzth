<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowlivethread']) {
	showmessage('no_privilege_livethread');
}

if(!submitcheck('modsubmit')) {

	include template('forum/topicadmin_action');

} else {

	$modaction = $_GET['live'] ? 'LIV' : 'LIC';
	$reason = checkreasonpm();
	$expiration = $_GET['expirationlive'] ? dintval($_GET['expirationlive']) : 0;

	if($modaction == 'LIV') {
		table_forum_forumfield::t()->update($_G['fid'], ['livetid' => $_G['tid']]);
	} elseif($modaction == 'LIC') {
		if($_G['tid'] != $_G['forum']['livetid']) {
			showmessage('topicadmin_live_noset_error');
		}
		table_forum_forumfield::t()->update($_G['fid'], ['livetid' => 0]);
	}

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread', 'notictype' => 'post', 'item' => $_GET['live'] ? 'reason_live_update' : 'reason_live_cancle'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason],
		'modaction' => $modaction,
		'modlog' => $thread
	];
	$modpostsnum = 1;

	updatemodlog($_G['tid'], $modaction, $expiration, 0, '', $modaction == 'LIV' ? 1 : 0);

}

