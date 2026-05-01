<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_blog', 'class/block/space');

class block_blogspecified extends block_blog {
	function __construct() {
		$this->setting = [
			'blogids' => [
				'title' => 'bloglist_blogids',
				'type' => 'text'
			],
			'uids' => [
				'title' => 'bloglist_uids',
				'type' => 'text',
			],
			'catid' => [
				'title' => 'bloglist_catid',
				'type' => 'mselect',
			],
			'titlelength' => [
				'title' => 'bloglist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'bloglist_summarylength',
				'type' => 'text',
				'default' => 80
			]
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_blog_script_blogspecified');
	}

}

