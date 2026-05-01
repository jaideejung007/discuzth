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

class block_membernew extends block_member {
	function __construct() {
		$this->setting = [
			'gender' => [
				'title' => 'memberlist_gender',
				'type' => 'mradio',
				'value' => [
					['1', 'memberlist_gender_male'],
					['2', 'memberlist_gender_female'],
					['', 'memberlist_gender_nolimit'],
				],
				'default' => ''
			],
			'birthcity' => [
				'title' => 'memberlist_birthcity',
				'type' => 'district',
				'value' => ['xbirthcountry', 'xbirthprovince', 'xbirthcity', 'xbirthdist', 'xbirthcommunity'],
			],
			'residecity' => [
				'title' => 'memberlist_residecity',
				'type' => 'district',
				'value' => ['xresidecountry', 'xresideprovince', 'xresidecity', 'xresidedist', 'xresidecommunity']
			],
			'avatarstatus' => [
				'title' => 'memberlist_avatarstatus',
				'type' => 'radio',
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
		return lang('blockclass', 'blockclass_member_script_membernew');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'regdate';
		return parent::cookparameter($parameter);
	}
}

