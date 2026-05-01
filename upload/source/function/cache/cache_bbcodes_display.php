<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_bbcodes_display() {
	$data = [];
	$i = 0;
	foreach(table_forum_bbcode::t()->fetch_all_by_available_icon(2, true) as $bbcode) {
		$bbcode['perm'] = explode("\t", $bbcode['perm']);
		if(in_array('', $bbcode['perm']) || !$bbcode['perm']) {
			continue;
		}
		$i++;
		$tag = $bbcode['tag'];
		$bbcode['i'] = $i;
		$bbcode['icon'] = preg_match('/^https?:\/\//is', $bbcode['icon']) ? $bbcode['icon'] : STATICURL.'image/common/'.$bbcode['icon'];
		$bbcode['explanation'] = dhtmlspecialchars(trim($bbcode['explanation']));
		$bbcode['prompt'] = addcslashes($bbcode['prompt'], '\\\'');
		unset($bbcode['tag']);
		foreach($bbcode['perm'] as $groupid) {
			$data[$groupid][$tag] = $bbcode;
		}
	}

	savecache('bbcodes_display', $data);
}

