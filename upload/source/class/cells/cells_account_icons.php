<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/post');

class cells_account_icons {

	public static function process() {
		global $_G;

		$_G['account_icons'] = [];
		if(empty($_G['setting']['account']['loginLink'])) {
			return;
		}
		foreach(account_base::getInterfaces() as $interface) {
			if(!account_base::allow($interface)) {
				continue;
			}
			if(!in_array($interface, $_G['setting']['account']['loginLink'])) {
				continue;
			}
			$_G['account_icons'][] = [$interface, account_base::getName($interface), account_base::getIcon($interface)[0]];
		}
	}

}