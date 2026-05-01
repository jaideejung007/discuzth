<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_doing extends discuz_block {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'uids' => [
				'title' => 'doinglist_uids',
				'type' => 'text',
				'value' => ''
			],
			'titlelength' => [
				'title' => 'doinglist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'orderby' => [
				'title' => 'doinglist_orderby',
				'type' => 'mradio',
				'value' => [
					['dateline', 'doinglist_orderby_dateline'],
					['replynum', 'doinglist_orderby_replynum']
				],
				'default' => 'dateline'
			],
			'startrow' => [
				'title' => 'doinglist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_doing_script_doing');
	}

	function blockclass() {
		return ['doing', lang('blockclass', 'blockclass_space_doing')];
	}

	function fields() {
		return [
			'id' => ['name' => lang('blockclass', 'blockclass_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('blockclass', 'blockclass_doing_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_doing_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'uid' => ['name' => lang('blockclass', 'blockclass_doing_field_uid'), 'formtype' => 'text', 'datatype' => 'pic'],
			'username' => ['name' => lang('blockclass', 'blockclass_doing_field_username'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar' => ['name' => lang('blockclass', 'blockclass_doing_field_avatar'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar_middle' => ['name' => lang('blockclass', 'blockclass_doing_field_avatar_middle'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar_big' => ['name' => lang('blockclass', 'blockclass_doing_field_avatar_big'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg' => ['name' => lang('blockclass', 'blockclass_doing_field_avatar'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg_middle' => ['name' => lang('blockclass', 'blockclass_doing_field_avatar_middle'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg_big' => ['name' => lang('blockclass', 'blockclass_doing_field_avatar_big'), 'formtype' => 'text', 'datatype' => 'string'],
			'dateline' => ['name' => lang('blockclass', 'blockclass_doing_field_dateline'), 'formtype' => 'date', 'datatype' => 'date'],
			'replynum' => ['name' => lang('blockclass', 'blockclass_doing_field_replynum'), 'formtype' => 'text', 'datatype' => 'int'],
		];
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		return $settings;
	}

	function getdata($style, $parameter) {
		global $_G;

		$parameter = $this->cookparameter($parameter);
		$uids = isset($parameter['uids']) && !in_array(0, (array)$parameter['uids']) ? $parameter['uids'] : '';
		$startrow = isset($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items = isset($parameter['items']) ? intval($parameter['items']) : 10;
		$titlelength = intval($parameter['titlelength']);

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];

		$datalist = $list = [];

		$query = table_home_doing::t()->fetch_all_by_uid_doid($uids, $bannedids, $parameter['orderby'], $startrow, $items, true, true);
		foreach($query as $data) {
			$datalist = [
				'id' => $data['doid'],
				'idtype' => 'doid',
				'title' => cutstr(strip_tags($data['message']), $titlelength, ''),
				'url' => 'home.php?mod=space&uid='.$data['uid'].'&do=doing&doid='.$data['doid'],
				'pic' => '',
				'summary' => '',
				'fields' => [
					'fulltitle' => strip_tags($data['message']),
					'uid' => $data['uid'],
					'username' => $data['username'],
					'avatar' => avatar($data['uid'], 'small', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatar_middle' => avatar($data['uid'], 'middle', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatar_big' => avatar($data['uid'], 'big', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg' => avatar($data['uid'], 'small', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg_middle' => avatar($data['uid'], 'middle', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg_big' => avatar($data['uid'], 'big', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'dateline' => $data['dateline'],
					'replynum' => $data['replynum'],
				]
			];
			if($titlelength) {
				$datalist['title'] = cutstr(strip_tags($data['message']), $titlelength);
			} else {
				$datalist['title'] = strip_tags($data['message'], '<img>');
			}
			$list[] = $datalist;
		}
		return ['html' => '', 'data' => $list];
	}
}

