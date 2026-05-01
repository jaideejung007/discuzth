<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_couplebanner {

	var $version = '1.1';
	var $name = 'couplebanner_name';
	var $description = 'couplebanner_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $targets = ['portal', 'home', 'member', 'forum', 'group', 'search', 'plugin', 'custom'];
	var $imagesizes = ['60x120', '60x250', '60x468'];

	function getsetting() {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'couplebanner_fids',
				'type' => 'mselect',
				'value' => [],
			],
			'groups' => [
				'title' => 'couplebanner_groups',
				'type' => 'mselect',
				'value' => [],
			],
			'category' => [
				'title' => 'couplebanner_category',
				'type' => 'mselect',
				'value' => [],
			],
			'position' => [
				'title' => 'couplebanner_position',
				'type' => 'mradio',
				'value' => [
					[1, 'couplebanner_position_left'],
					[2, 'couplebanner_position_right'],
				],
				'default' => 1,
			],
			'coupleadid' => [
				'title' => 'couplebanner_coupleadid',
				'type' => 'select',
				'value' => [],
			],
			'disableclose' => [
				'title' => 'couplebanner_disableclose',
				'type' => 'mradio',
				'value' => [
					[0, 'couplebanner_show'],
					[1, 'couplebanner_hidden'],
				],
				'default' => 0,
			]
		];
		loadcache(['forums', 'grouptype']);
		$settings['fids']['value'][] = $settings['groups']['value'][] = [0, '&nbsp;'];
		$settings['fids']['value'][] = $settings['groups']['value'][] = [-1, 'couplebanner_index'];
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
		foreach(table_common_advertisement::t()->fetch_all_by_type('couplebanner') as $couple) {
			$settings['coupleadid']['value'][] = [$couple['advid'], $couple['title']];
		}
		loadcache('portalcategory');
		$this->categoryvalue[] = [-1, 'couplebanner_index'];
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
			|| $_G[\'basescript\'] == \'forum\' && $parameter[\'fids\'] && !(in_array($_G[\'fid\'], $parameter[\'fids\']) || CURMODULE == \'index\' && in_array(-1, $parameter[\'fids\']))
			|| $_G[\'basescript\'] == \'group\' && $parameter[\'groups\'] && !(in_array($_G[\'grouptypeid\'], $parameter[\'groups\']) || CURMODULE == \'index\' && in_array(-1, $parameter[\'groups\']))
			|| $_G[\'basescript\'] == \'portal\' && $parameter[\'category\'] && !(!empty($_G[\'catid\']) && in_array($_G[\'catid\'], $parameter[\'category\']) || empty($_G[\'catid\']) && in_array(-1, $parameter[\'category\']))
			) {
				$checked = false;
			}',
			'create' => '
			if(empty($_G[\'couplebrother\'])) {
				$coupleadid = $adids[array_rand($adids)];
				$_G[\'couplebrother\'] = $parameters[$coupleadid][\'coupleadid\'];
			} else {
				$coupleadid = $_G[\'couplebrother\'];
			}
			$adcode = empty($parameter[\'disableclose\']) ? (empty($_G[\'cookie\'][\'adclose_\'.$coupleadid]) ? $codes[$coupleadid].\'<br /><a href="javascript:;" onclick="setcookie(\\\'adclose_\'.$coupleadid.\'\\\', 1, 86400);this.parentNode.style.display=\\\'none\\\'"><img src="\'.STATICURL.\'image/common/ad_close.gif" /></a>\' : \'\') : $codes[$coupleadid];
			',
		];
	}

}

