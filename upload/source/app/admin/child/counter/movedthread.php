<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&movedthreadsubmit=yes";
$processed = 0;

$tids = [];
$updateclosed = [];

foreach(table_forum_thread::t()->fetch_all_movedthread($current, $pertask) as $thread) {
	$processed = 1;
	if($thread['isgroup'] && $thread['status'] == 3) {
		$updateclosed[] = $thread['tid'];
	} elseif($thread['threadexists']) {
		$tids[] = $thread['tid'];
	}
}

if($tids) {
	table_forum_thread::t()->delete_by_tid($tids, true);
}
if($updateclosed) {
	table_forum_thread::t()->update($updateclosed, ['closed' => '']);
}

if($processed) {
	cpmsg(cplang('counter_moved_thread').': '.cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_moved_thread_succeed', 'action=counter', 'succeed');
}
	