<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_avatar {

	var $version = '1.0';
	var $name = 'avatar_name';
	var $description = 'avatar_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [];

	function csc($task = []) {
		global $_G;

		if(!empty($_G['member']['avatarstatus'])) {
			return true;
		} else {
			return ['csc' => 0, 'remaintime' => 0];
		}
	}

	function view() {
		return lang('task/avatar', 'avatar_view');
	}

}

