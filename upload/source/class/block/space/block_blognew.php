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

class block_blognew extends block_blog {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'catid' => [
				'title' => 'bloglist_catid',
				'type' => 'mselect',
			],
			'picrequired' => [
				'title' => 'bloglist_picrequired',
				'type' => 'radio',
				'default' => '0'
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
			],
			'startrow' => [
				'title' => 'bloglist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_blog_script_blognew');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'dateline';
		return parent::cookparameter($parameter);
	}
}

