<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_grouptype() {
	$data = [];
	$query = table_forum_forum::t()->fetch_all_group_type(1);
	$data['second'] = $data['first'] = [];
	foreach($query as $group) {
		if($group['type'] == 'forum') {
			$data['second'][$group['fid']] = $group;
		} else {
			$data['first'][$group['fid']] = $group;
		}
	}
	foreach($data['second'] as $fid => $secondgroup) {
		$data['first'][$secondgroup['fup']]['groupnum'] += $secondgroup['groupnum'];
		$data['first'][$secondgroup['fup']]['secondlist'][] = $secondgroup['fid'];
	}
	savecache('grouptype', $data);
}

