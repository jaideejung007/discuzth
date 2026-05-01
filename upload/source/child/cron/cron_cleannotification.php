<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

table_home_notification::t()->delete_clear(0, 2);
table_home_notification::t()->delete_clear(1, 30);

$deltime = $_G['timestamp'] - 7 * 3600 * 24;
table_home_pokearchive::t()->delete_by_dateline($deltime);

table_home_notification::t()->optimize();

