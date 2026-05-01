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

class block_grouptradehot extends block_grouptrade {
	function __construct() {
		$this->setting = [
			'gtids' => [
				'title' => 'grouptrade_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'orderby' => [
				'title' => 'grouptrade_orderby',
				'type' => 'mradio',
				'value' => [
					['todayhots', 'grouptrade_orderby_todayhots'],
					['weekhots', 'grouptrade_orderby_weekhots'],
					['monthhots', 'grouptrade_orderby_monthhots'],
				],
				'default' => 'weekhots'
			],
			'gviewperm' => [
				'title' => 'grouptrade_gviewperm',
				'type' => 'mradio',
				'value' => [
					['0', 'grouptrade_gviewperm_only_member'],
					['1', 'grouptrade_gviewperm_all_member']
				],
				'default' => '1'
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
			'startrow' => [
				'title' => 'grouptrade_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_grouptrade_script_grouptradehot');
	}
}

