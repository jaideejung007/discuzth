<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_blog extends discuz_block {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'blogids' => [
				'title' => 'bloglist_blogids',
				'type' => 'text'
			],
			'uids' => [
				'title' => 'bloglist_uids',
				'type' => 'text',
			],
			'catid' => [
				'title' => 'bloglist_catid',
				'type' => 'mselect',
			],
			'picrequired' => [
				'title' => 'bloglist_picrequired',
				'type' => 'radio',
				'default' => '0'
			],
			'orderby' => [
				'title' => 'bloglist_orderby',
				'type' => 'mradio',
				'value' => [
					['dateline', 'bloglist_orderby_dateline'],
					['viewnum', 'bloglist_orderby_viewnum'],
					['replynum', 'bloglist_orderby_replynum'],
					['hot', 'bloglist_orderby_hot']
				],
				'default' => 'dateline'
			],
			'hours' => [
				'title' => 'bloglist_hours',
				'type' => 'mradio',
				'value' => [
					['', 'bloglist_hours_nolimit'],
					['1', 'bloglist_hours_hour'],
					['24', 'bloglist_hours_day'],
					['168', 'bloglist_hours_week'],
					['720', 'bloglist_hours_month'],
					['8760', 'bloglist_hours_year'],
				],
				'default' => ''
			],
			'titlelength' => [
				'title' => 'bloglist_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'bloglist_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'startrow' => [
				'title' => 'bloglist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_blog_script_blog');
	}

	function blockclass() {
		return ['blog', lang('blockclass', 'blockclass_space_blog')];
	}

	function fields() {
		return [
			'id' => ['name' => lang('blockclass', 'blockclass_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('blockclass', 'blockclass_blog_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_blog_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'summary' => ['name' => lang('blockclass', 'blockclass_blog_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'],
			'pic' => ['name' => lang('blockclass', 'blockclass_blog_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'],
			'dateline' => ['name' => lang('blockclass', 'blockclass_blog_field_dateline'), 'formtype' => 'date', 'datatype' => 'date'],
			'uid' => ['name' => lang('blockclass', 'blockclass_blog_field_uid'), 'formtype' => 'text', 'datatype' => 'int'],
			'username' => ['name' => lang('blockclass', 'blockclass_blog_field_username'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar' => ['name' => lang('blockclass', 'blockclass_blog_field_avatar'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar_middle' => ['name' => lang('blockclass', 'blockclass_blog_field_avatar_middle'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar_big' => ['name' => lang('blockclass', 'blockclass_blog_field_avatar_big'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg' => ['name' => lang('blockclass', 'blockclass_blog_field_avatar'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg_middle' => ['name' => lang('blockclass', 'blockclass_blog_field_avatar_middle'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg_big' => ['name' => lang('blockclass', 'blockclass_blog_field_avatar_big'), 'formtype' => 'text', 'datatype' => 'string'],
			'replynum' => ['name' => lang('blockclass', 'blockclass_blog_field_replynum'), 'formtype' => 'text', 'datatype' => 'int'],
			'viewnum' => ['name' => lang('blockclass', 'blockclass_blog_field_viewnum'), 'formtype' => 'text', 'datatype' => 'int'],
			'click1' => ['name' => lang('blockclass', 'blockclass_blog_field_click1'), 'formtype' => 'text', 'datatype' => 'int'],
			'click2' => ['name' => lang('blockclass', 'blockclass_blog_field_click2'), 'formtype' => 'text', 'datatype' => 'int'],
			'click3' => ['name' => lang('blockclass', 'blockclass_blog_field_click3'), 'formtype' => 'text', 'datatype' => 'int'],
			'click4' => ['name' => lang('blockclass', 'blockclass_blog_field_click4'), 'formtype' => 'text', 'datatype' => 'int'],
			'click5' => ['name' => lang('blockclass', 'blockclass_blog_field_click5'), 'formtype' => 'text', 'datatype' => 'int'],
			'click6' => ['name' => lang('blockclass', 'blockclass_blog_field_click6'), 'formtype' => 'text', 'datatype' => 'int'],
			'click7' => ['name' => lang('blockclass', 'blockclass_blog_field_click7'), 'formtype' => 'text', 'datatype' => 'int'],
			'click8' => ['name' => lang('blockclass', 'blockclass_blog_field_click8'), 'formtype' => 'text', 'datatype' => 'int'],
		];
	}

	function fieldsconvert() {
		return [
			'forum_thread' => [
				'name' => lang('blockclass', 'blockclass_forum_thread'),
				'script' => 'thread',
				'searchkeys' => ['username', 'uid', 'viewnum', 'replynum'],
				'replacekeys' => ['author', 'authorid', 'views', 'replies'],
			],
			'group_thread' => [
				'name' => lang('blockclass', 'blockclass_group_thread'),
				'script' => 'groupthread',
				'searchkeys' => ['username', 'uid', 'viewnum', 'replynum'],
				'replacekeys' => ['author', 'authorid', 'views', 'replies'],
			],
			'portal_article' => [
				'name' => lang('blockclass', 'blockclass_portal_article'),
				'script' => 'article',
				'searchkeys' => ['replynum'],
				'replacekeys' => ['commentnum'],
			],
		];
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		if(!empty($settings['catid'])) {
			$settings['catid']['value'][] = [0, lang('portalcp', 'block_all_category')];
			loadcache('blogcategory');
			foreach($_G['cache']['blogcategory'] as $value) {
				if($value['level'] == 0) {
					$settings['catid']['value'][] = [$value['catid'], $value['catname']];
					if($value['children']) {
						foreach($value['children'] as $catid2) {
							$value2 = $_G['cache']['blogcategory'][$catid2];
							$settings['catid']['value'][] = [$value2['catid'], '-- '.$value2['catname']];
							if($value2['children']) {
								foreach($value2['children'] as $catid3) {
									$value3 = $_G['cache']['blogcategory'][$catid3];
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
		$blogids = !empty($parameter['blogids']) ? explode(',', $parameter['blogids']) : [];
		$uids = !empty($parameter['uids']) ? explode(',', $parameter['uids']) : [];
		$catid = !empty($parameter['catid']) ? $parameter['catid'] : [];
		$startrow = isset($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items = isset($parameter['items']) ? intval($parameter['items']) : 10;
		$hours = isset($parameter['hours']) ? intval($parameter['hours']) : '';
		$titlelength = $parameter['titlelength'] ? intval($parameter['titlelength']) : 40;
		$summarylength = $parameter['summarylength'] ? intval($parameter['summarylength']) : 80;
		$orderby = isset($parameter['orderby']) && in_array($parameter['orderby'], ['dateline', 'viewnum', 'replynum', 'hot']) ? $parameter['orderby'] : 'dateline';
		$picrequired = !empty($parameter['picrequired']) ? 1 : 0;

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];

		$datalist = $list = [];
		$wheres = [];
		if(!$blogids && !$catid && $_G['setting']['blockmaxaggregationitem']) {
			if(($maxid = $this->getmaxid() - $_G['setting']['blockmaxaggregationitem']) > 0) {
				$wheres[] = 'b.blogid > '.$maxid;
			}
		}
		if($blogids) {
			$wheres[] = 'b.blogid IN ('.dimplode($blogids).')';
		}
		if($bannedids) {
			$wheres[] = 'b.blogid NOT IN ('.dimplode($bannedids).')';
		}
		if($uids) {
			$wheres[] = 'b.uid IN ('.dimplode($uids).')';
		}
		if($catid && !in_array('0', $catid)) {
			$wheres[] = 'b.catid IN ('.dimplode($catid).')';
		}
		if($hours) {
			$timestamp = TIMESTAMP - 3600 * $hours;
			$wheres[] = "b.dateline >= '$timestamp'";
		}
		$tablesql = $fieldsql = '';
		if($style['getsummary'] || $picrequired || $style['getpic']) {
			if($picrequired) {
				$wheres[] = "bf.pic != ''";
			}
			$tablesql = ' LEFT JOIN '.DB::table('home_blogfield').' bf ON b.blogid = bf.blogid';
			$fieldsql = ', bf.pic, b.picflag, bf.message';
		}
		$wheres[] = "b.friend = '0'";
		$wheres[] = "b.status='0'";
		$wheresql = $wheres ? implode(' AND ', $wheres) : '1';
		$sql = "SELECT b.* $fieldsql FROM ".DB::table('home_blog')." b $tablesql WHERE $wheresql ORDER BY b.$orderby DESC";
		$query = DB::query($sql." LIMIT $startrow,$items;");
		while($data = DB::fetch($query)) {
			if(empty($data['pic'])) {
				$data['pic'] = STATICURL.'image/common/nophoto.gif';
				$data['picflag'] = '0';
			} else {
				$data['pic'] = preg_replace('/\.thumb\.jpg$/', '', $data['pic']);
				$data['pic'] = 'album/'.$data['pic'];
				$data['picflag'] = $data['remote'] == '1' ? '2' : '1';
			}
			$list[] = [
				'id' => $data['blogid'],
				'idtype' => 'blogid',
				'title' => cutstr($data['subject'], $titlelength, ''),
				'url' => 'home.php?mod=space&uid='.$data['uid'].'&do=blog&id='.$data['blogid'],
				'pic' => $data['pic'],
				'picflag' => $data['picflag'],
				'summary' => $data['message'] ? preg_replace('/&amp;[a-z]+\;/i', '', cutstr(strip_tags($data['message']), $summarylength, '')) : '',
				'fields' => [
					'fulltitle' => $data['subject'],
					'dateline' => $data['dateline'],
					'uid' => $data['uid'],
					'username' => $data['username'],
					'avatar' => avatar($data['uid'], 'small', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatar_middle' => avatar($data['uid'], 'middle', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatar_big' => avatar($data['uid'], 'big', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg' => avatar($data['uid'], 'small', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg_middle' => avatar($data['uid'], 'middle', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg_big' => avatar($data['uid'], 'big', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'replynum' => $data['replynum'],
					'viewnum' => $data['viewnum'],
					'click1' => $data['click1'],
					'click2' => $data['click2'],
					'click3' => $data['click3'],
					'click4' => $data['click4'],
					'click5' => $data['click5'],
					'click6' => $data['click6'],
					'click7' => $data['click7'],
					'click8' => $data['click8'],
				]
			];
		}
		return ['html' => '', 'data' => $list];
	}

	function getmaxid() {
		loadcache('databasemaxid');
		$data = getglobal('cache/databasemaxid');
		if(!is_array($data)) {
			$data = [];
		}
		if(!isset($data['blog']) || TIMESTAMP - $data['blog']['dateline'] >= 86400) {
			$data['blog']['dateline'] = TIMESTAMP;
			$data['blog']['id'] = DB::result_first('SELECT MAX(blogid) FROM '.DB::table('home_blog'));
			savecache('databasemaxid', $data);
		}
		return $data['blog']['id'];
	}


}

