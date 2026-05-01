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

class block_adv extends commonblock_html {

	function __construct() {
	}

	function name() {
		return lang('blockclass', 'blockclass_html_script_adv');
	}

	function getsetting() {
		global $_G;
		$settings = [
			'adv' => [
				'title' => 'adv_adv',
				'type' => 'mradio',
				'value' => [],
			],
			'title' => [
				'title' => 'adv_title',
				'type' => 'text',
			]
		];
		foreach(table_common_advertisement_custom::t()->fetch_all_data() as $value) {
			$settings['adv']['value'][] = [$value['name'], $value['name']];
		}
		return $settings;
	}

	function getdata($style, $parameter) {
		$advid = 0;
		if(!empty($parameter['title'])) {
			$adv = table_common_advertisement_custom::t()->fetch_by_name($parameter['title']);
			if(empty($adv)) {
				$advid = table_common_advertisement_custom::t()->insert(['name' => $parameter['title']], true);
			} else {
				$advid = $adv['id'];
			}
		} elseif(!empty($parameter['adv'])) {
			$adv = table_common_advertisement_custom::t()->fetch_by_name($parameter['adv']);
			$advid = intval($adv['id']);
		} else {
			$return = 'Empty Ads';
		}
		if($advid) {
			$flag = false;
			if(getglobal('inajax')) {
				$flag = true;
				setglobal('inajax', 0);
			}
			$return = adshow('custom_'.$advid);
			if($flag) setglobal('inajax', 1);
		}
		return ['html' => $return, 'data' => null];
	}
}

