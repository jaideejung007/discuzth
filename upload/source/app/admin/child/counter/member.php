<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&membersubmit=yes";
$processed = 0;

$threadtableids = table_common_setting::t()->fetch_setting('threadtableids', true);
$queryt = table_common_member::t()->range($current, $pertask);
foreach($queryt as $mem) {
	$processed = 1;
	$postcount = 0;
	loadcache('posttable_info');
	if(!empty($_G['cache']['posttable_info']) && is_array($_G['cache']['posttable_info'])) {
		foreach($_G['cache']['posttable_info'] as $key => $value) {
			$postcount += table_forum_post::t()->count_by_authorid($key, $mem['uid']);
		}
	} else {
		$postcount += table_forum_post::t()->count_by_authorid(0, $mem['uid']);
	}
	$postcount += table_forum_postcomment::t()->count_by_authorid($mem['uid']);
	$threadcount = table_forum_thread::t()->count_by_authorid($mem['uid']);
	foreach($threadtableids as $tableid) {
		if(!$tableid) {
			continue;
		}
		$threadcount += table_forum_thread::t()->count_by_authorid($mem['uid'], $tableid);
	}
	table_common_member_count::t()->update($mem['uid'], ['posts' => $postcount, 'threads' => $threadcount]);
}

if($processed) {
	cpmsg("{$lang['counter_member']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_member_succeed', 'action=counter', 'succeed');
}
	