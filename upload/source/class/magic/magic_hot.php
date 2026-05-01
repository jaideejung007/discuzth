<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_hot {

	var $version = '1.0';
	var $name = 'hot_name';
	var $description = 'hot_desc';
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

		if(table_common_magiclog::t()->count_by_action_uid_targetid_idtype_magicid(2, $_G['uid'], $id, $idtype, $this->magic['magicid'])) {
			showmessage('magicuse_object_once_limit');
		}

		$num = !empty($_G['setting']['feedhotmin']) ? intval($_G['setting']['feedhotmin']) : 3;
		table_home_feed::t()->update_hot_by_id($id, $idtype, $_G['uid'], $num);
		table_home_blog::t()->increase($id, $_G['uid'], ['hot' => $num]);

		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', $idtype, $id);
		showmessage('magics_use_success', '', ['magicname' => $_G['setting']['magics']['hot']], ['alert' => 'right', 'showdialog' => 1]);
	}

	function show() {
		global $_G;
		$id = intval($_GET['id']);
		$idtype = $_GET['idtype'];
		$blog = magic_check_idtype($id, $idtype);
		if(table_common_magiclog::t()->count_by_action_uid_targetid_idtype_magicid(2, $_G['uid'], $id, $idtype, $this->magic['magicid'])) {
			showmessage('magicuse_object_once_limit');
		}

		$num = !empty($_G['setting']['feedhotmin']) ? intval($_G['setting']['feedhotmin']) : 3;
		magicshowtips(lang('magic/hot', 'hot_info', ['num' => $num]));
		echo <<<HTML
<p>
	<input type="hidden" name="id" value="'.$id.'" />
	<input type="hidden" name="idtype" value="'.$idtype.'" />
</p>
HTML;
	}

}

