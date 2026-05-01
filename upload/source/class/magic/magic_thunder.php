<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_thunder {

	var $version = '1.0';
	var $name = 'thunder_name';
	var $description = 'thunder_desc';
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

		$uid = $_G['uid'];
		$_G['uid'] = 0;
		$avatar = avatar($uid, 'middle', true);
		include_once libfile('function/feed');
		feed_add(
			'thunder', 'magicuse_thunder_announce_title',
			[
				'uid' => $uid,
				'username' => "<a href=\"home.php?mod=space&uid=$uid\">{$_G['username']}</a>"],
			'magicuse_thunder_announce_body',
			[
				'uid' => $uid,
				'magic_thunder' => 1], '', [$avatar], ["home.php?mod=space&uid=$uid"]
		);
		$_G['uid'] = $uid;
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', 'uid', $_G['uid']);
		showmessage('magics_thunder_message', 'home.php?mod=space&do=home&view=all', ['magicname' => $_G['setting']['magics']['thunder']], ['alert' => 'right', 'showdialog' => 1, 'locationtime' => true]);
	}

	function show() {
		magicshowtips(lang('magic/thunder', 'thunder_info'));
	}

}

