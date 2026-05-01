<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_downdateline {

	var $version = '1.0';
	var $name = 'downdateline_name';
	var $description = 'downdateline_desc';
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

		$newdateline = strtotime($_POST['newdateline']);
		if(!$_POST['newdateline'] || $newdateline < strtotime('1970-1-1') || $newdateline > $blog['dateline']) {
			showmessage('magicuse_bad_dateline');
		}

		$tablename = gettablebyidtype($idtype);
		C::t($tablename)->update_dateline_by_id_idtype_uid($id, $idtype, $newdateline, $_G['uid']);

		table_home_feed::t()->update_feed($id, ['dateline' => $newdateline], $idtype, $_G['uid']);

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', $idtype, $id);
		showmessage('magics_use_success', '', ['magicname' => $_G['setting']['magics']['downdateline']], ['alert' => 'right', 'showdialog' => 1]);
	}

	function show() {
		$id = intval($_GET['id']);
		$idtype = $_GET['idtype'];
		$blog = magic_check_idtype($id, $idtype);
		magicshowtips(lang('magic/downdateline', 'downdateline_info'));
		$time = dgmdate($blog['dateline'], 'Y-m-d H:i');
		$op = 'use';
		include template('home/magic_downdateline');
	}

}

