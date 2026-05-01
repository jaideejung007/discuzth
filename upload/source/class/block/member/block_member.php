<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_member extends discuz_block {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'uids' => [
				'title' => 'memberlist_uids',
				'type' => 'text'
			],
			'groupid' => [
				'title' => 'memberlist_groupid',
				'type' => 'mselect',
				'value' => []
			],
			'special' => [
				'title' => 'memberlist_special',
				'type' => 'mradio',
				'value' => [
					['', 'memberlist_special_nolimit'],
					['0', 'memberlist_special_hot'],
					['1', 'memberlist_special_default'],
				],
				'default' => ''
			],
			'gender' => [
				'title' => 'memberlist_gender',
				'type' => 'mradio',
				'value' => [
					['1', 'memberlist_gender_male'],
					['2', 'memberlist_gender_female'],
					['', 'memberlist_gender_nolimit'],
				],
				'default' => ''
			],
			'birthcity' => [
				'title' => 'memberlist_birthcity',
				'type' => 'district',
				'value' => ['xbirthcountry', 'xbirthprovince', 'xbirthcity', 'xbirthdist', 'xbirthcommunity'],
			],
			'residecity' => [
				'title' => 'memberlist_residecity',
				'type' => 'district',
				'value' => ['xresidecountry', 'xresideprovince', 'xresidecity', 'xresidedist', 'xresidecommunity']
			],
			'avatarstatus' => [
				'title' => 'memberlist_avatarstatus',
				'type' => 'radio',
				'default' => ''
			],
			'emailstatus' => [
				'title' => 'memberlist_emailstatus',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'memberlist_yes'],
				],
				'default' => ''
			],
			'secmobilestatus' => [
				'title' => 'memberlist_secmobilestatus',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'memberlist_yes'],
				],
				'default' => ''
			],
			'verifystatus' => [
				'title' => 'memberlist_verifystatus',
				'type' => 'mcheckbox',
				'value' => [],
				'default' => '',
			],
			'orderby' => [
				'title' => 'memberlist_orderby',
				'type' => 'mradio',
				'value' => [
					['credits', 'memberlist_orderby_credits'],
					['extcredits', 'memberlist_orderby_extcredits'],
					['threads', 'memberlist_orderby_threads'],
					['posts', 'memberlist_orderby_posts'],
					['blogs', 'memberlist_orderby_blogs'],
					['doings', 'memberlist_orderby_doings'],
					['albums', 'memberlist_orderby_albums'],
					['sharings', 'memberlist_orderby_sharings'],
					['digestposts', 'memberlist_orderby_digestposts'],
					['regdate', 'memberlist_orderby_regdate'],
					['show', 'memberlist_orderby_show'],
					['special', 'memberlist_orderby_special'],
					['todayposts', 'memberlist_orderby_todayposts'],
				],
				'default' => 'credits'
			],
			'extcredit' => [
				'title' => 'memberlist_orderby_extcreditselect',
				'type' => 'select',
				'value' => []
			],
			'lastpost' => [
				'title' => 'memberlist_lastpost',
				'type' => 'mradio',
				'value' => [
					['', 'memberlist_lastpost_nolimit'],
					['3600', 'memberlist_lastpost_hour'],
					['86400', 'memberlist_lastpost_day'],
					['604800', 'memberlist_lastpost_week'],
					['2592000', 'memberlist_lastpost_month'],
				],
				'default' => ''
			],
			'startrow' => [
				'title' => 'memberlist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
		$verifys = getglobal('setting/verify');
		if(!empty($verifys)) {
			foreach($verifys as $key => $value) {
				if($value['title']) {
					$this->setting['verifystatus']['value'][] = [$key, $value['title']];
				}
			}
		}
		if(empty($this->setting['verifystatus']['value'])) {
			unset($this->setting['verifystatus']);
		}
	}

	function name() {
		return lang('blockclass', 'blockclass_member_script_member');
	}

	function blockclass() {
		return ['member', lang('blockclass', 'blockclass_member_member')];
	}

	function fields() {
		global $_G;
		$fields = [
			'id' => ['name' => lang('blockclass', 'blockclass_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('blockclass', 'blockclass_member_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'uid' => array('name' => lang('blockclass', 'blockclass_member_field_uid'), 'formtype' => 'text', 'datatype' => 'int'),
			'title' => ['name' => lang('blockclass', 'blockclass_member_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'avatar' => ['name' => lang('blockclass', 'blockclass_member_field_avatar'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar_middle' => ['name' => lang('blockclass', 'blockclass_member_field_avatar_middle'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatar_big' => ['name' => lang('blockclass', 'blockclass_member_field_avatar_big'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg' => ['name' => lang('blockclass', 'blockclass_member_field_avatar'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg_middle' => ['name' => lang('blockclass', 'blockclass_member_field_avatar_middle'), 'formtype' => 'text', 'datatype' => 'string'],
			'avatarimg_big' => ['name' => lang('blockclass', 'blockclass_member_field_avatar_big'), 'formtype' => 'text', 'datatype' => 'string'],
			'regdate' => ['name' => lang('blockclass', 'blockclass_member_field_regdate'), 'formtype' => 'date', 'datatype' => 'date'],
			'posts' => ['name' => lang('blockclass', 'blockclass_member_field_posts'), 'formtype' => 'text', 'datatype' => 'int'],
			'threads' => ['name' => lang('blockclass', 'blockclass_member_field_threads'), 'formtype' => 'text', 'datatype' => 'int'],
			'digestposts' => ['name' => lang('blockclass', 'blockclass_member_field_digestposts'), 'formtype' => 'text', 'datatype' => 'int'],
			'credits' => ['name' => lang('blockclass', 'blockclass_member_field_credits'), 'formtype' => 'text', 'datatype' => 'int'],
			'reason' => ['name' => lang('blockclass', 'blockclass_member_field_reason'), 'formtype' => 'text', 'datatype' => 'string'],
			'unitprice' => ['name' => lang('blockclass', 'blockclass_member_field_unitprice'), 'formtype' => 'text', 'datatype' => 'int'],
			'showcredit' => ['name' => lang('blockclass', 'blockclass_member_field_showcredit'), 'formtype' => 'text', 'datatype' => 'int'],
			'shownote' => ['name' => lang('blockclass', 'blockclass_member_field_shownote'), 'formtype' => 'text', 'datatype' => 'string'],
		];
		foreach($_G['setting']['extcredits'] as $key => $value) {
			$fields['extcredits'.$key] = ['name' => $value['title'], 'formtype' => 'text', 'datatype' => 'int'];
		}
		loadcache('profilesetting');
		foreach($_G['cache']['profilesetting'] as $key => $value) {
			if($value['available']) {
				$fields[$key] = ['name' => $value['title'], 'formtype' => 'text', 'datatype' => 'string'];
			}
		}
		return $fields;
	}

	function getsetting() {
		global $_G;
		$settings = $this->setting;

		if($settings['extcredit']) {
			foreach($_G['setting']['extcredits'] as $id => $credit) {
				$settings['extcredit']['value'][] = [$id, $credit['title']];
			}
		}
		if($settings['groupid']) {
			$settings['groupid']['value'][] = [0, lang('portalcp', 'block_all_group')];
			foreach(table_common_usergroup::t()->fetch_all_by_type(['member', 'special']) as $value) {
				$settings['groupid']['value'][] = [$value['groupid'], $value['grouptitle']];
			}
		}
		return $settings;
	}

	function getdata($style, $parameter) {
		global $_G;

		$parameter = $this->cookparameter($parameter);

		$uids = !empty($parameter['uids']) ? explode(',', $parameter['uids']) : [];
		$groupid = !empty($parameter['groupid']) && !in_array(0, $parameter['groupid']) ? $parameter['groupid'] : [];
		$startrow = !empty($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items = !empty($parameter['items']) ? intval($parameter['items']) : 10;
		$orderby = isset($parameter['orderby']) && in_array($parameter['orderby'], ['credits', 'extcredits', 'threads', 'posts', 'digestposts', 'regdate', 'show', 'blogs', 'albums', 'doings', 'sharings', 'special', 'todayposts']) ? $parameter['orderby'] : '';
		$special = isset($parameter['special']) && strlen($parameter['special']) ? intval($parameter['special']) : null;
		$lastpost = !empty($parameter['lastpost']) ? intval($parameter['lastpost']) : '';
		$avatarstatus = !empty($parameter['avatarstatus']) ? 1 : 0;
		$emailstatus = !empty($parameter['emailstatus']) ? 1 : 0;
		$secmobilestatus = !empty($parameter['secmobilestatus']) ? 1 : 0;
		$verifystatus = !empty($parameter['verifystatus']) ? $parameter['verifystatus'] : [];
		$profiles = [];
		$profiles['gender'] = !empty($parameter['gender']) ? intval($parameter['gender']) : 0;
		$profiles['residecountry'] = !empty($parameter['xresidecountry']) ? $parameter['xresidecountry'] : '';
		$profiles['resideprovince'] = !empty($parameter['xresideprovince']) ? $parameter['xresideprovince'] : '';
		$profiles['residecity'] = !empty($parameter['xresidecity']) ? $parameter['xresidecity'] : '';
		$profiles['residedist'] = !empty($parameter['xresidedist']) ? $parameter['xresidedist'] : '';
		$profiles['residecommunity'] = !empty($parameter['xresidecommunity']) ? $parameter['xresidecommunity'] : '';
		$profiles['birthcountry'] = !empty($parameter['xbirthcountry']) ? $parameter['xbirthcountry'] : '';
		$profiles['birthprovince'] = !empty($parameter['xbirthprovince']) ? $parameter['xbirthprovince'] : '';
		$profiles['birthcity'] = !empty($parameter['xbirthcity']) ? $parameter['xbirthcity'] : '';

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];

		$list = $todayuids = $todayposts = [];
		$tables = $wheres = [];
		$sqlorderby = '';
		$olditems = $items;
		$tables[] = DB::table('common_member').' m';
		if($groupid) {
			$wheres[] = 'm.groupid IN ('.dimplode($groupid).')';
		}
		if($bannedids) {
			$wheres[] = 'm.uid NOT IN ('.dimplode($bannedids).')';
		}
		if($avatarstatus) {
			$wheres[] = "m.avatarstatus='1'";
		}
		if($emailstatus) {
			$wheres[] = "m.emailstatus='1'";
		}
		if($secmobilestatus) {
			$wheres[] = "m.secmobilestatus='1'";
		}
		if(!empty($verifystatus)) {
			$flag = false;
			foreach($verifystatus as $value) {
				if(isset($_G['setting']['verify'][$value])) {
					$flag = true;
					$wheres[] = "cmv.verify$value='1'";
				}
			}
			if($flag) {
				$tables[] = DB::table('common_member_verify').' cmv';
				$wheres[] = 'cmv.uid=m.uid';
			}
		}
		$tables[] = DB::table('common_member_count').' mc';
		$wheres[] = 'mc.uid=m.uid';
		foreach($profiles as $key => $value) {
			if($value) {
				$tables[] = DB::table('common_member_profile').' mp';
				$wheres[] = 'mp.uid=m.uid';
				$wheres[] = "mp.$key='$value'";
			}
		}

		$reason = $show = '';
		if($special !== null) {
			$special = in_array($special, [-1, 0, 1]) ? $special : -1;
			$tables[] = DB::table('home_specialuser').' su';
			if($special != -1) {
				$wheres[] = "su.status='$special'";
			}
			$wheres[] = 'su.uid=m.uid';
			$reason = ', su.reason';
		}
		if($lastpost && $orderby != 'todayposts') {
			$time = TIMESTAMP - $lastpost;
			$tables[] = DB::table('common_member_status').' ms';
			$wheres[] = 'ms.uid=m.uid';
			$wheres[] = "ms.lastpost>'$time'";
		}
		switch($orderby) {
			case 'credits':
			case 'regdate':
				$sqlorderby = " ORDER BY m.$orderby DESC";
				break;
			case 'extcredits':
				$extcredits = 'extcredits'.(in_array($parameter['extcredit'], range(1, 8)) ? $parameter['extcredit'] : '1');
				$sqlorderby = " ORDER BY mc.$extcredits DESC";
				break;
			case 'threads':
			case 'posts':
			case 'blogs':
			case 'albums':
			case 'doings':
			case 'sharings':
			case 'digestposts':
				$sqlorderby = " ORDER BY mc.$orderby DESC";
				break;
			case 'show':
				$show = ', s.unitprice, s.credit as showcredit, s.note as shownote';
				$tables[] = DB::table('home_show').' s';
				$wheres[] = 's.uid=m.uid';
				$sqlorderby = ' ORDER BY s.unitprice DESC, s.credit DESC';
				break;
			case 'special':
				$sqlorderby = $special !== null ? ' ORDER BY su.displayorder, dateline DESC' : '';
				break;
			case 'todayposts':
				$todaytime = strtotime(dgmdate(TIMESTAMP, 'Ymd'));
				$inuids = $uids ? ' AND uid IN ('.dimplode($uids).')' : '';
				$items = $items * 5;
				$query = DB::query('SELECT uid, count(*) as sum FROM '.DB::table('common_member_action_log')."
						WHERE dateline>=$todaytime AND action='".getuseraction('pid')."'$inuids GROUP BY uid ORDER BY sum DESC LIMIT $items");
				while($value = DB::fetch($query)) {
					$todayposts[$value['uid']] = $value['sum'];
					$todayuids[] = $value['uid'];
				}
				if(empty($todayuids)) {
					$todayuids = [0];
				}
				$uids = $todayuids;
				break;
		}

		if($uids) {
			$wheres[] = 'm.uid IN ('.dimplode($uids).')';
		}
		$wheres[] = '(m.groupid < 4 OR m.groupid > 8)';

		$tables = array_unique($tables);
		$wheres = array_unique($wheres);
		$tablesql = implode(',', $tables);
		$wheresql = implode(' AND ', $wheres);
		$query = DB::query("SELECT m.*, mc.*$reason$show FROM $tablesql WHERE $wheresql $sqlorderby LIMIT $startrow,$items");
		$resultuids = [];
		while($data = DB::fetch($query)) {
			$resultuids[] = intval($data['uid']);
			$list[] = [
				'id' => $data['uid'],
				'idtype' => 'uid',
				'title' => $data['username'],
				'url' => 'home.php?mod=space&uid='.$data['uid'],
				'pic' => '',
				'picflag' => 0,
				'summary' => '',
				'fields' => [
					'uid' => $data['uid'],
					'avatar' => avatar($data['uid'], 'small', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatar_middle' => avatar($data['uid'], 'middle', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatar_big' => avatar($data['uid'], 'big', true, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg' => avatar($data['uid'], 'small', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg_middle' => avatar($data['uid'], 'middle', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'avatarimg_big' => avatar($data['uid'], 'big', false, false, false, $_G['setting']['ucenterurl'], avatarapi: true),
					'credits' => $data['credits'],
					'extcredits1' => $data['extcredits1'],
					'extcredits2' => $data['extcredits2'],
					'extcredits3' => $data['extcredits3'],
					'extcredits4' => $data['extcredits4'],
					'extcredits5' => $data['extcredits5'],
					'extcredits6' => $data['extcredits6'],
					'extcredits7' => $data['extcredits7'],
					'extcredits8' => $data['extcredits8'],
					'regdate' => $data['regdate'],
					'posts' => empty($todayposts[$data['uid']]) ? $data['posts'] : $todayposts[$data['uid']],
					'threads' => $data['threads'],
					'digestposts' => $data['digestposts'],
					'reason' => $data['reason'] ?? '',
					'unitprice' => $data['unitprice'] ?? '',
					'showcredit' => $data['showcredit'] ?? '',
					'shownote' => $data['shownote'] ?? '',
				]
			];
		}
		if($resultuids) {
			include_once libfile('function/profile');
			$profiles = [];
			$query = DB::query('SELECT * FROM '.DB::table('common_member_profile').' WHERE uid IN ('.dimplode($resultuids).')');
			while($data = DB::fetch($query)) {
				$profile = [];
				foreach($data as $fieldid => $fieldvalue) {
					$fieldvalue = profile_show($fieldid, $data, true);
					if(false !== $fieldvalue) {
						$profile[$fieldid] = $fieldvalue;
					}
				}
				$profiles[$data['uid']] = $profile;
			}
			for($i = 0, $L = count($list); $i < $L; $i++) {
				$uid = $list[$i]['id'];
				if($profiles[$uid]) {
					$list[$i]['fields'] = array_merge($list[$i]['fields'], $profiles[$uid]);
				}
			}

			if(!empty($todayuids)) {
				$datalist = [];
				foreach($todayuids as $uid) {
					foreach($list as $user) {
						if($user['id'] == $uid) {
							$datalist[] = $user;
							break;
						}
					}
					if(count($datalist) >= $olditems) {
						break;
					}
				}
				$list = $datalist;
			}
		}
		return ['html' => '', 'data' => $list];
	}
}

