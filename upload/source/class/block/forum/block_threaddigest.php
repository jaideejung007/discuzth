<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_thread', 'class/block/forum');

class block_threaddigest extends block_thread {

	function __construct() {
		$this->setting = [
			'fids' => [
				'title' => 'threadlist_fids',
				'type' => 'mselect',
				'value' => []
			],
			'digest' => [
				'title' => 'threadlist_digest',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'threadlist_digest_1'],
					[2, 'threadlist_digest_2'],
					[3, 'threadlist_digest_3'],
					[0, 'threadlist_digest_0']
				],
			],
			'special' => [
				'title' => 'threadlist_special',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'threadlist_special_1'],
					[2, 'threadlist_special_2'],
					[3, 'threadlist_special_3'],
					[4, 'threadlist_special_4'],
					[5, 'threadlist_special_5'],
					[0, 'threadlist_special_0'],
				],
				'default' => ['0']
			],
			'viewmod' => [
				'title' => 'threadlist_viewmod',
				'type' => 'radio'
			],
			'rewardstatus' => [
				'title' => 'threadlist_special_reward',
				'type' => 'mradio',
				'value' => [
					[0, 'threadlist_special_reward_0'],
					[1, 'threadlist_special_reward_1'],
					[2, 'threadlist_special_reward_2']
				],
				'default' => 0,
			],
			'picrequired' => [
				'title' => 'threadlist_picrequired',
				'type' => 'radio',
				'value' => '0'
			],
			'titlelength' => [
				'title' => 'threadlist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'threadlist_summarylength',
				'type' => 'text',
				'default' => 80
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_thread_script_threaddigest');
	}
}

