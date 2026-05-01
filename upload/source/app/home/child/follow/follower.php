<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$count = table_home_follow::t()->count_follow_user($uid, 1);
if($viewself && !empty($_G['member']['newprompt_num']['follower'])) {
	$newfollower = table_home_notification::t()->fetch_all_by_uid($uid, -1, 'follower', 0, $_G['member']['newprompt_num']['follower']);
	$newfollower_list = [];
	foreach($newfollower as $val) {
		$newfollower_list[] = $val['from_id'];
	}
	table_home_notification::t()->delete_by_type('follower', $_G['uid']);
	helper_notification::update_newprompt($_G['uid'], 'follower');
}
if($count) {
	$list = table_home_follow::t()->fetch_all_follower_by_uid($uid, $start, $perpage);
	$multi = multi($count, $perpage, $page, $theurl);
}
if(helper_access::check_module('follower')) {
	$followerlist = table_home_follow::t()->fetch_all_following_by_uid($uid, 0, 9);
}
$navactives = [$do => ' class="a"'];
	