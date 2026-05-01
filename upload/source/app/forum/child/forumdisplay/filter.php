<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$filterarr = [];

if(!empty($setting['forum_fids'])) {
	$filterarr['inforum'] = $setting['forum_fids'];
}
if(!empty($setting['group_fids'])) {
	$filterarr['inforum'] = array_merge((array)$filterarr['inforum'], explode(',', $setting['group_fids']));
}
if(!empty($setting['authorids'])) {
	$filterarr['authorid'] = explode(',', $setting['authorids']);
	$filterarr['noanony'] = true;
}
if(!empty($setting['follow'])) {
	$followdata = table_home_follow::t()->fetch_all_following_by_uid($_G['uid']);
	$fuids = [];
	foreach($followdata as $follow) {
		$fuids[] = $follow['followuid'];
	}
	$filterarr['authorid'] = $fuids;
	$filterarr['noanony'] = true;
}
if(!empty($setting['digest'])) {
	$filterarr['digest'] = range(1, $setting['digest']);
}
if(!empty($setting['displayorder'])) {
	$filterarr['sticky'] = 4;
	$filterarr['displayorder'] = range(1, $setting['displayorder']);
} else {
	$filterarr['sticky'] = 0;
}
if(!empty($setting['special'])) {
	$filterarr['specialthread'] = 1;
	$filterarr['special'] = $setting['special'];
}
if(!empty($setting['heats'])) {
	$filterarr['heats'] = $setting['heats'];
}
if(!empty($setting['recommends'])) {
	$filterarr['recommends'] = $setting['recommends'];
}
if(!empty($setting['dateline'])) {
	$filterarr['starttime'] = dgmdate(TIMESTAMP - $setting['dateline']);
}
if(!empty($setting['laspost'])) {
	$filterarr['lastpostmore'] = TIMESTAMP - $setting['laspost'];
}


if(!empty($setting['order_before'])) {
	$field = '';
	switch($setting['order_before']) {
		case 1:
			$field = 'displayorder';
			break;
		case 2:
			$field = 'digest';
			break;
	}
	if($field) {
		$order = "$field DESC,";
	}
} else {
	$order = '';
}
if(!empty($setting['order'])) {
	$field = 'lastpost';
	switch($setting['order']) {
		case 1:
			$field = 'dateline';
			break;
		case 2:
			$field = 'replies';
			break;
		case 3:
			$field = 'views';
			break;
		case 4:
			$field = 'heats';
			break;
		case 5:
			$field = 'recommends';
			break;
	}
	$order .= "$field DESC";
} else {
	$order .= 'lastpost DESC';
}
