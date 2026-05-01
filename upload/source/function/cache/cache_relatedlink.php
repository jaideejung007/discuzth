<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_relatedlink() {
	global $_G;

	$data = [];
	$query = table_common_relatedlink::t()->range();
	foreach($query as $link) {
		if(!preg_match('/^https?:\/\//is', $link['url'])) {
			$link['url'] = 'http://'.$link['url'];
		}
		$data[] = $link;
	}
	savecache('relatedlink', $data);
}

