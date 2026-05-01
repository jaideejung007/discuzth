<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_call {

	var $version = '1.0';
	var $name = 'call_name';
	var $description = 'call_desc';
	var $price = '20';
	var $weight = '20';
	var $useevent = 0;
	var $targetgroupperm = false;
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
	}

	function setsetting(&$magicnew, &$parameters) {
	}

	function usesubmit() {
		global $_G;

		$id = intval($_GET['id']);
		$idtype = $_GET['idtype'];
		$blog = magic_check_idtype($id, $idtype);

		$num = 10;
		$list = $ids = $note_inserts = [];
		$fusername = dimplode($_POST['fusername']);
		if($fusername) {
			$query = table_home_friend::t()->fetch_all_by_uid_username($_G['uid'], $_POST['fusername'], 0, $num);
			$note = lang('spacecp', 'magic_call', ['url' => "home.php?mod=space&uid={$_G['uid']}&do=blog&id=$id"]);
			foreach($query as $value) {
				$ids[] = $value['fuid'];
				$value['avatar'] = str_replace("'", "\'", avatar($value['fuid'], 'small'));
				$list[] = $value;
				$note_inserts[] = [
					'uid' => $value['fuid'],
					'type' => 'magic',
					'new' => 1,
					'authorid' => $_G['uid'],
					'author' => $_G['username'],
					'note' => $note,
					'category' => 3,
					'dateline' => $_G['timestamp']
				];
			}
		}
		if(empty($ids)) {
			showmessage('magicuse_has_no_valid_friend');
		}
		foreach($note_inserts as $note_insert) {
			table_home_notification::t()->insert($note_insert);
		}

		table_common_member::t()->increase($ids, ['newprompt' => 1]);

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', $idtype, $id);

		$op = 'show';
		include template('home/magic_call');
	}

	function show() {
		$id = intval($_GET['id']);
		$idtype = $_GET['idtype'];
		magic_check_idtype($id, $idtype);
		magicshowtips(lang('magic/call', 'call_info'));
		$op = 'use';
		include template('home/magic_call');
	}
}

