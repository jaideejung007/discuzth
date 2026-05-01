<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_attachtype() {
	$attachtypes = table_forum_attachtype::t()->fetch_all_data();
	$data = [];
	foreach($attachtypes as $row) {
		$data[$row['fid']][strtolower($row['extension'])] = $row['maxsize'];
	}

	savecache('attachtype', $data);
}

