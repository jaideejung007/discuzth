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

class optimizer_inviteregister {

	public function __construct() {

	}

	public function check() {
		$regstatus = table_common_setting::t()->fetch_setting('regstatus');
		if($regstatus >= 2) {
			$inviteconfig = table_common_setting::t()->fetch_setting('inviteconfig', true);
			if(!$inviteconfig['inviteareawhite']) {
				$return = ['status' => 2, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_inviteregister_tip')];
			} else {
				$return = ['status' => 0, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_iniviteregister_normal')];
			}
		} else {
			$return = ['status' => 2, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_iniviteregister_normal')];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=setting&operation=access');
	}
}

