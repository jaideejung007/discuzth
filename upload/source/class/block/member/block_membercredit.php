<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('block_member', 'class/block/member');

class block_membercredit extends block_member {
	function __construct() {
		$this->setting = [
			'orderby' => [
				'title' => 'memberlist_orderby',
				'type' => 'mradio',
				'value' => [
					['credits', 'memberlist_orderby_credits'],
					['extcredits', 'memberlist_orderby_extcredits'],
				],
				'default' => 'credits'
			],
			'extcredit' => [
				'title' => 'memberlist_orderby_extcreditselect',
				'type' => 'select',
				'value' => []
			],
			'startrow' => [
				'title' => 'memberlist_startrow',
				'type' => 'text',
				'default' => 0
			],
		];
	}

	function name() {
		return lang('blockclass', 'blockclass_member_script_membercredit');
	}
}

