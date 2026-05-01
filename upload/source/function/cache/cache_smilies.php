<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_smilies() {
	$data = [];

	$data = ['searcharray' => [], 'replacearray' => [], 'typearray' => []];
	foreach(table_common_smiley::t()->fetch_all_cache() as $smiley) {
		if(empty($smiley['url'])) {
			continue;
		}
		$data['searcharray'][$smiley['id']] = '/'.preg_quote(dhtmlspecialchars($smiley['code']), '/').'/';
		$data['replacearray'][$smiley['id']] = $smiley['url'];
		$data['typearray'][$smiley['id']] = $smiley['typeid'];
	}

	savecache('smilies', $data);
}

