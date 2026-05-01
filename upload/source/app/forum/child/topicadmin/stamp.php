<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowstampthread']) {
	showmessage('no_privilege_stampthread');
}

loadcache('stamps');

if(!submitcheck('modsubmit')) {

	include template('forum/topicadmin_action');

} else {

	$modaction = $_GET['stamp'] !== '' ? 'SPA' : 'SPD';
	$_GET['stamp'] = $_GET['stamp'] !== '' ? $_GET['stamp'] : -1;
	$reason = checkreasonpm();

	table_forum_thread::t()->update($_G['tid'], ['moderated' => 1, 'stamp' => $_GET['stamp']]);
	if($modaction == 'SPA' && $_G['cache']['stamps'][$_GET['stamp']]['icon']) {
		table_forum_thread::t()->update($_G['tid'], ['icon' => $_G['cache']['stamps'][$_GET['stamp']]['icon']]);
		table_forum_threadhidelog::t()->delete_by_tid($_G['tid']);
	} elseif($modaction == 'SPD' && $_G['cache']['stamps'][$thread['stamp']]['icon'] == $thread['icon']) {
		table_forum_thread::t()->update($_G['tid'], ['icon' => -1]);
	}

	table_common_member_secwhite::t()->add($thread['authorid']);

	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$_G['tid']}&page=$page",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread', 'notictype' => 'post', 'item' => $_GET['stamp'] !== '' ? 'reason_stamp_update' : 'reason_stamp_delete'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'stamp' => $_G['cache']['stamps'][$stamp]['text']],
		'modaction' => $modaction,
		'modlog' => $thread
	];
	$modpostsnum = 1;

	updatemodlog($_G['tid'], $modaction, 0, 0, '', $modaction == 'SPA' ? $_GET['stamp'] : 0);

}

