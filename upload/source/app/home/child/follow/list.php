<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$uids = array_keys($list);
$fieldhome = table_common_member_field_home::t()->fetch_all($uids);
foreach($fieldhome as $fuid => $val) {
	$list[$fuid]['recentnote'] = $val['recentnote'];
}
$memberinfo = table_common_member_count::t()->fetch_all($uids);
$memberprofile = table_common_member_profile::t()->fetch_all($uids);

if(!$viewself) {
	$myfollow = table_home_follow::t()->fetch_all_by_uid_followuid($_G['uid'], $uids);
	foreach($uids as $muid) {
		$list[$muid]['mutual'] = 0;
		if(!empty($myfollow[$muid])) {
			$list[$muid]['mutual'] = $myfollow[$muid]['mutual'] ? 1 : -1;
		}

	}
}
$specialfollow = table_home_follow::t()->fetch_all_following_by_uid($uid, 1, 10);
	