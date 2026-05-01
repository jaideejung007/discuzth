<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$nextlink = "action=counter&current=$next&pertask=$pertask&groupmembernum=yes";
$processed = 0;

$query = table_forum_forum::t()->fetch_all_fid_for_group($current, $pertask, 1);
foreach($query as $group) {
	$processed = 1;
	$membernum = table_forum_groupuser::t()->fetch_count_by_fid($group['fid']);
	table_forum_forumfield::t()->update($group['fid'], ['membernum' => $membernum]);
}

if($processed) {
	cpmsg("{$lang['counter_groupmember_num']}: ".cplang('counter_processing', ['current' => $current, 'next' => $next]), $nextlink, 'loading');
} else {
	cpmsg('counter_groupmember_num_succeed', 'action=counter', 'succeed');
}
	