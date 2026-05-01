<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_group', 'class/block/group');

class block_groupnew extends block_group {

	function __construct() {
		$this->setting = [
			'gtids' => [
				'title' => 'grouplist_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'titlelength' => [
				'title' => 'grouplist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'grouplist_summarylength',
				'type' => 'text',
				'default' => 80
			]
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_group_script_groupnew');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'dateline';
		return parent::cookparameter($parameter);
	}

}

