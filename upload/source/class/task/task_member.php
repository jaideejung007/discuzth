<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_member {

	var $version = '1.0';
	var $name = 'member_name';
	var $description = 'member_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [
		'act' => [
			'title' => 'member_complete_var_act',
			'type' => 'mradio',
			'value' => [
				['favorite', 'member_complete_var_act_favorite'],
				['magic', 'member_complete_var_act_magic'],
			],
			'default' => 'favorite',
			'sort' => 'complete',
		],
		'num' => [
			'title' => 'member_complete_var_num',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		],
		'time' => [
			'title' => 'member_complete_var_time',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		]
	];

	function preprocess($task) {
		global $_G;

		$act = table_common_taskvar::t()->get_value_by_taskid($task['taskid'], 'act');
		if($act == 'favorite') {
			$value = table_home_favorite::t()->count_by_uid_idtype($_G['uid'], 'tid');
			table_forum_spacecache::t()->insert([
				'uid' => $_G['uid'],
				'variable' => 'favorite'.$task['taskid'],
				'value' => $value,
				'expiration' => $_G['timestamp'],
			], false, true);
		}
	}

	function csc($task = []) {
		global $_G;

		$taskvars = ['num' => 0];
		$num = 0;
		foreach(table_common_taskvar::t()->fetch_all_by_taskid($task['taskid']) as $taskvar) {
			if($taskvar['value']) {
				$taskvars[$taskvar['variable']] = $taskvar['value'];
			}
		}

		$taskvars['time'] = floatval($taskvars['time']);
		if($taskvars['act'] == 'favorite') {
			$favorite = table_forum_spacecache::t()->fetch_spacecache($_G['uid'], 'favorite'.$task['taskid']);
			$favorite = $favorite['value'];
			$num = table_home_favorite::t()->count_by_uid_idtype($_G['uid'], 'tid') - $favorite;
		} elseif($taskvars['act'] == 'magic') {
			$maxtime = $taskvars['time'] ? $task['applytime'] + 3600 * $taskvars['time'] : 0;
			$num = table_common_magiclog::t()->count_by_action_uid_dateline(2, $_G['uid'], $task['applytime'], $maxtime);
		}

		if($num && $num >= $taskvars['num']) {
			if($taskvars['act'] == 'favorite') {
				table_forum_spacecache::t()->delete_spacecache($_G['uid'], $taskvars['act'].$task['taskid']);
			}
			return TRUE;
		} elseif($taskvars['time'] && TIMESTAMP >= $task['applytime'] + 3600 * $taskvars['time'] && (!$num || $num < $taskvars['num'])) {
			return FALSE;
		} else {
			return ['csc' => $num > 0 && $taskvars['num'] ? sprintf('%01.2f', $num / $taskvars['num'] * 100) : 0, 'remaintime' => $taskvars['time'] ? $task['applytime'] + $taskvars['time'] * 3600 - TIMESTAMP : 0];
		}
	}

	function view($task, $taskvars) {
		$return = lang('task/member', 'task_complete_time_start');
		if($taskvars['complete']['time']) {
			$return .= lang('task/member', 'task_complete_time_limit', ['value' => $taskvars['complete']['time']['value']]);
		}
		$taskvars['complete']['num']['value'] = intval($taskvars['complete']['num']['value']);
		if($taskvars['complete']['act']['value'] == 'favorite') {
			$return .= lang('task/member', 'task_complete_act_favorite', ['value' => $taskvars['complete']['num']['value']]);
		} else {
			$return .= lang('task/member', 'task_complete_act_magic', ['value' => $taskvars['complete']['num']['value']]);
		}
		return $return;
	}

}


