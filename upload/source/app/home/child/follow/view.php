<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$list = getfollowfeed($uid, 'self', false, $start, $perpage);
if(empty($list['feed'])) {
	$primary = 0;
	$list = getfollowfeed($uid, 'self', true, $start, $perpage);
	if(empty($list['user'])) {
		$archiver = 0;
	}
}
if(!isset($_G['cache']['forums'])) {
	loadcache('forums');
}
if(helper_access::check_module('follower')) {
	$followerlist = table_home_follow::t()->fetch_all_following_by_uid($uid, 0, 9);
}
list($seccodecheck, $secqaacheck) = seccheck('publish');
	