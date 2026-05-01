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

class block_google extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_script_google');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'lang' => [
				'title' => 'google_lang',
				'type' => 'mradio',
				'value' => [
					['', 'google_lang_any'],
					['en', 'google_lang_en'],
					['zh-CN', 'google_lang_zh-CN'],
					['zh-TW', 'google_lang_zh-TW']
				]
			],
			'default' => [
				'title' => 'google_default',
				'type' => 'mradio',
				'value' => [
					[0, 'google_default_0'],
					[1, 'google_default_1'],
				]
			],
			'client' => [
				'title' => 'google_client',
				'type' => 'text',
			],
		];

		return $settings;
	}

	function getdata($style, $parameter) {
		$parameter = dhtmlspecialchars($this->cookparameter($parameter));
		$return = '<script type="text/javascript">var google_host="'.$_SERVER['HTTP_HOST'].'",google_charset="'.CHARSET.'",google_client="'.$parameter['client'].'",google_hl="'.$parameter['lang'].'",google_lr="'.($parameter['lang'] ? 'lang_'.$parameter['lang'] : '').'";google_default_0="'.($parameter['default'] == 0 ? ' selected' : '').'";google_default_1="'.($parameter['default'] == 1 ? ' selected' : '').'";</script><script type="text/javascript" src="'.STATICURL.'js/google.js"></script>';
		return ['html' => $return, 'data' => null];
	}

}

