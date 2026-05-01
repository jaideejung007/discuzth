<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class magic_detector {

	var $version = '1.0';
	var $name = 'detector_name';
	var $description = 'detector_desc';
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
				'title' => 'detector_num',
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

		$list = $uids = [];
		$num = !empty($this->parameters['num']) ? intval($this->parameters['num']) : 10;
		$limit = $num + 20;
		$giftMagicID = table_common_magic::t()->fetch_by_identifier('gift');
		$mid = $giftMagicID['available'] ? intval($giftMagicID['magicid']) : 0;
		if($mid) {
			foreach(table_common_magiclog::t()->fetch_all_by_magicid_action_uid($mid, 2, $_G['uid'], 0, $limit) as $value) {
				$uids[] = intval($value['uid']);
			}
		}
		if($uids) {
			$counter = 0;
			$members = table_common_member::t()->fetch_all($uids);
			foreach(table_common_member_field_home::t()->fetch_all($uids) as $uid => $value) {
				$value = array_merge($members[$uid], $value);
				$info = !empty($value['magicgift']) ? dunserialize($value['magicgift']) : [];
				if(!empty($info['left']) && (empty($info['receiver']) || !in_array($_G['uid'], $info['receiver']))) {
					$value['avatar'] = addcslashes(avatar($uid, 'small'), "'");
					$list[$uid] = $value;
					$counter++;
					if($counter >= $num) {
						break;
					}
				}
			}
		}
		usemagic($this->magic['magicid'], $this->magic['num']);
		updatemagiclog($this->magic['magicid'], '2', '1', '0', '0', 'uid', $_G['uid']);

		$op = 'show';
		include template('home/magic_detector');
	}

	function show() {
		global $_G;
		$num = !empty($this->parameters['num']) ? intval($this->parameters['num']) : 10;
		magicshowtips(lang('magic/detector', 'detector_info', ['num' => $num]));
	}
}

