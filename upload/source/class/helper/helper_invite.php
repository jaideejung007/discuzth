<?php
/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class helper_invite {

	public static function generate_key($uid) {
		global $_G;
		$user = table_common_member::t()->fetch($uid);
		return substr(md5(md5($user['password']).'|'.$uid), 8, 16);
	}

}