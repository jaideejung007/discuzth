<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_article', 'class/block/portal');

class block_articlehot extends block_article {
	function __construct() {
		$this->setting = [
			'catid' => [
				'title' => 'articlelist_catid',
				'type' => 'mselect',
				'value' => [],
			],
			'picrequired' => [
				'title' => 'articlelist_picrequired',
				'type' => 'radio',
				'default' => '0'
			],
			'orderby' => [
				'title' => 'articlelist_orderby',
				'type' => 'mradio',
				'value' => [
					['viewnum', 'articlelist_orderby_viewnum'],
					['commentnum', 'articlelist_orderby_commentnum'],
				],
				'default' => 'viewnum'
			],
			'titlelength' => [
				'title' => 'articlelist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'articlelist_summarylength',
				'type' => 'text',
				'default' => 80
			]
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_article_script_articlehot');
	}
}

