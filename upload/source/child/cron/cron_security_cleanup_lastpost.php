<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$queryf = table_forum_forum::t()->fetch_all_fids();
foreach($queryf as $forum) {
	$thread = table_forum_thread::t()->fetch_by_fid_displayorder($forum['fid']);
	$thread['shortsubject'] = cutstr($thread['subject'], 80);
	$lastpost = "{$thread['tid']}\t{$thread['shortsubject']}\t{$thread['lastpost']}\t{$thread['lastposter']}";

	table_forum_forum::t()->update($forum['fid'], ['lastpost' => $lastpost]);
}
