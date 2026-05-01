<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$subactives[$operation] = 'class="a"';
$loglist = [];
if($operation == 'uselog') {
	$count = table_common_magiclog::t()->count_by_uid_action($_G['uid'], 2);
	if($count) {
		$multipage = multi($count, $_G['tpp'], $page, 'home.php?mod=magic&action=log&amp;operation=uselog');

		$logs = table_common_magiclog::t()->fetch_all_by_uid_action($_G['uid'], 2, $start_limit, $_G['tpp']);
		$luids = [];
		foreach($luids as $log) {
			$luids[$log['uid']] = $log['uid'];
		}
		foreach($logs as $log) {
			$log['dateline'] = dgmdate($log['dateline'], 'u');
			$log['name'] = $magicarray[$log['magicid']]['name'];
			$loglist[] = $log;
		}
	}

} elseif($operation == 'buylog') {
	$count = table_common_magiclog::t()->count_by_uid_action($_G['uid'], 1);
	if($count) {
		$multipage = multi($count, $_G['tpp'], $page, 'home.php?mod=magic&action=log&amp;operation=buylog');

		foreach(table_common_magiclog::t()->fetch_all_by_uid_action($_G['uid'], 1, $start_limit, $_G['tpp']) as $log) {
			$log['credit'] = $log['credit'] ? $log['credit'] : $_G['setting']['creditstransextra'][3];
			$log['dateline'] = dgmdate($log['dateline'], 'u');
			$log['name'] = $magicarray[$log['magicid']]['name'];
			$loglist[] = $log;
		}
	}

} elseif($operation == 'givelog') {
	$count = table_common_magiclog::t()->count_by_uid_action($_G['uid'], 3);
	if($count) {
		$multipage = multi($count, $_G['tpp'], $page, 'home.php?mod=magic&action=log&amp;operation=givelog');

		$uids = null;
		$query = table_common_magiclog::t()->fetch_all_by_uid_action($_G['uid'], 3, $start_limit, $_G['tpp']);
		foreach($query as $log) {
			$uids[] = $log['targetuid'];
		}
		if($uids != null) {
			$memberdata = table_common_member::t()->fetch_all_username_by_uid($uids);
		}
		foreach($query as $log) {
			$log['username'] = $memberdata[$log['targetuid']];
			$log['dateline'] = dgmdate($log['dateline'], 'u');
			$log['name'] = $magicarray[$log['magicid']]['name'];
			$loglist[] = $log;
		}
	}

} elseif($operation == 'receivelog') {
	$count = table_common_magiclog::t()->count_by_targetuid_action($_G['uid'], 3);
	if($count) {
		$multipage = multi($count, $_G['tpp'], $page, 'home.php?mod=magic&action=log&amp;operation=receivelog');

		$logs = table_common_magiclog::t()->fetch_all_by_targetuid_action($_G['uid'], 3, $start_limit, $_G['tpp']);
		$luids = [];
		foreach($logs as $log) {
			$luids[$log['uid']] = $log['uid'];
		}
		$members = table_common_member::t()->fetch_all_username_by_uid($luids);

		foreach($logs as $log) {
			$log['username'] = $members[$log['uid']];
			$log['dateline'] = dgmdate($log['dateline'], 'u');
			$log['name'] = $magicarray[$log['magicid']]['name'];
			$loglist[] = $log;
		}
	}
}
$navtitle = lang('core', 'title_magics_log');
	