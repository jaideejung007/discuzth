<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_albumcategory() {
	$data = [];
	$query = table_home_album_category::t()->fetch_all_by_displayorder();

	foreach($query as $value) {
		$value['catname'] = dhtmlspecialchars($value['catname']);
		$data[$value['catid']] = $value;
	}
	foreach($data as $key => $value) {
		$upid = $value['upid'];
		$data[$key]['level'] = 0;
		if($upid && isset($data[$upid])) {
			$data[$upid]['children'][] = $key;
			while($upid && isset($data[$upid])) {
				$data[$key]['level'] += 1;
				$upid = $data[$upid]['upid'];
			}
		}
	}

	savecache('albumcategory', $data);
}

