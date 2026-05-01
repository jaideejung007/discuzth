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

class block_groupthreadspecified extends block_groupthread {
	function __construct() {
		$this->setting = [
			'tids' => [
				'title' => 'groupthread_tids',
				'type' => 'text'
			],
			'fids' => [
				'title' => 'groupthread_fids',
				'type' => 'text'
			],
			'uids' => [
				'title' => 'groupthread_uids',
				'type' => 'text'
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
		return lang('blockclass', 'blockclass_groupthread_script_groupthreadspecified');
	}
}

