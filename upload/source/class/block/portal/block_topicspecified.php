<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_topic', 'class/block/portal');

class block_topicspecified extends block_topic {
	function __construct() {
		$this->setting = [
			'topicids' => [
				'title' => 'topiclist_topicids',
				'type' => 'text',
				'value' => ''
			],
			'uids' => [
				'title' => 'topiclist_uids',
				'type' => 'text',
				'value' => ''
			],
			'titlelength' => [
				'title' => 'topiclist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'summary' => 'topiclist_summarylength',
				'type' => 'text',
				'default' => 80
			]
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_topic_script_topicspecified');
	}
}

