<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include libfile('function/forum');
$medalcredits = [];
foreach(table_forum_medal::t()->fetch_all_data(1) as $medal) {
	$medal['permission'] = medalformulaperm(serialize(['medal' => dunserialize($medal['permission'])]), $medal['type']);
	$medal['image'] = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
	if($medal['price']) {
		$medal['credit'] = $medal['credit'] ? $medal['credit'] : $_G['setting']['creditstransextra'][3];
		$medalcredits[$medal['credit']] = $medal['credit'];
	}
	$medallist[$medal['medalid']] = $medal;
}

$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
$membermedal = $memberfieldforum['medals'] ? explode("\t", $memberfieldforum['medals']) : [];
$membermedal = array_map('intval', $membermedal);

$lastmedals = $uids = [];
foreach(table_forum_medallog::t()->fetch_all_lastmedal(10) as $id => $lastmedal) {
	$lastmedal['dateline'] = dgmdate($lastmedal['dateline'], 'u');
	$lastmedals[$id] = $lastmedal;
	$uids[] = $lastmedal['uid'];
}
$lastmedalusers = table_common_member::t()->fetch_all($uids);
$mymedals = table_common_member_medal::t()->fetch_all_by_uid($_G['uid']);
$mymedals = array_keys($mymedals);
$applylogs = table_forum_medallog::t()->fetch_all_by_type(2);
foreach($applylogs as $id => $log) {
	$log['uid'] == $_G['uid'] && $mymedals[$log['medalid']] = $log['medalid'];
}
	