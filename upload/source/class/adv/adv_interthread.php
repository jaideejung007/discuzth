<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class adv_interthread {

	var $version = '1.1';
	var $name = 'interthread_name';
	var $description = 'interthread_desc';
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $targets = ['forum', 'group'];
	var $imagesizes = ['468x60', '658x60', '728x90', '760x90'];

	function getsetting() {
		global $_G;
		$settings = [
			'fids' => [
				'title' => 'interthread_fids',
				'type' => 'mselect',
				'value' => [],
			],
			'groups' => [
				'title' => 'interthread_groups',
				'type' => 'mselect',
				'value' => [],
			],
			'pnumber' => [
				'title' => 'interthread_pnumber',
				'type' => 'mselect',
				'value' => [],
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
					$settings['groups']['value'][] = [$sgid, str_repeat('&nbsp;', 8).$_G['cache']['grouptype']['second'][$sgid]['name']];
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
	}

	function evalcode() {
		return [
			'check' => '
			$parameter[\'pnumber\'] = $parameter[\'pnumber\'] ? $parameter[\'pnumber\'] : array(1);
			if(!in_array($params[2] + 1, (array)$parameter[\'pnumber\'])
			|| $_G[\'basescript\'] == \'forum\' && $parameter[\'fids\'] && !in_array($_G[\'fid\'], $parameter[\'fids\'])
			|| $_G[\'basescript\'] == \'group\' && $parameter[\'groups\'] && !in_array($_G[\'grouptypeid\'], $parameter[\'groups\'])
			) {
				$checked = false;
			}',
			'create' => '$adcode = $codes[$adids[array_rand($adids)]];',
		];
	}

}

