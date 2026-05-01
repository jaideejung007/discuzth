<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

if(!$_G['setting']['bbclosed']) {
	cpmsg('postsplit_forum_must_be_closed', 'action=postsplit&operation=manage', 'error');
}
list($tableid, $movesize, $targettableid, $sourcesize) = explode("\t", urldecode(authcode($_GET['hash'])));
$hash = urlencode($_GET['hash']);

if($tableid == $_GET['fromtable'] && $movesize == $_GET['movesize'] && $targettableid == $_GET['targettable']) {
	$fromtableid = intval($_GET['fromtable']);
	$movesize = intval($_GET['movesize']);
	$targettableid = intval($_GET['targettable']);

	$targettable = gettablefields(getposttable($targettableid));
	$fieldstr = '`'.implode('`, `', array_keys($targettable)).'`';

	loadcache('threadtableids');
	$threadtableids = [0];
	if(!empty($_G['cache']['threadtableids'])) {
		$threadtableids = array_merge($threadtableids, $_G['cache']['threadtableids']);
	}
	$tableindex = intval(!empty($_GET['tindex']) ? $_GET['tindex'] : 0);
	if(isset($threadtableids[$tableindex])) {

		if(!$fromtableid) {
			$threadtableid = $threadtableids[$tableindex];

			$count = table_forum_thread::t()->count_by_posttableid_displayorder($threadtableid);
			if($count) {
				$tids = [];
				foreach(table_forum_thread::t()->fetch_all_by_posttableid_displayorder($threadtableid) as $tid => $thread) {
					$tids[$tid] = $tid;
				}
				movedate($tids);
			}
			if($tableindex + 1 < count($threadtableids)) {
				$tableindex++;
				$status = helper_dbtool::gettablestatus(getposttable($targettableid, true), false);
				$targetsize = $sourcesize + $movesize * 1048576;
				$nowdatasize = $targetsize - $status['Data_length'];

				cpmsg('postsplit_doing', 'action=postsplit&operation=movepost&fromtable='.$tableid.'&movesize='.$movesize.'&targettable='.$targettableid.'&hash='.$hash.'&tindex='.$tableindex, 'loadingform', ['datalength' => sizecount($status['Data_length']), 'nowdatalength' => sizecount($nowdatasize)]);
			}

		} else {
			$count = table_forum_post::t()->count_by_first($fromtableid, 1);
			if($count) {
				$threads = table_forum_post::t()->fetch_all_tid_by_first($fromtableid, 1, 0, 1000);
				$tids = [];
				foreach($threads as $thread) {
					$tids[$thread['tid']] = $thread['tid'];
				}
				movedate($tids);
			} else {
				cpmsg('postsplit_done', 'action=postsplit&operation=optimize&tableid='.$fromtableid, 'form');
			}

		}
	}


} else {
	cpmsg('postsplit_abnormal', 'action=postsplit', 'succeed');
}
	