<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_doing', 'class/block/space');

class block_doinghot extends block_doing {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'titlelength' => [
				'title' => 'doinglist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'startrow' => [
				'title' => 'doinglist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_doing_script_doinghot');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'replynum';
		return parent::cookparameter($parameter);
	}
}

