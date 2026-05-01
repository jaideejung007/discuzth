<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_updateline {

	var $version = '1.0';
	var $name = 'updateline_name';
	var $description = 'updateline_desc';
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

		magic_check_idtype($id, $idtype);

		$tablename = gettablebyidtype($idtype);
		C::t($tablename)->update_dateline_by_id_idtype_uid($id, $idtype, $_G['timestamp'], $_G['uid']);

		table_home_feed::t()->update_feed($id, ['dateline' => $_G['timestamp']], $idtype, $_G['uid']);

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', $idtype, $id);
		showmessage('magics_use_success', '', ['magicname' => $_G['setting']['magics']['updateline']], ['alert' => 'right', 'showdialog' => 1]);
	}

	function show() {
		global $_G;
		$id = intval($_GET['id']);
		$idtype = $_GET['idtype'];

		magic_check_idtype($id, $idtype);
		magicshowtips(lang('magic/updateline', 'updateline_info'));
		echo '<p><input type="hidden" name="id" value="'.$id.'" /><input type="hidden" name="idtype" value="'.$idtype.'" /></p>';
	}

}

