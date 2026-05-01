<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_trade', 'class/block/forum');

class block_tradehot extends block_trade {
	function __construct() {
		$this->setting = [
			'fids' => [
				'title' => 'tradelist_fids',
				'type' => 'mselect',
				'value' => []
			],
			'viewmod' => [
				'title' => 'threadlist_viewmod',
				'type' => 'radio'
			],
			'orderby' => [
				'title' => 'tradelist_orderby',
				'type' => 'mradio',
				'value' => [
					['todayhots', 'tradelist_orderby_todayhots'],
					['weekhots', 'tradelist_orderby_weekhots'],
					['monthhots', 'tradelist_orderby_monthhots'],
				],
				'default' => 'weekhots'
			],
			'titlelength' => [
				'title' => 'tradelist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'tradelist_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'startrow' => [
				'title' => 'tradelist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_trade_script_tradehot');
	}
}

