<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_globalstick() {
	$data = [];
	$query = table_forum_forum::t()->fetch_all_valid_forum();
	$fuparray = $threadarray = [];
	foreach($query as $forum) {
		switch($forum['type']) {
			case 'forum':
				$fuparray[$forum['fid']] = $forum['fup'];
				break;
			case 'sub':
				$fuparray[$forum['fid']] = $fuparray[$forum['fup']];
				break;
		}
	}
	foreach(table_forum_thread::t()->fetch_all_by_displayorder([2, 3]) as $thread) {
		switch($thread['displayorder']) {
			case 2:
				$threadarray[$fuparray[$thread['fid']]][] = $thread['tid'];
				break;
			case 3:
				$threadarray['global'][] = $thread['tid'];
				break;
		}
	}
	foreach(array_unique($fuparray) as $gid) {
		if(!empty($threadarray[$gid])) {
			$data['categories'][$gid] = [
				'tids' => dimplode($threadarray[$gid]),
				'count' => intval(is_array($threadarray[$gid]) ? count($threadarray[$gid]) : 0)
			];
		}
	}
	$data['global'] = [
		'tids' => empty($threadarray['global']) ? '' : dimplode($threadarray['global']),
		'count' => intval(is_array($threadarray['global']) ? count($threadarray['global']) : 0)
	];

	savecache('globalstick', $data);
}

