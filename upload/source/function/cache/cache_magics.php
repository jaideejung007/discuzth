<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_magics() {
	$data = [];
	foreach(table_common_magic::t()->fetch_all_data(1) as $magic) {
		$data[$magic['magicid']] = $magic;
	}

	savecache('magics', $data);
}

