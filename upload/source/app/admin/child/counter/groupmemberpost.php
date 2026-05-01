<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&groupmemberpost=yes";
$processed = 0;

$queryf = table_forum_forum::t()->fetch_all_fid_for_group($current, $pertask, 1);
foreach($queryf as $group) {
	$processed = 1;

	$mreplies_array = [];
	loadcache('posttableids');
	$posttables = empty($_G['cache']['posttableids']) ? [0] : $_G['cache']['posttableids'];
	foreach($posttables as $posttableid) {
		$mreplieslist = table_forum_post::t()->count_group_authorid_by_fid($posttableid, $group['fid']);
		if($mreplieslist) {
			foreach($mreplieslist as $mreplies) {
				$mreplies_array[$mreplies['authorid']] = $mreplies_array[$mreplies['authorid']] + $mreplies['num'];
			}
		}
	}
	unset($mreplieslist);
	foreach($mreplies_array as $authorid => $num) {
		table_forum_groupuser::t()->update_for_user($authorid, $group['fid'], null, $num);

	}
	foreach(table_forum_thread::t()->count_group_thread_by_fid($group['fid']) as $mthreads) {
		table_forum_groupuser::t()->update_for_user($mthreads['authorid'], $group['fid'], $mthreads['num']);
	}
}

if($processed) {
	cpmsg("{$lang['counter_groupmember_post']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_groupmember_post_succeed', 'action=counter', 'succeed');
}
	