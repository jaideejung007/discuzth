<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_articlelist {

	var $version = '1.0';
	var $name = 'articlelist_name';
	var $description = 'articlelist_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $targets = ['portal'];
	var $imagesizes = ['658x40', '658x60'];

	function getsetting() {
		$settings = [
			'position' => [
				'title' => 'articlelist_position',
				'type' => 'mradio',
				'value' => [
					[1, 'articlelist_position_up1'],
					[2, 'articlelist_position_up2'],
					[3, 'articlelist_position_down1'],
					[4, 'articlelist_position_down2'],
				],
				'default' => 1,
			],
			'category' => [
				'title' => 'articlelist_category',
				'type' => 'mselect',
				'value' => [],
			],
		];
		loadcache('portalcategory');
		$this->getcategory(0);
		$settings['category']['value'] = $this->categoryvalue;
		return $settings;
	}

	function getcategory($upid) {
		global $_G;
		foreach($_G['cache']['portalcategory'] as $category) {
			if($category['upid'] == $upid) {
				$this->categoryvalue[] = [$category['catid'], str_repeat('&nbsp;', $category['level'] * 4).$category['catname']];
				$this->getcategory($category['catid']);
			}
		}
	}

	function setsetting(&$advnew, &$parameters) {
		global $_G;
		if(is_array($advnew['targets'])) {
			$advnew['targets'] = implode("\t", $advnew['targets']);
		}
		if(is_array($parameters['extra']['category']) && in_array(0, $parameters['extra']['category'])) {
			$parameters['extra']['category'] = [];
		}
	}

	function evalcode() {
		return [
			'check' => '
			$checked = $params[2] == $parameter[\'position\'] && (!$parameter[\'category\'] || $parameter[\'category\'] && in_array($_G[\'catid\'], $parameter[\'category\']));
			',
			'create' => '$adcode = $codes[$adids[array_rand($adids)]];',
		];
	}

}

