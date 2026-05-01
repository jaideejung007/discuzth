<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_announcements_forum() {
	$data = [];

	$data = table_forum_announcement::t()->fetch_by_displayorder(TIMESTAMP);
	if($data) {
		$uid = table_common_member::t()->fetch_uid_by_username($data['author']);
		$data['authorid'] = $uid;
		$data['authorid'] = intval($data['authorid']);
		if(empty($data['type'])) {
			unset($data['message']);
		}
	} else {
		$data = [];
	}
	savecache('announcements_forum', $data);
}

