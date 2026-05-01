<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_modreasons() {
	$settings = table_common_setting::t()->fetch_all_setting(['modreasons', 'userreasons']);
	foreach($settings as $key => $data) {
		$data = str_replace(["\r\n", "\r"], ["\n", "\n"], $data);
		$data = explode("\n", trim($data));
		savecache($key, $data);
	}
}

