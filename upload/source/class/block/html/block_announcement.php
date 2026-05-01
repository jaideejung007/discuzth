<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_announcement extends discuz_block {

	var $setting = [];

	function __construct() {
		$this->setting = [
			'type' => [
				'title' => 'announcement_type',
				'type' => 'mcheckbox',
				'value' => [
					['0', 'announcement_type_text'],
					['1', 'announcement_type_link'],
				],
				'default' => ['0']
			],
			'titlelength' => [
				'title' => 'announcement_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'announcement_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'startrow' => [
				'title' => 'announcement_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_announcement_script_announcement');
	}

	function blockclass() {
		return ['announcement', lang('blockclass', 'blockclass_html_announcement')];
	}

	function fields() {
		return [
			'url' => ['name' => lang('blockclass', 'blockclass_announcement_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_announcement_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'summary' => ['name' => lang('blockclass', 'blockclass_announcement_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'],
			'starttime' => ['name' => lang('blockclass', 'blockclass_announcement_field_starttime'), 'formtype' => 'text', 'datatype' => 'date'],
			'endtime' => ['name' => lang('blockclass', 'blockclass_announcement_field_endtime'), 'formtype' => 'text', 'datatype' => 'date'],
		];
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		return $settings;
	}

	function getdata($style, $parameter) {
		global $_G;


		$type = !empty($parameter['type']) && is_array($parameter['type']) ? array_map('intval', $parameter['type']) : ['0'];
		$titlelength = !empty($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$summarylength = !empty($parameter['summarylength']) ? intval($parameter['summarylength']) : 80;
		$startrow = !empty($parameter['startrow']) ? intval($parameter['startrow']) : '0';
		$items = !empty($parameter['items']) ? intval($parameter['items']) : 10;

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];

		$time = TIMESTAMP;

		$list = [];
		foreach(table_forum_announcement::t()->fetch_all_by_time($time, $type, $bannedids, $startrow, $items) as $data) {
			$list[] = [
				'id' => $data['id'],
				'idtype' => 'announcementid',
				'title' => cutstr(str_replace('\\\'', '&#39;', strip_tags($data['subject'])), $titlelength, ''),
				'url' => $data['type'] == '1' ? $data['message'] : 'forum.php?mod=announcement&id='.$data['id'],
				'pic' => '',
				'picflag' => '',
				'summary' => cutstr(str_replace('\\\'', '&#39;', $data['message']), $summarylength, ''),
				'fields' => [
					'starttime' => $data['starttime'],
					'endtime' => $data['endtime'],
				]
			];
		}
		return ['html' => '', 'data' => $list];
	}
}


