<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_album extends discuz_block {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'aids' => [
				'title' => 'albumlist_aids',
				'type' => 'text',
				'value' => ''
			],
			'uids' => [
				'title' => 'albumlist_uids',
				'type' => 'text',
				'value' => ''
			],
			'catid' => [
				'title' => 'albumlist_catid',
				'type' => 'mselect',
			],
			'orderby' => [
				'title' => 'albumlist_orderby',
				'type' => 'mradio',
				'value' => [
					['dateline', 'albumlist_orderby_dateline'],
					['updatetime', 'albumlist_orderby_updatetime'],
					['picnum', 'albumlist_orderby_picnum'],
				],
				'default' => 'dateline'
			],
			'titlelength' => [
				'title' => 'albumlist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'startrow' => [
				'title' => 'albumlist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_album_script_album');
	}

	function blockclass() {
		return ['album', lang('blockclass', 'blockclass_space_album')];
	}

	function fields() {
		return [
			'id' => ['name' => lang('blockclass', 'blockclass_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('blockclass', 'blockclass_album_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_album_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'pic' => ['name' => lang('blockclass', 'blockclass_album_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'],
			'uid' => ['name' => lang('blockclass', 'blockclass_album_field_uid'), 'formtype' => 'text', 'datatype' => 'int'],
			'username' => ['name' => lang('blockclass', 'blockclass_album_field_username'), 'formtype' => 'text', 'datatype' => 'string'],
			'dateline' => ['name' => lang('blockclass', 'blockclass_album_field_dateline'), 'formtype' => 'date', 'datatype' => 'date'],
			'updatetime' => ['name' => lang('blockclass', 'blockclass_album_field_updatetime'), 'formtype' => 'date', 'datatype' => 'date'],
			'picnum' => ['name' => lang('blockclass', 'blockclass_album_field_picnum'), 'formtype' => 'text', 'datatype' => 'int'],
		];
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		if($settings['catid']) {
			$settings['catid']['value'][] = [0, lang('portalcp', 'block_all_category')];
			loadcache('albumcategory');
			foreach($_G['cache']['albumcategory'] as $value) {
				if($value['level'] == 0) {
					$settings['catid']['value'][] = [$value['catid'], $value['catname']];
					if($value['children']) {
						foreach($value['children'] as $catid2) {
							$value2 = $_G['cache']['albumcategory'][$catid2];
							$settings['catid']['value'][] = [$value2['catid'], '-- '.$value2['catname']];
							if($value2['children']) {
								foreach($value2['children'] as $catid3) {
									$value3 = $_G['cache']['albumcategory'][$catid3];
									$settings['catid']['value'][] = [$value3['catid'], '---- '.$value3['catname']];
								}
							}
						}
					}
				}
			}
		}
		return $settings;
	}


	function getdata($style, $parameter) {
		global $_G;

		$parameter = $this->cookparameter($parameter);
		$uids = !empty($parameter['uids']) ? explode(',', $parameter['uids']) : [];
		$aids = !empty($parameter['aids']) ? explode(',', $parameter['aids']) : [];
		$catid = !empty($parameter['catid']) ? $parameter['catid'] : [];
		$startrow = isset($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items = isset($parameter['items']) ? intval($parameter['items']) : 10;
		$titlelength = isset($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$orderby = isset($parameter['orderby']) && in_array($parameter['orderby'], ['dateline', 'picnum', 'updatetime']) ? $parameter['orderby'] : 'dateline';

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];

		$list = [];

		$query = table_home_album::t()->fetch_all_by_block($aids, $bannedids, $uids, $catid, $startrow, $items, $orderby);
		foreach($query as $data) {
			if($_G['setting']['ftp']['on'] == 2) {
				$data['picflag'] = '0';
				$data['pic'] = $_G['setting']['attachurl'].'album/'.$data['pic'];
			} else {
				$data['pic'] = 'album/'.$data['pic'];
			}
			$list[] = [
				'id' => $data['albumid'],
				'idtype' => 'albumid',
				'title' => cutstr($data['albumname'], $titlelength, ''),
				'url' => "home.php?mod=space&uid={$data['uid']}&do=album&id={$data['albumid']}",
				'pic' => $data['pic'],
				'picflag' => $data['picflag'],
				'summary' => '',
				'fields' => [
					'fulltitle' => $data['albumname'],
					'uid' => $data['uid'],
					'username' => $data['username'],
					'dateline' => $data['dateline'],
					'updatetime' => $data['updatetime'],
					'picnum' => $data['picnum'],
				]
			];
		}
		return ['html' => '', 'data' => $list];
	}
}

