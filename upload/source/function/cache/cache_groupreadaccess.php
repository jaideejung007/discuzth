<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_groupreadaccess() {
	$data = [];
	$gf_data = table_common_usergroup_field::t()->fetch_readaccess_by_readaccess(0);
	$ug_data = table_common_usergroup::t()->fetch_all_usergroup(array_keys($gf_data));

	foreach($gf_data as $key => $datarow) {
		if(!$ug_data[$key]['groupid']) {
			continue;
		}
		$datarow['grouptitle'] = $ug_data[$key]['grouptitle'];
		$data[] = $datarow;
	}

	savecache('groupreadaccess', $data);
}

