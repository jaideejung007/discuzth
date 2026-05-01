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

class block_witframe extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_witframe');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'path' => [
				'title' => 'witframe_plugin_name',
				'type' => 'mradio',
				'value' => [],
			],
		];
		foreach(witframe_plugin::getApiByType('diy') as $path => $row) {
			$settings['path']['value'][] = [$path, $row['name']];
		}
		return $settings;
	}

	function getdata($style, $parameter) {
		$parameter = $this->cookparameter($parameter);
		if(empty($parameter['path'])) {
			return ['html' => '', 'data' => null];
		}
		$url = witframe_plugin::getApiUrl('diy', $parameter['path']);
		$html = '<iframe src="'.$url.'" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>';
		return ['html' => $html, 'data' => null];
	}
}

