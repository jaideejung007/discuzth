<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

namespace admin;
use table_common_setting;

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class optimizer_loginoutofdate {

	public function __construct() {

	}

	public function check() {
		$accountguard = table_common_setting::t()->fetch_setting('accountguard', true);
		if(!$accountguard['loginoutofdate']) {
			$return = ['status' => 1, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_loginoutofdate_need')];
		} else {
			$return = ['status' => 0, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_loginoutofdate_no_need')];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=setting&operation=accountguard');
	}
}

