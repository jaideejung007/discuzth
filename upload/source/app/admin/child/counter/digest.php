<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$current) {
	table_common_member_count::t()->clear_digestposts();
	$current = 0;
}
$nextlink = "action=counter&current=$next&pertask=$pertask&digestsubmit=yes";
$processed = 0;
$membersarray = $postsarray = [];

foreach(table_forum_thread::t()->fetch_all_by_digest_displayorder(0, '<>', 0, '>=', $current, $pertask) as $thread) {
	$processed = 1;
	$membersarray[$thread['authorid']]++;
}
$threadtableids = table_common_setting::t()->fetch_setting('threadtableids', true);
foreach($threadtableids as $tableid) {
	if(!$tableid) {
		continue;
	}
	foreach(table_forum_thread::t()->fetch_all_by_digest_displayorder(0, '<>', 0, '>=', $current, $pertask, $tableid) as $thread) {
		$processed = 1;
		$membersarray[$thread['authorid']]++;
	}
}

foreach($membersarray as $uid => $posts) {
	$postsarray[$posts][] = $uid;
}
unset($membersarray);

foreach($postsarray as $posts => $uids) {
	table_common_member_count::t()->increase($uids, ['digestposts' => $posts]);
}

if($processed) {
	cpmsg("{$lang['counter_digest']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_digest_succeed', 'action=counter', 'succeed');
}
	