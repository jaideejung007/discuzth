<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$medalnewarray = $medalsnew = $uids = [];


foreach(table_forum_medallog::t()->fetch_all_by_expiration(TIMESTAMP) as $medalnew) {
	$uids[] = $medalnew['uid'];
	$medalnews[] = $medalnew;
}

$membermedals = [];
foreach(table_common_member_field_forum::t()->fetch_all($uids) as $member) {
	$membermedals[$member['uid']] = $member['medals'];
}

foreach($medalnews as $medalnew) {
	$medalnew['medals'] = empty($medalnewarray[$medalnew['uid']]) ? explode("\t", $membermedals[$medalnew['uid']]) : explode("\t", $medalnewarray[$medalnew['uid']]);

	foreach($medalnew['medals'] as $key => $medalnewid) {
		list($medalid, $medalexpiration) = explode('|', $medalnewid);
		if($medalnew['medalid'] == $medalid) {
			unset($medalnew['medals'][$key]);
		}
	}

	$medalnewarray[$medalnew['uid']] = implode("\t", $medalnew['medals']);
	table_forum_medallog::t()->update($medalnew['id'], ['status' => 0]);
	table_common_member_field_forum::t()->update($medalnew['uid'], ['medals' => $medalnewarray[$medalnew['uid']]], 'UNBUFFERED');
	table_common_member_medal::t()->delete_by_uid_medalid($medalnew['uid'], $medalnew['medalid']);
}
