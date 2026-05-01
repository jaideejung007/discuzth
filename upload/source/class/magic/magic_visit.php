<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_visit {

	var $version = '1.0';
	var $name = 'visit_name';
	var $description = 'visit_desc';
	var $price = '20';
	var $weight = '20';
	var $useevent = 0;
	var $targetgroupperm = false;
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		$settings = [
			'num' => [
				'title' => 'visit_num',
				'type' => 'select',
				'value' => [
					['5', '5'],
					['10', '10'],
					['20', '20'],
				],
				'default' => '10'
			],
		];
		return $settings;
	}

	function setsetting(&$magicnew, &$parameters) {
		$magicnew['num'] = in_array($parameters['num'], [5, 10, 20, 50]) ? intval($parameters['num']) : '10';
	}

	function usesubmit() {
		global $_G;

		$num = !empty($this->parameters['num']) ? intval($this->parameters['num']) : 10;
		$friends = $uids = $fids = [];
		$query = table_home_friend::t()->fetch_all_by_uid($_G['uid'], 0, 500);
		foreach($query as $value) {
			$value['username'] = $value['fusername'];
			$value['uid'] = $value['fuid'];
			$uids[] = intval($value['fuid']);
			$friends[$value['fuid']] = $value;
		}
		$count = count($uids);
		if(!$count) {
			showmessage('magicuse_has_no_valid_friend');
		} elseif($count == 1) {
			$fids = [$uids[0]];
		} else {
			$keys = array_rand($uids, min($num, $count));
			$fids = [];
			foreach($keys as $key) {
				$fids[] = $uids[$key];
			}
		}
		$users = [];
		foreach($fids as $uid) {
			$value = $friends[$uid];
			$value['avatar'] = str_replace("'", "\'", avatar($value['uid'], 'small'));
			$users[$uid] = $value;
		}

		$inserts = [];
		if($_POST['visitway'] == 'poke') {
			$note = '';
			$icon = intval($_POST['visitpoke']);
			foreach($fids as $fid) {
				$insertdata = [
					'uid' => $fid,
					'fromuid' => $_G['uid'],
					'fromusername' => $_G['username'],
					'note' => $note,
					'dateline' => $_G['timestamp'],
					'iconid' => $icon
				];
				table_home_poke::t()->insert($insertdata, false, true);
			}
			$repokeids = [];
			foreach(table_home_poke::t()->fetch_all_by_uid_fromuid($_G['uid'], $fids) as $value) {
				$repokeids[] = $value['uid'];
			}
			$ids = array_diff($fids, $repokeids);
			if($ids) {
				require_once libfile('function/spacecp');
				$pokemsg = makepokeaction($icon);
				$pokenote = [
					'fromurl' => 'home.php?mod=space&uid='.$_G['uid'],
					'fromusername' => $_G['username'],
					'fromuid' => $_G['uid'],
					'from_id' => $_G['uid'],
					'from_idtype' => 'pokequery',
					'pokemsg' => $pokemsg
				];
				foreach($ids as $puid) {
					notification_add($puid, 'poke', 'poke_request', $pokenote);
				}
			}
		} elseif($_POST['visitway'] == 'comment') {
			$message = getstr($_POST['visitmsg'], 255);
			$ip = $_G['clientip'];
			$note_inserts = [];
			foreach($fids as $fid) {
				$actor = "<a href=\"home.php?mod=space&uid={$_G['uid']}\">{$_G['username']}</a>";
				$inserts[] = [
					'uid' => $fid,
					'id' => $fid,
					'idtype' => 'uid',
					'authorid' => $_G['uid'],
					'author' => $_G['username'],
					'ip' => $ip,
					'port' => $_G['remoteport'],
					'dateline' => $_G['timestamp'],
					'message' => $message
				];
				$note = lang('spacecp', 'magic_note_wall', ['actor' => $actor, 'url' => "home.php?mod=space&uid=$fid&do=wall"]);
				$note_inserts[] = [
					'uid' => $fid,
					'type' => 'comment',
					'new' => 1,
					'authorid' => $_G['uid'],
					'author' => $_G['username'],
					'note' => $note,
					'dateline' => $_G['timestamp']
				];
			}
			foreach($inserts as $insert) {
				table_home_comment::t()->insert($insert);
			}
			foreach($note_inserts as $note_insert) {
				table_home_notification::t()->insert($note_insert);
			}
			table_common_member::t()->increase($fids, ['newprompt' => 1]);
		} else {
			foreach($fids as $fid) {
				table_home_visitor::t()->insert(['uid' => $fid, 'vuid' => $_G['uid'], 'vusername' => $_G['username'], 'dateline' => $_G['timestamp']], false, true);
			}
		}
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', 'uid', $_G['uid']);

		$op = 'show';
		include template('home/magic_visit');
	}

	function show() {
		global $_G;
		$num = !empty($this->parameters['num']) ? intval($this->parameters['num']) : 10;
		magicshowtips(lang('magic/visit', 'visit_info', ['num' => $num]));
		$op = 'use';
		include template('home/magic_visit');
	}

}

