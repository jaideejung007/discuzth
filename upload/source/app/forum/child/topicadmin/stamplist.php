<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowstamplist']) {
	showmessage('no_privilege_stamplist');
}

loadcache('stamps');

if(!submitcheck('modsubmit')) {

	include template('forum/topicadmin_action');

} else {

	$_GET['stamplist'] = $_GET['stamplist'] !== '' ? $_GET['stamplist'] : -1;
	$modaction = $_GET['stamplist'] >= 0 ? 'L'.sprintf('%02d', $_GET['stamplist']) : 'SLD';
	$reason = checkreasonpm();

	table_forum_thread::t()->update($_G['tid'], ['moderated' => 1, 'icon' => $_GET['stamplist']]);
	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread', 'notictype' => 'post', 'item' => $_GET['stamplist'] !== '' ? 'reason_stamplist_update' : 'reason_stamplist_delete'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'stamp' => $_G['cache']['stamps'][$_GET['stamplist']]['text']],
		'modaction' => $modaction,
		'modlog' => $thread
	];
	$modpostsnum = 1;

	updatemodlog($_G['tid'], $modaction);

}

