<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_grouplevels() {
	$data = [];
	$query = table_forum_grouplevel::t()->range();
	foreach($query as $level) {
		$level['creditspolicy'] = dunserialize($level['creditspolicy']);
		$level['postpolicy'] = dunserialize($level['postpolicy']);
		$level['specialswitch'] = dunserialize($level['specialswitch']);
		$data[$level['levelid']] = $level;
	}

	savecache('grouplevels', $data);
}

