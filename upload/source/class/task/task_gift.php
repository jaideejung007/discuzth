<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class task_gift {

	var $version = '1.0';
	var $name = 'gift_name';
	var $description = 'gift_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $icon = '';
	var $period = '';
	var $periodtype = 0;
	var $conditions = [];

	function preprocess($task) {
		dheader("Location: home.php?mod=task&do=draw&id={$task['taskid']}");
	}

	function csc($task = []) {
		return true;
	}

}


