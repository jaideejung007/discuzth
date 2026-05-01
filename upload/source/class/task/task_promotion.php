<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_promotion {

	var $version = '1.0';
	var $name = 'promotion_name';
	var $description = 'promotion_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [
		'num' => [
			'title' => 'promotion_complete_var_iplimit',
			'type' => 'text',
			'value' => '',
			'default' => 100,
			'sort' => 'complete',
		],
	];

	function preprocess($task) {
		global $_G;

		$promotions = table_forum_promotion::t()->count_by_uid($_G['uid']);
		table_forum_spacecache::t()->insert([
			'uid' => $_G['uid'],
			'variable' => 'promotion'.$task['taskid'],
			'value' => $promotions,
			'expiration' => $_G['timestamp'],
		], false, true);
	}

	function csc($task = []) {
		global $_G;

		$promotion = table_forum_spacecache::t()->fetch_spacecache($_G['uid'], 'promotion'.$task['taskid']);
		$promotion = $promotion['value'];
		$num = table_forum_promotion::t()->count_by_uid($_G['uid']) - $promotion;
		$numlimit = table_common_taskvar::t()->get_value_by_taskid($task['taskid'], 'num');
		if($num && $num >= $numlimit) {
			return TRUE;
		} else {
			return ['csc' => $num > 0 && $numlimit ? sprintf('%01.2f', $num / $numlimit * 100) : 0, 'remaintime' => 0];
		}
	}

	function sufprocess($task) {
		global $_G;

		table_forum_spacecache::t()->delete_spacecache($_G['uid'], 'promotion'.$task['taskid']);
	}

}

