<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_medals() {
	$data = [];
	foreach(table_forum_medal::t()->fetch_all_data(1) as $medal) {
		$medal['image'] = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
		$data[$medal['medalid']] = ['name' => $medal['name'], 'image' => $medal['image'], 'description' => dhtmlspecialchars($medal['description'])];
	}

	savecache('medals', $data);
}

