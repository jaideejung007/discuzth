<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_stamps() {
	$data = [];

	$fillarray = range(0, 99);
	$count = 0;
	$repeats = $stampicon = [];
	foreach(table_common_smiley::t()->fetch_all_by_type(['stamp', 'stamplist']) as $stamp) {
		if(isset($fillarray[$stamp['displayorder']])) {
			unset($fillarray[$stamp['displayorder']]);
		} else {
			$repeats[] = $stamp['id'];
		}
		$count++;
	}
	foreach($repeats as $id) {
		reset($fillarray);
		$displayorder = current($fillarray);
		unset($fillarray[$displayorder]);
		table_common_smiley::t()->update($id, ['displayorder' => $displayorder]);
	}
	foreach(table_common_smiley::t()->fetch_all_by_type('stamplist') as $stamp) {
		if($stamp['typeid'] < 1) {
			continue;
		}
		$row = table_common_smiley::t()->fetch_by_id_type($stamp['typeid'], 'stamp');
		$stampicon[$row['displayorder']] = $stamp['displayorder'];
	}
	foreach(table_common_smiley::t()->fetch_all_by_type(['stamp', 'stamplist']) as $stamp) {
		$icon = $stamp['type'] == 'stamp' ? ($stampicon[$stamp['displayorder']] ?? 0) :
			($stamp['type'] == 'stamplist' && !in_array($stamp['displayorder'], $stampicon) ? 1 : 0);
		$data[$stamp['displayorder']] = ['url' => $stamp['url'], 'text' => $stamp['code'], 'type' => $stamp['type'], 'icon' => $icon];
	}

	savecache('stamps', $data);
}

