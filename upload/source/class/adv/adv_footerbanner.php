<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_footerbanner {

	var $version = '1.0';
	var $name = 'footerbanner_name';
	var $description = 'footerbanner_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $targets = ['portal', 'home', 'member', 'forum', 'group', 'search', 'plugin', 'custom'];
	var $imagesizes = ['468x60', '658x60', '728x90', '760x90', '950x90', '950x130'];

	function getsetting() {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'footerbanner_fids',
				'type' => 'mselect',
				'value' => [],
			],
			'groups' => [
				'title' => 'footerbanner_groups',
				'type' => 'mselect',
				'value' => [],
			],
			'category' => [
				'title' => 'footerbanner_category',
				'type' => 'mselect',
				'value' => [],
			],
			'position' => [
				'title' => 'footerbanner_position',
				'type' => 'mradio',
				'value' => [
					[1, 'footerbanner_position_up'],
					[2, 'footerbanner_position_middle'],
					[3, 'footerbanner_position_down'],
				],
				'default' => 1,
			],
		];
		loadcache(['forums', 'grouptype']);
		$settings['fids']['value'][] = $settings['groups']['value'][] = [0, '&nbsp;'];
		$settings['fids']['value'][] = $settings['groups']['value'][] = [-1, 'footerbanner_index'];
		$settings['fids']['value'][] = [-2, 'Archiver'];
		if(empty($_G['cache']['forums'])) $_G['cache']['forums'] = [];
		foreach($_G['cache']['forums'] as $fid => $forum) {
			$settings['fids']['value'][] = [$fid, ($forum['type'] == 'forum' ? str_repeat('&nbsp;', 4) : ($forum['type'] == 'sub' ? str_repeat('&nbsp;', 8) : '')).$forum['name']];
		}
		foreach($_G['cache']['grouptype']['first'] as $gid => $group) {
			$settings['groups']['value'][] = [$gid, str_repeat('&nbsp;', 4).$group['name']];
			if($group['secondlist']) {
				foreach($group['secondlist'] as $sgid) {
					$settings['groups']['value'][] = [$sgid, str_repeat('&nbsp;', 8).$_G['cache']['grouptype']['second'][$sgid]['name']];
				}
			}
		}
		loadcache('portalcategory');
		$this->categoryvalue[] = [-1, 'footerbanner_index'];
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
		if(is_array($parameters['extra']['fids']) && in_array(0, $parameters['extra']['fids'])) {
			$parameters['extra']['fids'] = [];
		}
		if(is_array($parameters['extra']['groups']) && in_array(0, $parameters['extra']['groups'])) {
			$parameters['extra']['groups'] = [];
		}
		if(is_array($parameters['extra']['category']) && in_array(0, $parameters['extra']['category'])) {
			$parameters['extra']['category'] = [];
		}
	}

	function evalcode() {
		return [
			'check' => '
			if($params[2] != $parameter[\'position\']
			|| $_G[\'basescript\'] == \'forum\' && $parameter[\'fids\'] && !(!defined(\'IN_ARCHIVER\') && (in_array($_G[\'fid\'], $parameter[\'fids\']) || CURMODULE == \'index\' && in_array(-1, $parameter[\'fids\'])) || defined(\'IN_ARCHIVER\') && in_array(-2, $parameter[\'fids\']))
			|| $_G[\'basescript\'] == \'group\' && $parameter[\'groups\'] && !(in_array($_G[\'grouptypeid\'], $parameter[\'groups\']) || CURMODULE == \'index\' && in_array(-1, $parameter[\'groups\']))
			|| $_G[\'basescript\'] == \'portal\' && $parameter[\'category\'] && !(!empty($_G[\'catid\']) && in_array($_G[\'catid\'], $parameter[\'category\']) || empty($_G[\'catid\']) && in_array(-1, $parameter[\'category\']))
			) {
				$checked = false;
			}',
			'create' => '$adcode = $codes[$adids[array_rand($adids)]];',
		];
	}

}

