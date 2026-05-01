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
foreach(table_forum_medal::t()->fetch_all_data(1) as $medal) {
	$medal['image'] = preg_match('/^https?:\/\//is', $medal['image']) ? $medal['image'] : STATICURL.'image/common/'.$medal['image'];
	$medallist[$medal['medalid']] = $medal;
}

$memberfieldforum = table_common_member_field_forum::t()->fetch($_G['uid']);
$membermedal = $memberfieldforum['medals'] ? explode("\t", $memberfieldforum['medals']) : [];
foreach($membermedal as $k => $medal) {
	if(!in_array($medal, array_keys($medallist))) {
		unset($membermedal[$k]);
	}
}
$medalcount = count($membermedal);

if(!empty($membermedal)) {
	$mymedal = [];
	foreach($membermedal as $medalid) {
		if($medalpos = strpos($medalid, '|')) {
			$medalid = substr($medalid, 0, $medalpos);
		}
		$mymedal['name'] = $_G['cache']['medals'][$medalid]['name'];
		$mymedal['image'] = $medallist[$medalid]['image'];
		$mymedals[] = $mymedal;
	}
}

$medallognum = table_forum_medallog::t()->count_by_uid($_G['uid']);
$multipage = multi($medallognum, $tpp, $page, 'home.php?mod=medal&action=log');

foreach(table_forum_medallog::t()->fetch_all_by_uid($_G['uid'], $start_limit, $tpp) as $medallog) {
	$medallog['name'] = $_G['cache']['medals'][$medallog['medalid']]['name'];
	$medallog['dateline'] = dgmdate($medallog['dateline']);
	$medallog['expiration'] = !empty($medallog['expiration']) ? dgmdate($medallog['expiration']) : '';
	$medallogs[] = $medallog;
}
	