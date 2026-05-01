<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$removetime = TIMESTAMP - $_G['setting']['followretainday'] * 86400;

foreach(table_home_follow_feed::t()->fetch_all_by_dateline($removetime, '<=') as $feed) {
	table_home_follow_feed::t()->insert_archiver($feed);
	table_home_follow_feed::t()->delete($feed['feedid']);
}

