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

class block_memberposts extends block_member {
	function __construct() {
		$this->setting = [
			'orderby' => [
				'title' => 'memberlist_orderby',
				'type' => 'mradio',
				'value' => [
					['threads', 'memberlist_orderby_threads'],
					['posts', 'memberlist_orderby_posts'],
					['digestposts', 'memberlist_orderby_digestposts'],
				],
				'default' => 'threads'
			],
			'lastpost' => [
				'title' => 'memberlist_lastpost',
				'type' => 'mradio',
				'value' => [
					['', 'memberlist_lastpost_nolimit'],
					['3600', 'memberlist_lastpost_hour'],
					['86400', 'memberlist_lastpost_day'],
					['604800', 'memberlist_lastpost_week'],
					['2592000', 'memberlist_lastpost_month'],
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
		return lang('blockclass', 'blockclass_member_script_memberposts');
	}
}

