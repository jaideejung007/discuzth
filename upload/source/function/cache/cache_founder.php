<?php

/**
 * [Discuz!] (C)2001-2099 Discuz! Team
 * This is NOT a freeware, use is subject to license terms
 * https://license.discuz.vip
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function build_cache_founder() {
	global $_G;

	$allowadmincp = $status0 = $status1 = [];
	$founders = explode(',', str_replace(' ', '', $_G['config']['admincp']['founder']));
	if($founders) {
		foreach($founders as $founder) {
			if(is_numeric($founder)) {
				$fuid[] = $founder;
			} else {
				$fuser[] = $founder;
			}
		}
		if($fuid) {
			$allowadmincp = table_common_member::t()->fetch_all($fuid, false, 0);
		}
		if($fuser) {
			$allowadmincp = $allowadmincp + table_common_member::t()->fetch_all_by_username($fuser);
		}
	}
	$allowadmincp = $allowadmincp + table_common_admincp_member::t()->range();

	$allallowadmincp = table_common_member::t()->fetch_all_by_allowadmincp('0', '>') + table_common_member::t()->fetch_all(array_keys($allowadmincp), false, 0);
	foreach($allallowadmincp as $uid => $user) {
		if(isset($allowadmincp[$uid]) && !getstatus($user['allowadmincp'], 1)) {
			$status1[$uid] = $uid;
		} elseif(!isset($allowadmincp[$uid]) && getstatus($user['allowadmincp'], 1)) {
			$status0[$uid] = $uid;
		}
	}
	if(!empty($status0)) {
		table_common_member::t()->clean_admincp_manage($status0);
	}
	if(!empty($status1)) {
		table_common_member::t()->update_admincp_manage($status1);
	}

}

