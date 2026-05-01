<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!$_G['group']['allowremovereward']) {
	showmessage('no_privilege_removereward');
}

if(!submitcheck('modsubmit')) {
	include template('forum/topicadmin_action');
} else {
	if(!is_array($thread) || $thread['special'] != '3') {
		showmessage('reward_end');
	}

	$modaction = 'RMR';
	$reason = checkreasonpm();
	$log = table_common_credit_log::t()->fetch_by_operation_relatedid('RAC', $thread['tid']);
	$answererid = $log['uid'];
	if($thread['price'] < 0) {
		$thread['price'] = abs($thread['price']);
		updatemembercount($answererid, [$_G['setting']['creditstransextra'][2] => -$thread['price']]);
	}

	updatemembercount($thread['authorid'], [$_G['setting']['creditstransextra'][2] => $thread['price']]);
	table_forum_thread::t()->update($thread['tid'], ['special' => 0, 'price' => 0], true);

	table_common_credit_log::t()->delete_by_operation_relatedid(['RTC', 'RAC'], $thread['tid']);
	$resultarray = [
		'redirect' => "forum.php?mod=viewthread&tid={$thread['tid']}",
		'reasonpm' => ($sendreasonpm ? ['data' => [$thread], 'var' => 'thread', 'item' => 'reason_remove_reward', 'notictype' => 'post'] : []),
		'reasonvar' => ['tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'threadid' => $thread['tid']],
		'modtids' => $thread['tid']
	];
}

