<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_otherfriendlink extends discuz_block {

	var $setting = [];

	function __construct() {
		$this->setting = [
			'type' => [
				'title' => 'friendlink_type',
				'type' => 'mcheckbox',
				'value' => [
					['1', 'friendlink_type_group1'],
					['2', 'friendlink_type_group2'],
					['3', 'friendlink_type_group3'],
					['4', 'friendlink_type_group4'],
				],
				'default' => ['1', '2', '3', '4']
			],
			'titlelength' => [
				'title' => 'friendlink_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'friendlink_summarylength',
				'type' => 'text',
				'default' => 80
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_other_script_friendlink');
	}

	function blockclass() {
		return ['otherfriendlink', lang('blockclass', 'blockclass_other_friendlink')];
	}

	function fields() {
		return [
			'url' => ['name' => lang('blockclass', 'blockclass_other_friendlink_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_other_friendlink_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'pic' => ['name' => lang('blockclass', 'blockclass_other_friendlink_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'],
			'summary' => ['name' => lang('blockclass', 'blockclass_other_friendlink_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'],
		];
	}

	function getsetting() {
		return $this->setting;
	}

	function getdata($style, $parameter) {

		$parameter = $this->cookparameter($parameter);
		$titlelength = isset($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$summarylength = isset($parameter['summarylength']) ? intval($parameter['summarylength']) : 80;
		$type = !empty($parameter['type']) && is_array($parameter['type']) ? $parameter['type'] : [];
		$b = '0000';
		for($i = 1; $i <= 4; $i++) {
			if(in_array($i, $type)) {
				$b[$i - 1] = '1';
			}
		}
		$type = intval($b, '2');
		$list = [];
		$query = table_common_friendlink::t()->fetch_all_by_displayorder($type);
		foreach($query as $data) {
			$list[] = [
				'id' => $data['id'],
				'idtype' => 'flid',
				'title' => cutstr($data['name'], $titlelength),
				'url' => $data['url'],
				'pic' => $data['logo'] ? $data['logo'] : $_G['style']['imgdir'].'/nophoto.gif',
				'picflag' => '0',
				'summary' => $data['description'],
				'fields' => [
					'fulltitle' => $data['name'],
				]
			];
		}
		return ['html' => '', 'data' => $list];
	}
}


