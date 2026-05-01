<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_gift {

	var $version = '1.0';
	var $name = 'gift_name';
	var $description = 'gift_desc';
	var $price = '20';
	var $weight = '20';
	var $useevent = 0;
	var $targetgroupperm = false;
	var $copyright = '<a href="https://www.discuz.vip/" target="_blank">Discuz!</a>';
	var $magic = [];
	var $parameters = [];

	function getsetting(&$magic) {
		$settings = [];
		return $settings;
	}

	function setsetting(&$magicnew, &$parameters) {
	}

	function usesubmit() {
		global $_G;

		$info = [
			'credits' => intval($_POST['credits']),
			'percredit' => intval($_POST['percredit']),
			'credittype' => $_GET['credittype'],
			'left' => intval($_POST['credits']),
			'magicid' => intval($this->magic['magicid']),
			'receiver' => []
		];
		if($info['credits'] < 1) {
			showmessage(lang('magic/gift', 'gift_bad_credits_input'));
		}
		if($info['percredit'] < 1 || $info['percredit'] > $info['credits']) {
			showmessage(lang('magic/gift', 'gift_bad_percredit_input'));
		}
		$member = [];
		if(preg_match('/^extcredits[1-8]$/', $info['credittype'])) {
			$member = table_common_member_count::t()->fetch($_G['uid']);
			if($member[$info['credittype']] < $info['credits']) {
				showmessage(lang('magic/gift', 'gift_credits_out_of_own'));
			}
			$extcredits = str_replace('extcredits', '', $info['credittype']);
			updatemembercount($_G['uid'], [$extcredits => -$info['credits']], 1, 'BGC', $this->magic['magicid']);
		} else {
			showmessage(lang('magic/gift', 'gift_bad_credittype_input'));
		}

		table_common_member_field_home::t()->update($_G['uid'], ['magicgift' => serialize($info)]);
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', 'uid', $_G['uid']);

		showmessage(lang('magic/gift', 'gift_succeed'), dreferer(), [], ['alert' => 'right', 'showdialog' => 1, 'locationtime' => true]);
	}

	function show() {
		global $_G;
		$num = !empty($this->parameters['num']) ? intval($this->parameters['num']) : 10;
		magicshowtips(lang('magic/gift', 'gift_info', ['num' => $num]));

		$extcredits = [];
		foreach($_G['setting']['extcredits'] as $id => $credit) {
			$extcredits['extcredits'.$id] = $credit['title'];
		}

		$op = 'show';
		include template('home/magic_gift');
	}
}

