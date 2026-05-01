<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&groupnum=yes";
$processed = 0;

$queryf = table_forum_forum::t()->fetch_all_fid_for_group($current, $pertask);
foreach($queryf as $group) {
	$processed = 1;
	$groupnum = table_forum_forum::t()->fetch_groupnum_by_fup($group['fid']);
	table_forum_forumfield::t()->update($group['fid'], ['groupnum' => intval($groupnum)]);
}

if($processed) {
	cpmsg("{$lang['counter_groupnum']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	updatecache('grouptype');
	cpmsg('counter_groupnum_succeed', 'action=counter', 'succeed');
}
	