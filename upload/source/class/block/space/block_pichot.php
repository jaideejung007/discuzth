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

class block_pichot extends block_pic {
	function __construct() {
		$this->setting = [
			'hours' => [
				'title' => 'piclist_hours',
				'type' => 'mradio',
				'value' => [
					['', 'piclist_hours_nolimit'],
					['1', 'piclist_hours_hour'],
					['24', 'piclist_hours_day'],
					['168', 'piclist_hours_week'],
					['720', 'piclist_hours_month'],
					['8760', 'piclist_hours_year'],
				],
				'default' => '720'
			],
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
		return lang('blockclass', 'blockclass_pic_script_pichot');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'hot';
		return parent::cookparameter($parameter);
	}
}

