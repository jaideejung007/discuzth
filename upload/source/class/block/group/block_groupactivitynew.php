<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_groupactivity', 'class/block/group');

class block_groupactivitynew extends block_groupactivity {
	function __construct() {
		$this->setting = [
			'gtids' => [
				'title' => 'groupactivity_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'class' => [
				'title' => 'groupactivity_class',
				'type' => 'select',
				'value' => []
			],
			'gviewperm' => [
				'title' => 'groupactivity_gviewperm',
				'type' => 'mradio',
				'value' => [
					['0', 'groupactivity_gviewperm_only_member'],
					['1', 'groupactivity_gviewperm_all_member']
				],
				'default' => '1'
			],
			'titlelength' => [
				'title' => 'groupactivity_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'groupactivity_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'startrow' => [
				'title' => 'groupactivity_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_groupactivity_script_groupactivitynew');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'dateline';
		return parent::cookparameter($parameter);
	}
}

