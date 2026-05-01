<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('commonblock_html', 'class/block/html');

class block_blank extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_script_blank');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'content' => [
				'title' => 'blank_content',
				'type' => 'mtextarea'
			]
		];
		return $settings;
	}

	function getdata($style, $parameter) {
		require_once libfile('function/home');
		$return = getstr($parameter['content'], '', 1, 0, 0, 1);
		return ['html' => $return, 'data' => null];
	}
}

