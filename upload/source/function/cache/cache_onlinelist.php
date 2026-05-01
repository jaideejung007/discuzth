<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_onlinelist() {
	$data = [];
	$data['legend'] = '';
	foreach(table_forum_onlinelist::t()->fetch_all_order_by_displayorder() as $list) {
		if(!$list['url']) {
			continue;
		}
		$url = preg_match('/^https?:\/\//is', $list['url']) ? $list['url'] : STATICURL.'image/common/'.$list['url'];
		$data[$list['groupid']] = $url;
		$data['legend'] .= !empty($url) ? "<img src=\"".$url."\" /> {$list['title']} &nbsp; &nbsp; &nbsp; " : '';
		if($list['groupid'] == 7) {
			$data['guest'] = $list['title'];
		}
	}

	savecache('onlinelist', $data);
}

