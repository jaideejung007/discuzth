<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_click() {
	$data = $keys = [];
	foreach(table_home_click::t()->fetch_all_by_available() as $value) {
		$value['icon'] = preg_match('/^https?:\/\//is', $value['icon']) ? $value['icon'] : STATICURL.'image/click/'.$value['icon'];
		if(!isset($data[$value['idtype']]) || count($data[$value['idtype']]) < 8) {
			$keys[$value['idtype']] = $keys[$value['idtype']] ? ++$keys[$value['idtype']] : 1;
			$data[$value['idtype']][$keys[$value['idtype']]] = $value;
		}
	}

	savecache('click', $data);
}

