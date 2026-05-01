<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_member', 'class/block/member');

class block_memberspecial extends block_member {
	function __construct() {
		$this->setting = [
			'special' => [
				'title' => 'memberlist_special',
				'type' => 'mradio',
				'value' => [
					['', 'memberlist_special_nolimit'],
					['0', 'memberlist_special_hot'],
					['1', 'memberlist_special_default'],
				],
				'default' => ''
			],
			'startrow' => [
				'title' => 'memberlist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_member_script_memberspecial');
	}

	function cookparameter($parameter) {
		if($parameter['special'] === '') {
			$parameter['special'] = -1;
		} else {
			$parameter['special'] = intval($parameter['special']);
		}
		$parameter['orderby'] = 'special';
		return parent::cookparameter($parameter);
	}
}

