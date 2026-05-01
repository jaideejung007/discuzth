<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&forumsubmit=yes";
$processed = 0;

$queryf = table_forum_forum::t()->fetch_all_fids(1, '', '', $current, $pertask);
foreach($queryf as $forum) {
	$processed = 1;
	$threads = $posts = 0;
	$threadtables = ['0'];
	$archive = 0;
	foreach(table_forum_forum_threadtable::t()->fetch_all_by_fid($forum['fid']) as $data) {
		if($data['threadtableid']) {
			$threadtables[] = $data['threadtableid'];
		}
	}
	$threadtables = array_unique($threadtables);
	foreach($threadtables as $tableid) {
		$data = table_forum_thread::t()->count_posts_by_fid($forum['fid'], $tableid);
		$threads += $data['threads'];
		$posts += $data['posts'];
		if($data['threads'] == 0 && $tableid != 0) {
			table_forum_forum_threadtable::t()->delete_threadtable($forum['fid'], $tableid);
		}
		if($data['threads'] > 0 && $tableid != 0) {
			$archive = 1;
		}
	}
	table_forum_forum::t()->update($forum['fid'], ['archive' => $archive]);

	$thread = table_forum_thread::t()->fetch_by_fid_displayorder($forum['fid']);
	$subject = cutstr($thread['subject'], 80);
	$lastpost = "{$thread['tid']}\t{$subject}\t{$thread['lastpost']}\t{$thread['lastposter']}";

	table_forum_forum::t()->update($forum['fid'], ['threads' => $threads, 'posts' => $posts, 'lastpost' => $lastpost]);
}

if($processed) {
	cpmsg("{$lang['counter_forum']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	table_forum_forum::t()->clear_forum_counter_for_group();
	cpmsg('counter_forum_succeed', 'action=counter', 'succeed');
}
	