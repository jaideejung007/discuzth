<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_stamptypeid() {
	$data = [];

	foreach(table_common_smiley::t()->fetch_all_by_type('stamp') as $stamp) {
		if($stamp['typeid'] < 0) {
			continue;
		}
		$data[$stamp['typeid']] = $stamp['displayorder'];
	}

	savecache('stamptypeid', $data);
}

