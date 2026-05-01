<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class block_groupactivity extends discuz_block {
	var $setting = [];

	function __construct() {
		$this->setting = [
			'tids' => [
				'title' => 'groupactivity_tids',
				'type' => 'text'
			],
			'uids' => [
				'title' => 'groupactivity_uids',
				'type' => 'text'
			],
			'keyword' => [
				'title' => 'groupactivity_keyword',
				'type' => 'text'
			],
			'fids' => [
				'title' => 'groupactivity_fids',
				'type' => 'text'
			],
			'gtids' => [
				'title' => 'groupactivity_gtids',
				'type' => 'mselect',
				'value' => [],
			],
			'digest' => [
				'title' => 'groupactivity_digest',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'groupactivity_digest_1'],
					[2, 'groupactivity_digest_2'],
					[3, 'groupactivity_digest_3'],
					[0, 'groupactivity_digest_0']
				],
			],
			'stick' => [
				'title' => 'groupactivity_stick',
				'type' => 'mcheckbox',
				'value' => [
					[1, 'groupactivity_stick_1'],
					[2, 'groupactivity_stick_2'],
					[3, 'groupactivity_stick_3'],
					[0, 'groupactivity_stick_0']
				],
			],
			'recommend' => [
				'title' => 'groupactivity_recommend',
				'type' => 'radio'
			],
			'place' => [
				'title' => 'groupactivity_place',
				'type' => 'text'
			],
			'class' => [
				'title' => 'groupactivity_class',
				'type' => 'select',
				'value' => []
			],
			'gender' => [
				'title' => 'groupactivity_gender',
				'type' => 'mradio',
				'value' => [
					['', 'groupactivity_gender_0'],
					['1', 'groupactivity_gender_1'],
					['2', 'groupactivity_gender_2'],
				],
				'default' => ''
			],
			'orderby' => [
				'title' => 'groupactivity_orderby',
				'type' => 'mradio',
				'value' => [
					['dateline', 'groupactivity_orderby_dateline'],
					['weekstart', 'groupactivity_orderby_weekstart'],
					['monthstart', 'groupactivity_orderby_monthstart'],
					['weekexp', 'groupactivity_orderby_weekexp'],
					['monthexp', 'groupactivity_orderby_monthexp'],
				],
				'default' => 'dateline'
			],
			'gviewperm' => [
				'title' => 'groupactivity_gviewperm',
				'type' => 'mradio',
				'value' => [
					['-1', 'groupactivity_gviewperm_nolimit'],
					['0', 'groupactivity_gviewperm_only_member'],
					['1', 'groupactivity_gviewperm_all_member']
				],
				'default' => '-1'
			],
			'highlight' => [
				'title' => 'groupactivity_highlight',
				'type' => 'radio',
				'default' => 0,
			],
			'titlelength' => [
				'title' => 'groupactivity_titlelength',
				'type' => 'text',
				'default' => 40
			],
			'summarylength' => [
				'title' => 'groupactivity_summarylength',
				'type' => 'text',
				'default' => 80
			],
			'startrow' => [
				'title' => 'groupactivity_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_group_activity');
	}

	function blockclass() {
		return ['activity', lang('blockclass', 'blockclass_group_activity')];
	}

	function fields() {
		return [
			'id' => ['name' => lang('blockclass', 'blockclass_field_id'), 'formtype' => 'text', 'datatype' => 'int'],
			'url' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_url'), 'formtype' => 'text', 'datatype' => 'string'],
			'title' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_title'), 'formtype' => 'title', 'datatype' => 'title'],
			'pic' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_pic'), 'formtype' => 'pic', 'datatype' => 'pic'],
			'summary' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_summary'), 'formtype' => 'summary', 'datatype' => 'summary'],
			'time' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_time'), 'formtype' => 'text', 'datatype' => 'text'],
			'expiration' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_expiration'), 'formtype' => 'text', 'datatype' => 'text'],
			'author' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_author'), 'formtype' => 'text', 'datatype' => 'text'],
			'authorid' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_authorid'), 'formtype' => 'text', 'datatype' => 'int'],
			'cost' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_cost'), 'formtype' => 'text', 'datatype' => 'int'],
			'place' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_place'), 'formtype' => 'text', 'datatype' => 'text'],
			'class' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_class'), 'formtype' => 'text', 'datatype' => 'text'],
			'gender' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_gender'), 'formtype' => 'text', 'datatype' => 'text'],
			'number' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_number'), 'formtype' => 'text', 'datatype' => 'int'],
			'applynumber' => ['name' => lang('blockclass', 'blockclass_groupactivity_field_applynumber'), 'formtype' => 'text', 'datatype' => 'int'],
		];
	}

	function fieldsconvert() {
		return [
			'forum_activity' => [
				'name' => lang('blockclass', 'blockclass_forum_activity'),
				'script' => 'activity',
				'searchkeys' => [],
				'replacekeys' => [],
			],
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
		$activitytype = explode("\n", $_G['setting']['activitytype']);
		$settings['class']['value'][] = ['', 'groupactivity_class_all'];
		foreach($activitytype as $item) {
			$settings['class']['value'][] = [$item, $item];
		}
		return $settings;
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
		$tids = !empty($parameter['tids']) ? explode(',', $parameter['tids']) : [];
		$fids = !empty($parameter['fids']) ? explode(',', $parameter['fids']) : [];
		$uids = !empty($parameter['uids']) ? explode(',', $parameter['uids']) : [];
		$startrow = !empty($parameter['startrow']) ? intval($parameter['startrow']) : 0;
		$items = !empty($parameter['items']) ? intval($parameter['items']) : 10;
		$digest = $parameter['digest'] ?? 0;
		$stick = $parameter['stick'] ?? 0;
		$orderby = isset($parameter['orderby']) ? (in_array($parameter['orderby'], ['dateline', 'weekstart', 'monthstart', 'weekexp', 'monthexp']) ? $parameter['orderby'] : 'dateline') : 'dateline';
		$titlelength = !empty($parameter['titlelength']) ? intval($parameter['titlelength']) : 40;
		$summarylength = !empty($parameter['summarylength']) ? intval($parameter['summarylength']) : 80;
		$recommend = !empty($parameter['recommend']) ? 1 : 0;
		$keyword = !empty($parameter['keyword']) ? $parameter['keyword'] : '';
		$place = !empty($parameter['place']) ? $parameter['place'] : '';
		$class = !empty($parameter['class']) ? $parameter['class'] : '';
		$gender = !empty($parameter['gender']) ? intval($parameter['gender']) : '';
		$gviewperm = isset($parameter['gviewperm']) ? intval($parameter['gviewperm']) : -1;
		$highlight = !empty($parameter['highlight']) ? 1 : 0;

		$bannedids = !empty($parameter['bannedids']) ? explode(',', $parameter['bannedids']) : [];

		$gviewwhere = $gviewperm == -1 ? '' : " AND ff.gviewperm='$gviewperm'";

		$groups = [];
		if(empty($fids) && $typeids) {
			$query = DB::query('SELECT f.fid, f.name, ff.description FROM '.DB::table('forum_forum').' f LEFT JOIN '.DB::table('forum_forumfield').' ff ON f.fid = ff.fid WHERE f.fup IN ('.dimplode($typeids).") AND threads > 0$gviewwhere");
			while($value = DB::fetch($query)) {
				$groups[$value['fid']] = $value;
				$fids[] = intval($value['fid']);
			}
			if(empty($fids)) {
				return ['html' => '', 'data' => ''];
			}
		}

		require_once libfile('function/post');
		require_once libfile('function/search');

		$datalist = $list = [];
		$keyword = $keyword ? searchkey($keyword, "t.subject LIKE '%{text}%'") : '';
		$sql = ($fids ? ' AND t.fid IN ('.dimplode($fids).')' : '')
			.($tids ? ' AND t.tid IN ('.dimplode($tids).')' : '')
			.($bannedids ? ' AND t.tid NOT IN ('.dimplode($bannedids).')' : '')
			.($digest ? ' AND t.digest IN ('.dimplode($digest).')' : '')
			.($stick ? ' AND t.displayorder IN ('.dimplode($stick).')' : '')
			.$keyword;

		if(empty($fids)) {
			$sql .= " AND t.isgroup='1'";
			if($gviewwhere) {
				$sql .= $gviewwhere;
			}
		}

		$where = '';
		if(in_array($orderby, ['weekstart', 'monthstart'])) {
			$historytime = 0;
			switch($orderby) {
				case 'weekstart':
					$historytime = TIMESTAMP + 86400 * 7;
					break;
				case 'monthstart':
					$historytime = TIMESTAMP + 86400 * 30;
					break;
			}
			$where = ' AND a.starttimefrom >= '.TIMESTAMP.' AND a.starttimefrom<='.$historytime;
			$orderby = 'a.starttimefrom ASC';
		} elseif(in_array($orderby, ['weekexp', 'monthexp'])) {
			$historytime = 0;
			switch($orderby) {
				case 'weekexp':
					$historytime = TIMESTAMP + 86400 * 7;
					break;
				case 'monthexp':
					$historytime = TIMESTAMP + 86400 * 30;
					break;
			}
			$where = ' AND a.expiration >= '.TIMESTAMP.' AND a.expiration<='.$historytime;
			$orderby = 'a.expiration ASC';
		} else {
			$orderby = 't.dateline DESC';
		}
		$where .= $uids ? ' AND t.authorid IN ('.dimplode($uids).')' : '';
		if($gender) {
			$where .= " AND a.gender='$gender'";
		}
		$where = $sql." AND t.displayorder>='0' ".$where;
		$sqlfrom = ' INNER JOIN `'.DB::table('forum_thread').'` t ON t.tid=a.tid ';
		$joinmethod = empty($tids) ? 'INNER' : 'LEFT';
		if($recommend) {
			$sqlfrom .= " $joinmethod JOIN `".DB::table('forum_forumrecommend').'` fc ON fc.tid=tr.tid';
		}

		$sqlfield = '';
		if(empty($fids)) {
			$sqlfield = ', f.name groupname';
			$sqlfrom .= ' LEFT JOIN '.DB::table('forum_forum').' f ON t.fid=f.fid LEFT JOIN '.DB::table('forum_forumfield').' ff ON f.fid = ff.fid';
		}
		$sqlfield = $highlight ? ', t.highlight' : '';

		$query = DB::query("SELECT a.*, t.tid, t.subject, t.authorid, t.author$sqlfield
			FROM ".DB::table('forum_activity')." a $sqlfrom
			WHERE 1$where
			ORDER BY $orderby
			LIMIT $startrow,$items;"
		);
		require_once libfile('block_thread', 'class/block/forum');
		$bt = new block_thread();
		$listtids = $threadtids = $threads = $aid2tid = $attachtables = [];
		while($data = DB::fetch($query)) {
			$data['time'] = dgmdate($data['starttimefrom']);
			if($data['starttimeto']) {
				$data['time'] .= ' - '.dgmdate($data['starttimeto']);
			}
			if($style['getsummary']) {
				$threadtids[$data['posttableid']][] = $data['tid'];
			}
			if($data['aid']) {
				$aid2tid[$data['aid']] = $data['tid'];
				$attachtable = getattachtableid($data['tid']);
				$attachtables[$attachtable][] = $data['aid'];
			}
			$listtids[] = $data['tid'];
			$list[$data['tid']] = [
				'id' => $data['tid'],
				'idtype' => 'tid',
				'title' => cutstr(str_replace('\\\'', '&#39;', addslashes($data['subject'])), $titlelength, ''),
				'url' => 'forum.php?mod=viewthread&tid='.$data['tid'],
				'pic' => ($data['aid'] ? '' : $_G['style']['imgdir'].'/nophoto.gif'),
				'picflag' => '0',
				'fields' => [
					'fulltitle' => str_replace('\\\'', '&#39;', addslashes($data['subject'])),
					'time' => $data['time'],
					'expiration' => $data['expiration'] ? dgmdate($data['expiration']) : 'N/A',
					'author' => $data['author'] ? $data['author'] : $_G['setting']['anonymoustext'],
					'authorid' => $data['authorid'] ? $data['authorid'] : 0,
					'cost' => $data['cost'],
					'place' => $data['place'],
					'class' => $data['class'],
					'gender' => $data['gender'],
					'number' => $data['number'],
					'applynumber' => $data['applynumber'],
				]
			];
			if($highlight && $data['highlight']) {
				$list[$data['tid']]['fields']['showstyle'] = $bt->getthreadstyle($data['highlight']);
			}
		}

		if(!empty($listtids)) {
			$query = DB::query('SELECT tid,COUNT(*) as sum FROM '.DB::table('forum_activityapply').' WHERE tid IN('.dimplode($listtids).') GROUP BY tid');
			while($value = DB::fetch($query)) {
				$list[$value['tid']]['fields']['applynumber'] = $value['sum'];
			}

			$threads = $bt->getthread($threadtids, $summarylength, true);
			if($threads) {
				foreach($threads as $tid => $var) {
					$list[$tid]['summary'] = $var;
				}
			}

			foreach($attachtables as $tableid => $taids) {
				$query = DB::query('SELECT aid, attachment, remote FROM '.DB::table('forum_attachment_'.$tableid).' WHERE aid IN ('.dimplode($taids).')');
				while($avalue = DB::fetch($query)) {
					$list[$aid2tid[$avalue['aid']]]['pic'] = 'forum/'.$avalue['attachment'];
					$list[$aid2tid[$avalue['aid']]]['picflag'] = $avalue['remote'] ? '2' : '1';
				}
			}

			foreach($listtids as $key => $value) {
				$datalist[] = $list[$value];
			}

		}
		return ['html' => '', 'data' => $datalist];
	}
}


