<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


$delnum = table_forum_announcement::t()->delete_all_by_endtime($_G['timestamp']);

if($delnum) {
	require_once libfile('function/cache');
	updatecache(['announcements', 'announcements_forum']);
}

