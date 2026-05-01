<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_album', 'class/block/space');

class block_albumspecified extends block_album {
	function __construct() {
		$this->setting = [
			'catid' => [
				'title' => 'albumlist_catid',
				'type' => 'mselect',
			],
			'aids' => [
				'title' => 'albumlist_aids',
				'type' => 'text',
				'value' => ''
			],
			'uids' => [
				'title' => 'albumlist_uids',
				'type' => 'text',
				'value' => ''
			],
			'titlelength' => [
				'title' => 'albumlist_titlelength',
				'type' => 'text',
				'default' => 40
			]
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_album_script_albumspecified');
	}
}

