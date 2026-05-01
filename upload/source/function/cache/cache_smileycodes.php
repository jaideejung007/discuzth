<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_smileycodes() {
	$data = [];
	foreach(table_forum_imagetype::t()->fetch_all_by_type('smiley', 1) as $type) {
		foreach(table_common_smiley::t()->fetch_all_by_type_code_typeid('smiley', $type['typeid']) as $smiley) {
			if($size = @getimagesize('./static/image/smiley/'.$type['directory'].'/'.$smiley['url'])) {
				$data[$smiley['id']] = $smiley['code'];
			}
		}
	}

	savecache('smileycodes', $data);
}

