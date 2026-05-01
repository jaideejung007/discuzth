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

class block_banner extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_script_banner');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'pic' => [
				'title' => 'banner_pic',
				'type' => 'mfile',
				'default' => 'http://'
			],
			'url' => [
				'title' => 'banner_url',
				'type' => 'text',
				'default' => ''
			],
			'atarget' => [
				'title' => 'banner_atarget',
				'type' => 'select',
				'value' => [
					['_blank', 'banner_atarget_blank'],
					['_self', 'banner_atarget_self'],
					['_top', 'banner_atarget_top'],
				],
				'default' => '_blank'
			],
			'width' => [
				'title' => 'banner_width',
				'type' => 'text',
				'default' => '100%'
			],
			'height' => [
				'title' => 'banner_height',
				'type' => 'text',
				'default' => ''
			],
			'text' => [
				'title' => 'banner_text',
				'type' => 'textarea',
				'default' => ''
			],
		];

		return $settings;
	}

	function getdata($style, $parameter) {
		$parameter = dhtmlspecialchars($this->cookparameter($parameter));
		$return = '<img src="'.$parameter['pic'].'"'
			.($parameter['width'] ? ' width="'.$parameter['width'].'"' : '')
			.($parameter['height'] ? ' height="'.$parameter['height'].'"' : '')
			.($parameter['text'] ? ' alt="'.$parameter['text'].'" title="'.$parameter['text'].'"' : '')
			.' />';
		if($parameter['url']) {
			$target = $parameter['atarget'] ? " target=\"{$parameter['atarget']}\"" : '';
			$return = "<a href=\"{$parameter['url']}\"$target>$return</a>";
		}
		return ['html' => $return, 'data' => null];
	}

}

