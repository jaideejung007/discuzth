<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($filter !== 'hot') {
	$threadlist = [];
	$indexadd = '';
	$_order = "displayorder DESC, {$_GET['orderby']} {$_GET['ascdesc']}";
	if($filterbool) {
		if(!empty($filterarr['digest'])) {
			$indexadd = ' FORCE INDEX (digest) ';
		}
	} elseif($showsticky && is_array($stickytids) && !empty($stickytids[0])) {
		$filterarr1 = $filterarr;
		$filterarr1['inforum'] = '';
		$filterarr1['intids'] = $stickytids;
		$filterarr1['displayorder'] = [2, 3, 4];
		$threadlist = table_forum_thread::t()->fetch_all_search($filterarr1, $tableid, $start_limit, $_G['tpp'], $_order, '');
		unset($filterarr1);
	}

	if(!$indexadd) {
		if(!empty($filterarr['intype'])) {
			if(!in_array($_GET['orderby'], ['dateline', 'replies', 'views', 'recommends', 'heats'])) {
				$indexadd = ' FORCE INDEX (typeid) ';
			} else {
				$indexadd = ' FORCE INDEX (typeid_'.$_GET['orderby'].') ';
			}
		} elseif(in_array($_GET['orderby'], ['dateline', 'replies', 'views', 'recommends', 'heats'])) {
			$indexadd = ' FORCE INDEX (displayorder_'.$_GET['orderby'].') ';
		}
	}
	$threadlist = array_merge($threadlist, table_forum_thread::t()->fetch_all_search($filterarr, $tableid, $start_limit, $_G['tpp'], $_order, '', $indexadd));
	unset($_order);

	if(empty($threadlist) && $page <= ceil($_G['forum_threadcount'] / $_G['tpp'])) {
		require_once libfile('function/post');
		updateforumcount($_G['fid']);
	}
} else {
	$hottime = dintval(str_replace('-', '', getgpc('time')));
	$multipage = '';
	if($hottime && checkdate(substr($hottime, 4, 2), substr($hottime, 6, 2), substr($hottime, 0, 4))) {
		$calendartime = abs($hottime);
		$ctime = sprintf('%04d', substr($hottime, 0, 4)).'-'.sprintf('%02d', substr($hottime, 4, 2)).'-'.sprintf('%02d', substr($hottime, 6, 2));
	} else {
		$calendartime = dgmdate(strtotime(dgmdate(TIMESTAMP, 'Y-m-d')) - 86400, 'Ymd');
		$ctime = dgmdate(strtotime(dgmdate(TIMESTAMP, 'Y-m-d')) - 86400, 'Y-m-d');
	}
	$caldata = table_forum_threadcalendar::t()->fetch_by_fid_dateline($_G['fid'], $calendartime);
	$_G['forum_threadcount'] = 0;
	if($caldata) {
		$hottids = table_forum_threadhot::t()->fetch_all_tid_by_cid($caldata['cid']);
		$threadlist = table_forum_thread::t()->fetch_all_by_tid($hottids);
		$_G['forum_threadcount'] = count($threadlist);
	}
}
