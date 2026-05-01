<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_friendnum {

	var $version = '1.0';
	var $name = 'friendnum_name';
	var $description = 'friendnum_desc';
	var $price = '20';
	var $weight = '20';
	var $useevent = 0;
	var $targetgroupperm = false;
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		$settings = [
			'addnum' => [
				'title' => 'friendnum_addnum',
				'type' => 'select',
				'value' => [
					['5', '5'],
					['10', '10'],
					['20', '20'],
					['50', '50'],
				],
				'default' => '10'
			],
		];
		return $settings;
	}

	function setsetting(&$magicnew, &$parameters) {
		$magicnew['addnum'] = in_array($parameters['addnum'], [5, 10, 20, 50]) ? intval($parameters['addnum']) : '10';
	}

	function usesubmit() {
		global $_G;

		$addnum = !empty($this->parameters['addnum']) ? intval($this->parameters['addnum']) : 10;
		table_common_member_field_home::t()->increase($_G['uid'], ['addfriend' => $addnum]);
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', 'uid', $_G['uid']);
		showmessage('magics_friendadd_message', '', ['num' => intval($this->parameters['addnum'])], ['alert' => 'right', 'showdialog' => 1]);
	}

	function show() {
		$addnum = !empty($this->parameters['addnum']) ? intval($this->parameters['addnum']) : 10;
		magicshowtips(lang('magic/friendnum', 'friendnum_info', ['num' => $addnum]));
	}

}

