<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_blog {

	var $version = '1.0';
	var $name = 'blog_name';
	var $description = 'blog_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [];

	function csc($task = []) {
		global $_G;

		if(table_home_blog::t()->count_by_uid($_G['uid'])) {
			return true;
		}
		return ['csc' => 0, 'remaintime' => 0];
	}

	function view() {
		return lang('task/blog', 'blog_view');
	}

}

