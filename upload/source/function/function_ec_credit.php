<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function updatecreditcache($uid, $type, $return = 0) {
	$all = countcredit($uid, $type);
	$halfyear = countcredit($uid, $type, 180);
	$thismonth = countcredit($uid, $type, 30);
	$thisweek = countcredit($uid, $type, 7);
	$before = [
		'good' => $all['good'] - $halfyear['good'],
		'soso' => $all['soso'] - $halfyear['soso'],
		'bad' => $all['bad'] - $halfyear['bad'],
		'total' => $all['total'] - $halfyear['total']
	];

	$data = ['all' => $all, 'before' => $before, 'halfyear' => $halfyear, 'thismonth' => $thismonth, 'thisweek' => $thisweek];

	table_forum_spacecache::t()->insert([
		'uid' => $uid,
		'variable' => $type,
		'value' => serialize($data),
		'expiration' => getexpiration(),
	], false, true);
	if($return) {
		return $data;
	}
}

function countcredit($uid, $type, $days = 0) {
	$type = $type == 'sellercredit' ? 1 : 0;
	$good = $soso = $bad = 0;
	foreach(table_forum_tradecomment::t()->fetch_all_by_rateeid($uid, $type, $days ? TIMESTAMP - $days * 86400 : 0) as $credit) {
		if($credit['score'] == 1) {
			$good++;
		} elseif($credit['score'] == 0) {
			$soso++;
		} else {
			$bad++;
		}
	}
	return ['good' => $good, 'soso' => $soso, 'bad' => $bad, 'total' => $good + $soso + $bad];
}

function updateusercredit($uid, $type, $level) {
	$uid = intval($uid);
	if(!$uid || !in_array($type, ['buyercredit', 'sellercredit']) || !in_array($level, ['good', 'soso', 'bad'])) {
		return;
	}

	if($cache = table_forum_spacecache::t()->fetch_spacecache($uid, $type)) {
		$expiration = $cache['expiration'];
		$cache = dunserialize($cache['value']);
	} else {
		$init = ['good' => 0, 'soso' => 0, 'bad' => 0, 'total' => 0];
		$cache = ['all' => $init, 'before' => $init, 'halfyear' => $init, 'thismonth' => $init, 'thisweek' => $init];
		$expiration = getexpiration();
	}

	foreach(['all', 'halfyear', 'thismonth', 'thisweek'] as $key) {
		$cache[$key][$level]++;
		$cache[$key]['total']++;
	}

	table_forum_spacecache::t()->insert([
		'uid' => $uid,
		'variable' => $type,
		'value' => serialize($cache),
		'expiration' => $expiration,
	], false, true);

	$score = $level == 'good' ? 1 : ($level == 'soso' ? 0 : -1);
	table_common_member_status::t()->increase($uid, [$type => $score]);
}

