<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
const IN_DEBUG = false;

@set_time_limit(0);
const MAX_POSTS_MOVE = 100000;
cpheader();
if(!isfounder()) cpmsg('noaccess_isfounder', '', 'error');
$topicperpage = 50;

if(empty($operation)) {
	$operation = 'manage';
}

$setting = table_common_setting::t()->fetch_all_setting(['posttable_info', 'posttableids', 'threadtableids'], true);
if($setting['posttable_info']) {
	$posttable_info = $setting['posttable_info'];
} else {
	$posttable_info = [];
	$posttable_info[0]['type'] = 'primary';
}
$posttableids = $setting['posttableids'] ? $setting['posttableids'] : [];
$threadtableids = $setting['threadtableids'];

$file = childfile('postsplit/'.$operation);
if(!file_exists($file)) {
	cpmsg('undefined_action');
}

require_once $file;

function gettableid($tablename) {
	$tableid = substr($tablename, strrpos($tablename, '_') + 1);
	return $tableid;
}

function getmaxposttableid() {
	$maxtableid = 0;
	foreach(table_forum_post::t()->show_table() as $table) {
		$tablename = current($table);
		$tableid = intval(gettableid($tablename));
		if($tableid > $maxtableid) {
			$maxtableid = $tableid;
		}
	}
	return $maxtableid;
}

function update_posttableids() {
	$tableids = get_posttableids();
	table_common_setting::t()->update_setting('posttableids', $tableids);
	savecache('posttableids', $tableids);
}

function get_posttableids() {
	$tableids = [0];
	foreach(table_forum_post::t()->show_table() as $table) {
		$tablename = current($table);
		$tableid = gettableid($tablename);
		if(!preg_match('/^\d+$/', $tableid)) {
			continue;
		}
		$tableid = intval($tableid);
		if(!$tableid) {
			continue;
		}
		$tableids[] = $tableid;
	}
	return $tableids;
}


function gettablefields($table) {
	static $tables = [];

	if(!isset($tables[$table])) {
		$tables[$table] = table_forum_post::t()->show_table_columns($table);
	}
	return $tables[$table];
}

function movedate($tids) {
	global $sourcesize, $tableid, $movesize, $targettableid, $hash, $tableindex, $threadtableids, $fieldstr, $fromtableid, $posttable_info;

	$fromtable = getposttable($fromtableid, true);
	table_forum_post::t()->move_table($targettableid, $fieldstr, $fromtable, $tids);
	if(DB::errno()) {
		table_forum_post::t()->delete_by_tid($targettableid, $tids);
	} else {
		foreach($threadtableids as $threadtableid) {
			$affected_rows = table_forum_thread::t()->update($tids, ['posttableid' => $targettableid], false, false, $threadtableid);
			if($affected_rows == count($tids)) {
				break;
			}
		}
		table_forum_post::t()->delete_by_tid($fromtableid, $tids);
	}
	$status = helper_dbtool::gettablestatus(getposttable($targettableid, true), false);
	$targetsize = $sourcesize + $movesize * 1048576;
	$nowdatasize = $targetsize - $status['Data_length'];

	if($status['Data_length'] >= $targetsize) {
		cpmsg('postsplit_done', 'action=postsplit&operation=optimize&tableid='.$fromtableid, 'form');
	}

	cpmsg('postsplit_doing', 'action=postsplit&operation=movepost&fromtable='.$tableid.'&movesize='.$movesize.'&targettable='.$targettableid.'&hash='.$hash.'&tindex='.$tableindex, 'loadingform', ['datalength' => sizecount($status['Data_length']), 'nowdatalength' => sizecount($nowdatasize)]);
}

