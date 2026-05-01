<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$step = intval($_GET['step']);
$continue = false;
if(isset($threadtableids[$step])) {
	$continue = true;
}
if($continue) {
	$threadtableid = $threadtableids[$step];
	table_forum_forum_threadtable::t()->update_by_threadtableid($threadtableid, ['threads' => '0', 'posts' => '0']);
	$threadtable = $threadtableid ? $threadtableid : 0;
	foreach(table_forum_thread::t()->count_group_by_fid($threadtable) as $row) {
		table_forum_forum_threadtable::t()->insert([
			'fid' => $row['fid'],
			'threadtableid' => $threadtableid,
			'threads' => $row['threads'],
			'posts' => $row['posts'],
		], false, true);
		if($row['threads'] > 0) {
			table_forum_forum::t()->update($row['fid'], ['archive' => '1']);
		}
	}
	$nextstep = $step + 1;
	cpmsg('threadsplit_manage_forum_processing', "action=threadsplit&operation=forumarchive&step=$nextstep", 'loading', ['table' => DB::table($threadtable)]);
} else {
	table_forum_forum_threadtable::t()->delete_none_threads();
	$fids = ['0'];
	foreach(table_forum_forum_threadtable::t()->range() as $row) {
		$fids[] = $row['fid'];
	}
	table_forum_forum::t()->update_archive($fids);
	cpmsg('threadsplit_manage_forum_complete', 'action=threadsplit&operation=manage', 'succeed');
}
	