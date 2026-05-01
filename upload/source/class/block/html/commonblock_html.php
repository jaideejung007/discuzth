<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class commonblock_html extends discuz_block {

	function fields() {
		return [];
	}

	function blockclass() {
		return ['html', lang('blockclass', 'blockclass_html_html')];
	}

}