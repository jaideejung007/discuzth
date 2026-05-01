<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_pic', 'class/block/space');

class block_picnew extends block_pic {
	function __construct() {
		$this->setting = [
			'titlelength' => [
				'title' => 'piclist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'startrow' => [
				'title' => 'piclist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_pic_script_picnew');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'dateline';
		return parent::cookparameter($parameter);
	}
}

