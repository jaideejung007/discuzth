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

class block_bloghot extends block_blog {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'hours' => [
				'title' => 'bloglist_hours',
				'type' => 'mradio',
				'value' => [
					['', 'bloglist_hours_nolimit'],
					['1', 'bloglist_hours_hour'],
					['24', 'bloglist_hours_day'],
					['168', 'bloglist_hours_week'],
					['720', 'bloglist_hours_month'],
					['8760', 'bloglist_hours_year'],
				],
				'default' => '720'
			],
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
		return lang('blockclass', 'blockclass_blog_script_bloghot');
	}

	function cookparameter($parameter) {
		$parameter['orderby'] = 'hot';
		return parent::cookparameter($parameter);
	}
}

