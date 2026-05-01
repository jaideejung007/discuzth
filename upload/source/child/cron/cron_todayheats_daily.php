<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$yesterday = strtotime(dgmdate(TIMESTAMP, 'Y-m-d')) - 86400;
$data = $tids = $fids = $hotnum = [];
$daystr = dgmdate($yesterday, 'Ymd');
foreach(table_forum_thread::t()->fetch_all_for_guide('hot', 0, [], $_G['setting']['heatthread']['guidelimit'], $yesterday, 0, 0) as $thread) {
	$data[$thread['tid']] = [
		'cid' => 0,
		'fid' => $thread['fid'],
		'tid' => $thread['tid']
	];
	$fids[$thread['fid']] = ['fid' => $thread['fid'], 'dateline' => $daystr, 'hotnum' => 0];
	$tids[$thread['fid']][$thread['tid']] = $thread['tid'];
}
if($data) {
	$cids = table_forum_threadcalendar::t()->fetch_all_by_fid_dateline(array_keys($fids), $daystr);
	foreach($cids as $fid => $cinfo) {
		$hotnum[$cinfo['cid']] = count($tids[$fid]);
		foreach($tids[$fid] as $tid) {
			$data[$tid]['cid'] = $cinfo['cid'];
		}
		unset($fids[$fid]);
	}
	if($fids) {
		table_forum_threadcalendar::t()->insert_multiterm($fids);
		foreach(table_forum_threadcalendar::t()->fetch_all_by_fid_dateline(array_keys($fids), $daystr) as $fid => $cinfo) {
			$hotnum[$cinfo['cid']] = count($tids[$fid]);
			foreach($tids[$fid] as $tid) {
				$data[$tid]['cid'] = $cinfo['cid'];
			}
		}
	}
	table_forum_threadhot::t()->insert_multiterm($data);
	foreach($hotnum as $cid => $num) {
		table_forum_threadcalendar::t()->update($cid, ['hotnum' => $num]);
	}
}

