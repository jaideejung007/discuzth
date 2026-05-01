<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$count = table_home_follow::t()->count_follow_user($uid);
if($count) {
	$status = $_GET['status'] ? 1 : 0;
	$list = table_home_follow::t()->fetch_all_following_by_uid($uid, $status, $start, $perpage);
	$multi = multi($count, $perpage, $page, $theurl);
}
if(helper_access::check_module('follower')) {
	$followerlist = table_home_follow::t()->fetch_all_follower_by_uid($uid, 9);
}
$navactives = [$do => ' class="a"'];
	