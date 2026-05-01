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

class block_vedio extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_script_vedio');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'url' => [
				'title' => 'vedio_url',
				'type' => 'text',
				'default' => 'http://'
			],
			'width' => [
				'title' => 'vedio_width',
				'type' => 'text',
				'default' => ''
			],
			'height' => [
				'title' => 'vedio_height',
				'type' => 'text',
				'default' => ''
			],
		];

		return $settings;
	}

	function getdata($style, $parameter) {
		require_once libfile('function/discuzcode');
		$parameter['width'] = !empty($parameter['width']) ? intval($parameter['width']) : 'auto';
		$parameter['height'] = !empty($parameter['height']) ? intval($parameter['height']) : 'auto';
		$parameter['url'] = addslashes($parameter['url']);
		$return = parseflv($parameter['url'], $parameter['width'], $parameter['height']);
		if(!$return) {
			$return = $parameter['url'];
		}
		return ['html' => $return, 'data' => null];
	}
}

