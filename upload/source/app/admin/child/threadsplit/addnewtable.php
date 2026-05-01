<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(empty($threadtableids)) {
	$maxtableid = 0;
} else {
	$maxtableid = max($threadtableids);
}

table_forum_thread::t()->create_table($maxtableid + 1);

update_threadtableids();
updatecache('setting');
cpmsg('threadsplit_table_create_succeed', 'action=threadsplit&operation=manage', 'succeed');
	