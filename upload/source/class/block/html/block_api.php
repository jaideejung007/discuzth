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

class block_api extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_script_api');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'url' => [
				'title' => 'api_url',
				'type' => 'text',
			],
		];
		return $settings;
	}

	function getdata($style, $parameter) {
		$parameter = $this->cookparameter($parameter);
		if(empty($parameter['url'])) {
			return ['html' => '', 'data' => null];
		}
		$url = parse_url($parameter['url']);
		if(empty($url['scheme']) || empty($url['host'])) {
			return ['html' => '', 'data' => null];
		}
		$html = dfsockopen($parameter['url']);
		return ['html' => $html, 'data' => null];
	}
}

