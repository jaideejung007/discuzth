<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

@set_time_limit(0);
const IN_DEBUG = false;

const MAX_THREADS_MOVE = 100;

cpheader();
if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');
$topicperpage = 50;
if(empty($operation)) {
	$operation = 'manage';
}
$settings = table_common_setting::t()->fetch_all_setting(['threadtableids', 'threadtable_info'], true);
$threadtableids = $settings['threadtableids'] ? $settings['threadtableids'] : [];
$threadtable_info = $settings['threadtable_info'] ? $settings['threadtable_info'] : [];

$file = childfile('threadsplit/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function threadsplit_search_threads($conditions, $offset = null, $length = null, $onlycount = FALSE) {
	global $_G, $searchurladd, $page, $threadcount;
	if($conditions) {
		$conditions = daddslashes($conditions);
	}
	$sql = '';
	$threadlist = [];

	$sql = table_forum_thread::t()->search_condition($conditions, 't');
	$searchurladd = table_forum_thread::t()->get_url_param();
	if($sql || $conditions['sourcetableid']) {
		$conditions['isgroup'] = 0;
		$tableid = $conditions['sourcetableid'] ? $conditions['sourcetableid'] : 0;
		$threadcount = table_forum_thread::t()->count_search($conditions, $tableid, 't');
		if(isset($offset) && isset($length)) {
			$sql .= " LIMIT $offset, $length";
		}
		if($onlycount) {
			return $threadcount;
		}
		if($threadcount) {

			foreach(table_forum_thread::t()->fetch_all_search($conditions, $tableid, $offset, $length) as $thread) {
				$thread['lastpost'] = dgmdate($thread['lastpost']);
				$threadlist[] = $thread;
			}
		}
	}
	return $threadlist;
}

function update_threadtableids() {
	$threadtableids = table_forum_thread::t()->fetch_thread_table_ids();
	table_common_setting::t()->update_setting('threadtableids', $threadtableids);
	savecache('threadtableids', $threadtableids);
}

