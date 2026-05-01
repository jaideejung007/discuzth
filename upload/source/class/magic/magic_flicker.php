<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_flicker {
	var $version = '1.0';
	var $name = 'flicker_name';
	var $description = 'flicker_desc';
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

		table_home_comment::t()->update_comment($_GET['id'], ['magicflicker' => 1], $_G['uid']);
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0');
		showmessage(lang('magic/flicker', 'flicker_succeed'), dreferer(), [], ['alert' => 'right', 'showdialog' => 1, 'closetime' => true, 'locationtime' => true]);
	}

	function show() {
		global $_G;
		magicshowtips(lang('magic/flicker', 'flicker_info'));
	}
}

