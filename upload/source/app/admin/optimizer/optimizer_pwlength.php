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

class optimizer_pwlength {

	public function __construct() {

	}

	public function check() {
		$pwlength = table_common_setting::t()->fetch_setting('pwlength');
		if($pwlength < 6) {
			$return = ['status' => 1, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_pwlength_need')];
		} else {
			$return = ['status' => 0, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_pwlength_no_need')];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=setting&operation=access');
	}
}

