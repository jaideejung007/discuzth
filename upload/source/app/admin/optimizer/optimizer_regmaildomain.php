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

class optimizer_regmaildomain {

	public function __construct() {

	}

	public function check() {
		$regmaildomain = table_common_setting::t()->fetch_setting('regmaildomain');
		$maildomainlist = table_common_setting::t()->fetch_setting('maildomainlist');
		if($regmaildomain == 2 && !$maildomainlist) {
			$return = ['status' => 1, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_regmaildomain_need')];
		} else {
			$return = ['status' => 2, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_regmaildomain_tip')];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=setting&operation=access');
	}
}

