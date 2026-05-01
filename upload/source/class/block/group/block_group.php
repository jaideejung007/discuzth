<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_group extends discuz_block {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'gtids' => [
				'title' => 'grouplist_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'fids' => [
				'title' => 'grouplist_fids',
				'type' => 'text'
			],
			'titlelength' => [
				'title' => 'grouplist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'grouplist_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'orderby' => [
				'title' => 'grouplist_orderby',
				'type' => 'mradio',
				'value' => [
					['displayorder', 'grouplist_orderby_displayorder'],
					['threads', 'grouplist_orderby_threads'],
					['posts', 'grouplist_orderby_posts'],
					['todayposts', 'grouplist_orderby_todayposts'],
					['membernum', 'grouplist_orderby_membernum'],
					['dateline', 'grouplist_orderby_dateline'],
					['level', 'grouplist_orderby_level'],
					['commoncredits', 'grouplist_orderby_commoncredits'],
					['activity', 'grouplist_orderby_activity']
				],
				'default' => 'displayorder'
			]
		];
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		if($settings['gtids']) {
			loadcache('grouptype');
			$settings['gtids']['value'][] = [0, lang('portalcp', 'block_all_type')];
			foreach($_G['cache']['grouptype']['first'] as $gid => $group) {
				$settings['gtids']['value'][] = [$gid, $group['name']];
				if($group['secondlist']) {
					foreach($group['secondlist'] as $subgid) {
						$settings['gtids']['value'][] = [$subgid, '&nbsp;&nbsp;'.$_G['cache']['grouptype']['second'][$subgid]['name']];
					}
				}
			}
		}
		return $settings;
	}

	function name() {
		return lang('blockclass', 'blockclass_group_script_group');
	}

	function blockclass() {
		return ['group', lang('blockclass', 'blockclass_group_group')];
	}

	function fields() {
		return [
			'id' => ['name' => lang('blockclass', 'blockclass_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('blockclass', 'blockclass_group_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_group_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'pic' => ['name' => lang('blockclass', 'blockclass_group_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'],
			'summary' => ['name' => lang('blockclass', 'blockclass_group_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'],
			'icon' => ['name' => lang('blockclass', 'blockclass_group_field_icon'), 'formtype' => 'text', 'datatype' => 'string'],
			'foundername' => ['name' => lang('blockclass', 'blockclass_group_field_foundername'), 'formtype' => 'text', 'datatype' => 'string'],
			'founderuid' => ['name' => lang('blockclass', 'blockclass_group_field_founderuid'), 'formtype' => 'text', 'datatype' => 'int'],
			'posts' => ['name' => lang('blockclass', 'blockclass_group_field_posts'), 'formtype' => 'text', 'datatype' => 'int'],
			'todayposts' => ['name' => lang('blockclass', 'blockclass_group_field_todayposts'), 'formtype' => 'text', 'datatype' => 'int'],
			'threads' => ['name' => lang('blockclass', 'blockclass_group_field_threads'), 'formtype' => 'date', 'datatype' => 'int'],
			'membernum' => ['name' => lang('blockclass', 'blockclass_group_field_membernum'), 'formtype' => 'text', 'datatype' => 'int'],
			'dateline' => ['name' => lang('blockclass', 'blockclass_group_field_dateline'), 'formtype' => 'date', 'datatype' => 'date'],
			'level' => ['name' => lang('blockclass', 'blockclass_group_field_level'), 'formtype' => 'text', 'datatype' => 'int'],
			'commoncredits' => ['name' => lang('blockclass', 'blockclass_group_field_commoncredits'), 'formtype' => 'text', 'datatype' => 'int'],
			'activity' => ['name' => lang('blockclass', 'blockclass_group_field_activity'), 'formtype' => 'text', 'datatype' => 'int'],
		];
	}

	function fieldsconvert() {
		return [
			'forum_forum' => [
				'name' => lang('blockclass', 'blockclass_forum_forum'),
				'script' => 'forum',
				'searchkeys' => [],
				'replacekeys' => [],
			],
			'portal_category' => [
				'name' => lang('blockclass', 'blockclass_portal_category'),
				'script' => 'portalcategory',
				'searchkeys' => ['threads'],
				'replacekeys' => ['articles'],
			],
		];
	}

	function getdata($style, $parameter) {
		global $_G;

		$parameter = $this->cookparameter($parameter);

		loadcache('grouptype');
		$typeids = [];
		if(!empty($parameter['gtids'])) {
			if($parameter['gtids'][0] == '0') {
				unset($parameter['gtids'][0]);
			}
			$typeids = $parameter['gtids'];
		}
		if(empty($typeids)) $typeids = array_keys($_G['cache']['grouptype']['second']);
		$fids = !empty($parameter['fids']) ? explode(',', $parameter['fids']) : [];
		$items = isset($parameter['items']) ? intval($parameter['items']) : 10;
		$titlelength = !empty($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$summarylength = !empty($parameter['summarylength']) ? intval($parameter['summarylength']) : 80;
		$orderby = in_array($parameter['orderby'], ['displayorder', 'posts', 'todayposts', 'threads', 'membernum', 'dateline', 'level', 'activity', 'commoncredits']) ? $parameter['orderby'] : 'displayorder';

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];
		$sqlban = !empty($bannedids) ? ' AND f.fid NOT IN ('.dimplode($bannedids).')' : '';

		if($fids) {
			$wheresql = 'f.fid IN ('.dimplode($fids).") AND f.status='3' AND f.type='sub' $sqlban";
		} else {
			$wheresql = !empty($typeids) ? 'f.fup IN ('.dimplode($typeids).") AND f.status='3' AND f.type='sub' $sqlban" : '0';
		}
		$wheresql .= " AND f.level > '0'";

		if(in_array($orderby, ['posts', 'todayposts', 'threads', 'level', 'commoncredits'])) {
			$orderbysql = "f.$orderby DESC";
		} elseif(in_array($orderby, ['dateline', 'activity', 'membernum'])) {
			$orderbysql = "ff.$orderby DESC";
		} else {
			$orderbysql = 'f.displayorder ASC';
		}
		$list = [];
		$query = DB::query('SELECT f.*, ff.* FROM '.DB::table('forum_forum').' f LEFT JOIN '.DB::table('forum_forumfield')." ff ON f.fid = ff.fid WHERE $wheresql ORDER BY $orderbysql LIMIT $items");
		while($data = DB::fetch($query)) {
			$list[] = [
				'id' => $data['fid'],
				'idtype' => 'fid',
				'title' => cutstr($data['name'], $titlelength, ''),
				'url' => 'forum.php?mod=group&fid='.$data['fid'],
				'pic' => 'group/'.$data['banner'],
				'picflag' => '1',
				'summary' => cutstr($data['description'], $summarylength, ''),
				'fields' => [
					'fulltitle' => $data['name'],
					'icon' => !empty($data['icon']) ? $_G['setting']['attachurl'].'group/'.$data['icon'] : STATICURL.'image/common/nophoto.gif',
					'founderuid' => $data['founderuid'],
					'foundername' => $data['foundername'],
					'threads' => $data['threads'],
					'posts' => $data['posts'],
					'todayposts' => $data['todayposts'],
					'dateline' => $data['dateline'],
					'level' => $data['level'],
					'membernum' => $data['membernum'],
					'activity' => $data['activity'],
					'commoncredits' => $data['commoncredits'],
				]
			];
		}
		return ['html' => '', 'data' => $list];
	}
}


