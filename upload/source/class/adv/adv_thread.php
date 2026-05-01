<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_thread {

	var $version = '1.0';
	var $name = 'thread_name';
	var $description = 'thread_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $targets = ['forum', 'group'];
	var $imagesizes = ['120x60', '120x240'];

	function getsetting() {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'thread_fids',
				'type' => 'mselect',
				'value' => [],
			],
			'groups' => [
				'title' => 'thread_groups',
				'type' => 'mselect',
				'value' => [],
			],
			'position' => [
				'title' => 'thread_position',
				'type' => 'mradio',
				'value' => [
					[2, 'thread_position_top'],
					[3, 'thread_position_right'],
					[1, 'thread_position_bottom'],
				],
				'default' => 1,
			],
			'pnumber' => [
				'title' => 'thread_pnumber',
				'type' => 'mselect',
				'value' => [
					[0, 'thread_pnumber_all'],
				],
				'default' => [0],
			],
		];
		loadcache(['forums', 'grouptype']);
		$settings['fids']['value'][] = $settings['groups']['value'][] = [0, '&nbsp;'];
		if(empty($_G['cache']['forums'])) $_G['cache']['forums'] = [];
		foreach($_G['cache']['forums'] as $fid => $forum) {
			$settings['fids']['value'][] = [$fid, ($forum['type'] == 'forum' ? str_repeat('&nbsp;', 4) : ($forum['type'] == 'sub' ? str_repeat('&nbsp;', 8) : '')).$forum['name']];
		}
		foreach($_G['cache']['grouptype']['first'] as $gid => $group) {
			$settings['groups']['value'][] = [$gid, $group['name']];
			if($group['secondlist']) {
				foreach($group['secondlist'] as $sgid) {
					$settings['groups']['value'][] = [$sgid, str_repeat('&nbsp;', 4).$_G['cache']['grouptype']['second'][$sgid]['name']];
				}
			}
		}
		for($i = 1; $i <= $_G['ppp']; $i++) {
			$settings['pnumber']['value'][$i] = [$i, '> #'.$i];
		}

		return $settings;
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
		if(is_array($parameters['extra']['pnumber']) && in_array(0, $parameters['extra']['pnumber'])) {
			$parameters['extra']['pnumber'] = [];
		}
	}

	function evalcode($adv) {
		return [
			'check' => '
			if($params[2] != $parameter[\'position\']
			|| $parameter[\'pnumber\'] && !in_array($params[3] + 1, (array)$parameter[\'pnumber\'])
			|| $_G[\'basescript\'] == \'forum\' && $parameter[\'fids\'] && !in_array($_G[\'fid\'], $parameter[\'fids\'])
			|| $_G[\'basescript\'] == \'group\' && $parameter[\'groups\'] && !in_array($_G[\'grouptypeid\'], $parameter[\'groups\'])
			) {
				$checked = false;
			}',
			'create' => '
				$adid = $adids[array_rand($adids)];
				if($parameters[$adid][\'position\'] == 3) {
					$_G[\'thread\'][\'contentmr\'] = $parameters[$adid][\'width\'] ? $parameters[$adid][\'width\'].\'px\' : \'auto\';
					$extra = \'style="margin-left:10px;width:\'.$_G[\'thread\'][\'contentmr\'].\'"\';
				}
				$adcode = $codes[$adid];
			',
		];
	}

}

