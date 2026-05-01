<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_groupthread', 'class/block/group');

class block_groupthreadhot extends block_groupthread {
	function __construct() {
		$this->setting = [
			'gtids' => [
				'title' => 'groupthread_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'special' => [
				'title' => 'groupthread_special',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'groupthread_special_1'],
					[2, 'groupthread_special_2'],
					[3, 'groupthread_special_3'],
					[4, 'groupthread_special_4'],
					[5, 'groupthread_special_5'],
					[0, 'groupthread_special_0'],
				]
			],
			'rewardstatus' => [
				'title' => 'groupthread_special_reward',
				'type' => 'mradio',
				'value' => [
					[0, 'groupthread_special_reward_0'],
					[1, 'groupthread_special_reward_1'],
					[2, 'groupthread_special_reward_2']
				],
				'default' => 0,
			],
			'picrequired' => [
				'title' => 'groupthread_picrequired',
				'type' => 'radio',
				'value' => '0'
			],
			'orderby' => [
				'title' => 'groupthread_orderby',
				'type' => 'mradio',
				'value' => [
					['replies', 'groupthread_orderby_replies'],
					['views', 'groupthread_orderby_views'],
					['heats', 'groupthread_orderby_heats'],
					['recommends', 'groupthread_orderby_recommends'],
				],
				'default' => 'replies'
			],
			'lastpost' => [
				'title' => 'groupthread_lastpost',
				'type' => 'mradio',
				'value' => [
					['0', 'groupthread_lastpost_nolimit'],
					['3600', 'groupthread_lastpost_hour'],
					['86400', 'groupthread_lastpost_day'],
					['604800', 'groupthread_lastpost_week'],
					['2592000', 'groupthread_lastpost_month'],
				],
				'default' => '0'
			],
			'gviewperm' => [
				'title' => 'groupthread_gviewperm',
				'type' => 'mradio',
				'value' => [
					['0', 'groupthread_gviewperm_only_member'],
					['1', 'groupthread_gviewperm_all_member']
				],
				'default' => '1'
			],
			'titlelength' => [
				'title' => 'groupthread_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'groupthread_summarylength',
				'type' => 'text',
				'default' => 80
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_groupthread_script_groupthreadhot');
	}
}

