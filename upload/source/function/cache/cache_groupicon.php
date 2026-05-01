<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_groupicon() {
	$data = [];
	foreach(table_forum_onlinelist::t()->fetch_all_order_by_displayorder() as $list) {
		if($list['url']) {
			$data[$list['groupid']] = preg_match('/^https?:\/\//is', $list['url']) ? $list['url'] : STATICURL.'image/common/'.$list['url'];
		}
	}

	savecache('groupicon', $data);
}

