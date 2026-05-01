<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_smileytypes() {
	$data = [];
	foreach(table_forum_imagetype::t()->fetch_all_by_type('smiley', 1) as $type) {
		$typeid = $type['typeid'];
		unset($type['typeid']);
		if(table_common_smiley::t()->count_by_type_code_typeid('smiley', $typeid)) {
			$data[$typeid] = $type;
		}
	}

	savecache('smileytypes', $data);
}

