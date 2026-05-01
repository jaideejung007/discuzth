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

class optimizer_seo {

	public function __construct() {

	}

	public function check() {
		$seotitle = table_common_setting::t()->fetch_setting('seotitle', true);
		$seokeywords = table_common_setting::t()->fetch_setting('seokeywords', true);
		$seodescription = table_common_setting::t()->fetch_setting('seodescription', true);
		$rewritestatus = table_common_setting::t()->fetch_setting('rewritestatus', true);
		if(!$seotitle || !$seokeywords || !$seodescription || !$rewritestatus) {
			$return = ['status' => 1, 'type' => 'header', 'lang' => lang('optimizer', 'optimizer_seo_advice')];
		} else {
			$return = ['status' => 0, 'type' => 'none', 'lang' => lang('optimizer', 'optimizer_seo_no_need')];
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		dheader('Location: '.$_G['siteurl'].$adminfile.'?action=setting&operation=seo');
	}
}

