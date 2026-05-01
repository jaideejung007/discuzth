<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&threadsubmit=yes";
$processed = 0;

foreach(table_forum_thread::t()->fetch_all_by_displayorder(0, '>=', $current, $pertask) as $threads) {
	$processed = 1;
	$replynum = table_forum_post::t()->count_visiblepost_by_tid($threads['tid']);
	$replynum--;
	$lastpost = table_forum_post::t()->fetch_visiblepost_by_tid('tid:'.$threads['tid'], $threads['tid'], 0, 1);
	if($threads['replies'] != $replynum || $threads['lastpost'] != $lastpost['dateline'] || $threads['lastposter'] != $lastpost['author']) {
		if(empty($threads['author'])) {
			$lastpost['author'] = '';
		}
		$updatedata = [
			'replies' => $replynum,
			'lastpost' => $lastpost['dateline'],
			'lastposter' => $lastpost['author']
		];
		table_forum_thread::t()->update($threads['tid'], $updatedata, true, true);
	}
}

if($processed) {
	cpmsg("{$lang['counter_thread']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_thread_succeed', 'action=counter', 'succeed');
}
	