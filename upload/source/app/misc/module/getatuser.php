<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$result = '';
$search_str = getgpc('search_str');
if($_G['uid']) {
	$atlist = $atlist_cookie = [];
	$limit = 200;
	if(getcookie('atlist')) {
		$cookies = explode(',', $_G['cookie']['atlist']);
		foreach(table_common_member::t()->fetch_all($cookies, false) as $row) {
			if($row['uid'] != $_G['uid'] && in_array($row['uid'], $cookies)) {
				$atlist_cookie[$row['uid']] = $row['username'];
			}
		}
	}
	foreach(table_home_follow::t()->fetch_all_following_by_uid($_G['uid'], 0, 0, $limit) as $row) {
		if($atlist_cookie[$row['followuid']]) {
			continue;
		}
		$atlist[$row['followuid']] = $row['fusername'];
	}
	$num = count($atlist);
	if($num < $limit) {
		$query = table_home_friend::t()->fetch_all_by_uid($_G['uid'], 0, $limit * 2);
		foreach($query as $row) {
			if(count($atlist) == $limit) {
				break;
			}
			if($atlist_cookie[$row['fuid']]) {
				continue;
			}
			$atlist[$row['fuid']] = $row['fusername'];
		}
	}
	$result = implode(',', $atlist_cookie).($atlist_cookie && $atlist ? ',' : '').implode(',', $atlist);
}
include template('common/getatuser');
