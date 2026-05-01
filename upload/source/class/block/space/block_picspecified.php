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

class block_picspecified extends block_pic {
	function __construct() {
		$this->setting = [
			'picids' => [
				'title' => 'piclist_picids',
				'type' => 'text',
				'value' => ''
			],
			'uids' => [
				'title' => 'piclist_uids',
				'type' => 'text',
				'value' => ''
			],
			'aids' => [
				'title' => 'piclist_aids',
				'type' => 'text',
				'value' => ''
			],
			'titlelength' => [
				'title' => 'piclist_titlelength',
				'type' => 'text',
				'default' => 40
			]
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_pic_script_picspecified');
	}
}

