<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_grouptrade', 'class/block/group');

class block_grouptradespecified extends block_grouptrade {
	function __construct() {
		$this->setting = [
			'tids' => [
				'title' => 'grouptrade_tids',
				'type' => 'text'
			],
			'uids' => [
				'title' => 'grouptrade_uids',
				'type' => 'text'
			],
			'keyword' => [
				'title' => 'grouptrade_keyword',
				'type' => 'text'
			],
			'fids' => [
				'title' => 'grouptrade_fids',
				'type' => 'text'
			],
			'gtids' => [
				'title' => 'grouptrade_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'titlelength' => [
				'title' => 'grouptrade_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'grouptrade_summarylength',
				'type' => 'text',
				'default' => 80
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_grouptrade_script_grouptradespecified');
	}
}

