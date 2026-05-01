<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_post {

	var $version = '1.0';
	var $name = 'post_name';
	var $description = 'post_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [
		'act' => [
			'title' => 'post_complete_var_act',
			'type' => 'mradio',
			'value' => [
				['newthread', 'post_complete_var_act_newthread'],
				['newreply', 'post_complete_var_act_newreply'],
				['newpost', 'post_complete_var_act_newpost'],
			],
			'default' => 'newthread',
			'sort' => 'complete',
		],
		'forumid' => [
			'title' => 'post_complate_var_forumid',
			'type' => 'select',
			'value' => [],
			'sort' => 'complete',
		],
		'threadid' => [
			'title' => 'post_complate_var_threadid',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		],
		'num' => [
			'title' => 'post_complete_var_num',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		],
		'time' => [
			'title' => 'post_complete_var_time',
			'type' => 'text',
			'value' => '',
			'sort' => 'complete',
		]
	];

	function __construct() {
		global $_G;
		loadcache('forums');
		$this->conditions['forumid']['value'][] = [0, '&nbsp;'];
		if(empty($_G['cache']['forums'])) $_G['cache']['forums'] = [];
		foreach($_G['cache']['forums'] as $fid => $forum) {
			$this->conditions['forumid']['value'][] = [$fid, ($forum['type'] == 'forum' ? str_repeat('&nbsp;', 4) : ($forum['type'] == 'sub' ? str_repeat('&nbsp;', 8) : '')).$forum['name'], $forum['type'] == 'group' ? 1 : 0];
		}
	}

	function csc($task = []) {
		global $_G;

		$taskvars = ['num' => 0];
		foreach(table_common_taskvar::t()->fetch_all_by_taskid($task['taskid']) as $taskvar) {
			if($taskvar['value']) {
				$taskvars[$taskvar['variable']] = $taskvar['value'];
			}
		}
		$taskvars['num'] = $taskvars['num'] ? $taskvars['num'] : 1;

		$tbladd = $sqladd = '';
		if($taskvars['act'] == 'newreply' && $taskvars['threadid']) {
			$threadid = $taskvars['threadid'];
		} else {
			if($taskvars['forumid']) {
				$forumid = $taskvars['forumid'];
			}
			if($taskvars['author']) {
				return TRUE;
			}
		}
		if($taskvars['act']) {
			if($taskvars['act'] == 'newthread') {
				$first = '1';
			} elseif($taskvars['act'] == 'newreply') {
				$first = '0';
			}
		}

		$starttime = $task['applytime'];
		if($taskvars['time'] = floatval($taskvars['time'])) {
			$endtime = $task['applytime'] + 3600 * $taskvars['time'];
		}

		$num = table_forum_post::t()->count_by_search(0, $threadid, null, 0, $forumid, $_G['uid'], null, $starttime, $endtime, null, $first);

		if($num && $num >= $taskvars['num']) {
			return TRUE;
		} elseif($taskvars['time'] && TIMESTAMP >= $task['applytime'] + 3600 * $taskvars['time'] && (!$num || $num < $taskvars['num'])) {
			return FALSE;
		} else {
			return ['csc' => $num > 0 && $taskvars['num'] ? sprintf('%01.2f', $num / $taskvars['num'] * 100) : 0, 'remaintime' => $taskvars['time'] ? $task['applytime'] + $taskvars['time'] * 3600 - TIMESTAMP : 0];
		}
	}

	function view($task, $taskvars) {
		global $_G;
		$return = $value = '';
		if(!empty($taskvars['complete']['forumid'])) {
			$value = intval($taskvars['complete']['forumid']['value']);
			loadcache('forums');
			$value = '<a href="forum.php?mod=forumdisplay&fid='.$value.'"><strong>'.$_G['cache']['forums'][$value]['name'].'</strong></a>';
		} elseif(!empty($taskvars['complete']['threadid'])) {
			$value = intval($taskvars['complete']['threadid']['value']);
			$thread = table_forum_thread::t()->fetch_thread($value);
			$value = '<a href="forum.php?mod=viewthread&tid='.$value.'"><strong>'.($thread['subject'] ? $thread['subject'] : 'TID '.$value).'</strong></a>';
		} elseif(!empty($taskvars['complete']['author'])) {
			$value = $taskvars['complete']['author']['value'];
			$authorid = table_common_member::t()->fetch_uid_by_username($value);
			$value = '<a href="home.php?mod=space&uid='.$authorid.'"><strong>'.$value.'</strong></a>';
		}
		$taskvars['complete']['num']['value'] = intval($taskvars['complete']['num']['value']);
		$taskvars['complete']['num']['value'] = $taskvars['complete']['num']['value'] ? $taskvars['complete']['num']['value'] : 1;
		if($taskvars['complete']['act']['value'] == 'newreply') {
			if($taskvars['complete']['threadid']) {
				$return .= lang('task/post', 'task_complete_act_newreply_thread', ['value' => $value, 'num' => $taskvars['complete']['num']['value']]);
			} else {
				$return .= lang('task/post', 'task_complete_act_newreply_author', ['value' => $value, 'num' => $taskvars['complete']['num']['value']]);
			}
		} else {
			if($taskvars['complete']['forumid']) {
				$return .= lang('task/post', 'task_complete_forumid', ['value' => $value]);
			}
			if($taskvars['complete']['act']['value'] == 'newthread') {
				$return .= lang('task/post', 'task_complete_act_newthread', ['num' => $taskvars['complete']['num']['value']]);
			} else {
				$return .= lang('task/post', 'task_complete_act_newpost', ['num' => $taskvars['complete']['num']['value']]);
			}
		}
		return $return;
	}

	function sufprocess($task) {
	}

}

